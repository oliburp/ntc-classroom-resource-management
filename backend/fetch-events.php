<?php
include "db_connection.php"; // Include your DB connection

$sql = "SELECT schedules.schedule_id, schedules.subject, schedules.start_time, schedules.end_time, rooms.room_code
        FROM schedules
        JOIN rooms ON schedules.room_id = rooms.room_id";

$result = $conn->query($sql);
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['schedule_id'],
        'title' => $row['subject'],
        'start' => $row['start_time'],
        'end' => $row['end_time'],
        'room_code' => $row['room_code']  // Include room_code in the event data
    ];
}

echo json_encode($events);
