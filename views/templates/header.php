<?php
// Function to determine the correct base path for assets
function getBasePath() {
    $current_path = $_SERVER['PHP_SELF'];
    $base_path = '';
    
    // Count directory levels from views folder
    if (strpos($current_path, '/views/admin/') !== false) {
        $base_path = '../../';
    } elseif (strpos($current_path, '/views/petugas/') !== false) {
        $base_path = '../../';
    } elseif (strpos($current_path, '/views/mahasiswa/') !== false) {
        $base_path = '../../';
    } elseif (strpos($current_path, '/views/auth/') !== false) {
        $base_path = '../../';
    } elseif (strpos($current_path, '/views/templates/') !== false) {
        $base_path = '../../';
    } elseif (strpos($current_path, '/views/') !== false) {
        $base_path = '../';
    } else {
        // If called from root or other location
        $base_path = '';
    }
    
    return $base_path;
}

$base_path = getBasePath();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Apel Tingkat</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= $base_path ?>public/css/modern-style.css">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom inline styles untuk konsistensi -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .btn-success { background-color: var(--success-color); }
        .btn-success:hover { background-color: #218838; }
        
        .btn-danger { background-color: var(--danger-color); }
        .btn-danger:hover { background-color: #c82333; }
        
        .btn-warning { background-color: var(--warning-color); color: #212529; }
        .btn-warning:hover { background-color: #e0a800; }
        
        .btn-info { background-color: var(--info-color); }
        .btn-info:hover { background-color: #138496; }
        
        .btn-secondary { background-color: var(--secondary-color); }
        .btn-secondary:hover { background-color: #5a6268; }

        /* Card Styles */
        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .card-header {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .table th {
            background-color: #e9ecef;
            font-weight: 600;
            color: var(--dark-color);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--dark-color);
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }

        /* Grid System */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col {
            flex: 1;
            padding: 0 10px;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 10px;
        }

        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 10px;
        }

        .col-md-3 {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .col-md-6, .col-md-4, .col-md-3 {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .container {
                padding: 0 15px;
            }
            
            .card {
                padding: 15px;
            }
            
            .table {
                font-size: 12px;
            }
            
            .table th, .table td {
                padding: 8px;
            }
        }

        /* Status badges */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Loading spinner */
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Utility classes */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .mt-3 { margin-top: 1rem; }
        .mb-3 { margin-bottom: 1rem; }
        .p-3 { padding: 1rem; }
        .d-none { display: none; }
        .d-block { display: block; }
        .d-flex { display: flex; }
        .justify-content-center { justify-content: center; }
        .align-items-center { align-items: center; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="header-brand">
                    <i class="fas fa-graduation-cap"></i>
                    <div class="brand-text">
                        <h1>Sistem Absensi Apel Tingkat</h1>
                        <span>Politeknik Statistika STIS</span>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
</div>
</body>
</html>
