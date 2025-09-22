<?php
require_once 'db_config.php';

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $program = htmlspecialchars(trim($_POST['program']));
    
    $verification_token = bin2hex(random_bytes(32));
    
    $stmt = $conn->prepare("INSERT INTO students (name, email, phone, program, verification_token) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $program, $verification_token);
    
    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

          
            $mail->Username = 'student.mgment.system@gmail.com';
            $mail->Password = 'rwnulciywetiuyht';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('student.mgment.system@gmail.com', 'Student Management System');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';
            
            $verification_link = "http://" . $_SERVER['HTTP_HOST'] . "/phase1/verify.php?token=$verification_token";

            $mail->Body = "
                <h2>Email Verification</h2>
                <p>Hello $name,</p>
                <p>Thank you for registering with our Student Management System. Please verify your email address by clicking the link below:</p>
                <p><a href='$verification_link' style='background-color: #0062cc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Verify Email Address</a></p>
                <p>Or copy and paste this URL into your browser:<br>$verification_link</p>
                <p>If you didn't create an account with us, please ignore this email.</p>
                <br>
                <p>Best regards,<br>Student Management Team</p>
            ";
            
            $mail->AltBody = "Hello $name,\n\nThank you for registering with our Student Management System. Please verify your email by visiting this link: $verification_link\n\nIf you didn't create an account with us, please ignore this email.\n\nBest regards,\nStudent Management Team";
            
            $mail->send();
            
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Registration Successful</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { background-color: #f8f9fa; padding-top: 50px; }
                    .success-container { max-width: 600px; margin: 0 auto; text-align: center; }
                    .success-icon { font-size: 4rem; color: #28a745; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="success-container">
                        <div class="success-icon">✅</div>
                        <h2>Registration Successful!</h2>
                        <p class="lead">Thank you for registering. A verification email has been sent to <strong>'.$email.'</strong>.</p>
                        <p>Please check your inbox and click the verification link to complete the registration process.</p>
                        <div class="mt-4">
                            <a href="index.html" class="btn btn-primary">Return to Home</a>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';
            
        } catch (Exception $e) {
            echo "<p>Registration was successful but the verification email could not be sent. Error: {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<p>Error: Could not register student. Please try again.</p>";
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: register.html");
    exit();
}
?>
