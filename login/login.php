<?php
if (isset($row) && isset($row['fullname'])) {
    $_SESSION['user_name'] = $row['fullname'];
} else {
    $_SESSION['user_name'] = ''; // or handle the error as needed
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inventoty System</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../css/custom-login.css" rel="stylesheet">

    <style>
        body {
            background-image: url('../img/bg-gmc.jpg');
            background-size: cover; /* Adjusts the image to cover the entire background */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
            display: flex; /* Enables flexbox layout */
            align-items: center; /* Vertically centers the content */
            justify-content: center; /* Horizontally centers the content */
        }
    </style>

</head>

<body class="bg-light">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-5 col-lg-6 col-md-8">

                <div class="card border-0 shadow-lg my-5">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <img src="../img/gmc-logo.jpg" alt="GMC Logo" class="rounded-circle" style="width: 80px; height: 80px; margin-bottom: 10px;">
                            <h1 class="h4 text-dark">GENSANMED</h1>
                        </div>
                        <form action="logincode.php" method="POST">
                            <div class="form-group">
                                <input type="text" name="username" class="form-control form-control-user" placeholder="Username" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="pword" class="form-control form-control-user" placeholder="Password" required>
                            </div>

                            <hr>

                            <div class="form-group mb-3">
                                <button type="submit" name="login-btn" class="btn btn-warning btn-block" style="background-color: #76a73c; border-color: #4e73df;">Submit</button>
                            </div>
                            
                            <hr>
                            
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>
