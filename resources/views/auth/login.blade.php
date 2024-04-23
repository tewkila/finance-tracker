<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/login-style.css') }}" rel="stylesheet">
    <title>Login</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<span class="app-title">
    <a href="{{ url('/') }}" class="home-link">Finanss</a>
</span>

<!-- Login Form -->
<div class="login-form">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <!-- Password -->
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit"><a href="{{ url('/dashboard') }}" class="btn btn-primary">Login</a>
        </button>
    </form>

</div>

</body>
</html>
