<?php
include("../conn.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request ID, new status, issued_by, date_issued, date_approved, date_declined from the POST data
    $requestId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    $issued_by = isset($_POST['issued_by']) ? $_POST['issued_by'] : '';
    $date_issued = isset($_POST['date_issued']) ? $_POST['date_issued'] : '';
    $date_approved = ($status === 1) ? date('Y-m-d H:i:s') : null; // Set date_approved if status is 1
    $date_declined = ($status === 2) ? date('Y-m-d H:i:s') : null; // Set date_declined if status is 2
    $declined_by = isset($_POST['declined_by']) ? $_POST['declined_by'] : ''; // Get declined_by name

    // Get updated items and quantities
    $updatedItems = isset($_POST['items']) ? $_POST['items'] : []; // Expecting an array of items with their quantities
    $removedItems = isset($_POST['removed_items']) ? $_POST['removed_items'] : []; // Expecting an array of item IDs to remove

    // Log incoming data
    error_log("Request ID: $requestId");
    error_log("Status: $status");
    error_log("Issued By: $issued_by");
    error_log("Date Issued: $date_issued");
    error_log("Updated Items: " . json_encode($updatedItems));
    error_log("Removed Items: " . json_encode($removedItems));

    // Check if the request ID and status are valid
    if ($requestId > 0 && ($status === 1 || $status === 2)) { 
        // Prepare the SQL statement to update the status, issued_by, date_issued, date_approved, date_declined, and declined_by
        $query = "UPDATE request SET status = ?, issued_by = ?, date_issued = ?, date_approved = ?, date_declined = ?, declined_by = ? WHERE req_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssssi", $status, $issued_by, $date_issued, $date_approved, $date_declined, $declined_by, $requestId);

        if ($stmt->execute()) {
            // Update quantities for each item
            foreach ($updatedItems as $item) {
                $itemId = intval($item['id']);
                $quantity = intval($item['qty']);
                $updateQuery = "UPDATE stockin SET qty = qty - ? WHERE stockin_id = ?";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bind_param("ii", $quantity, $itemId);
                if (!$updateStmt->execute()) {
                    error_log("Error updating stockin for item ID $itemId: " . $updateStmt->error);
                }
                $updateStmt->close();
            }

            // Remove items if any
            foreach ($removedItems as $itemId) {
                $hideQuery = "UPDATE stockin SET status = 'hidden' WHERE stockin_id = ?";
                $hideStmt = $conn->prepare($hideQuery);
                $hideStmt->bind_param("i", $itemId);
                if (!$hideStmt->execute()) {
                    error_log("Error hiding stockin for item ID $itemId: " . $hideStmt->error);
                }
                $hideStmt->close();
            }

            echo json_encode(['success' => true]);
        } else {
            error_log("SQL Error: " . $stmt->error);
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