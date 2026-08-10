# 🛡️ Inventarisasi Aset & Pemetaan Scope Pengujian Keamanan (OWASP Top 10)
## Aplikasi Chatbot Keperawatan Pintar (Nersia Health)

**Judul Penelitian:** *Vulnerability Assessment and Security Enhancement of Chatbot Keperawatan Pintar Using the OWASP Top 10 Framework*  
**Tanggal Penyusunan:** 10 Agustus 2026  
**Status Scope:** Definisi Ruang Lingkup Pengujian (In-Scope Target Assessment)

---

## 1. Ringkasan Eksekutif & Ruang Lingkup (Scope Overview)

Dokumen ini memetakan seluruh komponen, endpoint URL/API, modul autentikasi, manajemen sesi, kontrol akses (otorisasi), dan mekanisme pertukaran data pada aplikasi **Chatbot Keperawatan Pintar**. Pemetaan ini berfungsi sebagai **Asset Inventory & Attack Surface Map** yang akan menjadi acuan utama dalam tahap pemindaian (*vulnerability scanning*) dan pengujian penetrasi (*penetration testing*) menggunakan kerangka kerja **OWASP Top 10**.

### Ringkasan Arsitektur & Vektor Keamanan

| Komponen Arsitektur | Teknologi / Implementasi | Potensi Vektor Risiko (OWASP Mapping) |
|---|---|---|
| **Web Framework** | Laravel 11.x (PHP 8.x) | A05:2021-Security Misconfiguration |
| **Authentication & Guard** | Laravel Breeze / Session Guard | A07:2021-Identification & Auth Failures |
| **Session Management** | Cookie-based HTTP Session (`laravel_session`, `XSRF-TOKEN`) | A07:2021-Identification & Auth Failures |
| **Authorization / Middleware** | Custom Middleware (`EnsureUserIsAdmin`, `EnsureScreeningCompleted`, `auth`, `verified`) | A01:2021-Broken Access Control (BPOA/IDOR) |
| **Data Exchange / Input** | Multipart Form-Data (Bukti Bayar, Foto Profil), JSON API, Query Parameters | A03:2021-Injection (XSS, SQLi, File Upload) |
| **Integrasi Eksternal** | WhatsApp Notification API (Fonnte / Wablas Gateway) | A10:2021-Server-Side Request Forgery (SSRF) / API Security |

---

## 2. Tabel Inventarisasi Aset & Endpoint Aplikasi

Tabel di bawah ini menginventarisasi seluruh endpoint yang berpotensi menjadi objek pengujian (*Target Testing Surface*).

