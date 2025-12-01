<?php

/**
 * SIPANG POLRI - Admin Dashboard API
 * Khusus untuk Admin Dashboard
 * - Badge status dengan normalisasi (TERKIRIM → TERIMA BERKAS)
 * - Data pengajuan mengikuti dari user API
 */

// Suppress error output - handle all errors as JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database_config.php';

header('Content-Type: application/json');

// Allow requests from same origin
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = array('http://localhost', 'http://127.0.0.1');

if (in_array($origin, $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized - Please login first',
        'code' => 'UNAUTHORIZED'
    ]);
    http_response_code(401);
    exit();
}

// Only allow ADMIN roles
$currentUser = $auth->getCurrentUser();
if (!in_array($currentUser['role'], ['ADMIN_BAGREN', 'ADMIN_SIKEU'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Forbidden - Admin access required',
        'code' => 'FORBIDDEN'
    ]);
    http_response_code(403);
    exit();
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_pengajuan_dashboard':
            handleGetPengajuanDashboard();
            break;

        case 'get_statistics':
            handleGetStatistics();
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Action not found',
                'code' => 'NOT_FOUND'
            ]);
            http_response_code(404);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'code' => 'SERVER_ERROR'
    ]);
    http_response_code(500);
}

/**
 * Get pengajuan data untuk dashboard admin
 * Status sudah di-normalize: TERKIRIM → TERIMA BERKAS
 * Supports pagination via limit and offset query parameters
 */
function handleGetPengajuanDashboard()
{
    try {
        $db = Database::getInstance()->getConnection();

        if (!$db) {
            throw new Exception('Database connection failed');
        }

        // Pagination parameters (default 50 items per page)
        $limit = (int)($_GET['limit'] ?? 50);
        $offset = (int)($_GET['offset'] ?? 0);

        // Validate limit (max 100 items per page)
        $limit = min(max($limit, 1), 100);
        $offset = max($offset, 0);

        // Get total count query
        $countQuery = "SELECT COUNT(*) as total FROM pengajuan";
        $countResult = $db->query($countQuery);
        $totalCount = $countResult->fetch(PDO::FETCH_ASSOC)['total'];

        // Get paginated pengajuan with normalized status
        $query = "SELECT 
                    p.id,
                    p.user_id,
                    p.nomor_surat,
                    p.bulan_pengajuan,
                    p.tahun_pengajuan,
                    p.uraian,
                    k.nama as nama_kegiatan,
                    p.jumlah_diajukan,
                    p.sumber_dana,
                    p.status,
                    p.tanggal_pengajuan,
                    p.updated_at as tanggal_update,
                    p.status_keterangan as keterangan,
                    p.penanggung_jawab,
                    p.bendahara_pengeluaran_pembantu,
                    p.file_path,
                    u.nama_lengkap,
                    u.role,
                    u.jabatan as satfung
                FROM pengajuan p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN kegiatan k ON p.kegiatan_id = k.id
                ORDER BY p.updated_at DESC
                LIMIT :limit OFFSET :offset";

        $result = $db->prepare($query);
        $result->bindValue(':limit', $limit, PDO::PARAM_INT);
        $result->bindValue(':offset', $offset, PDO::PARAM_INT);
        $result->execute();

        if (!$result) {
            throw new Exception('Query failed: ' . json_encode($db->errorInfo()));
        }

        $pengajuan = [];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if (!$row) break;

            // NORMALIZE STATUS: TERKIRIM → TERIMA BERKAS
            $normalizedStatus = normalizeStatusForBadge($row['status']);

            $pengajuan[] = [
                'id' => (int)$row['id'],
                'user_id' => (int)$row['user_id'],
                'kode' => $row['nomor_surat'],
                'bulan_pengajuan' => $row['bulan_pengajuan'],
                'tahun_pengajuan' => $row['tahun_pengajuan'],
                'uraian' => $row['uraian'],
                'nama_kegiatan' => $row['nama_kegiatan'],
                'jumlah_diajukan' => (float)$row['jumlah_diajukan'],
                'sumber_dana' => $row['sumber_dana'],
                'status' => $normalizedStatus,  // ← NORMALIZED STATUS
                'status_raw' => $row['status'], // ← ORIGINAL STATUS (untuk debug)
                'tanggal_pengajuan' => $row['tanggal_pengajuan'],
                'tanggal_update' => $row['tanggal_update'],
                'keterangan' => $row['keterangan'],
                'penanggung_jawab' => $row['penanggung_jawab'],
                'bendahara_pengeluaran_pembantu' => $row['bendahara_pengeluaran_pembantu'],
                'file_path' => $row['file_path'],
                'nama_lengkap' => $row['nama_lengkap'],
                'role' => $row['role'],
                'satfung' => $row['satfung']
            ];
        }

        // Calculate pagination metadata
        $totalPages = ceil($totalCount / $limit);
        $currentPage = floor($offset / $limit) + 1;
        $hasNextPage = $currentPage < $totalPages;
        $nextOffset = $hasNextPage ? $offset + $limit : null;

        echo json_encode([
            'success' => true,
            'data' => $pengajuan,
            'count' => count($pengajuan),
            'pagination' => [
                'total' => $totalCount,
                'limit' => $limit,
                'offset' => $offset,
                'page' => $currentPage,
                'total_pages' => $totalPages,
                'has_next' => $hasNextPage,
                'next_offset' => $nextOffset
            ],
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * Get statistics dengan status yang sudah di-normalize
 */
function handleGetStatistics()
{
    $db = Database::getInstance()->getConnection();

    try {
        $query = "SELECT status, COUNT(*) as count
                FROM pengajuan
                GROUP BY status";

        $result = $db->query($query);
        $stats = [
            'TERIMA_BERKAS' => 0,      // PENDING (includes TERKIRIM)
            'DISPOSISI_KABAG_REN' => 0,
            'DISPOSISI_WAKA' => 0,
            'TERIMA_SIKEU' => 0,
            'DIBAYARKAN' => 0,
            'DITOLAK' => 0
        ];

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $normalizedStatus = normalizeStatusForBadge($row['status']);
            if (isset($stats[$normalizedStatus])) {
                $stats[$normalizedStatus] += (int)$row['count'];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => $stats,
            'total' => array_sum($stats),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        throw $e;
    }
}

/**
 * NORMALIZE STATUS untuk Admin Dashboard
 * TERKIRIM → TERIMA_BERKAS
 * All statuses use underscore format from database
 */
function normalizeStatusForBadge($status)
{
    // Convert to standard format with underscores
    $status = strtoupper(trim($status));
    $status = str_replace(' ', '_', $status);

    // Map any variations to standard values
    $standardStatuses = [
        'DRAFT' => 'DRAFT',
        'TERKIRIM' => 'TERIMA_BERKAS',  // TERKIRIM → TERIMA_BERKAS for display
        'TERIMA_BERKAS' => 'TERIMA_BERKAS',
        'DISPOSISI_KABAG_REN' => 'DISPOSISI_KABAG_REN',
        'DISPOSISI_WAKA' => 'DISPOSISI_WAKA',
        'TERIMA_SIKEU' => 'TERIMA_SIKEU',
        'DIBAYARKAN' => 'DIBAYARKAN',
        'DITOLAK' => 'DITOLAK'
    ];

    return $standardStatuses[$status] ?? $status;
}
