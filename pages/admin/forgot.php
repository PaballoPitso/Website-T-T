<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../PHPMailer-master/src/Exception.php';
require __DIR__ . '/../PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer-master/src/SMTP.php';

$feedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminID = $_POST['admin_id'];
    $email = $_POST['email'];

    // Verify credentials
    if ($adminID === "146604" && $email === "pabloslaeger@gmail.com") {
        // Generate reset link (you could use a token system here too)
        $resetLink = "http://localhost/admin/reset.php?id=146604";

        // Send Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pabloslaeger@gmail.com';
            $mail->Password = 'lyzf kjgr ffxm uugt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('pabloslaeger@gmail.com', 'Tee n Tee Admin');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Admin Login Reset';
            $mail->Body = "Click the link below to reset your login credentials:<br></br><a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $feedback = "Reset link has been sent to your email.";
        } catch (Exception $e) {
            $feedback = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $feedback = "Invalid ID or email.";
    }
}
?>

<h2>Reset Login Credentials</h2>
<link rel="stylesheet" href="style.css">
<form method="POST">
    <label>Admin ID: <input type="text" name="admin_id" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <button class="btn" type="submit">Send Reset Link</button>
</form>

<p><?= $feedback ?></p>
<a href="login.php">Back to Login</a>
