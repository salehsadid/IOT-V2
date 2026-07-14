#pragma once

#include "SystemState.h"
#include "Config.h"

class DetectionManager {
public:
    DetectionManager(SystemState* state);

    void init();
    void update();
    
    // Future detection methods
    void detectTremor();
    void detectFOG();
    void reset();

private:
    SystemState* systemState;

    // Moving Average Buffers
    float handGyroBuffer[Config::TREMOR_MA_WINDOW_SIZE];
    float legGyroBuffer[Config::TREMOR_MA_WINDOW_SIZE];
    uint8_t bufferIndex;
    bool bufferFilled;
    unsigned long lastUpdateTime;
    
    // State Tracking for Tremor Detection
    uint8_t pendingTremorLevel;
    uint8_t confirmationCounter;

    // State Tracking for FOG Detection
    unsigned long stateTimerStart;

    float getMovingAverage(const float* buffer, uint8_t size);
    void changeMotionState(MotionState newState);
};
