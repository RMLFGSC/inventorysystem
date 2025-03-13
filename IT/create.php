<?php
session_start();
include("../dbconn/conn.php");

//start of request

if (!isset($_SESSION['auth_user']['user_id'])) {
    die("Error: User is not logged in.");
}

if (isset($_POST['addRequest'])) {
    // Debugging: Check if form data is received
    if (empty($_POST['stockin_id']) || empty($_POST['qty'])) {
        $_SESSION['message'] = "No items selected for the request.";
        header("Location: requisitions.php");
        exit();
    }   

    $user_id = $_SESSION['auth_user']['user_id']; 
    $req_number = mysqli_real_escape_string($conn, $_POST['req_number']);
    
    // Fetch user details
    $userQuery = "SELECT fullname, department FROM users WHERE user_id = '$user_id'";
    $user = mysqli_fetch_assoc(mysqli_query($conn, $userQuery));
    
    // Get the user's name and department
    $department = $user['department'];

    $items = $_POST['stockin_id']; 
    $qtys = $_POST['qty']; 
    $status = 0; 
    $date = date('Y-m-d'); 

    // Loop through items and insert into request
    foreach ($items as $index => $item) {
        $item = mysqli_real_escape_string($conn, $item);
        $qty = intval($qtys[$index]);

        // Check if the stockin_id exists in the stock_in table
        $checkQuery = "SELECT stockin_id FROM stock_in WHERE item = '$item'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if ($stockinRow = mysqli_fetch_assoc($checkResult)) {
            // Insert each item with the same req_number
            $query = "INSERT INTO request (req_number, user_id, stockin_id, qty, department, date, status) 
                      VALUES ('$req_number', '$user_id', '{$stockinRow['stockin_id']}', '$qty', '$department', '$date', '$status')";
            
            if (!mysqli_query($conn, $query)) {
                echo "Error: " . mysqli_error($conn);
                exit(); 
            }
        } else {
            echo "Error: Item '$item' does not exist.";
            exit(); 
        }
    }

    $_SESSION['toast_message'] = "Request Added Successfully";
    header("Location: request.php");
    exit();
}

?>