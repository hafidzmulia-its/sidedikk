# cPanel Deployment Guide

Tanggal: 2026-08-02

Panduan ini menyiapkan arsitektur:

- main domain statis: `https://sidedikk.my.id`
- Laravel app + PWA: `https://app.sidedikk.my.id`

## Arsitektur Target

- Laravel source: `/home/ewyjotxg/sidedikk`
- Main domain document root: `/home/ewyjotxg/public_html`
- App subdomain document root: `/home/ewyjotxg/public_html/app`

Jangan memindahkan seluruh project Laravel ke `public_html`.

## Langkah 1. Buat Subdomain `app.sidedikk.my.id`

1. Buka cPanel atau DirectAdmin.
2. Buat subdomain `app.sidedikk.my.id`.
3. Pastikan document root diarahkan ke:
   - `/home/ewyjotxg/public_html/app`
4. Pastikan tidak memakai shared document root dengan main domain.

## Langkah 2. Aktifkan SSL

1. Aktifkan SSL untuk:
   - `sidedikk.my.id`
   - `app.sidedikk.my.id`
2. Verifikasi keduanya dapat diakses via HTTPS.

## Langkah 3. Pilih PHP Yang Benar

1. Pilih PHP `8.3` atau lebih baru.
2. Verifikasi CLI:

```bash
php -v
php artisan --version
```

## Langkah 4. Verifikasi Ekstensi PHP

Minimal yang perlu aktif:

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

Verifikasi:

```bash
php -m
```

## Langkah 5. Siapkan Source Laravel

Letakkan source aplikasi di:

```bash
/home/ewyjotxg/sidedikk
```

Verifikasi file penting:

```bash
cd /home/ewyjotxg/sidedikk
ls
test -f artisan && echo artisan-ok
test -f composer.json && echo composer-ok
test -f public/index.php && echo public-index-ok
```

## Langkah 6. Buat Database MySQL

1. Buat database, misalnya:
   - `ewyjotxg_sidedikk`
2. Buat user database, misalnya:
   - `ewyjotxg_user`
3. Assign user ke database
4. Beri privilege yang dibutuhkan

Lihat detail aman di [`DATABASE-DEPLOYMENT.md`](D:/Downloads/sidedikk-pwa/docs/DATABASE-DEPLOYMENT.md).

## Langkah 7. Konfigurasi `.env` Dengan Aman

Edit:

```bash
/home/ewyjotxg/sidedikk/.env
```

Jangan pindahkan `.env` ke `public_html`.

Minimum production values:

```dotenv
APP_NAME=SIDEDIKK
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.sidedikk.my.id
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ewyjotxg_sidedikk
DB_USERNAME=ewyjotxg_user
DB_PASSWORD='REPLACE_WITH_SECRET'
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Lihat detail lengkap di [`CPANEL-ENVIRONMENT.md`](D:/Downloads/sidedikk-pwa/docs/CPANEL-ENVIRONMENT.md).

## Langkah 8. Test Kredensial MySQL

```bash
php artisan optimize:clear
php artisan migrate:status
```

Kalau perlu, test koneksi:

```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'ok';"
```

## Langkah 9. Install Dependency Composer

Jalankan dari root Laravel:

```bash
cd /home/ewyjotxg/sidedikk
composer install --no-dev --optimize-autoloader --no-interaction
```

## Langkah 10. Build Frontend Production Assets

Jika Node dan npm tersedia di server:

```bash
npm install
npm run build
```

Jika build dilakukan lokal, upload hasil `public/build/` yang sesuai.

## Langkah 11. Siapkan Public Index Deployment

Jangan overwrite repo `public/index.php`.

Gunakan deployment copy:

```bash
deployment/app-public/index.php
deployment/app-public/.htaccess
```

Copy ke:

```bash
/home/ewyjotxg/public_html/app/index.php
/home/ewyjotxg/public_html/app/.htaccess
```

## Langkah 12. Sinkronisasi Public Laravel Files

Sinkronisasi hanya isi `public/` ke document root app:

```bash
rsync -av /home/ewyjotxg/sidedikk/public/ /home/ewyjotxg/public_html/app/
cp /home/ewyjotxg/sidedikk/deployment/app-public/index.php /home/ewyjotxg/public_html/app/index.php
cp /home/ewyjotxg/sidedikk/deployment/app-public/.htaccess /home/ewyjotxg/public_html/app/.htaccess
```

Jangan copy:

- `.env`
- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `resources/`
- `routes/`
- `storage/framework/`
- `tests/`

## Langkah 13. Deploy Static Main Domain

Sinkronkan isi:

```bash
deployment/main-domain/
```

ke:

```bash
/home/ewyjotxg/public_html/
```

Tanpa menghapus subdirectory `app/`.

Contoh aman:

```bash
rsync -av --exclude app/ /home/ewyjotxg/sidedikk/deployment/main-domain/ /home/ewyjotxg/public_html/
```

## Langkah 14. Storage Link

Jika aplikasi memakai public disk:

```bash
php artisan storage:link
```

Verifikasi hasil symlink di:

```bash
/home/ewyjotxg/public_html/app/storage
```

## Langkah 15. Permission Aman

Writable hanya untuk:

- `storage/`
- `bootstrap/cache/`

Hindari `chmod 777`.

Contoh aman:

```bash
chmod -R u+rwX,go-rwx storage bootstrap/cache
```

Sesuaikan dengan owner/group hosting.

## Langkah 16. Backup Database Sebelum Migrasi

Sebelum migration production:

1. backup database via phpMyAdmin atau CLI
2. verifikasi file backup bisa dipulihkan

Lalu cek status:

```bash
php artisan migrate:status
```

Jika sudah diverifikasi aman:

```bash
php artisan migrate --force
```

Jangan pernah pakai:

- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan db:wipe`

## Langkah 17. Optimasi Laravel

```bash
php artisan optimize:clear
php artisan optimize
```

## Langkah 18. Verifikasi URL Penting

```bash
https://sidedikk.my.id
https://sidedikk.my.id/privacy.html
https://app.sidedikk.my.id/
https://app.sidedikk.my.id/login
https://app.sidedikk.my.id/register
https://app.sidedikk.my.id/install
https://app.sidedikk.my.id/manifest.webmanifest
https://app.sidedikk.my.id/sw.js
https://app.sidedikk.my.id/up
```

## Langkah 19. Smoke Test Aplikasi

1. Login user
2. Akses dashboard
3. Akses profile
4. Jalankan screening
5. Lihat hasil dan history
6. Akses edukasi
7. Login admin
8. Akses panel admin

## Langkah 20. Periksa Log Laravel

Pastikan log tidak terekspos via web dan periksa error terbaru di server:

```bash
tail -n 100 /home/ewyjotxg/sidedikk/storage/logs/laravel.log
```

## Langkah 21. Rollback Public Files

Jika deploy public app gagal:

1. restore backup `public_html/app`
2. restore landing static bila perlu
3. jangan ubah `.env` tanpa kebutuhan
4. jangan rollback database tanpa backup yang valid
