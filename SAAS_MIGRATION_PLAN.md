# School Portal — SaaS Conversion Master Plan
> Based on analysis of `portal.gplmschool.com` (Laravel 9 · PHP 8.2 · MySQL)
> Generated: 2026-03-23

---

## 🏗️ Recommended Architecture

**Approach: Shared Database + `school_id` column + Subdomain/Custom Domain routing**

```
schoolportal.in (Super Admin Panel)
    ├── gplmschool.schoolportal.in     → school_id: 1
    ├── sdbmsss.schoolportal.in        → school_id: 2
    ├── portal.gplmschool.com          → (custom domain → school_id: 1)
    └── portal.sdbmsss.com             → (custom domain → school_id: 2)
```

**Package:** `stancl/tenancy` v3 — best for Laravel 9, subdomain + custom domain support

---

## Phase 1 — Foundation (Week 1–2)

### Step 1.1 — Install stancl/tenancy

```bash
composer require stancl/tenancy
php artisan tenancy:install
php artisan migrate
```

### Step 1.2 — Create `schools` Table (Central DB)

```php
// Migration: create_schools_table
Schema::create('schools', function (Blueprint $table) {
    $table->id();
    $table->string('name');                          // "GPLM School"
    $table->string('slug')->unique();                // "gplmschool"
    $table->string('custom_domain')->nullable();     // "portal.gplmschool.com"
    $table->string('logo_path')->nullable();         // S3 path
    $table->string('address')->nullable();
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('primary_color')->default('#007bff');
    $table->string('razorpay_key')->nullable();
    $table->string('razorpay_secret')->nullable();
    $table->string('firebase_project_id')->nullable();
    $table->text('firebase_credentials')->nullable(); // JSON
    $table->string('mail_from_address')->nullable();
    $table->string('mail_from_name')->nullable();
    $table->string('s3_prefix')->nullable();         // "school_1/"
    $table->string('plan')->default('basic');        // basic/standard/premium
    $table->date('plan_expires_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Step 1.3 — School Model

```php
// app/Models/School.php
class School extends Model
{
    protected $fillable = [
        'name', 'slug', 'custom_domain', 'logo_path', 'address',
        'phone', 'email', 'razorpay_key', 'razorpay_secret',
        'firebase_project_id', 'firebase_credentials',
        'mail_from_address', 'mail_from_name', 's3_prefix',
        'plan', 'plan_expires_at', 'is_active', 'primary_color'
    ];
}
```

### Step 1.4 — Add `school_id` to ALL Tables

**Tables that need `school_id` column:**

```php
// Run this migration for each table listed below
$tables = [
    'users', 'admins', 'teachers', 'cashiers',
    'fee_heads', 'fee_plans', 'route_names', 'route_fee_plans',
    'receipts', 'concessions', 'fee_plan_user',
    'sub_codes', 'exams', 'terms', 'classworks',
    'student_exams', 'student_exam_works',
    'attendances', 'student_attendances', 'teacher_attendances',
    'holidays', 'flash_news', 'device_tokens',
    'result_performas', 'result_performa_items', 'result_terms',
    'result_components', 'result_subject_components',
    'student_result_items', 'student_exam_entries',
    'result_co_scholastic_areas', 'result_student_co_scholastics',
    'result_student_health_records', 'result_finalizations',
    'result_entry_permissions', 'categories', 'category_user',
];

