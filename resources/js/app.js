import './bootstrap';

// ==========================================================================
// Consolidation of scattered JS code
// ==========================================================================

// ==========================================================================
// Like Button Animation
// ==========================================================================
function animateHeart(btn) {
    const heart = btn.querySelector('.heart-icon');
    heart.classList.add('animate');
    heart.classList.toggle('liked');
    heart.addEventListener('animationend', function handler() {
        heart.classList.remove('animate');
        heart.removeEventListener('animationend', handler);
    });
}

function showFloatingHearts(btn) {
    const container = btn.parentElement.querySelector('.floating-hearts-container');
    for (let i = 0; i < 2; i++) { // Create 2 floating hearts
        const heart = document.createElement('i');
        heart.className = 'fa-solid fa-heart floating-heart';
        // Random left/right position, size, and rotation
        const offset = (Math.random() - 0.5) * 60; // -30px to +30px
        const scale = 1 + Math.random() * 0.5; // 1x to 1.5x
        const rotate = (Math.random() - 0.5) * 40; // -20 to +20 degrees
        heart.style.left = `calc(50% + ${offset}px)`;
        heart.style.fontSize = `${2 * scale}rem`;
        heart.style.transform = `translate(-50%, 0) scale(${scale}) rotate(${rotate}deg)`;
        container.appendChild(heart);

        // Remove after animation ends
        heart.addEventListener('animationend', () => {
            heart.remove();
        });
    }
    // Call existing like animation here if you want to trigger it simultaneously
    animateHeart(btn);
}



// ==========================================================================
// Google Maps Integration
// ==========================================================================
let map, marker, autocomplete;

function initMap() {
    const mapElement = document.getElementById('map');
    const geoAlert = document.getElementById('geolocation-alert');
    if (!mapElement) {
        console.error('Map element not found');
        return;
    }

    // Default position (Tokyo Station)
    const defaultLatLng = new google.maps.LatLng(35.681236, 139.767125);

    // Get current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                // Hide alert when geolocation succeeds
                if (geoAlert) geoAlert.style.display = 'none';

                const currentLatLng = new google.maps.LatLng(
                    position.coords.latitude,
                    position.coords.longitude
                );
                initializeMapWithLocation(currentLatLng);
                initializeAutocomplete();
            },
            function (error) {
                // Keep alert displayed on failure
                console.log('Geolocation error:', error);
                initializeMapWithLocation(defaultLatLng);
                initializeAutocomplete();
            }
        );
    } else {
        initializeMapWithLocation(defaultLatLng);
        initializeAutocomplete();
    }
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

    // Set latitude and longitude to hidden fields when marker moves
    marker.addListener('dragend', function () {
        updateHiddenFields(marker.getPosition());
    });

    // Move marker on map click
    map.addListener('click', function (e) {
        marker.setPosition(e.latLng);
        updateHiddenFields(e.latLng);
    });

    // Set initial values
    updateHiddenFields(latLng);
}

function initializeAutocomplete() {
    const input = document.getElementById('location_search');
    if (!input) return;

    // Prevent form submission on Enter key
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    // Autocomplete settings
    autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['geocode'], // Address only
        //componentRestrictions: { country: 'jp' }, // Japan only (optional)
    });

    // Handle when address is selected
    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
            console.log("No geometry found for the selected place");
            return;
        }

        // Move map to selected location
        const latLng = place.geometry.location;
        map.setCenter(latLng);
        marker.setPosition(latLng);

        // Adjust zoom level
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setZoom(17);
        }

        // Update hidden fields
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



// ==========================================================================
// Post Map Initialization
// ==========================================================================
function initPostMap() {
    const postMapElement = document.getElementById('post-map');
    if (!postMapElement) return;

    const lat = parseFloat(postMapElement.dataset.lat);
    const lng = parseFloat(postMapElement.dataset.lng);

    if (isNaN(lat) || isNaN(lng)) return;

    const latLng = { lat: lat, lng: lng };
    const map = new google.maps.Map(postMapElement, {
        center: latLng,
        zoom: 15,
    });
    new google.maps.Marker({
        position: latLng,
        map: map,
    });
}

