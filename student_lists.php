<?php
  include 'db_config.php';


 $sql = "SELECT id, name, email, phone, program, verified FROM students ORDER BY id DESC";
 $result = $conn->query($sql);

 $students = array();
 if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
 }

 $conn->close();

?>











<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="index.html" class="btn btn-secondary mb-3">← Back to Home</a>
                
                <div class="table-container">
                    <div class="page-header">
                        <h2>Student List</h2>
                        <p class="text-muted">View and manage all registered students</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($students) && count($students) > 0) {
                                    foreach ($students as $student) {
                                        $status_badge = $student['verified'] ? 
                                            '<span class="badge verified-badge">Verified</span>' : 
                                            '<span class="badge pending-badge">Pending</span>';
                                        
                                        echo "
                                        <tr>
                                            <td>{$student['id']}</td>
                                            <td>{$student['name']}</td>
                                            <td>{$student['email']}</td>
                                            <td>{$student['phone']}</td>
                                            <td>{$student['program']}</td>
                                            <td>$status_badge</td>
                                            <td>
                                                <a href='edit.php?id={$student['id']}' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i> Edit</a>
                                                <a href='delete.php?id={$student['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this student?\");'><i class='bi bi-trash'></i> Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center'>No students found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="register.html" class="btn btn-primary">Register New Student</a>
                        <a href="index.html" class="btn btn-outline-secondary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>