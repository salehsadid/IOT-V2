#include "ApiClient.h"
#include "Config.h"
#include "Logger.h"

ApiClient::ApiClient(SystemState* state) 
    : systemState(state), lastWifiCheckTime(0), lastHeartbeatTime(0),
      inTremorEvent(false), tremorStartTime(0), currentStartLevel(0), currentMaxLevel(0),
      inFogEvent(false), fogStartTime(0) {
}

void ApiClient::init() {
    Logger::info("Initializing ApiClient...");
    WiFi.mode(WIFI_STA);
    connectWiFi();
    Logger::info("ApiClient initialization complete.");
}

void ApiClient::connectWiFi() {
    String ssid = Config::getWifiSSID();
    String pass = Config::getWifiPass();

    if (ssid == "PLACEHOLDER_SSID" || ssid == "") {
        Logger::error("WiFi SSID not configured. Skipping WiFi connect.");
        if (systemState) systemState->setWifiConnected(false);
        return;
    }

    Logger::info("Connecting to WiFi: " + ssid);
    WiFi.begin(ssid.c_str(), pass.c_str());

}

void ApiClient::checkWiFiConnection() {
    bool connected = (WiFi.status() == WL_CONNECTED);
    if (systemState) {
        systemState->setWifiConnected(connected);
    }
}

void ApiClient::update() {
    unsigned long currentMillis = millis();

    if (currentMillis - lastWifiCheckTime >= 5000) {
        lastWifiCheckTime = currentMillis;
        checkWiFiConnection();
        if (WiFi.status() != WL_CONNECTED && Config::getWifiSSID() != "PLACEHOLDER_SSID") {
            Logger::info("WiFi disconnected. Reconnecting...");
            WiFi.disconnect();
            WiFi.begin(Config::getWifiSSID().c_str(), Config::getWifiPass().c_str());
        }
    }

    if (systemState == nullptr) return;

    if (currentMillis - lastHeartbeatTime >= 5000) {
        lastHeartbeatTime = currentMillis;
        sendHeartbeat();
    }

    uint8_t currentTremor = systemState->getTremorLevel();
    if (currentTremor > 0) {
        if (!inTremorEvent) {
            inTremorEvent = true;
            tremorStartTime = currentMillis;
            currentStartLevel = currentTremor;
            currentMaxLevel = currentTremor;
            Logger::info("[API] Tremor event started (Lvl " + String(currentStartLevel) + ")");
        } else {
            if (currentTremor > currentMaxLevel) {
                currentMaxLevel = currentTremor;
            }
        }
    } else {
        if (inTremorEvent) {
            inTremorEvent = false;
            unsigned long duration = currentMillis - tremorStartTime;
            Logger::info("[API] Tremor event ended. Duration: " + String(duration) + " ms, Max Lvl: " + String(currentMaxLevel));
            uploadTremorEvent(currentStartLevel, currentMaxLevel, duration);
        }
    }

    bool currentFog = systemState->isFogActive();
    if (currentFog) {
        if (!inFogEvent) {
            inFogEvent = true;
            fogStartTime = currentMillis;
            Logger::info("[API] FOG event started - Forcing immediate heartbeat");
            sendHeartbeat(); 
            lastHeartbeatTime = currentMillis;
        }
    } else {
        if (inFogEvent) {
            inFogEvent = false;
            unsigned long duration = currentMillis - fogStartTime;
            Logger::info("[API] FOG event ended. Duration: " + String(duration) + " ms");
            uploadFogEvent(duration);
            sendHeartbeat(); 
            lastHeartbeatTime = currentMillis;
        }
    }
}

void ApiClient::uploadTremorEvent(uint8_t startLvl, uint8_t maxLvl, unsigned long durationMs) {

    String payload = "{";
    payload += "\"device_id\":\"ESP32-A1B2C3D4\",";
    payload += "\"event_type\":\"TREMOR\",";
    payload += "\"start_level\":" + String(startLvl) + ",";
    payload += "\"max_level\":" + String(maxLvl) + ",";
    payload += "\"duration_ms\":" + String(durationMs);
    payload += "}";

    sendPostRequest(payload);
}

void ApiClient::uploadFogEvent(unsigned long durationMs) {
    String payload = "{";
    payload += "\"device_id\":\"ESP32-A1B2C3D4\",";
    payload += "\"event_type\":\"FOG\",";
    payload += "\"duration_ms\":" + String(durationMs);
    payload += "}";

    sendPostRequest(payload);
}

bool ApiClient::sendPostRequest(String payload) {
    if (WiFi.status() != WL_CONNECTED) {
        Logger::error("Cannot upload event. WiFi not connected.");
        return false;
    }

    String serverUrl = Config::getServerUrl() + "/api/events";
    String token = Config::getApiToken();

    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");
    if (token != "" && token != "PLACEHOLDER_TOKEN") {
        http.addHeader("Authorization", "Bearer " + token);
    }

    Logger::info("Uploading Event to: " + serverUrl);
    Logger::info("Payload: " + payload);

    int httpResponseCode = http.POST(payload);

    bool success = false;
    if (httpResponseCode > 0) {
        String response = http.getString();
        Logger::info("HTTP Response code: " + String(httpResponseCode));
        if (httpResponseCode == 200 || httpResponseCode == 201) {
            success = true;
        } else {
            Logger::error("Server response: " + response);
        }
    } else {
        Logger::error("Error on sending POST: " + String(http.errorToString(httpResponseCode).c_str()));
    }

    http.end();
    return success;
}

void ApiClient::sendHeartbeat() {
    if (WiFi.status() != WL_CONNECTED) return;

    String payload = "{";
    payload += "\"device_id\":\"ESP32-A1B2C3D4\",";
    payload += "\"hand_ok\":" + String(systemState->isHandSensorReady() ? "true" : "false") + ",";
    payload += "\"leg_ok\":" + String(systemState->isLegSensorReady() ? "true" : "false") + ",";
    payload += "\"tremor_level\":" + String(systemState->getTremorLevel()) + ",";
    payload += "\"fog_active\":" + String(systemState->isFogActive() ? "true" : "false");
    payload += "}";

    String serverUrl = Config::getServerUrl() + "/api/heartbeat";
    HTTPClient http;
    http.begin(serverUrl);
    http.addHeader("Content-Type", "application/json");

    int httpResponseCode = http.POST(payload);
    if (httpResponseCode > 0) {
        String response = http.getString();
        if (response.indexOf("\"command\":\"STOP_BUZZER\"") >= 0) {
            Logger::info("[API] Received STOP_BUZZER command from server.");
            systemState->setRemoteBuzzerStop(true);
        }
    }
    http.end();
}
