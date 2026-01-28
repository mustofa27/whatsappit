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
                <input type="password" class="form-control" id="password" name="password" 
                       required placeholder="Enter your password">
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" 
                       name="password_confirmation" required 
                       placeholder="Confirm your password">
            </div>

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
