@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <!-- Modern Page Header -->
            <div class="modern-header">
                <div class="header-content">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="header-text">
                                <h1 class="page-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    My Classes
                                </h1>
                                <p class="page-subtitle">Manage your academic journey and track your progress</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="header-stats">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="stat-info">
                                        <span class="stat-number">{{ $enrollments->count() }}</span>
                                        <span class="stat-label">Active Classes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Grid -->
            <div class="classes-container">
                @if($enrollments->count() > 0)
                    <div class="row">
                        @foreach($enrollments as $enrollment)
                            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-4">
                                <div class="modern-class-card">
                                    <div class="card-header">
                                        <div class="subject-info">
                                            <div class="subject-code">#{{ $enrollment->subject->id }}: {{ $enrollment->subject->subject_code ?? 'SUB' . $enrollment->subject->id }}</div>
                                            <h3 class="subject-title">{{ $enrollment->subject->subject_name }}</h3>
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
                                        </div>
                                        
                                        <div class="progress-section">
                                            <div class="progress-header">
                                                <span>Course Progress</span>
                                                <span class="progress-percentage">75%</span>
                                            </div>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 75%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <div class="action-buttons">
                                            <a href="{{ route('student.class.detail', $enrollment->id) }}" class="btn btn-primary btn-modern">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </a>
                                            <a href="#" class="btn btn-secondary btn-modern">
                                                <i class="fas fa-download"></i>
                                                Materials
                                            </a>
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
                        <a href="{{ route('student/dashboard') }}" class="btn btn-primary btn-modern">
                            <i class="fas fa-arrow-left"></i>
                            Back to Dashboard
                        </a>
                    </div>
                @endif
            </div>

            <!-- Summary Section -->
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
                </div>
            @endif
        </div>
    </div>

@push('styles')
<style>
/* Modern Header Styling */
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
    margin-bottom: 0;
    font-weight: 300;
}

.header-stats {
    display: flex;
    justify-content: flex-end;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #ffd700;
}

.stat-number {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    color: #ffd700;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Classes Container */
.classes-container {
    padding: 0 20px;
}

/* Modern Class Cards */
.modern-class-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.modern-class-card:hover {
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
    margin-bottom: 0;
    line-height: 1.3;
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

.progress-section {
    margin-bottom: 20px;
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
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
    transition: width 0.6s ease;
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

/* Empty State */
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

/* Summary Section */
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
    
    .header-stats {
        justify-content: center;
        margin-top: 20px;
    }
    
    .stat-card {
        padding: 15px;
    }
    
    .classes-container {
        padding: 0 15px;
    }
    
    .modern-class-card {
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
}

@media (max-width: 576px) {
    .classes-container {
        padding: 0 10px;
    }
    
    .modern-class-card {
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
    // Add hover effects to progress bars
    $('.modern-class-card').hover(
        function() {
            $(this).find('.progress-fill').css('width', '85%');
        },
        function() {
            $(this).find('.progress-fill').css('width', '75%');
        }
    );
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush

@endsection 