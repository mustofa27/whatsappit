<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - WhatsApp IT</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #f8f9fa;
        }
        .main-wrapper {
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            transition: width 0.3s ease, margin-left 0.3s ease;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-title {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            text-align: center;
            padding: 0.75rem 0.5rem;
        }
        
        .sidebar.collapsed .nav-link i {
            margin: 0 !important;
        }
        
        .sidebar-header {
            background: rgba(0,0,0,0.2);
        }
        
        .sidebar-title {
            color: #fff;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .main-content-wrapper {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            flex-grow: 1;
        }
        
        .sidebar.collapsed ~ .main-content-wrapper {
            margin-left: 70px;
        }
        
        /* Mobile Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                margin-left: -250px;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
            
            .main-content-wrapper {
                margin-left: 0 !important;
            }
        }
        
        .nav-link {
            transition: all 0.2s;
            margin: 2px 0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        
        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(3px);
        }
        
        .nav-link.bg-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }
        
        /* Navbar Styles */
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            padding: 0.75rem 1.5rem;
        }
        
        .navbar-brand {
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Table Styles */
        .table {
            font-size: 0.9rem;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
        }
        
        .table tbody tr {
            transition: all 0.2s;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8f9fb;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .card-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            background-color: #fff !important;
        }
        
        .card-header h5 {
            color: #1e293b;
            font-weight: 600;
        }
        
        /* Badge Styles */
        .badge {
            padding: 0.4rem 0.75rem;
            font-weight: 500;
            border-radius: 20px;
        }
        
        /* Button Styles */
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-outline-primary {
            border-color: #667eea;
            color: #667eea;
        }
        
        .btn-outline-primary:hover {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        /* Main content padding */
        main {
            background-color: #f8f9fa;
        }
        
        /* Stats Cards */
        .card.shadow-sm {
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
            transition: all 0.3s;
        }
        
        .card.shadow-sm:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="d-flex main-wrapper">
        <!-- Sidebar -->
        @include('admin.partials.sidebar-new')
        
        <!-- Main Content -->
        <div class="main-content-wrapper">
            <!-- Header -->
            @include('admin.partials.header-new')
            
            <!-- Content -->
            <main class="p-4">
                <!-- Alerts -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <!-- Page Content -->
                @yield('content')
            </main>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
            
            // Desktop toggle
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }
            
            // Mobile toggle
            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 992) {
                    if (!sidebar.contains(event.target) && !mobileSidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
            
            // Restore sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
            }
        });
    </script>
</body>
</html>