| No | Modul / Komponen | Endpoint / URL | HTTP Method | Tingkat Akses (Authorization) | Vektor Pengujian Utama (OWASP Top 10) |
|---|---|---|---|---|---|
| **1. Autentikasi & Sesi** |
| 1.1 | Halaman Login | `/login` | GET | Publik / Anonymous | Cross-Site Scripting (XSS), Clickjacking |
| 1.2 | Proses Authenticate | `/login` | POST | Publik / Anonymous | Brute Force, Auth Bypass (A07:2021), Credential Stuffing |
| 1.3 | Halaman Registrasi | `/register` | GET | Publik / Anonymous | UI Redirection, Information Disclosure |
| 1.4 | Proses Registrasi | `/register` | POST | Publik / Anonymous | Mass Assignment (Privilege Escalation A01:2021), Parameter Tampering (Role Manipulation) |
| 1.5 | Logout | `/logout` | POST | Authenticated | Session Invalidation, CSRF pada Logout |
| 1.6 | Request Reset Password | `/forgot-password` | GET, POST | Publik / Anonymous | Rate Limiting Bypass, Email Enumeration |
| 1.7 | Form & Process New Password | `/reset-password`, `/reset-password/{token}` | GET, POST | Publik (Token Required) | Weak Token Validation, Insecure Password Reset Logic |
| **2. Manajemen Pengguna & Profil** |
| 2.1 | Halaman Profil Pasien | `/profil` | GET | Publik / Authenticated | Information Disclosure |
| 2.2 | Edit Profil Pengguna | `/profile` | GET, PATCH | Authenticated (`auth`) | Mass Assignment, Input Validation Bypass |
| 2.3 | Update Alamat | `/profile/address` | POST | Authenticated (`auth`) | Stored XSS, Insecure Data Handling |
| 2.4 | Hapus Akun Self | `/profile` | DELETE | Authenticated (`auth`) | Broken Authentication (Password verification bypass) |
| **3. Chatbot & Skrining Kesehatan** |
| 3.1 | Form Identitas Skrining | `/deteksi/identitas` | GET, POST | Authenticated (`auth`) | SQL Injection, Input Validation |
| 3.2 | Menu Skrining | `/deteksi`, `/deteksi/pilih-skrining` | GET | Authenticated (`auth`) | Direct Object Reference |
| 3.3 | Skrining Awal Chatbot | `/deteksi/skrining-awal` | GET | Authenticated (`auth`) | Logic Flaw, Session Manipulation |
| 3.4 | Session Chatbot Penyakit | `/deteksi/{disease}/skrining` | GET | Authenticated (`auth`) | Path Traversal / Parameter Pollution (`{disease}`) |
| 3.5 | Chatbot View Disease | `/deteksi/{disease}` | GET | Authenticated (`auth`) | IDOR / Broken Access Control |
| 3.6 | Endpoint API Store Screening | `/api/screening` | POST | Authenticated (`auth`) | API Input Validation, Mass Assignment, Data Tampering |
| 3.7 | Endpoint API TTS | `/api/screening-tts` | POST | Authenticated (`auth`) | SSRF, Resource Exhaustion, Command Injection |
| 3.8 | API Cascade Wilayah | `/api/wilayah/children` | GET | Authenticated (`auth`) | SQL Injection pada Query Param (`parent_code`) |
| 3.9 | Riwayat Skrining | `/riwayat`, `/riwayat/{id}` | GET | Authenticated (`auth`) | IDOR (A01:2021) — mengakses riwayat pasien lain |
| **4. Self Management & Health Monitoring** |
| 4.1 | Index & Detail Self Mgmt | `/self-management`, `/self-management/{disease}` | GET | Authenticated (`auth`) | LFI/RFI via `{disease}`, Unauthorized Access |
| 4.2 | Catat Aktivitas | `/self-management/activities` | POST | Authenticated (`screening.completed`) | CSRF, Broken Access Control |
| 4.3 | Toggle Status Aktivitas | `/self-management/activities/{log}/toggle` | PATCH | Authenticated (`screening.completed`) | IDOR (`{log}` parameter tampering) |
| 4.4 | Form & Preview Monitoring | `/monitoring`, `/monitoring/preview` | GET | Authenticated (`screening.completed`) | Information Disclosure, Input Bypassing |
| 4.5 | Submit Data Monitoring | `/monitoring` | POST | Authenticated (`screening.completed`) | Input Validation, Logic Flaws in Score Calculation |
| **5. Konsultasi & Layanan Chat Provider** |
| 5.1 | Katalog Konsultasi | `/konsultasi`, `/konsultasi/{category}` | GET | Authenticated / Public | XSS, Information Leakage |
| 5.2 | Checkout Konsultasi | `/konsultasi/{provider}/checkout` | GET | Authenticated (`auth`) | IDOR pada `{provider}` key |
| 5.3 | Redeem Voucher | `/konsultasi/{provider}/voucher` | POST | Authenticated (`auth`) | Race Condition, Voucher Logic Flaw (Double Redeem) |
| 5.4 | Proses Bayar Direct | `/konsultasi/{provider}/pay` | POST | Authenticated (`auth`) | Payment Bypass / Price Manipulation |
| 5.5 | Gateway DANA Payment | `/konsultasi/{provider}/pembayaran/dana` | POST | Authenticated (`auth`) | SSRF / API Parameter Injection |
| 5.6 | Upload Bukti Bayar | `/konsultasi/{provider}/pembayaran` | GET, POST | Authenticated (`auth`) | Unrestricted File Upload, MIME-Type Spoofing |
| 5.7 | Chat Interface Pasien | `/konsultasi/{provider}/chat` | GET | Authenticated (`auth`) | Uncontrolled Resource Access |
| 5.8 | Fetch Pesan Chat (Poll) | `/konsultasi/{provider}/chat/pesan` | GET | Authenticated (`auth`) | Broken Object Level Authorization (BOLA/IDOR) |
| 5.9 | Kirim Pesan Chat | `/konsultasi/{provider}/chat` | POST | Authenticated (`auth`) | Stored XSS via Chat Message, Rate Limiting |
| 5.10 | Pembatalan Konsultasi | `/konsultasi/{provider}/batal` | DELETE | Authenticated (`auth`) | IDOR / Unauthorized Cancel |
| **6. Modul E-Commerce Apotek (Obat)** |
| 6.1 | Katalog & Keranjang Obat | `/obat`, `/obat/keranjang` | GET, POST | Publik & Authenticated | Shopping Cart Manipulation, Negative Quantity |
| 6.2 | Modifikasi Item Keranjang | `/obat/keranjang/update`, `/obat/keranjang/{id}` | POST, DELETE | Authenticated (`auth`) | Business Logic Flaw, Negative Price/Quantity |
| 6.3 | Checkout Obat | `/obat/checkout` | POST | Authenticated (`auth`) | Price Tampering, Address Injection |
| 6.4 | Upload Bukti Bayar Obat | `/obat/pesanan/{order}/pembayaran/konfirmasi` | POST | Authenticated (`auth`) | Malicious File Upload (Web Shell / Executable Script) |
| 6.5 | Status Pesanan | `/obat/pesanan/{order}/status` | GET | Authenticated (`auth`) | IDOR (`{order}` Enumeration) |
| 6.6 | Pembatalan Pesanan Obat | `/obat/pesanan/{order}/batal` | DELETE | Authenticated (`auth`) | Unauthorized State Change |
| **7. Modul Service Homecare** |
| 7.1 | Index & Booking Form | `/homecare`, `/homecare/{package}/pesan` | GET, POST | Authenticated (`auth`) | IDOR, Parameter Tampering |
| 7.2 | Pembayaran & Konfirmasi | `/homecare/booking/{booking}/pembayaran/konfirmasi` | GET, POST | Authenticated (`auth`) | Arbitrary File Upload, Status Override |
| 7.3 | Pembatalan Booking | `/homecare/booking/{booking}/batal` | DELETE | Authenticated (`auth`) | IDOR on `{booking}` |
| **8. Dashboard Administrator (`/admin/*`)** |
| 8.1 | Admin Dashboard | `/admin/` | GET | Authenticated (`admin`) | Privilege Escalation, BPOA |
| 8.2 | Data Pasien & User Mgmt | `/admin/users`, `/admin/users/{user}` | GET | Authenticated (`admin`) | Sensitive Data Exposure (PII Leak) |
| 8.3 | Kelola Akses & Role | `/admin/access`, `/admin/access/provider` | GET, POST, DELETE | Authenticated (`admin`) | Privilege Escalation (Self-Promote to Admin) |
| 8.4 | Approval Provider / Mitra | `/admin/access/provider/{user}/approve` | POST | Authenticated (`admin`) | Authorization Bypass |
| 8.5 | Hasil Skrining Admin | `/admin/screenings`, `/admin/screenings/{id}` | GET | Authenticated (`admin`) | Unauthorized Data Access |
| 8.6 | Kelola & Reply Chat Admin | `/admin/konsultasi/chat/{order}/balas` | GET, POST | Authenticated (`admin`) | Stored XSS, Admin Impersonation |
| 8.7 | Toggle Free Consultation | `/admin/konsultasi/toggle-free/{category?}` | POST | Authenticated (`admin`) | Parameter Pollution / CSRF |
| 8.8 | Approve/Reject Konsultasi | `/admin/konsultasi/{order}/setujui` | POST | Authenticated (`admin`) | Unauthorized Financial State Change |
| 8.9 | Kelola Nakes (Provider) | `/admin/konsultasi/tenaga-kesehatan/*` | GET, POST, PUT, DELETE | Authenticated (`admin`) | Malicious File Upload (Foto Nakes), Stored XSS |
| 8.10 | Kelola Voucher Admin | `/admin/konsultasi/voucher/*` | GET, POST, PUT, DELETE | Authenticated (`admin`) | Discount Logic Manipulation |
| 8.11 | Kelola Stok & Order Obat | `/admin/obat/*` | GET, POST, PUT, DELETE | Authenticated (`admin`) | File Upload, IDOR, SQLi |
| 8.12 | Kelola Paket & Booking Homecare | `/admin/homecare/*` | GET, POST, PUT, DELETE | Authenticated (`admin`) | Business Logic Bypass |
| 8.13 | Kelola Artikel Edukasi | `/admin/edukasi/*` | GET, POST, PUT, DELETE | Authenticated (`admin`) | Stored XSS via Rich Text / Video Embed |
| 8.14 | Setting WhatsApp & System | `/admin/settings` | GET, POST | Authenticated (`admin`) | Stored XSS, Telemetry/Phone Number Spoofing |

