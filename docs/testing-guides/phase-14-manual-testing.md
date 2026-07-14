# Phase 14 Manual Testing Guide

This guide provides step-by-step instructions for verifying the Phase 14 monitoring, statistics, and remote alarm functionalities.

## 1. Dashboard Statistics & Live Monitoring
1. Log in to the Laravel web application as a Doctor or Caregiver.
2. Navigate to the **Dashboard**.
3. Verify that the **Daily & Lifetime Statistics** section displays.
4. Verify that "Tremor Events (Today)" and "FOG Events (Today)" update if new events are recorded.
5. Simulate a Tremor event by shaking the Hand MPU for >3 seconds.
6. Observe the **Tremor Status (Live)** card on the dashboard. It should turn red and display "TREMOR LVL X".
7. Verify the "Syncing every 3 seconds..." text and that the UI power/sensor badges indicate "ONLINE" and "OK".

## 2. Remote Alarm Control (Stop Buzzer)
1. Simulate a FOG event by shaking both the Hand and Leg MPUs for >2 seconds.
2. The OLED will show "FOG DETECTED" and the ESP32 buzzer will start sounding rhythmically.
3. On the Dashboard, the **FOG Status (Live)** card will turn red and display "FOG DETECTED".
4. The **Stop Buzzer** button will become enabled (red pointer cursor).
5. Click **Stop Buzzer**.
6. The button text will change to "Queued..." and become temporarily disabled.
7. Within 5 seconds, the ESP32 buzzer should stop sounding.
8. The button will revert to "Stop Buzzer".
9. Stop shaking the MPUs to reset the FOG state. The dashboard should return to "NO FOG" and the button should become disabled.

## 3. History Filters & Sorting
1. Navigate to the **Event History** page via the dashboard link.
2. The page should display a table of all recorded events (Tremor and FOG).
3. Use the **Event Type** dropdown to select `FOG` and click **Apply Filters**.
4. Verify that only FOG events are displayed in the table.
5. Use the **Start Date** and **End Date** inputs to select yesterday's date. Click **Apply Filters**.
6. Verify that the table updates to reflect the date range.
7. Change **Sort By** to `Duration` and **Order** to `Descending`. Click **Apply Filters**.
8. Verify that the events with the longest `Duration (s)` appear at the top of the list.
9. Click **Clear** to reset all filters.

## 4. Authentication Verification
1. Click **Logout** from the navbar.
2. Attempt to directly access `http://localhost:8000/dashboard` or `http://localhost:8000/history`.
3. Verify that you are redirected to the Login page.
