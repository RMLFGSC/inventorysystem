<?php
include("../includes/header.php");
include("../includes/navbar_eng.php");
?>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">


        <!-- topbar -->
        <?php
        include("../includes/topbar_eng.php");
        ?>


        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            </div>

            <!-- New Statistics Card -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Approved Requests</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total approved requests
                                        $sql = "SELECT COUNT(*) AS total FROM request WHERE status = 1";
                                        $result = $conn->query($sql);
                                        $row = $result->fetch_assoc();
                                        echo $row['total'];
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="col-xl-8 col-lg-7 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Requests Overview</h6>
                        </div>
                        <div class="card-body">
                            <canvas id="requestsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Row -->
            <div class="row">

                

                <!-- Total Pending Request Card Example -->
                <!-- <div class="col-xl-3 col-md-6 mb-4">
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

                

                <!-- Pending Requests Card Example -->
                <!-- <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Low stock alerts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-triangle-exclamation fa-2x text-gray-300"></i> -->
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

    <script>
        // Chart.js code to create a chart
        const ctx = document.getElementById('requestsChart').getContext('2d');
        const requestsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Pending', 'Approved', 'Declined'],
                datasets: [{
                    label: 'Requests',
                    data: [
                        <?php
                        // Fetch counts for each status
                        $pending = $conn->query("SELECT COUNT(*) FROM request WHERE status = 0")->fetch_row()[0];
                        $approved = $conn->query("SELECT COUNT(*) FROM request WHERE status = 1")->fetch_row()[0];
                        $declined = $conn->query("SELECT COUNT(*) FROM request WHERE status = 2")->fetch_row()[0];
                        echo "$pending, $approved, $declined";
                        ?>
                    ],
                    backgroundColor: [
                        'rgba(0, 123, 255, 1)', // Solid blue
                        'rgba(40, 167, 69, 1)', // Solid green
                        'rgba(255, 193, 7, 1)'  // Solid yellow
                    ],
                    borderColor: [
                        'rgba(0, 123, 255, 1)', // Solid blue
                        'rgba(40, 167, 69, 1)', // Solid green
                        'rgba(255, 193, 7, 1)'  // Solid yellow
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            min: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Disable the legend
                    }
                }
            }
        });
    </script>

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");

    ?>