// ==========================================================================
// Location Map Initialization (for edit pages)
// ==========================================================================
function initLocationMap(options) {
    const mapElement = document.getElementById(options.mapId);
    const searchInput = document.getElementById(options.searchInputId);
    const latInput = document.getElementById(options.latInputId);
    const lngInput = document.getElementById(options.lngInputId);
    const nameInput = document.getElementById(options.nameInputId);

    if (!mapElement) return;

    // Get initial values from hidden inputs (DB values)
    const initialLat = parseFloat(latInput.value);
    const initialLng = parseFloat(lngInput.value);

    // Use DB values if available, otherwise use defaults
    let centerLat, centerLng;
    if (!isNaN(initialLat) && !isNaN(initialLng) && initialLat !== 0 && initialLng !== 0) {
        centerLat = initialLat;
        centerLng = initialLng;
    } else {
        centerLat = options.defaultLat || 35.681236;
        centerLng = options.defaultLng || 139.767125;
    }

    const initialLatLng = new google.maps.LatLng(centerLat, centerLng);

    // Clear any existing map instance
    if (window.editMapInstance) {
        window.editMapInstance = null;
    }

    // Initialize map
    const map = new google.maps.Map(mapElement, {
        center: initialLatLng,
        zoom: 15,
    });

    // Store map instance globally to prevent conflicts
    window.editMapInstance = map;

    const marker = new google.maps.Marker({
        position: initialLatLng,
        map: map,
        draggable: true,
    });

    // Update hidden fields when marker moves
    marker.addListener('dragend', function () {
        const position = marker.getPosition();
        latInput.value = position.lat();
        lngInput.value = position.lng();
    });

    // Move marker on map click
    map.addListener('click', function (e) {
        marker.setPosition(e.latLng);
        latInput.value = e.latLng.lat();
        lngInput.value = e.latLng.lng();
    });

    // Initialize autocomplete if search input exists
    if (searchInput) {
        // Prevent form submission on Enter key
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                return false;
            }
        });

        // Autocomplete settings
        const autocomplete = new google.maps.places.Autocomplete(searchInput, {
            types: ['geocode'],
        });

        // Handle when address is selected
        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();

            if (!place.geometry) {
                console.log("No geometry found for the selected place");
                return;
            }

            // Move map to selected location
            const latLng = place.geometry.location;
            map.setCenter(latLng);
            marker.setPosition(latLng);

            // Adjust zoom level
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setZoom(17);
            }

            // Update hidden fields
            latInput.value = latLng.lat();
            lngInput.value = latLng.lng();
            if (nameInput) {
                nameInput.value = place.formatted_address;
            }
        });
    }
}

// Make functions globally available
window.animateHeart = animateHeart;
window.showFloatingHearts = showFloatingHearts;
window.initMap = initMap;
window.initPostMap = initPostMap;
window.initLocationMap = initLocationMap;

// Initialize functions when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Swiper for all elements with class 'mySwiper'
    const swiperElements = document.querySelectorAll('.mySwiper');
    swiperElements.forEach(function (element) {
        if (typeof Swiper !== 'undefined') {
            new Swiper(element, {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    type: 'fraction',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    });

    // Initialize Swiper for edit post page
    const editPostSwiperElements = document.querySelectorAll('.edit-post-swiper');
    editPostSwiperElements.forEach(function (element) {
        if (typeof Swiper !== 'undefined') {
            new Swiper(element, {
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    type: 'bullets',
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    });

    // Initialize image preview
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('preview');

    if (imageInput && preview) {
        imageInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        });
    }

    // Initialize dark mode toggle
    const toggleBtn = document.getElementById('mode-toggle');
    const modeIcon = document.getElementById('mode-icon');
    const modeLabel = document.getElementById('mode-label');

    if (toggleBtn) {
        // Get mode from localStorage
        let mode = localStorage.getItem('color-mode') || 'light';
        setMode(mode);

        toggleBtn.addEventListener('click', function () {
            mode = (mode === 'light') ? 'dark' : 'light';
            setMode(mode);
            localStorage.setItem('color-mode', mode);
        });

        function setMode(mode) {
            document.body.classList.remove('light-mode', 'dark-mode');
            document.body.classList.add(mode + '-mode');
            if (mode === 'dark') {
                modeIcon.className = 'fa fa-sun';
            } else {
                modeIcon.className = 'fa fa-moon';
            }
        }
    }

    // Initialize map if Google Maps is available
    const mapElement = document.getElementById('map');
    const geolocationAlert = document.getElementById('geolocation-alert');

    if (mapElement) {
        // Check if this is a create page (has geolocation alert) or edit page (has existing values)
        const isCreatePage = geolocationAlert !== null;
        const hasExistingValues = document.getElementById('latitude') &&
            document.getElementById('latitude').value &&
            document.getElementById('longitude') &&
            document.getElementById('longitude').value;

        // For create pages, use initMap (current location)
        // For edit pages, let the page-specific script handle it
        if (isCreatePage && !hasExistingValues) {
            if (typeof google !== 'undefined' && google.maps) {
                initMap();
            } else {
                setTimeout(initMap, 1000);
            }
        }
    }

    // Initialize post map for show pages
    const postMapElement = document.getElementById('post-map');
    if (postMapElement) {
        if (typeof google !== 'undefined' && google.maps) {
            initPostMap();
        } else {
            // Wait for Google Maps API to load
            const checkGoogleMaps = setInterval(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    initPostMap();
                    clearInterval(checkGoogleMaps);
                }
            }, 100);
        }
    }
});
