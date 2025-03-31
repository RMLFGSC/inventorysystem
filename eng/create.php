<?php
session_start();
include("../conn.php");

//start of request

if (!isset($_SESSION['auth_user']['user_id'])) {
    die("Error: User is not logged in.");
}

if (isset($_POST['addRequest'])) {


    $user_id = $_SESSION['auth_user']['user_id'];
    $formatted_req_number = mysqli_real_escape_string($conn, $_POST['req_number']);

    // Fetch user details
    $userQuery = "SELECT fullname, department FROM users WHERE user_id = '$user_id'";
    $user = mysqli_fetch_assoc(mysqli_query($conn, $userQuery));

    // Get the user's name and department
    $department = $user['department'];

    $items = $_POST['item_request'];
    $qtys = $_POST['qty'];
    $status = 0;
    $date = date('Y-m-d');

    // Loop through items and insert into request
    foreach ($items as $index => $item) {
        $item = mysqli_real_escape_string($conn, $item);
        $quantity = intval($qtys[$index]);

        // Insert each item with the same req_number
        $query = "INSERT INTO request (req_number, user_id, item_request, qty, department, date, status) 
                  VALUES ('$formatted_req_number', '$user_id', '$item', '$quantity', '$department', '$date', '$status')";

        if (!mysqli_query($conn, $query)) {
            echo "Error: " . mysqli_error($conn);
            exit();
        }
    }

    $_SESSION['toast_message'] = "Request Added Successfully";
    header("Location: request.php");
    exit();
}


?>