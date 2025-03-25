<?php
session_start();
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $item = $_POST['stockin_item'] ?? '';
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    $serial = $_POST['serial'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $department = $_POST['department'] ?? '';

    // Validate inputs
    if (empty($item) || $qty < 1 || empty($serial) || empty($owner) || empty($department)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input', 'data' => [
            'item' => $item,
            'qty' => $qty,
            'serial_number' => $serial,
            'owner' => $owner,
            'department' => $department
        ]]);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into fixed_assets table
        $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, qty, serial_number, owner, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sisss', $item, $qty, $serial, $owner, $department);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into fixed_assets: " . $stmt->error);
        }

        // Update unassigned items in request table
        $stmt = $conn->prepare("UPDATE request SET unassigned_qty = unassigned_qty - ? WHERE stockin_id = (SELECT stockin_id FROM stock_in WHERE item = ? LIMIT 1)");
        $stmt->bind_param('is', $qty, $item);
        if (!$stmt->execute()) {
            throw new Exception("Error updating request: " . $stmt->error);
        }

        // Commit transaction
        $conn->commit();
        echo json_encode(['success' => 'Item assigned successfully']);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
