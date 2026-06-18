# Pit Wall F1

Simulateur de pit wall F1 : remontée de capteurs (température/humidité) vers une base de données et pilotage de LEDs depuis une interface web.

## Structure du dépôt

- [`firmware/`](firmware/) — Firmware STM32 (lecture DHT11, pilotage LEDs)
- [`backend/`](backend/) — API Python (FastAPI) : pont série, BDD partagée MySQL, WebSocket
- [`frontend/`](frontend/) — Pit wall (PHP) : capteur, signaux LED, buzzer stand, timing et monoplaces Alpine. Tourne sous son propre serveur PHP, connecté directement à la BDD partagée.
- [`docs/`](docs/) — Schémas et documentation complémentaire
