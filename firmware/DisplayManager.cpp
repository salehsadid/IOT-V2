#include "DisplayManager.h"
#include "Logger.h"
#include "Config.h"
#include <Arduino.h>

DisplayManager::DisplayManager(SystemState* state) 
    : systemState(state), 
      display(Config::OLED_WIDTH, Config::OLED_HEIGHT, &Wire, -1),
      currentScreen(SCREEN_BOOT), 
      lastRefreshTime(0), 
      alertStartTime(0) {
}

void DisplayManager::init() {
    Logger::info("Initializing DisplayManager at address 0x" + String(Config::ADDR_OLED, HEX));
    
    if(!display.begin(SSD1306_SWITCHCAPVCC, Config::ADDR_OLED)) {
        Logger::error("SSD1306 allocation failed or device not found");
        if (systemState != nullptr) {
            systemState->setOledReady(false);
        }
        return;
    }
    
    if (systemState != nullptr) {
        systemState->setOledReady(true);
    }
    
    display.clearDisplay();
    display.setTextColor(SSD1306_WHITE);
    drawBootScreen();
    display.display();
    
    Logger::info("DisplayManager initialization complete.");
    delay(2000); // Hold boot screen briefly
}

void DisplayManager::update() {
    if (systemState == nullptr || !systemState->isOledReady()) return;

    unsigned long currentMillis = millis();
    if (currentMillis - lastRefreshTime >= Config::OLED_REFRESH_INTERVAL) {
        lastRefreshTime = currentMillis;
        
        // Determine correct screen state
        if (!systemState->isHandSensorReady() || !systemState->isLegSensorReady()) {
            currentScreen = SCREEN_ERROR;
        } else if (systemState->getTremorLevel() > 0 || systemState->isFogActive()) {
            if (currentScreen != SCREEN_ALERT) {
                alertStartTime = currentMillis; // Just entered alert
            }
            currentScreen = SCREEN_ALERT;
        } else if (currentScreen == SCREEN_ALERT) {
            // We were in alert, check if duration expired
            if (currentMillis - alertStartTime >= Config::OLED_ALERT_DURATION) {
                currentScreen = SCREEN_NORMAL;
            }
        } else {
            currentScreen = SCREEN_NORMAL;
        }

        display.clearDisplay();
        
        switch (currentScreen) {
            case SCREEN_BOOT:
                drawBootScreen();
                break;
            case SCREEN_NORMAL:
                drawNormalScreen();
                break;
            case SCREEN_ALERT:
                drawAlertScreen();
                break;
            case SCREEN_ERROR:
                drawErrorScreen();
                break;
        }
        
        display.display();
    }
}

void DisplayManager::drawBootScreen() {
    display.setTextSize(1);
    display.setCursor(15, 10);
    display.print("Parkinson Monitor");
    display.setCursor(20, 30);
    display.print("Initializing...");
    display.setCursor(40, 50);
    display.print("v1.0.0");
}

void DisplayManager::drawNormalScreen() {
    display.setTextSize(1);
    
    // Line 1: Sensors
    display.setCursor(0, 0);
    display.print("Hand:");
    display.print(systemState->isHandSensorReady() ? "OK" : "ERR");
    display.print(" Leg:");
    display.print(systemState->isLegSensorReady() ? "OK" : "ERR");
    
    // Line 2: Motion
    display.setCursor(0, 20);
    display.print("Motion: ");
    MotionState ms = systemState->getMotionState();
    switch (ms) {
        case REST: display.print("REST"); break;
        case POSSIBLE_WALKING: display.print("POSS WALK"); break;
        case WALKING: display.print("WALKING"); break;
        case POSSIBLE_FOG: display.print("POSS FOG"); break;
        case FOG_CONFIRMED: display.print("FOG"); break;
        case RECOVERY: display.print("RECOVERY"); break;
    }
    
    // Line 3: Tremor
    display.setCursor(0, 40);
    display.print("Tremor: Level ");
    display.print(systemState->getTremorLevel());
}

void DisplayManager::drawAlertScreen() {
    display.setTextSize(2);
    
    if (systemState->isFogActive()) {
        display.setCursor(25, 15);
        display.print("FOG");
        display.setCursor(15, 35);
        display.print("DETECTED");
    } else if (systemState->getTremorLevel() > 0) {
        display.setCursor(25, 15);
        display.print("TREMOR");
        display.setCursor(20, 35);
        display.print("Level ");
        display.print(systemState->getTremorLevel());
    }
}

void DisplayManager::drawErrorScreen() {
    display.setTextSize(1);
    display.setCursor(10, 10);
    display.print("SENSOR ERROR!");
    
    display.setCursor(10, 30);
    if (!systemState->isHandSensorReady()) {
        display.print("- Hand MPU Missing");
    }
    
    display.setCursor(10, 45);
    if (!systemState->isLegSensorReady()) {
        display.print("- Leg MPU Missing");
    }
}
