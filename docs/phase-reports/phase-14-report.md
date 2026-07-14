# Phase 14 Report: Doctor & Caregiver Dashboard, Live Monitoring & Remote Alarm Control

## Overview
Phase 14 transforms the Laravel application into a comprehensive monitoring dashboard tailored for doctors and caregivers. It focuses exclusively on observing and controlling the system, while the ESP32 retains full autonomy over Tremor and Freezing of Gait (FOG) detection algorithms.

## Dashboard Architecture
The dashboard serves as the central hub for real-time monitoring and historical analysis:
- **Real-Time Polling**: The frontend uses vanilla JavaScript to poll the `/api/status` endpoint every 3 seconds.
- **Glassmorphism UI**: Developed using HTML and CSS with a modern glassmorphism aesthetic. A user-configurable Light/Dark theme toggle persists via `localStorage`.
- **Statistics Integration**: Eloquent queries compute daily metrics (e.g., today's tremor count, highest level) and lifetime statistics (e.g., average duration) displayed directly in the dashboard UI.

## Database Flow & Statistics Calculation
All medical events are logged in the `detection_events` table by the ESP32.
- **Daily Stats**: Calculated using `whereDate('start_time', Carbon::today())`.
- **Aggregations**: Functions like `count()`, `max('max_level')`, and `avg('duration_ms')` are processed at the database layer (via Eloquent) to ensure optimal performance. 
- **Filtering**: The `HistoryController` handles dynamic query building based on GET parameters for date ranges, event types, and sorting columns.

## Remote Alarm Workflow
The system allows remote caregivers to silence an active FOG alarm without disrupting the core detection logic.

### 1. Send Command (Laravel)
- Caregiver clicks **Stop Buzzer** on the dashboard.
- Frontend makes a POST request to `/api/command/stop-buzzer`.
- Laravel stores a temporary command in the cache (`device_command_ESP32...`).

### 2. Receive Command (ESP32)
- ESP32 sends its routine heartbeat (every 5 seconds, or immediately when FOG starts) to `/api/heartbeat`.
- The Laravel server checks the cache. If a `STOP_BUZZER` command exists, it attaches it to the JSON response and immediately clears the cache.
- ESP32 receives the `STOP_BUZZER` command in the HTTP response.

### 3. Execute Command (ESP32)
- `systemState->setRemoteBuzzerStop(true)` is triggered.
- `BuzzerController` silences the buzzer.
- FOG detection remains fully active. When motion transitions to REST, the state resets naturally, allowing future FOG events to trigger the buzzer again.

## Known Limitations
- The system currently supports a single hardcoded device (`ESP32-A1B2C3D4`) and patient (`PKN-0001`), per the single-patient scope.
- Notifications (Email, SMS, Push) are intentionally omitted.
- The 3-second UI polling means transient events (< 3 seconds) might only appear in the History table if they finish between UI polling intervals, though the ESP32 now forces an immediate heartbeat to mitigate this.
