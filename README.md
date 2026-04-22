# Sistem Parkir Digital

Sistem manajemen parkir berbasis **Laravel** dengan fitur lengkap:

## ✨ Fitur Utama
- **Entry Parkir** 🚗: Input plat nomor, auto-assign lahan kosong
- **Exit & Pembayaran** 💰: Scan QR ticket, hitung biaya otomatis
- **Dashboard Parkir** 📊: Statistik real-time (lahan kosong, kendaraan aktif, pendapatan)
- **Laporan Keuangan** 📈: Grafik harian/mingguan/bulanan (admin only)
- **Pengaturan Tarif** ⚙️: Konfigurasi tarif per jenis kendaraan (admin only)
- **Panel Admin** 🛠️: CRUD ticket/user, edit status/biaya
- **Struk QR Code** 🖨️: Print receipt entry/exit
- **Dark Mode** 🌙: Responsive Tailwind CSS

## 🛠️ Tech Stack
```
Laravel 11
Tailwind CSS + Vite
MySQL/SQLite  
Carbon (WIB)
Chart.js
```

## 🚀 Instalasi
```bash
git clone <repo>
cd parking_system
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
npm install && npm run dev
```

## 📱 Akses
```
User biasa: /parking/dashboard
Admin: /admin/dashboard (is_admin = true)
Entry: /parking/entry
Exit: /parking/exit
Laporan: /parking/reports (admin)
Tarif: /parking/rates (admin)
```

## 🧪 Test Data
```
Plate: B 1234 XYZ → Entry lot B2
Tarif default:
  Motor: Rp3.000 (1st) / Rp2.000 (add)
  Mobil: Rp5.000 / Rp3.000
  Truk: Rp8.000 / Rp5.000
Timezone: Asia/Jakarta (WIB)
```

## 🗄️ Database
```
tickets: entry_time, exit_time, status (entry/paid), fee
parking_lots: lot_number (A1-B10), status (available/occupied)
rates: vehicle_type, first_hour, additional_hour
users: is_admin field
```

## ⚙️ Customisasi
```
Tarif: /parking/rates  
Laporan: /parking/reports
Timezone: config/app.php → 'Asia/Jakarta'
Seeder: php artisan db:seed
```

## 🔗 Routes Lengkap
```
parking.dashboard    GET  Dashboard
parking.entry        GET/POST  Entry form + AJAX  
parking.exit         GET/POST  Exit + QR scan
parking.reports      GET  Reports/charts (admin)
parking.rates        GET/POST  Tarif config (admin)
admin.dashboard      GET  Admin panel
```

**Dibuat dengan ❤️ menggunakan BLACKBOXAI**

