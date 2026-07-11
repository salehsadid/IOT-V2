#include "SystemState.h"

SystemState::SystemState() 
    : wifiConnected(false), 
      handSensorReady(false), 
      legSensorReady(false), 
      oledReady(false), 
      serverConnected(false), 
      cueingActive(false) {
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
