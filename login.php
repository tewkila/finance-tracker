<?php
require_once 'settings/config.php'; // Include database configuration
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start a new session or resume the existing one

$error = ''; // Variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $link->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare a select statement to fetch user id, email, password, and username
        $sql = "SELECT id, email, password, username FROM users WHERE email = ?";

        if ($stmt = $link->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $email);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if email exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($id, $email, $hashed_password, $username);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_regenerate_id();
                            $_SESSION['loggedin'] = true;
                            $_SESSION['id'] = $id;
                            $_SESSION['user_id'] = $id;
                            $_SESSION['email'] = $email;
                            $_SESSION['username'] = $username; // Store the username in the session

                            // Redirect user to dashboard page
                            header("location: dashboard.php");
                            exit;
                        } else {
                            $error = 'Invalid password.';
                        }
                    }
                } else {
                    $error = 'No account found with that email.';
                }
            } else {
                $error = "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    $link->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet" />
    <link href="assets/css/login-style.css" rel="stylesheet" />
    <title>Login</title>
</head>
<body>
<div class="header"></div>
<div class="user-info"></div>
<span class="app-title">
    <a href="index.php" class="home-link">Finanss</a>
</span>

<!-- Login Form -->
<div class="login-form">
    <h2>Login</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

        <!-- Error Message -->
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
