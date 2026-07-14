#include "SensorManager.h"
#include "Logger.h"
#include "Config.h"
#include <Wire.h>

// Scaling factors for ±2g and ±250°/s ranges
const float ACCEL_SCALE = 16384.0;
const float GYRO_SCALE = 131.0;

SensorManager::SensorManager(SystemState* state) : systemState(state), lastReadTime(0), lastHealthCheckTime(0) {
    // Initialize data structs to 0
    handData = {0, 0, 0, 0, 0, 0};
    legData = {0, 0, 0, 0, 0, 0};
}

void SensorManager::init() {
    Logger::info("Initializing I2C...");
    // Initialize I2C with pins from Config
    Wire.begin(Config::PIN_I2C_SDA, Config::PIN_I2C_SCL);
    
    // Check and initialize both sensors
    initHandSensor();
    initLegSensor();
}

void SensorManager::update() {
    unsigned long currentMillis = millis();
    
    // 1. Data reading (every 100 ms)
    if (currentMillis - lastReadTime >= 100) {
        lastReadTime = currentMillis;
        
        bool printed = false;

        if (systemState != nullptr && systemState->isHandSensorReady()) {
            readHandSensor();
            printed = true;
        }
        
        if (systemState != nullptr && systemState->isLegSensorReady()) {
            readLegSensor();
            printed = true;
        }

        if (printed && Config::DEBUG_SENSOR_OUTPUT) {
            printSensorData();
        }
    }

    // 2. Health check (every 5 seconds)
    if (currentMillis - lastHealthCheckTime >= 5000) {
        lastHealthCheckTime = currentMillis;
        
        bool handOk = checkSensor(Config::ADDR_MPU6050_HAND);
        if (systemState != nullptr) {
            if (systemState->isHandSensorReady() && !handOk) {
                Logger::error("Hand MPU6050 disconnected!");
            } else if (!systemState->isHandSensorReady() && handOk) {
                Logger::info("Hand MPU6050 reconnected.");
                initHandSensor(); // Re-initialize to wake and configure
            }
            systemState->setHandSensorReady(handOk);
        }
        
        bool legOk = checkSensor(Config::ADDR_MPU6050_LEG);
        if (systemState != nullptr) {
            if (systemState->isLegSensorReady() && !legOk) {
                Logger::error("Leg MPU6050 disconnected!");
            } else if (!systemState->isLegSensorReady() && legOk) {
                Logger::info("Leg MPU6050 reconnected.");
                initLegSensor(); // Re-initialize to wake and configure
            }
            systemState->setLegSensorReady(legOk);
        }
    }
}

bool SensorManager::checkSensor(uint8_t address) {
    Wire.beginTransmission(address);
    byte error = Wire.endTransmission();
    return (error == 0);
}

void SensorManager::wakeSensor(uint8_t address) {
    Wire.beginTransmission(address);
    Wire.write(MPU6050_PWR_MGMT_1);
    Wire.write(0x00); // Set to 0 to wake up the sensor
    Wire.endTransmission();
}

void SensorManager::configureSensor(uint8_t address) {
    // Configure Accelerometer to ±2g (0x00)
    Wire.beginTransmission(address);
    Wire.write(MPU6050_ACCEL_CONFIG);
    Wire.write(0x00); 
    Wire.endTransmission();

    // Configure Gyroscope to ±250°/s (0x00)
    Wire.beginTransmission(address);
    Wire.write(MPU6050_GYRO_CONFIG);
    Wire.write(0x00);
    Wire.endTransmission();
}

