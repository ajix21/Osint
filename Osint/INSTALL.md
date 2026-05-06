# LeakOSINT Tool — Laravel Edition
## Panduan Instalasi Lengkap

---

## Persyaratan Sistem

| Komponen | Versi Minimum |
|----------|---------------|
| PHP      | 8.2+          |
| MySQL    | 5.7+ / MariaDB 10.3+ |
| Composer | 2.x           |
| PHP Extensions | `pdo_mysql`, `curl`, `mbstring`, `openssl`, `json` |

---

## Cara 1: Install Langsung (Recommended)

```bash
# 1. Install Laravel fresh di folder ini
composer create-project laravel/laravel leakosint-app "^11.0"
cd leakosint-app

# 2. Copy file-file dari folder leakosint-laravel ke folder ini:
#    - app/Http/Controllers/AuthController.php
#    - app/Http/Controllers/SearchController.php
#    - app/Http/Controllers/AdminController.php
#    - app/Http/Middleware/CheckRole.php
#    - app/Models/User.php, SearchLog.php, LoginAttempt.php
#    - config/leakosint.php
#    - routes/web.php
#    - resources/views/ (semua folder)
#    - database/migrations/ (3 file)
#    - database/seeders/DatabaseSeeder.php
#    - bootstrap/app.php (replace)
```

---

## Cara 2: Langsung dari folder ini

```bash
# 1. Masuk ke folder
cd "D:\Project Bot - Work\Leak Osint\leakosint-laravel"

# 2. Install dependencies
composer install

# 3. Salin .env
copy .env.example .env

# 4. Generate app key
php artisan key:generate
```

---

## Konfigurasi Database

### Opsi A — Import SQL langsung
```sql
-- Di MySQL/HeidiSQL/phpMyAdmin:
SOURCE leakosint.sql;
```

### Opsi B — Migrasi Laravel (recommended)
```bash
# Edit .env terlebih dahulu:
# DB_DATABASE=leakosint_db
# DB_USERNAME=root
# DB_PASSWORD=password_anda

php artisan migrate
php artisan db:seed
```

---

## Konfigurasi .env

```env
# Wajib diisi:
APP_URL=http://localhost:8000
DB_HOST=127.0.0.1
DB_DATABASE=leakosint_db
DB_USERNAME=root
DB_PASSWORD=

# API Token LeakOSINT (isi dengan token Anda):
LEAKOSINT_API_TOKEN=6330301949:5yJCvg0Z
```

---

## Menjalankan Aplikasi

```bash
php artisan serve
# Buka: http://localhost:8000
```

---

## Akun Default

| Username   | Password        | Role     | Dapat Cari? |
|------------|-----------------|----------|-------------|
| `admin`    | `Admin@12345`   | Admin    | Ya          |
| `operator` | `Operator@12345`| Operator | Ya          |
| `viewer`   | `Viewer@12345`  | Viewer   | Tidak        |

> **PENTING:** Ganti semua password setelah login pertama!

---

## Struktur Roles

| Role     | Login | Cari | History Sendiri | History Semua | Admin Panel |
|----------|-------|------|-----------------|---------------|-------------|
| Admin    | ✓     | ✓    | ✓               | ✓             | ✓           |
| Operator | ✓     | ✓    | ✓               | ✗             | ✗           |
| Viewer   | ✓     | ✗    | ✓               | ✗             | ✗           |

---

## Fitur Keamanan

- **Brute-force protection**: 5 kali gagal = lock 15 menit per IP
- **Audit log**: Semua login attempt tercatat (berhasil/gagal)
- **Search log**: Semua query tercatat dengan user & IP
- **CSRF protection**: Semua form dilindungi token CSRF
- **API token tersembunyi**: Token tidak pernah terekspos ke frontend
- **Per-user API token**: Admin bisa set token berbeda per user
- **Session regeneration**: Token sesi diperbarui setelah login

---

## Perbedaan vs Versi Python

| Fitur | Python Flask | Laravel PHP |
|-------|-------------|-------------|
| Perlu jalankan server terpisah | Ya (`python app.py`) | **Tidak** |
| API Token di HTML (terekspos) | Ya | **Tidak** |
| Login system | Tidak ada | **Ada** |
| Multi-user | Tidak ada | **Ada** |
| Audit log | Tidak ada | **Ada** |
| Export PDF/Excel | Ya | **Ya** |

---

## Tambah User Baru

Melalui Admin Panel:
```
http://localhost:8000/admin/users/create
```

Atau via artisan (quick):
```bash
php artisan tinker
>>> App\Models\User::create(['name'=>'Nama','username'=>'user1','email'=>'u@a.com','password'=>Hash::make('Pass@123'),'role'=>'operator','is_active'=>true]);
```
