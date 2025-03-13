<?php
include '../../conn.php';

if (isset($_POST['addMR'])) {
    $stockin_item = $_POST['stockin_item'];
    $serial_number = $_POST['serial_number'];
    $assigned_to = $_POST['assigned_to'];
    $location = $_POST['location'];

    // Sample query
    $query = "INSERT INTO fixed_assets (stockin_item, serial_number, assigned_to, location)
              VALUES ('$stockin_item', '$serial_number', '$assigned_to', '$location')";

    mysqli_query($conn, $query);
    header("Location: ../asset.php");
}
?>
