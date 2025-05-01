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
                                                data-id="<?php echo htmlspecialchars($row['asset_id']); ?>"
                                                data-owner="<?php echo htmlspecialchars($row['owner']); ?>"
                                                data-location="<?php echo htmlspecialchars($row['location']); ?>"
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

                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">Fixed Asset Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Updated button in view modal -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary btn-sm btn-icon-split" data-toggle="modal" data-target="#GMCassignModal" data-id="<?php echo htmlspecialchars($row['asset_id']); ?>">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user-plus fa-sm text-white-50"></i>
                                </span>
                                <span class="text">Assign</span>
                            </button>
                        </div>

                        <!-- Dynamic content will be inserted here via AJAX -->
                        <div id="assetDetailsContent">
                            <!-- AJAX content -->
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

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
                            <button type="button" class="btn btn-sm btn-primary" id="submitAssignBtn">Save</button>
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
        $('.viewAssetBtn').click(function() {
            var assetId = $(this).data('id');
            $('#assetIdInput').val(assetId);
        });

    });

    $(document).on('click', '.viewAssetBtn', function() {
        var assetId = $(this).data('id');
        var owner = $(this).data('owner');
        var location = $(this).data('location');

        // Make an AJAX call to fetch asset details
        $.ajax({
            url: 'fetch_asset_details',
            type: 'POST',
            data: {
                id: assetId,
                owner: owner,
                location: location
            },
            success: function(response) {
                // Populate the modal with the response data
                $('#viewModal .modal-body').html(response);
                $('#viewModal').modal('show');
            },
            error: function() {
                alert('Error fetching asset details.');
            }
        });
    });
</script>