<?php
session_start();
include ("../conn.php"); 

// Check if stockin_id is provided
if (isset($_GET['stockin_id'])) {
    $stockin_id = intval($_GET['stockin_id']);

    $sql = "UPDATE stock_in SET is_posted = 1 WHERE controlNO = (SELECT controlNO FROM stock_in WHERE stockin_id = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $stockin_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Stock-in successfully finalized!";
    } else {
        $_SESSION['error_message'] = "Failed to finalize stock-in.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to stock-in page
    header("Location: stockin.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid stock-in request.";
    header("Location: stockin.php");
    exit();
}

?>
