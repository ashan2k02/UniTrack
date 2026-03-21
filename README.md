# UniTrack - Phase 3 (PHP + MySQL)

**UniTrack** is a smart academic management web application designed for university students to track tasks, timetables, and calculate GPA all in one place. Phase 3 implements full backend functionality with user authentication, database persistence, and responsive design.

## ✨ Phase 3 Features

### Authentication & Security
- User registration with email and password validation
- Secure login with PHP sessions
- Password hashing using `password_hash()` (bcrypt)
- Logout functionality with session cleanup
- Account settings page with profile update and password change
- Session-aware navbar showing username dropdown for logged-in users

### Database Features
- Contact form with message storage in MySQL
- Student task management with CRUD operations (My DB Tasks)
- GPA subject tracking with weighted GPA calculation
- Lecture timetable storage and retrieval
- User account management (name, email, password)

### UI/UX Enhancements
- Unified navbar (Home, Dashboard, About, Contact) across all pages
- Consistent footer with navigation links and developer credits
- Session-aware dropdowns (Account Settings, Logout)
- Glass-morphism design with dark theme
- Responsive layout (mobile, tablet, desktop)
- Real-time GPA calculation

### Frontend Modules (Browser-based)
- Task Manager - Create, filter, and manage tasks locally
- Lecture Timetable - Weekly schedule with today highlight
- GPA Calculator - Calculate weighted GPA with complete grade scale (4.0-0.0)

---

## 📁 Project Structure

```
UniTrack/
├── css/
│   ├── style.css           (Global styles)
│   ├── dashboard.css       (Dashboard-specific)
│   ├── login.css           (Auth pages)
│   ├── register.css
│   ├── about.css
│   └── contact.css
├── js/
│   ├── app.js              (Global utilities)
│   ├── dashboard.js        (Dashboard interactions)
│   ├── login.js            (Auth validation)
│   └── register.js
├── images/
│   └── logo.png
├── includes/
│   ├── db.php              (Database connection)
│   └── functions.php       (Reusable PHP functions)
├── auth/
│   ├── login.php           (Login handler)
│   ├── register.php        (Registration handler)
│   └── logout.php          (Session cleanup)
├── api/
│   ├── gpa.php             (GPA CRUD operations)
│   ├── tasks.php           (Task CRUD operations)
│   ├── lectures.php        (Lecture CRUD operations)
├── index.php               (Home page)
├── dashboard.php           (Main dashboard)
├── login.html/php
├── register.html/php
├── contact.php             (Contact form)
├── account.php             (User account settings)
├── my_tasks.php            (DB task management)
├── about.php               (About page)
├── database.sql            (Database schema)
└── README.md               (This file)
```

---

## 🔧 Requirements

- **XAMPP** (Apache + MySQL + PHP)
- **PHP 8.0 or higher**
- **MySQL 5.7 or higher**
- **Modern web browser** (Chrome, Firefox, Safari, Edge)

---

## 📋 Setup Instructions

### Step 1: Copy Project to XAMPP

Copy the `UniTrack` folder to your XAMPP htdocs directory:

**macOS:**
```bash
cp -r UniTrack /Applications/XAMPP/xamppfiles/htdocs/
```

**Windows:**
```
Copy folder to: C:\xampp\htdocs\UniTrack
```

**Linux:**
```bash
cp -r UniTrack /opt/lampp/htdocs/
```

### Step 2: Start XAMPP Services

1. Open XAMPP Control Panel
2. Click **Start** for:
   - Apache
   - MySQL

### Step 3: Import Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click **Import** tab
3. Select `database.sql` from the UniTrack folder
4. Click **Go** to import

The database will create:
- `users` table (registration/login)
- `messages` table (contact form)
- `student_tasks` table (My DB Tasks)
- `lectures` table (timetable)
- `gpa_subjects` table (GPA calculator)

### Step 4: Verify Database Config

Open `includes/db.php` and confirm settings:

```php
'host' => 'localhost',
'database' => 'unitrack_db',
'user' => 'root',
'password' => ''  // Empty default in XAMPP
```

### Step 5: Access the Application

Open your browser and go to:
```
http://localhost/UniTrack/index.php
```

---

## 👤 User Workflows

### Registration & Login

