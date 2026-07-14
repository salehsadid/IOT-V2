# Phase 12 - Manual Testing Guide

## Objective
Verify the Buzzer only triggers for FOG events (not Tremors), runs on a correct rhythmic pattern, and stops instantly upon recovery.

## Prerequisites
- Both Hand and Leg MPU6050 sensors wired.
- SSD1306 OLED connected.
- Active Buzzer connected to `PIN_BUZZER` (GPIO 25) and GND.
- ESP32 flashed with the Phase 12 code.
- Serial Monitor open at 115200 baud.

## Test Cases

### Test Case 1: Tremor Immunity
1. **Action:** Violently shake the Hand sensor to trigger Tremor Level 1, 2, or 3.
2. **Expected Result:** The OLED displays the Tremor alert. The buzzer remains **completely silent**.

### Test Case 2: FOG Activation
1. **Action:** Simulate a FOG event by swinging the Leg sensor normally to enter `WALKING`, and then slowing it down to a "tremble" (but not a complete stop) to trigger `FOG_CONFIRMED`.
2. **Expected Result:** 
   - The Serial Monitor prints `[BUZZER] FOG Alert Started`.
   - The buzzer begins beeping rhythmically: 400ms ON, 300ms OFF.
   - The OLED display successfully continues to render the FOG alert without freezing or lagging.

### Test Case 3: FOG Recovery
1. **Action:** While the buzzer is active, resume normal swinging of the Leg sensor.
2. **Expected Result:** 
   - The `DetectionManager` shifts to `RECOVERY`.
   - The Serial Monitor prints `[BUZZER] FOG Alert Stopped`.
   - The buzzer goes instantly silent.
