<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id      = $_POST['id'];
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $program = htmlspecialchars(trim($_POST['program']));
    $password = trim($_POST['password']); 
    $confirm_password = trim($_POST['confirm_password']); 

    if (!empty($password)) {
        if ($password !== $confirm_password) {
            die("Error: Passwords do not match. <a href='edit.php?id=$id'>Go back</a>");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, program=?, password=? WHERE id=?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $program, $hashedPassword, $id);
    } else {
        $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, program=? WHERE id=?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $program, $id);
    }

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: student_lists.php");
        exit();
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} 
elseif (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT id, name, email, phone, program FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        echo "Student not found.";
        exit();
    }

    $stmt->close();
    $conn->close();
} 
else {
    header("Location: student_lists.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="student_lists.php" class="btn btn-secondary mb-3">← Back to Student List</a>
                
                <div class="form-container">
                    <div class="page-header">
                        <h2>Edit Student Information</h2>
                        <p class="text-muted">Update student details as needed</p>
                    </div>

                    <form action="edit.php" method="POST">
                        <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($student['name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($student['email']); ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($student['phone']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="program" class="form-label">Program *</label>
                                <input type="text" class="form-control" id="program" name="program" 
                                       value="<?php echo htmlspecialchars($student['program']); ?>" required>
                                <div class="form-text">e.g., Computer Science, Business Administration, Engineering</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">New Password (leave blank to keep old one)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('confirm_password', this)">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="student_lists.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
