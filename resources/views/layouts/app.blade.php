<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gym') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,800|dm-sans:400,500,700" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        .starfield {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
            background: radial-gradient(circle at 50% 0%, rgba(58, 28, 113, 0.25), transparent 60%),
                        radial-gradient(circle at 85% 20%, rgba(255, 209, 102, 0.06), transparent 40%);
        }

        /* Bintang Titik Kecil */
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

        /* Bintang Berkilau Melayang */
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

        /* Peningkatan Visual Kartu */
        #app {
            position: relative;
            z-index: 1;
        }

        .hero {
            position: relative;
            padding: 1.5rem 0 0.5rem;
        }

        .glow-card {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 209, 102, 0.14) !important;
            background: linear-gradient(145deg, rgba(28, 22, 56, 0.78), rgba(16, 12, 36, 0.9)) !important;
            backdrop-filter: blur(10px);
            transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .glow-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 209, 102, 0.4) !important;
            box-shadow: 0 10px 28px -8px rgba(255, 209, 102, 0.15);
        }

        /* Ornamen Bintang di Sudut Kartu */
        .card-star {
            position: absolute;
            top: 14px;
            right: 18px;
            color: rgba(255, 209, 102, 0.25);
            font-size: 1.1rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .glow-card:hover .card-star {
            color: #ffd166;
            transform: rotate(45deg) scale(1.2);
            text-shadow: 0 0 10px rgba(255, 209, 102, 0.8);
        }

        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
        }

        .empty-icon {
            font-size: 2rem;
            color: #ffd166;
            text-shadow: 0 0 14px rgba(255, 209, 102, 0.6);
            display: inline-block;
            animation: floatPulse 3s ease-in-out infinite;
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

    <div id="app">
        <nav class="navbar navbar-expand-md gym-nav sticky-top" data-bs-theme="dark">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <span class="brand-star">✦</span> {{ config('app.name', 'Gym') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                   href="{{ route('home') }}">Dashboard</a>
                            </li>
                            @if(auth()->user()->role === 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                                       href="{{ route('admin.test') }}">Menu Admin</a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-md-center">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                    <span class="role-pill role-{{ Auth::user()->role }} ms-1">
                                        {{ ucfirst(Auth::user()->role) }}
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>