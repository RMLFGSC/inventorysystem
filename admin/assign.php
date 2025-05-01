<?php
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $items = $_POST['item'];
    $quantities = $_POST['qty'];

    $success = true;
    $message = '';

    try {
        $conn->begin_transaction(); // Start transaction

        // Prepare and execute insert statements
        foreach ($items as $index => $item) {
            $qty = (int)$quantities[$index];
            $stmt = $conn->prepare("INSERT INTO fixed_assets (stockin_item, qty) VALUES (?, ?)");
            $stmt->bind_param("si", $item, $qty);

            if (!$stmt->execute()) {
                $success = false;
                $message = 'Failed to add asset: ' . $stmt->error;
                break;
            }
        }

        if ($success) {
            $conn->commit(); // Commit transaction
            $message = 'Assets added successfully.';
        } else {
            $conn->rollback(); // Rollback transaction on failure
        }

    } catch (Exception $e) {
        $conn->rollback(); // Rollback on exception
        $message = $e->getMessage();
    }

    // Return JSON response
    echo json_encode(['success' => $success, 'message' => $message]);
}
?>
