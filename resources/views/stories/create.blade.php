@extends('layouts.app')

@section('title', 'Post Story')

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card shadow p-4" style="width: 100%; max-width: 500px;">
            <h4 class="mb-4 text-center">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Post a Story
            </h4>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('story.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- プレビュー表示 --}}
                <div class="mb-3 text-center">
                    <img id="preview" src="#" alt="Preview" class="img-fluid rounded d-none"
                        style="max-height: 300px;">
                </div>

                {{-- 画像アップロード --}}
                <div class="mb-3 text-center">
                    <label for="image" class="form-label d-block">
                        <div class="border rounded-3 p-4 bg-light" style="cursor: pointer;">
                            <i class="fas fa-image fa-2x text-muted"></i>
                            <div class="mt-2 text-muted">Click to select image</div>
                        </div>
                        <input type="file" name="image" id="image" class="d-none" accept="image/*" required>
                    </label>
                </div>

                {{-- テキスト追加 --}}
                <div class="mb-3">
                    <label for="text" class="form-label">Optional message</label>
                    <input type="text" name="text" id="text" class="form-control" placeholder="Say something...">
                </div>

                <button type="submit" class="btn btn-primary w-100">Share to Story</button>
            </form>
        </div>
    </div>

    <script>
        // 🔵 Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('preview');
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            }
        });
    </script>
@endsection
