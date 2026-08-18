# Diagram UML – Nersia.co
**Sistem Informasi Kesehatan Digital Keperawatan (nersia.co)**

---

## Gambar 1 – Use Case Diagram

```mermaid
%%{init: {'theme':'base','themeVariables':{'primaryColor':'#e8f4fd','primaryBorderColor':'#2980b9','lineColor':'#2c3e50','fontSize':'14px'}}}%%
graph LR
    %% Actors
    Patient(["🧑 Pasien / Pengguna"])
    Admin(["🛡️ Admin"])

    subgraph SYSTEM ["Sistem Nersia.co"]
        direction TB

        %% Auth group
        UC1(["Register Akun"])
        UC2(["Login"])
        UC3(["Edit Profil"])
        UC1 -. "«extend»" .-> UC2
        UC3 -. "«extend»" .-> UC2

        %% Screening group
        UC4(["Skrining Awal"])
        UC5(["Skrining Penyakit Lanjut"])
        UC5 -. "«include»" .-> UC4

        %% Health features
        UC6(["Monitoring Kesehatan Harian/Bulanan"])
        UC7(["Self-Management Panduan"])
        UC8(["Riwayat Skrining"])

        %% Consultation
        UC9(["Konsultasi Tenaga Kesehatan"])
        UC10(["Chat Konsultasi"])
        UC10 -. "«include»" .-> UC9

        %% Homecare
        UC11(["Pesan Layanan Homecare"])
        UC12(["Konfirmasi Pembayaran Homecare"])
        UC12 -. "«include»" .-> UC11

        %% Medicine
        UC13(["Beli Obat Online"])
        UC14(["Keranjang & Checkout Obat"])
        UC14 -. "«include»" .-> UC13

        %% Education
        UC15(["Baca Edukasi Kesehatan"])

        %% Emergency
        UC16(["Akses Info Darurat"])

        %% Admin features
        UC17(["Kelola Pengguna"])
        UC18(["Kelola Konsultasi & Provider"])
        UC19(["Kelola Obat"])
        UC20(["Kelola Homecare"])
        UC21(["Kelola Artikel Edukasi"])
        UC22(["Lihat Data Skrining & Monitoring"])
        UC23(["Kelola Voucher Konsultasi"])
        UC24(["Pengaturan Sistem"])
    end

    %% Pasien connections
    Patient --- UC2
    Patient --- UC1
    Patient --- UC3
    Patient --- UC4
    Patient --- UC5
    Patient --- UC6
    Patient --- UC7
    Patient --- UC8
    Patient --- UC9
    Patient --- UC10
    Patient --- UC11
    Patient --- UC13
    Patient --- UC15
    Patient --- UC16

    %% Admin connections
    Admin --- UC2
    Admin --- UC17
    Admin --- UC18
    Admin --- UC19
    Admin --- UC20
    Admin --- UC21
    Admin --- UC22
    Admin --- UC23
    Admin --- UC24
```

---

## Gambar 2 – Activity Diagram

