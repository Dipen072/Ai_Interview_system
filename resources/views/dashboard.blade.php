@extends('layouts.app')

@slot('title')
    Dashboard
@endslot

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0 text-white">Welcome back, {{ auth()->user()->name }}!</h2>
            <p class="text-muted-custom m-0">Ready to level up your technical skills today?</p>
        </div>
        <div>
            <a href="{{ route('interviews.setup') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i> Start New Interview
            </a>
        </div>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-indigo p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-journal-text fs-4 text-indigo"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Total Attempts</h6>
                        <h3 class="fw-bold m-0 text-white">{{ $totalInterviews }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-patch-check fs-4 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Completed Sessions</h6>
                        <h3 class="fw-bold m-0 text-white">{{ $completedInterviews }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(6, 182, 212, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-graph-up fs-4 text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Average Score</h6>
                        <h3 class="fw-bold m-0 text-white">{{ number_format($avgScore, 1) }} <span class="fs-6 text-muted-custom">/10</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-trophy fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Best Performance</h6>
                        <h3 class="fw-bold m-0 text-white">{{ number_format($bestScore, 1) }} <span class="fs-6 text-muted-custom">/10</span></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- 2. Performance Chart Widget -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="fw-bold m-0 text-white"><i class="bi bi-activity text-indigo me-2"></i> Performance Analytics</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center p-4">
                    @if(count($chartData) > 0)
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted-custom">
                            <i class="bi bi-bar-chart-line fs-1 mb-3 d-block text-secondary"></i>
                            <p class="m-0">No completed interviews yet. Finish your first mock session to see analytics trends!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Category Progress -->
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="fw-bold m-0 text-white"><i class="bi bi-bar-chart text-info me-2"></i> Skills Progress</h5>
                </div>
                <div class="card-body p-4" style="max-height: 280px; overflow-y: auto;">
                    @php $hasAttempts = false; @endphp
                    @foreach($categoryProgress as $progress)
                        @if($progress->total_attempts > 0)
                            @php $hasAttempts = true; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.9rem;">
                                    <span class="text-white"><i class="bi {{ $progress->icon_class }} me-2 text-indigo"></i>{{ $progress->name }}</span>
                                    <span class="text-muted-custom font-monospace">{{ number_format($progress->avg_score, 1) }} / 10 ({{ $progress->total_attempts }} attempts)</span>
                                </div>
                                <div class="progress" style="height: 6px; background-color: #374151;">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress->avg_score * 10 }}%;" aria-valuenow="{{ $progress->avg_score * 10 }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasAttempts)
                        <div class="text-center py-4 text-muted-custom">
                            <i class="bi bi-award fs-2 mb-2 d-block text-secondary"></i>
                            <p class="m-0" style="font-size: 0.9rem;">Start taking interviews to populate your skill progress bars.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Category Selector Grid -->
    <div class="mb-4">
        <h4 class="fw-bold text-white mb-3"><i class="bi bi-grid-3x3-gap text-indigo me-2"></i> Select Interview Topic</h4>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach($allCategories as $category)
                <div class="col">
                    <a href="{{ route('interviews.setup', ['category_id' => $category->id]) }}" class="text-decoration-none card h-100 hover-card p-3 border-secondary-hover">
                        <div class="d-flex align-items-center mb-2">
                            <div class="rounded-3 bg-secondary-subtle p-2 me-3 d-flex align-items-center justify-content-center text-info" style="width: 40px; height: 40px; background-color: rgba(6, 182, 212, 0.1) !important;">
                                <i class="bi {{ $category->icon_class ?? 'bi-tag' }} fs-5"></i>
                            </div>
                            <h5 class="fw-bold text-white m-0">{{ $category->name }}</h5>
                        </div>
                        <p class="text-muted-custom m-0 text-truncate-2" style="font-size: 0.85rem; line-height: 1.4;">
                            {{ $category->description }}
                        </p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 5. Recent Attempts Table -->
    <div class="card mb-4">
        <div class="card-header bg-transparent py-3">
            <h5 class="fw-bold m-0 text-white"><i class="bi bi-clock-history text-indigo me-2"></i> Recent Attempts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover m-0 align-middle">
                    <thead>
                        <tr class="border-secondary" style="font-size: 0.85rem; color: var(--text-secondary);">
                            <th class="px-4 py-3">DATE</th>
                            <th class="py-3">TOPIC</th>
                            <th class="py-3">DIFFICULTY</th>
                            <th class="py-3">QUESTIONS</th>
                            <th class="py-3 text-center">STATUS</th>
                            <th class="py-3 text-end">SCORE</th>
                            <th class="px-4 py-3 text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInterviews as $item)
                            <tr class="border-secondary">
                                <td class="px-4 py-3 text-muted-custom" style="font-size: 0.9rem;">
                                    {{ $item->created_at->format('M d, Y h:i A') }}
                                </td>
                                <td class="py-3 fw-bold text-white">
                                    <i class="bi {{ $item->category->icon_class }} me-2 text-info"></i>{{ $item->category->name }}
                                </td>
                                <td class="py-3">
                                    <span class="badge font-monospace text-uppercase 
                                        @if($item->difficulty === 'easy') bg-success-subtle text-success-emphasis
                                        @elseif($item->difficulty === 'medium') bg-warning-subtle text-warning-emphasis
                                        @else bg-danger-subtle text-danger-emphasis
                                        @endif px-2 py-1 border-0">
                                        {{ $item->difficulty }}
                                    </span>
                                </td>
                                <td class="py-3 text-muted-custom">{{ $item->total_questions }}</td>
                                <td class="py-3 text-center">
                                    @if($item->status === 'completed')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Completed</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2">Ongoing</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end fw-bold">
                                    @if($item->status === 'completed')
                                        <span class="text-info">{{ number_format($item->overall_score, 1) }}</span><span class="text-muted" style="font-size: 0.8rem;"> / 10</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($item->status === 'completed')
                                        <a href="{{ route('results.show', $item->id) }}" class="btn btn-sm btn-secondary py-1 px-3">
                                            <i class="bi bi-eye me-1"></i> View Result
                                        </a>
                                    @else
                                        <a href="{{ route('interviews.arena', $item->id) }}" class="btn btn-sm btn-primary py-1 px-3">
                                            Resume <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted-custom">
                                    <i class="bi bi-folder2-open fs-1 mb-2 d-block text-secondary"></i>
                                    You have not attempted any interviews yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        border-color: var(--border-color);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-card:hover {
        transform: translateY(-4px);
        background-color: #243042 !important;
        border-color: var(--primary-accent) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }
    .border-secondary-hover {
        border-color: #374151;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .text-indigo {
        color: #818cf8 !important;
    }
</style>
@endsection

@section('scripts')
@if(count($chartData) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        // Gradient fill for chart lines
        const gradient = ctx.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Overall Score',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#818cf8',
                    pointBorderColor: '#0b0f19',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        borderColor: '#374151',
                        borderWidth: 1,
                        titleColor: '#f9fafb',
                        bodyColor: '#f9fafb',
                        callbacks: {
                            label: function(context) {
                                return ' Score: ' + context.raw + ' / 10';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(55, 65, 81, 0.4)',
                        },
                        ticks: {
                            color: '#9ca3af',
                            font: {
                                family: 'Outfit'
                            }
                        }
                    },
                    y: {
                        min: 0,
                        max: 10,
                        grid: {
                            color: 'rgba(55, 65, 81, 0.4)',
                        },
                        ticks: {
                            color: '#9ca3af',
                            font: {
                                family: 'Outfit'
                            },
                            stepSize: 2
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
