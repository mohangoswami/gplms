# GPLM School — SaaS Conversion Analysis

> **Purpose:** Comprehensive audit of the existing single-tenant school management system to architect a full multi-tenant SaaS conversion.
> **Date:** 2026-03-23
> **Framework:** Laravel 9 · PHP 8.2 · MySQL · VPS (Apache/Nginx)
> **Note:** App previously ran on AWS Lambda (Bref serverless) — now migrated to VPS. File storage still uses AWS S3.

---

## 1. Project Structure

### Directory Tree (vendor/node_modules excluded)

```
/var/www/gplmschool/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   ├── Api/
│   │   │   ├── Auth/
│   │   │   ├── Fee/
│   │   │   ├── Result/
│   │   │   ├── Student/
│   │   │   └── Teacher/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   ├── migrations/          (70 files)
│   └── seeders/
├── public/
├── resources/
│   ├── js/
│   ├── sass/
│   └── views/
│       ├── admin/
│       ├── analytics/
│       ├── auth/
│       ├── layouts/
│       ├── student/
│       ├── teacher/
│       └── vendor/mail/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
│   └── app/firebase/        (FCM credentials)
├── .env
├── composer.json
├── package.json
├── serverless.yml           (AWS Lambda deployment)
└── android.keystore         (Android app signing key)
```

### Key Config Files

| File | Purpose |
|------|---------|
| `.env` | Production config — APP_URL, DB, AWS, FCM, Razorpay keys |
| `config/auth.php` | Multiple guards: web, api, admin, teacher, cashier |
| `config/database.php` | MySQL + DynamoDB cache |
| `config/filesystems.php` | S3 file storage |
| `config/services.php` | Third-party service credentials |
| `serverless.yml` | AWS Lambda deployment config — **now outdated, app runs on VPS** |
| `composer.json` | PHP dependencies |
| `package.json` | Node/frontend dependencies |

---

## 2. Database Schema

**Total migrations:** 70 files spanning 2014–2026.

### Core User Tables

#### `users` — Students
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| admission_number | string | Login username |
| name | string | |
| fName | string | Father's name |
| mName | string | Mother's name |
| dob | date | |
| address | text | |
| mobile | string | |
| rfid | string | |
| email | string | |
| password | string | Hashed |
| grade | string | Class (e.g., "1", "UKG") |
| section | string | |
| aadhar | string | National ID |
| pen | string | Permanent Education Number |
| apaar | string | Academic Bank of Credits ID |
| house | string | School house |
| caste | string | |
| gender | string | |
| blood_group | string | |
| height | decimal | |
| weight | decimal | |
| vision_left | string | |
| vision_right | string | |
| dental_hygiene | string | |
| category_id | bigint FK → categories | |
| route_id | bigint FK → route_names | Transport route |
| app_permission | boolean | Android app access |
| exam_permission | boolean | Online exam access |
| timestamps | | |

#### `admins` — Admin Users
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string | |
| email | string | |
| password | string | |
| timestamps | | |

#### `teachers` — Teaching Staff
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string | |
| email | string | |
| password | string | |
| class_code0–11 | string | 12 columns for class assignments |
| timestamps | | |

#### `cashiers` — Fee Processing Staff
| Column | Type |
|--------|------|
| id | bigint PK |
| name | string |
| email | string |
| password | string |

#### `device_tokens` — FCM Push Notification Tokens
| Column | Type |
|--------|------|
| id | bigint PK |
| user_id | bigint FK → users |
| token | text |
| platform | string |

---

### Academic Tables

#### `sub_codes` — Class-Subject Mapping & Schedule
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| class | string | |
| subject | string | |
| link_url | string | Video/meeting link |
| start_time | time | |
| end_time | time | |
| Monday–Sunday | string | Daily schedule (7 columns) |

#### `exams` — Exam/Assessment Records
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| class | string | |
| name | string | |
| subject | string | |
| title | string | |
| examUrl | string | Exam resource URL |
| maxMarks | integer | |
| startExam | datetime | |
| endExam | datetime | |
| admin_id | bigint FK → admins | |
| teacher_id | bigint FK → teachers | |
| term_id | bigint FK → terms | |

#### `terms` — Academic Terms
| Column | Type |
|--------|------|
| id | bigint PK |
| term | string |

#### `classworks` — Assignments & Materials
| Column | Type |
|--------|------|
| id | bigint PK |
| class | string |
| subject | string |
| title | string |
| fileUrl | string |
| youtubeLink | string |
| type | string |

#### `student_exams` — Student Exam Submissions
| Column | Type |
|--------|------|
| id | bigint PK |
| titleId | bigint FK → exams |
| student_id | bigint FK → users |
| marks | decimal |
| teacher_id | bigint FK → teachers |
| remark | text |

#### `student_exam_works` — Homework Submissions
| Column | Type |
|--------|------|
| id | bigint PK |
| titleId | bigint FK → classworks |
| class | string |
| subject | string |
| title | string |
| fileUrl | string |
| submittedDone | boolean |

