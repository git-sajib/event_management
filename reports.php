<?php
// Start the session
session_start();

// Redirect to login if the user is not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'includes/db.php';

// Get the event ID from the URL
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    // If no event ID is provided, redirect to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch event details
$stmt = $pdo->prepare("SELECT name FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch();

if (!$event) {
    // If the event doesn't exist, redirect to the admin dashboard
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch attendees for the event
$stmt = $pdo->prepare("SELECT * FROM attendees WHERE event_id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$attendees = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $event['name'] . '_attendees.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, ['Name', 'Email', 'Registered At']);

// Write attendee data to CSV
foreach ($attendees as $attendee) {
    fputcsv($output, [
        $attendee['name'],
        $attendee['email'],
        $attendee['registered_at']
    ]);
}

// Close the output stream
fclose($output);
exit();
?>