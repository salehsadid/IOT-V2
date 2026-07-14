#include "Config.h"

// If secrets.h exists, it would be included here.
// For the repository, we just use placeholder values in case secrets.h is missing.
#if __has_include("secrets.h")
#include "secrets.h"
#else
#define SECRET_WIFI_SSID "PLACEHOLDER_SSID"
#define SECRET_WIFI_PASS "PLACEHOLDER_PASS"
#define SECRET_API_TOKEN "PLACEHOLDER_TOKEN"
#define SECRET_SERVER_URL "http://parkinson-monitor.test"
#endif

void Config::init() {
    // Future initialization if needed (e.g., reading from EEPROM or NVS)
}

String Config::getWifiSSID() {
    return String(SECRET_WIFI_SSID);
}

String Config::getWifiPass() {
    return String(SECRET_WIFI_PASS);
}

String Config::getServerUrl() {
    return String(SECRET_SERVER_URL);
}

String Config::getApiToken() {
    return String(SECRET_API_TOKEN);
}
