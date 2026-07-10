# Phase 2 Report — Database Design, Migrations, Models & Seeders

**Status:** ✅ Completed  
**Date:** 2026-07-11  
**Automated Verification:** 49/49 tests passed

---

## Phase Summary

Phase 2 established the complete MySQL database foundation for the Parkinson's Monitoring System. All project-domain tables were created via Laravel migrations, backed by Eloquent models with proper enum casts and relationships. Realistic seed data was inserted for development and demonstration.

No authentication, no business logic, no API, and no frontend were implemented. This phase is strictly a database foundation.

---

## Database Schema Summary

### Tables Created in This Phase

| Table | Purpose | Key Columns |
|---|---|---|
| `users` *(modified)* | Web app users (doctors/caregivers) | Added `role` column |
| `patients` | Monitored Parkinson's patients | `patient_code` (unique), `status` |
| `devices` | ESP32 device units | `device_uid` (unique), `api_token`, `last_seen_at` |
| `detection_events` | Confirmed ESP32 detection events | `event_uuid` (UUID, unique indexed), `event_type`, `tremor_level`, dual timestamps |
| `device_commands` | Remote commands to ESP32 devices | `command_type`, `status`, `issued_at`, `acknowledged_at` |

### All Tables in `parkinson_monitor` Database

| Table | Category |
|---|---|
| `migrations` | Laravel framework |
| `users` | Framework default + Phase 2 role column |
| `password_reset_tokens` | Laravel framework |
| `sessions` | Laravel framework |
| `cache` / `cache_locks` | Laravel framework |
| `jobs` / `job_batches` / `failed_jobs` | Laravel framework |
| `patients` | Phase 2 — project domain |
| `devices` | Phase 2 — project domain |
| `detection_events` | Phase 2 — project domain |
| `device_commands` | Phase 2 — project domain |

---

## Schema Design Decisions

### `patients` — No User Assignment Yet
`assigned_doctor_id` and `assigned_caregiver_id` were intentionally **not** added. Authentication and user-patient relationships will be implemented in Phase 3 via an additional migration, avoiding a premature foreign key dependency on a feature not yet built.

### `detection_events` — event_uuid for Idempotency
A UUID column (`event_uuid`, char 36, unique, indexed) was added to support idempotent event ingestion. If the ESP32 retries a POST request due to a network timeout, the server can detect the duplicate by checking `event_uuid` and skip re-inserting.

### `devices` — api_token Column Design
The `api_token` column is `string(100)` (not `string(64)`) to accommodate future SHA-256 hashing (64 hex chars), prefixed hash formats (e.g., `sha256|...` = 71 chars), or token rotation without any schema changes. The column is hidden from all model serialization via `$hidden`.

### `detection_events` — Generic metadata JSON
The `metadata` column has no fixed schema. It is designed to accept any diagnostic information that future firmware versions may report (RMS values, motion scores, calibration versions, confidence indicators) without requiring future schema migrations.

### Timestamp Compatibility (MariaDB 10.4)
MariaDB 10.4 enforces strict SQL mode where multiple `NOT NULL TIMESTAMP` columns without defaults cause an error. The migrations use `->useCurrent()` on `device_detected_at`, `server_received_at`, and `issued_at` to satisfy this constraint. This is a MariaDB compatibility fix only; the actual values are always set explicitly by the application.

---

## Enums Created

| Enum | Type | Values |
|---|---|---|
| `App\Enums\UserRole` | string | `doctor`, `caregiver` |
| `App\Enums\PatientStatus` | string | `active`, `inactive` |
| `App\Enums\DeviceStatus` | string | `active`, `inactive`, `offline` |
| `App\Enums\EventType` | string | `tremor`, `fog` |
| `App\Enums\TremorLevel` | int | `0`=None, `1`=Mild, `2`=Moderate, `3`=Severe |
| `App\Enums\CommandType` | string | `stop_cueing` |
| `App\Enums\CommandStatus` | string | `pending`, `sent`, `acknowledged`, `failed` |

---

## Models Created / Modified

| Model | Status | Key Casts | Relationships |
|---|---|---|---|
| `User` | Modified | `role → UserRole` | `issuedCommands()` |
| `Patient` | New | `status → PatientStatus`, `date_of_birth → date` | `devices()`, `detectionEvents()` |
| `Device` | New | `status → DeviceStatus`, `last_seen_at → datetime` | `patient()`, `detectionEvents()`, `commands()` |
| `DetectionEvent` | New | `event_type → EventType`, `tremor_level → TremorLevel`, `metadata → array`, `cueing_activated → boolean` | `patient()`, `device()` |
| `DeviceCommand` | New | `command_type → CommandType`, `status → CommandStatus`, `payload → array` | `device()`, `issuer()` |

