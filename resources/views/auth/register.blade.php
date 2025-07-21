@extends('layouts.app')

@section('title', 'Register')

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
                                <a class="nav-link" id="login-tab" href="{{ route('login') }}" role="tab"
                                    aria-controls="login" aria-selected="false">Login</a>

                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="regi-tab" role="tab" aria-controls="register"
                                    aria-selected="true">Register</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Name') }}</label>
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email">
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
                                    autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password-confirm" class="form-label">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
