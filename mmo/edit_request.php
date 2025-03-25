<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['req_number'])) {
        $req_number = $_POST['req_number'];

        $query = "SELECT s.item, r.qty, r.stockin_id 
                  FROM request r 
                  JOIN stock_in s ON r.stockin_id = s.stockin_id 
                  WHERE r.req_number = ?"; 

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $req_number); 
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row; 
            }
            
            echo json_encode([
                'items' => $items 
            ]);
        } else {
            echo json_encode([
                'items' => [] 
            ]);
        }

        $stmt->close();
    } elseif (isset($_POST['items'])) {
        $items = $_POST['items'];

        $query = "UPDATE request SET qty = ? WHERE req_number = ? AND stockin_id = ?"; 

        $stmt = $conn->prepare($query);

        foreach ($items as $item) {
            $qty = $item['qty'];
            $req_number = $item['req_number'];
            $stockin_id = $item['id']; 

            $stmt->bind_param("isi", $qty, $req_number, $stockin_id);
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Error updating item: ' . $stmt->error]);
                exit;
            }
        }

        $stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No items provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
