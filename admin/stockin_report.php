<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "SELECT * FROM stock_in
         ";
if ($startDate) {
    $query .= " AND dr >= '$startDate'";
}
if ($endDate) {
    $query .= " AND dr <= '$endDate'";
}
$query .= " ORDER BY stock_in.dr ASC";

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
                <h1 class="h3 mb-0 text-gray-800">Stock-in Report</h1>
                <button type="button" class="btn btn-sm btn-primary btn-icon-split" data-toggle="modal" data-target="#GMCaddRequest">
                    <span class="icon text-white-50">
                        <i class="fas fa-download fa-sm text-white-50"></i>
                    </span>
                    <span class="text">Export Report</span>
                </button>
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
                        <table class="table table-hover table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Control Number</th>
                                    <th>Serial Number</th>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Date Purchased</th>
                                    <th>Date Received</th>
                                    <th>Warranty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Status logic
                                    if ($row['warranty'] == 1) {
                                        $warranty = '<span class="badge badge-success">Yes</span>';
                                    } else {
                                        $warranty = '<span class="badge badge-danger">No</span>';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $row['controlNO']; ?></td>
                                        <td><?= $row['serialNO']; ?></td>
                                        <td><?= $row['item']; ?></td>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= $row['dop']; ?></td>
                                        <td><?= $row['dr'];?></td>
                                        <td><?= $warranty; ?></td>
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