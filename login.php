<?php
require_once 'db_config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_type = $_POST['login_type'];
    $password = $_POST['password'];
    
    if ($login_type === 'email') {
        $email = htmlspecialchars(trim($_POST['email']));
        $stmt = $conn->prepare("SELECT * FROM students WHERE email = ? AND verified = 1");
        $stmt->bind_param("s", $email);
    } else {
        $phone = htmlspecialchars(trim($_POST['phone']));
        $stmt = $conn->prepare("SELECT * FROM students WHERE phone = ? AND verified = 1");
        $stmt->bind_param("s", $phone);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
        
        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];
            $_SESSION['student_email'] = $student['email'];
            $_SESSION['logged_in'] = true;
            
           
            header("Location: student_lists.php");
            exit();
        } else {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Login Failed</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { background-color: #f8f9fa; padding-top: 50px; }
                    .error-container { max-width: 500px; margin: 0 auto; text-align: center; }
                    .error-icon { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="error-container">
                        <div class="error-icon">❌</div>
                        <h2>Login Failed</h2>
                        <p class="lead">Invalid email/phone or password.</p>
                        <p>Please check your credentials and try again.</p>
                        <div class="mt-4">
                            <a href="login.html" class="btn btn-primary">Try Again</a>
                            <a href="index.html" class="btn btn-secondary">Return to Home</a>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';
        }
    } else {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Login Failed</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f8f9fa; padding-top: 50px; }
                .error-container { max-width: 500px; margin: 0 auto; text-align: center; }
                .error-icon { font-size: 4rem; color: #dc3545; margin-bottom: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="error-container">
                    <div class="error-icon">❌</div>
                    <h2>Login Failed</h2>
                    <p class="lead">Account not found or email not verified.</p>
                    <p>Please check your credentials or verify your email address.</p>
                    <div class="mt-4">
                        <a href="login.html" class="btn btn-primary">Try Again</a>
                        <a href="register.html" class="btn btn-success">Register</a>
                        <a href="index.html" class="btn btn-secondary">Return to Home</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: login.html");
    exit();
}
?>