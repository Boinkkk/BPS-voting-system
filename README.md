# SIVOTA - Sistem Informasi Voting Terpadu BPS

SIVOTA (Sistem Informasi Voting Terpadu) adalah aplikasi internal berbasis web yang dikembangkan khusus untuk Badan Pusat Statistik (BPS). Aplikasi ini memfasilitasi proses pemilihan Pegawai Teladan/Berprestasi yang transparan, objektif, dan otomatis, mulai dari perhitungan rekap absensi, nilai CKP, pengisian kuesioner antarsejawat (survei), hingga pemungutan suara (voting) dan keputusan akhir pimpinan.

---

## 1. Teknologi Yang Digunakan

Sistem ini dikembangkan menggunakan *Tech Stack* modern yang sangat tangguh:

* **Framework Backend**: Laravel 11.x (Framework PHP standar industri)
* **Bahasa Pemrograman**: PHP 8.3
* **Database**: MySQL 8.0+ / MariaDB 10.11+
* **Frontend**: HTML5, Blade Templating Engine, Tailwind CSS (Vanilla), Vite (Asset Bundler)
* **Web Server**: Nginx (direkomendasikan untuk Production) atau Apache.
* **Libraries / Modul Penting**:
  * `maatwebsite/excel`: Untuk mengolah unggahan file Excel (Absensi & CKP).
  * `spatie/laravel-activitylog`: Untuk merekam jejak (*Audit Log*) aktivitas Admin/User.
  * `spatie/laravel-backup`: Untuk pencadangan (*backup*) otomatis database.

---

## 2. Arsitektur dan Logika Bisnis

SIVOTA dirancang untuk melakukan kalkulasi objektif secara otomatis dengan meminimalisir intervensi manual.

### A. Struktur Hak Akses (Role Base)
Sistem memiliki 4 (empat) level hak akses (*Role*):
1. **Admin**: Mengelola data utama (master data), manajemen periode, unggah rekap absensi & CKP, mengatur bobot penalti, dan pemantauan sistem.
2. **Pegawai**: Membaca pengumuman, mengisi survei sejawat, melihat top 10 kandidat, dan memberikan *vote* pada periode pemilihan.
3. **Tim Penilai**: Melakukan validasi kandidat Top 10 dan memberikan catatan khusus sebelum diajukan ke Kepala.
4. **Kepala BPS (Pimpinan)**: Memegang otoritas tertinggi untuk menetapkan Top 3 (Pemenang 1, 2, 3) dari daftar kandidat hasil algoritma dan voting.

### B. Alur Logika Algoritma Perhitungan Top 10
Admin tidak memilih langsung 10 orang terbaik, melainkan sistem menghitungnya otomatis dengan rumus:
1. **Skor Kehadiran (Presensi)**: Sistem memulai dengan nilai dasar 100, lalu menguranginya berdasarkan bobot penalti (Terlambat, Pulang Cepat, Tanpa Keterangan) dari data Excel yang diunggah.
2. **Skor CKP (Capaian Kinerja Pegawai)**: Diambil langsung dari nilai Excel CKP (0 - 100).
3. **Skor Kinerja**: Diambil dari Excel data Kinerja.
4. Aplikasi akan menghitung akumulasi total semua nilai objektif ini secara **otomatis** dan langsung menyaring (filter) seluruh pegawai BPS menjadi 10 Kandidat Terbaik (*Top 10*).

### C. Fase Siklus Pemilihan
Setiap periode pemilihan harus melewati 4 siklus berurutan:
* **Fase Penginputan**: Admin mengunggah absen & CKP, menginput pegawai baru.
* **Fase Survei**: Pegawai saling menilai etika/perilaku pegawai lain.
* **Fase Voting**: Kandidat disaring menjadi Top 10. Seluruh pegawai memberikan 1 (satu) suara/vote secara tertutup.
* **Fase Review Pimpinan**: Kepala BPS melihat hasil akhir (poin teknis + jumlah vote) lalu menetapkan 3 juara. 

---

## 3. Panduan Instalasi (Deployment)

Panduan ini ditujukan bagi Administrator IT / Programmer yang ingin mendeploy atau menjalankan sistem ini dari awal.

### Kebutuhan Perangkat Keras Minimum:
* CPU: 2 Core
* RAM: 4 GB
* Storage: 20 GB SSD
* OS: Ubuntu 22.04 LTS / 24.04 LTS

### Langkah Instalasi:

1. **Clone Repository (Unduh Kode)**
   ```bash
   git clone <URL_REPO_ANDA>
   cd voting-web
   ```

