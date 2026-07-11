#pragma once

#include "SystemState.h"

class SensorManager {
public:
    SensorManager(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
    
    // Future placeholder methods
    void initHandSensor();
    void initLegSensor();
    void readHandSensor();
    void readLegSensor();
};
