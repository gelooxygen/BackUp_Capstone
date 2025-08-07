@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Enhanced Modern Header -->
            <div class="modern-header">
                <div class="header-content">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="header-text">
                                <h1 class="page-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    My Classes
                                </h1>
                                <p class="page-subtitle">Track your academic progress and manage your courses</p>
                                <div class="quick-stats">
                                    <div class="stat-item">
                                        <i class="fas fa-book-open"></i>
                                        <span>{{ $enrollments->count() }} Active Courses</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="fas fa-calendar-check"></i>
                                        <span>{{ $enrollments->unique('academic_year_id')->count() }} Academic Years</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="header-actions">
                                <div class="search-box">
                                    <input type="text" id="classSearch" placeholder="Search classes..." class="form-control">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="filter-buttons">
                                    <button class="btn btn-filter active" data-filter="all">All</button>
                                    <button class="btn btn-filter" data-filter="active">Active</button>
                                    <button class="btn btn-filter" data-filter="completed">Completed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Classes Grid -->
            <div class="classes-container">
                @if($enrollments->count() > 0)
                    <div class="row" id="classesGrid">
                        @foreach($enrollments as $enrollment)
                            @php
                                // Calculate progress based on grades (placeholder logic)
                                $grades = $enrollment->student->grades()->where('subject_id', $enrollment->subject->id)->get();
                                $progress = $grades->count() > 0 ? min(100, ($grades->avg('percentage') ?? 0)) : 0;
                                
                                // Get recent activities
                                $recentActivities = collect(); // Placeholder for activities
                                
                                // Get next class schedule
                                $nextClass = null; // Placeholder for next class
                            @endphp
                            
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4 class-card" 
                                 data-status="{{ $enrollment->status }}" 
                                 data-subject="{{ strtolower($enrollment->subject->subject_name) }}">
                                <div class="enhanced-class-card">
                                    <div class="card-header">
                                        <div class="subject-info">
                                            <div class="subject-code">#{{ $enrollment->subject->id }}: {{ $enrollment->subject->subject_code ?? 'SUB' . $enrollment->subject->id }}</div>
                                            <h3 class="subject-title">{{ $enrollment->subject->subject_name }}</h3>
                                            <div class="subject-category">{{ $enrollment->subject->class ?? 'General' }}</div>
                                        </div>
                                        <div class="status-indicator {{ $enrollment->status === 'active' ? 'active' : 'inactive' }}">
                                            <span class="status-dot"></span>
                                            {{ ucfirst($enrollment->status) }}
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <div class="course-details">
                                            <div class="detail-row">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>{{ $enrollment->academicYear->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $enrollment->semester->name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row">
                                                <i class="fas fa-user-graduate"></i>
                                                <span>Enrolled: {{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : 'N/A' }}</span>
                                            </div>
                                            <div class="detail-row">
                                                <i class="fas fa-users"></i>
                                                <span>Section: {{ $enrollment->student->sections->first()->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Enhanced Progress Section -->
                                        <div class="progress-section">
                                            <div class="progress-header">
                                                <span>Course Progress</span>
                                                <span class="progress-percentage">{{ number_format($progress, 1) }}%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <div class="progress-stats">
                                                <div class="stat">
                                                    <span class="stat-number">{{ $grades->count() }}</span>
                                                    <span class="stat-label">Grades</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-number">{{ $recentActivities->count() }}</span>
                                                    <span class="stat-label">Activities</span>
                                                </div>
                                                <div class="stat">
                                                    <span class="stat-number">{{ $nextClass ? 'Today' : 'None' }}</span>
                                                    <span class="stat-label">Next Class</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="quick-actions">
                                            <div class="action-item">
                                                <i class="fas fa-file-alt"></i>
                                                <span>Assignments</span>
                                                <span class="count">3</span>
                                            </div>
                                            <div class="action-item">
                                                <i class="fas fa-question-circle"></i>
                                                <span>Quizzes</span>
                                                <span class="count">2</span>
                                            </div>
                                            <div class="action-item">
                                                <i class="fas fa-video"></i>
                                                <span>Online Classes</span>
                                                <span class="count">5</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <div class="action-buttons">
                                            <a href="{{ route('student.class.detail', $enrollment->id) }}" class="btn btn-primary btn-modern">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-download"></i> Download Materials</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-calendar"></i> View Schedule</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line"></i> View Grades</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-bell"></i> Set Reminders</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <h3>No Classes Enrolled</h3>
                        <p>You haven't been enrolled in any classes yet. Contact your academic advisor to get started.</p>
                        <div class="empty-actions">
                            <a href="{{ route('student/dashboard') }}" class="btn btn-primary btn-modern">
                                <i class="fas fa-arrow-left"></i>
                                Back to Dashboard
                            </a>
                            <a href="#" class="btn btn-secondary btn-modern">
                                <i class="fas fa-question-circle"></i>
                                Contact Advisor
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Enhanced Summary Section -->
            @if($enrollments->count() > 0)
                <div class="summary-section">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="section-title">Academic Overview</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="summary-card">
                                <div class="card-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="card-content">
                                    <h3>{{ $enrollments->count() }}</h3>
                                    <p>Total Classes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="summary-card">
                                <div class="card-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="card-content">
                                    <h3>{{ $enrollments->unique('academic_year_id')->count() }}</h3>
                                    <p>Academic Years</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="summary-card">
                                <div class="card-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="card-content">
                                    <h3>{{ $enrollments->unique('semester_id')->count() }}</h3>
                                    <p>Semesters</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="summary-card">
                                <div class="card-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="card-content">
                                    <h3>{{ $enrollments->where('status', 'active')->count() }}</h3>
                                    <p>Active Classes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Statistics -->
                    <div class="row mt-4">
                        <div class="col-lg-6 mb-4">
                            <div class="stats-card">
                                <h4><i class="fas fa-chart-line"></i> Performance Overview</h4>
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $performanceStats['average_grade'] }}%</span>
                                        <span class="stat-label">Average Grade</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $performanceStats['attendance_rate'] }}%</span>
                                        <span class="stat-label">Attendance Rate</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $performanceStats['total_assignments'] }}</span>
                                        <span class="stat-label">Assignments</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-value">{{ $performanceStats['total_quizzes'] }}</span>
                                        <span class="stat-label">Quizzes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="stats-card">
                                <h4><i class="fas fa-calendar-alt"></i> Upcoming Deadlines</h4>
                                <div class="deadlines-list">
                                    @foreach($upcomingDeadlines as $deadline)
                                        @php
                                            $daysLeft = $deadline['due_date']->diffInDays(now(), false);
                                            $statusClass = $daysLeft < 0 ? 'overdue' : ($daysLeft == 0 ? 'today' : ($daysLeft <= 2 ? 'urgent' : 'normal'));
                                            $statusText = $daysLeft < 0 ? 'Overdue' : ($daysLeft == 0 ? 'Today' : ($daysLeft == 1 ? '1 day left' : $daysLeft . ' days left'));
                                        @endphp
                                        <div class="deadline-item">
                                            <div class="deadline-icon">
                                                <i class="{{ $deadline['icon'] }}"></i>
                                            </div>
                                            <div class="deadline-content">
                                                <span class="deadline-title">{{ $deadline['title'] }}</span>
                                                <span class="deadline-date">Due: {{ $deadline['due_date']->format('M d, Y') }}</span>
                                            </div>
                                            <div class="deadline-status {{ $statusClass }}">{{ $statusText }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

@push('styles')
<style>
/* Enhanced Modern Header Styling */
.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 30px;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.page-title i {
    font-size: 2rem;
    color: #ffd700;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 20px;
    font-weight: 300;
}

.quick-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    opacity: 0.9;
}

