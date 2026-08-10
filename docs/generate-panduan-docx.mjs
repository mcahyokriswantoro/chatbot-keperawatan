/**
 * generate-panduan-docx.mjs
 * Generates a comprehensive Word document guide for Nersia Health application.
 * Run: node docs/generate-panduan-docx.mjs
 */

import { Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType, BorderStyle, Table, TableRow, TableCell, WidthType, ShadingType, PageBreak, Header, Footer, ImageRun } from 'docx';
import fs from 'fs';
import path from 'path';

// ═══════════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════════

const BRAND = '1F4E79';   // Dark blue
const ACCENT = '0AA4B0';  // Teal
const GRAY = '666666';
const LIGHT_BG = 'EEF5FF';
const WHITE = 'FFFFFF';
const TABLE_HEADER_BG = '1F4E79';
const TABLE_ALT_BG = 'F0F7FF';

function heading1(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_1,
        spacing: { before: 480, after: 200 },
        children: [new TextRun({ text, bold: true, size: 32, color: BRAND, font: 'Calibri' })],
    });
}

function heading2(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_2,
        spacing: { before: 360, after: 160 },
        children: [new TextRun({ text, bold: true, size: 26, color: BRAND, font: 'Calibri' })],
    });
}

function heading3(text) {
    return new Paragraph({
        heading: HeadingLevel.HEADING_3,
        spacing: { before: 240, after: 120 },
        children: [new TextRun({ text, bold: true, size: 22, color: ACCENT, font: 'Calibri' })],
    });
}

function para(text, opts = {}) {
    return new Paragraph({
        spacing: { after: 120, line: 360 },
        alignment: opts.center ? AlignmentType.CENTER : AlignmentType.JUSTIFIED,
        children: [new TextRun({ text, size: 22, font: 'Calibri', color: opts.color || '333333', ...opts })],
    });
}

function paraMulti(runs) {
    return new Paragraph({
        spacing: { after: 120, line: 360 },
        alignment: AlignmentType.JUSTIFIED,
        children: runs.map(r => new TextRun({ size: 22, font: 'Calibri', color: '333333', ...r })),
    });
}

function bullet(text, level = 0) {
    return new Paragraph({
        bullet: { level },
        spacing: { after: 80, line: 340 },
        children: [new TextRun({ text, size: 22, font: 'Calibri', color: '333333' })],
    });
}

function numberedItem(text, level = 0) {
    return new Paragraph({
        numbering: { reference: 'main-numbering', level },
        spacing: { after: 80, line: 340 },
        children: [new TextRun({ text, size: 22, font: 'Calibri', color: '333333' })],
    });
}

function note(text) {
    return new Paragraph({
        spacing: { before: 120, after: 120 },
        indent: { left: 400 },
        border: { left: { style: BorderStyle.SINGLE, size: 8, color: ACCENT } },
        children: [
            new TextRun({ text: '📌 Catatan: ', bold: true, size: 20, font: 'Calibri', color: ACCENT }),
            new TextRun({ text, size: 20, font: 'Calibri', color: GRAY, italics: true }),
        ],
    });
}

function screenshotPlaceholder(label) {
    return new Paragraph({
        spacing: { before: 200, after: 200 },
        alignment: AlignmentType.CENTER,
        border: {
            top: { style: BorderStyle.DASHED, size: 2, color: '999999' },
            bottom: { style: BorderStyle.DASHED, size: 2, color: '999999' },
            left: { style: BorderStyle.DASHED, size: 2, color: '999999' },
            right: { style: BorderStyle.DASHED, size: 2, color: '999999' },
        },
        shading: { type: ShadingType.SOLID, color: 'F9FAFB' },
        children: [
            new TextRun({ text: `[ Screenshot: ${label} ]`, size: 20, font: 'Calibri', color: '999999', italics: true }),
        ],
    });
}

function makeTable(headers, rows) {
    const headerRow = new TableRow({
        tableHeader: true,
        children: headers.map(h => new TableCell({
            shading: { type: ShadingType.SOLID, color: TABLE_HEADER_BG },
            width: { size: Math.floor(9000 / headers.length), type: WidthType.DXA },
            children: [new Paragraph({
                spacing: { before: 60, after: 60 },
                alignment: AlignmentType.CENTER,
                children: [new TextRun({ text: h, bold: true, size: 20, font: 'Calibri', color: WHITE })],
            })],
        })),
    });

    const dataRows = rows.map((row, idx) => new TableRow({
        children: row.map(cell => new TableCell({
            shading: idx % 2 === 1 ? { type: ShadingType.SOLID, color: TABLE_ALT_BG } : undefined,
            children: [new Paragraph({
                spacing: { before: 40, after: 40 },
                children: [new TextRun({ text: String(cell), size: 20, font: 'Calibri', color: '333333' })],
            })],
        })),
    }));

    return new Table({
        width: { size: 9000, type: WidthType.DXA },
        rows: [headerRow, ...dataRows],
    });
}

function pageBreak() {
    return new Paragraph({ children: [new PageBreak()] });
}

function emptyLine() {
    return new Paragraph({ spacing: { after: 80 }, children: [] });
}

// ═══════════════════════════════════════════════════════════════════
// BUILD DOCUMENT
// ═══════════════════════════════════════════════════════════════════

