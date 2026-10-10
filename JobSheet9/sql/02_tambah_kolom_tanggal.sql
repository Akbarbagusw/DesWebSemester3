-- Jobsheet 8 - Latihan Tambahan 7.4 Poin 2:
-- Menambahkan kolom tanggal_ditambahkan ke tabel buku
-- Perintah SQL DDL:

ALTER TABLE buku
ADD COLUMN IF NOT EXISTS tanggal_ditambahkan TIMESTAMP DEFAULT NOW();
