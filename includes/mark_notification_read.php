<?php
session_start();
include 'db_connection.php'; // Include your database connection file

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Check if req_id is set
    if (isset($data['req_id'])) {
        $req_id = $data['req_id'];
        $user_id = $_SESSION['auth_user']['user_id'] ?? null; // Ensure user_id is set

        if ($user_id !== null) { // Check if user_id is valid
            // Prepare the SQL statement to mark the notification as read
            $query = "UPDATE request SET is_read = 1 WHERE req_id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $req_id, $user_id);

            // Execute the statement
            if ($stmt->execute()) {
                // Successfully marked as read
                echo json_encode(['status' => 'success']);
            } else {
                // Error occurred
                echo json_encode(['status' => 'error', 'message' => 'Failed to update notification']);
            }

            $stmt->close();
        } else {
            // Invalid user_id
            echo json_encode(['status' => 'error', 'message' => 'Invalid user']);
        }
    } else {
        // Invalid request
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    // Invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
