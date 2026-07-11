#include "SensorManager.h"
#include "Logger.h"
#include "Config.h"
#include <Wire.h>

SensorManager::SensorManager(SystemState* state) : systemState(state) {
}

void SensorManager::init() {
    Logger::info("Initializing I2C...");
    // Initialize I2C with pins from Config
    Wire.begin(Config::PIN_I2C_SDA, Config::PIN_I2C_SCL);
    
    // Check both sensors
    initHandSensor();
    initLegSensor();
}

void SensorManager::update() {
    // Periodically monitor sensor availability
    static unsigned long lastCheck = 0;
    unsigned long currentMillis = millis();
    
    // Check every 5 seconds (5000 ms)
    if (currentMillis - lastCheck >= 5000) {
        lastCheck = currentMillis;
        
        bool handOk = checkSensor(Config::ADDR_MPU6050_HAND);
        if (systemState != nullptr) {
            systemState->setHandSensorReady(handOk);
        }
        
        bool legOk = checkSensor(Config::ADDR_MPU6050_LEG);
        if (systemState != nullptr) {
            systemState->setLegSensorReady(legOk);
        }
    }
}

bool SensorManager::checkSensor(uint8_t address) {
    Wire.beginTransmission(address);
    byte error = Wire.endTransmission();
    return (error == 0);
}

void SensorManager::initHandSensor() {
    if (checkSensor(Config::ADDR_MPU6050_HAND)) {
        Logger::info("Hand MPU6050 detected (0x" + String(Config::ADDR_MPU6050_HAND, HEX) + ")");
        if (systemState != nullptr) {
            systemState->setHandSensorReady(true);
        }
    } else {
        Logger::error("Hand MPU6050 not detected");
        if (systemState != nullptr) {
            systemState->setHandSensorReady(false);
        }
    }
}

void SensorManager::initLegSensor() {
    if (checkSensor(Config::ADDR_MPU6050_LEG)) {
        Logger::info("Leg MPU6050 detected (0x" + String(Config::ADDR_MPU6050_LEG, HEX) + ")");
        if (systemState != nullptr) {
            systemState->setLegSensorReady(true);
        }
    } else {
        Logger::error("Leg MPU6050 not detected");
        if (systemState != nullptr) {
            systemState->setLegSensorReady(false);
        }
    }
}

void SensorManager::readHandSensor() {
    // To be implemented in later phases
}

void SensorManager::readLegSensor() {
    // To be implemented in later phases
}
