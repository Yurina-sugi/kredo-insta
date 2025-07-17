@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <style>
        body {
            background-color: #F9F5EE;
            /* 変更後の白 */
        }

        .card {
            background-color: #F9F5EE;
            /* 変更後の白 */
            border: none;
            /* 枠を削除 */
            box-shadow: none;
            /* 影を削除 */
            border-radius: 10px;
        }

        .card-header {
            background-color: transparent;
            border-bottom: none;
            padding-bottom: 0;
        }

        .form-label {
            color: #B39A84;
            /* beige (茶色系) */
            font-family: sans-serif;
            /* 必要に応じてフォントを指定 */
            font-weight: bold;
        }

        .form-control {
            border-color: #B39A84;
            /* beige (茶色系) */
        }

        .btn-primary {
            background-color: #0A1E54;
            /* navy */
            border-color: #0A1E54;
        }

        .btn-primary:hover {
            background-color: #0A1E54;
            border-color: #0A1E54;
        }

        .forgot-password-link {
            color: #B39A84;
            /* beige */
            font-weight: bold;
        }

        .nav-tabs-custom {
            border-bottom: 1px solid #CFC7C8;
            /* gray */
            margin-bottom: 20px;
        }

        .nav-tabs-custom .nav-item {
            margin-right: 0;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            border-bottom: 2px solid transparent;
            background-color: #F9F5EE;
            /* 変更後の白 */
            color: #0A1E54;
            /* navy */
            border-radius: 5px 5px 0 0;
            padding: 8px 20px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s, border-bottom-color 0.3s;
        }

        .nav-tabs-custom .nav-link.active {
            background-color: #F9F5EE;
            /* 変更後の白 */
            color: #B39A84;
            /* beige (茶色系) */
            border-bottom-color: #B39A84;
            /* beige (茶色系) */
        }

        .nav-tabs-custom .nav-link:hover:not(.active) {
            background-color: #CFC7C8;
            /* gray */
            color: #0A1E54;
            /* navy */
        }
    </style>

    <div class="container py-5">
        <h1 class="text-center mb-4" style="color: #0A1E54; font-weight: bold;">Insta</h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="login-tab" role="tab"
                                    aria-controls="login" aria-selected="true">Login</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="regi-tab" href="{{ route('register') }}" role="tab"
                                    aria-controls="register" aria-selected="false">Register</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end mb-3">
                                @if (Route::has('password.request'))
                                    <a class="forgot-password-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password !?') }}
                                    </a>
                                @endif
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
