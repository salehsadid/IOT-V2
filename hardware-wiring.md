# Hardware Wiring Guide

## Complete Wiring Table

| Component | ESP32 Pin | Note |
|-----------|-----------|------|
| **I2C SDA** | GPIO 21 | Shared across both MPU6050 sensors & OLED |
| **I2C SCL** | GPIO 22 | Shared across both MPU6050 sensors & OLED |
| **Power** | 3.3V | VCC of all components connected to ESP32 3.3V out |
| **Ground** | GND | Common ground for ESP32 and all components |
| **Buzzer** | GPIO 25 | Active Buzzer Signal (PWM not strictly required) |

## Component Breakdown

### 1. Hand Sensor (MPU6050 #1)
- **SDA:** GPIO 21
- **SCL:** GPIO 22
- **AD0:** `GND` (Leaves address as `0x68`)

### 2. Leg Sensor (MPU6050 #2)
- **SDA:** GPIO 21
- **SCL:** GPIO 22
- **AD0:** `3.3V` (Changes address to `0x69`)

### 3. OLED Display (0.96" SSD1306)
- **SDA:** GPIO 21
- **SCL:** GPIO 22
- **Address:** Automatically `0x3C`

### 4. Buzzer
- **Signal:** GPIO 25
- **GND:** GND
- **Role:** Emits rhythmic medical alarm `(150ms ON, 100ms OFF)` during Freezing of Gait (FOG).

## The AD0 Pin Explanation

By default, an MPU6050 sensor has an I2C address of **0x68**. 
I2C is a bus protocol, meaning multiple devices can share the same two wires (SDA and SCL). However, every device on the bus must have a **unique address**.

If you connect two MPU6050s out of the box, they will both respond to `0x68`, causing data collisions and making it impossible to read them individually.

To fix this, the MPU6050 has an `AD0` (Address 0) pin. 
- When `AD0` is connected to **GND**, the address is **0x68**.
- When `AD0` is connected to **3.3V (HIGH)**, the internal logic changes the address to **0x69**.

### Required Action:
For this project, you must connect the `AD0` pin of the **Leg** sensor to **3.3V**. This allows the ESP32 to differentiate between the Hand sensor (`0x68`) and the Leg sensor (`0x69`).

## Important Notes & Best Practices

1. **Common Ground:** It is an absolute requirement that all components share a common `GND`. Without a common ground reference, high/low voltage signals cannot be interpreted correctly by the ESP32.
2. **Wire Length:** I2C is designed for short-distance, intra-board communication. Because the Leg sensor will be physically distant from the ESP32, you must ensure the wires are of high quality. If communication fails intermittently, add stronger pull-up resistors (e.g., 4.7kΩ).
3. **Soldering:** Breadboard connections are prone to failure during human movement. For a wearable monitoring device, always solder connections securely.
