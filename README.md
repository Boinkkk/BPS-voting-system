# Sistem Penilaian & Ranking Pegawai BPS

Aplikasi berbasis web ini digunakan untuk mengelola rekap absensi bulanan dan penilaian kinerja pegawai, serta secara otomatis menghitung dan merangking 10 Kandidat Pegawai Terbaik berdasarkan algoritma yang telah ditentukan (perpaduan rata-rata kinerja bulanan dikurangi dengan skor penalti absensi kedisiplinan).

Aplikasi ini dibangun menggunakan kerangka kerja **Laravel** (PHP) dan disajikan dengan antarmuka yang modern.

---

## 🛠️ Persyaratan Sistem (Prerequisites)

Sebelum menjalankan proyek ini, pastikan komputer/server Anda telah terpasang perangkat lunak berikut:

1. **PHP** (Minimal versi 8.2 atau lebih baru)
2. **Composer** (Untuk mengelola dependensi PHP)
3. **Node.js & npm** (Untuk mengompilasi aset *frontend* seperti Tailwind CSS dan Vite)
4. **MySQL / MariaDB** (Untuk sistem *database*)
5. **Git** (Opsional, untuk melakukan *clone* repositori)

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan aplikasi dari nol:

### 1. Clone Repositori
Langkah pertama adalah mengunduh (*clone*) *source code* ke komputer lokal Anda:
```bash
git clone <URL_REPOSITORY_ANDA>
cd web
```
*(Pastikan Anda masuk ke dalam *directory* proyek utama, di mana *file* `composer.json` berada).*

### 2. Install Dependensi PHP (Composer)
Unduh seluruh pustaka (*library*) backend yang dibutuhkan Laravel:
```bash
composer install
```
*(Proses ini akan mengunduh banyak file ke dalam folder `/vendor`. Pastikan koneksi internet Anda stabil).*

### 3. Install Dependensi Frontend (NPM)
Unduh seluruh *library frontend* (seperti Tailwind CSS, Alpine.js) yang dibutuhkan:
```bash
npm install
```

### 4. Konfigurasi Environment (File `.env`)
Salin file konfigurasi bawaan menjadi file konfigurasi rahasia (*environment*) Anda:
```bash
cp .env.example .env
```
Setelah itu, buka file `.env` di teks editor (seperti VS Code atau Notepad), dan sesuaikan pengaturan koneksi *database* Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bps_ranking_karyawan   # Ganti dengan nama database Anda
DB_USERNAME=root                   # Ganti dengan username MySQL Anda
DB_PASSWORD=                       # Ganti dengan password MySQL Anda (kosongkan jika tidak ada)
```

### 5. Generate Application Key
Buat kunci unik (*App Key*) untuk mengamankan enkripsi data aplikasi Anda:
```bash
php artisan key:generate
```

### 6. Persiapan Database
Pastikan Anda sudah membuat *database* kosong di MySQL dengan nama yang persis sama dengan yang Anda isi di `DB_DATABASE` (misal: `bps_ranking_karyawan`).

### 7. Migrasi & Seeding Database
Jalankan perintah ini untuk membuat semua tabel ke dalam database Anda sekaligus mengisi data-data bawaan (Akun admin, Master Data Pegawai dari file CSV, Bobot Penalti, dll):
```bash
php artisan migrate:fresh --seed
```
*Catatan: Karena data pegawai diambil dari file `data/data_pegawai.csv`, pastikan file tersebut berada di posisinya agar data pegawai tersinkronisasi.*

### 8. Compile Aset Frontend (Vite)
Bangun aset CSS dan Javascript agar tampilan situs menjadi rapi dan modern:
```bash
npm run build
```
*(Atau gunakan `npm run dev` jika Anda ingin menjalankannya dalam mode *development* sambil mengedit kode).*

### 9. Jalankan Server Lokal
Nyalakan server internal Laravel:
```bash
php artisan serve
```
Aplikasi kini siap diakses! Buka *browser* Anda dan kunjungi URL berikut:  
👉 **http://localhost:8000**

---

## 🔑 Akun Demo (Testing)
Jika Anda menggunakan *seeder* bawaan, berikut adalah beberapa akun pengujian yang bisa Anda gunakan untuk masuk (Login):

**1. Administrator (Admin Sistem)**
- **Email**: `admin@bps.go.id`
- **Password**: `password123`
- *Fungsi: Mengelola data pegawai, mengatur bobot penalti, mengunggah rekap Excel.*

**2. Kepala BPS**
- **Email**: `kepala@bps.go.id`
- **Password**: `password123`
- *Fungsi: Memantau dan melihat hasil 10 Kandidat Terbaik.*

**3. Pegawai**
- **Email**: `pegawai@bps.go.id`
- **Password**: `password123`
- *Fungsi: Hanya melihat penilaian kinerja pribadi (Dashboard khusus pegawai).*

---

## 📝 Catatan Penting Penggunaan
1. **Upload File Excel**: Pastikan saat Admin mengunggah rekapitulasi absensi atau kinerja, Anda **wajib menggunakan Template Excel resmi** yang disediakan dengan cara mengklik tombol "Download Template Excel" pada masing-masing halaman.
2. **Perhitungan Otomatis**: Setiap kali data Kinerja atau data Absensi baru diunggah, sistem akan **otomatis melakukan perhitungan ulang** dan menyusun daftar 10 Kandidat Terbaik saat itu juga.
3. **Pengaturan Bobot**: Angka potongan indisipliner (seperti Terlambat, Pulang Cepat, atau KJK) bisa diubah secara dinamis langsung dari menu "Absensi Pegawai" dengan login sebagai Admin.

Selamat mencoba aplikasi BPS Ranking Karyawan!
