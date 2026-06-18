#include <SimpleDHT.h>

// DHT11 DATA sur D2 (cf. docs/wiring.md)
const int DHT_PIN = D2;
SimpleDHT11 dht11(DHT_PIN);

// LEDs 1-4 sur D3-D6 (cf. docs/wiring.md)
const int LED_PINS[] = {D3, D4, D5, D6};
const int NUM_LEDS = 4;

// Lecture + envoi des mesures toutes les ~2s
const unsigned long READ_INTERVAL_MS = 2000;
unsigned long lastReadMs = 0;

String commandBuffer;

void setup() {
  Serial.begin(9600); // port ST-Link, liaison vers le backend

  for (int i = 0; i < NUM_LEDS; i++) {
    pinMode(LED_PINS[i], OUTPUT);
    digitalWrite(LED_PINS[i], LOW);
  }
}

void loop() {
  readDht();
  readLedCommands();
}

void readDht() {
  unsigned long now = millis();
  if (now - lastReadMs < READ_INTERVAL_MS) {
    return;
  }
  lastReadMs = now;

  byte temperature = 0;
  byte humidity = 0;
  int err = dht11.read(&temperature, &humidity, NULL);
  if (err == SimpleDHTErrSuccess) {
    Serial.print("DATA;temp=");
    Serial.print((int)temperature);
    Serial.print(";hum=");
    Serial.println((int)humidity);
  }
}

// Traite les commandes "LED;<1-4>;ON" / "LED;<1-4>;OFF" reçues sur Serial
void readLedCommands() {
  while (Serial.available() > 0) {
    char c = Serial.read();
    if (c == '\n') {
      handleLedCommand(commandBuffer);
      commandBuffer = "";
    } else if (c != '\r') {
      commandBuffer += c;
    }
  }
}

void handleLedCommand(const String &line) {
  int sep1 = line.indexOf(';');
  int sep2 = line.indexOf(';', sep1 + 1);
  if (sep1 == -1 || sep2 == -1 || line.substring(0, sep1) != "LED") {
    return;
  }

  int ledId = line.substring(sep1 + 1, sep2).toInt();
  if (ledId < 1 || ledId > NUM_LEDS) {
    return;
  }

  bool on = line.substring(sep2 + 1) == "ON";
  digitalWrite(LED_PINS[ledId - 1], on ? HIGH : LOW);
}