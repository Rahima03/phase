<?php
require_once 'db_config.php';

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

 
    $stmt = $conn->prepare("SELECT id, name FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $name = $student['name'];

       
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour")); 

        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();

        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/phase1/reset_password.php?token=$token";

      
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'student.mgment.system@gmail.com';
            $mail->Password = 'rwnulciywetiuyht';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('student.mgment.system@gmail.com', 'Student Management System');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";

            $mail->Body = "
                <h2>Password Reset</h2>
                <p>Hello $name,</p>
                <p>You requested to reset your password. Click the link below to set a new password:</p>
                <p><a href='$reset_link' style='background-color: #0062cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>Or copy this link into your browser:<br>$reset_link</p>
                <p>If you didn’t request this, you can safely ignore this email.</p>
                <br>
                <p>Best regards,<br>Student Management Team</p>
            ";

            $mail->AltBody = "Hello $name,\n\nYou requested to reset your password. Use the link below:\n$reset_link\n\nIf you didn’t request this, ignore this email.\n\nBest regards,\nStudent Management Team";

            $mail->send();
            echo "✅ A password reset link has been sent to your email.";
        } catch (Exception $e) {
            echo "⚠️ Error sending email: {$mail->ErrorInfo}";
        }

    } else {
        echo "❌ No account found with that email.";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: forgot_password.html");
    exit();
}
?>
