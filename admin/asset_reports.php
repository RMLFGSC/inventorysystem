<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

$query = "SELECT item, category, SUM(orig_qty) AS total_orig_qty, SUM(qty) AS total_qty FROM stock_in WHERE is_posted = 1";

$query .= " GROUP BY item, category";

$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include("../includes/topbar.php"); ?>
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Fixed Assets Report</h1>
                <button type="button" class="btn btn-sm btn-primary btn-icon-split" data-toggle="modal" data-target="#GMCaddRequest">
                    <span class="icon text-white-50">
                        <i class="fas fa-download fa-sm text-white-50"></i>
                    </span>
                    <span class="text">Export Report</span>
                </button>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="thead-light">
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>User</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // UNASSIGNED ITEMS - from stock_in table only
                            $unassignedQuery = "
                                SELECT item, COUNT(*) AS qty
                                FROM stock_in
                                WHERE is_posted = 1
                                GROUP BY item
                            ";

                            $unassignedResult = mysqli_query($conn, $unassignedQuery);
                            while ($row = mysqli_fetch_assoc($unassignedResult)) {
                                // Get how many are assigned from fixed_assets
                                $item = $row['item'];
                                $qty = $row['qty'];

                                $assignedQuery = "SELECT SUM(qty) AS assigned_qty FROM fixed_assets WHERE stockin_item = '$item'";
                                $assignedResult = mysqli_query($conn, $assignedQuery);
                                $assignedRow = mysqli_fetch_assoc($assignedResult);
                                $assignedQty = $assignedRow['assigned_qty'] ?? 0;

                                $unassignedQty = $qty - $assignedQty;

                                if ($unassignedQty > 0) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($item) . "</td>
                                    <td>" . $unassignedQty . "</td>
                                    <td>N/A</td>
                                    <td>Stockroom</td>
                                    <td>Unassigned</td>
                                </tr>";
                                }
                            }

                            // ASSIGNED ITEMS - from fixed_assets table only
                            $assignedQuery = "SELECT stockin_item AS item, qty, owner, location FROM fixed_assets";
                            $assignedResult = mysqli_query($conn, $assignedQuery);
                            while ($drow = mysqli_fetch_assoc($assignedResult)):
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($drow['item']); ?></td>
                                    <td><?= htmlspecialchars($drow['qty']); ?></td>
                                    <td><?= htmlspecialchars($drow['owner']); ?></td>
                                    <td><?= htmlspecialchars($drow['location']); ?></td>
                                    <td>Assigned</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include("../includes/scripts.php"); ?>
    <?php include("../includes/footer.php"); ?>