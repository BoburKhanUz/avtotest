<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- AdminLTE 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css">
    <!-- OverlayScrollbars CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">

    <style>
        /* Custom Styles */
        :root {
            --primary-color: #3b82f6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #06b6d4;
        }

        .navbar-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }

        .stats-card {
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .stats-card.success {
            border-left-color: var(--success-color);
        }

        .stats-card.danger {
            border-left-color: var(--danger-color);
        }

        .stats-card.warning {
            border-left-color: var(--warning-color);
        }

        .stats-card.info {
            border-left-color: var(--info-color);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .badge-status {
            padding: 0.35em 0.65em;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Toastr Custom Styles */
        .toast-success {
            background-color: var(--success-color) !important;
        }
        .toast-error {
            background-color: var(--danger-color) !important;
        }
        .toast-info {
            background-color: var(--info-color) !important;
        }
        .toast-warning {
            background-color: var(--warning-color) !important;
        }

        #toast-container > div {
            opacity: 0.95;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }

        .sidebar-menu .nav-header {
            color: #adb5bd;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
        }
    </style>

    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <!-- Navbar -->
    <nav class="app-header navbar navbar-expand bg-body navbar-dark navbar-primary">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                        <i class="bi bi-list"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-md-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#" data-widget="fullscreen" role="button">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </a>
                </li>
                <li class="nav-item dropdown user-menu">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <span class="d-none d-md-inline ms-1">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                        <li class="user-header text-bg-primary">
                            <i class="bi bi-person-circle" style="font-size: 4rem;"></i>
                            <p class="mt-2">
                                {{ auth()->user()->name }}
                                <small>Administrator</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <a href="{{ route('admin.profile.show', auth()->user()) }}" class="btn btn-default btn-flat">
                                <i class="bi bi-person"></i> Profile
                            </a>
                            <a href="#" class="btn btn-default btn-flat float-end" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i> Chiqish
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <i class="bi bi-car-front-fill"></i>
                <span class="brand-text fw-bold">Avto Test Pro</span>
            </a>
        </div>
        <div class="sidebar-wrapper">
            <nav class="mt-2">
                <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer2"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Content Management -->
                    <li class="nav-header">KONTENTLAR</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.tests.index') }}" class="nav-link {{ request()->routeIs('admin.tests.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-file-earmark-text"></i>
                            <p>Testlar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-folder"></i>
                            <p>Kategoriyalar</p>
                        </a>
                    </li>

                    <!-- User Management -->
                    <li class="nav-header">FOYDALANUVCHILAR</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Foydalanuvchilar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-star"></i>
                            <p>Obunalar</p>
                        </a>
                    </li>

                    <!-- Financial -->
                    <li class="nav-header">MOLIYA</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-credit-card"></i>
                            <p>To'lovlar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-box"></i>
                            <p>Tariflar</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.promocodes.index') }}" class="nav-link {{ request()->routeIs('admin.promocodes.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-tag"></i>
                            <p>Promokodlar</p>
                        </a>
                    </li>

                    <!-- Analytics -->
                    <li class="nav-header">ANALITIKA</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.test-results.index') }}" class="nav-link {{ request()->routeIs('admin.test-results.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-clipboard-data"></i>
                            <p>Test Natijalari</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.statistics') }}" class="nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-graph-up"></i>
                            <p>Keng Statistika</p>
                        </a>
                    </li>

                    <!-- API & Documentation -->
                    <li class="nav-header">API</li>
                    <li class="nav-item">
                        <a href="{{ route('admin.api-docs') }}" class="nav-link {{ request()->routeIs('admin.api-docs') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-book"></i>
                            <p>API Dokumentatsiya</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3 class="mb-0">@yield('title')</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            @yield('breadcrumb')
                            <li class="breadcrumb-item active" aria-current="page">@yield('title')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="app-footer">
        <div class="float-end d-none d-sm-inline">
            <b>Version</b> 2.0.0
        </div>
        <strong>Copyright &copy; {{ date('Y') }} <a href="#">Avto Test Pro</a>.</strong>
        Barcha huquqlar himoyalangan.
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- OverlayScrollbars -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>

<script>
    // Toastr Configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Display Laravel session messages
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif

    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif

    @if(session('info'))
        toastr.info('{{ session('info') }}');
    @endif

    @if(session('warning'))
        toastr.warning('{{ session('warning') }}');
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

    // OverlayScrollbars initialization
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector('.sidebar-wrapper');
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: 'os-theme-light',
                    autoHide: 'leave',
                    clickScroll: true,
                },
            });
        }
    });

    // Confirm delete
    function confirmDelete(message = 'Rostdan ham o\'chirmoqchimisiz?') {
        return confirm(message);
    }
</script>

@stack('scripts')
</body>
</html>
