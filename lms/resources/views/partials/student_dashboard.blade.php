{{-- message --}}
{!! Toastr::message() !!}

<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="page-sub-header">
                <h3 class="page-title">Welcome {{ $student['student']->first_name ?? $student['student']->name ?? 'Student' }}!</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Student</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-sm-6 col-12 d-flex">
        <div class="card bg-comman w-100">
            <div class="card-body">
                <div class="db-widgets d-flex justify-content-between align-items-center">
                    <div class="db-info">
                        <h6>All Courses</h6>
                        <h3>{{ $student['enrollments'] ? $student['enrollments']->count() : 0 }}/{{ $student['enrollments'] ? $student['enrollments']->count() : 0 }}</h3>
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
                        <h6>All Projects</h6>
                        <h3>40/60</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{URL::to('assets/img/icons/teacher-icon-02.svg')}}" alt="Dashboard Icon">
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
                        <h6>Test Attended</h6>
                        <h3>30/50</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{URL::to('assets/img/icons/student-icon-01.svg')}}" alt="Dashboard Icon">
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
                        <h6>Test Passed</h6>
                        <h3>15/20</h3>
                    </div>
                    <div class="db-icon">
                        <img src="{{URL::to('assets/img/icons/student-icon-02.svg')}}" alt="Dashboard Icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-12 col-xl-8">
        <div class="card flex-fill comman-shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-6">
                        <h5 class="card-title">Today's Lesson</h5>
                    </div>
                    <div class="col-6">
                        <ul class="chart-list-out">
                            <li>
                                <span class="circle-blue"></span>
                                <span class="circle-gray"></span>
                                <span class="circle-gray"></span>
                            </li>
                            <li class="star-menus"><a href="javascript:;"><i
                                        class="fas fa-ellipsis-v"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th>Lesson</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student['enrollments'] as $enrollment)
                            <tr>
                                <td>{{ $enrollment->subject->subject_name ?? 'Subject' }}</td>
                                <td>{{ $enrollment->subject->description ?? 'Lesson Description' }}</td>
                                <td>{{ $enrollment->academicYear->name ?? 'N/A' }} - {{ $enrollment->semester->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-success">Active</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    <i class="fas fa-info-circle me-2"></i>No enrollments available
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-12 col-xl-4 d-flex">
        <div class="card flex-fill comman-shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-12">
                        <h5 class="card-title">My Courses</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="course-list">
                                            @forelse($student['enrollments'] as $enrollment)
                    <div class="course-item d-flex align-items-center mb-3">
                        <div class="course-icon me-3">
                            <i class="fas fa-book text-primary fs-4"></i>
                        </div>
                        <div class="course-info">
                            <h6 class="mb-1">{{ $enrollment->subject->subject_name ?? 'Subject' }}</h6>
                            <p class="text-muted mb-0 small">{{ $enrollment->academicYear->name ?? 'N/A' }} - {{ $enrollment->semester->name ?? 'N/A' }}</p>
                        </div>
                        <div class="course-status ms-auto">
                            <span class="badge bg-success">Active</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-info-circle me-2"></i>No courses enrolled
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