void SensorManager::initHandSensor() {
    if (checkSensor(Config::ADDR_MPU6050_HAND)) {
        Logger::info("Hand MPU6050 detected (0x" + String(Config::ADDR_MPU6050_HAND, HEX) + "). Waking up and configuring...");
        wakeSensor(Config::ADDR_MPU6050_HAND);
        configureSensor(Config::ADDR_MPU6050_HAND);
        
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
        Logger::info("Leg MPU6050 detected (0x" + String(Config::ADDR_MPU6050_LEG, HEX) + "). Waking up and configuring...");
        wakeSensor(Config::ADDR_MPU6050_LEG);
        configureSensor(Config::ADDR_MPU6050_LEG);
        
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

bool SensorManager::readSensorData(uint8_t address, SensorData& data) {
    Wire.beginTransmission(address);
    Wire.write(MPU6050_ACCEL_XOUT_H);
    Wire.endTransmission(false);
    
    // Request 14 bytes (6 accel, 2 temp, 6 gyro)
    Wire.requestFrom((uint8_t)address, (uint8_t)14, (uint8_t)true);
    
    if (Wire.available() == 14) {
        int16_t ax = (Wire.read() << 8 | Wire.read());
        int16_t ay = (Wire.read() << 8 | Wire.read());
        int16_t az = (Wire.read() << 8 | Wire.read());
        
        // Skip temperature
        Wire.read(); 
        Wire.read();
        
        int16_t gx = (Wire.read() << 8 | Wire.read());
        int16_t gy = (Wire.read() << 8 | Wire.read());
        int16_t gz = (Wire.read() << 8 | Wire.read());
        
        // Convert to float (g and deg/s)
        data.accelX = ax / ACCEL_SCALE;
        data.accelY = ay / ACCEL_SCALE;
        data.accelZ = az / ACCEL_SCALE;
        
        data.gyroX = gx / GYRO_SCALE;
        data.gyroY = gy / GYRO_SCALE;
        data.gyroZ = gz / GYRO_SCALE;
        
        return true;
    }
    return false;
}

void SensorManager::readHandSensor() {
    if (readSensorData(Config::ADDR_MPU6050_HAND, handData) && systemState != nullptr) {
        systemState->setHandAccelMagnitude(calculateAccelMagnitude(handData));
        systemState->setHandGyroMagnitude(calculateGyroMagnitude(handData));
    }
}

void SensorManager::readLegSensor() {
    if (readSensorData(Config::ADDR_MPU6050_LEG, legData) && systemState != nullptr) {
        systemState->setLegAccelMagnitude(calculateAccelMagnitude(legData));
        systemState->setLegGyroMagnitude(calculateGyroMagnitude(legData));
    }
}

float SensorManager::calculateAccelMagnitude(const SensorData& data) {
    return sqrt((data.accelX * data.accelX) + 
                (data.accelY * data.accelY) + 
                (data.accelZ * data.accelZ));
}

float SensorManager::calculateGyroMagnitude(const SensorData& data) {
    return sqrt((data.gyroX * data.gyroX) + 
                (data.gyroY * data.gyroY) + 
                (data.gyroZ * data.gyroZ));
}

void SensorManager::printSensorData() {
    Serial.println("========================");
    
    if (systemState != nullptr && systemState->isHandSensorReady()) {
        float handAccelMag = systemState->getHandAccelMagnitude();
        float handGyroMag = systemState->getHandGyroMagnitude();
        
        Serial.println("HAND SENSOR");
        Serial.print("Accel Mag : "); Serial.print(handAccelMag, 3); Serial.println(" g");
        Serial.print("Gyro Mag  : "); Serial.print(handGyroMag, 3); Serial.println(" °/s");
        
        // Print raw for debugging
        Serial.print("Raw Accel -> X: "); Serial.print(handData.accelX, 3);
        Serial.print("  Y: "); Serial.print(handData.accelY, 3);
        Serial.print("  Z: "); Serial.println(handData.accelZ, 3);
        Serial.print("Raw Gyro  -> X: "); Serial.print(handData.gyroX, 3);
        Serial.print("  Y: "); Serial.print(handData.gyroY, 3);
        Serial.print("  Z: "); Serial.println(handData.gyroZ, 3);
        Serial.println("---");
    }

    if (systemState != nullptr && systemState->isLegSensorReady()) {
        float legAccelMag = systemState->getLegAccelMagnitude();
        float legGyroMag = systemState->getLegGyroMagnitude();
        
        Serial.println("LEG SENSOR");
        Serial.print("Accel Mag : "); Serial.print(legAccelMag, 3); Serial.println(" g");
        Serial.print("Gyro Mag  : "); Serial.print(legGyroMag, 3); Serial.println(" °/s");
        
        // Print raw for debugging
        Serial.print("Raw Accel -> X: "); Serial.print(legData.accelX, 3);
        Serial.print("  Y: "); Serial.print(legData.accelY, 3);
        Serial.print("  Z: "); Serial.println(legData.accelZ, 3);
        Serial.print("Raw Gyro  -> X: "); Serial.print(legData.gyroX, 3);
        Serial.print("  Y: "); Serial.print(legData.gyroY, 3);
        Serial.print("  Z: "); Serial.println(legData.gyroZ, 3);
    }
    
    Serial.println("========================");
}
