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

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* ネイビーバージョン用のスタイル */
        .navbar-navy {
            background-color: #0A1E54 !important;
            /* navy */
            border-bottom: none;
            box-shadow: none;
        }

        .navbar-navy .navbar-brand,
        .navbar-navy .nav-link,
        .navbar-navy .navbar-toggler-icon {
            color: #F9F5EE !important;
            /* white */
            font-family: sans-serif;
        }

        .navbar-navy .navbar-brand h1 {
            color: #F9F5EE !important;
            /* white */
        }

        .navbar-navy .navbar-toggler {
            border-color: #F9F5EE;
            /* white */
        }

        .navbar-navy .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28249, 245, 238, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        .navbar-navy .form-control {
            border-color: #F9F5EE;
            /* white */
            background-color: rgba(249, 245, 238, 0.2);
            /* 半透明の白 */
            color: #F9F5EE;
            /* white */
            border-radius: 5px;
        }

        .navbar-navy .form-control::placeholder {
            color: rgba(249, 245, 238, 0.7);
            /* 半透明の白 */
        }

        .navbar-navy .form-control:focus {
            background-color: rgba(249, 245, 238, 0.3);
            border-color: #F9F5EE;
            box-shadow: none;
            color: #F9F5EE;
        }

        .navbar-navy .icon-sm {
            color: #F9F5EE !important;
            /* white */
        }

        .navbar-navy .dropdown-menu {
            background-color: #F9F5EE;
            /* white */
            border: 1px solid #CFC7C8;
            /* gray */
            border-radius: 5px;
        }

        .navbar-navy .dropdown-item {
            color: #0A1E54;
            /* navy */
            font-family: sans-serif;
        }

        .navbar-navy .dropdown-item:hover {
            background-color: #CFC7C8;
            /* gray */
            color: #0A1E54;
            /* navy */
        }

        .navbar-navy .avatar-sm {
            border: 1px solid #F9F5EE;
            /* white */
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-md navbar-light shadow-sm navbar-navy">
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
                {{-- [SOON] Search bar here. Show it when a user logs in --}}
                @auth
                    @if (!request()->is('admin/*'))
                        <ul class="navbar-nav ms-auto">
                            <form action="{{ route('search') }}" style="width: 300px">
                                <input type="search" name="search" class="form-control form-control-sm"
                                    placeholder="Search...">
                            </form>
                        </ul>
                    @endif
                @endauth


                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
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
                        {{-- Home --}}
                        <li class="nav-item" title="Home">
                            <a href="{{ route('index') }}" class="nav-link"><i
                                    class="fa-solid fa-house text-light icon-sm"></i></a>
                        </li>

                        {{-- Create Post --}}
                        <li class="nav-item" title="Create Post">
                            <a href="{{ route('post.create') }}" class="nav-link"><i
                                    class="fa-solid fa-circle-plus text-light icon-sm"></i></a>
                        </li>

                        {{-- Account --}}
                        <li class="nav-item dropdown">
                            <button id="account-dropdown" class="btn shadow-none nav-link" data-bs-toggle="dropdown">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                        class="rounded-circle avatar-sm">
                                @else
                                    <i class="fa-solid fa-circle-user text-light icon-sm"></i>
                                @endif
                            </button>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="account-dropdown">
                                {{-- [SOON] Admin Controls --}}
                                @can('admin')
                                    <a href="{{ route('admin.users') }}" class="dropdown-item">
                                        <i class="fa-solid fa-user-gear"></i> Admin
                                    </a>

                                    <hr class="dropdown-divider">
                                @endcan
                                {{-- Profile --}}
                                <a href="{{ route('profile.show', Auth::user()->id) }}" class="dropdown-item">
                                    <i class="fa-solid fa-circle-user"></i> Profile
                                </a>

                                {{-- Logout --}}
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
                </ul>
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
</body>

</html>
