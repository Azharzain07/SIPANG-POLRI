<?php

/**
 * SIPANG POLRI - Database Configuration & User Roles
 * Sistem Informasi Perencanaan Anggaran Kepolisian
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sipang_polri');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'SIPANG POLRI');
define('APP_FULL_NAME', 'Sistem Informasi Perencanaan Anggaran Kepolisian');
define('APP_AUTHOR', 'Polres Garut');
define('APP_VERSION', '1.0.0');

// User Roles Constants
define('ROLE_USER_SATFUNG', 'USER_SATFUNG');
define('ROLE_USER_POLSEK', 'USER_POLSEK');
define('ROLE_ADMIN_BAGREN', 'ADMIN_BAGREN');
define('ROLE_ADMIN_SIKEU', 'ADMIN_SIKEU');

// Status Constants (match DB enum values in database/sipang_polri.sql)
define('STATUS_DRAFT', 'DRAFT');
define('STATUS_TERKIRIM', 'TERKIRIM');
define('STATUS_TERIMA_BERKAS', 'TERIMA_BERKAS');
define('STATUS_DISPOSISI_KABAG_REN', 'DISPOSISI_KABAG_REN');
define('STATUS_DISPOSISI_WAKA', 'DISPOSISI_WAKA');
define('STATUS_TERIMA_SIKEU', 'TERIMA_SIKEU');
define('STATUS_DIBAYARKAN', 'DIBAYARKAN');
define('STATUS_DITOLAK', 'DITOLAK');

// Status Labels (Indonesian)
define('STATUS_LABELS', [
    STATUS_DRAFT => 'Draft',
    STATUS_TERKIRIM => 'Terkirim',
    STATUS_TERIMA_BERKAS => 'Terima Berkas',
    STATUS_DISPOSISI_KABAG_REN => 'Disposisi Kabag Ren',
    STATUS_DISPOSISI_WAKA => 'Disposisi Waka',
    STATUS_TERIMA_SIKEU => 'Terima Sikeu',
    STATUS_DIBAYARKAN => 'Dibayarkan',
    STATUS_DITOLAK => 'Ditolak'
]);

// Role Permissions
define('ROLE_PERMISSIONS', [
    ROLE_USER_SATFUNG => [
        'can_create_pengajuan' => true,
        'can_edit_own_pengajuan' => true,
        'can_view_own_pengajuan' => true,
        'can_submit_pengajuan' => true,
        'can_view_riwayat' => true,
        'can_export_data' => false,
        'can_manage_users' => false,
        'can_approve_pengajuan' => false
    ],
    ROLE_USER_POLSEK => [
        'can_create_pengajuan' => true,
        'can_edit_own_pengajuan' => true,
        'can_view_own_pengajuan' => true,
        'can_submit_pengajuan' => true,
        'can_view_riwayat' => true,
        'can_export_data' => false,
        'can_manage_users' => false,
        'can_approve_pengajuan' => false
    ],
    ROLE_ADMIN_BAGREN => [
        'can_create_pengajuan' => true,
        'can_edit_own_pengajuan' => true,
        'can_view_own_pengajuan' => true,
        'can_submit_pengajuan' => true,
        'can_view_riwayat' => true,
        'can_export_data' => true,
        'can_manage_users' => true,
        'can_approve_pengajuan' => true,
        'can_view_all_pengajuan' => true,
        'can_change_status' => true,
        'can_disposisi' => true,
        'can_send_to_sikeu' => true
    ],
    ROLE_ADMIN_SIKEU => [
        'can_create_pengajuan' => false,
        'can_edit_own_pengajuan' => false,
        'can_view_own_pengajuan' => false,
        'can_submit_pengajuan' => false,
        'can_view_riwayat' => true,
        'can_export_data' => true,
        'can_manage_users' => false,
        'can_approve_pengajuan' => true,
        'can_view_all_pengajuan' => true,
        'can_change_status' => true,
        'can_disposisi' => false,
        'can_process_payment' => true,
        'can_view_financial_reports' => true,
        'can_download_reports' => true,
        'can_print_pdf' => true,
        'can_export_excel' => true
    ]
]);

// Database Connection Class
class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

// User Authentication Class
class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function login($username, $password)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, p.nama as nama_polsek, p.kode as kode_polsek 
            FROM users u 
            LEFT JOIN polsek p ON u.polsek_id = p.id 
            WHERE u.username = ? AND u.is_active = 1
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['polsek_id'] = $user['polsek_id'];
            $_SESSION['nama_polsek'] = $user['nama_polsek'];
            $_SESSION['kode_polsek'] = $user['kode_polsek'];
            $_SESSION['jabatan'] = $user['jabatan'];
            return true;
        }
        return false;
    }

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function hasPermission($permission)
    {
        if (!isset($_SESSION['role'])) {
            return false;
        }

        $permissions = ROLE_PERMISSIONS[$_SESSION['role']] ?? [];
        return $permissions[$permission] ?? false;
    }

    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'nama_lengkap' => $_SESSION['nama_lengkap'],
            'role' => $_SESSION['role'],
            'polsek_id' => $_SESSION['polsek_id'],
            'nama_polsek' => $_SESSION['nama_polsek'],
            'kode_polsek' => $_SESSION['kode_polsek'],
            'jabatan' => $_SESSION['jabatan']
        ];
    }
}

// Pengajuan Management Class
class PengajuanManager
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createPengajuan($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO pengajuan (
                nomor_surat, tanggal_pengajuan, bulan_pengajuan, tahun_pengajuan,
                sumber_dana, uraian, penanggung_jawab, bendahara_pengeluaran_pembantu,
                kegiatan_id, jumlah_diajukan, jumlah_pagu, sisa_pagu,
                user_id, polsek_id, status, file_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $nomor_surat = $this->generateNomorSurat();

        return $stmt->execute([
            $nomor_surat,
            $data['tanggal_pengajuan'],
            $data['bulan_pengajuan'],
            $data['tahun_pengajuan'],
            $data['sumber_dana'],
            $data['uraian'],
            $data['penanggung_jawab'],
            $data['bendahara_pengeluaran_pembantu'],
            $data['kegiatan_id'],
            $data['jumlah_diajukan'],
            $data['jumlah_pagu'],
            $data['sisa_pagu'],
            $_SESSION['user_id'],
            $_SESSION['polsek_id'],
            STATUS_DRAFT,
            $data['file_path'] ?? null
        ]);
    }

    public function rejectPengajuan($pengajuan_id, $keterangan)
    {
        $this->db->beginTransaction();

        try {
            // Update pengajuan status to DITOLAK with keterangan
            $stmt = $this->db->prepare("
                UPDATE pengajuan 
                SET status = 'DITOLAK', 
                    status_keterangan = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$keterangan, $pengajuan_id]);

            // Log status change in pengajuan_status_log
            $stmt = $this->db->prepare("
                SELECT status FROM pengajuan WHERE id = ?
            ");
            $stmt->execute([$pengajuan_id]);
            $current_status = $stmt->fetchColumn();

            $stmt = $this->db->prepare("
                INSERT INTO pengajuan_status_log (pengajuan_id, status_lama, status_baru, keterangan, user_id)
                VALUES (?, ?, 'DITOLAK', ?, ?)
            ");
            $stmt->execute([$pengajuan_id, $current_status, $keterangan, $_SESSION['user_id'] ?? null]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("rejectPengajuan error: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($pengajuan_id, $status_baru, $keterangan = '', $user_id = null)
    {
        $this->db->beginTransaction();

        try {
            // Get current status
            $stmt = $this->db->prepare("SELECT status FROM pengajuan WHERE id = ?");
            $stmt->execute([$pengajuan_id]);
            $current_status = $stmt->fetchColumn();

            // Update pengajuan status
            $stmt = $this->db->prepare("
                UPDATE pengajuan 
                SET status = ?, status_keterangan = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$status_baru, $keterangan, $pengajuan_id]);

            // Log status change
            $stmt = $this->db->prepare("
                INSERT INTO pengajuan_status_log (pengajuan_id, status_lama, status_baru, keterangan, user_id)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$pengajuan_id, $current_status, $status_baru, $keterangan, $user_id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function revisePengajuan($pengajuan_id, $jumlah_baru, $keterangan_revisi = '')
    {
        $this->db->beginTransaction();

        try {
            // Update pengajuan with new amount and reset status to DRAFT
            $stmt = $this->db->prepare("
                UPDATE pengajuan 
                SET jumlah_diajukan = ?, 
                    status = 'DRAFT', 
                    status_keterangan = ?,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$jumlah_baru, $keterangan_revisi, $pengajuan_id]);

            // Log status change in pengajuan_status_log
            $stmt = $this->db->prepare("
                INSERT INTO pengajuan_status_log (pengajuan_id, status_lama, status_baru, keterangan, user_id)
                VALUES (?, 'DITOLAK', 'DRAFT', ?, ?)
            ");
            $stmt->execute([$pengajuan_id, $keterangan_revisi, $_SESSION['user_id'] ?? null]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("revisePengajuan error: " . $e->getMessage());
            return false;
        }
    }

    public function getRiwayat($user_role, $polsek_id = null)
    {
        $sql = "
            SELECT p.*, p.user_id, u.nama_lengkap, u.role, ps.nama as nama_polsek, k.nama as nama_kegiatan, k.kode as kode_kegiatan
            FROM pengajuan p
            LEFT JOIN users u ON p.user_id = u.id
            LEFT JOIN polsek ps ON p.polsek_id = ps.id
            LEFT JOIN kegiatan k ON p.kegiatan_id = k.id
            WHERE 1=1
        ";

        $params = [];

        // Role-based filtering
        if ($user_role === ROLE_USER_SATFUNG || $user_role === ROLE_USER_POLSEK) {
            $sql .= " AND p.user_id = ?";
            $params[] = $_SESSION['user_id'];
        } elseif ($user_role === ROLE_ADMIN_BAGREN && $polsek_id) {
            $sql .= " AND p.polsek_id = ?";
            $params[] = $polsek_id;
        } elseif ($user_role === ROLE_ADMIN_SIKEU) {
            // SIKEU only sees pengajuan that are already approved by BAGREN
            // Use underscore format to match database: TERIMA_SIKEU, DIBAYARKAN
            $sql .= " AND p.status IN ('TERIMA_SIKEU', 'DIBAYARKAN')";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function generateNomorSurat()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = "BU-{$year}{$month}";

        try {
            // Get the highest existing number for this month/year
            $stmt = $this->db->prepare("
                SELECT nomor_surat 
                FROM pengajuan 
                WHERE nomor_surat LIKE ? 
                AND YEAR(created_at) = ?
                ORDER BY nomor_surat DESC 
                LIMIT 1
            ");
            $stmt->execute([$prefix . '-%', $year]);
            $lastNomor = $stmt->fetchColumn();

            if ($lastNomor) {
                // Extract the number part and increment
                preg_match('/BU-\d{6}-(\d{4})/', $lastNomor, $matches);
                if (isset($matches[1])) {
                    $lastNum = intval($matches[1]);
                    $nextNum = $lastNum + 1;
                } else {
                    $nextNum = 1;
                }
            } else {
                $nextNum = 1;
            }

            $newNomor = $prefix . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            // Check if this nomor already exists (double-check)
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM pengajuan WHERE nomor_surat = ?");
            $stmt->execute([$newNomor]);
            $exists = $stmt->fetchColumn();

            if ($exists == 0) {
                return $newNomor;
            } else {
                // If exists, try next number
                $nextNum++;
                $newNomor = $prefix . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
                return $newNomor;
            }
        } catch (Exception $e) {
            // Fallback with timestamp
            $timestamp = substr(time(), -6);
            return $prefix . '-' . $timestamp;
        }
    }
}

// Helper Functions
function formatRupiah($angka)
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function getStatusClass($status)
{
    $classes = [
        STATUS_DRAFT => 'status-draft',
        STATUS_TERKIRIM => 'status-terkirim',
        STATUS_TERIMA_BERKAS => 'status-terima',
        STATUS_DISPOSISI_KABAG_REN => 'status-disposisi',
        STATUS_DISPOSISI_WAKA => 'status-disposisi',
        STATUS_TERIMA_SIKEU => 'status-sikeu',
        STATUS_DIBAYARKAN => 'status-selesai',
        STATUS_DITOLAK => 'status-ditolak'
    ];

    return $classes[$status] ?? 'status-default';
}

function getStatusLabel($status)
{
    return STATUS_LABELS[$status] ?? $status;
}

// Session will be started by individual files that need it