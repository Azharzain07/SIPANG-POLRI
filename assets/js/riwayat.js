// Fungsi untuk format tanggal
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Fungsi untuk format angka
function formatNumber(num) {
    if (num === null || num === undefined || num === '') return '-';
    return new Intl.NumberFormat('id-ID').format(Number(num));
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Global variable to store all pengajuan data
let allPengajuanData = [];

// Show detail modal with single pengajuan detail directly (NEW: no list view first)
function showDetailRow(id, row) {
    try {
        const modal = document.getElementById('detailModal');
        if (!modal) {
            console.error('Detail modal not found');
            return;
        }

        // Show loading state
        const modalBody = modal.querySelector('#detailModalBody');
        modalBody.innerHTML = '<div class="loading">Memuat detail...</div>';
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Fetch detail via AJAX (cache-bust with timestamp)
        $.get('api/get_pengajuan_detail.php', { id: id, t: Date.now() })
            .done(function(resp) {
                let data = resp;
                if (typeof resp === 'string') {
                    try {
                        data = JSON.parse(resp);
                    } catch (err) {
                        const safe = escapeHtml(resp).replace(/\n/g, '<br>');
                        modalBody.innerHTML = `
                            <div class="empty-message">Tidak dapat memuat detail: <br><small style="color:#b00">${safe}</small></div>
                        `;
                        return;
                    }
                }

                // Build detail HTML from data
                const tanggal = formatDate(data.tanggal || data.created_at || data.createdAt);
                const nomor = escapeHtml(data.nomor_surat || data.nomor || '-');
                const uraian = escapeHtml(data.uraian || data.keterangan || '-');
                const jumlah = formatNumber(data.jumlah_diajukan || data.jumlah || data.total || 0);
                const statusNama = escapeHtml(data.status_nama || data.status || '-');
                const statusKode = (data.status_kode || data.status_code || '').toLowerCase();
                const filePath = data.file_path || data.file || null;

                const fileHtml = filePath ? `<button class="action-btn" onclick="viewPDF('${filePath.replace(/'/g, "\\'")}')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg> Lihat Berkas</button>` : '<span>-</span>';

                const bulan = escapeHtml(data.bulan_pengajuan || '-');
                const sumberDana = escapeHtml(data.sumber_dana || '-');
                const penanggungJawab = escapeHtml(data.penanggung_jawab || '-');
                const bendahara = escapeHtml(data.bendahara_pengeluaran_pembantu || '-');
                const namaKegiatan = escapeHtml(data.nama_kegiatan || data.kegiatan_nama || '-');
                const kodeKegiatan = escapeHtml(data.kode_kegiatan || data.kegiatan_kode || '-');

                const detailHtml = `
                    <div class="detail-content" style="animation: slideInUp 0.3s ease-out;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef;">
                            <h4 style="margin:0;color:#1a5490;font-size:1.1rem;">Detail Pengajuan</h4>
                            ${fileHtml}
                        </div>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <h4>Tanggal Pengajuan</h4>
                                <p>${tanggal}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Bulan Pengajuan</h4>
                                <p>${bulan}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Sumber Dana</h4>
                                <p>${sumberDana}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Nomor Surat</h4>
                                <p>${nomor}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Total Anggaran</h4>
                                <p>Rp ${jumlah}</p>
                            </div>
                        </div>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <h4>Nama Program / Kegiatan</h4>
                                <p>${namaKegiatan}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Kode Program</h4>
                                <p>${kodeKegiatan}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Penanggung Jawab</h4>
                                <p>${penanggungJawab}</p>
                            </div>
                            <div class="detail-item">
                                <h4>Bendahara Pembantu</h4>
                                <p>${bendahara}</p>
                            </div>
                        </div>

                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <h4>Uraian</h4>
                            <p>${uraian}</p>
                        </div>

                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <h4>Status Terakhir</h4>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <span class="status-badge status-${statusKode}">${statusNama}</span>
                                ${ (data.status_keterangan || data.last_rejection_reason) ? `
                                <span style="color:#b00;font-size:0.9rem;">Alasan: ${escapeHtml(data.status_keterangan || data.last_rejection_reason)}</span>
                                ` : '' }
                            </div>
                        </div>
                    </div>`;

                modalBody.innerHTML = detailHtml;
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                let message = 'Gagal memuat detail pengajuan.';
                if (jqXHR && jqXHR.status === 401) message += ' (Unauthorized — silakan login ulang)';
                if (jqXHR && jqXHR.responseText) {
                    const safe = escapeHtml(jqXHR.responseText).replace(/\n/g, '<br>');
                    message += `<br><small style="color:#b00">${safe}</small>`;
                } else if (errorThrown) {
                    message += `<br><small style="color:#b00">${escapeHtml(errorThrown)}</small>`;
                }
                modalBody.innerHTML = `
                    <div class="empty-message">${message}</div>
                `;
            });
    } catch (e) {
        console.error('showDetailRow error', e);
    }
}

// Go back to list view (show all pengajuan)
function backToList() {
    try {
        const modal = document.getElementById('detailModal');
        const modalBody = modal.querySelector('#detailModalBody');
        
        // Build table HTML with all pengajuan data
        let tableHtml = `
            <div class="detail-content" style="animation: slideInUp 0.3s ease-out;">
                <div style="margin-bottom:1rem;">
                    <h4 style="margin:0;color:#1a5490;font-size:1.1rem;">Daftar Semua Pengajuan</h4>
                    <p style="color:#666;font-size:0.9rem;margin:0.5rem 0 0;">Total: ${allPengajuanData.length} pengajuan</p>
                </div>
                
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                            <th style="padding:1rem;text-align:left;color:#1a5490;font-weight:600;">No.</th>
                            <th style="padding:1rem;text-align:left;color:#1a5490;font-weight:600;">Tanggal</th>
                            <th style="padding:1rem;text-align:left;color:#1a5490;font-weight:600;">Nomor</th>
                            <th style="padding:1rem;text-align:left;color:#1a5490;font-weight:600;">Uraian</th>
                            <th style="padding:1rem;text-align:right;color:#1a5490;font-weight:600;">Total Anggaran</th>
                            <th style="padding:1rem;text-align:center;color:#1a5490;font-weight:600;">Status</th>
                            <th style="padding:1rem;text-align:center;color:#1a5490;font-weight:600;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        // Build rows for all pengajuan
        if (allPengajuanData.length === 0) {
            tableHtml += `
                        <tr>
                            <td colspan="7" style="text-align:center;padding:2rem;color:#666;">Belum ada data pengajuan</td>
                        </tr>
            `;
        } else {
            allPengajuanData.forEach((item, index) => {
                const status = item.status_class || item.status.toLowerCase();
                const formattedDate = formatDate(item.tanggal || item.created_at);
                
                tableHtml += `
                        <tr style="border-bottom:1px solid #e9ecef;transition:background 0.2s ease;">
                            <td style="padding:1rem;">${index + 1}</td>
                            <td style="padding:1rem;">${formattedDate}</td>
                            <td style="padding:1rem;">${item.nomor_surat || '-'}</td>
                            <td style="padding:1rem;">${escapeHtml(item.uraian)}</td>
                            <td style="padding:1rem;text-align:right;">Rp ${formatNumber(item.jumlah_diajukan)}</td>
                            <td style="padding:1rem;text-align:center;">
                                <span class="status-badge status-${status}" style="display:inline-block;">
                                    ${item.status}
                                </span>
                            </td>
                            <td style="padding:1rem;text-align:center;">
                                <button class="action-btn" onclick="event.stopPropagation(); showDetailRow(${item.id})" style="padding:0.4rem 0.8rem;font-size:0.85rem;">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                `;
            });
        }

        tableHtml += `
                    </tbody>
                </table>
            </div>
        `;

        modalBody.innerHTML = tableHtml;
    } catch (e) {
        console.error('backToList error', e);
    }
}

// Close detail modal
function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Prevent document click from closing dropdown when clicking on a main row
$(document).on('click', '.main-row', function(e) {
    e.stopPropagation();
});

// Close detail modal when clicking outside of it
$(document).on('click', function(e) {
    const modal = document.getElementById('detailModal');
    if (modal && e.target === modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});
