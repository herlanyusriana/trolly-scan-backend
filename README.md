# In-Out Trolley Platform

Aplikasi Laravel untuk mengelola approval akun mobile, registrasi troli, serta pencatatan aktivitas keluar-masuk troli.

## Fitur Utama
- Panel admin bertema gelap untuk login, dashboard ringkasan, dan persetujuan akun pengguna mobile.
- CRUD master troli (internal & external) dengan status IN/OUT dan QR code otomatis.
- REST API (v1) untuk aplikasi mobile: login akun yang sudah disetujui, proses check-out/check-in troli, dan ringkasan cepat.
- Pencatatan histori pergerakan troli beserta durasi penggunaan dan tujuan.
- File QR code disimpan di storage publik untuk dipasang pada troli atau discan aplikasi mobile.

## Kebutuhan Sistem
- PHP 8.2+
- Composer
- Database MySQL/MariaDB (atau SQLite untuk pengujian awal)
- Node.js & npm (untuk asset bundling via Vite)

## Instalasi
1. Pasang dependensi PHP & Composer.
2. Clone repositori ini kemudian masuk ke direktori proyek.
3. Jalankan `composer install`.
4. Salin file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database dan mail.
5. Generate kunci aplikasi (`php artisan key:generate`).
6. Jalankan migrasi dan seeder: `php artisan migrate --seed`.
7. Buat symlink storage publik: `php artisan storage:link`.
8. Instal asset front-end: `npm install && npm run build` (atau `npm run dev` saat pengembangan).

## Struktur Modul
- `app/Models` berisi model `Admin`, `MobileUser`, `Trolley`, `TrolleyMovement`, dan `ApprovalLog`.
- `app/Http/Controllers/Admin` menangani panel admin (login, dashboard, approval, troli).
- `app/Http/Controllers/Api` menyediakan endpoint mobile.
- `database/migrations` berisi skema tabel utama, termasuk log approval dan histori pergerakan troli.
- `resources/views/admin` memuat tampilan panel admin berbasis Blade.

## Endpoint API (Ringkas)
- `POST /api/v1/auth/login` — autentikasi mobile (akun harus berstatus `approved`).
- `GET /api/v1/trolleys` — daftar troli aktif.
- `POST /api/v1/trolleys/{id}/checkout` — catat troli OUT (keluar).
- `POST /api/v1/trolleys/{id}/checkin` — catat troli IN (kembali).
- `GET /api/v1/trolleys/{id}/history` — riwayat singkat per troli.
- `GET /api/v1/dashboard/summary` — ringkasan penggunaan untuk mobile.

Semua endpoint (kecuali login) dilindungi oleh guard `auth:mobile` menggunakan token Sanctum.

## Pengembangan
- Gunakan `php artisan serve` untuk server lokal.
- Jalankan `php artisan queue:work` bila menggunakan notifikasi atau reminder terjadwal.
- Tambahkan job/scheduler di `app/Console/Kernel.php` untuk pengingat troli yang belum kembali.

## Catatan
Lingkungan ini belum menjalankan `composer install`, `npm install`, serta belum memiliki nilai `APP_KEY`. Pastikan perintah tersebut dijalankan di mesin pengembangan sebelum aplikasi digunakan.
- Status troli kini hanya mendukung nilai `in` dan `out`. Jalankan ulang migrasi bila sebelumnya memakai status berbeda.
