<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

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

//query
$query = "SELECT request.*, users.fullname AS requester_name, users.department, stock_in.item
          FROM request 
          JOIN users ON request.user_id = users.user_id
          JOIN stock_in ON request.stockin_id = stock_in.stockin_id
          WHERE req_id IN (SELECT MIN(req_id) FROM request GROUP BY req_number) 
          AND request.is_posted = 1
          ORDER BY status ASC, req_number DESC";
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



        <div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog"
            aria-labelledby="viewRequestModalLabel" aria-hidden="true">
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
                                <input type="text" id="requisitionNumber" name="req_number" class="form-control"
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Date</label>
                                <input type="text" id="date" name="date" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Issued By</label>
                                <input type="text" id="issuedBy" name="issued_by" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Issued Date</label>
                                <input type="text" id="issuedDate" name="issued_date" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Declined By</label>
                                <input type="text" id="declinedBy" name="declined_by" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label>Decline Date</label>
                                <input type="text" id="declineDate" name="decline_date" class="form-control" readonly>
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
                                <tbody id="view_request_items">
                                    <!-- Rows will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex w-100">
                            <div>
                                <div id="action-buttons" class="text-right mt-3">
                                    <button id="saveRequest" class="btn btn-sm btn-success">Approve</button>
                                    <button id="confirmDecline" class="btn btn-sm btn-danger">Decline</button>
                                </div>
                            </div>
                            <div class="ml-auto d-flex align-items-center gap-2">
                                <!-- Print button - initially hidden -->
                                <button id="printRequestBtn" class="btn btn-sm btn-secondary mr-2" style="display: none;">
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End of view modal-->
        <div class="container-fluid">
 
    <!-- Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Issuance List</h6>
        </div>
        <div class="card-body">
            <div class="table">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Req Number</th>
                            <th>Requester</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['req_number']; ?></td>
                                <td><?php echo $row['requester_name']; ?></td>
                                <td><?php echo $row['department']; ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td>
                                    <?php
                                    if ($row['status'] == 0) {
                                        echo '<span class="badge badge-warning">Pending</span>';
                                    } elseif ($row['status'] == 1) {
                                        echo '<span class="badge badge-success">Served</span>';
                                    } elseif ($row['status'] == 2) {
                                        echo '<span class="badge badge-danger">Declined</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status'] == 0): // Pending ?>
                                        <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                            class="btn btn-sm btn-success viewrequest-btn"
                                            data-id="<?php echo $row['req_id']; ?>"
                                            data-req_number="<?php echo $row['req_number']; ?>"
                                            data-status="<?php echo $row['status']; ?>">
                                            <i class="fa fa-check text-white"></i>
                                        </button>
                                    <?php else: // Served or Declined ?>
                                        <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                            class="btn btn-sm btn-warning viewrequest-btn"
                                            data-id="<?php echo $row['req_id']; ?>"
                                            data-req_number="<?php echo $row['req_number']; ?>"
                                            data-status="<?php echo $row['status']; ?>">
                                            <i class="fa-solid fa-eye text-white"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($row['status'] == 0): // If Pending ?>
                                        <button type="button" data-toggle="modal" data-target="#editRequestModal"
                                            class="btn btn-sm btn-primary editrequest-btn"
                                            data-id="<?php echo $row['req_id']; ?>"
                                            data-req_number="<?php echo $row['req_number']; ?>">
                                            <i class="fa-solid fa-pencil-alt"></i>
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

    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>

    <script>
        $(document).ready(function() {
            // View request modal functionality
            $('.viewrequest-btn').on('click', function() {
                const reqno = $(this).data('req_number');
                const reqId = $(this).data('id');
                const status = $(this).data('status');

                // Store the request ID inside the modal
                $('#viewRequestModal').data('id', reqId);

                // Fetch request items and details via AJAX
                $.ajax({
                    url: 'fetch_request_items.php', // Update to the correct PHP file
                    type: 'POST',
                    data: {
                        req_number: reqno
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(data) {
                        if (data) {
                            $('#requestedBy').val(data.requester_name); 
                            $('#department').val(data.department); 
                            $('#requisitionNumber').val(data.req_number); 
                            $('#date').val(data.date || 'N/A'); // Set date or N/A

                            // Check the status and show/hide relevant fields
                            if (status == 1) { // If the status is "Approved"
                                $('#issuedBy').val(data.issued_by || 'N/A'); 
                                $('#issuedDate').val(data.date_issued || 'N/A'); 
                                $('#declinedBy').val('N/A'); 
                                $('#declineDate').val('N/A'); 
                                $('#issuedBy').closest('.row').show(); 
                                $('#issuedDate').closest('.row').show(); 
                                $('#declinedBy').closest('.row').hide(); 
                                $('#declineDate').closest('.row').hide(); 
                                $('#printRequestBtn').show(); 
                                $('#action-buttons').hide(); 
                            } else if (status == 2) { // If the status is "Declined"
                                $('#declinedBy').val(data.declined_by || 'N/A'); 
                                $('#declineDate').val(data.date_declined || 'N/A'); 
                                $('#issuedBy').val('N/A'); 
                                $('#issuedDate').val('N/A'); 
                                $('#declinedBy').closest('.row').show(); 
                                $('#declineDate').closest('.row').show(); 
                                $('#issuedBy').closest('.row').hide(); 
                                $('#issuedDate').closest('.row').hide(); 
                                $('#printRequestBtn').hide(); 
                                $('#action-buttons').hide(); 
                            } else { // If the status is "Pending"
                                $('#issuedBy').val('N/A'); 
                                $('#issuedDate').val('N/A'); 
                                $('#declinedBy').val('N/A'); 
                                $('#declineDate').val('N/A'); 
                                $('#issuedBy').closest('.row').hide(); 
                                $('#issuedDate').closest('.row').hide(); 
                                $('#declinedBy').closest('.row').hide(); 
                                $('#declineDate').closest('.row').hide(); 
                                $('#printRequestBtn').hide(); 
                                $('#action-buttons').show(); 
                            }

                            // Populate the items in the table
                            let itemsHtml = '';
                            data.items.forEach(item => {
                                itemsHtml += `<tr>
                                                <td>${item.item}</td>
                                                <td>${item.qty}</td>
                                              </tr>`;
                            });
                            $('#view_request_items').html(itemsHtml); 
                            $('#viewRequestModal').data('id', reqId); 
                        } else {
                            console.error("No data returned from the server.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching request items: ", error);
                    }
                });
            });

            // Decline button handler
            $('#confirmDecline').on('click', function() {
                const requestId = $('#viewRequestModal').data('id');

                // Hide the modal before prompting for the declined by name
                $('#viewRequestModal').modal('hide');

                Swal.fire({
                    title: 'Enter Declined By',
                    input: 'text',
                    inputPlaceholder: 'Enter name',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    preConfirm: (declinedBy) => {
                        if (!declinedBy) {
                            Swal.showValidationMessage('Please enter a valid name for "Declined By".');
                        }
                        return declinedBy;
                    }
                }).then((inputResult) => {
                    if (inputResult.isConfirmed) {
                        const declinedBy = inputResult.value.trim();

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You want to decline this request?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Decline',
                            cancelButtonText: 'Cancel',
                            width: '300px',
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'update_status.php',
                                    type: 'POST',
                                    data: {
                                        id: requestId,
                                        status: 2,
                                        declined_by: declinedBy, 
                                        date_declined: new Date().toISOString().slice(0, 10) 
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Declined!',
                                            text: 'The request has been declined by: ' + declinedBy,
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload();
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Approve button handler with SweetAlert
            $('#saveRequest').on('click', function() {
                $('#viewRequestModal').modal('hide');

                const requestId = $('#viewRequestModal').data('id');
                if (!requestId) {
                    console.error("No request ID found!");
                    return;
                }

                Swal.fire({
                    title: 'Enter Issued By',
                    input: 'text',
                    inputPlaceholder: 'Enter name',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    preConfirm: (issuedBy) => {
                        if (!issuedBy) {
                            Swal.showValidationMessage('Please enter a valid name for "Issued By".');
                        }
                        return issuedBy;
                    }
                }).then((inputResult) => {
                    if (inputResult.isConfirmed) {
                        const issuedBy = inputResult.value.trim();

                        const itemsToDeduct = [];
                        $('#view_request_items tr').each(function() {
                            const itemId = $(this).data('item_id');
                            const quantity = $(this).find('td:eq(1)').text().trim();
                            itemsToDeduct.push({
                                id: itemId,
                                qty: quantity
                            });
                        });

                        $.ajax({
                            url: 'update_status.php',
                            type: 'POST',
                            data: {
                                id: requestId,
                                status: 1,
                                date_issued: new Date().toISOString().slice(0, 10),
                                issued_by: issuedBy,
                                items: itemsToDeduct
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: 'The request has been approved and issued by: ' + issuedBy,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error("Error updating status: ", error);
                            }
                        });
                    }
                });

                // Manually focus on the input field after the modal appears
                setTimeout(() => {
                    document.querySelector('#swal2-input').focus();
                }, 100);
            });

            // printing
            // Print button functionality
        $('#printRequestBtn').on('click', function () {
            const reqNumber = $('#requisitionNumber').val();
            const requestedBy = $('#requestedBy').val();
            const department = $('#department').val();
            const date = $('#date').val();

            let printContents = document.getElementById('view_request_items').innerHTML;

            let printWindow = window.open('', '', 'height=1000,width=1000');

            printWindow.document.write('<html><head><title>.</title>');
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
            printWindow.document.write('.footer-signatures { margin-top: 30px; display: flex; justify-content: space-between; font-size: 14px; }');
            printWindow.document.write('.footer-signatures div { text-align: center; width: 23%; }');
            printWindow.document.write('.footer-signatures div p { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 5px; }');
            printWindow.document.write('@media print { .container { width: 100%; max-width: 100%; } }');

            // force landscape printing
            printWindow.document.write('@page { size: A4 landscape; }');

            printWindow.document.write('</style>');
            printWindow.document.write('</head><body>');

            // Add current date and time
            const currentDate = new Date();
            const formattedDate = currentDate.toLocaleString(); 
            printWindow.document.write('<div style="text-align: right; font-size: 12px;">' + formattedDate + '</div>'); 

            printWindow.document.write('<div class="container">');
            printWindow.document.write('<div class="header" style="margin-bottom: 20px;">');
            printWindow.document.write('<h1>GENSAN MEDICAL CENTER</h1>');
            printWindow.document.write('<div><strong>Issued Date:</strong> ' + new Date().toLocaleDateString() + '</div>');
            printWindow.document.write('</div>');

            printWindow.document.write('<div class="meta-data" style="text-align: left; width: 100%;">');
            printWindow.document.write('<div style="float: left; margin-right: 20px;">');
            printWindow.document.write('<div style="margin-bottom: 5px;"><strong>Requisition #:</strong> ' + reqNumber + '</div>');
            printWindow.document.write('<div style="margin-bottom: 5px;"><strong>Requesting Department:</strong> ' + department + '</div>');
            printWindow.document.write('</div>');
            printWindow.document.write('</div>');

            // Table with normal look
            printWindow.document.write('<table>');
            printWindow.document.write('<thead><tr><th>Item Description</th><th>Quantity</th></tr></thead>');
            printWindow.document.write('<tbody>' + printContents + '</tbody>');
            printWindow.document.write('</table>');

            printWindow.document.write('<div class="footer-signatures" style="font-size: 12px;">');
            printWindow.document.write('<div><strong>Requested By:</strong><br>' + requestedBy + '<br>____________________</div>');
            printWindow.document.write('<div><strong>Received By:</strong><br>____________________</div>');
            printWindow.document.write('<div><strong>Issued By:</strong><br>____________________</div>');
            printWindow.document.write('<div><strong>Approved By:</strong><br>____________________</div>');
            printWindow.document.write('</div>');

            printWindow.document.write('</div>'); 
            printWindow.document.write('</body></html>');

            printWindow.document.close();
            printWindow.print();
            printWindow.close();
            });

           // Edit button handler
            $('.editrequest-btn').on('click', function() {
                const reqNumber = $(this).data('req_number');
                const requestId = $(this).data('id'); // Get requestId from the button

                // Store requestId in the edit modal for later use
                $('#editRequestModal').data('id', requestId);

                // Fetch request items and details via AJAX
                $.ajax({
                    url: 'fetch_request_items.php',
                    type: 'POST',
                    data: {
                        req_number: reqNumber
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data) {
                            // Populate the fields with the fetched data
                            $('#editRequestedBy').val(data.requester_name);
                            $('#editDepartment').val(data.department);
                            $('#editRequisitionNumber').val(data.req_number);
                            $('#editDate').val(data.date);

                            // Populate the items in the table
                            let itemsHtml = '';
                            data.items.forEach(item => {
                                itemsHtml += `<tr>
                                                    <td>${item.item}</td>
                                                    <td><input type="number" value="${item.qty}" class="form-control qty-input" data-item_id="${item.id}"></td>
                                                    <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">Remove</button></td>
                                                </tr>`;
                            });
                            $('#edit_request_items').html(itemsHtml); // Set items in the table
                        } else {
                            console.error("No data returned from the server.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching request items: ", error);
                    }
                });
            });

            // Remove item button handler
            $(document).on('click', '.remove-item-btn', function() {
                $(this).closest('tr').remove(); // Remove the row from the table
            });

            // Save changes button handler
            $('#saveEditRequest').on('click', function() {
                console.log("Save Changes button clicked"); // Debugging log

                const updatedItems = [];
                const removedItems = [];
                const requestId = $('#editRequestModal').data('id'); // Get requestId from the modal

                // Collect updated items
                $('#edit_request_items tr').each(function() {
                    const itemId = $(this).find('.qty-input').data('item_id'); // Get item ID
                    const quantity = $(this).find('.qty-input').val(); // Get quantity
                    updatedItems.push({
                        id: itemId,
                        qty: quantity
                    });
                });

                // Collect removed items
                $('#edit_request_items tr').each(function() {
                    if ($(this).find('.remove-item-btn').length === 0){
                        const itemId = $(this).find('.qty-input').data('item_id');
                        removedItems.push(itemId);
                    }
                });

                // Debugging: Log the collected data
                console.log("Request ID:", requestId);
                console.log("Updated Items:", updatedItems);
                console.log("Removed Items:", removedItems);

                // Proceed with AJAX to save updated items
                $.ajax({
                    url: 'update_status.php', // Ensure this is the correct URL
                    type: 'POST',
                    data: {
                        id: requestId, // Ensure requestId is defined and valid
                        items: updatedItems,
                        removed_items: removedItems
                    },
                    success: function(response) {
                        console.log("Server Response:", response); // Log the server response
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Changes Saved!',
                                text: 'The request has been updated successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#editRequestModal').modal('hide'); // Hide the modal after saving
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            console.error("Error:", response.error); // Log any errors
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.error,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating status: ", error); // Log any AJAX errors
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while saving changes.',
                        });
                    }
                });
            });
        });
    </script>

    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog"
        aria-labelledby="editRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRequestModalLabel">Edit Requisition Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Requested By</label>
                            <input type="text" id="editRequestedBy" name="fullname" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Department</label>
                            <input type="text" id="editDepartment" name="department" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Requisition #</label>
                            <input type="text" id="editRequisitionNumber" name="req_number" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Date</label>
                            <input type="text" id="editDate" name="date" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Items</th>
                                    <th>Qty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="edit_request_items">
                                <!-- Rows will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="saveEditRequest" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>