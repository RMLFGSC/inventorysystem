<?php
session_start();
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stockin_item = $_POST['stockin_item'] ?? '';
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 0;
    $serial = $_POST['serial'] ?? '';
    $owner = $_POST['owner'] ?? '';
    $department = $_POST['department'] ?? '';

    // Validate inputs
    if (empty($stockin_item) || $qty < 1 || empty($serial) || empty($owner) || empty($department)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input', 'data' => [
            'item' => $stockin_item,
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
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO fixed_assets (serial_number, stockin_item, qty, owner, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $serial, $stockin_item, $qty, $owner, $department);

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into fixed_assets: " . $stmt->error);
        }

        // Update items in stockin table
        $stmt = $conn->prepare("UPDATE stock_in SET qty = qty - ? WHERE stockin_id = (SELECT stockin_id FROM stock_in WHERE item = ? LIMIT 1)");
        $stmt->bind_param('is', $qty, $stockin_item);
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
    } finally {
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>