// Migration template:
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('school_id')
          ->after('id')
          ->constrained('schools')
          ->onDelete('cascade');
    $table->index('school_id');
});
```

### Step 1.5 — Tenant Resolution Middleware

```php
// app/Http/Middleware/TenantMiddleware.php
class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $school = null;

        // Check custom domain first (e.g., portal.gplmschool.com)
        $school = School::where('custom_domain', $host)
                        ->where('is_active', true)
                        ->first();

        // Then check subdomain (e.g., gplmschool.schoolportal.in)
        if (!$school) {
            $subdomain = explode('.', $host)[0];
            $school = School::where('slug', $subdomain)
                            ->where('is_active', true)
                            ->first();
        }

        if (!$school) {
            abort(404, 'School not found');
        }

        // Store in app container for global access
        app()->instance('school', $school);
        config(['school.id' => $school->id]);
        config(['school.data' => $school]);

        // Set per-tenant mail
        config(['mail.from.address' => $school->mail_from_address ?? config('mail.from.address')]);
        config(['mail.from.name'    => $school->mail_from_name    ?? config('mail.from.name')]);

        return $next($request);
    }
}
```

### Step 1.6 — Global Scopes on ALL Models

```php
// app/Traits/BelongsToSchool.php
trait BelongsToSchool
{
    protected static function bootBelongsToSchool(): void
    {
        // Auto-filter all queries by current school
        static::addGlobalScope('school', function (Builder $builder) {
            if (app()->has('school')) {
                $builder->where(
                    (new static)->getTable() . '.school_id',
                    app('school')->id
                );
            }
        });

        // Auto-set school_id on create
        static::creating(function ($model) {
            if (app()->has('school') && empty($model->school_id)) {
                $model->school_id = app('school')->id;
            }
        });
    }
}

// Apply to every model:
class User extends Authenticatable
{
    use BelongsToSchool; // Add this line
    // ...
}
// Apply same to: Admin, Teacher, Cashier, FeeHead, FeePlan,
// Receipt, Attendance, ResultPerforma, FlashNews, etc.
```

### Step 1.7 — Register Middleware in `web.php`

```php
// routes/web.php
Route::middleware(['tenant'])->group(function () {
    // All existing school portal routes
    require __DIR__.'/school.php'; // move existing routes here
});

// Super admin (no tenant middleware)
Route::prefix('superadmin')->middleware(['super_admin_auth'])->group(function () {
    require __DIR__.'/superadmin.php';
});
```

---

## Phase 2 — Per-Tenant Configuration (Week 3)

### Step 2.1 — Per-Tenant Razorpay

```php
// app/Http/Controllers/Fee/StudentFeeController.php
// Replace static config with dynamic school config:

$school = app('school');
$razorpay = new Api(
    $school->razorpay_key    ?? config('services.razorpay.key'),
    $school->razorpay_secret ?? config('services.razorpay.secret')
);
```

### Step 2.2 — Per-Tenant S3 Storage Paths

```php
// app/Helpers/StorageHelper.php
function tenantStoragePath(string $path): string
{
    $school = app('school');
    $prefix = $school->s3_prefix ?? 'school_' . $school->id;
    return "{$prefix}/{$path}";
}

// Usage:
Storage::disk('s3')->put(tenantStoragePath('homework/' . $filename), $file);
// Result: "school_1/homework/file.pdf"
```

### Step 2.3 — Per-Tenant FCM (Shared Project + Topic Routing)

```php
// app/Services/FcmService.php
// Use school_id as FCM topic for broadcasts:

class FcmService
{
    public function sendToSchool(string $title, string $body): void
    {
        $school = app('school');
        $topic  = "school_{$school->id}";

        $this->sendToTopic($topic, $title, $body);
    }

    // Students subscribe to topic on login:
    // POST /api/student/subscribe-topic → topic: school_{id}
}
```

### Step 2.4 — Dynamic Branding in Blade Views

```php
// resources/views/layouts/app.blade.php
@php $school = app('school') @endphp

<title>{{ $school->name }} - Admin Portal</title>
<link rel="icon" href="{{ Storage::url($school->logo_path) }}">

<style>
  :root {
    --primary-color: {{ $school->primary_color ?? '#007bff' }};
  }
</style>

<!-- Logo -->
<img src="{{ $school->logo_path ? Storage::url($school->logo_path) : asset('images/default-logo.png') }}"
     alt="{{ $school->name }}">
