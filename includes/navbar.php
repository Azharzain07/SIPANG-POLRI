<?php
/**
 * SIPANG POLRI - Navigation Bar
 * Included in authenticated pages
 */

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? '';
// Optional avatar URL stored in session (set when user has profile photo)
$avatarUrl = $_SESSION['avatar'] ?? '';
?>

<header class="header">
    <div class="header-left">
        <img src="images/logo_bagren.png" alt="Logo Bagren" class="header-logo">
        <div class="header-text">
            <h2><?php echo APP_NAME; ?></h2>
            <p><?php echo APP_FULL_NAME; ?></p>
        </div>
    </div>

    <div class="header-right">
        <?php if ($is_logged_in): ?>
            <!-- Dropdown Menu on Username -->
            <div class="user-dropdown-panel">
                <button id="userDropdownBtn" class="user-dropdown-btn">
                    <?php echo htmlspecialchars($username); ?>
                    <svg class="chevron" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
                </button>
                <div id="userDropdownMenu" class="user-dropdown-menu">
                    <a href="dashboard.php"><span class="icon">🏠</span> Dashboard</a>
                    <a href="pengajuan"><span class="icon">📝</span> Pengajuan</a>
                    <a href="riwayat"><span class="icon">📑</span> Riwayat</a>
                    <?php if ($role === ROLE_ADMIN_BAGREN || $role === ROLE_ADMIN_SIKEU): ?>
                        <div class="dropdown-divider"></div>
                        <a href="admin"><span class="icon">⚙️</span> Admin</a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a href="logout" class="logout"><span class="icon">🚪</span> Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login" class="login-btn">
                <span class="login-icon">🔐</span>
                <span class="login-text">Masuk Akun</span>
            </a>
        <?php endif; ?>
    </div>
</header>
