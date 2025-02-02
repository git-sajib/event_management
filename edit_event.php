<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'includes/db.php';

// Get the event ID from the URL
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    // If no event ID is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Fetch the event details to ensure the logged-in user owns the event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $event_id, 'user_id' => $_SESSION['user_id']]);
$event = $stmt->fetch();

if (!$event) {
    // If the event doesn't exist or doesn't belong to the user, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $max_capacity = $_POST['max_capacity'];

    // Update the event in the database
    $stmt = $pdo->prepare("UPDATE events SET name = :name, description = :description, event_date = :event_date, max_capacity = :max_capacity WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'description' => $description,
        'event_date' => $event_date,
        'max_capacity' => $max_capacity,
        'id' => $event_id
    ]);

    // Redirect to the dashboard with a success flag
    header("Location: dashboard.php?edit=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
     <!-- Header -->
     <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Edit Event</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="event_date" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="max_capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" value="<?php echo htmlspecialchars($event['max_capacity']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Event</button>
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
 <!-- Footer -->
 <?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>

