#include <Wire.h>
#include <ESP8266WiFi.h>
#include <ArduinoJson.h>
#include <SoftwareSerial.h>
#include "wifi.h"
#include "mqtt.h"
#include "airQualityAnalyzer.h"
#include "bmeAnalyzer.h"
#include "airQualityAnalyzer.h"
#include "setpoints.h"
#include "css811Reader.h"
#include "pins.h"

// ===== bme setup =====
Adafruit_BME280 bme280;
BMEReader bme(bme280);

// ===== Initiliazing PMS5003 (PMserial) =====
// Constructor recomendado por la librería:
// SerialPM pms(PMSx003, RX, TX);
SoftwareSerial pmsSerial(pins::PMS_RX, pins::PMS_TX);
PMSReader pms(pmsSerial);

// ===== css setup =====
Adafruit_CCS811 ccs811;
CCSReader ccs(ccs811);

// SET-POINTS
int sp_temp_min = -1, sp_temp_max = -1;
int sp_hum_min  = -1, sp_hum_max  = -1;
int sp_pres_min = -1, sp_pres_max = -1;
int sp_co2_min  = -1, sp_co2_max  = -1;
int sp_tvoc_min = -1, sp_tvoc_max = -1;
int sp_pm1_min  = -1, sp_pm1_max  = -1;

// -------- CONECTAR WIFI --------


// -------- RECONNECT MQTT --------




// ------- ALARMS --------


// ===== SETUP =====
void setup() {
  Serial.begin(115200); // inicializando baud rate

  // ===== pms ======
  pmsSerial.begin(9600);   // configure interanl serial port to 9600

  // ===== bme ======
  Wire.begin(pins::I2C_SDA, pins::I2C_SCL); // SDA, SCL
  if (!bme280.begin(0x76)) { // caso: direccion de memoria del bme no encontrada
    Serial.println("ERROR: No se encontró BME280.");
    while (true) { delay(1000); } // evita Soft WDT;
  }

  Serial.println("Sensores inicializados correctamente.");

  // ===== ccs =====
  if (!ccs811.begin()) {
    Serial.println("No se pudo iniciar el sensor CCS811. Verifica cableado!");
    while (true) { delay(1000); } // evita Soft WDT;
  }

  // Configurar modo de medición (1 lectura/seg)
  ccs811.setDriveMode(CCS811_DRIVE_MODE_1SEC);

  // Esperar a que se estabilice (warm-up)
  Serial.println("Esperando a que el sensor esté listo...");
  while (!ccs811.available()) delay(100);
  Serial.println("CCS811 listo!");
  
  
  // ===== wifi setup =====
  setup_wifi();
  client.setServer(mqttServer, mqttPort);

  setup_mqtt();

  // ===== alarm ======
  pinMode(pins::BUZZER, OUTPUT);
  digitalWrite(pins::BUZZER, LOW);

}

// ===== LOOP =====
void loop() {
  if (!client.connected()) reconnect();
  client.loop();

  publishToMQTT(pms, bme, ccs);
  delay(3000);
  
  

  // ===== Alarms =====
  const BMEData& bmeData = bme.getData();
  const PMSData& pmsData = pms.getData();
  ccs.updateEvery3s(bme);
  const CCSData& ccsData = ccs.getData();
  bool alarmOn = 
    isOutOfRange("temperatura", bmeData.temp) ||
    isOutOfRange("humedad",     bmeData.humid) ||
    isOutOfRange("presion",     bmeData.press) ||
    isOutOfRange("pm1",         pmsData.pm1);
  
  digitalWrite(pins::BUZZER, alarmOn ? HIGH : LOW);
  // serial monitor 
  //Serial.println(bme.toString());
  Serial.println(pms.toString());
  Serial.println(ccs.toString());


}

