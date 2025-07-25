@extends('layouts.app')

@section('content')
    @php
        $currentUserId = auth()->id();
        $hasMyStory = isset($stories[$currentUserId]);
        $storiesWithoutMe = $stories->filter(function ($_, $key) use ($currentUserId) {
            return $key != $currentUserId;
        });
    @endphp

    <div class="container">
        <h2 class="mb-4">Story</h2>

        {{-- 投稿フォーム --}}
        <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
            @csrf
            <div class="d-flex align-items-center">
                <input type="file" name="image" accept="image/*" required class="form-control me-2"
                    style="max-width: 300px;">
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
            @error('image')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </form>

        {{-- Story bar style horizontal scroll --}}
        <div class="d-flex overflow-auto mb-4" style="gap: 16px;">
            {{-- 自分のストーリー --}}
            <div class="text-center position-relative" style="min-width: 80px;">
                @if ($hasMyStory)
                    @php $myStory = $stories[$currentUserId]->first(); @endphp
                    <a href="#storyModal{{ $myStory->user->id }}" data-bs-toggle="modal">
                        <div class="story-ring">
                            <img src="{{ asset('storage/' . $myStory->image_path) }}" class="rounded-circle border-primary"
                            style="width: 60px; height: 60px; object-fit: cover;">
                        </div>
                        {{-- 再投稿ボタン（hover表示） --}}
                        <form action="{{ route('stories.create') }}" method="GET"
                            class="position-absolute top-0 end-0 translate-middle" style="display: none;"
                            id="add-icon-form">
                            <button type="submit" class="btn btn-sm btn-primary rounded-circle"
                                style="width: 24px; height: 24px; padding: 0;">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </form>
                    </a>
                @else
                    {{-- 自分のストーリーがないとき：新規投稿用プラスボタン --}}
                    <form action="{{ route('stories.create') }}" method="GET">
                        <button type="submit" class="btn p-0 border-0 bg-transparent">
                            <img src="{{ Auth::user()->profile_image ?? 'https://via.placeholder.com/60' }}"
                                class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-light rounded-circle">
                                <i class="bi bi-plus text-white" style="font-size: 0.75rem;"></i>
                            </span>
                        </button>
                    </form>
                @endif
                <div class="small mt-1">You</div>
            </div>

            {{-- 他のユーザーのストーリー --}}
            @foreach ($storiesWithoutMe as $userId => $userStories)
                @php $story = $userStories->first(); @endphp
                <div class="text-center" style="min-width: 80px;">
                    <a href="#storyModal{{ $story->user->id }}" data-bs-toggle="modal">
                        <img src="{{ asset('storage/' . $story->image_path) }}" class="rounded-circle"
                            style="width: 60px; height: 60px; object-fit: cover;">
                    </a>
                    <div class="small mt-1">{{ $story->user->name }}</div>
                </div>
            @endforeach
        </div>
    </div>

    @include('stories.modals.story')

    @push('styles')
        <style>
            .position-relative:hover #add-icon-form {
                display: block !important;
            }
        </style>
    @endpush
@endsection
