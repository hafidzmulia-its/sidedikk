# Baseline Test Results

Tanggal baseline: 2026-08-02

## Environment Notes

- Workspace lokal: `D:\Downloads\sidedikk-pwa`
- PHP CLI: `8.3.22`
- Composer CLI: `2.8.9`
- Node CLI: `22.16.0`
- `npm` via PowerShell alias gagal karena execution policy; baseline command menggunakan `npm.cmd`

## Commands Run

### 1. `composer validate`

- Ran: yes
- Result: PASS
- Output summary: `./composer.json is valid`

### 2. `php artisan about`

- Ran: yes
- Result: PASS
- Output summary:
  - Laravel `13.22.0`
  - PHP `8.3.22`
  - Environment `local`
  - Debug `ENABLED`
  - Database driver `mysql`
  - Cache `database`
  - Queue `database`
  - Session `database`
  - `public/storage` linked

### 3. `php artisan route:list`

- Ran: yes
- Result: PASS
- Output summary:
  - 59 routes discovered
  - Auth, dashboard, screening, history, education, admin, manifest, sw, offline, and health routes registered

### 4. `php artisan test`

- Ran: yes
- Result: PASS
- Output summary:
  - 55 tests passed
  - 248 assertions
  - duration ~57.7s

### 5. `vendor\bin\pint --test`

- Ran: yes
- Result: FAIL
- Classification: pre-existing baseline failure
- Output summary:
  - `app\Http\Controllers\Admin\DashboardController.php`
  - fixer: `no_trailing_whitespace`

### 6. `npm.cmd run build`

- Ran: yes
- Result: PASS
- Output summary:
  - Vite build succeeded
  - manifest emitted
  - CSS and JS bundles emitted under `public/build/assets`

## Commands Not Run

### `npm ci`

- Ran: no
- Reason:
  - baseline verification did not require reinstalling dependencies
  - `node_modules` already present
  - this task is deployment preparation, not dependency refresh
  - avoiding unnecessary network/dependency churn keeps baseline focused on repository behavior

## Baseline Status Summary

- Functional tests: passing
- Production frontend build: passing
- Composer manifest validation: passing
- Route/app introspection: passing
- Style check: failing before deployment work begins

## Existing Failures That Must Not Be Misclassified As Regressions

- `vendor\bin\pint --test` fails due to existing trailing whitespace in:
  - [`app/Http/Controllers/Admin/DashboardController.php`](D:/Downloads/sidedikk-pwa/app/Http/Controllers/Admin/DashboardController.php)

This must be tracked separately from any regressions introduced by deployment changes.
