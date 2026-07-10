# Phase 3 — Manual Testing Guide

**Purpose:** Steps you can perform yourself to verify Phase 3 is complete and correct.

---

## Prerequisites

- XAMPP running (Apache + MySQL)
- Terminal open at `d:\Academic Projects\IOT V2\web\`
- Serve the application by running:
  ```powershell
  php artisan serve
  ```
- Open your browser to `http://localhost:8000`

---

## Test 1 — Redirect to Login

1. Go to `http://localhost:8000`
2. **Expected:** You should be automatically redirected to `http://localhost:8000/login`
3. The page should display the clean, custom-styled "Sign In" form.

---

## Test 2 — Wrong Password Rejection

1. On the login page, enter:
   - Email: `doctor@example.com`
   - Password: `wrongpassword`
2. Click **Sign In**.
3. **Expected:** The page reloads, and you see a red error message: "The provided credentials do not match our records."

---

## Test 3 — Protected Routes Require Login

1. Try to manually navigate to `http://localhost:8000/dashboard` in the browser URL bar.
2. **Expected:** You should be redirected back to `http://localhost:8000/login`.

---

## Test 4 — Doctor Login Works

1. On the login page, enter:
   - Email: `doctor@example.com`
   - Password: `password`
2. Click **Sign In**.
3. **Expected:** You are redirected to `http://localhost:8000/dashboard`.
4. The page should display:
   - **✅ Authentication Successful**
   - Name: Demo Doctor
   - Email: doctor@example.com
   - Role: Doctor

---

## Test 5 — Session Persists

1. After logging in successfully in Test 4, reload the dashboard page.
2. **Expected:** You stay on the dashboard, and you are not logged out.
3. Open a new tab and go to `http://localhost:8000`.
4. **Expected:** You are automatically redirected to `http://localhost:8000/dashboard`.

---

## Test 6 — Logout Works

1. On the dashboard, click the red **Logout** button in the top right.
2. **Expected:** You are redirected back to the login page.
3. Press the back button in your browser.
4. **Expected:** The dashboard page should not load, or if it does due to browser cache, refreshing it will redirect you to login.

---

## Test 7 — Caregiver Login Works

1. On the login page, enter:
   - Email: `caregiver@example.com`
   - Password: `password`
2. Click **Sign In**.
3. **Expected:** You are redirected to `http://localhost:8000/dashboard`.
4. The page should display:
   - Name: Demo Caregiver
   - Email: caregiver@example.com
   - Role: Caregiver

---

## Test 8 — Automated Verification Script

1. Stop `php artisan serve` if running in your main terminal.
2. Run the verification script:
   ```powershell
   php verify_phase3.php
   ```
3. **Expected:** `Results: 15 passed, 0 failed`

---

## ✅ Phase 3 Checklist

| Check | Expected |
|---|---|
| Unauthenticated users redirected | ✅ |
| Clean custom login page | ✅ |
| Login rejection for wrong password | ✅ |
| Doctor login works | ✅ |
| Caregiver login works | ✅ |
| Temporary dashboard displays user info | ✅ |
| Logout works and invalidates session | ✅ |
| Automated script: 15/15 passed | ✅ |