.stat-item i {
    color: #ffd700;
}

.header-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.search-box {
    position: relative;
}

.search-box input {
    padding: 12px 40px 12px 15px;
    border-radius: 25px;
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    backdrop-filter: blur(10px);
}

.search-box input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.search-box i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.7);
}

.filter-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
}

.btn-filter {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-filter.active,
.btn-filter:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
}

/* Enhanced Class Cards */
.enhanced-class-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.enhanced-class-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px 25px 20px;
    position: relative;
    border-bottom: 1px solid #e9ecef;
}

.subject-code {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.subject-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
    line-height: 1.3;
}

.subject-category {
    font-size: 0.8rem;
    color: #667eea;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-indicator {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-indicator.active {
    color: #28a745;
}

.status-indicator.active .status-dot {
    background: #28a745;
}

.status-indicator.inactive {
    color: #6c757d;
}

.status-indicator.inactive .status-dot {
    background: #6c757d;
}

.card-body {
    padding: 25px;
}

.course-details {
    margin-bottom: 25px;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    font-size: 0.9rem;
    color: #6c757d;
}

.detail-row i {
    color: #667eea;
    width: 16px;
    text-align: center;
}

/* Enhanced Progress Section */
.progress-section {
    margin-bottom: 25px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.progress-header span:first-child {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.progress-percentage {
    font-size: 0.9rem;
    font-weight: 700;
    color: #667eea;
}

.progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
    transition: width 0.6s ease;
}

.progress-stats {
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

.stat {
    text-align: center;
    flex: 1;
}

.stat-number {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
}

.stat-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Quick Actions */
.quick-actions {
    margin-bottom: 20px;
}

.action-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f8f9fa;
    font-size: 0.9rem;
}

.action-item:last-child {
    border-bottom: none;
}

.action-item i {
    color: #667eea;
    width: 20px;
    text-align: center;
}

.action-item .count {
    background: #667eea;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

.card-footer {
    padding: 20px 25px 25px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.btn-modern {
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    flex: 1;
    justify-content: center;
}

.btn-modern.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.btn-modern.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.btn-modern.btn-secondary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-modern.btn-secondary:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

/* Enhanced Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    max-width: 500px;
    margin: 0 auto;
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.empty-state h3 {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 15px;
}

.empty-state p {
    font-size: 1.1rem;
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 30px;
}

.empty-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Enhanced Summary Section */
.summary-section {
    padding: 40px 20px;
    background: #f8f9fa;
    margin-top: 40px;
    border-radius: 20px;
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 30px;
    text-align: center;
}

.summary-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.card-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.card-content h3 {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.card-content p {
    font-size: 1rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
}

/* Stats Cards */
.stats-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e9ecef;
}

.stats-card h4 {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.stats-card h4 i {
    color: #667eea;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Deadlines List */
.deadlines-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.deadline-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.deadline-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.deadline-icon {
    width: 40px;
    height: 40px;
    background: #667eea;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.deadline-content {
    flex: 1;
}

.deadline-title {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 3px;
}

.deadline-date {
    font-size: 0.85rem;
    color: #6c757d;
}

.deadline-status {
    font-size: 0.8rem;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 15px;
    background: #28a745;
    color: white;
}

.deadline-status.urgent {
    background: #dc3545;
}

.deadline-status.today {
    background: #ffc107;
    color: #212529;
}

.deadline-status.overdue {
    background: #dc3545;
    color: white;
}

.deadline-status.normal {
    background: #28a745;
    color: white;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-header {
        padding: 30px 0;
        border-radius: 0 0 15px 15px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .page-title i {
        font-size: 1.5rem;
    }
    
    .quick-stats {
        justify-content: center;
    }
    
    .header-actions {
        margin-top: 20px;
    }
    
    .filter-buttons {
        justify-content: center;
    }
    
    .classes-container {
        padding: 0 15px;
    }
    
    .enhanced-class-card {
        margin-bottom: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-modern {
        width: 100%;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .summary-card {
        margin-bottom: 20px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .empty-actions {
        flex-direction: column;
    }
}

@media (max-width: 576px) {
    .classes-container {
        padding: 0 10px;
    }
    
    .enhanced-class-card {
        border-radius: 12px;
    }
    
    .card-header,
    .card-body,
    .card-footer {
        padding: 20px;
    }
    
    .subject-title {
        font-size: 1.2rem;
    }
    
    .page-title {
        font-size: 1.8rem;
    }
    
    .summary-section {
        padding: 30px 15px;
        border-radius: 15px;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Search functionality
    $('#classSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.class-card').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Filter functionality
    $('.btn-filter').on('click', function() {
        $('.btn-filter').removeClass('active');
        $(this).addClass('active');
        
        var filter = $(this).data('filter');
        
        if (filter === 'all') {
            $('.class-card').show();
        } else {
            $('.class-card').hide();
            $('.class-card[data-status="' + filter + '"]').show();
        }
    });
    
    // Enhanced hover effects
    $('.enhanced-class-card').hover(
        function() {
            $(this).find('.progress-fill').css('width', '85%');
        },
        function() {
            var progress = $(this).find('.progress-percentage').text();
            $(this).find('.progress-fill').css('width', progress);
        }
    );
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Animate progress bars on load
    $('.progress-fill').each(function() {
        var width = $(this).css('width');
        $(this).css('width', '0%');
        setTimeout(() => {
            $(this).css('width', width);
        }, 500);
    });
});
</script>
@endpush

@endsection 