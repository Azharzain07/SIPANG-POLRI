<?php
/**
 * SIPANG POLRI - Authentication Guard
 * Sistem autentikasi dan autorisasi untuk semua halaman
 */

// Start session if not already started and no output has been sent
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/../config/database_config.php';

class AuthGuard {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    /**
     * Check if user is logged in, redirect to login if not
     */
    public function requireLogin($redirectTo = 'login.php') {
        if (!$this->auth->isLoggedIn()) {
            $this->redirectToLogin($redirectTo);
        }
    }
    
    /**
     * Check if user has specific role, redirect if not
     */
    public function requireRole($requiredRoles, $redirectTo = 'login.php') {
        $this->requireLogin($redirectTo);
        
        $user = $this->auth->getCurrentUser();
        if (!$user) {
            $this->redirectToLogin($redirectTo);
        }
        
        // Convert single role to array
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }
        
        if (!in_array($user['role'], $requiredRoles)) {
            $this->showAccessDenied();
        }
    }
    
    /**
     * Check if user is admin (BAGREN or SIKEU)
     */
    public function requireAdmin($redirectTo = 'login.php') {
        $this->requireRole(['ADMIN_BAGREN', 'ADMIN_SIKEU'], $redirectTo);
    }
    
    /**
     * Check if user is regular user (SATFUNG or POLSEK)
     */
    public function requireUser($redirectTo = 'login.php') {
        $this->requireRole(['USER_SATFUNG', 'USER_POLSEK'], $redirectTo);
    }
    
    /**
     * Get current user info
     */
    public function getCurrentUser() {
        return $this->auth->getCurrentUser();
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($permission) {
        return $this->auth->hasPermission($permission);
    }
    
    /**
     * Redirect to login page
     */
    private function redirectToLogin($redirectTo) {
        // Store current URL for redirect after login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        header("Location: $redirectTo");
        exit();
    }
    
    /**
     * Show access denied page
     */
    private function showAccessDenied() {
        http_response_code(403);
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied - SIPANG POLRI</title>
            <style>
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
                    border-radius: 10px;
                    padding: 40px;
                    text-align: center;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    max-width: 500px;
                }
                .icon {
                    font-size: 4rem;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #dc3545;
                    margin-bottom: 20px;
                }
                p {
                    color: #666;
                    margin-bottom: 30px;
                    line-height: 1.6;
                }
                .btn {
                    display: inline-block;
                    padding: 12px 24px;
                    background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 600;
                    transition: transform 0.3s ease;
                }
                .btn:hover {
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="icon">🚫</div>
                <h1>Access Denied</h1>
                <p>
                    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
                    Silakan login dengan akun yang sesuai atau hubungi administrator.
                </p>
                <a href="login.php" class="btn">🔐 Back to Login</a>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Helper functions for easy use
function requireLogin($redirectTo = 'login.php') {
    $guard = new AuthGuard();
    $guard->requireLogin($redirectTo);
}

function requireRole($requiredRoles, $redirectTo = 'login.php') {
    $guard = new AuthGuard();
    $guard->requireRole($requiredRoles, $redirectTo);
}

function requireAdmin($redirectTo = 'login.php') {
    $guard = new AuthGuard();
    $guard->requireAdmin($redirectTo);
}

function requireUser($redirectTo = 'login.php') {
    $guard = new AuthGuard();
    $guard->requireUser($redirectTo);
}

function getCurrentUser() {
    $guard = new AuthGuard();
    return $guard->getCurrentUser();
}

function hasPermission($permission) {
    $guard = new AuthGuard();
    return $guard->hasPermission($permission);
}
