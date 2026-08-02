# Database Deployment Guide

Tanggal: 2026-08-02

## Verifikasi Nama Database Dan User

Pastikan nama final di cPanel/DirectAdmin sesuai yang benar-benar dibuat:

- database: contoh `ewyjotxg_sidedikk`
- user: contoh `ewyjotxg_user`

Jangan menebak prefix akun hosting.

## Assign User Ke Database

1. Buat database
2. Buat database user
3. Assign user ke database
4. Beri privilege yang diperlukan

## Password Database

Jika password direset:

1. update `.env`
2. quote value bila ada karakter spesial
3. clear config cache

Contoh:

```bash
php artisan optimize:clear
php artisan optimize
```

## Test Kredensial MySQL

Setelah `.env` diperbarui:

```bash
php artisan tinker --execute="DB::connection()->getPdo(); echo 'ok';"
```

Jika output memuat `ok`, koneksi berhasil.

## Check Migration Status

```bash
php artisan migrate:status
```

## Backup Database Sebelum Migrasi

Sebelum `migrate --force`:

1. backup database via panel atau CLI
2. simpan backup di lokasi non-public
3. pastikan backup bisa direstore

## Production Migration

Jika backup valid dan schema review selesai:

```bash
php artisan migrate --force
```

## Larangan

Jangan pernah gunakan:

```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan db:wipe
```

## Diagnosa Gagal Koneksi

Periksa:

- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- privilege database user
- typo pada `.env`
- quote password yang salah

Lalu clear config:

```bash
php artisan optimize:clear
```

## Diagnosa Gagal Migrasi

Periksa:

- apakah migration lama sudah pernah jalan
- apakah ada perubahan destructive
- apakah DB user punya CREATE/ALTER/INDEX privilege
- apakah ada kolom/table bentrok

## Rollback Considerations

- treat migration lama sebagai immutable
- bila perlu perubahan schema baru, buat migration forward-only baru
- jangan rename/delete kolom production tanpa persetujuan eksplisit