#### `student_attendances` — Daily Attendance (Legacy)
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| date | date |
| status | string |

#### `teacher_attendances` — Teacher Attendance
| Column | Type |
|--------|------|
| id | bigint PK |
| teacher_id | bigint FK → teachers |
| date | date |
| status | string |

#### `attendances` — Unified Attendance (New)
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| teacher_id | bigint FK → teachers |
| class | string |
| date | date |
| status | string |
| marked_by | string |

#### `holidays` — School Calendar
| Column | Type |
|--------|------|
| id | bigint PK |
| title | string |
| start | date |
| end | date |

#### `flash_news` — Announcements
| Column | Type |
|--------|------|
| id | bigint PK |
| news | text |
| timestamps | |

---

### Fee Management Tables

#### `fee_heads` — Fee Categories
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | string | E.g., "Tuition", "Books" |
| accountName | string | Ledger account |
| frequency | string | Monthly/quarterly/annual |
| jan–dec | boolean | 12 columns for applicability |

#### `route_names` — Transport Routes
| Column | Type |
|--------|------|
| id | bigint PK |
| routeName | string |
| accountName | string |
| frequency | string |
| jan–dec | boolean (12 cols) |

#### `fee_plans` — Fee Amount by Class/Category/Head
| Column | Type |
|--------|------|
| id | bigint PK |
| class | string |
| category | string |
| feeHead | bigint FK → fee_heads |
| value | decimal |

#### `route_fee_plans` — Transport Fee by Route
Similar structure to fee_plans but for routes.

#### `receipts` — Payment Records
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK → users | |
| receiptId | string | Unique receipt number |
| date | date | |
| oldBalance | decimal | Carried-forward balance |
| feeHead | string | |
| jan–dec | decimal (12 cols) | Monthly amounts |
| lateFee | decimal | |
| concession | decimal | |
| netFee | decimal | |
| receivedAmt | decimal | |
| balance | decimal | |
| paymentMode | string | cash/cheque/Razorpay |
| bankName | string | |
| chequeNo | string | |
| submission_token | string | Idempotency key |

#### `concessions` — Fee Discounts
| Column | Type |
|--------|------|
| id | bigint PK |
| user_id | bigint FK → users |
| fee_plan_id | bigint FK → fee_plans |
| fee_type | string |
| concession_fee | decimal |
| reason | text |

---

### Result Management Tables (Added 2025)

#### `result_performas` — Result Card Templates
| Column | Type |
|--------|------|
| id | bigint PK |
| class | string |
| academic_year | string |
| name | string |
| is_default | boolean |

#### `result_performa_items` — Subjects in Template
| Column | Type |
|--------|------|
| id | bigint PK |
| performa_id | bigint FK → result_performas |
| sub_code_id | bigint FK → sub_codes |
| term | string |
| evaluation_type | string |
| component | string |
| max_marks | integer |
| is_included | boolean |

#### `result_terms` — P1, HY, P2, AN Terms
| Column | Type |
|--------|------|
| id | bigint PK |
| performa_id | bigint FK → result_performas |
| name | string |
| order_no | integer |

#### `result_components` — Marking Components (PT, SE, Written…)
| Column | Type |
|--------|------|
| id | bigint PK |
| term_id | bigint FK → result_terms |
| name | string |
| evaluation_type | string |
| max_marks | integer |

#### `result_subject_components` — Subject-Component Mapping
| Column | Type |
|--------|------|
| id | bigint PK |
| performa_item_id | bigint FK → result_performa_items |
| component_id | bigint FK → result_components |

#### `student_result_items` — Final Student Marks
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| performa_item_id | bigint FK → result_performa_items |
| marks | decimal |
| grade | string |

#### `student_exam_entries` — Detailed Mark Entry (with Audit)
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| result_performa_item_id | bigint |
| component_id | bigint FK → result_components |
| term_id | bigint FK → result_terms |
| marks | decimal |
| grade | string |
| entered_by_id | bigint |
| entered_by_role | string |

#### `result_co_scholastic_areas` — Extra-Curricular Criteria
| Column | Type |
|--------|------|
| id | bigint PK |
| performa_id | bigint FK → result_performas |
| class | string |
| area_name | string |
| display_order | integer |

#### `result_student_co_scholastics` — Student Co-Scholastic Grades
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| area_id | bigint FK → result_co_scholastic_areas |
| grade | string |

#### `result_student_health_records` — Health per Term
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| term_id | bigint FK → result_terms |
| height | decimal |
| weight | decimal |
| remark | text |

#### `result_finalizations` — Result Approval Workflow
| Column | Type |
|--------|------|
| id | bigint PK |
| student_id | bigint FK → users |
| performa_id | bigint FK → result_performas |
| status | string |
| finalized_by_id | bigint |
| finalized_by_role | string |

#### `result_entry_permissions` — Role-Based Mark Entry Access
| Column | Type |
|--------|------|
| id | bigint PK |
| (role/subject/class permission columns) | |

---

### Pivot / Relationship Tables

