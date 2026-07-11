# Phase 4 — Manual Testing Guide

**Purpose:** Steps you can perform yourself to verify Phase 4 (Dashboard UI) is complete and correct.

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

## Test 1 — Login Still Works

1. Go to `http://localhost:8000/login`
2. Enter:
   - Email: `doctor@example.com`
   - Password: `password`
3. Click **Sign In**.
4. **Expected:** You are redirected to `http://localhost:8000/dashboard`.

---

## Test 2 — Dashboard Layout Renders Correctly

1. Look at the `/dashboard` page.
2. **Expected:** You should see a clean, professional UI with a dark sidebar on the left and a top white navbar.
3. Verify that there is **NO** broken CSS and the layout looks modern.
4. Verify the top navbar displays "Demo Doctor" and "Doctor".

---

## Test 3 — Sidebar & Navigation

1. Check the left sidebar.
2. **Expected:** The "Dashboard" link should be highlighted (active state).
3. Try clicking "Patients" or "Devices".
4. **Expected:** The URL ends with `#`, and the page does not change. (Functionality will be added in later phases).

---

## Test 4 — Dashboard Content Placholders

1. Look at the main content area.
2. **Expected:** You should see four statistics cards:
   - Total Patients (Value: 0)
   - Total Devices (Value: 0)
   - Today's Tremor Events (Value: 0)
   - Today's FOG Events (Value: 0)
3. Scroll down to the "Recent Activity" section.
4. **Expected:** A styled table with 3 dummy rows showing fake events, patients, and colorful badges (e.g., FOG Detected, Severe Tremor).

---

## Test 5 — Responsiveness (Mobile View)

1. Resize your browser window to be very narrow (less than 768px wide), simulating a mobile phone.
2. **Expected:** 
   - The sidebar should hide completely off the left side of the screen.
   - A hamburger menu icon (☰) should appear in the top-left of the navbar.
3. Click the hamburger menu icon.
4. **Expected:** 
   - The sidebar slides into view.
   - A dark, semi-transparent overlay appears over the main content.
5. Click the dark overlay or the hamburger icon again.
6. **Expected:** The sidebar hides again.

---

## Test 6 — Logout Still Works

1. On the top right of the navbar, click the red **Logout** button.
2. **Expected:** You are successfully logged out and redirected to the `/login` page.

---

## Test 7 — Automated Verification Script

1. Stop `php artisan serve` if running in your main terminal.
2. Run the verification script:
   ```powershell
   php verify_phase4.php
   ```
3. **Expected:** `Results: 12 passed, 0 failed`

---

## ✅ Phase 4 Checklist

| Check | Expected |
|---|---|
| Login still works | ✅ |
| Custom CSS applied (no Bootstrap) | ✅ |
| Sidebar renders with active state | ✅ |
| Navbar renders user info and logout | ✅ |
| Dashboard cards show 0 (placeholders) | ✅ |
| Activity table shows dummy data | ✅ |
| Mobile responsiveness works (sidebar toggle) | ✅ |
| Logout still works | ✅ |
| Automated script: 12/12 passed | ✅ |
