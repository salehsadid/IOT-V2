#pragma once

#include "SystemState.h"
#include "Config.h"
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

enum DisplayScreen {
    SCREEN_BOOT,
    SCREEN_NORMAL,
    SCREEN_ALERT,
    SCREEN_ERROR
};

class DisplayManager {
public:
    DisplayManager(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
    Adafruit_SSD1306 display;

    DisplayScreen currentScreen;
    unsigned long lastRefreshTime;
    unsigned long alertStartTime;

    void drawBootScreen();
    void drawNormalScreen();
    void drawAlertScreen();
    void drawErrorScreen();
};
