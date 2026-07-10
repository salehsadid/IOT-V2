# System Architecture

## Overview

This document defines the confirmed architecture for the ESP32-Based Parkinson's Monitoring System. All design decisions recorded here are authoritative for subsequent development phases.

---

## Architecture Principles

| Principle | Decision |
|---|---|
| **Detection location** | All sensor-based decisions (tremor, FOG) are made **locally on the ESP32**. The server never performs detection. |
| **Detection method** | Rule-based, threshold-based algorithms only. No machine learning. No dataset-based prediction. |
| **Communication** | ESP32 transmits **confirmed events** to Laravel over Wi-Fi (HTTP/JSON). |
| **Cueing** | Vibration motor controlled exclusively by the ESP32. |
| **Command channel** | Doctor/caregiver sends Stop Cueing via Laravel; ESP32 polls for commands. |
| **Database** | MySQL (via XAMPP/MariaDB in development). No substitutes (no SQLite, no PostgreSQL). |
| **Frontend** | Laravel Blade templates. Basic JavaScript only when required. |

---

## Full System Architecture Diagram

```mermaid
flowchart TD
    subgraph PATIENT["👤 Patient (Wearable Device)"]
        MPU1["MPU6050 #1\n(Wrist / Hand)"]
        MPU2["MPU6050 #2\n(Ankle / Foot)"]
        ESP["ESP32\n(Central Controller)"]
        OLED["OLED Display\n(Patient Status)"]
        VM["Vibration Motor\n+ Driver Circuit"]
        MPU1 -- "I²C — Raw accel/gyro" --> ESP
        MPU2 -- "I²C — Raw accel/gyro" --> ESP
        ESP -- "Status text\nTremor / FOG / OK" --> OLED
        ESP -- "PWM cue signal" --> VM
    end

    subgraph DETECT["🧠 On-Device Detection (ESP32 Only)"]
        TD["Tremor Detection\nRule-Based Threshold\n3–7 Hz, amplitude tiers"]
        FD["FOG Detection\nState Machine\nWalking → Candidate → FOG"]
        ESP --> TD
        ESP --> FD
        FD -- "FOG Confirmed" --> VM
    end

    subgraph WEB["🌐 Web Application (Server)"]
        API["Laravel API\n(Event Receiver\nCommand Endpoint)"]
        DB["MySQL Database\n(Events, Logs, Users)"]
        DASH["Blade Dashboard\n(Doctor / Caregiver)"]
        API --> DB
        DB --> DASH
    end

    subgraph USERS["👨‍⚕️ Users (Browser)"]
        DOC["Doctor"]
        CG["Caregiver"]
        DASH --> DOC
        DASH --> CG
    end

    ESP -- "Wi-Fi HTTP POST\nConfirmed events" --> API
    ESP -- "Wi-Fi HTTP GET\nPoll for commands" --> API
    DOC -- "Stop Cueing\ncommand" --> DASH
    CG -- "Stop Cueing\ncommand" --> DASH
    DASH -- "POST /command\nstop_cue" --> API
    API -- "Command flag\nin DB" --> DB
    DB -- "Command\nreturned on poll" --> API
    API -- "HTTP response\n{action: stop_cue}" --> ESP
    ESP -- "Motor OFF" --> VM
```

---

## Data Flow: Tremor Event

```mermaid
sequenceDiagram
    participant MPU1 as MPU6050 #1 (Wrist)
    participant ESP as ESP32
    participant API as Laravel API
    participant DB as MySQL
    participant DASH as Blade Dashboard

    loop Every ~10ms
        MPU1->>ESP: Raw accel/gyro data (I²C)
        ESP->>ESP: Compute magnitude, apply threshold
    end

    ESP->>ESP: Tremor sustained > debounce window?
    Note over ESP: YES → Event confirmed

    ESP->>OLED: Show "Tremor Detected — Level X"
    ESP->>API: POST /api/events {type:tremor, level:X, ts:...}
    API->>DB: INSERT INTO tremor_events
    DB-->>DASH: Updated on next page load / poll
```

---

