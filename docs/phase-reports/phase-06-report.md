# Phase 6 Report — Hardware Initialization & Dual MPU6050 Detection

**Status:** ✅ Completed  
**Date:** 2026-07-11  

---

## Phase Summary

Phase 6 successfully initialized the ESP32 hardware and I2C bus, establishing the communication foundation required for the Dual MPU6050 sensor array. The system now actively detects the presence of both the Hand sensor (`0x68`) and the Leg sensor (`0x69`). 

Following the strict constraints of the project roadmap, **no actual sensor reading or mathematical processing** (e.g., accelerometer/gyroscope extraction) was implemented. The system solely verifies the health and availability of the sensors on the bus and cleanly logs the results using the `Logger` utility.

---

## Hardware Implementation Details

### I2C Bus Initialization
- The I2C bus is initialized safely within the `SensorManager::init()` method using `Wire.begin()`.
- The pins are dynamically loaded from `Config` (`GPIO 21` for SDA, `GPIO 22` for SCL). Hardcoding was explicitly avoided.

### Dual MPU6050 Detection
- Implemented a standard I2C ping utility (`SensorManager::checkSensor`) which utilizes `Wire.beginTransmission()` and `Wire.endTransmission()` to check for `ACK` signals at specific addresses.
- Hand MPU6050 is expected at `0x68`.
- Leg MPU6050 is expected at `0x69`.

### State Management & Logging
- The system checks the sensor status once during `setup()`, and then periodically every 5 seconds inside the `SensorManager::update()` loop.
- If a sensor is detected, `SystemState` is updated to `ready = true` and an `[INFO]` log is fired.
- If a sensor is missing or disconnected, `SystemState` is updated to `ready = false` and an `[ERROR]` log is fired, gracefully preventing a system crash.

---

## Documentation Created

- **`firmware/README.md`**: Updated to include the critical wiring table for I2C and the `AD0` pin.
- **`docs/hardware-wiring.md`**: Created a dedicated, highly detailed markdown document explaining power constraints, I2C logic, and exactly why/how the `AD0` pin must be pulled HIGH (3.3V) to alter the Leg MPU6050 address to `0x69`.
- **`docs/testing-guides/phase-06-manual-testing.md`**: Created a robust guide covering wiring, Arduino IDE setup, compilation, expected Serial output, and comprehensive troubleshooting steps.

---

## Known Limitations & Next Steps

1. **No Data Extraction:** The sensors are pinged for presence, but their FIFO buffers are not being read. 
2. **Wire Length Physics:** As documented in the wiring guide, I2C may struggle over long distances. If the Leg sensor frequently drops connection in physical testing, the I2C clock speed will need to be lowered or pull-up resistors added in future phases.
