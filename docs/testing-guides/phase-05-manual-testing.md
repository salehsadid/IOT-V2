# Phase 5 — Manual Testing Guide

**Purpose:** Steps you can perform yourself to verify Phase 5 (ESP32 Firmware Foundation) is complete and structurally correct.

---

## Prerequisites

- **Arduino IDE** (Version 1.8.x or 2.x) installed on your computer.
- **ESP32 Board Package** installed in your Arduino IDE Boards Manager.

---

## Test 1 — Open the Project in Arduino IDE

1. Open your Arduino IDE.
2. Go to **File > Open...**
3. Navigate to `d:\Academic Projects\IOT V2\firmware\` and select the `firmware.ino` file.
4. **Expected:** 
   - The Arduino IDE opens the project successfully.
   - You should see multiple tabs at the top of the editor for all the created files: `firmware.ino`, `Config.h`, `Config.cpp`, `Logger.h`, `Logger.cpp`, `SystemState.h`, `SystemState.cpp`, `SensorManager.h`, `SensorManager.cpp`, `DisplayManager.h`, `DisplayManager.cpp`, `CueingController.h`, `CueingController.cpp`, and `secrets.h.example`.

---

## Test 2 — Compile the Code

1. In the Arduino IDE, go to **Tools > Board** and ensure an ESP32 board is selected (e.g., "DOIT ESP32 DEVKIT V1" or "ESP32 Dev Module").
2. Click the **Verify** button (the checkmark icon in the top left corner).
3. **Expected:**
   - The compiler will run.
   - At the bottom of the screen, it should eventually print **"Done compiling."**
   - There should be **NO** red error messages about missing headers (`No such file or directory`) or multiple definitions.
   - The memory usage statistics should be extremely low, confirming there is no heavy logic implemented yet.

---

## Test 3 — Check File Modularity

1. Click on the `SystemState.h` tab.
2. **Expected:** Verify that the file only contains variables related to system readiness (e.g., `wifiConnected`, `handSensorReady`), and does NOT contain anything related to tremor thresholds or math.
3. Click on the `firmware.ino` tab.
4. **Expected:** The `setup()` and `loop()` functions should look clean and simple, mostly just calling `.init()` and `.update()` on the respective manager objects. There should be no `Wire.begin()` or `WiFi.begin()` calls visible directly in the main file.

---

## ✅ Phase 5 Checklist

| Check | Expected |
|---|---|
| `firmware` folder created | ✅ |
| Opens successfully in Arduino IDE with all tabs | ✅ |
| Compiles successfully for ESP32 without errors | ✅ |
| Modular OOP class structure is used | ✅ |
| Main `.ino` file is kept clean | ✅ |
| NO actual hardware/Wi-Fi logic is implemented yet | ✅ |
| `secrets.h.example` is present | ✅ |
| `README.md` is present in firmware folder | ✅ |
