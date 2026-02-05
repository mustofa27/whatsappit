@extends('auth.layout-new')

@section('title', 'Register')

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white text-center">
        <h3 class="mb-0">Create New Account</h3>
    </div>

    <div class="card-body p-4">
        @if($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="{{ old('name') }}" required autofocus 
                       placeholder="Enter your name">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email') }}" required 
                       placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="Enter your password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <script>
                document.getElementById('togglePassword').addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            </script>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password_confirmation" 
                           name="password_confirmation" required 
                           placeholder="Confirm your password">
                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <script>
                document.getElementById('togglePasswordConfirm').addEventListener('click', function() {
                    const passwordInput = document.getElementById('password_confirmation');
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            </script>

            <button type="submit" class="btn btn-primary w-100">
                Sign Up
            </button>

            <div class="text-center mt-3">
                <p class="mb-0">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-decoration-none">Sign In</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
