# Parkinson's Monitoring System - ESP32 Firmware

## Phase 10 - FOG & Tremor Detection
This project currently implements the **Phase 10** detection algorithms for both Tremor and Freezing of Gait (FOG). It tracks instantaneous movements, smooths them with moving averages, and runs them through `DetectionManager` to evaluate tremor levels and motion states.

### Tremor Algorithm
- **Walking-Aware**: Raises tremor threshold by 1.5x while walking to filter out arm-swings.
- **Hysteresis & Confirmation**: Prevents level flickering using a 5.0 °/s margin and 3-sample confirmation requirement.
- **Smooth Transition**: Forces smooth `0 -> 1 -> 2 -> 3` level transitions.

### Freezing of Gait (FOG) Algorithm
- **State Machine**: Tracks motion through 6 states (`REST`, `POSSIBLE_WALKING`, `WALKING`, `POSSIBLE_FOG`, `FOG_CONFIRMED`, `RECOVERY`).
- **Standing Discrimination**: Using real-world data, the algorithm recognizes that Standing yields a Leg Gyro of `< 5.0 °/s`, whereas FOG yields trembling between `5.0` and `40.0 °/s`. 
- **Time-Confirmed**: When a drop in speed occurs, a 2-second timer starts. If the leg becomes completely still (`< 5.0`) it enters `REST`. If it trembles for the full 2 seconds, it triggers `FOG_CONFIRMED`.

## Phase 11 - OLED Display Integration
The **Phase 11** firmware introduces a completely decoupled visual UI using a 128x64 SSD1306 OLED display.
The display acts purely as a passive observer, querying `SystemState` twice a second to determine which screen to render:
- **Boot Screen**: Displays the project name and firmware version on startup.
- **Normal Screen**: Tracks Hand/Leg sensor connection status, current Motion State, and current Tremor Level.
- **Alert Screen**: Overrides the display to flash a large "TREMOR DETECTED" or "FOG DETECTED" warning for 3 seconds when triggered.
- **Error Screen**: Halts the UI and explicitly states which sensor disconnected.

## Phase 12 - FOG Audible Cueing System (Buzzer)
The **Phase 12** firmware introduces an active buzzer system designed explicitly for Freezing of Gait (FOG) cueing.
- **Strict FOG Targeting**: The buzzer *only* activates when the `SystemState` reports a confirmed FOG event. Tremor events (Levels 1-3) are strictly silent.
- **Rhythmic Cueing**: When a freeze is detected, the buzzer emits a rhythmic beep pattern (400ms ON, 300ms OFF) to help the patient break the freeze.
- **Non-Blocking Architecture**: The buzzer toggle is driven by `millis()`, ensuring that I2C sensor reads, motion state calculations, and OLED screen updates continue flawlessly without any CPU freezing.
- **Instant Recovery**: The moment the user resumes walking and triggers the `RECOVERY` state, the buzzer shuts off.

## Phase 13 - Cloud Sync & Backend Logging
The **Phase 13** update transforms the system from a localized offline prototype into an IoT cloud device.
- **Dedicated ApiClient**: A custom, non-blocking HTTP module parses and uploads events exactly the moment they conclude. 
- **Bandwidth Efficiency**: It does *not* upload continuous telemetry data. It exclusively uploads confirmed `TREMOR` and `FOG` events.
- **Smart Timekeeping**: The ESP32 tracks the peak severity (`max_level`) and total length (`duration_ms`) of an event, allowing the Laravel backend to retroactively generate standard ISO timestamps without requiring an RTC module on the hardware.

### Required Libraries
To compile this firmware, you must install the following libraries via the Arduino Library Manager:
- **Adafruit GFX Library**
- **Adafruit SSD1306**

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

## ESP32 Wiring

| Component | ESP32 Pin | Note |
|-----------|-----------|------|
| I2C SDA   | GPIO 21   | Must be shared across both sensors |
| I2C SCL   | GPIO 22   | Must be shared across both sensors |
| Power     | 3.3V      | VCC of both sensors to ESP32 3.3V |
| Ground    | GND       | Common ground for ESP32 and both sensors |

### MPU6050 Configuration
- **Hand MPU6050 (0x68):** Connect `AD0` pin to `GND` (or leave floating on most breakout boards).
- **Leg MPU6050 (0x69):** **CRITICAL:** Connect `AD0` pin to `3.3V`. This changes the I2C address to 0x69, allowing both sensors to share the same I2C bus.

### Sensor Configuration (Phase 8)
- **Accelerometer Range:** ±2g (Sensitivity: 16384 LSB/g)
- **Gyroscope Range:** ±250°/s (Sensitivity: 131 LSB/°/s)

**Formulas (Raw Conversion & Magnitude):**
- `Accel (g) = Raw_Value / 16384.0`
- `Gyro (°/s) = Raw_Value / 131.0`
- `Magnitude = sqrt(X² + Y² + Z²)`

### Calibration Procedure & Thresholds
Initial calibration variables are stored centrally in `Config.h`. Do not hardcode these in detection logic. Current baseline references based on experimental data:
- `REST_GYRO_THRESHOLD`: 5.0 °/s
- `HAND_TREMOR_THRESHOLD`: 20.0 °/s
- `LEG_WALKING_THRESHOLD`: 15.0 °/s

### Debug Mode
Set `DEBUG_SENSOR_OUTPUT = true` in `Config.h` to print formatted raw values and calculated magnitudes to the Serial Monitor every 100ms. Use these outputs to refine the initial thresholds.

## Official Project Roadmap
- [x] Phase 0–7: Setup & Raw Data Acquisition
- [x] Phase 8: Threshold Calibration & Motion Analysis
- [ ] Phase 9: Threshold-Based Tremor Detection
- [ ] Phase 10: Threshold-Based Freezing of Gait (FOG) Detection
- [ ] Phase 11: OLED Display Integration
- [ ] Phase 12: Buzzer Alert System
- [ ] Phase 13: ESP32 ↔ Laravel API Communication
- [ ] Phase 14: Live Dashboard, Event Logging & Remote Alarm Control
- [ ] Phase 15: Full System Integration, Testing & Final Validation
