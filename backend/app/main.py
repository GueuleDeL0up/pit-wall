import asyncio
import os
from contextlib import asynccontextmanager

from fastapi import FastAPI, WebSocket, WebSocketDisconnect

from .db import GROUPE, get_cursor, init_db
from .models import LedCommand
from .serial_bridge import SerialBridge
from .serial_reader import SerialReader

SERIAL_PORT = os.environ.get("PITWALL_SERIAL_PORT", "COM6")
SERIAL_BAUDRATE = int(os.environ.get("PITWALL_SERIAL_BAUDRATE", "9600"))

websocket_clients: set[WebSocket] = set()
serial_reader: SerialReader | None = None


async def broadcast_mesure(mesure: dict) -> None:
    for websocket in list(websocket_clients):
        try:
            await websocket.send_json(mesure)
        except Exception:
            websocket_clients.discard(websocket)


@asynccontextmanager
async def lifespan(app: FastAPI):
    global serial_reader
    bridge = SerialBridge(SERIAL_PORT, SERIAL_BAUDRATE)
    reader = SerialReader(bridge, asyncio.get_running_loop(), broadcast_mesure)
    try:
        reader.start()
        serial_reader = reader
        print(f"[serial] connecté sur {SERIAL_PORT} @ {SERIAL_BAUDRATE} bauds", flush=True)
    except Exception as exc:
        print(f"[serial] connexion impossible sur {SERIAL_PORT} : {exc}", flush=True)
        serial_reader = None

    yield

    if serial_reader is not None:
        serial_reader.stop()


app = FastAPI(title="Pit Wall F1", lifespan=lifespan)

try:
    init_db()
    print("[db] connexion BDD OK", flush=True)
except Exception as exc:
    print(f"[db] connexion impossible : {exc}", flush=True)


@app.get("/api/mesures")
def get_mesures(limit: int = 50):
    with get_cursor() as cur:
        cur.execute(
            "SELECT id, type, valeur FROM Mesure WHERE groupe = %s ORDER BY id DESC LIMIT %s",
            (GROUPE, limit * 2),
        )
        rows = cur.fetchall()

    # Le firmware envoie temp puis hum à chaque lecture : on reconstitue les paires
    # (id croissants consécutifs) en records {id, temperature, humidite}.
    mesures = []
    pending_temp = None
    for row in reversed(rows):
        if row["type"] == "temperature":
            pending_temp = row
        elif row["type"] == "humidite" and pending_temp is not None:
            mesures.append(
                {
                    "id": row["id"],
                    "temperature": pending_temp["valeur"],
                    "humidite": row["valeur"],
                }
            )
            pending_temp = None

    return list(reversed(mesures[-limit:]))


@app.get("/api/leds")
def get_leds():
    with get_cursor() as cur:
        cur.execute("SELECT id, etat, updated_at FROM leds_g2c ORDER BY id")
        return cur.fetchall()


@app.post("/api/leds/{led_id}")
def set_led(led_id: int, command: LedCommand):
    with get_cursor() as cur:
        cur.execute(
            "UPDATE leds_g2c SET etat = %s, updated_at = NOW() WHERE id = %s",
            (int(command.etat), led_id),
        )
    if serial_reader is not None:
        serial_reader.send_led_command(led_id, command.etat)
    return {"id": led_id, "etat": command.etat}


@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await websocket.accept()
    websocket_clients.add(websocket)
    try:
        while True:
            await websocket.receive_text()
    except WebSocketDisconnect:
        pass
    finally:
        websocket_clients.discard(websocket)
