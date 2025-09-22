<?php
include 'db_config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $stmt = $conn->prepare("UPDATE students SET verified = 1 WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Email Verified</title>
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
                        <h2>Email Verified Successfully!</h2>
                        <p class="lead">Your email address has been verified. Your account is now active.</p>
                        <div class="mt-4">
                            <a href="index.html" class="btn btn-primary">Go to Home Page</a>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            ';
        } else {
            echo "Invalid verification token.";
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo "No verification token provided.";
}
?>