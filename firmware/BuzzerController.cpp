#include "BuzzerController.h"
#include "Logger.h"
#include "Config.h"
#include <Arduino.h>

static const unsigned long RHYTHM_PATTERN[] = {150, 100, 150, 600};
static const int RHYTHM_STEPS = 4;

BuzzerController::BuzzerController(SystemState* state) 
    : systemState(state), lastToggleTime(0), buzzerState(false), isAlerting(false), rhythmIndex(0) {
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

        if (isAlerting) {
            unsigned long currentMillis = millis();
            unsigned long interval = RHYTHM_PATTERN[rhythmIndex];

            if (currentMillis - lastToggleTime >= interval) {
                lastToggleTime = currentMillis;
                rhythmIndex = (rhythmIndex + 1) % RHYTHM_STEPS;
                buzzerState = (rhythmIndex % 2 == 0);
                digitalWrite(Config::PIN_BUZZER, buzzerState ? HIGH : LOW);
            }
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
    rhythmIndex = 0;
    lastToggleTime = millis();
    digitalWrite(Config::PIN_BUZZER, HIGH);
    systemState->setCueingActive(true);
    Logger::info("[BUZZER] FOG Alert Started (Rhythmic)");
}

void BuzzerController::stopBuzzer() {
    isAlerting = false;
    buzzerState = false;
    rhythmIndex = 0;
    digitalWrite(Config::PIN_BUZZER, LOW);
    systemState->setCueingActive(false);
    Logger::info("[BUZZER] FOG Alert Stopped");
}
