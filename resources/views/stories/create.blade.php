@extends('layouts.app')

@section('title', 'Post Story')

@section('content')
    <div class="d-flex justify-content-center">
        <div class="card shadow p-4" style="width: 100%; max-width: 500px;">
            <h2 class="mb-4 text-center title">Post a Story</h2>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- 🔸画像プレビュー --}}
                {{-- Preview display --}}
                <div class="mb-3 text-center">
                    <img id="preview" src="#" alt="Preview" class="img-fluid rounded d-none"
                        style="max-height: 300px;">
                </div>

                {{-- 🔸アップロードUI --}}
                <div class="mb-3 text-center" id="uploadArea">
                    <label for="image" class="form-label d-block">
                        <div id="imagePlaceholder" class="border rounded-3 p-4 bg-light" style="cursor: pointer;">
                            <i class="fas fa-image fa-2x text-muted"></i>
                            <div class="mt-2 text-muted">Click to select image</div>
                        </div>
                        <input type="file" name="image" id="image" class="d-none" accept="image/*" required>
                    </label>
                </div>


                <div class="mb-3">
                    <label for="text" class="form-label">Optional message</label>
                    <input type="text" name="text" id="text" class="form-control rounded-pill"
                        placeholder="Say something nice...">
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill shadow-sm">
                    Share to Story
                </button>
            </form>
        </div>
    </div>

    {{-- 🔸JS --}}
    <script>
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('preview');
        const placeholder = document.getElementById('imagePlaceholder');
        const uploadArea = document.getElementById('uploadArea');
        const changeBtn = document.getElementById('changeImageBtn');
        const changeButtonArea = document.getElementById('changeButtonArea');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
                uploadArea.classList.add('d-none');
                changeButtonArea.classList.remove('d-none');
            }
        });

        changeBtn.addEventListener('click', function() {
            imageInput.click(); // 再度選択ダイアログを開く
        });
    </script>
@endsection
