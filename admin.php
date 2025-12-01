<?php

/**
 * SIPANG POLRI - Admin Page
 * Halaman admin dengan data real-time dari pengajuan anggaran
 */

// Require authentication and admin role
require_once 'includes/auth_guard.php';
requireAdmin(); // Only ADMIN_BAGREN and ADMIN_SIKEU can access

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SIPANG POLRI</title>
    <link rel="stylesheet" href="assets/css/admin-style.css?v=1">
    <style>
        /* Main styles are imported from assets/css/admin-style.css */
    </style>
</head>

<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="images/Lambang_Polri.png" alt="Logo POLRI">
                <h1>SIPANG POLRI</h1>
            </div>

            <div class="user-info">
                <div class="user-dropdown-panel">
                    <button id="userDropdownBtn" class="user-dropdown-btn">
                        <?php echo htmlspecialchars($currentUser['nama_lengkap']); ?>
                        <svg class="chevron" style="margin-left:4px;width:17px;vertical-align:middle" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9" fill="none" stroke="#19598F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
                        </svg>
                    </button>
                    <div id="userDropdownMenu" class="user-dropdown-menu">
                        <a href="admin"><span class="icon">⚙️</span> Admin</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout" class="logout"><span class="icon">🚪</span> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="admin-header">
            <h2>📊 Dashboard Admin</h2>
            <p>Tugas Anda: Verifikasi dan setujui pengajuan anggaran yang masuk. Pengajuan yang Anda setujui akan dilanjutkan ke tahap berikutnya.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalPengajuan">0</h3>
                <p>Total Pengajuan</p>
            </div>
            <div class="stat-card">
                <h3 id="pendingCount">0</h3>
                <p>Menunggu Persetujuan</p>
            </div>
            <div class="stat-card">
                <h3 id="approvedCount">0</h3>
                <p>Disetujui</p>
            </div>
            <div class="stat-card">
                <h3 id="rejectedCount">0</h3>
                <p>Ditolak</p>
            </div>
        </div>

        <div class="filter-tabs">
            <div class="tabs">
                <div class="tab active" data-status="all">
                    Semua <span class="tab-count" id="countAll">0</span>
                </div>
                <div class="tab" data-status="TERIMA_BERKAS">
                    Menunggu Persetujuan <span class="tab-count" id="countPending">0</span>
                </div>
                <div class="tab" data-status="TERIMA_SIKEU">
                    Disetujui (Ke Bendahara) <span class="tab-count" id="countApproved">0</span>
                </div>
                <div class="tab" data-status="DITOLAK">
                    Ditolak <span class="tab-count" id="countRejected">0</span>
                </div>
            </div>


        </div>





        <div class="data-table">
            <div class="table-header">
                <h3>📋 Data Pengajuan Anggaran</h3>
                <div class="filter-controls-wrapper">

                    <!-- Filter Bulan (sebelah kanan) -->
                    <div class="filter-container">
                        <span class="filter-label">Filter Bulan</span>
                        <div class="month-filter-wrapper">
                            <select id="monthFilter" class="month-filter" onchange="applyMonthFilter()">
                                <option value="">Semua Bulan</option>
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tableContent">
                <div class="loading">Memuat data...</div>
            </div>
            <!-- Load jQuery for compatibility -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        </div>
    </div>

    <!-- PDF Viewer Modal -->
    <div id="pdfModal" class="pdf-modal">
        <div class="pdf-modal-content">
            <div class="pdf-modal-header">
                <h3>Dokumen Pendukung</h3>
                <button class="pdf-modal-close" onclick="closePDFModal()">&times;</button>
            </div>
            <div class="pdf-modal-body">
                <iframe id="pdfViewer" class="pdf-viewer" src=""></iframe>
            </div>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="rejectionModalTitle">Tolak Pengajuan</h3>
                <button class="modal-close" onclick="closeRejectionModal()">&times;</button>
            </div>
            <div class="modal-body" id="rejectionModalBody">
                <!-- Content will be dynamically inserted -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectionModal()">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmRejection()">Tolak Pengajuan</button>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Penolakan telah dihapus - sekarang langsung ke rejectionModal -->

    <script>
        let pengajuanData = [];
        let currentFilter = 'all';
        let currentMonthFilter = '';

        // Inject user role from PHP
        const userRole = '<?php echo isset($currentUser['role']) ? $currentUser['role'] : ''; ?>';

        // PDF Viewer Functions
        function viewPDF(pengajuanId) {
            if (!pengajuanId) {
                alert('ID pengajuan tidak valid');
                return;
            }

            const modal = document.getElementById('pdfModal');
            const viewer = document.getElementById('pdfViewer');

            // Set PDF source to endpoint
            viewer.src = 'api/get_file.php?id=' + encodeURIComponent(pengajuanId) + '&action=view';

            // Show modal
            modal.style.display = 'block';

            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closePDFModal() {
            const modal = document.getElementById('pdfModal');
            const viewer = document.getElementById('pdfViewer');

            // Hide modal
            modal.style.display = 'none';

            // Clear iframe source
            viewer.src = '';

            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        function closeRejectionModal() {
            const modal = document.getElementById('rejectionModal');

            // Hide modal
            modal.style.display = 'none';
            modal.classList.remove('show');

            // Clear textarea
            const rejectionReason = document.getElementById('rejectionReason');
            if (rejectionReason) {
                rejectionReason.value = '';
            }

            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        // Safe text escaper for HTML injection prevention
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/[&<>"']/g, function(s) {
                switch (s) {
                    case '&':
                        return '&amp;';
                    case '<':
                        return '&lt;';
                    case '>':
                        return '&gt;';
                    case '"':
                        return '&quot;';
                    case "'":
                        return '&#39;';
                    default:
                        return s;
                }
            });
        }

        // Download PDF for SIKEU
        function downloadPDF(pengajuanId) {
            // Create PDF download link
            const downloadUrl = `api/main.php?action=download_pdf&pengajuan_id=${pengajuanId}`;

            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `Pengajuan_${pengajuanId}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success message
            alert('PDF berhasil didownload! File siap untuk dikirim ke pusat.');
        }

        // Download Excel for SIKEU
        function downloadExcel(pengajuanId) {
            // Create Excel download link
            const downloadUrl = `api/main.php?action=download_excel&pengajuan_id=${pengajuanId}`;

            // Create temporary link and trigger download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `Pengajuan_${pengajuanId}.xlsx`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success message
            alert('Excel berhasil didownload! File siap untuk dikirim ke pusat.');
        }

        // Download All PDF for SIKEU
        function downloadAllPDF() {
            if (confirm('Download semua pengajuan dalam format PDF? Ini akan mendownload semua pengajuan yang sudah di-approve.')) {
                // Create bulk PDF download link
                const downloadUrl = `api/main.php?action=download_all_pdf`;

                // Create temporary link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `Semua_Pengajuan_${new Date().toISOString().split('T')[0]}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Show success message
                alert('Semua PDF berhasil didownload! File siap untuk dikirim ke pusat.');
            }
        }

        // Download All Excel for SIKEU
        function downloadAllExcel() {
            if (confirm('Download semua pengajuan dalam format Excel? Ini akan mendownload semua pengajuan yang sudah di-approve.')) {
                // Create bulk Excel download link
                const downloadUrl = `api/main.php?action=download_all_excel`;

                // Create temporary link and trigger download
                const link = document.createElement('a');
                link.href = downloadUrl;
                link.download = `Semua_Pengajuan_${new Date().toISOString().split('T')[0]}.xlsx`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Show success message
                alert('Semua Excel berhasil didownload! File siap untuk dikirim ke pusat.');
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const pdfModal = document.getElementById('pdfModal');
            const rejectionModal = document.getElementById('rejectionModal');

            if (event.target === pdfModal) {
                closePDFModal();
            }
            if (event.target === rejectionModal) {
                closeRejectionModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePDFModal();
                closeRejectionModal();
            }
        });

        // Load data from API
        // Pagination state
        let currentPage = 1;
        const itemsPerPage = 50;
        let totalItems = 0;
        let totalPages = 1;

        async function loadPengajuanData(page = 1) {
            try {
                console.log('Starting to load pengajuan data... (page:', page, ')');
                const offset = (page - 1) * itemsPerPage;
                const url = `api/admin_dashboard.php?action=get_pengajuan_dashboard&limit=${itemsPerPage}&offset=${offset}`;
                console.log('Fetching from:', url);

                // Use admin-specific API endpoint - status already normalized (TERKIRIM → TERIMA BERKAS)
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout

                const response = await fetch(url, {
                    credentials: 'include',
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                console.log('Response status:', response.status, response.statusText);

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                console.log('API Response:', result);

                if (result.success) {
                    pengajuanData = result.data;
                    currentPage = page;

                    // Update pagination info
                    if (result.pagination) {
                        totalItems = result.pagination.total;
                        totalPages = result.pagination.total_pages;
                    }

                    console.log('Loaded pengajuan data from admin API:', pengajuanData.length, 'items');
                    console.log('Status values:', pengajuanData.map(p => p.status));
                    console.log('Pagination:', {
                        totalItems,
                        totalPages,
                        currentPage
                    });
                    updateStatistics();
                    renderTable();
                    return true;
                } else {
                    console.error('Failed to load pengajuan data:', result.message);
                    document.getElementById('tableContent').innerHTML = '<div class="no-data">Gagal memuat data: ' + result.message + '</div>';
                    return false;
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    console.error('Load pengajuan data timeout after 15 seconds');
                    document.getElementById('tableContent').innerHTML = '<div class="no-data">Timeout - Server tidak merespons dalam 15 detik. Silakan refresh halaman.</div>';
                } else {
                    console.error('Error loading pengajuan data:', error);
                    document.getElementById('tableContent').innerHTML = '<div class="no-data">Terjadi kesalahan saat memuat data: ' + error.message + '</div>';
                }
                return false;
            }
        }

        // Apply month filter
        function applyMonthFilter() {
            const monthSelect = document.getElementById('monthFilter');
            currentMonthFilter = monthSelect.value;
            currentPage = 1; // Reset to first page when filtering by month
            // NOTE: Month filter works TOGETHER with status filter, not replacing it
            // If currentFilter is set to specific status, it will filter both by month AND status
            updateStatistics();
            renderTable();
        }

        // Reject pengajuan individual - langsung tampilkan modal
        function rejectPengajuan(id, event) {
            event.stopPropagation();
            showApprovalConfirmation(id, false);
        }

        // Update statistics
        function updateStatistics() {
            let filteredData = pengajuanData;

            // Apply month filter to statistics if selected
            if (currentMonthFilter) {
                filteredData = pengajuanData.filter(item => {
                    const itemMonth = item.bulan ? item.bulan.toString() : '';
                    return itemMonth === currentMonthFilter;
                });
            }

            const total = filteredData.length;
            // Count by actual status from database
            const pending = filteredData.filter(item => item.status === 'TERIMA_BERKAS').length;
            const approved = filteredData.filter(item => item.status === 'TERIMA_SIKEU').length;
            const rejected = filteredData.filter(item => item.status === 'DITOLAK').length;

            document.getElementById('totalPengajuan').textContent = total;
            document.getElementById('pendingCount').textContent = pending;
            document.getElementById('approvedCount').textContent = approved;
            document.getElementById('rejectedCount').textContent = rejected;

            document.getElementById('countAll').textContent = total;
            document.getElementById('countPending').textContent = pending;
            document.getElementById('countApproved').textContent = approved;
            document.getElementById('countRejected').textContent = rejected;
        }

        // Render table
        function renderTable() {
            console.log('🔍 renderTable called - currentFilter:', currentFilter, 'userRole:', userRole);
            console.log('📊 pengajuanData length:', pengajuanData.length);
            console.log('📊 Data statuses:', pengajuanData.map(p => p.status));

            // Filter and sort data
            let filteredData = currentFilter === 'all' ?
                pengajuanData.slice() :
                pengajuanData.filter(item => item.status === currentFilter);

            console.log('✅ filteredData after status filter:', filteredData.length, 'items');

            filteredData.sort((a, b) => {
                const dateA = new Date(a.tanggal_pengajuan || a.tanggal_update || 0);
                const dateB = new Date(b.tanggal_pengajuan || b.tanggal_update || 0);
                return dateB - dateA;
            });

            if (currentMonthFilter) {
                const bulanMapping = {
                    '01': 'Januari',
                    '02': 'Februari',
                    '03': 'Maret',
                    '04': 'April',
                    '05': 'Mei',
                    '06': 'Juni',
                    '07': 'Juli',
                    '08': 'Agustus',
                    '09': 'September',
                    '10': 'Oktober',
                    '11': 'November',
                    '12': 'Desember'
                };
                const namaBulan = bulanMapping[currentMonthFilter];
                filteredData = filteredData.filter(item => item.bulan_pengajuan === namaBulan);
            }

            if (filteredData.length === 0) {
                document.getElementById('tableContent').innerHTML = '<div class="no-data">Tidak ada data</div>';
                return;
            }

            // Group filtered data for display
            const grouped = {};
            filteredData.forEach(item => {
                const key = String(item.user_id);
                if (!grouped[key]) grouped[key] = {
                    user: `${item.nama_lengkap} (${item.role})`,
                    items: []
                };
                grouped[key].items.push(item);
            });

            const groups = Object.values(grouped).sort((a, b) => {
                const da = new Date(a.items[0].tanggal_pengajuan || a.items[0].tanggal_update || 0);
                const db = new Date(b.items[0].tanggal_pengajuan || b.items[0].tanggal_update || 0);
                return db - da;
            });

            // Build HTML
            const rows = [];
            rows.push('<table><thead><tr><th>Tanggal</th><th>Pengguna</th><th>Kode</th><th>Program/Kegiatan</th><th>Jumlah</th><th>Status</th><th>Aksi</th></tr></thead><tbody>');

            groups.forEach(group => {
                const first = group.items[0];
                const total = group.items.reduce((s, it) => s + parseFloat(it.jumlah_diajukan || 0), 0);
                const detailId = 'detail-user-' + first.user_id;

                // main row
                rows.push('<tr class="main-row ' + (isProcessedRow(first.status) ? 'processed-row' : '') + '" data-detail-id="' + detailId + '">');
                rows.push('<td>' + formatDate(first.tanggal_pengajuan || first.tanggal_update) + '</td>');
                rows.push('<td>' + escapeHtml(group.user) + '</td>');
                rows.push('<td>' + escapeHtml(first.kode || '-') + '</td>');
                rows.push('<td><div class="program-title"><div class="judul-kegiatan">' + (group.items.length > 1 ? (group.items.length + ' Pengajuan') : escapeHtml(first.uraian || first.nama_kegiatan || '-')) + '</div>' + (group.items.length === 1 ? '<div class="nama-program">' + escapeHtml(first.nama_kegiatan || '-') + '</div>' : '') + '</div></td>');
                rows.push('<td>' + formatRupiah(total) + '</td>');
                rows.push('<td><span class="status-badge ' + getStatusClass(first.status) + '">' + escapeHtml(first.status) + '</span></td>');
                rows.push('<td><div class="action-buttons">' + getActionButtons(first.status, first.id, detailId) + '</div></td>');
                rows.push('</tr>');

                // detail row
                rows.push('<tr class="detail-row" id="' + detailId + '"><td colspan="7"><div class="detail-content"><h4>Detail Pengajuan (' + group.items.length + ' item)</h4><div class="detail-grid">');

                // items
                group.items.slice().sort((a, b) => new Date(b.tanggal_pengajuan || b.tanggal_update || 0) - new Date(a.tanggal_pengajuan || a.tanggal_update || 0)).forEach(item => {
                    rows.push('<div class="detail-item" style="border-left:4px solid #234e80;padding:12px;background:#f8f9fa;border-radius:4px;margin-bottom:12px;">');
                    rows.push('<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px;"><div><h4 style="margin:0 0 5px 0;color:#234e80;font-size:14px;">Pengajuan #' + item.id + '</h4><span class="status-badge ' + (getStatusClass(item.status)) + '" style="display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;">' + escapeHtml(item.status) + '</span></div></div>');
                    rows.push('<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:10px;font-size:13px;"><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Tanggal</label><span style="color:#333;">' + formatDate(item.tanggal_pengajuan || item.tanggal_update || new Date().toISOString()) + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Bulan</label><span style="color:#333;">' + escapeHtml(item.bulan_pengajuan || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Tahun</label><span style="color:#333;">' + escapeHtml(item.tahun_pengajuan || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Sumber Dana</label><span style="color:#333;">' + escapeHtml(item.sumber_dana || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Program</label><span style="color:#333;">' + escapeHtml(item.nama_kegiatan || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Kode</label><span style="color:#333;">' + escapeHtml(item.kode || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Diajukan</label><span style="color:#333;font-weight:500;">' + formatRupiah(item.jumlah_diajukan || 0) + '</span></div></div>');
                    rows.push('<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:10px;font-size:13px;"><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">PJ</label><span style="color:#333;">' + escapeHtml(item.penanggung_jawab || '-') + '</span></div><div><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Bendahara</label><span style="color:#333;">' + escapeHtml(item.bendahara_pengeluaran_pembantu || '-') + '</span></div></div>');
                    rows.push('<div style="margin-bottom:8px;font-size:13px;"><label style="display:block;color:#666;font-weight:600;margin-bottom:3px;">Uraian</label><span style="color:#333;display:block;word-wrap:break-word;">' + escapeHtml(item.uraian || item.nama_kegiatan || '-') + '</span></div>');
                    if (item.status === 'DITOLAK' && (item.keterangan || item.rejected_reason)) {
                        rows.push('<div style="background:#fff3cd;border-left:3px solid #ffc107;padding:8px;margin-bottom:8px;border-radius:3px;font-size:13px;"><strong style="color:#856404;">Catatan Penolakan:</strong> <span style="color:#856404;">' + escapeHtml(item.keterangan || item.rejected_reason) + '</span></div>');
                    }
                    rows.push('<div style="margin-bottom:8px;font-size:13px;"><label style="display:block;color:#666;font-weight:600;margin-bottom:5px;">Dokumen Pendukung</label>');
                    if (item.file_path || item.id) {
                        rows.push('<div style="display:flex;gap:8px;"><a href="api/get_file.php?id=' + item.id + '&action=download" style="display:inline-block;padding:6px 12px;background:#007bff;color:white;border-radius:3px;text-decoration:none;">📥 Download</a><button onclick="viewPDF(' + item.id + ')" style="display:inline-block;padding:6px 12px;background:#28a745;color:white;border:none;border-radius:3px;cursor:pointer;">👁️ View</button></div>');
                    } else {
                        rows.push('<span style="color:#999;">Tidak ada dokumen</span>');
                    }
                    rows.push('</div>');

                    // Determine which buttons to show based on status and user role
                    const userRole = '<?php echo $currentUser['role']; ?>';
                    let shouldShowButtons = false;
                    let buttonLabel = '';

                    // ADMIN_BAGREN: can approve/reject TERIMA_BERKAS (pending)
                    if (userRole === 'ADMIN_BAGREN' && item.status === 'TERIMA_BERKAS') {
                        shouldShowButtons = true;
                        buttonLabel = 'Setujui & Tolak';
                    }
                    // ADMIN_SIKEU: can process TERIMA_SIKEU (approved by BAGREN, ready for payment)
                    else if (userRole === 'ADMIN_SIKEU' && item.status === 'TERIMA_SIKEU') {
                        shouldShowButtons = true;
                        buttonLabel = 'Bayarkan & Tolak';
                    }

                    if (shouldShowButtons) {
                        rows.push('<div style="margin-top:12px;display:flex;gap:8px;border-top:1px solid #ddd;padding-top:12px;">');
                        if (userRole === 'ADMIN_SIKEU') {
                            // ADMIN_SIKEU: Process for payment or reject
                            rows.push('<button class="btn btn-success" onclick="approvePengajuan(' + item.id + ', event)" style="padding:6px 12px;background:#28a745;color:white;border:none;border-radius:3px;cursor:pointer;">💰 Bayarkan</button>');
                            rows.push('<button class="btn btn-danger" onclick="rejectPengajuan(' + item.id + ', event)" style="padding:6px 12px;background:#dc3545;color:white;border:none;border-radius:3px;cursor:pointer;">❌ Tolak</button>');
                        } else {
                            // ADMIN_BAGREN: Approve or reject
                            rows.push('<button class="btn btn-success" onclick="approvePengajuan(' + item.id + ', event)" style="padding:6px 12px;background:#28a745;color:white;border:none;border-radius:3px;cursor:pointer;">✅ Setujui</button>');
                            rows.push('<button class="btn btn-danger" onclick="rejectPengajuan(' + item.id + ', event)" style="padding:6px 12px;background:#dc3545;color:white;border:none;border-radius:3px;cursor:pointer;">❌ Tolak</button>');
                        }
                        rows.push('</div>');
                    } else {
                        // Show status badge for processed pengajuan - buttons HIDDEN
                        let statusIcon = '';
                        let statusText = item.status;
                        let statusBg = '#f0f0f0';
                        let statusColor = '#666';

                        // Map all possible statuses with labels and colors
                        if (item.status === 'DRAFT') {
                            statusIcon = '📝';
                            statusText = 'Draft';
                            statusBg = '#f5f5f5';
                            statusColor = '#999';
                        } else if (item.status === 'TERKIRIM') {
                            statusIcon = '📤';
                            statusText = 'Terkirim';
                            statusBg = '#fff3cd';
                            statusColor = '#856404';
                        } else if (item.status === 'TERIMA_BERKAS') {
                            statusIcon = '📥';
                            statusText = 'Terima Berkas';
                            statusBg = '#fff3cd';
                            statusColor = '#856404';
                        } else if (item.status === 'DISPOSISI_KABAG_REN') {
                            statusIcon = '📋';
                            statusText = 'Disposisi Kabag REN';
                            statusBg = '#e3f2fd';
                            statusColor = '#1976d2';
                        } else if (item.status === 'DISPOSISI_WAKA') {
                            statusIcon = '📋';
                            statusText = 'Disposisi WAKA';
                            statusBg = '#e3f2fd';
                            statusColor = '#1976d2';
                        } else if (item.status === 'TERIMA_SIKEU') {
                            statusIcon = '✅';
                            statusText = 'Disetujui & Menunggu Pembayaran';
                            statusBg = '#e8f5e9';
                            statusColor = '#2e7d32';
                        } else if (item.status === 'DIBAYARKAN') {
                            statusIcon = '💰';
                            statusText = 'Dibayarkan';
                            statusBg = '#f3e5f5';
                            statusColor = '#6a1b9a';
                        } else if (item.status === 'DITOLAK') {
                            statusIcon = '❌';
                            statusText = 'Ditolak';
                            statusBg = '#ffebee';
                            statusColor = '#c62828';
                        }

                        rows.push('<div style="margin-top:12px;padding-top:12px;border-top:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">');
                        rows.push('<div style="background:' + statusBg + ';padding:10px;border-radius:3px;border-left:3px solid ' + statusColor + ';color:' + statusColor + ';font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px;flex:1;">');
                        rows.push(statusIcon + ' <span><strong>' + statusText + '</strong></span> <span style="font-size:12px;opacity:0.8;">Sudah diproses</span>');
                        rows.push('</div>');

                        // Add delete button for demo (ONLY FOR TESTING)
                        rows.push('<button class="btn btn-demo-delete" onclick="deletePengajuan(' + item.id + ', event)" style="margin-left:8px;padding:6px 10px;background:#ff6b6b;color:white;border:none;border-radius:3px;cursor:pointer;font-size:12px;transition:all 0.2s;" title="[DEMO] Hapus pengajuan ini">🗑️ Hapus</button>');
                        rows.push('</div>');
                    }

                    rows.push('</div>');
                });

                rows.push('</div></div></td></tr>');
            });

            rows.push('</tbody></table>');

            // Add pagination controls
            rows.push('<div style="display:flex;justify-content:center;align-items:center;gap:12px;padding:20px;background:#f8f9fa;border-top:1px solid #dee2e6;margin-top:20px;border-radius:0 0 10px 10px;">');

            // Previous button
            if (currentPage > 1) {
                rows.push('<button onclick="loadPengajuanData(' + (currentPage - 1) + ')" class="btn" style="padding:8px 16px;background:#1a5490;color:white;border:none;border-radius:5px;cursor:pointer;transition:all 0.3s;">← Sebelumnya</button>');
            } else {
                rows.push('<button disabled class="btn" style="padding:8px 16px;background:#ccc;color:#666;border:none;border-radius:5px;cursor:not-allowed;opacity:0.6;">← Sebelumnya</button>');
            }

            // Page info
            rows.push('<span style="color:#666;font-weight:600;min-width:150px;text-align:center;">Halaman ' + currentPage + ' dari ' + totalPages + ' (' + totalItems + ' item)</span>');

            // Next button
            if (currentPage < totalPages) {
                rows.push('<button onclick="loadPengajuanData(' + (currentPage + 1) + ')" class="btn" style="padding:8px 16px;background:#1a5490;color:white;border:none;border-radius:5px;cursor:pointer;transition:all 0.3s;">Berikutnya →</button>');
            } else {
                rows.push('<button disabled class="btn" style="padding:8px 16px;background:#ccc;color:#666;border:none;border-radius:5px;cursor:not-allowed;opacity:0.6;">Berikutnya →</button>');
            }

            rows.push('</div>');

            const html = rows.join('');
            document.getElementById('tableContent').innerHTML = html;

            // Re-attach event listeners setelah render ulang
            initializeDetailRowListeners();
        }

        // Initialize event listeners untuk detail rows setelah render
        function initializeDetailRowListeners() {
            // Attach click handlers ke expand icons
            document.querySelectorAll('[id^="expand-detail-user-"]').forEach(expandIcon => {
                const detailId = expandIcon.id.replace('expand-', '');
                expandIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleDetail(detailId);
                });
            });

            // Attach click handlers ke main rows
            document.querySelectorAll('tr.main-row').forEach(mainRow => {
                mainRow.addEventListener('click', function(e) {
                    // Jangan trigger jika click pada button
                    if (e.target.closest('button')) {
                        return;
                    }
                    const detailId = this.getAttribute('data-detail-id');
                    if (detailId) {
                        toggleDetail(detailId);
                    }
                });
            });
        }

        // Check if row is processed
        function isProcessedRow(status) {
            const processedStatuses = ['DISPOSISI KABAG REN', 'DISPOSISI WAKA', 'TERIMA SIKEU', 'DIBAYARKAN', 'DITOLAK'];
            return processedStatuses.includes(status);
        }

        // Get action buttons based on status and user role
        function getActionButtons(status, pengajuanId, detailId) {
            let buttons = `<button class="btn btn-info" onclick="toggleDetail('${detailId}'); return false;" style="cursor:pointer;">📋 Detail</button>`;

            // BAGREN: ACC/TOLAK untuk TERIMA_BERKAS
            if (userRole === 'ADMIN_BAGREN' && status === 'TERIMA_BERKAS') {
                buttons += ` <button class="btn btn-success" onclick="approvePengajuan(${pengajuanId}, event)" style="cursor:pointer;">✓ ACC</button>`;
                buttons += ` <button class="btn btn-danger" onclick="rejectPengajuan(${pengajuanId}, event)" style="cursor:pointer;">✕ TOLAK</button>`;
            }

            // SIKEU: BAYARKAN/TOLAK untuk TERIMA_SIKEU
            if (userRole === 'ADMIN_SIKEU' && status === 'TERIMA_SIKEU') {
                buttons += ` <button class="btn btn-success" onclick="approvePengajuan(${pengajuanId}, event)" style="cursor:pointer;">💰 Bayarkan</button>`;
                buttons += ` <button class="btn btn-danger" onclick="rejectPengajuan(${pengajuanId}, event)" style="cursor:pointer;">✕ TOLAK</button>`;
            }

            return buttons;
        }

        // Format rupiah helper function
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Toggle detail row
        function toggleDetail(detailId) {
            if (!detailId) {
                console.error('❌ toggleDetail: detailId tidak valid');
                return;
            }

            const detailRow = document.getElementById(detailId);
            if (!detailRow) {
                console.error('❌ Detail row tidak ditemukan:', detailId);
                return;
            }

            const isExpanded = detailRow.classList.contains('show');

            // Close all other details dulu
            document.querySelectorAll('.detail-row.show').forEach(row => {
                row.classList.remove('show');
            });

            // Toggle current detail
            if (!isExpanded) {
                console.log('📖 Membuka detail:', detailId);
                detailRow.classList.add('show');
            } else {
                console.log('📖 Menutup detail:', detailId);
                detailRow.classList.remove('show');
            }
        }

        // View detail (legacy function kept for compatibility)
        function viewDetail(detailId, event) {
            event.stopPropagation();
            toggleDetail(detailId);
        }

        // Handle clicking outside detail to close it
        // Menggunakan capture phase untuk intercept sebelum event propagate
        document.addEventListener('click', function(e) {
            // Jika click pada detail content sendiri, jangan tutup
            if (e.target.closest('.detail-content')) {
                return;
            }

            // Jika click pada button atau link di dalam tabel, biarkan handler mereka
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            // Jika click pada row yang masih bagian dari tabel utama (bukan detail row)
            const clickedRow = e.target.closest('tr');
            if (clickedRow && !clickedRow.classList.contains('detail-row')) {
                // Jangan tutup detail di sini - biarkan row onclick handler yang handle
                return;
            }

            // Jika click di area lain (bukan tabel sama sekali), tutup semua detail
            if (!e.target.closest('table')) {
                document.querySelectorAll('.detail-row.show').forEach(row => {
                    row.classList.remove('show');
                });
            }
        }, true); // Menggunakan capture phase

        // View detail (same as toggle)
        function viewDetail(detailId, event) {
            event.stopPropagation();
            toggleDetail(detailId);
        }

        // Approve pengajuan - INDIVIDUAL
        async function approvePengajuan(id, event) {
            event.stopPropagation();
            showApprovalConfirmation(id, true);
        }

        // Reject pengajuan individual - langsung tampilkan modal
        function rejectPengajuan(id, event) {
            event.stopPropagation();

            // Tampilkan modal rejection untuk individual
            showRejectionModalIndividual(id);
        }

        // Toggle selection dropdown
        function toggleSelectionDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('selectionDropdownMenu');
            const isVisible = dropdown.classList.contains('show');

            // Close all dropdowns
            document.querySelectorAll('.selection-dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });

            // Toggle current dropdown
            if (!isVisible) {
                dropdown.classList.add('show');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.selection-dropdown-container')) {
                document.querySelectorAll('.selection-dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });







        // Show custom alert notification
        function showCustomAlert(message, type = 'info') {
            // Create alert element
            const alertDiv = document.createElement('div');
            alertDiv.className = `custom-alert alert-${type}`;
            alertDiv.innerHTML = `
                <div class="alert-content">
                    <span class="alert-icon">${type === 'success' ? '✅' : type === 'error' ? '❌' : type === 'warning' ? '⚠️' : 'ℹ️'}</span>
                    <span class="alert-message">${message}</span>
                    <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
                </div>
            `;

            // Add styles
            if (!document.getElementById('alertStyles')) {
                const style = document.createElement('style');
                style.id = 'alertStyles';
                style.textContent = `
                    .custom-alert {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 9999;
                        background: white;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        padding: 1rem;
                        min-width: 300px;
                        animation: slideInRight 0.3s ease;
                    }
                    .custom-alert.alert-success { border-left: 4px solid #28a745; }
                    .custom-alert.alert-error { border-left: 4px solid #dc3545; }
                    .custom-alert.alert-warning { border-left: 4px solid #ffc107; }
                    .custom-alert.alert-info { border-left: 4px solid #17a2b8; }
                    .alert-content { display: flex; align-items: center; gap: 0.5rem; }
                    .alert-icon { font-size: 1.2rem; }
                    .alert-message { flex: 1; font-size: 0.9rem; }
                    .alert-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; color: #666; }
                    .alert-close:hover { color: #333; }
                    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
                `;
                document.head.appendChild(style);
            }

            // Add to page
            document.body.appendChild(alertDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        // Show loading indicator
        function showLoading(message = 'Loading...') {
            const loadingDiv = document.createElement('div');
            loadingDiv.id = 'loadingOverlay';
            loadingDiv.className = 'loading-overlay';
            loadingDiv.innerHTML = `
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">${message}</div>
                </div>
            `;

            // Add styles if not exists
            if (!document.getElementById('loadingStyles')) {
                const style = document.createElement('style');
                style.id = 'loadingStyles';
                style.textContent = `
                    .loading-overlay {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0,0,0,0.5);
                        z-index: 9999;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                    .loading-content {
                        background: white;
                        padding: 2rem;
                        border-radius: 10px;
                        text-align: center;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    }
                    .loading-spinner {
                        width: 40px;
                        height: 40px;
                        border: 4px solid #f3f3f3;
                        border-top: 4px solid #1a5490;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                        margin: 0 auto 1rem;
                    }
                    .loading-message {
                        color: #333;
                        font-size: 1rem;
                    }
                    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(loadingDiv);
        }

        // Hide loading indicator
        function hideLoading() {
            const loadingDiv = document.getElementById('loadingOverlay');
            if (loadingDiv) {
                loadingDiv.remove();
            }
        }











        // Show rejection modal dengan animasi yang lebih keren
        function showRejectionModal(itemCount, pengajuanIds) {
            const modal = document.getElementById('rejectionModal');
            const modalTitle = document.getElementById('rejectionModalTitle');
            const modalBody = document.getElementById('rejectionModalBody');
            const rejectionReason = document.getElementById('rejectionReason');

            modalTitle.textContent = `Tolak ${itemCount} Pengajuan`;
            modalBody.innerHTML = `
                <div class="rejection-info">
                    <h4>⚠️ Konfirmasi Penolakan</h4>
                    <p>Anda akan menolak <strong>${itemCount} pengajuan</strong> dalam grup ini.</p>
                    <p><strong>Catatan:</strong> Alasan penolakan akan dikirim ke user untuk membantu mereka melakukan revisi.</p>
                </div>
                
                <div class="form-group">
                    <label for="rejectionReason">📝 Alasan Penolakan <span class="required">*</span></label>
                    <textarea id="rejectionReason" rows="4" placeholder="Contoh: Jumlah terlalu besar, maksimal Rp 2.000.000. Atau: Dokumen tidak lengkap, mohon upload dokumen pendukung yang valid..." maxlength="500" required></textarea>
                    <small class="form-help">
                        <strong>Tips:</strong> Berikan alasan yang jelas dan konstruktif agar user dapat memperbaiki pengajuan dengan tepat.<br>
                        Contoh: "Jumlah melebihi anggaran yang tersedia", "Dokumen pendukung tidak sesuai", "Format pengajuan perlu diperbaiki"
                    </small>
                    <div style="margin-top:8px;font-size:12px;color:#666;display:flex;justify-content:space-between;padding-top:5px;border-top:1px solid #e0e0e0;">
                        <span id="charCounter">0 / 500 karakter</span>
                        <span id="validationStatus" style="color:#999;"></span>
                    </div>
                </div>
            `;

            // Tambahkan animasi fade in
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
                // Setup character counter
                const textarea = document.getElementById('rejectionReason');
                if (textarea) {
                    textarea.addEventListener('input', function() {
                        const count = this.value.length;
                        const counter = document.getElementById('charCounter');
                        const status = document.getElementById('validationStatus');
                        if (counter) counter.textContent = `${count} / 500 karakter`;
                        if (status) {
                            if (count === 0) {
                                status.textContent = '⚠️ Wajib diisi';
                                status.style.color = '#dc3545';
                            } else if (count < 10) {
                                status.textContent = '📝 Minimum 10 karakter';
                                status.style.color = '#ffc107';
                            } else {
                                status.textContent = '✅ Baik';
                                status.style.color = '#28a745';
                            }
                        }
                    });
                    textarea.focus();
                }
            }, 10);

            document.body.style.overflow = 'hidden';

            // Store pengajuan IDs for later use
            modal.dataset.pengajuanIds = JSON.stringify(pengajuanIds);
            modal.dataset.mode = 'group';
        }

        // Show rejection modal for individual pengajuan
        function showRejectionModalIndividual(pengajuanId) {
            const modal = document.getElementById('rejectionModal');
            const modalTitle = document.getElementById('rejectionModalTitle');
            const modalBody = document.getElementById('rejectionModalBody');

            modalTitle.textContent = 'Tolak Pengajuan';
            modalBody.innerHTML = `
                <div class="rejection-info">
                    <h4>⚠️ Konfirmasi Penolakan</h4>
                    <p>Anda akan menolak <strong>1 pengajuan</strong>.</p>
                    <p><strong>Catatan:</strong> Alasan penolakan akan dikirim ke user untuk membantu mereka melakukan revisi.</p>
                </div>
                
                <div class="form-group">
                    <label for="rejectionReason">📝 Alasan Penolakan <span class="required">*</span></label>
                    <textarea id="rejectionReason" rows="4" placeholder="Contoh: Jumlah terlalu besar, maksimal Rp 2.000.000. Atau: Dokumen tidak lengkap, mohon upload dokumen pendukung yang valid..." maxlength="500" required></textarea>
                    <small class="form-help">
                        <strong>Tips:</strong> Berikan alasan yang jelas dan konstruktif agar user dapat memperbaiki pengajuan dengan tepat.<br>
                        Contoh: "Jumlah melebihi anggaran yang tersedia", "Dokumen pendukung tidak sesuai", "Format pengajuan perlu diperbaiki"
                    </small>
                    <div style="margin-top:8px;font-size:12px;color:#666;display:flex;justify-content:space-between;padding-top:5px;border-top:1px solid #e0e0e0;">
                        <span id="charCounter">0 / 500 karakter</span>
                        <span id="validationStatus" style="color:#999;"></span>
                    </div>
                </div>
            `;

            // Tambahkan animasi fade in
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
                // Setup character counter
                const textarea = document.getElementById('rejectionReason');
                if (textarea) {
                    textarea.addEventListener('input', function() {
                        const count = this.value.length;
                        const counter = document.getElementById('charCounter');
                        const status = document.getElementById('validationStatus');
                        if (counter) counter.textContent = `${count} / 500 karakter`;
                        if (status) {
                            if (count === 0) {
                                status.textContent = '⚠️ Wajib diisi';
                                status.style.color = '#dc3545';
                            } else if (count < 10) {
                                status.textContent = '📝 Minimum 10 karakter';
                                status.style.color = '#ffc107';
                            } else {
                                status.textContent = '✅ Baik';
                                status.style.color = '#28a745';
                            }
                        }
                    });
                    textarea.focus();
                }
            }, 10);

            document.body.style.overflow = 'hidden';

            // Store pengajuan ID for later use
            modal.dataset.pengajuanIds = JSON.stringify([pengajuanId]);
            modal.dataset.mode = 'individual';
        }


        // Confirm rejection dengan loading animation
        async function confirmRejection() {
            const modal = document.getElementById('rejectionModal');
            const mode = modal.dataset.mode;
            const reason = document.getElementById('rejectionReason').value.trim();

            // Validation: Alasan harus ada dan minimal 10 karakter
            if (!reason) {
                showCustomAlert('⚠️ Alasan penolakan wajib diisi', 'warning');
                document.getElementById('rejectionReason').focus();
                return;
            }

            if (reason.length < 10) {
                showCustomAlert('⚠️ Alasan penolakan minimal 10 karakter. Saat ini: ' + reason.length + ' karakter', 'warning');
                document.getElementById('rejectionReason').focus();
                return;
            }

            if (reason.length > 500) {
                showCustomAlert('⚠️ Alasan penolakan maksimal 500 karakter', 'warning');
                return;
            }

            showLoading('Menolak pengajuan...');

            try {
                // Penolakan INDIVIDUAL saja
                const pengajuanIds = JSON.parse(modal.dataset.pengajuanIds);
                const pengajuanId = pengajuanIds[0];

                const response = await fetch('api/main.php?action=reject_pengajuan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pengajuan_id: pengajuanId,
                        keterangan: reason
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                if (result.success) {
                    showCustomAlert('✅ Pengajuan berhasil ditolak', 'success');
                    closeRejectionModal();
                    // Reload data dengan timeout safety
                    await Promise.race([
                        loadPengajuanData(),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout loading data')), 10000))
                    ]);
                    renderTable();
                } else {
                    showCustomAlert('❌ Gagal menolak pengajuan: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error rejecting pengajuan:', error);
                showCustomAlert('❌ Terjadi kesalahan: ' + error.message, 'error');
            } finally {
                hideLoading();
            }
        }

        // [DEMO FEATURE] Delete pengajuan - ONLY for ADMIN_BAGREN testing
        async function deletePengajuan(pengajuanId, event) {
            event.stopPropagation();

            // Konfirmasi dengan user
            const confirm = window.confirm('⚠️ DEMO FEATURE: Yakin ingin MENGHAPUS pengajuan #' + pengajuanId + '?\n\nTindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait.');
            if (!confirm) return;

            showLoading('Menghapus pengajuan...');

            try {
                const response = await fetch('api/main.php?action=delete_pengajuan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pengajuan_id: pengajuanId
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();
                console.log('Delete response:', result);

                if (result.success) {
                    showCustomAlert('✅ Pengajuan berhasil dihapus', 'success');
                    // Reload data dengan timeout safety
                    await Promise.race([
                        loadPengajuanData(),
                        new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout loading data')), 10000))
                    ]);
                } else {
                    showCustomAlert('❌ Gagal menghapus pengajuan: ' + result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting pengajuan:', error);
                showCustomAlert('❌ Terjadi kesalahan saat menghapus pengajuan: ' + error.message, 'error');
            } finally {
                hideLoading();
            }
        }

        // Filter by status
        function filterByStatus(status) {
            currentFilter = status;
            currentPage = 1; // Reset to first page when filtering

            // Update active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-status="${status}"]`).classList.add('active');

            updateStatistics();
            renderTable();
        }

        // Get status class
        function getStatusClass(status) {
            const statusMap = {
                'DRAFT': 'status-draft',
                'TERKIRIM': 'status-terima-berkas status-pending',
                'TERIMA_BERKAS': 'status-terima-berkas status-pending',
                'DISPOSISI_KABAG_REN': 'status-disposisi-kabag',
                'DISPOSISI_WAKA': 'status-disposisi-waka',
                'TERIMA_SIKEU': 'status-terima-sikeu',
                'DIBAYARKAN': 'status-dibayarkan',
                'DITOLAK': 'status-ditolak'
            };
            return statusMap[status] || 'status-pending';
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        // Delete pengajuan function - DINONAKTIFKAN
        async function deletePengajuan(pengajuanId, event) {
            event.stopPropagation();

            // Tampilkan pesan bahwa fitur delete dinonaktifkan
            showCustomAlert('Fitur delete pengajuan telah dinonaktifkan', 'warning');
            return;

            /* Kode delete asli - dinonaktifkan
            // Konfirmasi penghapusan
            if (!confirm('Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }
            
            // Tampilkan loading dengan notifikasi sederhana
            const loadingMsg = document.createElement('div');
            loadingMsg.id = 'deleteLoadingMsg';
            loadingMsg.className = 'custom-notification info show';
            loadingMsg.innerHTML = `
                <div class="notification-icon">⏳</div>
                <div class="notification-content">
                    <div class="notification-title">Menghapus</div>
                    <div class="notification-message">Menghapus pengajuan...</div>
                </div>
            `;
            document.body.appendChild(loadingMsg);
            
            try {
                const response = await fetch('api/main.php?action=delete_pengajuan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pengajuan_id: pengajuanId
                    })
                });
                
                const result = await response.json();
                
                // Hapus loading message
                if (loadingMsg) loadingMsg.remove();
                
                if (result.success) {
                    showCustomAlert('Pengajuan berhasil dihapus', 'success');
                    loadPengajuanData(); // Reload data
                } else {
                    showCustomAlert('Gagal menghapus pengajuan: ' + result.message, 'error');
                }
            } catch (error) {
                // Hapus loading message jika error
                if (loadingMsg) loadingMsg.remove();
                console.error('Error deleting pengajuan:', error);
                showCustomAlert('Terjadi kesalahan saat menghapus pengajuan', 'error');
            }
            */
        }

        // Format currency
        function formatRupiah(amount) {
            return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        }

        // Delete pengajuan function
        async function deletePengajuan(pengajuanId, event) {
            event.stopPropagation();

            if (confirm('Apakah Anda yakin ingin menghapus pengajuan ini? Data yang dihapus tidak dapat dikembalikan.')) {
                showLoading('Menghapus pengajuan...');

                try {
                    const response = await fetch('api/main.php?action=delete_pengajuan', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            pengajuan_id: pengajuanId
                        })
                    });

                    const result = await response.json();
                    if (result.success) {
                        hideLoading();
                        showNotification('Sukses', 'Pengajuan berhasil dihapus', 'success');
                        loadPengajuanData(); // Reload data
                    } else {
                        hideLoading();
                        showNotification('Error', 'Gagal menghapus pengajuan: ' + result.message, 'error');
                    }
                } catch (error) {
                    hideLoading();
                    console.error('Error deleting pengajuan:', error);
                    showNotification('Error', 'Terjadi kesalahan saat menghapus pengajuan', 'error');
                }
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', () => {
                    const status = tab.getAttribute('data-status');
                    filterByStatus(status);
                });
            });
        }

        // Initialize
        window.onload = function() {
            // Auto-set filter based on user role
            if (userRole === 'ADMIN_BAGREN') {
                currentFilter = 'TERIMA_BERKAS';
            } else if (userRole === 'ADMIN_SIKEU') {
                currentFilter = 'TERIMA_SIKEU';
            }

            loadPengajuanData();
            setupEventListeners();

            // Auto refresh every 30 seconds
            setInterval(loadPengajuanData, 30000);
        };

        // Attach generic detail-open behavior for admin table rows (if they have data-id)
        document.addEventListener('click', function(e) {
            // ignore clicks on buttons/links inside rows
            if (e.target.closest('button, a, input, svg')) return;
            const tr = e.target.closest('tr[data-id]');
            if (tr) {
                const id = tr.getAttribute('data-id');
                // If showDetailRow is available (from riwayat.js), use it, otherwise fallback to inline fetch
                if (typeof showDetailRow === 'function') {
                    showDetailRow(id, tr);
                } else {
                    // Simple fallback: open a small inline detail using fetch
                    (async function() {
                        try {
                            const res = await fetch('api/get_pengajuan_detail.php?id=' + encodeURIComponent(id));
                            const data = await res.json();
                            // Basic alert fallback
                            alert('Nomor: ' + (data.nomor_surat || data.nomor || '-') + '\n' + 'Uraian: ' + (data.uraian || '-'));
                        } catch (err) {
                            console.error(err);
                        }
                    })();
                }
            }
        });

        // ========== Custom Confirmation Modal ==========
        let confirmationState = {
            pengajuanId: null,
            action: null,
            isApprove: true
        };

        function showApprovalConfirmation(pengajuanId, isApprove = true) {
            const pengajuan = pengajuanData.find(p => p.id == pengajuanId);
            if (!pengajuan) return;

            confirmationState.pengajuanId = pengajuanId;
            confirmationState.isApprove = isApprove;

            const modal = document.getElementById('approvalConfirmationModal');
            const title = document.getElementById('confirmTitle');
            const message = document.getElementById('confirmMessage');
            const okButton = document.getElementById('confirmOkBtn');
            const cancelButton = document.getElementById('confirmCancelBtn');
            const userRole = '<?php echo $currentUser['role']; ?>';

            // Set title and message based on action
            if (isApprove) {
                title.textContent = 'Konfirmasi Pengajuan';
                if (userRole === 'ADMIN_SIKEU') {
                    message.textContent = 'Apakah Anda yakin ingin memproses pembayaran untuk pengajuan ini?\n\nPembayaran akan diproses oleh sistem dan tidak dapat dibatalkan.';
                } else {
                    message.textContent = 'Apakah Anda yakin ingin menyetujui pengajuan ini?\n\nPengajuan akan diteruskan ke bagian SIKEU untuk proses pembayaran.';
                }
                okButton.textContent = 'Setuju';
                okButton.style.background = 'linear-gradient(135deg, #17a2b8 0%, #138496 100%)';
            } else {
                title.textContent = 'Konfirmasi Penolakan';
                message.textContent = 'Apakah Anda yakin ingin menolak pengajuan ini?\n\nAnda harus memberikan alasan penolakan.';
                okButton.textContent = 'Lanjutkan';
                okButton.style.background = 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)';
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeApprovalConfirmation() {
            const modal = document.getElementById('approvalConfirmationModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            confirmationState.pengajuanId = null;
            confirmationState.isApprove = true;
        }

        async function handleConfirmationOK() {
            const pengajuanId = confirmationState.pengajuanId;
            const isApprove = confirmationState.isApprove;

            closeApprovalConfirmation();

            if (isApprove) {
                // Execute approve
                await executeApprovePengajuan(pengajuanId);
            } else {
                // Show rejection modal
                showRejectionModalIndividual(pengajuanId);
            }
        }

        // Wrapper function for inline onclick (because async functions can't be called directly from onclick)
        function handleConfirmationOKWrapper() {
            handleConfirmationOK().catch(error => {
                console.error('Error in handleConfirmationOK:', error);
            });
        }

        async function executeApprovePengajuan(id) {
            const userRole = '<?php echo $currentUser['role']; ?>';
            let apiAction;

            if (userRole === 'ADMIN_SIKEU') {
                apiAction = 'approve_pengajuan_sikeu';
            } else {
                apiAction = 'approve_pengajuan_bagren';
            }

            showLoading('Memproses pengajuan...');
            try {
                const response = await fetch(`api/main.php?action=${apiAction}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        pengajuan_ids: [id]
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    hideLoading();
                    showCustomAlert('Pengajuan berhasil disetujui!', 'success', 'Sukses');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    hideLoading();
                    showCustomAlert('Gagal memproses pengajuan: ' + (result.message || 'Unknown error'), 'error', 'Error');
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                showCustomAlert('Terjadi kesalahan: ' + error.message, 'error', 'Error');
            }
        }

        // Dropdown menu handler
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('userDropdownBtn');
            const menu = document.getElementById('userDropdownMenu');
            if (!btn || !menu) return;

            function closeMenu() {
                btn.closest('.user-dropdown-panel').classList.remove('open');
            }

            function openMenu() {
                btn.closest('.user-dropdown-panel').classList.add('open');
            }

            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const panel = btn.closest('.user-dropdown-panel');
                if (panel.classList.contains('open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && !btn.contains(e.target)) {
                    closeMenu();
                }
            });

            // Close on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeMenu();
            });
        });
    </script>

    <!-- Custom Approval Confirmation Modal -->
    <div id="approvalConfirmationModal" class="confirmation-modal-overlay">
        <div class="confirmation-modal">
            <div class="confirmation-modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 16v-4"></path>
                    <path d="M12 8h.01"></path>
                </svg>
            </div>
            <h2 id="confirmTitle">Konfirmasi Pengajuan</h2>
            <p id="confirmMessage">Apakah Anda yakin ingin melakukan aksi ini?</p>
            <div class="confirmation-modal-buttons">
                <button id="confirmCancelBtn" class="confirm-btn confirm-cancel" onclick="closeApprovalConfirmation()">Batal</button>
                <button id="confirmOkBtn" class="confirm-btn confirm-ok" onclick="handleConfirmationOKWrapper()">Setuju</button>
            </div>
        </div>
    </div>

    <style>
        /* Confirmation Modal Styles */
        .confirmation-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeInOverlay 0.3s ease-out;
        }

        .confirmation-modal-overlay.show {
            display: flex;
        }

        @keyframes fadeInOverlay {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .confirmation-modal {
            background: white;
            border-radius: 12px;
            padding: 40px 35px;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: slideUpModal 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
        }

        @keyframes slideUpModal {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .confirmation-modal-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            animation: iconBounce 0.5s ease-out;
        }

        @keyframes iconBounce {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .confirmation-modal h2 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 0 0 12px 0;
            font-weight: 600;
        }

        .confirmation-modal p {
            font-size: 0.95rem;
            color: #555;
            margin: 0 0 30px 0;
            line-height: 1.5;
            white-space: pre-line;
        }

        .confirmation-modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .confirm-btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            outline: none;
        }

        .confirm-cancel {
            background: linear-gradient(135deg, #f0ad4e 0%, #ec971f 100%);
        }

        .confirm-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 173, 78, 0.4);
        }

        .confirm-ok {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .confirm-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        }

        .confirm-btn:active {
            transform: translateY(0);
        }
    </style>

</body>

</html>