# Deploy via cPanel File Manager (Qwords)

Subdomain: **chatbot-keperawatan.damgocompany.com**

Paket siap upload ada di folder `deploy/`:
- `chatbot-keperawatan-deploy.zip` (~29 MB, sudah termasuk vendor + public/build)

---

## Langkah 1 — Login cPanel Qwords

1. Login member area Qwords → buka **cPanel** untuk `damgocompany.com`
2. **Select PHP Version** → pilih **PHP 8.3** (untuk akun/subdomain ini)

---

## Langkah 2 — Cari folder subdomain

1. Buka **File Manager**
2. Di sidebar kiri, cari folder subdomain, biasanya salah satu:
   - `chatbot-keperawatan.damgocompany.com`
   - atau `public_html/chatbot-keperawatan.damgocompany.com`
3. **Masuk ke folder tersebut** (jika ada file placeholder `index.html`, hapus saja)

---

## Langkah 3 — Upload & extract ZIP

1. Di File Manager, tetap berada **di dalam folder subdomain**
2. Klik **Upload** → pilih file dari laptop:
   ```
   c:\Users\Laptopku\Documents\GitHub\chatchat-simpel\deploy\chatbot-keperawatan-deploy.zip
   ```
3. Setelah upload selesai, kembali ke File Manager
4. Klik kanan ZIP → **Extract**
5. Pastikan struktur seperti ini (file `artisan` ada di root folder subdomain):
   ```
   chatbot-keperawatan.damgocompany.com/
     app/
     public/
     vendor/
     artisan
     ...
   ```
6. Hapus file ZIP setelah extract

---

## Langkah 4 — Ubah Document Root

1. cPanel → **Domains** (atau **Subdomains**)
2. Cari `chatbot-keperawatan.damgocompany.com`
3. Klik ikon pensil di kolom **Document Root**
4. Ubah path agar berakhiran **`/public`**

   Contoh:
   ```
   /home/username/chatbot-keperawatan.damgocompany.com/public
   ```

5. Simpan

---

## Langkah 5 — Buat file `.env`

1. File Manager → folder **root project** (sejajar dengan `app/`, bukan di dalam `public/`)
2. **+ File** → nama: `.env`
3. Salin isi dari `deploy/env-production.example`
4. Edit nilai database Anda:

```env
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_database_anda
```

Pastikan juga:
```env
APP_DEBUG=false
APP_URL=https://chatbot-keperawatan.damgocompany.com
```

Simpan file.

---

## Langkah 6 — Jalankan setup (tanpa Terminal)

1. Di File Manager, buka folder `deploy/` di server
2. **Copy** file `setup-once.php` ke folder `public/`
3. Buka browser:
   ```
   https://chatbot-keperawatan.damgocompany.com/setup-once.php?key=ck2026setup
   ```
4. Tunggu sampai muncul teks **SELESAI**
5. **WAJIB:** hapus `public/setup-once.php` dari File Manager

---

## Langkah 7 — Permission folder

Di File Manager, klik kanan folder → **Change Permissions**:

| Folder | Permission |
|--------|------------|
| `storage` | 775 (recursive) |
| `bootstrap/cache` | 775 (recursive) |

---

## Langkah 8 — SSL

cPanel → **SSL/TLS Status** → jalankan **AutoSSL** untuk subdomain.

---

## Langkah 9 — Cron job (sync artikel mingguan)

cPanel → **Cron Jobs** → tambah (setiap menit):

```
* * * * * cd /home/USERNAME/chatbot-keperawatan.damgocompany.com && php artisan schedule:run >> /dev/null 2>&1
```

Ganti `USERNAME` dengan username cPanel Anda (terlihat di pojok kanan atas cPanel).

---

## Cek hasil

Buka: https://chatbot-keperawatan.damgocompany.com

Harus tampil halaman **Chatbot Keperawatan**, bukan placeholder.

---

## Jika error 500

1. File Manager → `storage/logs/laravel.log` → baca baris error terakhir
2. Pastikan PHP **8.3**
3. Pastikan `.env` database benar
4. Pastikan document root sudah `.../public`