2. **Instalasi Modul PHP (Composer)**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Konfigurasi Environment**
   Salin file konfigurasi bawaan dan hasilkan kunci keamanan (App Key).
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   **Edit file `.env`** dan sesuaikan baris berikut:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://sivota.bps.go.id # Ganti dengan domain Anda
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_sivota
   DB_USERNAME=root
   DB_PASSWORD=password_db_anda
   
   SESSION_DRIVER=database
   # Ubah menjadi 'true' JIKA domain Anda SUDAH menggunakan HTTPS (SSL)
   SESSION_SECURE_COOKIE=false 
   ```

4. **Instalasi Modul Frontend & Build Assets**
   ```bash
   npm install
   npm run build
   ```

5. **Migrasi Database & Seeding Awal**
   Perintah ini akan membuat struktur tabel sekaligus membuat akun Super Admin.
   ```bash
   php artisan migrate --seed
   ```

6. **Pembuatan Storage Link**
   Agar foto profil dan *file* unggahan dapat diakses publik.
   ```bash
   php artisan storage:link
   ```

7. **Konfigurasi Cron Job (Penting)**
   Sistem SIVOTA memiliki pergantian *Timeline* (Fase) otomatis dan fungsi *Auto-Backup*. Buka *cron editor* di server Linux Anda (`crontab -e`) dan tambahkan baris berikut:
   ```text
   * * * * * cd /path-ke-project/voting-web && php artisan schedule:run >> /dev/null 2>&1
   ```

---

## 4. User Manual (Panduan Penggunaan Lengkap)

### A. Panduan Untuk ADMIN (HR/Administrator)

1. **Masuk (Login)**
   Gunakan email: `admin@bps.go.id` (atau yang telah dibuat).
2. **Membuat Periode Pemilihan Baru**
   - Buka menu **Master Data** -> **Periode Penilaian**.
   - Klik **Tambah Periode**. 
   - Tentukan Nama Periode, Tanggal Mulai dan Selesai untuk masing-masing Fase (Penginputan, Survei, Voting).
   - Pastikan Periode dalam status `penginputan`.
3. **Mengunggah Data Nilai Objektif (Excel)**
   - Saat berada di fase `penginputan`, klik menu **Unggah Absensi** atau **Unggah CKP**.
   - Klik tombol **Download Template** jika belum memiliki format Excel yang benar.
   - Pilih *file* `.xlsx` yang sudah terisi dan klik **Upload**. Sistem akan memproses dan memvalidasinya dalam hitungan detik. 
   *(Sistem tidak menggunakan Queue agar proses berlangsung cepat secara real-time dan notifikasi gagal/berhasil langsung terlihat).*
4. **Mengubah Pengaturan Bobot Penalti**
   - Buka menu **Pengaturan Bobot**. Di sini Admin bisa mengganti pengali skor (Misal: 1 Hari Terlambat = minus 0.5 poin). Perubahan bobot akan segera mereset dan mengkalkulasi ulang kandidat secara instan.
5. **Membuat Pengumuman / FAQ**
   - Gunakan menu **Pengumuman** untuk menyiarkan pesan (misal: "Pemilihan Pegawai Tahun Ini Dimulai"). Pesan ini akan muncul sebagai *Notifikasi* di halaman awal setiap pegawai.

### B. Panduan Untuk PEGAWAI (Karyawan)

1. **Mengisi Kuesioner Survei (Fase Survei)**
   - Saat Admin menetapkan fase survei telah dimulai, pegawai yang login akan melihat notifikasi *Timeline*.
   - Masuk ke menu **Isi Survei**. 
   - Sistem akan menyajikan pertanyaan terkait perilaku (*Core Values* ASN BerAKHLAK) dari teman sejawatnya.
2. **Memberikan Suara / Voting (Fase Voting)**
   - Setelah masuk fase voting, menu **Vote Sekarang** akan terbuka.
   - Pegawai dapat melihat profil singkat dan skor prestasi 10 Kandidat Terbaik.
   - Pegawai hanya dapat **memilih 1 Kandidat**. Pemilihan bersifat **rahasia** (Admin tidak bisa melihat siapa memilih siapa, sistem hanya mencatat total suaranya saja).

### C. Panduan Untuk KEPALA BPS (Pimpinan)

1. **Review Akhir (Fase Review)**
   - Pada halaman utama Kepala BPS, akan terpampang statistik akhir Top 10 Pegawai beserta hasil nilai objektif (CKP+Absensi) dan suara *Voting*.
2. **Penetapan Pemenang**
   - Kepala BPS menekan tombol **Tetapkan Top 3**. 
   - Hasil akhir ini tidak dapat diganggu gugat dan periode otomatis selesai/terkunci secara permanen.

---

## 5. Maintenance & Troubleshooting

Bagi divisi IT BPS, berikut adalah pedoman penyelesaian masalah (*troubleshooting*):

1. **File Berubah namun Tampilan (*View*) tidak Berubah?**
   Jalankan perintah ini di dalam *folder* project untuk membersihkan *cache*:
   ```bash
   php artisan optimize:clear
   php artisan view:clear
   ```

2. **Gagal Login (Page Expired / Error 419)**
   * Penyebab: Celah CSRF Token atau *Cookie* Session ditolak.
   * Solusi: Buka `.env`. Jika aplikasi tidak memakai HTTPS (hanya HTTP / Localhost), pastikan `SESSION_SECURE_COOKIE=false`. Jika sudah diubah, selalu jalankan `php artisan config:clear`.

3. **Cara Melihat Penyebab Error 500 (Internal Server Error)**
   Jika layar hanya menampilkan tulisan "500 Server Error", penyebab rincinya tersimpan di *file log*.
   Buka file teks di dalam: `storage/logs/laravel.log`. Gulir ke baris paling bawah untuk melihat *Exception* terbarunya.

4. **Database Backup & Recovery**
   Aplikasi ini dipasangkan modul `spatie/laravel-backup`.
   * **Backup Manual**: Jalankan `php artisan backup:run` (mem-backup DB dan *file* penting ke dalam folder `storage/app/backup`).
   * **Auto Backup**: Sistem sudah dijadwalkan otomatis mem-backup dirinya sendiri setiap dini hari pukul 01:00 (jika *cron job* server telah dipasang).

---

*(Dokumentasi SIVOTA - Hak Cipta dilindungi. Dikembangkan khusus untuk instansi Badan Pusat Statistik)*
