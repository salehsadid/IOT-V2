#include "Logger.h"

void Logger::init(unsigned long baudRate) {
    Serial.begin(baudRate);

    while (!Serial) {
        delay(10); 
    }
    info("Logger initialized.");
}

void Logger::info(const String& message) {
    log("INFO", message);
}

void Logger::warning(const String& message) {
    log("WARN", message);
}

void Logger::error(const String& message) {
    log("ERROR", message);
}

void Logger::log(const String& level, const String& message) {

    Serial.print("[");
    Serial.print(level);
    Serial.print("] ");
    Serial.println(message);
}
