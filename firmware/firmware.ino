#include <Arduino.h>
#include "Config.h"
#include "Logger.h"
#include "SystemState.h"
#include "SensorManager.h"
#include "DetectionManager.h"
#include "DisplayManager.h"
#include "BuzzerController.h"
#include "ApiClient.h"
SystemState systemState;
SensorManager sensorManager(&systemState);
DetectionManager detectionManager(&systemState);
DisplayManager displayManager(&systemState);
BuzzerController buzzerController(&systemState);
ApiClient apiClient(&systemState);
void setup() {
    Logger::init();
    Logger::info("Starting Parkinson's Monitoring System ESP32 Firmware...");
    Config::init();

    sensorManager.init();
    detectionManager.init();
    displayManager.init();
    buzzerController.init();
    apiClient.init();

    Logger::info("Setup complete. Entering main loop.");
}
void loop() {
    sensorManager.update();
    detectionManager.update();
    displayManager.update();
    buzzerController.update();

    apiClient.update();
    delay(10); 
}
