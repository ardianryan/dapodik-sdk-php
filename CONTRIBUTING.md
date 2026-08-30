# Panduan Kontribusi (Contributing Guidelines)

Terima kasih telah berkontribusi pada pengembangan **`smansage/dapodik-sdk`** untuk PHP & Laravel!

---

## 🛠️ Pengembangan Lokal

1. **Fork** repositori ini ke akun GitHub Anda.
2. **Clone** hasil fork:
   ```bash
   git clone https://github.com/ardianryan/dapodik-sdk-php.git
   cd dapodik-sdk-php
   ```
3. **Instal Dependensi**:
   ```bash
   composer install
   ```
4. **Jalankan Pengujian Unit**:
   ```bash
   composer test
   ```

---

## 🌿 Standar Kode & Pull Request

1. Gunakan standar penulisan **PSR-12**.
2. Pastikan seluruh pengujian lolos sebelum mengajukan PR:
   ```bash
   ./vendor/bin/phpunit
   ```
3. Gunakan pesan commit deskriptif (*Conventional Commits*).
