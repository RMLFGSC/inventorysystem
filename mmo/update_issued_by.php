<?php
include("../conn.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $issued_by = $_POST['issued_by'];
    $date_approved = date('Y-m-d H:i:s'); // Set the current date and time for date_approved

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE request SET issued_by = ?, date_approved = ? WHERE req_id = ?");
    $stmt->bind_param("ssi", $issued_by, $date_approved, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>