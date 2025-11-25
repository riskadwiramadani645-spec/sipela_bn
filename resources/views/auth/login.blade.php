<!DOCTYPE html>
<html lang="id">
@include('partials.head', ['title' => 'Login - SIPELA'])
    <link href="{{ asset('assets/css/auth-admin.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="login-container flex-grow-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="login-card">
                        <div class="row h-100">
                            <!-- Info Section (Kiri) -->
                            <div class="col-md-6">
                                <div class="info-section">
                                    <div class="text-center mb-4">
                                        <div class="admin-logo">
                                            <img src="{{ asset('assets/img/bn.png') }}" alt="Logo BN">
                                        </div>
                                        <h2 class="text-white mb-3">SIPELA</h2>
                                        <h5 class="text-white-75 mb-4">Sistem Informasi Pelanggaran</h5>
                                    </div>
                                    <div class="text-white-50">
                                        <h6 class="text-white mb-3">Portal Login</h6>
                                        <p class="mb-3">Masuk ke sistem menggunakan akun yang telah didaftarkan oleh administrator.</p>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-user-shield me-2 text-primary"></i>
                                            <span>Multi-level access</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-graduation-cap me-2 text-success"></i>
                                            <span>Student management</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-chart-line me-2 text-warning"></i>
                                            <span>Reporting system</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Form Section (Kanan) -->
                            <div class="col-md-6">
                                <div class="form-section">
                                    <div class="admin-warning">
                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Login to SIPELA System</span>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger" style="background: rgba(30, 58, 138, 0.1); border: 1px solid rgba(30, 58, 138, 0.3); color: #93c5fd;">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('login.post') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label text-white">Username</label>
                                            <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}" required>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="form-label text-white">Password</label>
                                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt me-2"></i>Login
                                            </button>
                                            <a href="/" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Portal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @include('partials.footer-auth')
    @include('partials.scripts')
</body>
</html>