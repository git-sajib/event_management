<?php
$host = 'localhost';
$dbname = 'texagigc_event_management';
$username = 'texagigc_sam';
$password = 'texagigc_sam';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>