<?php
// Include database connection
require 'includes/db.php';

// Start the session
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the event ID from the URL
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    // If no event ID is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Fetch the event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute(['id' => $event_id]);
$event = $stmt->fetch();

if (!$event) {
    // If the event doesn't exist, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}

// Handle attendee registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Check if the event has reached maximum capacity
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM attendees WHERE event_id = :event_id");
    $stmt->execute(['event_id' => $event_id]);
    $attendee_count = $stmt->fetch()['count'];

    if ($attendee_count >= $event['max_capacity']) {
        // Redirect back to the event page
        header("Location: view_event.php?id=$event_id&error=true");
        exit();
        
    } else {
        // Insert attendee into the database
        $stmt = $pdo->prepare("INSERT INTO attendees (event_id, name, email) VALUES (:event_id, :name, :email)");
        $stmt->execute(['event_id' => $event_id, 'name' => $name, 'email' => $email]);

        // Redirect to dashboard with success parameter
        header("Location: dashboard.php?register_success=true");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
     <!-- Header -->
     <?php include 'includes/header.php'; ?>
     
    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($event['name']); ?></h1>
        <p><?php echo htmlspecialchars($event['description']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
        <p><strong>Max Capacity:</strong> <?php echo htmlspecialchars($event['max_capacity']); ?></p>

        <!-- Display error message if registration fails -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Attendee Registration Form -->
        <h2 class="mt-4">Register Attendee</h2>
        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>

    <!-- Toast Messages -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <!-- Error Toast -->
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto text-danger">Error</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Event has reached maximum capacity. Registration is closed.
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