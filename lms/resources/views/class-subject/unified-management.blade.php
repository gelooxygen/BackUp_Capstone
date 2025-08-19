@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Class & Subject Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Class & Subject Management</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title">Unified Management Operations</h5>
                                <p class="card-text text-muted">Choose your operation type and configure the settings below.</p>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="{{ route('sections.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Section
                                </a>
                                <a href="{{ route('subject/list/page') }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus"></i> Add Subject
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Operation Type Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Operation Type <span class="text-danger">*</span></label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check operation-type-option">
                                                <input class="form-check-input" type="radio" name="operation_type" id="type_teacher_subject" value="teacher_subject" checked>
                                                <label class="form-check-label" for="type_teacher_subject">
                                                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                                                    <strong>Assign Teacher to Subject</strong>
                                                    <small class="d-block text-muted">Link teachers to specific subjects</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check operation-type-option">
                                                <input class="form-check-input" type="radio" name="operation_type" id="type_student_section" value="student_section">
                                                <label class="form-check-label" for="type_student_section">
                                                    <i class="fas fa-users text-success"></i>
                                                    <strong>Assign Students to Section</strong>
                                                    <small class="d-block text-muted">Place students in specific sections</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check operation-type-option">
                                                <input class="form-check-input" type="radio" name="operation_type" id="type_students_to_subject" value="students_to_subject">
                                                <label class="form-check-label" for="type_students_to_subject">
                                                    <i class="fas fa-user-graduate text-info"></i>
                                                    <strong>Enroll Students to Subject</strong>
                                                    <small class="d-block text-muted">Enroll many students in one subject</small>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check operation-type-option">
                                                <input class="form-check-input" type="radio" name="operation_type" id="type_subjects_to_student" value="subjects_to_student">
                                                <label class="form-check-label" for="type_subjects_to_student">
                                                    <i class="fas fa-book-open text-warning"></i>
                                                    <strong>Enroll Student to Subjects</strong>
                                                    <small class="d-block text-muted">Enroll one student in multiple subjects</small>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Quick Setup Information -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div>
                                    <strong>Quick Setup:</strong> Need to create new sections or subjects first? Use the buttons above to add them, then come back here to assign teachers and students.
                                </div>
                            </div>
                        </div>

                        <!-- Data Integrity Warning -->
                        @php
                            $studentsWithoutUser = $students->filter(function($student) {
                                return !$student->user_id || !$student->user;
                            });
                        @endphp
                        @if($studentsWithoutUser->count() > 0)
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <div>
                                        <strong>Data Integrity Notice:</strong> 
                                        {{ $studentsWithoutUser->count() }} student(s) do not have associated user accounts. 
                                        These students will be displayed as "Unknown Name" but can still be enrolled in subjects. 
                                        To fix this, ensure all students have corresponding user accounts in the User Management section.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Dynamic Form Content -->
                        <div id="dynamicFormContent">
                            <!-- Content will be loaded here based on operation type -->
                        </div>
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
    // Initial load
    loadFormContent();
    
    // Listen for operation type changes
    $('input[name="operation_type"]').change(function() {
        loadFormContent();
    });
});

function loadFormContent() {
    const operationType = $('input[name="operation_type"]:checked').val();
    
    switch(operationType) {
        case 'teacher_subject':
            loadTeacherSubjectForm();
            break;
        case 'student_section':
            loadStudentSectionForm();
            break;
        case 'students_to_subject':
            loadStudentsToSubjectForm();
            break;
        case 'subjects_to_student':
            loadSubjectsToStudentForm();
            break;
    }
}

function loadTeacherSubjectForm() {
    const formHtml = `
        <form method="POST" action="#" id="unifiedForm">
            @csrf
            <input type="hidden" name="operation_type" value="teacher_subject">
            <input type="hidden" name="subject_id_for_route" id="subject_id_for_route">
            
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

            <!-- Teacher Selection -->
            <div class="form-group">
                <label>Select Teachers <span class="text-danger">*</span></label>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6>Available Teachers</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllTeachers()">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllTeachers()">Deselect All</button>
                            </div>
                        </div>
                        
                        <div id="teachersList" class="row">
                            <!-- Teachers will be loaded here -->
                                                         @foreach($teachers as $teacher)
                                 @php
                                     $fullName = $teacher->full_name ?: ($teacher->user ? $teacher->user->name : 'Unknown Teacher');
                                     // Ensure we have a valid name
                                     $displayName = $fullName ?: 'Unknown Teacher';
                                 @endphp
                                 <div class="col-md-4 col-lg-3 mb-3">
                                     <div class="teacher-checkbox-card">
                                         <div class="form-check">
                                             <input class="form-check-input teacher-checkbox" type="checkbox" 
                                                    name="teacher_ids[]" value="{{ $teacher->id }}" 
                                                    id="teacher{{ $teacher->id }}">
                                             <label class="form-check-label" for="teacher{{ $teacher->id }}">
                                                 <div class="teacher-info">
                                                     <strong>{{ $displayName }}</strong>
                                                     @if($teacher->phone_number)
                                                         <br><small class="text-muted">{{ $teacher->phone_number }}</small>
                                                     @endif
                                                 </div>
                                             </label>
                                         </div>
                                     </div>
                                 </div>
                             @endforeach
                        </div>
                        
                        @error('teacher_ids')
                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Assign Teachers to Subject
                </button>
            </div>
        </form>
    `;
    
    $('#dynamicFormContent').html(formHtml);
}

