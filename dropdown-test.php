<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Test - SIPANG POLRI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10000;
            overflow: visible;
        }

        .header-right {
            text-align: right;
            overflow: visible;
        }

        /* User dropdown styles */
        .user-dropdown-panel { 
            position: relative !important; 
            display: inline-block !important; 
            z-index: 10001 !important;
        }

        .user-dropdown-btn { 
            padding: 10px 24px; 
            border-radius: 25px; 
            border: none; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff; 
            font-size: 0.95rem; 
            font-weight: 700; 
            cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: visible;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .user-dropdown-btn:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
        }

        .user-dropdown-menu { 
            position: absolute !important; 
            right: 0 !important; 
            top: calc(100% + 10px) !important;
            min-width: 220px !important; 
            background: #ffffff !important; 
            border-radius: 12px !important; 
            box-shadow: 0 10px 40px rgba(26, 84, 144, 0.2) !important; 
            opacity: 0 !important; 
            transform: translateY(-15px) scale(0.95) !important; 
            pointer-events: none !important; 
            transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease !important; 
            padding: 8px 0 !important; 
            z-index: 10002 !important;
            border: 1px solid rgba(26, 84, 144, 0.15) !important;
            max-height: 0 !important;
            overflow: hidden !important;
            display: block !important;
        }

        .user-dropdown-panel.open .user-dropdown-menu { 
            opacity: 1 !important; 
            pointer-events: auto !important; 
            transform: translateY(0) scale(1) !important;
            max-height: 500px !important;
            box-shadow: 0 15px 50px rgba(26, 84, 144, 0.25) !important;
        }

        .user-dropdown-menu a { 
            display: flex;
            align-items: center;
            gap: 8px; 
            padding: 9px 20px; 
            text-decoration: none; 
            color: #164a72;
            font-size: .97em; 
            border-radius: 5px; 
            transition: all .2s ease;
            position: relative;
            overflow: hidden;
        }

        .user-dropdown-menu a:hover { 
            background-color: rgba(26, 84, 144, 0.08);
            color: #1a5490;
        }

        .user-dropdown-menu a.logout { 
            border-top: 1px solid rgba(26, 84, 144, 0.1);
            color: #d9534f;
        }

        .user-dropdown-menu a.logout:hover { 
            background-color: rgba(217, 83, 79, 0.08);
            color: #c9302c;
        }

        .chevron {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .user-dropdown-panel.open .chevron {
            transform: rotate(180deg);
        }

        .test-box {
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-left">
            <h1>SIPANG POLRI - Dropdown Test</h1>
        </div>
        <div class="header-right">
            <div class="user-dropdown-panel">
                <button id="userDropdownBtn" class="user-dropdown-btn">
                    Test User
                    <svg class="chevron" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div id="userDropdownMenu" class="user-dropdown-menu">
                    <a href="#dashboard">Dashboard</a>
                    <a href="#pengajuan">Pengajuan</a>
                    <a href="#riwayat">Riwayat</a>
                    <a href="#logout" class="logout">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="test-box">
        <h2>Dropdown Test Instructions:</h2>
        <ol>
            <li>Open browser console (F12)</li>
            <li>Click the "Test User" button above</li>
            <li>Check console for debug messages</li>
            <li>The dropdown menu should appear below the button</li>
            <li>Click outside to close the dropdown</li>
        </ol>
        <p><strong>Status: </strong><span id="status">Ready to test</span></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userDropdownBtn = document.getElementById('userDropdownBtn');
            const userDropdownPanel = document.querySelector('.user-dropdown-panel');
            const statusEl = document.getElementById('status');

            if (!userDropdownBtn || !userDropdownPanel) {
                statusEl.textContent = 'ERROR: Elements not found';
                return;
            }

            statusEl.textContent = 'Ready - click button above';

            // Toggle dropdown
            userDropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdownPanel.classList.toggle('open');
                const isOpen = userDropdownPanel.classList.contains('open');
                console.log(isOpen ? '✓ Dropdown opened' : '✓ Dropdown closed');
                statusEl.textContent = isOpen ? 'Dropdown OPENED!' : 'Dropdown closed';
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdownPanel.contains(e.target)) {
                    if (userDropdownPanel.classList.contains('open')) {
                        userDropdownPanel.classList.remove('open');
                        console.log('✓ Dropdown closed (outside click)');
                        statusEl.textContent = 'Dropdown closed (outside click)';
                    }
                }
            });

            // Close on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && userDropdownPanel.classList.contains('open')) {
                    userDropdownPanel.classList.remove('open');
                    console.log('✓ Dropdown closed (Escape key)');
                    statusEl.textContent = 'Dropdown closed (Escape)';
                }
            });
        });
    </script>
</body>
</html>
