### Wireframe: Halaman Login Petugas
```text
+-------------------------------------------------------+
| SIMPUS-Mini                                           |
|-------------------------------------------------------|
|                                                       |
|                  [ Login Petugas ]                    |
|                                                       |
|   Username : [_________________________]              |
|   Password : [_________________________]              |
|                                                       |
|                     [ Masuk ]                         |
|                                                       |
|           Belum punya akun? Daftar di sini            |
+-------------------------------------------------------+
```

### 4.2 Wireframe: Dashboard Petugas
```text
+-----------------------------------------------------------------------------+
| SIMPUS-Mini  Beranda | Buku | Anggota | Peminjaman | (Petugas: Akbar) Logout|
|-----------------------------------------------------------------------------|
| [ Total Buku: 12 ]   [ Total Anggota: 8 ]   [ Sedang Dipinjam: 3 ]          |
|                                                                             |
| Aksi Cepat:                                                                 |
| [ + Peminjaman Baru ]     [ + Pengembalian Buku ]     [ Cek Tunggakan ]     |
|                                                                             |
| Transaksi Peminjaman Terbaru                                                |
| --------------------------------------------------------------------------- |
| No Transaksi | Nama Anggota    | Judul Buku          | Tgl Pinjam | Status  |
| TR001        | Siti Aminah     | Laskar Pelangi      | 2026-09-20 | Dipinjam|
| TR002        | Budi Santoso    | Bumi Manusia        | 2026-09-18 | Dipinjam|
+-----------------------------------------------------------------------------+
```

### 4.3 Wireframe: Form Peminjaman Buku
```text
+-----------------------------------------------------------------------------+
| SIMPUS-Mini   Beranda | Buku | Anggota | Peminjaman | (Petugas: Akbar) Logout|
|-----------------------------------------------------------------------------|
|                       [ Form Transaksi Peminjaman ]                         |
|                                                                             |
|   No. Transaksi      : TR003 (Otomatis)                                     |
|   Pilih Anggota      : [v Siti Aminah (A001)                     ]          |
|   Pilih Buku         : [v Filosofi Teras (Stok: 5)               ]          |
|                        *Catatan: Buku stok 0 otomatis tidak ditampilkan     |
|   Tanggal Pinjam     : [ 2026-09-23 ] (Hari ini)                            |
|   Batas Jatuh Tempo  : [ 2026-09-30 ] (7 Hari ke depan)                     |
|                                                                             |
|                     [ Simpan Peminjaman ]   [ Batal ]                       |
+-----------------------------------------------------------------------------+
```

### 4.4 Wireframe: Form Pengembalian Buku
```text
+-----------------------------------------------------------------------------+
| SIMPUS-Mini   Beranda | Buku | Anggota | Peminjaman | (Petugas: Akbar) Logout|
|-----------------------------------------------------------------------------|
|                       [ Form Transaksi Pengembalian ]                       |
|                                                                             |
|   Cari Transaksi     : [ Ketik ID / Nama Anggota / Judul Buku ] [ Cari ]    |
|                                                                             |
|   Data Transaksi Terpilih:                                                  |
|   - No. Transaksi    : TR001                                                |
|   - Nama Peminjam    : Siti Aminah (A001)                                   |
|   - Judul Buku       : Laskar Pelangi                                       |
|   - Tgl Pinjam       : 2026-09-15 | Batas Tempo: 2026-09-22                 |
|   - Status Keterlambatan: Terlambat 1 Hari (Denda: Rp 1.000)                |
|   - Kondisi Buku     : (•) Baik    ( ) Rusak    ( ) Hilang                  |
|                                                                             |
|             [ Proses Pengembalian & Tambah Stok ]   [ Batal ]               |
+-----------------------------------------------------------------------------+
```

