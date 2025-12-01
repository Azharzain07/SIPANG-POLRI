<?php
// Detail pengajuan endpoint
// Returns JSON. Defensive: use absolute include paths and return JSON errors instead of HTML.
require_once __DIR__ . '/../config/database_config.php';
require_once __DIR__ . '/../includes/auth_guard.php';

// Show errors locally for debugging and return JSON
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID pengajuan tidak ditemukan']);
    exit;
}

$id = $_GET['id'];
$currentUser = getCurrentUser();

if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $conn = Database::getInstance()->getConnection();

    // Note: database schema uses ENUM status in pengajuan and pengajuan_status_log for history.
    // There is no separate `status` table in this schema. Use pengajuan.status and map labels via STATUS_LABELS.
    $query = "SELECT 
        p.*,
        u.nama_lengkap as nama_pengaju,
        u.nip as nip_pengaju,
        ps.nama as nama_polsek,
        ps.kode as kode_polsek,
        k.nama as nama_kegiatan,
        k.kode as kode_kegiatan
    FROM pengajuan p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN polsek ps ON p.polsek_id = ps.id
    LEFT JOIN kegiatan k ON p.kegiatan_id = k.id
    WHERE p.id = :id";

    $isAdmin = in_array($currentUser['role'] ?? '', [ROLE_ADMIN_BAGREN, ROLE_ADMIN_SIKEU]);
    if (!$isAdmin) {
        $query .= " AND p.user_id = :user_id";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id);
    if (!$isAdmin) {
        $stmt->bindParam(':user_id', $currentUser['id']);
    }
    $stmt->execute();
    $detail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$detail) {
        http_response_code(404);
        echo json_encode(['error' => 'Data pengajuan tidak ditemukan']);
        exit;
    }

    // Use pengajuan_status_log for history (status_baru stores the enum value)
    $queryStatus = "SELECT 
        psl.id,
        psl.pengajuan_id,
        psl.status_lama,
        psl.status_baru,
        psl.keterangan,
        psl.user_id as updated_by,
        u.nama_lengkap as updated_by_name,
        psl.created_at
    FROM pengajuan_status_log psl
    LEFT JOIN users u ON psl.user_id = u.id
    WHERE psl.pengajuan_id = :pengajuan_id
    ORDER BY psl.created_at DESC";

    $stmtStatus = $conn->prepare($queryStatus);
    $stmtStatus->bindParam(':pengajuan_id', $id);
    $stmtStatus->execute();
    $statusRiwayat = $stmtStatus->fetchAll(PDO::FETCH_ASSOC);


    // Normalize some fields for client convenience
    $detail['tanggal'] = $detail['tanggal_pengajuan'] ?? $detail['created_at'] ?? null;
    $detail['bulan_pengajuan'] = $detail['bulan_pengajuan'] ?? null;
    $detail['nomor_surat'] = $detail['nomor_surat'] ?? $detail['nomor'] ?? null;
    $detail['jumlah_diajukan'] = $detail['jumlah_diajukan'] ?? $detail['jumlah'] ?? null;
    $detail['sumber_dana'] = $detail['sumber_dana'] ?? null;
    $detail['penanggung_jawab'] = $detail['penanggung_jawab'] ?? null;
    $detail['bendahara_pengeluaran_pembantu'] = $detail['bendahara_pengeluaran_pembantu'] ?? null;
    $detail['nama_kegiatan'] = $detail['nama_kegiatan'] ?? null;
    $detail['kode_kegiatan'] = $detail['kode_kegiatan'] ?? null;

    // Map main pengajuan status to human label and code
    $detail['status_kode'] = strtolower($detail['status'] ?? '');
    $detail['status_nama'] = STATUS_LABELS[$detail['status'] ?? ''] ?? ($detail['status'] ?? '');

    // Map history statuses to include readable names/codes
    foreach ($statusRiwayat as &$row) {
        $kode = $row['status_baru'] ?? '';
        $row['status_kode'] = strtolower($kode);
        $row['status_nama'] = STATUS_LABELS[$kode] ?? $kode;
    }
    unset($row);

    $detail['status_riwayat'] = $statusRiwayat;

    // Include any top-level status_keterangan (e.g., rejection reason stored on pengajuan)
    $detail['status_keterangan'] = $detail['status_keterangan'] ?? $detail['revisi_keterangan'] ?? null;

    // Also provide the latest rejection reason from history if present
    $detail['last_rejection_reason'] = null;
    foreach ($statusRiwayat as $r) {
        if (isset($r['status_baru']) && strtoupper($r['status_baru']) === 'DITOLAK' && !empty($r['keterangan'])) {
            $detail['last_rejection_reason'] = $r['keterangan'];
            break;
        }
    }

    echo json_encode($detail);

} catch (Throwable $e) {
    // Catch any error and return JSON (helps debug 500)
    http_response_code(500);
    $msg = $e->getMessage();
    // Provide trace in response only for local development
    echo json_encode(['error' => 'Unhandled error: ' . $msg, 'trace' => $e->getTraceAsString()]);
}

?>