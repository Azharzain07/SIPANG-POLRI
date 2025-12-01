<?php
/**
 * SIPANG POLRI - Application Constants
 * Version: 1.0.0
 */

// Application Info
define('APP_NAME', 'SIPANG POLRI');
define('APP_FULL_NAME', 'Sistem Informasi Perencanaan Anggaran Kepolisian');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Bagren Polres Garut');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// File Upload Configuration
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);

// Pagination Configuration
define('ITEMS_PER_PAGE', 5);

// Date Format
define('DATE_FORMAT_DISPLAY', 'd/m/Y');
define('DATE_FORMAT_DATABASE', 'Y-m-d');

// Status Constants
define('STATUS_PENDING', 'Pending');
define('STATUS_APPROVED', 'Diterima');
define('STATUS_REJECTED', 'Ditolak');

// Sumber Dana
define('SUMBER_RM', 'RM');
define('SUMBER_PNBP', 'PNBP');

// User Roles
define('ROLE_USER', 'User');
define('ROLE_ADMIN_NPWP', 'Admin NPWP');
define('ROLE_ADMIN_PPK', 'Admin PPK');
define('ROLE_SUPER_ADMIN', 'Super Admin');

// Polsek Location
define('POLSEK_NAME', 'Polsek Garut Kota');
define('POLSEK_ADDRESS', 'Jalan Pembangunan No. 200, Garut, Jawa Barat 44151');
define('POLSEK_PHONE', '(0262) 232 262');
define('POLSEK_EMAIL', 'info@polresgarut.go.id');

// Error Messages
define('ERROR_REQUIRED_FIELDS', 'Mohon lengkapi semua field yang wajib diisi!');
define('ERROR_INVALID_CREDENTIALS', 'Username atau password salah!');
define('ERROR_CAPTCHA_MISMATCH', 'Kode captcha tidak sesuai!');
define('ERROR_SESSION_EXPIRED', 'Sesi Anda telah berakhir. Silakan login kembali.');
define('ERROR_UNAUTHORIZED', 'Anda tidak memiliki akses ke halaman ini.');
define('ERROR_DATABASE', 'Terjadi kesalahan pada database. Silakan coba lagi.');

// Success Messages
define('SUCCESS_LOGIN', 'Login berhasil! Selamat datang.');
define('SUCCESS_LOGOUT', 'Logout berhasil. Terima kasih.');
define('SUCCESS_SUBMIT', 'Data berhasil dikirim!');
define('SUCCESS_UPDATE', 'Data berhasil diperbarui!');
define('SUCCESS_DELETE', 'Data berhasil dihapus!');
define('SUCCESS_APPROVE', 'Pengajuan berhasil disetujui!');
define('SUCCESS_REJECT', 'Pengajuan berhasil ditolak!');
?>

