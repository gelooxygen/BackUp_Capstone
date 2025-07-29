@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">My Academic Analytics</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Analytics</li>
                        </ul>
                    </div>
                    <div class="col-auto text-end float-end ms-auto download-grp">
                        <button type="button" class="btn btn-primary" onclick="exportReport()">
                            <i class="fas fa-download"></i> Export Report
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="student-group-form">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="academic_year_filter" name="academic_year_id">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="semester_filter" name="semester_id">
                                <option value="">Select Semester</option>
                                @foreach($semesters as $semester)
                                    <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>
                                        {{ $semester->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="search-student-btn">
                            <button type="button" class="btn btn-primary" onclick="updateAnalytics()">
                                <i class="fas fa-chart-line"></i> Update Analytics
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $analytics['performance_indicators']['average_score'] }}%</h4>
                                    <p class="mb-0">Average Score</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $analytics['performance_indicators']['total_assignments'] }}</h4>
                                    <p class="mb-0">Total Assignments</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $analytics['attendance_summary']['overall_percentage'] }}%</h4>
                                    <p class="mb-0">Attendance Rate</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $analytics['performance_indicators']['low_grades_count'] }}</h4>
                                    <p class="mb-0">Low Grades</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Alerts -->
            @if(count($analytics['performance_indicators']['alerts']) > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-bell"></i> Performance Alerts
                                </h5>
                                @foreach($analytics['performance_indicators']['alerts'] as $alert)
                                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                                        <i class="fas fa-info-circle"></i> {{ $alert['message'] }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Charts Row 1 -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Grade Trends</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="gradeTrendsChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">GPA Trend</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="gpaTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Monthly Attendance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Subject Performance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="subjectPerformanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Performance Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Subject Performance Details</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Average Score</th>
                                            <th>Assignments</th>
                                            <th>Performance Level</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['subject_performance'] as $subject)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>{{ $subject['subject_name'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <strong>{{ $subject['average_score'] }}%</strong>
                                                </td>
                                                <td>{{ $subject['assignments_count'] }}</td>
                                                <td>{{ $subject['performance_level'] }}</td>
                                                <td>
                                                    @if($subject['average_score'] >= 90)
                                                        <span class="badge bg-success">Excellent</span>
                                                    @elseif($subject['average_score'] >= 80)
                                                        <span class="badge bg-primary">Good</span>
                                                    @elseif($subject['average_score'] >= 70)
                                                        <span class="badge bg-warning">Average</span>
                                                    @else
                                                        <span class="badge bg-danger">Needs Improvement</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            @if(count($analytics['recent_activities']) > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Activities</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Activity</th>
                                                <th>Lesson</th>
                                                <th>Submitted</th>
                                                <th>Status</th>
                                                <th>Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analytics['recent_activities'] as $activity)
                                                <tr>
                                                    <td>{{ $activity->activity->title }}</td>
                                                    <td>{{ $activity->activity->lesson->title }}</td>
                                                    <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        @if($activity->status === 'submitted')
                                                            <span class="badge bg-warning">Submitted</span>
                                                        @elseif($activity->status === 'graded')
                                                            <span class="badge bg-success">Graded</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($activity->status === 'graded')
                                                            <strong>{{ $activity->total_score }}/{{ $activity->max_possible_score }}</strong>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
<style>
    .card.bg-primary {
        background-color: #3d5ee1 !important;
    }
    
    .card.bg-success {
        background-color: #7bb13c !important;
    }
    
    .card.bg-info {
        background-color: #17a2b8 !important;
    }
    
    .card.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .badge.bg-success {
        background-color: #7bb13c !important;
    }
    
    .badge.bg-primary {
        background-color: #3d5ee1 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let gradeTrendsChart, gpaTrendChart, attendanceChart, subjectPerformanceChart;

function updateAnalytics() {
    const academicYear = $('#academic_year_filter').val();
    const semester = $('#semester_filter').val();
    
    // Build query string
    const params = new URLSearchParams();
    if (academicYear) params.append('academic_year_id', academicYear);
    if (semester) params.append('semester_id', semester);
    
    // Redirect with parameters
    window.location.href = '{{ route("analytics.student-dashboard") }}?' + params.toString();
}

function exportReport() {
    const academicYear = $('#academic_year_filter').val();
    const semester = $('#semester_filter').val();
    
    // Build query string
    const params = new URLSearchParams();
    if (academicYear) params.append('academic_year_id', academicYear);
    if (semester) params.append('semester_id', semester);
    
    // Download report
    window.location.href = '{{ route("analytics.export") }}?' + params.toString();
}

$(document).ready(function() {
    // Grade Trends Chart
    const gradeCtx = document.getElementById('gradeTrendsChart').getContext('2d');
    gradeTrendsChart = new Chart(gradeCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($analytics['grade_trends'])->pluck('period')) !!},
            datasets: [{
                label: 'Grade Score (%)',
                data: {!! json_encode(collect($analytics['grade_trends'])->pluck('score')) !!},
                borderColor: '#3d5ee1',
                backgroundColor: 'rgba(61, 94, 225, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // GPA Trend Chart
    const gpaCtx = document.getElementById('gpaTrendChart').getContext('2d');
    gpaTrendChart = new Chart(gpaCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($analytics['gpa_trend'])->pluck('period')) !!},
            datasets: [{
                label: 'GPA',
                data: {!! json_encode(collect($analytics['gpa_trend'])->pluck('gpa')) !!},
                borderColor: '#7bb13c',
                backgroundColor: 'rgba(123, 177, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 4.0
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($analytics['attendance_summary']['monthly_data'])->pluck('month')) !!},
            datasets: [{
                label: 'Attendance (%)',
                data: {!! json_encode(collect($analytics['attendance_summary']['monthly_data'])->pluck('percentage')) !!},
                backgroundColor: '#17a2b8',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Subject Performance Chart
    const subjectCtx = document.getElementById('subjectPerformanceChart').getContext('2d');
    subjectPerformanceChart = new Chart(subjectCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(collect($analytics['subject_performance'])->pluck('subject_name')) !!},
            datasets: [{
                data: {!! json_encode(collect($analytics['subject_performance'])->pluck('average_score')) !!},
                backgroundColor: [
                    '#3d5ee1',
                    '#7bb13c',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8',
                    '#6c757d'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush 