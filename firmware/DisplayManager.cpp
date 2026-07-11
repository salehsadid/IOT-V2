#include "DisplayManager.h"
#include "Logger.h"
#include "Config.h"

DisplayManager::DisplayManager(SystemState* state) : systemState(state) {
}

void DisplayManager::init() {
    Logger::info("Initializing DisplayManager at address 0x" + String(Config::ADDR_OLED, HEX));
    
    // Placeholder for actual OLED initialization
    
    if (systemState != nullptr) {
        systemState->setOledReady(true);
    }
    
    Logger::info("DisplayManager initialization complete.");
}

void DisplayManager::update() {
    // Placeholder for updating OLED display
}
