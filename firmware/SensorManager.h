#pragma once

#include <Arduino.h>
#include "SystemState.h"

#define MPU6050_SMPLRT_DIV   0x19
#define MPU6050_CONFIG       0x1A
#define MPU6050_GYRO_CONFIG  0x1B
#define MPU6050_ACCEL_CONFIG 0x1C
#define MPU6050_ACCEL_XOUT_H 0x3B
#define MPU6050_PWR_MGMT_1   0x6B

struct SensorData {
    float accelX, accelY, accelZ;
    float gyroX, gyroY, gyroZ;
};

class SensorManager {
public:
    SensorManager(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
    unsigned long lastReadTime;
    unsigned long lastHealthCheckTime;

    SensorData handData;
    SensorData legData;

    bool checkSensor(uint8_t address);
    void wakeSensor(uint8_t address);
    void configureSensor(uint8_t address);

    void initHandSensor();
    void initLegSensor();
    bool readSensorData(uint8_t address, SensorData& data);
    void readHandSensor();
    void readLegSensor();

    float calculateAccelMagnitude(const SensorData& data);
    float calculateGyroMagnitude(const SensorData& data);

    void printSensorData();
};
