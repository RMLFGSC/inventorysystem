<?php
session_start();
include("../conn.php");

if (isset($_POST['id'])) {
    $assetId = $_POST['id'];

    $stmt = $conn->prepare("SELECT qty, stockin_item, serial_number, owner, location FROM fixed_assets WHERE asset_id = ?");
    $stmt->bind_param("s", $assetId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<div class='mt-3'>";
    if ($row = $result->fetch_assoc()) {
        echo "<div class='form-group row'>";
        echo "<div class='col-md-8'>";
        echo "<label>Item</label>";
        echo "<input type='text' class='form-control' value='" . htmlspecialchars($row['stockin_item']) . "' readonly>";
        echo "</div>";
        echo "<div class='col-md-4'>";
        echo "<label>Qty</label>";
        echo "<input type='text' class='form-control' value='" . htmlspecialchars($row['qty']) . "' readonly>";
        echo "</div>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label>User</label>";
        echo "<input type='text' class='form-control' value='" . htmlspecialchars($row['owner']) . "' readonly>";
        echo "</div>";
        echo "<div class='form-group'>";
        echo "<label>Location</label>";
        echo "<input type='text' class='form-control' value='" . htmlspecialchars($row['location']) . "' readonly>";
        echo "</div>";
    } else {
        echo "<p class='text-danger'>No asset found.</p>";
    }
    echo "</div>";

    $stmt->close();
    $conn->close();
} else {
    echo "<p class='text-danger'>Asset ID not specified.</p>";
}
