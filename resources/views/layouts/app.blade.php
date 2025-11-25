<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'SIPELA - Sistem Informasi Pelanggaran Siswa')</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link href="{{ asset('assets/img/favicon.ico') }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    
    <!-- Modal Fix Stylesheet -->
    <link href="{{ asset('assets/css/modal-fix.css') }}" rel="stylesheet">
    
    <!-- Date Picker Stylesheet -->
    <link href="{{ asset('css/date-picker.css') }}" rel="stylesheet">

</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="{{ route('dashboard') }}" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>SIPELA</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="{{ session('user')->profile_photo_url ?? asset('img/default-avatar.svg') }}" alt="Profile" style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;" onclick="window.location.href='{{ route('profile.index') }}'">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">{{ session('user')->nama ?? 'User' }}</h6>
                        <span>{{ ucfirst(str_replace('_', ' ', session('user')->level ?? 'guest')) }}</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    @include('partials.sidebar-dynamic')
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="{{ route('dashboard') }}" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="{{ session('user')->profile_photo_url ?? asset('img/default-avatar.svg') }}" alt="Profile" style="width: 40px; height: 40px; object-fit: cover;">
                            <span class="d-none d-lg-inline-flex">{{ session('user')->nama ?? 'User' }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="{{ route('profile.index') }}" class="dropdown-item">Profile Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Content Body Start -->
            <div class="container-fluid pt-4 px-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
            <!-- Content Body End -->

            @include('partials.footer')
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/lib/chart/chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('assets/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('assets/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Template Javascript -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    <!-- Dashboard Fix Script -->
    <script src="{{ asset('assets/js/dashboard-fix.js') }}"></script>
    
    <!-- Enhanced DataTable Script -->
    <script src="{{ asset('assets/js/datatable.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
    
    <!-- Global CSRF Token -->
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    
    <!-- Modal Backdrop Fix -->
    <script>
    // Global modal backdrop cleanup
    document.addEventListener('DOMContentLoaded', function() {
        cleanupBackdrops();
    });
    
    // Cleanup function
    function cleanupBackdrops() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = 'auto';
        document.body.style.paddingRight = '';
    }
    
    // Auto cleanup every 2 seconds
    setInterval(cleanupBackdrops, 2000);
    
    // Cleanup on any click
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.modal')) {
            setTimeout(cleanupBackdrops, 100);
        }
    });
    
    // Cleanup on modal events
    document.addEventListener('hidden.bs.modal', cleanupBackdrops);
    document.addEventListener('hide.bs.modal', cleanupBackdrops);
    </script>
</body>

</html>