const doc = new Document({
    creator: 'Nersia Health Team',
    title: 'Panduan Penggunaan Aplikasi Nersia Health',
    description: 'Panduan lengkap penggunaan aplikasi Nersia Health - Chatbot Smart Health Screening & Care',
    numbering: {
        config: [{
            reference: 'main-numbering',
            levels: [
                { level: 0, format: 'decimal', text: '%1.', alignment: AlignmentType.LEFT },
                { level: 1, format: 'lowerLetter', text: '%2)', alignment: AlignmentType.LEFT },
            ],
        }],
    },
    styles: {
        default: {
            document: { run: { font: 'Calibri', size: 22 } },
        },
    },
    sections: [
        // ═══════════════════════════════════════════════════════════
        // COVER PAGE
        // ═══════════════════════════════════════════════════════════
        {
            properties: {
                page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } },
            },
            children: [
                emptyLine(), emptyLine(), emptyLine(), emptyLine(), emptyLine(),
                emptyLine(), emptyLine(), emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 200 },
                    children: [new TextRun({ text: '📘', size: 80, font: 'Calibri' })],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 120 },
                    children: [new TextRun({ text: 'PANDUAN PENGGUNAAN APLIKASI', bold: true, size: 36, color: BRAND, font: 'Calibri' })],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 80 },
                    children: [
                        new TextRun({ text: 'NERSIA ', bold: true, size: 48, color: BRAND, font: 'Calibri' }),
                        new TextRun({ text: 'HEALTH', bold: true, size: 48, color: ACCENT, font: 'Calibri' }),
                    ],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 300 },
                    children: [new TextRun({ text: 'Chatbot Smart Health Screening & Care', size: 24, color: GRAY, font: 'Calibri', italics: true })],
                }),
                emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 60 },
                    border: { top: { style: BorderStyle.SINGLE, size: 2, color: ACCENT } },
                    children: [],
                }),
                emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 80 },
                    children: [new TextRun({ text: 'Versi 1.0', size: 22, color: GRAY, font: 'Calibri' })],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 80 },
                    children: [new TextRun({ text: new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }), size: 22, color: GRAY, font: 'Calibri' })],
                }),
                emptyLine(), emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    children: [new TextRun({ text: 'Disusun oleh Tim Pengembang Nersia Health', size: 20, color: GRAY, font: 'Calibri' })],
                }),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // DAFTAR ISI
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'Panduan Aplikasi Nersia Health', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            footers: {
                default: new Footer({
                    children: [new Paragraph({
                        alignment: AlignmentType.CENTER,
                        children: [new TextRun({ text: 'Nersia Health — Chatbot Smart Health Screening & Care', size: 16, color: GRAY, font: 'Calibri' })],
                    })],
                }),
            },
            children: [
                heading1('DAFTAR ISI'),
                emptyLine(),
                ...[
                    ['BAB I', 'Pendahuluan'],
                    ['BAB II', 'Registrasi dan Login Akun'],
                    ['BAB III', 'Halaman Beranda'],
                    ['BAB IV', 'Deteksi Kesehatan (Skrining)'],
                    ['BAB V', 'Riwayat Skrining'],
                    ['BAB VI', 'Self Management'],
                    ['BAB VII', 'Monitoring Kesehatan'],
                    ['BAB VIII', 'Konsultasi Kesehatan'],
                    ['BAB IX', 'Layanan Apotek (Pemesanan Obat)'],
                    ['BAB X', 'Layanan Homecare'],
                    ['BAB XI', 'Edukasi Kesehatan'],
                    ['BAB XII', 'Profil Pengguna'],
                    ['BAB XIII', 'Nomor Darurat'],
                    ['BAB XIV', 'Panel Admin'],
                    ['BAB XV', 'Pengaturan Mitra dan WhatsApp'],
                ].map(([bab, title]) => paraMulti([
                    { text: `${bab}   `, bold: true, color: BRAND },
                    { text: title, color: '333333' },
                ])),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB I — PENDAHULUAN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB I — Pendahuluan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB I — PENDAHULUAN'),

                heading2('1.1 Tentang Aplikasi'),
                para('Nersia Health merupakan aplikasi web berbasis teknologi Laravel yang dirancang untuk membantu masyarakat melakukan skrining kesehatan secara mandiri melalui chatbot interaktif. Aplikasi ini menyediakan layanan komprehensif yang meliputi deteksi dini penyakit kronis, konsultasi langsung dengan tenaga kesehatan profesional, pemesanan obat dari apotek mitra, serta layanan homecare berupa panggilan perawat ke rumah.'),
                para('Aplikasi ini dikembangkan dengan tujuan untuk meningkatkan aksesibilitas layanan kesehatan bagi masyarakat, khususnya dalam upaya deteksi dini dan pengelolaan penyakit kronis secara mandiri (self management) di lingkungan rumah.'),

                screenshotPlaceholder('Halaman Beranda Nersia Health'),

                heading2('1.2 Teknologi yang Digunakan'),
                makeTable(
                    ['Komponen', 'Teknologi'],
                    [
                        ['Framework Backend', 'Laravel (PHP 8.x)'],
                        ['Frontend', 'Blade Templates, Alpine.js, Tailwind CSS'],
                        ['Database', 'MySQL / MariaDB'],
                        ['Notifikasi', 'WhatsApp API (Fonnte / Wablas)'],
                        ['Pembayaran', 'Transfer Giro BRI, DANA'],
                        ['Text-to-Speech', 'Web Speech API (Client-side)'],
                    ],
                ),
                emptyLine(),

                heading2('1.3 Jenis Pengguna (Role)'),
                para('Aplikasi Nersia Health mendukung enam jenis pengguna dengan hak akses yang berbeda-beda sebagaimana dijabarkan dalam tabel berikut:'),
                makeTable(
                    ['No', 'Role', 'Keterangan', 'Verifikasi Admin'],
                    [
                        ['1', '🧑‍🦰 Pasien', 'Pengguna umum untuk skrining dan konsultasi kesehatan', 'Tidak diperlukan'],
                        ['2', '👩‍⚕️ Perawat', 'Tenaga kesehatan perawat (Ners)', 'Diperlukan'],
                        ['3', '🩺 Dokter', 'Dokter umum atau dokter spesialis', 'Diperlukan'],
                        ['4', '💊 Apotek', 'Mitra apotek (UMLA FARMA)', 'Diperlukan'],
                        ['5', '🏠 Homecare', 'Mitra homecare (Medical Center)', 'Diperlukan'],
                        ['6', '🛡️ Admin', 'Super Admin pengelola sistem', 'Diberikan oleh admin lain'],
                    ],
                ),
                note('Untuk role selain Pasien, pendaftaran memerlukan persetujuan dari Admin sebelum pengguna dapat mengakses aplikasi.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB II — REGISTRASI DAN LOGIN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB II — Registrasi dan Login', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB II — REGISTRASI DAN LOGIN AKUN'),

                heading2('2.1 Registrasi Akun Baru'),
                para('Untuk menggunakan aplikasi Nersia Health, pengguna terlebih dahulu harus membuat akun melalui halaman registrasi. Berikut adalah langkah-langkah pendaftaran akun:'),

                heading3('Langkah 1: Akses Halaman Registrasi'),
                para('Buka halaman registrasi melalui alamat /register atau klik tombol "Daftar" pada halaman login. Pada halaman ini, pengguna akan melihat enam pilihan jenis akun yang tersedia.'),
                screenshotPlaceholder('Halaman Registrasi — Pilihan Role'),

                heading3('Langkah 2: Pilih Jenis Akun'),
                para('Pilih salah satu jenis akun sesuai dengan kebutuhan:'),
                bullet('Pasien — Untuk masyarakat umum yang ingin melakukan skrining dan konsultasi kesehatan.'),
                bullet('Perawat — Untuk tenaga kesehatan perawat yang ingin menjadi penyedia layanan konsultasi.'),
                bullet('Dokter — Untuk dokter umum maupun dokter spesialis.'),
                bullet('Apotek — Untuk mitra apotek yang menyediakan layanan penjualan obat.'),
                bullet('Homecare — Untuk mitra layanan perawatan di rumah.'),

                heading3('Langkah 3: Isi Formulir Pendaftaran'),
                para('Lengkapi formulir pendaftaran dengan data berikut:'),
                makeTable(
                    ['Field', 'Keterangan', 'Wajib'],
                    [
                        ['Nama Lengkap', 'Nama sesuai identitas', 'Ya'],
                        ['Email', 'Alamat email aktif (unik)', 'Ya'],
                        ['No. HP / WhatsApp', 'Nomor telepon yang aktif', 'Ya'],
                        ['Password', 'Minimal 8 karakter', 'Ya'],
                        ['Jenis Kelamin', 'Laki-laki / Perempuan (khusus Pasien)', 'Ya (Pasien)'],
                        ['Tanggal Lahir', 'Format: DD/MM/YYYY (khusus Pasien)', 'Ya (Pasien)'],
                        ['Alamat Lengkap', 'Alamat domisili saat ini', 'Opsional'],
                        ['Bidang / Jabatan', 'Spesialisasi (khusus Perawat & Dokter)', 'Ya (Nakes)'],
                    ],
                ),
                emptyLine(),

                heading3('Langkah 4: Kirim Pendaftaran'),
                para('Setelah semua data terisi lengkap, klik tombol "Daftar" untuk mengirimkan formulir pendaftaran.'),
                note('Untuk akun Pasien, pengguna dapat langsung login setelah mendaftar. Untuk role lainnya (Perawat, Dokter, Apotek, Homecare), akun akan berstatus "Menunggu Verifikasi Admin" dan pengguna belum dapat login hingga admin menyetujui pendaftarannya.'),

                screenshotPlaceholder('Formulir Registrasi — Data Diri Pasien'),

                pageBreak(),

                heading2('2.2 Login ke Aplikasi'),
                para('Pengguna yang telah memiliki akun dapat masuk ke aplikasi melalui halaman login.'),

                heading3('Langkah Login'),
                bullet('Buka halaman login melalui alamat /login.'),
                bullet('Masukkan Email atau Nomor HP yang terdaftar.'),
                bullet('Masukkan Password.'),
                bullet('Klik tombol "Masuk" untuk mengakses aplikasi.'),

                screenshotPlaceholder('Halaman Login'),

                heading2('2.3 Lupa Password'),
                para('Jika pengguna lupa password, dapat menggunakan fitur reset password dengan langkah berikut:'),
                bullet('Klik tautan "Lupa Password?" pada halaman login.'),
                bullet('Masukkan alamat email yang terdaftar.'),
                bullet('Sistem akan mengirimkan tautan reset password melalui email.'),
                bullet('Klik tautan tersebut dan buat password baru.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB III — HALAMAN BERANDA
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB III — Halaman Beranda', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB III — HALAMAN BERANDA'),

                para('Halaman beranda merupakan halaman utama aplikasi yang pertama kali dilihat oleh pengguna setelah mengakses Nersia Health. Halaman ini dirancang untuk memberikan akses cepat ke seluruh fitur utama aplikasi.'),

                screenshotPlaceholder('Halaman Beranda Nersia Health — Tampilan Lengkap'),

                heading2('3.1 Hero Banner'),
                para('Bagian atas halaman beranda menampilkan hero banner dengan sapaan "Hi, Saya Nersia Health" disertai tagline "Chatbot Smart Health Screening & Care". Terdapat pula karakter robot animasi yang menjadi maskot aplikasi. Di samping kanan, ditampilkan tips kesehatan harian yang berputar secara otomatis setiap 5 detik.'),

                heading2('3.2 Menu Fitur Unggulan'),
                para('Di bawah hero banner, terdapat enam kartu fitur unggulan yang memberikan akses cepat ke seluruh layanan aplikasi:'),
                makeTable(
                    ['No', 'Fitur', 'Deskripsi', 'Alamat URL'],
                    [
                        ['1', '🔍 Deteksi Kesehatan', 'Cek kondisi kesehatan melalui skrining chatbot', '/deteksi'],
                        ['2', '📋 Riwayat Kesehatan', 'Lihat riwayat hasil deteksi/skrining', '/riwayat'],
                        ['3', '💊 Layanan Apotek', 'Beli obat dan vitamin secara online', '/obat'],
                        ['4', '🏠 Layanan Homecare', 'Pesan layanan perawat ke rumah', '/homecare'],
                        ['5', '🎓 Edukasi Kesehatan', 'Akses video dan artikel edukasi kesehatan', '/edukasi'],
                        ['6', '💬 Konsultasi Langsung', 'Konsultasi chat dengan tenaga kesehatan', '/konsultasi'],
                    ],
                ),
                emptyLine(),

                heading2('3.3 Navigasi Bawah (Bottom Navigation)'),
                para('Pada setiap halaman pasien, terdapat navigasi bawah (bottom navigation bar) yang memudahkan pengguna berpindah antar fitur utama. Navigasi ini memiliki enam menu: Beranda, Deteksi, Self Management, Konsultasi, Edukasi, dan Profil.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB IV — DETEKSI KESEHATAN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB IV — Deteksi Kesehatan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB IV — DETEKSI KESEHATAN (SKRINING)'),

                para('Fitur Deteksi Kesehatan merupakan fitur utama dari aplikasi Nersia Health yang memungkinkan pengguna melakukan skrining kesehatan secara mandiri melalui chatbot interaktif. Sistem ini dirancang untuk mendeteksi dini risiko berbagai penyakit kronis melalui serangkaian pertanyaan terstruktur.'),

                heading2('4.1 Alur Skrining'),
                para('Proses skrining kesehatan dilakukan melalui empat tahapan utama yang saling berkesinambungan:'),
                bullet('Tahap 1: Pengisian data identitas pasien'),
                bullet('Tahap 2: Skrining awal (20 pertanyaan umum)'),
                bullet('Tahap 3: Skrining lanjut berdasarkan penyakit yang terindikasi'),
                bullet('Tahap 4: Hasil skrining beserta rekomendasi tindak lanjut'),

                heading2('4.2 Tahap 1 — Pengisian Data Identitas'),
                para('Sebelum memulai skrining, pengguna diminta untuk mengisi formulir data identitas yang meliputi: nama lengkap, usia, jenis kelamin, alamat lengkap (dengan dropdown bertingkat Provinsi → Kabupaten/Kota → Kecamatan → Kelurahan/Desa), pekerjaan, dan nomor telepon yang dapat dihubungi.'),
                screenshotPlaceholder('Form Identitas Pasien — Data Diri'),

                heading2('4.3 Tahap 2 — Skrining Awal'),
                para('Pada tahap skrining awal, pengguna menjawab 20 pertanyaan mengenai gejala dan faktor risiko secara umum. Pertanyaan disampaikan satu per satu oleh chatbot dalam format percakapan interaktif. Pengguna cukup memilih jawaban "Ya" atau "Tidak" untuk setiap pertanyaan.'),
                para('Berdasarkan jawaban skrining awal, sistem akan menganalisis dan merekomendasikan skrining lanjut yang sesuai dengan kondisi pengguna.'),
                screenshotPlaceholder('Chatbot Skrining Awal — Percakapan Interaktif'),

                heading2('4.4 Tahap 3 — Skrining Lanjut'),
                para('Setelah skrining awal selesai, sistem akan merekomendasikan satu atau lebih skrining lanjut berdasarkan penyakit yang terindikasi. Berikut adalah daftar penyakit yang dapat dideteksi oleh aplikasi:'),
                makeTable(
                    ['No', 'Penyakit', 'Ikon', 'Jumlah Pertanyaan', 'Deskripsi'],
                    [
                        ['1', 'TB Paru', '🫁', '23', 'Tuberkulosis paru — gejala dan faktor risiko'],
                        ['2', 'DHF (Demam Berdarah)', '🦟', '24', 'Dengue hemorrhagic fever — gejala 7 hari terakhir'],
                        ['3', 'PPOK', '💨', '19', 'Penyakit paru obstruktif kronis'],
                        ['4', 'Penyakit Ginjal', '🫘', '26', 'Gangguan ginjal — gejala dan riwayat'],
                        ['5', 'Stroke', '🧠', '23', 'Tanda stroke dan faktor risikonya'],
                        ['6', 'Jantung Koroner', '❤️', '25', 'Gejala dan faktor risiko jantung koroner'],
                        ['7', 'Diabetes Melitus', '🩸', '23', 'Gejala dan faktor risiko diabetes'],
                        ['8', 'Hipertensi', '📈', '20', 'Tekanan darah tinggi — gejala dan risiko'],
                        ['9', 'Rheumatoid Arthritis', '🦴', '16', 'Arthritis reumatoid — gejala sendi'],
                    ],
                ),
                emptyLine(),
                screenshotPlaceholder('Daftar Pilihan Skrining Lanjut'),

                heading2('4.5 Tahap 4 — Hasil Skrining'),
                para('Setelah pengguna menyelesaikan seluruh pertanyaan skrining lanjut, sistem akan menampilkan hasil skrining yang meliputi:'),
                bullet('Skor total berdasarkan jawaban yang diberikan'),
                bullet('Tingkat risiko yang dikategorikan menjadi: Risiko Rendah, Risiko Sedang, atau Risiko Tinggi'),
                bullet('Rangkuman seluruh jawaban yang telah diberikan'),
                bullet('Rekomendasi tindak lanjut yang sesuai dengan tingkat risiko'),
                bullet('Fitur Text-to-Speech (TTS) untuk membacakan hasil skrining secara otomatis'),
                screenshotPlaceholder('Hasil Skrining — Skor dan Tingkat Risiko'),
                note('Seluruh hasil skrining tersimpan secara otomatis di dalam sistem dan dapat diakses kembali melalui menu Riwayat Kesehatan.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB V — RIWAYAT SKRINING
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB V — Riwayat Skrining', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB V — RIWAYAT SKRINING'),
                para('Fitur Riwayat Skrining memungkinkan pengguna untuk mengakses kembali seluruh hasil skrining yang pernah dilakukan. Fitur ini penting untuk memantau perkembangan kondisi kesehatan dari waktu ke waktu.'),

                heading2('5.1 Daftar Riwayat'),
                para('Halaman riwayat skrining (alamat URL: /riwayat) menampilkan daftar seluruh sesi skrining yang pernah dilakukan oleh pengguna. Setiap entri riwayat memuat informasi mengenai tanggal dan waktu pelaksanaan skrining, jenis penyakit yang diskrining, serta tingkat risiko yang terdeteksi.'),
                screenshotPlaceholder('Daftar Riwayat Skrining'),

                heading2('5.2 Detail Riwayat'),
                para('Pengguna dapat mengklik salah satu entri riwayat untuk melihat detail lengkap sesi skrining, termasuk seluruh jawaban yang diberikan, skor per kategori, dan rekomendasi tindak lanjut yang diberikan oleh sistem.'),
                screenshotPlaceholder('Detail Riwayat Skrining — Rekap Jawaban'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB VI — SELF MANAGEMENT
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB VI — Self Management', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB VI — SELF MANAGEMENT'),
                para('Fitur Self Management menyediakan panduan perawatan mandiri bagi pengguna yang telah menyelesaikan proses skrining. Fitur ini memberikan rekomendasi aktivitas harian yang disesuaikan dengan jenis penyakit yang terdeteksi, sehingga pengguna dapat melakukan pengelolaan kesehatan secara mandiri di rumah.'),

                heading2('6.1 Daftar Penyakit Self Management'),
                para('Pada halaman utama Self Management (alamat URL: /self-management), pengguna dapat melihat daftar penyakit yang tersedia untuk panduan perawatan mandiri. Setiap penyakit dilengkapi dengan ikon, deskripsi singkat, dan jumlah aktivitas yang direkomendasikan.'),
                screenshotPlaceholder('Halaman Daftar Self Management'),

                heading2('6.2 Panduan Aktivitas Harian'),
                para('Setiap penyakit memiliki halaman panduan khusus (alamat URL: /self-management/{penyakit}) yang berisi:'),
                bullet('Penjelasan tentang penyakit dan pentingnya perawatan mandiri'),
                bullet('Daftar aktivitas harian yang direkomendasikan (meliputi pola makan/diet, aktivitas fisik/olahraga, kepatuhan minum obat, pengelolaan stres, dan lain-lain)'),
                bullet('Checklist interaktif untuk menandai aktivitas yang sudah dilakukan'),
                bullet('Log riwayat aktivitas harian beserta progres visual'),

                heading2('6.3 Pencatatan Aktivitas'),
                para('Pengguna dapat menandai aktivitas yang telah dilakukan dengan mengklik tombol centang pada setiap item aktivitas. Sistem akan mencatat tanggal dan waktu pengerjaan secara otomatis, sehingga pengguna dapat memantau konsistensi perawatan mandiri dari hari ke hari.'),
                screenshotPlaceholder('Checklist Aktivitas Self Management'),
                note('Fitur Self Management hanya dapat diakses setelah pengguna menyelesaikan minimal satu sesi skrining kesehatan.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB VII — MONITORING KESEHATAN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB VII — Monitoring Kesehatan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB VII — MONITORING KESEHATAN'),
                para('Fitur Monitoring Kesehatan memungkinkan pengguna untuk melakukan pemantauan berkala terhadap kondisi kesehatannya. Fitur ini mengevaluasi tingkat keluhan, kepatuhan self management, dan frekuensi kekambuhan penyakit.'),

                heading2('7.1 Pengisian Monitoring'),
                para('Pada halaman monitoring (alamat URL: /monitoring), pengguna mengisi formulir monitoring yang terdiri dari tiga aspek penilaian:'),

                heading3('A. Keluhan (Tingkat Keparahan Gejala)'),
                para('Pengguna menilai tingkat keparahan gejala yang dirasakan dengan pilihan:'),
                makeTable(
                    ['Pilihan', 'Skor', 'Keterangan'],
                    [
                        ['Tidak Ada', '0', 'Tidak merasakan gejala apapun'],
                        ['Ringan', '1', 'Gejala ringan, tidak mengganggu aktivitas'],
                        ['Sedang', '2', 'Gejala cukup terasa, sedikit mengganggu'],
                        ['Berat', '3', 'Gejala berat, sangat mengganggu aktivitas'],
                    ],
                ),
                emptyLine(),

                heading3('B. Self Management (Kepatuhan Perawatan Mandiri)'),
                para('Pengguna mengevaluasi apakah telah melakukan perawatan mandiri sesuai rekomendasi:'),
                bullet('Tidak (skor 0) — Belum melakukan self management'),
                bullet('Ya (skor 2) — Sudah melakukan self management'),

                heading3('C. Kekambuhan'),
                para('Pengguna melaporkan frekuensi kekambuhan penyakit:'),
                bullet('Tidak pernah kambuh (skor 0)'),
                bullet('Sekali (skor 1)'),
                bullet('Lebih dari 2 kali (skor 2)'),
                bullet('Lebih dari 3 kali (skor 3)'),

                screenshotPlaceholder('Form Monitoring Kesehatan'),

                heading2('7.2 Hasil Monitoring'),
                para('Sistem menganalisis jawaban dan memberikan label penilaian berdasarkan skor yang diperoleh:'),
                makeTable(
                    ['Label', 'Kriteria', 'Keterangan'],
                    [
                        ['✅ Baik', 'Keluhan ≤25%, Self Management ≥80%', 'Kondisi terkontrol dengan baik'],
                        ['⚠️ Cukup', 'Keluhan 25-50%, Self Management 60-80%', 'Perlu peningkatan perawatan'],
                        ['❌ Kurang', 'Keluhan >50%, Self Management <60%', 'Perlu konsultasi lebih lanjut'],
                    ],
                ),
                note('Fitur Monitoring juga memerlukan minimal satu sesi skrining yang telah diselesaikan.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB VIII — KONSULTASI KESEHATAN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB VIII — Konsultasi Kesehatan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB VIII — KONSULTASI KESEHATAN'),
                para('Fitur Konsultasi Kesehatan memungkinkan pengguna untuk berkonsultasi secara langsung dengan tenaga kesehatan profesional melalui chat dalam aplikasi. Layanan ini tersedia dalam tiga kategori tenaga kesehatan yang dapat dipilih sesuai kebutuhan.'),

                heading2('8.1 Kategori Konsultasi'),
                makeTable(
                    ['Kategori', 'Deskripsi Layanan', 'Harga Default'],
                    [
                        ['👩‍⚕️ Perawat (Ners)', 'Edukasi perawatan, pemantauan gejala, dan bantuan self management di rumah', 'Rp 100.000'],
                        ['👨‍⚕️ Dokter Umum', 'Konsultasi keluhan umum, interpretasi hasil skrining, dan rujukan lanjut', 'Rp 100.000'],
                        ['🫀 Dokter Spesialis Penyakit Dalam', 'Konsultasi gangguan metabolik, diabetes, hipertensi, dan penyakit kronis lainnya', 'Rp 150.000'],
                    ],
                ),
                emptyLine(),
                note('Administrator dapat mengatur setiap kategori menjadi GRATIS atau BERBAYAR secara terpisah melalui panel admin. Ketika diatur sebagai gratis, pengguna dapat langsung mengakses fitur chat tanpa proses pembayaran.'),

                screenshotPlaceholder('Halaman Pilihan Kategori Konsultasi'),

                heading2('8.2 Alur Konsultasi'),
                para('Berikut adalah langkah-langkah untuk melakukan konsultasi kesehatan:'),

                heading3('Langkah 1: Pilih Kategori'),
                para('Pada halaman konsultasi (alamat URL: /konsultasi), pengguna memilih salah satu dari tiga kategori tenaga kesehatan yang tersedia.'),

                heading3('Langkah 2: Pilih Tenaga Kesehatan'),
                para('Setelah memilih kategori, sistem menampilkan daftar tenaga kesehatan yang tersedia lengkap dengan profil, spesialisasi, pengalaman, rating, dan tarif konsultasi. Pengguna kemudian memilih tenaga kesehatan yang diinginkan dan mengklik tombol "Mulai Konsultasi".'),
                screenshotPlaceholder('Daftar Tenaga Kesehatan — Profil Provider'),

                heading3('Langkah 3: Pembayaran (Mode Berbayar)'),
                para('Jika kategori tersebut dalam mode berbayar, pengguna akan diarahkan ke halaman checkout untuk menyelesaikan pembayaran. Terdapat tiga metode pembayaran yang tersedia:'),
                bullet('Transfer Giro BRI — Pengguna melakukan transfer manual ke rekening yang tertera, kemudian mengunggah bukti transfer melalui aplikasi.'),
                bullet('DANA — Pembayaran melalui aplikasi dompet digital DANA.'),
                bullet('Kode Voucher — Pengguna memasukkan kode voucher diskon atau gratis yang diperoleh dari promosi.'),
                para('Setelah bukti pembayaran dikirimkan, pesanan akan berstatus "Menunggu Verifikasi Admin". Administrator akan memverifikasi dan menyetujui pembayaran tersebut.'),
                screenshotPlaceholder('Halaman Checkout — Metode Pembayaran'),

                heading3('Langkah 4: Chat Konsultasi'),
                para('Setelah pembayaran diverifikasi (atau jika mode gratis), pengguna dapat langsung memulai percakapan chat dengan tenaga kesehatan. Setiap pesan yang dikirimkan oleh pengguna akan disertai notifikasi WhatsApp ke tenaga kesehatan yang bersangkutan, sehingga respons dapat diberikan dengan cepat.'),
                para('Sesi konsultasi berlaku selama 24 jam sejak pembayaran disetujui.'),
                screenshotPlaceholder('Halaman Chat Konsultasi'),

                heading2('8.3 Pembatalan Konsultasi'),
                para('Pengguna dapat membatalkan pesanan konsultasi yang belum disetujui oleh admin dengan mengklik tombol "Batalkan" pada halaman status pembayaran.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB IX — LAYANAN APOTEK
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB IX — Layanan Apotek', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB IX — LAYANAN APOTEK (PEMESANAN OBAT)'),
                para('Fitur Layanan Apotek memungkinkan pengguna untuk memesan obat dan vitamin secara online dari apotek mitra Nersia Health. Saat ini, terdapat dua mitra apotek yang terintegrasi dalam sistem, yaitu UMLA FARMA 1 (Kampus 1) dan UMLA FARMA 2 (Kembangbahu).'),

                heading2('9.1 Katalog Obat'),
                para('Halaman katalog obat (alamat URL: /obat) menampilkan daftar seluruh obat dan vitamin yang tersedia. Pengguna dapat menyaring produk berdasarkan mitra apotek menggunakan filter pills yang tersedia di bagian atas halaman:'),
                bullet('📍 Semua Apotek — Menampilkan seluruh produk dari semua apotek mitra'),
                bullet('📍 UMLA FARMA 1 (Kampus 1) — Menampilkan produk khusus apotek mitra pertama'),
                bullet('📍 UMLA FARMA 2 (Kembangbahu) — Menampilkan produk khusus apotek mitra kedua'),
                para('Setiap kartu produk menampilkan nama obat, harga, stok tersedia, dan label lokasi apotek mitra.'),
                screenshotPlaceholder('Katalog Obat — Filter Per Apotek Mitra'),

                heading2('9.2 Keranjang Belanja'),
                para('Pengguna dapat menambahkan obat ke keranjang belanja dengan mengklik tombol "Tambah ke Keranjang" pada kartu produk. Pada halaman keranjang (alamat URL: /obat/keranjang), pengguna dapat:'),
                bullet('Melihat daftar obat yang telah dipilih beserta jumlah dan subtotal'),
                bullet('Mengubah jumlah pesanan untuk setiap item'),
                bullet('Menghapus item yang tidak diinginkan'),
                bullet('Melihat total keseluruhan harga pesanan'),
                screenshotPlaceholder('Halaman Keranjang Belanja'),

                heading2('9.3 Checkout dan Pembayaran'),
                para('Setelah memastikan pesanan sudah benar, pengguna mengklik tombol "Checkout" untuk melanjutkan proses pembayaran. Pada halaman pembayaran, pengguna mengunggah bukti transfer pembayaran (Transfer Giro BRI) dan mengirimkan untuk diverifikasi oleh admin.'),
                screenshotPlaceholder('Halaman Pembayaran Obat — Upload Bukti Transfer'),

                heading2('9.4 Status Pesanan'),
                para('Pengguna dapat memantau status pesanan obat pada halaman status (alamat URL: /obat/pesanan/{order}/status). Terdapat lima status pesanan:'),
                makeTable(
                    ['Status', 'Ikon', 'Keterangan'],
                    [
                        ['Menunggu Pembayaran', '⏳', 'Pesanan dibuat, belum melakukan pembayaran'],
                        ['Menunggu Verifikasi', '🔄', 'Bukti transfer telah diunggah, menunggu verifikasi admin'],
                        ['Disetujui', '✅', 'Pembayaran diverifikasi, obat sedang disiapkan'],
                        ['Dikirim', '🚚', 'Obat dalam proses pengiriman'],
                        ['Ditolak', '❌', 'Pembayaran atau pesanan ditolak oleh admin'],
                    ],
                ),
                emptyLine(),
                screenshotPlaceholder('Halaman Status Pesanan Obat'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB X — LAYANAN HOMECARE
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB X — Layanan Homecare', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB X — LAYANAN HOMECARE'),
                para('Fitur Layanan Homecare menyediakan layanan perawatan kesehatan di rumah (home nursing care) yang dikelola oleh Medical Center UMLA. Melalui fitur ini, pengguna dapat memesan kunjungan perawat profesional ke rumah untuk berbagai kebutuhan perawatan kesehatan.'),

                heading2('10.1 Daftar Paket Homecare'),
                para('Pada halaman homecare (alamat URL: /homecare), pengguna dapat melihat berbagai paket layanan yang tersedia. Setiap paket menampilkan nama layanan, deskripsi, durasi, dan harga. Administrator dapat menambahkan dan mengelola paket layanan melalui panel admin.'),
                screenshotPlaceholder('Daftar Paket Layanan Homecare'),

                heading2('10.2 Pemesanan Homecare'),
                para('Untuk memesan layanan homecare, pengguna mengikuti langkah-langkah berikut:'),
                bullet('Pilih paket layanan yang diinginkan dan klik tombol "Pesan Sekarang"'),
                bullet('Isi formulir pemesanan yang meliputi: tanggal dan waktu kunjungan, alamat lengkap tujuan, serta catatan tambahan (opsional)'),
                bullet('Konfirmasi pemesanan dan unggah bukti pembayaran (Transfer Giro BRI)'),
                bullet('Menunggu verifikasi dan persetujuan dari admin'),
                screenshotPlaceholder('Formulir Pemesanan Homecare'),

                heading2('10.3 Status Booking'),
                para('Setelah pemesanan dikirimkan, pengguna dapat memantau status booking. Saat admin menyetujui, sistem akan mengirimkan notifikasi WhatsApp ke mitra homecare untuk menjadwalkan kunjungan. Pengguna juga dapat membatalkan booking yang belum disetujui.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB XI — EDUKASI KESEHATAN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB XI — Edukasi Kesehatan', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB XI — EDUKASI KESEHATAN'),
                para('Fitur Edukasi Kesehatan menyediakan konten edukasi berupa artikel dan video yang berkaitan dengan kesehatan. Konten ini disusun dan dikelola oleh administrator serta tenaga kesehatan untuk memberikan informasi yang akurat dan terpercaya kepada pengguna.'),

                heading2('11.1 Daftar Konten Edukasi'),
                para('Pada halaman edukasi (alamat URL: /edukasi), pengguna dapat melihat daftar seluruh konten edukasi yang tersedia. Setiap konten menampilkan judul, ringkasan, dan kategori. Halaman ini dapat diakses tanpa perlu login.'),
                screenshotPlaceholder('Halaman Daftar Edukasi Kesehatan'),

                heading2('11.2 Detail Konten'),
                para('Pengguna dapat mengklik salah satu konten untuk melihat detail lengkapnya. Halaman detail menampilkan isi artikel secara lengkap dan/atau embed video YouTube jika tersedia.'),
                screenshotPlaceholder('Detail Artikel Edukasi Kesehatan'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB XII — PROFIL PENGGUNA
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB XII — Profil Pengguna', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB XII — PROFIL PENGGUNA'),
                para('Fitur Profil Pengguna memungkinkan setiap pengguna untuk melihat dan mengelola data pribadi mereka di dalam aplikasi Nersia Health.'),

                heading2('12.1 Halaman Profil'),
                para('Halaman profil (alamat URL: /profil) menampilkan ringkasan informasi pengguna yang meliputi: nama lengkap, email, nomor HP, jenis kelamin, usia, dan alamat. Di halaman ini juga ditampilkan ringkasan riwayat skrining terakhir serta menu navigasi cepat ke fitur-fitur utama.'),
                screenshotPlaceholder('Halaman Profil Pengguna'),

                heading2('12.2 Edit Profil'),
                para('Pengguna dapat mengubah data profilnya melalui halaman edit profil (alamat URL: /profile). Data yang dapat diubah meliputi nama, email, nomor HP, alamat, dan password.'),
                note('Untuk akun mitra (Apotek dan Homecare), jika nomor HP diubah melalui halaman profil, nomor WhatsApp notifikasi di pengaturan admin juga akan otomatis ter-update secara dua arah (sinkronisasi 2 arah). Begitu pula sebaliknya, jika admin mengubah nomor WhatsApp mitra di panel admin, profil mitra yang bersangkutan juga akan otomatis ter-update.'),

                heading2('12.3 Hapus Akun'),
                para('Pengguna memiliki opsi untuk menghapus akun secara permanen. Proses ini memerlukan konfirmasi password sebagai langkah keamanan. Setelah akun dihapus, seluruh data terkait akan dihapus dari sistem dan tidak dapat dipulihkan.'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB XIII — NOMOR DARURAT
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB XIII — Nomor Darurat', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB XIII — NOMOR DARURAT'),
                para('Aplikasi Nersia Health menyediakan halaman khusus yang berisi informasi nomor telepon darurat yang dapat dihubungi dalam situasi darurat kesehatan maupun keamanan. Halaman ini dapat diakses melalui alamat URL: /darurat.'),

                heading2('13.1 Daftar Nomor Darurat'),
                makeTable(
                    ['Layanan', 'Nomor', 'Kategori', 'Keterangan'],
                    [
                        ['🚑 Ambulans / PSC 119', '119', 'Medis', 'Darurat medis nasional — 24 jam'],
                        ['🩸 PMI', '115', 'Medis', 'Palang Merah Indonesia'],
                        ['📞 Kemenkes Halo', '1500567', 'Konsultasi', 'Konsultasi kesehatan 24 jam'],
                        ['🚔 Polisi', '110', 'Keamanan', 'Keamanan dan bantuan darurat'],
                    ],
                ),
                emptyLine(),
                para('Pengguna dapat menyaring nomor berdasarkan kategori: Semua, Medis, Konsultasi, atau Keamanan. Setiap nomor dilengkapi dengan tombol panggilan langsung untuk memudahkan akses cepat dalam situasi darurat.'),
                screenshotPlaceholder('Halaman Nomor Darurat'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB XIV — PANEL ADMIN
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB XIV — Panel Admin', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB XIV — PANEL ADMIN'),
                para('Panel Admin merupakan area khusus yang hanya dapat diakses oleh pengguna dengan role Administrator. Melalui panel ini, admin dapat mengelola seluruh aspek operasional aplikasi Nersia Health, mulai dari verifikasi pembayaran, pengelolaan konten, hingga pengaturan sistem.'),
                note('Panel admin hanya dapat diakses oleh pengguna dengan role Admin (is_admin = true). Akses ke panel admin melalui alamat URL: /admin.'),

                heading2('14.1 Dashboard Admin'),
                para('Halaman dashboard (alamat URL: /admin) menampilkan ringkasan statistik operasional secara real-time yang meliputi:'),
                bullet('Jumlah total pengguna terdaftar dan pengguna baru minggu ini'),
                bullet('Total sesi skrining yang telah dilakukan dan jumlah identitas yang terdaftar'),
                bullet('Jumlah data monitoring kesehatan yang tercatat'),
                bullet('Jumlah skrining dengan risiko tinggi dan kasus darurat yang memerlukan perhatian'),
                bullet('Jumlah pembayaran konsultasi yang masih menunggu verifikasi'),
                para('Dashboard juga menampilkan notifikasi cepat (alert) untuk item-item yang memerlukan tindakan segera dari admin, seperti skrining risiko tinggi, pembayaran konsultasi, obat, dan homecare yang menunggu verifikasi.'),
                screenshotPlaceholder('Dashboard Admin — Statistik Ringkasan'),

                heading2('14.2 Kelola Hasil Skrining'),
                para('Pada halaman Hasil Skrining (alamat URL: /admin/screenings), admin dapat melihat seluruh hasil skrining yang dilakukan oleh pasien. Terdapat filter berdasarkan tingkat risiko (rendah, sedang, tinggi) untuk memudahkan identifikasi pasien yang memerlukan perhatian khusus.'),
                screenshotPlaceholder('Admin — Daftar Hasil Skrining'),

                heading2('14.3 Kelola Data Pasien'),
                para('Halaman Data Pasien (alamat URL: /admin/users) menampilkan daftar seluruh pengguna yang terdaftar di dalam sistem. Admin dapat melihat detail profil dan riwayat skrining setiap pasien.'),

                heading2('14.4 Kelola Konsultasi'),
                para('Halaman Konsultasi (alamat URL: /admin/konsultasi) merupakan pusat pengelolaan seluruh layanan konsultasi, meliputi:'),

                heading3('A. Order Konsultasi'),
                para('Admin dapat melihat seluruh order konsultasi, memfilter berdasarkan status (Pending, Disetujui, Ditolak), dan melakukan verifikasi pembayaran. Untuk setiap order, admin dapat menyetujui atau menolak berdasarkan bukti transfer yang diunggah oleh pasien.'),
                screenshotPlaceholder('Admin — Daftar Order Konsultasi'),

                heading3('B. Toggle Mode Biaya Per Kategori'),
                para('Di halaman konsultasi admin, terdapat tiga kartu kontrol biaya yang memungkinkan admin mengatur status biaya untuk setiap kategori tenaga kesehatan secara terpisah:'),
                bullet('👩‍⚕️ Perawat (Ners) — Toggle antara mode Gratis dan Berbayar'),
                bullet('👨‍⚕️ Dokter Umum — Toggle antara mode Gratis dan Berbayar'),
                bullet('🫀 Dokter Spesialis Penyakit Dalam — Toggle antara mode Gratis dan Berbayar'),
                screenshotPlaceholder('Admin — Toggle Mode Biaya Konsultasi Per Kategori'),

                heading3('C. Kelola Tenaga Kesehatan'),
                para('Pada halaman Tenaga Kesehatan (alamat URL: /admin/konsultasi/tenaga-kesehatan), admin dapat mengelola profil seluruh tenaga kesehatan yang terdaftar. Fitur ini meliputi penambahan, pengeditan, dan penghapusan data nakes, pengaturan foto profil, spesialisasi, pengalaman, harga konsultasi, nomor WhatsApp, dan sapaan pembuka.'),
                para('Tenaga kesehatan yang mendaftar melalui registrasi user (Perawat/Dokter) dan telah disetujui oleh admin akan otomatis terdaftar di halaman ini.'),
                screenshotPlaceholder('Admin — Kelola Tenaga Kesehatan'),

                heading3('D. Kelola Voucher'),
                para('Admin dapat membuat dan mengelola voucher diskon konsultasi melalui halaman Voucher (alamat URL: /admin/konsultasi/voucher). Setiap voucher memiliki kode unik, persentase diskon, batasan penggunaan, dan status aktif/nonaktif.'),

                heading3('E. Chat Admin'),
                para('Melalui halaman Chat Admin (alamat URL: /admin/konsultasi/chat), admin dapat melihat seluruh percakapan konsultasi yang aktif, membaca pesan dari pasien, dan memberikan balasan langsung dari panel admin. Setiap balasan akan disertai notifikasi WhatsApp ke pasien.'),

                pageBreak(),

                heading2('14.5 Kelola Obat'),
                para('Halaman Kelola Obat (alamat URL: /admin/obat) memungkinkan admin untuk mengelola katalog obat dan memproses pesanan dari pasien.'),

                heading3('A. Katalog Obat'),
                para('Admin dapat menambah, mengedit, dan menghapus produk obat. Setiap produk memiliki atribut: nama, harga, stok, deskripsi, foto, dan lokasi apotek mitra (UMLA FARMA 1, UMLA FARMA 2, atau Semua Apotek). Filter berdasarkan mitra apotek tersedia untuk memudahkan pengelolaan.'),
                screenshotPlaceholder('Admin — Kelola Katalog Obat'),

                heading3('B. Pesanan Obat'),
                para('Admin dapat melihat daftar pesanan masuk dan mengambil tindakan berupa menyetujui, mengirim, atau menolak pesanan. Setiap perubahan status akan memicu notifikasi WhatsApp ke apotek mitra terkait.'),

                heading2('14.6 Kelola Homecare'),
                para('Halaman Kelola Homecare (alamat URL: /admin/homecare) digunakan untuk mengelola paket layanan homecare dan memproses booking dari pasien. Admin dapat menambah/mengedit paket layanan, serta menyetujui, menyelesaikan, atau menolak booking yang masuk.'),
                screenshotPlaceholder('Admin — Kelola Homecare dan Booking'),

                heading2('14.7 Kelola Edukasi'),
                para('Melalui halaman Kelola Edukasi (alamat URL: /admin/edukasi), admin dapat membuat, mengedit, dan menghapus konten edukasi kesehatan berupa artikel dan video. Konten mendukung format markdown/HTML dan embed video YouTube.'),

                heading2('14.8 Kelola Akses Pengguna'),
                para('Halaman Akses Pengguna (alamat URL: /admin/access) merupakan pusat pengelolaan hak akses yang meliputi:'),
                bullet('Penambahan dan pencabutan akses admin berdasarkan email pengguna terdaftar'),
                bullet('Verifikasi dan persetujuan pendaftaran mitra (Nakes, Apotek, Homecare)'),
                bullet('Penolakan pendaftaran mitra yang tidak memenuhi syarat'),
                para('Ketika admin menyetujui pendaftaran mitra, sistem secara otomatis akan:'),
                bullet('Mengaktifkan akun sehingga mitra dapat login ke aplikasi'),
                bullet('Menyinkronisasi nomor HP mitra ke pengaturan WhatsApp notifikasi'),
                bullet('Untuk Dokter dan Perawat: mendaftarkan secara otomatis sebagai tenaga kesehatan konsultasi'),
                screenshotPlaceholder('Admin — Kelola Akses Pengguna dan Verifikasi Mitra'),
            ],
        },

        // ═══════════════════════════════════════════════════════════
        // BAB XV — PENGATURAN MITRA DAN WHATSAPP
        // ═══════════════════════════════════════════════════════════
        {
            properties: { page: { margin: { top: 1440, bottom: 1440, left: 1440, right: 1440 } } },
            headers: {
                default: new Header({
                    children: [new Paragraph({
                        alignment: AlignmentType.RIGHT,
                        children: [new TextRun({ text: 'BAB XV — Pengaturan Mitra dan WhatsApp', size: 16, color: GRAY, font: 'Calibri', italics: true })],
                    })],
                }),
            },
            children: [
                heading1('BAB XV — PENGATURAN MITRA DAN WHATSAPP'),
                para('Halaman Pengaturan Mitra dan WhatsApp (alamat URL: /admin/settings) merupakan pusat konfigurasi untuk mengelola status biaya konsultasi dan nomor WhatsApp notifikasi seluruh mitra yang terintegrasi dengan aplikasi.'),

                heading2('15.1 Pengaturan Status Biaya Konsultasi'),
                para('Pada bagian ini, admin dapat mengatur mode biaya konsultasi secara terpisah untuk setiap kategori tenaga kesehatan:'),
                makeTable(
                    ['Kategori', 'Pilihan Mode Biaya'],
                    [
                        ['👩‍⚕️ Perawat (Ners)', '💳 Harus Bayar (Berbayar) atau 🎁 Gratis 100%'],
                        ['👨‍⚕️ Dokter Umum', '💳 Harus Bayar (Berbayar) atau 🎁 Gratis 100%'],
                        ['🫀 Dokter Spesialis Penyakit Dalam', '💳 Harus Bayar (Berbayar) atau 🎁 Gratis 100%'],
                    ],
                ),
                emptyLine(),
                para('Ketika suatu kategori diatur sebagai "Gratis 100%", seluruh pengguna dapat langsung mengakses fitur chat konsultasi untuk kategori tersebut tanpa melalui proses pembayaran.'),
                screenshotPlaceholder('Pengaturan Mode Biaya Konsultasi Per Kategori'),

                heading2('15.2 Nomor WhatsApp Notifikasi'),
                para('Bagian ini menampilkan dan mengatur nomor WhatsApp yang menjadi tujuan notifikasi otomatis saat terjadi transaksi baru (pesanan obat, booking homecare, atau pesan konsultasi). Berikut adalah daftar nomor yang dikonfigurasi:'),
                makeTable(
                    ['Pengaturan', 'Keterangan'],
                    [
                        ['Admin Utama', 'Nomor penerima seluruh notifikasi transaksi baru'],
                        ['UMLA FARMA 1 (Kampus 1)', 'Notifikasi pesanan obat untuk apotek mitra pertama'],
                        ['UMLA FARMA 2 (Kembangbahu)', 'Notifikasi pesanan obat untuk apotek mitra kedua'],
                        ['Medical Center UMLA 1 (Plosowahyu)', 'Notifikasi booking homecare untuk mitra pertama'],
                        ['Medical Center UMLA 2 (Paciran)', 'Notifikasi booking homecare untuk mitra kedua'],
                    ],
                ),
                emptyLine(),
                screenshotPlaceholder('Pengaturan Nomor WhatsApp Notifikasi'),

                heading2('15.3 Mekanisme Sinkronisasi Dua Arah'),
                para('Aplikasi Nersia Health menerapkan mekanisme sinkronisasi dua arah (two-way sync) untuk nomor WhatsApp mitra. Mekanisme ini memastikan konsistensi data antara panel admin dan profil akun mitra:'),
                bullet('Perubahan dari Admin: Jika admin mengubah nomor WhatsApp mitra di halaman pengaturan, profil akun mitra yang bersangkutan akan otomatis ter-update.'),
                bullet('Perubahan dari Mitra: Jika mitra mengubah nomor HP melalui halaman profil pribadinya, nomor WhatsApp di pengaturan admin juga akan otomatis ter-update.'),
                bullet('Pendaftaran Baru: Ketika mitra baru mendaftar, nomor HP yang diisi saat registrasi akan otomatis tersimpan ke pengaturan WhatsApp notifikasi admin.'),
                bullet('Persetujuan Admin: Saat admin menyetujui pendaftaran mitra baru, sistem mengonfirmasi ulang sinkronisasi nomor HP ke pengaturan admin.'),
                note('Mekanisme sinkronisasi ini berlaku untuk seluruh jenis mitra, baik Apotek (UMLA FARMA) maupun Homecare (Medical Center).'),

                emptyLine(), emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { before: 600 },
                    border: { top: { style: BorderStyle.SINGLE, size: 2, color: ACCENT } },
                    children: [],
                }),
                emptyLine(),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    children: [
                        new TextRun({ text: 'NERSIA ', bold: true, size: 28, color: BRAND, font: 'Calibri' }),
                        new TextRun({ text: 'HEALTH', bold: true, size: 28, color: ACCENT, font: 'Calibri' }),
                    ],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    spacing: { after: 200 },
                    children: [new TextRun({ text: 'Chatbot Smart Health Screening & Care', size: 20, color: GRAY, font: 'Calibri', italics: true })],
                }),
                new Paragraph({
                    alignment: AlignmentType.CENTER,
                    children: [new TextRun({ text: '— Akhir Dokumen —', size: 20, color: GRAY, font: 'Calibri' })],
                }),
            ],
        },
    ],
});

// ═══════════════════════════════════════════════════════════════════
// GENERATE FILE
// ═══════════════════════════════════════════════════════════════════
const outputPath = path.resolve('docs', 'Panduan_Penggunaan_Nersia_Health.docx');

Packer.toBuffer(doc).then(buffer => {
    fs.writeFileSync(outputPath, buffer);
    console.log(`✅ Dokumen Word berhasil dibuat: ${outputPath}`);
    console.log(`   Ukuran: ${(buffer.length / 1024).toFixed(1)} KB`);
}).catch(err => {
    console.error('❌ Gagal membuat dokumen:', err);
    process.exit(1);
});
