# Câblage

## DHT11 (4 broches, sans module)

En regardant la face avant (grille) du capteur, de gauche à droite :

| Broche | Fonction |
|---|---|
| 1 | VCC (3 à 5,5V) |
| 2 | DATA (signal) |
| 3 | NC (non connectée) |
| 4 | GND |

Sans module, une résistance de pull-up (4,7-10kΩ) est nécessaire entre DATA et VCC, sinon la ligne flotte et la lecture est instable.

## Câblage vers le NUCLEO-L412KB

| DHT11 | NUCLEO-L412KB | Remarque |
|---|---|---|
| Pin 1 (VCC) | 3V3 (CN4, broche 14) | |
| Pin 2 (DATA) | D2 | + pull-up 4,7-10kΩ vers VCC |
| Pin 3 (NC) | — | rien à connecter |
| Pin 4 (GND) | GND (CN3 broche 4 ou CN4 broche 2) | |

## LEDs (x4)

Pour chaque LED : `GPIO --[220-330Ω]--> anode (patte longue) --> cathode --> GND`

| LED | GPIO |
|---|---|
| LED1 | D3 |
| LED2 | D4 |
| LED3 | D5 |
| LED4 | D6 |

## Schéma

```
        3V3 ──┬──────────────► DHT11 VCC (pin1)
              │
           [pull-up 10kΩ]
              │
   D2 ────────┴──────────────► DHT11 DATA (pin2)
                                DHT11 NC (pin3) -> rien
   GND ───────────────────────► DHT11 GND (pin4)

   D3 ──[220Ω]──►|── GND   (LED1)
   D4 ──[220Ω]──►|── GND   (LED2)
   D5 ──[220Ω]──►|── GND   (LED3)
   D6 ──[220Ω]──►|── GND   (LED4)
```
