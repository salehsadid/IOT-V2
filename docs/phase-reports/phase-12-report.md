# Phase 12 Report: FOG Audible Cueing System (Buzzer)

## Objectives Achieved
The goal of Phase 12 was to integrate an active buzzer that provides a rhythmic, audible cue *strictly* for Freezing of Gait (FOG) events. The critical requirements were to ensure that the buzzer never triggered for Tremor events and that the buzzer's toggle loop never blocked the main execution thread of the ESP32.

## Buzzer Architecture & Non-Blocking Design
The `BuzzerController` class was fully implemented and tied into the core `firmware.ino` update loop. 
Rather than calculating thresholds, the `BuzzerController` acts as a pure observer. It checks `systemState->isFogActive()` every loop.

When `isFogActive()` flips to true, the controller initiates the alert mode. It utilizes a state machine driven entirely by the ESP32's `millis()` timer. 
- It holds the buzzer HIGH for `BUZZER_ON_TIME_MS` (400ms).
- It drops the buzzer LOW for `BUZZER_OFF_TIME_MS` (300ms).
Because `delay()` is strictly avoided, the main `loop()` continues running at maximum speed. This ensures the I2C sensors are polled exactly on time and the OLED continues refreshing beautifully while the buzzer beeps asynchronously in the background.

When `isFogActive()` returns to false (i.e., the user enters the `RECOVERY` or `REST` state), the controller immediately drops the buzzer pin LOW and halts the timer.

## Files Modified
- `Config.h`: Stored the Buzzer parameters (`BUZZER_PIN`, `BUZZER_ON_TIME_MS`, `BUZZER_OFF_TIME_MS`).
- `BuzzerController.h/cpp`: Built the non-blocking `update()` loop and internal state tracking variables.
- `docs/phase-status.md` & `firmware/README.md`: Updated documentation.

## Known Limitations
- The buzzer operates purely on GPIO digital highs/lows. Tone/frequency manipulation (via `tone()` or PWM `ledcWrite()`) is not implemented, meaning the pitch is fixed by the hardware of the active buzzer itself. This is standard for simple active buzzers.
