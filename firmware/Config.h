#pragma once

#include <Arduino.h>

class Config {
public:
    static void init();

    // Debugging
    static const bool DEBUG_SENSOR_OUTPUT = true;

    // Initial Calibration Thresholds
    static constexpr float REST_GYRO_THRESHOLD = 5.0; // °/s
    static constexpr float TREMOR_LEVEL_1_THRESHOLD = 20.0; // °/s
    static constexpr float TREMOR_LEVEL_2_THRESHOLD = 120.0; // °/s
    static constexpr float TREMOR_LEVEL_3_THRESHOLD = 250.0; // °/s
    
    // Phase 9 Refinement Constants
    static constexpr uint8_t TREMOR_MA_WINDOW_SIZE = 5; // Samples
    static constexpr uint8_t TREMOR_CONFIRMATION_COUNT = 3; // Samples
    static constexpr float TREMOR_HYSTERESIS = 5.0; // °/s
    static constexpr float TREMOR_WALKING_MULTIPLIER = 1.5; // Threshold modifier during walking

    static constexpr float LEG_WALKING_THRESHOLD = 15.0; // °/s

    // Phase 10 FOG Constants
    static constexpr float FOG_ENTRY_THRESHOLD = 40.0; // °/s
    static constexpr float FOG_MIN_THRESHOLD = 5.0; // °/s
    static constexpr unsigned long FOG_CONFIRMATION_TIME_MS = 2000;
    static constexpr unsigned long WALKING_CONFIRMATION_TIME_MS = 2000;
    static constexpr unsigned long RECOVERY_CONFIRMATION_TIME_MS = 2000;

    // Phase 11 OLED Constants
    static constexpr uint16_t OLED_WIDTH = 128;
    static constexpr uint16_t OLED_HEIGHT = 64;
    static constexpr unsigned long OLED_REFRESH_INTERVAL = 500; // ms
    static constexpr unsigned long OLED_ALERT_DURATION = 3000; // ms

    // Phase 12 Buzzer Constants
    static constexpr bool BUZZER_ENABLED = true;
    static constexpr unsigned long BUZZER_ON_TIME_MS = 400; // ms
    static constexpr unsigned long BUZZER_OFF_TIME_MS = 300; // ms

    // Network & Server
    static String getWifiSSID();
    static String getWifiPass();
    static String getServerUrl();
    static String getApiToken();

    // Hardware Pins
    static const uint8_t PIN_I2C_SDA = 21;
    static const uint8_t PIN_I2C_SCL = 22;
    static const uint8_t PIN_BUZZER = 25;

    // I2C Addresses
    static const uint8_t ADDR_MPU6050_HAND = 0x68;
    static const uint8_t ADDR_MPU6050_LEG = 0x69;
    static const uint8_t ADDR_OLED = 0x3C;
};
