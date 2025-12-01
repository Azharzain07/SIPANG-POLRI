<?php
/**
 * SIPANG POLRI - 403 Forbidden
 * Halaman untuk akses yang ditolak
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database_config.php';

$auth = new Auth();
$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied - SIPANG POLRI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            max-width: 600px;
            margin: 20px;
        }
        
        .icon {
            font-size: 6rem;
            margin-bottom: 30px;
            color: #dc3545;
        }
        
        h1 {
            color: #dc3545;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        p {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 1.1rem;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        
        .user-info h3 {
            color: #dc3545;
            margin-bottom: 10px;
        }
        
        .user-info p {
            margin: 5px 0;
            color: #333;
        }
        
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.3s ease;
            margin: 10px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        }
        
        .actions {
            margin-top: 30px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚫</div>
        <h1>403</h1>
        <h2>Access Denied</h2>
        <p>
            Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Silakan login dengan akun yang sesuai atau hubungi administrator.
        </p>
        
        <?php if ($user): ?>
        <div class="user-info">
            <h3>👤 User Terlogin</h3>
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($user['nama_lengkap']); ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="actions">
            <a href="login.php" class="btn">🔐 Back to Login</a>
            <?php if ($user): ?>
            <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>SIPANG POLRI - Sistem Informasi Perencanaan Anggaran</p>
            <p>&copy; <?php echo date('Y'); ?> POLRI. All Rights Reserved.</p>
        </div>
    </div>
</body>
</html>