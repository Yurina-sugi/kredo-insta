@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
    <div class="container py-4">
        <h2 class="mb-4 fw-bold">
            Search results for "{{ $query }}"
        </h2>

        @if ($posts->isEmpty())
            <div class="text-center text-muted">
                <p>No posts matched your search.</p>
                <a href="{{ route('index') }}" class="btn btn-outline-secondary">Back to Home</a>
            </div>
        @else
            <div class="row gx-5">
                <div class="col-8">
                    @foreach ($posts as $post)
                        <div class="card mb-4" data-post-id="{{ $post->id }}">
                            @include('users.posts.contents.title')
                            @include('users.posts.contents.body')
                        </div>
                    @endforeach
                </div>

                <div class="col-4">
                    <div class="bg-white rounded-3 shadow-sm p-3 mb-3">
                        <h5 class="fw-bold mb-3">Search Summary</h5>
                        <p class="mb-0">
                            <strong>"{{ $query }}"</strong> returned {{ $posts->count() }} result{{ $posts->count() > 1 ? 's' : '' }}.
                        </p>
                    </div>
                    <a href="{{ route('index') }}" class="btn btn-outline-primary w-100">Back to Home</a>
                </div>
            </div>
        @endif
    </div>
@endsection
