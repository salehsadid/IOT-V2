#pragma once

#include "SystemState.h"

class CueingController {
public:
    CueingController(SystemState* state);

    void init();
    void update();
    
    // Future methods
    void startCueing();
    void stopCueing();

private:
    SystemState* systemState;
};
