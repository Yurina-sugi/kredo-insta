window.initLocationMap = function (options) {
    // options: {
    //   mapId, searchInputId, latInputId, lngInputId, nameInputId, defaultLat, defaultLng
    // }
    let map, marker, autocomplete;

    function updateHiddenFields(latLng, address = null) {
        const latInput = document.getElementById(options.latInputId);
        const lngInput = document.getElementById(options.lngInputId);
        const addressInput = document.getElementById(options.nameInputId);
        if (latInput && lngInput) {
            latInput.value = latLng.lat();
            lngInput.value = latLng.lng();
        }
        if (addressInput && address) {
            addressInput.value = address;
        }
    }

    function initializeMapWithLocation(latLng) {
        map = new google.maps.Map(document.getElementById(options.mapId), {
            center: latLng,
            zoom: 15,
        });

        marker = new google.maps.Marker({
            position: latLng,
            map: map,
            draggable: true,
        });

        marker.addListener('dragend', function () {
            updateHiddenFields(marker.getPosition());
        });

        map.addListener('click', function (e) {
            marker.setPosition(e.latLng);
            updateHiddenFields(e.latLng);
        });

        updateHiddenFields(latLng);
    }

    function initializeAutocomplete() {
        const input = document.getElementById(options.searchInputId);
        if (!input) return;

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });

        autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['geocode'],
            // componentRestrictions: { country: 'jp' }, // if needed
        });

        autocomplete.addListener('place_changed', function () {
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

    // Initial values
    const initialLat = parseFloat(document.getElementById(options.latInputId).value) || options.defaultLat || 35.681236;
    const initialLng = parseFloat(document.getElementById(options.lngInputId).value) || options.defaultLng || 139.767125;
    const initialLatLng = new google.maps.LatLng(initialLat, initialLng);

    initializeMapWithLocation(initialLatLng);
    initializeAutocomplete();
};