```

```php
// resources/views/vendor/mail/html/header.blade.php
@php $school = app()->has('school') ? app('school') : null @endphp
<h1>{{ $school?->name ?? 'School Portal' }}</h1>
```

### Step 2.5 — School Settings Page (Admin)

```
/admin/settings → Edit school profile
    - School name, address, phone, email, website
    - Logo upload
    - Razorpay keys (masked)
    - Primary color picker
    - Custom domain setting
    - Mail from address
```

---

## Phase 3 — Super Admin Panel (Week 4)

### Step 3.1 — Super Admin Routes & Controller

```php
// routes/superadmin.php
Route::get('/dashboard', [SuperAdminController::class, 'dashboard']);
Route::resource('/schools', SchoolManagementController::class);
Route::post('/schools/{school}/activate', [SchoolManagementController::class, 'activate']);
Route::post('/schools/{school}/suspend', [SchoolManagementController::class, 'suspend']);
Route::get('/schools/{school}/impersonate', [SchoolManagementController::class, 'impersonate']);
Route::get('/billing', [BillingController::class, 'index']);
```

### Step 3.2 — Super Admin Dashboard Features

```
superadmin.schoolportal.in/dashboard
    ├── Total schools: 12
    ├── Active schools: 10
    ├── Revenue this month: ₹24,000
    ├── Schools list with status, plan, expiry
    ├── Add new school button
    └── Impersonate (login as school admin)
```

### Step 3.3 — New School Onboarding Flow

```
1. Super admin fills form:
   - School name, slug (auto-suggest), email
   - Plan selection
   - Admin email + temp password

2. System creates:
   - Record in `schools` table
   - First admin in `admins` table (with school_id)
   - S3 folder: school_{id}/
   - Wildcard DNS already covers *.schoolportal.in

3. Welcome email to school admin:
   "Your portal is ready: gplmschool.schoolportal.in
    Login: admin@gplmschool.com / TempPass123"

4. School admin can then:
   - Upload logo
   - Add Razorpay keys
   - Set custom domain (optional, Premium plan)
   - Start adding students
```

### Step 3.4 — Custom Domain Setup (Premium Plan)

```
School admin enters: portal.gplmschool.com

System shows instructions:
  "Add this CNAME record to your DNS:
   Name: portal
   Value: schoolportal.in
   TTL: 3600"

After DNS propagates:
  php artisan ssl:provision portal.gplmschool.com
  (Uses certbot + Let's Encrypt for per-domain SSL)
```

```bash
# SSL automation script
# scripts/provision-ssl.sh
certbot certonly --standalone \
  --non-interactive \
  --agree-tos \
  --email admin@schoolportal.in \
  -d {$custom_domain}
```

---

## Phase 4 — Database Migration of Existing Data (Week 5)

### Step 4.1 — Migrate GPLM School Data

```sql
-- Set school_id = 1 for ALL existing data
-- Run AFTER adding school_id columns

UPDATE users             SET school_id = 1;
UPDATE admins            SET school_id = 1;
UPDATE teachers          SET school_id = 1;
UPDATE cashiers          SET school_id = 1;
UPDATE fee_heads         SET school_id = 1;
UPDATE fee_plans         SET school_id = 1;
UPDATE route_names       SET school_id = 1;
UPDATE receipts          SET school_id = 1;
UPDATE concessions       SET school_id = 1;
UPDATE sub_codes         SET school_id = 1;
UPDATE exams             SET school_id = 1;
UPDATE terms             SET school_id = 1;
UPDATE classworks        SET school_id = 1;
UPDATE attendances       SET school_id = 1;
UPDATE holidays          SET school_id = 1;
UPDATE flash_news        SET school_id = 1;
UPDATE device_tokens     SET school_id = 1;
UPDATE result_performas  SET school_id = 1;
-- ... (all remaining tables)
```

### Step 4.2 — Create GPLM School Record

```sql
INSERT INTO schools (id, name, slug, custom_domain, email, plan, is_active)
VALUES (
    1,
    'GPLM School',
    'gplmschool',
    'portal.gplmschool.com',
    'gplmschool@gmail.com',
    'premium',
    1
);
```

---

## Phase 5 — API Multi-tenancy (Mobile App) (Week 6)

### Step 5.1 — School Identification in API

```php
// routes/api.php
// Add school identification header to all API requests

// Option A: Via subdomain (same as web)
// api.gplmschool.schoolportal.in/api/...

// Option B: Via header (simpler for mobile)
// X-School-ID: gplmschool (slug)

// Middleware for API:
class ApiTenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Try header first
        $slug = $request->header('X-School-Slug');

        // Fallback to subdomain
        if (!$slug) {
            $slug = explode('.', $request->getHost())[0];
        }

        $school = School::where('slug', $slug)
                        ->where('is_active', true)
                        ->firstOrFail();

        app()->instance('school', $school);
        return $next($request);
    }
}
```

### Step 5.2 — New API Endpoint: School Discovery

```php
// GET /api/school-info?slug=gplmschool
// Returns: name, logo, colors, features enabled
// Used by mobile app on first launch / login screen
{
    "name": "GPLM School",
    "logo": "https://cdn.schoolportal.in/school_1/logo.png",
    "primary_color": "#1a6fa3",
    "slug": "gplmschool"
}
```

### Step 5.3 — Mobile App Login Flow (Updated)

```
Old flow: Enter admission_number + password → Login

