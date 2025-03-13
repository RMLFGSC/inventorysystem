<?php
include("../dbconn/conn.php"); 

if (isset($_POST['stockin_id'])) {
    $stockinId = $_POST['stockin_id'];
    $itemNames = $_POST['item']; // This will be an array
    $quantities = $_POST['qty']; // This will be an array
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $dop = $_POST['dop'];
    $dr = $_POST['dr'];
    $warranty = isset($_POST['warranty']) ? 1 : 0;

    // Debugging: Log the received data
    error_log("Updating stockin_id: $stockinId");
    error_log("Items: " . json_encode($itemNames));
    error_log("Quantities: " . json_encode($quantities));
    error_log("Category: $category, DOP: $dop, DR: $dr, Warranty: $warranty");

    // Loop through items and quantities to update them
    for ($i = 0; $i < count($itemNames); $i++) {
        $itemName = mysqli_real_escape_string($conn, $itemNames[$i]);
        $qty = intval($quantities[$i]);

        // Update query for each item
        $query = "UPDATE stockin SET item='$itemName', qty='$qty', category='$category', dop='$dop', dr='$dr', warranty='$warranty' 
                  WHERE stockin_id='$stockinId'";

        if (!mysqli_query($conn, $query)) {
            // Log the error if the query fails
            error_log("Error updating record: " . mysqli_error($conn));
            echo "Error updating record: " . mysqli_error($conn);
            exit();
        }
    }

    // Redirect after successful update
    header("Location: stockin.php");
    exit();
} else {
    echo "No stockin_id provided.";
}
?>
