# Panduan Penggunaan SIVOTA (Sistem Informasi Voting Terpadu BPS)

Berdasarkan analisis struktur sistem, berikut adalah panduan lengkap dan detail penggunaan aplikasi SIVOTA berdasarkan hak akses (*role*) masing-masing pengguna.

---

## 1. Panduan Untuk Pegawai

### 1.1 Cara Melakukan Login
1. Buka halaman utama aplikasi SIVOTA.
2. Masukkan **NIP** atau **Email** yang terdaftar di BPS pada kolom yang tersedia.
3. Masukkan **Password** (password bawaan adalah `password` atau NIP, sesuai ketentuan saat akun dibuat).
4. Klik tombol **Login**. Jika berhasil, Anda akan diarahkan ke halaman *Dashboard* Pegawai.

### 1.2 Cara Melakukan Ubah Password
Demi keamanan, sangat disarankan untuk mengubah password bawaan Anda.
1. Setelah login, klik foto profil atau nama Anda di pojok kanan atas, lalu pilih menu **Profil**.
2. Gulir ke bagian **Ubah Password**.
3. Masukkan **Password Lama** Anda untuk verifikasi.
4. Masukkan **Password Baru** yang kuat.
5. Ketik ulang password baru pada kolom **Konfirmasi Password**.
6. Klik tombol **Simpan Password**. Sistem akan melakukan validasi dan memperbarui password Anda.

### 1.3 Update Foto Profil
1. Buka menu **Profil** dari pojok kanan atas.
2. Pada bagian foto profil, klik ikon **Kamera** atau tombol **Pilih Foto**.
3. Pilih foto dari perangkat Anda (format yang didukung: JPG, JPEG, PNG, dengan ukuran maksimal biasanya 2MB).
4. Klik tombol **Simpan Profil**. Foto akan otomatis terpotong (*crop*) atau disesuaikan ukurannya dan langsung diperbarui di seluruh sistem.

### 1.4 Melakukan Voting
Proses ini hanya dapat dilakukan saat siklus periode berada pada **Fase Voting**.
1. Pada *Dashboard*, sistem akan menampilkan 10 Kandidat Terbaik (*Top 10*) yang sudah disaring otomatis oleh algoritma berdasarkan nilai objektif (CKP & Presensi) dan hasil Survei.
2. Klik tombol **Vote Sekarang** pada menu utama atau *banner* di dashboard.
3. Anda akan melihat profil kandidat beserta ringkasan nilai mereka.
4. Pilih **satu kandidat** yang menurut Anda paling pantas dengan mengklik tombol **Pilih / Vote**.
5. Konfirmasi pilihan Anda. **Peringatan:** Pilihan bersifat rahasia dan tidak dapat diubah setelah dikonfirmasi.

---

## 2. Panduan Untuk Admin (Administrator)

### 2.1 Cara Edit dan Tambah Data Pegawai
1. Buka menu **Master Data** -> **Pegawai**.
2. **Tambah Pegawai:** Klik tombol **Tambah Pegawai**. Isi form (NIP, Nama, Email, Jabatan, Role dll). Password default akan dibuat otomatis oleh sistem (biasanya `password`).
3. **Edit Pegawai:** Klik tombol ikon Pensil/Edit pada baris pegawai yang ingin diubah. Admin dapat memperbarui data biodata hingga me-reset password pengguna ke default.

### 2.2 Membuat Pengumuman & Kategori Prioritas
Admin dapat menyiarkan informasi yang akan muncul di *dashboard* seluruh pegawai.
1. Buka menu **Master Data** -> **Pengumuman**, klik **Tambah Pengumuman**.
2. Isi **Judul** dan isi **Konten** pengumuman.
3. **Aturan Kategori Prioritas:** Prioritas menentukan indikator warna dan letak urgensi pesan tersebut.
   - **Low / Normal:** Pengumuman biasa, warna netral (biru/abu-abu).
   - **Medium:** Pengumuman menengah yang perlu mendapat perhatian (kuning/orange).
   - **High / Critical:** Informasi mendesak atau peringatan penting (merah).
4. **Fitur Tambahan:**
   - **Is Sticky (Sematkan):** Pengumuman akan tetap berada di urutan paling atas meski ada pengumuman baru.
   - **Is Popup:** Pengumuman akan muncul sebagai *modal pop-up* yang menutupi layar pegawai saat mereka baru login.
   - **Kirim Notifikasi:** Centang opsi ini jika Anda ingin sistem langsung mengirim email / notifikasi saat itu juga.

### 2.3 Cara Membaca Audit Log
Sistem menggunakan `spatie/laravel-activitylog` untuk merekam jejak aktivitas secara ketat.
1. Buka menu **Sistem** -> **Audit Log**.
2. Tabel akan menampilkan informasi berurutan mencakup:
   - **User:** Siapa yang melakukan aksi (Nama & NIP).
   - **Action:** Aksi yang dilakukan (contoh: *Created, Updated, Deleted, Login, Logout*).
   - **Model/Subject:** Data apa yang diubah (contoh: `Pegawai`, `PengaturanBobot`).
   - **Date/Time:** Waktu pasti (Timestamp) kejadian.
   - **Properties (Detail):** Klik tombol detail untuk melihat perbandingan **sebelum** dan **sesudah** data diubah (sistem menyimpan data *old* vs *new*).

