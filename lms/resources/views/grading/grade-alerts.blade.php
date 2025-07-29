@extends('layouts.app')

@section('title', 'Grade Alerts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-exclamation-triangle"></i> Grade Alerts & Performance Monitoring
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Alert Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Critical Alerts</h5>
                                    <h3>{{ $alerts->where('alert_type', 'at_risk')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-title">Performance Drops</div>
                                <h3>{{ $alerts->where('alert_type', 'performance_drop')->count() }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Low Grades</h5>
                                    <h3>{{ $alerts->where('alert_type', 'low_grade')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Active</h5>
                                    <h3>{{ $alerts->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($alerts->count() > 0)
                        <!-- Alerts Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Student</th>
                                        <th>Alert Type</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Current Value</th>
                                        <th>Threshold</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $alert)
                                        <tr class="table-{{ $alert->severity_level }}">
                                            <td>
                                                <strong>{{ $alert->student->first_name }} {{ $alert->student->last_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $alert->student->admission_id ?? $alert->student->id }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $alert->severity_level }}">
                                                    <i class="fas fa-{{ $alert->alert_icon }}"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($alert->subject)
                                                    {{ $alert->subject->subject_name }}
                                                @else
                                                    <span class="text-muted">General</span>
                                                @endif
                                            </td>
                                            <td>{{ $alert->message }}</td>
                                            <td>
                                                @if($alert->current_value)
                                                    <span class="badge badge-{{ $alert->current_value < $alert->threshold_value ? 'danger' : 'success' }}">
                                                        {{ $alert->current_value }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($alert->threshold_value)
                                                    <span class="badge badge-info">{{ $alert->threshold_value }}%</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $alert->created_at->format('M d, Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('teacher.grading.performance-analytics', ['student_id' => $alert->student->id]) }}"
                                                       class="btn btn-sm btn-info" title="View Analytics">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                                                                            <form method="POST" action="{{ route('teacher.grading.resolve-alert', $alert->id) }}" 
                                                          style="display: inline;" 
                                                          onsubmit="return confirm('Mark this alert as resolved?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Resolve Alert">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $alerts->links() }}
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Great news!</strong> No active grade alerts at the moment. All students are performing well.
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('teacher.grading.grade-entry') }}" class="btn btn-primary btn-block">
                                                <i class="fas fa-plus"></i> Enter Grades
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('teacher.grading.gpa-ranking') }}" class="btn btn-info btn-block">
                                                <i class="fas fa-chart-bar"></i> View GPA Ranking
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('teacher.grading.weight-settings') }}" class="btn btn-warning btn-block">
                                                <i class="fas fa-cog"></i> Weight Settings
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('teacher.grading.performance-analytics') }}" class="btn btn-success btn-block">
                                                <i class="fas fa-analytics"></i> Performance Analytics
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh alerts every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000); // 5 minutes

    // Highlight critical alerts
    $('.table-critical').addClass('table-danger');
    
    // Add tooltips
    $('[title]').tooltip();
});
</script>
@endpush
@endsection 