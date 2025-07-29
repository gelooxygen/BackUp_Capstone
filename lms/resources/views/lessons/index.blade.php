@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">

            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="page-title">Lesson Planner</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Lesson Planner</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="student-group-form">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" id="searchLesson" placeholder="Search lessons...">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="subject_filter">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group">
                            <select class="form-control" id="status_filter">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="search-student-btn">
                            <button type="button" class="btn btn-primary" id="filterLessons">Filter</button>
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
                                        <h3 class="page-title">Lesson Management</h3>
                                    </div>
                                    <div class="col-auto text-end float-end ms-auto download-grp">
                                        <a href="{{ route('lessons.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Create Lesson
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Lesson Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-lg-3 col-md-6">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h4 class="mb-0" id="totalLessons">{{ $lessons->total() }}</h4>
                                                    <p class="mb-0">Total Lessons</p>
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
                                                    <h4 class="mb-0" id="publishedLessons">{{ $lessons->where('status', 'published')->count() }}</h4>
                                                    <p class="mb-0">Published</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-check-circle fa-2x"></i>
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
                                                    <h4 class="mb-0" id="draftLessons">{{ $lessons->where('status', 'draft')->count() }}</h4>
                                                    <p class="mb-0">Draft</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-edit fa-2x"></i>
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
                                                    <h4 class="mb-0" id="completedLessons">{{ $lessons->where('status', 'completed')->count() }}</h4>
                                                    <p class="mb-0">Completed</p>
                                                </div>
                                                <div class="align-self-center">
                                                    <i class="fas fa-flag-checkered fa-2x"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                                    <thead class="student-thread">
                                        <tr>
                                            <th>
                                                <div class="form-check check-tables">
                                                    <input class="form-check-input" type="checkbox" value="something" id="selectAllLessons">
                                                </div>
                                            </th>
                                            <th>Lesson Title</th>
                                            <th>Subject</th>
                                            <th>Section</th>
                                            <th>Curriculum Objective</th>
                                            <th>Lesson Date</th>
                                            <th>Status</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lessonTableBody">
                                        @forelse($lessons as $lesson)
                                            <tr>
                                                <td>
                                                    <div class="form-check check-tables">
                                                        <input class="form-check-input" type="checkbox" value="{{ $lesson->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <h2>
                                                        <a href="{{ route('lessons.show', $lesson) }}">{{ $lesson->title }}</a>
                                                    </h2>
                                                    <small class="text-muted">{{ Str::limit($lesson->description, 100) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $lesson->subject->subject_name }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $lesson->section->name }}</strong>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $lesson->curriculumObjective->code }}</small><br>
                                                    <strong>{{ Str::limit($lesson->curriculumObjective->title, 50) }}</strong>
                                                </td>
                                                <td>
                                                    <strong>{{ $lesson->lesson_date->format('M d, Y') }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $lesson->status_badge }}">{{ ucfirst($lesson->status) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <div class="actions">
                                                        <a href="{{ route('lessons.show', $lesson) }}" class="btn btn-sm bg-danger-light" title="View">
                                                            <i class="far fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('lessons.edit', $lesson) }}" class="btn btn-sm bg-danger-light" title="Edit">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('lessons.activities.index', $lesson) }}" class="btn btn-sm bg-danger-light" title="Activities">
                                                            <i class="fas fa-tasks"></i>
                                                        </a>
                                                        @if($lesson->status === 'draft')
                                                            <form action="{{ route('lessons.publish', $lesson) }}" method="POST" style="display:inline-block;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm bg-danger-light" title="Publish">
                                                                    <i class="fas fa-paper-plane"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($lesson->status === 'published')
                                                            <form action="{{ route('lessons.complete', $lesson) }}" method="POST" style="display:inline-block;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm bg-danger-light" title="Mark Complete">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <form action="{{ route('lessons.destroy', $lesson) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm bg-danger-light" onclick="return confirm('Are you sure you want to delete this lesson?')" title="Delete">
                                                                <i class="far fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-5">
                                                    <div class="text-muted">
                                                        <i class="fas fa-book fa-3x mb-3"></i>
                                                        <h5>No lessons found</h5>
                                                        <p>No lessons have been created yet.</p>
                                                        <a href="{{ route('lessons.create') }}" class="btn btn-primary">
                                                            <i class="fas fa-plus"></i> Create Your First Lesson
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($lessons->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $lessons->links() }}
                                </div>
                            @endif
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

/* Summary cards */
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

/* Actions */
.actions {
    display: flex;
    justify-content: end;
}

.actions a, .actions button {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 5px;
    border: none;
    background: transparent;
}

.actions a:hover, .actions button:hover {
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

/* Badge styling */
.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.badge.bg-primary {
    background-color: #3d5ee1 !important;
}

.badge.bg-success {
    background-color: #7bb13c !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
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

/* Text styling */
.text-muted {
    color: #6c757d !important;
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
    // Filter lessons
    $('#filterLessons').on('click', function() {
        const searchTerm = $('#searchLesson').val();
        const subjectId = $('#subject_filter').val();
        const status = $('#status_filter').val();
        
        // Show loading state
        $(this).html('<i class="fas fa-spinner fa-spin me-2"></i>Filtering...').prop('disabled', true);
        
        // Build URL with parameters
        let url = '{{ route("lessons.index") }}?';
        if (searchTerm) url += 'search=' + encodeURIComponent(searchTerm) + '&';
        if (subjectId) url += 'subject_id=' + subjectId + '&';
        if (status) url += 'status=' + status;
        
        // Redirect to filtered page
        window.location.href = url;
    });
    
    // Select all functionality
    $('#selectAllLessons').on('change', function() {
        $('.form-check-input').prop('checked', $(this).is(':checked'));
    });
    
    // Auto-filter on search input
    $('#searchLesson').on('keyup', function() {
        $('#filterLessons').click();
    });
    
    // Auto-filter on dropdown changes
    $('#subject_filter, #status_filter').on('change', function() {
        $('#filterLessons').click();
    });
});
</script>
@endpush

@endsection 