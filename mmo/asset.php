<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

// Query
$query = "
    SELECT fa.owner AS assigned_name, fa.location, COUNT(*) AS total_items
    FROM fixed_assets fa
    GROUP BY fa.owner, fa.location
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
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#GMCAssign">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Add
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Department</th>
                                    <th>Item count</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['assigned_name']; ?></td>
                                        <td><?php echo $row['location']; ?></td>
                                        <td><?php echo $row['total_items']; ?></td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm viewAssetBtn"
                                                data-owner="<?php echo htmlspecialchars($row['assigned_name']); ?>"
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
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="viewModalLabel">Fixed Asset Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" id="assetDetailsContent">
                        <!-- AJAX content here -->
                    </div>

                </div>
            </div>
        </div>

        <!-- Add Fixed Asset Modal -->
        <div class="modal fade" id="GMCAssign" tabindex="-1" role="dialog" aria-labelledby="GMCAssignLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
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

                            <!-- Dynamic item + qty fields -->
                            <div class="card">
                                <div class="card-header text-white" style="background-color: #76a73c;">
                                    <strong>Assign Fixed Asset</strong>
                                </div>
                                <div class="card-body">
                                    <div id="fixedAssetFields">
                                        <div class="form-row item-row mb-3">
                                            <div class="form-group col-md-8 col-12">
                                                <label for="item">Item</label>
                                                <select name="item[]" class="form-control" required>
                                                    <option value="" disabled selected>Select Item</option>
                                                    <?php
                                                    $query = "SELECT item, serialNO FROM stock_in WHERE qty > 0 GROUP BY item, serialNO";
                                                    $result = $conn->query($query);
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='" . $row['serialNO'] . "'>" . $row['item'] . " - " . $row['serialNO'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 col-12">
                                                <label for="qty">Quantity</label>
                                                <input type="number" name="qty[]" class="form-control" placeholder="Qty" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <button type="button" class="btn btn-sm btn-secondary" id="addFixedAssetItem">Add Item</button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- User and Location Fields -->
                            <div class="form-group">
                                <label for="user">User</label>
                                <input type="text" class="form-control" name="user" placeholder="Enter user" required>
                            </div>

                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" name="location" placeholder="Enter location" required>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <!-- Change to button type -->
                            <button type="button" class="btn btn-primary" id="submitAssignBtn">Save</button>
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
                    <select name="item[]" class="form-control" required>
                        option value="" disabled selected>Select Item</option>
                            <?php
                            $query = "SELECT item, serialNO FROM stock_in WHERE qty > 0 GROUP BY item, serialNO";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['serialNO'] . "'>" . $row['item'] . " - " . $row['serialNO'] . "</option>";
                            }
                            ?>
                    </select>
                </div>
                    <div class="form-group col-md-4 col-12">
                        <label for="qty">Quantity</label>
                        <input type="number" class="form-control" name="qty[]" placeholder="Qty" required>
                    </div>
                    <div class="form-group col-md-2 col-12 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm removeItem">X</button>
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

    // Remove item row on button click
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('removeItem')) {
            e.target.closest('.item-row').remove();
        }
    });

    $(document).ready(function() {
        $('#submitAssignBtn').click(function() {
            var formData = $('#assignForm').serialize();

            $.ajax({
                url: 'assign',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#assignError').hide().text('');
                        $('#assignForm')[0].reset();
                        $('#GMCAssign').modal('hide');
                        location.reload(); // refresh page if needed
                    } else {
                        $('#assignError').text(response.error).show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#assignError').text('An unexpected error occurred.').show();
                }
            });
        });
    });
    $('.viewAssetBtn').click(function() {
        var owner = $(this).data('owner');
        var location = $(this).data('location');

        $.ajax({
            url: 'fetch_asset_details',
            type: 'POST',
            data: {
                owner: owner,
                location: location
            },
            success: function(data) {
                $('#assetDetailsContent').html(data);
            },
            error: function() {
                $('#assetDetailsContent').html('<p class="text-danger">Error loading data.</p>');
            }
        });
    });
</script>