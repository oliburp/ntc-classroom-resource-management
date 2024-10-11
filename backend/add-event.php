<?php
session_start();
include "db_connection.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in."]);
    exit();
}

// Collect POST data
$user_id = $_POST['user_id']; // The user ID from the session
$room_id = $_POST['room_id'];
$subject = $_POST['subject'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

// Add additional validation if needed, e.g., check for empty fields, validate datetime format, etc.

// Insert event into the schedules table
$sql = "INSERT INTO schedules (user_id, room_id, subject, start_time, end_time) 
        VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $user_id, $room_id, $subject, $start_time, $end_time);

if ($stmt->execute()) {
    echo json_encode(["success" => "Event added successfully"]);
} else {
    echo json_encode(["error" => "Failed to add event: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
