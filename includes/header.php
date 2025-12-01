<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Page Specific CSS -->
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="assets/css/<?php echo $page_css; ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo_bagren.png">
    
    <!-- Meta Description -->
    <meta name="description" content="<?php echo APP_FULL_NAME; ?> - Transparan, Akuntabel, dan Terintegrasi">
    <meta name="keywords" content="SIPANG, Polri, Anggaran, Kepolisian, Garut">
    <meta name="author" content="<?php echo APP_AUTHOR; ?>">
</head>
<body>

