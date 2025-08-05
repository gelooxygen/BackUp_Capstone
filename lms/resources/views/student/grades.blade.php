@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">My Grades</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">Grades</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Alerts Section -->
            @if($gradeAlerts->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Grade Alerts
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <h6>You have {{ $gradeAlerts->count() }} grade alert(s) that need attention:</h6>
                                <ul class="mb-0">
                                    @foreach($gradeAlerts as $alert)
                                    <li>
                                        <strong>{{ $alert->subject->subject_name ?? 'Subject' }}</strong>: 
                                        {{ $alert->message }} (Current Grade: {{ $alert->current_grade }})
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- GPA Summary Section -->
            @if($gpaRecords->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-chart-line me-2"></i>GPA Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($gpaRecords->take(3) as $gpa)
                                <div class="col-md-4">
                                    <div class="gpa-card text-center p-3 border rounded">
                                        <h4 class="text-primary mb-2">{{ $gpa->gpa }}</h4>
                                        <p class="mb-1"><strong>{{ $gpa->academicYear->name ?? 'N/A' }}</strong></p>
                                        <p class="text-muted mb-0">{{ $gpa->semester->name ?? 'N/A' }}</p>
                                        <small class="text-muted">Total Units: {{ $gpa->total_units }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Grades Table Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class="fas fa-clipboard-list me-2"></i>All Grades
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($grades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Component</th>
                                                <th>Score</th>
                                                <th>Max Score</th>
                                                <th>Percentage</th>
                                                <th>Teacher</th>
                                                <th>Period</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grades as $grade)
                                            <tr>
                                                <td>
                                                    <strong>{{ $grade->subject->subject_name ?? 'N/A' }}</strong>
                                                </td>
                                                <td>{{ $grade->component->name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $grade->percentage >= 75 ? 'bg-success' : ($grade->percentage >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $grade->score ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>{{ $grade->max_score ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge {{ $grade->percentage >= 75 ? 'bg-success' : ($grade->percentage >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $grade->percentage ?? 'N/A' }}%
                                                    </span>
                                                </td>
                                                <td>{{ $grade->teacher->full_name ?? 'N/A' }}</td>
                                                <td>{{ $grade->grading_period ?? 'N/A' }}</td>
                                                <td>{{ $grade->created_at ? $grade->created_at->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                        <h5>No Grades Available</h5>
                                        <p class="text-muted">You don't have any grades recorded yet.</p>
                                        <p class="text-muted">Grades will appear here once your teachers have entered them.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
.gpa-card {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    transition: all 0.3s ease;
}

.gpa-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.empty-state {
    padding: 40px 20px;
}

.empty-state i {
    color: #dee2e6;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.badge {
    font-size: 0.875em;
}
</style>
@endpush

@endsection 