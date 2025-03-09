<?php
include 'Bakery.php';
session_start();
$login_error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT id, password FROM signup WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare to log the attempt
    $status = 'failure';

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username; // Store username in session as well
            $status = 'success';
            header('Location: Bakery.html');
            exit;
        } else {
            $login_error = "Invalid password.";
            
        }
    } else {
        $login_error = "User not found.";
    }

    // Log the login attempt
    $log_sql = "INSERT INTO login (username, status) VALUES (?, ?)";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("ss", $username, $status);
    $log_stmt->execute();
    $log_stmt->close();

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($login_error): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="signUp.php">Sign Up here</a></p>
    </div>
</body>
</html>
