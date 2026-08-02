# PWA Deployment Notes

Tanggal: 2026-08-02

## Origin Yang Benar

PWA SIDEDIKK harus dilayani dari:

```text
https://app.sidedikk.my.id
```

Main domain `https://sidedikk.my.id` hanya landing static dan tidak boleh mencoba memanggil install prompt lintas origin.

## URL Yang Harus Valid

- `https://app.sidedikk.my.id/`
- `https://app.sidedikk.my.id/login`
- `https://app.sidedikk.my.id/install`
- `https://app.sidedikk.my.id/manifest.webmanifest`
- `https://app.sidedikk.my.id/sw.js`

URL tidak boleh mengandung `/public`.

## Manifest

Manifest saat ini perlu melayani metadata utama:

- `id: "/"`
- `start_url: "/"`
- `scope: "/"`
- `display: "standalone"`
- `theme_color: "#95409E"`

Icon yang harus tersedia:

- `brand/icon-192.png`
- `brand/icon-512.png`

## Service Worker Privacy Rules

Service worker tidak boleh cache-first untuk halaman sensitif.

Sensitive routes yang saat ini diperlakukan non-cache/public-cache-disabled:

- `/dashboard`
- `/admin`
- `/screenings`
- `/history`
- `/profile`
- `/education`
- `/login`
- `/register`
- `/forgot-password`
- `/reset-password`
- `/verify-email`
- `/confirm-password`

Static public assets yang boleh di-cache:

- build CSS/JS
- icon PWA
- logo publik
- offline fallback

## Halaman `/install`

Route baru:

- `GET /install`
- route name: `pwa.install`

Halaman ini:

- menampilkan tombol `Pasang Sekarang`
- menampilkan tombol `Buka Aplikasi` atau `Masuk`
- memberi panduan Android
- memberi panduan iPhone/iPad
- mendeteksi state installed
- memberi fallback bila browser tidak memberi install prompt

## Testing Checklist

Verifikasi manual di browser production:

1. buka `/install`
2. cek tombol `Pasang Sekarang`
3. cek fallback browser unsupported
4. cek iOS instructions
5. cek installed state saat app dibuka dari home screen
6. cek manifest dapat diakses
7. cek service worker dapat diakses
8. cek icon 192 dan 512 dapat diakses
