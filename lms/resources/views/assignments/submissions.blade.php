@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Grade Submissions</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('assignments.index') }}">Assignments</a></li>
                        <li class="breadcrumb-item active">Grade Submissions</li>
                    </ul>
                </div>
                <div class="col-auto text-right float-right ml-auto">
                    <a href="{{ route('assignments.show', $assignment->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Assignment
                    </a>
                </div>
            </div>
        </div>

        {{-- Assignment Summary --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Assignment: {{ $assignment->title }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Subject</h6>
                                    <p class="fw-bold">{{ $assignment->subject->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Section</h6>
                                    <p class="fw-bold">{{ $assignment->section->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Due Date</h6>
                                    <p class="fw-bold">{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h6 class="text-muted">Max Score</h6>
                                    <p class="fw-bold">{{ $assignment->max_score }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submissions List --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="card-title">Student Submissions ({{ $submissions->count() }})</h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="filterSubmissions('all')">All</button>
                                    <button type="button" class="btn btn-outline-warning" onclick="filterSubmissions('pending')">Pending</button>
                                    <button type="button" class="btn btn-outline-success" onclick="filterSubmissions('graded')">Graded</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($submissions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-striped" id="submissionsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Student</th>
                                            <th>Submission Date</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Feedback</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submissions as $submission)
                                            <tr class="submission-row" data-status="{{ $submission->status }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <i class="fas fa-user text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $submission->student->full_name ?? 'Student' }}</h6>
                                                            <small class="text-muted">{{ $submission->student->email ?? 'N/A' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y H:i') : 'N/A' }}</span>
                                                        @if($submission->submitted_at)
                                                            <small class="text-muted">
                                                                @if($submission->isLate())
                                                                    <span class="text-danger">Late</span>
                                                                @else
                                                                    <span class="text-success">On Time</span>
                                                                @endif
                                                            </small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @switch($submission->status)
                                                        @case('submitted')
                                                            <span class="badge bg-warning">Pending Review</span>
                                                            @break
                                                        @case('graded')
                                                            <span class="badge bg-success">Graded</span>
                                                            @break
                                                        @case('late')
                                                            <span class="badge bg-danger">Late</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($submission->status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @if($submission->status === 'graded')
                                                        <span class="fw-bold text-success">{{ $submission->score }}/{{ $assignment->max_score }}</span>
                                                        @if($submission->score_percentage)
                                                            <br><small class="text-muted">{{ number_format($submission->score_percentage, 1) }}%</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not graded</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($submission->feedback)
                                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $submission->feedback }}">
                                                            {{ Str::limit($submission->feedback, 30) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No feedback</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                                onclick="viewSubmission({{ $submission->id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($submission->status !== 'graded')
                                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                                    onclick="gradeSubmission({{ $submission->id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="editGrade({{ $submission->id }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $submissions->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-inbox fa-4x text-muted"></i>
                                </div>
                                <h5 class="text-muted">No submissions yet</h5>
                                <p class="text-muted mb-4">Students haven't submitted any work for this assignment</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Grade Submission Modal --}}
<div class="modal fade" id="gradeModal" tabindex="-1" aria-labelledby="gradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gradeModalLabel">Grade Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gradeForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div id="submissionDetails"></div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="score" class="form-label">Score <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="score" name="score" 
                                   min="0" max="{{ $assignment->max_score }}" required>
                            <small class="form-text text-muted">Maximum score: {{ $assignment->max_score }}</small>
                        </div>
                        <div class="col-md-6">
                            <label for="score_percentage" class="form-label">Percentage</label>
                            <input type="text" class="form-control" id="score_percentage" readonly>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="feedback" class="form-label">Feedback</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" 
                                  placeholder="Provide constructive feedback to the student..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Grade</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Submission Modal --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">View Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewSubmissionContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    let currentSubmissionId = null;

    // Filter submissions by status
    function filterSubmissions(status) {
        const rows = document.querySelectorAll('.submission-row');
        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // View submission details
    function viewSubmission(submissionId) {
        // Load submission details via AJAX
        fetch(`/assignments/submissions/${submissionId}/view`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('viewSubmissionContent').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('viewModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading submission details');
            });
    }

    // Grade submission
    function gradeSubmission(submissionId) {
        currentSubmissionId = submissionId;
        document.getElementById('gradeForm').action = `/assignments/submissions/${submissionId}/grade`;
        document.getElementById('gradeModalLabel').textContent = 'Grade Submission';
        
        // Load submission details
        fetch(`/assignments/submissions/${submissionId}/details`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('submissionDetails').innerHTML = data.html;
                new bootstrap.Modal(document.getElementById('gradeModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading submission details');
            });
    }

    // Edit existing grade
    function editGrade(submissionId) {
        currentSubmissionId = submissionId;
        document.getElementById('gradeForm').action = `/assignments/submissions/${submissionId}/grade`;
        document.getElementById('gradeModalLabel').textContent = 'Edit Grade';
        
        // Load existing grade data
        fetch(`/assignments/submissions/${submissionId}/grade-data`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('score').value = data.score;
                document.getElementById('feedback').value = data.feedback;
                updateScorePercentage();
                new bootstrap.Modal(document.getElementById('gradeModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading grade data');
            });
    }

    // Update score percentage
    function updateScorePercentage() {
        const score = document.getElementById('score').value;
        const maxScore = parseInt('{{ $assignment->max_score }}');
        if (score && maxScore) {
            const percentage = (score / maxScore) * 100;
            document.getElementById('score_percentage').value = percentage.toFixed(1) + '%';
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Update percentage when score changes
        document.getElementById('score').addEventListener('input', updateScorePercentage);
        
        // Handle form submission
        document.getElementById('gradeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal and reload page
                    bootstrap.Modal.getInstance(document.getElementById('gradeModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving grade');
            });
        });
    });
</script>
@endsection
