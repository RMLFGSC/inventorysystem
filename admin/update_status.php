<?php
include("../conn.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the request ID and new status from the POST data
    $requestId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    $items = isset($_POST['items']) ? $_POST['items'] : []; // Get items to deduct

    // Check if the request ID and status are valid
    if ($requestId > 0 && ($status === 1 || $status === 2)) { 
        // Prepare the SQL statement to update the status and date_issued
        $query = "UPDATE request SET status = ?, date_issued = ? WHERE req_number = (SELECT req_number FROM request WHERE req_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isi", $status, $dateIssued, $requestId);
        
        $dateIssued = date('Y-m-d'); // Set the current date

        if ($stmt->execute()) {
            // Deduct items from inventory
            foreach ($items as $item) {
                $itemId = intval($item['id']);
                $quantity = intval($item['qty']);
                
                // Prepare the SQL statement to deduct the item quantity
                $deductQuery = "UPDATE stock_in SET qty = qty - ? WHERE stockin_id = ?";
                $deductStmt = $conn->prepare($deductQuery);
                $deductStmt->bind_param("ii", $quantity, $itemId);
                $deductStmt->execute();
                $deductStmt->close();
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn->close();
?>