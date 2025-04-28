<?php
session_start();
include("../conn.php");

if (isset($_POST['owner']) && isset($_POST['location'])) {
    $owner = $_POST['owner'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("SELECT qty, stockin_item, serial_number FROM fixed_assets WHERE owner = ? AND location = ?");
    $stmt->bind_param("ss", $owner, $location);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<div class='table-responsive mt-3'>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-light'>";
    echo "<tr>";
    echo "<th class = text-center>Qty</th>";
    echo "<th>Item</th>";
    echo "<th>Serial Number</th>";
    echo "<th class= text-center>Action</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class = text-center>" . htmlspecialchars($row['qty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stockin_item']) . "</td>";
        echo "<td>" . htmlspecialchars($row['serial_number']) . "</td>";
        echo "<td class='text-center'>
        <a href='#' class='text-danger removeAssetBtn' 
           data-serial='" . htmlspecialchars($row['serial_number']) . "'
           data-owner='" . htmlspecialchars($owner) . "'
           data-location='" . htmlspecialchars($location) . "'>
           <i class='fas fa-times-circle'></i>
        </a>
      </td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "<div class='row mt-2'>";
        echo "<div class='col-md-6'>";
            echo "<label for='viewModalUser'><strong>User</strong></label>";
            echo "<input type='text' class='form-control' id='viewModalUser' value='" . htmlspecialchars($owner) . "' readonly>";
        echo "</div>";
        echo "<div class='col-md-6'>";
            echo "<label for='viewModalLocation'><strong>Location</strong></label>";
            echo "<input type='text' class='form-control' id='viewModalLocation' value='" . htmlspecialchars($location) . "' readonly>";
        echo "</div>";
    echo "</div>";

    if ($result->num_rows === 0) {
        echo "<p class='mt-3'>No assets found for this user and location.</p>";
    }

    $stmt->close();
    $conn->close();

} else {
    echo "<p class='text-danger'>Owner or location not specified.</p>";
}
?>