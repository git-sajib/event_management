<?php
header('Content-Type: application/json');

require '../includes/db.php';

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    echo json_encode(['error' => 'Event ID is required']);
    exit();
}

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo json_encode(['error' => 'Event not found']);
    exit();
}

// Fetch attendees for the event
$stmt = $pdo->prepare("SELECT * FROM attendees WHERE event_id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return event details and attendees in JSON format
echo json_encode([
    'event' => $event,
    'attendees' => $attendees
]);
?>