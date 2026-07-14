# Phase 10 - Manual Testing Guide

## Objective
Verify the Freezing of Gait (FOG) state machine transitions and validate that standing still does not falsely trigger FOG.

## Prerequisites
- Both Hand and Leg MPU6050 sensors wired.
- ESP32 flashed with the Phase 10 code.
- Serial Monitor open at 115200 baud.

## Test Cases

### Test Case 1: Rest to Walking
1. **Action:** Keep the Leg sensor perfectly still. Then, begin swinging it back and forth continuously (simulating steps).
2. **Expected Result:**
   - `[Motion] REST -> POSSIBLE_WALKING` (Instantly upon moving)
   - `[Motion] POSSIBLE_WALKING -> WALKING` (After 2 seconds of continuous swinging)

### Test Case 2: Walking to Standing (No False FOG)
1. **Action:** While in the `WALKING` state, abruptly stop swinging the sensor and hold it perfectly still on a flat surface.
2. **Expected Result:**
   - `[Motion] WALKING -> POSSIBLE_FOG` (Instantly upon stopping)
   - `[Motion] POSSIBLE_FOG -> REST` (Almost instantly, as the sensor drops below 5.0 °/s before the 2-second FOG timer expires).
3. **Pass Criteria:** `FOG_CONFIRMED` must NEVER print during this test.

### Test Case 3: Walking to FOG Event
1. **Action:** While in the `WALKING` state, slow down your swinging drastically and begin "trembling" or shaking the Leg sensor lightly (simulating a stutter step). Do not completely stop!
2. **Expected Result:**
   - `[Motion] WALKING -> POSSIBLE_FOG` (Instantly upon slowing down)
   - `[Motion] POSSIBLE_FOG -> FOG_CONFIRMED` (After 2 seconds of continuous trembling).

### Test Case 4: FOG to Recovery
1. **Action:** While in `FOG_CONFIRMED`, resume large, fast swings (simulating normal walking resuming).
2. **Expected Result:**
   - `[Motion] FOG_CONFIRMED -> RECOVERY` (Instantly upon large movement)
   - `[Motion] RECOVERY -> WALKING` (After 2 seconds of continuous large movement).
