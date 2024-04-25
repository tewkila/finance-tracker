<?php
require_once 'config.php';  // Include the database connection

$errors = [];  // Initialize an array to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $link->real_escape_string(strip_tags(trim($_POST['username'])));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    // Validate password strength
    $passwordPattern = "/^(?=.*[A-Z])(?=.*\d).{8,}$/";
    if (!preg_match($passwordPattern, $password)) {
        $errors['password'] = "Password must be at least 8 characters long, include at least one uppercase letter, and at least one number.";
    }

    if ($email === false) {
        $errors['email'] = "Please include an '@' in the email address.";
    }

    // Check if there are any errors
    if (count($errors) === 0) {
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $link->prepare($sql);

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            echo "<p class='success'>Registration successful!</p>";
        } else {
            echo "<p class='error'>Error: " . $stmt->error . "</p>";
        }

        // Close statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <!-- Display error if username validation fails -->
        <?php if (!empty($errors['username'])) echo "<p class='error'>{$errors['username']}</p>"; ?>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <!-- Display error if email validation fails -->
        <?php if (!empty($errors['email'])) echo "<p class='error'>{$errors['email']}</p>"; ?>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required 
               pattern="(?=.*\d)(?=.*[A-Z]).{8,}" title="Password must be at least 8 characters long and include at least one uppercase letter and one number.">
        <!-- Display error if password validation fails -->
        <?php if (!empty($errors['password'])) echo "<p class='error'>{$errors['password']}</p>"; ?>

        <button type="submit" name="register">Register</button>
    </form>
</div>

<style>
.error {
    color: red;
    font-size: 0.8em;
}
</style>

</body>
</html>
