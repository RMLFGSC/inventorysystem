<?php
include("../conn.php");

if (isset($_POST['items'])) {
    $items = $_POST['items']; // This should contain the items and their quantities

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Deduct stock for each item
        foreach ($items as $item) {
            $stockin_id = $item['id']; // Assuming this is the stock_in ID
            $qty = $item['qty'];

            // Check if there is enough stock to deduct
            $checkStockQuery = "SELECT qty FROM stock_in WHERE stockin_id = ?";
            $checkStmt = $conn->prepare($checkStockQuery);
            $checkStmt->bind_param("i", $stockin_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $currentStock = $checkResult->fetch_assoc()['qty'];

            if ($currentStock >= $qty) {
                // Deduct the quantity from stock_in
                $deductStockQuery = "UPDATE stock_in SET qty = qty - ? WHERE stockin_id = ?";
                $deductStmt = $conn->prepare($deductStockQuery);
                $deductStmt->bind_param("ii", $qty, $stockin_id);
                $deductStmt->execute();
                $deductStmt->close();
            } else {
                // Handle insufficient stock case
                throw new Exception("Insufficient stock for item ID: $stockin_id");
            }

            $checkStmt->close();
        }

        // Commit the transaction
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error deducting stock: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No items provided.']);
}

$conn->close();
?>
