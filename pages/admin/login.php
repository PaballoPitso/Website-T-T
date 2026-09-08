<?php
session_start();

// If already logged in
if (isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$login_error = "";

// Check for login attempt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Fetch stored credentials (reset or default)
    $storedUser = $_SESSION['admin_user'] ?? 'Tee&Tee_Admin';
    $storedPass = $_SESSION['admin_pass'] ?? 'Tee&Tee@1*';

    if ($user === $storedUser && $pass === $storedPass) {
        $_SESSION['admin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Admin Login</h2>

    <?php if ($login_error): ?>
        <p style="color:red;"><?= $login_error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" required></label><br><br>
        <label>Password: <input type="password" name="password" required></label><br>
        <button class="btn" type="submit">Login</button>
    </form>

    <p><a href="forgot.php">Forgot Username or Password?</a></p>
    <p><a href="../Contacts.html">Back to Home</a></p>

</body>
</html>
