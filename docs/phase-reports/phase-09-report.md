# Phase 9 Report: Threshold-Based Tremor Detection

## Objectives Achieved
1. **Data Processing:** Evaluated 8 sets of demonstration data (Rest, Walking, Fast Walking, FOG, Tremor Level 1, 2, 3) to build the logic constraints.
2. **Tremor Level Calibration:** Set explicit `HAND_TREMOR_THRESHOLD` replacements:
   - `TREMOR_LEVEL_1_THRESHOLD` = 20.0 °/s
   - `TREMOR_LEVEL_2_THRESHOLD` = 120.0 °/s
   - `TREMOR_LEVEL_3_THRESHOLD` = 250.0 °/s
3. **Detection Algorithm:**
   - Instead of instantaneous checks which jump erratically, implemented a 10-sample Moving Average (1 second window) inside `DetectionManager`.
   - Used the Leg Gyro MA to filter out arm swings. If `legAvg > LEG_WALKING_THRESHOLD (15.0)`, tremor detection is suppressed (Level = 0).
4. **State Machine Hookup:** `DetectionManager` now automatically updates the `currentTremorLevel` variable inside `SystemState`.

## Code Modifications
- `Config.h`: Added Level 1, 2, 3 Tremor constants.
- `SystemState.h/cpp`: Added `currentTremorLevel` getter and setter.
- `DetectionManager.h/cpp`: Added circular buffers for 10-sample MA, implemented `detectTremor()` matching the specified thresholds.

## Next Steps
- Phase 10: Threshold-Based Freezing of Gait (FOG) Detection.
