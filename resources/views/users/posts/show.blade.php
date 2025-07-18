@extends('layouts.app')

@section('title', 'Show Post')

@section('content')
    <style>
        .col-4 {
            overflow-y: scroll;
        }

        .card-body {
            position: absolute;
            top: 65px;
        }

        .img-fixed {
            width: 100%;
            height: 450px;
            object-fit: contain;
            /* 画像比率を維持 */
        }

        .swiper {
            width: 100%;
            height: auto;
            position: relative;
        }

        .swiper-wrapper {
            height: 450px;
            /* スライドの高さ固定 */
        }

        .swiper-pagination-fraction {
            color: #000;
            /* 黒文字 */
            font-weight: bold;
            font-size: 13px;
            position: static;
            /* 画像の下に自然に配置 */
            text-align: center;
        }

        /* ナビゲーション矢印 */
        .swiper-button-next,
        .swiper-button-prev {
            color: #000;
        }
    </style>
    <div class="row border shadow">
        {{-- <div class="col p-0 border-end">
            <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="w-100">
        </div> --}}
        <div class="col-8 p-0 border-end">
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

                    <!-- ページ数（画像の下に表示） -->
                    <div class="swiper-pagination swiper-pagination-fraction"></div>

                    <!-- 矢印（必要なら） -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            @else
                <a href="{{ route('post.show', $post->id) }}">
                    <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="w-100 img-fixed">
                </a>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.mySwiper', {
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
            });
        </script>
        <div class="col-4 px-0 bg-white">
            <div class="card border-0">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <a href="{{ route('profile.show', $post->user->id) }}">
                                @if ($post->user->avatar)
                                    <img src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}"
                                        class="rounded-circle avatar-sm">
                                @else
                                    <i class="fa-solid fa-circle-user text-secondary icon-sm"></i>
                                @endif
                            </a>
                        </div>
                        <div class="col ps-0">
                            <a href="{{ route('profile.show', $post->user->id) }}"
                                class="text-decoration-none text-dark">{{ $post->user->name }}</a>
                        </div>
                        <div class="col-auto">
                            {{-- If you are the owner, you can edit or delete --}}
                            @if (Auth::user()->id === $post->user->id)
                                <div class="dropdown">
                                    <button class="btn btn-sm shadow-none" data-bs-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>

                                    <div class="dropdown-menu">
                                        <a href="{{ route('post.edit', $post->id) }}" class="dropdown-item">
                                            <i class="fa-regular fa-pen-to-square"></i> Edit
                                        </a>
                                        <button class="dropdown-item text-danger" data-bs-toggle="modal"
                                            data-bs-target="#delete-post-{{ $post->id }}">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </div>
                                    {{-- Include modal here --}}
                                    @include('users.posts.contents.modals.delete')
                                </div>
                            @else
                                {{-- If you are not the owner, show follow/unfollow button --}}
                                {{-- show follow button for now --}}
                                @if ($post->user->isFollowed())
                                    <form action="{{ route('follow.destroy', $post->user->id) }}" method="post"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="border-0 bg-transparent p-0 text-secondary">Following</button>
                                    </form>
                                @else
                                    <form action="{{ route('follow.store', $post->user->id) }}" method="post">
                                        @csrf
                                        <button type="submit"
                                            class="border-0 bg-transparent p-0 text-primary">Follow</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body w-100">
                    {{-- heart button + no. of likes + categories --}}
                    <div class="row align-items-center">
                        <div class="col-auto">
                            @if ($post->isLiked())
                                <form action="{{ route('like.destroy', $post->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="like-btn btn btn-sm p-0" onclick="showFloatingHearts(this)">
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
                        <!-- Liked User List -->
                        <div class="col-auto px-0">
                            <span>{{ $post->likes->count() }}</span>
                        </div>
                        <div class="col text-end">
                            @foreach ($post->categoryPost as $category_post)
                                <div class="badge bg-secondary bg-opacity-50">
                                    {{ $category_post->category->name }}
                                </div>
                            @endforeach
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

                    {{-- Include comments here --}}
                    <div class="mt-4">
                        <form action="{{ route('comment.store', $post->id) }}" method="post">
                            @csrf

                            <div class="input-group">
                                <textarea name="comment_body{{ $post->id }}" cols="30" rows="1" class="form-control form-control-sm"
                                    placeholder="Add a comment...">{{ old('comment_body' . $post->id) }}</textarea>
                                <button type="submit" class="btn btn-outline-secondary btn-sm" title="Post"><i
                                        class="fa-regular fa-paper-plane"></i></button>
                            </div>
                            {{-- Error --}}
                            @error('comment_body' . $post->id)
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </form>

                        {{-- Show all comments here --}}
                        @if ($post->comments->isNotEmpty())
                            <ul class="list-group mt-2">
                                @foreach ($post->comments as $comment)
                                    <li class="list-group-item border-0 p-0 mb-2">
                                        <a href="{{ route('profile.show', $comment->user->id) }}"
                                            class="text-decoration-none text-dark fw-bold">{{ $comment->user->name }}</a>
                                        &nbsp;

                                        <p class="d-inline fw-light">{{ $comment->body }}</p>

                                        <form action="{{ route('comment.destroy', $comment->id) }}" method="post">
                                            @csrf
                                            @method('DELETE')

                                            <span
                                                class="text-uppercase text-muted xsmall">{{ date('M d, Y', strtotime($comment->created_at)) }}</span>

                                            {{-- If the auth user is the OWNER OF THE COMMENT, show a delete btn. --}}
                                            @if (Auth::user()->id === $comment->user->id)
                                                &middot;
                                                <button type="submit"
                                                    class="border-0 bg-transparent text-danger p-0 xsmall">Delete</button>
                                            @endif
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
