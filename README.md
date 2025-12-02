# SPK Kelayakan Influencer dengan Metode WASPAS

Sistem Pendukung Keputusan (SPK) berbasis web untuk menentukan kelayakan influencer dalam kegiatan endorsment menggunakan metode Weighted Aggregated Sum Product Assessment (WASPAS).

Dibangun menggunakan Laravel 11 dan MySQL.

## Fitur Utama
### Autentikasi
- Login dan logout
- Role pengguna (Manager & Staff)

### Manager
- Dashboard
- Mengelola akun staff
- Melihat hasil perhitungan WASPAS
- Melihat riwayat influencer yang dipilih

### Staff
- Dashboard
- Mengelola data influencer
- Mengelola kriteria dan bobot penilaian
- Melakukan perhitungan WASPAS
- Mengelola riwayat perhitungan influencer

## Metode WASPAS
WASPAS menggabungkan Weighted Sum Model (WSM) dan Weighted Product Model (WPM):

Qi = λ * Qi(WSM) + (1 - λ) * Qi(WPM)

## Teknologi
- Laravel 11
- PHP 8.x
- MySQL
- Blade Template
- Bootstrap 5

## Instalasi
1. Clone repository  
2. composer install  
3. cp .env.example .env  
4. php artisan key:generate  
5. Set database  
6. php artisan migrate  
7. php artisan serve

## Lisensi
Untuk keperluan akademik dan pembelajaran.
