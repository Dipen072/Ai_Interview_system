@extends('layouts.app')

@slot('title')
    Interview Setup
@endslot

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
        <h2 class="fw-bold text-white"><i class="bi bi-sliders text-indigo me-2"></i> Configure Your AI Mock Interview</h2>
        <p class="text-muted-custom">Configure the parameters below. The AI will customize a dynamic list of questions based on your selections.</p>
    </div>

    <div class="card p-4">
        <form action="{{ route('interviews.start') }}" method="POST" id="startInterviewForm">
            @csrf

            <!-- 1. Category Selection -->
            <div class="mb-4">
                <label for="category_id" class="form-label fw-bold text-white fs-5">1. Select Technology / Topic</label>
                <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 12px;">
                    <option value="" disabled selected>Choose a topic...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (request('category_id') == $category->id || old('category_id') == $category->id) ? 'selected' : '' }}>
                            {{ $category->name }} &mdash; {{ Str::limit($category->description, 60) }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- 2. Difficulty Level -->
            <div class="mb-4">
                <label class="form-label fw-bold text-white fs-5 d-block">2. Choose Difficulty Level</label>
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="difficulty" id="diff_easy" value="easy" {{ old('difficulty', 'easy') === 'easy' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success w-100 py-3" for="diff_easy">
                            <i class="bi bi-shield-check fs-4 d-block mb-1"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">Easy</span>
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">Fundamental questions</span>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="difficulty" id="diff_medium" value="medium" {{ old('difficulty') === 'medium' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning w-100 py-3" for="diff_medium">
                            <i class="bi bi-shield-slash fs-4 d-block mb-1"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">Medium</span>
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">Intermediate concepts</span>
                        </label>
                    </div>
                    <div class="col-md-4">
                        <input type="radio" class="btn-check" name="difficulty" id="diff_hard" value="hard" {{ old('difficulty') === 'hard' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger w-100 py-3" for="diff_hard">
                            <i class="bi bi-shield-exclamation fs-4 d-block mb-1"></i>
                            <span class="fw-bold text-uppercase" style="font-size: 0.85rem;">Hard</span>
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.75rem;">Advanced design & code patterns</span>
                        </label>
                    </div>
                </div>
                @error('difficulty')
                    <div class="text-danger mt-2" style="font-size: 0.85rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- 3. Question Count selection -->
            <div class="mb-4">
                <label for="total_questions_select" class="form-label fw-bold text-white fs-5">3. Number of Questions</label>
                <div class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <select id="total_questions_select" class="form-select" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 12px;">
                            <option value="5" selected>5 Questions (Recommended)</option>
                            <option value="10">10 Questions</option>
                            <option value="15">15 Questions</option>
                            <option value="20">20 Questions</option>
                            <option value="custom">Custom Amount...</option>
                        </select>
                    </div>
                    <div class="col-md-6 d-none" id="customQuestionsDiv">
                        <div class="input-group">
                            <input type="number" name="total_questions" id="total_questions" class="form-control @error('total_questions') is-invalid @enderror" value="5" min="1" max="100" style="background-color: #1f2937; border-color: #374151; color: #fff; padding: 12px;" placeholder="Enter amount (1-100)">
                            <span class="input-group-text bg-dark border-secondary text-secondary">Questions</span>
                        </div>
                    </div>
                </div>
                @error('total_questions')
                    <div class="text-danger mt-2" style="font-size: 0.85rem;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Info card on interview terms -->
            <div class="p-3 bg-secondary-subtle rounded-3 mb-4" style="background-color: rgba(99, 102, 241, 0.05) !important; border: 1px solid rgba(99, 102, 241, 0.15);">
                <div class="d-flex">
                    <i class="bi bi-info-square text-indigo me-3 fs-4"></i>
                    <div>
                        <h6 class="fw-bold text-white m-0">Arena Guidelines</h6>
                        <ul class="m-0 mt-1 text-muted-custom p-0 ps-3" style="font-size: 0.85rem; line-height: 1.5;">
                            <li>Once you start, a countdown timer will begin based on the number of questions (2 mins/question).</li>
                            <li>Your progress is auto-saved in real-time. Feel free to navigate back and forth.</li>
                            <li>Ensure you do not reload or exit once the timer hits zero as it auto-submits answers.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold" id="startBtn">
                <i class="bi bi-cpu me-2"></i> Generate & Start Interview
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectElement = document.getElementById('total_questions_select');
        const customDiv = document.getElementById('customQuestionsDiv');
        const customInput = document.getElementById('total_questions');
        const form = document.getElementById('startInterviewForm');
        const startBtn = document.getElementById('startBtn');

        // Toggle custom input field based on selection
        selectElement.addEventListener('change', function () {
            if (this.value === 'custom') {
                customDiv.classList.remove('d-none');
                customInput.value = '';
                customInput.focus();
            } else {
                customDiv.classList.add('d-none');
                customInput.value = this.value;
            }
        });

        // Set initial value
        if (selectElement.value !== 'custom') {
            customInput.value = selectElement.value;
        }

        form.addEventListener('submit', function () {
            startBtn.disabled = true;
            startBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Preparing Interview Context...';
        });
    });
</script>
@endsection
