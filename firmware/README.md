# Firmware STM32

Firmware pour le NUCLEO-L412KB, écrit avec l'**IDE Arduino** (core STM32duino), responsable de :
- lire le capteur DHT11 (température/humidité) et envoyer les mesures sur la liaison série (port virtuel du ST-Link / `Serial`)
- piloter les 4 LEDs en GPIO selon les commandes reçues sur cette même liaison

## Câblage

Voir [docs/wiring.md](../docs/wiring.md).

## Protocole série

Format texte ligne par ligne, 9600 bauds :
- STM32 -> Backend (toutes les ~2s) : `DATA;temp=23;hum=45` (entiers, le DHT11 n'a pas de décimales)
- Backend -> STM32 (sur action utilisateur) : `LED;1;ON` / `LED;1;OFF`

Le firmware n'écrit sur `Serial` que ces trames (pas de message de debug), pour ne pas perturber
le parsing côté backend.

## Setup Arduino IDE

1. Fichier > Préférences > "URL de gestionnaire de cartes additionnelles", ajouter :
   `https://github.com/stm32duino/BoardManagerFiles/raw/main/package_stmicroelectronics_index.json`
2. Outils > Type de carte > Gestionnaire de cartes > installer **"STM32 MCU based boards"**
3. Sélectionner la carte : Outils > Type de carte > STM32 boards groups > **Nucleo-32** > **Nucleo L412KB**
4. Méthode d'upload (Outils > Upload method) : **STM32CubeProgrammer (SWD)**, via le ST-Link intégré (USB)

## Dépendances

- Bibliothèque **SimpleDHT** (Winlin) : Outils > Gérer les bibliothèques > rechercher "SimpleDHT" > Installer

## Liaison série avec le backend

- Le backend communique avec le STM32 via le port COM virtuel exposé par le câble USB du ST-Link (`Serial`, USART2) — le même câble que celui utilisé pour téléverser le sketch.
- Aucun adaptateur supplémentaire n'est nécessaire. D0/D1 (USART1, `Serial1`) ne sont pas utilisés et restent libres.
- Le sketch ne doit écrire sur `Serial` que les trames du protocole (`DATA;...`) : pas de message de debug, sous peine de perturber le parsing côté backend.
- Côté backend, configurer `PITWALL_SERIAL_PORT` avec le port COM du ST-Link (visible dans le Gestionnaire de périphériques, ex. `COM6` — le numéro dépend de la machine).

## Build & flash

Le sketch se trouve dans [sketch/sketch.ino](sketch/sketch.ino). L'ouvrir dans l'IDE Arduino (carte branchée en USB) et faire Téléverser.