New flow:
  Screen 1: Enter school code (slug) → Fetch school info → Show school logo
  Screen 2: Enter admission_number + password → Login
```

---

## Phase 6 — Security Fixes (Immediate Priority)

```php
// .env
APP_DEBUG=false              // CRITICAL: Fix immediately!

// Tenant data isolation test:
// Ensure no query returns data across school_ids
// Add tests for this

// Session isolation:
// Cookie sessions are safe (per-browser)
// File cache — use tenant prefix:
Cache::put("school_{$school->id}:fee_summary", $data);
```

---

## Billing & Subscription Plans

### Recommended Plans for Indian Market

| Plan | Price/month | Students | Modules |
|------|------------|----------|---------|
| **Basic** | ₹999 | up to 500 | Fees, Attendance |
| **Standard** | ₹1,999 | up to 1,500 | + Results, Exams, App |
| **Premium** | ₹3,499 | Unlimited | + Custom domain, Branding |

### Implementation Options
1. **Manual billing** (start here) — Invoice schools monthly via email
2. **Razorpay Subscriptions** — Automate later with `cashier`-style integration

---

## Infrastructure Changes Needed

| Item | Current | SaaS Required |
|------|---------|---------------|
| Domain | `portal.gplmschool.com` | `*.schoolportal.in` wildcard DNS |
| SSL | Single cert | Wildcard `*.schoolportal.in` + per-domain via certbot |
| S3 | Single bucket, flat | Same bucket, `school_{id}/` prefix per tenant |
| Queue | `sync` | Switch to `database` queue + supervisor worker |
| Cache | `file` | Add tenant prefix to all cache keys |
| Firebase | 1 project | Shared project + topic-based routing |
| Razorpay | 1 account | Per-tenant keys stored in `schools` table |

---

## Wildcard DNS & SSL Setup (One-Time)

```bash
# 1. Add wildcard DNS record (in your domain registrar):
# Type: A
# Name: *
# Value: YOUR_VPS_IP

# 2. Get wildcard SSL cert:
certbot certonly --manual --preferred-challenges dns \
  -d "*.schoolportal.in" -d "schoolportal.in"

# 3. Nginx config:
server {
    listen 443 ssl;
    server_name ~^(?<slug>.+)\.schoolportal\.in$;

    ssl_certificate     /etc/letsencrypt/live/schoolportal.in/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/schoolportal.in/privkey.pem;

    root /var/www/schoolportal/public;
    # ... standard Laravel config
}

