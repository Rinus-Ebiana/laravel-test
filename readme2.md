# Sistem Perwalian Pascasarjana ITB STIKOM Bali

## 🚀 Fitur Utama
- CRUD Data Mahasiswa, Dosen, dan Matakuliah.
- CRUD Data KRS Mahasiswa
- Sistem Autentikasi.
- Tampilan Responsif dengan Blade & Bootstrap.

## 🛠️ Teknologi yang Digunakan
- **Framework:** Laravel 12
- **Language:** PHP 8.4
- **Database:** MySQL

## 📋 Prasyarat
Sebelum menjalankan proyek ini, pastikan Anda sudah menginstal:
- PHP >= 8.4
- Composer
- Node.js & NPM
- MySQL

## ⚙️ Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal Anda:

1. **Clone Repository**
   git clone https://github.com/Rinus-Ebiana/laravel-perwalian
   cd laravel-perwalian

2. **Instal Dependensi PHP**
   composer install

3. **Instal Dependensi JavaScript**
   npm install && npm run dev

4. **Konfigurasi Environment**
   cp .env.example .env
   Buka file .env dan sesuaikan bagian database:
   Cuplikan kode
    DB_DATABASE=nama_database_anda
    DB_USERNAME=root
    DB_PASSWORD=

5. **Generate App Key**
   php artisan key:generate

6. **Migrasi Database**
   php artisan migrate

7. **Jalankan Server**
   php artisan serve