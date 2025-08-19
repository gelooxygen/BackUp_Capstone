@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Modern Hero Section -->
            <div class="hero-section">
                <div class="hero-background"></div>
                <div class="hero-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="hero-main">
                                <div class="breadcrumb-nav">
                                    <a href="{{ route('student.my-classes') }}" class="breadcrumb-link">
                                        <i class="fas fa-arrow-left"></i> Back to My Classes
                                    </a>
                                </div>
                                <h1 class="hero-title">{{ $enrollment->subject->subject_code }} {{ $enrollment->subject->subject_name }}</h1>
                                <div class="hero-subtitle">{{ $enrollment->subject->class ?? 'General Course' }}</div>
                                <div class="hero-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-user-graduate"></i>
                                        <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-id-card"></i>
                                        <span>Student #{{ $student->admission_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <span>{{ $student->section ?? '4IT-B' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="hero-sidebar">
                                <div class="status-card">
                                    <div class="status-header">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Enrollment Status</span>
                                    </div>
                                    <div class="status-value">ENROLLED</div>
                                    <div class="status-details">
                                        <div class="detail-row">
                                            <span class="label">Academic Year:</span>
                                            <span class="value">{{ $enrollment->academicYear->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Semester:</span>
                                            <span class="value">{{ $enrollment->semester->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="label">Class ID:</span>
                                            <span class="value">#{{ $enrollment->subject->id }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="schedule-card">
                                    <div class="schedule-header">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span>Class Schedule</span>
                                    </div>
                                    <div class="schedule-list">
                                        <div class="schedule-item">
                                            <div class="day">Tuesday</div>
                                            <div class="time">11:30 AM - 1:00 PM</div>
                                            <div class="room">Network Room</div>
                                        </div>
                                        <div class="schedule-item">
                                            <div class="day">Friday</div>
                                            <div class="time">11:30 AM - 1:00 PM</div>
                                            <div class="room">Network Room</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modern Tab Navigation -->
            <div class="modern-tabs">
                <div class="tabs-container">
                    <div class="tabs-wrapper">
                        <button class="tab-button {{ $activeTab === 'assignments' ? 'active' : '' }}" 
                                data-tab="assignments">
                            <div class="tab-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="tab-content">
                                <span class="tab-title">Assignments</span>
                                <span class="tab-subtitle">View & submit work</span>
                            </div>
                        </button>
                        
                        <button class="tab-button {{ $activeTab === 'quizzes' ? 'active' : '' }}" 
                                data-tab="quizzes">
                            <div class="tab-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="tab-content">
                                <span class="tab-title">Quizzes & Exams</span>
                                <span class="tab-subtitle">Take assessments</span>
                            </div>
                        </button>
                        
                        <button class="tab-button {{ $activeTab === 'online-classes' ? 'active' : '' }}" 
                                data-tab="online-classes">
                            <div class="tab-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <div class="tab-content">
                                <span class="tab-title">Online Classes</span>
                                <span class="tab-subtitle">Virtual learning</span>
                            </div>
                        </button>
                        
                        <button class="tab-button {{ $activeTab === 'class-posts' ? 'active' : '' }}" 
                                data-tab="class-posts">
                            <div class="tab-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div class="tab-content">
                                <span class="tab-title">Class Posts</span>
                                <span class="tab-subtitle">Announcements</span>
                            </div>
                        </button>
                        
                        <button class="tab-button {{ $activeTab === 'grades' ? 'active' : '' }}" 
                                data-tab="grades">
                            <div class="tab-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="tab-content">
                                <span class="tab-title">Grades</span>
                                <span class="tab-subtitle">Performance tracking</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modern Tab Content -->
            <div class="tab-content-container">
                <div class="tab-panel {{ $activeTab === 'assignments' ? 'active' : '' }}" id="assignments-panel">
                <!-- Assignments Tab -->
                <div class="tab-pane fade {{ $activeTab === 'assignments' ? 'show active' : '' }}" 
                     id="assignments" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        @if($assignments->count() > 0)
                            <div class="assignments-list">
                                @foreach($assignments as $assignment)
                                    @php
                                        $submission = $assignment->submissions()
                                            ->where('student_id', $student->id)
                                            ->first();
                                        $status = $submission ? $submission->status : 'pending';
                                        $isOverdue = now() > $assignment->due_date;
                                    @endphp
                                    
                                    <div class="assignment-card {{ $isOverdue && $status === 'pending' ? 'overdue' : '' }}">
                                        <div class="assignment-header">
                                            <div class="assignment-title">
                                                <h5>{{ $assignment->title }}</h5>
                                                <span class="assignment-code">#{{ $assignment->id }}</span>
                                            </div>
                                            <div class="assignment-status">
                                                @if($status === 'submitted')
                                                    <span class="badge bg-info">Submitted</span>
                                                @elseif($status === 'graded')
                                                    <span class="badge bg-success">Graded</span>
                                                @elseif($status === 'late')
                                                    <span class="badge bg-warning">Late</span>
                                                @elseif($isOverdue)
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="assignment-body">
                                            <div class="assignment-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <span>{{ $assignment->teacher->name ?? 'Teacher' }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar text-warning"></i>
                                                    <span>Due: {{ $assignment->due_date->format('M d, Y g:i A') }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-star text-success"></i>
                                                    <span>Max Score: {{ $assignment->max_score }}</span>
                                                </div>
                                                @if($submission)
                                                    <div class="detail-item">
                                                        <i class="fas fa-upload text-info"></i>
                                                        <span>Submitted: {{ $submission->submitted_at->format('M d, Y g:i A') }}</span>
                                                    </div>
                                                    @if($submission->score)
                                                        <div class="detail-item">
                                                            <i class="fas fa-trophy text-warning"></i>
                                                            <span>Score: {{ $submission->score }}/{{ $assignment->max_score }}</span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            
                                            @if($assignment->description)
                                                <div class="assignment-description">
                                                    <p>{{ Str::limit($assignment->description, 150) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="assignment-actions">
                                            @if(!$submission)
                                                <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View Assignment
                                                </a>
                                                @if(!$isOverdue)
                                                    <a href="{{ route('student.assignments.show', $assignment) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-upload"></i> Submit Work
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('student.assignments.submission', $assignment) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View Submission
                                                </a>
                                                @if($submission->status === 'submitted' && !$submission->score)
                                                    <span class="text-muted">Waiting for grading...</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h4>No Assignments Found</h4>
                                <p>There are no assignments available for this class at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quizzes & Exams Tab -->
                <div class="tab-pane fade {{ $activeTab === 'quizzes' ? 'show active' : '' }}" 
                     id="quizzes" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        @if($quizzes->count() > 0)
                            <div class="quizzes-list">
                                @foreach($quizzes as $quiz)
                                    @php
                                        $submission = $quiz->submissions()
                                            ->where('student_id', $student->id)
                                            ->first();
                                        $status = $submission ? $submission->status : 'pending';
                                        $isOverdue = now() > $quiz->due_date;
                                    @endphp
                                    
                                    <div class="quiz-card {{ $isOverdue && $status === 'pending' ? 'overdue' : '' }}">
                                        <div class="quiz-header">
                                            <div class="quiz-title">
                                                <h5>{{ $quiz->title }}</h5>
                                                <span class="quiz-code">#{{ $quiz->id }}</span>
                                            </div>
                                            <div class="quiz-status">
                                                @if($status === 'submitted')
                                                    <span class="badge bg-info">Submitted</span>
                                                @elseif($status === 'graded')
                                                    <span class="badge bg-success">Graded</span>
                                                @elseif($status === 'late')
                                                    <span class="badge bg-warning">Late</span>
                                                @elseif($isOverdue)
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-secondary">Pending</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="quiz-body">
                                            <div class="quiz-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <span>{{ $quiz->lesson->teacher->name ?? 'Teacher' }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar text-warning"></i>
                                                    <span>Due: {{ $quiz->due_date->format('M d, Y g:i A') }}</span>
                                                </div>
                                                @if($quiz->instructions)
                                                    <div class="detail-item">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        <span>{{ Str::limit($quiz->instructions, 100) }}</span>
                                                    </div>
                                                @endif
                                                @if($submission)
                                                    <div class="detail-item">
                                                        <i class="fas fa-upload text-info"></i>
                                                        <span>Submitted: {{ $submission->submitted_at->format('M d, Y g:i A') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="quiz-actions">
                                            @if(!$submission)
                                                <a href="#" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i> View Quiz
                                                </a>
                                                @if(!$isOverdue)
                                                    <a href="#" class="btn btn-success btn-sm">
                                                        <i class="fas fa-edit"></i> Take Quiz
                                                    </a>
                                                @endif
                                            @else
                                                <a href="#" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> View Submission
                                                </a>
                                                @if($submission->status === 'submitted' && !$submission->grades->count())
                                                    <span class="text-muted">Waiting for grading...</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <h4>No Quizzes & Exams Found</h4>
                                <p>There are no quizzes or exams available for this class at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Online Classes Tab -->
                <div class="tab-pane fade {{ $activeTab === 'online-classes' ? 'show active' : '' }}" 
                     id="online-classes" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        @if($onlineClasses->count() > 0)
                            <div class="online-classes-list">
                                @foreach($onlineClasses as $lesson)
                                    <div class="lesson-card">
                                        <div class="lesson-header">
                                            <div class="lesson-title">
                                                <h5>{{ $lesson->title }}</h5>
                                                <span class="lesson-code">#{{ $lesson->id }}</span>
                                            </div>
                                            <div class="lesson-status">
                                                @if($lesson->lesson_date > now())
                                                    <span class="badge bg-info">Upcoming</span>
                                                @elseif($lesson->lesson_date->isToday())
                                                    <span class="badge bg-success">Today</span>
                                                @else
                                                    <span class="badge bg-secondary">Completed</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="lesson-body">
                                            <div class="lesson-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <span>{{ $lesson->teacher->name ?? 'Teacher' }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar text-warning"></i>
                                                    <span>Date: {{ $lesson->lesson_date->format('M d, Y') }}</span>
                                                </div>
                                                @if($lesson->description)
                                                    <div class="detail-item">
                                                        <i class="fas fa-info-circle text-info"></i>
                                                        <span>{{ Str::limit($lesson->description, 100) }}</span>
                                                    </div>
                                                @endif
                                                @if($lesson->file_path)
                                                    <div class="detail-item">
                                                        <i class="fas fa-file text-success"></i>
                                                        <span>Has materials</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($lesson->description)
                                                <div class="lesson-description">
                                                    <p>{{ Str::limit($lesson->description, 150) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="lesson-actions">
                                            <a href="#" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> View Lesson
                                            </a>
                                            @if($lesson->file_path)
                                                <a href="{{ asset('storage/' . $lesson->file_path) }}" class="btn btn-success btn-sm" target="_blank">
                                                    <i class="fas fa-download"></i> Download Materials
                                                </a>
                                            @endif
                                            @if($lesson->activities->count() > 0)
                                                <a href="#" class="btn btn-info btn-sm">
                                                    <i class="fas fa-tasks"></i> View Activities ({{ $lesson->activities->count() }})
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                                <h4>No Online Classes Found</h4>
                                <p>There are no online classes available for this class at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Class Posts Tab -->
                <div class="tab-pane fade {{ $activeTab === 'class-posts' ? 'show active' : '' }}" 
                     id="class-posts" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        @if($classPosts->count() > 0)
                            <div class="class-posts-list">
                                @foreach($classPosts as $post)
                                    <div class="post-card">
                                        <div class="post-header">
                                            <div class="post-title">
                                                <h5>{{ $post->title }}</h5>
                                                <span class="post-code">#{{ $post->id }}</span>
                                            </div>
                                            <div class="post-meta">
                                                <span class="post-date">{{ $post->created_at->format('M d, Y g:i A') }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="post-body">
                                            <div class="post-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <span>{{ $post->teacher->name ?? 'Teacher' }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-tag text-info"></i>
                                                    <span>{{ $post->type ?? 'General' }}</span>
                                                </div>
                                            </div>
                                            
                                            @if($post->content)
                                                <div class="post-content">
                                                    <p>{{ Str::limit($post->content, 200) }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($post->file_path)
                                                <div class="post-attachments">
                                                    <i class="fas fa-paperclip text-muted"></i>
                                                    <span class="text-muted">Has attachments</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="post-actions">
                                            <a href="{{ route('class-posts.show', $post) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Read Full Post
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <h4>No Class Posts Found</h4>
                                <p>There are no class posts available for this class at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Grades Tab -->
                <div class="tab-pane fade {{ $activeTab === 'grades' ? 'show active' : '' }}" 
                     id="grades" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        @if($grades->count() > 0)
                            <div class="grades-list">
                                @foreach($grades as $grade)
                                    <div class="grade-card">
                                        <div class="grade-header">
                                            <div class="grade-title">
                                                <h5>{{ $grade->component->name ?? 'Grade Component' }}</h5>
                                                <span class="grade-code">#{{ $grade->id }}</span>
                                            </div>
                                            <div class="grade-score">
                                                <span class="score-badge {{ $grade->percentage >= 90 ? 'bg-success' : ($grade->percentage >= 75 ? 'bg-info' : 'bg-warning') }}">
                                                    {{ $grade->percentage }}%
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="grade-body">
                                            <div class="grade-details">
                                                <div class="detail-item">
                                                    <i class="fas fa-user text-primary"></i>
                                                    <span>{{ $grade->teacher->name ?? 'Teacher' }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-calendar text-warning"></i>
                                                    <span>Posted: {{ $grade->created_at->format('M d, Y') }}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <i class="fas fa-star text-success"></i>
                                                    <span>Score: {{ $grade->score }}/{{ $grade->max_score }}</span>
                                                </div>
                                                @if($grade->remarks)
                                                    <div class="detail-item">
                                                        <i class="fas fa-comment text-info"></i>
                                                        <span>{{ $grade->remarks }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Grade Summary -->
                                <div class="grade-summary mt-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="summary-card text-center">
                                                <div class="summary-icon">
                                                    <i class="fas fa-trophy text-warning"></i>
                                                </div>
                                                <div class="summary-value">{{ $grades->avg('percentage') ? round($grades->avg('percentage'), 1) : 0 }}%</div>
                                                <div class="summary-label">Average Grade</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="summary-card text-center">
                                                <div class="summary-icon">
                                                    <i class="fas fa-chart-line text-success"></i>
                                                </div>
                                                <div class="summary-value">{{ $grades->max('percentage') ?? 0 }}%</div>
                                                <div class="summary-label">Highest Grade</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="summary-card text-center">
                                                <div class="summary-icon">
                                                    <i class="fas fa-chart-bar text-info"></i>
                                                </div>
                                                <div class="summary-value">{{ $grades->min('percentage') ?? 0 }}%</div>
                                                <div class="summary-label">Lowest Grade</div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="summary-card text-center">
                                                <div class="summary-icon">
                                                    <i class="fas fa-list text-primary"></i>
                                                </div>
                                                <div class="summary-value">{{ $grades->count() }}</div>
                                                <div class="summary-label">Total Grades</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h4>No Grades Posted</h4>
                                <p>No grades have been posted for this class yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
/* Modern Hero Section */
.hero-section {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
    border-radius: 0 0 30px 30px;
    overflow: hidden;
}

.hero-background {
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon fill="rgba(255,255,255,0.1)" points="0,0 100,0 100,100"/></svg>') no-repeat;
    background-size: cover;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-main {
    padding-right: 30px;
}

.breadcrumb-nav {
    margin-bottom: 20px;
}

.breadcrumb-link {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.breadcrumb-link:hover {
    color: white;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    line-height: 1.2;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    margin-bottom: 25px;
    font-weight: 300;
}

.hero-meta {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.1);
    padding: 12px 20px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.meta-item i {
    color: #ffd700;
    font-size: 1.1rem;
}

.hero-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.status-card,
.schedule-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 25px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.status-header,
.schedule-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-header i,
.schedule-header i {
    color: #ffd700;
    font-size: 1.1rem;
}

.status-value {
    font-size: 2rem;
    font-weight: 700;
    color: #28a745;
    margin-bottom: 20px;
    text-align: center;
}

.status-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row .label {
    font-size: 0.8rem;
    opacity: 0.8;
}

.detail-row .value {
    font-weight: 600;
}

.schedule-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.schedule-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 15px;
    padding: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.schedule-item .day {
    font-weight: 600;
    color: #ffd700;
    margin-bottom: 5px;
}

.schedule-item .time {
    font-size: 0.9rem;
    margin-bottom: 3px;
}

.schedule-item .room {
    font-size: 0.8rem;
    opacity: 0.8;
}

/* Modern Tab Navigation */
.modern-tabs {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.tabs-container {
    overflow-x: auto;
}

.tabs-wrapper {
    display: flex;
    gap: 20px;
    min-width: max-content;
}

.tab-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
    padding: 25px 20px;
    background: #f8f9fa;
    border: 2px solid transparent;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 140px;
    text-align: center;
}

.tab-button:hover {
    background: #e9ecef;
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.tab-button.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-color: #667eea;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.tab-button.hover {
    transform: translateY(-3px);
}

.tab-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.tab-button.active .tab-icon {
    background: rgba(255, 255, 255, 0.3);
}

.tab-content {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.tab-title {
    font-weight: 600;
    font-size: 0.9rem;
}

.tab-subtitle {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Tab Content Container */
.tab-content-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.tab-panel {
    display: none;
    padding: 40px;
}

.tab-panel.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Tab Navigation Styling */
.tab-navigation {
    margin-bottom: 30px;
}

.nav-tabs {
    border-bottom: 3px solid #e9ecef;
    background: #f8f9fa;
    border-radius: 10px 10px 0 0;
    padding: 0 20px;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 600;
    font-size: 14px;
    padding: 15px 20px;
    margin-right: 5px;
    border-radius: 8px 8px 0 0;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    background: rgba(30, 60, 114, 0.1);
    color: #1e3c72;
}

.nav-tabs .nav-link.active {
    background: #1e3c72;
    color: white;
    border: none;
}

/* Tab Content Styling */
.tab-content-wrapper {
    background: white;
    border: 1px solid #e9ecef;
    border-top: none;
    border-radius: 0 0 10px 10px;
    padding: 40px;
    min-height: 400px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
}

.empty-icon i {
    font-size: 40px;
    color: #6c757d;
}

.empty-state h4 {
    color: #2c323f;
    margin-bottom: 15px;
    font-weight: 600;
}

.empty-state p {
    color: #6c757d;
    font-size: 16px;
    margin: 0;
}

/* Assignment Cards Styling */
.assignments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.assignment-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.assignment-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.assignment-card.overdue {
    border-left: 4px solid #dc3545;
    background: #fff5f5;
}

.assignment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.assignment-title h5 {
    margin: 0 0 5px 0;
    color: #2c323f;
    font-weight: 600;
}

.assignment-code {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.assignment-status .badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 20px;
}

.assignment-body {
    margin-bottom: 20px;
}

.assignment-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 14px;
}

.detail-item i {
    width: 16px;
    text-align: center;
}

.assignment-description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.assignment-description p {
    margin: 0;
    color: #495057;
    line-height: 1.5;
}

.assignment-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* Class Posts Cards Styling */
.class-posts-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.post-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.post-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.post-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.post-title h5 {
    margin: 0 0 5px 0;
    color: #2c323f;
    font-weight: 600;
}

.post-code {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.post-date {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.post-body {
    margin-bottom: 20px;
}

.post-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.post-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #28a745;
    margin-bottom: 15px;
}

.post-content p {
    margin: 0;
    color: #495057;
    line-height: 1.5;
}

.post-attachments {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6c757d;
    font-size: 14px;
}

.post-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

/* Grades Cards Styling */
.grades-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.grade-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.grade-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.grade-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.grade-title h5 {
    margin: 0 0 5px 0;
    color: #2c323f;
    font-weight: 600;
}

.grade-code {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.score-badge {
    font-size: 14px;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
}

.grade-body {
    margin-bottom: 20px;
}

.grade-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

/* Grade Summary Styling */
.grade-summary {
    margin-top: 30px;
}

.summary-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.summary-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.summary-icon {
    font-size: 32px;
    margin-bottom: 15px;
}

.summary-value {
    font-size: 24px;
    font-weight: 700;
    color: #2c323f;
    margin-bottom: 8px;
}

.summary-label {
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
}

/* Quiz Cards Styling */
.quizzes-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.quiz-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.quiz-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.quiz-card.overdue {
    border-left: 4px solid #dc3545;
    background: #fff5f5;
}

.quiz-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.quiz-title h5 {
    margin: 0 0 5px 0;
    color: #2c323f;
    font-weight: 600;
}

.quiz-code {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.quiz-status .badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 20px;
}

.quiz-body {
    margin-bottom: 20px;
}

.quiz-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.quiz-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* Lesson Cards Styling */
.online-classes-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.lesson-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.lesson-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.lesson-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.lesson-title h5 {
    margin: 0 0 5px 0;
    color: #2c323f;
    font-weight: 600;
}

.lesson-code {
    color: #6c757d;
    font-size: 12px;
    font-weight: 500;
}

.lesson-status .badge {
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 20px;
}

.lesson-body {
    margin-bottom: 20px;
}

.lesson-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.lesson-description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #28a745;
}

.lesson-description p {
    margin: 0;
    color: #495057;
    line-height: 1.5;
}

.lesson-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-meta {
        gap: 20px;
    }
    
    .meta-item {
        padding: 10px 16px;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 40px 0;
        border-radius: 0 0 20px 20px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-meta {
        justify-content: center;
        gap: 15px;
    }
    
    .meta-item {
        font-size: 0.9rem;
        padding: 10px 14px;
    }
    
    .hero-sidebar {
        margin-top: 30px;
    }
    
    .status-card,
    .schedule-card {
        padding: 20px;
    }
    
    .modern-tabs {
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .tabs-wrapper {
        gap: 15px;
    }
    
    .tab-button {
        padding: 20px 15px;
        min-width: 120px;
    }
    
    .tab-panel {
        padding: 30px 20px;
    }
    
    .tab-content-wrapper {
        padding: 30px 20px;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .hero-meta {
        flex-direction: column;
        align-items: center;
    }
    
    .meta-item {
        width: 100%;
        justify-content: center;
    }
    
    .status-card,
    .schedule-card {
        padding: 15px;
    }
    
    .modern-tabs {
        padding: 15px;
        border-radius: 15px;
    }
    
    .tabs-wrapper {
        gap: 10px;
    }
    
    .tab-button {
        padding: 15px 10px;
        min-width: 100px;
    }
    
    .tab-button .tab-icon {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    
    .tab-title {
        font-size: 0.8rem;
    }
    
    .tab-subtitle {
        font-size: 0.7rem;
    }
    
    .tab-panel {
        padding: 20px 15px;
    }
}

@media (max-width: 576px) {
    .nav-tabs .nav-link {
        padding: 10px 12px;
        font-size: 12px;
        margin-right: 2px;
    }
    
    .course-title {
        font-size: 24px;
    }
    
    .course-details {
        font-size: 14px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Handle modern tab navigation
    $('.tab-button').on('click', function() {
        const tabName = $(this).data('tab');
        
        // Update active tab button
        $('.tab-button').removeClass('active');
        $(this).addClass('active');
        
        // Update active tab panel
        $('.tab-panel').removeClass('active');
        $('#' + tabName + '-panel').addClass('active');
        
        // Update URL
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('tab', tabName);
        window.history.pushState({}, '', currentUrl);
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Add smooth transitions
    $('.tab-button').hover(
        function() {
            $(this).addClass('hover');
        },
        function() {
            $(this).removeClass('hover');
        }
    );
});
</script>
@endpush

@endsection 