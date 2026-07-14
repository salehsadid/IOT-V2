#include <Arduino.h>
#include "Config.h"
#include "Logger.h"
#include "SystemState.h"
#include "SensorManager.h"
#include "DetectionManager.h"
#include "DisplayManager.h"
#include "BuzzerController.h"
#include "ApiClient.h"

// Global instances for core architecture
SystemState systemState;
SensorManager sensorManager(&systemState);
DetectionManager detectionManager(&systemState);
DisplayManager displayManager(&systemState);
BuzzerController buzzerController(&systemState);
ApiClient apiClient(&systemState);

void setup() {
    // 1. Initialize Logger (Serial)
    Logger::init();
    Logger::info("Starting Parkinson's Monitoring System ESP32 Firmware...");

    // 2. Initialize Configuration
    Config::init();
    
    // 3. Initialize Core Managers
    sensorManager.init();
    detectionManager.init();
    displayManager.init();
    buzzerController.init();
    apiClient.init();
    
    Logger::info("Setup complete. Entering main loop.");
}

void loop() {
    // Update system state & managers periodically
    sensorManager.update();
    detectionManager.update();
    displayManager.update();
    buzzerController.update();
    
    // HTTP API communication for event uploads
    apiClient.update();
    
    delay(10); // Prevent watchdog timeout and reduce CPU load
}
