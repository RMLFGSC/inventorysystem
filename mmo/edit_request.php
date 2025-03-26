<?php
// backend-endpoint.php

// Include database connection
include '../conn.php'; // Assuming $conn is established here

// Get req_number from the POST request
$reqNumber = $_POST['req_number'];

// SQL query to retrieve items
$sql = "SELECT item_request, qty FROM request WHERE req_number = ?"; // Replace 'your_items_table'

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $reqNumber); // "s" indicates a string
$stmt->execute();
$result = $stmt->get_result();

$items = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}   

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($items);

$stmt->close();
$conn->close(); // Close the connection if it's not handled elsewhere.
?>