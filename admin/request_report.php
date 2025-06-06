<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "SELECT req_id, req_number, u.fullname AS requestor, r.qty, r.item_request, u.department, r.status, r.date_issued , r.issued_by, r.date_declined, r.declined_by
          FROM request r 
          JOIN users u ON r.user_id = u.user_id WHERE r.status='1,2'
         ";
if ($startDate) {
    $query .= " AND date_issued >= '$startDate'";
}
if ($endDate) {
    $query .= " AND date_issued <= '$endDate'";
}
$query .= " ORDER BY r.status ASC";

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
                                    <th>Request #</th>
                                    <th>Requestor</th>
                                    <th>Department</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date Approved</th>
                                    <th>Approved By</th>
                                    <th>Date Declined</th>
                                    <th>Declined By</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                    // Status logic
                                    if ($row['status'] == 1) {
                                        $statusText = '<span class="badge badge-success">Approved</span>';
                                    } elseif ($row['status'] == 2) {
                                        $statusText = '<span class="badge badge-danger">Declined</span>';
                                    } else {
                                        $statusText = '<span class="badge badge-warning">Pending</span>';
                                    }
                                ?>
                                    <tr>
                                        <td><?= $row['req_number']; ?></td>
                                        <td><?= $row['requestor']; ?></td>
                                        <td><?= $row['department']; ?></td>
                                        <td><?= $row['item_request']; ?></td>
                                        <td><?= $row['qty']; ?></td>
                                        <td><?= $row['date_issued'] ?: 'N/A'; ?></td>
                                        <td><?= $row['issued_by'] ?: 'N/A'; ?></td>
                                        <td><?= $row['date_declined'] ?: 'N/A'; ?></td>
                                        <td><?= $row['declined_by'] ?: 'N/A'; ?></td>
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