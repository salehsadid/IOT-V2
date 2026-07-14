# Phase 09 - Manual Testing Guide

## Objective
Verify that Tremor Level (0, 1, 2, 3) is correctly detected based on Hand MPU6050 magnitudes, and that Leg MPU6050 correctly suppresses tremor false alarms during walking.

## Prerequisites
- Both Hand and Leg MPU6050 sensors wired and detected (I2C 0x68 and 0x69).
- ESP32 flashed with the Phase 9 code.
- Serial Monitor open at 115200 baud.

## Test Cases

### Test Case 1: Resting (Level 0)
1. **Action:** Sit perfectly still with both sensors on a flat surface.
2. **Expected Log:** `[Tremor] Level changed to: 0` (if it was previously non-zero). No further tremor prints.
3. **Pass Criteria:** Serial Monitor does not output any Level 1, 2, or 3 warnings.

### Test Case 2: Hand Tremor (Level 1, 2, 3)
1. **Action:** Keep the Leg sensor perfectly still. Shake the Hand sensor in a back-and-forth motion.
   - Gently shake for Level 1.
   - Moderately shake for Level 2.
   - Violently shake for Level 3.
2. **Expected Log:** `[Tremor] Level changed to: X | HandAvg: YY.Y | LegAvg: ZZ.Z`
3. **Pass Criteria:** The level actively updates (1, 2, or 3) correlating to the intensity of your shake.

### Test Case 3: Tremor Suppression During Walking
1. **Action:** Shake the Hand sensor (to simulate Tremor) AND simultaneously swing/shake the Leg sensor continuously.
2. **Expected Log:** `[Tremor] Level changed to: 0`
3. **Pass Criteria:** Because the Leg sensor is moving rapidly (MA > 15), the system ignores the hand movement and suppresses the Tremor warning.
