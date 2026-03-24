<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GPLM School</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/favicon.ico') }}">
    <style>
        body {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to bottom, #d4ede7, #b2dfdb);
        }
        .navbar {
            width: 100%;
            background: white;
            padding: 0.5rem; /* Reduced height to half */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .login-card {
            width: 350px;
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .logo img {
            width: 80px;
        }
        .btn-primary {
            background: black;
            border: none;
        }
        .btn-primary:hover {
            background: #333;
        }
        .form-label {
            text-align: left;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">Portal</a>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="loginDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Login As
                </button>
                <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                    <li><a class="dropdown-item" href="/teacher/login">Teacher</a></li>
                    <li><a class="dropdown-item" href="/admin/login">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-card mt-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="logo mb-3">
            <img src="{{ URL::asset('assets/images/gpl_logo2.png') }}" alt="Logo">
        </div>
        <h3><strong>G P L M School</strong></h3>
        <h4>Student login</h4>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="admission_number">Admission Number</label>
                <input type="text" name="admission_number" class="form-control" required autofocus>

                {{-- <input type="email" name="email" class="form-control" placeholder="Enter your email" required> --}}
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                        👁️
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <a href="#" class="text-muted">Forgot Password?</a>
            </div>
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
