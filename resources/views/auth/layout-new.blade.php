<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - WAIt</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    <style>
        body {
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            max-width: 450px;
            width: 100%;
        }
        .auth-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .bg-primary {
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%) !important;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #128C7E 0%, #25D366 100%);
            border: none;
            box-shadow: 0 4px 6px rgba(18, 140, 126, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #0e7268 0%, #1fb356 100%);
            box-shadow: 0 6px 12px rgba(18, 140, 126, 0.4);
            transform: translateY(-2px);
        }
        a {
            color: #128C7E;
        }
        a:hover {
            color: #0e7268;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 auth-card">
                <div class="auth-logo">
                    <img src="{{ asset('assets/logo-wait-3.svg') }}" alt="WAIt Logo" height="72">
                </div>
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
