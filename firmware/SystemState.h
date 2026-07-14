#pragma once

// Phase 10: Motion State Enum
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

    // Setters
    void setWifiConnected(bool connected);
    void setHandSensorReady(bool ready);
    void setLegSensorReady(bool ready);
    void setOledReady(bool ready);
    void setServerConnected(bool connected);
    void setCueingActive(bool active);

    // Sensor Magnitude Setters
    void setHandAccelMagnitude(float mag);
    void setHandGyroMagnitude(float mag);
    void setLegAccelMagnitude(float mag);
    void setLegGyroMagnitude(float mag);

    // Detection State Setters
    void setTremorLevel(uint8_t level);
    void setMotionState(MotionState state);
    void setFogActive(bool active);

    // Getters
    bool isWifiConnected() const;
    bool isHandSensorReady() const;
    bool isLegSensorReady() const;
    bool isOledReady() const;
    bool isServerConnected() const;
    bool isCueingActive() const;
    
    // Detection State Getters
    uint8_t getTremorLevel() const;
    MotionState getMotionState() const;
    bool isFogActive() const;
    
    // Sensor Magnitude Getters
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
    
    float handAccelMag;
    float handGyroMag;
    float legAccelMag;
    float legGyroMag;
    
    uint8_t currentTremorLevel; // 0=None, 1=Mild, 2=Moderate, 3=Severe
    MotionState currentMotionState;
    bool fogActive;
};
