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

        {{-- 🔍 AI Search Form --}}
        <div class="mb-4 p-4 rounded-4"
            style="width: 65%;
           background: linear-gradient(135deg, #e0f7fa, #fce4ec);
           box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
           border-left: 6px solid #00bcd4;
           position: relative;">

            {{-- AI Powered Badge --}}
            <div
                style="position: absolute; top: -12px; left: 12px; background: #00bcd4; color: white; font-size: 0.75rem; padding: 4px 10px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                🚀 AI Powered
            </div>

            <form action="{{ route('post.searchFromAI') }}" method="GET" class="d-flex flex-column gap-3 mt-3">
                <h4 class="text-dark fw-bold mb-0" style="font-size: 1.4rem;">
                    🔍 Discover Smarter Results with AI
                </h4>

                <input type="text" name="query" placeholder="Ask anything... AI will find it for you"
                    class="form-control border-0 rounded-4 px-4 py-2"
                    style="background-color: rgba(255,255,255,0.9); font-size: 1rem; box-shadow: inset 0 1px 4px rgba(0,0,0,0.1);">

                <button type="submit" class="btn fw-semibold text-white"
                    style="background: linear-gradient(90deg, #00bcd4, #2196f3);
                   border: none;
                   padding: 10px 24px;
                   border-radius: 50px;
                   font-size: 1rem;
                   transition: all 0.3s ease;">
                    🔎 Search with AI
                </button>
            </form>
        </div>


        {{-- 🟢 Post Feed --}}
        <div class="row gx-5">
            <div class="col-8">
                @forelse ($home_posts as $post)
                    <div class="card mb-4 shadow-sm border-0 rounded-4" data-post-id="{{ $post->id }}">
                        @include('users.posts.contents.title')
                        @include('users.posts.contents.body')
                    </div>
                @empty
                    <div class="text-center py-5">
                        <h2 class="fw-bold text-muted">No posts yet</h2>
                        <p class="text-secondary">Your shared photos will appear here.</p>
                        <a href="{{ route('post.create') }}" class="btn btn-outline-primary mt-2">Share your first
                            photo</a>
                    </div>
                @endforelse
            </div>

            {{-- 🔵 Sidebar (User info + Suggestions) --}}
            <div class="col-4">
                <div class="d-flex align-items-center mb-4 bg-white shadow-sm rounded-4 p-3">
                    <div class="me-3">
                        <a href="{{ route('profile.show', Auth::user()->id) }}">
                            @if (Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}"
                                    class="rounded-circle avatar-md shadow">
                            @else
                                <i class="fa-solid fa-circle-user text-secondary icon-md"></i>
                            @endif
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('profile.show', Auth::user()->id) }}"
                            class="text-decoration-none text-dark fw-bold">{{ Auth::user()->name }}</a>
                        <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                </div>

                @if ($suggested_users)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <p class="fw-bold text-muted mb-0">Suggested for you</p>
                        <a href="#" class="fw-bold text-decoration-none text-dark small">See all</a>
                    </div>
                    @foreach ($suggested_users as $user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-2">
                                <a href="{{ route('profile.show', $user->id) }}">
                                    @if ($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                            class="rounded-circle avatar-sm shadow-sm">
                                    @else
                                        <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                                    @endif
                                </a>
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <a href="{{ route('profile.show', $user->id) }}"
                                    class="text-decoration-none text-dark fw-bold">
                                    {{ $user->name }}
                                </a>
                            </div>
                            <div>
                                <form action="{{ route('follow.store', $user->id) }}" method="post">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-outline-primary rounded-pill">Follow</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        @include('stories.modals.story')

    @endsection
