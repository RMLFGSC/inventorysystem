<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $req_number = $_POST['req_number'];
    $status = $_POST['status'];
    $date_issued = $_POST['date_issued'];
    $issued_by = $_POST['issued_by'];
    $items = $_POST['items']; 

    // 1. Update the request status
    $updateQuery = "UPDATE request SET status = ?, date_issued = ?, issued_by = ? WHERE req_number = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("isss", $status, $date_issued, $issued_by, $req_number);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to update request.']);
        exit;
    }

   
    foreach ($items as $item) {
        $itemName = $item['name'];
        $qtyToDeduct = intval($item['qty']);
        $remainingQtyToDeduct = $qtyToDeduct;

        $searchTerm = "%" . $itemName . "%";
        $query = $conn->prepare("SELECT stockin_id, qty FROM stock_in WHERE item LIKE ? AND qty > 0 ORDER BY dop ASC");
        $query->bind_param("s", $searchTerm);
        $query->execute();
        $result = $query->get_result();

        while (($row = $result->fetch_assoc()) && $remainingQtyToDeduct > 0) {
            $stockinId = $row['stockin_id'];
            $availableQty = $row['qty'];

            if ($availableQty >= $remainingQtyToDeduct) {
                $newQty = $availableQty - $remainingQtyToDeduct;

                $update = $conn->prepare("UPDATE stock_in SET qty = ? WHERE stockin_id = ?");
                $update->bind_param("ii", $newQty, $stockinId);
                $update->execute();

                $remainingQtyToDeduct = 0;
            } else {
                
                $update = $conn->prepare("UPDATE stock_in SET qty = 0 WHERE stockin_id = ?");
                $update->bind_param("i", $stockinId);
                $update->execute();

                $remainingQtyToDeduct -= $availableQty;
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Request approved and stock deducted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
