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

    {{-- js --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


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

    <script>
        /**
         * Like button animation
         */
        function animateHeart(btn) {
            const heart = btn.querySelector('.heart-icon');
            heart.classList.add('animate');
            heart.classList.toggle('liked');
            heart.addEventListener('animationend', function handler() {
                heart.classList.remove('animate');
                heart.removeEventListener('animationend', handler);
            });
        }

        function showFloatingHearts(btn) {

            const container = btn.parentElement.querySelector('.floating-hearts-container');
            for (let i = 0; i < 2; i++) { // 2つのハートを舞わせる
                const heart = document.createElement('i');
                heart.className = 'fa-solid fa-heart floating-heart';
                // ランダムな左右位置・大きさ・回転
                const offset = (Math.random() - 0.5) * 60; // -30px〜+30px
                const scale = 1 + Math.random() * 0.5; // 1〜1.5倍
                const rotate = (Math.random() - 0.5) * 40; // -20〜+20度
                heart.style.left = `calc(50% + ${offset}px)`;
                heart.style.fontSize = `${2 * scale}rem`;
                heart.style.transform = `translate(-50%, 0) scale(${scale}) rotate(${rotate}deg)`;
                // 色をランダムにしたい場合
                // heart.style.color = ['#e0245e', '#ff69b4', '#ffb6c1'][Math.floor(Math.random()*3)];
                container.appendChild(heart);

                // アニメーション終了後に削除
                heart.addEventListener('animationend', () => {
                    heart.remove();
                });
            }
            // 既存のlikeアニメーションも同時に発火したい場合はここで呼ぶ
            animateHeart(btn);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('mode-toggle');
            const modeIcon = document.getElementById('mode-icon');
            const modeLabel = document.getElementById('mode-label');

            // ローカルストレージからモードを取得
            let mode = localStorage.getItem('color-mode') || 'light';
            setMode(mode);

            toggleBtn.addEventListener('click', function() {
                mode = (mode === 'light') ? 'dark' : 'light';
                setMode(mode);
                localStorage.setItem('color-mode', mode);
            });

            function setMode(mode) {
                document.body.classList.remove('light-mode', 'dark-mode');
                document.body.classList.add(mode + '-mode');
                if (mode === 'dark') {
                    modeIcon.className = 'fa fa-sun';
                } else {
                    modeIcon.className = 'fa fa-moon';
                }
            }
        });
    </script>

    <script>
        let map, marker, autocomplete;

        function initMap() {
            const mapElement = document.getElementById('map');
            const geoAlert = document.getElementById('geolocation-alert');
            if (!mapElement) {
                console.error('Map element not found');
                return;
            }

            // デフォルト位置（東京駅）
            const defaultLatLng = new google.maps.LatLng(35.681236, 139.767125);

            // 現在地を取得
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // 位置情報取得成功時にalertを非表示
                        if (geoAlert) geoAlert.style.display = 'none';

                        const currentLatLng = new google.maps.LatLng(
                            position.coords.latitude,
                            position.coords.longitude
                        );
                        initializeMapWithLocation(currentLatLng);
                        initializeAutocomplete();
                    },
                    function(error) {
                        // 失敗時はalertをそのまま表示
                        console.log('Geolocation error:', error);
                        initializeMapWithLocation(defaultLatLng);
                        initializeAutocomplete();
                    }
                );
            } else {
                initializeMapWithLocation(defaultLatLng);
                initializeAutocomplete();
            }
        }

        function initializeMapWithLocation(latLng) {
            map = new google.maps.Map(document.getElementById('map'), {
                center: latLng,
                zoom: 15,
            });

            marker = new google.maps.Marker({
                position: latLng,
                map: map,
                draggable: true,
            });

            // マーカー移動時に緯度経度をhiddenにセット
            marker.addListener('dragend', function() {
                updateHiddenFields(marker.getPosition());
            });

            // 地図クリックでマーカー移動
            map.addListener('click', function(e) {
                marker.setPosition(e.latLng);
                updateHiddenFields(e.latLng);
            });

            // 初期値セット
            updateHiddenFields(latLng);
        }

        function initializeAutocomplete() {
            const input = document.getElementById('location_search');
            if (!input) return;

            // Enterキーでフォーム送信を防ぐ
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    return false;
                }
            });

            // Autocompleteの設定
            autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'], // 住所のみ
                //componentRestrictions: { country: 'jp' }, // 日本限定（オプション）
            });

            // 住所が選択された時の処理
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();

                if (!place.geometry) {
                    console.log("No geometry found for the selected place");
                    return;
                }

                // 地図を選択された場所に移動
                const latLng = place.geometry.location;
                map.setCenter(latLng);
                marker.setPosition(latLng);

                // ズームレベルを調整
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setZoom(17);
                }

                // hiddenフィールドを更新
                updateHiddenFields(latLng, place.formatted_address);
            });
        }

        function updateHiddenFields(latLng, address = null) {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const addressInput = document.getElementById('location_name');

            if (latInput && lngInput) {
                latInput.value = latLng.lat();
                lngInput.value = latLng.lng();
            }

            if (addressInput && address) {
                addressInput.value = address;
            }
        }

        // DOMが読み込まれた後にinitMapを実行
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof google !== 'undefined' && google.maps) {
                initMap();
            } else {
                setTimeout(initMap, 1000);
            }
        });

        window.initMap = initMap;
    </script>

    <style>
        /* ナビゲーションバーの共通スタイル */
        .navbar-custom {
            border-bottom: none;
        }

        /* ライトテーマ（ログイン状態に関わらず統一） */
        .navbar-light-theme {
            background-color: #F9F5EE !important;
            /* 白 */
            border-bottom: none;
            box-shadow: none;
            border-radius: 0;
        }

        .navbar-light-theme .navbar-brand,
        .navbar-light-theme .nav-link,
        .navbar-light-theme .navbar-toggler-icon {
            color: #B39A84 !important;
            /* ベージュ */
            font-family: sans-serif;
        }

        .navbar-light-theme .navbar-brand h1 {
            color: #B39A84 !important;
            /* ベージュ */
        }

        .navbar-light-theme .navbar-toggler {
            border-color: #B39A84;
            /* ベージュ */
        }

        .navbar-light-theme .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28179, 154, 132, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }

        /* 検索バーのスタイル */
        .navbar-light-theme .search-bar-container {
            position: relative;
            display: flex;
            /* アイコンと入力フィールドを整列 */
            align-items: center;
            width: 300px;
            /* フォームの幅を維持 */
        }

        .navbar-light-theme .search-icon {
            position: absolute;
            /* コンテナ内で絶対位置指定 */
            left: 5px;
            /* アイコンの左からの位置 */
            color: #B39A84;
            /* ベージュ色 (虫眼鏡アイコンの色) */
            font-size: 1rem;
            z-index: 1;
            /* アイコンが入力フィールドの上に表示されるように */
        }

        .navbar-light-theme .search-input {
            border: none;
            /* デフォルトのボーダーを削除 */
            border-bottom: 1px solid #B39A84;
            /* ベージュ色のアンダーライン */
            background-color: transparent;
            /* 背景を透明に */
            padding-left: 30px;
            /* アイコンのためのスペース */
            padding-right: 5px;
            /* 右側の小さなパディング */
            color: #0A1E54;
            /* ネイビーの文字色 */
            border-radius: 0;
            /* 角丸を削除 */
            box-shadow: none;
            /* 影を削除 */
            width: 100%;
            /* コンテナの全幅を使用 */
        }

        .navbar-light-theme .search-input::placeholder {
            color: rgba(10, 30, 84, 0.7);
            /* 半透明のネイビーのプレースホルダー */
        }

        .navbar-light-theme .search-input:focus {
            background-color: transparent;
            /* フォーカス時も透明を維持 */
            border-color: #0A1E54;
            /* フォーカス時はネイビーのアンダーライン */
            box-shadow: none;
            color: #0A1E54;
        }

        /* 既存のフォームコントロールスタイルは、より具体的な.search-inputで上書きされる */
        .navbar-light-theme .form-control {
            /* このスタイルは、.search-inputによって上書きされるため、
       他のフォームコントロールに影響を与える場合にのみ調整が必要 */
            border-color: #B39A84;
            /* ベージュ */
            background-color: transparent;
            /* 半透明のベージュ */
            color: #0A1E54;

        }

        .navbar-light-theme .form-control::placeholder {
            color: rgba(10, 30, 84, 0.7);
            /* 半透明のネイビー */
        }

        .navbar-light-theme .form-control:focus {
            background-color: transparent;
            border-color: #B39A84;
            box-shadow: none;
            color: #0A1E54;
        }


        .navbar-light-theme .icon-sm {
            color: #B39A84 !important;
            /* ベージュ */
        }

        .navbar-light-theme .avatar-sm {
            border: 1px solid #B39A84;
            /* ベージュ */
        }

        /* ドロップダウンメニュー（統一テーマで共通） */
        .dropdown-menu {
            background-color: #F9F5EE;
            /* 白 */
            border: 1px solid #CFC7C8;
            /* グレー */
            border-radius: 5px;
        }

        .dropdown-item {
            color: #0A1E54;
            /* ネイビー */
            font-family: sans-serif;
        }

        .dropdown-item:hover {
            background-color: #CFC7C8;
            /* グレー */
            color: #0A1E54;
            /* ネイビー */
        }
    </style>
</head>

<body>

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
                    {{-- 言語選択リンクのリストに変更 --}}
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
