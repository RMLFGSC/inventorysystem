<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_number = $_POST['req_number'];
    $status = $_POST['status'];
    $date_issued = $_POST['date_issued'];
    $issued_by = $_POST['issued_by'];
    $items = $_POST['items'];

    // 1. Update request table
    $updateQuery = "UPDATE request SET status = ?, date_issued = ?, issued_by = ? WHERE req_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("isss", $status, $date_issued, $issued_by, $req_number);
    
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to update request.']);
        exit;
    }

    // 2. Deduct stock for each item
    foreach ($items as $item) {
        $itemId = $item['id'];
        $quantity = (int)$item['qty'];

        // Optional: Check current stock (recommended for validation)
        $checkStock = $conn->prepare("SELECT qty FROM stock_in WHERE stockin_id = ?");
        $checkStock->bind_param("i", $itemId);
        $checkStock->execute();
        $checkStock->bind_result($currentQty);
        $checkStock->fetch();
        $checkStock->close();

        if ($currentQty < $quantity) {
            echo json_encode(['success' => false, 'message' => "Insufficient stock for item ID $itemId."]);
            exit;
        }

        // Deduct from correct column: qty
        $deductQuery = "UPDATE stock_in SET qty = qty - ? WHERE stockin_id = ?";
        $deductStmt = $conn->prepare($deductQuery);
        $deductStmt->bind_param("ii", $quantity, $itemId);

        if (!$deductStmt->execute()) {
            echo json_encode(['success' => false, 'message' => "Failed to deduct stock for item ID $itemId."]);
            exit;
        }
    }

    // 3. Success response
    echo json_encode(['success' => true, 'message' => 'Request approved and stock deducted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