| Table | Purpose |
|-------|---------|
| `category_user` | Many-to-many: users ↔ categories |
| `fee_plan_user` | Many-to-many: users ↔ fee_plans |
| `personal_access_tokens` | Sanctum API tokens |
| `password_resets` | Password reset tokens |
| `failed_jobs` | Laravel queue failed jobs |

### Foreign Key Relationship Summary

```
users ──┬── receipts (user_id)
        ├── student_exams (student_id)
        ├── student_exam_works (student_id)
        ├── attendances (student_id)
        ├── device_tokens (user_id)
        ├── concessions (user_id)
        ├── student_result_items (student_id)
        ├── student_exam_entries (student_id)
        ├── result_finalizations (student_id)
        ├── result_student_co_scholastics (student_id)
        ├── result_student_health_records (student_id)
        ├── category_user (user_id)
        └── fee_plan_user (user_id)

teachers ──┬── exams (teacher_id)
           └── attendances (teacher_id)

admins ────── exams (admin_id)

fee_heads ──── fee_plans (feeHead)
fee_plans ─── concessions (fee_plan_id)
route_names ── users (route_id)

result_performas ──┬── result_terms (performa_id)
                   ├── result_performa_items (performa_id)
                   └── result_co_scholastic_areas (performa_id)

result_terms ────── result_components (term_id)
result_performa_items ─── result_subject_components (performa_item_id)
result_components ─────── result_subject_components (component_id)
```

---

## 3. Modules & Features

### Fee Management Module
- **Fee Head Configuration** — Define fee categories (Tuition, Books, Lab, etc.) with monthly applicability
- **Transport Routes** — Route-based fee structure with separate monthly flags
- **Fee Plans** — Hierarchical fee amounts by class × category × fee head
- **Student Fee Assignment** — Many-to-many assignment of fee plans to students
- **Receipt Generation** — Payment recording with old balance carry-forward, concessions, late fees
- **Multiple Payment Modes** — Cash, cheque, Razorpay online
- **Concession Management** — Student-level discounts with reason audit trail
- **Fee Reminders** — FCM push notifications to students with outstanding fees
- **Cashier Role** — Dedicated interface for fee counter staff

### Academic / Exam Module
- **Subject-Class Mapping** (`sub_codes`) — Links subjects to classes with schedule
- **Exam Creation** — Admin/teacher creates exams with time windows and max marks
- **Online Exams** — Students access exams via URL within time window
- **Homework** — Classwork assignment with file uploads and YouTube links
- **Homework Submission** — Students submit work with file uploads
- **Term Management** — Academic term configuration

### Result Management Module (CBSE-style, added 2025)
- **Result Performa Builder** — Template creation per class with configurable subjects
- **Term Structure** — P1, Half-Yearly (HY), P2, Annual (AN) terms
- **Evaluation Components** — PT (Periodic Test), SE (Subject Enrichment), Notebook, Written, etc.
- **Marks Entry** — Teacher-wise entry with `entered_by` audit trail
- **Co-Scholastic Areas** — Poem, Rhymes, Computer, Discipline, etc.
- **Health Records** — Height, weight per term
- **Result Finalization** — Approval workflow with role-based permissions
- **PDF Generation** — Result card PDF export (DOMPDF + FPDI)
- **Result Entry Permissions** — Granular access control for mark entry

### Attendance Module
- **Student Daily Attendance** — Class-wise daily marking with date tracking
- **Teacher Attendance** — Staff attendance tracking
- **Unified Attendance** — New `attendances` table with `marked_by` field
- **Live Class Attendance** — Separate tracking for online classes
- **Attendance in Result** — Linked to result card for reporting
- **Holiday Calendar** — Holiday definition for accurate attendance calculation

### Student Portal
- **Student Dashboard** — Fee summary, announcements, upcoming exams
- **Profile Management** — View/update profile details
- **Attendance View** — Monthly attendance calendar
- **Homework Access** — View and submit assignments
- **Exam Access** — Online exam participation
- **Announcement Feed** — Flash news from admin

### Teacher Portal
- **Class Management** — Teacher assigned to multiple classes/subjects
- **Exam Management** — Create and manage exams for assigned classes
- **Mark Entry** — Enter student marks (both exam marks and result entries)
- **Attendance Marking** — Mark student attendance for assigned classes
- **Result Entry** — Access result entry based on permissions

### Admin Portal
- **Student Management** — Admission, profile, class assignment
- **Teacher Management** — Staff profiles and class assignments
- **Fee Administration** — Complete fee module management
- **Result Administration** — Performa creation, permission management, finalization
- **Notification System** — Flash news with FCM push to all students
- **Fee Reminder Push** — Batch FCM to students with outstanding fees
- **Analytics** — Separate analytics section in views

### Mobile App Support (Android) — Live on Google Play Store
The school has a **published Android app** on the Google Play Store (`com.gplmschool.app`) that uses this Laravel app as its backend via REST API.

