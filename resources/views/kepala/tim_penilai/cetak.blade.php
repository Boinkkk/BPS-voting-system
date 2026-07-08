<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Tugas Tim Penilai Kinerja</title>
    <style>
        @page { size: A4; margin: 2cm; }
        body { font-family: "Times New Roman", Times, serif; color: #000; line-height: 1.5; font-size: 12pt; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-surat h1 { font-size: 16pt; margin: 0; text-transform: uppercase; font-weight: bold; }
        .kop-surat h2 { font-size: 14pt; margin: 5px 0 0 0; }
        .kop-surat p { font-size: 10pt; margin: 5px 0 0 0; font-style: italic; }
        .judul-surat { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .judul-surat .underline { text-decoration: underline; text-transform: uppercase; }
        .isi-surat { text-align: justify; }
        .table-tim { width: 90%; margin: 20px auto; border-collapse: collapse; }
        .table-tim th, .table-tim td { border: 1px solid #000; padding: 8px 12px; text-align: left; }
        .table-tim th { background-color: #f0f0f0; }
        .ttd-box { width: 300px; float: right; margin-top: 40px; text-align: center; }
        .ttd-box p { margin: 0; }
        .ttd-space { height: 80px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .clearfix::after { content: ""; display: table; clear: both; }
        
        /* Print Utilities */
        @media print {
            .no-print { display: none !important; }
        }
        .btn-print {
            display: inline-block; padding: 10px 20px; background-color: #0091d5; color: white;
            text-decoration: none; border-radius: 5px; font-family: sans-serif; cursor: pointer;
            border: none; position: fixed; bottom: 20px; right: 20px; font-weight: bold;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="btn-print no-print">🖨️ Cetak Surat</button>

    <div class="kop-surat">
        <h1>BADAN PUSAT STATISTIK</h1>
        <h2>TIM PENILAI KINERJA PEGAWAI TERBAIK BerAKHLAK</h2>
        <p>Jl. Dr. Sutomo No.6-8, Ps. Baru, Kecamatan Sawah Besar, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10710</p>
    </div>

    <div class="judul-surat">
        <div class="underline">SURAT TUGAS</div>
        <div>Nomor: ST.{{ \Carbon\Carbon::now()->format('m/Y') }}/BPS/{{ $periode->id }}</div>
    </div>

    <div class="isi-surat">
        <p>Berdasarkan Peraturan Badan Pusat Statistik mengenai Pedoman Pemberian Penghargaan bagi Pegawai Berprestasi, dengan ini Kepala Bagian memberikan tugas kepada:</p>

        <table class="table-tim">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="35%">Nama</th>
                    <th width="30%">Jabatan Asal</th>
                    <th width="30%">Peran dalam Tim</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align: center;">1</td>
                    <td>{{ $penanggungJawab->pegawai->nama }}</td>
                    <td>{{ $penanggungJawab->pegawai->jabatan }}</td>
                    <td><b>Penanggung Jawab</b></td>
                </tr>
                <tr>
                    <td style="text-align: center;">2</td>
                    <td>{{ $ketua->pegawai->nama }}</td>
                    <td>{{ $ketua->pegawai->jabatan }}</td>
                    <td><b>Ketua</b></td>
                </tr>
                <tr>
                    <td style="text-align: center;">3</td>
                    <td>{{ $anggota->pegawai->nama }}</td>
                    <td>{{ $anggota->pegawai->jabatan }}</td>
                    <td><b>Anggota</b></td>
                </tr>
            </tbody>
        </table>

        <p>Untuk melaksanakan tugas pokok dan kewenangan sebagai <b>Tim Penilai Kinerja dan Pemberian Reward kepada ASN dengan Kinerja Terbaik</b> pada periode <b>{{ $periode->nama }}</b> ({{ \Carbon\Carbon::parse($periode->tanggal_mulai)->format('d F Y') }} s.d {{ \Carbon\Carbon::parse($periode->tanggal_selesai)->format('d F Y') }}).</p>
        
        <p>Tim yang ditunjuk diberikan hak akses penuh ke dalam Sistem Informasi BPS untuk mengelola data Kandidat, memantau absensi, melakukan pengawasan manajemen survei, dan mengawal integritas pemungutan suara (Voting) hingga hasil akhir diserahkan kembali kepada Kepala Bagian.</p>

        <p>Surat tugas ini berlaku sejak tanggal ditetapkan sampai dengan berakhirnya periode kinerja yang bersangkutan. Segala biaya yang timbul akibat diterbitkannya Surat Tugas ini dibebankan pada DIPA BPS Tahun Anggaran yang berjalan.</p>

        <p>Demikian surat tugas ini dibuat agar dapat dilaksanakan dengan penuh tanggung jawab dan integritas.</p>
    </div>

    <div class="ttd-box">
        <p>Ditetapkan di Jakarta</p>
        <p>Pada tanggal {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <p style="margin-top: 10px;">Kepala Bagian,</p>
        <div class="ttd-space"></div>
        <p class="ttd-name">{{ $kepala ? $kepala->nama : '______________________' }}</p>
        <p>NIP. {{ $kepala ? $kepala->nip : '______________________' }}</p>
    </div>

    <div class="clearfix"></div>

    <script>
        // Opsional: Langsung memicu print saat halaman dibuka jika diinginkan
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
