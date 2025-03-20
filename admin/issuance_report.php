<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

// Modify the SQL query to include date filtering and join with users and fixed_assets tables
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$query = "
    SELECT 
        r.req_number, 
        u.fullname AS requestor, 
        u.department, 
        s.item, 
        s.orig_qty,
        r.issued_by, 
        r.date_issued, 
        fa.owner, 
        fa.location, 
        r.status 
    FROM 
        request r 
    JOIN 
        users u ON r.user_id = u.user_id 
    LEFT JOIN 
        fixed_assets fa ON r.req_number = fa.req_id 
    LEFT JOIN
        stock_in s ON r.stockin_id = s.stockin_id
    WHERE 
        1=1"; // Assuming 'requestor_id' in request table corresponds to 'id' in users table

if ($startDate) {
    $query .= " AND r.date_issued >= '$startDate'"; // Assuming 'date_issued' is the column name for the date
}
if ($endDate) {
    $query .= " AND r.date_issued <= '$endDate'";
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
                <h1 class="h3 mb-0 text-gray-800">Issuance Report</h1>
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
                                    <th>Req Number</th>
                                    <th>Requestor</th>
                                    <th>Department</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Issued By</th>
                                    <th>Date Issued</th>
                                    <th>Assigned To</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = mysqli_fetch_assoc($result)):
                                ?>
                                    <tr>
                                        <td><?= $row['req_number']; ?></td>
                                        <td><?= $row['requestor']; ?></td>
                                        <td><?= $row['department']; ?></td>
                                        <td><?= $row['item']; ?></td>
                                        <td><?= $row['orig_qty']; ?></td>
                                        <td><?= $row['issued_by']; ?></td>
                                        <td><?= $row['date_issued']; ?></td>
                                        <td><?= $row['owner']; ?></td>
                                        <td><?= $row['location']; ?></td>
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