- **API backend:** This same Laravel app — all `/api/*` routes serve the mobile app
- **Authentication:** Sanctum token-based login using `admission_number`
- **Version check:** `GET /api/app/version-check` — supports force-update via `APP_MIN_ANDROID_BUILD` in `.env`
- **Push notifications:** FCM tokens registered via `POST /api/student/device-token`
- **Features available on app:** Fee dashboard, receipts, homework, attendance, profile, announcements
- **Play Store URL:** `https://play.google.com/store/apps/details?id=com.gplmschool.app`
- **Current version:** 1.0.1 (build 10)
- **SaaS impact:** For multi-school SaaS, the app needs a school-selection screen at login, and dynamic branding (name, logo, colors) must come from the API

---

## 4. Authentication & Roles

### Login Mechanism

The system uses **multiple Laravel guards** — each user type has its own authentication guard, provider, and session cookie:

| Guard | Provider/Model | Login Field | Route Prefix |
|-------|---------------|-------------|--------------|
| `web` (students) | `App\Models\User` | `admission_number` | `/` |
| `admin` | `App\Models\Admin` | `email` | `/admin` |
| `teacher` | `App\Models\Teacher` | `email` | `/teacher` |
| `cashier` | `App\Models\Cashier` | `email` | `/cashier` |
| `api` | `App\Models\User` (Sanctum) | `admission_number` | `/api` |

**Config file:** `config/auth.php`

### API Authentication
- Laravel Sanctum (^3.3)
- Stateless token-based for mobile app
- Token stored in `personal_access_tokens`
- Device token registration in `device_tokens` (for FCM)

### Middleware Stack

| Middleware | File | Purpose |
|-----------|------|---------|
| `Authenticate` | `app/Http/Middleware/Authenticate.php` | Guard-based auth check |
| `MultiAuthMiddleware` | `app/Http/Middleware/MultiAuthMiddleware.php` | Handle multiple concurrent guards |
| `RedirectIfAuthenticated` | Standard | Redirect logged-in users away from login |
| `RedirectIfSessionExpired` | Custom | Handle session expiry gracefully |
| `TrustProxies` | Standard | Reverse proxy support (was needed for Lambda/CloudFront; still useful on VPS with Nginx) |
| `VerifyCsrfToken` | Standard | CSRF protection for web routes |

### Role Capabilities Summary

| Feature | Admin | Teacher | Cashier | Student |
|---------|-------|---------|---------|---------|
| Student management | ✓ | — | — | — |
| Fee configuration | ✓ | — | — | — |
| Fee receipt creation | ✓ | — | ✓ | — |
| Exam creation | ✓ | ✓ | — | — |
| Mark entry | ✓ | ✓ | — | — |
| Result finalization | ✓ | — | — | — |
| Attendance marking | ✓ | ✓ | — | — |
| Flash news/notifications | ✓ | — | — | — |
| View own results | — | — | — | ✓ |
| View own attendance | — | — | — | ✓ |
| Pay fees (Razorpay) | — | — | — | ✓ |

---

## 5. Controllers & Models

### Controllers (48 files)

#### Auth Controllers (`app/Http/Controllers/Auth/`)
| Controller | Purpose |
|-----------|---------|
| `AdminLoginController` | Admin email/password login |
| `AdminRegisterController` | Admin registration |
| `TeacherLoginController` | Teacher email/password login |
| `TeacherRegisterController` | Teacher registration |
| `LoginController` | Student login (admission_number) |
| `RegisterController` | Student registration |
| `CashierLoginController` | Cashier login |
| `ForgotPasswordController` | Password reset initiation |
| `ResetPasswordController` | Password reset completion |
| `VerificationController` | Email verification |
| `ConfirmPasswordController` | Password re-confirmation |

#### API Controllers (`app/Http/Controllers/Api/`)
| Controller | Purpose |
|-----------|---------|
| `AuthController` | Mobile login, version check, device token storage |
| `StudentFeeApiController` | Fee details, dashboard summary, receipt listing |
| `StudentHomeworkApiController` | Homework listing for mobile app |

#### Admin Controllers (`app/Http/Controllers/Admin/`)
| Controller | Purpose |
|-----------|---------|
| `AdminController` | Student CRUD, flash news, FCM batch notifications, fee reminders (~large) |
| `ExamController` | Admin exam management |
| `TermController` | Academic term CRUD |
| `AttendanceAdminController` | Admin-side attendance view |
| `Calendar` | Holiday/event calendar |
| `ForgotPasswordController` | Admin password reset |
| `AdminExamMarksController` | Admin marks entry for exams |

#### Fee Controllers (`app/Http/Controllers/Fee/`)
| Controller | Purpose |
|-----------|---------|
| `FeeController` | Fee heads, categories, plans — full configuration (~2519 lines) |
| `StudentFeeController` | Student-level fee details, Razorpay payment processing |
| `TransportController` | Transport route fees |
| `CashierController` | Cashier-facing payment recording interface |

