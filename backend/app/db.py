import os
from contextlib import contextmanager

import pymysql
import pymysql.cursors
from dotenv import load_dotenv

load_dotenv()

# Groupe assigné dans la BDD partagée (table Mesure, colonne `groupe`, et table `leds_g2c`)
GROUPE = "g2c"

DB_CONFIG = {
    "host": os.environ.get("PITWALL_DB_HOST", ""),
    "port": int(os.environ.get("PITWALL_DB_PORT", "3306")),
    "user": os.environ.get("PITWALL_DB_USER", ""),
    "password": os.environ.get("PITWALL_DB_PASSWORD", ""),
    "database": os.environ.get("PITWALL_DB_NAME", ""),
    "cursorclass": pymysql.cursors.DictCursor,
    "autocommit": True,
}


@contextmanager
def get_cursor():
    conn = pymysql.connect(**DB_CONFIG)
    try:
        with conn.cursor() as cur:
            yield cur
    finally:
        conn.close()


def init_db() -> None:
    """S'assure que les 4 LEDs existent dans `leds_g2c` (BDD partagée, tables déjà créées)."""
    with get_cursor() as cur:
        cur.executemany(
            "INSERT IGNORE INTO leds_g2c (id, etat) VALUES (%s, 0)",
            [(led_id,) for led_id in range(1, 5)],
        )