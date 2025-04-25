<?php
include("../conn.php");

if (isset($_POST['serial'])) {
    $serial = $_POST['serial'];

    $stmt = $conn->prepare("UPDATE fixed_assets SET status = 'unassigned', owner = 'N/A', location = 'N/A' WHERE serial_number = ?");
    $stmt->bind_param("s", $serial);

    if ($stmt->execute()) {
        echo "Asset successfully unassigned and updated.";
    } else {
        echo "Error updating asset.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Missing serial number.";
}
?>
