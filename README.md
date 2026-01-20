# 📡 IoT Environmental Monitoring System for Vaccine Cold-Chain Preservation

This project is an **IoT-based environmental monitoring system** designed to ensure the correct preservation of vaccines by continuously measuring **temperature, humidity, atmospheric pressure, air quality (PM), TVOC, and CO₂ levels** in real time.

The system was developed as a **functional prototype** focused on **vaccine cold-chain monitoring**, addressing one of the most critical challenges in the pharmaceutical logistics industry: preventing losses caused by failures in environmental control during storage and transportation.

---

## Project Motivation

Failures in cold-chain logistics are a major cause of vaccine waste worldwide. Studies estimate that up to **30% of vaccines are lost** due to improper temperature control, directly impacting public health and increasing operational costs.

This project aims to provide a **cost-effective, scalable, and easy-to-use monitoring solution** for small and medium-sized pharmaceutical companies, enabling real-time supervision, historical tracking, and alerting when environmental conditions exceed safe thresholds.

---

## Project Objectives

- Develop an IoT prototype capable of measuring:
  - Temperature
  - Humidity
  - Atmospheric pressure
  - Air quality (PM)
  - TVOC and CO₂
- Transmit sensor data in real time using MQTT
- Store and process data in a centralized backend
- Visualize data through a secure web platform
- Trigger visual and audible alerts when critical thresholds are exceeded
- Maintain a measurement error margin below **5%**
- Document system performance through controlled testing

---

## System Architecture Overview

### 1️⃣ IoT Device Layer
- **ESP8266 microcontroller**
- Sensors:
  - **BME280** – Temperature, Humidity, Pressure
  - **PMS5003** – Particulate Matter (Air Quality)
  - **CCS811** – TVOC and CO₂
- **Buzzer** for local audible alerts
- Prototype mounted inside a **thermal cooler** to simulate real cold-chain conditions

### 2️⃣ Communication Layer
- **Wi-Fi connectivity**
- **MQTT protocol** (Mosquitto broker)
- UART communication for sensor data acquisition

### 3️⃣ Backend Processing
- **Node-RED** for:
  - MQTT topic subscription
  - Data processing and routing
  - Integration with the database and web platform

### 4️⃣ Data Storage
- **MySQL** database
- Stores:
  - Sensor measurements (timestamped)
  - User credentials
  - Historical records

### 5️⃣ Web Platform
- Secure authentication system
- Real-time dashboard
- Historical data visualization and filtering
- Responsive design for desktop, tablet, and mobile devices

---

## Web Platform Features

- User authentication and access control
- Real-time sensor data visualization (≤ 3s delay)
- Visual alerts when conditions exceed setpoints
- Historical data records with date-based filtering
- Responsive UI for multiple devices
- Intuitive interface designed for non-technical medical staff

---

## Alerting System

- **Local alerts** via buzzer when critical conditions are detected
- **Remote alerts** sent to mobile devices using **N8N**
- Clearly distinguishable visual alert indicators on the dashboard

---

## Technologies Used

### Programming & Firmware
- C++
- Arduino IDE
- ESP8266

### IoT & Backend
- MQTT (Mosquitto)
- Node-RED
- N8N
- UART
- WebSockets

### Web & Database
- PHP
- MySQL
- HTML
- CSS
- JavaScript

---

## Use Cases

- Vaccine cold-chain monitoring
- Pharmaceutical storage supervision
- Environmental condition tracking
- IoT data visualization and alerting
- Remote monitoring systems

---

## 🏆 Recognition

🥉 **Third Place Winner — ExpoIngenierías 2025**  
**Tecnológico de Monterrey, Campus Sonora Norte**

This project was awarded third place for its innovation, real-world applicability, and successful integration of IoT hardware, backend services, and web technologies.

---

## Project Status

✅ Fully functional end-to-end prototype  
✅ Real-time data acquisition and visualization  
✅ Stable MQTT communication and backend processing  
✅ Local and remote alerting mechanisms operational  
✅ Historical data storage and retrieval implemented 


---

## 📄 License & Disclaimer

This project was developed for academic and prototype purposes. It is not certified for medical or commercial deployment without further validation and regulatory approval.

---

Feel free to explore the repository to review the firmware, backend workflows, database schema, and web platform implementation.

