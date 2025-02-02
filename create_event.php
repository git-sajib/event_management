<?php
require 'includes/auth.php';
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $max_capacity = $_POST['max_capacity'];

    $stmt = $pdo->prepare("INSERT INTO events (user_id, name, description, event_date, max_capacity) VALUES (:user_id, :name, :description, :event_date, :max_capacity)");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'name' => $name,
        'description' => $description,
        'event_date' => $event_date,
        'max_capacity' => $max_capacity
    ]);

    // Redirect to dashboard with success flag
    header("Location: dashboard.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Create Event</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <div class="mb-3">
                <label for="event_date" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" required>
            </div>
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>

 <!-- Footer -->
 <?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>


