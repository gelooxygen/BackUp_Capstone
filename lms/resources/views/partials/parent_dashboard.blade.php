{{-- message --}}
{!! Toastr::message() !!}

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

@if(isset($parent['error']))
    <!-- Error State -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem; opacity: 0.6;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-3">Dashboard Error</h4>
                    <p class="text-muted mb-4">{{ $parent['error'] }}</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Return to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
@elseif(isset($parent['noChildren']) && $parent['noChildren'])
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
@elseif(isset($parent['children']) && $parent['children']->count() > 0)
    <!-- Child Selector -->
    @if($parent['children']->count() > 1)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-3">Select Child:</h5>
                            <select class="form-control w-auto" id="childSelector" onchange="switchChild(this.value)">
                                @foreach($parent['children'] as $child)
                                    <option value="{{ $child->id }}" {{ $parent['selectedChild']->id == $child->id ? 'selected' : '' }}>
                                        {{ $child->full_name }} - {{ $child->sections->first() ? $child->sections->first()->name : 'No Section' }}
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
                            <img src="{{ $parent['selectedChild']->upload ? asset('storage/' . $parent['selectedChild']->upload) : URL::to('assets/img/profiles/avatar-01.jpg') }}" 
                                 alt="Student Photo" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="col-md-6">
                            <h4 class="mb-2">{{ $parent['selectedChild']->full_name }}</h4>
                            <p class="text-muted mb-1">
                                <strong>Student ID:</strong> {{ $parent['selectedChild']->admission_id }}
                            </p>
                            <p class="text-muted mb-1">
                                <strong>Section:</strong> {{ $parent['selectedChild']->sections->first() ? $parent['selectedChild']->sections->first()->name : 'Not Assigned' }}
                            </p>
                            <p class="text-muted mb-0">
                                <strong>Academic Year:</strong> {{ $parent['currentAcademicYear']->name ?? 'Not Set' }} - {{ $parent['currentSemester']->name ?? 'Not Set' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Widgets -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 d-flex">
            <div class="card bg-comman w-100">
                <div class="card-body">
                    <div class="db-widgets d-flex justify-content-between align-items-center">
                        <div class="db-info">
                            <h6>Attendance</h6>
                            <h3>{{ $parent['attendanceStats']->present_count ?? 0 }}/{{ $parent['attendanceStats']->total_records ?? 0 }}</h3>
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
                            <h6>Subjects</h6>
                            <h3>{{ $parent['enrollments'] ? $parent['enrollments']->count() : 0 }}</h3>
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
                            <h6>Grades</h6>
                            <h3>{{ $parent['grades'] ? $parent['grades']->count() : 0 }}</h3>
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
                            <h6>Events</h6>
                            <h3>{{ $parent['upcomingEvents'] ? $parent['upcomingEvents']->count() : 0 }}</h3>
                        </div>
                        <div class="db-icon">
                            <img src="{{ URL::to('assets/img/icons/student-icon-02.svg') }}" alt="Dashboard Icon">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional content can be added here based on the original parent dashboard -->
@endif

<script>
function switchChild(childId) {
    if (childId) {
        window.location.href = '{{ route("dashboard") }}?child_id=' + childId;
    }
}
</script>
