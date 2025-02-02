<?php
require 'includes/auth.php';
require 'includes/db.php';

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    redirect('dashboard.php');
}

// Fetch event details to ensure the logged-in user owns the event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $event_id, 'user_id' => $_SESSION['user_id']]);
$event = $stmt->fetch();

if (!$event) {
    // If the event doesn't exist or doesn't belong to the user, redirect to dashboard
    redirect('dashboard.php');
}

// Delete the event from the database
$stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
$stmt->execute(['id' => $event_id]);

// Redirect to dashboard with a success flag
header("Location: dashboard.php?delete=1");
exit();
?>