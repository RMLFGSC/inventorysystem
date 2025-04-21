<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");


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
                <h1 class="h3 mb-0 text-gray-800">Inventory Report</h1>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="GET" action="" class="mb-4">
                        <div class="form-row align-items-end">
                            
                        </div>
                    </form>

                   
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
                    
                </div>
            </div>
        </div>
    </div>

    <?php include("../includes/scripts.php"); ?>
    <?php include("../includes/footer.php"); ?>