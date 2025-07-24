@foreach ($stories as $user_id => $userStories)
    @php $user = $userStories->first()->user; @endphp
    <div class="modal fade" id="storyModal{{ $user_id }}" tabindex="-1" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-white rounded-4 overflow-hidden"
                style="background-color: rgba(255, 250, 230, 0.752); backdrop-filter: blur(12px);">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-dark">{{ $user->name }}'s Story</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div class="story-slider position-relative">
                        @foreach ($userStories as $index => $story)
                            <div class="story-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"
                                style="display: {{ $index === 0 ? 'block' : 'none' }};">

                                {{-- Progress bar --}}
                                <div class="progress position-absolute top-0 start-0 w-100" style="height: 5px;">
                                    <div class="progress-bar bg-white" role="progressbar" style="width: 0%;"></div>
                                </div>

                                {{-- Image --}}
                                <img src="{{ Storage::url($story->image_path) }}" alt="story image"
                                    class="img-fluid rounded" style="max-height: 500px; object-fit: contain;">

                                {{-- Text --}}
                                @if ($story->text)
                                    <p class="fs-5 mt-3 text-dark">{{ $story->text }}</p>
                                @endif

                                {{-- Delete Button（自分の投稿のみ） --}}
                                @if ($story->user_id === Auth::id())
                                    <form action="{{ route('stories.destroy', $story->id) }}" method="POST"
                                        class="position-absolute top-0 end-0 m-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fa fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach


{{-- ストーリー再生スクリプト --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const storyModals = document.querySelectorAll('.modal');

        storyModals.forEach(modal => {
            modal.addEventListener('show.bs.modal', () => {
                ;
                const slides = modal.querySelectorAll('.story-slide');
                let index = 0;
                let slideInterval = null;

                const showSlide = (i) => {
                    if (slideInterval) clearInterval(slideInterval);

                    slides.forEach((slide, j) => {
                        slide.style.display = (j === i) ? 'block' : 'none';
                        const bar = slide.querySelector('.progress-bar');
                        if (bar) bar.style.width = '0%';
                    });
                    const activeBar = slides[i]?.querySelector('.progress-bar');
                    if (activeBar) {
                        let progress = 0;
                        slideInterval = setInterval(() => { // ← ここだけ修正！
                            progress += 1;
                            activeBar.style.width = `${progress}%`;
                            if (progress >= 100) {
                                clearInterval(slideInterval); // ← これで止められるようになる
                                index++;
                                if (index < slides.length) {
                                    showSlide(index);
                                } else {
                                    const bsModal = bootstrap.Modal.getInstance(
                                        modal);
                                    bsModal.hide();
                                    window.location.href = "/";

                                }
                            }
                        }, 30);
                    }
                };

                showSlide(index);

                // 矢印キー対応
                modal.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowRight' && index < slides.length - 1) {
                        index++;
                        showSlide(index);
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        index--;
                        showSlide(index);
                    }
                });

                // クリックでも進む
                modal.querySelector('.story-slider').addEventListener('click', () => {
                    if (index < slides.length - 1) {
                        index++;
                        showSlide(index);
                    }
                });

                // キーボード入力を有効にするために focus
                modal.focus();
            });
        });
    });
</script>