## Data Flow: FOG Event and Cueing

```mermaid
sequenceDiagram
    participant MPU2 as MPU6050 #2 (Ankle)
    participant ESP as ESP32
    participant VM as Vibration Motor
    participant API as Laravel API
    participant DB as MySQL

    loop Every ~10ms
        MPU2->>ESP: Raw accel/gyro data (I²C)
        ESP->>ESP: FOG state machine evaluation
    end

    ESP->>ESP: State: WALKING → CANDIDATE → FOG CONFIRMED
    ESP->>VM: Activate rhythmic cue (PWM pattern)
    ESP->>OLED: Show "FOG Detected — Cueing Active"
    ESP->>API: POST /api/events {type:fog, ts:...}
    API->>DB: INSERT INTO fog_events

    loop Every ~5s (poll)
        ESP->>API: GET /api/commands
        API->>DB: SELECT pending commands
        DB-->>API: {action: stop_cue}
        API-->>ESP: {action: stop_cue}
        ESP->>VM: Motor OFF
        ESP->>API: POST /api/commands/{id}/ack
    end
```

---

## Component Responsibilities

### ESP32 — Responsibilities
- Read raw sensor data from both MPU6050 sensors via I²C.
- Run tremor detection algorithm (wrist sensor).
- Run FOG detection state machine (ankle sensor).
- Drive OLED display with current status.
- Drive vibration motor via driver circuit (PWM).
- Synchronize time via NTP over Wi-Fi.
- Transmit confirmed events to Laravel API.
- Poll Laravel API for remote commands (Stop Cueing).
- Acknowledge received commands.

### Laravel — Responsibilities
- Expose HTTP API endpoints for event ingestion and command polling.
- Authenticate requests from the ESP32 (API token).
- Store event data in MySQL with accurate timestamps.
- Provide Blade-rendered dashboard for Doctor/Caregiver.
- Implement role-based access control (Doctor vs Caregiver).
- Store and serve remote Stop Cueing commands.
- Log command acknowledgements.

### Laravel — Explicit Non-Responsibilities
> ❌ Laravel does **NOT** perform tremor detection.  
> ❌ Laravel does **NOT** perform FOG detection.  
> ❌ Laravel does **NOT** analyze raw sensor data.  
> ❌ Laravel does **NOT** control the vibration motor directly.

### MySQL — Responsibilities
- Store all application data: users, roles, devices, events, commands, logs.
- Provide indexed queries for dashboard display and filtering.
- Maintain referential integrity between entities.

---

## Hardware Interface Summary

| Interface | Devices | Protocol |
|---|---|---|
| MPU6050 #1 ↔ ESP32 | Wrist sensor | I²C (address 0x68) |
| MPU6050 #2 ↔ ESP32 | Ankle sensor | I²C (address 0x69, AD0=HIGH) |
| OLED ↔ ESP32 | SSD1306 128×64 | I²C |
| Vibration Motor ↔ ESP32 | Via MOSFET/BJT driver | GPIO PWM |
| ESP32 ↔ Internet | Wi-Fi router | IEEE 802.11 b/g/n |
| Browser ↔ Laravel | Doctor/Caregiver | HTTP/HTTPS |

---

## Security Considerations (To Be Implemented in Later Phases)

- ESP32 authenticates to Laravel API using a pre-shared API token stored in firmware.
- Doctor/Caregiver authenticate via Laravel's session-based authentication (Breeze or custom).
- `.env` and all secrets are excluded from version control via `.gitignore`.
- Environment-specific credentials are never hardcoded in source files.

---

## Development Environment Stack

| Tool | Version | Role |
|---|---|---|
| PHP | 8.2.12 (XAMPP) | Laravel runtime |
| Composer | 2.9.8 | PHP dependency manager |
| MySQL/MariaDB | 10.4.32 (XAMPP) | Database server |
| Node.js | 24.14.1 | Frontend asset tooling |
| npm | 11.11.0 | Node package manager |
| Git | 2.51.0 | Version control |
| PlatformIO | Not yet installed | ESP32 firmware build |
