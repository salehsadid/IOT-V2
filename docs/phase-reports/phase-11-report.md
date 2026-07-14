# Phase 11 Report: OLED Display Integration

## Objectives Achieved
The goal of this phase was to connect an SSD1306 OLED via I2C to act as a pure, passive display for the ESP32 Parkinson Monitoring system. The OLED was programmed to track all data visually without implementing any standalone decision-making logic.

## Display Logic and Architecture
The `DisplayManager` was fully implemented utilizing the `Adafruit_GFX` and `Adafruit_SSD1306` libraries.

The OLED operates at a stable refresh rate of `500ms` (configurable via `Config::OLED_REFRESH_INTERVAL`), querying the central `SystemState` on every loop. 

A standard UI state machine handles the visual display:
- **`SCREEN_BOOT`**: Shown for 2 seconds on startup. Confirms initialization.
- **`SCREEN_NORMAL`**: The default monitoring view. It tracks the hardware status of the Hand and Leg MPUs (OK/ERR), the current `MotionState` (e.g. WALKING, REST), and the `currentTremorLevel`.
- **`SCREEN_ALERT`**: If `SystemState` reports a `Tremor Level > 0` or `fogActive == true`, the display is hijacked. It flashes large, readable text indicating the alert. The alert persists for a configurable `OLED_ALERT_DURATION` (3 seconds) to ensure it is noticed.
- **`SCREEN_ERROR`**: A fatal screen that locks the display if the system detects that an MPU6050 sensor has disconnected.

## Files Modified
- `Config.h`: Stored the OLED parameters (`OLED_WIDTH`, `OLED_HEIGHT`, refresh and alert durations).
- `DisplayManager.h`: Included the Adafruit UI libraries and declared the screen states.
- `DisplayManager.cpp`: Implemented the full UI drawing logic using standard `Adafruit_GFX` cursor and text functions.

## Known Limitations
- The OLED shares the I2C bus with the MPU6050 sensors. While highly efficient, if the I2C bus crashes due to a loose wire, the OLED will freeze on its last frame. This is mitigated by the hardware `SCREEN_ERROR` state, which should catch most physical disconnects before a full bus crash.
