<?php
// Include database connection
include("../conn.php");

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $itemSerial = $_POST['item']; // The serial number of the item
    $quantity = $_POST['qty']; // The quantity of the item
    $owner = $_POST['owner']; // The user to whom the item is being assigned
    $location = $_POST['location']; // The location of the user

    // Validate the input
    if (empty($itemSerial) || empty($quantity) || empty($owner) || empty($location)) {
        echo json_encode(['success' => false, 'error' => 'All fields are required.']);
        exit;
    }

    // Prepare the SQL statement to insert the item into the fixed_assets table
    $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, owner, qty, location, serial_number, status) VALUES (?, ?, ?, ?, ?, 'Assigned')");
    $stmt->bind_param("ssiss", $itemName, $owner, $quantity, $location, $itemSerial);

    // Assuming you have a way to get the item name from the serial number
    // You might need to fetch the item name from the stock_in table based on the serial number
    $itemQuery = $conn->prepare("SELECT item FROM stock_in WHERE serialNO = ?");
    $itemQuery->bind_param("s", $itemSerial);
    $itemQuery->execute();
    $itemQuery->bind_result($itemName);
    $itemQuery->fetch();
    $itemQuery->close();

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add item.']);
    }

    // Close the statement
    $stmt->close();
} else {
    // If not a POST request, return an error
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
