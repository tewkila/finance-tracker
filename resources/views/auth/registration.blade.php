<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <title>Registration</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<span class="app-title">
    <a href="{{ url('/') }}" class="home-link">Finanss</a>
</span>

<!-- Registration Form -->
<div class="registration-form">
    <h2>Registration</h2>
    <form method="POST" action="{{ route('registration.submit') }}">
        @csrf <!-- CSRF token for security -->

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        @error('username')
        <span class="error" style="color: red;">{{ $message }}</span>
        @enderror

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        @error('email')
        <span class="error" style="color: red;">{{ $message }}</span>
        @enderror

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required
               pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long and include at least one uppercase letter and one number.">
        @error('password')
        <span class="error" style="color: red;">{{ $message }}</span>
        @enderror

        <button type="submit" name="registration">Register</button>
    </form>


</div>
</body>
</html>
