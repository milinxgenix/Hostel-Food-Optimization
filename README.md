# Hostel Food Optimization (Hostel Mess Preferences)

A small PHP + MySQL web app to collect student mess preferences and dietary requirements, and provide admin analytics to reduce food waste. Built for easy local deployment (XAMPP / Apache + MySQL). Includes a student form, admin login/register, and Chart.js-based analytics.

---

## Table of Contents

- Project overview
- Features
- Tech stack
- Quick start (local)
- Database schema (example SQL)
- Pages & files
- Data flow & normalization
- Development notes
- Troubleshooting
- Contributing
- License

---

## Project overview

Hostel Food Optimization captures student mess choices (Veg / Non-Veg / Special / All) and dietary info, centralizes the data in a MySQL table, and visualizes trends for admins to make better meal planning decisions and reduce leftover food.

This repo contains:
- Student-facing form (form.php)
- Submission handler (submit_form.php)
- Admin auth and dashboard (login.php, register.php, dashboard.php, logout.php)
- Data analysis / charts (data_analysis.php) using Chart.js
- Landing page and UI polish (index.php)

---

## Key Features

- Simple student form to capture preferences and special diets
- Admin registration and login
- Admin dashboard with mini charts and a link to a full data-analysis page
- Data-analysis page with multiple Chart.js visualizations (pie, bar, doughnut, histogram)
- Dual-layer tsParticles background for a modern UI touch
- Demo seeder to populate representative data for charts
- Normalization performed at query time (canonical categories: Veg / Non-Veg / Special / All)
- Safe seeder with backup and optional truncation flow

---

## Tech stack

- PHP (procedural)
- MySQL (local via XAMPP)
- Tailwind CSS (via CDN)
- Chart.js (via CDN)
- tsParticles (via CDN)
- No frameworks — single-file pages for simplicity

---

## Quick start (local using XAMPP)

1. Place the project folder inside your XAMPP `htdocs` directory (e.g., ProjectSPM).
2. Start Apache and MySQL in XAMPP.
3. Create the database (if not already present) using phpMyAdmin or the MySQL CLI:
   - Example CLI:
     /Applications/XAMPP/xamppfiles/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS hostel_food_waste;"
4. Create the tables (example SQL below) or import from your existing schema.
5. Open in the browser:
   - Landing page: http://localhost/ProjectSPM/index.php
   - Student form: http://localhost/ProjectSPM/form.php
   - Admin login: http://localhost/ProjectSPM/login.php
6. To populate demo data run the seeder:
   - From CLI (recommended):
     /Applications/XAMPP/xamppfiles/bin/php seed_data.php
   - Or in the browser (confirm safeguard):
     http://localhost/ProjectSPM/seed_data.php?confirm=1

---

## Database schema (example)

Below is a recommended example SQL schema for the primary table `mess_preferences`. Adjust to your needs.

CREATE TABLE mess_preferences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255) NOT NULL,
  registration_number VARCHAR(64),
  college_email VARCHAR(255),
  phone VARCHAR(32),
  mess_name VARCHAR(64),       -- stores canonical 'Veg'/'Non-Veg'/'Special' or legacy values
  hostel_block VARCHAR(16),    -- single letter (A..T) preferred
  religious_event VARCHAR(128),
  special_diet VARCHAR(255),
  diet_days INT DEFAULT 0,
  no_eat_days INT DEFAULT 0,
  switch_mess VARCHAR(8),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Admin table example (simple):

CREATE TABLE admin_register (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE,
  password_hash VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

Add a sample admin (replace <hash> with password_hash via PHP's password_hash):

INSERT INTO admin_register (username, password_hash) VALUES ('admin', '<hash>');

---

## Seeder (demo data) — seed_data.php

- Purpose: Insert demo rows so Chart.js visualizations have meaningful distributions.
- Behavior:
  - Runs only if called from CLI or `?confirm=1` in browser.
  - Detects if `created_at` column exists and adapts inserts.
  - Generates an initial batch (configurable) and ensures minimum rows per mess type and per block.
  - You can truncate + reseed safely; a backup of `mess_preferences` is created before truncation (if you choose that flow).
- Example run:
  - CLI: /Applications/XAMPP/xamppfiles/bin/php seed_data.php
  - Browser: http://localhost/ProjectSPM/seed_data.php?confirm=1

---

## Pages & where to look

- index.php — Landing page and quick access buttons (Login, Register, Dashboard, Student Form)
- form.php — Student form (POSTs to itself; optional centralization to submit_form.php)
- submit_form.php — Handles student form submission and inserts into database (accepts `mess_type` mapped to DB `mess_name`)
- login.php / register.php — Admin auth (stored in `admin_register`)
- dashboard.php — Admin quick dashboard with mini charts and link to data_analysis.php
- data_analysis.php — Full analytics page with Chart.js charts and filters (mess_type, hostel_block)
- logout.php — Destroy session and redirect to login

---

## Data flow & normalization

- Students submit preferences via form.php → rows inserted into `mess_preferences`.
- data_analysis.php / dashboard.php aggregate data for visualization.
- Code applies simple normalization heuristics in the PHP aggregations to map free-text mess names into canonical types (Veg / Non-Veg / Special).
- If you prefer stronger guarantees, the README suggests adding canonical columns (`canonical_mess_type`, `canonical_block`) and populating them via a script or running normalization SQL updates.

---

## Recommended maintenance tasks

- Backup `mess_preferences` before doing destructive operations:
  - CREATE TABLE mess_preferences_backup AS SELECT * FROM mess_preferences;
- If you want to normalize existing legacy values in-place, create a backup first and run controlled UPDATE statements (examples can be provided).
- Consider extracting repeated JS (particles and Chart.js config) to shared assets for maintainability.

---

## Troubleshooting

- If pages are blank / show PHP errors:
  - Enable display of errors in `php.ini` or add these lines near top of PHP pages during development:
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
- If `php` is not found in your terminal, use XAMPP's PHP binary:
  - /Applications/XAMPP/xamppfiles/bin/php -v
- If Chart.js or tsParticles effects don't show, ensure your browser can fetch CDNs; check DevTools Network / Console.

---

## Contributing

- Keep the project procedural and simple; follow existing file structure.
- When adding features:
  - Add new PHP files in root and link from index.php.
  - Add migrations or SQL scripts for any schema changes.
  - Keep seeded/demo data generation in seed_data.php and make changes configurable.

---

## License

If you'd like, I can:
- Create `README.md` in the repo now.
- Add an example SQL migration script and explicit normalization SQL.
- Add a non-destructive “canonicalization” script that creates `canonical_mess_type` and `canonical_block` columns and populates them.

Which of these would you like next?
