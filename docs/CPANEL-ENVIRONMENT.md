# cPanel Environment Variables

Tanggal: 2026-08-02

Dokumen ini hanya berisi template aman. Jangan masukkan secret asli ke repository.

## Lokasi `.env`

```bash
/home/ewyjotxg/sidedikk/.env
```

`.env` harus tetap berada di luar `public_html`.

## Nilai Minimum Production

```dotenv
APP_NAME=SIDEDIKK
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.sidedikk.my.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ewyjotxg_sidedikk
DB_USERNAME=ewyjotxg_user
DB_PASSWORD='REPLACE_WITH_SECRET'

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_FROM_ADDRESS="noreply@sidedikk.my.id"
MAIL_FROM_NAME="${APP_NAME}"
```

## Important Rules

- Jangan tampilkan `APP_KEY`
- Jangan ubah `APP_KEY` di production tanpa rencana rotasi yang valid
- Jangan commit `.env`
- Jangan taruh `.env` di `public_html`
- Jangan set `APP_DEBUG=true` di production
- Jangan set `SESSION_DOMAIN=.sidedikk.my.id` kecuali memang butuh auth lintas subdomain

## Variabel Opsional

Redis atau AWS hanya perlu bila benar-benar dipakai oleh deployment final.

Seeder flags production sebaiknya nonaktif:

```dotenv
SIDEDIKK_SEED_DEMO_USER=false
SIDEDIKK_SEED_ADMIN=false
```

## Quoting Values

Gunakan quote bila value mengandung:

- `#`
- spasi
- `$`
- tanda kutip
- karakter shell khusus

Contoh aman:

```dotenv
DB_PASSWORD='P@ss#word$2026'
```

## Setelah Mengubah `.env`

```bash
php artisan optimize:clear
php artisan optimize
```
