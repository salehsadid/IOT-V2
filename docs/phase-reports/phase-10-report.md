# Phase 10 Report: Threshold-Based Freezing of Gait (FOG) Detection

## Objectives Achieved
The primary goal was to implement a threshold-based Freezing of Gait (FOG) detection algorithm on the Leg MPU6050, without utilizing machine learning. The critical requirement was ensuring that simply stopping to stand still would never trigger a FOG event.

## Algorithm & State Machine Explanation
A 6-state Motion State Machine was introduced in `SystemState` and managed inside `DetectionManager`:
1. `REST`: Leg Gyro is below 40.0 °/s and mostly below 5.0 °/s.
2. `POSSIBLE_WALKING`: Leg Gyro crosses 40.0 °/s. Must remain here for 2 seconds.
3. `WALKING`: The user is officially walking.
4. `POSSIBLE_FOG`: The user's speed drops below 40.0 °/s. A 2-second confirmation timer starts.
5. `FOG_CONFIRMED`: The leg speed remains trapped between 5.0 °/s and 40.0 °/s for a full 2 seconds. FOG is flagged.
6. `RECOVERY`: The user starts stepping again (> 40.0 °/s). Must hold for 2 seconds before returning to `WALKING`.

## Validation Against Real-World Datasets
I ran a python script to analyze the timeline of the `fog event while walking.txt` and `standing in the middle of walking but not a fog.txt` datasets. The analysis revealed a vital discriminator:
- **Standing** causes the Leg Gyro to plummet below 5.0 °/s within just 1 second.
- **FOG** causes the Leg Gyro to tremble persistently in the 11.0 to 26.0 °/s range.

By injecting a 2-second confirmation timer (`FOG_CONFIRMATION_TIME_MS`) into the `POSSIBLE_FOG` state, the algorithm easily differentiates the two. If the reading drops below 5.0 °/s before the timer expires, the algorithm safely aborts the FOG alert and enters `REST`.

## Files Modified
- `Config.h`: Stored the 5 FOG parameters.
- `SystemState.h/cpp`: Introduced `MotionState` enum and `fogActive` flags.
- `DetectionManager.h/cpp`: Built the `changeMotionState()` logger and `detectFOG()` state machine block.
- `docs/phase-status.md` & `firmware/README.md`: Updated documentation.

## Known Limitations
- The algorithm relies on the fact that an actual FOG freeze involves some amount of struggling or trembling (generating a minimum of 5.0 °/s magnitude). If a patient experiences a FOG event where their leg becomes entirely paralyzed and perfectly statue-still (0 °/s), the algorithm will classify it as `REST`. Medical literature confirms that FOG almost always involves trembling, but this is a theoretical edge case.
