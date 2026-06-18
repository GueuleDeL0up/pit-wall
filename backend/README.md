# Backend

API Python (FastAPI) qui :
- lit la liaison série avec le STM32 et enregistre les mesures dans la BDD partagée (MySQL)
- expose une API REST (mesures, état des LEDs) et un WebSocket (mesures en temps réel)
- relaie les commandes LED reçues du frontend vers le STM32

## Installation

```bash
cd backend
python -m venv .venv
.venv\Scripts\activate
pip install -r requirements.txt
```

## Configuration

Copier `.env.example` en `.env` (déjà fait, et `.env` est gitignore car il contient des secrets)
et renseigner les variables :

| Variable | Défaut | Description |
|---|---|---|
| `PITWALL_DB_HOST` | — | Hôte MySQL de la BDD partagée (AlwaysData) |
| `PITWALL_DB_PORT` | `3306` | Port MySQL |
| `PITWALL_DB_USER` | `pitwallg2` | Utilisateur (panneau AlwaysData) |
| `PITWALL_DB_PASSWORD` | — | Mot de passe (panneau AlwaysData) |
| `PITWALL_DB_NAME` | — | Nom de la base de données |
| `PITWALL_SERIAL_PORT` | `COM6` | Port virtuel série du ST-Link (Gestionnaire de périphériques, varie selon la machine) |
| `PITWALL_SERIAL_BAUDRATE` | `9600` | Doit correspondre au baudrate du firmware |

## Lancement

```bash
uvicorn app.main:app --reload
```

Le backend n'expose qu'une API (REST + WebSocket) — aucun frontend statique n'est servi ici.
Le site PHP de [`frontend/`](../frontend) tourne séparément sous son propre serveur PHP et s'y connecte
(BDD partagée en direct, et `frontend/config/backend.php` pour relayer les commandes LED en temps réel).

## Base de données partagée

Le backend écrit dans la BDD MySQL partagée entre les groupes (identifiants dans `backend/.env`, gitignore) :

- Table `Mesure` (`id`, `type`, `valeur`, `date`, `groupe`) : chaque lecture DHT11 produit deux
  lignes (`type='temperature'` et `type='humidite'`), avec `groupe='g2c'`.
- Table `leds_g2c` (`id` 1-4, `etat`, `updated_at`) : état des 4 LEDs.

Au démarrage, le backend s'assure que les 4 lignes de `leds_g2c` existent (`INSERT IGNORE`).
Si la connexion échoue, un avertissement `[db] connexion impossible : ...` est affiché et
l'application continue de démarrer (mais les routes `/api/mesures` et `/api/leds` échoueront).

## Liaison série avec le STM32

Au démarrage, le backend ouvre le port série configuré et lance un thread qui lit en continu
les trames `DATA;temp=...;hum=...`, les enregistre dans `Mesure` et les diffuse aux clients connectés
sur `/ws`. Les commandes
`POST /api/leds/{id}` sont relayées au STM32 via `LED;<id>;ON/OFF`.

Si le port est introuvable ou indisponible, le backend affiche un avertissement au démarrage
(`[serial] connexion impossible sur ... : ...`) et continue de fonctionner (API et frontend
restent utilisables, mais sans nouvelles mesures ni pilotage réel des LEDs).
