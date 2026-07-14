# Phase 13 Report: ESP32 -> Laravel Event Logging & History

## Objectives Achieved
Phase 13 successfully bridges the offline hardware system with a cloud-based server backend using Laravel. 
The core objective was to implement one-way event logging, ensuring that the ESP32 sends ONLY confirmed Tremor and FOG events rather than a raw continuous stream of sensor data, thereby saving massive bandwidth and server costs.

## Architecture Highlights
### 1. ESP32 `ApiClient`
A dedicated HTTP client was written from scratch. It utilizes `WiFi.status()` in a non-blocking way to ensure that if the WiFi router reboots, the ESP32 will silently reconnect in the background without dropping its active I2C polling of the patient's limbs.

### 2. Time Calculation Offloading
Because the ESP32 lacks a Real-Time Clock (RTC), generating true timestamps natively is complex. Instead, the `ApiClient` calculates the `duration_ms` of an event. Upon receiving the JSON payload, the Laravel `EventController` utilizes `Carbon::now()` as the `end_time` and subtracts the duration to mathematically deduce the exact `start_time`.

### 3. Medical Severity Tracking
Instead of simply logging "A tremor happened", the ESP32 tracks the *escalation* of the tremor. The payload explicitly sends the `start_level` and the `max_level` (peak severity) reached during the episode.

## JSON Payload Structure
```json
{
  "device_id": "ESP32-001",
  "event_type": "TREMOR",
  "start_level": 1,
  "max_level": 3,
  "duration_ms": 5400
}
```

## Files Created / Modified
**Firmware:**
- `ApiClient.h` & `ApiClient.cpp`
- `firmware.ino` (instantiation)

**Laravel Backend:**
- `bootstrap/app.php` (enabled API routing)
- `routes/api.php` & `routes/web.php`
- `database/migrations/2026_07_11_000004_create_detection_events_table.php` (altered schema)
- `app/Models/DetectionEvent.php`
- `app/Http/Controllers/Api/EventController.php`
- `app/Http/Controllers/HistoryController.php`
- `resources/views/history.blade.php`
