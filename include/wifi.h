#pragma once
#include <ESP8266WiFi.h>


// ========== CONFIGURACIÓN WIFI ==========
constexpr const char* ssid      = "diego";
constexpr const char* password  = "golololol";


void setup_wifi() {
  Serial.println();
  Serial.print("Conectando a ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi conectado!");
  Serial.print("IP asignada: ");
  Serial.println(WiFi.localIP());
}


