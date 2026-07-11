# Phase 6 — Manual Testing Guide

**Purpose:** Steps to physically wire the hardware and verify that the ESP32 successfully detects both MPU6050 sensors.

---

## 1. Required Hardware & Wiring

You will need:
- 1x ESP32 Dev Board
- 2x MPU6050 (GY-521) modules
- Jumper wires

### Wiring Table

| MPU6050 Pin | ESP32 Pin / Connection |
|-------------|------------------------|
| **VCC**     | **3.3V** on ESP32 |
| **GND**     | **GND** on ESP32 |
| **SCL**     | **GPIO 22** |
| **SDA**     | **GPIO 21** |

### Critical AD0 Wiring
- **Hand Sensor:** Connect the `AD0` pin to **GND** (or leave it completely unconnected). Its address will be `0x68`.
- **Leg Sensor:** Connect the `AD0` pin to **3.3V**. Its address will be `0x69`.

*(Both sensors share the exact same VCC, GND, SCL, and SDA lines. Only the AD0 pin differs.)*

---

## 2. Software Setup

1. Open `d:\Academic Projects\IOT V2\firmware\firmware.ino` in the Arduino IDE.
2. Select your ESP32 board from **Tools > Board**.
3. Select your ESP32's COM port from **Tools > Port**.
4. *(No external libraries are required yet, as we are only using the built-in `Wire` library for I2C).*

---

## 3. Upload & Verify

1. Click the **Upload** button (Right arrow icon).
2. Once it says "Done uploading", immediately open the **Serial Monitor** (magnifying glass icon in top right).
3. Set the baud rate in the bottom right corner to **115200**.
4. Press the `EN` (Reset) button on your ESP32.

### Expected Output

If everything is wired correctly, you should see exactly this:

```
[INFO] Starting Parkinson's Monitoring System ESP32 Firmware...
[INFO] Initializing I2C...
[INFO] Hand MPU6050 detected (0x68)
[INFO] Leg MPU6050 detected (0x69)
[INFO] Initializing DisplayManager at address 0x3c
[INFO] DisplayManager initialization complete.
[INFO] Initializing CueingController on pin 25
[INFO] CueingController initialization complete.
[INFO] Setup complete. Entering main loop.
```

*(Note: The system will silently check the sensors every 5 seconds in the background. It will not spam the serial monitor unless you disconnect a sensor).*

---

## 4. Troubleshooting

If you do NOT get the expected output, find your symptom below:

### Symptom: `[ERROR] Hand MPU6050 not detected`
- **Cause:** The ESP32 cannot find address `0x68`.
- **Fix:** Check the wires for the Hand sensor. Ensure `AD0` is NOT connected to 3.3V. Ensure SDA/SCL are not swapped.

### Symptom: `[ERROR] Leg MPU6050 not detected`
- **Cause:** The ESP32 cannot find address `0x69`.
- **Fix:** Ensure you actually connected the `AD0` pin of the Leg sensor to the `3.3V` line. Without this, it defaults to `0x68` and collides with the hand sensor.

### Symptom: BOTH sensors say "not detected"
- **Cause:** Total I2C bus failure.
- **Fix:** 
  1. Ensure you used GPIO 21 for SDA and GPIO 22 for SCL. Do not mix them up.
  2. Verify that your breadboard power rails are actually connected to the ESP32 3.3V and GND.
  3. Ensure a common ground exists.

### Symptom: Serial Monitor prints gibberish (e.g., `⸮⸮⸮x⸮`)
- **Cause:** Baud rate mismatch.
- **Fix:** Change the dropdown in the bottom right corner of the Serial Monitor to **115200 baud**.

---

## ✅ Phase 6 Checklist

| Check | Expected |
|---|---|
| Hardware physically wired | ✅ |
| Leg sensor AD0 pin wired to 3.3V | ✅ |
| Sketch uploaded successfully | ✅ |
| Serial Monitor shows `Hand MPU6050 detected` | ✅ |
| Serial Monitor shows `Leg MPU6050 detected` | ✅ |
