#pragma once

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include "SystemState.h"

class ApiClient {
public:
    ApiClient(SystemState* state);

    void init();
    void update();

private:
    SystemState* systemState;
    unsigned long lastWifiCheckTime;
    unsigned long lastHeartbeatTime;

    bool inTremorEvent;
    unsigned long tremorStartTime;
    uint8_t currentStartLevel;
    uint8_t currentMaxLevel;

    bool inFogEvent;
    unsigned long fogStartTime;

    void connectWiFi();
    void checkWiFiConnection();

    void uploadTremorEvent(uint8_t startLvl, uint8_t maxLvl, unsigned long durationMs);
    void uploadFogEvent(unsigned long durationMs);
    void sendHeartbeat();
    bool sendPostRequest(String payload);
};
