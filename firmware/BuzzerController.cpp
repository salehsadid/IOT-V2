#include "BuzzerController.h"
#include "Logger.h"
#include "Config.h"
#include <Arduino.h>

BuzzerController::BuzzerController(SystemState* state) 
    : systemState(state), lastToggleTime(0), buzzerState(false), isAlerting(false) {
}

void BuzzerController::init() {
    Logger::info("Initializing BuzzerController on pin " + String(Config::PIN_BUZZER));
    
    pinMode(Config::PIN_BUZZER, OUTPUT);
    digitalWrite(Config::PIN_BUZZER, LOW);
    
    Logger::info("BuzzerController initialization complete.");
}

void BuzzerController::update() {
    if (systemState == nullptr || !Config::BUZZER_ENABLED) return;

    if (systemState->isFogActive()) {
        if (systemState->getRemoteBuzzerStop()) {
            if (isAlerting) stopBuzzer();
        } else {
            if (!isAlerting) {
                startBuzzer();
            }
        }

        unsigned long currentMillis = millis();
        unsigned long interval = buzzerState ? Config::BUZZER_ON_TIME_MS : Config::BUZZER_OFF_TIME_MS;

        if (currentMillis - lastToggleTime >= interval) {
            lastToggleTime = currentMillis;
            buzzerState = !buzzerState;
            digitalWrite(Config::PIN_BUZZER, buzzerState ? HIGH : LOW);
        }
    } else {
        if (isAlerting) {
            stopBuzzer();
        }
    }
}

void BuzzerController::startBuzzer() {
    isAlerting = true;
    buzzerState = true;
    lastToggleTime = millis();
    digitalWrite(Config::PIN_BUZZER, HIGH);
    systemState->setCueingActive(true);
    Logger::info("[BUZZER] FOG Alert Started");
}

void BuzzerController::stopBuzzer() {
    isAlerting = false;
    buzzerState = false;
    digitalWrite(Config::PIN_BUZZER, LOW);
    systemState->setCueingActive(false);
    Logger::info("[BUZZER] FOG Alert Stopped");
}
