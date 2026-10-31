# Library System

Simple Library Information System (Laravel + Blade + MySQL).

## Requirements

* PHP ^8.3
* Composer
* MySQL
* Laravel ^13

## Installation

1. Clone repository, lalu masuk ke foldernya.
2. Install dependencies:

```
composer install
```

3. Salin file environment dan buat app key:

```
cp .env.example .env
php artisan key:generate
```

4. Buat database MySQL bernama `library-system`, lalu sesuaikan `DB_USERNAME` dan `DB_PASSWORD` di `.env`.

5. Jalankan migration dan seeder:

```
php artisan migrate --seed
```

6. Jalankan aplikasi:

```
php artisan serve
```

## Akun contoh (hanya untuk pengembangan lokal)

| Email | Password |
|---|---|
| admin@example.com | password123 |
| test@example.com | password |

Ganti atau hapus akun ini sebelum aplikasi dipakai di luar lokal.

## Testing

```
php artisan test
```

## Catatan deployment

Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env` agar detail error tidak tampil ke pengguna.

## Author

Waylend Yutani
