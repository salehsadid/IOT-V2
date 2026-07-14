# Phase 11 - Manual Testing Guide

## Objective
Verify that the SSD1306 OLED properly displays Boot, Normal, Alert, and Error screens, and updates seamlessly in real-time according to sensor inputs.

## Prerequisites
- Both Hand and Leg MPU6050 sensors wired.
- SSD1306 OLED connected to I2C pins.
- **Adafruit GFX Library** and **Adafruit SSD1306** installed via the Arduino IDE Library Manager.
- ESP32 flashed with the Phase 11 code.

## Test Cases

### Test Case 1: Boot Sequence
1. **Action:** Reset the ESP32.
2. **Expected Result:** The OLED should instantly display:
   ```
   Parkinson Monitor
   Initializing...
   v1.0.0
   ```
   This screen should hold for 2 seconds before moving to the Normal screen.

### Test Case 2: Normal Monitoring Screen
1. **Action:** Keep the sensors still.
2. **Expected Result:** The OLED should display:
   ```
   Hand:OK Leg:OK
   
   Motion: REST
   
   Tremor: Level 0
   ```
3. **Action 2:** Start walking with the Leg sensor.
4. **Expected Result 2:** "Motion: REST" should update to "Motion: WALKING" without flickering.

### Test Case 3: Alert Screen (Tremor & FOG)
1. **Action:** Violently shake the Hand sensor to trigger Tremor Level 3.
2. **Expected Result:** The screen should be hijacked by a large font displaying:
   ```
     TREMOR
     Level 3
   ```
   This alert should hold on the screen for a full 3 seconds before reverting back to the Normal screen (assuming you stopped shaking).
3. **Action 2:** Trigger a FOG event by shaking the Leg sensor lightly.
4. **Expected Result 2:** The screen should display:
   ```
       FOG
     DETECTED
   ```

### Test Case 4: Sensor Disconnect (Error Screen)
1. **Action:** While the system is running, physically disconnect the VCC or SDA wire from the Hand MPU6050.
2. **Expected Result:** Within 500ms, the screen should display:
   ```
   SENSOR ERROR!
   
   - Hand MPU Missing
   ```
