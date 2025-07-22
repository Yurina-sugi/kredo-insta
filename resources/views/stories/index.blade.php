@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Story</h2>

        {{-- 投稿フォーム --}}
        <form action="{{ route('story.store') }}" method="POST" enctype="multipart/form-data" class="mb-5">
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

        {{-- ストーリーバー風の横スクロール --}}
        <div class="d-flex overflow-auto mb-4" style="gap: 16px;">
            @foreach ($stories as $userStories)
                @php $story = $userStories->first(); @endphp
                <div class="text-center" style="min-width: 80px;">
                    <a href="#user{{ $story->user->id }}" data-bs-toggle="modal">
                        <img src="{{ asset('storage/' . $story->image_path) }}" class="rounded-circle"
                            style="width: 60px; height: 60px; object-fit: cover;">
                    </a>
                    <div class="small mt-1">{{ $story->user->name }}</div>
                </div>
            @endforeach
        </div>

        {{-- モーダル表示（ユーザーごと） --}}
        @foreach ($stories as $user_id => $userStories)
            <div class="modal fade" id="user{{ $user_id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            @foreach ($userStories as $story)
                                <img src="{{ asset('storage/' . $story->image_path) }}" class="w-100"
                                    style="object-fit: contain;">
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
