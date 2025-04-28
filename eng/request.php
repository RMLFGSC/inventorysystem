<?php
include("../includes/header.php");
include("../includes/navbar_eng.php");

if (!isset($_SESSION['auth_user']['user_id'])) {
    die("Error: User is not logged in. Please log in first.");
}

$req_query = "SELECT req_number FROM request ORDER BY req_id DESC LIMIT 1";
$req_result = mysqli_query($conn, $req_query);
$req_row = mysqli_fetch_assoc($req_result);

if ($req_row) {
    $last_number = (int)substr($req_row['req_number'], 4);
    $new_number = $last_number + 1;
} else {
    $new_number = 1;
}

$formatted_req_number = 'REQ-' . str_pad($new_number, 5, '0', STR_PAD_LEFT);

// Get the current user's ID
$current_user_id = $_SESSION['auth_user']['user_id'];

//query
$query = "SELECT request.req_number, request.date, request.status, request.item_request, request.qty, request.is_posted
          FROM request 
          WHERE request.user_id = '$current_user_id' AND request.req_id IN (SELECT MIN(req_id) 
          FROM request 
          GROUP BY req_number)
          ORDER BY request.status = 0 DESC";
$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- topbar -->
        <?php
        include("../includes/topbar_eng.php");
        ?>


        <!-- ADD MODAL -->
        <div class="modal fade" id="GMCaddRequest" tabindex="-1" role="dialog" aria-labelledby="RequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="RequestModalLabel">Add Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="create" method="POST">
                        <div class="modal-body">
                            <div class="card">
                                <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                                <div class="card-header text-white" style="background-color: #76a73c;">
                                    <strong>Requisition items</strong>
                                </div>
                                <div class="card-body">
                                    <div id="message" class="alert alert-info" style="display: none;">Please fill in the item details below.</div>
                                    <div class="form-group">
                                        <label>Requisition #</label>
                                        <input type="text" name="req_number" class="form-control" value="<?php echo $formatted_req_number; ?>" readonly>
                                    </div>
                                    <div id="itemFields">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Item</label>
                                                <input type="text" name="item_request[]" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Quantity</label>
                                                <input type="text" name="qty[]" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 text-center">
                                        <button type="button" class="btn btn-sm btn-secondary" id="addRequest">Add
                                            Item</button>
                                    </div>

                                    <hr>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="addRequest" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end of add modal -->
        <!-- Start of View Modal -->
        <div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog" aria-labelledby="viewStockinModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewStockinModalLabel">Requisition Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Requested By</label>
                                <input type="text" id="requestedBy" name="fullname" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Department</label>
                                <input type="text" id="department" name="department" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Requisition #</label>
                                <input type="text" id="requisitionNumber" name="req_number" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Date</label>
                                <input type="text" id="date" name="date" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Items</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="requestDetailsBody">
                                    <!-- Dynamic content will be inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-primary" id="printRequest">Print</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of View Modal -->

        <!--Edit modal-->
        <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRequestModalLabel">Edit Requisition Items</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 60%;">Items</th>
                                        <th style="width: 40%;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="edit_request_items">
                                    <!-- Rows will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="saveEditRequest" class="btn btn-sm btn-success">Save Changes</button>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!--End of edit modal-->

        <div class="container-fluid">
            <!-- Table Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Requisition List</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#GMCaddRequest">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Add Request
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Requisition #</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $row['req_number']; ?></td>
                                        <td><?php echo $row['item_request']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td>
                                            <?php
                                            if ($row['status'] == 0) {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            } elseif ($row['status'] == 1) {
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } elseif ($row['status'] == 2) {
                                                echo '<span class="badge badge-danger">Declined</span>';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <?php if ($row['status'] == 0 && $row['is_posted'] == 0): ?>
                                                <button type="button" data-toggle="modal" data-target="#editRequestModal"
                                                    class="btn btn-sm btn-success editRequest"
                                                    data-req_number="<?php echo htmlspecialchars($row['req_number']); ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <?php endif; ?>

                                            <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                                class="btn btn-sm btn-warning viewrequest-btn"
                                                data-req_number="<?php echo htmlspecialchars($row['req_number']); ?>">
                                                <i class="fa-solid fa-eye text-white"></i>
                                            </button>

                                            <?php if ($row['is_posted'] == 0): ?>
                                                <button type="button" class="btn btn-sm btn-info postRequest"
                                                    data-req_number="<?php echo htmlspecialchars($row['req_number']); ?>">
                                                    <i class="fas fa-square-check"></i>
                                                </button>
                                            <?php endif; ?>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add new item row
            document.getElementById('addRequest').addEventListener('click', function() {
                const itemFields = document.getElementById('itemFields');
                const newItemRow = document.createElement('div');
                newItemRow.classList.add('item-row', 'mb-2');

                newItemRow.innerHTML = `
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Item</label>
                        <input type="text" name="item_request[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Quantity</label>
                        <input type="text" name="qty[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <button type="button" class="btn btn-danger btn-sm removeItem">X</button>
                    </div>
                </div>`;

                itemFields.appendChild(newItemRow);

                newItemRow.querySelector('.removeItem').addEventListener('click', function() {
                    itemFields.removeChild(newItemRow);
                });
            });

            // Update view modal functionality
            $('.viewrequest-btn').on('click', function() {
                const reqno = $(this).data('req_number');

                // AJAX call to fetch the requisition details
                $.ajax({
                    url: 'fetch_request_items',
                    type: 'POST',
                    data: {
                        req_number: reqno
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            $('#requestedBy').val(data.requester_name);
                            $('#department').val(data.department);
                            $('#requisitionNumber').val(data.req_number);
                            $('#date').val(data.date);

                            // Populate the items in the table
                            let itemsHtml = '';
                            data.items.forEach(item_request => {
                                itemsHtml += `<tr>
                                                <td>${item_request.item_request}</td>
                                                <td>${item_request.qty}</td>
                                              </tr>`;
                            });
                            $('#requestDetailsBody').html(itemsHtml); 
                        } else {
                            console.error("No data returned from the server.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching requisition details: ", error);
                    }
                });
            });

            // Print request
            document.getElementById('printRequest').addEventListener('click', function() {
                const reqNumber = $('#requisitionNumber').val();
                const requestedBy = $('#requestedBy').val();
                const department = $('#department').val();
                const date = $('#date').val();

                let printContents = document.getElementById('requestDetailsBody').innerHTML;

                let printWindow = window.open('', '', 'height=1000,width=1000');

                printWindow.document.write('<html><head><title>Requisition Print</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: "Arial", sans-serif; margin: 20px; color: #333; text-align: left; }');
                printWindow.document.write('.header { text-align: center; margin-bottom: 40px; }');
                printWindow.document.write('.header h1 { font-size: 20px; color: #000; font-weight: 700; margin-bottom: 5px; }');
                printWindow.document.write('.header h2 { font-size: 15px; color: #666; margin-top: 0; font-weight: 400; }');
                printWindow.document.write('.meta-data { display: flex; justify-content: space-between; font-size: 14px; color: #555; margin-bottom: 20px; padding: 5px 0; border-bottom: 1px solid #eee; }');
                printWindow.document.write('.table-container { margin-top: 20px; width: 100%; border-collapse: collapse; }');
                printWindow.document.write('table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }');
                printWindow.document.write('th, td { padding: 12px 15px; text-align: left; font-size: 12px; border: 1px solid #000; }');
                printWindow.document.write('th { background-color: #f4f4f4; color: #333; font-weight: 600; }');
                printWindow.document.write('tbody tr:nth-child(even) { background-color: #f9f9f9; }');
                printWindow.document.write('.footer { margin-top: 30px; font-size: 12px; color: #555; text-align: center; padding-top: 20px; border-top: 1px solid #ddd; }');
                printWindow.document.write('.footer-signatures { margin-top: 30px; display: flex; justify-content: flex-end; font-size: 14px; }');
                printWindow.document.write('.footer-signatures div { text-align: center; width: 23%; }');
                printWindow.document.write('.footer-signatures div p { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 5px; }');
                printWindow.document.write('@media print { .container { width: 100%; max-width: 100%; } }');
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');

                // Add current date and time
                const currentDate = new Date();
                const formattedDate = currentDate.toLocaleString(); // Format as needed
                printWindow.document.write('<div style="text-align: right; font-size: 12px;">' + formattedDate + '</div>'); // Add date and time

                printWindow.document.write('<div class="container">');
                printWindow.document.write('<div class="header">');
                printWindow.document.write('<h1>GENSAN MEDICAL CENTER</h1>');
                printWindow.document.write('<h2>Requisition Form</h2>');
                printWindow.document.write('</div>');

                printWindow.document.write('<div class="meta-data" style="text-align: left; width: 100%; font-size: 12px;">');
                printWindow.document.write('<div style="float: left; margin-right: 20px;">');
                printWindow.document.write('<div style="margin-bottom: 5px;"><strong>Requested Date:</strong> ' + new Date().toLocaleDateString() + '</div>');
                printWindow.document.write('<div style="margin-bottom: 5px;"><strong>Requisition #:</strong> ' + reqNumber + '</div>');
                printWindow.document.write('<div style="margin-bottom: 5px;"><strong>Requesting Department:</strong> ' + department + '</div>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');

                // Table with normal look
                printWindow.document.write('<table>');
                printWindow.document.write('<thead><tr><th>Item Description</th><th>Quantity</th></tr></thead>');
                printWindow.document.write('<tbody>' + printContents + '</tbody>');
                printWindow.document.write('</table>');

                printWindow.document.write('<div class="footer-signatures" style="font-size: 12px; display: flex; justify-content: flex-end;">');
                printWindow.document.write('<div><strong>Requested By:</strong><br><br>' + requestedBy + '<br>____________________</div>');
                printWindow.document.write('</div>');

                printWindow.document.write('</div>');
                printWindow.document.write('</body></html>');

                printWindow.document.close();
                printWindow.print();
                printWindow.close();
            });

            // Post request with SweetAlert2
            $('.postRequest').click(function() {
                const reqno = $(this).data('req_number');

                Swal.fire({
                    title: 'Post Request?',
                    text: 'Are you sure you want to post this request? Once posted, it can no longer be edited.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Post it',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#28a745',
                    width: '300px',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'post_request',
                            type: 'POST',
                            data: {
                                req_number: reqno,
                                is_posted: 1
                            },
                            success: function(response) {
                                $('.postRequest[data-req_number="' + reqno + '"]').hide();
                                $('.editRequest[data-req_number="' + reqno + '"]').hide();

                                Swal.fire({
                                    title: 'Posted!',
                                    text: 'The request has been posted successfully.',
                                    icon: 'success',
                                    timer: 1500,
                                    width: '300px',
                                    showConfirmButton: false
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Something went wrong while posting.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            $(document).ready(function() {
                // Edit button click event
                $(document).on('click', '.editRequest', function() {
                    let reqNumber = $(this).data('req_number');

                    // Fetch items from the server
                    $.ajax({
                        url: 'edit_request',
                        method: 'POST',
                        data: {
                            req_number: reqNumber
                        },
                        success: function(response) {
                            console.log("Response: ", response); // Debugging line
                            populateEditModal(response); // Ensure this function is defined correctly
                            $('#editRequestModal').data('req_number', reqNumber);
                            $('#editRequestModal').modal('show'); // Show the modal after populating
                        },
                        error: function(error) {
                            console.error('Error fetching items:', error);
                        }
                    });
                });

                // Function to populate the edit modal with data
                function populateEditModal(items) {
                    let rows = '';
                    items.forEach(item => {
                        rows += `
                <tr>
                    <td>${item.item_request}</td>
                    <td><input type="number" class="form-control edit-qty" value="${item.qty}"></td>
                </tr>
            `;
                    });
                    $('#edit_request_items').html(rows); // Populate the table with items
                }

                // to save edited request qty
                $('#saveEditRequest').click(function() {
                    $('#editRequestModal').modal('hide');
                    // SweetAlert confirmation
                    Swal.fire({
                        title: 'Save Changes?',
                        text: 'Are you sure you want to save the changes made to this request?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, save it!',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#28a745',
                        width: '300px',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let editedItems = [];
                            $('#edit_request_items tr').each(function() {
                                let item_request = $(this).find('td:first-child').text();
                                let qty = $(this).find('.edit-qty').val();
                                editedItems.push({
                                    item_request: item_request,
                                    qty: qty
                                });
                            });

                            let reqNumber = $('#editRequestModal').data('req_number');

                            // Debugging: Log the data being sent
                            console.log('Sending data:', {
                                req_number: reqNumber,
                                items: editedItems
                            });

                            $.ajax({
                                url: 'update_request',
                                method: 'POST',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    req_number: reqNumber,
                                    items: editedItems
                                }),
                                success: function(response) {
                                    console.log('Items saved successfully:', response);
                                    $('#editRequestModal').modal('hide');
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'The request has been successfully updated.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                },
                                error: function(error) {
                                    console.error('Error saving items:', error);
                                }
                            });
                        }
                    });
                });
            });
        });
    </script>

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>