#include <Wire.h>
#include <Adafruit_Sensor.h>
#include <Adafruit_BME280.h>
#define SEALEVELPRESSURE_HPA (1010.80)
#define LED1 16 //D0 AZUL
#define LED2  5 //D1 AZUL
#define LED3  4 //D2 MORADO
#define LED4  0 //D3 BLANCO
#define LED5  2 //D4 MORADO
#define LED6 13 //D7 BLANCO
Adafruit_BME280 bme;


void setup() {
  Serial.begin(115200);

  // Cambiar los pines I2C aquí 👇
  Wire.begin(12, 14);  // SDA = GPIO12, SCL = GPIO14 (D5)9

  if (!bme.begin(0x76)) {
    Serial.println("No se detecta el BME280");
    while (1);
  }

  pinMode(LED1, OUTPUT);
  pinMode(LED2, OUTPUT);
  pinMode(LED3, OUTPUT); 
  pinMode(LED4, OUTPUT);
  pinMode(LED5, OUTPUT);
  pinMode(LED6, OUTPUT); // empieza apagado (activo-bajo)
  digitalWrite(LED1, LOW);
  digitalWrite(LED2, LOW);
  digitalWrite(LED3, LOW);
  digitalWrite(LED4, LOW);
  digitalWrite(LED5, LOW);
  digitalWrite(LED6, LOW);
  
}

void loop() {
  float t = bme.readTemperature();         // °C
  float humedad = bme.readHumidity();                // %
  float presion = bme.readPressure() / 100.0F;       // Pa → hPa
  float altitud = bme.readAltitude(SEALEVELPRESSURE_HPA); // metros

  // Mostrar en el monitor serial
  Serial.print("🌡Temp: ");
  Serial.print(t);
  Serial.print(" °C  |  💧 Hum: ");
  Serial.print(humedad);
  Serial.print(" %  |  🌬 Presión: ");
  Serial.print(presion);
  Serial.print(" hPa  |  🏔 Altitud: ");
  Serial.print(altitud);
  Serial.println(" m");

  delay(3000);

  // SE DECLARA EN QUE RANGO SE PRENDERA CADA LED SEGUN LA TEMPERATURA QUE TENGA EL SENSOR
  
  if (altitud > 242.7 && altitud < 243) {
   digitalWrite(LED2, HIGH);
  } else{
    digitalWrite(LED2, LOW);
  }

  if (altitud > 243 && altitud < 243.3) {
   digitalWrite(LED3, HIGH);
  } else{
    digitalWrite(LED3, LOW);
  }

  if (altitud > 243.3) {
    digitalWrite(LED4, HIGH);
    } else{
      digitalWrite(LED4, LOW);
    }

  if (t > 23.5 && t < 29) {
   digitalWrite(LED5, HIGH);
  } else{
    digitalWrite(LED5, LOW);
  }

  if (t > 29 && t < 30) {
   digitalWrite(LED6, HIGH);
  } else{
    digitalWrite(LED6, LOW);
  }

  if (t > 30) {
   digitalWrite(LED1, HIGH);
  } else{
    digitalWrite(LED1, LOW);
  }


}



// prueba