import asyncio
import threading
import time
from typing import Awaitable, Callable

import serial

from .db import GROUPE, get_cursor
from .serial_bridge import SerialBridge

OnMesure = Callable[[dict], Awaitable[None]]

# Délai entre deux vérifications de leds_g2c, pour relayer au STM32 les changements
# faits par un autre client que ce backend (ex. le site PHP qui écrit direct en BDD).
LED_POLL_INTERVAL_S = 1.0


def parse_data_line(line: str) -> dict | None:
    """Parse une trame `DATA;temp=23;hum=45` envoyée par le firmware."""
    if not line.startswith("DATA;"):
        return None

    values: dict[str, str] = {}
    for part in line.split(";")[1:]:
        if "=" not in part:
            return None
        key, value = part.split("=", 1)
        values[key] = value

    try:
        return {"temperature": float(values["temp"]), "humidite": float(values["hum"])}
    except (KeyError, ValueError):
        return None


class SerialReader:
    """Lit en continu les trames DATA;... du STM32, les enregistre dans la BDD partagée et relaie les commandes LED."""

    def __init__(self, bridge: SerialBridge, loop: asyncio.AbstractEventLoop, on_mesure: OnMesure):
        self._bridge = bridge
        self._loop = loop
        self._on_mesure = on_mesure
        self._stop_event = threading.Event()
        self._thread: threading.Thread | None = None
        self._led_cache: dict[int, int] = {}
        self._last_led_poll = 0.0

    def start(self) -> None:
        self._bridge.connect()
        try:
            self._led_cache = self._read_led_states()
        except Exception as exc:
            print(f"[leds] lecture initiale impossible : {exc}", flush=True)
        self._thread = threading.Thread(target=self._run, daemon=True)
        self._thread.start()

    def stop(self) -> None:
        self._stop_event.set()
        if self._thread is not None:
            self._thread.join(timeout=2)
        self._bridge.close()

    def send_led_command(self, led_id: int, etat: bool) -> None:
        self._bridge.send_command(f"LED;{led_id};{'ON' if etat else 'OFF'}")
        self._led_cache[led_id] = int(etat)

    def _run(self) -> None:
        while not self._stop_event.is_set():
            try:
                line = self._bridge.read_line()
            except serial.SerialException as exc:
                print(f"[serial] liaison perdue : {exc}")
                return

            if line is not None:
                print(f"[serial] reçu : {line!r}", flush=True)

                mesure = parse_data_line(line)
                if mesure is not None:
                    self._save_and_notify(mesure)
                else:
                    print(f"[serial] ligne ignorée (format inattendu) : {line!r}", flush=True)

            now = time.monotonic()
            if now - self._last_led_poll >= LED_POLL_INTERVAL_S:
                self._last_led_poll = now
                self._poll_led_changes()

    def _read_led_states(self) -> dict[int, int]:
        with get_cursor() as cur:
            cur.execute("SELECT id, etat FROM leds_g2c")
            return {row["id"]: row["etat"] for row in cur.fetchall()}

    def _poll_led_changes(self) -> None:
        """Détecte les changements faits directement en BDD (ex. depuis le site PHP) et les relaie au STM32."""
        try:
            current = self._read_led_states()
        except Exception as exc:
            print(f"[leds] vérification BDD impossible : {exc}", flush=True)
            return

        for led_id, etat in current.items():
            if self._led_cache.get(led_id) != etat:
                self._bridge.send_command(f"LED;{led_id};{'ON' if etat else 'OFF'}")
                print(f"[leds] changement détecté en BDD -> LED;{led_id};{'ON' if etat else 'OFF'}", flush=True)

        self._led_cache = current

    def _save_and_notify(self, mesure: dict) -> None:
        with get_cursor() as cur:
            cur.execute(
                "INSERT INTO Mesure (type, valeur, date, groupe) VALUES (%s, %s, CURDATE(), %s)",
                ("temperature", mesure["temperature"], GROUPE),
            )
            temp_id = cur.lastrowid
            cur.execute(
                "INSERT INTO Mesure (type, valeur, date, groupe) VALUES (%s, %s, CURDATE(), %s)",
                ("humidite", mesure["humidite"], GROUPE),
            )
            hum_id = cur.lastrowid

        payload = {"id": hum_id, **mesure}
        print(f"[serial] mesure enregistrée (ids={temp_id},{hum_id}) : {mesure}", flush=True)
        asyncio.run_coroutine_threadsafe(self._on_mesure(payload), self._loop)
