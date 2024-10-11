<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to find the user by username
    $stmt = $conn->prepare('SELECT user_id, username, password, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, now verify the password
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Password is correct
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            $logQuery = "INSERT INTO user_activity_log (user_id, login_time) VALUES (?, NOW())";
            $logStmt = $conn->prepare($logQuery);
            $logStmt->bind_param("i", $user['user_id']);
            $logStmt->execute();

            header('Location: ../pages/dashboard.php');

            exit;
        } else {
            // Invalid password
            echo "Invalid password!";
        }
    } else {
        // No user found
        echo "No user found with that username!";
    }
}
