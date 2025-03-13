<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

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

<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                                <!-- ✅ Print button - initially hidden -->
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
            <!-- Page Heading -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Issuance List</h6>
        </div>
            <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Req Number</th>
                                    <th>Requester</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
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

                                        <td>
                                            <!-- VIEW BUTTON: Show always -->
                                            <button type="button" data-toggle="modal" data-target="#viewRequestModal"
                                                class="btn btn-sm btn-warning viewrequest-btn"
                                                data-id="<?php echo $row['req_id']; ?>"
                                                data-req_number="<?php echo $row['req_number']; ?>"
                                                data-status="<?php echo $row['status']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>

                                            <!-- EDIT BUTTON: Show only if status is still pending -->
                                            <?php if ($row['status'] == 0): ?>
                                                <button type="button" data-toggle="modal" data-target="#editRequestModal"
                                                    class="btn btn-sm btn-primary editrequest-btn"
                                                    data-id="<?php echo $row['req_id']; ?>">
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
        <!-- /.container-fluid -->


    </div>
    <!-- End of Main Content -->

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>

<script>
    $(document).ready(function () {
        // View request modal functionality
        $('.viewrequest-btn').on('click', function () {
            const reqno = $(this).data('req_number');
            const reqId = $(this).data('id');
            const status = $(this).data('status');

            // Store the request ID inside the modal
            $('#viewRequestModal').data('id', reqId);

            if (status == 2 || status == 1) {
                $('#action-buttons').hide();
                $('#printRequestBtn').show(); // ✅ show print button only if approved or declined
            } else {
                $('#action-buttons').show();
                $('#printRequestBtn').hide(); // ✅ hide print button for pending requests
            }

            // Fetch request items via AJAX
            $.ajax({
                url: 'fetch_request_items.php',
                type: 'POST',
                data: {
                    req_number: reqno
                },
                success: function (data) {
                    $('#view_request_items').html(data);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching request items: ", error);
                }
            });
        });

        // Decline button handler
        $('#confirmDecline').on('click', function () {
            const requestId = $('#viewRequestModal').data('id');

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
                            status: 2
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Declined!',
                                text: 'The request has been declined.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // Approve button handler with SweetAlert + Print
        $('#saveRequest').on('click', function () {
            const requestId = $('#viewRequestModal').data('id');
            if (!requestId) {
                console.error("No request ID found!");
                return;
            }

            Swal.fire({
                title: 'Approve Request?',
                text: "Are you sure you want to approve this request?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                width: '300px'
            }).then((result) => {
                if (result.isConfirmed) {
                    const itemsToDeduct = [];
                    $('#view_request_items tr').each(function () {
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
                            items: itemsToDeduct
                        },
                        success: function (response) {
                            const printContent = `
                                <div id="printSection" style="display: none;">
                                    <h3>Request Approved</h3>
                                    <p><strong>Request Number:</strong> ${requestId}</p>
                                    <table border="1" cellspacing="0" cellpadding="5" style="width:100%; margin-top:10px;">
                                        <thead>
                                            <tr><th>Item</th><th>Quantity</th></tr>
                                        </thead>
                                        <tbody>
                                            ${itemsToDeduct.map(item => `<tr><td>${item.id}</td><td>${item.qty}</td></tr>`).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            `;
                            $('body').append(printContent);

                            const printWindow = window.open('', '', 'width=800,height=600');
                            printWindow.document.write('<html><head><title>Print</title></head><body>');
                            printWindow.document.write(document.getElementById('printSection').innerHTML);
                            printWindow.document.write('</body></html>');
                            printWindow.document.close();
                            printWindow.focus();
                            printWindow.print();
                            printWindow.close();

                            $('#printSection').remove();

                            // sweet alert show after printint
                            Swal.fire({
                                icon: 'success',
                                title: 'Approved!',
                                text: 'The request has been approved.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Error updating status: ", error);
                        }
                    });
                }
            });
        });

        // printing
        $('#printRequestBtn').on('click', function () {
            const requestId = $('#viewRequestModal').data('id');
            const itemsToDeduct = [];

            $('#view_request_items tr').each(function () {
                const itemId = $(this).data('item_id');
                const quantity = $(this).find('td:eq(1)').text().trim(); 
                itemsToDeduct.push({
                    id: itemId,
                    qty: quantity
                });
            });

            // Build printable content
            const printContent = `
                <div id="printSection" style="display: none;">
                    <h3>Request Approved</h3>
                    <p><strong>Request Number:</strong> ${requestId}</p>
                    <table border="1" cellspacing="0" cellpadding="5" style="width:100%; margin-top:10px;">
                        <thead>
                            <tr><th>Item</th><th>Quantity</th></tr>
                        </thead>
                        <tbody>
                            ${itemsToDeduct.map(item => `<tr><td>${item.id}</td><td>${item.qty}</td></tr>`).join('')}
                        </tbody>
                    </table>
                </div>
            `;

            $('body').append(printContent);

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write('<html><head><title>Print</title></head><body>');
            printWindow.document.write(document.getElementById('printSection').innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();

            $('#printSection').remove();
        });

    });
</script>

    