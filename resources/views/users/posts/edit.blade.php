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
                @php
                    $images = json_decode($post->image, true);
                @endphp
                @if (is_array($images))
                    {{-- Multiple images with Swiper --}}
                    <div class="swiper edit-post-swiper mb-2">
                        <div class="swiper-wrapper">
                            @foreach ($images as $index => $img)
                                <div class="swiper-slide">
                                    <img src="{{ $img }}" alt="post image {{ $index + 1 }}"
                                        class="img-thumbnail w-100">
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                @else
                    {{-- Single image --}}
                    <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="img-thumbnail w-100 mb-2">
                @endif
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
            <input type="text" id="location_search" placeholder="Input location..." class="form-control mb-2"
                value="{{ old('location_name', $post->location_name) }}">
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
            <input type="hidden" id="location_name" name="location_name"
                value="{{ old('location_name', $post->location_name) }}">
        </div>

        <button type="submit" class="btn btn-warning px-5">Save</button>
    </form>

    <script>
        // Wait for both DOM and Google Maps to be ready
        function initializeEditMap() {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');

            if (!latInput || !lngInput) return;

            const dbLat = parseFloat(latInput.value);
            const dbLng = parseFloat(lngInput.value);

            // Use DB values if they exist and are valid
            const defaultLat = (!isNaN(dbLat) && dbLat !== 0) ? dbLat : 35.681236;
            const defaultLng = (!isNaN(dbLng) && dbLng !== 0) ? dbLng : 139.767125;

            if (typeof google !== 'undefined' && google.maps) {
                // Clear any existing map
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.innerHTML = '';
                }

                window.initLocationMap({
                    mapId: 'map',
                    searchInputId: 'location_search',
                    latInputId: 'latitude',
                    lngInputId: 'longitude',
                    nameInputId: 'location_name',
                    defaultLat: defaultLat,
                    defaultLng: defaultLng
                });
            } else {
                setTimeout(initializeEditMap, 1000);
            }
        }

        // Initialize immediately if DOM is ready, otherwise wait
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeEditMap);
        } else {
            initializeEditMap();
        }
    </script>
@endsection
