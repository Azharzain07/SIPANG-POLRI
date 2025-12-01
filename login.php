<?php
/**
 * SIPANG POLRI - Login Page
 * Halaman login dengan sistem autentikasi yang kuat
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database_config.php';

$auth = new Auth();

// If user is already logged in, redirect to appropriate page
if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    if ($user['role'] === 'ADMIN_BAGREN' || $user['role'] === 'ADMIN_SIKEU') {
        header('Location: admin.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $captcha_input = $_POST['captcha'] ?? '';
    $captcha_hidden = $_POST['captcha-hidden'] ?? '';
    
    // Validate captcha
    if ($captcha_input !== $captcha_hidden) {
        $error = 'Kode keamanan tidak sesuai!';
    } elseif (empty($username) || empty($password)) {
        $error = 'Mohon isi username dan password!';
    } else {
        // Attempt login
        if ($auth->login($username, $password)) {
            $user = $auth->getCurrentUser();
            $redirect = ($user['role'] === 'ADMIN_BAGREN' || $user['role'] === 'ADMIN_SIKEU') ? 'admin.php' : 'index.php';
            header("Location: $redirect");
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPANG POLRI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            max-width: 1000px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Left Side - Illustration */
        .login-left {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .login-logos {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-out;
        }

        .login-logos img {
            height: 70px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
        }

        .login-logos img:hover {
            transform: scale(1.1);
        }

        .logo-divider {
            width: 2px;
            height: 50px;
            background: rgba(255,255,255,0.3);
            border-radius: 1px;
        }

        .login-illustration {
            max-width: 200px;
            width: 100%;
            height: auto;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .login-left h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .login-left p {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Right Side - Form */
        .login-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            color: #1a5490;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        .login-form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            color: #1a5490;
            font-size: 1.2rem;
            z-index: 1;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #1a5490;
            background: white;
            box-shadow: 0 0 0 3px rgba(26, 84, 144, 0.1);
        }

        /* Captcha Styles */
        .captcha-wrapper {
            margin-bottom: 1.5rem;
        }

        .captcha-box {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .captcha-display {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: bold;
            letter-spacing: 2px;
            text-align: center;
            min-width: 120px;
            user-select: none;
            box-shadow: 0 2px 8px rgba(26, 84, 144, 0.3);
        }

        .captcha-refresh {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .captcha-refresh:hover {
            background: #218838;
            transform: rotate(180deg);
        }

        .captcha-input {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        /* Form Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1a5490;
        }

        .remember-me label {
            color: #666;
            font-size: 0.9rem;
            cursor: pointer;
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(26, 84, 144, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 84, 144, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Back Home Link */
        .back-home {
            text-align: center;
        }

        .back-home a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .back-home a:hover {
            color: #1a5490;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Demo Accounts */
        .demo-accounts {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #1a5490;
        }

        .demo-accounts h4 {
            color: #1a5490;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .account-item {
            background: white;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            border: 1px solid #bbdefb;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .account-item:hover {
            background: #f3e5f5;
            transform: translateX(5px);
        }

        .account-item strong {
            color: #1a5490;
            display: block;
            margin-bottom: 2px;
        }

        .account-item span {
            color: #666;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }

            .login-left {
                padding: 2rem;
            }

            .login-right {
                padding: 2rem;
            }

            .login-logos {
                gap: 1rem;
            }

            .login-logos img {
                height: 50px;
            }

            .login-illustration {
                max-width: 150px;
            }

            .login-header h1 {
                font-size: 1.8rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Illustration -->
        <div class="login-left">
            <div class="login-left-content">
                <div class="login-logos">
                    <img src="images/Lambang_Polri.png" alt="Logo Polri">
                    <div class="logo-divider"></div>
                    <img src="images/logo_bagren.png" alt="Logo Bagren">
                </div>
                
                <img src="images/login_illustration.png" alt="Login Illustration" class="login-illustration">
                
                <h2>Sistem Informasi Perencanaan Anggaran</h2>
                <p>Kepolisian Negara Republik Indonesia</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="login-right">
            <div class="login-header">
                <h1>Selamat Datang</h1>
                <p>Silakan login untuk melanjutkan ke sistem</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>

            <form class="login-form" method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="form-group captcha-wrapper">
                    <label for="captcha">Kode Keamanan</label>
                    <div class="captcha-box">
                        <div class="captcha-display" id="captcha-display">ABC123</div>
                        <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Refresh Captcha">🔄</button>
                    </div>
                    <input type="hidden" id="captcha-hidden" name="captcha-hidden">
                    <div class="input-wrapper" style="margin-top: 0.5rem;">
                        <span class="input-icon">🛡️</span>
                        <input type="text" id="captcha-input" name="captcha" class="captcha-input" placeholder="Masukkan kode keamanan" required>
                    </div>
                </div>

                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                </div>

                <button type="submit" class="btn-login">🔐 Login</button>

                <div class="back-home">
                    <a href="index.php">← Kembali ke Beranda</a>
                </div>
            </form>

            <div class="demo-accounts">
                <h4>📋 Demo Accounts</h4>
                
                <div class="account-item" onclick="fillLogin('admin.bagren', 'password')">
                    <strong>ADMIN_BAGREN</strong>
                    <span>admin.bagren / password</span>
                </div>
                
                <div class="account-item" onclick="fillLogin('admin.sikeu', 'password')">
                    <strong>ADMIN_SIKEU</strong>
                    <span>admin.sikeu / password</span>
                </div>
                
                <div class="account-item" onclick="fillLogin('polsek.grt.kta', 'password')">
                    <strong>USER_POLSEK</strong>
                    <span>polsek.grt.kta / password</span>
                </div>
                
                <div class="account-item" onclick="fillLogin('satfung.grt.kta', 'password')">
                    <strong>USER_SATFUNG</strong>
                    <span>satfung.grt.kta / password</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Generate Captcha
        function generateCaptcha() {
            const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            let captcha = '';
            for (let i = 0; i < 6; i++) {
                captcha += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('captcha-display').textContent = captcha;
            document.getElementById('captcha-hidden').value = captcha;
        }

        // Refresh Captcha
        function refreshCaptcha() {
            generateCaptcha();
            document.getElementById('captcha-input').value = '';
        }

        // Fill login form
        function fillLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            refreshCaptcha();
        }

        // Form validation
        function validateForm(event) {
            const captchaInput = document.getElementById('captcha-input').value;
            const captchaHidden = document.getElementById('captcha-hidden').value;
            
            if (captchaInput !== captchaHidden) {
                alert('Kode keamanan tidak sesuai! Silakan coba lagi.');
                refreshCaptcha();
                return false;
            }
            
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (!username || !password) {
                alert('Mohon isi username dan password!');
                return false;
            }
            
            return true;
        }

        // Add form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            if (!validateForm(e)) {
                e.preventDefault();
            }
        });

        // Generate captcha on page load
        window.onload = function() {
            generateCaptcha();
            
            // Auto-focus username field
            document.getElementById('username').focus();
        };

        // Add enter key support for captcha
        document.getElementById('captcha-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
    </script>
</body>
</html>