```mermaid
%%{init: {'theme':'base','themeVariables':{'primaryColor':'#e8f5e9','primaryBorderColor':'#27ae60','lineColor':'#2c3e50','fontSize':'13px'}}}%%
flowchart TD
    START([● Mulai]) --> A[Buka Nersia.co]
    A --> B{Sudah Login?}
    B -- Tidak --> C[Halaman Registrasi / Login]
    C --> D[Isi Form Registrasi / Login]
    D --> E{Autentikasi Berhasil?}
    E -- Tidak --> C
    E -- Ya --> F[Dashboard Pengguna]
    B -- Ya --> F

    F --> G{Pilih Fitur}

    %% Screening path
    G -- Skrining --> H[Pilih Jenis Skrining]
    H --> H1[Isi Identitas Pasien]
    H1 --> H2[Jawab Pertanyaan Skrining]
    H2 --> H3[Hitung Skor & Evaluasi Risiko]
    H3 --> H4[Tampilkan Hasil & Rekomendasi]
    H4 --> H5{Risiko Tinggi?}
    H5 -- Ya --> H6[Tampilkan Panduan Darurat / Self-Management]
    H5 -- Tidak --> H7[Tampilkan Self-Management Sesuai Risiko]
    H6 & H7 --> SAVE1[(Simpan Riwayat Skrining)]

    %% Monitoring path
    G -- Monitoring --> M[Pilih Tipe Monitoring Harian/Bulanan]
    M --> M1[Isi Data Keluhan & Tanda Vital]
    M1 --> M2[Isi Data Kepatuhan Obat]
    M2 --> M3[Isi Self-Management Checklist]
    M3 --> M4[Hitung Skor & Simpan]
    M4 --> SAVE2[(Simpan Data Monitoring)]

    %% Consultation path
    G -- Konsultasi --> K[Pilih Kategori & Provider Konsultasi]
    K --> K1[Checkout & Pilih Metode Bayar]
    K1 --> K2[Upload Bukti Pembayaran]
    K2 --> K3{Admin Verifikasi}
    K3 -- Ditolak --> K4[Notif Ditolak]
    K3 -- Disetujui --> K5[Akses Chat Konsultasi]
    K5 --> K6[Kirim & Terima Pesan dengan Nakes]

    %% Homecare path
    G -- Homecare --> HC[Pilih Paket Homecare]
    HC --> HC1[Isi Data Pasien & Jadwal]
    HC1 --> HC2[Hitung Ongkir Berdasarkan Jarak]
    HC2 --> HC3[Upload Bukti Pembayaran]
    HC3 --> HC4{Admin Verifikasi}
    HC4 -- Ditolak --> HC5[Notif Ditolak]
    HC4 -- Disetujui --> HC6[Booking Dikonfirmasi]
    HC6 --> HC7[Nakes Datang ke Rumah & Selesaikan]

    %% Medicine path
    G -- Beli Obat --> OB[Cari & Tambah Obat ke Keranjang]
    OB --> OB1[Proses Checkout]
    OB1 --> OB2[Upload Bukti Bayar]
    OB2 --> OB3{Admin Setujui & Kirim}
    OB3 -- Ditolak --> OB4[Notif Ditolak]
    OB3 -- Dikirim --> OB5[Pesanan Selesai]

    %% Education path
    G -- Edukasi --> ED[Baca Artikel Kesehatan]
    ED --> END

    SAVE1 & SAVE2 & K6 & HC7 & OB5 --> END([● Selesai])
```

---

## Gambar 3 – Sequence Diagram

```mermaid
%%{init: {'theme':'base','themeVariables':{'primaryColor':'#fdf2e9','primaryBorderColor':'#e67e22','lineColor':'#2c3e50','fontSize':'13px'}}}%%
sequenceDiagram
    actor Pengguna
    participant LP as Login Page
    participant ACC as Account
    participant SCR as Screening
    participant MON as Monitoring
    participant CONS as Consultation
    participant HC as Homecare
    participant ADMIN as Admin System

    Pengguna->>LP: Akses Nersia.co
    LP-->>Pengguna: Tampilkan Halaman Login/Register

    Pengguna->>ACC: Register / Login
    ACC->>ACC: Validasi Kredensial
    ACC-->>Pengguna: Berhasil Login → Dashboard

    Pengguna->>ACC: Edit Profil (data diri, foto)
    ACC->>ACC: Simpan Perubahan
    ACC-->>Pengguna: Profil Diperbarui

    Pengguna->>SCR: Input Identitas Pasien
    SCR->>SCR: Simpan Data Identitas
    SCR-->>Pengguna: Tampilkan Menu Skrining

    Pengguna->>SCR: Jawab Pertanyaan Skrining
    SCR->>SCR: Evaluasi Skor & Tingkat Risiko
    SCR-->>Pengguna: Hasil Skrining & Rekomendasi Self-Management

    Pengguna->>MON: Input Data Monitoring Harian
    MON->>MON: Hitung Skor Keluhan, Kepatuhan Obat, Self-Management
    MON-->>Pengguna: Ringkasan Monitoring Tersimpan

    Pengguna->>CONS: Pilih Provider Konsultasi
    CONS-->>Pengguna: Halaman Checkout & Pembayaran
    Pengguna->>CONS: Upload Bukti Pembayaran
    CONS->>ADMIN: Notifikasi Pembayaran Masuk
    ADMIN->>ADMIN: Verifikasi Bukti Bayar
    ADMIN-->>CONS: Setujui / Tolak Pembayaran
    CONS-->>Pengguna: Status Pembayaran
    Pengguna->>CONS: Kirim Pesan Chat ke Nakes
    CONS-->>Pengguna: Balasan dari Nakes

    Pengguna->>HC: Pilih Paket Homecare & Isi Data
    HC->>HC: Hitung Ongkir (Jarak GPS)
    HC-->>Pengguna: Ringkasan Booking & Total Biaya
    Pengguna->>HC: Upload Bukti Pembayaran
    HC->>ADMIN: Notifikasi Booking Masuk
    ADMIN->>ADMIN: Verifikasi & Konfirmasi Jadwal
    ADMIN-->>HC: Booking Disetujui
    HC-->>Pengguna: Konfirmasi Jadwal Nakes ke Rumah
```

