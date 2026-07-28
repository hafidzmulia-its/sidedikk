# SIDEDIKK Load Test

## Objective

- Exercise the main authenticated user flow with approximately `20` virtual users.
- Observe `p50`, `p95`, and `error rate` for the current Laravel monolith target.
- Avoid claiming production visitor capacity from a local synthetic run.

## Scenario

1. Open `/login`.
2. Submit user credentials.
3. Load `/dashboard`.
4. Start screening.
5. Continue through screening pages until review and submit.
6. Open `/history`.

## Script

- K6 scenario file: `tests/Performance/sidedikk-main-flow.k6.js`
- Expected environment variables:
  - `BASE_URL`
  - `USER_EMAIL`
  - `USER_PASSWORD`
  - optional `VUS`
  - optional `DURATION`

## Example Command

```bash
k6 run tests/Performance/sidedikk-main-flow.k6.js
```

## Current Status

- Script scaffold is present in the repository.
- Full load execution has **not** been run in this workspace yet because `k6` availability has not been verified here.
- Before running against a real MySQL-backed environment, prepare:
  - a non-production test account
  - a seeded published questionnaire
  - a seeded published risk-rule version
  - a test database backup if using non-disposable data

## Metrics To Report

- `p50` request duration
- `p95` request duration
- `http_req_failed` rate
- visible bottlenecks:
  - PHP worker saturation
  - MySQL query latency
  - session or cache lock contention
  - large response payloads
