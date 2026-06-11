# Deploy ke Qwords — chatbot-keperawatan.damgocompany.com

## 1. cPanel → PHP 8.3

Select PHP Version → pilih **8.3** untuk subdomain ini.

## 2. Buat database MySQL

MySQL® Databases → buat DB + user → ALL PRIVILEGES.

## 3. Clone repo (Git Version Control atau Terminal)

```bash
cd ~
git clone https://github.com/mcahyokriswantoro/chatbot-keperawatan.git chatbot-keperawatan.damgocompany.com
cd chatbot-keperawatan.damgocompany.com
```

## 4. Document root

Domains → edit subdomain → arahkan ke:

```
/home/CPANEL_USER/chatbot-keperawatan.damgocompany.com/public
```

## 5. Upload build assets (wajib — tidak ada di Git)

Dari laptop, upload folder `public/build/` ke server (File Manager / FTP).

## 6. Buat .env

Salin `deploy/env-production.example` → `.env`, isi DB credentials.

## 7. Jalankan setup

```bash
cd ~/chatbot-keperawatan.damgocompany.com
bash deploy/server-setup.sh
```

## 8. Cron (setiap menit)

```
* * * * * cd /home/CPANEL_USER/chatbot-keperawatan.damgocompany.com && php artisan schedule:run >> /dev/null 2>&1
```

## 9. SSL

cPanel → SSL/TLS Status → AutoSSL untuk subdomain.
