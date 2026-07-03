# Laporan Alur Sistem FAA Frozen Food

## Daftar Isi

1. Pendahuluan
2. Role Sistem
3. Alur Customer
4. Alur Admin
5. Alur Sistem (Midtrans)
6. Alur Sistem (COD)
7. Alur Pengiriman
8. Alur Pesanan Selesai
9. Status Pesanan
10. Status Pembayaran
11. Struktur Database Order
12. Ringkasan Workflow

---

# 1. Pendahuluan

Dokumen ini menjelaskan seluruh proses bisnis pada aplikasi **FAA Frozen Food**, mulai dari pelanggan melihat produk hingga pesanan selesai diterima. Sistem mendukung dua metode pembayaran:

- Midtrans (Online Payment)
- Cash On Delivery (COD)

---

# 2. Role Sistem

Sistem memiliki beberapa role yang saling berinteraksi.

## 1. Customer

Hak akses:

- Registrasi akun
- Login
- Mengubah profil
- Mengelola alamat
- Melihat produk
- Menambah produk ke keranjang
- Checkout
- Memilih metode pembayaran
- Melihat status pesanan
- Melihat resi pengiriman
- Konfirmasi pesanan diterima

---

## 2. Admin

Hak akses:

- Login dashboard
- Mengelola produk
- Mengelola kategori
- Mengelola stok
- Mengelola pesanan
- Memverifikasi pembayaran
- Memproses pesanan
- Menginput kurir
- Menginput nomor resi
- Mengubah status pesanan

---

## 3. Midtrans

Berfungsi sebagai payment gateway.

Tugas:

- Membuat transaksi
- Memproses pembayaran
- Mengirim callback pembayaran
- Mengubah status pembayaran

---

## 4. Kurir

Berfungsi mengirimkan pesanan kepada customer.

---

# 3. Alur Customer

## 1. Mengakses Website

Customer membuka website.

↓

## 2. Melihat Produk

Customer dapat melihat:

- Nama produk
- Harga
- Stok
- Foto
- Deskripsi

↓

## 3. Menambahkan Produk

Customer memilih produk.

↓

Klik

Tambah ke Keranjang

↓

Produk masuk ke keranjang.

---

## 4. Keranjang Belanja

Customer dapat:

- Menambah jumlah
- Mengurangi jumlah
- Menghapus produk
- Melihat subtotal

↓

Checkout

---

## 5. Login

Jika belum login:

- Register
- Login

↓

Masuk ke dashboard customer.

---

## 6. Lengkapi Profil

Customer mengisi:

- Nama
- Nomor HP
- Email

---

## 7. Tambah Alamat

Customer dapat:

- Tambah alamat
- Edit alamat
- Hapus alamat
- Pilih alamat utama

↓

Checkout

---

## 8. Checkout

Customer melihat:

- Produk
- Jumlah
- Ongkir
- Total pembayaran

↓

Pilih metode pembayaran.

---

## 9. Pilih Pembayaran

Pilihan:

- Midtrans
- COD

---

# 4. Alur Pembayaran Midtrans

Customer memilih Midtrans.

↓

Sistem membuat transaksi.

↓

Midtrans mengembalikan:

- Snap Token
- Transaction ID

↓

Customer diarahkan ke halaman pembayaran.

↓

Customer membayar.

↓

Midtrans memverifikasi pembayaran.

↓

Midtrans mengirim callback.

↓

Status pembayaran berubah menjadi

Paid

↓

Status order menjadi

Menunggu Diproses

---

# 5. Alur Pembayaran COD

Customer memilih

COD

↓

Order langsung dibuat.

↓

Status pembayaran

COD

↓

Status order

Menunggu Diproses

↓

Admin menerima pesanan.

---

# 6. Alur Admin

## Dashboard

Admin login.

↓

Dashboard

↓

Menu

Pesanan

↓

Daftar Pesanan

↓

Detail Pesanan

Admin melihat:

- Invoice
- Customer
- Nomor HP
- Produk
- Jumlah
- Total
- Metode pembayaran
- Status pembayaran

---

## Memproses Pesanan

Admin klik

Proses Pesanan

↓

Status

Menunggu Diproses

↓

Diproses

↓

