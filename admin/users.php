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

        // query to select only visible users
        $query = "SELECT * FROM users WHERE is_hide = 0";
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

                    <form action="create" method="POST">
                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" name="fullname" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>

                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Password</label>
                                    <input type="password" name="pword" class="form-control" required>
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
                                    <option value="it">IT</option>
                                    <option value="engineering">Engineering</option>
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

        <!-- EDIT MODAL -->
        <div class="modal fade" id="GMCedituser" tabindex="-1" role="dialog" aria-labelledby="EditUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="EditUserModalLabel">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form id="editUserForm" method="POST" action="edit.php">
                        <div class="modal-body">
                            <!-- Hidden ID Field -->
                            <input type="hidden" name="edit_id" id="edit_id">

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Full Name</label>
                                    <input type="text" name="edit_fullname" id="edit_fullname" class="form-control" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Username</label>
                                    <input type="text" name="edit_username" id="edit_username" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Password</label>
                                    <input type="password" name="edit_pword" id="edit_pword" class="form-control" placeholder="only fill to update">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Department</label>
                                    <input type="text" name="edit_department" id="edit_department" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select name="edit_role" id="edit_role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="mmo">MMO</option>
                                    <option value="it">IT</option>
                                    <option value="engineering">Engineering</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_user" id="updateUserBtn" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <!-- END OF EDIT MODAL -->
        

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="user_hide.php" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="user_id" id="deleteUserId">
                            Are you sure you want to hide this user?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Yes, Hide</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="container-fluid">

            <!-- Table Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#GMCadduser">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Add User
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Department</th>
                                    <th>User Role</th>
                                    <th class="text-center">Actions</th>
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
                                                case 'it':
                                                    echo 'IT';
                                                    break;
                                                case 'engineering':
                                                    echo 'Engineering';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success edituser-btn"
                                                data-toggle="modal"
                                                data-target="#GMCedituser"
                                                data-id="<?php echo $row['user_id']; ?>"
                                                data-fullname="<?php echo $row['fullname']; ?>"
                                                data-username="<?php echo $row['username']; ?>"
                                                data-department="<?php echo $row['department']; ?>"
                                                data-role="<?php echo $row['role']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger delete-btn"
                                                data-id="<?php echo $row['user_id']; ?>"
                                                data-toggle="modal"
                                                data-target="#deleteConfirmationModal">
                                                <i class="fas fa-trash-alt text-white"></i>
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



    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");

    ?>


    <script>
        $('#GMCedituser').on('shown.bs.modal', function() {
                $('#updateUserBtn').off('click').on('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save these changes?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!',
                        width: '300px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#editUserForm')[0].submit();
                        }
                    });
                });
            });

        $(document).ready(function() {
            $(".delete-btn").click(function() {
                var userId = $(this).data("id");
                $("#deleteUserId").val(userId);
            });
        });

        $(document).ready(function() {
            $('.edituser-btn').on('click', function() {
                var userId = $(this).data('id');
                var fullname = $(this).data('fullname');
                var username = $(this).data('username');
                var department = $(this).data('department');
                var role = $(this).data('role');

                // Populate modal fields
                $('#edit_id').val(userId);
                $('#edit_fullname').val(fullname);
                $('#edit_username').val(username);
                $('#edit_department').val(department);
                $('#edit_role').val(role);
            });
        });
    </script>