---

## Gambar 4 – Class Diagram

```mermaid
%%{init: {'theme':'base','themeVariables':{'primaryColor':'#f0e6ff','primaryBorderColor':'#8e44ad','lineColor':'#2c3e50','fontSize':'12px'}}}%%
classDiagram
    class User {
        +Int id
        +String name
        +String email
        +String password
        +String gender
        +String phone
        +Date date_of_birth
        +Int age
        +Float weight
        +Float height
        +String address
        +String occupation
        +String profile_photo
        +Boolean is_admin
        +Boolean is_approved
        +isAdmin() bool
        +isApproved() bool
        +isFemale() bool
        +profilePhotoUrl() String
        +genderLabel() String
        +screeningSessions() HasMany
        +healthMonitorings() HasMany
        +medications() HasMany
    }

    class ScreeningIdentity {
        +Int id
        +Int user_id
        +String name
        +String nik
        +String gender
        +Date date_of_birth
        +String address
    }

    class ScreeningSession {
        +Int id
        +Int user_id
        +Int screening_identity_id
        +String disease
        +Array answers
        +String summary
        +String risk_level
        +Boolean is_emergency
        +diseaseLabel() String
        +displayRiskLevel() String
        +displayRiskLabel() String
        +scoreData() Array
        +scoreLabel() String
        +nextStepMessage() String
        +hasSelfManagement() bool
        +showsEmergencyUi() bool
    }

    class HealthMonitoring {
        +Int id
        +Int user_id
        +String monitor_type
        +String disease
        +String period_month
        +Array complaint_answers
        +Int complaint_total
        +String complaint_score_label
        +String medication_name
        +Float medication_compliance_percent
        +String medication_compliance_label
        +Int systolic
        +Int diastolic
        +Int heart_rate
        +Float temperature
        +Float blood_sugar
        +Float oxygen_saturation
        +Float weight
        +String notes
        +isDaily() bool
        +isMonthly() bool
        +bloodPressureLabel() String
        +vitalsSummary() String
    }

    class ConsultationProvider {
        +Int id
        +String name
        +String specialty
        +String bio
        +String photo
        +Int price
        +String category
        +Boolean is_active
        +Boolean is_approved
    }

    class ConsultationOrder {
        +Int id
        +Int user_id
        +String provider_key
        +String reference_code
        +Int amount
        +Int discount_amount
        +Int total_paid
        +String status
        +String payment_method
        +String payment_proof
        +DateTime paid_at
        +DateTime verified_at
        +Int verified_by
        +DateTime expires_at
        +isPending() bool
        +isActive() bool
        +isRejected() bool
        +paymentProofUrl() String
    }

    class ConsultationMessage {
        +Int id
        +Int consultation_order_id
        +Int sender_id
        +String sender_type
        +String message
        +String attachment
    }

    class ConsultationVoucher {
        +Int id
        +String code
        +Int discount_amount
        +String discount_type
        +Boolean is_active
        +Date expires_at
    }

    class HomecarePackage {
        +Int id
        +String name
        +String description
        +Int price
        +Boolean is_active
    }

    class HomecareBooking {
        +Int id
        +Int user_id
        +Int homecare_package_id
        +String reference_code
        +String patient_name
        +String patient_phone
        +DateTime booking_date
        +String address
        +Float latitude
        +Float longitude
        +Float distance_km
        +Int transport_fee
        +String payment_proof
        +String status
        +String admin_note
        +isPending() bool
        +isPaid() bool
        +isCompleted() bool
        +isRejected() bool
        +totalPrice() Int
    }

    class Medicine {
        +Int id
        +String name
        +String category
        +String description
        +Int price
        +Int stock
        +String image
        +Boolean is_active
    }

    class MedicineOrder {
        +Int id
        +Int user_id
        +String reference_code
        +Int total_amount
        +String status
        +String payment_proof
        +DateTime paid_at
        +DateTime verified_at
    }

    class MedicineOrderItem {
        +Int id
        +Int medicine_order_id
        +Int medicine_id
        +Int quantity
        +Int price
    }

    class UserMedication {
        +Int id
        +Int user_id
        +String name
        +String dose
        +String schedule
        +Int prescription_days
        +Boolean is_active
        +Int sort_order
    }

    class SelfManagementLog {
        +Int id
        +Int user_id
        +String disease
        +String activity
        +Boolean is_done
        +Date recorded_at
    }

    class HealthArticle {
        +Int id
        +String title
        +String slug
        +String content
        +String category
        +String cover_image
        +Boolean is_published
    }

    %% Relationships
    User "1" --> "0..*" ScreeningSession : has
    User "1" --> "0..*" HealthMonitoring : has
    User "1" --> "0..*" ConsultationOrder : places
    User "1" --> "0..*" HomecareBooking : books
    User "1" --> "0..*" MedicineOrder : orders
    User "1" --> "0..*" UserMedication : has
    User "1" --> "0..*" SelfManagementLog : logs
    User "1" --> "0..*" ScreeningIdentity : has

    ScreeningSession "0..*" --> "1" ScreeningIdentity : uses
    ConsultationOrder "0..*" --> "1" ConsultationProvider : for
    ConsultationOrder "1" --> "0..*" ConsultationMessage : contains
    ConsultationOrder "0..*" --> "0..1" ConsultationVoucher : uses
    HomecareBooking "0..*" --> "1" HomecarePackage : uses
    MedicineOrder "1" --> "1..*" MedicineOrderItem : contains
    MedicineOrderItem "0..*" --> "1" Medicine : references
```

---

> **Keterangan Sistem Nersia.co**
> 
> | Aktor | Peran |
> |-------|-------|
> | **Pasien / Pengguna** | Melakukan skrining, monitoring, konsultasi, homecare, beli obat, edukasi |
> | **Admin** | Verifikasi pembayaran, kelola data master, pantau laporan |
> | **Nakes / Provider** | Menjawab konsultasi chat pasien |
>
> | Modul Utama | Fungsi |
> |-------------|--------|
> | Skrining | Deteksi risiko penyakit dengan skor berbasis pertanyaan |
> | Monitoring | Rekam tanda vital, kepatuhan obat, & self-management harian/bulanan |
> | Konsultasi | Chat berbayar dengan tenaga kesehatan terverifikasi |
> | Homecare | Pemesanan kunjungan nakes ke rumah dengan kalkulasi ongkir GPS |
> | Obat Online | Pembelian obat dengan keranjang & verifikasi admin |
> | Edukasi | Artikel kesehatan yang dikelola admin |
