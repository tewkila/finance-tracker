<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/login-style.css" rel="stylesheet" />
    <title>Login</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<span class="app-title">
    <a href="index.html" class="home-link">Finanss</a>
  </span>

<!-- Login Form -->
<div class="login-form">
    <h2>Login</h2>
    <form>
        <label for="username">Username or Email:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        
        <a href="password-reset.php" class="forgot-password">Forgot Password?</a>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
