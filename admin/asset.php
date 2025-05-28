<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");

// Query
$query = "
    SELECT fa.asset_id, fa.stockin_item, fa.qty, fa.owner, fa.location
    FROM fixed_assets fa
    ORDER BY fa.owner, fa.stockin_item
";

$result = mysqli_query($conn, $query);

?>


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <?php
        include("../includes/topbar.php");
        ?>
        <!-- CONTENT -->
        <div class="container-fluid">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Fixed Assets</h6>
                    <button type="button" class="btn btn-sm btn-primary btn-icon-split" data-toggle="modal" data-target="#GMCAssign">
                        <span class="icon text-white-50">
                            <i class="fas fa-plus fa-sm text-white-50"></i>
                        </span>
                        <span class="text">Add Fixed Assets</span>
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['stockin_item']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm viewAssetBtn"
                                                data-toggle="modal"
                                                data-target="#viewModal">
                                                <i class="fas fa-eye"></i>
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


        <!-- View Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">View Asset Info</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body">
                        <p><strong>Item:</strong> <span id="viewItem"></span></p>
                        <p><strong>Quantity:</strong> <span id="viewQty"></span></p>
                        <p><strong>Status:</strong> <span id="viewStatus">Not Assigned</span></p>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="assignBtn">Assign</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- End of View Modal -->


        <!-- Add Fixed Asset Modal -->
        <div class="modal fade" id="GMCAssign" tabindex="-1" role="dialog" aria-labelledby="GMCAssignLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="GMCAssignLabel">Add Fixed Asset</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Give the form an ID for JS -->
                    <form id="assignForm">
                        <div class="modal-body">
                            <!-- Error message container -->
                            <div id="assignError" class="alert alert-danger" style="display: none;"></div>

                            <div id="fixedAssetFields">
                                <div class="form-row item-row mb-3">
                                    <div class="form-group col-md-8 col-12">
                                        <label for="item">Item</label>
                                        <input type="text" name="item[]" class="form-control" placeholder="Enter item name" required>
                                    </div>
                                    <div class="form-group col-md-4 col-12">
                                        <label for="qty">Quantity</label>
                                        <div class="d-flex align-items-end">
                                            <input type="number" class="form-control" name="qty[]" placeholder="Qty" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <button type="button" class="btn btn-sm btn-secondary" id="addFixedAssetItem">Add Item</button>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary" id="submitAssignBtn">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    <!-- /.container-fluid -->


    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>
</div>

<!-- JavaScript to add more item rows -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addFixedAssetItem').addEventListener('click', function() {
            var container = document.getElementById('fixedAssetFields');

            var newRow = document.createElement('div');
            newRow.classList.add('form-row', 'item-row', 'mb-3');

            newRow.innerHTML = `
                <div class="form-group col-md-8 col-12">
                    <label for="item">Item</label>
                    <input type="text" name="item[]" class="form-control" placeholder="Enter item name" required>
                </div>
                <div class="form-group col-md-4 col-12">
                    <label for="qty">Quantity</label>
                    <div class="d-flex align-items-end">
                        <input type="number" class="form-control" name="qty[]" placeholder="Qty" required>
                        <button type="button" class="btn btn-danger btn-sm removeItem px-2 ml-2">X</button>
                    </div>
                </div>
            `;

            container.appendChild(newRow);
        });

        // Remove row when 'X' button is clicked
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('removeItem')) {
                e.target.closest('.item-row').remove();
            }
        });
    });



    $(document).ready(function() {
        $('#assignForm').submit(function(e) {
            e.preventDefault(); // Prevent normal form submission

            var formData = $(this).serialize();

            $.ajax({
                url: 'assign',
                type: 'POST',
                data: formData,
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        $('#GMCAssign').modal('hide');
                        location.reload();
                    } else {
                        $('#assignError').text(result.message).show();
                    }
                },
                error: function() {
                    $('#assignError').text('Error adding asset.').show();
                }
            });
        });
    });
</script>