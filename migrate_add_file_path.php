<?php

/**
 * Add file_path column to pengajuan table
 * Run this once to update the database schema
 */

require_once 'config/database_config.php';

try {
  $db = Database::getInstance()->getConnection();

  echo "=== Adding file_path column to pengajuan table ===\n\n";

  // Check if column already exists
  $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
              WHERE TABLE_NAME='pengajuan' AND COLUMN_NAME='file_path'";
  $result = $db->query($query);
  $exists = $result->rowCount() > 0;

  if ($exists) {
    echo "✓ Column 'file_path' already exists\n";
  } else {
    // Add column
    $query = "ALTER TABLE pengajuan ADD COLUMN file_path VARCHAR(255) NULL AFTER status_keterangan";
    $db->exec($query);
    echo "✓ Column 'file_path' added successfully\n";
  }

  echo "\n✓ Database migration completed\n";
} catch (Exception $e) {
  echo "✗ Error: " . $e->getMessage() . "\n";
}
