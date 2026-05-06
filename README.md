# LeakOSINT Tool — Laravel Edition

> Alat pencarian data OSINT berbasis web dengan sistem autentikasi multi-role, audit log, dan perlindungan API token. Dibangun di atas Laravel 11.

---

## Daftar Isi

- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi `.env`](#konfigurasi-env)
- [Setup Database](#setup-database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Default](#akun-default)
- [Struktur Roles](#struktur-roles)
- [Fitur Keamanan](#fitur-keamanan)
- [Manajemen User](#manajemen-user)
- [Perbandingan dengan Versi Python](#perbandingan-dengan-versi-python)
- [Troubleshooting](#troubleshooting)

---

## Persyaratan Sistem

Pastikan semua komponen berikut sudah terinstal sebelum memulai.

| Komponen        | Versi Minimum              |
|-----------------|----------------------------|
| PHP             | 8.2+                       |
| MySQL / MariaDB | 5.7+ / 10.3+               |
| Composer        | 2.x                        |
| PHP Extensions  | `pdo_mysql`, `curl`, `mbstring`, `openssl`, `json` |

Untuk mengecek versi yang terinstal:

```bash
php -v
composer -V
mysql --version
```

---

## Instalasi

### Langkah 1 — Clone Repository

```bash
git clone https://github.com/ajix21/osint.git
cd osint
```

### Langkah 2 — Install Dependencies PHP

```bash
composer install
```

> Jika ada peringatan tentang versi PHP atau ekstensi yang hilang, pastikan ekstensi `pdo_mysql`, `curl`, `mbstring`, `openssl`, dan `json` sudah diaktifkan di `php.ini`.

### Langkah 3 — Salin File Environment

**Linux / macOS:**
```bash
cp .env.example .env
```

**Windows:**
```cmd
copy .env.example .env
```

### Langkah 4 — Generate Application Key

```bash
php artisan key:generate
```

---

## Konfigurasi `.env`

Buka file `.env` dan sesuaikan nilai-nilai berikut:

```env
# ───────────── Aplikasi ─────────────
APP_NAME="LeakOSINT Tool"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# ───────────── Database ─────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leakosint_db
DB_USERNAME=root
DB_PASSWORD=password_anda

# ───────────── API LeakOSINT ────────
# Dapatkan token dari: https://leakosint.com
LEAKOSINT_API_TOKEN=your_api_token_here
```

> **Penting:** Jangan pernah commit file `.env` ke repository. File ini sudah ada di `.gitignore`.

---

## Setup Database

### Buat Database Baru

Masuk ke MySQL dan buat database:

```sql
CREATE DATABASE leakosint_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Opsi A — Migrasi Laravel (Direkomendasikan)

Jalankan migrasi dan seeder untuk membuat tabel dan data awal:

```bash
php artisan migrate
php artisan db:seed
```

### Opsi B — Import SQL Manual

Jika ingin menggunakan file SQL yang sudah ada:

```bash
# Via command line
mysql -u root -p leakosint_db < leakosint.sql
```

Atau import melalui phpMyAdmin / HeidiSQL dengan membuka file `leakosint.sql`.

---

## Menjalankan Aplikasi

### Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: **http://localhost:8000**

### Konfigurasi Port Kustom (Opsional)

```bash
php artisan serve --port=8080
```

### Optimasi untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Akun Default

Setelah menjalankan seeder, akun berikut tersedia untuk login:

| Username   | Password          | Role     | Dapat Mencari |
|------------|-------------------|----------|:-------------:|
| `admin`    | `Admin@12345`     | Admin    | ✓             |
| `operator` | `Operator@12345`  | Operator | ✓             |
| `viewer`   | `Viewer@12345`    | Viewer   | ✗             |

> **Penting:** Segera ganti semua password default setelah login pertama kali melalui halaman profil atau Admin Panel.

---

## Struktur Roles

Aplikasi ini menggunakan tiga level akses:

| Kemampuan              | Admin | Operator | Viewer |
|------------------------|:-----:|:--------:|:------:|
| Login                  | ✓     | ✓        | ✓      |
| Melakukan pencarian    | ✓     | ✓        | ✗      |
| Lihat history sendiri  | ✓     | ✓        | ✓      |
| Lihat semua history    | ✓     | ✗        | ✗      |
| Akses Admin Panel      | ✓     | ✗        | ✗      |
| Manajemen user         | ✓     | ✗        | ✗      |

---

## Fitur Keamanan

| Fitur                     | Deskripsi                                                     |
|---------------------------|---------------------------------------------------------------|
| Brute-force protection    | 5 percobaan gagal akan mengunci akses selama 15 menit per IP |
| Audit log login           | Setiap percobaan login (berhasil maupun gagal) dicatat       |
| Search log                | Semua query pencarian dicatat beserta user dan IP address    |
| CSRF protection           | Seluruh form dilindungi token CSRF bawaan Laravel            |
| API token tersembunyi     | Token API tidak pernah dikirim ke frontend / browser         |
| Per-user API token        | Admin dapat mengatur token API berbeda untuk tiap user       |
| Session regeneration      | Token sesi diperbarui otomatis setiap setelah login          |

---

## Manajemen User

### Tambah User via Admin Panel

Akses halaman pembuatan user melalui browser:

```
http://localhost:8000/admin/users/create
```

### Tambah User via Artisan Tinker

Untuk pembuatan user secara cepat melalui terminal:

```bash
php artisan tinker
```

Kemudian jalankan perintah berikut di prompt tinker:

```php
App\Models\User::create([
    'name'      => 'Nama Lengkap',
    'username'  => 'username_baru',
    'email'     => 'email@domain.com',
    'password'  => Hash::make('Password@123'),
    'role'      => 'operator',   // 'admin', 'operator', atau 'viewer'
    'is_active' => true,
]);
```

---

## Perbandingan dengan Versi Python

| Fitur                       | Python Flask | Laravel PHP    |
|-----------------------------|:------------:|:--------------:|
| Server terpisah             | Ya           | **Tidak**      |
| API Token terekspos di HTML | Ya           | **Tidak**      |
| Sistem login                | Tidak ada    | **Ada**        |
| Multi-user & role           | Tidak ada    | **Ada**        |
| Audit log                   | Tidak ada    | **Ada**        |
| Brute-force protection      | Tidak ada    | **Ada**        |
| Export PDF / Excel          | Ya           | **Ya**         |

---

## Troubleshooting

### Error: `php_pdo_mysql` extension not found

Aktifkan ekstensi di `php.ini`:
```ini
extension=pdo_mysql
```
Kemudian restart web server.

### Error: `SQLSTATE[HY000] [2002] Connection refused`

Pastikan layanan MySQL sedang berjalan:
```bash
# Linux (systemd)
sudo systemctl start mysql

# macOS (Homebrew)
brew services start mysql
```

### Error: `No application encryption key has been specified`

Jalankan ulang:
```bash
php artisan key:generate
```

### Halaman tampil kosong / error 500

Periksa log error Laravel:
```bash
tail -f storage/logs/laravel.log
```

Pastikan permission folder `storage` dan `bootstrap/cache` sudah benar:
```bash
chmod -R 775 storage bootstrap/cache
```

---

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
