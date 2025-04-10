<?php
session_start();
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner = $_POST['user'];
    $location = $_POST['location'];
    $items = $_POST['item'];
    $quantities = $_POST['qty'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Loop through each item
        for ($i = 0; $i < count($items); $i++) {
            $stockin_item = $items[$i];
            $qty = (int)$quantities[$i];

            // Step 1: Check if enough stock is available
            $totalStockStmt = $conn->prepare("SELECT SUM(qty) AS total_qty FROM stock_in WHERE item = ?");
            $totalStockStmt->bind_param("s", $stockin_item);
            $totalStockStmt->execute();
            $totalStockResult = $totalStockStmt->get_result();
            $totalStockRow = $totalStockResult->fetch_assoc();
            $totalAvailableStock = $totalStockRow['total_qty'] ?? 0;

            if ($qty > $totalAvailableStock) {
                throw new Exception("Insufficient stock for item: " . $stockin_item . ". Available: " . $totalAvailableStock . ", Requested: " . $qty);
            }

            // Step 2: Insert into fixed_assets
            $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, qty, owner, location) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $stockin_item, $qty, $owner, $location);
            $stmt->execute();

            // Step 3: Deduct stock accordingly
            $remainingQty = $qty;

            while ($remainingQty > 0) {
                $selectStmt = $conn->prepare("SELECT stockin_id, qty FROM stock_in WHERE item = ? AND qty > 0 ORDER BY stockin_id ASC LIMIT 1");
                $selectStmt->bind_param("s", $stockin_item);
                $selectStmt->execute();
                $result = $selectStmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception("Stock ran out during deduction for item: " . $stockin_item);
                }

                $row = $result->fetch_assoc();
                $stockin_id = $row['stockin_id'];
                $currentQty = $row['qty'];

                $deductQty = min($remainingQty, $currentQty);
                $newQty = $currentQty - $deductQty;

                $updateStmt = $conn->prepare("UPDATE stock_in SET qty = ? WHERE stockin_id = ?");
                $updateStmt->bind_param("ii", $newQty, $stockin_id);
                $updateStmt->execute();

                $remainingQty -= $deductQty;

                $selectStmt->close();
                $updateStmt->close();
            }

            $stmt->close();
            $totalStockStmt->close();
        }

        // Commit if all successful
        $conn->commit();
        $_SESSION['success'] = 'Items assigned successfully';
        header("Location: asset");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header("Location: asset");
        exit;
    } finally {
        $conn->close();
    }
}
?>
