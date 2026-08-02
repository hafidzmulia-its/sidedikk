#!/usr/bin/env bash

set -euo pipefail

APP_ROOT="/home/ewyjotxg/sidedikk"
PUBLIC_APP_DIR="/home/ewyjotxg/public_html/app"
PUBLIC_MAIN_DIR="/home/ewyjotxg/public_html"
APP_URL="https://app.sidedikk.my.id"

pass() {
  printf 'PASS: %s\n' "$1"
}

warn() {
  printf 'WARNING: %s\n' "$1"
}

fail() {
  printf 'FAIL: %s\n' "$1"
}

check_cmd() {
  local cmd="$1"
  local label="$2"
  if command -v "$cmd" >/dev/null 2>&1; then
    pass "$label tersedia"
  else
    fail "$label tidak tersedia"
  fi
}

check_file() {
  local path="$1"
  local label="$2"
  if [[ -e "$path" ]]; then
    pass "$label ada ($path)"
  else
    fail "$label tidak ditemukan ($path)"
  fi
}

check_writable() {
  local path="$1"
  local label="$2"
  if [[ -w "$path" ]]; then
    pass "$label writable"
  else
    fail "$label tidak writable"
  fi
}

check_cmd php "PHP"
check_cmd composer "Composer"

if command -v node >/dev/null 2>&1; then
  pass "Node.js tersedia"
else
  warn "Node.js tidak tersedia"
fi

if command -v npm >/dev/null 2>&1; then
  pass "npm tersedia"
else
  warn "npm tidak tersedia"
fi

check_file "$APP_ROOT/artisan" "Artisan"
check_file "$APP_ROOT/.env" ".env di luar public_html"
check_file "$PUBLIC_APP_DIR/index.php" "Public index aplikasi"
check_file "$PUBLIC_APP_DIR/.htaccess" ".htaccess aplikasi"
check_file "$PUBLIC_APP_DIR/manifest.webmanifest" "Manifest PWA"
check_file "$PUBLIC_APP_DIR/sw.js" "Service worker"
check_file "$PUBLIC_APP_DIR/brand/icon-192.png" "Icon 192"
check_file "$PUBLIC_APP_DIR/brand/icon-512.png" "Icon 512"
check_file "$PUBLIC_APP_DIR/build/manifest.json" "Vite manifest"

check_writable "$APP_ROOT/storage" "Direktori storage"
check_writable "$APP_ROOT/bootstrap/cache" "Direktori bootstrap/cache"

cd "$APP_ROOT"

if php -r "exit(extension_loaded('pdo_mysql') ? 0 : 1);"; then
  pass "Ekstensi pdo_mysql aktif"
else
  fail "Ekstensi pdo_mysql tidak aktif"
fi

if php -r "exit(extension_loaded('mbstring') ? 0 : 1);"; then
  pass "Ekstensi mbstring aktif"
else
  fail "Ekstensi mbstring tidak aktif"
fi

APP_ENV_VALUE="$(php artisan env 2>/dev/null | sed 's/^.*: //')"
if [[ "$APP_ENV_VALUE" == "production" ]]; then
  pass "APP_ENV production"
else
  warn "APP_ENV saat ini: $APP_ENV_VALUE"
fi

if php artisan about 2>/dev/null | grep -q 'Debug Mode .. DISABLED'; then
  pass "APP_DEBUG nonaktif"
else
  warn "APP_DEBUG belum terdeteksi nonaktif"
fi

if php artisan migrate:status >/dev/null 2>&1; then
  pass "Laravel dapat membaca status migrasi"
else
  fail "Gagal membaca status migrasi"
fi

if php artisan tinker --execute="DB::connection()->getPdo(); echo 'ok';" 2>/dev/null | grep -q 'ok'; then
  pass "Koneksi database Laravel berhasil"
else
  fail "Koneksi database Laravel gagal"
fi

if curl -IkfsS "$APP_URL/up" >/dev/null 2>&1; then
  pass "Health URL dapat diakses via HTTPS"
else
  warn "Health URL belum terverifikasi via HTTPS"
fi

if curl -IkfsS "$APP_URL/login" >/dev/null 2>&1; then
  pass "Login URL dapat diakses via HTTPS"
else
  warn "Login URL belum terverifikasi via HTTPS"
fi

if curl -IkfsS "$APP_URL/install" >/dev/null 2>&1; then
  pass "Install URL dapat diakses via HTTPS"
else
  warn "Install URL belum terverifikasi via HTTPS"
fi

if curl -ksS "$APP_URL/.env" | grep -q 'APP_'; then
  fail ".env tampak terekspos dari web"
else
  pass ".env tidak terekspos dari web"
fi

if curl -ksS "$APP_URL/storage/logs/laravel.log" | grep -q .; then
  fail "Laravel log tampak terekspos dari web"
else
  pass "Laravel log tidak terekspos dari web"
fi

if curl -ksS "$APP_URL/vendor/" | grep -qi 'index of'; then
  fail "Directory listing vendor terdeteksi"
else
  pass "Directory listing vendor tidak terdeteksi"
fi
