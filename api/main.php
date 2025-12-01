<?php

/**
 * SIPANG POLRI - Main API Handler
 * Handles requests for all user roles: USER_SATFUNG, USER_POLSEK, ADMIN_BAGREN, ADMIN_SIKEU
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database_config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$auth = new Auth();

// Check authentication for all requests except login and get_kegiatan
$publicActions = ['login', 'get_kegiatan', 'get_polsek'];
$action = $_GET['action'] ?? '';

if (!in_array($action, $publicActions)) {
    if (!$auth->isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Please login again.',
            'code' => 'SESSION_EXPIRED'
        ]);
        exit();
    }
}

$action = $_GET['action'] ?? '';
$pengajuanManager = new PengajuanManager();

try {
    switch ($action) {
        case 'login':
            handleLogin($auth);
            break;

        case 'logout':
            handleLogout($auth);
            break;

        case 'get_user_info':
            handleGetUserInfo($auth);
            break;

        case 'create_pengajuan':
            handleCreatePengajuan($pengajuanManager, $auth);
            break;

        case 'get_riwayat':
            handleGetRiwayat($pengajuanManager, $auth);
            break;

        case 'update_status':
            handleUpdateStatus($pengajuanManager, $auth);
            break;

        case 'update_status_group':
            handleUpdateStatusGroup($pengajuanManager, $auth);
            break;

        case 'approve_pengajuan_bagren':
            handleApprovePengajuanBagren($pengajuanManager, $auth);
            break;

        case 'approve_pengajuan_sikeu':
            handleApprovePengajuanSikeu($pengajuanManager, $auth);
            break;

        case 'submit_drafts':
            handleSubmitDrafts($pengajuanManager, $auth);
            break;

        case 'reject_pengajuan':
            handleRejectPengajuan($pengajuanManager, $auth);
            break;

        case 'revise_pengajuan':
            handleRevisePengajuan($pengajuanManager, $auth);
            break;

        case 'download_pdf':
            handleDownloadPDF($pengajuanManager, $auth);
            break;

        case 'download_excel':
            handleDownloadExcel($pengajuanManager, $auth);
            break;

        case 'download_all_pdf':
            handleDownloadAllPDF($pengajuanManager, $auth);
            break;

        case 'download_all_excel':
            handleDownloadAllExcel($pengajuanManager, $auth);
            break;

        case 'delete_pengajuan':
            handleDeletePengajuan($pengajuanManager, $auth);
            break;

        case 'check_session':
            handleCheckSession($auth);
            break;

        case 'get_kegiatan':
            handleGetKegiatan();
            break;

        case 'get_polsek':
            handleGetPolsek();
            break;

        case 'get_dashboard_data':
            handleGetDashboardData($pengajuanManager, $auth);
            break;

        case 'export_data':
            handleExportData($pengajuanManager, $auth);
            break;

        case 'get_status_log':
            handleGetStatusLog($pengajuanManager, $auth);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function handleLogin($auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Username and password required']);
        return;
    }

    if ($auth->login($data['username'], $data['password'])) {
        $user = $auth->getCurrentUser();
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
}

function handleLogout($auth)
{
    $auth->logout();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function handleGetUserInfo($auth)
{
    $user = $auth->getCurrentUser();
    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => $user,
            'permissions' => ROLE_PERMISSIONS[$user['role']] ?? []
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
}

function handleCreatePengajuan($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_create_pengajuan')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    // Handle file upload
    $file_path = null;
    if (isset($_FILES['dokumen_pendukung']) && $_FILES['dokumen_pendukung']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';

        // Get original filename and sanitize it
        $original_name = $_FILES['dokumen_pendukung']['name'];
        $file_extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        // Generate safe filename (no spaces, special chars)
        $safe_filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($original_name, PATHINFO_FILENAME));
        $file_name = uniqid() . '_' . time() . '_' . $safe_filename . '.' . $file_extension;

        // Ensure upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_path = $upload_dir . $file_name;

        // Debug: Log the file path being used
        error_log("Attempting to move file to: " . $file_path);

        if (!move_uploaded_file($_FILES['dokumen_pendukung']['tmp_name'], $file_path)) {
            error_log("Failed to move uploaded file. Source: " . $_FILES['dokumen_pendukung']['tmp_name'] . " Target: " . $file_path);
            echo json_encode(['success' => false, 'message' => 'Failed to upload file. Please check file permissions.']);
            return;
        }
    }

    $data = json_decode($_POST['data'], true);

    // Allow creating with specific initial status when client requests it (e.g., submit immediately)
    $initial_status = isset($_POST['initial_status']) ? trim($_POST['initial_status']) : STATUS_DRAFT;
    // Validate initial_status - fall back to STATUS_DRAFT if unknown
    $allowedStatuses = [STATUS_DRAFT, STATUS_TERKIRIM, STATUS_TERIMA_BERKAS, STATUS_DISPOSISI_KABAG_REN, STATUS_DISPOSISI_WAKA, STATUS_TERIMA_SIKEU, STATUS_DIBAYARKAN, STATUS_DITOLAK];
    if (!in_array($initial_status, $allowedStatuses)) {
        $initial_status = STATUS_DRAFT;
    }

    // Validate required fields
    $required = [
        'tanggal_pengajuan',
        'bulan_pengajuan',
        'tahun_pengajuan',
        'sumber_dana',
        'uraian',
        'penanggung_jawab',
        'bendahara_pengeluaran_pembantu',
        'kegiatan_id',
        'jumlah_diajukan',
        'jumlah_pagu',
        'sisa_pagu'
    ];

    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Field {$field} is required"]);
            return;
        }
    }

    // Set session data for database insertion
    $user = $auth->getCurrentUser();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['polsek_id'] = $user['polsek_id'];

    // Create pengajuan with provided initial status
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO pengajuan (
                nomor_surat, tanggal_pengajuan, bulan_pengajuan, tahun_pengajuan,
                sumber_dana, uraian, penanggung_jawab, bendahara_pengeluaran_pembantu,
                kegiatan_id, jumlah_diajukan, jumlah_pagu, sisa_pagu,
                user_id, polsek_id, status, file_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        // Generate a safe nomor_surat
        $nomor_surat = 'BU-' . date('YmdHis') . '-' . mt_rand(100, 999);

        $executed = $stmt->execute([
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
            $initial_status,
            $file_path
        ]);

        if ($executed) {
            $insertedId = $db->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Pengajuan created successfully', 'id' => $insertedId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create pengajuan']);
        }
    } catch (Exception $e) {
        error_log('[create_pengajuan] exception: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to create pengajuan: ' . $e->getMessage()]);
    }
}
function handleGetRiwayat($pengajuanManager, $auth)
{
    $user = $auth->getCurrentUser();
    $polsek_id = $_GET['polsek_id'] ?? null;

    $riwayat = $pengajuanManager->getRiwayat($user['role'], $polsek_id);

    // Format data for frontend
    $formatted_data = array_map(function ($item) {
        return [
            'id' => $item['id'],
            'user_id' => $item['user_id'],
            'nama_lengkap' => $item['nama_lengkap'],
            'role' => $item['role'],
            'tanggal' => $item['tanggal_pengajuan'],
            'bulan' => $item['bulan_pengajuan'],
            'kode' => $item['kode_kegiatan'],
            'uraian' => $item['uraian'],
            'jumlah_diajukan' => $item['jumlah_diajukan'],
            'status' => getStatusLabel($item['status']),
            'status_class' => getStatusClass($item['status']),
            'nomor_surat' => $item['nomor_surat'],
            'nama_polsek' => $item['nama_polsek'],
            'nama_kegiatan' => $item['nama_kegiatan'],
            'penanggung_jawab' => $item['penanggung_jawab'],
            'bendahara_pengeluaran_pembantu' => $item['bendahara_pengeluaran_pembantu'],
            'created_at' => $item['created_at'],
            'file_path' => $item['file_path'] ?? null
        ];
    }, $riwayat);

    echo json_encode([
        'success' => true,
        'data' => $formatted_data
    ]);
}

function handleUpdateStatus($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_change_status')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_id']) || empty($data['status'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID and status required']);
        return;
    }

    $keterangan = $data['keterangan'] ?? '';

    if ($pengajuanManager->updateStatus($data['pengajuan_id'], $data['status'], $keterangan)) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
}

function handleRejectPengajuan($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_change_status')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_id']) || empty($data['keterangan'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID and keterangan required']);
        return;
    }

    if ($pengajuanManager->rejectPengajuan($data['pengajuan_id'], $data['keterangan'])) {
        echo json_encode(['success' => true, 'message' => 'Pengajuan rejected and sent for revision']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject pengajuan']);
    }
}

function handleApprovePengajuanBagren($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    // Only ADMIN_BAGREN can use this endpoint
    $user = $auth->getCurrentUser();
    if ($user['role'] !== 'ADMIN_BAGREN') {
        echo json_encode(['success' => false, 'message' => 'Permission denied - Only ADMIN_BAGREN can approve']);
        return;
    }

    if (!$auth->hasPermission('can_change_status')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_ids']) || !is_array($data['pengajuan_ids'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan IDs array required']);
        return;
    }

    $successCount = 0;
    $totalCount = count($data['pengajuan_ids']);

    foreach ($data['pengajuan_ids'] as $pengajuan_id) {
        // ADMIN_BAGREN: approve TERIMA_BERKAS → TERIMA_SIKEU
        if ($pengajuanManager->updateStatus($pengajuan_id, 'TERIMA_SIKEU', 'Disetujui oleh Admin BAGREN', $_SESSION['user_id'])) {
            $successCount++;
        }
    }

    if ($successCount === $totalCount) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully approved {$successCount} pengajuan",
            'status' => 'TERIMA_SIKEU'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "Approved {$successCount} out of {$totalCount} pengajuan"]);
    }
}

function handleApprovePengajuanSikeu($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    // Only ADMIN_SIKEU can use this endpoint
    $user = $auth->getCurrentUser();
    if ($user['role'] !== 'ADMIN_SIKEU') {
        echo json_encode(['success' => false, 'message' => 'Permission denied - Only ADMIN_SIKEU can process payment']);
        return;
    }

    if (!$auth->hasPermission('can_change_status')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_ids']) || !is_array($data['pengajuan_ids'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan IDs array required']);
        return;
    }

    $successCount = 0;
    $totalCount = count($data['pengajuan_ids']);

    foreach ($data['pengajuan_ids'] as $pengajuan_id) {
        // ADMIN_SIKEU: approve TERIMA_SIKEU → DIBAYARKAN
        if ($pengajuanManager->updateStatus($pengajuan_id, 'DIBAYARKAN', 'Disetujui untuk diproses pembayaran oleh Bendahara', $_SESSION['user_id'])) {
            $successCount++;
        }
    }

    if ($successCount === $totalCount) {
        echo json_encode([
            'success' => true,
            'message' => "Successfully processed {$successCount} pengajuan for payment",
            'status' => 'DIBAYARKAN'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => "Processed {$successCount} out of {$totalCount} pengajuan"]);
    }
}


function handleUpdateStatusGroup($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_change_status')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_ids']) || !is_array($data['pengajuan_ids']) || empty($data['status'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan IDs array and status required']);
        return;
    }

    $successCount = 0;
    $totalCount = count($data['pengajuan_ids']);

    foreach ($data['pengajuan_ids'] as $pengajuan_id) {
        if ($pengajuanManager->updateStatus($pengajuan_id, $data['status'], $data['keterangan'] ?? '', $_SESSION['user_id'])) {
            $successCount++;
        }
    }

    if ($successCount === $totalCount) {
        echo json_encode(['success' => true, 'message' => "Successfully updated {$successCount} pengajuan"]);
    } else {
        echo json_encode(['success' => false, 'message' => "Updated {$successCount} out of {$totalCount} pengajuan"]);
    }
}

function handleSubmitDrafts($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_submit_pengajuan')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    // Server-side logging for debugging submit_drafts
    error_log('[submit_drafts] called by user_id=' . ($_SESSION['user_id'] ?? 'unknown') . ' with payload: ' . json_encode($data));

    if (empty($data['pengajuan_ids']) || !is_array($data['pengajuan_ids'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan IDs array required']);
        return;
    }

    $successCount = 0;
    $totalCount = count($data['pengajuan_ids']);
    $db = Database::getInstance()->getConnection();
    $updatedIds = [];
    $failedIds = [];

    foreach ($data['pengajuan_ids'] as $pengajuan_id) {
        // ensure the pengajuan belongs to current user
        $stmt = $db->prepare("SELECT user_id, status FROM pengajuan WHERE id = ?");
        $stmt->execute([$pengajuan_id]);
        $row = $stmt->fetch();
        if (!$row) continue;
        if ($row['user_id'] != $_SESSION['user_id']) continue; // skip others'

        // only submit if current status seems like draft
        // Accept various draft labels (case-insensitive)
        $currentStatus = strtoupper(trim($row['status'] ?? ''));
        if ($currentStatus !== 'DRAFT') {
            // still attempt update, but prefer skipping non-draft entries
            // continue;
        }

        $updated = $pengajuanManager->updateStatus($pengajuan_id, STATUS_TERKIRIM, '', $_SESSION['user_id']);
        if ($updated) {
            $successCount++;
            $updatedIds[] = $pengajuan_id;
        } else {
            $failedIds[] = $pengajuan_id;
            error_log('[submit_drafts] failed update for pengajuan_id=' . $pengajuan_id . ' by user_id=' . ($_SESSION['user_id'] ?? 'unknown'));
        }
    }
    // Build response payload with detailed results
    $response = [
        'updated' => $updatedIds,
        'failed' => $failedIds,
        'requested' => $data['pengajuan_ids']
    ];

    if (count($updatedIds) === $totalCount) {
        $response['success'] = true;
        $response['message'] = "Successfully submitted {$successCount} pengajuan";
    } else if (count($updatedIds) > 0) {
        error_log('[submit_drafts] partial result: ' . count($updatedIds) . '/' . $totalCount . ' submitted by user_id=' . ($_SESSION['user_id'] ?? 'unknown'));
        $response['success'] = true;
        $response['message'] = "Submitted " . count($updatedIds) . " out of {$totalCount} pengajuan";
    } else {
        error_log('[submit_drafts] no pengajuan submitted for user_id=' . ($_SESSION['user_id'] ?? 'unknown'));
        $response['success'] = false;
        $response['message'] = "No pengajuan submitted";
    }

    echo json_encode($response);
}


function handleDeletePengajuan($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    // Only ADMIN_BAGREN can delete pengajuan (demo feature)
    $user = $auth->getCurrentUser();
    if ($user['role'] !== 'ADMIN_BAGREN') {
        echo json_encode(['success' => false, 'message' => 'Permission denied - Only ADMIN_BAGREN can delete']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_id'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID required']);
        return;
    }

    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Delete pengajuan and related data
        $stmt = $pdo->prepare('DELETE FROM pengajuan WHERE id = ?');
        if ($stmt->execute([$data['pengajuan_id']])) {
            // Also delete from pengajuan_detail
            $stmt = $pdo->prepare('DELETE FROM pengajuan_detail WHERE pengajuan_id = ?');
            $stmt->execute([$data['pengajuan_id']]);

            // Also delete from pengajuan_status_log
            $stmt = $pdo->prepare('DELETE FROM pengajuan_status_log WHERE pengajuan_id = ?');
            $stmt->execute([$data['pengajuan_id']]);

            echo json_encode(['success' => true, 'message' => 'Pengajuan deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete pengajuan']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function handleRevisePengajuan($pengajuanManager, $auth)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    if (!$auth->hasPermission('can_create_pengajuan')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['pengajuan_id']) || empty($data['jumlah_baru'])) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID and jumlah baru required']);
        return;
    }

    $pengajuan_id = $data['pengajuan_id'];
    $jumlah_baru = $data['jumlah_baru'];
    $keterangan_revisi = $data['keterangan_revisi'] ?? '';

    // Validate jumlah
    if (!is_numeric($jumlah_baru) || $jumlah_baru <= 0) {
        echo json_encode(['success' => false, 'message' => 'Jumlah baru harus berupa angka positif']);
        return;
    }

    // Revise pengajuan
    if ($pengajuanManager->revisePengajuan($pengajuan_id, $jumlah_baru, $keterangan_revisi)) {
        echo json_encode(['success' => true, 'message' => 'Pengajuan berhasil direvisi dan dikirim ulang']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal merevisi pengajuan']);
    }
}

function handleGetStatusLog($pengajuanManager, $auth)
{
    $pengajuan_id = $_GET['pengajuan_id'] ?? null;

    if (!$pengajuan_id) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID required']);
        return;
    }

    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("
        SELECT psl.*, u.nama_lengkap, u.role
        FROM pengajuan_status_log psl
        LEFT JOIN users u ON psl.user_id = u.id
        WHERE psl.pengajuan_id = ?
        ORDER BY psl.created_at ASC
    ");
    $stmt->execute([$pengajuan_id]);
    $logs = $stmt->fetchAll();

    $formatted_logs = array_map(function ($log) {
        return [
            'status_lama' => getStatusLabel($log['status_lama']),
            'status_baru' => getStatusLabel($log['status_baru']),
            'keterangan' => $log['keterangan'],
            'user' => $log['nama_lengkap'],
            'role' => $log['role'],
            'tanggal' => $log['created_at']
        ];
    }, $logs);

    echo json_encode([
        'success' => true,
        'data' => $formatted_logs
    ]);
}

function handleCheckSession($auth)
{
    if ($auth->isLoggedIn()) {
        $user = $auth->getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'nama_lengkap' => $user['nama_lengkap'],
                'role' => $user['role']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Session expired. Please login again.',
            'code' => 'SESSION_EXPIRED'
        ]);
    }
}

function handleGetKegiatan()
{
    $db = Database::getInstance()->getConnection();
    $sumber_dana = $_GET['sumber_dana'] ?? null;

    $sql = "SELECT * FROM kegiatan";
    $params = [];

    if ($sumber_dana) {
        $sql .= " WHERE sumber_dana = ?";
        $params[] = $sumber_dana;
    }

    $sql .= " ORDER BY nama";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $kegiatan = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $kegiatan
    ]);
}

function handleGetPolsek()
{
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM polsek ORDER BY nama");
    $stmt->execute();
    $polsek = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $polsek
    ]);
}

function handleGetDashboardData($pengajuanManager, $auth)
{
    $user = $auth->getCurrentUser();
    $db = Database::getInstance()->getConnection();

    $dashboard_data = [];

    // Get statistics based on user role
    if ($user['role'] === ROLE_ADMIN_BAGREN || $user['role'] === ROLE_ADMIN_SIKEU) {
        // Admin can see all data
        $sql = "SELECT status, COUNT(*) as count FROM pengajuan GROUP BY status";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $status_counts = $stmt->fetchAll();

        $dashboard_data['status_counts'] = $status_counts;
        $dashboard_data['total_pengajuan'] = array_sum(array_column($status_counts, 'count'));
    } else {
        // Regular users see only their data
        $sql = "SELECT status, COUNT(*) as count FROM pengajuan WHERE user_id = ? GROUP BY status";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['id']]);
        $status_counts = $stmt->fetchAll();

        $dashboard_data['status_counts'] = $status_counts;
        $dashboard_data['total_pengajuan'] = array_sum(array_column($status_counts, 'count'));
    }

    echo json_encode([
        'success' => true,
        'data' => $dashboard_data
    ]);
}

function handleExportData($pengajuanManager, $auth)
{
    if (!$auth->hasPermission('can_export_data')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $format = $_GET['format'] ?? 'excel';
    $user = $auth->getCurrentUser();

    $riwayat = $pengajuanManager->getRiwayat($user['role']);

    if ($format === 'excel') {
        // Generate Excel file
        $filename = 'riwayat_pengajuan_' . date('Y-m-d') . '.xlsx';

        // For now, return data for frontend to handle Excel generation
        echo json_encode([
            'success' => true,
            'data' => $riwayat,
            'filename' => $filename
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unsupported format']);
    }
}

// Handle PDF download for SIKEU
function handleDownloadPDF($pengajuanManager, $auth)
{
    if (!$auth->hasPermission('can_download_reports')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $pengajuan_id = $_GET['pengajuan_id'] ?? null;

    if (!$pengajuan_id) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID required']);
        return;
    }

    // Get pengajuan data
    $riwayat = $pengajuanManager->getRiwayat($auth->getCurrentUser()['role']);
    $pengajuan = null;

    foreach ($riwayat as $item) {
        if ($item['id'] == $pengajuan_id) {
            $pengajuan = $item;
            break;
        }
    }

    if (!$pengajuan) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan not found']);
        return;
    }

    // Generate PDF content
    $pdf_content = generatePDFContent($pengajuan);

    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Pengajuan_' . $pengajuan_id . '.pdf"');
    header('Content-Length: ' . strlen($pdf_content));

    echo $pdf_content;
}

// Handle Excel download for SIKEU
function handleDownloadExcel($pengajuanManager, $auth)
{
    if (!$auth->hasPermission('can_export_excel')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    $pengajuan_id = $_GET['pengajuan_id'] ?? null;

    if (!$pengajuan_id) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan ID required']);
        return;
    }

    // Get pengajuan data
    $riwayat = $pengajuanManager->getRiwayat($auth->getCurrentUser()['role']);
    $pengajuan = null;

    foreach ($riwayat as $item) {
        if ($item['id'] == $pengajuan_id) {
            $pengajuan = $item;
            break;
        }
    }

    if (!$pengajuan) {
        echo json_encode(['success' => false, 'message' => 'Pengajuan not found']);
        return;
    }

    // Generate Excel content
    $excel_content = generateExcelContent($pengajuan);

    // Set headers for Excel download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Pengajuan_' . $pengajuan_id . '.xlsx"');
    header('Content-Length: ' . strlen($excel_content));

    echo $excel_content;
}

// Generate PDF content
function generatePDFContent($pengajuan)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Pengajuan Anggaran - ' . $pengajuan['nomor_surat'] . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .title { font-size: 18px; font-weight: bold; }
            .subtitle { font-size: 14px; color: #666; }
            .content { margin: 20px 0; }
            .field { margin: 10px 0; }
            .label { font-weight: bold; display: inline-block; width: 200px; }
            .value { display: inline-block; }
            .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">PENGAJUAN ANGGARAN POLRI</div>
            <div class="subtitle">Nomor: ' . $pengajuan['nomor_surat'] . '</div>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="label">Tanggal Pengajuan:</span>
                <span class="value">' . $pengajuan['tanggal'] . '</span>
            </div>
            <div class="field">
                <span class="label">Bulan Pengajuan:</span>
                <span class="value">' . $pengajuan['bulan'] . '</span>
            </div>
            <div class="field">
                <span class="label">Program/Kegiatan:</span>
                <span class="value">' . $pengajuan['nama_kegiatan'] . '</span>
            </div>
            <div class="field">
                <span class="label">Kode:</span>
                <span class="value">' . $pengajuan['kode'] . '</span>
            </div>
            <div class="field">
                <span class="label">Uraian:</span>
                <span class="value">' . $pengajuan['uraian'] . '</span>
            </div>
            <div class="field">
                <span class="label">Jumlah Diajukan:</span>
                <span class="value">Rp ' . number_format($pengajuan['jumlah_diajukan'], 0, ',', '.') . '</span>
            </div>
            <div class="field">
                <span class="label">Penanggung Jawab:</span>
                <span class="value">' . $pengajuan['penanggung_jawab'] . '</span>
            </div>
            <div class="field">
                <span class="label">Bendahara Pengeluaran:</span>
                <span class="value">' . $pengajuan['bendahara_pengeluaran_pembantu'] . '</span>
            </div>
            <div class="field">
                <span class="label">Status:</span>
                <span class="value">' . $pengajuan['status'] . '</span>
            </div>
            <div class="field">
                <span class="label">Pengguna:</span>
                <span class="value">' . $pengajuan['nama_lengkap'] . ' (' . $pengajuan['role'] . ')</span>
            </div>
        </div>
        
        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh sistem SIPANG POLRI</p>
            <p>Tanggal cetak: ' . date('d/m/Y H:i:s') . '</p>
        </div>
    </body>
    </html>';

    // For now, return HTML content (in real implementation, use a PDF library like TCPDF or mPDF)
    return $html;
}

// Generate Excel content
function generateExcelContent($pengajuan)
{
    // Simple CSV format for Excel compatibility
    $csv_content = "Nomor Surat,Tanggal,Bulan,Program/Kegiatan,Kode,Uraian,Jumlah Diajukan,Penanggung Jawab,Bendahara Pengeluaran,Status,Pengguna\n";
    $csv_content .= '"' . $pengajuan['nomor_surat'] . '",';
    $csv_content .= '"' . $pengajuan['tanggal'] . '",';
    $csv_content .= '"' . $pengajuan['bulan'] . '",';
    $csv_content .= '"' . $pengajuan['nama_kegiatan'] . '",';
    $csv_content .= '"' . $pengajuan['kode'] . '",';
    $csv_content .= '"' . $pengajuan['uraian'] . '",';
    $csv_content .= '"' . number_format($pengajuan['jumlah_diajukan'], 0, ',', '.') . '",';
    $csv_content .= '"' . $pengajuan['penanggung_jawab'] . '",';
    $csv_content .= '"' . $pengajuan['bendahara_pengeluaran_pembantu'] . '",';
    $csv_content .= '"' . $pengajuan['status'] . '",';
    $csv_content .= '"' . $pengajuan['nama_lengkap'] . ' (' . $pengajuan['role'] . ')"';

    return $csv_content;
}

// Handle bulk PDF download for SIKEU
function handleDownloadAllPDF($pengajuanManager, $auth)
{
    if (!$auth->hasPermission('can_download_reports')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    // Get all approved pengajuan data
    $riwayat = $pengajuanManager->getRiwayat($auth->getCurrentUser()['role']);

    // Filter only approved pengajuan
    $approvedPengajuan = array_filter($riwayat, function ($item) {
        return in_array($item['status'], ['TERIMA SIKEU', 'DIBAYARKAN']);
    });

    if (empty($approvedPengajuan)) {
        echo json_encode(['success' => false, 'message' => 'No approved pengajuan found']);
        return;
    }

    // Generate bulk PDF content
    $pdf_content = generateBulkPDFContent($approvedPengajuan);

    // Set headers for PDF download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Semua_Pengajuan_' . date('Y-m-d') . '.pdf"');
    header('Content-Length: ' . strlen($pdf_content));

    echo $pdf_content;
}

// Handle bulk Excel download for SIKEU
function handleDownloadAllExcel($pengajuanManager, $auth)
{
    if (!$auth->hasPermission('can_export_excel')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        return;
    }

    // Get all approved pengajuan data
    $riwayat = $pengajuanManager->getRiwayat($auth->getCurrentUser()['role']);

    // Filter only approved pengajuan
    $approvedPengajuan = array_filter($riwayat, function ($item) {
        return in_array($item['status'], ['TERIMA SIKEU', 'DIBAYARKAN']);
    });

    if (empty($approvedPengajuan)) {
        echo json_encode(['success' => false, 'message' => 'No approved pengajuan found']);
        return;
    }

    // Generate bulk Excel content
    $excel_content = generateBulkExcelContent($approvedPengajuan);

    // Set headers for Excel download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Semua_Pengajuan_' . date('Y-m-d') . '.xlsx"');
    header('Content-Length: ' . strlen($excel_content));

    echo $excel_content;
}

// Generate bulk PDF content
function generateBulkPDFContent($pengajuanList)
{
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Semua Pengajuan Anggaran - ' . date('d/m/Y') . '</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; }
            .title { font-size: 20px; font-weight: bold; }
            .subtitle { font-size: 14px; color: #666; }
            .summary { margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 5px; }
            .pengajuan-item { margin: 30px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; page-break-inside: avoid; }
            .pengajuan-header { font-weight: bold; font-size: 16px; margin-bottom: 15px; color: #1a5490; }
            .field { margin: 8px 0; }
            .label { font-weight: bold; display: inline-block; width: 200px; }
            .value { display: inline-block; }
            .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">SEMUA PENGAJUAN ANGGARAN POLRI</div>
            <div class="subtitle">Tanggal: ' . date('d/m/Y H:i:s') . '</div>
        </div>
        
        <div class="summary">
            <h3>Ringkasan</h3>
            <p><strong>Total Pengajuan:</strong> ' . count($pengajuanList) . ' pengajuan</p>
            <p><strong>Total Jumlah:</strong> Rp ' . number_format(array_sum(array_column($pengajuanList, 'jumlah_diajukan')), 0, ',', '.') . '</p>
        </div>';

    foreach ($pengajuanList as $index => $pengajuan) {
        $html .= '
        <div class="pengajuan-item">
            <div class="pengajuan-header">Pengajuan #' . ($index + 1) . ' - ' . $pengajuan['nomor_surat'] . '</div>
            
            <div class="field">
                <span class="label">Tanggal Pengajuan:</span>
                <span class="value">' . $pengajuan['tanggal'] . '</span>
            </div>
            <div class="field">
                <span class="label">Bulan Pengajuan:</span>
                <span class="value">' . $pengajuan['bulan'] . '</span>
            </div>
            <div class="field">
                <span class="label">Program/Kegiatan:</span>
                <span class="value">' . $pengajuan['nama_kegiatan'] . '</span>
            </div>
            <div class="field">
                <span class="label">Kode:</span>
                <span class="value">' . $pengajuan['kode'] . '</span>
            </div>
            <div class="field">
                <span class="label">Uraian:</span>
                <span class="value">' . $pengajuan['uraian'] . '</span>
            </div>
            <div class="field">
                <span class="label">Jumlah Diajukan:</span>
                <span class="value">Rp ' . number_format($pengajuan['jumlah_diajukan'], 0, ',', '.') . '</span>
            </div>
            <div class="field">
                <span class="label">Penanggung Jawab:</span>
                <span class="value">' . $pengajuan['penanggung_jawab'] . '</span>
            </div>
            <div class="field">
                <span class="label">Bendahara Pengeluaran:</span>
                <span class="value">' . $pengajuan['bendahara_pengeluaran_pembantu'] . '</span>
            </div>
            <div class="field">
                <span class="label">Status:</span>
                <span class="value">' . $pengajuan['status'] . '</span>
            </div>
            <div class="field">
                <span class="label">Pengguna:</span>
                <span class="value">' . $pengajuan['nama_lengkap'] . ' (' . $pengajuan['role'] . ')</span>
            </div>
        </div>';
    }

    $html .= '
        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh sistem SIPANG POLRI</p>
            <p>Tanggal cetak: ' . date('d/m/Y H:i:s') . '</p>
        </div>
    </body>
    </html>';

    return $html;
}

// Generate bulk Excel content
function generateBulkExcelContent($pengajuanList)
{
    // CSV header
    $csv_content = "No,Nomor Surat,Tanggal,Bulan,Program/Kegiatan,Kode,Uraian,Jumlah Diajukan,Penanggung Jawab,Bendahara Pengeluaran,Status,Pengguna\n";

    // Add each pengajuan as a row
    foreach ($pengajuanList as $index => $pengajuan) {
        $csv_content .= ($index + 1) . ',';
        $csv_content .= '"' . $pengajuan['nomor_surat'] . '",';
        $csv_content .= '"' . $pengajuan['tanggal'] . '",';
        $csv_content .= '"' . $pengajuan['bulan'] . '",';
        $csv_content .= '"' . $pengajuan['nama_kegiatan'] . '",';
        $csv_content .= '"' . $pengajuan['kode'] . '",';
        $csv_content .= '"' . $pengajuan['uraian'] . '",';
        $csv_content .= '"' . number_format($pengajuan['jumlah_diajukan'], 0, ',', '.') . '",';
        $csv_content .= '"' . $pengajuan['penanggung_jawab'] . '",';
        $csv_content .= '"' . $pengajuan['bendahara_pengeluaran_pembantu'] . '",';
        $csv_content .= '"' . $pengajuan['status'] . '",';
        $csv_content .= '"' . $pengajuan['nama_lengkap'] . ' (' . $pengajuan['role'] . ')"';
        $csv_content .= "\n";
    }

    // Add summary row
    $totalAmount = array_sum(array_column($pengajuanList, 'jumlah_diajukan'));
    $csv_content .= "\n";
    $csv_content .= '"","","","","","","","","","","",""';
    $csv_content .= "\n";
    $csv_content .= '"TOTAL","","","","","","","' . number_format($totalAmount, 0, ',', '.') . '","","",""';

    return $csv_content;
}
