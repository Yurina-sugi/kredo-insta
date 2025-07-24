@extends('layouts.app')

@section('title', 'Home')

@section('content')

    {{-- ストーリーバー --}}
    <div class="d-flex overflow-auto mb-4" style="gap: 16px;">
        <div class="d-flex overflow-auto" style="gap: 16px; max-width: 100%; padding: 0 8px;">
            {{-- 🔵 自分のストーリー --}}
            <div class="text-decoration-none text-center">
                <div class="position-relative d-inline-block">
                    @if (Auth::user()->avatar)
                        <a href="#" data-bs-toggle="modal" data-bs-target="#storyModal{{ Auth::id() }}">
                            <div class="story-ring">
                                <img src="{{ Auth::user()->avatar }}" alt="your avatar" class="rounded-circle"
                                    style="width: 64px; height: 64px; object-fit: cover;">
                            </div>
                        </a>
                    @else
                        <a href="#" data-bs-toggle="modal" data-bs-target="#storyModal{{ Auth::id() }}">
                            <div class="story-ring">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-secondary"></i>
                                </div>
                            </div>
                        </a>
                    @endif

                    <a href="{{ route('stories.create') }}"
                        class="btn btn-primary position-absolute d-flex justify-content-center align-items-center p-0"
                        style="width: 20px; height: 20px; font-size: 14px;
                border-radius: 50%;
                border: 3px solid white;
                transform: translate(20%, 20%);
                bottom: 0;
                right: 0;">
                        +
                    </a>
                </div>
                <div class="small text-dark mt-1 text-truncate" style="width: 64px;">
                    {{ Auth::user()->name }}
                </div>
            </div>

            {{-- 🔴 他のユーザーのストーリー --}}
            @foreach ($stories as $user_id => $userStories)
                @if ($user_id == Auth::id())
                    @continue
                @endif

                @php $user = $userStories->first()->user; @endphp
                <div class="text-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#storyModal{{ $user_id }}">
                        @if ($user->avatar)
                            <div class="story-ring">
                                <img src="{{ $user->avatar }}" class="rounded-circle"
                                    style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                        @else
                            <div class="story-ring">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-light"
                                    style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-secondary"></i>
                                </div>
                            </div>
                        @endif
                    </a>
                    <div class="small mt-1 text-dark text-truncate" style="width: 60px;">{{ $user->name }}</div>
                </div>
            @endforeach
        </div>
    </div>



    {{-- 🟡 投稿一覧 --}}
    <div class="row gx-5">
        <div class="col-8">
            @forelse ($home_posts as $post)
                <div class="card mb-4">
                    @include('users.posts.contents.title')
                    @include('users.posts.contents.body')
                </div>
            @empty
                <div class="text-center">
                    <h2>Share Photos</h2>
                    <p class="text-secondary">When you share photos, they'll appear on your profile.</p>
                    <a href="{{ route('post.create') }}" class="text-decoration-none">Share your first photo</a>
                </div>
            @endforelse
        </div>

        {{-- 🔵 プロフィールとおすすめ --}}
        <div class="col-4">
            <div class="row align-items-center mb-5 bg-white shadow-sm rounded-3 py-3">
                <div class="col-auto">
                    <a href="{{ route('profile.show', Auth::user()->id) }}">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                class="rounded-circle avatar-md">
                        @else
                            <i class="fa-solid fa-circle-user text-secondary icon-md"></i>
                        @endif
                    </a>
                </div>
                <div class="col ps-0">
                    <a href="{{ route('profile.show', Auth::user()->id) }}"
                        class="text-decoration-none text-dark fw-bold">{{ Auth::user()->name }}</a>
                    <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                </div>
            </div>

            @if ($suggested_users)
                <div class="row">
                    <div class="col-auto">
                        <p class="fw-bold text-secondary">{{ __('messages.suggestions_for_you') }}</p>
                    </div>
                    <div class="col text-end">
                        <a href="#" class="fw-bold text-dark text-decoration-none">{{ __('messages.see_all') }}</a>
                    </div>
                </div>
                @foreach ($suggested_users as $user)
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <a href="{{ route('profile.show', $user->id) }}">
                                @if ($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                        class="rounded-circle avatar-sm">
                                @else
                                    <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col ps-0 text-truncate">
                            <a href="{{ route('profile.show', $user->id) }}"
                                class="text-decoration-none text-dark fw-bold">
                                {{ $user->name }}
                            </a>
                        </div>
                        <div class="col-auto">
                            <form action="{{ route('follow.store', $user->id) }}" method="post">
                                @csrf
                                <button type="submit"
                                    class="border-0 bg-transparent p-0 text-primary btn-sm">{{ __('messages.follow') }}</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @include('stories.modals.story')

@endsection
