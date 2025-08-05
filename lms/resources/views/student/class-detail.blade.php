@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Student Header -->
            <div class="student-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="student-info">
                            <h4 class="student-name">{{ $student->first_name }} {{ $student->last_name }}</h4>
                            <p class="student-number">Student No. {{ $student->admission_id ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <span class="enrollment-status">ENROLLED</span>
                    </div>
                </div>
            </div>

            <!-- Course Banner -->
            <div class="course-banner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="course-info">
                            <h1 class="course-title">{{ $enrollment->subject->subject_code }} {{ $enrollment->subject->subject_name }}</h1>
                            <div class="course-details">
                                <div class="detail-item">
                                    <strong>Section/Group:</strong> {{ $student->section ?? '4IT-B' }}
                                </div>
                                <div class="detail-item">
                                    <strong>Lecture Status:</strong> LECTURE TBA
                                </div>
                                <div class="detail-item">
                                    <strong>Schedule:</strong>
                                    <ul class="schedule-list">
                                        <li>Tuesday, 11:30 am - 1:00 pm (Network Room)</li>
                                        <li>Friday, 11:30 am - 1:00 pm (Network Room)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="class-number">
                            Class #{{ $enrollment->subject->id }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <ul class="nav nav-tabs" id="classTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'assignments' ? 'active' : '' }}" 
                                id="assignments-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#assignments" 
                                type="button" 
                                role="tab">
                            ASSIGNMENTS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'quizzes' ? 'active' : '' }}" 
                                id="quizzes-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#quizzes" 
                                type="button" 
                                role="tab">
                            QUIZZES & EXAMS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'online-classes' ? 'active' : '' }}" 
                                id="online-classes-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#online-classes" 
                                type="button" 
                                role="tab">
                            ONLINE CLASSES
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'class-posts' ? 'active' : '' }}" 
                                id="class-posts-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#class-posts" 
                                type="button" 
                                role="tab">
                            CLASS POSTS
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'grades' ? 'active' : '' }}" 
                                id="grades-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#grades" 
                                type="button" 
                                role="tab">
                            POST GRADES
                        </button>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="classTabsContent">
                <!-- Assignments Tab -->
                <div class="tab-pane fade {{ $activeTab === 'assignments' ? 'show active' : '' }}" 
                     id="assignments" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <h4>No Assignments Found</h4>
                            <p>There are no assignments available for this class at the moment.</p>
                        </div>
                    </div>
                </div>

                <!-- Quizzes & Exams Tab -->
                <div class="tab-pane fade {{ $activeTab === 'quizzes' ? 'show active' : '' }}" 
                     id="quizzes" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <h4>No Quizzes & Exams Found</h4>
                            <p>There are no quizzes or exams available for this class at the moment.</p>
                        </div>
                    </div>
                </div>

                <!-- Online Classes Tab -->
                <div class="tab-pane fade {{ $activeTab === 'online-classes' ? 'show active' : '' }}" 
                     id="online-classes" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <h4>No Online Classes Found</h4>
                            <p>There are no online classes scheduled for this class at the moment.</p>
                        </div>
                    </div>
                </div>

                <!-- Class Posts Tab -->
                <div class="tab-pane fade {{ $activeTab === 'class-posts' ? 'show active' : '' }}" 
                     id="class-posts" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h4>No Class Posts Found</h4>
                            <p>There are no class posts available for this class at the moment.</p>
                        </div>
                    </div>
                </div>

                <!-- Grades Tab -->
                <div class="tab-pane fade {{ $activeTab === 'grades' ? 'show active' : '' }}" 
                     id="grades" 
                     role="tabpanel">
                    <div class="tab-content-wrapper">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h4>No Grades Posted</h4>
                            <p>No grades have been posted for this class yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
/* Student Header Styling */
.student-header {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 20px 30px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.student-name {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}

.student-number {
    font-size: 16px;
    opacity: 0.9;
    margin: 0;
}

.enrollment-status {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Course Banner Styling */
.course-banner {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: white;
    padding: 40px 30px;
    border-radius: 15px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(30, 60, 114, 0.3);
}

.course-banner::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><polygon fill="rgba(255,255,255,0.1)" points="0,0 100,0 100,100"/></svg>') no-repeat;
    background-size: cover;
}

.course-title {
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 20px;
    line-height: 1.2;
}

.course-details {
    font-size: 16px;
    line-height: 1.6;
}

.detail-item {
    margin-bottom: 12px;
}

.detail-item strong {
    color: #a8d5ff;
}

.schedule-list {
    list-style: none;
    padding-left: 0;
    margin: 8px 0 0 0;
}

.schedule-list li {
    padding: 4px 0;
    position: relative;
    padding-left: 20px;
}

.schedule-list li::before {
    content: '•';
    position: absolute;
    left: 0;
    color: #a8d5ff;
}

.class-number {
    font-size: 24px;
    font-weight: 700;
    color: #a8d5ff;
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

/* Responsive Design */
@media (max-width: 768px) {
    .student-header {
        padding: 15px 20px;
    }
    
    .student-name {
        font-size: 20px;
    }
    
    .course-banner {
        padding: 30px 20px;
    }
    
    .course-title {
        font-size: 28px;
    }
    
    .class-number {
        font-size: 20px;
        margin-top: 20px;
    }
    
    .nav-tabs {
        padding: 0 10px;
    }
    
    .nav-tabs .nav-link {
        padding: 12px 15px;
        font-size: 13px;
    }
    
    .tab-content-wrapper {
        padding: 30px 20px;
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
    // Handle tab navigation with URL updates
    $('.nav-tabs .nav-link').on('click', function() {
        const tabId = $(this).attr('id').replace('-tab', '');
        const currentUrl = new URL(window.location);
        currentUrl.searchParams.set('tab', tabId);
        window.history.pushState({}, '', currentUrl);
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@endsection 