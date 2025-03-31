<?php
// backend-save-endpoint.php

include '../conn.php'; // Include database connection

// Get data from POST request
$input = json_decode(file_get_contents('php://input'), true);
$reqNumber = $input['req_number'] ?? null;
$items = $input['items'] ?? [];

// Check for missing data
if (empty($reqNumber) || empty($items)) {
    echo json_encode(['success' => false, 'error' => 'Request number or items are missing.']);
    exit;
}

// Begin transaction (for atomicity)
$conn->begin_transaction();

$success = true;
$errors = []; // Array to hold error messages

foreach ($items as $item) {
    $itemRequest = $item['item_request'];
    $qty = $item['qty'];

    // SQL query to update quantity
    $sql = "UPDATE request SET qty = ? WHERE req_number = ? AND item_request = ?"; 

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $qty, $reqNumber, $itemRequest); 

    if (!$stmt->execute()) {
        $success = false;
        $errors[] = "Error updating item $itemRequest: " . $stmt->error; // Log error
    }
    $stmt->close();
}

if ($success) {
    $conn->commit();
    echo json_encode(['success' => true]);
} else {
    $conn->rollback();
    echo json_encode(['success' => false, 'errors' => $errors]); // Return all errors
}

$conn->close();
?>