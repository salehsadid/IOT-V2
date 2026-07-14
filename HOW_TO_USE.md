# How to Use & Simulate the Parkinson's Monitor System

This document provides a complete, step-by-step guideline on how to set up, run, and simulate this project from scratch. Even if you have no prior knowledge of the project, following these steps will get the system running.

---

## 1. Hardware Setup & Firmware Upload

### Requirements
- **ESP32 Development Board**
- **2x MPU6050 Sensors**
- **OLED Display (I2C 128x64)**
- **Active Buzzer**
- **Jumper Wires**

### Step 1.1: Circuit Connections
Wire the components according to the **Connections** table in the main [README.md](README.md). Ensure the Leg MPU6050 has its `AD0` pin connected to `3.3V` so its I2C address becomes `0x69` (Hand MPU remains at `0x68`).

### Step 2.2: Configure Environment (secrets.h)
Before uploading the code to the ESP32, you must configure your local network and server IP.
1. Open the `firmware/firmware.ino` project in the **Arduino IDE**.
2. Open the `secrets.h` file.
3. Update your Wi-Fi credentials and your computer's IP address (where Laravel will run):
   ```cpp
   #define SECRET_WIFI_SSID "Your_WiFi_Name"
   #define SECRET_WIFI_PASS "Your_WiFi_Password"
   #define SECRET_SERVER_URL "http://192.168.x.x:8000" // Replace with your PC's IP
   ```

### Step 2.3: Upload Firmware
1. Connect the ESP32 to your PC via USB.
2. Select the correct COM port and board (`DOIT ESP32 DEVKIT V1`) in Arduino IDE.
3. Click **Upload**. Wait for the "Done uploading" message.

---

## 2. Web Server Setup (Laravel)

The web dashboard is built with Laravel and requires PHP and MySQL.

### Step 2.1: Requirements
- **XAMPP** (or any local server with PHP 8.2+ and MySQL)
- **Composer** (PHP dependency manager)

### Step 2.2: Database Setup
1. Open XAMPP and start **Apache** and **MySQL**.
2. Open phpMyAdmin (`http://localhost/phpmyadmin`) and create a new database named `parkinson_monitor`.

### Step 2.3: Laravel Configuration
1. Open a terminal in the `web/` folder.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate the app key:
   ```bash
   php artisan key:generate
   ```
5. Open `.env` and verify the database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=parkinson_monitor
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Step 2.4: Database Migration & Seeding
Run the following command to create the tables and add a default patient (John Doe):
```bash
php artisan migrate --seed
```

### Step 2.5: Start the Server
Start the Laravel development server. **Important:** Bind it to `0.0.0.0` so the ESP32 can communicate with it over the local network!
```bash
php artisan serve --host 0.0.0.0 --port 8000
```
You can now access the dashboard at `http://localhost:8000/login`.
**Login Credentials:**
- **Email:** `doctor@example.com`
- **Password:** `password`

---

## 3. Telegram Bot Setup (Optional but Recommended)

To receive real-time alerts on your phone:
1. Open Telegram and search for `@BotFather`.
2. Send `/newbot` and follow the instructions to get an **HTTP API Token**.
3. Put the token in the `web/.env` file:
   ```env
   TELEGRAM_BOT_TOKEN="your_token_here"
   ```
4. Start a chat with your new bot in Telegram and send a message (e.g., "Hello").
5. Run this PowerShell command to find your Chat ID:
   ```powershell
   Invoke-RestMethod -Uri "https://api.telegram.org/bot<YOUR_TOKEN_HERE>/getUpdates" | ConvertTo-Json -Depth 10
   ```
6. Put the `id` from the `chat` object into your `.env`:
   ```env
   TELEGRAM_CHAT_ID="your_chat_id"
   ```
7. Clear Laravel cache:
   ```bash
   php artisan config:clear
   ```

---

## 4. How to Simulate the Project

Since this is a prototype, you will physically shake the sensors to simulate medical events.

### Simulating a Tremor
1. Ensure the Leg MPU is perfectly still (REST state).
2. Rapidly shake the Hand MPU back and forth.
3. Keep shaking for at least **1.5 seconds**.
4. **Observation:**
   - The OLED will display `TREMOR LEVEL X`.
   - The Web Dashboard will turn red and show the tremor level.
   - A Telegram message will be sent to your phone.

### Simulating Freezing of Gait (FOG)
1. Pick up the Leg MPU and move it rhythmically to simulate walking.
2. The Dashboard should show `WALKING`.
3. Suddenly start trembling/shaking the Leg MPU (simulating a stuttering gait) while still moving slightly. Maintain this for **2 seconds**.
4. **Observation:**
   - The OLED will display `FOG DETECTED`.
   - The Buzzer will start a rhythmic medical alarm pattern.
   - The Web Dashboard will turn red and indicate FOG.
   - A Telegram message will be sent.
5. **Stopping the Alarm:**
   - From the Web Dashboard, click the **Stop Buzzer** button to remotely silence the buzzer while keeping the detection active.

---

## 5. Troubleshooting & Common Issues

| Issue | Cause | Solution |
|---|---|---|
| **OLED is blank / ESP32 stuck in boot loop** | I2C Wiring issue | Check the SDA (21) and SCL (22) pins. Ensure both MPUs and OLED are connected properly. |
| **Only one MPU is detected** | AD0 pin not set | Ensure the Leg MPU's AD0 pin is wired to 3.3V (Address `0x69`). |
| **Web dashboard shows OFFLINE** | ESP32 can't reach server | Check if `SECRET_SERVER_URL` in `secrets.h` matches your PC's IP. Ensure firewall allows port `8000`. |
| **Tremor not detecting** | Moving too briefly | You must shake the Hand MPU continuously for > 1.5s while the Leg MPU is completely still. |
| **Telegram alerts not sending** | Invalid Chat ID | Ensure you sent a message to the bot first before fetching the Chat ID. Run `php artisan config:clear`. |
| **Buzzer stays on permanently** | Transistor/Wiring issue | Ensure the buzzer is connected to Pin 25. If using a passive buzzer, it requires a PWM signal (code uses active buzzer logic). |
