# Phase Status Tracker

> **Last Updated:** Phase 8  
> **Development Model:** Strictly sequential. No phase begins until the previous phase is manually verified and explicitly approved.

---

## Phase 0 — Workspace and Environment Setup

**Status:** ✅ Completed

### Objectives
- [x] Inspect workspace — confirmed empty
- [x] Check PHP — v8.2.12 via XAMPP ✅
- [x] Check Composer — v2.9.8 ✅
- [x] Check MySQL server — MariaDB 10.4.32 running via XAMPP ✅
- [x] Check MySQL client on PATH — ⚠️ Not on PATH (binary at `C:\xampp\mysql\bin\mysql.exe`)
- [x] Check Node.js — v24.14.1 ✅
- [x] Check npm — v11.11.0 ✅
- [x] Check Git — v2.51.0 ✅
- [x] Check PlatformIO CLI — ❌ Not installed (non-blocking for Phase 1–4)
- [x] Create `README.md`
- [x] Create `docs/project-overview.md`
- [x] Create `docs/architecture.md`
- [x] Create `docs/phase-status.md`
- [x] Create `.gitignore`

### Environment Notes
- PHP emits a startup warning about `oci8_12c` (Oracle extension not present). This is a harmless XAMPP misconfiguration and does **not** block PHP, Composer, or Laravel.
- MySQL client (`mysql`) is not on the system PATH. To use it from the terminal, either:
  - Add `C:\xampp\mysql\bin` to the Windows `PATH` environment variable, **or**
  - Call it with the full path: `C:\xampp\mysql\bin\mysql.exe`
  - This does **not** block Phase 1 (Laravel connects via PDO, not the CLI client).
- PlatformIO CLI is not installed. This is expected and **does not block** Phase 1 through Phase 4 (web application phases). It will be needed starting Phase 5.

---

---

## Phase 1 — Laravel + MySQL Foundation

**Status:** ✅ Completed

### Objectives Achieved
- [x] Laravel 12.63.0 installed via Composer into `web/` directory
- [x] Laravel boots successfully — `php artisan about` confirms `Database: mysql`
- [x] APP_KEY generated and set (base64 encoded) — not exposed
- [x] `web/.env` configured: `DB_CONNECTION=mysql`, `DB_DATABASE=parkinson_monitor`, `DB_HOST=127.0.0.1`, `DB_PORT=3306`
- [x] `web/.env.example` updated with MySQL template (no real credentials)
- [x] MySQL database `parkinson_monitor` created (utf8mb4_unicode_ci)
- [x] `pdo_mysql` PHP extension confirmed available
- [x] Laravel → PDO → MySQL → `parkinson_monitor` connection verified
- [x] Default Laravel migrations ran against MySQL — 3 migrations successful
- [x] Default SQLite file removed (was created by post-install hook, replaced by MySQL)
- [x] Laravel dev server started — HTTP 200 confirmed on `http://127.0.0.1:8000`
- [x] No project-specific domain tables created
- [x] No authentication implemented
- [x] No dashboard implemented
- [x] No firmware project created
- [x] No Git commit performed

### Framework-Default Tables Created (MySQL `parkinson_monitor`)
| Table | Source Migration | Purpose |
|---|---|---|
| `migrations` | Laravel framework | Tracks migration history |
| `users` | `0001_01_01_000000_create_users_table` | Default user model table |
| `password_reset_tokens` | `0001_01_01_000000_create_users_table` | Password reset functionality |
| `sessions` | `0001_01_01_000000_create_users_table` | Session storage |
| `cache` | `0001_01_01_000001_create_cache_table` | Cache storage |
| `cache_locks` | `0001_01_01_000001_create_cache_table` | Cache lock mechanism |
| `jobs` | `0001_01_01_000002_create_jobs_table` | Queue job storage |
| `job_batches` | `0001_01_01_000002_create_jobs_table` | Queue batch tracking |
| `failed_jobs` | `0001_01_01_000002_create_jobs_table` | Failed job logging |

> All tables above are **Laravel framework defaults**. No project-specific domain tables exist yet.

---

## Phase 2 — Database Schema + Models

**Status:** ✅ Completed

### Objectives Achieved
- [x] 7 PHP 8.1 backed enums created (`UserRole`, `PatientStatus`, `DeviceStatus`, `EventType`, `TremorLevel`, `CommandType`, `CommandStatus`)
- [x] 5 migrations created and ran successfully (Batch 2)
- [x] `users` table extended with `role` column (via separate migration, no re-creation)
- [x] `patients` table created — no `assigned_doctor_id` (deferred to Phase 3)
- [x] `devices` table created — `device_uid` unique, `api_token` string(100) for future hashing
- [x] `detection_events` table created — `event_uuid` UUID unique indexed for idempotency
- [x] `device_commands` table created — full `pending→sent→acknowledged→failed` lifecycle
- [x] MariaDB 10.4 timestamp compatibility fixed (`useCurrent()` on NOT NULL timestamps)
- [x] 5 Eloquent models created/updated with enum casts and relationships
- [x] 5 factories created/updated with realistic states
- [x] 5 seeders created, orchestrated by `DatabaseSeeder`
- [x] Seed data: 4 users, 5 patients, 5 devices, 23 detection events, 2 device commands
- [x] Automated verification: 49/49 tests passed
- [x] Phase report: `docs/phase-reports/phase-02-report.md`
- [x] Manual testing guide: `docs/testing-guides/phase-02-manual-testing.md`
- [x] No authentication, no dashboard, no API, no firmware
- [x] MySQL ONLY — no SQLite, no PostgreSQL

