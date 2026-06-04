@extends('layouts.app')

@slot('title')
    Admin Console
@endslot

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold m-0 text-white"><i class="bi bi-shield-lock text-indigo me-2"></i> Central Console</h2>
        <p class="text-muted-custom m-0">Monitor system statistics, candidate activities, audit trails, and configurations.</p>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-people fs-4 text-indigo"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Total Users</h6>
                        <h3 class="fw-bold m-0 text-white">{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(6, 182, 212, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-journal-text fs-4 text-info"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Total Sessions</h6>
                        <h3 class="fw-bold m-0 text-white">{{ $totalInterviews }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-award fs-4 text-success"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">System Average</h6>
                        <h3 class="fw-bold m-0 text-white">{{ number_format($avgScore, 1) }} <span class="fs-6 text-muted-custom">/10</span></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card p-3 h-100">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3 text-white d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.15); width: 50px; height: 50px;">
                        <i class="bi bi-cpu fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted-custom m-0 font-monospace text-uppercase" style="font-size: 0.75rem;">Active Engine</h6>
                        <h3 class="fw-bold m-0 text-white text-uppercase" style="font-size: 1.25rem;">
                            {{ $aiProvider }}
                            <span class="d-block text-muted-custom fs-7" style="font-size: 0.7rem; font-weight: normal;">{{ $activeModel }}</span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Dual Panels: Recent Interviews & Activity Logs -->
    <div class="row g-4">
        
        <!-- Recent Interviews Panel -->
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0 text-white"><i class="bi bi-clock-history text-indigo me-2"></i> Recent Candidates Mock Exams</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary py-1" style="font-size: 0.8rem;">Manage Users</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover m-0 align-middle" style="font-size: 0.85rem;">
                            <thead>
                                <tr class="border-secondary text-muted-custom">
                                    <th class="px-3">CANDIDATE</th>
                                    <th>TOPIC</th>
                                    <th>DIFFICULTY</th>
                                    <th class="text-end">SCORE</th>
                                    <th class="text-end px-3">DATE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInterviews as $item)
                                    <tr class="border-secondary">
                                        <td class="px-3">
                                            <div class="fw-bold text-white">{{ $item->user->name }}</div>
                                            <div class="text-muted-custom" style="font-size: 0.75rem;">{{ $item->user->email }}</div>
                                        </td>
                                        <td class="fw-bold">
                                            <i class="bi {{ $item->category->icon_class }} me-1 text-info"></i>{{ $item->category->name }}
                                        </td>
                                        <td class="text-uppercase" style="font-size: 0.75rem;">{{ $item->difficulty }}</td>
                                        <td class="text-end fw-bold">
                                            @if($item->status === 'completed')
                                                <span class="text-info">{{ number_format($item->overall_score, 1) }}</span><span class="text-muted" style="font-size: 0.75rem;">/10</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Ongoing</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted-custom px-3" style="font-size: 0.75rem;">
                                            {{ $item->created_at->format('M d, H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted-custom">No interviews attempted yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Audit Logs Panel -->
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-transparent py-3">
                    <h5 class="fw-bold m-0 text-white"><i class="bi bi-shield-shaded text-info me-2"></i> Audit Logs</h5>
                </div>
                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                    <ul class="list-group list-group-flush bg-transparent">
                        @forelse($activityLogs as $log)
                            <li class="list-group-item bg-transparent border-secondary text-white py-3 px-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.8rem;">
                                    <span class="fw-bold text-info">{{ $log->user ? $log->user->name : 'System Guest' }}</span>
                                    <span class="text-muted-custom">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="fw-bold text-white mb-1" style="font-size: 0.85rem;">{{ $log->action }}</div>
                                <p class="m-0 text-muted-custom" style="font-size: 0.8rem; line-height: 1.4;">{{ $log->description }}</p>
                                <div class="text-muted-custom mt-1 font-monospace" style="font-size: 0.7rem;">IP: {{ $log->ip_address }}</div>
                            </li>
                        @empty
                            <li class="list-group-item bg-transparent text-center py-5 text-muted-custom border-0">No activities logged yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
