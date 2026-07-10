# Project Overview

## Problem Statement

Parkinson's disease (PD) is a progressive neurodegenerative disorder affecting millions worldwide. Two of its most disabling motor symptoms are:

1. **Resting Tremor** — Involuntary rhythmic shaking of the hands or limbs, most prominent when the limb is at rest. Tremor significantly impacts daily activities such as eating, writing, and holding objects.

2. **Freezing of Gait (FOG)** — A brief, episodic inability to initiate or continue walking. The patient feels as though their feet are "glued" to the floor. FOG is a major fall risk and a leading cause of injury in PD patients.

Both symptoms are difficult for caregivers and clinicians to track accurately over time because they are episodic — they may not occur during a clinic visit and are typically underreported by patients.

---

## Project Goal

Design and build a **wearable IoT monitoring and cueing system** that:

- Continuously monitors hand tremor and lower-limb gait using inertial sensors.
- Detects tremor episodes and FOG episodes **locally on-device** using rule-based, threshold-based algorithms.
- Delivers **vibration-based rhythmic cueing** to the patient when FOG is detected, helping to break the freeze.
- Shows real-time status to the patient via an on-wrist/on-device OLED display.
- Transmits confirmed event logs to a cloud-accessible web dashboard over Wi-Fi.
- Allows doctors and caregivers to monitor event history and remotely stop vibration cueing if needed.

> **Scope Clarification:** This is a **university research prototype** intended for academic demonstration. It is not a certified medical device. It does not replace clinical assessment.

---

## Intended Users

| User | Role |
|---|---|
| **Patient** | Wears the device. Receives local OLED feedback and vibration cueing. Does not interact with the web app. |
| **Doctor** | Views monitoring dashboard. Reviews tremor and FOG event history. May issue remote cueing stop. |
| **Caregiver** | Views monitoring dashboard. Receives alerts. May issue remote cueing stop. |

---

## Patient-Side Hardware Components

| Component | Placement | Purpose |
|---|---|---|
| ESP32 | Body-worn enclosure | Central controller, Wi-Fi connectivity, all detection logic |
| MPU6050 #1 | Wrist / Hand | Tremor detection (accelerometer + gyroscope) |
| MPU6050 #2 | Ankle / Foot | Gait analysis and Freezing of Gait detection |
| OLED Display | Wrist / Enclosure | Patient-visible status: normal, tremor detected, FOG detected, cueing active |
| Vibration Motor + Driver Circuit | Wrist / Foot area | Rhythmic tactile cueing during FOG episodes |
| Li-Po Battery + Management | Enclosure | Portable power supply |

---

## Doctor / Caregiver Web Application

A Laravel-powered web dashboard accessible from any browser. The web application:

- Receives confirmed detection event reports from the ESP32 over HTTP.
- Stores all events in a MySQL database with accurate timestamps.
- Presents a dashboard showing:
  - Current device connectivity status
  - Recent tremor events (with severity/level indicator)
  - Recent FOG events
  - Active cueing status
  - Alert notifications
- Allows authorized users to send a **Stop Cueing** command back to the ESP32.
- Implements role-based access for Doctor and Caregiver accounts.
- Uses Laravel Blade templates for server-rendered views with minimal JavaScript.

---

## Tremor Monitoring Concept

- The wrist-mounted MPU6050 continuously streams accelerometer and gyroscope data to the ESP32.
- The ESP32 applies a rule-based algorithm:
  - Computes signal magnitude or frequency characteristics over a short rolling window.
  - Compares against configurable thresholds (amplitude, frequency band typical of PD tremor: 3–7 Hz).
  - Classifies tremor as: None / Mild / Moderate / Severe based on threshold tiers.
- When a sustained tremor episode is confirmed (debounced), the ESP32:
  - Updates the OLED display.
  - Transmits an event report to the Laravel API.
- Laravel does **not** perform tremor detection. It only receives and stores confirmed results.

---

## FOG Monitoring Concept

- The ankle-mounted MPU6050 continuously streams data to the ESP32.
- The ESP32 implements a **state machine** to detect FOG:
  - **Walking state:** Regular periodic gait signals detected.
  - **Freezing candidate state:** Gait signal drops below walking threshold but some low-amplitude high-frequency "trembling" is present (characteristic of FOG).
  - **FOG confirmed state:** Sustained abnormal signal pattern exceeding the FOG duration threshold.
- Detection is entirely rule-based with configurable thresholds. No machine learning is used.
- On FOG confirmation:
  - ESP32 activates the vibration motor (rhythmic cueing pattern).
  - ESP32 updates the OLED.
  - ESP32 transmits an FOG event report to the Laravel API.

---

## Vibration Cueing Concept

- Research indicates that rhythmic sensory cueing (auditory, visual, or tactile) can help PD patients break a freezing episode by engaging alternate motor pathways.
- This system uses **tactile (vibration) cueing**:
  - The vibration motor is driven by a transistor/MOSFET driver circuit (not driven directly from an ESP32 GPIO pin).
  - The ESP32 outputs a PWM or digital pulse pattern to the motor driver.
  - The cueing pattern is a rhythmic on/off cycle (e.g., 1 Hz to simulate a stepping cadence).
- Cueing stops when:
  - The ESP32 detects that walking has resumed (FOG state machine exits FOG state).
  - A Stop Cueing command is received from the Laravel web application.

---

## Timestamped Event Logging Concept

- Every confirmed tremor and FOG event includes:
  - Event type (tremor / FOG)
  - Severity or level (for tremor)
  - Timestamp (ESP32 synchronized via NTP over Wi-Fi)
  - Device identifier
- Events are stored in MySQL tables with proper indexing for efficient querying.
- The dashboard displays paginated event history with filtering by date and event type.

---

## Remote Cueing Stop Concept

- The web dashboard provides an authorized Stop Cueing button.
- When pressed, Laravel stores a pending command in the database.
- The ESP32 periodically polls a Laravel endpoint (or Laravel pushes via a simple flag endpoint).
- On receiving the Stop Cueing command, the ESP32 immediately deactivates the vibration motor.
- The stop event is logged with timestamp and the user who issued the command.
