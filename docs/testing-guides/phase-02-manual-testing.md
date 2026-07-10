# Phase 2 — Manual Testing Guide

**Purpose:** Steps you can perform yourself to verify Phase 2 is complete and correct.

---

## Prerequisites

- XAMPP running (Apache + MySQL)
- Terminal open at `d:\Academic Projects\IOT V2\web\`

---

## Test 1 — Migration Status

```powershell
# From: d:\Academic Projects\IOT V2\web\
php artisan migrate:status
```

**Expected output:**

```
Migration name                                    Batch / Status
0001_01_01_000000_create_users_table              [1] Ran
0001_01_01_000001_create_cache_table              [1] Ran
0001_01_01_000002_create_jobs_table               [1] Ran
2026_07_11_000001_add_role_to_users_table         [2] Ran
2026_07_11_000002_create_patients_table           [2] Ran
2026_07_11_000003_create_devices_table            [2] Ran
2026_07_11_000004_create_detection_events_table   [2] Ran
2026_07_11_000005_create_device_commands_table    [2] Ran
```

---

## Test 2 — All Tables Exist

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SHOW TABLES;"
```

**Expected:** 14 tables listed, including `patients`, `devices`, `detection_events`, `device_commands`.

---

## Test 3 — Users Have Correct Roles

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT name, role FROM users;"
```

**Expected:**
- Maria Santos → `caregiver`
- Robert Tan → `caregiver`
- Dr. James Mwangi → `doctor`
- Dr. Sarah Ahmed → `doctor`

---

## Test 4 — Patients Exist

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT patient_code, full_name, status FROM patients;"
```

**Expected:** PKN-0001 through PKN-0005, all `active`.

---

## Test 5 — Devices Are Linked to Patients

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT d.device_uid, d.status, p.patient_code FROM devices d JOIN patients p ON d.patient_id = p.id ORDER BY p.patient_code;"
```

**Expected:** 5 rows, each device linked to one patient.

---

## Test 6 — Detection Events Mix

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT event_type, tremor_level, cueing_activated, COUNT(*) AS count FROM detection_events GROUP BY event_type, tremor_level, cueing_activated ORDER BY event_type, tremor_level;"
```

**Expected:**
- FOG: tremor_level NULL, cueing_activated 1
- Tremor level 1 (Mild), cueing_activated 0
- Tremor level 2 (Moderate), cueing_activated 0
- Tremor level 3 (Severe), cueing_activated 0

---

## Test 7 — event_uuid is Set and Unique

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT COUNT(*) AS total, COUNT(DISTINCT event_uuid) AS unique_uuids FROM detection_events;"
```

**Expected:** total = unique_uuids = 23 (no duplicates)

---

## Test 8 — Device Commands Lifecycle

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT command_type, status, acknowledged_at IS NOT NULL AS is_acked FROM device_commands;"
```

**Expected:**
- Row 1: `stop_cueing`, `pending`, is_acked = 0
- Row 2: `stop_cueing`, `acknowledged`, is_acked = 1

---

## Test 9 — metadata JSON Column

```powershell
& "C:\xampp\mysql\bin\mysql.exe" -u root parkinson_monitor -e "SELECT id, JSON_EXTRACT(metadata, '$.firmware_version') AS fw, JSON_EXTRACT(metadata, '$.sensor_placement') AS sensor FROM detection_events LIMIT 5;"
```

**Expected:** firmware_version = "1.0.0", sensor_placement = "ankle" or "wrist"

---

## Test 10 — Run Automated Verification Script

```powershell
# From: d:\Academic Projects\IOT V2\web\
php verify_phase2.php
```

**Expected:** `Results: 49 passed, 0 failed`

---

## Test 11 — Confirm No SQLite

```powershell
Test-Path "d:\Academic Projects\IOT V2\web\database\database.sqlite"
```

**Expected:** `False`

---

## Test 12 — Confirm No Firmware Directory

```powershell
Test-Path "d:\Academic Projects\IOT V2\firmware"
```

**Expected:** `False`

---

## Test 13 — Confirm api_token is Hidden

Open `web/app/Models/Device.php` and confirm `api_token` is listed in `$hidden`.

Verify it does NOT appear in JSON output:
```powershell
php -r "
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
\$d = App\Models\Device::first();
echo array_key_exists('api_token', \$d->toArray()) ? 'FAIL: token visible' : 'PASS: token hidden';
echo PHP_EOL;
"
```

---

## Test 14 — Database Connection Type

```powershell
php artisan about
```

Look for: **Database: mysql**  
Confirm it does NOT say: sqlite

---

## ✅ Phase 2 Checklist

| Check | Expected |
|---|---|
| `migrate:status` all 8 show `Ran` | ✅ |
| 14 tables in database | ✅ |
| 4 users (2 doctor, 2 caregiver) | ✅ |
| 5 patients (PKN-0001 to PKN-0005) | ✅ |
| 5 devices (1 per patient) | ✅ |
| 23 detection events (tremor + FOG mix) | ✅ |
| 2 commands (1 pending, 1 acknowledged) | ✅ |
| All event_uuids unique | ✅ |
| metadata JSON readable | ✅ |
| api_token hidden from serialization | ✅ |
| No SQLite file | ✅ |
| No firmware directory | ✅ |
| Automated script: 49/49 | ✅ |
