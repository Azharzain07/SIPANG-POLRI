<?php

/**
 * SIPANG POLRI - Riwayat Page
 * Halaman riwayat pengajuan - Untuk semua user yang login
 */

// Require authentication
require_once 'includes/auth_guard.php';
requireLogin(); // Any logged in user can access

$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan - Sistem Informasi Perencanaan Anggaran</title>

    <!-- Libraries for Export and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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

        @keyframes expandDown {
            from {
                max-height: 0;
                opacity: 0;
            }

            to {
                max-height: 500px;
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            margin: 0 0.5rem;
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
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Card */
        .card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            animation: slideUp 0.7s ease-out;
        }

        .card-title {
            color: #164a7a;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #1a5490;
        }

        /* Search & Filter Bar */
        .filter-bar {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.8rem 3rem 0.8rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #1a5490;
            box-shadow: 0 0 0 3px rgba(26, 84, 144, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
        }

        .export-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.6rem 1.1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        /* Sort toggle removed (oldest/newest not used) */

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 8px;
        }

        table thead {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 4px 8px rgba(26, 84, 144, 0.15);
        }

        table th {
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        table td {
            padding: 1.1rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        table tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background: white;
        }

        table tbody tr:hover {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef7 100%);
            box-shadow: 0 4px 12px rgba(26, 84, 144, 0.1);
        }

        .main-row.active {
            background: linear-gradient(135deg, #e8eef7 0%, #dce4f1 100%);
            box-shadow: 0 4px 12px rgba(26, 84, 144, 0.15);
        }

        /* Status Badge - Modern Gradient */
        .status-badge {
            padding: 0.6rem 1.1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 100px;
            text-align: center;
            cursor: default;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
        }

        .status-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .status-draft {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            color: #495057;
            border: 1px solid #adb5bd;
        }

        .status-terima-berkas {
            background: linear-gradient(135deg, #bbdefb 0%, #64b5f6 100%);
            color: #0d47a1;
            border: 1px solid #2196f3;
        }

        .status-disposisi-kabag {
            background: linear-gradient(135deg, #ffe0b2 0%, #ffb74d 100%);
            color: #e65100;
            border: 1px solid #ff9800;
        }

        .status-disposisi-waka {
            background: linear-gradient(135deg, #e1bee7 0%, #ce93d8 100%);
            color: #4a148c;
            border: 1px solid #ab47bc;
        }

        .status-terima-sikeu {
            background: linear-gradient(135deg, #c8e6c9 0%, #81c784 100%);
            color: #1b5e20;
            border: 1px solid #4caf50;
        }

        .status-dibayarkan {
            background: linear-gradient(135deg, #a5d6a7 0%, #66bb6a 100%);
            color: #0d3b0d;
            border: 1px solid #43a047;
        }

        /* Detail Row (Accordion) - Modern Styling */
        .detail-row {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8eef7 100%);
        }

        .detail-wrapper {
            padding: 1.2rem 1.5rem;
            margin: 0.5rem;
            border-radius: 8px;
            background: white;
            box-shadow: 0 4px 12px rgba(26, 84, 144, 0.1);
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .detail-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            padding: 0.8rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 6px;
            border-left: 3px solid #1a5490;
            transition: all 0.2s ease;
        }

        .info-group:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(26, 84, 144, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f0f4fa 100%);
        }

        .info-group label {
            font-size: 0.75rem;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-group span {
            font-size: 0.95rem;
            color: #1a5490;
            font-weight: 700;
            letter-spacing: -0.2px;
        }

        /* Make detailed description (uraian) use the same font/weight as info-group values */
        .info-detail .info-value {
            font-size: 0.95rem;
            color: #333;
            font-weight: 500;
            margin: 0;
            line-height: 1.5;
            padding: 0.8rem;
            background: #f8fafb;
            border-radius: 6px;
            border-left: 3px solid #2d7ab5;
        }

        .info-detail {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .info-detail label {
            display: block;
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 0.4rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-detail p {
            font-size: 0.95rem;
            color: #333;
            line-height: 1.5;
            margin: 0;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.8rem;
            background: linear-gradient(135deg, rgba(26, 84, 144, 0.02) 0%, rgba(45, 122, 181, 0.02) 100%);
            border-radius: 6px;
            border-bottom: 2px solid #e9ecef;
        }

        .detail-header h4 {
            color: #1a5490;
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: -0.2px;
        }

        .detail-header a {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.85rem;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .detail-header a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
            background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
        }

        .detail-content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .detail-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .info-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .info-group label {
            color: #666;
            font-size: 0.9rem;
        }

        .info-group span {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }

        .detail-docs {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .detail-docs h5 {
            color: #1a5490;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .btn-view-doc {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: white;
            border: 1px solid #dee2e6;
            padding: 0.7rem 1rem;
            border-radius: 6px;
            color: #1a5490;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-view-doc:hover {
            background: #e3f2fd;
            border-color: #1a5490;
        }

        .riwayat-status {
            margin-top: 2rem;
        }

        .riwayat-status h5 {
            color: #1a5490;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .status-timeline {
            position: relative;
            padding-left: 2rem;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            left: 0.85rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-marker {
            position: absolute;
            left: -2rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: white;
            border: 2px solid #1a5490;
            margin-top: 0.25rem;
        }

        .timeline-item.current .timeline-marker {
            background: #1a5490;
            box-shadow: 0 0 0 4px rgba(26, 84, 144, 0.2);
        }

        .timeline-content {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1rem;
        }

        .status-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .status-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .status-keterangan {
            font-size: 0.9rem;
            color: #495057;
            margin: 0;
        }

        .detail-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #1a5490;
        }

        .detail-title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .detail-title-row h4 {
            margin: 0;
        }

        .detail-status-inline {
            display: inline-flex;
            align-items: center;
        }

        .detail-item label {
            display: block;
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 0.3rem;
        }

        .detail-item .value {
            font-size: 1rem;
            font-weight: 600;
            color: #164a7a;
        }

        .file-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            padding: 5px 10px;
            background: #e3f2fd;
            border-radius: 4px;
            display: inline-block;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .file-link:hover {
            background: #bbdefb;
            color: #0056b3;
            text-decoration: none;
        }

        /* PDF Viewer Modal */
        .pdf-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .pdf-modal-content {
            position: relative;
            margin: 2% auto;
            width: 90%;
            height: 90%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .pdf-modal-header {
            background: #164a7a;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pdf-modal-header h3 {
            margin: 0;
            font-size: 1.2rem;
        }

        .pdf-modal-close {
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

        .pdf-modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .pdf-modal-body {
            height: calc(100% - 60px);
            padding: 0;
        }

        .pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Detail Modal */
        .detail-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
            overflow-y: auto;
        }

        .detail-modal-content {
            position: relative;
            margin: 2% auto;
            width: 85%;
            max-width: 900px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideInUp 0.4s ease-out;
        }

        .detail-modal-header {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(26, 84, 144, 0.15);
        }

        .detail-modal-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .detail-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            padding: 0;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .detail-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .detail-modal-body {
            padding: 2rem;
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }

        /* Scrollbar styling for modal body */
        .detail-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .detail-modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .detail-modal-body::-webkit-scrollbar-thumb {
            background: #1a5490;
            border-radius: 4px;
        }

        .detail-modal-body::-webkit-scrollbar-thumb:hover {
            background: #0d3660;
        }

        .detail-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-detail {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
            transform: translateY(-2px);
        }

        .action-btn {
            background: none;
            border: none;
            color: #1a5490;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .action-btn:hover {
            background: #e3f2fd;
        }

        .expand-icon {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .expand-icon.rotated {
            transform: rotate(180deg);
        }

        .empty-message {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-style: italic;
        }

        /* Detail content styling (matching admin.php) */
        .detail-content {
            padding: 1rem;
            animation: slideInUp 0.3s ease-out;
        }

        .detail-content table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
        }

        .detail-content table thead tr {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .detail-content table th {
            padding: 1rem;
            text-align: left;
            color: #1a5490;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .detail-content table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .detail-content table tbody tr:hover {
            background: #f8f9fa;
            transition: background 0.2s ease;
        }

        .detail-content table tbody tr:last-child td {
            border-bottom: none;
        }
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            background: white;
            padding: 1rem;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }

        .detail-item:hover {
            box-shadow: 0 2px 8px rgba(26, 84, 144, 0.1);
            border-color: #1a5490;
        }

        .detail-item h4 {
            color: #1a5490;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .detail-item p {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
            word-break: break-word;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .header-left {
                flex-direction: column;
            }

            .header-right {
                text-align: center;
            }

            .filter-bar {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }

            .export-buttons {
                width: 100%;
            }

            .btn {
                flex: 1;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.85rem;
            }

            table th,
            table td {
                padding: 0.6rem;
            }
        }

        /* Custom Alert Modal */
        .custom-alert-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            animation: fadeIn 0.3s ease-out;
            backdrop-filter: blur(4px);
        }

        .custom-alert-overlay.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .custom-alert-box {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: scaleIn 0.3s ease-out;
            position: relative;
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

        .custom-alert-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .custom-alert-icon.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .custom-alert-icon.error {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }

        .custom-alert-icon.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }

        .custom-alert-icon.info {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
        }

        .custom-alert-title {
            font-size: 1.3rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1rem;
            color: #164a7a;
        }

        .custom-alert-message {
            text-align: center;
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            white-space: pre-line;
        }

        .custom-alert-button {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-alert-button.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .custom-alert-button.error {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
        }

        .custom-alert-button.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: white;
        }

        .custom-alert-button.info {
            background: linear-gradient(135deg, #1a5490 0%, #2d7ab5 100%);
            color: white;
        }

        .custom-alert-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .custom-alert-button:active {
            transform: translateY(0);
        }

        .user-dropdown-panel {
            position: relative;
            display: inline-block;
        }

        .user-dropdown-btn {
            padding: .5em 1.05em;
            border-radius: 23px;
            border: none;
            background: #eaf2fe;
            color: #1a5490;
            font-size: .98rem;
            font-weight: 600;
            cursor: pointer;
            transition: all.2s;
        }

        .user-dropdown-btn:focus,
        .user-dropdown-btn:hover {
            background: #d2e8fc;
            color: #17436e;
        }

        .user-dropdown-menu {
            position: absolute;
            right: 0;
            top: 120%;
            min-width: 163px;
            background: #fff;
            border-radius: 11px;
            box-shadow: 0 8px 19px #19598f1a;
            opacity: 0;
            transform: translateY(-7px) scale(.98);
            pointer-events: none;
            transition: transform.22s, opacity.18s;
            padding: 5px 0;
            z-index: 60;
        }

        .user-dropdown-panel.open .user-dropdown-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
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
            transition: background.13s, color.12s;
        }

        .user-dropdown-menu a.logout {
            color: #c13d2f;
            font-weight: 700;
        }

        .user-dropdown-menu a.logout:hover {
            background: #ffdede;
            color: #a0190a;
        }

        .user-dropdown-menu a:hover,
        .user-dropdown-menu a:focus {
            background: #f0f7fc;
            color: #1164af;
        }

        .dropdown-divider {
            height: 1px;
            background: #ebf2fa;
            margin: 5px 0;
        }

        .chevron {
            transition: transform.2s;
            display: inline;
            vertical-align: middle;
        }

        .user-dropdown-panel.open .chevron {
            transform: rotate(180deg);
        }
    </style>
</head>

<body>
    <!-- Custom Alert Modal -->
    <div class="custom-alert-overlay" id="customAlertOverlay">
        <div class="custom-alert-box">
            <div class="custom-alert-icon" id="alertIcon"></div>
            <div class="custom-alert-title" id="alertTitle"></div>
            <div class="custom-alert-message" id="alertMessage"></div>
            <button class="custom-alert-button" id="alertButton" onclick="closeCustomAlert()">OK</button>
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
                    <svg class="chevron" style="margin-left:4px;width:17px;vertical-align:middle" viewBox="0 0 24 24">
                        <polyline points="6 9 12 15 18 9" fill="none" stroke="#19598F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></polyline>
                    </svg>
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
        <div class="card">
            <h2 class="card-title">Riwayat Pengajuan Anggaran Saya</h2>

            <!-- Search & Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari berdasarkan uraian..." onkeyup="searchTable()">
                    <button class="search-btn" onclick="searchTable()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; vertical-align: middle; margin-right: 0.3rem;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Cari
                    </button>
                </div>

                <div class="export-buttons">
                    <button class="btn btn-danger" onclick="exportToPDF()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        Ekspor PDF
                    </button>
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <line x1="9" y1="15" x2="15" y2="15"></line>
                        </svg>
                        Ekspor Excel
                    </button>
                    <!-- Sort toggle removed: ordering will follow server-provided order -->
                </div>
            </div>

            <!-- Table (Admin-style layout for consistent UI) -->
            <div class="data-table">
                <div class="table-header">
                    <h3>📋 Riwayat Pengajuan Anggaran</h3>
                </div>
                <div id="tableContent">
                    <div class="loading">Memuat data...</div>
                </div>
            </div>
        </div>
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

    <!-- Detail Modal -->
    <div id="detailModal" class="detail-modal" style="display: none;">
        <div class="detail-modal-content">
            <div class="detail-modal-header">
                <h3>Detail Pengajuan</h3>
                <button class="detail-modal-close" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="detailModalBody" class="detail-modal-body">
                <!-- Content will be inserted here -->
            </div>
        </div>
    </div>

    <script src="assets/js/riwayat.js"></script>
    <script>
        // Initialize data table with expandable rows
        $(document).ready(function() {
            loadPengajuanData();
        });

        function loadPengajuanData() {
            $.get('api/main.php?action=get_riwayat', function(response) {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                const data = response.data || [];

                // Store all pengajuan data in global variable for modal
                allPengajuanData = data;

                let tableHtml = `
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Nomor</th>
                                <th>Uraian</th>
                                <th>Total Anggaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                if (data.length === 0) {
                    tableHtml += `
                        <tr>
                            <td colspan="7" class="empty-message">
                                Belum ada data pengajuan
                            </td>
                        </tr>
                    `;
                } else {
                    data.forEach((item, index) => {
                        const status = item.status_class || item.status.toLowerCase();
                        const formattedDate = new Date(item.tanggal || item.created_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric'
                        });

                        tableHtml += `
                            <tr class="main-row" onclick="showDetailRow(${item.id}, this)" data-id="${item.id}">
                                <td>${index + 1}</td>
                                <td>${formattedDate}</td>
                                <td>${item.nomor_surat || '-'}</td>
                                <td>${item.uraian}</td>
                                <td>Rp ${formatNumber(item.jumlah_diajukan)}</td>
                                <td>
                                    <span class="status-badge status-${status}">
                                        ${item.status}
                                    </span>
                                </td>
                                <td>
                                    <button class="action-btn" onclick="event.stopPropagation(); viewPDF('${item.id}')">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                            <line x1="12" y1="18" x2="12" y2="12"></line>
                                            <line x1="9" y1="15" x2="15" y2="15"></line>
                                        </svg>
                                        Lihat Berkas
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }

                tableHtml += '</tbody></table>';
                $('#tableContent').html(tableHtml);
            });
        }

        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('main-row');

            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            }
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function exportToPDF() {
            window.jspdf.jsPDF = window.jspdf.jsPDF;
            const doc = new jspdf.jsPDF();

            doc.autoTable({
                html: 'table',
                startY: 20,
                styles: {
                    fontSize: 8
                },
                columnStyles: {
                    0: {
                        cellWidth: 10
                    }
                },
                didDrawPage: function(data) {
                    doc.setFontSize(15);
                    doc.text('Riwayat Pengajuan Anggaran', 14, 15);
                }
            });

            doc.save('riwayat-pengajuan.pdf');
        }

        function exportToExcel() {
            const table = document.querySelector('table');
            const wb = XLSX.utils.table_to_book(table, {
                sheet: 'Riwayat Pengajuan'
            });
            XLSX.writeFile(wb, 'riwayat-pengajuan.xlsx');
        }
        // PDF Viewer Functions
        function viewPDF(pengajuanId) {
            if (!pengajuanId) {
                showCustomAlert('ID pengajuan tidak valid', 'error');
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

        function downloadFile(pengajuanId) {
            if (!pengajuanId) {
                showCustomAlert('ID pengajuan tidak valid', 'error');
                return;
            }

            // Redirect to download endpoint
            window.location.href = 'api/get_file.php?id=' + encodeURIComponent(pengajuanId) + '&action=download';
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

        // Close modal when clicking outside
        window.onclick = function(event) {
            const pdfModal = document.getElementById('pdfModal');
            if (event.target === pdfModal) {
                closePDFModal();
            }
            const detailModal = document.getElementById('detailModal');
            if (event.target === detailModal) {
                closeDetailModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                if (document.getElementById('pdfModal').style.display === 'block') {
                    closePDFModal();
                }
                if (document.getElementById('detailModal').style.display === 'block') {
                    closeDetailModal();
                }
            }
        });

        // Custom Alert Function
        function showCustomAlert(message, type = 'info', title = '') {
            const overlay = document.getElementById('customAlertOverlay');
            const icon = document.getElementById('alertIcon');
            const titleEl = document.getElementById('alertTitle');
            const messageEl = document.getElementById('alertMessage');
            const button = document.getElementById('alertButton');

            // Set icon based on type with SVG
            const iconsSVG = {
                'success': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><path d="M9 12l2 2 4-4"></path></svg>',
                'error': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
                'warning': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                'info': '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            };

            // Set titles based on type
            const titles = {
                'success': title || 'Berhasil',
                'error': title || 'Gagal',
                'warning': title || 'Perhatian',
                'info': title || 'Informasi'
            };

            // Clear previous classes
            icon.className = 'custom-alert-icon ' + type;
            button.className = 'custom-alert-button ' + type;

            // Set content with SVG
            icon.innerHTML = iconsSVG[type];
            titleEl.textContent = titles[type];
            messageEl.textContent = message;

            // Show overlay
            overlay.classList.add('show');

            // Close on overlay click
            overlay.onclick = function(e) {
                if (e.target === overlay) {
                    closeCustomAlert();
                }
            };
        }

        function closeCustomAlert() {
            const overlay = document.getElementById('customAlertOverlay');
            overlay.classList.remove('show');
        }

        // Data riwayat akan diambil dari database via API
        // Inisialisasi kosong; data akan diisi oleh `loadRiwayatData()`
        let riwayatData = [];

        // Helper: return data in server-provided order (no client-side sort toggle)
        function getOrderedData(data) {
            return (data || []).slice();
        }

        // Helper: determine if an item is accepted (diterima)
        function isAccepted(item) {
            if (!item) return false;
            const acceptedStatuses = ['TERIMA BERKAS', 'TERIMA_BERKAS', 'TERIMA SIKEU', 'TERIMA_SIKEU', 'DIBAYARKAN', 'DIBAYAR'];
            const checkFields = ['status', 'statusNPWP', 'statusPPK'];
            for (const field of checkFields) {
                const val = (item[field] || '').toString().toUpperCase();
                if (!val) continue;
                for (const s of acceptedStatuses) {
                    if (val.indexOf(s) !== -1) return true;
                }
            }
            return false;
        }

        // Escape HTML to avoid injection when injecting into innerHTML
        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Display Uraian in ALL CAPS and escaped
        function displayUraian(text) {
            if (!text && text !== 0) return '';
            return escapeHtml(String(text).toUpperCase());
        }

        // Format currency
        function formatRupiah(angka) {
            return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
        }

        // Get status class
        function getStatusClass(status) {
            const statusMap = {
                'Draft': 'status-draft',
                'DRAFT': 'status-draft',
                'TERIMA BERKAS': 'status-terima-berkas',
                'TERIMA_BERKAS': 'status-terima-berkas',
                'DISPOSISI KABAG REN': 'status-disposisi-kabag',
                'DISPOSISI_KABAG_REN': 'status-disposisi-kabag',
                'DISPOSISI WAKA': 'status-disposisi-waka',
                'DISPOSISI_WAKA': 'status-disposisi-waka',
                'TERIMA SIKEU': 'status-terima-sikeu',
                'TERIMA_SIKEU': 'status-terima-sikeu',
                'DIBAYARKAN': 'status-dibayarkan'
            };
            return statusMap[status] || 'status-draft';
        }

        // Modal functions for detail view
        function openDetailModal(id) {
            const item = getItemById(id);
            if (!item) {
                showCustomAlert('Data tidak ditemukan', 'error');
                return;
            }

            // Check if modal elements exist
            const modalBody = document.getElementById('detailModalBody');
            const modal = document.getElementById('detailModal');

            if (!modalBody || !modal) {
                console.error('Detail modal elements not found in DOM');
                showCustomAlert('Terjadi kesalahan pada tampilan modal. Silakan refresh halaman.', 'error');
                return;
            }

            const accepted = isAccepted(item);
            const html = `
                <div class="detail-wrapper">
                    <div class="detail-header">
                        <h4>📋 Pengajuan #${item.id}</h4>
                        <span class="status-badge ${accepted ? 'status-dibayarkan' : 'status-draft'}">${accepted ? '✓ Diterima' : '⏱ Proses'}</span>
                    </div>

                    <div class="detail-info-grid">
                        <div class="info-group">
                            <label>📅 Tanggal</label>
                            <span>${formatDate(item.tanggal_pengajuan || item.tanggal) || '-'}</span>
                        </div>
                        <div class="info-group">
                            <label>📋 Nomor</label>
                            <span>${item.nomor_surat || item.kode || '-'}</span>
                        </div>
                        <div class="info-group">
                            <label>💼 Program</label>
                            <span>${item.nama_kegiatan || '-'}</span>
                        </div>
                        <div class="info-group">
                            <label>🏷️ Kode</label>
                            <span>${item.kode || '-'}</span>
                        </div>
                    </div>

                    <div class="detail-info-grid">
                        <div class="info-group">
                            <label>💰 Diajukan</label>
                            <span style="color: #27ae60;">${formatRupiah(item.jumlah_diajukan || item.totalAnggaran || 0)}</span>
                        </div>
                        <div class="info-group">
                            <label>📊 Pagu</label>
                            <span>${formatRupiah(item.jumlah_pagu || 0)}</span>
                        </div>
                        <div class="info-group">
                            <label>📈 Sisa</label>
                            <span>${formatRupiah(item.sisa_pagu || 0)}</span>
                        </div>
                        <div class="info-group">
                            <label>💵 Dana</label>
                            <span>${item.sumber_dana || '-'}</span>
                        </div>
                    </div>

                    <div class="info-detail">
                        <label>👤 Penanggung Jawab</label>
                        <p class="info-value">${item.penanggung_jawab || '-'}</p>
                    </div>

                    <div class="info-detail">
                        <label>🏦 Bendahara</label>
                        <p class="info-value">${item.bendahara_pengeluaran_pembantu || '-'}</p>
                    </div>

                    <div class="info-detail">
                        <label>📝 Uraian</label>
                        <p class="info-value">${item.uraian || '-'}</p>
                    </div>

                    <div style="margin-top: 0.8rem; padding-top: 0.8rem; border-top: 1px solid #e9ecef; display: flex; gap: 0.6rem; flex-wrap: wrap;">
                        ${item.file_path ? `<button class="btn btn-info" onclick="event.stopPropagation(); openGeneratedPDF(${item.id})">👁️ PDF</button>` : ''}
                        <button class="btn btn-primary" onclick="event.stopPropagation(); downloadItemPDF(${item.id})">📥 PDF</button>
                        <button class="btn btn-success" onclick="event.stopPropagation(); downloadItemExcel(${item.id})">📊 Excel</button>
                    </div>
                </div>
            `;

            try {
                modalBody.innerHTML = html;
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } catch (e) {
                console.error('Error setting modal content:', e);
                showCustomAlert('Gagal menampilkan detail pengajuan. Silakan coba lagi.', 'error');
            }
        }

        function closeDetailModal() {
            const modal = document.getElementById('detailModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }



        // Render table (grouped by user_id similar to admin view)
        function renderTable(data = riwayatData) {
            const filteredData = data || [];

            // Order data according to sortOrder
            const orderedData = getOrderedData(filteredData);

            if (orderedData.length === 0) {
                document.getElementById('tableContent').innerHTML = '<div class="empty-message">Tidak ada data yang ditemukan</div>';
                return;
            }

            // Group by user_id (for most users this will be a single group)
            groupedData = {};
            orderedData.forEach(item => {
                const key = `${item.user_id}`;
                if (!groupedData[key]) {
                    groupedData[key] = {
                        user: `${item.nama_lengkap} (${item.role})`,
                        polsek: item.nama_polsek,
                        items: []
                    };
                }
                groupedData[key].items.push(item);
            });

            let html = `
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pengguna</th>
                            <th>Kode</th>
                            <th>Program/Kegiatan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            // Build flat ordered list (respecting sortOrder) to compute global numbering
            const flatList = orderedData.slice();
            const idToIndex = {};
            flatList.forEach((it, idx) => {
                idToIndex[it.id] = idx + 1;
            });

            Object.values(groupedData).forEach((group, index) => {
                const firstItem = group.items[0];
                const totalAmount = group.items.reduce((sum, item) => sum + parseFloat(item.jumlah_diajukan || item.totalAnggaran || 0), 0);

                html += `
                    <tr>
                            <td>${idToIndex[firstItem.id] || '-'}</td>
                            <td>${firstItem.tanggal}</td>
                            <td>${group.user}</td>
                            <td>${firstItem.kode || '-'}</td>
                            <td>${group.items.length > 1 ? `${group.items.length} Pengajuan` : (firstItem.uraian ? displayUraian(firstItem.uraian) : '-')}</td>
                            <td>${formatRupiah(totalAmount)}</td>
                            <td><span class="status-badge ${getStatusClass(firstItem.status || firstItem.statusNPWP)}">${firstItem.status || firstItem.statusNPWP || '-'}</span></td>
                            <td>
                                <button class="btn btn-info" onclick="openDetailModal(${firstItem.id})">Detail</button>
                            </td>
                        </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            document.getElementById('tableContent').innerHTML = html;
        }

        // Helper: format date to Indonesian format
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            try {
                const date = new Date(dateStr);
                if (isNaN(date)) return dateStr;
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            } catch (e) {
                return dateStr;
            }
        }

        // Search function (filter by uraian)
        function searchTable() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const filteredData = riwayatData.filter(item => (item.uraian || '').toLowerCase().includes(input));
            renderTable(filteredData);
        }

        // Helper: find item by id in current data
        function getItemById(id) {
            for (const item of riwayatData) {
                if (item.id == id) return item;
            }
            // If not found in riwayatData, search groupedData
            for (const g of Object.values(groupedData)) {
                for (const item of g.items) {
                    if (item.id == id) return item;
                }
            }
            return null;
        }

        // PDF generation and viewing
        let currentPdfUrl = null;

        function openGeneratedPDF(id) {
            const item = getItemById(id);
            if (!item) {
                showCustomAlert('Data tidak ditemukan untuk PDF', 'error');
                return;
            }

            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('portrait');
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.text('DETAIL PENGAJUAN ANGGARAN', 105, 20, {
                align: 'center'
            });
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');

            let y = 30;
            const left = 20;

            // Dynamically include only fields that exist
            const fields = [
                ['Nomor', item.nomor_surat || item.kode || '-'],
                ['Tanggal', item.tanggal_pengajuan || item.tanggal || '-'],
                ['Bulan', item.bulan_pengajuan || item.bulan || '-'],
                ['Sumber Dana', item.sumber_dana || item.sumberDana || '-'],
                ['Uraian', item.uraian || '-'],
                ['Program/Kegiatan', item.nama_kegiatan || item.programKegiatan || '-'],
                ['Penanggung Jawab', item.penanggung_jawab || item.penanggungJawab || '-'],
                ['Bendahara', item.bendahara_pengeluaran_pembantu || item.penanggungPerbendaharaan || '-'],
                ['Jumlah Diajukan', formatRupiah(item.jumlah_diajukan || item.totalAnggaran || 0)],
                ['Jumlah Pagu', formatRupiah(item.jumlah_pagu || item.jumlahPagu || 0)],
                ['Sisa Saldo', formatRupiah(item.sisa_pagu || item.sisaSaldo || item.sd || 0)],
                ['Lokasi', item.lokasi || '-']
            ];

            fields.forEach(([label, value]) => {
                if (value === null || value === undefined) return;
                const lines = doc.splitTextToSize(`${label}: ${value}`, 160);
                doc.text(lines, left, y);
                y += lines.length * 6;
                if (y > 260) {
                    doc.addPage();
                    y = 20;
                }
            });

            // Output to blob and show in iframe modal
            const blob = doc.output('blob');
            if (currentPdfUrl) URL.revokeObjectURL(currentPdfUrl);
            currentPdfUrl = URL.createObjectURL(blob);
            const viewer = document.getElementById('pdfViewer');
            viewer.src = currentPdfUrl;
            document.getElementById('pdfModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function downloadItemPDF(id) {
            const item = getItemById(id);
            if (!item) {
                showCustomAlert('Data tidak ditemukan untuk download', 'error');
                return;
            }
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF('portrait');
            doc.setFontSize(14);
            doc.setFont(undefined, 'bold');
            doc.text('DETAIL PENGAJUAN ANGGARAN', 105, 20, {
                align: 'center'
            });
            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');

            let y = 30;
            const left = 20;

            const fields = [
                ['Nomor', item.nomor_surat || item.kode || '-'],
                ['Tanggal', item.tanggal_pengajuan || item.tanggal || '-'],
                ['Bulan', item.bulan_pengajuan || item.bulan || '-'],
                ['Sumber Dana', item.sumber_dana || item.sumberDana || '-'],
                ['Uraian', item.uraian || '-'],
                ['Program/Kegiatan', item.nama_kegiatan || item.programKegiatan || '-'],
                ['Penanggung Jawab', item.penanggung_jawab || item.penanggungJawab || '-'],
                ['Bendahara', item.bendahara_pengeluaran_pembantu || item.penanggungPerbendaharaan || '-'],
                ['Jumlah Diajukan', formatRupiah(item.jumlah_diajukan || item.totalAnggaran || 0)],
                ['Jumlah Pagu', formatRupiah(item.jumlah_pagu || item.jumlahPagu || 0)],
                ['Sisa Saldo', formatRupiah(item.sisa_pagu || item.sisaSaldo || item.sd || 0)],
                ['Lokasi', item.lokasi || '-']
            ];

            fields.forEach(([label, value]) => {
                if (value === null || value === undefined) return;
                const lines = doc.splitTextToSize(`${label}: ${value}`, 160);
                doc.text(lines, left, y);
                y += lines.length * 6;
                if (y > 260) {
                    doc.addPage();
                    y = 20;
                }
            });

            const filename = `Pengajuan_${item.id || 'unknown'}_${(item.tanggal_pengajuan || item.tanggal || '').replace(/\//g, '-')}.pdf`;
            doc.save(filename);
            showCustomAlert('PDF berhasil diunduh!', 'success');
        }

        // Download Excel for single item
        function downloadItemExcel(id) {
            const item = getItemById(id);
            if (!item) {
                showCustomAlert('Data tidak ditemukan untuk download', 'error');
                return;
            }
            // Prepare a flat object with readable labels
            const excelRow = {
                'ID': item.id || '',
                'Nomor': item.nomor_surat || item.kode || '',
                'Tanggal': item.tanggal_pengajuan || item.tanggal || '',
                'Bulan': item.bulan_pengajuan || item.bulan || '',
                'Sumber Dana': item.sumber_dana || item.sumberDana || '',
                'Uraian': item.uraian || '',
                'Program/Kegiatan': item.nama_kegiatan || item.programKegiatan || '',
                'Penanggung Jawab': item.penanggung_jawab || item.penanggungJawab || '',
                'Bendahara': item.bendahara_pengeluaran_pembantu || item.penanggungPerbendaharaan || '',
                'Jumlah Diajukan': item.jumlah_diajukan || item.totalAnggaran || 0,
                'Jumlah Pagu': item.jumlah_pagu || item.jumlahPagu || 0,
                'Sisa Saldo': item.sisa_pagu || item.sisaSaldo || item.sd || 0,
                'Lokasi': item.lokasi || ''
            };

            const ws = XLSX.utils.json_to_sheet([excelRow]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Pengajuan');
            const filename = `Pengajuan_${item.id || 'unknown'}_${(item.tanggal_pengajuan || item.tanggal || '').replace(/\//g, '-')}.xlsx`;
            XLSX.writeFile(wb, filename);
            showCustomAlert('Excel berhasil diunduh!', 'success');
        }

        // Export to PDF
        function exportToPDF() {
            try {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('landscape');

                // Header
                doc.setFontSize(16);
                doc.setFont(undefined, 'bold');
                doc.text('RIWAYAT PENGAJUAN ANGGARAN', 148, 15, {
                    align: 'center'
                });
                doc.setFontSize(12);
                doc.text('Polsek Garut Kota', 148, 22, {
                    align: 'center'
                });

                // Build global numbering according to current (server) order
                const flatListForExport = getOrderedData(riwayatData);
                const idToIndexExport = {};
                flatListForExport.forEach((it, idx) => {
                    idToIndexExport[it.id] = idx + 1;
                });

                // Table headers (sesuai form pengajuan)
                const headers = [
                    'No',
                    'Nomor / Kode',
                    'Tanggal Pengajuan',
                    'Bulan Pengajuan',
                    'Sumber Dana',
                    'Program / Kegiatan',
                    'Kode Kegiatan',
                    'Jumlah Pagu',
                    'Sisa Pagu',
                    'Jumlah Diajukan',
                    'Penanggung Jawab',
                    'Bendahara Pengeluaran',
                    'Uraian',
                    'Lokasi'
                ];

                // Table data mapped to headers
                const tableData = riwayatData.map(item => [
                    idToIndexExport[item.id] || '-',
                    item.nomor_surat || item.kode || '-',
                    item.tanggal_pengajuan || item.tanggal || '-',
                    item.bulan_pengajuan || item.bulan || '-',
                    item.sumber_dana || item.sumberDana || '-',
                    item.nama_kegiatan || item.programKegiatan || '-',
                    item.kode || '-',
                    formatRupiah(item.jumlah_pagu || item.jumlahPagu || 0),
                    formatRupiah(item.sisa_pagu || item.sisaSaldo || item.sd || 0),
                    formatRupiah(item.jumlah_diajukan || item.totalAnggaran || 0),
                    item.penanggung_jawab || item.penanggungJawab || '-',
                    item.bendahara_pengeluaran_pembantu || item.penanggungPerbendaharaan || '-',
                    item.uraian || '-',
                    item.lokasi || '-'
                ]);

                // Generate table
                doc.autoTable({
                    startY: 30,
                    head: [headers],
                    body: tableData,
                    theme: 'grid',
                    styles: {
                        fontSize: 8,
                        cellPadding: 2
                    },
                    headStyles: {
                        fillColor: [26, 84, 144],
                        textColor: 255,
                        fontStyle: 'bold',
                        halign: 'center'
                    },
                    columnStyles: {
                        0: {
                            cellWidth: 25
                        },
                        1: {
                            cellWidth: 65
                        },
                        2: {
                            cellWidth: 15,
                            halign: 'center'
                        },
                        3: {
                            cellWidth: 20,
                            halign: 'center'
                        },
                        4: {
                            cellWidth: 25,
                            halign: 'right'
                        },
                        5: {
                            cellWidth: 25,
                            halign: 'right'
                        },
                        6: {
                            cellWidth: 25,
                            halign: 'right'
                        },
                        7: {
                            cellWidth: 45
                        }
                    }
                });

                // Footer
                const pageCount = doc.internal.getNumberOfPages();
                doc.setFontSize(8);
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.text('Halaman ' + i + ' dari ' + pageCount, 148, 200, {
                        align: 'center'
                    });
                    doc.text('Dicetak pada: ' + new Date().toLocaleString('id-ID'), 148, 205, {
                        align: 'center'
                    });
                }

                // Save PDF
                doc.save('Riwayat_Pengajuan_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-') + '.pdf');

                showCustomAlert('File PDF berhasil diunduh!', 'success');
            } catch (error) {
                console.error(error);
                showCustomAlert('Gagal mengekspor PDF. Silakan coba lagi.', 'error');
            }
        }

        // Export to Excel
        function exportToExcel() {
            try {
                // Build global numbering according to current (server) order
                const flatListForExport = getOrderedData(riwayatData);
                const idToIndexExport = {};
                flatListForExport.forEach((it, idx) => {
                    idToIndexExport[it.id] = idx + 1;
                });

                // Excel columns ordered to match pengajuan form
                const excelData = riwayatData.map(item => ({
                    'No': idToIndexExport[item.id] || '-',
                    'Nomor / Kode': item.nomor_surat || item.kode || '-',
                    'Tanggal Pengajuan': item.tanggal_pengajuan || item.tanggal || '-',
                    'Bulan Pengajuan': item.bulan_pengajuan || item.bulan || '-',
                    'Sumber Dana': item.sumber_dana || item.sumberDana || '-',
                    'Program / Kegiatan': item.nama_kegiatan || item.programKegiatan || '-',
                    'Kode Kegiatan': item.kode || '-',
                    'Jumlah Pagu': item.jumlah_pagu || item.jumlahPagu || 0,
                    'Sisa Pagu': item.sisa_pagu || item.sisaSaldo || item.sd || 0,
                    'Jumlah Diajukan': item.jumlah_diajukan || item.totalAnggaran || 0,
                    'Penanggung Jawab': item.penanggung_jawab || item.penanggungJawab || '-',
                    'Bendahara Pengeluaran': item.bendahara_pengeluaran_pembantu || item.penanggungPerbendaharaan || '-',
                    'Uraian': item.uraian || '-',
                    'Lokasi': item.lokasi || '-'
                }));

                // Create worksheet
                const ws = XLSX.utils.json_to_sheet(excelData);

                // Set column widths
                const wscols = [{
                        wch: 20
                    }, // Nomor / Kode
                    {
                        wch: 15
                    }, // Tanggal Pengajuan
                    {
                        wch: 15
                    }, // Bulan Pengajuan
                    {
                        wch: 15
                    }, // Sumber Dana
                    {
                        wch: 40
                    }, // Program / Kegiatan
                    {
                        wch: 20
                    }, // Kode Kegiatan
                    {
                        wch: 15
                    }, // Jumlah Pagu
                    {
                        wch: 15
                    }, // Sisa Pagu
                    {
                        wch: 18
                    }, // Jumlah Diajukan
                    {
                        wch: 25
                    }, // Penanggung Jawab
                    {
                        wch: 25
                    }, // Bendahara Pengeluaran
                    {
                        wch: 40
                    }, // Uraian
                    {
                        wch: 20
                    } // Lokasi
                ];
                ws['!cols'] = wscols;

                // Create workbook
                const wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Pengajuan');

                // Add metadata
                wb.Props = {
                    Title: 'Riwayat Pengajuan Anggaran',
                    Subject: 'Data Pengajuan',
                    Author: 'Bagren Polres Garut',
                    CreatedDate: new Date()
                };

                // Save Excel file
                XLSX.writeFile(wb, 'Riwayat_Pengajuan_' + new Date().toLocaleDateString('id-ID').replace(/\//g, '-') + '.xlsx');

                showCustomAlert('File Excel berhasil diunduh!', 'success');
            } catch (error) {
                console.error(error);
                showCustomAlert('Gagal mengekspor Excel. Silakan coba lagi.', 'error');
            }
        }

        // Print detail
        function printDetail(id) {
            try {
                const data = riwayatData.find(item => item.id === id);

                if (!data) {
                    showCustomAlert('Data tidak ditemukan!', 'error');
                    return;
                }

                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF('portrait');

                // Header
                doc.setFontSize(18);
                doc.setFont(undefined, 'bold');
                doc.text('DETAIL PENGAJUAN ANGGARAN', 105, 20, {
                    align: 'center'
                });

                doc.setFontSize(12);
                doc.setFont(undefined, 'normal');
                doc.text('Polsek Garut Kota', 105, 28, {
                    align: 'center'
                });
                doc.text('Bagren Polres Garut', 105, 34, {
                    align: 'center'
                });

                // Line separator
                doc.setLineWidth(0.5);
                doc.line(20, 40, 190, 40);

                let yPos = 50;

                // Data Pengajuan
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.text('INFORMASI PENGAJUAN', 20, yPos);
                yPos += 8;

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');

                const infoData = [
                    ['Tanggal Pengajuan', ': ' + data.tanggal],
                    ['Uraian', ': ' + data.uraian],
                    ['Sumber Dana', ': ' + data.sumberDana],
                    ['Bulan Pengajuan', ': ' + data.bulanPengajuan],
                    ['Lokasi', ': ' + data.lokasi]
                ];

                infoData.forEach(([label, value]) => {
                    doc.setFont(undefined, 'bold');
                    doc.text(label, 25, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(value, 80, yPos);
                    yPos += 6;
                });

                yPos += 5;

                // Penanggung Jawab
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.text('PENANGGUNG JAWAB', 20, yPos);
                yPos += 8;

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');

                const pjData = [
                    ['Penanggung Jawab', ': ' + data.penanggungJawab],
                    ['Penanggung Perbendaharaan', ': ' + data.penanggungPerbendaharaan]
                ];

                pjData.forEach(([label, value]) => {
                    doc.setFont(undefined, 'bold');
                    doc.text(label, 25, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(value, 80, yPos);
                    yPos += 6;
                });

                yPos += 5;

                // Detail Kegiatan
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.text('DETAIL KEGIATAN', 20, yPos);
                yPos += 8;

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');

                doc.setFont(undefined, 'bold');
                doc.text('Kode', 25, yPos);
                doc.setFont(undefined, 'normal');
                doc.text(': ' + data.kode, 80, yPos);
                yPos += 6;

                doc.setFont(undefined, 'bold');
                doc.text('Program/Kegiatan', 25, yPos);
                doc.setFont(undefined, 'normal');

                // Wrap long text
                const programText = doc.splitTextToSize(': ' + data.programKegiatan, 110);
                doc.text(programText, 80, yPos);
                yPos += (programText.length * 6) + 2;

                yPos += 5;

                // Rincian Anggaran
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.text('RINCIAN ANGGARAN', 20, yPos);
                yPos += 8;

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');

                const anggaranData = [
                    ['Volume', ': ' + data.vol + ' ' + data.satuan],
                    ['Harga Satuan', ': ' + formatRupiah(data.hargaSatuan)],
                    ['Jumlah Pagu', ': ' + formatRupiah(data.jumlahPagu)],
                    ['Sisa Saldo (SD)', ': ' + formatRupiah(data.sd)],
                    ['Jumlah Diajukan', ': ' + formatRupiah(data.totalAnggaran)]
                ];

                anggaranData.forEach(([label, value]) => {
                    doc.setFont(undefined, 'bold');
                    doc.text(label, 25, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(value, 80, yPos);
                    yPos += 6;
                });

                yPos += 5;

                // Status Persetujuan
                doc.setFontSize(12);
                doc.setFont(undefined, 'bold');
                doc.text('STATUS PERSETUJUAN', 20, yPos);
                yPos += 8;

                doc.setFontSize(10);
                doc.setFont(undefined, 'normal');

                const statusData = [
                    ['Status NPWP', ': ' + data.statusNPWP],
                    ['Status PPK', ': ' + data.statusPPK]
                ];

                statusData.forEach(([label, value]) => {
                    doc.setFont(undefined, 'bold');
                    doc.text(label, 25, yPos);
                    doc.setFont(undefined, 'normal');
                    doc.text(value, 80, yPos);
                    yPos += 6;
                });

                // Footer
                yPos = 270;
                doc.setFontSize(8);
                doc.setFont(undefined, 'italic');
                doc.text('Dokumen ini dicetak pada: ' + new Date().toLocaleString('id-ID'), 105, yPos, {
                    align: 'center'
                });
                doc.text('Sistem Informasi Perencanaan Anggaran - Bagren Polres Garut', 105, yPos + 5, {
                    align: 'center'
                });

                // Save PDF
                const filename = 'Detail_Pengajuan_' + data.tanggal.replace(/\//g, '-') + '_' + data.kode.substring(0, 10) + '.pdf';
                doc.save(filename);

                showCustomAlert('Detail pengajuan berhasil dicetak!', 'success', 'Cetak Berhasil');
            } catch (error) {
                console.error(error);
                showCustomAlert('Gagal mencetak detail. Silakan coba lagi.', 'error');
            }
        }

        // Load riwayat data from database
        async function loadRiwayatData() {
            try {
                const response = await fetch('api/main.php?action=get_riwayat');
                const result = await response.json();

                if (result.success) {
                    riwayatData = result.data;
                    renderTable(riwayatData);
                } else {
                    console.error('Failed to load riwayat data:', result.message);
                    showCustomAlert('Gagal memuat data riwayat', 'error');
                }
            } catch (error) {
                console.error('Error loading riwayat data:', error);
                showCustomAlert('Terjadi kesalahan saat memuat data', 'error');
            }
        }

        // Initialize page
        window.onload = function() {
            loadRiwayatData();
        };

        // Dropdown Menu Logic
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('userMenuButton');
            const dropdown = document.getElementById('userDropdown');

            if (menuButton && dropdown) {
                menuButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    dropdown.classList.toggle('show');
                    menuButton.querySelector('.chevron-down').classList.toggle('rotated');
                });

                // Close dropdown if clicked outside
                window.addEventListener('click', function(event) {
                    if (!menuButton.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.remove('show');
                        menuButton.querySelector('.chevron-down').classList.remove('rotated');
                    }
                });

                // Close with escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        dropdown.classList.remove('show');
                        menuButton.querySelector('.chevron-down').classList.remove('rotated');
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('userDropdownBtn'),
                menu = document.getElementById('userDropdownMenu');
            if (!btn || !menu) return;
            var wrap = btn.parentNode;
            btn.onclick = function(e) {
                e.stopPropagation();
                wrap.classList.toggle('open');
            };
            document.addEventListener('click', function(e) {
                if (!wrap.contains(e.target)) wrap.classList.remove('open');
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') wrap.classList.remove('open');
            });
        });
    </script>
</body>

</html>