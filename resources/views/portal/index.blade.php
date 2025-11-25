<!DOCTYPE html>
<html lang="id">
@include('partials.head', ['title' => 'SIPELA - Portal Akses'])
<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 50%, #4a5568 100%);
            min-height: 100vh;
            font-family: 'Nunito', 'Roboto', 'Arial', sans-serif;
            color: white;
        }
        
        .header {
            position: absolute;
            top: 30px;
            left: 50px;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 10;
        }
        
        .header-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
        }
        
        .header-logo img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }
        
        .header-text {
            color: white;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .main-container {
            min-height: calc(100vh - 100px);
            display: flex;
            align-items: center;
            padding: 100px 50px 0 50px;
        }
        
        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            align-items: center;
        }
        
        .left-content {
            color: white;
            animation: slideInLeft 0.8s ease-out;
        }
        
        .main-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            color: white;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
        }
        
        .subtitle {
            font-size: 1rem;
            color: #cbd5e1;
            margin-bottom: 35px;
            line-height: 1.6;
            font-weight: 400;
            opacity: 0.95;
        }
        
        .action-buttons {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .btn-action {
            padding: 16px 32px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-family: 'Nunito', sans-serif;
            min-height: 48px;
        }
        
        .btn-action:active {
            transform: scale(0.98);
        }
        
        .btn-action:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        .btn-action.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .btn-login {
            background: #3b82f6;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        }
        
        .btn-login:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
            color: white;
        }
        
        .btn-register {
            background: transparent;
            color: white;
            border: 2px solid #3b82f6;
        }
        
        .btn-register:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-2px);
        }
        
        .system-info {
            color: #a0aec0;
            font-size: 0.9rem;
            font-weight: 400;
        }
        
        .status-indicator {
            width: 8px;
            height: 8px;
            background: #48bb78;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .right-content {
            display: flex;
            justify-content: center;
            align-items: center;
            animation: slideInRight 0.8s ease-out;
        }
        
        .sipela-icon {
            width: 350px;
            height: 350px;
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 8px solid rgba(30, 58, 138, 0.2);
        }
        
        .sipela-icon::before {
            content: '';
            position: absolute;
            top: -15px;
            left: -15px;
            right: -15px;
            bottom: -15px;
            border: 3px solid rgba(30, 58, 138, 0.3);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .sipela-icon i {
            font-size: 6rem;
            color: #1e3a8a;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }
        
        @media (max-width: 768px) {
            .header {
                top: 20px;
                left: 20px;
            }
            
            .main-container {
                padding: 80px 20px 0 20px;
            }
            
            .content-wrapper {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }
            
            .main-title {
                font-size: 2.4rem;
            }
            
            .header-text {
                font-size: 1.4rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-action {
                width: 200px;
                justify-content: center;
            }
            
            .sipela-icon {
                width: 250px;
                height: 250px;
            }
            
            .sipela-icon i {
                font-size: 4rem;
            }
        }
        
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Header dengan Logo dan Nama -->
    <div class="header">
        <div class="header-logo">
            <img src="{{ asset('assets/img/bn.png') }}" alt="Logo BN">
        </div>
        <div class="header-text">SIPELA_BN</div>
    </div>

    <!-- Main Content -->
    <div class="main-container flex-grow-1">
        <div class="content-wrapper">
            <!-- Left Content -->
            <div class="left-content">
                <h1 class="main-title">Nikmati Sistem<br>Prestasi & Pelanggaran<br>Bakti Nusantara</h1>
                <p class="subtitle">
                    Sistem informasi terintegrasi untuk mengelola prestasi dan pelanggaran siswa, 
                    mendukung pembinaan karakter komprehensif di SMK Bakti Nusantara 666.
                </p>
                
                <div class="action-buttons">
                    <a href="/login" class="btn-action btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Masuk
                    </a>

                </div>
                
                <div class="system-info">
                    <p>Masuk ke sistem dengan akun yang telah terdaftar</p>
                    <p>
                        <span class="status-indicator"></span>
                        System Online • <span id="current-time"></span> WIB
                    </p>
                </div>
            </div>
            
            <!-- Right Content -->
            <div class="right-content">
                <div class="sipela-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>
    </div>
    
    @include('partials.footer-auth')
    @include('partials.scripts')
    <script>
        function updateTime() {
            const now = new Date();
            const options = {
                day: '2-digit',
                month: 'short', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            };
            const timeString = now.toLocaleDateString('id-ID', options).replace(',', ' •');
            document.getElementById('current-time').textContent = timeString;
        }

        setInterval(updateTime, 1000);
        updateTime();
        
        // Loading states untuk buttons
        document.querySelectorAll('.btn-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                this.classList.add('loading');
                setTimeout(() => {
                    this.classList.remove('loading');
                }, 2000);
            });
        });
    </script>
</body>
</html>