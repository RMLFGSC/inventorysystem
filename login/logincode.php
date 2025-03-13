<?php
session_start();
include("../conn.php");

if (isset($_POST['login-btn']) && !empty($_POST['username']) && !empty($_POST['pword'])) {
    $uname = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['pword']);

    $login_query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $login_query);
    mysqli_stmt_bind_param($stmt, "s", $uname);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($data = mysqli_fetch_assoc($result)) {
        $hashed_password = $data['pword'];

        // Check if the user is hidden
        if ($data['is_hide'] == 1) {
            $_SESSION['message_ni'] = "bawal naka mo logged in et ni resign naka.";
            header("Location: login.php");
            exit(0);
        }

        if (password_verify($password, $hashed_password)) {
            session_regenerate_id(true); 

            $_SESSION['auth'] = true;
            $_SESSION['auth_role'] = $data['role']; // 'admin', 'engineering', 'mmo', 'user'
            $_SESSION['auth_user'] = [
                'user_id' => $data['user_id'],
                'user_name' => $data['fullname'],
                'user_uname' => $data['username'],
            ];

            $_SESSION['message_ni'] = "Welcome " . $_SESSION['auth_user']['user_name'];

            switch ($_SESSION['auth_role']) {
                case 'admin':
                    header("Location: ../admin/index.php");
                    break;
                case 'mmo':
                    header("Location: ../mmo/index.php");
                    break;
                case 'it':
                    header("Location: ../IT/request.php");
                    break;
                case 'engineering':
                    header("Location: ../eng/request.php");
                    break;
                default:
                    $_SESSION['message_ni'] = "Invalid user role";
                    header("Location: login.php");
                    break;
            }
            exit(0);
        }
    }
    $_SESSION['message_ni'] = "Invalid Username or Password";
} else {
    $_SESSION['message_ni'] = "Please fill in all fields";
}
header("Location: login.php");
exit(0);
?>
