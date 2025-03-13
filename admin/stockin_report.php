<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

// Modify the SQL query to include date filtering
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "SELECT item, category, SUM(orig_qty) AS total_orig_qty, SUM(qty) AS total_qty FROM stock_in WHERE is_posted = 1"; // Added condition to count only posted items
$query .= " GROUP BY item, category"; // Added grouping
if ($startDate) {
    $query .= " AND date >= '$startDate'"; // Assuming 'date' is the column name for the date
}
if ($endDate) {
    $query .= " AND date <= '$endDate'";
}

$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- topbar -->
        <?php include("../includes/topbar.php"); ?>

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Inventory Report</h1>
            </div>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <!-- Add date filtering form above the table -->
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-5">
                                <label for="start_date">Start Date:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate); ?>" required>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="end_date">End Date:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate); ?>" required>
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <div class="card-datatable">
                        <table class="datatables-basic table table-bordered" id="dataTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Initial Qty</th>
                                    <th>Current Qty</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                while ($row = mysqli_fetch_assoc($result)):
                                    $fixedQty = $row['total_orig_qty']; // Updated to use summed original qty
                                    $currentQty = $row['total_qty']; // Updated to use summed current qty

                                    // Status logic
                                    if ($currentQty == 0) {
                                        $status = 'Out of Stock';
                                        $badgeClass = 'badge bg-danger text-white';
                                    } elseif ($currentQty <= 5) {
                                        $status = 'Low Stock';
                                        $badgeClass = 'badge bg-warning text-white';
                                    } else {
                                        $status = 'Available';
                                        $badgeClass = 'badge bg-success text-white';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $row['item']; ?></td>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= $fixedQty; ?></td>
                                        <td><?= $currentQty; ?></td>
                                        <td><span class="<?= $badgeClass ?>"><?= $status ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>
</div>