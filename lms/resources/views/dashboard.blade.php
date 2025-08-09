@extends('layouts.master')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="page-wrapper">
    <div class="content container-fluid">
        @if($user->role_name === 'Admin')
            @include('partials.admin_dashboard')
        @elseif($user->role_name === 'Teacher')
            @include('partials.teacher_dashboard')
        @elseif($user->role_name === 'Student')
            @include('partials.student_dashboard')
        @elseif($user->role_name === 'Parent')
            @include('partials.parent_dashboard')
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem; opacity: 0.6;"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-3">Role Not Recognized</h4>
                            <p class="text-muted mb-4">Your user role is not recognized. Please contact the administrator.</p>
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
