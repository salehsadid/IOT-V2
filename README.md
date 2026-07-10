# ESP32-Based Parkinson's Tremor and Freezing of Gait Monitoring System with Vibration Cueing and Laravel Web Dashboard

> **Status:** Phase 0 — Workspace & Environment Setup  
> **Development Model:** Phase-by-phase. No phase is begun until the previous phase is manually verified and approved.

---

## Project Description

A university prototype IoT monitoring and assistance system designed to help caregivers and clinicians observe Parkinson's disease-related motor symptoms in near real-time.

The system detects **hand tremor** and **Freezing of Gait (FOG)** entirely on-device using a rule-based, threshold-based algorithm running on an ESP32 microcontroller. When FOG is detected, the device activates a vibration motor to deliver rhythmic cueing to the patient. All confirmed events are transmitted over Wi-Fi to a Laravel web dashboard accessible by doctors and caregivers.

> ⚠️ This is a university research prototype. It is **not** a certified medical diagnostic device and must not be used as a substitute for clinical judgment.

---

## High-Level Architecture

```
[MPU6050 - Wrist]  ──┐
                      ├──► [ESP32] ──► Local Tremor Decision
[MPU6050 - Ankle]  ──┘         │
                                ├──► Local FOG Decision ──► [Vibration Motor]
                                ├──► [OLED Display]
                                └──► Wi-Fi ──► [Laravel API] ──► [MySQL]
                                                                      │
                                                          [Blade Dashboard]
                                                                      │
                                                     [Doctor / Caregiver Browser]
                                                                      │
                                                        Stop Cueing Command
                                                                      │
                                                          [Laravel] ──► [ESP32]
```

---

## Technology Stack

### Embedded (Patient-Side)
| Component | Role |
|---|---|
| ESP32 | Main microcontroller, Wi-Fi, all decision logic |
| MPU6050 #1 (wrist) | Tremor monitoring via accelerometer + gyroscope |
| MPU6050 #2 (ankle) | Gait and Freezing of Gait monitoring |
| OLED Display | Patient-visible status and alerts |
| Vibration Motor + Driver | FOG rhythmic cueing |

### Web Application (Doctor/Caregiver-Side)
| Technology | Role |
|---|---|
| Laravel (PHP) | Backend framework, API endpoints, routing |
| MySQL | Persistent storage of events and logs |
| Blade Templates | Server-side rendered dashboard views |
| HTML / CSS | Page structure and styling |
| JavaScript (basic) | Minimal client-side interactivity only |

### Decision-Making Architecture
- **All** tremor detection logic runs locally on the ESP32.
- **All** FOG detection logic runs locally on the ESP32.
- Detection is **rule-based and threshold-based** — no machine learning, no datasets.
- Laravel receives and stores **confirmed events** from the ESP32; it does not perform detection.

---

## Development Phases (Planned)

| Phase | Title | Status |
|---|---|---|
| 0 | Workspace & Environment Setup | ✅ Completed |
| 1 | Laravel + MySQL Foundation | ✅ Completed — Laravel 12 in `web/`, MySQL `parkinson_monitor` |
| 2 | Database Schema + Models | ✅ Completed — 4 domain tables, 5 models, 7 enums, 23 seed events |
| 3 | Authentication + Doctor/Caregiver Roles | ✅ Completed — Custom Auth, RoleMiddleware, Dashboard |
| 4 | Laravel Blade Dashboard Foundation | ⬜ Pending |
| 5 | ESP32 Firmware Foundation | ⬜ Pending |
| 6 | Dual MPU6050 Integration | ⬜ Pending |
| 7 | Tremor Detection Logic | ⬜ Pending |
| 8 | FOG Detection State Machine | ⬜ Pending |
| 9 | OLED Integration | ⬜ Pending |
| 10 | Vibration Cueing | ⬜ Pending |
| 11 | ESP32 to Laravel API Integration | ⬜ Pending |
| 12 | Event Logging and Web Alerts | ⬜ Pending |
| 13 | Remote Cueing Stop | ⬜ Pending |
| 14 | Full System Integration | ⬜ Pending |
| 15 | Calibration, Reliability, Testing & Final Audit | ⬜ Pending |

---

## Repository Structure (Planned)

```
project-root/
├── docs/                   # Planning and architecture documentation
├── web/                    # Laravel web application (Phase 1+)
├── firmware/               # ESP32 PlatformIO project (Phase 5+)
├── README.md
└── .gitignore
```

---

## Getting Started

> Detailed setup instructions will be added as each phase is completed.

For Phase 0, see [`docs/phase-status.md`](docs/phase-status.md) for the current environment checklist.
