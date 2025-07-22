@extends('layouts.app')

@section('title', 'Home')

@section('content')
    {{-- 🟣 ストーリーバー（丸アイコン＋名前＋クリックでモーダル） --}}
    @if ($stories->count())
        <div class="d-flex overflow-auto mb-4" style="gap: 16px;">
            @foreach ($stories as $user_id => $userStories)
                @php $user = $userStories->first()->user; @endphp
                <div class="text-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#storyModal{{ $user_id }}">
                        <img src="{{ $user->avatar ?? '/default-avatar.png' }}" class="rounded-circle border border-primary"
                            style="width: 60px; height: 60px; object-fit: cover;">
                    </a>
                    <div class="small mt-1 text-dark text-truncate" style="width: 60px;">{{ $user->name }}</div>
                </div>
            @endforeach
        </div>
    @endif

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

    {{-- 🔮 ストーリーモーダル（クリック時に表示） --}}
    @foreach ($stories as $user_id => $userStories)
        <div class="modal fade" id="storyModal{{ $user_id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-black text-white">
                    <div class="modal-body p-0">
                        @foreach ($userStories as $story)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $story->image_path) }}" class="w-100"
                                    style="object-fit: contain;">
                                @if ($story->text)
                                    <div class="position-absolute bottom-0 start-0 p-3 bg-dark bg-opacity-50 w-100">
                                        {{ $story->text }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
