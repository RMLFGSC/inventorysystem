<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "SELECT req_id, req_number, u.fullname AS requestor, r.qty, s.item, s.category, u.department, r.status, r.date_issued 
          FROM request r 
          JOIN users u ON r.user_id = u.user_id 
          JOIN stock_in s ON r.stockin_id = s.stockin_id WHERE 1=1";
if ($startDate) {
    $query .= " AND date_approved >= '$startDate'"; 
}
if ($endDate) {
    $query .= " AND date_approved <= '$endDate'";
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
                <h1 class="h3 mb-0 text-gray-800">Request Report</h1>
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
                                    <th>Request #</th>
                                    <th>Requestor</th>
                                    <th>Department</th>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Date Approved</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Status logic
                                    $statusText = $row['status'] ? 'Approved' : 'Pending'; 
                                ?>
                                    <tr>
                                        <td><?= $row['req_number']; ?></td>
                                        <td><?= $row['requestor']; ?></td>
                                        <td><?= $row['department']; ?></td>
                                        <td><?= $row['item']; ?></td>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= $row['qty']; ?></td>
                                        <td><?= $row['date_issued']; ?></td>
                                        <td><?= $statusText; ?></td>
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
