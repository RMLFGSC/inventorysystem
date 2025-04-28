<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_number = $_POST['req_number'];
    $status = $_POST['status'];
    $date_issued = $_POST['date_issued'];
    $issued_by = $_POST['issued_by'];
    // $items = $_POST['items']; // Removed items as it's no longer needed

    // 1. Update request table
    $updateQuery = "UPDATE request SET status = ?, date_issued = ?, issued_by = ? WHERE req_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("isss", $status, $date_issued, $issued_by, $req_number);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to update request.']);
        exit;
    }

    // 3. Success response
    echo json_encode(['success' => true, 'message' => 'Request approved successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
