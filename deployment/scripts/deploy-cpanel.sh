#!/usr/bin/env bash

set -euo pipefail

APP_NAME="SIDEDIKK"
EXPECTED_ROOT="/home/ewyjotxg/sidedikk"
PUBLIC_APP_DIR="/home/ewyjotxg/public_html/app"
PUBLIC_MAIN_DIR="/home/ewyjotxg/public_html"
DEPLOY_INDEX_SOURCE="deployment/app-public/index.php"
DEPLOY_HTACCESS_SOURCE="deployment/app-public/.htaccess"
PUBLIC_SOURCE_DIR="public"
PUBLIC_APP_BACKUP_DIR="/home/ewyjotxg/backups/sidedikk-app"

log() {
  printf '[%s] %s\n' "$APP_NAME" "$1"
}

fail() {
  printf '[%s] ERROR: %s\n' "$APP_NAME" "$1" >&2
  exit 1
}

CURRENT_DIR="$(pwd)"

if [[ "$CURRENT_DIR" != "$EXPECTED_ROOT" ]]; then
  fail "Script harus dijalankan dari $EXPECTED_ROOT, bukan $CURRENT_DIR"
fi

[[ -f artisan ]] || fail "File artisan tidak ditemukan"
[[ -f composer.json ]] || fail "composer.json tidak ditemukan"
[[ -f "$PUBLIC_SOURCE_DIR/index.php" ]] || fail "public/index.php tidak ditemukan"
[[ -d "$PUBLIC_APP_DIR" ]] || fail "Direktori tujuan aplikasi tidak ditemukan: $PUBLIC_APP_DIR"
[[ -f "$DEPLOY_INDEX_SOURCE" ]] || fail "Deployment index tidak ditemukan: $DEPLOY_INDEX_SOURCE"
[[ -f "$DEPLOY_HTACCESS_SOURCE" ]] || fail "Deployment .htaccess tidak ditemukan: $DEPLOY_HTACCESS_SOURCE"

if [[ -f package.json ]]; then
  command -v npm >/dev/null 2>&1 || fail "npm tidak tersedia untuk build frontend"
fi

mkdir -p "$PUBLIC_APP_BACKUP_DIR"
BACKUP_TARGET="$PUBLIC_APP_BACKUP_DIR/$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_TARGET"

log "Membuat backup public app saat ini ke $BACKUP_TARGET"
if command -v rsync >/dev/null 2>&1; then
  rsync -av "$PUBLIC_APP_DIR/" "$BACKUP_TARGET/"
else
  cp -R "$PUBLIC_APP_DIR/." "$BACKUP_TARGET/"
fi

log "Menginstal dependency Composer production"
composer install --no-dev --optimize-autoloader --no-interaction

if [[ -f package.json ]]; then
  log "Membangun asset frontend production"
  npm run build
fi

log "Membersihkan cache Laravel"
php artisan optimize:clear
log "Membangun cache produksi Laravel"
php artisan optimize

log "Sinkronisasi file public Laravel ke $PUBLIC_APP_DIR"
if command -v rsync >/dev/null 2>&1; then
  rsync -av "$PUBLIC_SOURCE_DIR/" "$PUBLIC_APP_DIR/"
else
  cp -R "$PUBLIC_SOURCE_DIR/." "$PUBLIC_APP_DIR/"
fi

log "Menyalin deployment-specific index.php dan .htaccess"
cp "$DEPLOY_INDEX_SOURCE" "$PUBLIC_APP_DIR/index.php"
cp "$DEPLOY_HTACCESS_SOURCE" "$PUBLIC_APP_DIR/.htaccess"

log "Deploy public app selesai"
log "Migrasi production tidak dijalankan otomatis. Jalankan manual setelah backup database diverifikasi."
log "File .env tidak disentuh oleh script ini."