#### Result Controllers (`app/Http/Controllers/Result/`)
| Controller | Purpose |
|-----------|---------|
| `ResultController` | Result listing and management |
| `ResultPerformaController` | Result template CRUD |
| `ResultPerformaBuilderController` | Complex template builder |
| `ResultPerformaTermController` | Term management within performa |
| `ResultPerformaComponentController` | Component management |
| `ResultSubjectComponentController` | Subject-component mapping |
| `ResultCoScholasticAreaController` | Co-scholastic area management |
| `MarksEntryController` | Teacher/admin marks entry |
| `ResultPermissionController` | Entry permission management |
| `ResultPdfController` | Result card PDF generation |
| `ResultFinalizationController` | Result approval workflow |
| `Seeder` | Dev tool — seeds result data |

#### Student Controllers (`app/Http/Controllers/Student/`)
| Controller | Purpose |
|-----------|---------|
| `StudentController` | Profile, homework, attendance, announcements |
| `ExamController` | Student exam listing and results |
| `CalendarController` | Student calendar view |

#### Teacher Controllers (`app/Http/Controllers/Teacher/`)
| Controller | Purpose |
|-----------|---------|
| `TeacherController` | Teacher dashboard, class view |
| `ExamMarksController` | Teacher marks entry for traditional exams |
| `ResultEntryController` | Result performa marks entry |
| `TeacherResultController` | Teacher result view |
| `teacherExamController` | Teacher exam management |
| `TeacherAttendanceController` | Teacher's own attendance |
| `CalendarController` | Teacher calendar view |

#### Other
| Controller | Purpose |
|-----------|---------|
| `HomeController` | Landing/home page |
| `MetricaController` | Analytics/metrics views |

---

### Models (38 files) with Relationships

#### Core User Models
| Model | Key Relationships |
|-------|-----------------|
| `User` | hasMany: receipts, concessions; belongsTo: route, category; belongsToMany: feePlans |
| `Admin` | hasMany: exams |
| `Teacher` | hasMany: exams, attendances; belongsToMany: classCodes, subCodes |
| `Cashier` | (standalone) |

#### Academic Models
| Model | Key Relationships |
|-------|-----------------|
| `Exam` | belongsTo: term, admin, teacher; hasMany: studentExams |
| `subCode` | hasMany: resultPerformaItems |
| `classwork` | hasMany: studentExamWorks |
| `studentExams` | belongsTo: exam, user, teacher |
| `studentExamWorks` | belongsTo: classwork |
| `stuHomeworkUpload` | (tracking model) |
| `Attendance` | belongsTo: user, teacher |
| `Holiday` | (standalone) |
| `Term` | hasMany: exams |
| `flashNews` | (standalone) |
| `liveClassAttendence` | (standalone) |

#### Fee Models
| Model | Key Relationships |
|-------|-----------------|
| `FeeHead` | hasMany: feePlans |
| `FeePlan` | belongsTo: feeHead; belongsToMany: users |
| `Receipt` | belongsTo: user |
| `RouteName` | hasMany: users |
| `Concession` | belongsTo: user, feePlan |
| `routeFeePlan` | (standalone) |
| `Category` | belongsToMany: users |

#### Result Models
| Model | Key Relationships |
|-------|-----------------|
| `ResultPerforma` | hasMany: terms, items, coScholasticAreas |
| `ResultPerformaItem` | belongsTo: performa, subCode; hasMany: subjectComponents |
| `ResultTerm` | belongsTo: performa; hasMany: components |
| `ResultComponent` | belongsTo: term; hasMany: subjectMappings |
| `ResultSubjectComponent` | belongsTo: performaItem, component |
| `StudentResultItem` | belongsTo: user, performaItem |
| `StudentExamEntry` | belongsTo: user, performaItem, component, term |
| `ResultCoScholasticArea` | belongsTo: performa; hasMany: studentCoScholastics |
| `ResultStudentCoScholastic` | belongsTo: area, user |
| `ResultStudentHealthRecord` | belongsTo: user, term |
| `ResultFinalization` | belongsTo: user, performa |
| `ResultEntryPermission` | (access control) |

#### Services
| Service | Purpose |
|---------|---------|
| `app/Services/ResultCalculationService.php` | Complex result calculation: term marks, percentages, pass/fail, subject accumulation |

---

## 6. Hardcoded School Data

These are all values that must become **per-tenant configurable** settings for SaaS conversion.

### In `.env` (Production)
```
APP_NAME=GPLMSCHOOL
APP_URL=https://portal.gplmschool.com
MAIL_FROM_ADDRESS=gplmschool@gmail.com
MAIL_FROM_NAME=GPLMSCHOOL
```

### In Views / Blade Templates

| Location | Hardcoded Value |
|---------|----------------|
| Login pages | "GPLM School" — school name displayed |
| Email templates | `config('GPLMSchool')` referenced |
| Email templates | `gplmschool@gmail.com` |
| Email templates | `www.gplmschool.com`, `gplmschool.co.in` |
| Logo references | `https://gplmschool.co.in/assets/images/gpl_logo.png` |

