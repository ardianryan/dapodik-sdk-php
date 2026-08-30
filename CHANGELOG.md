# Changelog

Semua perubahan penting pada paket **`smansage/dapodik-sdk`** (PHP & Laravel) akan didokumentasikan di file ini.

Format changelog ini mengacu pada [Keep a Changelog](https://keepachangelog.com/id-ID/1.1.0/), dan proyek ini mematuhi [Semantic Versioning](https://semver.org/lang/id/).

---

## [1.0.0] - 2026-08-30

### Ditambahkan
- **DapodikClient Core**:
  - HTTP Client berbasis Guzzle dengan penanganan timeout dan normalisasi otomatis data `rows` / `data`.
  - Kembalian otomatis berupa **`Illuminate\Support\Collection`** untuk mempermudah manipulasi data di PHP & Laravel.
- **Integrasi Penuh Laravel**:
  - `DapodikServiceProvider` dengan fitur *Package Auto-Discovery*.
  - `Facades\Dapodik` untuk pemanggilan statis yang elegan di Controller (`Dapodik::pd()`, `Dapodik::sekolah()`, dll.).
  - File konfigurasi otomatis `config/dapodik.php` via `vendor:publish`.
- **Dukungan Endpoint Lengkap**:
  - `getSekolah` / `sekolah`
  - `getPengguna` / `pengguna`
  - `getGtk` / `gtk`
  - `getRombonganBelajar` / `rombel`
  - `getPesertaDidik` / `pd`
  - `getMataPelajaran` / `mataPelajaran`
  - `getMatevNilai` / `matevNilai`
  - `postNilai` & `postMatevRapor`
- **Fitur Auto-Pagination**:
  - `fetchAllPesertaDidik($limit, $delayMs, $onProgress)`
  - `fetchAllGtk($limit, $delayMs, $onProgress)`
- **Hierarki Exception**:
  - `DapodikException`, `DapodikAuthException`, `DapodikConnectionException`, `DapodikHttpException`.
- **10 Unit Tests PHPUnit** dengan 100% kelulusan.
