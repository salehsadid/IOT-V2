#pragma once

#include <Arduino.h>

enum MotionState {
    REST,
    POSSIBLE_WALKING,
    WALKING,
    POSSIBLE_FOG,
    FOG_CONFIRMED,
    RECOVERY
};

class SystemState {
public:
    SystemState();

    void setWifiConnected(bool connected);
    void setHandSensorReady(bool ready);
    void setLegSensorReady(bool ready);
    void setOledReady(bool ready);
    void setServerConnected(bool connected);
    void setCueingActive(bool active);
    void setRemoteBuzzerStop(bool stop);

    void setHandAccelMagnitude(float mag);
    void setHandGyroMagnitude(float mag);
    void setLegAccelMagnitude(float mag);
    void setLegGyroMagnitude(float mag);

    void setTremorLevel(uint8_t level);
    void setMotionState(MotionState state);
    void setFogActive(bool active);

    bool isWifiConnected() const;
    bool isHandSensorReady() const;
    bool isLegSensorReady() const;
    bool isOledReady() const;
    bool isServerConnected() const;
    bool isCueingActive() const;
    bool getRemoteBuzzerStop() const;

    uint8_t getTremorLevel() const;
    MotionState getMotionState() const;
    bool isFogActive() const;

    float getHandAccelMagnitude() const;
    float getHandGyroMagnitude() const;
    float getLegAccelMagnitude() const;
    float getLegGyroMagnitude() const;

private:
    bool wifiConnected;
    bool handSensorReady;
    bool legSensorReady;
    bool oledReady;
    bool serverConnected;
    bool cueingActive;
    bool remoteBuzzerStop;

    float handAccelMag;
    float handGyroMag;
    float legAccelMag;
    float legGyroMag;

    uint8_t currentTremorLevel; 
    MotionState currentMotionState;
    bool fogActive;
};
