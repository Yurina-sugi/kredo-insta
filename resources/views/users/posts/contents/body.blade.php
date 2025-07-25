{{-- Clickable image --}}

<div class="container p-0">
    @php
        $images = json_decode($post->image, true);
    @endphp

    @if (is_array($images))
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @foreach ($images as $img)
                    <div class="swiper-slide d-flex justify-content-center align-items-center">
                        <a href="{{ route('post.show', $post->id) }}">
                            <img src="{{ $img }}" alt="post image" class="img-fixed">
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Page numbers (displayed below images) -->
            <div class="swiper-pagination swiper-pagination-fraction"></div>

            <!-- Arrows (if needed) -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    @else
        <a href="{{ route('post.show', $post->id) }}">
            <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="w-100 img-fixed">
        </a>
    @endif
</div>





<div class="card-body">
    {{-- heart button + no. of likes + categories --}}
    <div class="row align-items-center">
        <div class="col-auto">
            @if ($post->isLiked())
                <form action="{{ route('like.destroy', $post->id) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="like-btn btn btn-sm p-0" onclick="animateHeart(this)">
                        <i class="fa-heart fa-2x fa-solid heart-icon liked"></i>
                    </button>
                    <div class="floating-hearts-container" style="position: relative;"></div>
                </form>
            @else
                <form action="{{ route('like.store', $post->id) }}" method="post">
                    @csrf
                    <button type="submit" class="like-btn btn btn-sm p-0" onclick="showFloatingHearts(this)">
                        <i class="fa-heart fa-2x fa-regular heart-icon"></i>
                    </button>
                    <div class="floating-hearts-container" style="position: relative;"></div>
                </form>
            @endif

        </div>
        <div class="col-auto px-0">
            <span>{{ $post->likes->count() }}</span>
        </div>
        <div class="col text-end">
            @forelse ($post->categoryPost as $category_post)
                <div class="badge bg-secondary bg-opacity-50">
                    {{ $category_post->category->name }}
                </div>
            @empty
                <div class="badge bg-dark text-wrap">Uncategorized</div>
            @endforelse
        </div>
        <div class="liked-users" style="display: inline-block;">
            <span>{{ __('messages.liked') }}:</span>
            @foreach ($post->likedUsers as $user)
                <a class="text-decoration-none"
                    href="{{ route('profile.show', $user->id) }}"<span>{{ $user->name }}</span></a>
            @endforeach
        </div>
    </div>

    {{-- owner + description --}}
    <a href="{{ route('profile.show', $post->user->id) }}"
        class="text-decoration-none text-dark fw-bold">{{ $post->user->name }}</a>
    &nbsp;
    <p class="d-inline fw-light">{{ $post->description }}</p>
    <p class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($post->created_at)) }}</p>
    @if ($post->location_name)
        <p>
            <a class="text-decoration-none text-muted "
                href="https://www.google.com/maps?q={{ urlencode($post->location_name) }}" target="_blank"
                rel="noopener">
                {{ $post->location_name }}
            </a>
        </p>
    @endif
    {{-- Include comments here --}}
    @include('users.posts.contents.comments')
</div>
