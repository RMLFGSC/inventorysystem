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
                                        <td><?php echo $row['owner']; ?></td>
                                        <td><?php echo $row['location']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td><button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#viewModal">
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

        <!-- Modal Structure -->
        <div class="modal fade" id="GMCAssign" tabindex="-1" role="dialog" aria-labelledby="GMCAssignLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="GMCAssignLabel">Add Fixed Asset</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Error Message Display -->
                        <div id="errorMsg" class="alert alert-danger" style="display: none;"></div>
                        <form action="assign" method="POST">
                            <div class="form-row">
                                <div class="form-group col-md-8 col-12">
                                    <label for="item">Item</label>
                                    <select name="item" class="form-control" required>
                                        <option value="" disabled selected>Select Item</option>
                                        <?php
                                        $query = "SELECT item FROM stock_in WHERE qty > 0 GROUP BY item";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='" . $row['item'] . "'>" . $row['item'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4 col-12">
                                    <label for="qty">Quantity</label>
                                    <input type="number" class="form-control" id="qty" name="qty" placeholder="Qty" required>
                                </div>
                            </div>

                            <!-- Hidden Serial Number Input -->
        <input type="hidden" id="serialNumber" name="serial">

                            <div class="form-group">
                                <label for="user">User</label>
                                <input type="text" class="form-control" id="user" name="user" placeholder="Enter user" required>
                            </div>

                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="Enter location" required>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
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
    document.getElementById('addItem').addEventListener('click', function() {
        var container = document.getElementById('itemContainer');

        var newRow = document.createElement('div');
        newRow.classList.add('form-row', 'item-row', 'mb-3');

        newRow.innerHTML = `
            <div class="form-group col-md-8 col-12">
                <label for="item">Item</label>
                <select class="form-control" name="item[]" required>
                    <option value="" selected disabled>Select item</option>
                    <?php
                    $sql = "SELECT DISTINCT item FROM stock_in";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['item'] . "'>" . $row['item'] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No items available</option>";
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

        container.insertBefore(newRow, container.lastElementChild);
    });

    // Remove item row on button click
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('removeItem')) {
            e.target.closest('.item-row').remove();
        }
    });

    $('#saveButton').click(function(e) {
        e.preventDefault(); // Prevent the form from submitting

        var formData = $('#assignForm').serialize();

        $.ajax({
            url: 'assign.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.success);
                    $('#assignForm')[0].reset();
                    $('#addModal').modal('hide'); 
                    location.reload(); 
                } else if (response.error) {
                    // Display the error inside the modal
                    $('#errorMsg').text(response.error).show();
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                $('#errorMsg').text('Something went wrong. Please try again.').show();
            }
        });
    });
    
</script>