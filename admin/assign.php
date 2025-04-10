<?php
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner = $_POST['user'];
    $location = $_POST['location'];
    $items = $_POST['item'];  
    $quantities = $_POST['qty'];

    try {
        $conn->begin_transaction(); // Add this to manually handle commit/rollback

        // Check if owner already exists
        $checkStmt = $conn->prepare("SELECT 1 FROM fixed_assets WHERE owner = ? LIMIT 1");
        $checkStmt->bind_param("s", $owner);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo json_encode(['error' => "User already exists."]);
            exit;
        }
        $checkStmt->close();

        for ($i = 0; $i < count($items); $i++) {
            $serial_number = $items[$i]; 
            $qty = (int)$quantities[$i];

            // Check stock availability
            $totalStockStmt = $conn->prepare("SELECT SUM(qty) AS total_qty FROM stock_in WHERE serialNO = ?");
            $totalStockStmt->bind_param("s", $serial_number);
            $totalStockStmt->execute();
            $totalStockResult = $totalStockStmt->get_result();
            $totalAvailableStock = $totalStockResult->fetch_assoc()['total_qty'] ?? 0;

            if ($qty > $totalAvailableStock) {
                echo json_encode(['error' => "Insufficient stock for serial number: $serial_number"]);
                exit;
            }

            // Get item name
            $itemQuery = $conn->prepare("SELECT item FROM stock_in WHERE serialNO = ? LIMIT 1");
            $itemQuery->bind_param("s", $serial_number);
            $itemQuery->execute();
            $itemRow = $itemQuery->get_result()->fetch_assoc();
            $item_name = $itemRow['item'];

            // Insert to fixed_assets
            $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, qty, owner, location, serial_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisss", $item_name, $qty, $owner, $location, $serial_number);
            $stmt->execute();

            // Deduct stock
            $remainingQty = $qty;
            while ($remainingQty > 0) {
                $selectStmt = $conn->prepare("SELECT stockin_id, qty FROM stock_in WHERE serialNO = ? AND qty > 0 ORDER BY stockin_id ASC LIMIT 1");
                $selectStmt->bind_param("s", $serial_number);
                $selectStmt->execute();
                $result = $selectStmt->get_result();

                if ($result->num_rows === 0) {
                    echo json_encode(['error' => "Stock ran out while deducting for $serial_number"]);
                    exit;
                }

                $row = $result->fetch_assoc();
                $deductQty = min($remainingQty, $row['qty']);
                $newQty = $row['qty'] - $deductQty;

                $updateStmt = $conn->prepare("UPDATE stock_in SET qty = ? WHERE stockin_id = ?");
                $updateStmt->bind_param("ii", $newQty, $row['stockin_id']);
                $updateStmt->execute();

                $remainingQty -= $deductQty;
            }
        }

        $conn->commit();
        echo json_encode(['success' => 'Items assigned successfully']);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
?>
