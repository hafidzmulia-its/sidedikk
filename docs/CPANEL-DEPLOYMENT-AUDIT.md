# CPANEL Deployment Audit

Tanggal audit: 2026-08-02  
Workspace: `D:\Downloads\sidedikk-pwa`

## Kondisi Repository

- Git branch: `main`
- Git status: clean (`git -c core.excludesfile=.gitignore status --short --branch`)
- Repo sudah berupa aplikasi Laravel yang berjalan, bukan skeleton kosong.
- Sudah ada implementasi auth, dashboard user, admin panel custom, screening, PWA assets, tests, build assets, dan dokumentasi internal sebelumnya.

## Detected Stack And Versions

- PHP requirement (composer): `^8.3`
- PHP CLI lokal: `8.3.22`
- Laravel requirement (composer): `^13.8`
- Laravel installed (artisan): `13.22.0`
- Composer CLI lokal: `2.8.9`
- Node.js CLI lokal: `v22.16.0`
- npm CLI via `npm.cmd`: tersedia, versi inline call gagal via `npm.ps1` karena execution policy PowerShell
- Frontend build tool: Vite
- Vite installed: `8.1.5`
- Tailwind CSS installed: `4.3.3`
- `@tailwindcss/vite` installed: `4.3.3`
- Alpine.js installed: `3.15.12`
- Chart.js installed: `4.5.1`
- `laravel-vite-plugin` installed: `3.1.3`
- Auth scaffolding package: `laravel/breeze` `2.4.2`
- Testing: `phpunit/phpunit` `12.5.31`
- Code style: `laravel/pint` `1.29.3`
- Faker: `fakerphp/faker` `1.24.1`
- Tinker: `laravel/tinker` `3.0.2`

## Architecture Discovered

### Application Runtime

- Monolith Laravel 13 application.
- Entry point saat ini: [`public/index.php`](D:/Downloads/sidedikk-pwa/public/index.php)
- Health endpoint tersedia di `/up`.
- Default route `/` masih Laravel view `welcome`.
- PWA served dari origin aplikasi yang sama, bukan split-domain deployment.

### Authentication And Authorization

- Auth routes berada di [`routes/auth.php`](D:/Downloads/sidedikk-pwa/routes/auth.php)
- Auth implementation menggunakan controller Breeze standard, bukan Fortify standalone, WorkOS, atau provider eksternal.
- Email verification aktif (`MustVerifyEmail` pada [`User.php`](D:/Downloads/sidedikk-pwa/app/Models/User.php)).
- Authorization custom:
  - middleware alias `admin`
  - middleware alias `no-store`
  - policy: [`ScreeningPolicy.php`](D:/Downloads/sidedikk-pwa/app/Policies/ScreeningPolicy.php)

### Admin Panel

- Tidak ditemukan Filament.
- Tidak ditemukan Livewire.
- Admin panel adalah panel custom berbasis route/controller/view sendiri di prefix `/admin`.

### Domain Model

Model yang terdeteksi:

- `User`
- `Screening`
- `ScreeningAnswer`
- `QuestionnaireVersion`
- `Question`
- `RiskRuleVersion`
- `RiskLevel`
- `EducationPost`
- `AuditLog`

### Database

- Default configured driver: `mysql` pada environment lokal aktual (`php artisan about`)
- Config default fallback: `sqlite` di [`config/database.php`](D:/Downloads/sidedikk-pwa/config/database.php)
- MySQL dan MariaDB keduanya dikonfigurasi.
- Migration files:
  - Laravel defaults: users, cache, jobs
  - domain migration: `2026_07_25_092000_create_sidedikk_domain_tables.php`

### Storage / Cache / Session / Queue

- Cache default: `database`
- Session default: `database`
- Queue default: `database`
- Filesystem default: `local`
- Public disk configured via `storage/app/public`
- `storage:link` dibutuhkan dan saat ini lokal sudah linked (`public/storage`)
- Writable directories yang relevan untuk production:
  - `storage/`
  - `bootstrap/cache/`

### Scheduler / Queue Worker

- Tidak ditemukan schedule aplikasi custom pada [`routes/console.php`](D:/Downloads/sidedikk-pwa/routes/console.php)
- Queue backend default adalah `database`, sehingga background worker hanya dibutuhkan bila production benar-benar memakai queue async
- Tidak ada bukti scheduler business-critical saat audit ini

## PWA Audit

### Current Files

- Manifest: [`public/manifest.webmanifest`](D:/Downloads/sidedikk-pwa/public/manifest.webmanifest)
- Service worker: [`public/sw.js`](D:/Downloads/sidedikk-pwa/public/sw.js)
- Offline page: [`public/offline.html`](D:/Downloads/sidedikk-pwa/public/offline.html)
- Icons:
  - [`public/brand/icon-192.png`](D:/Downloads/sidedikk-pwa/public/brand/icon-192.png)
  - [`public/brand/icon-512.png`](D:/Downloads/sidedikk-pwa/public/brand/icon-512.png)

### Current PWA Behavior