### 4.5 Wireframe: Riwayat Transaksi Peminjaman
```text
+-----------------------------------------------------------------------------+
| SIMPUS-Mini   Beranda | Buku | Anggota | Peminjaman | (Petugas: Akbar) Logout|
|-----------------------------------------------------------------------------|
|                         [ Riwayat Transaksi Sirkulasi ]                     |
|                                                                             |
| Filter: [v Semua Status ]  Periode: [__________] s/d [__________] [ Filter ]|
|                                                                             |
| ID Trans | Anggota       | Buku              | Tgl Pinjam | Tgl Kembali| Denda|
| TR001    | Siti Aminah   | Laskar Pelangi    | 2026-09-15 | 2026-09-23 | 1.000|
| TR002    | Budi Santoso  | Bumi Manusia      | 2026-09-18 | -          | 0    |
+-----------------------------------------------------------------------------+
```

---

## 5. User Flow Inti Transaksi

### 5.1 User Flow: Peminjaman Buku
```text
[Petugas Login] -> [Dashboard Petugas] -> [Pilih Aksi "Peminjaman Baru"]
  -> [Pilih Anggota Aktif] -> [Pilih Buku (Stok > 0)]
  -> [Periksa Batas Kuota Anggota] -> [Klik "Simpan Peminjaman"]
  -> [Sistem: Buat Record Transaksi Baru]
  -> [Sistem: Kurangi Stok Buku -1]
  -> [Tampil Notifikasi Berhasil] -> [Kembali ke Dashboard]
```

### 5.2 User Flow: Pengembalian Buku
```text
[Dashboard Petugas] -> [Pilih Aksi "Pengembalian Buku"]
  -> [Cari ID Transaksi / Anggota] -> [Pilih Transaksi Aktif]
  -> [Periksa Kondisi Fisik Buku & Tanggal Jatuh Tempo]
  -> (Apakah Terlambat / Rusak?)
        |-- Ya  --> [Hitung Denda & Konfirmasi Pembayaran]
        |-- Tidak --> [Lanjut Konfirmasi Pengembalian]
  -> [Klik "Tandai Dikembalikan"]
  -> [Sistem: Ubah Status Transaksi jadi "Kembali"]
  -> [Sistem: Tambah Stok Buku +1]
  -> [Kembali ke Dashboard]
```

---

### 6.1 Latihan 1: Wireframe "Registrasi Anggota Baru" (Aktor Tamu)
#### Wireframe ASCII Art:
```text
+-----------------------------------------------------------------------------+
| SIMPUS-Mini      Beranda | Daftar Buku | Login Petugas                      |
|-----------------------------------------------------------------------------|
|                                                                             |
|                     [ FORM REGISTRASI ANGGOTA BARU ]                        |
|        Silakan lengkapi formulir di bawah ini untuk menjadi anggota         |
|                                                                             |
|   Nama Lengkap       : [__________________________________________]         |
|   No. Identitas      : [__________________________________________]         |
|   (NIK / NIM / NIS)                                                         |
|   Alamat Email       : [__________________________________________]         |
|   Nomor HP/WhatsApp  : [__________________________________________]         |
|   Jenis Kelamin      : (•) Laki-laki     ( ) Perempuan                      |
|   Alamat Domisili    : [__________________________________________]         |
|                        [__________________________________________]         |
|   Password Akun      : [____________________]                               |
|   Konfirmasi Password: [____________________]                               |
|                                                                             |
|            [ Daftar Sekarang ]            [ Batal / Kembali ]               |
|                                                                             |
|               Sudah terdaftar sebagai anggota? Masuk di sini                |
+-----------------------------------------------------------------------------+
```

---