1. Click **Get Started** on home page
2. Enter name, email, password (min 8 characters)
3. Click **Create Account**
4. Log in with email and password
5. Dashboard opens with session active

### Dashboard Features

**Tasks Module:**
- Add tasks with title, deadline, priority
- Mark as done with checkboxes
- Filter: All / Pending / Done
- Local browser storage

**Timetable Module:**
- Add lectures: Day, Subject, Time (Start-End)
- Weekly view with today highlighted
- Clear all to reset

**GPA Calculator:**
- Add subjects with credits and grade
- Grade scale: A+ (4.0) to E (0.0)
- Auto-calculates weighted GPA
- Stores in database (My DB Tasks)

### Account Settings

1. Click username dropdown in navbar
2. Select **Account Settings**
3. Update profile (name, email)
4. Change password with current password verification

### Contact Form

1. Navigate to **Contact** page
2. Fill name, email, message
3. Submit - saves to database

### My DB Tasks

1. Click **My DB Tasks** in sidebar
2. Add tasks with title, deadline, priority
3. View all saved tasks from database
4. Toggle complete status or delete

### My Tasks

1. Click **My DB Tasks** in sidebar
2. View saved tasks with status and priority

---

## 🗄️ Database Schema

### users
```sql
id (PK) | username | email (UNIQUE) | password (hashed) | created_at
```

### messages
```sql
id (PK) | name | email | message | created_at
```

### student_tasks
```sql
id (PK) | user_id (FK) | title | deadline | priority | is_done | created_at
```

### lectures
```sql
id (PK) | user_id (FK) | day | subject | start_time | end_time | created_at
```

### gpa_subjects
```sql
id (PK) | user_id (FK) | subject_name | credits | grade_point | created_at
```

---

## 🔐 Security Features

- **Passwords:** Hashed with `password_hash()` (PASSWORD_DEFAULT / bcrypt)
- **SQL Injection:** Protected with prepared statements (PDO)
- **Session Management:** PHP sessions with timeout
- **Password Verification:** Double-layer check (app + DB WHERE clause)
- **Input Sanitization:** `sanitize_input()` function for all user inputs
- **Email Validation:** `filter_var()` with FILTER_VALIDATE_EMAIL

### Password Requirements
- Minimum 8 characters
- Must match confirmation field
- Original password verified before change

---

## 🎨 Grade Scale Reference

| Grade | Points |
|-------|--------|
| A+    | 4.0    |
| A     | 4.0    |
| A-    | 3.7    |
| B+    | 3.3    |
| B     | 3.0    |
| B-    | 2.7    |
| C+    | 2.3    |
| C     | 2.0    |
| C-    | 1.7    |
| D+    | 1.3    |
| D     | 1.0    |
| E     | 0.0    |

---

## 🐛 Troubleshooting

### "Connection Error" on Dashboard
- Verify Apache & MySQL are running
- Check `includes/db.php` connection settings
- Confirm database imported successfully in phpMyAdmin

### "Session Missing" Error
- Clear browser cookies/cache
- Log out and log back in
- Check `auth/login.php` is setting session correctly

### "Cannot Add Grade" / "Invalid Grade Point"
- Refresh page after login
- Select grade from dropdown (not manually typed)
- Use one of the 12 supported grades (A+ through E)

### "No Tables Found" in phpMyAdmin
- Re-import `database.sql`
- Verify database name is `unitrack_db`
- Check MySQL is running

### JavaScript Not Working (Tasks/Timetable/GPA)
- Enable JavaScript in browser
- Check browser console for errors (F12 > Console)
- Clear browser cache and refresh
- Verify `js/dashboard.js` is not modified

---

## 📝 Notes

- **Frontend Modules** (Tasks, Timetable, GPA) use browser `localStorage` for quick access. They also sync with database when logged in (`my_tasks.php`, API endpoints).
- **Responsive Design:** Mobile-first approach with hamburger menu on small screens
- **Dark Theme:** Uses CSS variables for consistent styling
- **Cross-browser:** Tested on Chrome, Firefox, Safari, Edge

---

## 📧 Support

For issues or questions, review:
- `includes/functions.php` for helper functions
- `api/*.php` for API logic
- `js/dashboard.js` for frontend calculations
- Browser DevTools (F12) for client-side errors

---

