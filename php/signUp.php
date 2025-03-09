 <?php
include 'Bakery.php';
session_start();
$signup_error = '';
$signup_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $signup_error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $signup_error = "Passwords do not match.";
    } else {
        $sql = "SELECT * FROM signup WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $signup_error = "Username already taken.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO signup (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $username, $hashed_password);

            if ($stmt->execute()) {
                $signup_success = true;
                header('location:login.php');
            } else {
                $signup_error = "Error registering user.";
            }
            $stmt->close();
        }
        $stmt->close();
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h2>Sign Up</h2>
        <?php if ($signup_error): ?>
            <p class="error"><?php echo $signup_error; ?></p>
        <?php endif; ?>
        <?php if ($signup_success): ?>
            <p class="success">Sign up successful. You can now log in.</p>
        <?php endif; ?>
        <form action="signUp.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
<?php
    $conn->close();
?>