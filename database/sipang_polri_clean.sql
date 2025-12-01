-- SIPANG POLRI Database Setup - CLEAN VERSION
-- Database schema only, ready for fresh data entry
-- Run this file in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS sipang_polri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sipang_polri;

-- ===== TABLE DEFINITIONS =====

-- Create Polsek table
CREATE TABLE polsek (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    kode VARCHAR(20) UNIQUE NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('USER_SATFUNG', 'USER_POLSEK', 'ADMIN_BAGREN', 'ADMIN_SIKEU') NOT NULL,
    polsek_id INT,
    jabatan VARCHAR(100),
    nip VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Kegiatan table
CREATE TABLE kegiatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    kode VARCHAR(50) UNIQUE NOT NULL,
    pagu DECIMAL(15,2) NOT NULL DEFAULT 0,
    sumber_dana ENUM('RM', 'PNBP') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Pengajuan table (empty, ready for fresh data)
CREATE TABLE pengajuan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_surat VARCHAR(50) UNIQUE NOT NULL,
    tanggal_pengajuan DATE NOT NULL,
    bulan_pengajuan VARCHAR(20) NOT NULL,
    tahun_pengajuan YEAR NOT NULL,
    sumber_dana ENUM('RM', 'PNBP') NOT NULL,
    uraian TEXT NOT NULL,
    penanggung_jawab VARCHAR(100) NOT NULL,
    bendahara_pengeluaran_pembantu VARCHAR(100) NOT NULL,
    kegiatan_id INT NOT NULL,
    jumlah_diajukan DECIMAL(15,2) NOT NULL,
    jumlah_pagu DECIMAL(15,2) NOT NULL,
    sisa_pagu DECIMAL(15,2) NOT NULL,
    status ENUM('DRAFT', 'TERKIRIM', 'TERIMA_BERKAS', 'DISPOSISI_KABAG_REN', 'DISPOSISI_WAKA', 'TERIMA_SIKEU', 'DIBAYARKAN', 'DITOLAK') DEFAULT 'DRAFT',
    status_keterangan TEXT,
    user_id INT NOT NULL,
    polsek_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Pengajuan Detail table (empty, ready for fresh data)
CREATE TABLE pengajuan_detail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_id INT NOT NULL,
    kegiatan_id INT NOT NULL,
    kode VARCHAR(50) NOT NULL,
    uraian_detail TEXT NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Pengajuan Status Log table (empty, ready for fresh logs)
CREATE TABLE pengajuan_status_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pengajuan_id INT NOT NULL,
    status_lama ENUM('DRAFT', 'TERKIRIM', 'TERIMA_BERKAS', 'DISPOSISI_KABAG_REN', 'DISPOSISI_WAKA', 'TERIMA_SIKEU', 'DIBAYARKAN', 'DITOLAK'),
    status_baru ENUM('DRAFT', 'TERKIRIM', 'TERIMA_BERKAS', 'DISPOSISI_KABAG_REN', 'DISPOSISI_WAKA', 'TERIMA_SIKEU', 'DIBAYARKAN', 'DITOLAK') NOT NULL,
    keterangan TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===== INITIAL DATA SETUP =====

-- Insert Polsek (Master Data)
INSERT INTO polsek (nama, kode, alamat) VALUES
('Polsek Garut Kota', 'PSK-GRT-KTA', 'Jl. Ahmad Yani No. 123, Garut'),
('Polsek Garut Selatan', 'PSK-GRT-SLT', 'Jl. Raya Selatan No. 45, Garut'),
('Polsek Garut Utara', 'PSK-GRT-UTR', 'Jl. Raya Utara No. 67, Garut');

-- Insert Users (Test Accounts)
INSERT INTO users (username, password, nama_lengkap, email, role, polsek_id, jabatan, nip) VALUES
('polsek.grt.kta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kapolsek Garut Kota', 'kapolsek.grt.kta@polri.go.id', 'USER_POLSEK', 1, 'Kapolsek', '196501011990031001'),
('wakapolsek.grt.kta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Wakapolsek Garut Kota', 'wakapolsek.grt.kta@polri.go.id', 'USER_POLSEK', 1, 'Wakapolsek', '196502011990031002'),
('satfung.grt.kta', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kaur Rena Garut Kota', 'kaur.rena.grt.kta@polri.go.id', 'USER_SATFUNG', 1, 'Kaur Rena', '196503011990031003'),
('admin.bagren', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Bagren Polres Garut', 'admin.bagren@polri.go.id', 'ADMIN_BAGREN', NULL, 'Admin Bagren', '196504011990031004'),
('admin.sikeu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Sikeu Polres Garut', 'admin.sikeu@polri.go.id', 'ADMIN_SIKEU', NULL, 'Admin Sikeu', '196505011990031005');

-- Insert Kegiatan (Master Data)
INSERT INTO kegiatan (nama, kode, pagu, sumber_dana) VALUES
('MAKAN TAHANAN POLSEK', 'BI 3137 EBA KD 521112', 12775000, 'RM'),
('PERAWATAN TAHANAN POLSEK', 'BI 3137 EBA LU 521119', 3650000, 'RM'),
('TINDAK PIDANA UMUM POLSEK', 'BI 3142 BCE BX 521119', 60000000, 'RM'),
('PEMELIHARAAN PERALATAN FUNGSIONAL INTEL POLSEK - HAR KOMPUTER SKCK', 'BP 5059 EBA MS 523121', 1460000, 'PNBP'),
('PATROLI SAMAPTA POLSEK', 'BQ 3130 BHB FM 521119', 27000000, 'RM'),
('PATROLI LANTAS POLSEK', 'BQ 3133 BHB FS 521119', 46000000, 'PNBP'),
('PEMELIHARAAN GEDUNG KANTOR POLSEK', 'WA 3073 EBA LP 523111', 14994000, 'RM'),
('KEBUTUHAN DASAR PERKANTORAN - POLSEK', 'WA 3073 EBA OE 521111', 9120000, 'RM');

-- ===== FOREIGN KEY CONSTRAINTS =====
ALTER TABLE users ADD CONSTRAINT fk_users_polsek FOREIGN KEY (polsek_id) REFERENCES polsek(id) ON DELETE SET NULL;
ALTER TABLE pengajuan ADD CONSTRAINT fk_pengajuan_kegiatan FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id);
ALTER TABLE pengajuan ADD CONSTRAINT fk_pengajuan_user FOREIGN KEY (user_id) REFERENCES users(id);
ALTER TABLE pengajuan ADD CONSTRAINT fk_pengajuan_polsek FOREIGN KEY (polsek_id) REFERENCES polsek(id);
ALTER TABLE pengajuan_detail ADD CONSTRAINT fk_detail_pengajuan FOREIGN KEY (pengajuan_id) REFERENCES pengajuan(id) ON DELETE CASCADE;
ALTER TABLE pengajuan_detail ADD CONSTRAINT fk_detail_kegiatan FOREIGN KEY (kegiatan_id) REFERENCES kegiatan(id);
ALTER TABLE pengajuan_status_log ADD CONSTRAINT fk_log_pengajuan FOREIGN KEY (pengajuan_id) REFERENCES pengajuan(id) ON DELETE CASCADE;
ALTER TABLE pengajuan_status_log ADD CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id);

-- ===== INDEXES FOR PERFORMANCE =====
CREATE INDEX idx_pengajuan_tanggal ON pengajuan(tanggal_pengajuan);
CREATE INDEX idx_pengajuan_status ON pengajuan(status);
CREATE INDEX idx_pengajuan_user ON pengajuan(user_id);
CREATE INDEX idx_pengajuan_polsek ON pengajuan(polsek_id);
CREATE INDEX idx_status_log_pengajuan ON pengajuan_status_log(pengajuan_id);
CREATE INDEX idx_status_log_created ON pengajuan_status_log(created_at);

-- ===== DATABASE VIEWS =====
CREATE VIEW v_pengajuan_complete AS
SELECT 
    p.id,
    p.nomor_surat,
    p.tanggal_pengajuan,
    p.bulan_pengajuan,
    p.tahun_pengajuan,
    p.sumber_dana,
    p.uraian,
    p.penanggung_jawab,
    p.bendahara_pengeluaran_pembantu,
    p.jumlah_diajukan,
    p.jumlah_pagu,
    p.sisa_pagu,
    p.status,
    p.status_keterangan,
    p.created_at,
    p.updated_at,
    u.nama_lengkap as nama_user,
    u.role as user_role,
    ps.nama as nama_polsek,
    ps.kode as kode_polsek,
    k.nama as nama_kegiatan,
    k.kode as kode_kegiatan
FROM pengajuan p
LEFT JOIN users u ON p.user_id = u.id
LEFT JOIN polsek ps ON p.polsek_id = ps.id
LEFT JOIN kegiatan k ON p.kegiatan_id = k.id;

CREATE VIEW v_status_tracking AS
SELECT 
    p.id as pengajuan_id,
    p.nomor_surat,
    p.status as status_terkini,
    p.created_at as tanggal_pengajuan,
    psl.status_baru,
    psl.keterangan,
    psl.created_at as tanggal_status,
    u.nama_lengkap as user_status,
    u.role as role_user_status
FROM pengajuan p
LEFT JOIN pengajuan_status_log psl ON p.id = psl.pengajuan_id
LEFT JOIN users u ON psl.user_id = u.id
ORDER BY p.id, psl.created_at DESC;
