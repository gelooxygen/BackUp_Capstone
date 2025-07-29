@extends('layouts.app')

@section('title', 'GPA Ranking')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">GPA Ranking & Analysis</h4>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('teacher.grading.gpa-ranking') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="academic_year_id">Academic Year</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control">
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
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
                                            <option value="{{ $semester->id }}" {{ $selectedSemester == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="section_id">Section (Optional)</label>
                                    <select name="section_id" id="section_id" class="form-control">
                                        <option value="">All Sections</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ $selectedSection == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
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
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('teacher.grading.grade-entry') }}" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Enter Grades
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Statistics Cards -->
                    @if($gpaRecords->count() > 0)
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Students</h5>
                                        <h3>{{ $gpaRecords->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Average GPA</h5>
                                        <h3>{{ number_format($gpaRecords->avg('gpa'), 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Highest GPA</h5>
                                        <h3>{{ number_format($gpaRecords->max('gpa'), 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Lowest GPA</h5>
                                        <h3>{{ number_format($gpaRecords->min('gpa'), 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-success dropdown-toggle" data-toggle="dropdown">
                                        <i class="fas fa-download"></i> Export GPA Report
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('teacher.grading.export-gpa', ['format' => 'excel', 'academic_year_id' => $selectedAcademicYear, 'semester_id' => $selectedSemester, 'section_id' => $selectedSection]) }}">
                                            <i class="fas fa-file-excel"></i> Export to Excel
                                        </a>
                                        <a class="dropdown-item" href="{{ route('teacher.grading.export-gpa', ['format' => 'pdf', 'academic_year_id' => $selectedAcademicYear, 'semester_id' => $selectedSemester, 'section_id' => $selectedSection]) }}">
                                            <i class="fas fa-file-pdf"></i> Export to PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- GPA Ranking Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>GPA</th>
                                        <th>Letter Grade</th>
                                        <th>Grade Description</th>
                                        <th>Total Units</th>
                                        <th>Total Grade Points</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gpaRecords as $record)
                                        <tr class="{{ $record->gpa < 2.0 ? 'table-danger' : ($record->gpa < 2.5 ? 'table-warning' : '') }}">
                                            <td>
                                                @if($record->rank == 1)
                                                    <span class="badge badge-warning">🥇 1st</span>
                                                @elseif($record->rank == 2)
                                                    <span class="badge badge-secondary">🥈 2nd</span>
                                                @elseif($record->rank == 3)
                                                    <span class="badge badge-info">🥉 3rd</span>
                                                @else
                                                    <span class="badge badge-light">{{ $record->rank }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $record->student->admission_id ?? $record->student->id }}</td>
                                            <td>{{ $record->student->first_name }} {{ $record->student->last_name }}</td>
                                            <td>
                                                <strong>{{ number_format($record->gpa, 2) }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $record->gpa >= 3.5 ? 'success' : ($record->gpa >= 3.0 ? 'info' : ($record->gpa >= 2.5 ? 'warning' : 'danger')) }}">
                                                    {{ $record->letter_grade }}
                                                </span>
                                            </td>
                                            <td>{{ $record->grade_description }}</td>
                                            <td>{{ $record->total_units }}</td>
                                            <td>{{ $record->total_grade_points }}</td>
                                            <td>
                                                <a href="{{ route('teacher.grading.performance-analytics', ['student_id' => $record->student->id, 'academic_year_id' => $selectedAcademicYear, 'semester_id' => $selectedSemester]) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-chart-line"></i> Analytics
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- GPA Distribution Chart -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">GPA Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="gpaDistributionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Grade Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="gradeDistributionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No GPA records found for the selected criteria.
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
    @if($gpaRecords->count() > 0)
        // GPA Distribution Chart
        const gpaCtx = document.getElementById('gpaDistributionChart').getContext('2d');
        const gpaData = @json($gpaRecords->pluck('gpa'));
        
        const gpaRanges = {
            '4.0-3.5': gpaData.filter(gpa => gpa >= 3.5).length,
            '3.49-3.0': gpaData.filter(gpa => gpa >= 3.0 && gpa < 3.5).length,
            '2.99-2.5': gpaData.filter(gpa => gpa >= 2.5 && gpa < 3.0).length,
            '2.49-2.0': gpaData.filter(gpa => gpa >= 2.0 && gpa < 2.5).length,
            'Below 2.0': gpaData.filter(gpa => gpa < 2.0).length,
        };

        new Chart(gpaCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(gpaRanges),
                datasets: [{
                    label: 'Number of Students',
                    data: Object.values(gpaRanges),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(108, 117, 125, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Grade Distribution Chart
        const gradeCtx = document.getElementById('gradeDistributionChart').getContext('2d');
        const letterGrades = @json($gpaRecords->pluck('letter_grade'));
        
        const gradeCounts = {};
        letterGrades.forEach(grade => {
            gradeCounts[grade] = (gradeCounts[grade] || 0) + 1;
        });

        new Chart(gradeCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(gradeCounts),
                datasets: [{
                    data: Object.values(gradeCounts),
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    @endif
});
</script>
@endpush
@endsection 