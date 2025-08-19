@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Bulk Enrollment: Assign Subjects to Student</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Enrollments</a></li>
                        <li class="breadcrumb-item active">Bulk Enrollment</li>
                    </ul>
                </div>
                <div class="col-auto text-right float-right ml-auto">
                    <a href="{{ route('bulk-enrollment.assign-students-to-subject') }}" class="btn btn-info">
                        <i class="fas fa-users"></i> Assign Students to Subject
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Assign Multiple Subjects to One Student</h5>
                        <p class="card-text text-muted">Select a student and academic period, then choose the subjects to enroll them in.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('bulk-enrollment.assign-subjects-to-student') }}" id="bulkEnrollmentForm">
                            @csrf
                            
                            <div class="row">
                                <!-- Student Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="student_id">Student <span class="text-danger">*</span></label>
                                        <select class="form-control @error('student_id') is-invalid @enderror" name="student_id" id="student_id" required>
                                            <option value="">Select Student</option>
                                            @foreach($students as $student)
                                                @php
                                                    $fullName = trim($student->first_name . ' ' . $student->last_name);
                                                    if (!$fullName && $student->user) {
                                                        $fullName = $student->user->name;
                                                    }
                                                @endphp
                                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                    {{ $fullName ?: 'Unknown Name' }}
                                                    @if($student->admission_id)
                                                        ({{ $student->admission_id }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Academic Year -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="academic_year_id">Academic Year <span class="text-danger">*</span></label>
                                        <select class="form-control @error('academic_year_id') is-invalid @enderror" name="academic_year_id" id="academic_year_id" required>
                                            <option value="">Select Academic Year</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_year_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Semester -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="semester_id">Semester <span class="text-danger">*</span></label>
                                        <select class="form-control @error('semester_id') is-invalid @enderror" name="semester_id" id="semester_id" required>
                                            <option value="">Select Semester</option>
                                            @foreach($semesters as $semester)
                                                <option value="{{ $semester->id }}" {{ old('semester_id') == $semester->id ? 'selected' : '' }}>
                                                    {{ $semester->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('semester_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Section (Optional) -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="section_id">Section (Optional)</label>
                                        <select class="form-control @error('section_id') is-invalid @enderror" name="section_id" id="section_id">
                                            <option value="">Select Section (Optional)</option>
                                            @foreach($sections as $section)
                                                <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('section_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Subject Selection -->
                            <div class="form-group">
                                <label>Select Subjects <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="subjectSearch" placeholder="Search subjects by name or class...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchSubjects">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>Available Subjects</h6>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllSubjects()">Select All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllSubjects()">Deselect All</button>
                                            </div>
                                        </div>
                                        
                                        <div id="subjectsList" class="row">
                                            <!-- Subjects will be loaded here via AJAX -->
                                        </div>
                                        
                                        @error('subject_ids')
                                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <a href="{{ route('enrollments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Enroll Student in Selected Subjects
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    // Search subjects
    $('#searchSubjects').click(function() {
        searchSubjects();
    });

    // Search on Enter key
    $('#subjectSearch').keypress(function(e) {
        if (e.which == 13) {
            searchSubjects();
        }
    });

    // Initial load of subjects
    searchSubjects();

    // Form validation
    $('#bulkEnrollmentForm').submit(function(e) {
        var selectedSubjects = $('input[name="subject_ids[]"]:checked').length;
        if (selectedSubjects === 0) {
            e.preventDefault();
            alert('Please select at least one subject to enroll the student in.');
            return false;
        }
    });
});

function searchSubjects() {
    var searchTerm = $('#subjectSearch').val();
    
    $.ajax({
        url: '{{ route("bulk-enrollment.get-subjects") }}',
        method: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            displaySubjects(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching subjects:', error);
            $('#subjectsList').html('<div class="col-12"><div class="alert alert-danger">Error loading subjects. Please try again.</div></div>');
        }
    });
}

function displaySubjects(subjects) {
    var html = '';
    
    if (subjects.length === 0) {
        html = '<div class="col-12"><div class="alert alert-info">No subjects found matching your search criteria.</div></div>';
    } else {
        subjects.forEach(function(subject) {
            html += `
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="subject-checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input subject-checkbox" type="checkbox" 
                                   name="subject_ids[]" value="${subject.id}" 
                                   id="subject${subject.id}">
                            <label class="form-check-label" for="subject${subject.id}">
                                <div class="subject-info">
                                    <strong>${subject.subject_name}</strong>
                                    ${subject.class ? '<br><small class="text-muted">Class: ' + subject.class + '</small>' : ''}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#subjectsList').html(html);
}

function selectAllSubjects() {
    $('.subject-checkbox').prop('checked', true);
}

function deselectAllSubjects() {
    $('.subject-checkbox').prop('checked', false);
}
</script>

<style>
.subject-checkbox-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.subject-checkbox-card:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.subject-checkbox-card .form-check {
    margin: 0;
}

.subject-checkbox-card .form-check-label {
    cursor: pointer;
    width: 100%;
    margin: 0;
}

.subject-info {
    margin-left: 10px;
}

.subject-info strong {
    color: #495057;
    font-size: 14px;
}

.subject-info small {
    font-size: 12px;
    color: #6c757d;
}
</style>
@endsection
