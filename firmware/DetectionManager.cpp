#include "DetectionManager.h"
#include "Logger.h"
#include "Config.h"
#include <Arduino.h>

DetectionManager::DetectionManager(SystemState* state) 
    : systemState(state), bufferIndex(0), bufferFilled(false), lastUpdateTime(0),
      pendingTremorLevel(0), confirmationCounter(0), stateTimerStart(0) {
    for (int i = 0; i < Config::TREMOR_MA_WINDOW_SIZE; i++) {
        handGyroBuffer[i] = 0.0f;
        legGyroBuffer[i] = 0.0f;
    }
}

void DetectionManager::init() {
    Logger::info("Initializing DetectionManager...");
    reset();
    Logger::info("DetectionManager initialization complete.");
}

void DetectionManager::update() {
    if (systemState == nullptr) return;

    unsigned long currentMillis = millis();
    if (currentMillis - lastUpdateTime >= 100) {
        lastUpdateTime = currentMillis;
        
        // Push new values to buffers
        handGyroBuffer[bufferIndex] = systemState->getHandGyroMagnitude();
        legGyroBuffer[bufferIndex] = systemState->getLegGyroMagnitude();
        
        bufferIndex++;
        if (bufferIndex >= Config::TREMOR_MA_WINDOW_SIZE) {
            bufferIndex = 0;
            bufferFilled = true;
        }

        // Only run detection if we have a full window of data
        if (bufferFilled) {
            detectTremor();
            detectFOG(); // Will be implemented in Phase 10
        }
    }
}

float DetectionManager::getMovingAverage(const float* buffer, uint8_t size) {
    float sum = 0;
    for (uint8_t i = 0; i < size; i++) {
        sum += buffer[i];
    }
    return sum / size;
}

void DetectionManager::detectTremor() {
    float handAvg = getMovingAverage(handGyroBuffer, Config::TREMOR_MA_WINDOW_SIZE);
    float legAvg = getMovingAverage(legGyroBuffer, Config::TREMOR_MA_WINDOW_SIZE);

    float multiplier = (legAvg > Config::LEG_WALKING_THRESHOLD) ? Config::TREMOR_WALKING_MULTIPLIER : 1.0f;
    
    uint8_t evaluatedLevel = 0;
    float thresh3 = Config::TREMOR_LEVEL_3_THRESHOLD * multiplier;
    float thresh2 = Config::TREMOR_LEVEL_2_THRESHOLD * multiplier;
    float thresh1 = Config::TREMOR_LEVEL_1_THRESHOLD * multiplier;
    
    // Hysteresis: lower the threshold if we are already at or above that pending level
    if (handAvg >= (pendingTremorLevel >= 3 ? thresh3 - Config::TREMOR_HYSTERESIS : thresh3)) {
        evaluatedLevel = 3;
    } else if (handAvg >= (pendingTremorLevel >= 2 ? thresh2 - Config::TREMOR_HYSTERESIS : thresh2)) {
        evaluatedLevel = 2;
    } else if (handAvg >= (pendingTremorLevel >= 1 ? thresh1 - Config::TREMOR_HYSTERESIS : thresh1)) {
        evaluatedLevel = 1;
    } else {
        evaluatedLevel = 0;
    }

    if (evaluatedLevel == pendingTremorLevel) {
        if (confirmationCounter < 255) confirmationCounter++;
    } else {
        pendingTremorLevel = evaluatedLevel;
        confirmationCounter = 1;
    }

    if (confirmationCounter >= Config::TREMOR_CONFIRMATION_COUNT) {
        uint8_t currentLevel = systemState->getTremorLevel();
        if (pendingTremorLevel != currentLevel) {
            uint8_t newLevel = currentLevel;
            if (pendingTremorLevel > currentLevel) {
                newLevel++;
            } else if (pendingTremorLevel < currentLevel) {
                newLevel--;
            }
            
            systemState->setTremorLevel(newLevel);
            Logger::info("[Tremor] Level changed to: " + String(newLevel) + " | Target: " + String(pendingTremorLevel) + " | HandAvg: " + String(handAvg, 1) + " | LegAvg: " + String(legAvg, 1));
            
            // Require new confirmations to take the next smooth step if target is still further away
            confirmationCounter = 0;
        }
    }
}

