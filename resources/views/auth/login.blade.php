<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - {{ config('app.name') }}</title>
    
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('newdesign/assets/vendor/fonts/fontawesome/css/fontawesome-all.css') }}">
    <link rel="stylesheet" href="{{ asset('newdesign/assets/libs/css/style.css') }}">
    
    <style>
        body {
            background-color: #f8fafc !important;
        }
        
        .splash-container {
            max-width: 500px;
            margin: 0 auto;
            padding-top: 100px;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #ffffff !important;
        }
        
        .card-header {
            background-color: #1a202c !important;
            color: #c1ec4a !important;
            border-bottom: 1px solid #1a202c !important;
        }
        
        .card-body {
            background-color: #ffffff !important;
            color: #1a202c !important;
        }
        
        .card-footer {
            background-color: #1a202c !important;
            color: #a0aec0 !important;
            border-top: 1px solid #1a202c !important;
        }
        
        .form-control {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #1a202c !important;
        }
        
        .form-control:focus {
            background-color: #ffffff !important;
            border-color: #00d4aa !important;
            color: #1a202c !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 212, 170, 0.25) !important;
        }
        
        .form-label {
            color: #1a202c !important;
        }
        
        .btn-primary {
            background-color: #C1EC4A !important;
            border-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 6px;
        }
        
        .btn-primary:hover {
            background-color: #B0D93F !important;
            border-color: #B0D93F !important;
            color: #1A202C !important;
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }
    </style>
</head>

<body>
    <div class="splash-container">
        <div class="card">
            <div class="card-header text-center">
                <h3 class="mb-1">{{ config('app.name') }}</h3>
                <p>Please enter your credentials to login</p>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               id="password" name="password" required
                               placeholder="Enter your password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label" for="remember">Remember Me</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg btn-block">
                        Login
                    </button>
                </form>
            </div>
            <div class="card-footer bg-white">
                <p class="text-center mb-0">
                    <small>Default credentials: admin@flettons.com / password</small>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('newdesign/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
</body>
</html>

