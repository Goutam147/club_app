# Club Application Project Plan

This document serves as the central planning hub for the Club Application. We will update and refine this plan as we discuss and finalize requirements, features, and technology choices.

---

## 1. Project Overview & Core Goals
The main goal of this application is **Club Management** and **Transaction Management**.
- **Timezone:** `Asia/Kolkata`
- **File Storage:** All uploaded files and images must be stored directly in the `public/` directory (e.g., `public/uploads/`), not in the `storage/` directory.
- **Maintenance Feature:** Complete site lockdown when activated (no public or admin routes will be accessible). This will be toggleable using custom Artisan commands (e.g., `php artisan site:down` and `php artisan site:up`) so that administrators can disable maintenance mode.

---

## 2. Target Audience & User Roles
We will use **Spatie Laravel-Permission** to handle authorization. The roles will be:
- **TH (Technical Head / Super Admin):** Full system developer & database manager access, handles maintenance mode and core configurations.
- **President:** Executive control, approves transactions, manages overall club settings, and acts as a key club administrator.
- **Secretary:** Manages events, notices, gallery uploads, and member details.
- **Member:** Can log in (if active), view notices, view gallery, update their profile (including profile picture), and upload transaction receipts (credits/debits) for admin approval.
- **Guests/Anonymous Users:** Can access registration and login pages (unless maintenance mode is active).

---

## 3. Database Schema Design

*Note: As per requirements, all tables include `created_at` and `updated_at` timestamps. All fields (except primary keys) are defined as nullable and default to `null`.*

### 3.1. Users Table (`users`)
Used for members and administrators. Contains soft delete capabilities.
- `id` (Primary Key, Auto-increment)
- `name` (String, Nullable, Default: Null)
- `email` (String, Unique, Nullable, Default: Null)
- `phone` (String, Unique, Nullable, Default: Null)
- `password` (String, Hashed, Nullable, Default: Null)
- `profile` (String, Nullable, Default: Null - Path to uploaded profile picture in `public/uploads/profiles/`)
- `status` (Enum: `pending`, `active`, `inactive`, Nullable, Default: `pending`)
- `created_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)
- `deleted_at` (Timestamp, Nullable, Default: Null - Soft Delete)

### 3.2. Transactions Table (`transactions`)
For tracking club payments, donations, dues, and cash/bank credits or debits.
- `id` (Primary Key, Auto-increment)
- `transaction_id` (String, Unique system-generated ID, formatted as `DDMMYY###` (e.g., `030626001`), Nullable, Default: Null)
- `user_id` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `amount` (Decimal `10,2`, Nullable, Default: Null)
- `remark` (Text, Nullable, Default: Null)
- `document_url` (String, Nullable, Default: Null - Path to receipt in `public/uploads/transactions/`)
- `method` (Enum: `cash`, `bank`, Nullable, Default: Null)
- `type` (Enum: `credit`, `debit`, Nullable, Default: Null)
- `status` (Enum: `pending`, `approved`, `rejected`, Nullable, Default: `pending`)
- `approved_at` (Timestamp, Nullable, Default: Null)
- `approved_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `rejected_at` (Timestamp, Nullable, Default: Null)
- `rejected_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)

### 3.3. Notices Table (`notices`)
For bulletins and announcements. Uses soft deletes.
- `id` (Primary Key, Auto-increment)
- `title` (String, Nullable, Default: Null)
- `description` (Text, Nullable, Default: Null)
- `note` (Text, Nullable, Default: Null)
- `start_at` (Timestamp, Nullable, Default: Null)
- `expiry_at` (Timestamp, Nullable, Default: Null)
- `status` (Enum: `active`, `inactive`, Nullable, Default: `active`)
- `created_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)
- `deleted_at` (Timestamp, Nullable, Default: Null - Soft Delete)

### 3.4. Events Table (`events`)
For club activities and meetings.
- `id` (Primary Key, Auto-increment)
- `title` (String, Nullable, Default: Null)
- `description` (Text, Nullable, Default: Null)
- `start_date` (DateTime, Nullable, Default: Null)
- `end_date` (DateTime, Nullable, Default: Null)
- `manager_id` (Foreign Key -> `users.id`, manager user, Nullable, Default: Null)
- `status` (Enum: `active`, `inactive`, Nullable, Default: `active`)
- `created_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)

