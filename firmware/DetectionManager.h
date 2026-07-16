#pragma once

#include "SystemState.h"
#include "Config.h"

class DetectionManager {
public:
    DetectionManager(SystemState* state);

    void init();
    void update();

    void detectTremor();
    void detectFOG();
    void reset();

private:
    SystemState* systemState;

    float handGyroBuffer[Config::TREMOR_MA_WINDOW_SIZE];
    float legGyroBuffer[Config::TREMOR_MA_WINDOW_SIZE];
    uint8_t bufferIndex;
    bool bufferFilled;
    unsigned long lastUpdateTime;

    uint8_t pendingTremorLevel;
    uint8_t confirmationCounter;

    unsigned long stateTimerStart;

    float getMovingAverage(const float* buffer, uint8_t size);
    void changeMotionState(MotionState newState);
};
