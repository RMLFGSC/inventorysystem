<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$viewMode = $_GET['view_mode'] ?? 'summary';

$query = "SELECT item, category, SUM(orig_qty) AS total_orig_qty, SUM(qty) AS total_qty FROM stock_in WHERE is_posted = 1";

// Only filter by date if both are provided
if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND dr >= '$startDate' AND dr <= '$endDate'";
}

$query .= " GROUP BY item, category";

$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <?php include("../includes/topbar.php"); ?>
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Inventory Report</h1>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-4">
                                <label for="start_date">Start:</label>
                                <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate); ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="end_date">End:</label>
                                <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate); ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="view_mode">View Mode:</label>
                                <select id="view_mode" name="view_mode" class="form-control">
                                    <option value="summary" <?= $viewMode === 'summary' ? 'selected' : '' ?>>Summary</option>
                                    <option value="detailed" <?= $viewMode === 'detailed' ? 'selected' : '' ?>>Detailed</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                    </form>

                    <?php if ($viewMode === 'summary'): ?>
                        <!-- SUMMARY VIEW -->
                        <table class="table table-bordered table-hover" id="dataTable" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Total Equipments</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <?php
                                    $currentQty = $row['total_orig_qty'];

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
                                        <td><?= htmlspecialchars($row['item']); ?></td>
                                        <td><?= htmlspecialchars($row['category']); ?></td>
                                        <td><?= $currentQty; ?></td>
                                        <td><span class="<?= $badgeClass ?>"><?= $status ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                    <?php elseif ($viewMode === 'detailed'): ?>
                        <!-- DETAILED VIEW -->
                        <table class="table table-bordered table-hover">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include("../includes/scripts.php"); ?>
    <?php include("../includes/footer.php"); ?>