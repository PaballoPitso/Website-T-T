<?php
session_start();

if ($_GET['id'] !== "146604") {
    die("Invalid reset link.");
}

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUser = $_POST['new_username'];
    $newPass = $_POST['new_password'];

    // For now, save to session (or later save to file/db)
    $_SESSION['admin_user'] = $newUser;
    $_SESSION['admin_pass'] = $newPass;

    $success = "Username and password updated successfully!";
}
?>

<h2>Reset Admin Login</h2>
<form method="POST">
    <label>New Username: <input type="text" name="new_username" required></label><br>
    <label>New Password: <input type="password" name="new_password" required></label><br>
    <button type="submit">Reset</button>
</form>

<p style="color: green;"><?= $success ?></p>
<a href="login.php">Go to Login</a>