- SW caches only static assets cache-first.
- Non-sensitive GET pages use network-first fallback to cache.
- Sensitive routes (`/dashboard`, `/admin`, `/screenings`, `/history`, `/profile`, `/education`, auth flows) are fetched network-only via direct `fetch(request)`.
- This is broadly aligned with privacy expectations for authenticated healthcare data.

### PWA Gaps Against Requested Deployment

- No dedicated Laravel `/install` route yet.
- Root route `/` is still Laravel app landing, not split static main-domain landing page.
- Manifest theme color currently `#9C36B5`; requested deployment identity centers `#95409E`.
- Manifest does not currently declare explicit `id`.
- Existing install UX lives in shared JS and current welcome page, not a dedicated install page.

## Public Index / Apache Audit

- Current public index is standard Laravel 13 entrypoint with `handleRequest`.
- Current `.htaccess` is standard Laravel front-controller rewrite.
- For cPanel split-domain deployment, a deployment-specific copy is required instead of modifying the repo original.

## Existing Routes Relevant To Deployment

Important existing routes:

- `/` -> welcome page
- `/login`
- `/register`
- `/dashboard`
- `/history`
- `/education`
- `/screenings/...`
- `/admin/...`
- `/manifest.webmanifest`
- `/sw.js`
- `/offline.html`
- `/up`

Missing route requested by deployment prompt:

- `/install`

## Existing Frontend Build

- Vite inputs:
  - `resources/css/app.css`
  - `resources/js/app.js`
- No SPA framework introduced.
- App remains Blade + Alpine + vanilla browser APIs.

## Environment Variables And Production-Relevant Inputs

Observed from `.env.example` and config usage:

- Core app:
  - `APP_NAME`
  - `APP_ENV`
  - `APP_KEY`
  - `APP_DEBUG`
  - `APP_URL`
- Locale:
  - `APP_LOCALE`
  - `APP_FALLBACK_LOCALE`
  - `APP_FAKER_LOCALE`
- DB:
  - `DB_CONNECTION`
  - `DB_HOST`
  - `DB_PORT`
  - `DB_DATABASE`
  - `DB_USERNAME`
  - `DB_PASSWORD`
- Session:
  - `SESSION_DRIVER`
  - `SESSION_LIFETIME`
  - `SESSION_ENCRYPT`
  - `SESSION_PATH`
  - `SESSION_DOMAIN`
  - `SESSION_SECURE_COOKIE`
  - `SESSION_HTTP_ONLY`
  - `SESSION_SAME_SITE`
- Queue/cache/filesystem:
  - `QUEUE_CONNECTION`
  - `CACHE_STORE`
  - `FILESYSTEM_DISK`
- Mail:
  - `MAIL_*`
- Optional Redis / AWS
- Seeder toggles:
  - `SIDEDIKK_SEED_DEMO_USER`
  - `SIDEDIKK_SEED_ADMIN`
  - `SIDEDIKK_ADMIN_*`

## Required PHP Extensions Observed Locally

Installed locally and relevant:

- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `gd`
- `intl`
- `json`
- `mbstring`
- `openssl`
- `pdo_mysql`
- `pdo_sqlite`
- `session`
- `tokenizer`
- `xml`
- `zip`

Production minimum should also keep:

- `opcache`
- `mysqli` or `pdo_mysql`

## Shared Hosting Compatibility Notes

Compatible aspects:

- Laravel 13 on PHP 8.3+
- MySQL/MariaDB compatible config present
- No Redis hard requirement
- No SPA build server needed in production
- Public assets already Vite-built
- Admin panel custom, so no Filament hosting constraint

Risks / caveats:

- Database queue on shared hosting may need explicit worker strategy or sync fallback if async jobs are expected.
- `APP_ENV` and `APP_DEBUG` are local-safe right now but must be hardened in production.
- Split-domain architecture is not yet implemented.
- Static main domain landing page does not exist yet.
- `/install` route and dedicated install UX do not exist yet.

## Security Findings During Audit

Positive:

- Security headers middleware exists.
- Sensitive-response cache prevention middleware exists.
- Auth and admin routes protected by middleware.
- Screening policy exists.
- PWA service worker avoids cache-first on sensitive authenticated paths.

Risks / gaps:

- Local environment currently reports `APP_DEBUG=true` and `APP_ENV=local`; production docs must force `APP_DEBUG=false`.
- `public/index.php` is still standard same-directory Laravel layout; split-domain deployment copy must be prepared carefully.
- No deployment verification scripts yet.

## Deployment Blockers

No hard blocker found for preparing deployment assets and documentation.

Current non-blocking gaps:

- Missing `deployment/` structure
- Missing static main-domain landing page
- Missing Laravel `/install` page
- Missing cPanel deployment docs
- Missing verification and deploy scripts
- Existing baseline style failure in Pint (pre-existing)

## Conclusion

Repository is suitable for a deployment-focused minimal-diff implementation for:

- split-domain cPanel deployment
- static public main domain
- Laravel app under subdomain document root
- dedicated install page
- deployment-specific `index.php` and `.htaccess`
- deployment and verification scripts

No evidence was found that a framework rewrite, auth replacement, admin replacement, or business-logic refactor is necessary.