### 3.5. Gallery Table (`galleries`)
For event photos and documents.
- `id` (Primary Key, Auto-increment)
- `title` (String, Nullable, Default: Null)
- `event_id` (Foreign Key -> `events.id`, Nullable, Default: Null)
- `doc_url` (String, Nullable, Default: Null - path of upload inside `public/uploads/gallery/`)
- `description` (Text, Nullable, Default: Null)
- `created_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)

### 3.6. Club Master Table (`clubmaster`)
Stores core organization details. Confined to exactly one row.
- `id` (Primary Key, Auto-increment)
- `name` (String, Nullable, Default: 'Bhimchak Sunrise Club')
- `logo` (String, Nullable, Default: 'bsc_logo.jpeg' - Path to uploaded logo in `public/uploads/logo/`)
- `address` (Text, Nullable, Default: Null)
- `estd` (String, Nullable, Default: Null - Year/Date established)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)

### 3.7. Additional Required Tables

#### System Settings Table (`settings`)
Allows tracking configuration key-value pairs (like maintenance status, system maintenance message, global contact phone, email, etc.).
- `id` (Primary Key, Auto-increment)
- `key` (String, Unique, Nullable, Default: Null)
- `value` (Text, Nullable, Default: Null)
- `updated_by` (Foreign Key -> `users.id`, Nullable, Default: Null)
- `created_at` (Timestamp, Nullable, Default: Null)
- `updated_at` (Timestamp, Nullable, Default: Null)

---

## 4. Application Views & User Flows

### 4.1. Authentication Flow
- **Registration Page:** Anyone can register with Name, Email, Phone, Password, and optional Profile Picture. Newly registered users are assigned a status of `pending` by default.
- **Login Page:** Users can log in using either their **Email** or **Phone**. The system will verify that the password matches and that the user's status is `active`. If status is `pending` or `inactive`, login is denied with an appropriate error message.

### 4.2. Core Pages & Views
- **User Directory Page:** Shows a clean tabular list of all users, their profiles, roles/positions, contact information (email/phone), and active/pending status.
- **Transactions Management:**
  - **Create Transaction Page:** Form for members/admins to record credits/debits, upload payment documents (saved directly under `public/uploads/transactions/`), select the method (cash/bank), amount, and remark.
  - **Transactions List Page:** Tabular view of transaction history showing ID, transaction_id, user, amount, status (pending/approved/rejected), type, method, document link, and action buttons for admins to approve/reject.
- **Roles & Permissions Management Page:** An admin-only interface to assign/revoke Spatie roles (TH, President, Secretary, Member) and permissions for each user.
- **Notices Board:** Page to view active notices and for admins to manage announcements.
- **Events List:** View scheduled events, start/end dates, managers, and status.
- **Gallery Page:** Visual grid of event-related photos and documents.

---

## 5. Technical Architecture & Tech Stack
- **Backend Framework:** Laravel 12 (PHP 8.2 on XAMPP)
- **Frontend / View Layer:** Laravel Blade Templates
- **Styling Method:** Tailwind CSS (configured via Laravel Vite) with theme CSS variables for easy updates.
- **Database:** MySQL (hosted locally via XAMPP)
- **Authentication:** Laravel Breeze (Blade/Tailwind stack, customized to allow email OR phone login, verifying that `status == 'active'`).
- **Roles & Permissions:** Spatie Laravel-Permission package (`spatie/laravel-permission`).

### 5.1. Theme Customization
To support easy customization, the theme colors will be defined as CSS variables in `resources/css/app.css` (using hues of blue: dark blue, sky blue, light blue, etc.) and mapped in `tailwind.config.js`:
- `--color-primary` (Theme dark blue)
- `--color-secondary` (Theme blue)
- `--color-accent` (Theme sky blue)
- `--color-light` (Theme light blue / backgrounds)

---

## 6. Next Steps & Action Plan
1. **Initialize Laravel 12 Project:** Install Laravel 12 in the current directory.
2. **Setup Database:** Configure MySQL database in XAMPP and update the `.env` file.
3. **Install Starter Kit & CSS:** Install Laravel Breeze (with Blade & Tailwind stack).
4. **Install Spatie Laravel-Permission:** Configure roles/permissions tables, models, and seeds.
5. **Implement Database Migrations & Models:** Create the tables and relations as detailed in section 3.
6. **Implement Maintenance Mode Middleware:** Build custom middleware to check database settings or application status and redirect users to a maintenance page if active.
7. **Begin Core Feature Development:** Develop the user approval flow, transaction modules, events list, notices bulletin, and photo gallery.

---

## 7. Open Discussion Questions
- **User Approval Flow:** When a user registers, they are `pending` by default. Should they get a notification email, or should the admin just see them in a list of "Pending Users" in the dashboard to click Approve?



