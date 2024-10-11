<?php
include "db_connection.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eventId = $_POST['id'];
    $subject = $_POST['subject'];
    $room_id = $_POST['room_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Validate the incoming data
    if (empty($eventId) || empty($subject) || empty($room_id) || empty($start_time) || empty($end_time)) {
        echo json_encode(['error' => 'Missing fields']);
        exit;
    }

    // Update the event in the database
    $sql = "UPDATE schedules SET subject = ?, room_id = ?, start_time = ?, end_time = ? WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sissi', $subject, $room_id, $start_time, $end_time, $eventId);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Event updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update event']);
    }

    $stmt->close();
    $conn->close();
}
?>
