# Phase 7 Report: Raw MPU6050 Data Acquisition

## Objectives Achieved
- [x] Defined MPU6050 register addresses for power management, config, and data output in `SensorManager.h`.
- [x] Implemented `wakeSensor()` method to write `0x00` to the `PWR_MGMT_1` register, bringing the sensors out of sleep mode.
- [x] Implemented `configureSensor()` method to set the accelerometer range to ±2g and gyroscope range to ±250°/s.
- [x] Implemented `readSensorData()` method to fetch 14 consecutive bytes (accel, temp, gyro) from `ACCEL_XOUT_H`.
- [x] Created `SensorData` struct to hold parsed and converted floating-point values.
- [x] Applied appropriate scaling factors (16384.0 for Accel, 131.0 for Gyro) to convert raw data to `g` and `°/s`.
- [x] Implemented a 100ms non-blocking reading loop in `SensorManager::update()`.
- [x] Created a formatted `printSensorData()` function to cleanly output the readings.
- [x] Added `DEBUG_SENSOR_OUTPUT` constant in `Config.h` to toggle serial verbosity.
- [x] Ensured graceful failure if a sensor disconnects, updating `SystemState` and printing a single error instead of freezing the program.
- [x] Updated `firmware/README.md` with new configuration details.

## Code Quality and Architecture
- No code was added to `firmware.ino` directly; all logic resides inside `SensorManager` as per requirements.
- The conversion logic is clean and relies on constant scaling factors `ACCEL_SCALE` and `GYRO_SCALE`.
- Hardware faults (e.g., I2C timeouts or sensor disconnections) are handled non-destructively.

## Next Steps (Phase 8 - DO NOT START)
Phase 8 will introduce the Freezing of Gait (FOG) detection state machine using the data acquired in this phase.
