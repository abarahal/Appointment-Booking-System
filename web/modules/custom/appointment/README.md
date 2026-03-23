# Appointment Booking System

A custom Drupal 10 module that lets visitors (anonymous or logged-in) book, modify, cancel and look up appointments through a **6-step wizard form**.

---

## What does this module do?

It adds a multi-page booking form to your Drupal site at `/appointment/book`.
A visitor picks an agency, an appointment type, an adviser, a date/time, fills in personal info, reviews a summary, and confirms. The appointment is saved in the database and a confirmation email is sent.

---

## Module structure

```
appointment/
├── appointment.info.yml          → Tells Drupal this is a module (name, dependencies)
├── appointment.install           → Runs once when the module is installed (creates sample data)
├── appointment.module            → Hook implementations (e.g. hook_mail for emails)
├── appointment.routing.yml       → Defines all the URLs/pages the module provides
├── appointment.services.yml      → Registers reusable services (email sender, manager)
├── README.md                     → This file
└── src/
    ├── Controller/
    │   └── AppointmentController.php   → Handles confirmation page & "my appointments" list
    ├── Entity/
    │   ├── AgencyEntity.php            → Custom entity: an agency (name, address, advisers)
    │   └── AppointmentEntity.php       → Custom entity: a booked appointment
    ├── Form/
    │   ├── AppointmentBookForm.php     → The 6-step wizard booking/edit form
    │   ├── AppointmentCancelForm.php   → Cancellation form
    │   └── AppointmentLookupForm.php   → Lookup form (find appointments by email/token)
    └── Service/
        ├── AppointmentManagerService.php → Business logic (availability checks, etc.)
        └── EmailService.php              → Sends confirmation/modification/cancellation emails
```

### Key concepts for beginners

| Drupal concept    | File                      | What it means                                                                                                                            |
| ----------------- | ------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| **Module info**   | `appointment.info.yml`    | A YAML file that tells Drupal the module name, version, and which core modules it depends on (user, taxonomy, datetime, options).        |
| **Routing**       | `appointment.routing.yml` | Maps URLs to PHP code. For example, `/appointment/book` → `AppointmentBookForm`.                                                         |
| **Entity**        | `src/Entity/*.php`        | A custom data type stored in its own database table. Like a "content type" but defined in code. We have two: `agency` and `appointment`. |
| **Form**          | `src/Form/*.php`          | A PHP class that builds an HTML form, validates input, and processes the submission.                                                     |
| **Controller**    | `src/Controller/*.php`    | A PHP class that returns a page (HTML). Used for pages that are not forms (e.g. the confirmation page).                                  |
| **Service**       | `src/Service/*.php`       | A reusable PHP class registered in `appointment.services.yml`. Other code can use it without creating it manually.                       |
| **Install hooks** | `appointment.install`     | `hook_install()` runs once when you enable the module. `hook_update_N()` functions run when you do `drush updb` (database updates).      |

---

## Pages / Routes

| URL                              | What it does                         |
| -------------------------------- | ------------------------------------ |
| `/appointment/book`              | Start the booking wizard (step 1)    |
| `/appointment/book/{step}`       | Jump to a specific wizard step (1–6) |
| `/appointment/{id}/edit`         | Edit an existing appointment         |
| `/appointment/{id}/edit/{step}`  | Edit at a specific step              |
| `/appointment/{id}/cancel`       | Cancel an appointment                |
| `/appointment/{id}/confirmation` | Confirmation page after booking      |
| `/appointment/my`                | List the current user's appointments |
| `/appointment/lookup`            | Find appointments by email or token  |

---

## The 6-step booking wizard

1. **Agency** — Pick an agency from a dropdown
2. **Type** — Pick the appointment type (Consultation, Follow-up, Support)
3. **Adviser** — Pick an adviser who works at the selected agency
4. **Date & Time** — Choose a date and time slot (checks for conflicts)
5. **Personal info** — Enter name, email, phone, notes
6. **Confirmation** — Review a summary and confirm

Data is saved between steps using Drupal's **Private TempStore** (server-side session storage), so nothing is lost if you navigate back and forth.

---

## Database tables

The module creates two custom tables:

- **`agency`** — Stores agencies with fields: name, address, advisers (JSON), status
- **`appointment`** — Stores bookings with fields: agency, adviser, type, start/end time, client info, status, access token

It also uses the core **taxonomy** system for appointment types (vocabulary: `appointment_type`).

---

## Sample data

When installed, the module seeds:

- **3 appointment types**: Consultation, Follow-up, Support
- **2 agencies**:
  - _Central Agency_ (123 Main Street) — advisers: Alice Morgan, David Reed
  - _North Branch_ (77 North Avenue) — advisers: Rina Patel, Leo Grant

---

## Requirements

- Drupal 10
- PHP 8.1+
- Core modules: `user`, `options`, `taxonomy`, `datetime`

---

## Installation

```bash
# Enable the module
drush en appointment -y

# Run database updates (creates tables + seeds data)
drush updb -y

# Clear cache
drush cr
```

If the module was already enabled but tables are missing, `drush updb` will create them automatically (via update hooks 10001 and 10002).

---

## Useful Drush commands

```bash
# Rebuild the cache (do this after any code change)
drush cr

# Run pending database updates
drush updb -y

# View recent log messages (useful for debugging)
drush watchdog:show --count=20

# Check if a table exists in the database
drush sqlq "SHOW TABLES LIKE 'agency';"
```

---

## Troubleshooting

| Problem                                  | Solution                                                                                                               |
| ---------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| **500 error on `/appointment/book`**     | Run `drush updb -y` then `drush cr`. The database tables may be missing.                                               |
| **Empty agency dropdown**                | Run `drush updb -y` — the seed data hook will populate agencies.                                                       |
| **"Class does not exist" on `drush cr`** | Check `appointment.routing.yml` — class paths must use single backslash (`\Drupal\...`), not double (`\\Drupal\\...`). |
| **Changes not showing**                  | Always run `drush cr` after editing PHP or YAML files.                                                                 |