### 6.2 Latihan 2: User Flow "Pencarian Anggota dengan Tunggakan Jatuh Tempo"
#### Diagram User Flow:
```text
+-------------------+
|   Petugas Login   |
+---------+---------+
          |
          v
+-------------------+
| Dashboard Petugas |
+---------+---------+
          |
          v
+---------------------------------------+
| Klik Menu/Filter "Tunggakan Terlambat"|
+-------------------+-------------------+
                    |
                    v
+-------------------------------------------------------------+
| Sistem Query: WHERE status = 'Dipinjam' AND tgl_tempo < NOW |
+-----------------------------+-------------------------------+
                              |
                              v
+-------------------------------------------------------------+
| Tampil Tabel Anggota Menunggak:                             |
| [No, Nama Anggota, Kontak HP, Judul Buku, Hari Lewat, Denda]|
+-----------------------------+-------------------------------+
                              |
        +---------------------+---------------------+
        |                                           |
        v                                           v
[Aksi 1: Hubungi Anggota]                 [Aksi 2: Proses Pengembalian]
        |                                           |
        v                                           v
[Kirim Pesan WhatsApp / Email]            [Hitung Total Akumulasi Denda]
        |                                           |
        v                                           v
[Update Status Catatan:                   [Konfirmasi Pembayaran Denda
 "Pengingat Terkirim"]                     & Serah Terima Buku Fisik]
        |                                           |
        |                                           v
        |                                 [Ubah Status: "Dikembalikan"]
        |                                           |
        |                                           v
        |                                 [Stok Buku Otomatis +1]
        |                                           |
        +---------------------+---------------------+
                              |
                              v
             +---------------------------------+
             | Kembali ke Dashboard Tunggakan  |
             | (Data Terupdate Secara Realtime)|
             +---------------------------------+
```

#### Rincian Logika Tiap Langkah:
1. **Pemicu Alur:** Petugas melihat indikator ringkasan "Buku Terlambat: 5" pada kartu statistik Dashboard, lalu menekan kartu atau tombol "Cek Tunggakan".
2. **Aturan Filter Sistem:** Sistem secara otomatis menghitung selisih hari:  
   $$\text{Hari Keterlambatan} = \max(0, \text{Hari Ini} - \text{Tanggal Jatuh Tempo})$$  
   Hanya data dengan nilai keterlambatan $> 0$ yang ditampilkan.
3. **Pilihan Tindakan Petugas:**
   - **Tindakan Preventif:** Menekan tombol `Kirim Notifikasi` untuk mengirim pesan otomatis ke kontak WhatsApp/Email anggota yang terdaftar di database.
   - **Tindakan Kuratif:** Jika anggota datang mengembalikan buku, Petugas langsung mengklik `Proses Denda & Kembali`, sistem mengalkulasikan biaya denda (misal Rp 1.000/hari), memproses konfirmasi pelunasan, lalu mengembalikan stok buku ke inventaris perpustakaan.

---

### 6.3 Latihan 3: Analisis & Identifikasi Edge Cases Tambahan
#### Batas Kuota Maksimal Peminjaman (*Max Borrow Limit*)
- **Skenario:** Seorang anggota ingin meminjam buku ke-4, padahal peraturan perpustakaan membatasi maksimal 3 buku aktif sekaligus.
- **Solusi:** Sistem menghitung jumlah peminjaman aktif anggota. Jika $\ge 3$, tombol `[ + Peminjaman Baru ]` untuk anggota tersebut dinonaktifkan (*disabled*) dan muncul label status `Kuota Peminjaman Penuh (3/3)`.

---

### 6.4 Latihan 4: Menerjemahkan Wireframe Login ke HTML Statis
#### Hasil Implementasi:
File implementasi: [`jobsheet4/login.html`](file:///C:/Users/apahayooo%21%21%21/Music/AKBAR/POLINEMA/NetbeansProject/TI_Semester3/DesWebSemester3/jobsheet4/login.html).

---

## 7. Konsistensi dengan Desain yang Sudah Berjalan

Seluruh halaman dan rancangan baru pada Jobsheet 4 mematuhi prinsip konsistensi desain sistem SIMPUS-Mini:
1. **Palet Warna Utama:** Menggunakan aksen hijau `#2e7d32` untuk identitas navigasi, tombol utama, dan *card highlight*.
2. **Tipografi & Reset CSS:** Font *system-ui* modern (Segoe UI, Arial, sans-serif) dengan *box-sizing: border-box*.
3. **Responsive Design (Mobile-First):** Memanfaatkan Flexbox dan CSS Grid serta navigasi tersembunyi (*hamburger checkbox hack*) yang otomatis beradaptasi dari layar ponsel hingga desktop lebar.
4. **Skalabilitas Modul:** Formulir dirancang secara generik sehingga ketika memasuki tahap pemrograman PHP dan integrasi basis data pada jobsheet selanjutnya, struktur input dan penamaan atribut `name` sudah siap menerima pemrosesan POST/GET.

---