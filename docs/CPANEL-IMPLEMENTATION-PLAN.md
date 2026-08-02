# cPanel Implementation Plan

Tanggal plan: 2026-08-02

## Detected Architecture

- Laravel 13 monolith
- Breeze-based auth
- Custom admin panel under `/admin`
- Blade + Alpine + Vite + Tailwind 4
- Custom PWA manifest and service worker
- Root route currently serves Laravel welcome page
- No existing split-domain deployment assets

## Scope To Implement

Per prompt, implementation will focus on deployment and infrastructure with minimal functional change:

1. Add static main-domain landing page under `deployment/main-domain/`
2. Add Laravel `/install` route and page
3. Audit and safely correct manifest/service-worker/deployment-facing PWA details
4. Add deployment-specific `deployment/app-public/index.php`
5. Add deployment-specific `deployment/app-public/.htaccess`
6. Add cPanel deployment and verification scripts
7. Add deployment documentation

## Existing Files That Must Be Changed

### 1. [`routes/web.php`](D:/Downloads/sidedikk-pwa/routes/web.php)

- Reason: add `GET /install`
- Preserved behavior:
  - existing route names remain unchanged
  - existing auth, screening, admin, PWA asset routes remain unchanged
- Minimal change:
  - add one new route only

### 2. [`resources/js/app.js`](D:/Downloads/sidedikk-pwa/resources/js/app.js)

- Reason: support dedicated `/install` page install UX without duplicating fragile listener logic
- Preserved behavior:
  - existing welcome/install prompt behavior should continue working
  - admin charts initialization must remain unchanged
- Minimal change:
  - extend current PWA handler rather than replace app JS architecture

### 3. [`public/manifest.webmanifest`](D:/Downloads/sidedikk-pwa/public/manifest.webmanifest)

- Reason: align manifest with deployment target and required fields
- Preserved behavior:
  - app identity remains SIDEDIKK
  - existing icon paths remain preferred if valid
- Minimal change:
  - only manifest metadata corrections

### 4. [`public/sw.js`](D:/Downloads/sidedikk-pwa/public/sw.js)

- Reason: verify and adjust cache behavior or asset list only if needed for deployment/privacy correctness
- Preserved behavior:
  - no caching of sensitive authenticated routes
  - static asset caching remains lightweight
- Minimal change:
  - only privacy-safe and deployment-safe corrections

## New Files To Create

### Deployment Documentation

- `docs/CPANEL-DEPLOYMENT.md`
- `docs/CPANEL-ENVIRONMENT.md`
- `docs/DATABASE-DEPLOYMENT.md`
- `docs/PWA-DEPLOYMENT.md`
- `docs/POST-DEPLOYMENT-TEST-RESULTS.md`

### Static Main Domain

- `deployment/main-domain/index.html`
- `deployment/main-domain/privacy.html`
- `deployment/main-domain/assets/css/app.css`
- `deployment/main-domain/assets/js/app.js`
- `deployment/main-domain/assets/images/` assets reused/exported from existing approved branding where possible

### App Public Deployment Copies

- `deployment/app-public/index.php`
- `deployment/app-public/.htaccess`

### Scripts

- `deployment/scripts/deploy-cpanel.sh`
- `deployment/scripts/verify-production.sh`

### Laravel Install Page

- new Blade view for `/install`, using existing layout/components where possible

## Preserved Behavior Commitments

- No route rename
- No database column rename
- No migration rewrite
- No scoring/risk logic changes
- No auth replacement
- No admin replacement
- No broad UI redesign of approved application screens
- No business-logic refactor purely for style

## Deployment Risks

- Shared hosting may not support symlinks consistently
- Queue worker availability may vary on cPanel hosting
- Main static domain and app subdomain are different origins, so install prompt cannot originate from main domain
- Production `.env` differences may affect session, cache, DB, and mail behavior
- Existing baseline Pint failure may remain unless explicitly cleaned up as a non-functional formatting fix

## Rollback Approach

- Public app files in `public_html/app` should be backed up before synchronization
- Static main-domain files should be backed up independently from `public_html/app`
- Deployment script must avoid destructive sync by default
- Original repository `public/index.php` remains untouched
- Existing database schema remains untouched unless operator explicitly runs forward-only migrations

## Testing Approach

Before implementation:

- use recorded baseline results

After implementation:

- rerun applicable baseline checks
- add focused regression tests for `/install` and public PWA assets if needed
- confirm main-domain static assets build-independent
- confirm Laravel routes still pass existing feature tests

## Manual cPanel Steps Expected

- create `app.sidedikk.my.id`
- point document root to `public_html/app`
- keep Laravel source outside public_html
- configure `.env` under `/home/ewyjotxg/sidedikk`
- install Composer deps on server
- build frontend assets on server or upload built assets
- sync `public/` contents to `public_html/app`
- sync static landing files to `public_html/` excluding `app/`
- verify symlink or safe storage alternative
- run safe migration and optimization commands

## Blockers

No high-risk blocker currently prevents implementation.

Known caution:

- baseline style failure exists before work starts and must not be confused with deployment regressions.
