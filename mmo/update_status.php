<?php
include("../conn.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request ID, new status, issued_by, date_issued, date_approved, and date_declined from the POST data
    $requestId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    $issued_by = isset($_POST['issued_by']) ? $_POST['issued_by'] : '';
    $date_issued = isset($_POST['date_issued']) ? $_POST['date_issued'] : '';
    $date_approved = ($status === 1) ? date('Y-m-d H:i:s') : null; // Set date_approved if status is 1
    $date_declined = ($status === 2) ? date('Y-m-d H:i:s') : null; // Set date_declined if status is 2

    // Check if the request ID and status are valid
    if ($requestId > 0 && ($status === 1 || $status === 2)) { 
        // Prepare the SQL statement to update the status, issued_by, date_issued, date_approved, and date_declined
        $query = "UPDATE request SET status = ?, issued_by = ?, date_issued = ?, date_approved = ?, date_declined = ? WHERE req_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issssi", $status, $issued_by, $date_issued, $date_approved, $date_declined, $requestId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?>