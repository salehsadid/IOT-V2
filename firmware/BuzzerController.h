#pragma once

#include "SystemState.h"

class BuzzerController {
public:
    BuzzerController(SystemState* state);

    void init();
    void update();
    
private:
    SystemState* systemState;
    
    unsigned long lastToggleTime;
    bool buzzerState;
    bool isAlerting;
    
    void startBuzzer();
    void stopBuzzer();
};
