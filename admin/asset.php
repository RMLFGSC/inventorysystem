<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <?php
        include("../includes/topbar.php");

        // Query for all fixed assets
        $query = "
            SELECT fa.*, u.fullname AS assigned_name
            FROM fixed_assets fa
            JOIN users u ON fa.assigned_to = u.user_id
        ";
        $result = mysqli_query($conn, $query);

        // Query for unassigned fixed assets
        $unassignedQuery = "
            SELECT s.item, r.qty
            FROM stock_in s
            JOIN request r ON s.stockin_id = r.stockin_id
            WHERE r.status = '1' AND r.date_issued IS NOT NULL
        ";
        $unassignedResult = mysqli_query($conn, $unassignedQuery);
        ?>

        <!-- ADD MODAL -->
        <div class="modal fade" id="GMCaddMR" tabindex="-1" role="dialog" aria-labelledby="ItemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ItemModalLabel">Add Fixed Asset</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="actions/addmr.php" method="POST">
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header text-white" style="background-color: #76a73c;">
                                    <strong>Fixed Assets</strong>
                                </div>
                                <div class="card-body">
                                    <div id="itemFields">
                                        <div class="form-row item-row">
                                            <div class="form-group col-md-6">
                                                <label>Item</label>
                                                <select name="stockin_item" class="form-control" required>
                                                    <?php
                                                    $itemQuery = "
                                                        SELECT DISTINCT s.item
                                                        FROM request r 
                                                        JOIN stock_in s ON r.stockin_id = s.stockin_id 
                                                        WHERE r.status = '1'
                                                    ";
                                                    $itemResult = mysqli_query($conn, $itemQuery);
                                                    while ($itemRow = mysqli_fetch_assoc($itemResult)): ?>
                                                        <option value="<?php echo htmlspecialchars($itemRow['item']); ?>">
                                                            <?php echo htmlspecialchars($itemRow['item']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Serial Number</label>
                                                <input type="text" name="serial_number" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Assign to</label>
                                    <select name="assigned_to" class="form-control">
                                        <option value="0">Unassigned</option>
                                        <?php
                                        $userQuery = "SELECT user_id, fullname FROM users";
                                        $userResult = mysqli_query($conn, $userQuery);
                                        while ($userRow = mysqli_fetch_assoc($userResult)): ?>
                                            <option value="<?php echo htmlspecialchars($userRow['user_id']); ?>">
                                                <?php echo htmlspecialchars($userRow['fullname']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Location</label>
                                    <input type="text" name="location" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="addMR" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end of add modal -->

        <!-- View Product Modal -->
        <div class="modal fade" id="viewMRModal" tabindex="-1" role="dialog" aria-labelledby="viewMRModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewMRModalLabel">View Fixed Asset Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Name and Description</th>
                                        <th class="text-center">Serial Number</th>
                                    </tr>
                                </thead>
                                <tbody id="view_items_table">
                                    <!-- Items will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <div class="form-group">
                                <label><strong>Owner:</strong></label>
                                <div><span id="view_name"></span></div>
                            </div>
                            <div class="form-group">
                                <label><strong>Department</strong></label>
                                <div><span id="view_ip"></span></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--End of view modal-->

        <!-- CONTENT -->
        <div class="container-fluid">
            <div class="row">
                <!-- Fixed Assets Table (Left side - larger) -->
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Fixed Assets</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>User</th>
                                            <th>Location</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                    <td><?php echo htmlspecialchars($row['assigned_name']); ?></td>
                                                <td><?php echo $row['location']; ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-warning fixed-btn"
                                                        data-toggle="modal" 
                                                        data-target="#viewMRModal"
                                                        data-qty='<?php echo htmlspecialchars(json_encode($row['qty']), ENT_QUOTES); ?>'
                                                        data-item='<?php echo htmlspecialchars(json_encode($row['stockin_item']), ENT_QUOTES); ?>'
                                                        data-serialno='<?php echo htmlspecialchars(json_encode($row['serial_number']), ENT_QUOTES); ?>'
                                                        data-name="<?php echo htmlspecialchars($row['assigned_name']); ?>"
                                                        data-ip="<?php echo htmlspecialchars($row['location']); ?>">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Unassigned Fixed Assets Table -->
                    <div class="col-md-4">
                        <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Unassigned items</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = mysqli_fetch_assoc($unassignedResult)): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['item']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['qty']); ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-primary btn-sm">Assign</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
            </div> <!-- /.row -->
        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>
</div>
