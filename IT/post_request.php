<?php
session_start();
include("../dbconn/conn.php"); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the required POST variables are set
    if (isset($_POST['req_number']) && isset($_POST['is_posted'])) {
        $req_number = mysqli_real_escape_string($conn, $_POST['req_number']);
        $is_posted = (int)$_POST['is_posted']; 

        $query = "UPDATE request SET is_posted = $is_posted WHERE req_number = '$req_number'";
        
        if (mysqli_query($conn, $query)) {
            echo "Success: The request has been posted."; 
        } else {
            echo "Error: " . mysqli_error($conn); 
        }
    } else {
        echo "Error: Invalid input."; 
    }
} else {
    echo "Error: Invalid request method."; 
}

mysqli_close($conn);
?>
