<?php
session_start();
include 'db_connection.php'; // Include your database connection

if (isset($_SESSION['auth_user'])) {
    $notification_count_query = "SELECT COUNT(DISTINCT req_number) AS unread_count FROM request WHERE is_read = 0 AND is_posted = 1";
    $notification_count_result = mysqli_query($conn, $notification_count_query);
    $unread_notification_count = mysqli_fetch_assoc($notification_count_result)['unread_count'] ?? 0;

    echo json_encode(['unread_count' => $unread_notification_count]);
} else {
    echo json_encode(['unread_count' => 0]);
}
?> 