# Phase 09 Refinement - Manual Testing Guide

## Objective
Verify the Tremor Algorithm's new stability mechanisms: Hysteresis, Moving Average, Consecutive Confirmation, Walking-Aware thresholds, and Smooth Transitions.

## Prerequisites
- Hand and Leg MPU6050 sensors wired.
- ESP32 flashed with the Phase 9 Refinement code.
- Serial Monitor open at 115200 baud.

## Test Cases

### Test Case 1: Hysteresis and Stability
1. **Action:** Gently and consistently shake the Hand sensor just enough to trigger a Level 1 Tremor. Attempt to hold it right at the boundary edge (around 20-25 °/s).
2. **Expected Result:** The Serial Monitor output should stabilize at Level 1 and stay there. It should not flicker back and forth between 0 and 1, owing to the 5.0 °/s Hysteresis threshold.

### Test Case 2: Smooth Level Transition
1. **Action:** Keep the sensor completely at rest for 2 seconds. Suddenly, shake the sensor violently as fast as you can.
2. **Expected Result:** The Serial Monitor should output:
   - `[Tremor] Level changed to: 1 ...`
   - `[Tremor] Level changed to: 2 ...`
   - `[Tremor] Level changed to: 3 ...`
3. **Pass Criteria:** The level should step smoothly through 1 and 2 before arriving at 3. It should never jump directly from 0 to 3 in a single print.

### Test Case 3: Walking-Aware Detection
1. **Action:** Swing both the Leg and Hand sensors rhythmically as if you were walking (simulating standard arm swing).
2. **Expected Result:** The Tremor Level should remain at 0, because the arm-swing magnitude will be roughly 30-40 °/s, which is below the new active threshold (20.0 * 1.5 = 30.0).
3. **Action 2:** While continuing to swing the leg, violently shake the Hand sensor.
4. **Expected Result 2:** Because you are shaking significantly harder than an arm swing, the level should increase to Level 1, 2, or 3, successfully bypassing the walking suppression!
