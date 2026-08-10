import { Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType, Table, TableRow, TableCell, WidthType, ShadingType, Header } from 'docx';
import fs from 'fs';
import path from 'path';

const BRAND = '1F4E79';
const ACCENT = '0AA4B0';
const GRAY = '666666';
const WHITE = 'FFFFFF';
const TABLE_HEADER_BG = '1F4E79';
const TABLE_ALT_BG = 'F0F7FF';

function heading1(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_1,
        spacing: { before: 480, after: 200 },
        children: [new TextRun({ text, bold: true, size: 30, color: BRAND, font: 'Calibri' })],
    });
}

function heading2(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360, after: 160 },
        children: [new TextRun({ text, bold: true, size: 24, color: BRAND, font: 'Calibri' })],
    });
}

function para(text) {
    return new Paragraph({
        spacing: { after: 120, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: [new TextRun({ text, size: 22, font: 'Calibri', color: '333333' })],
    });
}

function makeTable(headers, rows, widths) {
    const headerRow = new TableRow({
        tableHeader: true,
        children: headers.map((h, i) => new TableCell({
            shading: { type: ShadingType.SOLID, color: TABLE_HEADER_BG },
            width: { size: widths[i], type: WidthType.DXA },
            children: [new Paragraph({
                spacing: { before: 60, after: 60 },
                alignment: AlignmentType.CENTER,
                children: [new TextRun({ text: h, bold: true, size: 18, font: 'Calibri', color: WHITE })],
            })],
        })),
    });

    const dataRows = rows.map((row, idx) => new TableRow({
        children: row.map((cell, i) => new TableCell({
            shading: idx % 2 === 1 ? { type: ShadingType.SOLID, color: TABLE_ALT_BG } : undefined,
            width: { size: widths[i], type: WidthType.DXA },
            children: [new Paragraph({
                spacing: { before: 40, after: 40 },
                children: [new TextRun({ text: String(cell), size: 18, font: 'Calibri', color: '333333' })],
            })],
        })),
    }));

    return new Table({
        width: { size: 9000, type: WidthType.DXA },
        rows: [headerRow, ...dataRows],
    });
}

const doc = new Document({
    creator: 'Nersia Health Security Research Team',
    title: 'Inventarisasi Aset & Scope Pengujian OWASP Top 10',
    sections: [{
        properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
        headers: {
            default: new Header({
                children: [new Paragraph({
                    alignment: AlignmentType.RIGHT,
                    children: [new TextRun({ text: 'OWASP Top 10 Asset Inventory — Chatbot Keperawatan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                })],
            }),
        },
        children: [
            heading1('🛡️ INVENTARISASI ASET & PEMETAAN ATTACK SURFACE'),
            new Paragraph({
                spacing: { after: 200 },
                children: [new TextRun({ text: 'Vulnerability Assessment and Security Enhancement of Chatbot Keperawatan Pintar Using the OWASP Top 10 Framework', size: 20, color: ACCENT, bold: true })],
            }),
            para('Dokumen ini memetakan seluruh komponen, endpoint URL/API, modul autentikasi, manajemen sesi, kontrol akses (otorisasi), dan mekanisme pertukaran data pada aplikasi Chatbot Keperawatan Pintar. Pemetaan ini berfungsi sebagai Asset Inventory & Attack Surface Map yang acuan utama pengujian keamanan OWASP Top 10.'),
            
            heading2('Tabel Inventarisasi Aset & Endpoint Target'),
            makeTable(
                ['No', 'Modul / Komponen', 'Endpoint / URL', 'Method', 'Akses / Auth', 'Vektor Pengujian OWASP Top 10'],
                [
                    ['1.1', 'Halaman Login', '/login', 'GET', 'Publik', 'Cross-Site Scripting (XSS), Clickjacking'],
                    ['1.2', 'Proses Authenticate', '/login', 'POST', 'Publik', 'Brute Force, Auth Bypass (A07:2021), Credential Stuffing'],
                    ['1.3', 'Halaman Registrasi', '/register', 'GET', 'Publik', 'UI Redirection, Information Disclosure'],
                    ['1.4', 'Proses Registrasi', '/register', 'POST', 'Publik', 'Mass Assignment (Privilege Escalation A01:2021), Role Manipulation'],
                    ['1.5', 'Logout', '/logout', 'POST', 'Auth', 'Session Invalidation, CSRF pada Logout'],
                    ['1.6', 'Request Reset Password', '/forgot-password', 'POST', 'Publik', 'Rate Limiting Bypass, Email Enumeration'],
                    ['1.7', 'Process New Password', '/reset-password/{token}', 'POST', 'Publik', 'Weak Token Validation, Insecure Reset Logic'],
                    ['2.1', 'Halaman Profil Pasien', '/profil', 'GET', 'Auth', 'Information Disclosure'],
                    ['2.2', 'Edit Profil Pengguna', '/profile', 'PATCH', 'Auth', 'Mass Assignment, Input Validation Bypass'],
                    ['2.3', 'Update Alamat', '/profile/address', 'POST', 'Auth', 'Stored XSS, Insecure Data Handling'],
                    ['2.4', 'Hapus Akun Self', '/profile', 'DELETE', 'Auth', 'Password Verification Bypass'],
                    ['3.1', 'Form Identitas Skrining', '/deteksi/identitas', 'POST', 'Auth', 'SQL Injection, Input Validation'],
                    ['3.2', 'Skrining Awal Chatbot', '/deteksi/skrining-awal', 'GET', 'Auth', 'Logic Flaw, Session Manipulation'],
                    ['3.3', 'Session Chatbot Penyakit', '/deteksi/{disease}/skrining', 'GET', 'Auth', 'Path Traversal / Parameter Pollution ({disease})'],
                    ['3.4', 'Endpoint API Store Screening', '/api/screening', 'POST', 'Auth', 'API Input Validation, Mass Assignment, Data Tampering'],
                    ['3.5', 'Endpoint API TTS', '/api/screening-tts', 'POST', 'Auth', 'SSRF, Resource Exhaustion, Command Injection'],
                    ['3.6', 'API Cascade Wilayah', '/api/wilayah/children', 'GET', 'Auth', 'SQL Injection pada Query Param (parent_code)'],
                    ['3.7', 'Riwayat Skrining', '/riwayat/{id}', 'GET', 'Auth', 'IDOR (A01:2021) — mengakses riwayat pasien lain'],
                    ['4.1', 'Detail Self Management', '/self-management/{disease}', 'GET', 'Auth', 'LFI/RFI via {disease}, Unauthorized Access'],
                    ['4.2', 'Catat Aktivitas', '/self-management/activities', 'POST', 'Screening', 'CSRF, Broken Access Control'],
                    ['4.3', 'Toggle Status Aktivitas', '/self-management/activities/{log}/toggle', 'PATCH', 'Screening', 'IDOR ({log} parameter tampering)'],
                    ['4.4', 'Submit Data Monitoring', '/monitoring', 'POST', 'Screening', 'Input Validation, Logic Flaws in Score Calculation'],
                    ['5.1', 'Checkout Konsultasi', '/konsultasi/{provider}/checkout', 'GET', 'Auth', 'IDOR pada {provider} key'],
                    ['5.2', 'Redeem Voucher', '/konsultasi/{provider}/voucher', 'POST', 'Auth', 'Race Condition, Voucher Logic Flaw (Double Redeem)'],
                    ['5.3', 'Proses Bayar Direct', '/konsultasi/{provider}/pay', 'POST', 'Auth', 'Payment Bypass / Price Manipulation'],
                    ['5.4', 'Upload Bukti Bayar', '/konsultasi/{provider}/pembayaran', 'POST', 'Auth', 'Unrestricted File Upload, MIME-Type Spoofing'],
                    ['5.5', 'Fetch Pesan Chat (Poll)', '/konsultasi/{provider}/chat/pesan', 'GET', 'Auth', 'Broken Object Level Authorization (BOLA/IDOR)'],
                    ['5.6', 'Kirim Pesan Chat', '/konsultasi/{provider}/chat', 'POST', 'Auth', 'Stored XSS via Chat Message, Rate Limiting'],
                    ['6.1', 'Modifikasi Item Keranjang', '/obat/keranjang/update', 'POST', 'Auth', 'Business Logic Flaw, Negative Price/Quantity'],
                    ['6.2', 'Checkout Obat', '/obat/checkout', 'POST', 'Auth', 'Price Tampering, Address Injection'],
                    ['6.3', 'Upload Bukti Bayar Obat', '/obat/pesanan/{order}/pembayaran/konfirmasi', 'POST', 'Auth', 'Malicious File Upload (Web Shell / Script)'],
                    ['6.4', 'Status Pesanan', '/obat/pesanan/{order}/status', 'GET', 'Auth', 'IDOR ({order} Enumeration)'],
                    ['7.1', 'Booking Homecare', '/homecare/{package}/pesan', 'POST', 'Auth', 'IDOR, Parameter Tampering'],
                    ['7.2', 'Konfirmasi Pembayaran Homecare', '/homecare/booking/{booking}/pembayaran/konfirmasi', 'POST', 'Auth', 'Arbitrary File Upload, Status Override'],
                    ['8.1', 'Admin Dashboard', '/admin/', 'GET', 'Admin', 'Privilege Escalation, BPOA'],
                    ['8.2', 'Data Pasien & User Mgmt', '/admin/users/{user}', 'GET', 'Admin', 'Sensitive Data Exposure (PII Leak)'],
                    ['8.3', 'Kelola Akses & Role', '/admin/access', 'POST', 'Admin', 'Privilege Escalation (Self-Promote to Admin)'],
                    ['8.4', 'Approval Provider / Mitra', '/admin/access/provider/{user}/approve', 'POST', 'Admin', 'Authorization Bypass'],
                    ['8.5', 'Approve/Reject Konsultasi', '/admin/konsultasi/{order}/setujui', 'POST', 'Admin', 'Unauthorized Financial State Change'],
                    ['8.6', 'Kelola Nakes (Provider)', '/admin/konsultasi/tenaga-kesehatan/*', 'POST', 'Admin', 'Malicious File Upload (Foto Nakes), Stored XSS'],
                    ['8.7', 'Kelola Stok & Order Obat', '/admin/obat/*', 'POST', 'Admin', 'File Upload, IDOR, SQLi'],
                    ['8.8', 'Setting WhatsApp & System', '/admin/settings', 'POST', 'Admin', 'Stored XSS, Telemetry/Phone Number Spoofing']
                ],
                [500, 1500, 2200, 800, 1000, 3000]
            ),
            heading2('Pemetaan Terhadap OWASP Top 10 (2021)'),
            makeTable(
                ['Kategori OWASP Top 10 (2021)', 'Target Modul / Endpoint Pengujian', 'Metode & Focus Pengujian'],
                [
                    ['A01:2021 – Broken Access Control', '/riwayat/{id}, /konsultasi/{provider}/chat/pesan, /obat/pesanan/{order}/status, /admin/*', 'Pengujian IDOR/BOLA pada ID pesanan & riwayat; Vertical/Horizontal Privilege Escalation.'],
                    ['A02:2021 – Cryptographic Failures', 'Transmit data sensitif, simpan password DB, reset token', 'Evaluasi enkripsi Bcrypt, proteksi PII rekam medis, dan HTTP/HTTPS headers.'],
                    ['A03:2021 – Injection', '/api/screening, /api/wilayah/children, /deteksi/{disease}, /admin/edukasi', 'Testing SQL Injection, Stored & Reflected XSS, Path Traversal.'],
                    ['A04:2021 – Insecure Design', 'Modul Checkout, Redeem Voucher, Keranjang Obat', 'Testing logic flaw (voucher ganda, nominal harga negatif, manipulasi quantity).'],
                    ['A05:2021 – Security Misconfiguration', 'Debug mode (APP_DEBUG), HTTP Headers, File Storage', 'Pemindaian error trace leaks, CORS headers, directory listing /storage/.'],
                    ['A06:2021 – Vulnerable Components', 'Package Laravel, NPM Dependencies', 'Dependency Audit via composer audit & npm audit untuk mendeteksi CVE.'],
                    ['A07:2021 – Auth Failures', '/login, /register, /forgot-password', 'Testing Brute Force, Weak Password policy, Session Fixation, Role manipulation.'],
                    ['A08:2021 – Data Integrity Failures', 'Upload Bukti Bayar (/konsultasi/..., /obat/...)', 'File Upload vulnerability (uploading PHP webshell, bypass extension/MIME).'],
                    ['A09:2021 – Logging Failures', 'Logging pada storage/logs/laravel.log', 'Evaluasi keterlacakan aktivitas mencurigakan dan penanganan exception.'],
                    ['A10:2021 – SSRF', 'Integrasi WhatsApp Gateway & Embed Video Youtube', 'Testing manipulasi parameter URL outbound request atau API callback.']
                ],
                [2500, 3000, 3500]
            )
        ]
    }]
});

Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(path.resolve('docs', 'Inventarisasi_Aset_OWASP_Top_10_Nersia_Health.docx'), buffer);
    console.log('✅ Document Word OWASP created successfully!');
}).catch(err => {
    console.error('❌ Failed to create docx:', err);
});
