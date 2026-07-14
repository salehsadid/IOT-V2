# Phase 8 Manual Testing Guide: Threshold Calibration & Motion Analysis

## Prerequisites
- Hardware: ESP32 board, two MPU6050 modules connected via I2C.
- `DEBUG_SENSOR_OUTPUT` set to `true` in `Config.h`.

## Test 1: Compilation
1. Open `firmware/firmware.ino` in Arduino IDE.
2. Click **Verify/Compile**.
3. **Expected Result:** Project compiles successfully with 0 errors.

## Test 2: Serial Magnitude Output
1. Upload the firmware to the ESP32.
2. Open the **Serial Monitor** at `115200` baud.
3. Observe the data streams for both HAND SENSOR and LEG SENSOR.
4. **Expected Result:** You should now see `Accel Mag : ... g` and `Gyro Mag  : ... °/s` printed clearly above the raw X/Y/Z variables every 100ms.

## Test 3: Magnitude Verification
1. Place the sensors perfectly flat and stationary on a desk.
2. **Expected Result:** 
   - `Accel Mag` should be extremely close to `1.000` g (due to gravity).
   - `Gyro Mag` should be very close to `0.000` °/s (ideally `< 5.0` °/s).

## Test 4: Threshold Monitoring
Use this phase to physically perform the following actions and manually verify they cross the designated thresholds in `Config.h` without triggering false positives:
- **Phase 4A (Rest):** Do not move. Verify `Gyro Mag` is below `REST_GYRO_THRESHOLD` (5.0 °/s).
- **Phase 4B (Tremor):** Vigorously shake the Hand sensor while keeping the Leg sensor still. Verify Hand `Gyro Mag` frequently exceeds `HAND_TREMOR_THRESHOLD` (20.0 °/s).
- **Phase 4C (Walking):** Rhythmically swing the Leg sensor. Verify Leg `Gyro Mag` frequently exceeds `LEG_WALKING_THRESHOLD` (15.0 °/s).

*(Note: No alarms or detections will occur in this phase. This test is purely to ensure the numbers align with physical reality before Phase 9).*
