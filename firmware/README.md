# Parkinson's Monitoring System - ESP32 Firmware

## Phase 5 - Foundation Status
This project currently holds the **architecture and foundation** for the ESP32 firmware. It is fully compatible with the Arduino IDE. 

**Note:** At this phase (Phase 5), there is **no actual hardware logic** implemented. The files contain skeleton structures and class headers intended to organize the codebase.

## Project Structure
- `firmware.ino`: Main Arduino sketch file containing `setup()` and `loop()`.
- `Config.h/cpp`: Central configuration for pins, network, and API settings.
- `secrets.h.example`: Template for sensitive data (SSID, Password, Token). Must be copied to `secrets.h` locally.
- `Logger.h/cpp`: Lightweight Serial logging utility.
- `SystemState.h/cpp`: Shared state object tracking hardware readiness and system status.
- `SensorManager.h/cpp`: Skeleton for future MPU6050 dual-sensor initialization and reading.
- `DisplayManager.h/cpp`: Skeleton for future OLED display management.
- `CueingController.h/cpp`: Skeleton for future vibration motor cueing control.

## Compilation Instructions
1. Open the `firmware` folder in **Arduino IDE**. (Opening `firmware.ino` will automatically load the project).
2. Select your ESP32 board from the Boards Manager (e.g., "ESP32 Dev Module").
3. Click the Verify/Compile button (Checkmark icon).
4. *Do NOT upload yet unless you just want to see the Serial log output.*

## Required Libraries (Future)
No external libraries are required to compile the Phase 5 foundation. Future phases will require:
- Adafruit_MPU6050
- Adafruit_GFX
- Adafruit_SSD1306
- ArduinoJson
- HTTPClient

## Current Implementation Status
- [x] Folder Structure
- [x] Class Skeletons
- [x] Arduino IDE Compatibility
- [x] Main Setup/Loop Initialization
- [ ] Sensor Logic (Pending)
- [ ] Display Logic (Pending)
- [ ] WiFi/API Communication (Pending)
- [ ] Detection Algorithms (Pending)
