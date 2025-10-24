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
            padding: 2rem 1.5rem 1.5rem 1.5rem !important;
        }
        
        .card-header h3 {
            font-size: 2.2rem !important;
            font-weight: 700 !important;
            margin-bottom: 0.5rem !important;
        }
        
        .card-header p {
            font-size: 1.1rem !important;
            margin-bottom: 0 !important;
            opacity: 0.9 !important;
        }
        
        .card-body {
            background-color: #ffffff !important;
            color: #1a202c !important;
            padding: 2rem 1.5rem !important;
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
            font-size: 1rem !important;
            padding: 0.875rem 1rem !important;
            border-radius: 8px !important;
        }
        
        .form-control:focus {
            background-color: #ffffff !important;
            /* border-color: #00d4aa !important; */
            color: #1a202c !important;
            box-shadow: none !important;
        }
        
        .form-label {
            color: #1a202c !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.75rem !important;
        }
        
        .btn-primary {
            background-color: #C1EC4A !important;
            border-color: #C1EC4A !important;
            color: #1A202C !important;
            font-weight: 600 !important;
            font-size: 1.1rem !important;
            padding: 14px 28px !important;
            border-radius: 8px !important;
            margin-top: 1rem !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-primary:hover {
            background-color: #B0D93F !important;
            border-color: #B0D93F !important;
            color: #1A202C !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(193, 236, 74, 0.3) !important;
        }
        
        .custom-control-label {
            font-size: 1rem !important;
            color: #1a202c !important;
        }
        
        .form-group {
            margin-bottom: 1.5rem !important;
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
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
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
                    
                    <button type="submit" class="btn btn-primary btn-block">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('newdesign/assets/vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('newdesign/assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
</body>
</html>

