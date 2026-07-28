# SIDEDIKK Implementation Plan

## 0. Delivery Status Snapshot

- Audit, planning documents, and Laravel 13 foundation are complete.
- Phase 1 (foundation) is complete and verified.
- Phase 2 (user-facing auth, profile, dashboard, education, history list) is complete and verified.
- Phase 3 (screening flow, version locking, immutable results, result/history detail, and phase tests) is complete and verified.
- Phase 4 (custom administrator panel replacing Filament) is complete and verified.
- Phase 5 (PWA manifest, service worker, offline fallback, placeholder brand icons, and safe cache policy) is complete and verified.
- Remaining work:
  - Phase 6 hardening, export polish, rate limiting, headers, and load-test documentation/execution
  - Phase 7 shared-hosting deployment, backup/restore, admin guide, and security checklist documentation
- Production readiness remains blocked by unapproved medical/disclaimer/privacy content and incomplete later phases.

## 1. Current Repository Condition

- Repository contents currently include only source documents and article image assets under `docs/`.
- There is no Laravel application scaffold yet: no `composer.json`, `artisan`, `app/`, `routes/`, `database/`, `resources/`, or `tests/`.
- The working directory is not currently a Git working tree. `git status --short --branch` failed with `fatal: not a git repository`.
- Existing source-of-truth inputs found:
  - `docs/PRD-SIDEDIKK-EN.md`
  - `docs/INSTRUMEN DAN MOCKUP SIDEDIKK-2-1.pdf`
  - `docs/artikel/artikel-1.png`
  - `docs/artikel/artikel-2.png`
  - `docs/artikel/artikel-3.png`

## 2. Environment Audit

### 2.1 Runtime and Tooling

- PHP: `8.3.22`
- Composer: `2.8.9`
- Node.js: `v22.16.0`
- `npm -v` via PowerShell script shim failed because PowerShell script execution is disabled. `npm.cmd` should be used instead of `npm`.
- `php artisan --version` failed because Laravel has not been initialized yet.
- After initialization and dependency install, Laravel resolved successfully to `laravel/framework v13.22.0`.

### 2.2 PHP Extensions Present

Relevant extensions confirmed:

- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `gd`
- `intl`
- `mbstring`
- `openssl`
- `pdo_mysql`
- `session`
- `tokenizer`
- `xml`
- `zip`

Additional drivers also present: `pdo_sqlite`, `pdo_pgsql`, `sqlsrv`.

### 2.3 Database Availability

- PDO MySQL support is available in PHP.
- Local `mysql` and `mariadb` CLI clients were not found in `PATH`.
- No existing application `.env` or database configuration exists yet.
- A running local MySQL or MariaDB server has not yet been verified.

### 2.4 Source Document Findings

#### PRD

The PRD is complete enough to begin architecture and implementation planning. Core locked decisions include:

- Laravel monolith
- Fresh project target: Laravel 13, PHP 8.3+
- MySQL 8 or compatible MariaDB
- Blade + Tailwind CSS 4 + Alpine.js
- Livewire 4 only when clearly useful
- Filament 5 for admin
- Versioned questionnaire and risk-rule model
- Immutable completed screenings with snapshots
- PWA with strict no-sensitive-cache policy

#### PDF Instrument

The attached PDF contains client-supplied screening content and mockup reference. Text extracted from the PDF shows:

- 20 `Ya`/`Tidak` screening questions
- Positive `Ya` scores ranging from `+2` to `+6`
- Risk categories:
  - `0-4`: `Risiko Rendah`
  - `5-8`: `Risiko Sedang`
  - `9-14`: `Risiko Tinggi`
  - `>=15`: `Risiko Sangat Tinggi`

This is materially important because it provides a client-supplied baseline questionnaire and score mapping. It still needs explicit product/medical approval status to be treated as production-valid content.

#### Article Assets

Three article-card images are available and appear ready to be used as education covers or featured education cards:

- `artikel-1.png`: danger signs in pregnancy
- `artikel-2.png`: pregnancy risk factors
- `artikel-3.png`: routine ANC schedule

No article body copy, slugs, or formal publication metadata are present yet.

## 3. Existing vs PRD Gap Analysis

### 3.1 Present Today

- Product requirements document
- Instrument PDF with question/scoring baseline
- Mockup references
- Three education-card images
- Compatible PHP runtime
- Laravel 13.22.0 base project installed
- Official Laravel Breeze auth scaffolding installed
- Custom Laravel-native admin panel approved to replace Filament

### 3.2 Missing Application Foundation

- Laravel application scaffold
- Dependency lock files
- Authentication
- Database schema
- Policies and middleware
- Services and domain logic
- UI implementation
- Filament admin panel
- PWA assets and service worker
- Tests
- Deployment documentation

### 3.3 Missing Client Inputs

- Final logo asset package
- Final privacy policy text
- Final consent wording
- Final medical disclaimer wording
- Admin operator email
- Final domain choice
- Formal approval of questionnaire and risk rules from responsible medical approver
- Risk descriptions and recommendations for each category
- Article descriptions/body content if full article detail is required

## 4. Proposed Architecture

### 4.1 Application Shape

- Single Laravel 13 monolith
- Blade-driven mobile-first frontend
- Tailwind CSS 4 design tokens aligned to PRD GSM
- Alpine.js for lightweight interactions
- Livewire 4 only for admin workflows or complex draft editors if it clearly reduces complexity
- Laravel-native custom admin panel on a dedicated route group

### 4.2 Domain Layers

Planned structure:

```text
app/
├── Actions/
├── Enums/
├── Filament/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Policies/
├── Services/
│   ├── PregnancyAgeService.php
│   ├── ScreeningService.php
│   ├── RiskClassificationService.php
│   ├── QuestionnairePublishingService.php
│   └── RiskRulePublishingService.php
└── Support/
```

### 4.3 Authentication and Roles

- Laravel official authentication starter path
- `users.role` enum/string with `user` and `admin`
- Dedicated admin access gate and middleware
- Authorization policies and scoped queries for every user-owned resource
- HPHT captured during registration/profile updates, with server-side gestational-age calculation

### 4.4 UI Strategy

- Custom user-facing layout, not stock auth scaffolding
- Bottom navigation for `Beranda`, `Riwayat`, `Edukasi`, `Profil`
- Dashboard card for gestational age based on HPHT-derived reference date
- Education cards using provided PNG assets with supporting Bahasa Indonesia excerpts
- Risk badges with icon + text, never color-only

### 4.5 PWA Strategy

- Standards-based `manifest.webmanifest`
- Service worker with cache allowlist only for fingerprinted assets, offline page, and approved public/static files
- Network-only for authenticated routes, admin routes, screening routes, history, profile, exports, and all non-GET requests

## 5. Proposed Data Model and Indexes

### 5.1 Core Tables

- `users`
- `questionnaire_versions`
- `questions`
- `risk_rule_versions`
- `risk_levels`
- `screenings`
- `screening_answers`
- `education_posts`
- `audit_logs`

### 5.2 Key Table Notes

#### `users`

- Unique `email`
- `role`
- `age`
- `hpht_date`
- `pregnancy_weeks`
- `pregnancy_days`
- `pregnancy_reference_date`
- `privacy_consent_at`
- `medical_disclaimer_consent_at`

Indexes:

- unique(`email`)
- index(`role`)

#### `questionnaire_versions`

- `version_number`
- `status` (`draft`, `published`, `archived`)
- `published_at`
- `published_by`
- `max_score_snapshot`
- demo-data flag until medical approval is complete

Indexes:

- unique partial strategy or application-level guarantee for one published version
- index(`status`)
- index(`published_at`)

#### `questions`

- `questionnaire_version_id`
- `text`
- `help_text`
- `score_yes`
- `score_no`
- `display_order`
- `is_active`

Indexes:

