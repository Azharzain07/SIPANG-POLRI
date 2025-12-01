<?php

/**
 * SIPANG POLRI - File Serving Endpoint
 * Handle file downloads and inline viewing
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once '../config/database_config.php';

// Check authentication
$auth = new Auth();
if (!$auth->isLoggedIn()) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit();
}

// Get file path from request
$pengajuan_id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? 'view'; // 'view' or 'download'

if (!$pengajuan_id) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Missing pengajuan ID']);
  exit();
}

try {
  $db = Database::getInstance()->getConnection();

  // Get pengajuan data and check access
  $stmt = $db->prepare("
        SELECT p.id, p.file_path, p.user_id, p.nomor_surat, po.nama as polsek_nama
        FROM pengajuan p
        LEFT JOIN polsek po ON p.polsek_id = po.id
        WHERE p.id = ?
    ");
  $stmt->execute([$pengajuan_id]);
  $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$pengajuan) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pengajuan not found']);
    exit();
  }

  // Check access permissions
  $user = $auth->getCurrentUser();
  $isOwner = $pengajuan['user_id'] == $user['id'];
  $isAdmin = in_array($user['role'], ['ADMIN_BAGREN', 'ADMIN_SIKEU']);

  if (!$isOwner && !$isAdmin) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
  }

  // Check if file exists
  if (!$pengajuan['file_path'] || !file_exists($pengajuan['file_path'])) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'File not found']);
    exit();
  }

  $file_path = $pengajuan['file_path'];
  $file_name = basename($file_path);
  $file_size = filesize($file_path);
  $file_type = mime_content_type($file_path);

  // Log download/view
  error_log("File access: User {$user['id']} (" . $user['nama_lengkap'] . ") {$action} file {$file_path} from pengajuan {$pengajuan_id}");

  // Set headers
  if ($action === 'download') {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
  } else {
    // For inline viewing
    header('Content-Type: ' . $file_type);
    header('Content-Disposition: inline; filename="' . $file_name . '"');
  }

  header('Content-Length: ' . $file_size);
  header('Cache-Control: private, max-age=0, must-revalidate');
  header('Pragma: public');

  // Send file
  readfile($file_path);
  exit();
} catch (Exception $e) {
  error_log("File serving error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
  exit();
}
