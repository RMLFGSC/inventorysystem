<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <?php
        include("../includes/topbar.php");

        // Query for all fixed assets
        $query = "
            SELECT fa.*, fa.owner AS assigned_name, fa.department
            FROM fixed_assets fa
        ";
        $result = mysqli_query($conn, $query);

        // Query for unassigned fixed assets with quantity greater than zero
        $unassignedQuery = "
            SELECT r.item_request AS item, r.unassigned_qty
            FROM request r
            WHERE r.status = '1' AND r.date_issued IS NOT NULL AND r.unassigned_qty > 0
        ";
        $unassignedResult = mysqli_query($conn, $unassignedQuery);
        ?>


        <!-- CONTENT -->
        <div class="container-fluid">
        <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Fixed Asset</h6>
                    <button type="button" data-toggle="modal" data-target="#GMCaddAsset" class="btn btn-sm btn-primary assign-btn"><i class="fas fa-plus"></i> Add</button>
                </div>
                <div class="card-body">
                    <div class="table">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                        <tr>
                                            <th>Serial Number</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>User</th>
                                            <th>Department</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td><?php echo $row['serial_number']; ?></td>
                                                <td><?php echo $row['stockin_item']; ?></td>
                                                <td><?php echo $row['qty']; ?></td>
                                                <td><?php echo htmlspecialchars($row['assigned_name']); ?></td>
                                                <td><?php echo $row['department']; ?></td>
                                                
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

<!-- Bootstrap Modal for Assignment -->
<div class="modal fade" id="GMCaddAsset" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">Assign Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group text-left">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Item</label>
                            <input id="modal-item" name="item" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Quantity</label>
                            <input id="modal-qty" name="qty" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group text-left">
                    <label>Owner Name</label>
                    <input id="modal-owner" name="owner" class="form-control" placeholder="Enter Owner Name">
                </div>
                <div class="form-group text-left">
                    <label>Location</label>
                    <input id="modal-department" name="department" class="form-control" placeholder="Enter Equipment location">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmAssign">Assign</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.assign-btn').click(function() {
            let item = $(this).data('item');
            let qty = $(this).data('qty');
            let serial = $(this).closest('tr').find('td:first').text();

            // Set values in the modal
            $('#modal-item').val(item);
            $('#modal-qty').val(qty);
            $('#modal-owner').val('');
            $('#modal-department').val('');
            $('#assignModal').modal('show');

            $('#confirmAssign').off('click').on('click', function() {
                const owner = $('#modal-owner').val().trim();
                const department = $('#modal-department').val().trim();
                const modifiedQty = $('#modal-qty').val().trim();

                if (!serial || !owner || !department || !modifiedQty) {
                    alert('Please fill in all fields');
                    return;
                }

                // Send to PHP using AJAX
                $.ajax({
                    url: 'assign.php',
                    method: 'POST',
                    data: {
                        stockin_item: item,
                        qty: modifiedQty,
                        serial: serial,
                        owner: owner,
                        department: department
                    },
                    success: function(response) {
                        Swal.fire('Assigned!', 'The item has been assigned successfully.', 'success')
                            .then(() => {
                                location.reload(); // Optional: refresh to update table
                            });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Something went wrong. Try again.', 'error');
                        console.error(error);
                    }
                });

                $('#GMCaddAsset').modal('hide');
            });
        });
    });
</script>