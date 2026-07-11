#pragma once

#include <Arduino.h>

class Logger {
public:
    static void init(unsigned long baudRate = 115200);
    
    static void info(const String& message);
    static void warning(const String& message);
    static void error(const String& message);
    
private:
    static void log(const String& level, const String& message);
};
