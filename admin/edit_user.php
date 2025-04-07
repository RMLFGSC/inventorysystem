<?php



include("../conn.php"); 

if (isset($_POST['update_user'])) {
    $id = $_POST['edit_id'];
    $fullname = mysqli_real_escape_string($conn, $_POST['edit_fullname']);
    $username = mysqli_real_escape_string($conn, $_POST['edit_username']);
    $department = mysqli_real_escape_string($conn, $_POST['edit_department']);
    $role = mysqli_real_escape_string($conn, $_POST['edit_role']);
    $password = $_POST['edit_pword'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users SET fullname='$fullname', username='$username', department='$department', role='$role', password='$hashed' WHERE user_id='$id'";
    } else {
        $query = "UPDATE users SET fullname='$fullname', username='$username', department='$department', role='$role' WHERE user_id='$id'";
    }

    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = "User updated successfully.";
        header("Location: users.php"); 
        exit();
    } else {
        // error message
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: users.php");
        exit();
    }
}
?>
