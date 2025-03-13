<?php
include("../includes/header.php");
include("../includes/navbar_admin.php");
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- topbar -->
        <?php
        include("../includes/topbar.php");

        // query
        $query = "SELECT * FROM users";
        $result = mysqli_query($conn, $query);
        ?>


        <!-- ADD MODAL-->
        <div class="modal fade" id="GMCadduser" tabindex="-1" role="dialog" aria-labelledby="ItemModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ItemModalLabel">Add New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="create.php" method="POST">
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" name="fullname" class="form-control" required>
                                </div>
                               
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Password</label>
                                    <input type="password" name="pword" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Phone Number</label>
                                    <input type="text" name="number" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Department</label>
                                    <input type="text" name="department" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" id="roleSelect" required class="form-control" onchange="toggleBranchField(this)">
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="mmo">MMO</option>
                                    <option value="engineering">Engineering</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="adduser" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end of add modal -->

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to remove this user?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- end of delete confirmation modal -->

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Users</h1>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#GMCadduser">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Add
                </button>
            </div>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatables-basic table" id="dataTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Department</th>
                                    <th>User Role</th>
                                    <th>Actions</th>

                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['fullname']; ?></td>
                                        <td><?php echo $row['username']; ?></td>
                                        <td><?php echo $row['department']; ?></td>
                                        <td>
                                            <?php
                                            switch ($row['role']) {
                                                case 'admin':
                                                    echo 'Admin';
                                                    break;
                                                case 'mmo':
                                                    echo 'MMO';
                                                    break;
                                                case 'engineering':
                                                    echo 'Engineering';
                                                    break;
                                                case 'user':
                                                    echo 'User';
                                                        break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#editModal" class="btn btn-sm btn-success editproduct-btn"><i class="fa-solid fa-edit"></i></button>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#viewProductModal" class="btn btn-sm btn-warning viewproduct-btn"><i class="fa-solid fa-eye text-white"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['user_id']; ?>" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal"><i class="fa-solid fa-trash text-white"></i></button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->


    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>

<script>
    // JavaScript to handle delete confirmation
    let userIdToDelete;

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            userIdToDelete = this.getAttribute('data-id');
        });
    });

    document.getElementById('confirmDelete').addEventListener('click', function() {
        // Make an AJAX request to archive the user
        fetch('archive_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: userIdToDelete }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the user from the table
                document.querySelector(`button[data-id="${userIdToDelete}"]`).closest('tr').remove();
                $('#deleteConfirmationModal').modal('hide');
            } else {
                alert('Error archiving user.');
            }
        });
    });
</script>