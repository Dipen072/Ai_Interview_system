@extends('layouts.app')

@slot('title')
    Interview Arena
@endslot

@section('content')
<div class="container-fluid flex-grow-1 d-flex flex-column" style="min-height: calc(100vh - 120px);">
    
    <!-- Top Dashboard Header with Timer -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold m-0 text-white">
                <i class="bi bi-person-workspace text-info me-2"></i> {{ $interview->category->name }} Mock Session
            </h3>
            <span class="badge bg-secondary-subtle text-muted-custom border-secondary mt-1">Difficulty: {{ ucfirst($interview->difficulty) }}</span>
            <span class="badge bg-secondary-subtle text-muted-custom border-secondary mt-1">Questions: {{ $interview->total_questions }}</span>
        </div>
        <div class="col-auto">
            <div class="card bg-dark border-secondary px-4 py-2 d-flex flex-row align-items-center shadow-lg">
                <div class="me-3 text-secondary" style="font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px;">TIME REMAINING</div>
                <div id="countdownTimer" class="fs-3 fw-bold font-monospace text-warning">00:00</div>
            </div>
        </div>
    </div>

    <!-- Main Workspace Layout -->
    <div class="row flex-grow-1 g-4">
        
        <!-- Left Side: Navigation Sidebar -->
        <div class="col-md-3">
            <div class="card h-100 p-3">
                <h6 class="fw-bold text-muted-custom text-uppercase mb-3" style="font-size: 0.75rem;">Question Navigation</h6>
                <div class="d-flex flex-wrap gap-2 justify-content-start" id="questionNavGrid">
                    @foreach($questions as $index => $q)
                        <button type="button" 
                                class="btn q-nav-btn rounded-circle d-flex align-items-center justify-content-center fw-bold" 
                                style="width: 44px; height: 44px; font-size: 0.95rem;" 
                                data-index="{{ $index }}" 
                                id="nav_btn_{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <hr class="border-secondary my-3">

                <!-- Submit Section -->
                <div class="mt-auto">
                    <p class="text-muted-custom fs-7" style="font-size: 0.75rem; line-height: 1.4;">Verify your answers for all questions. Once finalized, click below to evaluate.</p>
                    <a href="{{ route('interviews.submit', $interview->id) }}" class="btn btn-primary w-100 py-2 fw-bold" id="submitInterviewBtn">
                        <i class="bi bi-check2-circle me-1"></i> Submit Session
                    </a>
                </div>
            </div>
        </div>

        <!-- Center/Right Side: Interactive Question Panel -->
        <div class="col-md-9 d-flex flex-column">
            <div class="card flex-grow-1 p-4 d-flex flex-column shadow">
                
                <!-- Question Title Header -->
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-3 mb-3">
                    <h5 class="fw-bold m-0 text-white" id="currentQuestionTitle">Question 1 of 5</h5>
                    <div id="savingIndicator" class="text-muted-custom font-monospace" style="font-size: 0.8rem;">
                        <i class="bi bi-cloud-check me-1 text-success"></i> All changes saved
                    </div>
                </div>

                <!-- Large Question Block -->
                <div class="mb-4">
                    <p class="fs-4 text-white fw-medium lh-base" id="questionText">Loading question...</p>
                </div>

                <!-- Candidate Answer Area -->
                <div class="flex-grow-1 mb-4 d-flex flex-column">
                    <label for="answerTextarea" class="form-label text-muted-custom" style="font-size: 0.85rem;">YOUR RESPONSE</label>
                    <textarea id="answerTextarea" 
                              class="form-control flex-grow-1 font-monospace p-3" 
                              style="background-color: #0b0f19; border-color: #374151; color: #fff; line-height: 1.6; resize: none; min-height: 250px;" 
                              placeholder="Write your explanation here. You can write paragraphs, bullet points, or reference pseudocode..."
                    ></textarea>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-secondary py-2 px-4" id="prevQuestionBtn">
                        <i class="bi bi-chevron-left me-1"></i> Previous
                    </button>
                    <button type="button" class="btn btn-primary py-2 px-4" id="nextQuestionBtn">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Question Navigation Circle Styles */
    .q-nav-btn {
        background-color: transparent;
        border: 2px solid var(--border-color);
        color: var(--text-secondary);
        transition: all 0.2s ease;
    }
    .q-nav-btn.active {
        border-color: var(--primary-accent);
        background-color: var(--primary-accent);
        color: #fff;
    }
    .q-nav-btn.completed {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    .q-nav-btn.completed.active {
        background-color: #10b981;
        color: #fff;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // --- Setup Data & State ---
        const questions = {!! json_encode($questions) !!};
        const initialAnswers = {!! json_encode($savedAnswers) !!};
        const interviewId = {{ $interview->id }};
        let currentIdx = 0;
        let saveTimeout = null;

        // Tracks answers currently in RAM
        const answers = {};
        questions.forEach(q => {
            answers[q.id] = (initialAnswers[q.id] ? initialAnswers[q.id].answer_text : '') || '';
        });

        // --- DOM Elements ---
        const qTitle = document.getElementById('currentQuestionTitle');
        const qText = document.getElementById('questionText');
        const answerTextarea = document.getElementById('answerTextarea');
        const prevBtn = document.getElementById('prevQuestionBtn');
        const nextBtn = document.getElementById('nextQuestionBtn');
        const saveIndicator = document.getElementById('savingIndicator');

        // --- Render UI for active Question Index ---
        function renderQuestion(idx) {
            currentIdx = idx;
            const q = questions[idx];

            // 1. Update Title & Body
            qTitle.textContent = `Question ${idx + 1} of ${questions.length}`;
            qText.textContent = q.question_text;

            // 2. Set Answer field
            answerTextarea.value = answers[q.id];

            // 3. Toggle prev/next button labels
            if (idx === 0) {
                prevBtn.classList.add('disabled');
            } else {
                prevBtn.classList.remove('disabled');
            }

            if (idx === questions.length - 1) {
                nextBtn.innerHTML = 'Finish <i class="bi bi-check-circle ms-1"></i>';
            } else {
                nextBtn.innerHTML = 'Next <i class="bi bi-chevron-right ms-1"></i>';
            }

            // 4. Update Navigation Grid styling
            document.querySelectorAll('.q-nav-btn').forEach(btn => {
                const btnIdx = parseInt(btn.dataset.index);
                const btnQId = questions[btnIdx].id;

                btn.classList.remove('active');
                if (btnIdx === currentIdx) {
                    btn.classList.add('active');
                }

                // If question is completed (has answer text)
                if (answers[btnQId] && answers[btnQId].trim() !== '') {
                    btn.classList.add('completed');
                } else {
                    btn.classList.remove('completed');
                }
            });
        }

        // --- Debounced Auto-Saving Handler ---
        function triggerAutoSave() {
            setSavingState('saving');
            
            const qId = questions[currentIdx].id;
            const text = answerTextarea.value;
            answers[qId] = text; // Update RAM cache

            // Cancel running timeout
            clearTimeout(saveTimeout);

            // Debounce save API hit
            saveTimeout = setTimeout(() => {
                saveAnswerToBackend(qId, text);
            }, 1000);
        }

        function setSavingState(state) {
            if (state === 'saving') {
                saveIndicator.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-info" role="status" aria-hidden="true"></span> Saving progress...';
                saveIndicator.className = "text-info font-monospace";
            } else if (state === 'saved') {
                saveIndicator.innerHTML = '<i class="bi bi-cloud-check me-1 text-success"></i> All changes saved';
                saveIndicator.className = "text-muted-custom font-monospace";
            } else if (state === 'error') {
                saveIndicator.innerHTML = '<i class="bi bi-cloud-slash me-1 text-danger"></i> Save failed. Retrying...';
                saveIndicator.className = "text-danger font-monospace";
            }
        }

        function saveAnswerToBackend(questionId, answerText) {
            fetch(`/interviews/${interviewId}/save-answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    question_id: questionId,
                    answer_text: answerText
                })
            })
            .then(response => {
                if (response.ok) {
                    setSavingState('saved');
                    // Refresh nav circle checked status
                    const navBtn = document.getElementById(`nav_btn_${currentIdx}`);
                    if (answerText && answerText.trim() !== '') {
                        navBtn.classList.add('completed');
                    } else {
                        navBtn.classList.remove('completed');
                    }
                } else {
                    setSavingState('error');
                }
            })
            .catch(err => {
                console.error("Save error:", err);
                setSavingState('error');
            });
        }

        // --- Event Listeners ---
        answerTextarea.addEventListener('input', triggerAutoSave);

        prevBtn.addEventListener('click', () => {
            if (currentIdx > 0) {
                // Ensure immediate save of current before moving
                clearTimeout(saveTimeout);
                const qId = questions[currentIdx].id;
                saveAnswerToBackend(qId, answerTextarea.value);
                
                renderQuestion(currentIdx - 1);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIdx < questions.length - 1) {
                // Ensure immediate save of current before moving
                clearTimeout(saveTimeout);
                const qId = questions[currentIdx].id;
                saveAnswerToBackend(qId, answerTextarea.value);
                
                renderQuestion(currentIdx + 1);
            } else {
                // Finish Session (Trigger immediate save and redirect to submit)
                clearTimeout(saveTimeout);
                const qId = questions[currentIdx].id;
                saveAnswerToBackend(qId, answerTextarea.value);
                window.location.href = `/interviews/${interviewId}/submit`;
            }
        });

        document.querySelectorAll('.q-nav-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                // Ensure immediate save of current before moving
                clearTimeout(saveTimeout);
                const qId = questions[currentIdx].id;
                saveAnswerToBackend(qId, answerTextarea.value);
                
                renderQuestion(parseInt(this.dataset.index));
            });
        });

        // --- Timer Countdown logic ---
        let timeRemaining = {{ $remainingSeconds }};
        const timerLabel = document.getElementById('countdownTimer');

        function updateCountdown() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;

            timerLabel.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            // Warn when timer gets low
            if (timeRemaining <= 60) {
                timerLabel.className = "fs-3 fw-bold font-monospace text-danger animate-pulse";
            }

            if (timeRemaining <= 0) {
                clearInterval(countdownInterval);
                // Trigger auto-submit redirect
                window.location.href = `/interviews/${interviewId}/submit`;
            }

            timeRemaining--;
        }

        updateCountdown(); // Initialize immediately
        const countdownInterval = setInterval(updateCountdown, 1000);

        // --- Render first question ---
        renderQuestion(0);
    });
</script>

<style>
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .animate-pulse {
        animation: pulse 1s infinite;
    }
</style>
@endsection
