#pragma once
#include <ArduinoJson.h>
#include <cstring>
#include <PubSubClient.h>
#include <ESP8266WiFi.h>
#include "setpoints.h"
#include "airQualityAnalyzer.h"
#include "bmeAnalyzer.h"
#include "css811reader.h"


// ========== CONFIGURACIÓN MQTT ==========
constexpr const char* mqttServer = "172.20.10.5";
constexpr const char* topic_pub = "sensores/diego";
constexpr const char* topic_sub = "esp8266/setpoint";
constexpr const int mqttPort = 1883;


// ======= OBJETOS PUB-SUB GLOBALES =========
WiFiClient espClient;
PubSubClient client(espClient);


// =======================================================
// DECLARACIONES ADELANTADAS (para poder tener setup arriba)
// =======================================================
void reiceiveFromMQTT(char* topic, byte* payload, unsigned int length);
void publishToMQTT(PMSReader& pms, BMEReader& bms);
void reconnect();


// ========== MQTT SETUP ==========
inline void setup_mqtt() {
  client.setCallback(reiceiveFromMQTT);
  client.subscribe(topic_sub);
}


// ========== RECONNECT MQTT ==========
void reconnect() {
  while (!client.connected()) {
    Serial.print("Intentando conectar al broker MQTT...");

    if (client.connect("ESP8266_SENSORES")) {
      Serial.println("Conectado!");
      client.subscribe(topic_sub);
    } else {
      Serial.print("Fallo, rc=");
      Serial.print(client.state());
      Serial.println(" — Reintentando...");
      delay(2000);
    }
  }
}


// ========== SEND JSON (PUB) ==========
void publishToMQTT(PMSReader& pms, BMEReader& bms, CCSReader& ccs) {
  
  // ========== Sensor readings plus reading integrity check ==========
  if (!pms.updateData()) Serial.println("failed reading at PMS5003");
  if (!bms.updateData()) Serial.println("failed reading at BME280");
  //if (!ccs.updateData()) Serial.println("failed reading at CCS811");
  const bool ccsUpdated = ccs.updateEvery3s(bms);
  // ========== referencing the read data ==========
  const PMSData& pmsData = pms.getData();
  const BMEData& bmeData = bms.getData();
  const CCSData& ccsData = ccs.getData();

  // ========== defining JSON document for packaging the data ==========
  StaticJsonDocument<350> doc;
  
  // ========== Parsing the data to a JSON file ==========
  // ========== bme data ==========
  doc["temperatura"] = bmeData.temp;
  doc["humedad"]     = bmeData.humid;
  doc["presion"]     = bmeData.press;

  //  ========== standard PM ==========
  doc["pm1"]  = pmsData.pm1;

  // ========== ccs data ==========
  doc["co2"]  = ccsData.co2;
  doc["tvoc"] = ccsData.tvoc;

  char buffer[400];
  serializeJson(doc, buffer);

  
  if (client.publish(topic_pub, buffer)) {
    //Serial.println("JSON enviado:");
    //Serial.println(buffer);
  } else {
    //Serial.println("Error enviando JSON.");
  } 
  
}


void reiceiveFromMQTT(char* topic, byte* payload, unsigned int length) {
  if (strcmp(topic, topic_sub) != 0) {
      return;
  }

  StaticJsonDocument<256> doc;
  DeserializationError err = deserializeJson(doc, payload, length);
  if (err) {
      Serial.print("Error parseando JSON de setpoint: ");
      Serial.println(err.c_str());
      return;
  }

  const char* target = doc["target"];
  int minSp = doc["min"] | -1;
  int maxSp = doc["max"] | -1;

  if (!target) {
    Serial.println("Setpoint recibido sin 'target'");
    return;
  }

  
  bool ok = updateSetpointByTarget(target, minSp, maxSp);

  Serial.print("Setpoint recibido para ");
  Serial.println(target);
  Serial.print("  Min: "); Serial.println(minSp);
  Serial.print("  Max: "); Serial.println(maxSp);

  if (!ok) {
    Serial.print("Target desconocido: ");
    Serial.println(target);
  }
}