### Project-Domain Tables Created
| Table | Migration |
|---|---|
| `patients` | `2026_07_11_000002_create_patients_table` |
| `devices` | `2026_07_11_000003_create_devices_table` |
| `detection_events` | `2026_07_11_000004_create_detection_events_table` |
| `device_commands` | `2026_07_11_000005_create_device_commands_table` |

---

## Phase 3 — Authentication + Doctor/Caregiver Roles

**Status:** ✅ Completed

### Objectives Achieved
- [x] Implemented manual Laravel authentication (Login/Logout)
- [x] Omitted Registration, Password Reset, Email Verification, and 2FA
- [x] Created `AuthController` and `DashboardController`
- [x] Created `RoleMiddleware` for future role-specific protected routes
- [x] Created clean HTML/CSS only `login.blade.php` (No Bootstrap/React/Vue)
- [x] Created temporary `dashboard.blade.php` displaying user Name, Email, Role, and Logout button
- [x] Configured protected routes requiring `auth` middleware
- [x] Updated `UsersSeeder` with demo accounts: `doctor@example.com` and `caregiver@example.com`
- [x] Automated verification: 15/15 tests passed
- [x] Created Phase 3 report and manual testing guide

---

## Phase 4 — Laravel Blade Dashboard Foundation

**Status:** ✅ Completed

### Objectives Achieved
- [x] Created `layouts.app` master layout
- [x] Implemented UI partials (`sidebar`, `navbar`, `footer`)
- [x] Designed responsive layout using completely custom CSS (No Bootstrap/Tailwind)
- [x] Included mobile-responsive sidebar with toggle behavior
- [x] Added placeholder dashboard cards (Patients, Devices, Events) with dummy values
- [x] Added placeholder "Recent Activity" table with dummy data
- [x] Sidebar navigation UI created with active state on Dashboard
- [x] Replaced old temporary `dashboard.blade.php` with structured `dashboard/index.blade.php`
- [x] Kept all backend functionality strictly placeholder as required
- [x] Automated verification: 12/12 tests passed
- [x] Created Phase 4 report and manual testing guide

---

## Phase 5 — ESP32 Firmware Foundation

**Status:** ✅ Completed

### Objectives Achieved
- [x] Created `firmware/` directory and Arduino-compatible structure (`firmware.ino`)
- [x] Implemented `Config` class for centralized pins, network, and I2C address settings
- [x] Included `secrets.h.example` to protect sensitive credentials (WiFi, API Token)
- [x] Implemented a lightweight Serial `Logger` utility
- [x] Created `SystemState` class to manage system readiness state safely
- [x] Designed skeleton managers for hardware separation: `SensorManager`, `DisplayManager`, `CueingController`
- [x] Applied proper header guards (`#pragma once`) across all headers
- [x] Orchestrated initialization in `setup()` and periodic loops in `loop()`
- [x] Ensured strict compliance: NO actual sensor, display, or WiFi logic implemented
- [x] Generated `README.md` inside firmware directory
- [x] Phase 5 report and manual testing guide created

---

## Phase 6 — Dual MPU6050 Integration

**Status:** ✅ Completed

### Objectives Achieved
- [x] Implemented I2C initialization in `SensorManager` using Config pins
- [x] Wrote standard I2C ping utility (`checkSensor`) using `Wire.h`
- [x] Detected Hand MPU6050 at address `0x68`
- [x] Detected Leg MPU6050 at address `0x69`
- [x] Implemented graceful error handling and system state updates on sensor disconnect
- [x] Implemented periodic (5-second) background checking of sensor health
- [x] Added detailed hardware wiring guide explaining I2C bus sharing and the `AD0` pin
- [x] Phase 6 report and manual testing guide created
- [x] Maintained strict compliance (no sensor readings or math implemented yet)

---

## Phase 7 — Raw MPU6050 Data Acquisition

**Status:** ✅ Completed

### Objectives Achieved
- [x] Implemented proper initialization of both MPU6050 sensors.
- [x] Woke both sensors from sleep mode.
- [x] Configured accelerometer to ±2g and gyroscope to ±250°/s.
- [x] Implemented reading of raw Accel and Gyro values for Hand and Leg sensors.
- [x] Converted raw values into human-readable units (g and °/s).
- [x] Created formatted Serial output every 100ms.
- [x] Handled sensor disconnection gracefully without freezing the program.
- [x] Created debug mode constant `DEBUG_SENSOR_OUTPUT`.
- [x] Phase 7 report and manual testing guide created.
- [x] (Tremor detection deferred to future phases based on new roadmap)