### 2.4 Manajemen Periode
Seluruh pemilihan diatur melalui Periode Penilaian.
1. Buka menu **Master Data** -> **Periode**.
2. Klik **Tambah Periode** dan tentukan jadwal untuk:
   - **Tanggal Penginputan:** Masa Admin / Kepala Umum mengunggah nilai CKP dan Absensi.
   - **Tanggal Survei:** Masa Pegawai menilai sejawatnya.
   - **Tanggal Voting:** Masa pemilihan suara.
3. Status periode akan otomatis berubah menyesuaikan tanggal saat ini dengan jadwal yang sudah dibuat oleh sistem *Cron Job*.

### 2.5 Manajemen Bobot (Pengaturan Bobot Penalti)
1. Buka menu **Sistem** -> **Pengaturan Bobot**.
2. Anda dapat mengatur pengurangan skor (penalti) untuk setiap jenis pelanggaran absensi (contoh: *Terlambat = -0.5*, *Tanpa Keterangan = -2*).
3. **Aturan:** Setiap perubahan pada pengaturan bobot ini akan langsung mempengaruhi/mereset kalkulasi skor semua pegawai secara *real-time*. Hati-hati mengubah ini saat fase voting sedang berjalan.

### 2.6 Manajemen Glosarium
1. Buka menu **Master Data** -> **Glosarium**.
2. Fitur ini digunakan untuk mendefinisikan istilah-istilah di lingkungan BPS atau aplikasi.
3. Klik **Tambah** lalu masukkan kata kunci beserta pengertiannya, ini berguna agar pegawai memahami singkatan atau kode absensi (seperti CT, TL, PSW dll).

### 2.7 Manajemen FAQ
1. Buka menu **Sistem** -> **FAQ**.
2. Klik **Tambah FAQ** untuk menambahkan Pertanyaan Umum dan Jawabannya yang sering ditanyakan pengguna. FAQ ini akan tampil di halaman bantuan pegawai.

### 2.8 Manajemen Survey
1. Buka menu **Master Data** -> **Survey**.
2. Admin dapat menambahkan, mengedit, atau menghapus daftar pertanyaan yang akan dijawab pegawai pada fase Survei.
3. **Aturan:** Pertanyaan tidak boleh diubah jika *Fase Survei* sudah aktif dan sebagian orang sudah menjawab, karena akan menyebabkan inkonsistensi data.

### 2.9 Monitoring Survey
1. Buka menu **Monitoring Survey**.
2. Menu ini menampilkan persentase progress dan status setiap pegawai (Apakah sudah mengisi survei atau belum).
3. Anda dapat mengingatkan secara manual pegawai yang progresnya masih 0% atau *Belum Selesai*.

---

## 3. Panduan Untuk Kepala Umum

### 3.1 Tata Cara Input Absensi
Kepala Umum bertanggung jawab atas data kehadiran yang memotong poin dasar kandidat.
1. Buka menu **Data Presensi / Absensi**.
2. Klik **Unggah Excel** (atau klik *Download Template* jika belum ada).
3. Pilih file data absensi (pastikan NIP cocok dengan sistem).
4. Klik **Upload**. Sistem akan melakukan ekstraksi dan segera menghitung total penalti setiap pegawai.
5. (Opsional) Input Manual dapat dilakukan melalui fitur **Input Manual** untuk merevisi absen orang per orang.

### 3.2 Tata Cara Input Nilai CKP (Capaian Kinerja Pegawai)
1. Buka menu **Data CKP**.
2. Klik **Unggah Excel**.
3. Pastikan format Excel hanya berisi kolom NIP dan Nilai (Skala 0 - 100).
4. Klik **Upload**. Nilai CKP ini memiliki porsi besar dalam penyusunan otomatis kandidat Top 10.

---

## 4. Panduan Untuk Kepala Kantor (Pimpinan BPS)

### 4.1 Tata Cara Melakukan Review Nominasi (Penetapan Top 3)
Wewenang mutlak pemilihan pemenang berada di tangan Kepala Kantor, yang hanya dapat diakses pada **Fase Review**.
1. Login menggunakan akun Kepala Kantor.
2. Di halaman **Dashboard**, Anda akan disajikan deretan 10 Kandidat (Top 10) beserta data komprehensif:
   - Skor Objektif Total (CKP - Penalti Absensi + Nilai Survei).
   - Jumlah *Vote* yang dikumpulkan pada fase sebelumnya.
3. Anda dapat menimbang indikator tersebut dan memilih 3 pegawai terbaik dari 10 nominasi tersebut.
4. Klik **Tetapkan Pemenang (Top 3)**.
5. Konfirmasi pilihan Anda. Setelah ditetapkan, **Fase Pemilihan dinyatakan selesai (Selesai)** dan daftar pemenang akan dipublikasikan ke seluruh pegawai.
