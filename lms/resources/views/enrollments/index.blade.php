@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Enrollment Management</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Enrollments</li>
                    </ul>
                </div>
                <div class="col-auto text-right float-right ml-auto">
                    <a href="{{ route('enrollments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New User
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">All Enrollments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center table-borderless table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Section</th>
                                        <th>Academic Year</th>
                                        <th>Semester</th>
                                        <th>Status</th>
                                        <th>Enrollment Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm mr-2">
                                                    <img src="{{ asset('assets/img/profiles/avatar-01.jpg') }}" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $enrollment->student->first_name ?? 'N/A' }} {{ $enrollment->student->last_name ?? '' }}</h6>
                                                    <small class="text-muted">{{ $enrollment->student->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $enrollment->subject->subject_name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $enrollment->section->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $enrollment->academicYear->name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->semester->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($enrollment->status === 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif($enrollment->status === 'inactive')
                                                <span class="badge badge-warning">Inactive</span>
                                            @elseif($enrollment->status === 'completed')
                                                <span class="badge badge-info">Completed</span>
                                            @elseif($enrollment->status === 'dropped')
                                                <span class="badge badge-danger">Dropped</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $enrollment->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $enrollment->enrollment_date ? $enrollment->enrollment_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <div class="actions">
                                                <a href="{{ route('enrollments.show', $enrollment->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('enrollments.edit', $enrollment->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('enrollments.destroy', $enrollment->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this enrollment?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-info-circle fs-1 mb-3"></i>
                                            <h6>No enrollments found</h6>
                                            <p class="small">Create your first enrollment by clicking the "Create New User" button above.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($enrollments->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $enrollments->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 