<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'School MS') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 280px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 18px;
        }

        .sidebar-header.collapsed h4 {
            display: none;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 2px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .nav-link:hover,
        .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            margin: 2px 15px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        .top-navbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 0;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-wrapper {
            padding: 25px;
        }

        /* Card Enhancements */
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.5rem;
            transition: box-shadow 0.15s ease-in-out;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
        }

        /* Button Enhancements */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        /* Table Enhancements */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.025);
        }

        /* Badge Enhancements */
        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }

        /* Form Enhancements */
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Alert Enhancements */
        .alert {
            border: none;
            border-radius: 0.5rem;
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }

        .modal-header {
            border-bottom: 1px solid #dee2e6;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block !important;
            }
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Utility Classes */
        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h4><i class="fas fa-graduation-cap me-2"></i><span>School MS</span></h4>
            </div>
            <ul class="nav flex-column mt-3">
                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <span>Admin Management</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.management.*') ? 'active' : '' }}" href="{{ route('admin.management.index') }}">
                                <i class="far fa-circle nav-icon"></i> Admins
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}" href="{{ route('admin.audit-logs.index') }}">
                                <i class="far fa-circle nav-icon"></i> Audit Logs
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                        <i class="fas fa-user-graduate"></i>
                        <span>Students</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('grades.*') ? 'active' : '' }}" href="{{ route('grades.index') }}">
                        <i class="fas fa-tasks"></i>
                        <span>Grade Entry</span>
                    </a>
                </li>

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teacher_courses.*') ? 'active' : '' }}" href="{{ route('teacher_courses.index') }}">
                        <i class="fas fa-link"></i>
                        <span>Teacher-Course Assignments</span>
                    </a>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Teachers</span>
                    </a>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}" href="{{ route('classes.index') }}">
                        <i class="fas fa-school"></i>
                        <span>Classes</span>
                    </a>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">
                        <i class="fas fa-book"></i>
                        <span>Courses</span>
                    </a>
                </li>
                @endcan

                @can('viewAny', App\Models\User::class)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('academics.*') ? 'active' : '' }}" href="{{ route('academics.index') }}">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <span>Academics</span>
                    </a>
                </li>
                @endcan

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.index') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Results</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.1);">

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="main-content" id="main-content">
            <!-- Top Navigation -->
            <nav class="top-navbar">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link d-none mobile-menu-btn me-2" id="mobile-menu-btn">
                            <i class="fas fa-bars"></i>
                        </button>
                        <button class="btn btn-link me-3" id="sidebar-toggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    </div>

                    <div class="d-flex align-items-center">
                        <!-- Notifications -->
                        <div class="dropdown me-3">
                            <button class="btn btn-link position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">New student registered</a></li>
                                <li><a class="dropdown-item" href="#">Grade entry pending</a></li>
                                <li><a class="dropdown-item" href="#">System update available</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">View all notifications</a></li>
                            </ul>
                        </div>

                        <!-- User Profile -->
                        <div class="dropdown">
                            <button class="btn btn-link d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <div class="avatar me-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                </div>
                                <span class="me-1">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down small"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><h6 class="dropdown-header">{{ Auth::user()->email }}</h6></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            $('#sidebar-toggle').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('#main-content').toggleClass('expanded');
            });

            // Mobile Menu Toggle
            $('#mobile-menu-btn').on('click', function() {
                $('#sidebar').toggleClass('show');
            });

            // Close mobile menu when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sidebar, #mobile-menu-btn').length) {
                    $('#sidebar').removeClass('show');
                }
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize popovers
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl);
            });

            // Auto-hide alerts
            $('.alert').each(function() {
                var alert = this;
                setTimeout(function() {
                    $(alert).fadeOut('slow');
                }, 5000);
            });

            // CSRF Token Setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Global AJAX Error Handler
            $(document).ajaxError(function(event, xhr, settings, thrownError) {
                if (xhr.status === 419) {
                    alert('Session expired. Please refresh the page and try again.');
                    location.reload();
                } else if (xhr.status === 403) {
                    alert('You do not have permission to perform this action.');
                } else if (xhr.status === 500) {
                    alert('An internal server error occurred. Please try again.');
                }
            });

            // Form Submission Loading State
            $('form').on('submit', function() {
                var submitBtn = $(this).find('button[type="submit"]');
                if (submitBtn.length) {
                    submitBtn.prop('disabled', true);
                    submitBtn.html('<span class="loading-spinner me-2"></span>' + submitBtn.text());
                }
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        });

        // Global JavaScript functions
        window.showToast = function(message, type = 'success') {
            const toastHtml = `
                <div class="toast align-items-center text-bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;

            if (!$('#toast-container').length) {
                $('body').append('<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>');
            }

            const $toast = $(toastHtml);
            $('#toast-container').append($toast);

            const toast = new bootstrap.Toast($toast[0]);
            toast.show();

            $toast.on('hidden.bs.toast', function() {
                $(this).remove();
            });
        };

        window.confirmAction = function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
