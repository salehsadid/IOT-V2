# Hardware Wiring Guide

## Complete Wiring Table

| Component | ESP32 Pin | Note |
|-----------|-----------|------|
| I2C SDA   | GPIO 21   | Shared across both MPU6050 sensors |
| I2C SCL   | GPIO 22   | Shared across both MPU6050 sensors |
| Power     | 3.3V      | VCC of both sensors connected to ESP32 3.3V out |
| Ground    | GND       | Common ground for ESP32 and both sensors |

## ESP32 Pin Mapping

- **SDA (Data):** GPIO 21
- **SCL (Clock):** GPIO 22

*(Note: While the ESP32 supports hardware I2C multiplexing on many pins, GPIO 21 and 22 are the standard default hardware I2C pins and should be used to ensure maximum library compatibility).*

## Power Requirements

The MPU6050 operates logically at 3.3V. While some breakout boards (like the GY-521) contain an onboard voltage regulator allowing 5V on the VCC pin, it is safest and most standard to power them directly from the ESP32's **3.3V** pin to ensure logic levels match perfectly.

## The AD0 Pin Explanation

By default, an MPU6050 sensor has an I2C address of **0x68**. 
I2C is a bus protocol, meaning multiple devices can share the same two wires (SDA and SCL). However, every device on the bus must have a **unique address**.

If you connect two MPU6050s out of the box, they will both respond to `0x68`, causing data collisions and making it impossible to read them individually.

To fix this, the MPU6050 has an `AD0` (Address 0) pin. 
- When `AD0` is connected to **GND** (or left floating on most boards, as they have an internal pull-down), the address is **0x68**.
- When `AD0` is connected to **3.3V (HIGH)**, the internal logic changes the address to **0x69**.

### Required Action:
For this project, you must connect the `AD0` pin of the **Leg** sensor to **3.3V**. This allows the ESP32 to differentiate between the Hand sensor (`0x68`) and the Leg sensor (`0x69`).

## Important Notes & Best Practices

1. **Common Ground:** It is an absolute requirement that all components share a common `GND`. Without a common ground reference, high/low voltage signals cannot be interpreted correctly by the ESP32.
2. **Wire Length:** I2C is designed for short-distance, intra-board communication. Because the Leg sensor will be physically distant from the ESP32, you must ensure the wires are of high quality. If communication fails intermittently, you may need to add stronger pull-up resistors (e.g., 4.7kΩ) or lower the I2C clock speed in future phases.
3. **Soldering:** Breadboard connections are prone to failure during human movement. For a wearable monitoring device, always solder connections securely.