- index(`questionnaire_version_id`, `display_order`)
- index(`questionnaire_version_id`, `is_active`)

#### `risk_rule_versions`

- `version_number`
- `status`
- `published_at`
- `published_by`
- `max_score_covered`
- demo-data flag until medical approval is complete

Indexes:

- index(`status`)
- index(`published_at`)

#### `risk_levels`

- `risk_rule_version_id`
- `name`
- `slug`
- `min_score`
- `max_score`
- `semantic_color`
- `description`
- `recommendation`
- `display_priority`
- `is_active`

Indexes:

- index(`risk_rule_version_id`, `display_priority`)
- index(`risk_rule_version_id`, `min_score`, `max_score`)

#### `screenings`

- `user_id`
- `questionnaire_version_id`
- `risk_rule_version_id`
- `status` (`in_progress`, `completed`, `abandoned`)
- `submission_key`
- `started_at`
- `completed_at`
- `gestational_age_weeks_snapshot`
- `gestational_age_days_snapshot`
- `total_score`
- `max_score`
- `risk_label_snapshot`
- `risk_description_snapshot`
- `recommendation_snapshot`
- `questionnaire_version_name_snapshot`
- `risk_rule_version_name_snapshot`

Indexes:

- unique(`submission_key`)
- index(`user_id`, `status`, `started_at`)
- index(`user_id`, `completed_at`)
- index(`status`, `completed_at`)
- index(`questionnaire_version_id`)
- index(`risk_rule_version_id`)

#### `screening_answers`

- `screening_id`
- `question_id`
- `questionnaire_version_id`
- `question_text_snapshot`
- `selected_answer`
- `awarded_score`
- `display_order_snapshot`

Indexes:

- index(`screening_id`, `display_order_snapshot`)
- index(`questionnaire_version_id`)
- index(`question_id`)

#### `education_posts`

- `title`
- `slug`
- `excerpt`
- `body`
- `cover_image_path`
- `status` (`draft`, `published`, `unpublished`, `archived`)
- `published_at`

Indexes:

- unique(`slug`)
- index(`status`, `published_at`)

#### `audit_logs`

- `actor_id`
- `action`
- `subject_type`
- `subject_id`
- `safe_metadata`
- `ip_hash`
- `created_at`

Indexes:

- index(`actor_id`, `created_at`)
- index(`subject_type`, `subject_id`)
- index(`action`, `created_at`)

## 6. Planned Routes, Controllers, Services, Policies, and Admin Pages

### 6.1 Web Routes

- Guest:
  - `/`
  - `/login`
  - `/register`
  - `/privacy`
  - `/disclaimer`
- Authenticated user:
  - `/dashboard`
  - `/screenings/start`
  - `/screenings/{screening}/questions`
  - `/screenings/{screening}/review`
  - `/screenings/{screening}/result`
  - `/history`
  - `/history/{screening}`
  - `/education`
  - `/education/{post:slug}`
  - `/profile`
- Operational:
  - `/up`
  - `/offline.html`

### 6.2 Controllers / Form Requests

- `Auth\RegisteredUserController`
- `DashboardController`
- `ProfileController`
- `EducationController`
- `ScreeningController`
- `ScreeningReviewController`
- `ScreeningSubmissionController`
- `ScreeningHistoryController`
- `Requests\Auth\RegisterRequest`
- `Requests\ProfileUpdateRequest`
- `Requests\StartScreeningRequest`
- `Requests\SubmitScreeningRequest`

### 6.3 Policies

- `UserPolicy`
- `ScreeningPolicy`
- `EducationPostPolicy`
- `QuestionnaireVersionPolicy`
- `RiskRuleVersionPolicy`
- Admin panel access policy/gate

### 6.4 Services

- `PregnancyAgeService`
- `ScreeningService`
- `RiskClassificationService`
- `QuestionnairePublishingService`
- `RiskRulePublishingService`
- `CsvExportService`
- `AuditLogService`

### 6.5 Admin Pages

