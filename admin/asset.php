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
            SELECT fa.*, fa.owner AS assigned_name, fa.department
            FROM fixed_assets fa
        ";
        $result = mysqli_query($conn, $query);

        // Query for unassigned fixed assets with quantity greater than zero
        $unassignedQuery = "
            SELECT s.item, r.unassigned_qty
            FROM stock_in s
            JOIN request r ON s.stockin_id = r.stockin_id
            WHERE r.status = '1' AND r.date_issued IS NOT NULL AND r.unassigned_qty > 0
        ";
        $unassignedResult = mysqli_query($conn, $unassignedQuery);
        ?>


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
                                                <td><?php echo htmlspecialchars($row['unassigned_qty']); ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-primary btn-sm assign-btn"
                                                        data-item="<?php echo htmlspecialchars($row['item']); ?>"
                                                        data-qty="<?php echo htmlspecialchars($row['unassigned_qty']); ?>">
                                                        Assign
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

<script>
    $(document).ready(function() {
        $('.assign-btn').click(function() {
            let item = $(this).data('item');
            let qty = $(this).data('qty');

            Swal.fire({
                title: 'Assign Item',
                html: `
                <div class="form-group text-left">
                    <div class="row">
                    <div class="col-md-6">
                        <label>Item</label>
                        <input id="swal-item" name="item" class="form-control" value="${item}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Quantity</label>
                        <input id="swal-qty" name="qty" class="form-control" value="${qty}">
                    </div>
                    </div>
                </div>
                <div class="form-group text-left">
                    <label>Serial Number</label>
                    <input id="swal-serial" name="serial_number" class="form-control" placeholder="Enter Serial Number">
                </div>
                <div class="form-group text-left">
                    <label>Owner Name</label>
                    <input id="swal-owner" name="owner" class="form-control" placeholder="Enter Owner Name">
                </div>
                <div class="form-group text-left">
                    <label>Department</label>
                    <input id="swal-department" name="department" class="form-control" placeholder="Enter Department">
                </div>
                `,

                showCancelButton: true,
                confirmButtonText: 'Assign',
                preConfirm: () => {
                    const serial = document.getElementById('swal-serial').value.trim();
                    const owner = document.getElementById('swal-owner').value.trim();
                    const department = document.getElementById('swal-department').value.trim();
                    const modifiedQty = document.getElementById('swal-qty').value.trim();

                    if (!serial || !owner || !department || !modifiedQty) {
                        Swal.showValidationMessage('Please fill in all fields');
                        return false;
                    }

                    // Return data to the .then() block
                    return {
                        item: item,
                        qty: modifiedQty,
                        serial: serial,
                        owner: owner,
                        department: department
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;

                    // Send to PHP using AJAX
                    $.ajax({
                        url: 'assign.php',
                        method: 'POST',
                        data: {
                            stockin_item: formData.item,
                            qty: formData.qty,
                            serial: formData.serial,
                            owner: formData.owner,
                            department: formData.department
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
                }
            });
        });
    });
</script>