# Phase 13 - Manual Testing Guide

## Objective
Verify the ESP32 successfully connects to WiFi, tracks events properly, and uploads them to the Laravel backend. Verify the backend stores and displays the data correctly.

## Prerequisites
- Laravel backend running (`php artisan serve`) on a local network IP (e.g., `192.168.1.100`).
- Ensure you have run `php artisan migrate:fresh` on the Laravel backend to apply the Phase 13 database schema.
- ESP32 configured with your router's SSID and Password in `secrets.h`.
- ESP32 configured with your PC's local IP address as the Server URL in `secrets.h` (e.g., `http://192.168.1.100:8000`).

## Test Cases

### Test Case 1: Tremor Peak Logging
1. **Action:** Shake the hand sensor mildly (Level 1) for 2 seconds, then shake violently (Level 3) for 2 seconds, then completely stop.
2. **Expected ESP32 Output:**
   - Serial Monitor prints: `[API] Tremor event started (Lvl 1)`
   - When stopped: `[API] Tremor event ended. Duration: 4000 ms, Max Lvl: 3`
   - `HTTP Response code: 201`
3. **Expected Laravel Output:**
   - Open your browser to `http://<your-ip>:8000/history`
   - You should see a new row: `TREMOR` | Duration: `4.0s` | Start Lvl: `1` | Max Lvl: `3`.

### Test Case 2: FOG Logging
1. **Action:** Swing the leg sensor to trigger `WALKING`, then tremble it to trigger `FOG_CONFIRMED`. Wait 5 seconds, then resume normal walking to trigger `RECOVERY`.
2. **Expected ESP32 Output:**
   - Serial Monitor prints: `[API] FOG event started`
   - When recovered: `[API] FOG event ended. Duration: ~5000 ms`
   - `HTTP Response code: 201`
3. **Expected Laravel Output:**
   - Open `/history` in the browser.
   - You should see a new row: `FOG` | Duration: `5.0s`.

### Test Case 3: Offline Resilience
1. **Action:** Turn off your WiFi router or PC server. Trigger a Tremor.
2. **Expected Result:**
   - The ESP32's OLED display and Buzzer (if FOG) **continue to work instantly and flawlessly**.
   - The Serial Monitor simply prints an HTTP Error (e.g., `Cannot upload event` or `connection refused`), proving that a network outage does not crash the medical device.
