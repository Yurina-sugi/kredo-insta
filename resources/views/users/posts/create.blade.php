@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
    <form action="{{ route('post.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="category" class="form-label d-block fw-bold">
                Category <span class="text-muted fw-normal">(up to 3)</span>
            </label>

            @foreach ($all_categories as $category)
                <div class="form-check form-check-inline">
                    <input type="checkbox" class="form-check-input" name="category[]" id="{{ $category->name }}"
                        value="{{ $category->id }}">
                    <label for="{{ $category->name }}" class="form-check-label">{{ $category->name }}</label>
                </div>
            @endforeach
            {{-- Error --}}
            @error('category')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-bold">Description</label>
            <textarea name="description" id="description" cols="4" rows="3" class="form-control"
                placeholder="What's on your mind?">{{ old('description') }}</textarea>
        </div>
        @error('description')
            <div class="text-danger small">{{ $message }}</div>
        @enderror

        <div class="mb-4">
            <label for="images[]" class="form-label fw-bold">Image (Maximum 4 images)</label>
            <input type="file" name="images[]" id="images[]" multiple class="form-control" aria-describedby="image-info">
            <div id="image-info" class="form-text">
                The acceptable formats are jpeg,jpg,png, and gif only. <br>
                Max file size is 1048kb.
            </div>
            {{-- Error --}}
            @error('images[]')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- location --}}
        <div class="mb-4">
            <label for="location_search" class="form-label fw-bold">Add location</label>
            <input type="text" id="location_search" placeholder="Input location..." class="form-control mb-2">
            {{-- Error --}}
            @error('location_name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- Map --}}
        <div class="mb-4">
            <div id="geolocation-alert" class="alert alert-info mb-2">
                <i class="fa fa-info-circle"></i>
                Please allow us to display your current location on the map.
            </div>
            <div id="map" style="height: 300px; width: 100%;"></div>
            <input type="hidden" id="latitude" name="latitude">
            <input type="hidden" id="longitude" name="longitude">
            <input type="hidden" id="location_name" name="location_name">
        </div>

        <button type="submit" class="btn btn-primary px-5">Post</button>
    </form>
@endsection
