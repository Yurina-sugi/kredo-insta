@extends('layouts.app')

@section('title', 'Admin: Posts')

@section('content')
    <div class="container w-75 mb-4">
        <div class="d-flex justify-content-center">
            <form action="{{ route('admin.posts') }}" method="GET" class="d-flex align-items-center gap-3"
                style="font-size: 1.3rem; width: 100%; max-width: 900px;">

                <select name="owner" class="form-select" style="width: 200px;">
                    <option value="">All Owners</option>
                    @foreach($owners as $owner)
                        <option value="{{ $owner->id }}" {{ request('owner') == $owner->id ? 'selected' : '' }}>
                            {{ $owner->name }}
                        </option>
                    @endforeach
                </select>

                <select name="category" class="form-select" style="width: 200px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select" style="width: 180px;">
                    <option value="">All Status</option>
                    <option value="visible" {{ request('status') === 'visible' ? 'selected' : '' }}>Visible</option>
                    <option value="hidden" {{ request('status') === 'hidden' ? 'selected' : '' }}>Hidden</option>
                </select>

                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <table class="table table-hover align-middle bg-white border text-secondary">
        <thead class="table-primary text-secondary small">
            <tr>
                <th></th>
                <th></th>
                <th>CATEGORY</th>
                <th>OWNER</th>
                <th>CREATED AT</th>
                <th>STATUS</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($all_posts as $post)
                <tr>
                    <td class="text-end">{{ $post->id }}</td>
                    <td>
                        <a href="{{ route('post.show', $post->id) }}">
                            <img src="{{ $post->image }}" alt="post id {{ $post->id }}" class="d-block mx-auto image-lg">
                        </a>
                    </td>
                    <td>
                        @forelse ($post->categoryPost as $category_post)
                            <div class="badge bg-secondary bg-opacity-50">{{ $category_post->category->name }}</div>
                        @empty
                            <div class="badge bg-dark">Uncategorized</div>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('profile.show', $post->user->id) }}"
                            class="text-dark text-decoration-none">{{ $post->user->name }}</a>
                    </td>
                    <td>{{ $post->created_at }}</td>
                    <td>
                        @if ($post->trashed())
                            <i class="fa-solid fa-circle-minus text-secondary"></i>&nbsp; Hidden
                        @else
                            <i class="fa-solid fa-circle text-primary"></i>&nbsp; Visible
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-sm" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>

                            @if ($post->trashed())
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#unhide-post-{{ $post->id }}">
                                        <i class="fa-solid fa-eye"></i> Unhide Post {{ $post->id }}
                                    </button>
                                </div>
                            @else
                                <div class="dropdown-menu">
                                    <button class="dropdown-item text-danger" data-bs-toggle="modal"
                                        data-bs-target="#hide-post-{{ $post->id }}">
                                        <i class="fa-solid fa-eye-slash"></i> Hide Post {{ $post->id }}
                                    </button>
                                </div>
                            @endif
                        </div>
                        @include('admin.posts.modals.status')
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="lead text-muted text-center">No posts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $all_posts->links() }}
@endsection
