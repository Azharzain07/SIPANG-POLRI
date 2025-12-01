<?php

/**
 * Simple Database Test Script
 * Check if database connection works and tables have correct structure
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database_config.php';

echo "=== DATABASE CONNECTION TEST ===\n\n";

try {
  $db = Database::getInstance()->getConnection();
  echo "✓ Database connection successful\n\n";

  // Check if pengajuan table exists and has correct columns
  echo "=== CHECKING PENGAJUAN TABLE ===\n";
  $result = $db->query("DESCRIBE pengajuan");
  $columns = [];
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $columns[$row['Field']] = $row['Type'];
    echo "  - {$row['Field']}: {$row['Type']}\n";
  }

  // Check for problematic columns
  echo "\n=== CHECKING FOR ISSUES ===\n";
  if (isset($columns['file_path'])) {
    echo "⚠ Column 'file_path' exists (may not be used in current schema)\n";
  } else {
    echo "✓ Column 'file_path' does not exist (OK)\n";
  }

  if (isset($columns['revisi_keterangan'])) {
    echo "⚠ Column 'revisi_keterangan' exists (not in default schema)\n";
  } else {
    echo "✓ Column 'revisi_keterangan' does not exist (uses status_keterangan)\n";
  }

  if (isset($columns['is_revisi'])) {
    echo "⚠ Column 'is_revisi' exists (not in default schema)\n";
  } else {
    echo "✓ Column 'is_revisi' does not exist (OK)\n";
  }

  // Check table pengajuan_status_log
  echo "\n=== CHECKING PENGAJUAN_STATUS_LOG TABLE ===\n";
  $result = $db->query("DESCRIBE pengajuan_status_log");
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['Field']}: {$row['Type']}\n";
  }

  // Check sample data
  echo "\n=== CHECKING DATA ===\n";
  $result = $db->query("SELECT COUNT(*) as total FROM pengajuan");
  $count = $result->fetch(PDO::FETCH_ASSOC)['total'];
  echo "Total pengajuan records: {$count}\n";

  $result = $db->query("SELECT COUNT(*) as total FROM users");
  $count = $result->fetch(PDO::FETCH_ASSOC)['total'];
  echo "Total users: {$count}\n";

  $result = $db->query("SELECT COUNT(*) as total FROM kegiatan");
  $count = $result->fetch(PDO::FETCH_ASSOC)['total'];
  echo "Total kegiatan: {$count}\n";

  echo "\n✓ All database checks completed\n";
} catch (Exception $e) {
  echo "✗ Error: " . $e->getMessage() . "\n";
}