---

## Factories Created / Modified

| Factory | Status | States |
|---|---|---|
| `UserFactory` | Modified | `doctor()`, `caregiver()` |
| `PatientFactory` | New | `inactive()` |
| `DeviceFactory` | New | `active()`, `offline()` |
| `DetectionEventFactory` | New | `tremor(TremorLevel)`, `fog()`, `fogActive()`, `withMetadata()` |
| `DeviceCommandFactory` | New | `sent()`, `acknowledged()`, `failed()`, `systemGenerated()` |

---

## Seed Data Inserted

| Entity | Count | Details |
|---|---|---|
| Users | 4 | 2 doctors (Sarah Ahmed, James Mwangi), 2 caregivers (Maria Santos, Robert Tan) |
| Patients | 5 | PKN-0001 through PKN-0005 with realistic clinical notes |
| Devices | 5 | 1 ESP32 per patient; 3 active, 2 offline |
| Detection Events | 23 | 16 tremor (Mild/Moderate/Severe), 7 FOG (6 with cueing stopped, 1 still active) |
| Device Commands | 2 | 1 pending stop_cueing, 1 acknowledged stop_cueing |

### Seed Breakdown by Patient

| Patient | Events | Types |
|---|---|---|
| PKN-0001 Ahmad | 5 | Tremor: 2×Mild, 2×Moderate, 1×Severe |
| PKN-0002 Fatimah | 5 | Tremor: 2×Mild, 1×Moderate; FOG: 2 (cueing stopped) |
| PKN-0003 Thomas | 6 | Tremor: 1×Moderate, 1×Severe; FOG: 4 (3 stopped, 1 still active) |
| PKN-0004 Rosmah | 3 | Tremor: 3×Mild |
| PKN-0005 Lim | 4 | Tremor: 1×Moderate, 1×Severe; FOG: 2 (1 stopped, 1 stopped) |

---

## Automated Verification

**Results: 49/49 PASSED — 0 FAILED**

All tests verified:
- Row counts in all 5 domain tables
- UserRole enum cast on User model
- isDoctor() / isCaregiver() helper methods
- Patient→Device and Patient→DetectionEvent relationships
- Device→Patient relationship correctness
- api_token correctly hidden from serialization
- FOG events: event_type enum cast, null tremor_level, cueing_activated = true
- Tremor events: TremorLevel enum cast (Severe level), cueing_activated = false
- Pending command: CommandStatus cast, acknowledged_at = null
- Acknowledged command: response_time_seconds > 0, issuer User relationship
- All 23 event_uuids are unique
- metadata column cast to PHP array with correct keys
- Tremor levels 1, 2, 3 all present in seed data

---

## Architecture Compliance

| Rule | Status |
|---|---|
| MySQL ONLY (no SQLite, no PostgreSQL) | ✅ Confirmed |
| ESP32 makes all detection decisions | ✅ Laravel stores results only — no detection logic |
| No authentication implemented | ✅ Confirmed |
| No dashboard implemented | ✅ Confirmed |
| No API routes implemented | ✅ Confirmed |
| No firmware created | ✅ Confirmed |
| No Git commit performed | ✅ Confirmed |

---

## Migration Execution Log

| Migration | Batch | Status |
|---|---|---|
| `0001_01_01_000000_create_users_table` | 1 | Ran (Phase 1) |
| `0001_01_01_000001_create_cache_table` | 1 | Ran (Phase 1) |
| `0001_01_01_000002_create_jobs_table` | 1 | Ran (Phase 1) |
| `2026_07_11_000001_add_role_to_users_table` | 2 | Ran ✅ |
| `2026_07_11_000002_create_patients_table` | 2 | Ran ✅ |
| `2026_07_11_000003_create_devices_table` | 2 | Ran ✅ |
| `2026_07_11_000004_create_detection_events_table` | 2 | Ran ✅ (fixed MariaDB timestamp issue) |
| `2026_07_11_000005_create_device_commands_table` | 2 | Ran ✅ |

---

## Known Limitations

1. **`users` table test user** — The seeded test user from Phase 1 (`test@example.com`) has `role = 'doctor'` (applied by the migration default). This user is not a real doctor; it will be removed or updated in Phase 3.
2. **Patient-User assignment** — Patients are not yet linked to doctors or caregivers. This will be added in Phase 3 via a separate migration.
3. **api_token not hashed** — Device tokens are stored as plain 64-char random strings for Phase 2. Token hashing will be implemented in Phase 11 when the ESP32 API is built.
4. **`verify_phase2.php`** — This file is a development verification script in the `web/` root. It should be deleted or moved to `tests/` before production deployment.
