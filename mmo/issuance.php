<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

//query
$query = "SELECT request.*, users.fullname AS requester_name, users.department, request.item_request
          FROM request 
          JOIN users ON request.user_id = users.user_id
          WHERE req_id IN (SELECT MIN(req_id) FROM request GROUP BY req_number) 
          AND request.is_posted = 1
          ORDER BY status ASC, req_number DESC";
$result = mysqli_query($conn, $query);

// Get the highlighted request ID if available
$highlighted_req_id = isset($_GET['req_id']) ? $_GET['req_id'] : null;
?>





<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- topbar -->
        <?php
        include("../includes/topbar.php");
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
                                <label>Declined Date</label>
                                <input type="text" id="declineDate" name="decline_date" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label>Declined Reason</label>
                                <input type="text" id="declineReason" name="decline_reason" class="form-control" readonly>
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
                                <th style="width: 60%;">Items</th> <th style="width: 40%;">Qty</th>   </tr>
                        </thead>
                        <tbody id="edit_request_items">
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
                                    <tr class="<?php echo $row['req_id'] == $highlighted_req_id ? 'table-warning' : ''; ?>">
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
                                            <?php if ($row['status'] == 0): // Pending 
                                            ?>
                                                <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                                    class="btn btn-sm btn-success viewrequest-btn"
                                                    data-id="<?php echo $row['req_id']; ?>"
                                                    data-req_number="<?php echo $row['req_number']; ?>"
                                                    data-status="<?php echo $row['status']; ?>">
                                                    <i class="fa fa-check-circle text-white"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger decline-btn"
                                                    data-req_number="<?php echo $row['req_number']; ?>">
                                                    <i class="fa fa-times-circle text-white"></i>
                                                </button>
                                                <button type="button" data-toggle="modal" data-target="#editRequestModal"
                                                    class="btn btn-sm btn-primary editrequest-btn"
                                                    data-req_number="<?php echo $row['req_number']; ?>"
                                                    data-id="<?php echo $row['req_id']; ?>">
                                                    <i class="fa fa-edit text-white"></i>
                                                </button>
                                            <?php else: // Served or Declined 
                                            ?>
                                                <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                                    class="btn btn-sm btn-warning viewrequest-btn"
                                                    data-id="<?php echo $row['req_id']; ?>"
                                                    data-req_number="<?php echo $row['req_number']; ?>"
                                                    data-status="<?php echo $row['status']; ?>">
                                                    <i class="fa-solid fa-eye text-white"></i>
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
                            $('#date').val(data.date || 'N/A'); // Set date or N/A

                            // Check the status and show/hide relevant fields
                            if (status == 1) { // If the status is "Approved"
                                $('#issuedBy').val(data.issued_by || 'N/A');
                                $('#issuedDate').val(data.date_issued || 'N/A');
                                $('#declinedBy').val('N/A');
                                $('#declineDate').val('N/A');
                                $('#declineReason').val('N/A');
                                $('#issuedBy').closest('.row').show();
                                $('#issuedDate').closest('.row').show();
                                $('#declinedBy').closest('.row').hide();
                                $('#declineDate').closest('.row').hide();
                                $('#declineReason').closest('.row').hide();
                                $('#printRequestBtn').show();
                                $('#action-buttons').hide();
                            } else if (status == 2) { // If the status is "Declined"
                                $('#declinedBy').val(data.declined_by || 'N/A');
                                $('#declineDate').val(data.date_declined || 'N/A');
                                $('#declineReason').val(data.decline_reason || 'N/A');
                                $('#issuedBy').val('N/A');
                                $('#issuedDate').val('N/A');
                                $('#declinedBy').closest('.row').show();
                                $('#declineDate').closest('.row').show();
                                $('#declineReason').closest('.row').show();
                                $('#issuedBy').closest('.row').hide();
                                $('#issuedDate').closest('.row').hide();
                                $('#printRequestBtn').hide();
                                $('#action-buttons').hide();
                            } else { // If the status is "Pending"
                                $('#issuedBy').val('N/A');
                                $('#issuedDate').val('N/A');
                                $('#declinedBy').val('N/A');
                                $('#declineDate').val('N/A');
                                $('#declineReason').val('N/A');
                                $('#issuedBy').closest('.row').hide();
                                $('#issuedDate').closest('.row').hide();
                                $('#declinedBy').closest('.row').hide();
                                $('#declineDate').closest('.row').hide();
                                $('#declineReason').closest('.row').hide();
                                $('#printRequestBtn').hide();
                                $('#action-buttons').show();
                            }

                            // Populate the items in the table
                            let itemsHtml = '';
                            data.items.forEach(item_request => {
                                itemsHtml += `<tr>
                                                <td>${item_request.item_request}</td>
                                                <td>${item_request.qty}</td>
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


            //Edit button Function
            $(document).ready(function() {
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
                    $('#edit_request_items').html(rows);
                }

                // Edit button click event
                $(document).on('click', '.editrequest-btn', function() {
                    let reqNumber = $(this).data('req_number');

                    // Fetch items from the server
                    $.ajax({
                        url: 'edit_request',
                        method: 'POST',
                        data: {
                            req_number: reqNumber
                        },
                        success: function(response) {
                            populateEditModal(response);
                            $('#editRequestModal').data('req_number', reqNumber);
                            $('#editRequestModal').modal('show');
                        },
                        error: function(error) {
                            console.error('Error fetching items:', error);
                        }
                    });
                });

                // to save edited request qty
                $('#saveEditRequest').click(function() {
                    let editedItems = [];
                    let isUpdated = false; // Flag to check if any quantity is updated
                    $('#edit_request_items tr').each(function() {
                        let item_request = $(this).find('td:first-child').text();
                        let qty = $(this).find('.edit-qty').val();
                        editedItems.push({
                            item_request: item_request,
                            qty: qty
                        });

                        // Check if the quantity has changed (assuming you have the original quantity stored)
                        let originalQty = $(this).data('original-qty'); // Assuming original qty is stored in a data attribute
                        if (qty != originalQty) {
                            isUpdated = true; // Set flag to true if any quantity is updated
                        }
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
                            // Show success message only if quantities were updated
                            if (isUpdated) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: 'The quantities have been successfully updated.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(error) {
                            console.error('Error saving items:', error);
                        }
                    });
                });
            });


            // Print button functionality
            $('#printRequestBtn').on('click', function() {
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

            // Approve button functionality
            $('#saveRequest').on('click', function() {
                $('#viewRequestModal').modal('hide');

                const reqNumber = $('#requisitionNumber').val();
                if (!reqNumber) {
                    console.error("No request number found!");
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

                        // AJAX
                        $.ajax({
                            url: 'approve_request',
                            type: 'POST',
                            data: {
                                req_number: reqNumber,
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
                                    // Remove the highlighted row
                                    $('tr.table-warning').remove(); // Remove the row with the highlight
                                            location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error("Error approving request: ", error);
                                Swal.fire("Error", "Something went wrong while approving the request.", "error");
                            }
                        });
                    }
                });

                setTimeout(() => {
                    document.querySelector('#swal2-input')?.focus();
                }, 100);
            });

            // Decline button functionality
            $('.decline-btn').on('click', function() {
                const reqNumber = $(this).data('req_number');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, decline it!',
                    width: '300px',
                    input: 'text',
                    inputPlaceholder: 'Enter reason for decline'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const declineReason = result.value;
                        if (!declineReason) {
                            Swal.showValidationMessage('Please enter a reason for declining.');
                            return;
                        }
                        Swal.fire({
                            title: 'Enter Declined By',
                            input: 'text',
                            inputPlaceholder: 'Enter name',
                            showCancelButton: true,
                            confirmButtonText: 'Submit',
                            cancelButtonText: 'Cancel',
                            preConfirm: (declinedBy) => {
                                if (!declinedBy) {
                                    Swal.showValidationMessage('Please enter a name for "Declined By".');
                                }
                                return declinedBy;
                            }
                        }).then((inputResult) => {
                            if (inputResult.isConfirmed) {
                                const declinedBy = inputResult.value.trim();

                                $.ajax({
                                    url: 'decline_request',
                                    type: 'POST',
                                    data: {
                                        req_number: reqNumber,
                                        declined_by: declinedBy,
                                        decline_reason: declineReason
                                    },
                                    success: function(response) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Declined!',
                                            text: 'The request has been successfully declined.',
                                            width: '300px',
                                            timer: 1500,
                                            showConfirmButton: false
                                        }).then(() => {
                                            // Remove the highlighted row
                                            $('tr.table-warning').remove(); // Remove the row with the highlight
                                            location.reload();
                                        });
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error declining request: ", error);
                                        Swal.fire("Error", "Something went wrong while declining the request.", "error");
                                    }
                                });
                            }
                        });
                    }
                });
            });

        });
    </script>