<?php
require 'includes/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // Store user role in session

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
        } else {
            header("Location: dashboard.php"); // Redirect to regular dashboard
        }
        exit();
    } else {
        echo "<div class='alert alert-danger'>Invalid credentials!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-light">
  <!-- Header -->
  <?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Bootstrap Card for Login Form -->
            <div class="card shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Login</h3>
                    
                    <!-- Login Form -->
                    <form method="POST" class="mt-3">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <!-- If not registered, show this link -->
                    <p class="mt-3 text-center">
                        Not registered? <a href="register.php">Register now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
 <!-- Footer -->
 <?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>

