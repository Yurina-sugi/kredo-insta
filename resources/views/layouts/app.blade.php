<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | @yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Image swiper --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    {{-- Google Maps API --}}
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBxIyTHVtRWu8CQG3mE_aO3RNTcGH6cN7c&libraries=places" async
        defer></script>

</head>

<body>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div class="loading-text">Loading...</div>
        </div>
    </div>

    <nav class="navbar navbar-expand-md navbar-light shadow-sm navbar-custom navbar-light-theme">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <h1 class="h5 mb-0">{{ config('app.name') }}</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                @auth
                    @if (!request()->is('admin/*'))
                        <ul class="navbar-nav ms-auto">
                            <form action="{{ route('search') }}" style="width: 300px">
                                <div class="search-bar-container">
                                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                    <input type="search" name="search" class="form-control form-control-sm search-input"
                                        placeholder="Search...">
                                </div>
                            </form>
                        </ul>
                    @endif
                @endauth

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    @guest
                    @else
                        {{-- Home --}}
                        <li class="nav-item" title="Home">
                            <a href="{{ route('index') }}" class="nav-link"><i class="fa-solid fa-house icon-sm"></i></a>
                        </li>

                        {{-- Create Post --}}
                        <li class="nav-item" title="Create Post">
                            <a href="{{ route('post.create') }}" class="nav-link"><i
                                    class="fa-solid fa-circle-plus icon-sm"></i></a>
                        </li>

                        {{-- Notifications --}}
                        <li class="nav-item" title="Notifications">
                            <a href="{{ route('notifications.index') }}" class="nav-link position-relative">
                                <i class="fa-solid fa-heart icon-sm"></i>
                                <span id="notification-badge"
                                    class="position-absolute badge rounded-pill notification-badge"
                                    style="display: none; top: 2px; right: 2px; font-size: 10px; min-width: 20px; height: 20px; line-height: 16px; padding: 0 4px; background: #ff3040; border: 2px solid #fff;">
                                    0
                                </span>
                            </a>
                        </li>

                        {{-- Account --}}
                        <li class="nav-item dropdown">
                            <button id="account-dropdown" class="btn shadow-none nav-link" data-bs-toggle="dropdown">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                        class="rounded-circle avatar-sm">
                                @else
                                    <i class="fa-solid fa-circle-user icon-sm"></i>
                                @endif
                            </button>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="account-dropdown">
                                @can('admin')
                                    <a href="{{ route('admin.users') }}" class="dropdown-item">
                                        <i class="fa-solid fa-user-gear"></i> Admin
                                    </a>
                                    <hr class="dropdown-divider">
                                @endcan
                                <a href="{{ route('profile.show', Auth::user()->id) }}" class="dropdown-item">
                                    <i class="fa-solid fa-circle-user"></i> Profile
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                    <i class="fa-solid fa-right-from-bracket"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                    {{-- Changed to language selection link list --}}
                    <li class="nav-item d-flex align-items-center lang-switcher-links">
                        <a href="{{ route('lang.switch', 'en') }}"
                            class="lang-switcher-link {{ app()->getLocale() == 'en' ? 'active-lang' : '' }}">EN</a>
                        <span class="lang-separator">|</span>
                        <a href="{{ route('lang.switch', 'ja') }}"
                            class="lang-switcher-link {{ app()->getLocale() == 'ja' ? 'active-lang' : '' }}">JP</a>
                        <span class="lang-separator">|</span>
                        <a href="{{ route('lang.switch', 'fil') }}"
                            class="lang-switcher-link {{ app()->getLocale() == 'fil' ? 'active-lang' : '' }}">PH</a>
                    </li>
                </ul>
            </div>
            <div class="me-2 text-end">
                <button id="mode-toggle" class="btn btn-outline-secondary">
                    <span id="mode-icon" class="fa fa-moon"></span>
                </button>
            </div>
        </div>
    </nav>


    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                {{-- [SOON] Admin Menu (col-3) --}}
                @if (request()->is('admin/*'))
                    <div class="col-3">
                        <div class="list-group">
                            <a href="{{ route('admin.users') }}"
                                class="list-group-item {{ request()->is('admin/users') ? 'active' : '' }}">
                                <i class="fa-solid fa-users"></i> Users
                            </a>
                            <a href="{{ route('admin.posts') }}"
                                class="list-group-item {{ request()->is('admin/posts') ? 'active' : '' }}">
                                <i class="fa-solid fa-newspaper"></i> Posts
                            </a>
                            <a href="{{ route('admin.categories') }}"
                                class="list-group-item {{ request()->is('admin/categories') ? 'active' : '' }}"">
                                <i class="fa-solid fa-tags"></i> Categories
                            </a>
                        </div>
                    </div>
                @endif

                <div class="col-9">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>
    </div>

    <script>
        // Update notification badge
        function updateNotificationBadge() {
            // Only update if user is authenticated and not on login/register pages
            if (window.location.pathname.includes('/login') || window.location.pathname.includes('/register')) {
                return;
            }

            fetch('/notifications/unread-count', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const badge = document.getElementById('notification-badge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else if (badge) {
                        badge.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching notification count:', error);
                });
        }

        // Disable prettyprint
        function disablePrettyPrint() {
            // Remove prettyprint classes
            document.querySelectorAll('.prettyprint').forEach(element => {
                element.classList.remove('prettyprint');
            });

            // Remove prettyprint scripts
            document.querySelectorAll('script[src*="prettify"], script[src*="prettyprint"]').forEach(script => {
                script.remove();
            });

            // Remove prettyprint stylesheets
            document.querySelectorAll('link[href*="prettify"], link[href*="prettyprint"]').forEach(link => {
                link.remove();
            });
        }

        // Update badge on page load with delay
        document.addEventListener('DOMContentLoaded', function() {
            disablePrettyPrint();

            // Delay notification badge update to avoid interfering with login redirect
            setTimeout(() => {
                updateNotificationBadge();
            }, 1000);

            // Update badge every 30 seconds
            setInterval(updateNotificationBadge, 30000);
        });
    </script>
</body>

</html>
