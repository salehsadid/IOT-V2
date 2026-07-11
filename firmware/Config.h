#pragma once

#include <Arduino.h>

class Config {
public:
    static void init();

    // Network & Server
    static String getWifiSSID();
    static String getWifiPass();
    static String getServerUrl();
    static String getApiToken();

    // Hardware Pins
    static const uint8_t PIN_I2C_SDA = 21;
    static const uint8_t PIN_I2C_SCL = 22;
    static const uint8_t PIN_VIBRATION_MOTOR = 25;

    // I2C Addresses
    static const uint8_t ADDR_MPU6050_HAND = 0x68;
    static const uint8_t ADDR_MPU6050_LEG = 0x69;
    static const uint8_t ADDR_OLED = 0x3C;
};
