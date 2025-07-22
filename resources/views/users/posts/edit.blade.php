@extends('layouts.app')

@section('title', 'Edit Post')

@section('content')
    <form action="{{ route('post.update', $post->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="category" class="form-label d-block fw-bold">
                Category <span class="text-muted fw-normal">(up to 3)</span>
            </label>

            @foreach ($all_categories as $category)
                <div class="form-check form-check-inline">
                    @if (in_array($category->id, $selected_categories))
                        <input type="checkbox" class="form-check-input" name="category[]" id="{{ $category->name }}"
                            value="{{ $category->id }}" checked>
                    @else
                        <input type="checkbox" class="form-check-input" name="category[]" id="{{ $category->name }}"
                            value="{{ $category->id }}">
                    @endif

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
                placeholder="What's on your mind?">{{ old('description', $post->description) }}</textarea>
            @error('description')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <label for="image" class="form-label fw-bold">Image</label>
                <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="img-thumbnail w-100">
                <input type="file" name="image" id="image" class="form-control mt-1" aria-describedby="image-info">
                <div id="image-info" class="form-text">
                    The acceptable formats are jpeg,jpg,png, and gif only. <br>
                    Max file size is 1048kb.
                </div>
                {{-- Error --}}
                @error('image')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- location --}}
        <div class="mb-4">
            <label for="location_search" class="form-label fw-bold">Edit location</label>
            <input type="text" id="location_search" placeholder="Input location..." class="form-control mb-2" value="{{ old('location_name', $post->location_name) }}">
            {{-- Error --}}
            @error('location_name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- Map --}}
        <div class="mb-4">
            <div id="map" style="height: 300px; width: 100%;"></div>
            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $post->latitude) }}">
            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $post->longitude) }}">
            <input type="hidden" id="location_name" name="location_name" value="{{ old('location_name', $post->location_name) }}">
        </div>

        <button type="submit" class="btn btn-warning px-5">Save</button>
    </form>
@endsection

@section('map-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined' && google.maps) {
        window.initLocationMap({
            mapId: 'map',
            searchInputId: 'location_search',
            latInputId: 'latitude',
            lngInputId: 'longitude',
            nameInputId: 'location_name',
            defaultLat: 35.681236,
            defaultLng: 139.767125
        });
    } else {
        setTimeout(function() {
            window.initLocationMap({
                mapId: 'map',
                searchInputId: 'location_search',
                latInputId: 'latitude',
                lngInputId: 'longitude',
                nameInputId: 'location_name',
                defaultLat: 35.681236,
                defaultLng: 139.767125
            });
        }, 1000);
    }
});
</script>
@endsection