- Admin dashboard page with summary widgets and charts
- Custom user management pages
- Custom screening list/detail pages
- Custom questionnaire draft/publish pages
- Custom risk-rule draft/publish pages
- Custom education management pages
- Custom audit-log and export pages

## 7. Planned Tests

### 7.1 Unit

- HPHT or reference-date based gestational age calculation
- Server-side score calculation
- Risk classification
- Risk-range validation
- Questionnaire publication rules
- Risk-rule publication rules

### 7.2 Feature

- Registration with consent validation
- Login/logout and rate limiting
- Role access boundaries
- Ownership and cross-user isolation
- Profile updates
- Questionnaire publication and new-screening version lock
- Risk-rule publication coverage validation
- Required answers
- Browser-submitted scores ignored
- Transaction rollback on failed submit
- Idempotent duplicate submission
- Completed screening immutability
- Snapshot preservation after new publication
- History visibility
- Admin read-only completed screenings
- Admin-only CSV export
- Education publication visibility
- Upload validation
- Manifest and PWA asset availability
- Service worker excludes sensitive routes

## 8. Implementation Phases

### Phase 0 - Audit and Planning

- Complete audit artifacts
- Validate toolchain
- Confirm client-supplied questionnaire/scoring input

### Phase 1 - Foundation

- Initialize Laravel 13 project safely in current workspace
- Configure MySQL-ready environment and SQLite/Pest test support as needed
- Install official auth starter
- Establish the custom Laravel-native admin foundation
- Add roles, middleware, policies, base layouts, and seeders
- Add foundation tests

### Phase 2 - User Features

- Registration, login, logout
- Profile and gestational-age calculation from HPHT reference
- Dashboard with pregnancy-age card
- Education list/detail using supplied article covers
- Responsive mobile UI and bottom navigation

### Phase 3 - Screening Domain

- Versioned questionnaire and risk rules
- In-progress screening flow
- Review page
- Idempotent transactional submission
- Immutable result/history snapshots

### Phase 4 - Administrator Panel

- Restricted custom admin panel
- Widgets/charts
- Read-only completed screening inspection
- Draft/publish workflows
- Education management
- Streamed CSV export
- Audit log

### Phase 5 - PWA

- Manifest
- Icons and placeholder brand assets
- Service worker
- Offline page
- Install prompt
- Manual cache-policy verification

### Phase 6 - Hardening

- Rate limits
- Security headers
- Query optimization and eager loading
- Error pages and accessibility review
- Full test/build sweep
- Load-test script/documentation

### Phase 7 - Deployment and Handover

- Shared-hosting deployment docs
- Backup/restore docs
- Admin guide
- Security checklist
- Load-test report

## 9. Deployment Assumptions

- Shared hosting can point document root to Laravel `public/`
- PHP 8.3+ is available in production
- Required PHP extensions match or exceed local environment
- HTTPS is available
- MySQL 8 or compatible MariaDB is available
- Scheduler/queue worker is not required for MVP core flow
- No Redis dependency is introduced

## 10. Security and Privacy Risks

- No Git repository is currently initialized, so change history and secret-protection defaults are not yet in place.
- Final privacy, disclaimer, and consent text are not yet supplied.
- Final medical approver status is not yet documented.
- Service worker must be implemented carefully to avoid caching authenticated health data.
- Exports must stay minimal and logged.
- Completed screening immutability must be enforced at controller, policy, and persistence layers.

## 11. Performance Risks

- Shared hosting may be sensitive to unoptimized admin dashboards and reporting queries.
- CSV export must stream or chunk to avoid memory spikes.
- Education images should be optimized and served efficiently.
- Screening history and admin tables require strict pagination and indexes from the start.

## 12. Real Blockers

- Local MySQL/MariaDB server availability has not yet been verified.
- The original PRD lock on Filament 5 conflicted with Laravel 13 in this environment, but the client has approved replacing Filament with a custom Laravel-native admin panel.
- Final production medical approval state is absent; production readiness must remain blocked even if technical implementation proceeds.
