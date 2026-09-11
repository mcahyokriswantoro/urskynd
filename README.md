# URSKYND - AI Skin Companion

URSKYND adalah aplikasi cerdas berbasis AI yang membantu Anda mengenali kondisi kulit, memantau perkembangan harian melalui jurnal, dan menemukan rutinitas skincare terbaik yang dipersonalisasi khusus untuk Anda.

Dibangun dengan pendekatan *Mobile First* menggunakan teknologi modern: **Laravel 11, Blade, Tailwind CSS, Alpine.js, dan MySQL.**

## ✨ Fitur Utama
1. **AI Skin Analysis**: Analisis kondisi kulit lewat foto *selfie* (Hydration, Acne, Wrinkles, dll).
2. **Skin Journaling**: Catat kondisi kulit, *mood*, dan keluhan harian (dilengkapi grafik tren).
3. **Skincare Routine**: Atur produk pagi dan malam, centang saat selesai.
4. **Product Catalog**: Ratusan *database* produk dengan filter pintar dan rekomendasi "AI Match".
5. **Gamification**: Kumpulkan poin dan raih *Badges* eksklusif dari setiap aktivitas sehat.

---

## 🚀 Panduan Instalasi (Development)

Berikut adalah panduan langkah demi langkah untuk menjalankan URSKYND di komputer lokal Anda:

### Persyaratan Sistem
* PHP >= 8.2
* Composer
* Node.js & NPM
* MySQL

### Langkah Instalasi
1. **Clone repository ini**
   ```bash
   git clone https://github.com/USERNAME/urskynd.git
   cd urskynd
   ```

2. **Salin file .env**
   ```bash
   cp .env.example .env
   ```
   *Atur konfigurasi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) di file `.env`.*

3. **Install dependensi PHP & Generate Key**
   ```bash
   composer install
   php artisan key:generate
   ```

4. **Install dependensi Frontend**
   ```bash
   npm install
   ```

5. **Migrasi Database & Isi Data Dummy (PENTING)**
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Perintah ini akan membuat tabel dan mengisi ratusan produk, kategori, tipe kulit, dan akun dummy)*

6. **Jalankan Aplikasi**
   Buka dua terminal terpisah:
   
   **Terminal 1 (Vite/Tailwind):**
   ```bash
   npm run dev
   ```
   
   **Terminal 2 (Laravel Server):**
   ```bash
   php artisan serve
   ```

7. **Akses Aplikasi**
   Buka browser dan akses: `http://127.0.0.1:8000`

---

## 🔑 Akun Demo (Seeder)

Anda dapat menggunakan akun berikut untuk masuk setelah menjalankan *seeder*:

**User Biasa:**
* Email: `user@urskynd.test`
* Password: `password`

**Admin:**
* Email: `admin@urskynd.test`
* Password: `password`

---

## 🎨 Design System
* **Primary Color:** `#9A6A52` (Cokelat premium / Earthy)
* **Background:** `#FDFBF9` (Cream)
* **Fonts:** Inter (Sans-serif) & Playfair Display (Serif)
* **UI Style:** Soft shadows, rounded corners (mobile-app feel)

---

*Dibuat dengan ❤️ untuk kulit yang lebih sehat.*
