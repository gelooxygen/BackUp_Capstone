@extends('layouts.app')

@section('title', 'Performance Analytics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Student Performance Analytics</h4>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('teacher.grading.performance-analytics') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="student_id">Student</label>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_id ?? $student->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="academic_year_id">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $academicYearId == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="semester_id">Semester</label>
                                    <select name="semester_id" id="semester_id" class="form-control">
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Analyze
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($performanceData && $performanceData->count() > 0)
                        <!-- Student Info -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Student Information</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Name:</strong> {{ $performanceData->first()->student->first_name }} {{ $performanceData->first()->student->last_name }}</p>
                                                <p><strong>Student ID:</strong> {{ $performanceData->first()->student->admission_id ?? $performanceData->first()->student->id }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Academic Year:</strong> {{ $academicYears->find($academicYearId)->name ?? 'N/A' }}</p>
                                                <p><strong>Semester:</strong> {{ $semesters->find($semesterId)->name ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Overview -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Subjects</h5>
                                        <h3>{{ $performanceData->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Average Grade</h5>
                                        <h3>{{ number_format($performanceData->flatten()->avg('percentage'), 1) }}%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Highest Grade</h5>
                                        <h3>{{ number_format($performanceData->flatten()->max('percentage'), 1) }}%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Lowest Grade</h5>
                                        <h3>{{ number_format($performanceData->flatten()->min('percentage'), 1) }}%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Alerts -->
                        @if($alerts && $alerts->count() > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-danger">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-exclamation-triangle"></i> Performance Alerts
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($alerts as $alert)
                                                <div class="alert alert-{{ $alert->severity_level }} mb-2">
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $alert->alert_type)) }}:</strong> 
                                                    {{ $alert->message }}
                                                    @if($alert->current_value)
                                                        <br><small>Current: {{ $alert->current_value }}% | Threshold: {{ $alert->threshold_value }}%</small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Performance Charts -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Subject Performance</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="subjectPerformanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Performance Trends</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="performanceTrendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Performance Table -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Detailed Performance by Subject</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Subject</th>
                                                        <th>Component</th>
                                                        <th>Score</th>
                                                        <th>Max Score</th>
                                                        <th>Percentage</th>
                                                        <th>Letter Grade</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($performanceData as $subjectId => $grades)
                                                        @foreach($grades as $grade)
                                                            <tr class="{{ $grade->percentage < 75 ? 'table-danger' : ($grade->percentage < 85 ? 'table-warning' : '') }}">
                                                                <td>{{ $grade->subject->subject_name }}</td>
                                                                <td>{{ $grade->component->name }}</td>
                                                                <td>{{ $grade->score }}</td>
                                                                <td>{{ $grade->max_score }}</td>
                                                                <td>
                                                                    <span class="badge badge-{{ $grade->percentage >= 90 ? 'success' : ($grade->percentage >= 80 ? 'info' : ($grade->percentage >= 70 ? 'warning' : 'danger')) }}">
                                                                        {{ number_format($grade->percentage, 1) }}%
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $letterGrade = '';
                                                                        if ($grade->percentage >= 90) $letterGrade = 'A';
                                                                        elseif ($grade->percentage >= 85) $letterGrade = 'B+';
                                                                        elseif ($grade->percentage >= 80) $letterGrade = 'B';
                                                                        elseif ($grade->percentage >= 75) $letterGrade = 'C+';
                                                                        elseif ($grade->percentage >= 70) $letterGrade = 'C';
                                                                        elseif ($grade->percentage >= 65) $letterGrade = 'D+';
                                                                        elseif ($grade->percentage >= 60) $letterGrade = 'D';
                                                                        elseif ($grade->percentage >= 55) $letterGrade = 'E+';
                                                                        elseif ($grade->percentage >= 50) $letterGrade = 'E';
                                                                        else $letterGrade = 'F';
                                                                    @endphp
                                                                    <span class="badge badge-{{ $grade->percentage >= 90 ? 'success' : ($grade->percentage >= 80 ? 'info' : ($grade->percentage >= 70 ? 'warning' : 'danger')) }}">
                                                                        {{ $letterGrade }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if($grade->percentage < 75)
                                                                        <span class="badge badge-danger">Low Performance</span>
                                                                    @elseif($grade->percentage < 85)
                                                                        <span class="badge badge-warning">Needs Improvement</span>
                                                                    @else
                                                                        <span class="badge badge-success">Good Performance</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($studentId)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No performance data found for the selected student and criteria.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    @if($performanceData && $performanceData->count() > 0)
        // Subject Performance Chart
        const subjectCtx = document.getElementById('subjectPerformanceChart').getContext('2d');
        const subjectData = @json($performanceData->map(function($grades) { 
            return $grades->avg('percentage'); 
        }));
        const subjectLabels = @json($performanceData->keys()->map(function($subjectId) { 
            return \App\Models\Subject::find($subjectId)->subject_name; 
        }));

        new Chart(subjectCtx, {
            type: 'bar',
            data: {
                labels: subjectLabels,
                datasets: [{
                    label: 'Average Percentage',
                    data: subjectData,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Performance Trend Chart (if trend data available)
        @if($trendData && $trendData->count() > 0)
            const trendCtx = document.getElementById('performanceTrendChart').getContext('2d');
            const trendLabels = @json($trendData->keys());
            const trendValues = @json($trendData->map(function($semesterGrades) { 
                return $semesterGrades->flatten()->avg('percentage'); 
            }));

            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Performance Trend',
                        data: trendValues,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y.toFixed(1) + '%';
                                }
                            }
                        }
                    }
                }
            });
        @endif
    @endif
});
</script>
@endpush
@endsection 