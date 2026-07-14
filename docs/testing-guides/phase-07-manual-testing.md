# Phase 7 Manual Testing Guide: Raw MPU6050 Data Acquisition

## Prerequisites
- Hardware: ESP32 development board, two MPU6050 modules connected via I2C (pins 21/22).
- The `AD0` pin of the leg sensor must be connected to `3.3V`.
- Arduino IDE with ESP32 board support installed.

## Test 1: Compilation
1. Open `firmware/firmware.ino` in Arduino IDE.
2. Click **Verify/Compile**.
3. **Expected Result:** Project compiles successfully with 0 errors and 0 warnings.

## Test 2: Startup and Wake Sequence
1. Upload the firmware to the ESP32.
2. Open the **Serial Monitor** at `115200` baud.
3. Press the `EN` (Reset) button on the ESP32.
4. **Expected Result:** You should see initialization messages:
   - "Starting Parkinson's Monitoring System ESP32 Firmware..."
   - "Initializing I2C..."
   - "Hand MPU6050 detected (0x68). Waking up and configuring..."
   - "Leg MPU6050 detected (0x69). Waking up and configuring..."

## Test 3: Sensor Data Output
1. With `DEBUG_SENSOR_OUTPUT = true` in `Config.h`, observe the Serial Monitor.
2. **Expected Result:** Every 100ms, a formatted block should appear showing Accel and Gyro readings for both the HAND and LEG sensors.
3. Values for Accel should hover near `0.0` for X and Y, and near `1.0` or `-1.0` for Z (due to gravity, depending on orientation).
4. Values for Gyro should hover near `0.0` when the sensors are stationary.

## Test 4: Hardware Fault Tolerance
1. While the system is running and printing data, disconnect the SDA wire from the Leg MPU6050 (or disconnect the entire sensor).
2. **Expected Result:** Within 5 seconds, the Serial Monitor should log an error: `Leg MPU6050 disconnected!`. The program should **not** freeze, and the Hand sensor should continue to print its values.
3. Reconnect the sensor.
4. **Expected Result:** Within 5 seconds, the Serial Monitor should log `Leg MPU6050 reconnected.`, re-initialize the sensor, and resume printing both datasets.
