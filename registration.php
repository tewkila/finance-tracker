<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="./css/registration-style.css" rel="stylesheet" />
    <title>Registration</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<span class="app-title">
    <a href="index.php" class="home-link">Finanss</a>
  </span>

<!-- Registration Form -->
<div class="registration-form">
    <h2>Registration</h2>
    <form>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>

