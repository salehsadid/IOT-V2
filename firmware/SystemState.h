#pragma once

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

    // Getters
    bool isWifiConnected() const;
    bool isHandSensorReady() const;
    bool isLegSensorReady() const;
    bool isOledReady() const;
    bool isServerConnected() const;
    bool isCueingActive() const;

private:
    bool wifiConnected;
    bool handSensorReady;
    bool legSensorReady;
    bool oledReady;
    bool serverConnected;
    bool cueingActive;
};