Sedang Dikemas

---

## Input Pengiriman

Setelah paket siap.

Admin mengisi:

Kurir

Contoh:

JNE

Nomor Resi

Contoh:

JNE230601999991

↓

Klik

Simpan

↓

Status menjadi

Dikirim

---

# 7. Alur Customer Setelah Dikirim

Customer membuka

Pesanan Saya

↓

Melihat daftar pesanan.

↓

Klik

Detail

Customer dapat melihat:

- Invoice
- Produk
- Jumlah
- Ongkir
- Total
- Kurir
- Nomor Resi
- Status Pesanan
- Status Pembayaran

↓

Klik

Cek Resi

↓

Website kurir terbuka.

---

# 8. Pesanan Selesai

Ketika barang diterima.

Customer klik

Pesanan Diterima

ATAU

Admin mengubah status.

↓

Status menjadi

Selesai

↓

Order masuk ke

Riwayat Pesanan

---

# 9. Status Order

```text
Menunggu Pembayaran
        │
        ▼
Pembayaran Berhasil
        │
        ▼
Menunggu Diproses
        │
        ▼
Diproses
        │
        ▼
Sedang Dikemas
        │
        ▼
Dikirim
        │
        ▼
Selesai
```

Status lain:

- Dibatalkan

---

# 10. Status Midtrans

```text
Pending
    │
    ▼
Settlement
    │
    ▼
Paid
    │
    ▼
Diproses
    │
    ▼
Dikirim
    │
    ▼
Selesai
```

---

# 11. Status COD

```text
Order Dibuat
      │
      ▼
Diproses
      │
      ▼
Sedang Dikemas
      │
      ▼
Dikirim
      │
      ▼
Customer Membayar
      │
      ▼
Selesai
```

---

# 12. Struktur Database Order

Tabel

orders

| Field           | Keterangan              |
| --------------- | ----------------------- |
| id              | Primary Key             |
| invoice         | Nomor Invoice           |
| user_id         | Customer                |
| address_id      | Alamat Pengiriman       |
| subtotal        | Total Produk            |
| shipping_cost   | Ongkir                  |
| total           | Total Pembayaran        |
| payment_method  | midtrans / cod          |
| payment_status  | pending / paid / failed |
| order_status    | Status Order            |
| courier         | Nama Kurir              |
| tracking_number | Nomor Resi              |
| snap_token      | Token Midtrans          |
| transaction_id  | ID Midtrans             |
| created_at      | Waktu Dibuat            |

---

# 13. Ringkasan Workflow

```text
                CUSTOMER
                    │
                    ▼
            Melihat Produk
                    │
                    ▼
        Tambah ke Keranjang
                    │
                    ▼
            Login / Register
                    │
                    ▼
           Lengkapi Profil
                    │
                    ▼
             Pilih Alamat
                    │
                    ▼
               Checkout
                    │
                    ▼
        Pilih Metode Pembayaran
             │               │
             │               │
             ▼               ▼
        MIDTRANS            COD
             │               │
             ▼               ▼
       Bayar Online     Order Dibuat
             │               │
             └───────┬───────┘
                     ▼
             Pesanan Masuk
                ke Admin
                     │
                     ▼
            Verifikasi Pesanan
                     │
                     ▼
                 Diproses
                     │
                     ▼
             Sedang Dikemas
                     │
                     ▼
      Input Kurir & Nomor Resi
                     │
                     ▼
                  Dikirim
                     │
                     ▼
        Customer Melihat Resi
                     │
                     ▼
          Barang Telah Diterima
                     │
                     ▼
                  Selesai
```

---

# Kesimpulan

Alur sistem FAA Frozen Food terdiri dari empat aktor utama:

1. Customer sebagai pengguna yang melakukan pembelian.
2. Admin sebagai pengelola seluruh proses pesanan.
3. Midtrans sebagai payment gateway untuk transaksi online.
4. Kurir sebagai pihak yang mengirimkan pesanan.

Dengan alur tersebut, seluruh proses mulai dari pemesanan, pembayaran, pengemasan, pengiriman hingga penyelesaian pesanan dapat dikelola secara terintegrasi dan status pesanan dapat dipantau secara real-time oleh customer maupun admin.
