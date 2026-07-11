#pragma once

#include "SystemState.h"

class SensorManager {
public:
    SensorManager(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
    
    // Check if a sensor is responding at a given address
    bool checkSensor(uint8_t address);
    
    // Future placeholder methods
    void initHandSensor();
    void initLegSensor();
    void readHandSensor();
    void readLegSensor();
};
