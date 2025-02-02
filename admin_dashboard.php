<?php
session_start();

// Redirect to login if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require 'includes/db.php';

// Fetch all events for the admin
$stmt = $pdo->prepare("SELECT * FROM events");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>

        <!-- Event Selection Form -->
        <form action="reports.php" method="GET" class="mb-4">
            <div class="mb-3">
                <label for="event_id" class="form-label">Select Event</label>
                <select class="form-select" id="event_id" name="id" required>
                    <option value="">Choose an event</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?php echo $event['id']; ?>">
                            <?php echo htmlspecialchars($event['name']); ?> (<?php echo htmlspecialchars($event['event_date']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Report</button>
        </form>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>