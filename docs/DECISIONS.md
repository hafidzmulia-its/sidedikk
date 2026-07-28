# SIDEDIKK Decisions Log

## Initial Audit Decisions

### 2026-07-25 - Use `docs/PRD-SIDEDIKK-EN.md` as the implementation source of truth

- Reason: The repository contains `docs/PRD-SIDEDIKK-EN.md`, and it is complete and internally consistent with the project brief.
- Consequence: Product, architecture, and test expectations will follow this PRD unless the client issues a newer explicit revision.

### 2026-07-25 - Treat the instrument PDF as client-supplied questionnaire input, not developer-invented medical data

- Reason: `docs/INSTRUMEN DAN MOCKUP SIDEDIKK-2-1.pdf` contains a concrete 20-question instrument and score thresholds supplied by the client.
- Consequence: The engine may be built around these values, but production readiness still depends on explicit medical/content approval and final recommendations/disclaimer text.

### 2026-07-25 - Use Bahasa Indonesia for end-user UI copy and English for code/documentation

- Reason: This is explicitly required by the PRD.
- Consequence: User-visible text, validation messages, labels, navigation, and status messages will be Indonesian.

### 2026-07-25 - Use a fresh Laravel project because the current workspace contains no application scaffold

- Reason: The workspace currently contains only documents and image assets.
- Consequence: Initialization must preserve the existing `docs/` directory and avoid overwriting the source materials.

### 2026-07-25 - Stop before deeper feature implementation because Laravel 13 and Filament 5 conflict

- Reason: Actual Composer resolution on Saturday, July 25, 2026 succeeded for Laravel `v13.22.0`, but failed for `filament/filament v5.0.0` because Filament 5 currently requires `illuminate/contracts ^11.28|^12.0`.
- Consequence: Continuing toward the PRD target would require a major-version deviation or a different admin-panel strategy. Per the implementation rules, work should stop at this blocker until the target stack is revised or approved.

### 2026-07-25 - Replace Filament with a Laravel-native custom admin panel

- Reason: The client approved option 2 after the Laravel 13 and Filament 5 compatibility conflict was confirmed.
- Consequence: Administrator features will be implemented with Laravel controllers, Blade views, policies, scoped queries, and Alpine where helpful, while matching the PRD feature scope.

### 2026-07-25 - Keep MySQL/MariaDB as the target runtime, allow SQLite only for tests if needed

- Reason: The PRD locks MySQL 8 or compatible MariaDB as the deployment target, while local database server availability has not yet been proven.
- Consequence: The app configuration and deployment docs will target MySQL/MariaDB. SQLite may be used only as a test convenience if it does not weaken application behavior coverage.

### 2026-07-25 - Use the provided article PNG files as seeded education cover assets

- Reason: The client has already supplied three article card images in `docs/artikel/`.
- Consequence: The MVP can ship with seeded demo education entries using these cover images plus short supporting excerpts, while long-form content remains editable from admin.

### 2026-07-25 - Do not derive official logo assets from screenshots

- Reason: The PRD explicitly forbids tracing or redrawing the logo from a screenshot.
- Consequence: A neutral replaceable wordmark placeholder should be used until official source logo assets are provided.

### 2026-07-25 - Use HPHT as the pregnancy-age source input

- Reason: The client explicitly requested pregnancy age on the home dashboard to be calculated from `HPHT` entered during registration.
- Consequence: Registration and profile editing will store `hpht_date` as the authoritative reference date, and pregnancy age will be calculated server-side and snapshotted for completed screenings.

### 2026-07-25 - Use Chart.js as the custom admin dashboard chart library

- Reason: Filament was replaced by a Laravel-native admin panel, but the PRD still requires charted admin reporting.
- Consequence: The admin dashboard uses locally bundled `chart.js` through Vite, without external CDN dependencies.

### 2026-07-25 - Use neutral generated placeholder PWA icons until official brand assets are supplied

- Reason: The PRD forbids tracing the logo from screenshots, while the PWA manifest still requires valid icon assets in the repository.
- Consequence: `public/brand/icon-192.png` and `public/brand/icon-512.png` are replaceable neutral placeholders and must be swapped with approved brand assets later.

## Open Technical Decisions

### Resolved - Admin account bootstrap mechanism

- Decision: environment-aware artisan command with explicit arguments and environment fallback.
- Constraint satisfied: credentials are never hardcoded or reported in source control.

### Resolved - Exact authentication starter

- Decision: Laravel Breeze (Blade).
- Constraint satisfied: it remains on the official Laravel path and fits shared-hosting simplicity.

### Pending - Livewire usage extent

- Candidate options: no Livewire in user-facing flows, limited use in admin publishing/editing workflows only.
- Constraint: use Livewire 4 only where it reduces complexity clearly.

## Mandatory Production Blocks

- Final privacy text missing
- Final consent text missing
- Final medical disclaimer approval missing
- Final risk descriptions/recommendations missing
- Final logo assets missing
- Final domain decision pending
- Responsible medical approver not documented
