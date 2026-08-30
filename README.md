<p align="center">
  <img src="https://dapo.kemendikdasmen.go.id/assets/logo-dapodik-BZDG7c6h.png" alt="Dapodik Logo" width="140" />
</p>

<h1 align="center">smansage/dapodik-sdk (PHP & Laravel)</h1>

<p align="center">
  <a href="https://packagist.org/packages/smansage/dapodik-sdk"><img src="https://img.shields.io/packagist/v/smansage/dapodik-sdk.svg?style=flat-square" alt="Latest Version on Packagist" /></a>
  <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square" alt="License: MIT" /></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-%3E%3D8.1-777bb4.svg?style=flat-square&logo=php" alt="PHP Version" /></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-FF2D20.svg?style=flat-square&logo=laravel" alt="Laravel Ready" /></a>
  <a href="https://www.instagram.com/smansagewithai/"><img src="https://img.shields.io/badge/Instagram-@smansagewithai-E4405F.svg?style=flat-square&logo=instagram&logoColor=white" alt="Instagram" /></a>
</p>

<p align="center">
  SDK PHP & Laravel modern, cepat, dan <i>Collection-ready</i> untuk integrasi penarikan data <b>WebService Dapodik Kemendikdasmen</b> (port 5774).
</p>

<p align="center">
  Dipublikasikan dan dikelola oleh <b>SMA Negeri 1 Gedeg (<a href="https://www.instagram.com/smansagewithai/">@smansagewithai</a>)</b><br />
  Dikembangkan oleh <b>Ryan Ardian</b>
</p>

---

> [!IMPORTANT]
> ### 📢 Pernyataan Penyangkalan (Disclaimer) & Misi Terbuka
> **`smansage/dapodik-sdk` adalah pustaka *Unofficial* (tidak resmi) dan independen.** Pustaka ini dikembangkan sebagai inisiatif komunitas sumber terbuka (*open-source*) oleh **SMA Negeri 1 Gedeg** dan **Ryan Ardian**, tanpa afiliasi langsung secara struktural dengan Kementerian Pendidikan Dasar dan Menengah (Kemendikdasmen).
>
> **Tujuan & Misi Pengembangan**:
> Pustaka ini lahir atas semangat memajukan transformasi digital dan interoperabilitas sistem pendidikan di Indonesia. Tujuan utamanya adalah **memberdayakan para pengembang perangkat lunak lintas platform dan multi-bahasa pemrograman** (PHP, Laravel, TypeScript, Node.js, Python, dll.) agar dapat mengintegrasikan sistem informasi sekolah, LMS, E-Rapor, presensi cerdas, serta analitik data pendidikan secara lebih cepat, aman, terstandarisasi, dan terbebas dari kompleksitas teknis protokol WebService lokal Dapodik.
>
> Seluruh hak cipta nama, logo, dan merek dagang **Dapodik (Data Pokok Pendidikan)** adalah milik sah **Kementerian Pendidikan Dasar dan Menengah Republik Indonesia**.

---

## 🏛️ Latar Belakang & Referensi