# 4. For custom domains (e.g., portal.gplmschool.com):
server {
    listen 443 ssl;
    server_name portal.gplmschool.com;

    ssl_certificate     /etc/letsencrypt/live/portal.gplmschool.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/portal.gplmschool.com/privkey.pem;

    root /var/www/schoolportal/public;
    # same Laravel config
}
```

---

## Claude Code Prompts for Implementation

### Prompt 1 — Phase 1 (Run first)
```
Read the entire Laravel project at /var/www/gplmschool.
Create the following:

1. Migration: create_schools_table (with all columns from SAAS_MIGRATION_PLAN.md)
2. Migration: add_school_id_to_all_tables (for all 30+ tables listed)
3. Model: app/Models/School.php with fillable fields
4. Trait: app/Traits/BelongsToSchool.php with global scope + creating hook
5. Apply BelongsToSchool trait to all models: User, Admin, Teacher, Cashier,
   FeeHead, FeePlan, RouteName, Receipt, Concession, SubCode, Exam, Term,
   Classwork, Attendance, Holiday, FlashNews, DeviceToken, ResultPerforma,
   ResultPerformaItem, ResultTerm, ResultComponent, StudentResultItem,
   StudentExamEntry, ResultCoScholasticArea, ResultFinalization
6. Middleware: app/Http/Middleware/TenantMiddleware.php
7. Register TenantMiddleware in app/Http/Kernel.php as 'tenant'
8. Update routes/web.php to wrap all routes in tenant middleware group

After creating files, run: php artisan migrate --pretend
to verify no errors.
```

### Prompt 2 — Phase 2 (After Phase 1)
```
Read app/Http/Controllers/Fee/StudentFeeController.php.
Update Razorpay initialization to use per-tenant keys from app('school') model.
Also create app/Helpers/StorageHelper.php with tenantStoragePath() function.
Update all Storage::put() and Storage::get() calls in the codebase to use
tenantStoragePath() prefix.
Find all hardcoded "GPLM" or "gplmschool" references in resources/views/ and
replace with dynamic {{ app('school')->name }} or {{ app('school')->logo_path }}.
```

### Prompt 3 — Phase 3 (Super Admin)
```
Create a complete Super Admin panel for the SaaS:
1. routes/superadmin.php with resource routes
2. app/Http/Controllers/SuperAdmin/SchoolController.php
   - index() → list all schools with stats
   - create() / store() → onboard new school (creates school + first admin)
   - edit() / update() → manage school settings
   - activate() / suspend() → toggle school status
3. resources/views/superadmin/ → dashboard, schools/index, schools/create
4. Model: app/Models/SuperAdmin.php with its own guard
5. Guard 'superadmin' in config/auth.php
6. Login page at /superadmin/login

The super admin panel should be accessible ONLY at the main domain
(schoolportal.in) and NOT through any school subdomain.
```

---

## 6-Week Timeline

| Week | Phase | Deliverable |
|------|-------|-------------|
| Week 1 | Phase 1 | school_id columns, School model, Global Scopes, Middleware |
| Week 2 | Phase 1 | Data migration, GPLM data to school_id=1, Testing |
| Week 3 | Phase 2 | Per-tenant Razorpay, S3 paths, Branding, Mail |
| Week 4 | Phase 3 | Super Admin panel, Onboarding flow, DNS/SSL setup |
| Week 5 | Phase 5 | API multi-tenancy, Mobile app school-selection screen |
| Week 6 | Testing | Add 2nd school (sdbmsss), end-to-end test, go live |

---

## Quick Wins (Do These NOW Before Full Migration)

1. **`APP_DEBUG=false`** — Security risk, fix immediately in `.env`
2. **Regenerate exposed API keys** — Razorpay test key visible in analysis
3. **Remove `serverless.yml`** — Outdated, misleading
4. **Remove `android.keystore` from repo** — Should NEVER be in version control

---

*This plan is based on SAAS_ANALYSIS.md generated from the live codebase.*
*Total effort estimate: 5–6 weeks solo development with Claude Code assistance.*
