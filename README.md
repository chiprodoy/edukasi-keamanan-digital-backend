# 🛠️ Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di lingkungan lokal (*development*).

---

## 📋 Prasyarat Sistem

Sebelum memulai, pastikan perangkat Anda telah terinstal:
* **PHP** >= 8.1
* **Composer** >= 2.x
* **Node.js** >= 18.x & **npm**
* **MySQL** / **MariaDB**
* **Git**

---

## 🚀 Langkah 1: Clone Repository

```bash
copy edukasi-keamanan-digital-backend d:/htdocs
cd d:/htdocs/edukasi-keamanan-digital-backend
```

---

## ⚙️ Langkah 2: Konfigurasi Backend (Laravel)

1. Masuk ke direktori backend:
   ```bash
   cd d:/htdocs/edukasi-keamanan-digital-backend
   ```

2. Instal seluruh dependensi PHP via Composer:
   ```bash
   composer install
   ```

3. Salin berkas lingkungan `.env`:
   ```bash
   cp .env.example .env
   ```

4. Generasi *Application Key*:
   ```bash
   php artisan key:generate
   ```

5. Konfigurasikan koneksi database pada berkas `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_edukasi_siber
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Jalankan migrasi tabel beserta *seeder* data awal:
   ```bash
   php artisan migrate --seed
   ```

7. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```

---

## 💻 Langkah 3: Konfigurasi Frontend (React + Vite)

copy edukasi-keamanan-digital-frontend  ke d:\htdoc\edukasi-keamanan-digital-frontend

1. Buka jendela terminal baru, lalu masuk ke direktori frontend:
   ```bash
   cd frontend
   ```

2. Instal seluruh dependensi JavaScript via npm:
   ```bash
   npm install
   ```

3. Buat berkas `.env` untuk frontend:
   ```bash
   cp .env.example .env
   ```
   *Isi dengan:*
   ```env
   VITE_API_BASE_URL=http://localhost:8000/api/v1
   ```

4. Jalankan server *development* React:
   ```bash
   npm run dev
   ```

5. Akses aplikasi melalui browser di tautan lokal yang tampil di terminal (biasanya `http://localhost:5173`).

---

## 🔑 Akun Uji Coba (Akses Default)

Setelah berhasil menjalankan `php artisan db:seed`, Anda dapat menguji aplikasi dengan kredensial berikut:

| Peran (Role) | Email | Kata Sandi |
| :--- | :--- | :--- |
| **Warga / User** | `warga@example.com` | `password` |
| **Admin** | `admin@example.com` | `password` |

---

## 🧪 Perintah Pengujian Tambahan

* **Menjalankan Linter Code (ESLint):**
  ```bash
  npm run lint
  ```
* **Build Frontend untuk Production:**
  ```bash
  npm run build
  ```
