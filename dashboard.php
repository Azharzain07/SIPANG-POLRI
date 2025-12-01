<?php
/**
 * SIPANG POLRI - Dashboard / Index Page
 * Halaman publik, landing page, dan dashboard user
 * Accessible oleh: siapa saja (login/belum login)
 * Admin akan di-redirect ke admin.php
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/auth_guard.php';
require_once 'config/database_config.php';

$auth = new Auth();
$isLoggedIn = $auth->isLoggedIn();
$currentUser = null;
$statusCounts = [];
$totalAmount = 0;
$recentPengajuan = [];

// Jika user sudah login dan adalah admin, redirect ke admin dashboard
if ($isLoggedIn) {
    $currentUser = $auth->getCurrentUser();
    $userRole = $currentUser['role'];
    
    // Redirect ke admin jika adalah ADMIN
    if ($userRole === 'ADMIN_BAGREN' || $userRole === 'ADMIN_SIKEU') {
        header('Location: admin.php');
        exit();
    }
    
    // Untuk user biasa, ambil data pengajuan
    $db = Database::getInstance()->getConnection();
    
    try {
        // Count pengajuan by status
        $query = "SELECT status, COUNT(*) as count FROM pengajuan WHERE user_id = ? GROUP BY status";
        $stmt = $db->prepare($query);
        $stmt->execute([$currentUser['id']]);
        $statusCounts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $statusCounts[$row['status']] = (int)$row['count'];
        }
        
        // Get total pengajuan amount
        $query = "SELECT COALESCE(SUM(jumlah_diajukan), 0) as total FROM pengajuan WHERE user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$currentUser['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalAmount = (int)$result['total'];
        
        // Get recent pengajuan
        $query = "SELECT id, kode, nama_kegiatan, jumlah_diajukan, status, tanggal_pengajuan, tanggal_update 
                  FROM pengajuan WHERE user_id = ? ORDER BY tanggal_pengajuan DESC LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute([$currentUser['id']]);
        $recentPengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Dashboard error: " . $e->getMessage());
    }
}

// Helper function to get status badge color
function getStatusColor($status) {
    $colors = [
        'DRAFT' => '#6c757d',
        'TERKIRIM' => '#0d6efd',
        'TERIMA_BERKAS' => '#0dcaf0',
        'DISPOSISI_KABAG_REN' => '#6f42c1',
        'DISPOSISI_WAKA' => '#d63384',
        'TERIMA_SIKEU' => '#198754',
        'DIBAYARKAN' => '#20c997',
        'DITOLAK' => '#dc3545'
    ];
    return $colors[$status] ?? '#6c757d';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIPANG POLRI</title>
    <link rel="stylesheet" href="assets/css/style.css?v=3">
    <style>
        /* Dashboard-Specific Inline Styles */
        .dashboard-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            width: 100%;
            padding: 0;
        }

        /* Hero Section - Main Banner */
        .hero-section {
            position: relative;
            height: 550px;
            background: url('images/background-website.png') center/cover;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: visible;
            z-index: 1;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(95, 110, 25, 0.6) 0%, rgba(45, 122, 181, 0.5) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 15px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 30px;
            font-weight: 300;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.2s forwards;
            opacity: 0;
            margin-top: 40px;
        }

        /* Quick Links/Services Section */
        .services-section {
            background: white;
            padding: 60px 20px;
            border-bottom: 3px solid #1a5490;
        }

        .services-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            color: #1a5490;
            margin-bottom: 50px;
            animation: slideInDown 0.8s ease-out;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .service-card {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
            padding: 35px 25px;
            border-radius: 12px;
            border-left: 5px solid #1a5490;
            text-align: center;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease-out forwards;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .service-card:nth-child(1) { animation-delay: 0.1s; }
        .service-card:nth-child(2) { animation-delay: 0.2s; }
        .service-card:nth-child(3) { animation-delay: 0.3s; }
        .service-card:nth-child(4) { animation-delay: 0.4s; }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(26, 84, 144, 0.15);
            background: linear-gradient(135deg, #e7f0ff 0%, #dce8ff 100%);
        }

        .service-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        .service-card h3 {
            font-size: 1.3rem;
            color: #1a5490;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .service-card p {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .service-card a {
            color: #1a5490;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .service-card a:hover {
            color: #f0d43a;
        }

        /* Vision Mission Section */
        .vision-mission-section {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            padding: 80px 20px;
        }

        .vision-mission-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .vision-mission-wrapper > div {
            animation: fadeInUp 0.8s ease-out;
        }

        .vision-mission-image {
            text-align: center;
            animation: slideInLeft 0.8s ease-out;
        }

        .vision-mission-image img {
            width: 100%;
            max-width: 350px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .vision-mission-image img:hover {
            transform: scale(1.05);
        }

        .vision-mission-content {
            animation: slideInRight 0.8s ease-out;
        }

        .vision-mission-content h2 {
            font-size: 2rem;
            margin-bottom: 25px;
            font-weight: 800;
        }

        .vision-mission-item {
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .vision-mission-item:last-child {
            border-bottom: none;
        }

        .vision-mission-item h4 {
            font-size: 1.3rem;
            margin-bottom: 12px;
            font-weight: 700;
        }

        .vision-mission-item p {
            font-size: 0.95rem;
            line-height: 1.7;
            opacity: 0.95;
        }

        /* Pengajuan Section */
        .pengajuan-section {
            background: white;
            padding: 60px 20px;
        }

        .pengajuan-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .pengajuan-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            animation: fadeInUp 0.8s ease-out forwards;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.2);
            transition: transform 0.3s ease;
        }

        .stat-box:nth-child(1) { animation-delay: 0.1s; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-box:nth-child(2) { animation-delay: 0.2s; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-box:nth-child(3) { animation-delay: 0.3s; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-box:nth-child(4) { animation-delay: 0.4s; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }

        .stat-box:hover {
            transform: translateY(-8px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 0.95rem;
            opacity: 0.95;
        }

        .pengajuan-list {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.8s ease-out 0.5s forwards;
            opacity: 0;
        }

        .pengajuan-list h3 {
            font-size: 1.5rem;
            color: #1a5490;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #f0d43a;
        }

        .pengajuan-item {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            animation: slideInRight 0.5s ease-out forwards;
        }

        .pengajuan-item:nth-child(1) { animation-delay: 0.55s; }
        .pengajuan-item:nth-child(2) { animation-delay: 0.6s; }
        .pengajuan-item:nth-child(3) { animation-delay: 0.65s; }
        .pengajuan-item:nth-child(4) { animation-delay: 0.7s; }
        .pengajuan-item:nth-child(5) { animation-delay: 0.75s; }

        .pengajuan-item:hover {
            background: #e7f0ff;
            transform: translateX(8px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .pengajuan-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .pengajuan-item-title {
            font-weight: 700;
            color: #1a5490;
            font-size: 1rem;
        }

        .pengajuan-item-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }

        .pengajuan-item-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .empty-state p {
            font-size: 1.1rem;
            color: #999;
            margin-bottom: 20px;
        }

        .empty-state a {
            display: inline-block;
            background: #1a5490;
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .empty-state a:hover {
            background: #f0d43a;
            color: #1a5490;
        }

        /* Contact Section */
        .contact-section {
            background: #f5f5f5;
            padding: 60px 20px;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .contact-card {
            background: white;
            padding: 35px 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            animation: fadeInUp 0.8s ease-out forwards;
            transition: all 0.3s ease;
        }

        .contact-card:nth-child(1) { animation-delay: 0.1s; }
        .contact-card:nth-child(2) { animation-delay: 0.2s; }
        .contact-card:nth-child(3) { animation-delay: 0.3s; }

        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            animation: bounce 2s ease-in-out infinite;
        }

        .contact-card:nth-child(1) .contact-icon { animation-delay: 0s; }
        .contact-card:nth-child(2) .contact-icon { animation-delay: 0.2s; }
        .contact-card:nth-child(3) .contact-icon { animation-delay: 0.4s; }

        .contact-card h4 {
            font-size: 1.2rem;
            color: #1a5490;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .contact-card p {
            font-size: 0.95rem;
            color: #666;
            line-height: 1.6;
        }

        /* Responsive Mobile Styles */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .hero-btn {
                width: 100%;
                max-width: 300px;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .vision-mission-wrapper {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .pengajuan-item-details {
                grid-template-columns: 1fr;
            }

            .contact-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Include Navbar -->
        <?php include 'includes/navbar.php'; ?>

        <div class="main-content">
            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <h1>SELAMAT DATANG DI WEBSITE</h1>
                    <h1>SIPANG POLRI GARUT</h1>
                    <h2>Sistem Informasi Perencanaan Anggaran</h1>
                    <h2>Profesional, Modern, dan Terpercaya dalam pengelolaan anggaran Bagren Polres Garut</h2>
                    <div class="hero-buttons">
                        <a href="pengajuan" class="hero-btn hero-btn-primary">Buat Pengajuan</a>
                        <a href="riwayat" class="hero-btn hero-btn-secondary">Lihat Riwayat</a>
                    </div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="services-section">
                <div class="services-container">
                    <h2 class="section-title">🎯 Fitur Utama</h2>
                    <div class="services-grid">
                        <div class="service-card">
                            <div class="service-icon">📝</div>
                            <h3>Pengajuan Anggaran</h3>
                            <p>Buat dan kelola pengajuan anggaran dengan mudah melalui sistem online kami.</p>
                            <a href="pengajuan">Selengkapnya →</a>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">📁</div>
                            <h3>Riwayat Lengkap</h3>
                            <p>Akses semua riwayat pengajuan Anda dengan dokumentasi lengkap.</p>
                            <a href="riwayat">Lihat Riwayat →</a>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">💬</div>
                            <h3>Bantuan & Support</h3>
                            <p>Hubungi tim support kami untuk bantuan teknis atau pertanyaan lainnya.</p>
                            <a href="#contact">Hubungi Kami →</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vision Mission Section -->
            <div class="vision-mission-section">
                <div class="vision-mission-wrapper">
                    <div class="vision-mission-image">
                        <img src="images/foto-kapolres.png" alt="Kapolres Garut">
                    </div>
                    <div class="vision-mission-content">
                        <h2>🎯 Visi & Misi</h2>
                        <div class="vision-mission-item">
                            <h4>📌 Visi</h4>
                            <p>Mewujudkan perencanaan anggaran kepolisian yang transparan, akuntabel, dan berkelanjutan untuk meningkatkan pelayanan publik.</p>
                        </div>
                        <div class="vision-mission-item">
                            <h4>🎯 Misi</h4>
                            <p>Menyediakan sistem informasi yang efisien dalam pengelolaan pengajuan anggaran dan meningkatkan koordinasi antar unit kerja Polres Garut.</p>
                        </div>
                        <div class="vision-mission-item">
                            <h4>👤 Kepemimpinan</h4>
                            <p>AKBP Yugi Bayu Hendarto, S.I.K., M.A.P.<br>Kepemimpinan yang Transparan dan Profesional</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Section - HANYA MUNCUL JIKA SUDAH LOGIN -->
            <?php if ($isLoggedIn): ?>
            <div class="pengajuan-section">
                <div class="pengajuan-container">
                    <h2 class="section-title">📊 Statistik Pengajuan Anda</h2>
                    <div class="pengajuan-stats">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo count($recentPengajuan); ?></div>
                            <div class="stat-label">Pengajuan Terbaru</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo number_format($totalAmount / 1000000000, 1); ?>M</div>
                            <div class="stat-label">Total Diajukan</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $statusCounts['DIBAYARKAN'] ?? 0; ?></div>
                            <div class="stat-label">Sudah Dibayarkan</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $statusCounts['TERKIRIM'] ?? 0; ?></div>
                            <div class="stat-label">Dalam Proses</div>
                        </div>
                    </div>

                    <?php if (!empty($recentPengajuan)): ?>
                        <div class="pengajuan-list">
                            <h3>📋 Pengajuan Terbaru</h3>
                            <?php foreach ($recentPengajuan as $p): ?>
                                <div class="pengajuan-item">
                                    <div class="pengajuan-item-header">
                                        <div class="pengajuan-item-title"><?php echo htmlspecialchars($p['nama_kegiatan'] ?? $p['kode']); ?></div>
                                        <div class="pengajuan-item-status" style="background-color: <?php echo getStatusColor($p['status']); ?>">
                                            <?php echo htmlspecialchars($p['status']); ?>
                                        </div>
                                    </div>
                                    <div class="pengajuan-item-details">
                                        <div>💰 <?php echo formatRupiah($p['jumlah_diajukan']); ?></div>
                                        <div>📅 <?php echo date('d/m/Y', strtotime($p['tanggal_pengajuan'])); ?></div>
                                        <div>🔄 <?php echo date('d/m/Y', strtotime($p['tanggal_update'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📭</div>
                            <p>Belum ada pengajuan. Mari mulai dengan membuat pengajuan baru!</p>
                            <a href="pengajuan">Buat Pengajuan Baru</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contact Section -->
            <div class="contact-section" id="contact">
                <div class="contact-container">
                    <h2 class="section-title">📞 Hubungi Kami</h2>
                    <div class="contact-grid">
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <h4>Lokasi Kantor</h4>
                            <p>Jl. Jendral Sudirman No. 204<br>Sucikaler, Kec. Karangpawitan<br>Kabupaten Garut, Jawa Barat 44182</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <h4>Telepon</h4>
                            <p>(0262) 236415<br>Call Center 110</p>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">✉️</div>
                            <h4>Email</h4>
                            <p>info@polresgarut.go.id<br>support@sipang.polri</p>
                        </div>
                    </div>
                    
                    <!-- Google Maps -->
                    <div class="maps-section" style="margin-top: 40px;">
                        <h3 style="text-align: center; color: #1a5490; margin-bottom: 25px; font-size: 1.3rem;">📍 Lokasi Polres Garut</h3>
                        <iframe 
                            width="100%" 
                            height="400" 
                            style="border: none; border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.923172!2d107.91931089927968!3d-7.211540974403235!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2d6a2d7c8c8c8c8d%3A0x9f5f5f5f5f5f5f5f!2sPolres%20Garut!5e0!3m2!1sid!2sid!4v1700000000" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                        <div style="margin-top: 20px; padding: 20px; background: #f8f9ff; border-radius: 8px; border-left: 4px solid #1a5490;">
                            <p style="color: #333; font-size: 0.9rem; margin: 0;">
                                <strong>📍 Alamat Lengkap:</strong><br>
                                Jl. Jendral Sudirman No. 204, Sucikaler, Kec. Karangpawitan<br>
                                Kabupaten Garut, Jawa Barat 44182
                            </p>
                            <p style="color: #666; font-size: 0.85rem; margin-top: 12px; margin-bottom: 0;">
                                <strong>Koordinat Geografis Marker:</strong><br>
                                Latitude: -7.211540974403235<br>
                                Longitude: 107.9193108992797<br>
                                <span style="font-size: 0.8rem; color: #999;">📍 Titik merah menunjukkan lokasi Polres Garut yang tepat</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <?php include 'includes/footer.php'; ?>
    </div>

    <script>
        // Pass authentication state to main.js
        const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    </script>
    <script src="assets/js/main.js?v=3"></script>
</body>
</html>
