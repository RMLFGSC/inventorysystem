<?php
include("../conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    $query = "UPDATE users SET is_hide = 1 WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "User successfully hidden!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error hiding user!";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();

    header("Location: users.php");
    exit();
}
?>
