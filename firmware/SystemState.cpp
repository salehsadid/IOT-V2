#include "SystemState.h"

SystemState::SystemState() 
    : wifiConnected(false), 
      handSensorReady(false), 
      legSensorReady(false), 
      oledReady(false), 
      serverConnected(false), 
      cueingActive(false),
      handAccelMag(0.0f),
      handGyroMag(0.0f),
      legAccelMag(0.0f),
      legGyroMag(0.0f),
      currentTremorLevel(0),
      currentMotionState(REST),
      fogActive(false),
      remoteBuzzerStop(false) {
}

void SystemState::setWifiConnected(bool connected) {
    wifiConnected = connected;
}

void SystemState::setHandSensorReady(bool ready) {
    handSensorReady = ready;
}

void SystemState::setLegSensorReady(bool ready) {
    legSensorReady = ready;
}

void SystemState::setOledReady(bool ready) {
    oledReady = ready;
}

void SystemState::setServerConnected(bool connected) {
    serverConnected = connected;
}

void SystemState::setCueingActive(bool active) {
    cueingActive = active;
}

bool SystemState::isWifiConnected() const {
    return wifiConnected;
}

bool SystemState::isHandSensorReady() const {
    return handSensorReady;
}

bool SystemState::isLegSensorReady() const {
    return legSensorReady;
}

bool SystemState::isOledReady() const {
    return oledReady;
}

bool SystemState::isServerConnected() const {
    return serverConnected;
}

bool SystemState::isCueingActive() const {
    return cueingActive;
}

void SystemState::setRemoteBuzzerStop(bool stop) {
    remoteBuzzerStop = stop;
}

bool SystemState::getRemoteBuzzerStop() const {
    return remoteBuzzerStop;
}

void SystemState::setHandAccelMagnitude(float mag) { handAccelMag = mag; }
void SystemState::setHandGyroMagnitude(float mag) { handGyroMag = mag; }
void SystemState::setLegAccelMagnitude(float mag) { legAccelMag = mag; }
void SystemState::setLegGyroMagnitude(float mag) { legGyroMag = mag; }

float SystemState::getHandAccelMagnitude() const { return handAccelMag; }
float SystemState::getHandGyroMagnitude() const { return handGyroMag; }
float SystemState::getLegAccelMagnitude() const { return legAccelMag; }
float SystemState::getLegGyroMagnitude() const { return legGyroMag; }

void SystemState::setTremorLevel(uint8_t level) { currentTremorLevel = level; }
uint8_t SystemState::getTremorLevel() const { return currentTremorLevel; }

void SystemState::setMotionState(MotionState state) { currentMotionState = state; }
MotionState SystemState::getMotionState() const { return currentMotionState; }

void SystemState::setFogActive(bool active) { fogActive = active; }
bool SystemState::isFogActive() const { return fogActive; }
