<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

// Fetch total items per category
$query = "SELECT category, COUNT(*) AS total FROM stock_in GROUP BY category";
$result = mysqli_query($conn, $query);

$categories = [];
$totals = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row['category'];
    $totals[] = $row['total'];
}

// Encode the data as JSON for use in JavaScript
$categories_json = json_encode($categories);
$totals_json = json_encode($totals);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <?php include("../includes/topbar.php"); ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            </div>

            <!-- Content Row -->
            <div class="row">

                <!-- Total Equipment Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Equipment</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total equipment for both categories
                                        $sql = "SELECT category, SUM(qty) AS total FROM stock_in WHERE category IN ('IT Equipment', 'Engineering Equipment', 'IT Fixed Asset', 'Engineering Fixed Asset') GROUP BY category";
                                        $result = $conn->query($sql);

                                        // Initialize totals
                                        $totalIT = 0;
                                        $totalEngineering = 0;
                                        $totalITFixed = 0;
                                        $totalEngineeringFixed = 0;

                                        // Fetch totals for each category
                                        while ($row = $result->fetch_assoc()) {
                                            if ($row['category'] == 'IT Equipment') {
                                                $totalIT = $row['total'];
                                            } elseif ($row['category'] == 'Engineering Equipment') {
                                                $totalEngineering = $row['total'];
                                            } elseif ($row['category'] == 'IT Fixed Asset') {
                                                $totalITFixed = $row['total'];
                                            } elseif ($row['category'] == 'Engineering Fixed Asset') {
                                                $totalEngineeringFixed = $row['total'];
                                            }
                                        }

                                        // Display totals
                                        $totalCombined = $totalIT + $totalEngineering + $totalITFixed + $totalEngineeringFixed; 
                                        echo "$totalCombined"; 
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

                <!-- Total Pending Request Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Pending Request</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total pending requests
                                        $sql = "SELECT COUNT(*) AS total FROM request WHERE status = 0";
                                        $result = $conn->query($sql);

                                        // Fetch total pending requests
                                        echo $result->num_rows > 0 ? $result->fetch_assoc()['total'] : "0";
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

                <!-- Total Issued Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Issued</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to get total issued requests
                                        $sql = "SELECT COUNT(*) AS total FROM request WHERE status = 1";
                                        $result = $conn->query($sql);

                                        // Fetch total issued requests
                                        echo $result->num_rows > 0 ? $result->fetch_assoc()['total'] : "0"; // Default value if no data
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

                <!-- Low Stock Alerts Card -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low stock alerts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php
                                        // Query to check for low stock items (e.g., less than 5)
                                        $sql = "SELECT COUNT(*) AS low_stock_count FROM stock_in WHERE qty < 5"; // Adjust the threshold as needed
                                        $result = $conn->query($sql);
                                        $low_stock_count = $result->num_rows > 0 ? $result->fetch_assoc()['low_stock_count'] : "0";
                                        echo $low_stock_count;
                                        ?>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-triangle-exclamation fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bar Chart for Total Items per Category and Assigned Fixed Assets Section -->
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Total Items per Category</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar">
                                <canvas id="categoryBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Assigned Fixed Assets</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="assignedAssetsTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>User</th>
                                            <th>Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query to fetch assigned fixed assets
                                        $query = "SELECT stockin_item, qty, owner, location FROM fixed_assets";
                                        $result = $conn->query($query);

                                        // Check if there are results and display them
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>{$row['stockin_item']}</td>
                                                        <td>{$row['qty']}</td>
                                                        <td>{$row['owner']}</td>
                                                        <td>{$row['location']}</td>
                                                      </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='4' class='text-center'>No assigned fixed assets found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Get data from PHP
    const categories = <?php echo $categories_json; ?>;
    const totals = <?php echo $totals_json; ?>;

    // Create datasets for each category
    const datasets = [{
        label: 'Total Items per Category',
        data: totals, // Use the totals array directly
        backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#8BC34A'], // Assign colors based on index
        borderColor: ['#4CAF50', '#2196F3', '#FFC107', '#8BC34A'],
        borderWidth: 1
    }];

    // Create the bar chart
    const ctx = document.getElementById('categoryBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: datasets 
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return Number.isInteger(value) ? value : '';
                        }
                    }
                }
            }
        }
    });
</script>