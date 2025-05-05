<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = $_POST['items']; 

    foreach ($items as $item) {
        $name = $item['name'];  
        $requestedQty = (int) $item['qty'];
        
        $likeTerm = '%' . $name . '%';

        $stmt = $conn->prepare("SELECT SUM(qty) as total FROM stock_in WHERE item LIKE ? AND qty > 0");
        $stmt->bind_param("s", $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $availableQty = (int) $row['total'];

        if ($availableQty < $requestedQty) {
            echo json_encode([
                'success' => false,
                'message' => "Not enough stock for item: {$name}. Requested: {$requestedQty}, Available: {$availableQty}"
            ]);
            exit;
        }
    }

    // All good
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
