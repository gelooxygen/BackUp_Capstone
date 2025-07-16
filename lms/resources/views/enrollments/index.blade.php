@extends('layouts.master')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-0">Enrollments</h2>
        <a href="{{ route('enrollments.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Add Enrollment
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollments as $enrollment)
                            <tr>
                                <td>{{ $enrollment->student->first_name ?? '-' }} {{ $enrollment->student->last_name ?? '' }}</td>
                                <td>{{ $enrollment->subject->subject_name ?? '-' }}</td>
                                <td>{{ $enrollment->academicYear->name ?? '-' }}</td>
                                <td>{{ $enrollment->semester->name ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-sm btn-outline-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('enrollments.destroy', $enrollment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No enrollments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 