---

## Phase 8 — Threshold Calibration & Motion Analysis

**Status:** ✅ Completed

### Objectives Achieved
- [x] Defined and added threshold configuration to `Config.h` (REST_GYRO_THRESHOLD, HAND_TREMOR_THRESHOLD, LEG_WALKING_THRESHOLD).
- [x] Implemented math helper functions in `SensorManager` to calculate 3D magnitude for Acceleration and Gyroscope.
- [x] Improved Serial Debug output to cleanly display magnitudes for reference.
- [x] Confirmed calculations operate on experimental data without implementing detection logic.
- [x] Created `phase-08-report.md` and `phase-08-manual-testing.md`.
- [x] Updated project roadmap across all documentation.

---

## Phase 9 — Threshold-Based Tremor Detection

**Status:** ✅ Completed

### Objectives Achieved
- [x] Processed 8 real-world demonstration data files to analyze Tremor and FOG scenarios.
- [x] Defined 4 Tremor levels (0, 1, 2, 3) with precise thresholds in `Config.h`.
- [x] Implemented a 10-sample Moving Average (MA) inside `DetectionManager` to stabilize fluctuating real-time readings.
- [x] Added leg movement suppression logic (Tremor Level forced to 0 if Leg Gyro MA > 15).
- [x] Tested mathematically to ensure thresholds match the demonstration data exactly.

---

## Phase 10 — Threshold-Based Freezing of Gait (FOG) Detection

**Status:** ✅ Completed

### Objectives Achieved
- [x] Analyzed real-world calibration data to differentiate Standing (Leg Gyro < 5.0) from FOG (Leg Gyro ~ 11.0 to 26.0).
- [x] Defined a 6-state Motion State Machine (`REST`, `POSSIBLE_WALKING`, `WALKING`, `POSSIBLE_FOG`, `FOG_CONFIRMED`, `RECOVERY`).
- [x] Implemented a 2-second confirmation timer before entering FOG to prevent false positives when coming to a normal stop.
- [x] Integrated state handling into `SystemState` and detection logic cleanly into `DetectionManager`.
- [x] Limited serial outputs to print ONLY when the motion state actually transitions.

---

## Phase 11 — OLED Display Integration

**Status:** ✅ Completed

### Objectives Achieved
- [x] Initialized the SSD1306 OLED via I2C inside `DisplayManager`.
- [x] Designed Boot, Normal, Alert, and Error screens.
- [x] Populated the screens dynamically using variables from `SystemState`.
- [x] Configured display refresh rate to 500ms to avoid screen flickering.
- [x] Alert screens override normal monitoring screens for 3 seconds upon tremor or FOG detection.

---

## Phase 12 — FOG Audible Cueing System (Buzzer)

**Status:** ✅ Completed

### Objectives Achieved
- [x] Configured buzzer timings in `Config.h` (400ms ON / 300ms OFF).
- [x] Implemented a non-blocking `millis()` toggle inside `BuzzerController`.
- [x] Restricted buzzer activation *strictly* to `FOG_CONFIRMED` events (Tremors do not beep).
- [x] Added clean serial logging for Alert Started/Stopped.
- [x] Validated that the buzzer halts instantly upon FOG Recovery.

---

## Phase 13 — ESP32 -> Laravel Event Logging & History

**Status:** ✅ Completed

### Objectives Achieved
- [x] Implemented `ApiClient` in ESP32 firmware for non-blocking WiFi connection and HTTP POST requests.
- [x] Configured the ESP32 to upload ONLY confirmed `TREMOR` and `FOG` events (using `duration_ms` logic).
- [x] Rewrote Laravel `DetectionEvent` model and migration to track `start_level` and `max_level` for Tremor events.
- [x] Created `Api\EventController` to validate JSON payload, calculate `start_time` and `end_time` dynamically, and save to MySQL.
- [x] Developed `HistoryController` and a custom Blade template (`resources/views/history.blade.php`) to view paginated historical data.
- [x] Confirmed WiFi connection loss does NOT crash or halt core offline detection.

---

## Phase 14 — Live Dashboard, Event Logging & Remote Alarm Control

**Status:** ⬜ Not Started

### Planned Objectives
- Display events on dashboard with severity, duration, and timestamp.
- Implement Stop Alarm button in dashboard.
- Store pending commands in MySQL and poll from ESP32.
- ESP32 acknowledges command and stops buzzer.

---

## Phase 15 — Full System Integration, Testing & Final Validation

**Status:** ⬜ Not Started

### Planned Objectives
- Run full hardware system.
- Test edge cases (Wi-Fi loss, sensor disconnect).
- Write final system documentation.
