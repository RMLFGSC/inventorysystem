<?php
session_start();
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user (owner), location, item, and quantity from the form
    $owner = $_POST['user'];
    $location = $_POST['location'];
    $stockin_item = $_POST['item'];
    $qty = $_POST['qty'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Step 1: Check if the total available stock is enough
        $totalStockStmt = $conn->prepare("SELECT SUM(qty) AS total_qty FROM stock_in WHERE item = ?");
        $totalStockStmt->bind_param("s", $stockin_item);
        $totalStockStmt->execute();
        $totalStockResult = $totalStockStmt->get_result();
        $totalStockRow = $totalStockResult->fetch_assoc();
        $totalAvailableStock = $totalStockRow['total_qty'] ?? 0;

        // Check if the requested quantity exceeds available stock
        if ($qty > $totalAvailableStock) {
            throw new Exception("Insufficient stock for item: " . $stockin_item . ". Available: " . $totalAvailableStock . ", Requested: " . $qty);
        }

        // Prepare the SQL statement to insert data into the fixed_assets table
        $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, qty, owner, location) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $stockin_item, $qty, $owner, $location);
        $stmt->execute();

        // Stock deduction after successful insertion
        $remainingQty = $qty;

        while ($remainingQty > 0) {
            // Find the next available stock record with the given item and a positive qty
            $selectStmt = $conn->prepare("SELECT stockin_id, qty FROM stock_in WHERE item = ? AND qty > 0 ORDER BY stockin_id ASC LIMIT 1");
            $selectStmt->bind_param("s", $stockin_item);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($result->num_rows === 0) {
                throw new Exception("Not enough stock for item: " . $stockin_item);
            }

            $row = $result->fetch_assoc();
            $stockin_id = $row['stockin_id'];
            $currentQty = $row['qty'];

            // Calculate the quantity to deduct
            $deductQty = min($remainingQty, $currentQty);
            $newQty = $currentQty - $deductQty;

            // Update the stock record
            $updateStmt = $conn->prepare("UPDATE stock_in SET qty = ? WHERE stockin_id = ?");
            $updateStmt->bind_param("ii", $newQty, $stockin_id);
            if (!$updateStmt->execute()) {
                throw new Exception("Error updating stock: " . $updateStmt->error);
            }

            // Subtract the deducted quantity from the remaining quantity
            $remainingQty -= $deductQty;

            // Close the prepared statements
            $selectStmt->close();
            $updateStmt->close();
        }

        // Commit transaction
        $conn->commit();
        
        // Redirect to the fixed asset page after successful submission
        $_SESSION['success'] = 'Item assigned successfully';
        header("Location: asset");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header("Location: asset");
        exit;
    } finally {
        // Close the statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>