Pustaka ini merupakan modernisasi dan implementasi klien (*client SDK*) berbasis **PHP & Laravel** yang mengadaptasi spesifikasi integrasi WebService Dapodik dari repositori referensi karya **Ade Reksi Susanto** ([`adereksisusanto/dapodik-api-php`](https://github.com/adereksisusanto/dapodik-api-php)).

Paket ini dirancang khusus untuk mempermudah sinkronisasi dan integrasi data pendidikan ke ekosistem **Laravel (10, 11, dan 12)** maupun aplikasi PHP 8.1+ mandiri, dengan integrasi penuh **Laravel Collection**, **Facade**, **Service Provider Auto-Discovery**, **Auto-Pagination**, penanganan relasi bertingkat (*nested relations*), serta dukungan operasi **HTTP POST** (seperti pengiriman nilai rapor dan mata evaluasi).

---

## ⚡ Fitur Utama & Peningkatan Modern

- 🚀 **Integrasi Penuh Laravel**: Dilengkapi `DapodikServiceProvider` (*Auto-Discovery*), Facade `Dapodik`, dan file konfigurasi `config/dapodik.php` yang siap di-publish.
- 📦 **Hasil Respon Berbasis `Illuminate\Support\Collection`**: Seluruh data yang ditarik otomatis dibungkus ke dalam Laravel Collection, memungkinkan chaining method seperti `->where()`, `->pluck()`, `->groupBy()`, dan `->map()`.
- 🔄 **Auto-Pagination Cerdas**: Tarik ribuan data siswa dan guru secara otomatis tanpa perlu membuat perulangan (*looping*) manual.
- 📝 **Dukungan Operasi Tulis (POST)**: Memfasilitasi pengiriman nilai rapor (`postNilai`) dan mata evaluasi (`postMatevRapor`) langsung ke WebService Dapodik.
- 🛡️ **Penanganan Error Terstruktur**: Hierarki exception jelas (`DapodikAuthException`, `DapodikConnectionException`, `DapodikHttpException`).
- ⏱️ **Koneksi Cepat & Andal**: Dibangun di atas library HTTP standar industri (`GuzzleHttp 7.x`) dengan manajemen timeout terukur.

---

## ⚠️ Kepatuhan UU Perlindungan Data Pribadi (UU PDP No. 27/2022)

Aplikasi Dapodik memproses **Data Pribadi Siswa dan Guru** (NIK, NISN, no kontak, riwayat keluarga, dll.). Pengembang wajib mematuhi **UU Perlindungan Data Pribadi No. 27 Tahun 2022 Pasal 67**. Jaga kerahasiaan token dan dilarang memublikasikan data tanpa hak.

---

## 📥 Instalasi

Pasang paket melalui Composer:

```bash
composer require smansage/dapodik-sdk
```

---

## ⚡ Penggunaan di Laravel (Sangat Cepat)

### 1. Konfigurasi Environment (`.env`)

Tambahkan variabel berikut ke file `.env` Laravel Anda:

```env
DAPODIK_HOST=192.168.1.100
DAPODIK_PORT=5774
DAPODIK_NPSN=20300001
DAPODIK_TOKEN=TOKEN_WEBSERVICE_DAPODIK
```

*(Opsional) Publish file konfigurasi jika ingin mengubah timeout atau opsi lanjutan:*
```bash
php artisan vendor:publish --tag=dapodik-config
```

### 2. Contoh Penggunaan di Controller / Service

Package ini otomatis terdaftar di Laravel (*Auto-Discovery*). Anda bisa langsung menggunakan **Facade `Dapodik`**:

```php
namespace App\Http\Controllers;

use Smansage\Dapodik\Laravel\Facades\Dapodik;

class SiswaController extends Controller
{
    public function index()
    {
        // 1. Ambil Profil Sekolah
        $sekolah = Dapodik::sekolah()->first();

        // 2. Ambil Siswa (mengembalikan Laravel Collection)
        $siswa = Dapodik::getPesertaDidik(['page' => 1, 'limit' => 50]);

        // Manfaatkan kekuatan Laravel Collection:
        $siswaLakiLaki = $siswa->where('jenis_kelamin', 'L');
        $namaSiswa = $siswa->pluck('nama');

        // 3. Ambil Rombel (Kelas) beserta relasi anggota & pembelajaran
        $rombel = Dapodik::rombel('20241'); // Semester 2024/2025 Ganjil

        return response()->json([
            'sekolah' => $sekolah['nama'] ?? null,
            'total_siswa' => $siswa->count(),
            'laki_laki' => $siswaLakiLaki->count(),
            'daftar_nama' => $namaSiswa,
            'rombel' => $rombel,
        ]);
    }
}
```

---

## 🐘 Penggunaan di PHP Native (Tanpa Framework)

Jika Anda menggunakan PHP native atau framework lain (seperti Slim, Symfony, CodeIgniter), gunakan `DapodikClient` atau Factory `Dapodik`:

```php
use Smansage\Dapodik\DapodikClient;

require 'vendor/autoload.php';

$client = new DapodikClient([
    'host' => '192.168.1.100',
    'port' => 5774,
    'npsn' => '20300001',
    'token' => 'TOKEN_WEBSERVICE_DAPODIK',
    'timeout' => 30.0,
]);

// 1. Ambil Profil Sekolah
$sekolah = $client->getSekolah();
echo "Sekolah: " . $sekolah->first()['nama'] . "\n";

// 2. Ambil Siswa
$siswa = $client->pd(['page' => 1, 'limit' => 50]);
echo "Jumlah siswa ditarik: " . $siswa->count() . "\n";
```

---

## 🔄 Auto-Pagination (Tarik Ribuan Data Otomatis)

Menarik seluruh data siswa atau guru tanpa perlu repot menghitung halaman dan melakukan looping manual:

```php
// Tarik seluruh siswa sekolah secara otomatis
$semuaSiswa = $client->fetchAllPesertaDidik(
    limit: 100,
    delayMs: 150, // Jeda 150ms per halaman agar server Dapodik tidak overload
    onProgress: function (int $page, int $batchCount, int $totalCount) {
        echo "Halaman {$page}: ditarik +{$batchCount} siswa (Total terakumulasi: {$totalCount})\n";
    }
);

echo "Total seluruh siswa: " . $semuaSiswa->count() . "\n";
```

---

## 📋 Daftar Endpoint Lengkap

| Endpoint WebService | Method (Standar) | Method (Alias PHP) | Deskripsi |
| :--- | :--- | :--- | :--- |
| **`/getSekolah`** | `Dapodik::getSekolah($params)` | `Dapodik::sekolah()` | Profil & izin operasional sekolah |
| **`/getPengguna`** | `Dapodik::getPengguna($params)` | `Dapodik::pengguna()` | Akun operator / pengguna Dapodik |
| **`/getGtk`** | `Dapodik::getGtk($params)` | `Dapodik::gtk()` | Data Guru & Tenaga Kependidikan (GTK) |
| **`/getRombonganBelajar`** | `Dapodik::getRombonganBelajar($sem)` | `Dapodik::rombel($sem)` | Rombel beserta anggota & mapel |
| **`/getPesertaDidik`** | `Dapodik::getPesertaDidik($params)` | `Dapodik::pd()` | Data seluruh siswa lengkap |
| **`/getMataPelajaran`** | `Dapodik::getMataPelajaran($params)`| `Dapodik::mataPelajaran()`| Referensi mata pelajaran nasional |
| **`/getMatevNilai`** | `Dapodik::getMatevNilai($params)` | `Dapodik::matevNilai()` | Referensi mata evaluasi nilai |
| **`/postNilai`** | `Dapodik::postNilai($body, $params)` | - | Pengiriman nilai rapor (HTTP POST) |
| **`/postMatevRapor`** | `Dapodik::postMatevRapor($body, $params)` | - | Pengiriman mata evaluasi (HTTP POST)|

---

## 🛡️ Penanganan Error (Error Handling)

```php
use Smansage\Dapodik\Exceptions\DapodikAuthException;
use Smansage\Dapodik\Exceptions\DapodikConnectionException;
use Smansage\Dapodik\Exceptions\DapodikHttpException;
use Smansage\Dapodik\Exceptions\DapodikException;

try {
    $siswa = Dapodik::getPesertaDidik();
} catch (DapodikAuthException $e) {
    // Error 401/403: Token salah atau IP client belum di-whitelist di Dapodik
    logger()->error("Autentikasi gagal: " . $e->getMessage());
} catch (DapodikConnectionException $e) {
    // Server Dapodik mati / port 5774 tidak bisa diakses
    logger()->error("Koneksi gagal: " . $e->getMessage());
} catch (DapodikHttpException $e) {
    // Error HTTP 500/404 dari WebService
    logger()->error("HTTP Error [{$e->getCode()}]: " . $e->getMessage());
} catch (DapodikException $e) {
    // Error umum lainnya
    logger()->error("Dapodik Error: " . $e->getMessage());
}
```

---

## 📄 Lisensi & Kontributor

- **Lisensi**: [MIT License](LICENSE) &copy; 2026 **Ryan Ardian, SMA Negeri 1 Gedeg (smansage)**.
- **Pengembang**: **Ryan Ardian** ([@smansagewithai](https://www.instagram.com/smansagewithai/)).
- **Inspirasi & Atribusi**: Adaptasi pustaka PHP Dapodik oleh **Ade Reksi Susanto** ([`adereksisusanto/dapodik-api-php`](https://github.com/adereksisusanto/dapodik-api-php)).

---

## 📑 Dokumen Repositori

- 📜 [Changelog](CHANGELOG.md) - Catatan riwayat versi dan rilis.
- 🛡️ [Security Policy & UU PDP](SECURITY.md) - Kebijakan keamanan & perlindungan data pribadi.
- 🤝 [Contributing Guidelines](CONTRIBUTING.md) - Panduan kontribusi kode.
- 📜 [Code of Conduct](CODE_OF_CONDUCT.md) - Kode etik komunitas kontributor.
