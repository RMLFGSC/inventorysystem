<?php

include("../conn.php"); 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_number = isset($_POST['req_number']) ? trim($_POST['req_number']) : '';
    $declined_by = isset($_POST['declined_by']) ? trim($_POST['declined_by']) : '';
    $decline_reason = isset($_POST['decline_reason']) ? trim($_POST['decline_reason']) : '';

    // Validate input
    if (!empty($req_number) && !empty($declined_by)) {
        $stmt = $conn->prepare("UPDATE request SET status = ?, declined_by = ?, decline_reason = ?, date_declined = NOW() WHERE req_number = ?");
        $declined_status = 2; 

        $stmt->bind_param("isss", $declined_status, $declined_by, $decline_reason, $req_number);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request declined successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to decline the request.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
