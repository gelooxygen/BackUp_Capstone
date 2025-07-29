@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Class Analytics Dashboard</h3>
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

            <!-- Teacher Summary Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ count($analytics['class_averages']) }}</h4>
                                    <p class="mb-0">Subjects Taught</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x"></i>
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
                                    <h4 class="mb-0">{{ count($analytics['top_performers']) }}</h4>
                                    <p class="mb-0">Top Performers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-trophy fa-2x"></i>
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
                                    <h4 class="mb-0">{{ count($analytics['low_performers']) }}</h4>
                                    <p class="mb-0">Need Support</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                                    <h4 class="mb-0">{{ count($analytics['attendance_overview']) }}</h4>
                                    <p class="mb-0">Classes Monitored</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Class Averages by Subject</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="classAveragesChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Assessment Type Breakdown</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="assessmentBreakdownChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Class Attendance Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="attendanceOverviewChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Performance Distribution</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceDistributionChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Performers Table -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Top Performers</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Student</th>
                                            <th>Average Score</th>
                                            <th>Assignments</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['top_performers'] as $student)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>{{ $student['student_name'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <strong>{{ $student['average_score'] }}%</strong>
                                                </td>
                                                <td>{{ $student['assignments_count'] }}</td>
                                                <td>
                                                    <span class="badge bg-success">Excellent</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Performers Table -->
                <div class="col-md-6">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Students Needing Support</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Student</th>
                                            <th>Average Score</th>
                                            <th>Assignments</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['low_performers'] as $student)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>{{ $student['student_name'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <strong>{{ $student['average_score'] }}%</strong>
                                                </td>
                                                <td>{{ $student['assignments_count'] }}</td>
                                                <td>
                                                    <span class="badge bg-danger">Needs Support</span>
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

            <!-- Class Performance Details -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Class Performance Details</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Subject</th>
                                            <th>Average Score</th>
                                            <th>Students</th>
                                            <th>Assignments</th>
                                            <th>Performance Level</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['class_averages'] as $class)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>{{ $class['subject_name'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <strong>{{ $class['average_score'] }}%</strong>
                                                </td>
                                                <td>{{ $class['students_count'] }}</td>
                                                <td>{{ $class['assignments_count'] }}</td>
                                                <td>
                                                    @if($class['average_score'] >= 90)
                                                        Excellent
                                                    @elseif($class['average_score'] >= 80)
                                                        Good
                                                    @elseif($class['average_score'] >= 70)
                                                        Average
                                                    @else
                                                        Needs Improvement
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($class['average_score'] >= 90)
                                                        <span class="badge bg-success">Excellent</span>
                                                    @elseif($class['average_score'] >= 80)
                                                        <span class="badge bg-primary">Good</span>
                                                    @elseif($class['average_score'] >= 70)
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
    
    .card.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .card.bg-info {
        background-color: #17a2b8 !important;
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let classAveragesChart, assessmentBreakdownChart, attendanceOverviewChart, performanceDistributionChart;

function updateAnalytics() {
    const academicYear = $('#academic_year_filter').val();
    const semester = $('#semester_filter').val();
    
    // Build query string
    const params = new URLSearchParams();
    if (academicYear) params.append('academic_year_id', academicYear);
    if (semester) params.append('semester_id', semester);
    
    // Redirect with parameters
    window.location.href = '{{ route("analytics.teacher-dashboard") }}?' + params.toString();
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
    // Class Averages Chart
    const classCtx = document.getElementById('classAveragesChart').getContext('2d');
    classAveragesChart = new Chart(classCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($analytics['class_averages'])->pluck('subject_name')) !!},
            datasets: [{
                label: 'Average Score (%)',
                data: {!! json_encode(collect($analytics['class_averages'])->pluck('average_score')) !!},
                backgroundColor: [
                    '#3d5ee1',
                    '#7bb13c',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8',
                    '#6c757d'
                ],
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

    // Assessment Breakdown Chart
    const assessmentCtx = document.getElementById('assessmentBreakdownChart').getContext('2d');
    assessmentBreakdownChart = new Chart(assessmentCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(collect($analytics['assessment_breakdown'])->pluck('assessment_type')) !!},
            datasets: [{
                data: {!! json_encode(collect($analytics['assessment_breakdown'])->pluck('assignments_count')) !!},
                backgroundColor: [
                    '#3d5ee1',
                    '#7bb13c',
                    '#ffc107',
                    '#dc3545',
                    '#17a2b8'
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

    // Attendance Overview Chart
    const attendanceCtx = document.getElementById('attendanceOverviewChart').getContext('2d');
    attendanceOverviewChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($analytics['attendance_overview'])->pluck('section_name')) !!},
            datasets: [{
                label: 'Attendance (%)',
                data: {!! json_encode(collect($analytics['attendance_overview'])->pluck('percentage')) !!},
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

    // Performance Distribution Chart
    const performanceCtx = document.getElementById('performanceDistributionChart').getContext('2d');
    performanceDistributionChart = new Chart(performanceCtx, {
        type: 'pie',
        data: {
            labels: ['Top Performers', 'Average Students', 'Need Support'],
            datasets: [{
                data: [
                    {{ count($analytics['top_performers']) }},
                    {{ count($analytics['class_averages']) * 2 }}, // Estimated
                    {{ count($analytics['low_performers']) }}
                ],
                backgroundColor: [
                    '#7bb13c',
                    '#ffc107',
                    '#dc3545'
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