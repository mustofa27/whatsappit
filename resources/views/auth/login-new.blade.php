@extends('auth.layout-new')

@section('title', 'Login')

@section('content')
<div class="card shadow">
    <div class="card-header bg-primary text-white text-center">
        <h3 class="mb-0">Sign In to WhatsApp IT</h3>
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

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="{{ old('email') }}" required autofocus 
                       placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" 
                       required placeholder="Enter your password">
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Sign In
            </button>

            <div class="text-center mt-3">
                <p class="mb-0">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-decoration-none">Sign Up</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
