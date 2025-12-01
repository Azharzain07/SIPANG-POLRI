<?php
/**
 * SIPANG POLRI - Pengajuan Page
 * Halaman pengajuan anggaran - Hanya untuk USER_SATFUNG dan USER_POLSEK
 */

// Require authentication and user role
require_once 'includes/auth_guard.php';
requireUser(); // Only USER_SATFUNG and USER_POLSEK can access

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Anggaran - Sistem Informasi Perencanaan Anggaran</title>
    
    <!-- Load Custom Alert CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideDown 0.6s ease-out;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-logo {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .header-text h2 {
            font-size: 1.3rem;
            margin-bottom: 0.2rem;
        }

        .header-text p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .header-right {
            text-align: right;
        }

        .header-right .menu {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .header-right .menu a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .header-right .menu a:hover {
            opacity: 0.8;
        }

        .header-right .user {
            font-weight: 600;
        }

        /* Container */
        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            animation: slideUp 0.7s ease-out;
        }

        .form-title {
            color: #164a7a;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #1a5490;
        }

        /* Form Section */
        .section-title {
            color: #164a7a;
            font-size: 1.1rem;
            margin: 1.5rem 0 1rem 0;
            font-weight: 600;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group         .form-help {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.8rem;
            color: #6c757d;
            font-style: italic;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s ease;
        }

        .form-group input[type="date"] {
            cursor: pointer;
            color: #1a5490;
            font-weight: 600;
        }

        .form-group input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            filter: brightness(0) saturate(100%) invert(29%) sepia(55%) saturate(1152%) hue-rotate(179deg) brightness(95%) contrast(92%);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a5490;
            box-shadow: 0 0 0 3px rgba(26, 84, 144, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .form-group .auto-fill {
            background-color: #e3f2fd;
            color: #1a5490;
            font-weight: 600;
            cursor: not-allowed;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 84, 144, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        /* Table */
        .table-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: slideUp 0.8s ease-out;
        }

        .table-title {
            color: #164a7a;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table thead {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
        }

        table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
        }

        table td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        table tbody tr {
            transition: all 0.3s ease;
        }

        table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-message {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
        }

        .btn-table {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
            margin-right: 0.3rem;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            min-width: 80px;
            cursor: default;
        }

        .status-draft {
            background-color: #f8f9fa;
            color: #6c757d;
            border: 1px solid #dee2e6;
        }

        .status-terima-berkas {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-disposisi-kabag {
            background-color: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffcc02;
        }

        .status-disposisi-waka {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #ce93d8;
        }

        .status-terima-sikeu {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .status-dibayarkan {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Rejected Row Styling */
        .rejected-row {
            background-color: #fff5f5;
            border-left: 4px solid #dc3545;
        }

        .rejected-row:hover {
            background-color: #ffe6e6;
        }

        .rejection-note {
            color: #dc3545;
            font-style: italic;
            margin-top: 5px;
            display: block;
        }

        .admin-rejection-reason {
            color: #6c757d;
            font-size: 0.85em;
            background-color: #f8f9fa;
            padding: 3px 6px;
            border-radius: 3px;
            border-left: 3px solid #dc3545;
            margin-top: 3px;
            display: block;
            max-width: 300px;
            word-wrap: break-word;
        }

        /* Detail Modal Styles */
        .detail-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #164a7a;
        }

        .detail-section h4 {
            color: #164a7a;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.1em;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .detail-item span {
            color: #333;
            font-size: 0.95em;
        }

        .admin-note-detail {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            color: #856404;
            font-style: italic;
            line-height: 1.5;
        }

        .next-steps {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
        }

        .next-steps p {
            margin: 5px 0;
            color: #164a7a;
        }

        .next-steps ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .next-steps li {
            margin: 5px 0;
            color: #164a7a;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
            margin-right: 5px;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        /* Revision Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            position: relative;
            margin: 5% auto;
            width: 600px;
            max-width: 90%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal-header {
            background: #164a7a;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 0 0 8px 8px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-primary {
            background: #164a7a;
            color: white;
        }

        .btn-primary:hover {
            background: #0d3a5f;
        }

        .revision-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #164a7a;
        }

        .revision-info h4 {
            margin: 0 0 10px 0;
            color: #164a7a;
        }

        .admin-note {
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-style: italic;
            color: #666;
        }

        .revision-form h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .revision-form p {
            margin: 0 0 15px 0;
            color: #666;
        }

        /* Checkbox */
        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #1a5490;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header-right {
                text-align: center;
                margin-top: 0.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 0 1rem;
            }

            .form-card, .table-card {
                padding: 1.5rem;
            }

            table {
                font-size: 0.85rem;
            }

            table th, table td {
                padding: 0.6rem;
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
        }

        .loading-overlay.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            text-align: center;
        }

        .spinner-container {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
        }

        .spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1.5s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        }

        .spinner-ring:nth-child(1) {
            border-top-color: #1a5490;
            animation-delay: -0.45s;
        }

        .spinner-ring:nth-child(2) {
            border-top-color: #2d7ab5;
            animation-delay: -0.3s;
        }

        .spinner-ring:nth-child(3) {
            border-top-color: #4a9fd9;
            animation-delay: -0.15s;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-size: 1rem;
            font-weight: 600;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        /* Input Animation */
        input:focus, select:focus, textarea:focus {
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        /* Button Hover Effect */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }
        .user-dropdown-panel { position: relative; display: inline-block; }
        .user-dropdown-btn { padding: .5em 1.05em; border-radius: 23px; border:none; background:#eaf2fe; color:#1a5490; font-size:.98rem; font-weight:600; cursor:pointer; transition:all.2s; }
        .user-dropdown-btn:focus,.user-dropdown-btn:hover { background:#d2e8fc; color:#17436e; }
        .user-dropdown-menu { position: absolute; right:0; top:120%; min-width:163px; background:#fff; border-radius:11px; box-shadow:0 8px 19px #19598f1a; opacity:0; transform:translateY(-7px) scale(.98); pointer-events:none; transition:transform.22s,opacity.18s; padding:5px 0; z-index:60; }
        .user-dropdown-panel.open .user-dropdown-menu { opacity:1; pointer-events:auto; transform:translateY(0) scale(1); }
        .user-dropdown-menu a { display:flex;align-items:center;gap:8px; padding:9px 20px; text-decoration:none; color:#164a72;font-size:.97em; border-radius:5px; transition:background.13s,color.12s;}
        .user-dropdown-menu a.logout{ color:#c13d2f;font-weight:700;}
        .user-dropdown-menu a.logout:hover{background:#ffdede;color:#a0190a;}
        .user-dropdown-menu a:hover,.user-dropdown-menu a:focus{background:#f0f7fc;color:#1164af;}
        .dropdown-divider{height:1px; background:#ebf2fa; margin:5px 0;}
        .chevron{transition:transform.2s;display:inline;vertical-align:middle;}
        .user-dropdown-panel.open .chevron{transform:rotate(180deg);} 
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner-container">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="loading-text">Memproses data Anda...</div>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="images/logo_bagren.png" alt="Logo Bagren" class="header-logo">
            <div class="header-text">
                <h2>Bagren Polres Garut</h2>
                <p>Sistem Informasi Perencanaan Anggaran</p>
            </div>
        </div>
        <div class="header-right">
            <!-- Dropdown Menu on Username -->
            <div class="user-dropdown-panel">
                <button id="userDropdownBtn" class="user-dropdown-btn">
                    <?php echo htmlspecialchars($currentUser['nama_lengkap']); ?> (<?php echo htmlspecialchars($currentUser['role']); ?>)
                    <svg class="chevron" style="margin-left:4px;width:17px;vertical-align:middle" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9" fill="none" stroke="#19598F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline></svg>
                </button>
                <div id="userDropdownMenu" class="user-dropdown-menu">
                    <a href="index.php"><span class="icon">🏠</span> Dashboard</a>
                    <a href="pengajuan.php"><span class="icon">📝</span> Pengajuan</a>
                    <a href="riwayat.php"><span class="icon">📑</span> Riwayat</a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="logout"><span class="icon">🚪</span> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Form Pengajuan -->
        <div class="form-card">
            <h2 class="form-title">Buat Pengajuan Anggaran Baru</h2>
            
            <form id="pengajuanForm">
                <!-- Informasi Utama -->
                <div class="section-title">Informasi Utama</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Pengajuan</label>
                        <input type="date" id="tanggal_pengajuan" name="tanggal_pengajuan" required readonly>
                        <small class="form-help">Tanggal pengajuan hanya bisa hari ini</small>
                    </div>

                    <div class="form-group">
                        <label>Bulan Pengajuan <span class="required">*</span></label>
                        <select id="bulan_pengajuan" name="bulan_pengajuan" required>
                            <option value="">Pilih Bulan</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                        <small class="form-help">Bulan pengajuan otomatis sesuai tanggal, bisa diubah jika diperlukan</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Sumber Dana <span class="required">*</span></label>
                        <select id="sumber_dana" name="sumber_dana" required onchange="filterKegiatan()">
                            <option value="">-- Pilih Sumber Dana --</option>
                            <option value="RM">RM (RUPIAH MURNI)</option>
                            <option value="PNBP">PNBP (Penerimaan Negara Bukan Pajak)</option>
                        </select>
                    </div>
                </div>

                <!-- Detail Tujuan & Kegiatan -->
                <div class="section-title">Detail Tujuan & Kegiatan</div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pejabat Yang Bertanggung Jawab</label>
                        <select id="penanggung_jawab" name="penanggung_jawab" required>
                            <option value="">-- Pilih Pejabat Yang Bertanggung Jawab --</option>
                            <optgroup label="FUNGSI">
                                <option value="KABAG">KABAG</option>
                                <option value="KASAT">KASAT</option>
                                <option value="KASI">KASI</option>
                            </optgroup>
                            <optgroup label="POLSEK">
                                <option value="KAPOLSEK">KAPOLSEK</option>
                                <option value="KAPOLSUBSEKTOR">KAPOLSUBSEKTOR</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Bendahara Pengeluaran Pembantu</label>
                        <select id="penanggung_perbendaharaan" name="penanggung_perbendaharaan" required>
                            <option value="">-- Pilih Bendahara Pengeluaran Pembantu --</option>
                            <option value="KAURMINTU">KAURMINTU</option>
                            <option value="KASIUM">KASIUM</option>
                            <option value="BAMIN">BAMIN</option>
                        </select>
                    </div>
                </div>

                <!-- Rincian -->
                <div class="section-title">Rincian</div>

                <div style="background: #e3f2fd; padding: 0.8rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #1a5490;">
                    <p style="margin: 0; color: #164a7a; font-size: 0.9rem;">
                        <strong>ℹ️ Petunjuk:</strong> Pilih Sumber Dana terlebih dahulu, maka kegiatan akan muncul sesuai sumber dana yang dipilih (RM atau PNBP).
                    </p>
                </div>

                <div class="form-group">
                    <label>PROGRAM/KEGIATAN/KRO/SUB KOMPONEN/DETAIL <span class="required">*</span></label>
                    <select id="kegiatan" name="kegiatan" required onchange="updatePagu()">
                        <option value="">-- Pilih Sumber Dana Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jumlah Pagu (Saldo Awal)</label>
                        <input type="text" id="jumlah_pagu" class="auto-fill" readonly placeholder="-- Pilih Aktivitas --">
                    </div>

                    <div class="form-group">
                        <label>SISA PAGU (Saldo Akhir)</label>
                        <input type="text" id="sisa_saldo" class="auto-fill" readonly placeholder="-- Pilih Aktivitas --">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Jumlah Diajukan <span class="required">*</span></label>
                        <input type="text" id="jumlah_diajukan" name="jumlah_diajukan" placeholder="0" required style="font-weight: 600; font-size: 1.1rem; color: #1a5490;">
                        <input type="hidden" id="jumlah_diajukan_raw" name="jumlah_diajukan_raw">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Upload Dokumen Pendukung <span class="required">*</span></label>
                        <input type="file" id="dokumen_pendukung" name="dokumen_pendukung" accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls" required>
                        <small class="form-help">Format yang diperbolehkan: PDF, JPG, PNG, XLS, XLSX (Max 5MB)</small>
                        <div id="file-preview" style="margin-top: 10px; display: none;">
                            <div style="background: #e8f5e8; padding: 10px; border-radius: 5px; border-left: 3px solid #28a745;">
                                <strong>File Terpilih:</strong> <span id="file-name"></span>
                                <br><small>Ukuran: <span id="file-size"></span></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Uraian <span class="required">*</span></label>
                        <textarea id="uraian" name="uraian" placeholder="Masukkan uraian pengajuan anggaran..." required></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-primary" onclick="simpanDraf()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 0.5rem;">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Simpan Draf
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Draf -->
        <div class="table-card">
            <h2 class="table-title">Draf Pengajuan (Siap Dikirim)</h2>
            
            <table id="tabelDraf">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onchange="toggleSelectAll()"></th>
                        <th>Tanggal Pengajuan Tambahan Bulan</th>
                        <th>Program</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="draftTableBody">
                    <tr>
                        <td colspan="6" class="empty-message">
                            Belum ada draf. Silakan simpan pengajuan di atas.
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="form-actions" style="margin-top: 1.5rem; justify-content: center;">
                <button id="kirimDrafBtn" type="button" class="btn btn-success" onclick="kirimSemuaDraf()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px; vertical-align: middle; margin-right: 0.5rem;">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Kirim Semua Draf Terpilih
                </button>
            </div>
        </div>
    </div>

    <script>
        // Data kegiatan akan diambil dari database
        let dataKegiatan = [];

        // Array untuk menyimpan draf
        let draftList = [];
        let draftCounter = 1;

        // Set tanggal pengajuan otomatis (hanya hari ini)
        window.onload = function() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            document.getElementById('tanggal_pengajuan').value = `${year}-${month}-${day}`;
            
            // Set bulan pengajuan otomatis sesuai tanggal
            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const currentMonth = monthNames[today.getMonth()];
            document.getElementById('bulan_pengajuan').value = currentMonth;
            
            // Setup file upload handler
            setupFileUpload();
            
            // Setup bulan pengajuan auto-update (jika tanggal berubah)
            setupBulanPengajuanAutoUpdate();
            
            // Load data kegiatan dari database
            loadKegiatanData();
            
            // Load draft data dari database
            // Load local drafts (we keep drafts client-side until the user submits them)
            loadDraftData();
        };

        // Setup bulan pengajuan auto-update
        function setupBulanPengajuanAutoUpdate() {
            const tanggalInput = document.getElementById('tanggal_pengajuan');
            const bulanSelect = document.getElementById('bulan_pengajuan');
            
            tanggalInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const monthNames = [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                
                // Auto-update bulan sesuai tanggal yang dipilih
                bulanSelect.value = monthNames[selectedDate.getMonth()];
            });
        }

        // Setup file upload handler
        function setupFileUpload() {
            const fileInput = document.getElementById('dokumen_pendukung');
            const filePreview = document.getElementById('file-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file size (5MB max)
                    if (file.size > 5 * 1024 * 1024) {
                        showCustomAlert('File terlalu besar! Maksimal 5MB', 'error');
                        fileInput.value = '';
                        filePreview.style.display = 'none';
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
                    if (!allowedTypes.includes(file.type)) {
                        showCustomAlert('Format file tidak diperbolehkan! Gunakan PDF, JPG, PNG, XLS, atau XLSX', 'error');
                        fileInput.value = '';
                        filePreview.style.display = 'none';
                        return;
                    }

                    // Show file preview
                    fileName.textContent = file.name;
                    fileSize.textContent = formatFileSize(file.size);
                    filePreview.style.display = 'block';
                } else {
                    filePreview.style.display = 'none';
                }
            });
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Load data kegiatan dari database
        async function loadKegiatanData() {
            try {
                const response = await fetch('api/main.php?action=get_kegiatan');
                const result = await response.json();
                
                if (result.success) {
                    dataKegiatan = result.data.map(item => ({
                        id: item.id, // Include database ID
                        nama: item.nama,
                        kode: item.kode,
                        pagu: parseFloat(item.pagu),
                        sumber: item.sumber_dana
                    }));
                    console.log('Data kegiatan loaded:', dataKegiatan.length, 'items');
                    console.log('Sample kegiatan data:', dataKegiatan[0]);
                } else {
                    console.error('Failed to load kegiatan data:', result.message);
                }
            } catch (error) {
                console.error('Error loading kegiatan data:', error);
            }
        }

        // Load draft data from database
        // Load draft data (local drafts kept in memory until user submits)
        async function loadDraftData() {
            try {
                // Drafts are stored client-side until submission. Render current local drafts.
                renderDraftTable();
                console.log('Draft data rendered from local memory:', draftList);
            } catch (error) {
                console.error('Error rendering local draft data:', error);
            }
        }

        // Fetch server-side riwayat when needed (returns parsed JSON)
        async function fetchRemoteRiwayat() {
            try {
                const response = await fetch('api/main.php?action=get_riwayat&t=' + Date.now());
                const result = await response.json();
                return result;
            } catch (err) {
                console.error('Error fetching remote riwayat:', err);
                return { success: false, message: err.message };
            }
        }

        // Filter kegiatan berdasarkan sumber dana
        function filterKegiatan() {
            const sumberDana = document.getElementById('sumber_dana').value;
            const kegiatanSelect = document.getElementById('kegiatan');
            
            // Reset
            kegiatanSelect.innerHTML = '<option value="">-- Pilih Kegiatan --</option>';
            document.getElementById('jumlah_pagu').value = '';
            document.getElementById('sisa_saldo').value = '';
            
            if (!sumberDana) {
                kegiatanSelect.innerHTML = '<option value="">-- Pilih Sumber Dana Terlebih Dahulu --</option>';
                return;
            }

            // Filter data berdasarkan sumber dana (RM atau PNBP)
            const filteredData = dataKegiatan.filter(item => item.sumber === sumberDana);
            
            if (filteredData.length === 0) {
                kegiatanSelect.innerHTML = '<option value="">-- Tidak ada data untuk sumber dana ini --</option>';
                return;
            }

            filteredData.forEach((item, index) => {
                const option = document.createElement('option');
                option.value = item.id || (index + 1); // Use database ID, fallback to index+1
                option.dataset.pagu = item.pagu;
                option.dataset.sumber = item.sumber;
                option.dataset.kode = item.kode;
                option.dataset.nama = item.nama;
                option.textContent = `${item.kode} - ${item.nama}`;
                kegiatanSelect.appendChild(option);
            });
        }

        // Update pagu berdasarkan kegiatan yang dipilih
        function updatePagu() {
            const kegiatanSelect = document.getElementById('kegiatan');
            const selectedOption = kegiatanSelect.options[kegiatanSelect.selectedIndex];
            
            if (selectedOption.value === '') {
                document.getElementById('jumlah_pagu').value = '';
                document.getElementById('sisa_saldo').value = '';
                return;
            }

            const pagu = parseInt(selectedOption.dataset.pagu);
            
            // Untuk simulasi, anggap 30% sudah terpakai
            const terpakai = Math.floor(pagu * 0.3);
            const sisaSaldo = pagu - terpakai;
            
            document.getElementById('jumlah_pagu').value = formatRupiah(pagu);
            document.getElementById('sisa_saldo').value = formatRupiah(sisaSaldo);
        }

        // Format Rupiah dengan pemisah ribuan
        function formatRupiah(angka) {
            if (angka === 0 || angka === '0') {
                return 'Rp 0';
            }
            const number = parseInt(angka);
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        // Simpan draf
        // Check session status
        async function checkSessionStatus() {
            try {
                const response = await fetch('api/main.php?action=check_session');
                const result = await response.json();
                
                if (!result.success) {
                    showCustomAlert('Session telah berakhir. Silakan login kembali.', 'error', 'Session Expired');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                    return false;
                }
                return true;
            } catch (error) {
                console.error('Error checking session:', error);
                showCustomAlert('Tidak dapat memverifikasi session. Silakan refresh halaman.', 'error');
                return false;
            }
        }

        async function simpanDraf() {
            // Check session first
            const sessionValid = await checkSessionStatus();
            if (!sessionValid) {
                return;
            }
            
            const form = document.getElementById('pengajuanForm');
            
            // Validasi form
            if (!form.checkValidity()) {
                showCustomAlert('Mohon lengkapi semua field yang wajib diisi!', 'warning');
                form.reportValidity();
                return;
            }

            // Show loading
            showLoading('Menyimpan draf...');

            const sumberDana = document.getElementById('sumber_dana').value;
            const kegiatanSelect = document.getElementById('kegiatan');
            const selectedOption = kegiatanSelect.options[kegiatanSelect.selectedIndex];

            if (!selectedOption.value || selectedOption.value === '' || selectedOption.value === '0') {
                showCustomAlert('Mohon pilih kegiatan terlebih dahulu!', 'warning');
                hideLoading();
                return;
            }

            const tanggal = document.getElementById('tanggal_pengajuan').value;
            const bulan = document.getElementById('bulan_pengajuan').value;
            const namaKegiatan = selectedOption.textContent;
            const kode = namaKegiatan.split(' - ')[0];
            const jumlah = parseInt(document.getElementById('jumlah_diajukan_raw').value || 0);
            const sisaSaldoText = document.getElementById('sisa_saldo').value;
            const sisaSaldo = parseInt(sisaSaldoText.replace(/[^0-9]/g, ''));

            // Validasi jumlah vs sisa saldo
            if (jumlah > sisaSaldo) {
                hideLoading();
                showCustomAlert('Jumlah yang diajukan melebihi sisa saldo!\n\nSisa Saldo: ' + formatRupiah(sisaSaldo) + '\nJumlah Diajukan: ' + formatRupiah(jumlah), 'error', 'Jumlah Melebihi Saldo');
                return;
            }

            if (jumlah <= 0) {
                hideLoading();
                showCustomAlert('Jumlah yang diajukan harus lebih dari 0!', 'warning');
                return;
            }

            // Prepare data for database
            const kegiatanId = parseInt(selectedOption.value);
            
            // Debug: Log kegiatan data
            console.log('Debug kegiatan data:', {
                selectedOptionValue: selectedOption.value,
                kegiatanId: kegiatanId,
                selectedOptionText: selectedOption.textContent,
                isNaN: isNaN(kegiatanId)
            });
            
            const pengajuanData = {
                tanggal_pengajuan: tanggal,
                bulan_pengajuan: bulan,
                tahun_pengajuan: new Date(tanggal).getFullYear(),
                sumber_dana: sumberDana,
                uraian: document.getElementById('uraian').value,
                penanggung_jawab: document.getElementById('penanggung_jawab').value,
                bendahara_pengeluaran_pembantu: document.getElementById('penanggung_perbendaharaan').value,
                kegiatan_id: kegiatanId,
                jumlah_diajukan: jumlah,
                jumlah_pagu: parseInt(document.getElementById('jumlah_pagu').value.replace(/[^0-9]/g, '')),
                sisa_pagu: sisaSaldo
            };
            
            // Debug: Log complete pengajuan data
            console.log('Debug pengajuan data:', pengajuanData);

            try {
                    // Instead of saving to server as a draft, keep draft client-side until user submits
                    const fileInput = document.getElementById('dokumen_pendukung');
                    const fileObj = (fileInput && fileInput.files && fileInput.files[0]) ? fileInput.files[0] : null;

                    // Create a temporary client-side id
                    const tempId = 'tmp-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

                    const localDraft = {
                        id: tempId,
                        tanggal: tanggal,
                        bulan: bulan,
                        program: namaKegiatan,
                        kode: kode,
                        jumlah: jumlah,
                        status: 'Draft',
                        revisi_keterangan: null,
                        is_revisi: false,
                        file: fileObj,
                        data: pengajuanData
                    };

                    // Add to draft list and render
                    draftList.push(localDraft);
                    renderDraftTable();

                    // Reset form (except tanggal and bulan)
                    const tanggalValue = document.getElementById('tanggal_pengajuan').value;
                    const bulanValue = document.getElementById('bulan_pengajuan').value;
                    form.reset();
                    document.getElementById('tanggal_pengajuan').value = tanggalValue;
                    document.getElementById('bulan_pengajuan').value = bulanValue;
                    document.getElementById('jumlah_pagu').value = '';
                    document.getElementById('sisa_saldo').value = '';
                    document.getElementById('jumlah_diajukan').value = '';
                    document.getElementById('jumlah_diajukan_raw').value = '';
                    document.getElementById('jumlah_diajukan').style.color = '#1a5490';
                    document.getElementById('jumlah_diajukan').style.borderColor = '#ddd';
                    document.getElementById('kegiatan').innerHTML = '<option value="">-- Pilih Sumber Dana Terlebih Dahulu --</option>';

                    hideLoading();
                    showCustomAlert('Pengajuan berhasil disimpan sebagai draf (lokal). Silakan kirim saat siap.', 'success');
            } catch (error) {
                hideLoading();
                console.error('Error saving pengajuan:', error);
                showCustomAlert('Terjadi kesalahan saat menyimpan pengajuan', 'error');
            }
        }

        // Format tanggal untuk display (YYYY-MM-DD ke DD/MM/YYYY)
        function formatTanggalDisplay(tanggal) {
            if (!tanggal) return '';
            const parts = tanggal.split('-');
            if (parts.length === 3) {
                return `${parts[2]}/${parts[1]}/${parts[0]}`;
            }
            return tanggal;
        }

        // Render tabel draf
        function renderDraftTable() {
            const tbody = document.getElementById('draftTableBody');
            
            if (draftList.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="empty-message">Belum ada draf. Silakan simpan pengajuan di atas.</td></tr>';
                return;
            }

            tbody.innerHTML = '';
            draftList.forEach(draft => {
                // Check if this draft was rejected and needs revision
                const isRejected = draft.status === 'DITOLAK' && draft.is_revisi;
                     const actionButtons = isRejected 
                          ? `<button class="btn btn-info btn-table" onclick="showDetailModal(${JSON.stringify(draft.id)})">Detail</button>
                              <button class="btn btn-warning btn-table" onclick="showRevisionModal(${JSON.stringify(draft.id)})">Revisi</button>`
                          : `<button class="btn btn-danger btn-table" onclick="hapusDraf(${JSON.stringify(draft.id)})">Hapus</button>`;
                
                const row = `
                    <tr ${isRejected ? 'class="rejected-row"' : ''}>
                        <td><input type="checkbox" class="draft-checkbox" value="${draft.id}"></td>
                        <td>${formatTanggalDisplay(draft.tanggal)}</td>
                        <td>${draft.program}</td>
                        <td>${formatRupiah(draft.jumlah)}</td>
                        <td>
                            <span class="status-badge ${getStatusClass(draft.status)}">${draft.status}</span>
                            ${isRejected ? `
                                <br><small class="rejection-note">📝 Ada catatan revisi</small>
                                <br><small class="admin-rejection-reason">💬 Admin: "${draft.revisi_keterangan || 'Tidak ada catatan'}"</small>
                            ` : ''}
                        </td>
                        <td>
                            ${actionButtons}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }

        // Show detail modal for rejected pengajuan
        function showDetailModal(draftId) {
            const draft = draftList.find(d => d.id === draftId);
            if (!draft) return;
            
            const modal = document.getElementById('detailModal');
            const modalTitle = document.getElementById('detailModalTitle');
            const modalBody = document.getElementById('detailModalBody');
            
            modalTitle.textContent = 'Detail Pengajuan Ditolak';
            modalBody.innerHTML = `
                <div class="detail-section">
                    <h4>📋 Informasi Pengajuan</h4>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Tanggal Pengajuan:</label>
                            <span>${formatTanggalDisplay(draft.tanggal)}</span>
                        </div>
                        <div class="detail-item">
                            <label>Program/Kegiatan:</label>
                            <span>${draft.program}</span>
                        </div>
                        <div class="detail-item">
                            <label>Kode:</label>
                            <span>${draft.kode}</span>
                        </div>
                        <div class="detail-item">
                            <label>Jumlah Diajukan:</label>
                            <span>${formatRupiah(draft.jumlah)}</span>
                        </div>
                        <div class="detail-item">
                            <label>Status:</label>
                            <span class="status-badge ${getStatusClass(draft.status)}">${draft.status}</span>
                        </div>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>💬 Catatan dari Admin</h4>
                    <div class="admin-note-detail">
                        ${draft.revisi_keterangan || 'Tidak ada catatan khusus dari admin'}
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4>🔧 Langkah Selanjutnya</h4>
                    <div class="next-steps">
                        <p><strong>Pengajuan Anda ditolak dengan alasan di atas.</strong></p>
                        <p>Anda dapat:</p>
                        <ul>
                            <li>📝 <strong>Revisi</strong> - Perbaiki pengajuan sesuai catatan admin</li>
                            <li>🗑️ <strong>Hapus</strong> - Hapus pengajuan jika tidak ingin melanjutkan</li>
                        </ul>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        // Show revision modal
        function showRevisionModal(draftId) {
            const draft = draftList.find(d => d.id === draftId);
            if (!draft) return;
            
            const modal = document.getElementById('revisionModal');
            const modalTitle = document.getElementById('revisionModalTitle');
            const modalBody = document.getElementById('revisionModalBody');
            
            modalTitle.textContent = 'Revisi Pengajuan';
            modalBody.innerHTML = `
                <div class="revision-info">
                    <h4>📋 Catatan Penolakan dari Admin:</h4>
                    <div class="admin-note">
                        ${draft.revisi_keterangan || 'Tidak ada catatan khusus dari admin'}
                    </div>
                </div>
                <div class="revision-form">
                    <h4>✏️ Perbaiki Pengajuan:</h4>
                    <p><strong>Silakan perbaiki pengajuan sesuai dengan catatan admin di atas.</strong></p>
                    <div class="form-group">
                        <label for="revisionJumlah">💰 Jumlah Diajukan (Baru) <span class="required">*</span></label>
                        <input type="number" id="revisionJumlah" value="${draft.jumlah}" min="0" step="1000" required>
                        <small class="form-help">Masukkan jumlah yang sudah diperbaiki sesuai saran admin</small>
                    </div>
                    <div class="form-group">
                        <label for="revisionUraian">Uraian (Opsional)</label>
                        <textarea id="revisionUraian" rows="3" placeholder="Tambahkan keterangan revisi jika diperlukan..."></textarea>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            
            // Store draft ID for later use
            modal.dataset.draftId = draftId;
        }
        
        // Confirm revision
        async function confirmRevision() {
            const modal = document.getElementById('revisionModal');
            const draftId = modal.dataset.draftId;
            const newJumlah = document.getElementById('revisionJumlah').value;
            const revisionNote = document.getElementById('revisionUraian').value.trim();
            
            if (!newJumlah || newJumlah <= 0) {
                alert('Jumlah revisi harus diisi dan lebih dari 0');
                return;
            }
            
            try {
                const response = await fetch('api/main.php?action=revise_pengajuan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pengajuan_id: draftId,
                        jumlah_baru: newJumlah,
                        keterangan_revisi: revisionNote
                    })
                });
                
                const result = await response.json();
                    if (result.success) {
                    alert('Pengajuan berhasil direvisi dan dikirim ulang untuk persetujuan');
                    closeRevisionModal();
                    // Refresh server riwayat if needed
                    await fetchRemoteRiwayat();
                } else {
                    alert('Gagal merevisi pengajuan: ' + result.message);
                }
            } catch (error) {
                console.error('Error revising pengajuan:', error);
                alert('Terjadi kesalahan saat merevisi pengajuan');
            }
        }
        
        // Close detail modal
        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close revision modal
        function closeRevisionModal() {
            const modal = document.getElementById('revisionModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const detailModal = document.getElementById('detailModal');
            const revisionModal = document.getElementById('revisionModal');
            
            if (event.target === detailModal) {
                closeDetailModal();
            }
            if (event.target === revisionModal) {
                closeRevisionModal();
            }
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDetailModal();
                closeRevisionModal();
            }
        });

        // Get status class for styling
        function getStatusClass(status) {
            const statusMap = {
                'Draft': 'status-draft',
                'TERIMA BERKAS': 'status-terima-berkas',
                'DISPOSISI KABAG REN': 'status-disposisi-kabag',
                'DISPOSISI WAKA': 'status-disposisi-waka',
                'TERIMA SIKEU': 'status-terima-sikeu',
                'DIBAYARKAN': 'status-dibayarkan',
                'DITOLAK': 'status-ditolak'
            };
            return statusMap[status] || 'status-draft';
        }

        // Hapus draf
        function hapusDraf(id) {
            if (confirm('Yakin ingin menghapus draf ini?')) {
                draftList = draftList.filter(draft => draft.id !== id);
                renderDraftTable();
            }
        }

        // Toggle select all
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.draft-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        }

        // Kirim semua draf terpilih
        async function kirimSemuaDraf() {
            const checkboxes = document.querySelectorAll('.draft-checkbox:checked');
            
            if (checkboxes.length === 0) {
                showCustomAlert('Pilih minimal satu draf untuk dikirim!', 'warning');
                return;
            }

            // Buat pesan konfirmasi yang lebih informatif
            let message = '';
            if (checkboxes.length === 1) {
                message = 'Apakah Anda yakin ingin mengirim 1 draft pengajuan?\n\n' +
                         'Draft yang telah dikirim akan diproses oleh sistem dan ' +
                         'tidak dapat diubah kembali.';
            } else {
                message = `Apakah Anda yakin ingin mengirim ${checkboxes.length} draft pengajuan?\n\n` +
                         `Total ${checkboxes.length} pengajuan akan dikirim ke sistem untuk diproses.\n` +
                         'Draft yang telah dikirim tidak dapat diubah kembali.';
            }

            showConfirm(
                message,
                async function() {
                    // Show loading
                    showLoading('Mengirim draft pengajuan...');

                    // Gather selected IDs (may be temporary string IDs for local drafts)
                    const ids = Array.from(checkboxes).map(cb => cb.value);

                    // Save original drafts in case we need to restore
                    const originalDrafts = draftList.slice();

                    // Optimistically remove selected drafts from UI
                    draftList = draftList.filter(d => !ids.includes(d.id));
                    renderDraftTable();

                    // Disable send button and checkboxes to prevent double submit
                    const sendBtn = document.getElementById('kirimDrafBtn');
                    if (sendBtn) sendBtn.disabled = true;
                    const allCheckboxes = document.querySelectorAll('.draft-checkbox');
                    allCheckboxes.forEach(cb => cb.disabled = true);

                    try {
                        const created = [];
                        const failed = [];

                        // Send drafts one by one to allow file upload (create_pengajuan expects FormData)
                        for (const id of ids) {
                            const draft = originalDrafts.find(d => d.id === id);
                            if (!draft) {
                                failed.push(id);
                                continue;
                            }

                            // Build FormData for this draft
                            const fd = new FormData();
                            fd.append('data', JSON.stringify(draft.data));
                            if (draft.file) {
                                fd.append('dokumen_pendukung', draft.file);
                            }
                            // Ask server to create with status TERKIRIM immediately
                            fd.append('initial_status', 'TERKIRIM');

                            try {
                                const resp = await fetch('api/main.php?action=create_pengajuan', {
                                    method: 'POST',
                                    body: fd
                                });
                                const result = await resp.json();
                                console.log('create_pengajuan result for', id, result);
                                if (result && result.success && result.id) {
                                    created.push(result.id);
                                } else {
                                    failed.push(id);
                                }
                            } catch (errInner) {
                                console.error('Error creating pengajuan for', id, errInner);
                                failed.push(id);
                            }
                        }

                        // If there are failed ones, restore them to draftList
                        if (failed.length > 0) {
                            failed.forEach(fid => {
                                const restored = originalDrafts.find(d => d.id === fid);
                                if (restored) draftList.push(restored);
                            });
                            // Sort by insertion order (temp id contains timestamp)
                            draftList.sort((a, b) => {
                                // temp ids are like 'tmp-<ts>-<rnd>' or numeric ids
                                const ta = String(a.id).match(/tmp-(\d+)/); const tb = String(b.id).match(/tmp-(\d+)/);
                                const na = ta ? parseInt(ta[1]) : 0; const nb = tb ? parseInt(tb[1]) : 0;
                                return na - nb;
                            });
                        }

                        // Always refresh server-side riwayat to reflect newly created submissions
                        showLoading('Memuat ulang daftar riwayat dari server...');
                        const remote = await fetchRemoteRiwayat();
                        hideLoading();

                        if (created.length > 0) {
                            document.getElementById('selectAll').checked = false;
                            showCustomAlert('Berhasil mengirim ' + created.length + ' pengajuan.','success','Pengiriman Berhasil');
                        }

                        if (failed.length > 0) {
                            showCustomAlert('Gagal mengirim ' + failed.length + ' pengajuan. Silakan periksa dan coba lagi.','error','Sebagian Gagal');
                        }

                        // Re-enable send button and checkboxes
                        if (sendBtn) sendBtn.disabled = false;
                        allCheckboxes.forEach(cb => cb.disabled = false);
                    } catch (err) {
                        hideLoading();
                        console.error('Error sending drafts:', err);
                        // Restore original drafts on network error
                        draftList = originalDrafts.slice();
                        renderDraftTable();
                        showCustomAlert('Terjadi kesalahan saat mengirim draft', 'error');
                        // Re-enable send button and checkboxes on error
                        const sendBtnErr = document.getElementById('kirimDrafBtn');
                        if (sendBtnErr) sendBtnErr.disabled = false;
                        const allCheckboxesErr = document.querySelectorAll('.draft-checkbox');
                        allCheckboxesErr.forEach(cb => cb.disabled = false);
                    }
                },
                null,
                'Konfirmasi Pengiriman'
            );
        }

        // Format input jumlah diajukan dengan pemisah ribuan
        const jumlahInput = document.getElementById('jumlah_diajukan');
        const jumlahRawInput = document.getElementById('jumlah_diajukan_raw');
        
        jumlahInput.addEventListener('input', function(e) {
            let value = this.value;
            
            // Hapus semua karakter non-digit
            let numericValue = value.replace(/[^0-9]/g, '');
            
            // Simpan nilai mentah
            jumlahRawInput.value = numericValue;
            
            // Format dengan pemisah ribuan
            if (numericValue) {
                let formatted = parseInt(numericValue).toLocaleString('id-ID');
                this.value = formatted;
                
                // Validasi dengan sisa saldo
                const sisaSaldoText = document.getElementById('sisa_saldo').value;
                if (sisaSaldoText) {
                    const sisaSaldo = parseInt(sisaSaldoText.replace(/[^0-9]/g, ''));
                    const jumlahDiajukan = parseInt(numericValue);
                    
                    if (jumlahDiajukan > sisaSaldo) {
                        this.style.color = '#dc3545';
                        this.style.borderColor = '#dc3545';
                    } else {
                        this.style.color = '#28a745';
                        this.style.borderColor = '#28a745';
                    }
                } else {
                    this.style.color = '#1a5490';
                    this.style.borderColor = '#ddd';
                }
            } else {
                this.value = '';
                this.style.color = '#1a5490';
                this.style.borderColor = '#ddd';
            }
        });
        
        // Reset style saat focus
        jumlahInput.addEventListener('focus', function() {
            if (!this.value || this.value === '0') {
                this.style.borderColor = '#1a5490';
            }
        });

        // Dropdown Menu Logic
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('userDropdownBtn'), menu = document.getElementById('userDropdownMenu');
            if(!btn||!menu)return; var wrap=btn.parentNode;
            btn.onclick=function(e){e.stopPropagation();wrap.classList.toggle('open');};
            document.addEventListener('click',function(e){if(!wrap.contains(e.target))wrap.classList.remove('open');});
            document.addEventListener('keydown',function(e){if(e.key==='Escape')wrap.classList.remove('open');});
        });
    </script>

    <!-- Include Alert Modal -->
    <?php include 'includes/alert-modal.php'; ?>
    
    <!-- Detail Modal -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detailModalTitle">Detail Pengajuan</h3>
                <button class="modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <!-- Content will be dynamically inserted -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Revision Modal -->
    <div id="revisionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="revisionModalTitle">Revisi Pengajuan</h3>
                <button class="modal-close" onclick="closeRevisionModal()">&times;</button>
            </div>
            <div class="modal-body" id="revisionModalBody">
                <!-- Content will be dynamically inserted -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRevisionModal()">Batal</button>
                <button type="button" class="btn btn-primary" onclick="confirmRevision()">Kirim Revisi</button>
            </div>
        </div>
    </div>
    
    <!-- Load Alert JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>
