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

<script>
let map, marker, autocomplete;

function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }

    // DBに保存されている位置情報を初期値に
    const initialLat = parseFloat(document.getElementById('latitude').value) || 35.681236;
    const initialLng = parseFloat(document.getElementById('longitude').value) || 139.767125;
    const initialLatLng = new google.maps.LatLng(initialLat, initialLng);

    initializeMapWithLocation(initialLatLng);
    initializeAutocomplete();
}

function initializeMapWithLocation(latLng) {
    map = new google.maps.Map(document.getElementById('map'), {
        center: latLng,
        zoom: 15,
    });

    marker = new google.maps.Marker({
        position: latLng,
        map: map,
        draggable: true,
    });

    marker.addListener('dragend', function() {
        updateHiddenFields(marker.getPosition());
    });

    map.addListener('click', function(e) {
        marker.setPosition(e.latLng);
        updateHiddenFields(e.latLng);
    });

    updateHiddenFields(latLng);
}

function initializeAutocomplete() {
    const input = document.getElementById('location_search');
    if (!input) return;

    // Enterキーでフォーム送信を防ぐ
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'],
        // componentRestrictions: { country: 'jp' }, // 必要に応じて
    });

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            console.log("No geometry found for the selected place");
            return;
        }
        const latLng = place.geometry.location;
        map.setCenter(latLng);
        marker.setPosition(latLng);
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setZoom(17);
        }
        updateHiddenFields(latLng, place.formatted_address);
    });
}

function updateHiddenFields(latLng, address = null) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('location_name');
    if (latInput && lngInput) {
        latInput.value = latLng.lat();
        lngInput.value = latLng.lng();
    }
    if (addressInput && address) {
        addressInput.value = address;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined' && google.maps) {
        initMap();
    } else {
        setTimeout(initMap, 1000);
    }
});
window.initMap = initMap;
</script>
