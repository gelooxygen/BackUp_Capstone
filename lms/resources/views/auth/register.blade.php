
@extends('layouts.app')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}

<div class="glass-form">
    <div class="school-logo-top">
        <img src="{{ URL::to('assets/img/Logo.jpg') }}" alt="School Logo">
        <h1 class="school-name">Panorama Montessori School</h1>
        <p class="school-motto">Nurturing Minds, Building Futures</p>
    </div>
    
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Full Name <span class="login-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
            <span class="profile-views"><i class="fas fa-user-circle"></i></span>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label>Email Address <span class="login-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
            <span class="profile-views"><i class="fas fa-envelope"></i></span>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        {{-- insert defaults --}}
        <input type="hidden" class="image" name="image" value="photo_defaults.jpg">
        
        <div class="form-group">
            <label>Role Type <span class="login-danger">*</span></label>
            <select class="form-control @error('role_name') is-invalid @enderror" name="role_name" id="role_name" required>
                <option value="" selected disabled>Select your role</option>
                <option value="Admin" {{ old('role_name') == 'Admin' ? 'selected' : '' }}>Admin</option>
                <option value="Parent" {{ old('role_name') == 'Parent' ? 'selected' : '' }}>Parent</option>
                <option value="Teacher" {{ old('role_name') == 'Teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="Student" {{ old('role_name') == 'Student' ? 'selected' : '' }}>Student</option>
            </select>
            <span class="profile-views"><i class="fas fa-user-tag"></i></span>
            @error('role_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span id="roleError" class="text-danger" style="display:none; font-size:13px;">Please select a role.</span>
        </div>
        
        <div class="form-group">
            <label>Password <span class="login-danger">*</span></label>
            <input type="password" class="form-control pass-input @error('password') is-invalid @enderror" name="password" placeholder="Create a password" required>
            <span class="profile-views feather-eye toggle-password"></span>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <label>Confirm Password <span class="login-danger">*</span></label>
            <input type="password" class="form-control pass-confirm @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="Confirm your password" required>
            <span class="profile-views feather-eye reg-toggle-password"></span>
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        
        <div class="form-group">
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </div>
        
        <div class="text-center">
            <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 0;">
                Already have an account? 
                <a href="{{ route('login') }}" style="color: white; text-decoration: none; font-weight: 600;">Sign in here</a>
            </p>
        </div>
    </form>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        var role = document.getElementById('role_name').value;
        var error = document.getElementById('roleError');
        if (!role) {
            e.preventDefault();
            error.style.display = 'block';
        } else {
            error.style.display = 'none';
        }
    });
</script>
@endsection