### In Firebase / Android
| Item | Value |
|------|-------|
| Firebase Project ID | `gplm-school-app` |
| FCM Credentials file | `storage/app/firebase/gplm-school-app-4aeab4084500.json` |
| Android App ID | `com.gplmschool.app` |
| Play Store URL | `https://play.google.com/store/apps/details?id=com.gplmschool.app` |
| Android Keystore | `android.keystore` (in project root!) |

### In Config
| Config Key | Hardcoded Value | Location |
|-----------|----------------|---------|
| `GPLMSchool` | School name config | Referenced in email templates |
| AWS S3 Buckets | `gplmschool-dev-storage-*` | `.env` / `serverless.yml` |
| CloudFront URL | `gplmschool-dev-assets-*.cloudfront.net` | `.env` |

### Settings That MUST Become Per-Tenant

For a proper SaaS conversion, each tenant needs configurable:
1. School name, address, phone, email
2. School logo (stored in S3 per tenant)
3. SMTP / email sender settings (or shared transactional email)
4. Razorpay keys (per school's merchant account)
5. FCM credentials (per school's Firebase project OR shared with topic routing)
6. Android app package name (shared app with dynamic branding OR per-school build)
7. Academic year start/end
8. Grading scheme (CBSE, ICSE, State Board)
9. Fee structure currency and late fee rules
10. Result performa defaults per class

---

## 7. Routes Overview

### web.php — Web Routes

#### Public Routes
```
GET  /                          → HomeController
GET  /privacy-policy            → Static view
GET  /account/delete-policy     → Static view
GET  /manifest.json             → PWA manifest
```

#### Authentication Routes
```
# Students
GET/POST  /login                → Auth\LoginController
POST      /logout               → Auth\LoginController
GET/POST  /register             → Auth\RegisterController
GET/POST  /password/reset       → Auth\ForgotPasswordController

# Admin
GET/POST  /admin/login          → Auth\AdminLoginController
POST      /admin/logout
GET/POST  /admin/password/reset

# Teacher
GET/POST  /teacher/login        → Auth\TeacherLoginController
POST      /teacher/logout

# Cashier
GET/POST  /cashier/login        → Auth\CashierLoginController
```

#### Protected Route Groups
```
# Admin (middleware: auth:admin)
/admin/dashboard
/admin/students/**
/admin/teachers/**
/admin/fees/**
/admin/exams/**
/admin/results/**
/admin/attendance/**
/admin/calendar
/admin/analytics

# Teacher (middleware: auth:teacher)
/teacher/dashboard
/teacher/classes/**
/teacher/exams/**
/teacher/marks/**
/teacher/results/**
/teacher/attendance/**

# Student (middleware: auth:web)
/student/dashboard
/student/fees/**
/student/exams/**
/student/homework/**
/student/attendance/**
/student/profile

# Cashier (middleware: auth:cashier)
/cashier/dashboard
/cashier/fees/**
```

### api.php — API Routes (Mobile App)

#### Public API Routes
```
POST  /api/login               → Api\AuthController@login
GET   /api/app/version-check   → Api\AuthController@checkVersion
```

#### Protected API Routes (auth:sanctum)
```
GET   /api/student/homework
GET   /api/student/attendance/monthly
GET   /api/student/profile
GET   /api/student/announcements
POST  /api/student/reset-password
POST  /api/student/device-token
GET   /api/student/receipts
POST  /api/student/fee/feeDetails
GET   /api/student/fee/dashboardSummary
```

---

## 8. Multi-Tenancy Blockers

### Current State: Single-Tenant Architecture

The application is purpose-built for **one school** (GPLM School). There is **zero multi-tenancy infrastructure** present.

### Critical Blockers

#### 1. No `school_id` / `tenant_id` in Any Table
Every table — users, receipts, exams, results, attendances — has **no tenant discriminator column**. All queries return all data globally.

**Impact:** Every database query must be audited and a `WHERE school_id = ?` clause (or global scope) added.

#### 2. Separate User Tables per Role (Compounding Problem)
`users`, `admins`, `teachers`, `cashiers` are 4 separate tables. For multi-tenancy, either:
- Add `school_id` to all 4 tables, OR
- Merge into a unified `users` table with a `role` column + `school_id`

The 12-column teacher class assignment (`class_code0`–`class_code11`) is a schema design debt that will complicate tenant isolation.

#### 3. No Global Scopes
No `GlobalScope` implementations exist. No `TenantScope`, no `BelongsToTenant` trait, no automatic query filtering by school.

#### 4. Hardcoded School Identity in Multiple Layers
See Section 6. School name, logo, email, Firebase project, Razorpay keys, and S3 buckets are all environment-level singletons — not per-tenant configurable.

#### 5. Single S3 Bucket for All File Storage
All student uploads, result PDFs, and assets share one S3 bucket (`gplmschool-dev-storage-*`). SaaS requires either:
- Per-tenant S3 path prefixes (`/{school_id}/uploads/...`)
- Per-tenant S3 buckets (complex, but full isolation)

#### 6. Single Firebase Project for FCM
One FCM project (`gplm-school-app`) serves all push notifications. In multi-tenant mode, either:
- Each school gets its own Firebase project (separate credentials per tenant)
- Use a shared project with topic-based routing per school

#### 7. Single Razorpay Account
Currently one Razorpay key pair in `.env`. Each school needs its own merchant account for proper financial isolation and settlement.

#### 8. AWS DynamoDB for Sessions
Sessions stored in DynamoDB. In multi-tenant mode, session keys must be namespaced per tenant to prevent cross-tenant session leakage. The session driver configuration is global.

#### 9. Single `admins` Table
There is one admin pool — no concept of "this admin belongs to School X". First admin to log in sees all data.

#### 10. Config-Level School Identity
```php
config('GPLMSchool')  // Used in email templates
```
School name/identity is loaded from config (environment variables), not from a per-request tenant resolution.

#### 11. Android App Tied to One School
`com.gplmschool.app` — the Android app is branded and compiled for GPLM School specifically. For SaaS with mobile support, consider a white-label app approach or dynamic branding via API.

#### 12. APP_DEBUG=true in Production
Not a multi-tenancy blocker, but a critical security issue: stack traces are exposed in production, potentially leaking inter-tenant data in error pages.

### What Partially Exists

- **Category system** — Students are grouped by category (class/section). This is an intra-school grouping, not cross-tenant isolation.
- **Route-based fee separation** — Transport routes are already a mini-segmentation concept.
- **Result performa per class** — Template-based result system is designed to be configurable, which is good.

### Recommended Multi-Tenancy Architecture

**Recommended approach: Shared Database, Separate Schemas (or single schema with `school_id`)**

```
Option A: Single DB, school_id column (simplest)
  - Add school_id to every table
  - Use Laravel Global Scopes to auto-filter
  - Use spatie/laravel-multitenancy or stancl/tenancy

Option B: Schema-per-tenant (medium complexity)
  - Each school gets its own MySQL schema
  - Laravel DB connection switches per request
  - Better isolation, harder to manage

Option C: Database-per-tenant (most isolated, highest cost)
  - Full isolation but expensive at scale
```

Given the current VPS + MySQL infrastructure, **Option A (shared DB + `school_id`)** with `stancl/tenancy` or manual global scopes is the recommended path.

---

## 9. Frontend / Views

### Template Engine
**Laravel Blade** — no React or Vue component framework detected. The frontend is traditional server-side rendered HTML.

### Frontend Stack
| Technology | Version | Purpose |
|-----------|---------|---------|
| Bootstrap | 5.3.3 | CSS framework |
| jQuery | 3.2 | DOM manipulation |
| Axios | — | AJAX HTTP requests |
| SASS/SCSS | — | CSS preprocessing |
| Laravel Mix / Webpack 5 | — | Asset compilation |
| PDFObject | — | In-browser PDF viewing |

### View Folder Structure
```
resources/views/
├── admin/              Admin dashboard, student lists, fee management, results
├── analytics/          Metrics/reporting views
├── auth/               Login, register, password reset (per-role)
│   ├── admin/
│   ├── teacher/
│   └── cashier/
├── layouts/            Base Blade layout templates
├── student/            Student portal views (fees, homework, exams)
├── teacher/            Teacher portal views (marks, attendance, exams)
├── vendor/mail/        Customized Laravel email templates
└── GrowAI Automation/  Privacy policy, automation info pages
```

### School Branding in Views

**Branding found in:**
- `resources/views/layouts/` — Main layout files contain school name/logo references
- `resources/views/auth/` — Login pages display "GPLM School"
- `resources/views/vendor/mail/` — Email HTML templates with school name, email, website URLs
- `resources/views/admin/` — Dashboard header with school logo

**For SaaS conversion, the following view elements must be made dynamic:**
1. School name in page titles (`<title>{{ $school->name }} - Admin</title>`)
2. Logo `<img src>` — must load from tenant's S3 path
3. Contact email and website in email templates
4. Any "GPLM" literal text in Blade files
5. Color theme/branding — consider per-tenant CSS variables

### PWA / Mobile
- `manifest.json` route exists — Progressive Web App support
- Android APK and keystore in project root (unusual for a web repo)

---

## 10. Third-Party Integrations

### Payment Gateway — Razorpay
| Item | Detail |
|------|--------|
| Package | `razorpay/razorpay ^2.9` |
| Key in .env | `RAZORPAY_KEY=rzp_test_bOI1eZAyffVS5D` (test mode in .env!) |
| Used in | `Fee/StudentFeeController` |
| Flow | Student initiates payment → Razorpay checkout → webhook/callback → receipt creation |
| SaaS impact | Each school needs own Razorpay account; keys stored per tenant |

### Push Notifications — Firebase Cloud Messaging
| Item | Detail |
|------|--------|
| Credentials | `storage/app/firebase/gplm-school-app-4aeab4084500.json` |
| API used | FCM v1 (`https://fcm.googleapis.com/v1/projects/{projectId}/messages:send`) |
| Features | Flash news broadcast, fee reminders, exam notifications |
| SaaS impact | Shared vs. per-tenant Firebase project decision needed |

### Email — AWS SES
| Item | Detail |
|------|--------|
| Driver | SMTP via AWS SES |
| Endpoint | `email-smtp.ap-south-1.amazonaws.com:587` |
| From address | `gplmschool@gmail.com` |
| Used for | Password reset, receipt emails, notifications |
| SaaS impact | From address and "from name" must be per-tenant |

### File Storage — AWS S3
| Item | Detail |
|------|--------|
| Package | `league/flysystem-aws-s3-v3` |
| Bucket | `gplmschool-dev-storage-vkmjgjn4dvol` |
| CDN | CloudFront: `gplmschool-dev-assets-yhvzvk0mit5j.s3.amazonaws.com` |
| Stored files | Student homework uploads, result PDFs, profile photos |
| SaaS impact | Add `/{school_id}/` prefix to all S3 paths |

### PDF Generation
| Item | Detail |
|------|--------|
| Package | `barryvdh/laravel-dompdf ^3.1` + `setasign/fpdf` + `setasign/fpdi` |
| Used for | Result card PDFs, fee receipt PDFs |

### Caching & Sessions — File / Cookie (VPS)
| Item | Detail |
|------|--------|
| Cache driver | `file` — stored in `storage/framework/cache` on VPS |
| Session driver | `cookie` — encrypted cookie in browser |
| Previous (Lambda) | DynamoDB for both cache and sessions |
| SaaS impact | Cookie sessions are per-browser and don't need tenant namespacing; file cache does need per-tenant key prefixing if shared server |

### Queue — Sync (VPS)
| Item | Detail |
|------|--------|
| Driver | `sync` — jobs run immediately in the same request (no background worker) |
| Previous (Lambda) | AWS SQS with a dedicated Lambda worker function |
| SaaS impact | For SaaS with heavy operations (bulk FCM, batch PDFs), switch to `database` or `redis` queue with a supervisor-managed worker process |

### Hosting — VPS (Current)
| Item | Detail |
|------|--------|
| Server | VPS (Linux) — previously AWS Lambda/Bref, now migrated |
| Web server | Apache or Nginx serving `public/index.php` |
| PHP | 8.2 |
| App path | `/var/www/gplmschool` |
| Cache driver | `file` (local filesystem) |
| Session driver | `cookie` (overrides earlier `file` setting in `.env`) |
| Queue driver | `sync` (jobs run inline, no worker process) |
| Database | MySQL on `127.0.0.1:3306`, DB: `gplmschool` |
| SaaS note | `bref/bref` package still in composer.json but not active — can be removed |

> **Previously (AWS Lambda):** Used DynamoDB for cache/sessions, SQS for queues, Lambda functions for web/artisan/worker. All of this has been replaced by VPS equivalents. `serverless.yml` is now outdated.

### Other Composer Packages
| Package | Purpose |
|---------|---------|
| `laravel/sanctum ^3.3` | API token authentication for mobile |
| `laravel/ui ^3.0` | Auth scaffolding |
| `barryvdh/laravel-dompdf ^3.1` | PDF generation |
| `guzzlehttp/guzzle` | HTTP client (FCM API calls) |
| `doctrine/dbal` | DB schema introspection |
| `spatie/laravel-ignition` | Error reporting |

---

## Summary: SaaS Conversion Priority Matrix

### Phase 1 — Foundation (Blockers)
| Item | Effort | Impact |
|------|--------|--------|
| Add `school_id` to all tables | High | Critical |
| Create `schools` tenant table | Low | Critical |
| Implement tenant resolution middleware | Medium | Critical |
| Add Global Scopes to all models | High | Critical |
| Remove hardcoded school identity from views | Medium | Critical |
| Fix APP_DEBUG=false in production | Trivial | Security |

### Phase 2 — Per-Tenant Configuration
| Item | Effort | Impact |
|------|--------|--------|
| Per-tenant Razorpay keys | Medium | Revenue |
| Per-tenant S3 path prefixes | Low | Data isolation |
| Per-tenant FCM (or shared + topics) | High | Notifications |
| Per-tenant email from-address | Low | Branding |
| Per-tenant logo and branding | Medium | UX |
| School settings table (name, address, etc.) | Low | Core |

### Phase 3 — Architecture
| Item | Effort | Impact |
|------|--------|--------|
| Merge 4 user tables into unified users+roles | High | Maintainability |
| Refactor teacher class assignment (12 columns → pivot) | Medium | Scalability |
| Add tenant namespacing to DynamoDB sessions | Low | Security |
| Admin super-panel (manage all schools) | Medium | Operations |
| Tenant onboarding/provisioning flow | Medium | Sales |
| Billing/subscription integration | High | Revenue |

### Phase 4 — Mobile
| Item | Effort | Impact |
|------|--------|--------|
| Dynamic branding via API (school name, logo, colors) | Medium | UX |
| Multi-school login (school selection screen) | Medium | UX |
| Per-school app build vs. shared white-label | High | Strategic |

---

*This document was auto-generated by Claude Code on 2026-03-23 for the purpose of SaaS architecture planning.*