---

## 3. Pemetaan Mekanisme Keamanan Utama (Security Controls)

### 3.1 Manajemen Sesi (Session Management)
- **Mekanisme:** Session berbasis cookie terenkripsi (`laravel_session`).
- **CSRF Protection:** Token `XSRF-TOKEN` / `_token` divalidasi pada setiap request berstatus mutasi data (`POST`, `PUT`, `PATCH`, `DELETE`).
- **Session Lifetime:** 120 menit (default Laravel configuration).
- **Pengujian OWASP:** Session Fixation, Session Hijacking, Concurrent Session Control, CSRF Token Reuse.

### 3.2 Mekanisme Otorisasi & Kontrol Akses (Access Control Mechanism)
Aplikasi menerapkan pembagian peran (*Role-Based Access Control* / RBAC) yang dikawal oleh Middleware Laravel:
1. `auth`: Memastikan request datang dari sesi terautentikasi.
2. `verified`: Memastikan email telah terverifikasi.
3. `admin`: Memastikan kolom `is_admin == true` pada tabel `users`.
4. `screening.completed`: Memastikan pengguna telah menyelesaikan minimal 1 sesi skrining sebelum mengakses *Self Management* dan *Monitoring*.

### 3.3 Komunikasi Data & Payload Handling
- **Format Payload:** Form URL Encoded, Multipart Form-Data (Upload Gambar/Bukti), JSON Payload.
- **Validasi Input:** Menggunakan kelas `Request::validate()` di level Controller.
- **Integrasi Pihak Ketiga:** Outbound HTTP request ke WhatsApp API (Fonnte/Wablas Gateway) saat notifikasi dikirim.

