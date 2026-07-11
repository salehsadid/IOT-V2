#include <Arduino.h>
#include "Config.h"
#include "Logger.h"
#include "SystemState.h"
#include "SensorManager.h"
#include "DisplayManager.h"
#include "CueingController.h"

// Global instances for core architecture
SystemState systemState;
SensorManager sensorManager(&systemState);
DisplayManager displayManager(&systemState);
CueingController cueingController(&systemState);

void setup() {
    // 1. Initialize Logger (Serial)
    Logger::init();
    Logger::info("Starting Parkinson's Monitoring System ESP32 Firmware...");

    // 2. Initialize Configuration
    Config::init();
    
    // 3. Initialize Core Managers
    sensorManager.init();
    displayManager.init();
    cueingController.init();
    
    Logger::info("Setup complete. Entering main loop.");
}

void loop() {
    // Update system state & managers periodically
    sensorManager.update();
    displayManager.update();
    cueingController.update();
    
    // Future placeholders for detection logic and HTTP API communication
    // will be inserted here in subsequent phases.
    
    delay(10); // Prevent watchdog timeout and reduce CPU load
}
