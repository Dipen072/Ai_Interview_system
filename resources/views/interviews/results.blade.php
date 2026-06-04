@extends('layouts.app')

@slot('title')
    Interview Results
@endslot

@section('content')
<div class="container-fluid" style="max-width: 1000px;">
    
    <!-- Breadcrumb / Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
            <a href="{{ route('results.export', $interview->id) }}" class="btn btn-info">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- 1. Score Summary Hero Panel -->
    <div class="card p-4 mb-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #111827 0%, #1f2937 100%); border-color: rgba(99, 102, 241, 0.2);">
        <div class="row align-items-center g-4">
            <div class="col-md-3 text-center">
                <!-- Circular Score Indicator -->
                <div class="d-inline-block position-relative mb-2" style="width: 130px; height: 130px;">
                    <svg viewBox="0 0 36 36" class="circular-chart indigo" style="display: block; margin: 10px auto; max-width: 100%; max-height: 100%;">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="fill: none; stroke: #374151; stroke-width: 2.8;" />
                        <path class="circle" stroke-dasharray="{{ $result->percentage }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" style="fill: none; stroke: #6366f1; stroke-width: 2.8; stroke-linecap: round; animation: progress 1s ease-out forwards;" />
                    </svg>
                    <div class="position-absolute start-50 top-50 translate-middle text-center">
                        <h2 class="fw-bold m-0 text-white font-monospace">{{ number_format($result->overall_score, 1) }}</h2>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">OUT OF 10</span>
                    </div>
                </div>
                <h6 class="fw-bold text-indigo text-uppercase font-monospace tracking-wide m-0" style="font-size: 0.8rem;">Overall Score</h6>
            </div>
            
            <div class="col-md-9 border-start-md border-secondary ps-md-4">
                <span class="badge bg-indigo text-white mb-2 font-monospace text-uppercase" style="background: var(--primary-accent);">{{ $interview->category->name }} Mock Exam</span>
                <h3 class="fw-bold text-white mb-2">Performance Assessment</h3>
                
                <div class="row g-3 mb-3 text-muted-custom" style="font-size: 0.9rem;">
                    <div class="col-sm-6 col-md-4">
                        <i class="bi bi-speedometer2 me-2 text-info"></i> Difficulty: <span class="text-white fw-bold text-uppercase">{{ $interview->difficulty }}</span>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <i class="bi bi-clock me-2 text-info"></i> Duration: <span class="text-white fw-bold">{{ floor($result->duration_seconds / 60) }}m {{ $result->duration_seconds % 60 }}s</span>
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <i class="bi bi-calendar-event me-2 text-info"></i> Completed: <span class="text-white fw-bold">{{ $result->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>

                <div class="p-3 rounded bg-dark border border-secondary">
                    <h6 class="fw-bold text-white mb-1"><i class="bi bi-chat-left-quote text-indigo me-2"></i> AI Evaluator Comments:</h6>
                    <p class="m-0 text-muted-custom style-quote" style="font-size: 0.9rem; line-height: 1.5; font-style: italic;">
                        "{{ $result->summary_feedback }}"
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Criteria Breakdown cards -->
    <div class="mb-4">
        <h4 class="fw-bold text-white mb-3"><i class="bi bi-bar-chart-steps text-indigo me-2"></i> Evaluation Criteria Breakdown</h4>
        <div class="row g-3">
            @foreach(['accuracy' => 'Accuracy', 'technical_knowledge' => 'Technical Depth', 'communication' => 'Communication', 'completeness' => 'Completeness'] as $key => $label)
                @php $fb = $feedback->get($key); @endphp
                @if($fb)
                    <div class="col-md-6">
                        <div class="card p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold text-white m-0">
                                    <i class="bi 
                                        @if($key === 'accuracy') bi-bullseye text-danger
                                        @elseif($key === 'technical_knowledge') bi-braces-asterisk text-warning
                                        @elseif($key === 'communication') bi-translate text-success
                                        @else bi-clipboard2-check text-info
                                        @endif me-2 fs-5"></i>{{ $label }}
                                </h6>
                                <span class="badge bg-secondary-subtle text-white font-monospace fs-6 px-2 border-secondary">
                                    {{ $fb->score }} / 10
                                </span>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="progress mb-3" style="height: 6px; background-color: #374151;">
                                <div class="progress-bar 
                                    @if($fb->score >= 8) bg-success
                                    @elseif($fb->score >= 6) bg-info
                                    @elseif($fb->score >= 4) bg-warning
                                    @else bg-danger
                                    @endif" 
                                     role="progressbar" 
                                     style="width: {{ $fb->score * 10 }}%;" 
                                     aria-valuenow="{{ $fb->score * 10 }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100"></div>
                            </div>

                            <!-- Feedback Details -->
                            <div style="font-size: 0.85rem; line-height: 1.5;">
                                <div class="mb-2">
                                    <span class="text-white fw-bold d-block mb-1">Assessment:</span>
                                    <p class="text-muted-custom m-0" style="white-space: pre-line;">{{ $fb->feedback_text }}</p>
                                </div>
                                @if($fb->suggestions)
                                    <div>
                                        <span class="text-info fw-bold d-block mb-1">Suggestions:</span>
                                        <p class="text-muted-custom m-0" style="white-space: pre-line;">{{ $fb->suggestions }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- 3. Question & Answer Logs -->
    <div class="mb-4">
        <h4 class="fw-bold text-white mb-3"><i class="bi bi-chat-left-text text-indigo me-2"></i> Responses Log</h4>
        
        <div class="accordion border-0" id="responsesAccordion">
            @foreach($questions as $index => $q)
                @php $ans = $answers->get($q->id); @endphp
                <div class="accordion-item bg-dark border-secondary mb-3 rounded overflow-hidden">
                    <h2 class="accordion-header" id="heading_{{ $index }}">
                        <button class="accordion-button bg-transparent text-white collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_{{ $index }}" aria-expanded="false" aria-controls="collapse_{{ $index }}">
                            <div class="d-flex align-items-center w-100">
                                <span class="badge bg-secondary me-3 font-monospace rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    {{ $index + 1 }}
                                </span>
                                <div class="text-truncate me-3" style="max-width: 75%;">{{ $q->question_text }}</div>
                                @if($ans && $ans->answer_text)
                                    <span class="badge bg-success-subtle text-success rounded-pill ms-auto me-3 d-none d-md-inline" style="font-size: 0.75rem;">Answered</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill ms-auto me-3 d-none d-md-inline" style="font-size: 0.75rem;">Skipped</span>
                                @endif
                            </div>
                        </button>
                    </h2>
                    
                    <div id="collapse_{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading_{{ $index }}" data-bs-parent="#responsesAccordion">
                        <div class="accordion-body border-top border-secondary p-4 bg-secondary-subtle" style="background-color: rgba(31, 41, 55, 0.4) !important;">
                            
                            <!-- Question body -->
                            <div class="mb-3">
                                <h6 class="fw-bold text-white mb-1">Question Prompt:</h6>
                                <p class="text-white-50 m-0">{{ $q->question_text }}</p>
                            </div>

                            <!-- Answer body -->
                            <div>
                                <h6 class="fw-bold text-white mb-1">Your Submitted Response:</h6>
                                @if($ans && $ans->answer_text)
                                    <div class="p-3 rounded bg-dark border border-secondary font-monospace" style="color: #cbd5e1; white-space: pre-wrap; font-size: 0.9rem;">
                                        {{ $ans->answer_text }}
                                    </div>
                                @else
                                    <div class="p-3 rounded bg-dark border border-danger-subtle text-danger font-monospace text-center py-4" style="font-size: 0.9rem;">
                                        <i class="bi bi-exclamation-triangle me-2"></i> No response was submitted for this question.
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* Printing styles */
    @media print {
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .card {
            background-color: #ffffff !important;
            border: 1px solid #dee2e6 !important;
            color: #000000 !important;
            box-shadow: none !important;
        }
        .circular-chart path.circle-bg {
            stroke: #e9ecef !important;
        }
        .circular-chart path.circle {
            stroke: #0d6efd !important;
        }
        .text-white, .accordion-button {
            color: #000000 !important;
        }
        .text-muted-custom, .text-white-50 {
            color: #495057 !important;
        }
        .badge {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            background-color: transparent !important;
        }
        .bg-dark {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }
        .accordion-item {
            background-color: #ffffff !important;
            border-color: #dee2e6 !important;
        }
        .accordion-body {
            background-color: #ffffff !important;
            border-top: 1px solid #dee2e6 !important;
        }
        .border-start-md {
            border-left: none !important;
        }
    }

    /* Column borders helper */
    @media (min-width: 768px) {
        .border-start-md {
            border-left: 1px solid var(--border-color) !important;
        }
    }

    /* Accordion Button customized styling */
    .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%239ca3af'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
    }
    .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236366f1'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
    }
</style>
@endsection
