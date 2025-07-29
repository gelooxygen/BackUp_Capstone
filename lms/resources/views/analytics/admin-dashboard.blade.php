@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">School Analytics Dashboard</h3>
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

            <!-- School Overview Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0">{{ $analytics['school_overview']['total_students'] }}</h4>
                                    <p class="mb-0">Total Students</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x"></i>
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
                                    <h4 class="mb-0">{{ $analytics['school_overview']['total_teachers'] }}</h4>
                                    <p class="mb-0">Total Teachers</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                                    <h4 class="mb-0">{{ $analytics['school_overview']['average_gpa'] }}</h4>
                                    <p class="mb-0">Average GPA</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
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
                                    <h4 class="mb-0">{{ $analytics['school_overview']['pass_rate'] }}%</h4>
                                    <p class="mb-0">Pass Rate</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pass/Fail Summary -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Pass/Fail Distribution</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h3 class="text-success">{{ $analytics['pass_fail_rates']['pass_rate'] }}%</h3>
                                        <p>Pass Rate</p>
                                        <small class="text-muted">{{ $analytics['pass_fail_rates']['passing_grades'] }} students</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-item">
                                        <h3 class="text-danger">{{ $analytics['pass_fail_rates']['fail_rate'] }}%</h3>
                                        <p>Fail Rate</p>
                                        <small class="text-muted">{{ $analytics['pass_fail_rates']['failing_grades'] }} students</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">School Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Total Subjects:</strong> {{ $analytics['school_overview']['total_subjects'] }}</p>
                                    <p><strong>Total Sections:</strong> {{ $analytics['school_overview']['total_sections'] }}</p>
                                </div>
                                <div class="col-6">
                                    <p><strong>Total Grades:</strong> {{ $analytics['pass_fail_rates']['total_grades'] }}</p>
                                    <p><strong>Overall Performance:</strong> 
                                        @if($analytics['school_overview']['average_gpa'] >= 3.5)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($analytics['school_overview']['average_gpa'] >= 3.0)
                                            <span class="badge bg-primary">Good</span>
                                        @elseif($analytics['school_overview']['average_gpa'] >= 2.5)
                                            <span class="badge bg-warning">Average</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement</span>
                                        @endif
                                    </p>
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
                            <h5 class="card-title">Year Level GPA Comparison</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="yearLevelChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Section Performance</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="sectionPerformanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">School Attendance Summary</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="schoolAttendanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Subject Performance Overview</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="subjectPerformanceChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Year Level Comparison Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Year Level Performance Details</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Grade Level</th>
                                            <th>Average GPA</th>
                                            <th>Students Count</th>
                                            <th>Performance Level</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['year_level_comparison'] as $year)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>Grade {{ $year['grade_level'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>
                                                    <strong>{{ $year['average_gpa'] }}</strong>
                                                </td>
                                                <td>{{ $year['students_count'] }}</td>
                                                <td>
                                                    @if($year['average_gpa'] >= 3.5)
                                                        Excellent
                                                    @elseif($year['average_gpa'] >= 3.0)
                                                        Good
                                                    @elseif($year['average_gpa'] >= 2.5)
                                                        Average
                                                    @else
                                                        Needs Improvement
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($year['average_gpa'] >= 3.5)
                                                        <span class="badge bg-success">Excellent</span>
                                                    @elseif($year['average_gpa'] >= 3.0)
                                                        <span class="badge bg-primary">Good</span>
                                                    @elseif($year['average_gpa'] >= 2.5)
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

            <!-- Section Performance Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Section Performance Details</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>Section</th>
                                            <th>Grade Level</th>
                                            <th>Average GPA</th>
                                            <th>Students Count</th>
                                            <th>Performance Level</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['section_performance'] as $section)
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <a>{{ $section['section_name'] }}</a>
                                                    </h2>
                                                </td>
                                                <td>{{ $section['grade_level'] }}</td>
                                                <td>
                                                    <strong>{{ $section['average_gpa'] }}</strong>
                                                </td>
                                                <td>{{ $section['students_count'] }}</td>
                                                <td>
                                                    @if($section['average_gpa'] >= 3.5)
                                                        Excellent
                                                    @elseif($section['average_gpa'] >= 3.0)
                                                        Good
                                                    @elseif($section['average_gpa'] >= 2.5)
                                                        Average
                                                    @else
                                                        Needs Improvement
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($section['average_gpa'] >= 3.5)
                                                        <span class="badge bg-success">Excellent</span>
                                                    @elseif($section['average_gpa'] >= 3.0)
                                                        <span class="badge bg-primary">Good</span>
                                                    @elseif($section['average_gpa'] >= 2.5)
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
    
    .stat-item h3 {
        margin: 0;
        font-weight: 600;
    }
    
    .stat-item p {
        margin: 5px 0;
        font-weight: 500;
    }
    
    .stat-item small {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let yearLevelChart, sectionPerformanceChart, schoolAttendanceChart, subjectPerformanceChart;

function updateAnalytics() {
    const academicYear = $('#academic_year_filter').val();
    const semester = $('#semester_filter').val();
    
    // Build query string
    const params = new URLSearchParams();
    if (academicYear) params.append('academic_year_id', academicYear);
    if (semester) params.append('semester_id', semester);
    
    // Redirect with parameters
    window.location.href = '{{ route("analytics.admin-dashboard") }}?' + params.toString();
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
    // Year Level Chart
    const yearCtx = document.getElementById('yearLevelChart').getContext('2d');
    yearLevelChart = new Chart(yearCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($analytics['year_level_comparison'])->pluck('grade_level')->map(function($level) { return 'Grade ' . $level; })) !!},
            datasets: [{
                label: 'Average GPA',
                data: {!! json_encode(collect($analytics['year_level_comparison'])->pluck('average_gpa')) !!},
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

    // Section Performance Chart
    const sectionCtx = document.getElementById('sectionPerformanceChart').getContext('2d');
    sectionPerformanceChart = new Chart(sectionCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(collect($analytics['section_performance'])->pluck('section_name')) !!},
            datasets: [{
                label: 'Average GPA',
                data: {!! json_encode(collect($analytics['section_performance'])->pluck('average_gpa')) !!},
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

    // School Attendance Chart
    const attendanceCtx = document.getElementById('schoolAttendanceChart').getContext('2d');
    schoolAttendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(collect($analytics['attendance_summary']['monthly_data'])->pluck('month')) !!},
            datasets: [{
                label: 'Attendance (%)',
                data: {!! json_encode(collect($analytics['attendance_summary']['monthly_data'])->pluck('percentage')) !!},
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