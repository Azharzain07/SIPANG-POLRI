// Custom Alert Functions

/**
 * Show a simple alert with one button
 * @param {string} message - The message to display
 * @param {string} type - Alert type: 'success', 'error', 'warning', 'info'
 * @param {string} title - Custom title (optional)
 */
function showCustomAlert(message, type = 'info', title = '') {
    const overlay = document.getElementById('customAlertOverlay');
    const alertBox = overlay.querySelector('.custom-alert-box');
    const icon = document.getElementById('alertIcon');
    const titleEl = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');
    const buttonsContainer = document.getElementById('alertButtons');

    // Set icon based on type with SVG
    const iconsSVG = {
        'success': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>',
        'error': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        'warning': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        'info': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
        'question': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'
    };

    // Set titles based on type
    const titles = {
        'success': title || 'Berhasil',
        'error': title || 'Gagal',
        'warning': title || 'Perhatian',
        'info': title || 'Informasi',
        'question': title || 'Konfirmasi'
    };

    // Clear previous classes
    icon.className = 'custom-alert-icon ' + type;

    // Set content with SVG
    icon.innerHTML = iconsSVG[type];
    titleEl.textContent = titles[type];
    messageEl.textContent = message;

    // Create single OK button
    buttonsContainer.innerHTML = `
        <button class="custom-alert-button ${type}" onclick="closeCustomAlert()">OK</button>
    `;

    // Show overlay
    overlay.classList.add('show');

    // Close on overlay click
    overlay.onclick = function(e) {
        if (e.target === overlay) {
            closeCustomAlert();
        }
    };

    // Handle Escape key
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeCustomAlert();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

/**
 * Show a confirmation dialog with OK and Cancel buttons
 * @param {string} message - The message to display
 * @param {function} onConfirm - Callback function when OK is clicked
 * @param {function} onCancel - Callback function when Cancel is clicked (optional)
 * @param {string} title - Custom title (optional, defaults to "localhost says")
 */
function showConfirm(message, onConfirm, onCancel = null, title = '') {
    const overlay = document.getElementById('customAlertOverlay');
    const alertBox = overlay.querySelector('.custom-alert-box');
    const icon = document.getElementById('alertIcon');
    const titleEl = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');
    const buttonsContainer = document.getElementById('alertButtons');

    // Question mark icon
    const questionIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';

    // Set content
    icon.className = 'custom-alert-icon question';
    icon.innerHTML = questionIcon;
    titleEl.textContent = title || 'localhost says';
    messageEl.textContent = message;

    // Create OK and Cancel buttons
    buttonsContainer.innerHTML = `
        <button class="custom-alert-button btn-ok" id="confirmOkBtn">OK</button>
        <button class="custom-alert-button btn-cancel" id="confirmCancelBtn">Cancel</button>
    `;

    // Show overlay
    overlay.classList.add('show');

    // Handle OK button click
    document.getElementById('confirmOkBtn').onclick = function() {
        closeCustomAlert();
        if (onConfirm && typeof onConfirm === 'function') {
            onConfirm();
        }
    };

    // Handle Cancel button click
    document.getElementById('confirmCancelBtn').onclick = function() {
        closeCustomAlert();
        if (onCancel && typeof onCancel === 'function') {
            onCancel();
        }
    };

    // Close on overlay click (counts as cancel)
    overlay.onclick = function(e) {
        if (e.target === overlay) {
            closeCustomAlert();
            if (onCancel && typeof onCancel === 'function') {
                onCancel();
            }
        }
    };

    // Handle Escape key (counts as cancel)
    const escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeCustomAlert();
            if (onCancel && typeof onCancel === 'function') {
                onCancel();
            }
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);

    // Handle Enter key (counts as OK)
    const enterHandler = function(e) {
        if (e.key === 'Enter') {
            closeCustomAlert();
            if (onConfirm && typeof onConfirm === 'function') {
                onConfirm();
            }
            document.removeEventListener('keydown', enterHandler);
        }
    };
    document.addEventListener('keydown', enterHandler);
}

/**
 * Close the custom alert
 */
function closeCustomAlert() {
    const overlay = document.getElementById('customAlertOverlay');
    overlay.classList.remove('show');
}

/**
 * Show loading overlay
 * @param {string} message - Loading message (optional)
 */
function showLoading(message = 'Memproses data Anda...') {
    const overlay = document.getElementById('loadingOverlay');
    const loadingText = overlay.querySelector('.loading-text');
    if (loadingText) {
        loadingText.textContent = message;
    }
    overlay.classList.add('show');
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.remove('show');
}

// ===== DROPDOWN MENU HANDLER - V3 FIXED =====
(function() {
    'use strict';
    
    console.log('[Dropdown] Script loaded');
    
    // Wait for elements to be available
    function init() {
        const btn = document.getElementById('userDropdownBtn');
        const panel = document.querySelector('.user-dropdown-panel');
        
        if (!btn || !panel) {
            console.warn('[Dropdown] Elements not found yet, retrying...');
            setTimeout(init, 100);
            return;
        }
        
        console.log('[Dropdown] Elements found, setting up...');
        setupDropdown(btn, panel);
    }
    
    function setupDropdown(btn, panel) {
        // Clone button to remove any existing listeners
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        console.log('[Dropdown] Setup starting');
        
        // Button click handler
        newBtn.addEventListener('click', function(e) {
            console.log('[Dropdown] Button clicked');
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = panel.classList.contains('open');
            if (isOpen) {
                panel.classList.remove('open');
                console.log('[Dropdown] Closed (toggle)');
            } else {
                panel.classList.add('open');
                console.log('[Dropdown] Opened (toggle)');
            }
        });
        
        // Outside click handler
        const outsideHandler = function(e) {
            if (!panel.contains(e.target)) {
                if (panel.classList.contains('open')) {
                    panel.classList.remove('open');
                    console.log('[Dropdown] Closed (outside click)');
                }
            }
        };
        document.addEventListener('click', outsideHandler, false);
        
        // Escape key handler
        const escapeHandler = function(e) {
            if (e.key === 'Escape' && panel.classList.contains('open')) {
                panel.classList.remove('open');
                console.log('[Dropdown] Closed (Escape key)');
            }
        };
        document.addEventListener('keydown', escapeHandler, false);
        
        console.log('[Dropdown] Setup complete - ready');
    }
    
    // Start initialization immediately
    if (document.readyState === 'loading') {
        console.log('[Dropdown] Waiting for DOM...');
        document.addEventListener('DOMContentLoaded', init);
    } else {
        console.log('[Dropdown] DOM ready, initializing...');
        init();
    }
})();

// ===== DASHBOARD AUTHENTICATION CHECK =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('[Auth] Check initialized');
    if (typeof isLoggedIn !== 'undefined') {
        document.querySelectorAll('a[href="pengajuan"], a[href="riwayat"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!isLoggedIn) {
                    e.preventDefault();
                    window.location.href = 'login.php';
                }
            });
        });
    }
});
