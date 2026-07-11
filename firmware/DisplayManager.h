#pragma once

#include "SystemState.h"

class DisplayManager {
public:
    DisplayManager(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
};
