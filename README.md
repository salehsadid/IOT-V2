# ESP32-Based Parkinson's Tremor and Freezing of Gait Monitoring System

> **Comprehensive Project Report & Documentation**

---

## 📖 Project Description

This project is an advanced, prototype IoT medical monitoring system designed to assist caregivers and clinicians in observing Parkinson's disease-related motor symptoms in near real-time. 

The system utilizes an ESP32 microcontroller and dual MPU6050 inertial sensors (accelerometer + gyroscope) to detect two primary symptoms of Parkinson's Disease:
1. **Resting Tremors** (Hand)
2. **Freezing of Gait (FOG)** (Legs/Ankle)

All detection logic runs **locally on the ESP32** using threshold-based algorithms. When a FOG event occurs, the system automatically triggers an active buzzer to deliver rhythmic cueing, helping the patient break the freeze. Real-time statuses and historical logs are transmitted over Wi-Fi to a Laravel-based web dashboard, where caregivers can monitor the patient, view statistics, and remotely override the buzzer.

*Note: This is a university research prototype and not a certified medical device.*

---

## 🚀 Key Features

- **Dual-Sensor Monitoring:** Simultaneous tracking of hand and leg movements.
- **On-Device Edge Computing:** FOG and Tremor algorithms execute natively on the ESP32 without relying on cloud processing.
- **Rhythmic Cueing:** Automatic auditory/vibration feedback to assist patients during a FOG episode.
- **Live Web Dashboard:** A premium, glassmorphism-styled Laravel dashboard for real-time monitoring and daily statistics.
- **Remote Alarm Control:** Caregivers can silence the patient's buzzer remotely directly from the web interface.
- **Telegram Integration:** Instant push notifications sent to caregivers when a critical Tremor or FOG event occurs.
- **Offline Resilience:** The ESP32 caches data and manages its own state even if the web server temporarily disconnects.

---

## 🎯 Objectives

1. **Continuous Monitoring:** Provide a non-invasive wearable solution for 24/7 symptom tracking.
2. **Immediate Intervention:** Utilize auditory cueing to actively assist patients in overcoming gait freezing.
3. **Data Logging for Clinicians:** Store immutable event logs (Duration, Timestamp, Severity Level) to aid doctors in adjusting medication.
4. **Remote Caregiver Awareness:** Ensure caregivers are immediately notified of emergencies via Telegram and the Live Dashboard.

---

## 🛠 Components Used

### Hardware
- **ESP32 Development Board** (Wi-Fi enabled Microcontroller)
- **2x MPU6050** (6-DOF Accelerometer and Gyroscope)
- **0.96" OLED Display** (I2C)
- **Active Buzzer** (or Vibration Motor)
- Jumper Wires & Breadboard

### Software & Stack
- **C++ (Arduino Framework):** Firmware logic, I2C communication, HTTP client.
- **PHP 8.2 & Laravel 12:** Backend API, Dashboard routing, and Eloquent ORM.
- **MySQL:** Relational database for historical event logs.
- **HTML/CSS/Vanilla JS:** Frontend UI utilizing CSS variables and modern glassmorphism.

---

## 🔌 Circuit Diagram & Connections

The hardware communicates primarily via the I2C protocol. Because both sensors are identical, the Leg MPU6050's `AD0` pin must be pulled HIGH to assign it a unique I2C address.

| Component | Pin | ESP32 Pin | Notes |
|---|---|---|---|
| **OLED Display** | SDA | GPIO 21 | I2C Address `0x3C` |
| | SCL | GPIO 22 | |
| **Hand MPU6050** | SDA | GPIO 21 | I2C Address `0x68` (Default) |
| | SCL | GPIO 22 | |
| | AD0 | GND | Keeps address at `0x68` |
| **Leg MPU6050** | SDA | GPIO 21 | I2C Address `0x69` |
| | SCL | GPIO 22 | |
| | AD0 | 3.3V | **CRITICAL:** Changes address to `0x69` |
| **Buzzer** | Signal | GPIO 25 | Active HIGH |

---

## ⚙️ How It Works

### Tremor Detection Algorithm
The system samples the **Hand MPU6050** every 100ms. It calculates the moving average (MA) of the gyroscope magnitude over a sliding window. 
- If the Leg MPU detects the patient is walking, Tremor detection is suppressed to prevent false positives from arm swings.
- If the patient is at rest, the algorithm compares the moving average against 3 severity thresholds.
- If the threshold is exceeded continuously for **1.5 seconds**, a Tremor Event is confirmed and logged.

### Freezing of Gait (FOG) Algorithm
The system implements a complex State Machine for the **Leg MPU6050**:
`REST -> POSSIBLE_WALKING -> WALKING -> POSSIBLE_FOG -> FOG_CONFIRMED -> RECOVERY`
- When a patient transitions from `WALKING` into a state where leg movement drops below walking thresholds but remains above resting thresholds (trembling/stuttering steps), the state changes to `POSSIBLE_FOG`.
- If this state persists for 2 seconds, it escalates to `FOG_CONFIRMED`.
- Cueing (Buzzer) is instantly activated.

### Web & API Communication
- **Heartbeat:** The ESP32 sends a JSON POST request to the Laravel `/api/heartbeat` endpoint every 5 seconds (or instantly on state change).
- **Event Logging:** Once an event concludes, its full duration and max severity are POSTed to `/api/events`.
- **Remote Control:** The dashboard writes a `STOP_BUZZER` command to the Laravel Cache. The ESP32 pulls this command during its next heartbeat and silences the hardware buzzer.

---

## 🚀 Further Implementation

This prototype establishes a strong foundation, but can be expanded in the future:
1. **Machine Learning Integration:** Replacing the threshold-based algorithm with a TinyML model (e.g., TensorFlow Lite for Microcontrollers) trained on actual patient datasets.
2. **Mobile App:** Developing a Flutter/React Native application via the existing Laravel APIs for better caregiver mobility.
3. **Wi-Fi Manager (Captive Portal):** Implementing `WiFiManager.h` so the SSID and Password can be configured via a smartphone without recompiling the firmware.
4. **OTA Updates:** Allowing over-the-air firmware updates from the Laravel dashboard.

---

## 📚 References & Documentation

- **[System Architecture Document](architecture.md)** — Detailed flowcharts, sequence diagrams, and module responsibilities.
- **[Hardware Wiring Guide](hardware-wiring.md)** — Complete pin mappings, I2C explanations, and physical setup instructions.
- **MPU6050 Datasheet:** InvenSense MPU-6000 and MPU-6050 Product Specification.
- **ESP32 Documentation:** Espressif IoT Development Framework.
- **Laravel 12 Documentation:** https://laravel.com/docs/12.x
- **Parkinson's FOG Research:** Clinical studies on rhythmic auditory and vibrotactile cueing for Freezing of Gait intervention.

---

## 🛠️ User Guide

For a complete, step-by-step guide on how to install, configure, simulate, and troubleshoot this project, please read the:

👉 **[HOW TO USE & SIMULATE GUIDE](HOW_TO_USE.md)**
