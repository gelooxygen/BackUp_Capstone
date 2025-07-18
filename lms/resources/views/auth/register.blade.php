
@extends('layouts.app')
@section('content')
    <div class="login-right">
        <div class="login-right-wrap">
            <h1>Sign Up</h1>
            <p class="account-subtitle">Enter details to create your account</p>
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Full Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name">
                    <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                </div>
                <div class="form-group">
                    <label>Email <span class="login-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">
                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                </div>
                {{-- insert defaults --}}
                <input type="hidden" class="image" name="image" value="photo_defaults.jpg">
                <div class="form-group local-forms">
                    <label>Role Name <span class="login-danger">*</span></label>
                    <select class="form-control select @error('role_name') is-invalid @enderror" name="role_name" id="role_name" required>
                        <option value="" selected disabled hidden>Role Type</option>
                        <option value="Admin">Admin</option>
                        <option value="Parent">Parent</option>
                        <option value="Teacher">Teacher</option>
                        <option value="Student">Student</option>
                    </select>
                    @error('role_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                    <span id="roleError" class="text-danger" style="display:none; font-size:13px;">Please select a role.</span>
                </div>
                
                <div class="form-group">
                    <label>Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control pass-input  @error('password') is-invalid @enderror" name="password">
                    <span class="profile-views feather-eye toggle-password"></span>
                </div>
                <div class="form-group">
                    <label>Confirm password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control pass-confirm @error('password_confirmation') is-invalid @enderror" name="password_confirmation">
                    <span class="profile-views feather-eye reg-toggle-password"></span>
                </div>
                <div class=" dont-have">Already Registered? <a href="{{ route('login') }}">Login</a></div>
                <div class="form-group mb-0">
                    <button class="btn btn-primary btn-block" type="submit">Register</button>
                </div>
            </form>
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
            <div class="login-or">
                <span class="or-line"></span>
                <span class="span-or">or</span>
            </div>
            <div class="social-login">
                <a href="#"><i class="fab fa-google-plus-g"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>
@endsection
