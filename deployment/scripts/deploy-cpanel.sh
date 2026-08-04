#!/usr/bin/env bash

set -euo pipefail

APP_NAME="SIDEDIKK"
EXPECTED_ROOT="/home/ewyjotxg/sidedikk"
PUBLIC_APP_DIR="/home/ewyjotxg/public_html/app"
PUBLIC_MAIN_DIR="/home/ewyjotxg/public_html"
DEPLOY_INDEX_SOURCE="deployment/app-public/index.php"
DEPLOY_HTACCESS_SOURCE="deployment/app-public/.htaccess"
PUBLIC_MAIN_SOURCE_DIR="deployment/main-domain"
PUBLIC_SOURCE_DIR="public"
BUILD_MANIFEST="$PUBLIC_SOURCE_DIR/build/manifest.json"
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
[[ -f "$BUILD_MANIFEST" ]] || fail "Manifest build frontend tidak ditemukan: $BUILD_MANIFEST"
[[ -d "$PUBLIC_APP_DIR" ]] || fail "Direktori tujuan aplikasi tidak ditemukan: $PUBLIC_APP_DIR"
[[ -f "$DEPLOY_INDEX_SOURCE" ]] || fail "Deployment index tidak ditemukan: $DEPLOY_INDEX_SOURCE"
[[ -f "$DEPLOY_HTACCESS_SOURCE" ]] || fail "Deployment .htaccess tidak ditemukan: $DEPLOY_HTACCESS_SOURCE"
[[ -d "$PUBLIC_MAIN_SOURCE_DIR" ]] || fail "Direktori landing page tidak ditemukan: $PUBLIC_MAIN_SOURCE_DIR"

HAS_NPM="false"

if command -v npm >/dev/null 2>&1; then
  HAS_NPM="true"
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
  if [[ "$HAS_NPM" == "true" ]]; then
    log "Membangun asset frontend production"
    npm run build
  else
    log "npm tidak tersedia di server. Menggunakan asset build yang sudah dikomit dari lokal."
  fi
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

log "Sinkronisasi landing page static ke $PUBLIC_MAIN_DIR"
if command -v rsync >/dev/null 2>&1; then
  rsync -av "$PUBLIC_MAIN_SOURCE_DIR/" "$PUBLIC_MAIN_DIR/"
else
  cp -R "$PUBLIC_MAIN_SOURCE_DIR/." "$PUBLIC_MAIN_DIR/"
fi

log "Deploy public app selesai"
log "Landing page static selesai disinkronkan"
log "Migrasi production tidak dijalankan otomatis. Jalankan manual setelah backup database diverifikasi."
log "File .env tidak disentuh oleh script ini."
