@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Bulk Enrollment: Assign Students to Subject</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('enrollments.index') }}">Enrollments</a></li>
                        <li class="breadcrumb-item active">Bulk Enrollment</li>
                    </ul>
                </div>
                <div class="col-auto text-right float-right ml-auto">
                    <a href="{{ route('bulk-enrollment.assign-subjects-to-student') }}" class="btn btn-info">
                        <i class="fas fa-exchange-alt"></i> Assign Subjects to Student
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Assign Multiple Students to One Subject</h5>
                        <p class="card-text text-muted">Select a subject and academic period, then choose the students to enroll.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('bulk-enrollment.assign-students-to-subject') }}" id="bulkEnrollmentForm">
                            @csrf
                            
                            <div class="row">
                                <!-- Subject Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subject_id">Subject <span class="text-danger">*</span></label>
                                        <select class="form-control @error('subject_id') is-invalid @enderror" name="subject_id" id="subject_id" required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->subject_name }} ({{ $subject->class }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('subject_id')
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

                            <!-- Student Selection -->
                            <div class="form-group">
                                <label>Select Students <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="studentSearch" placeholder="Search students by name, admission ID, or email...">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="searchStudents">
                                                    <i class="fas fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>Available Students</h6>
                                            <div>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllStudents()">Select All</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllStudents()">Deselect All</button>
                                            </div>
                                        </div>
                                        
                                        <div id="studentsList" class="row">
                                            <!-- Students will be loaded here via AJAX -->
                                        </div>
                                        
                                        @error('student_ids')
                                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <a href="{{ route('enrollments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Enroll Selected Students
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
    // Search students
    $('#searchStudents').click(function() {
        searchStudents();
    });

    // Search on Enter key
    $('#studentSearch').keypress(function(e) {
        if (e.which == 13) {
            searchStudents();
        }
    });

    // Initial load of students
    searchStudents();

    // Form validation
    $('#bulkEnrollmentForm').submit(function(e) {
        var selectedStudents = $('input[name="student_ids[]"]:checked').length;
        if (selectedStudents === 0) {
            e.preventDefault();
            alert('Please select at least one student to enroll.');
            return false;
        }
    });
});

function searchStudents() {
    var searchTerm = $('#studentSearch').val();
    
    $.ajax({
        url: '{{ route("bulk-enrollment.get-students") }}',
        method: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            displayStudents(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching students:', error);
            console.error('Status:', status);
            console.error('Response:', xhr.responseText);
            
            var errorMessage = 'Error loading students. ';
            if (xhr.status === 500) {
                errorMessage += 'Server error. Check console for details.';
            } else if (xhr.status === 404) {
                errorMessage += 'Route not found.';
            } else if (xhr.status === 403) {
                errorMessage += 'Access denied. Please check your permissions.';
            } else {
                errorMessage += 'Please try again.';
            }
            
            $('#studentsList').html('<div class="col-12"><div class="alert alert-danger">' + errorMessage + '</div></div>');
        }
    });
}

function displayStudents(students) {
    var html = '';
    
    if (students.length === 0) {
        html = '<div class="col-12"><div class="alert alert-info">No students found matching your search criteria.</div></div>';
    } else {
        students.forEach(function(student) {
            var fullName = (student.first_name + ' ' + student.last_name).trim();
            if (!fullName) fullName = student.user ? student.user.name : 'Unknown Name';
            
            html += `
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="student-checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input student-checkbox" type="checkbox" 
                                   name="student_ids[]" value="${student.id}" 
                                   id="student${student.id}">
                            <label class="form-check-label" for="student${student.id}">
                                <div class="student-info">
                                    <strong>${fullName}</strong>
                                    ${student.admission_id ? '<br><small class="text-muted">ID: ' + student.admission_id + '</small>' : ''}
                                    ${student.user && student.user.email ? '<br><small class="text-muted">' + student.user.email + '</small>' : ''}
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#studentsList').html(html);
}

function selectAllStudents() {
    $('.student-checkbox').prop('checked', true);
}

function deselectAllStudents() {
    $('.student-checkbox').prop('checked', false);
}
</script>

<style>
.student-checkbox-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.student-checkbox-card:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.student-checkbox-card .form-check {
    margin: 0;
}

.student-checkbox-card .form-check-label {
    cursor: pointer;
    width: 100%;
    margin: 0;
}

.student-info {
    margin-left: 10px;
}

.student-info strong {
    color: #495057;
    font-size: 14px;
}

.student-info small {
    font-size: 12px;
    color: #6c757d;
}
</style>
@endsection
