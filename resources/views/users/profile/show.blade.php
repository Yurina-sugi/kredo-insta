<style>
    .multi-photo-icon {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 4px 6px;
        border-radius: 5px;
        font-size: 14px;
    }
</style>
@extends('layouts.app')

@section('title')

@section('content')
    @include('users.profile.header')

    <div style="margin-top: 100px">
        @if ($user->posts->isNotEmpty())
            <div class="row">
                @foreach ($user->posts as $post)
                    <div class="col-lg-4 col-md-6 mb-4 position-relative">
                        <a href="{{ route('post.show', $post->id) }}" class="d-block position-relative">
                            @php
                                $images = json_decode($post->image, true);
                            @endphp

                            @if (is_array($images))
                                <img src="{{ $images[0] }}" alt="post image" class="grid-img">

                                {{-- 複数画像アイコン --}}
                                <div class="multi-photo-icon">
                                    <i class="fa-solid fa-clone"></i>
                                </div>
                            @else
                                <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="grid-img">
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <h3 class="text-muted text-center">No Posts Yet</h3>
        @endif
    </div>
@endsection
