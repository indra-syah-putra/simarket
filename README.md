# SI MARKET

Aplikasi web untuk **manajemen stok dan perhitungan uang bersih** pada toko sembako. Dibangun dengan Laravel 10, Tailwind CSS, dan Alpine.js.

Sistem ini menggunakan **satu pintu login** (single user / satu role: Administrator) dan fokus pada pencatatan **restock (pembelian)** serta **penjualan**, sehingga uang bersih dapat dihitung otomatis tanpa integrasi pembayaran.

## Fitur

- **Satu pintu login** dengan satu role (Administrator).
- **Dashboard** ringkasan stok, transaksi terbaru, dan uang bersih bulan berjalan.
- **Data Barang** dengan dua satuan: satuan beli (besar) dan satuan jual (eceran) beserta nilai konversinya.
- **Restock (Pembelian)** — menambah stok barang, otomatis mengurangi uang bersih (pengeluaran).
- **Penjualan** — mengurangi stok, otomatis menambah uang bersih (pemasukan). Mendukung penjualan eceran dengan konversi satuan.
- **Laporan** stok & keuangan per periode (Awal, Restock, Jual, Akhir, Nilai Stok) yang siap dicetak.
- **Riwayat Transaksi** dengan filter jenis, bulan, dan tahun.
- **Profil** untuk mengubah nama dan password.

## Konsep Perhitungan

**Uang bersih = Total Penjualan − Total Restock (Pembelian)**

Stok disimpan dalam **satuan beli** (satuan besar). Penjualan dapat dilakukan dalam **satuan jual** (satuan eceran), lalu dikonversi otomatis ke satuan beli.

### Contoh: Wortel

| Pengaturan Barang | Nilai |
| --- | --- |
| Satuan beli | `kg` |
| Satuan jual | `rubak` (¼ kg) |
| Isi per satuan jual | `0.25` |
| Harga beli | Rp 10.000 / kg |
| Harga jual | Rp 10.000 / rubak |

| Aksi | Input | Efek Stok | Uang |
| --- | --- | --- | --- |
| Restock | 50 kg (total Rp 500.000) | +50 kg | keluar Rp 500.000 |
| Penjualan | 4 rubak × Rp 10.000 | −1 kg | masuk Rp 40.000 |
| Penjualan | 2 rubak × Rp 10.000 | −0.5 kg | masuk Rp 20.000 |

Untuk barang tanpa eceran (mis. Mie), cukup isi satuan jual sama dengan satuan beli dan **Isi per satuan jual = 1**.

## Teknologi

- [Laravel 10](https://laravel.com)
- PHP 8.1+ (diuji pada PHP 8.2)
- MySQL / MariaDB
- [Tailwind CSS](https://tailwindcss.com) + [Alpine.js](https://alpinejs.dev)
- [Vite](https://vitejs.dev)

## Instalasi

Prasyarat: PHP >= 8.1, Composer, Node.js & npm, serta MySQL.

```bash
# 1. Clone repository
git clone https://github.com/indra-syah-putra/simarket.git
cd simarket

# 2. Install dependency backend
composer install

# 3. Salin konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Buat database (contoh via MySQL)
#    CREATE DATABASE simarket;

# 5. Sesuaikan koneksi database pada file .env
#    DB_DATABASE=simarket
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Jalankan migrasi sekaligus membuat akun admin
php artisan migrate --seed

# 7. Install dependency frontend & build aset
npm install
npm run build

# 8. Jalankan aplikasi
php artisan serve
```

Aplikasi berjalan di `http://127.0.0.1:8000`.

### Akun Default

Setelah `migrate --seed`, tersedia akun administrator:

| Email | Password |
| --- | --- |
| `admin@simarket.test` | `password` |

> Disarankan segera mengganti password melalui menu **Profil** setelah login pertama.

## Konfigurasi Identitas Toko

Nama, alamat, kontak, dan nama penandatangan pada laporan dapat diatur melalui `.env`:

```env
COMPANY_NAME="SI MARKET"
COMPANY_ADDRESS="Jl. Raya Pasar No. 10, Kota Anda, Indonesia"
COMPANY_PHONE="(021) 1234-5678"
COMPANY_EMAIL="simarket@example.com"
```

## Struktur Menu

- **Dashboard** — ringkasan stok & uang bersih.
- **Data Barang** — kelola master barang beserta satuan dan harga.
- **Restock** — catat pembelian / penambahan stok.
- **Laporan & Transaksi** — berisi **Riwayat Transaksi** dan **Laporan**.
- **Transaksi Baru** — catat penjualan ke pembeli.

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](https://opensource.org/licenses/MIT).
