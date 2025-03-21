<?php
include("../conn.php");

if (isset($_POST['req_number'])) {
    $req_number = $_POST['req_number'];

    // Query to fetch the requisition details along with stock-in items
    $query = "SELECT r.req_number, r.date, u.fullname AS requester_name, u.department, 
                     s.item, r.qty, r.issued_by, r.date_issued, r.date_declined
              FROM request r 
              JOIN stock_in s ON r.stockin_id = s.stockin_id 
              JOIN users u ON r.user_id = u.user_id 
              WHERE r.req_number = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $req_number); 
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $items[] = $row; // Collect items
        }
        
        // Return the first item's details for the modal
        $firstItem = $items[0];
        echo json_encode([
            'req_number' => $firstItem['req_number'],
            'date' => $firstItem['date'],
            'requester_name' => $firstItem['requester_name'],
            'department' => $firstItem['department'],
            'issued_by' => $firstItem['issued_by'],
            'date_issued' => $firstItem['date_issued'],
            'date_declined' => $firstItem['date_declined'],
            'items' => $items // Return all items
        ]);
    } else {
        echo json_encode([
            'req_number' => '',
            'date' => '',
            'requester_name' => '',
            'department' => '',
            'issued_by' => '',
            'date_issued' => '',
            'date_declined' => '',
            'items' => []
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'req_number' => '',
        'date' => '',
        'requester_name' => '',
        'department' => '',
        'issued_by' => '',
        'date_issued' => '',
        'date_declined' => '',
        'items' => []
    ]);
}

$conn->close();
?>
