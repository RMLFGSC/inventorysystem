<?php
include("../conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial = $_POST['serial'];

    if (empty($serial)) {
        echo json_encode(['success' => false, 'error' => 'Missing serial number.']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM fixed_assets WHERE serial_number = ?");
    $stmt->bind_param("s", $serial);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error deleting asset.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

$conn->close();
?>
