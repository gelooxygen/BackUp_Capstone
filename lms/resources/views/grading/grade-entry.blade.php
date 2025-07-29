@extends('layouts.master')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="fas fa-edit me-2"></i>Grade Entry Form
                            </h4>
                            <p class="mb-0 opacity-75">Enter student grades for selected criteria</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('teacher.grading.gpa-ranking') }}">
                                    <i class="fas fa-chart-bar me-2"></i>View GPA Ranking
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('teacher.grading.weight-settings') }}">
                                    <i class="fas fa-cog me-2"></i>Weight Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('teacher.grading.grade-alerts') }}">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Grade Alerts
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                                                <div class="card-body p-3">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('teacher.grading.grade-entry') }}" class="mb-3">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="subject_id" class="form-label fw-semibold text-dark small">
                                        <i class="fas fa-book text-primary me-1"></i>Subject
                                    </label>
                                    <select name="subject_id" id="subject_id" class="form-select form-select-sm" required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ $selectedSubject == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="section_id" class="form-label fw-semibold text-dark small">
                                        <i class="fas fa-users text-primary me-1"></i>Section
                                    </label>
                                    <select name="section_id" id="section_id" class="form-select form-select-sm" required>
                                        <option value="">Select Section</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" {{ $selectedSection == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="academic_year_id" class="form-label fw-semibold text-dark small">
                                        <i class="fas fa-calendar-alt text-primary me-1"></i>Academic Year
                                    </label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-select form-select-sm" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="semester_id" class="form-label fw-semibold text-dark small">
                                        <i class="fas fa-clock text-primary me-1"></i>Semester
                                    </label>
                                    <select name="semester_id" id="semester_id" class="form-select form-select-sm" required>
                                        <option value="">Select Semester</option>
                                        @foreach($semesters as $semester)
                                            <option value="{{ $semester->id }}" {{ $selectedSemester == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                                                        <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm px-3 py-1">
                                                <i class="fas fa-search me-1"></i>Load Students
                                            </button>
                                            <button type="reset" class="btn btn-outline-secondary btn-sm px-3 py-1">
                                                <i class="fas fa-undo me-1"></i>Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>
                    </form>

                    <!-- Student List Section -->
                    @if($students->count() > 0)
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <strong>Success!</strong> Found {{ $students->count() }} student(s) for the selected criteria.
                            </div>
                        </div>

                        <!-- Grade Entry Form -->
                        <form method="POST" action="{{ route('teacher.grading.store-grades') }}" id="gradeForm">
                            @csrf
                            <input type="hidden" name="subject_id" value="{{ $selectedSubject }}">
                            <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYear }}">
                            <input type="hidden" name="semester_id" value="{{ $selectedSemester }}">

                                                                <div class="row g-3 mb-3">
                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                            <label for="component_id" class="form-label small">
                                                <i class="fas fa-layer-group me-1"></i>Component
                                            </label>
                                            <select name="component_id" id="component_id" class="form-select form-select-sm" required>
                                                <option value="">Select Component</option>
                                                @foreach($components as $component)
                                                    <option value="{{ $component->id }}" data-weight="{{ $component->weight }}">
                                                        {{ $component->name }} ({{ $component->weight }}%)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-3 col-sm-6">
                                            <label for="max_score" class="form-label small">
                                                <i class="fas fa-star me-1"></i>Max Score
                                            </label>
                                            <input type="number" name="max_score" id="max_score" class="form-control form-control-sm" value="100" min="1" required>
                                        </div>
                                        <div class="col-lg-6 col-md-3 col-sm-6 d-flex align-items-end">
                                            <div class="d-flex gap-2 w-100">
                                                <button type="submit" class="btn btn-success btn-sm px-3 py-2">
                                                    <i class="fas fa-save me-1"></i>Save Grades
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2" onclick="clearAllGrades()">
                                                    <i class="fas fa-eraser me-1"></i>Clear All
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-sm">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="8%">
                                                <i class="fas fa-id-card me-1"></i>ID
                                            </th>
                                            <th width="25%">
                                                <i class="fas fa-user me-1"></i>Student Name
                                            </th>
                                            <th width="12%">
                                                <i class="fas fa-chart-line me-1"></i>Score
                                            </th>
                                            <th width="12%">
                                                <i class="fas fa-percentage me-1"></i>%
                                            </th>
                                            <th width="12%">
                                                <i class="fas fa-award me-1"></i>Grade
                                            </th>
                                            <th width="31%">
                                                <i class="fas fa-comment me-1"></i>Remarks
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary small">{{ $student->admission_id ?? $student->id }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px;">
                                                            <span class="text-white fw-bold small">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold small">{{ $student->first_name }} {{ $student->last_name }}</div>
                                                            <small class="text-muted">{{ $student->email ?? 'No email' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="grades[{{ $loop->index }}][score]" 
                                                           class="form-control form-control-sm score-input" 
                                                           step="0.01" 
                                                           min="0" 
                                                           data-student="{{ $loop->index }}"
                                                           placeholder="0.00">
                                                    <input type="hidden" name="grades[{{ $loop->index }}][student_id]" value="{{ $student->id }}">
                                                    <input type="hidden" name="grades[{{ $loop->index }}][max_score]" value="100">
                                                </td>
                                                <td>
                                                    <span class="badge bg-info small percentage-display" data-student="{{ $loop->index }}">-</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success small letter-grade-display" data-student="{{ $loop->index }}">-</span>
                                                </td>
                                                <td>
                                                    <input type="text" 
                                                           name="grades[{{ $loop->index }}][remarks]" 
                                                           class="form-control form-control-sm" 
                                                           placeholder="Remarks">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted small">
                                    <span class="me-3">
                                        <i class="fas fa-users me-1"></i>{{ $students->count() }} Students
                                    </span>
                                    <span>
                                        <i class="fas fa-clock me-1"></i>{{ now()->format('M d, Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </form>
                    @elseif($selectedSubject && $selectedSection)
                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>No students found!</strong> There are no students enrolled for the selected criteria. 
                                Please check the enrollment status or contact the administrator.
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="bg-gradient-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 60px; height: 60px;">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                            <h5 class="text-dark mb-2 fw-bold">Select Criteria to Load Students</h5>
                            <p class="text-muted small">Choose a subject, section, academic year, and semester above to begin grade entry.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Responsive adjustments for sidebar toggle */
@media (max-width: 991.98px) {
    .container-fluid {
        padding-left: 15px !important;
        padding-right: 15px !important;
    }
}

/* Default state - when sidebar is expanded */
.container-fluid {
    padding-left: 2rem !important;
    padding-right: 2rem !important;
    margin-left: 250px !important; /* Expanded sidebar width */
    width: calc(100% - 250px) !important;
    transition: all 0.3s ease-in-out !important;
    position: relative !important;
    z-index: 1 !important;
}

/* When sidebar is collapsed - maximize content without overlapping */
.sidebar-collapsed .container-fluid {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    margin-left: 60px !important; /* Collapsed sidebar width */
    width: calc(100% - 60px) !important;
    transition: all 0.3s ease-in-out !important;
    position: relative !important;
    z-index: 1 !important;
}

/* Ensure form card maximizes when sidebar is collapsed */
.sidebar-collapsed .card {
    max-width: 100% !important;
    margin: 0 !important;
    transition: all 0.3s ease-in-out !important;
}

/* Sidebar transition styles */
.sidebar {
    transition: all 0.3s ease-in-out !important;
    position: fixed !important;
    left: 0 !important;
    top: 0 !important;
    height: 100vh !important;
    z-index: 1000 !important;
    overflow: hidden !important;
}

/* Ensure smooth transitions for all elements */
.card, .container-fluid, .sidebar {
    transition: all 0.3s ease-in-out !important;
}

/* Prevent any overlapping */
.container-fluid {
    box-sizing: border-box !important;
}

/* Ensure form respects sidebar space */
.card {
    box-sizing: border-box !important;
    overflow: hidden !important;
}

/* Ensure form elements are responsive */
.form-select, .form-control {
    min-height: 38px;
}

/* Responsive table adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 0.5rem 0.25rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}

/* Ensure proper spacing when sidebar is collapsed */
.sidebar-collapsed .container-fluid {
    margin-left: 0 !important;
    width: 100% !important;
}

/* Ensure form card has proper default styling */
.card {
    margin: 0 auto;
    max-width: 1200px;
    transition: all 0.3s ease;
}

/* Responsive adjustments for different screen sizes */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 10px !important;
        padding-right: 10px !important;
    }
}

/* Responsive grid adjustments */
@media (max-width: 576px) {
    .row.g-3 > .col-md-6 {
        margin-bottom: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .d-flex.justify-content-between > div {
        text-align: center;
    }
}

/* Ensure form maintains proper width */
.card {
    border-radius: 0.75rem;
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 0 !important;
    padding: 1.5rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.bg-gradient-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
}

/* Form improvements */
.form-group {
    margin-bottom: 0;
}

.form-select-sm, .form-control-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
    border: 1px solid #dee2e6;
    transition: all 0.3s ease;
}

.form-select-sm:focus, .form-control-sm:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-label {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

/* Compact table styles */
.table-sm td, .table-sm th {
    padding: 0.5rem 0.25rem;
    font-size: 0.875rem;
}

/* Compact button styles */
.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.375rem;
}

/* Compact card styles */
.card-body {
    padding: 1rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-body {
        padding: 0.75rem;
    }
    
    .table-sm td, .table-sm th {
        padding: 0.25rem 0.125rem;
        font-size: 0.8rem;
    }
}

/* Responsive button adjustments */
@media (max-width: 480px) {
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn + .btn {
        margin-left: 0 !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle sidebar toggle for better responsiveness
    function adjustLayoutForSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.container-fluid');
        const formCard = document.querySelector('.card');
        
        if (sidebar && mainContent) {
            // Get current sidebar width to prevent overlapping
            const sidebarWidth = sidebar.offsetWidth || 250;
            const isCollapsed = sidebar.classList.contains('collapsed') || 
                               sidebar.classList.contains('sidebar-collapsed') ||
                               window.innerWidth < 992;
            
            if (isCollapsed) {
                // Sidebar is collapsed - maximize content without overlapping
                const collapsedSidebarWidth = 60; // Collapsed sidebar width
                
                mainContent.style.transition = 'all 0.3s ease-in-out';
                mainContent.style.marginLeft = collapsedSidebarWidth + 'px';
                mainContent.style.width = `calc(100% - ${collapsedSidebarWidth}px)`;
                mainContent.style.paddingLeft = '1rem';
                mainContent.style.paddingRight = '1rem';
                document.body.classList.add('sidebar-collapsed');
                
                // Maximize form card within available space
                if (formCard) {
                    formCard.style.transition = 'all 0.3s ease-in-out';
                    formCard.style.maxWidth = '100%';
                    formCard.style.margin = '0';
                    formCard.style.width = '100%';
                }
            } else {
                // Sidebar is expanded - normal layout without overlapping
                const expandedSidebarWidth = 250; // Expanded sidebar width
                
                mainContent.style.transition = 'all 0.3s ease-in-out';
                mainContent.style.marginLeft = expandedSidebarWidth + 'px';
                mainContent.style.width = `calc(100% - ${expandedSidebarWidth}px)`;
                mainContent.style.paddingLeft = '2rem';
                mainContent.style.paddingRight = '2rem';
                document.body.classList.remove('sidebar-collapsed');
                
                // Reset form card to normal size within available space
                if (formCard) {
                    formCard.style.transition = 'all 0.3s ease-in-out';
                    formCard.style.maxWidth = '1200px';
                    formCard.style.margin = '0 auto';
                    formCard.style.width = 'auto';
                }
            }
        }
    }
    
    // Listen for sidebar toggle events with multiple selectors
    $(document).on('click', '[data-toggle="sidebar"], .sidebar-toggle, .burger-menu, .navbar-toggler, .btn-sidebar', function() {
        setTimeout(adjustLayoutForSidebar, 50);
    });
    
    // Listen for sidebar state changes
    $(document).on('sidebar.toggle', function() {
        setTimeout(adjustLayoutForSidebar, 50);
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        setTimeout(adjustLayoutForSidebar, 100);
    });
    
    // Initial adjustment
    setTimeout(adjustLayoutForSidebar, 200);
    
    // Monitor for sidebar class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (mutation.target.classList.contains('sidebar')) {
                    setTimeout(adjustLayoutForSidebar, 50);
                }
            }
        });
    });
    
    // Start observing sidebar for class changes
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        observer.observe(sidebar, { attributes: true });
    }
    
    // Auto-calculate percentage and letter grade
    // Auto-calculate percentage and letter grade
    $('.score-input').on('input', function() {
        const studentIndex = $(this).data('student');
        const score = parseFloat($(this).val()) || 0;
        const maxScore = parseFloat($('#max_score').val()) || 100;
        
        if (score > 0 && maxScore > 0) {
            const percentage = (score / maxScore) * 100;
            const letterGrade = getLetterGrade(percentage);
            const gradeClass = getGradeClass(percentage);
            
            $(`.percentage-display[data-student="${studentIndex}"]`).text(percentage.toFixed(2) + '%');
            $(`.letter-grade-display[data-student="${studentIndex}"]`).text(letterGrade).removeClass().addClass(`badge ${gradeClass}`);
        } else {
            $(`.percentage-display[data-student="${studentIndex}"]`).text('-');
            $(`.letter-grade-display[data-student="${studentIndex}"]`).text('-').removeClass().addClass('badge bg-secondary');
        }
    });

    // Update max score for all students
    $('#max_score').on('input', function() {
        $('.score-input').trigger('input');
    });

    // Form validation
    $('#gradeForm').on('submit', function(e) {
        const componentId = $('#component_id').val();
        if (!componentId) {
            e.preventDefault();
            alert('Please select a component before saving grades.');
            $('#component_id').focus();
            return false;
        }
        
        const filledGrades = $('.score-input').filter(function() {
            return $(this).val() !== '';
        }).length;
        
        if (filledGrades === 0) {
            e.preventDefault();
            alert('Please enter at least one grade before saving.');
            return false;
        }
    });

    function getLetterGrade(percentage) {
        if (percentage >= 90) return 'A';
        if (percentage >= 85) return 'B+';
        if (percentage >= 80) return 'B';
        if (percentage >= 75) return 'C+';
        if (percentage >= 70) return 'C';
        if (percentage >= 65) return 'D+';
        if (percentage >= 60) return 'D';
        if (percentage >= 55) return 'E+';
        if (percentage >= 50) return 'E';
        return 'F';
    }

    function getGradeClass(percentage) {
        if (percentage >= 90) return 'bg-success';
        if (percentage >= 80) return 'bg-info';
        if (percentage >= 70) return 'bg-warning';
        if (percentage >= 60) return 'bg-orange';
        return 'bg-danger';
    }
});

function clearAllGrades() {
    if (confirm('Are you sure you want to clear all entered grades?')) {
        $('.score-input').val('');
        $('.percentage-display').text('-');
        $('.letter-grade-display').text('-').removeClass().addClass('badge bg-secondary');
    }
}
</script>
@endpush
@endsection 