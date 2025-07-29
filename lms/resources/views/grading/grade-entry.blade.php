@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Grade Entry</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Grade Entry</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="student-group-form">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="subject_id" name="subject_id">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="section_id" name="section_id">
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="academic_year_id" name="academic_year_id">
                                <option value="">Select Academic Year</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="search-student-btn">
                            <button type="button" class="btn btn-primary" id="loadStudents">Load Students</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="card card-table">
                        <div class="card-body">
                            <div class="page-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="page-title">Student Grades</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="#" class="btn btn-outline-primary me-2" id="exportGrades">
                                            <i class="fas fa-download"></i> Export
                                        </a>
                                        <button type="button" class="btn btn-primary" id="saveGrades">
                                            <i class="fas fa-save"></i> Save Grades
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div class="text-center py-5" id="emptyState">
                                <div class="mb-4">
                                    <i class="fas fa-users text-primary" style="font-size: 4rem; opacity: 0.6;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">Select Criteria to Load Students</h4>
                                <p class="text-muted mb-4">Choose a subject, section, and academic year above to begin grade entry.</p>
                                
                                <!-- Feature Highlights -->
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span class="text-dark fw-medium">Easy grade entry</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-chart-line text-primary me-2"></i>
                                            <span class="text-dark fw-medium">Performance tracking</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center justify-content-center mb-2">
                                            <i class="fas fa-download text-warning me-2"></i>
                                            <span class="text-dark fw-medium">Export reports</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Grade Component Selection -->
                            <div id="gradeComponentSection" style="display: none;">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Grade Component</label>
                                            <select class="form-control" id="component_id" name="component_id">
                                                <option value="">Select Component</option>
                                                @foreach($components as $component)
                                                    <option value="{{ $component->id }}">{{ $component->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">Max Score</label>
                                            <input type="number" class="form-control" id="max_score" name="max_score" value="100" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" id="saveGradesBtn">
                                                    <i class="fas fa-save me-1"></i> Save Grades
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" id="clearGrades">
                                                    <i class="fas fa-eraser me-1"></i> Clear All
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Students Table -->
                            <div id="studentsTableSection" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                        <thead class="student-thread">
                                            <tr>
                                                <th>
                                                    <div class="form-check check-tables">
                                                        <input class="form-check-input" type="checkbox" value="something" id="selectAll">
                                                    </div>
                                                </th>
                                                <th>Student ID</th>
                                                <th>Student Name</th>
                                                <th>Score</th>
                                                <th>Percentage</th>
                                                <th>Letter Grade</th>
                                                <th>Remarks</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="studentsTableBody">
                                            <!-- Students will be loaded here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
/* Admin-style form controls */
.student-group-form {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 31px 3px rgba(44,50,63,.02);
    margin-bottom: 20px;
}

.student-group-form .form-group {
    margin-bottom: 0;
}

.student-group-form .form-control {
    border: 1px solid #ddd;
    border-radius: 5px;
    height: 45px;
    padding: 10px 15px;
    font-size: 15px;
}

.student-group-form .form-control:focus {
    border-color: #3d5ee1;
    box-shadow: none;
    outline: 0;
}

.search-student-btn .btn {
    height: 45px;
    padding: 10px 20px;
    font-weight: 600;
}

/* Card styling */
.card-table {
    border: 0;
    border-radius: 10px;
    box-shadow: 0 0 31px 3px rgba(44,50,63,.02);
    margin-bottom: 1.875rem;
}

.card-table .card-body {
    padding: 1.5rem;
}

/* Table styling */
.table {
    color: #333;
    max-width: 100%;
    margin-bottom: 0;
    width: 100%;
}

.table thead th {
    vertical-align: bottom;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    color: #000;
    background-color: #f8f9fa;
    border-color: #eff2f7;
    padding: 15px;
}

.table tbody tr {
    border-bottom: 1px solid #dee2e6;
}

.table tbody td {
    padding: 15px;
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f7f7f7;
}

.table-hover tbody tr:hover td {
    color: #474648;
}

/* Form elements in table */
.table .form-control {
    border: 1px solid #ddd;
    border-radius: 5px;
    height: 40px;
    padding: 8px 12px;
    font-size: 14px;
}

.table .form-control:focus {
    border-color: #3d5ee1;
    box-shadow: none;
    outline: 0;
}

/* Buttons */
.btn {
    border-radius: 5px;
    font-weight: 600;
    transition: all .4s ease;
}

.btn-primary {
    background-color: #3d5ee1;
    border: 1px solid #3d5ee1;
}

.btn-primary:hover {
    background-color: #18aefa;
    border: 1px solid #18aefa;
}

.btn-success {
    background-color: #7bb13c;
    border: 1px solid #7bb13c;
}

.btn-success:hover {
    background-color: #699834;
    border: 1px solid #699834;
}

.btn-outline-primary {
    color: #3d5ee1;
    border-color: #3d5ee1;
}

.btn-outline-primary:hover {
    background-color: #18aefa;
    border-color: #18aefa;
    color: #fff;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

/* Actions */
.actions {
    display: flex;
    justify-content: end;
}

.actions a {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 5px;
}

.actions a:hover {
    background-color: #3d5ee1 !important;
    color: #fff !important;
}

/* Checkbox styling */
.form-check-input {
    width: 18px;
    height: 18px;
    margin-top: 0;
}

.form-check-input:checked {
    background-color: #3d5ee1;
    border-color: #3d5ee1;
}

/* Empty state styling */
#emptyState {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
    margin: 2rem 0;
}

/* Page header */
.page-header {
    margin-bottom: 1.875rem;
}

.page-header .breadcrumb {
    background-color: transparent;
    color: #6c757d;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0;
    padding: 0;
    margin-left: auto;
}

.page-header .breadcrumb a {
    color: #333;
}

.page-title {
    font-size: 22px;
    font-weight: 500;
    color: #2c323f;
    margin-bottom: 5px;
}

/* Download group */
.download-grp {
    display: flex;
    align-items: center;
}

/* Responsive */
@media (max-width: 768px) {
    .student-group-form {
        padding: 15px;
    }
    
    .card-table .card-body {
        padding: 1rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 10px 8px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Load Students functionality
    $('#loadStudents').on('click', function() {
        const subjectId = $('#subject_id').val();
        const sectionId = $('#section_id').val();
        const academicYearId = $('#academic_year_id').val();
        
        if (!subjectId || !sectionId || !academicYearId) {
            alert('Please select all criteria before loading students.');
            return;
        }
        
        // Show loading state
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...').prop('disabled', true);
        
        // Simulate loading students (replace with actual AJAX call)
        setTimeout(function() {
            $('#loadStudents').html('Load Students').prop('disabled', false);
            $('#emptyState').hide();
            $('#gradeComponentSection').show();
            $('#studentsTableSection').show();
            
            // Populate sample students
            populateStudentsTable();
        }, 1000);
    });
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        $('.student-checkbox').prop('checked', $(this).is(':checked'));
    });
    
    // Auto-calculate percentage and letter grade
    $(document).on('input', '.score-input', function() {
        const score = parseFloat($(this).val()) || 0;
        const maxScore = parseFloat($('#max_score').val()) || 100;
        const percentage = (score / maxScore) * 100;
        const letterGrade = getLetterGrade(percentage);
        
        const row = $(this).closest('tr');
        row.find('.percentage-display').text(percentage.toFixed(1) + '%');
        row.find('.letter-grade-display').text(letterGrade);
    });
    
    // Save grades
    $('#saveGrades, #saveGradesBtn').on('click', function() {
        // Implement grade saving logic here
        alert('Grades saved successfully!');
    });
    
    // Clear all grades
    $('#clearGrades').on('click', function() {
        if (confirm('Are you sure you want to clear all grades?')) {
            $('.score-input').val('');
            $('.percentage-display').text('0.0%');
            $('.letter-grade-display').text('F');
            $('.remarks-select').val('');
        }
    });
    
    // Export grades
    $('#exportGrades').on('click', function() {
        // Implement export functionality
        alert('Export functionality will be implemented here.');
    });
    
    // Helper function to get letter grade
    function getLetterGrade(percentage) {
        if (percentage >= 93) return 'A';
        if (percentage >= 90) return 'A-';
        if (percentage >= 87) return 'B+';
        if (percentage >= 83) return 'B';
        if (percentage >= 80) return 'B-';
        if (percentage >= 77) return 'C+';
        if (percentage >= 73) return 'C';
        if (percentage >= 70) return 'C-';
        if (percentage >= 67) return 'D+';
        if (percentage >= 63) return 'D';
        if (percentage >= 60) return 'D-';
        return 'F';
    }
    
    // Populate students table with sample data
    function populateStudentsTable() {
        const sampleStudents = [
            { id: 'STU001', name: 'John Doe', avatar: 'JD' },
            { id: 'STU002', name: 'Jane Smith', avatar: 'JS' },
            { id: 'STU003', name: 'Mike Johnson', avatar: 'MJ' },
            { id: 'STU004', name: 'Sarah Wilson', avatar: 'SW' },
            { id: 'STU005', name: 'David Brown', avatar: 'DB' }
        ];
        
        let tableBody = '';
        sampleStudents.forEach((student, index) => {
            tableBody += `
                <tr>
                    <td>
                        <div class="form-check check-tables">
                            <input class="form-check-input student-checkbox" type="checkbox" value="${student.id}">
                        </div>
                    </td>
                    <td>${student.id}</td>
                    <td>
                        <h2>
                            <a>${student.name}</a>
                        </h2>
                    </td>
                    <td>
                        <input type="number" class="form-control score-input" 
                               data-student="${index}" min="0" max="100" 
                               placeholder="Enter score" style="width: 100px;">
                    </td>
                    <td>
                        <span class="percentage-display">0.0%</span>
                    </td>
                    <td>
                        <span class="letter-grade-display">F</span>
                    </td>
                    <td>
                        <select class="form-control remarks-select" style="width: 120px;">
                            <option value="">Select</option>
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </td>
                    <td class="text-end">
                        <div class="actions">
                            <a href="#" class="btn btn-sm bg-danger-light">
                                <i class="far fa-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm bg-danger-light">
                                <i class="fe fe-trash-2"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        $('#studentsTableBody').html(tableBody);
    }
});
</script>
@endpush

@endsection 