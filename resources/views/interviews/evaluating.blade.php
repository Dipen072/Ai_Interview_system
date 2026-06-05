@extends('layouts.app')

@slot('title')
    Evaluating Session
@endslot

@section('content')
<div class="container d-flex align-items-center justify-content-center flex-grow-1" style="min-height: calc(100vh - 150px);">
    <div class="card p-5 text-center shadow-lg border-secondary" style="max-width: 600px; width: 100%;">
        
        <!-- Animated Icon -->
        <div class="mb-4">
            <div class="spinner-outer d-inline-block position-relative">
                <div class="spinner-border text-info" style="width: 5rem; height: 5rem; border-width: 0.25em;" role="status"></div>
                <i class="bi bi-cpu text-indigo position-absolute start-50 top-50 translate-middle fs-2 animate-glow"></i>
            </div>
        </div>

        <h3 class="fw-bold text-white mb-2">Analyzing Responses</h3>
        <p class="text-muted-custom mb-4">Our AI evaluator is grading your answers based on Accuracy, Technical Knowledge, Communication, and Completeness.</p>

        <!-- Progress Indicator -->
        <div class="progress mb-4" style="height: 8px; background-color: #374151;">
            <div id="evaluationProgressBar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 15%;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
        </div>

        <!-- Steps list -->
        <div class="text-start mx-auto p-3 rounded bg-dark border border-secondary mb-4" style="max-width: 450px; font-size: 0.9rem;">
            <div class="d-flex align-items-center mb-2" id="step1">
                <i class="bi bi-circle text-secondary me-2 fs-5" id="step1_icon"></i>
                <span class="text-muted-custom" id="step1_text">Consolidating candidate responses...</span>
            </div>
            <div class="d-flex align-items-center mb-2" id="step2">
                <i class="bi bi-circle text-secondary me-2 fs-5" id="step2_icon"></i>
                <span class="text-muted-custom" id="step2_text">Invoking AI Engine & grading...</span>
            </div>
            <div class="d-flex align-items-center mb-2" id="step3">
                <i class="bi bi-circle text-secondary me-2 fs-5" id="step3_icon"></i>
                <span class="text-muted-custom" id="step3_text">Analyzing criteria breakdowns...</span>
            </div>
            <div class="d-flex align-items-center" id="step4">
                <i class="bi bi-circle text-secondary me-2 fs-5" id="step4_icon"></i>
                <span class="text-muted-custom" id="step4_text">Compiling report card & scores...</span>
            </div>
        </div>

        <!-- Error Panel (hidden by default) -->
        <div id="errorPanel" class="d-none alert alert-danger border-0 bg-danger-subtle text-danger-emphasis p-3 mb-4" role="alert">
            <h6 class="fw-bold m-0"><i class="bi bi-exclamation-octagon-fill me-2"></i> Evaluation Timeout or Error</h6>
            <p class="m-0 mt-1" id="errorMessage" style="font-size: 0.85rem;">An error occurred while calling the AI APIs.</p>
            <button type="button" class="btn btn-danger btn-sm mt-3 px-4 fw-bold" id="retryBtn">
                <i class="bi bi-arrow-clockwise me-1"></i> Retry Evaluation
            </button>
        </div>
    </div>
</div>

<style>
    .spinner-outer {
        width: 80px;
        height: 80px;
    }
    .animate-glow {
        animation: glow 1.5s ease-in-out infinite alternate;
    }
    @keyframes glow {
        from {
            text-shadow: 0 0 4px rgba(99, 102, 241, 0.4);
            transform: translate(-50%, -50%) scale(0.95);
        }
        to {
            text-shadow: 0 0 12px rgba(99, 102, 241, 0.8);
            transform: translate(-50%, -50%) scale(1.05);
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const interviewId = {{ $interview->id }};
        const progressBar = document.getElementById('evaluationProgressBar');
        const errorPanel = document.getElementById('errorPanel');
        const errorMessage = document.getElementById('errorMessage');
        const retryBtn = document.getElementById('retryBtn');

        // Step Elements
        const steps = [
            { icon: document.getElementById('step1_icon'), text: document.getElementById('step1_text') },
            { icon: document.getElementById('step2_icon'), text: document.getElementById('step2_text') },
            { icon: document.getElementById('step3_icon'), text: document.getElementById('step3_text') },
            { icon: document.getElementById('step4_icon'), text: document.getElementById('step4_text') },
        ];

        let progressInterval = null;
        let simulatedProgress = 15;

        // Smoothly updates progress bars and simulated checklist steps
        function startProgressSimulation() {
            simulatedProgress = 15;
            progressBar.style.width = '15%';
            
            // Set first step active
            updateStep(0, 'active');
            updateStep(1, 'pending');
            updateStep(2, 'pending');
            updateStep(3, 'pending');

            progressInterval = setInterval(() => {
                if (simulatedProgress < 95) {
                    simulatedProgress += Math.floor(Math.random() * 5) + 2;
                    simulatedProgress = Math.min(simulatedProgress, 95);
                    progressBar.style.width = `${simulatedProgress}%`;

                    // Update steps based on progress metrics
                    if (simulatedProgress >= 25 && simulatedProgress < 55) {
                        updateStep(0, 'done');
                        updateStep(1, 'active');
                    } else if (simulatedProgress >= 55 && simulatedProgress < 80) {
                        updateStep(1, 'done');
                        updateStep(2, 'active');
                    } else if (simulatedProgress >= 80) {
                        updateStep(2, 'done');
                        updateStep(3, 'active');
                    }
                }
            }, 800);
        }

        function updateStep(idx, state) {
            const step = steps[idx];
            if (state === 'pending') {
                step.icon.className = "bi bi-circle text-secondary me-2 fs-5";
                step.text.className = "text-muted-custom";
            } else if (state === 'active') {
                step.icon.className = "bi bi-arrow-right-short text-info me-2 fs-4 spinner-border spinner-border-sm border-0";
                step.text.className = "text-info fw-bold";
            } else if (state === 'done') {
                step.icon.className = "bi bi-check-circle-fill text-success me-2 fs-5";
                step.text.className = "text-success";
            }
        }

        function performEvaluation() {
            errorPanel.classList.add('d-none');
            startProgressSimulation();

            fetch(`/interviews/${interviewId}/trigger-evaluation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(async response => {
                if (!response.ok) {
                    let errData = {};
                    try {
                        errData = await response.json();
                    } catch(e) {}
                    throw new Error(errData.error || "Server responded with status: " + response.status);
                }
                return response.json();
            })
            .then(data => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                
                // Complete all steps
                updateStep(0, 'done');
                updateStep(1, 'done');
                updateStep(2, 'done');
                updateStep(3, 'done');

                // Redirect to results
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 800);
            })
            .catch(err => {
                clearInterval(progressInterval);
                errorPanel.classList.remove('d-none');
                errorMessage.textContent = err.message || "An error occurred calling the Google Gemini / OpenAI Service.";
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                progressBar.classList.add('bg-danger');
            });
        }

        retryBtn.addEventListener('click', performEvaluation);

        // Run evaluation immediately
        performEvaluation();
    });
</script>
@endsection
