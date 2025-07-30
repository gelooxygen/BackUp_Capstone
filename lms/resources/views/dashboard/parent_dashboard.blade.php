@extends('layouts.master')
@section('content')
    {{-- message --}}
    {!! Toastr::message() !!}
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Parent Dashboard</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active">Parent Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($error))
                <!-- Error State -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem; opacity: 0.6;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">Dashboard Error</h4>
                                <p class="text-muted mb-4">{{ $error }}</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Return to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(isset($noChildren) && $noChildren)
                <!-- No Children State -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-users text-primary" style="font-size: 4rem; opacity: 0.6;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">No Children Linked</h4>
                                <p class="text-muted mb-4">No students are currently linked to your parent account. Please contact the school administration to link your children to your account.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Return to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(isset($children) && $children->count() > 0)
                <!-- Child Selector -->
                @if($children->count() > 1)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 me-3">Select Child:</h5>
                                        <select class="form-control w-auto" id="childSelector" onchange="switchChild(this.value)">
                                            @foreach($children as $child)
                                                <option value="{{ $child->id }}" {{ $selectedChild->id == $child->id ? 'selected' : '' }}>
                                                    {{ $child->full_name }} - {{ $child->sections->first()->name ?? 'No Section' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Student Info Card -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center">
                                        <img src="{{ $selectedChild->upload ? asset('storage/' . $selectedChild->upload) : URL::to('assets/img/profiles/avatar-01.jpg') }}" 
                                             alt="Student Photo" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="mb-2">{{ $selectedChild->full_name }}</h4>
                                        <p class="text-muted mb-1">
                                            <strong>Student ID:</strong> {{ $selectedChild->admission_id }}
                                        </p>
                                        <p class="text-muted mb-1">
                                            <strong>Section:</strong> {{ $selectedChild->sections->first()->name ?? 'Not Assigned' }}
                                        </p>
                                        <p class="text-muted mb-0">
                                            <strong>Academic Year:</strong> {{ $currentAcademicYear->year ?? 'Not Set' }} - {{ $currentSemester->name ?? 'Not Set' }}
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('parent.child.grades', $selectedChild->id) }}" class="btn btn-outline-primary mb-2">
                                                <i class="fas fa-chart-line me-2"></i>View Detailed Grades
                                            </a>
                                            <a href="{{ route('parent.child.attendance', $selectedChild->id) }}" class="btn btn-outline-info mb-2">
                                                <i class="fas fa-calendar-check me-2"></i>View Attendance
                                            </a>
                                            <a href="{{ route('parent.child.activities', $selectedChild->id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-tasks me-2"></i>View Activities
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Performance Insights -->
                @if(count($performanceInsights) > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Performance Insights
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($performanceInsights as $insight)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="alert alert-{{ $insight['color'] }} d-flex align-items-center">
                                                    <i class="{{ $insight['icon'] }} me-2"></i>
                                                    <div>
                                                        <strong>{{ $insight['title'] }}</strong><br>
                                                        <small>{{ $insight['message'] }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6>Current GPA</h6>
                                        <h3>{{ $selectedChild->getCurrentGpa($currentAcademicYear->id ?? null, $currentSemester->id ?? null)->gpa ?? 'N/A' }}</h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="{{ URL::to('assets/img/icons/student-icon-01.svg') }}" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6>Attendance Rate</h6>
                                        <h3>{{ $attendance->count() > 0 ? round(($attendance->where('status', 'present')->count() / $attendance->count()) * 100, 1) : 0 }}%</h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="{{ URL::to('assets/img/icons/teacher-icon-01.svg') }}" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6>Pending Activities</h6>
                                        <h3>{{ $activities->where('due_date', '>', now())->count() }}</h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="{{ URL::to('assets/img/icons/teacher-icon-02.svg') }}" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6>Recent Grades</h6>
                                        <h3>{{ $grades->count() }}</h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="{{ URL::to('assets/img/icons/student-icon-02.svg') }}" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Grades Overview -->
                    <div class="col-12 col-lg-6">
                        <div class="card flex-fill comman-shadow">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h5 class="card-title">Recent Grades</h5>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('parent.child.grades', $selectedChild->id) }}" class="float-end">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($grades->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Component</th>
                                                    <th>Score</th>
                                                    <th>Percentage</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($grades->take(5) as $grade)
                                                    <tr>
                                                        <td>{{ $grade->subject->subject_name ?? 'N/A' }}</td>
                                                        <td>{{ $grade->component->name ?? 'N/A' }}</td>
                                                        <td>{{ $grade->score }}/{{ $grade->max_score }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $grade->percentage >= 90 ? 'success' : ($grade->percentage >= 75 ? 'info' : 'warning') }}">
                                                                {{ number_format($grade->percentage, 1) }}%
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-chart-line text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No grades available yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Summary -->
                    <div class="col-12 col-lg-6">
                        <div class="card flex-fill comman-shadow">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h5 class="card-title">Attendance Summary</h5>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('parent.child.attendance', $selectedChild->id) }}" class="float-end">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($attendance->count() > 0)
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <div class="bg-success text-white rounded p-3">
                                                <h4>{{ $attendance->where('status', 'present')->count() }}</h4>
                                                <small>Present</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-danger text-white rounded p-3">
                                                <h4>{{ $attendance->where('status', 'absent')->count() }}</h4>
                                                <small>Absent</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-warning text-white rounded p-3">
                                                <h4>{{ $attendance->where('status', 'late')->count() }}</h4>
                                                <small>Late</small>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="bg-info text-white rounded p-3">
                                                <h4>{{ $attendance->where('status', 'excused')->count() }}</h4>
                                                <small>Excused</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-calendar-check text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No attendance records available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activities and Submissions -->
                <div class="row">
                    <!-- Recent Activities -->
                    <div class="col-12 col-lg-8">
                        <div class="card flex-fill comman-shadow">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h5 class="card-title">Recent Activities</h5>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('parent.child.activities', $selectedChild->id) }}" class="float-end">View All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($activities->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Activity</th>
                                                    <th>Subject</th>
                                                    <th>Due Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($activities->take(8) as $activity)
                                                    @php
                                                        $submission = $submissions->where('activity_id', $activity->id)->first();
                                                        $isOverdue = $activity->due_date < now() && !$submission;
                                                    @endphp
                                                    <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                @if($isOverdue)
                                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                                @endif
                                                                <div>
                                                                    <strong>{{ $activity->title }}</strong>
                                                                    <br><small class="text-muted">{{ Str::limit($activity->instructions, 50) }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $activity->lesson->subject->subject_name ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="{{ $isOverdue ? 'text-danger' : '' }}">
                                                                {{ $activity->due_date->format('M d, Y') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($submission)
                                                                <span class="badge bg-success">Submitted</span>
                                                            @elseif($isOverdue)
                                                                <span class="badge bg-danger">Overdue</span>
                                                            @else
                                                                <span class="badge bg-warning">Pending</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No activities available</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Submissions -->
                    <div class="col-12 col-lg-4">
                        <div class="card flex-fill comman-shadow">
                            <div class="card-header">
                                <h5 class="card-title">Recent Submissions</h5>
                            </div>
                            <div class="card-body">
                                @if($submissions->count() > 0)
                                    <div class="timeline">
                                        @foreach($submissions->take(6) as $submission)
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-{{ $submission->status == 'graded' ? 'success' : 'info' }}"></div>
                                                <div class="timeline-content">
                                                    <h6 class="timeline-title">{{ $submission->activity->title }}</h6>
                                                    <p class="timeline-text">
                                                        <small class="text-muted">
                                                            {{ $submission->activity->lesson->subject->subject_name ?? 'N/A' }}
                                                        </small>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-{{ $submission->status == 'graded' ? 'success' : 'info' }}">
                                                            {{ ucfirst($submission->status) }}
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $submission->submitted_at->format('M d') }}
                                                        </small>
                                                    </div>
                                                    @if($submission->status == 'graded' && $submission->grades->count() > 0)
                                                        <div class="mt-2">
                                                            <strong>Score: {{ $submission->total_score }}/{{ $submission->max_score }}</strong>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-upload text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">No submissions yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Fallback for when children is not set or empty -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem; opacity: 0.6;"></i>
                                </div>
                                <h4 class="fw-bold text-dark mb-3">Unable to Load Dashboard</h4>
                                <p class="text-muted mb-4">There was an issue loading the dashboard. Please try refreshing the page or contact support.</p>
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>Return to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function switchChild(childId) {
        window.location.href = '{{ route("parent/dashboard") }}?child_id=' + childId;
    }
</script>
@endsection     