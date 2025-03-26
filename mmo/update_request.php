<?php
// backend-save-endpoint.php

include '../conn.php'; // Include database connection

// Get data from POST request
$reqNumber = $_POST['req_number'];
$items = $_POST['items'];

// Begin transaction (for atomicity)
$conn->begin_transaction();

$success = true;

foreach ($items as $item) {
    $itemRequest = $item['item_request'];
    $qty = $item['qty'];

    // SQL query to update quantity
    $sql = "UPDATE request SET qty = ? WHERE req_number = ? AND item_request = ?"; // Replace 'your_items_table'

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $qty, $reqNumber, $itemRequest); // "isi" indicates integer, string, integer

    if (!$stmt->execute()) {
        $success = false;
        // Do not break here. Instead, log the error or handle it differently
    }
    $stmt->close();
}

if ($success) {
    $conn->commit();
    echo json_encode(['success' => true]);
} else {
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => 'Error updating items']);
}

$conn->close();
?>