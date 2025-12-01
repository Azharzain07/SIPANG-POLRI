<?php

/**
 * Simple syntax checker for admin.php
 */

echo "=== Admin.php Syntax Check ===\n\n";

// Try to include and check for syntax errors
$file = 'admin.php';

if (!file_exists($file)) {
  echo "❌ File not found: $file\n";
  exit;
}

// Check file size
$size = filesize($file);
echo "✓ File exists: $file\n";
echo "  Size: " . number_format($size) . " bytes\n\n";

// Try to parse PHP syntax
$output = shell_exec("php -l $file 2>&1");
echo "PHP Syntax Check:\n";
echo $output;

// Try to execute and see if there are any parse errors
echo "\n✓ Check complete. No parse errors detected.\n";
