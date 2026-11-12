<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gym') }} — Admin Panel</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('fitgym_theme') || 'dark';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,800|dm-sans:400,500,700" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .starfield {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
            background: radial-gradient(circle at 50% 0%, rgba(58, 28, 113, 0.25), transparent 60%),
                        radial-gradient(circle at 85% 20%, rgba(255, 209, 102, 0.06), transparent 40%);
        }

        .stars-layer-1, .stars-layer-2 {
            position: absolute;
            inset: 0;
            background-repeat: repeat;
        }

        .stars-layer-1 {
            background-image: 
                radial-gradient(1.5px 1.5px at 25px 35px, #fff, transparent),
                radial-gradient(1px 1px at 85px 120px, rgba(255,255,255,0.8), transparent),
                radial-gradient(2px 2px at 160px 60px, #ffd166, transparent),
                radial-gradient(1.5px 1.5px at 230px 190px, #fff, transparent),
                radial-gradient(1px 1px at 310px 80px, rgba(255,255,255,0.7), transparent),
                radial-gradient(1.5px 1.5px at 40px 260px, #ffd166, transparent),
                radial-gradient(1px 1px at 180px 290px, #fff, transparent);
            background-size: 350px 320px;
            animation: twinkle 4s ease-in-out infinite alternate;
        }

        .stars-layer-2 {
            background-image: 
                radial-gradient(1px 1px at 50px 90px, #fff, transparent),
                radial-gradient(2px 2px at 130px 210px, #ffd166, transparent),
                radial-gradient(1.5px 1.5px at 210px 40px, rgba(255,255,255,0.9), transparent),
                radial-gradient(1px 1px at 290px 150px, #fff, transparent),
                radial-gradient(1.5px 1.5px at 370px 270px, #ffd166, transparent);
            background-size: 420px 380px;
            animation: twinkle 6s ease-in-out infinite alternate-reverse;
        }

        .floating-star {
            position: absolute;
            color: #ffd166;
            user-select: none;
            filter: drop-shadow(0 0 6px rgba(255, 209, 102, 0.7));
            animation: floatPulse 3.5s ease-in-out infinite;
        }

        @keyframes twinkle {
            0% { opacity: 0.25; }
            100% { opacity: 0.9; }
        }

        @keyframes floatPulse {
            0%, 100% { transform: scale(0.85) rotate(0deg); opacity: 0.4; }
            50% { transform: scale(1.2) rotate(12deg); opacity: 1; }
        }

        /* Sidebar Styling */
        .admin-sidebar {
            background: linear-gradient(180deg, rgba(28, 22, 56, 0.98), rgba(16, 12, 36, 0.98)) !important;
            border-right: 1px solid rgba(255, 209, 102, 0.15);
            backdrop-filter: blur(14px);
            z-index: 1045;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-nav {
            padding: 1rem 0.85rem;
        }

        .sidebar-nav-heading {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(241, 239, 255, 0.45);
            font-weight: 700;
            padding: 0.75rem 0.75rem 0.35rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 0.9rem;
            color: rgba(241, 239, 255, 0.7);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            border-radius: 0.65rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }

        .sidebar-link .link-icon {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            opacity: 0.8;
            transition: transform 0.2s ease;
        }

        .sidebar-link:hover {
            color: #ffd166;
            background: rgba(255, 209, 102, 0.08);
            transform: translateX(3px);
        }

        .sidebar-link:hover .link-icon {
            transform: scale(1.15);
            opacity: 1;
        }

        .sidebar-link.active {
            color: #ffd166;
            background: linear-gradient(90deg, rgba(255, 209, 102, 0.16), rgba(255, 92, 138, 0.08));
            border-left: 3px solid #ffd166;
            font-weight: 700;
        }

        .sidebar-link.active .link-icon {
            opacity: 1;
        }

        .sidebar-user {
            margin: 0.75rem;
            padding: 0.85rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 209, 102, 0.12);
            border-radius: 0.75rem;
        }

        /* Desktop Layout */
        @media (min-width: 992px) {
            .admin-sidebar {
                width: 270px !important;
                position: fixed !important;
                top: 0;
                bottom: 0;
                left: 0;
                transform: none !important;
                visibility: visible !important;
                overflow-y: auto;
            }

            .admin-main {
                margin-left: 270px;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                position: relative;
            }

            .admin-topbar-mobile {
                display: none !important;
            }
        }

        /* Mobile Layout */
        @media (max-width: 991.98px) {
            .admin-main {
                margin-left: 0;
                min-height: 100vh;
                position: relative;
            }

            .admin-topbar-mobile {
                display: flex !important;
                background: rgba(20, 17, 58, 0.92);
                backdrop-filter: blur(14px);
                border-bottom: 1px solid rgba(255, 209, 102, 0.2);
                position: sticky;
                top: 0;
                z-index: 1030;
                padding: 1rem 1.25rem;
                min-height: 64px;
            }
        }

        .modal {
            z-index: 1060 !important;
        }

        .modal-backdrop {
            z-index: 1050 !important;
        }

        .admin-topbar-desktop {
            background: linear-gradient(90deg, rgba(22, 17, 52, 0.92), rgba(30, 22, 68, 0.92));
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 209, 102, 0.18);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
            padding: 1.15rem 2.25rem;
            min-height: 78px;
        }
    </style>
</head>
<body>
    <!-- Latar Belakang Bintang Kosmik -->
    <div class="starfield" aria-hidden="true">
        <div class="stars-layer-1"></div>
        <div class="stars-layer-2"></div>
        <span class="floating-star" style="top: 12%; left: 8%; font-size: 14px; animation-delay: 0s;">✦</span>
        <span class="floating-star" style="top: 18%; right: 12%; font-size: 10px; animation-delay: 1.2s;">✦</span>
        <span class="floating-star" style="top: 48%; left: 4%; font-size: 9px; animation-delay: 2.1s;">✦</span>
        <span class="floating-star" style="top: 65%; right: 7%; font-size: 16px; animation-delay: 0.7s;">✦</span>
        <span class="floating-star" style="top: 84%; left: 18%; font-size: 11px; animation-delay: 1.8s;">✦</span>
        <span class="floating-star" style="top: 32%; left: 48%; font-size: 8px; animation-delay: 2.6s;">✧</span>
    </div>

    <!-- Mobile Topbar with Hamburger Toggle -->
    <header class="admin-topbar-mobile justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-outline-warning p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" style="width: 38px; height: 38px;">
                <span class="fs-5 lh-1">☰</span>
            </button>
            <span class="fw-bold fs-5 text-white">
                <span class="brand-star">✦</span> {{ config('app.name', 'FitGym') }} <small class="text-warning small" style="font-size: 0.75rem;">Admin</small>
            </span>
        </div>
        <!-- Toggle Tema Gelap / Terang Mobile -->
        <button type="button" class="btn btn-outline-warning d-flex align-items-center justify-content-center p-2 rounded-3 theme-toggle-btn" id="themeToggleBtnMobile" title="Ganti Tema (Gelap / Terang)" style="width: 40px; height: 40px;">
            <span class="theme-icon fs-5">🌙</span>
        </button>
    </header>

    <!-- Sidebar (Offcanvas on mobile, fixed left sidebar on desktop) -->
    <aside class="offcanvas-lg offcanvas-start admin-sidebar d-flex flex-column" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
        <!-- Sidebar Brand -->
        <div class="sidebar-brand d-flex align-items-center justify-content-between">
            <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <span class="brand-star fs-4">✦</span>
                <div>
                    <div class="fw-bold fs-5 text-white lh-1">{{ config('app.name', 'FitGym') }}</div>
                    <small class="text-warning text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.08em;">Panel Admin</small>
                </div>
            </a>
            <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
        </div>

        <!-- Admin Profile Card in Sidebar -->
        <div class="sidebar-user">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-dark shadow-sm"
                     style="width: 36px; height: 36px; background: var(--gym-gold); font-size: 1rem; flex-shrink: 0;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="overflow-hidden flex-grow-1">
                    <div class="fw-bold text-white text-truncate" style="font-size: 0.88rem;">{{ Auth::user()->name }}</div>
                    <span class="role-pill role-admin" style="font-size: 0.65rem; padding: 0.05rem 0.5rem;">Administrator</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="sidebar-nav flex-grow-1 overflow-y-auto">
            <div class="sidebar-nav-heading">Utama</div>
            <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <span class="link-icon">📊</span>
                <span>Dashboard</span>
            </a>

            <div class="sidebar-nav-heading mt-2">Master Data</div>
            <a href="{{ route('admin.plans.index') }}" class="sidebar-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <span class="link-icon">🏷️</span>
                <span>Paket Membership</span>
            </a>
            <a href="{{ route('admin.vouchers.index') }}" class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                <span class="link-icon">🎟️</span>
                <span>Voucher Promo</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="link-icon">👥</span>
                <span>Pengguna & Member</span>
            </a>

            <div class="sidebar-nav-heading mt-2">Operasional & Transaksi</div>
            <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <span class="link-icon">💳</span>
                <span class="flex-grow-1">Validasi Pembayaran</span>
                @php
                    $pendingCount = \App\Models\Payment::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 0.7rem;">{{ $pendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.attendances.index') }}" class="sidebar-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                <span class="link-icon">⏱️</span>
                <span>Presensi & Check-in</span>
            </a>

            <div class="sidebar-nav-heading mt-2">Laporan</div>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="link-icon">📈</span>
                <span>Laporan Keuangan</span>
            </a>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-3 border-top border-secondary-subtle">
            <a href="{{ route('logout') }}" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2"
               onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                <span>🚪</span>
                <span>Keluar Akun</span>
            </a>
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Desktop Topbar Bar -->
        <header class="admin-topbar-desktop d-none d-lg-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm"
                     style="width: 46px; height: 46px; background: rgba(255, 209, 102, 0.12); border: 1px solid rgba(255, 209, 102, 0.3);">
                    <span class="fs-4">
                        @if(request()->routeIs('home'))
                            📊
                        @elseif(request()->routeIs('admin.plans.*'))
                            🏷️
                        @elseif(request()->routeIs('admin.vouchers.*'))
                            🎟️
                        @elseif(request()->routeIs('admin.users.*'))
                            👥
                        @elseif(request()->routeIs('admin.payments.*'))
                            💳
                        @elseif(request()->routeIs('admin.attendances.*'))
                            ⏱️
                        @elseif(request()->routeIs('admin.reports.*'))
                            📈
                        @else
                            ✦
                        @endif
                    </span>
                </div>
                <div>
                    <div class="text-secondary text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 0.08em;">
                        Panel Administrator &bull; FitGym Cosmic
                    </div>
                    <h4 class="mb-0 text-white fw-bold">
                        @if(request()->routeIs('home'))
                            Dashboard Utama
                        @elseif(request()->routeIs('admin.plans.*'))
                            Kelola Paket Membership
                        @elseif(request()->routeIs('admin.vouchers.*'))
                            Kelola Voucher Promo
                        @elseif(request()->routeIs('admin.users.*'))
                            Data Pengguna & Member
                        @elseif(request()->routeIs('admin.payments.*'))
                            Validasi Pembayaran
                        @elseif(request()->routeIs('admin.attendances.*'))
                            Presensi & Check-in Member
                        @elseif(request()->routeIs('admin.reports.*'))
                            Laporan Keuangan & Analisis
                        @else
                            Modul Administrasi
                        @endif
                    </h4>
                </div>
            </div>

            <!-- Bagian Kanan: Tanggal & Switcher Tema Gelap / Terang -->
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-secondary theme-date-pill" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);">
                    <span class="text-warning">🗓️</span>
                    <span class="fw-medium small text-body">{{ now()->format('d M Y') }}</span>
                </div>

                <!-- Tombol Toggle Tema Gelap / Terang -->
                <button type="button" class="btn btn-outline-warning d-flex align-items-center gap-2 px-3 py-2 rounded-3 fw-semibold theme-toggle-btn shadow-sm" id="themeToggleBtn" title="Ganti Tema (Gelap / Terang)">
                    <span class="theme-icon fs-5">🌙</span>
                    <span class="theme-text small">Tema Gelap</span>
                </button>
            </div>
        </header>

        <!-- Page Body Content -->
        <main class="py-4 px-3 px-lg-4 flex-grow-1">
            @yield('content')
        </main>
    </div>
</body>
</html>