---

## 4. Rencana Pemetaan Target Pengujian terhadap OWASP Top 10 (2021)

| Kategori OWASP Top 10 (2021) | Target Modul / Endpoint Pengujian | Metode & Focus Pengujian |
|---|---|---|
| **A01:2021 – Broken Access Control** | `/riwayat/{id}`, `/konsultasi/{provider}/chat/pesan`, `/obat/pesanan/{order}/status`, `/admin/*` | Pengujian IDOR / BOLA pada ID pesanan & riwayat; pengujian Horizontal & Vertical Privilege Escalation. |
| **A02:2021 – Cryptographic Failures** | Transmit data sensitif, simpan password di DB, token reset password | Evaluasi enkripsi password (Bcrypt), proteksi PII pasien (Rekam medis), dan HTTP/HTTPS headers. |
| **A03:2021 – Injection** | `/api/screening`, `/api/wilayah/children`, `/deteksi/{disease}`, `/admin/edukasi` | Testing SQL Injection, Cross-Site Scripting (Stored XSS pada chat & edukasi, Reflected XSS), Path Traversal. |
| **A04:2021 – Insecure Design** | Modul Checkout, Reedem Voucher, Flow Keranjang Obat | Testing logic flaw (misal: voucher dipasang berulang kali, nominal harga negatif, manipulasi quantity). |
| **A05:2021 – Security Misconfiguration** | Debug mode (`APP_DEBUG`), HTTP Headers, File Storage Permission | Pemindaian error trace leaks, CORS headers, directory listing pada `/storage/`. |
| **A06:2021 – Vulnerable and Outdated Components** | Package Laravel, NPM Dependencies | Dependency Audit via `composer audit` & `npm audit` untuk mendeteksi CVE pada dependensi. |
| **A07:2021 – Identification and Authentication Failures** | `/login`, `/register`, `/forgot-password` | Testing Brute Force pada login/reset token, Weak Password policy, Session Fixation setelah login. |
| **A08:2021 – Software and Data Integrity Failures** | Upload Bukti Bayar (`/konsultasi/.../pembayaran`, `/obat/.../pembayaran`) | File Upload vulnerability (uploading PHP webshell, bypass extension / MIME-Type). |
| **A09:2021 – Security Logging and Monitoring Failures** | Logging pada `storage/logs/laravel.log` | Evaluasi keterlacakan aktivitas mencurigakan dan penanganan exception tanpa mengekspos data sensitif. |
| **A10:2021 – Server-Side Request Forgery (SSRF)** | Integrasi WhatsApp Gateway & Embed Video Youtube | Testing manipulasi parameter URL outbound request atau API callback. |

---

## 5. Kesimpulan & Rekomendasi Langkah Pengujian

Inventarisasi aset ini menetapkan ruang lingkup (*scope*) yang jelas untuk pengujian keamanan berbasis kerangka **OWASP Top 10 Framework**. Seluruh 45+ endpoint yang terdata di atas merepresentasikan permukaan serangan (*attack surface*) lengkap dari aplikasi **Chatbot Keperawatan Pintar**. 

Langkah pengujian selanjutnya yang direkomendasikan:
1. **Automated Scanning:** Menjalankan pemindaian otomatis menggunakan OWASP ZAP / Burp Suite Professional terhadap endpoint yang terpetakan.
2. **Manual Penetration Testing:** Fokus pada pengujian logika bisnis (*Business Logic Flaw*), *IDOR*, *File Upload Bypass*, dan *Privilege Escalation* pada fungsi-fungsi kritis (Checkout, Modul Admin, dan Pembayaran).