void DetectionManager::changeMotionState(MotionState newState) {
    MotionState oldState = systemState->getMotionState();
    if (oldState != newState) {
        systemState->setMotionState(newState);
        stateTimerStart = millis();
        
        String stateNames[] = {"REST", "POSSIBLE_WALKING", "WALKING", "POSSIBLE_FOG", "FOG_CONFIRMED", "RECOVERY"};
        Logger::info("[Motion] " + stateNames[oldState] + " -> " + stateNames[newState]);

        // Update fogActive flag
        if (newState == FOG_CONFIRMED) {
            systemState->setFogActive(true);
        } else if (newState == REST || newState == WALKING) {
            systemState->setFogActive(false);
        }
    }
}

void DetectionManager::detectFOG() {
    float legAvg = getMovingAverage(legGyroBuffer, Config::TREMOR_MA_WINDOW_SIZE);
    MotionState currentState = systemState->getMotionState();
    unsigned long timeInState = millis() - stateTimerStart;

    switch (currentState) {
        case REST:
            if (legAvg >= Config::FOG_ENTRY_THRESHOLD) {
                changeMotionState(POSSIBLE_WALKING);
            }
            break;

        case POSSIBLE_WALKING:
            if (legAvg < Config::FOG_ENTRY_THRESHOLD) {
                changeMotionState(REST);
            } else if (timeInState >= Config::WALKING_CONFIRMATION_TIME_MS) {
                changeMotionState(WALKING);
            }
            break;

        case WALKING:
            if (legAvg < Config::FOG_ENTRY_THRESHOLD) {
                changeMotionState(POSSIBLE_FOG);
            }
            break;

        case POSSIBLE_FOG:
            if (legAvg < Config::FOG_MIN_THRESHOLD) {
                // Leg stopped completely. User is just standing.
                changeMotionState(REST);
            } else if (legAvg >= Config::FOG_ENTRY_THRESHOLD) {
                // Leg resumed normal walking speed before FOG confirmation
                changeMotionState(WALKING);
            } else if (timeInState >= Config::FOG_CONFIRMATION_TIME_MS) {
                // Leg has been trembling between FOG_MIN and FOG_ENTRY for the confirmation time
                changeMotionState(FOG_CONFIRMED);
            }
            break;

        case FOG_CONFIRMED:
            if (legAvg >= Config::FOG_ENTRY_THRESHOLD) {
                changeMotionState(RECOVERY);
            } else if (legAvg < Config::FOG_MIN_THRESHOLD) {
                // User gave up and stood perfectly still
                changeMotionState(REST);
            }
            break;

        case RECOVERY:
            if (legAvg < Config::FOG_ENTRY_THRESHOLD) {
                // Failed to recover fully, dropped back into FOG
                changeMotionState(FOG_CONFIRMED);
            } else if (timeInState >= Config::RECOVERY_CONFIRMATION_TIME_MS) {
                // Successfully walked normally for confirmation time
                changeMotionState(WALKING);
            }
            break;
    }
}

void DetectionManager::reset() {
    bufferIndex = 0;
    bufferFilled = false;
    lastUpdateTime = 0;
    pendingTremorLevel = 0;
    confirmationCounter = 0;
    stateTimerStart = 0;
    for (int i = 0; i < Config::TREMOR_MA_WINDOW_SIZE; i++) {
        handGyroBuffer[i] = 0.0f;
        legGyroBuffer[i] = 0.0f;
    }
    if (systemState != nullptr) {
        systemState->setTremorLevel(0);
        systemState->setMotionState(REST);
        systemState->setFogActive(false);
    }
}
