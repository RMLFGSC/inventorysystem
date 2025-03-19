<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");
?>


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">


        <!-- topbar -->
        <?php
        include("../includes/topbar.php");
        ?>


        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            </div>

            <!-- Content Row -->
            <div class="row">

                <!-- Earnings (Monthly) Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Equipment</div>
                                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total equipment for both categories
                                        $sql = "SELECT category, SUM(qty) AS total FROM stock_in WHERE category IN ('IT Equipment', 'Engineering Equipment') GROUP BY category";
                                        $result = $conn->query($sql);

                                        // Initialize totals
                                        $totalIT = 0;
                                        $totalEngineering = 0;

                                        // Fetch totals for each category
                                        while ($row = $result->fetch_assoc()) {
                                            if ($row['category'] == 'IT Equipment') {
                                                $totalIT = $row['total'];
                                            } elseif ($row['category'] == 'Engineering Equipment') {
                                                $totalEngineering = $row['total'];
                                            }
                                        }

                                        // Display totals
                                        echo "IT Equipment: " . $totalIT . "<br>";
                                        echo "Engineering Equipment: " . $totalEngineering;
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Pending Request Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Pending Request</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total pending requests
                                        $sql = "SELECT COUNT(*) AS total FROM request WHERE status = 0";
                                        $result = $conn->query($sql);

                                        // Fetch total pending requests
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            echo $row['total'];
                                        } else {
                                            echo "0"; 
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Issued Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Total Issued</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total issued requests
                                        $sql = "SELECT COUNT(*) AS total FROM request WHERE status = 1";
                                        $result = $conn->query($sql);

                                        // Fetch total issued requests
                                        if ($result->num_rows > 0) {
                                            $row = $result->fetch_assoc();
                                            echo $row['total'];
                                        } else {
                                            echo "0"; // Default value if no data
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-box-open fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests Card Example -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Low stock alerts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-triangle-exclamation fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Row -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->


    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");

    ?>