function loadStudentSectionForm() {
    const formHtml = `
        <form method="POST" action="{{ route('class-subject.assign-students-to-section') }}" id="unifiedForm">
            @csrf
            <input type="hidden" name="operation_type" value="student_section">
            
            <div class="row">
                <!-- Section Selection -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="section_id">Section <span class="text-danger">*</span></label>
                        <select class="form-control @error('section_id') is-invalid @enderror" name="section_id" id="section_id" required>
                            <option value="">Select Section</option>
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
                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Assign Students to Section
                </button>
            </div>
        </form>
    `;
    
    $('#dynamicFormContent').html(formHtml);
    
    // Load students after form is created
    setTimeout(function() {
        searchStudents();
    }, 100);
}

function loadStudentsToSubjectForm() {
    const formHtml = `
        <form method="POST" action="{{ route('bulk-enrollment.assign-students-to-subject') }}" id="unifiedForm">
            @csrf
            <input type="hidden" name="operation_type" value="students_to_subject">
            
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
                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Enroll Selected Students
                </button>
            </div>
        </form>
    `;
    
    $('#dynamicFormContent').html(formHtml);
    
    // Load students after form is created
    setTimeout(function() {
        searchStudents();
    }, 100);
}

function loadSubjectsToStudentForm() {
    const formHtml = `
        <form method="POST" action="{{ route('bulk-enrollment.assign-subjects-to-student') }}" id="unifiedForm">
            @csrf
            <input type="hidden" name="operation_type" value="subjects_to_student">
            
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
                                    // Ensure we have a valid name
                                    $displayName = $fullName ?: 'Unknown Name';
                                @endphp
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $displayName }}
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
                <a href="{{ route('dashboard') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> Enroll Student in Selected Subjects
                </button>
            </div>
        </form>
    `;
    
    $('#dynamicFormContent').html(formHtml);
    
    // Load subjects after form is created
    setTimeout(function() {
        searchSubjects();
    }, 100);
}

// Teacher functions
function selectAllTeachers() {
    $('.teacher-checkbox').prop('checked', true);
}

function deselectAllTeachers() {
    $('.teacher-checkbox').prop('checked', false);
}

// Student search functions
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
            $('#studentsList').html('<div class="col-12"><div class="alert alert-danger">Error loading students. Please try again.</div></div>');
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
            if (!fullName) {
                fullName = student.user && student.user.name ? student.user.name : 'Unknown Name';
            }
            
            // Ensure we have a valid name
            var displayName = fullName || 'Unknown Name';
            
            html += `
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="student-checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input student-checkbox" type="checkbox" 
                                   name="student_ids[]" value="${student.id}" 
                                   id="student${student.id}">
                            <label class="form-check-label" for="student${student.id}">
                                <div class="student-info">
                                    <strong>${displayName}</strong>
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

// Subject search functions
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

// Form validation and action setting
$(document).on('submit', '#unifiedForm', function(e) {
    const operationType = $('input[name="operation_type"]:checked').val();
    
    // Set form action based on operation type
    switch(operationType) {
        case 'teacher_subject':
            var selectedTeachers = $('input[name="teacher_ids[]"]:checked').length;
            if (selectedTeachers === 0) {
                e.preventDefault();
                alert('Please select at least one teacher to assign.');
                return false;
            }
            
            // Set the form action for teacher assignment
            var subjectId = $('#subject_id').val();
            if (subjectId) {
                var actionUrl = '{{ route("subjects.assignTeachers", ":subject_id") }}'.replace(':subject_id', subjectId);
                $(this).attr('action', actionUrl);
            }
            break;
            
        case 'student_section':
        case 'students_to_subject':
            var selectedStudents = $('input[name="student_ids[]"]:checked').length;
            if (selectedStudents === 0) {
                e.preventDefault();
                alert('Please select at least one student.');
                return false;
            }
            break;
            
        case 'subjects_to_student':
            var selectedSubjects = $('input[name="subject_ids[]"]:checked').length;
            if (selectedSubjects === 0) {
                e.preventDefault();
                alert('Please select at least one subject to enroll the student in.');
                return false;
            }
            break;
    }
});
</script>

<style>
.operation-type-option {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    height: 100%;
}

.operation-type-option:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.operation-type-option input[type="radio"]:checked + label {
    color: #007bff;
}

.operation-type-option input[type="radio"]:checked {
    accent-color: #007bff;
}

.teacher-checkbox-card,
.student-checkbox-card,
.subject-checkbox-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.teacher-checkbox-card:hover,
.student-checkbox-card:hover,
.subject-checkbox-card:hover {
    background: #e9ecef;
    border-color: #007bff;
}

.teacher-checkbox-card .form-check,
.student-checkbox-card .form-check,
.subject-checkbox-card .form-check {
    margin: 0;
}

.teacher-checkbox-card .form-check-label,
.student-checkbox-card .form-check-label,
.subject-checkbox-card .form-check-label {
    cursor: pointer;
    width: 100%;
    margin: 0;
}

.teacher-info,
.student-info,
.subject-info {
    margin-left: 10px;
}

.teacher-info strong,
.student-info strong,
.subject-info strong {
    color: #495057;
    font-size: 14px;
}

.teacher-info small,
.student-info small,
.subject-info small {
    font-size: 12px;
    color: #6c757d;
}

/* Button styling for the header buttons */
.btn-group .btn {
    margin-left: 5px;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

.btn-outline-primary:hover,
.btn-outline-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Info alert styling */
.alert-info {
    border-left: 4px solid #17a2b8;
}

.alert-info i {
    color: #17a2b8;
}

/* Warning alert styling */
.alert-warning {
    border-left: 4px solid #ffc107;
}

.alert-warning i {
    color: #ffc107;
}
</style>
@endsection
