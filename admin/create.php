<?php
session_start();
include("../conn.php");



// Start of stockin
if (isset($_POST['addStockin'])) {
    $controlNO = $_POST['controlNO'];
    $cat_name = $_POST['category'];
    $dop = $_POST['dop'];
    $dr = $_POST['dr'];
    $item_names = $_POST['item'];
    $qtys = $_POST['qty'];  
    $serialNOs = $_POST['serialNO'];
    $warranties = isset($_POST['warranty']) ? $_POST['warranty'] : [];

    for ($i = 0; $i < count($item_names); $i++) {
        $item = mysqli_real_escape_string($conn, $item_names[$i]);
        $qty = intval($qtys[$i]);  

        // Check if warranty exists for the current item
        $warranty = in_array($i, $warranties) ? 1 : 0;

        // Separate the serial numbers using commas
        $serialArray = array_map('trim', explode(',', $serialNOs[$i]));

        // Check if the number of serial numbers matches the qty
        if (count($serialArray) !== $qty) {
            echo "Error: The number of serial numbers must match the quantity.";
            exit();
        }

        foreach ($serialArray as $serialNO) {
            $serialNO = mysqli_real_escape_string($conn, $serialNO);

            // Insert each serial number as a separate entry with qty = 1
            $query = "INSERT INTO stock_in (controlNO, item, qty, orig_qty, serialNO, category, dop, dr, warranty) 
                      VALUES ('$controlNO', '$item', 1, 1, '$serialNO', '$cat_name', '$dop', '$dr', '$warranty')";

            if (!mysqli_query($conn, $query)) {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }

    header("Location: stockin.php");
    exit();
}


// End of stockin



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
        $unassigned_quantity = $quantity;

        // Insert each item with the same req_number
        $query = "INSERT INTO request (req_number, user_id, item_request, qty, department, date, status) 
                  VALUES ('$formatted_req_number', '$user_id', '$item', '$quantity', '$department', '$date', '$status')";

        if (!mysqli_query($conn, $query)) {
            echo "Error: " . mysqli_error($conn);
            exit();
        }
    }

    $_SESSION['toast_message'] = "Request Added Successfully";
    header("Location: requisitions.php");
    exit();
}



if (isset($_POST['adduser'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $pword = mysqli_real_escape_string($conn, $_POST['pword']);
    $uname = mysqli_real_escape_string($conn, $_POST['username']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $hashed_password = password_hash($pword, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (fullname, pword, username, number, department, role) VALUES ('$fname', '$hashed_password', '$uname', '$number', '$department', '$role')";
    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['message'] = "User added successfully!";
        header("Location: users.php");

        exit(0);
    } else {
        $_SESSION['message'] = "User not added!";
        header("Location: users.php");
        exit(0);
    }
}
