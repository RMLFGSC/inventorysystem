<?php
session_start();

include ("../../conn.php");

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

?>