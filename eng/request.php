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
$query = "SELECT request.req_number, request.date, request.status, stock_in.item, request.qty, request.is_posted
          FROM request 
          JOIN stock_in ON request.stockin_id = stock_in.stockin_id
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
        include("../includes/topbar_user.php");
        ?>


        <!-- ADD MODAL -->
        <div class="modal fade" id="GMCaddRequest" tabindex="-1" role="dialog" aria-labelledby="RequestModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="RequestModalLabel">Add Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="create.php" method="POST">
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-header text-white" style="background-color: #76a73c;">
                                    <strong>Requisition items</strong>
                                </div>
                                <div class="card-body">
                                <div class="form-group">
                                        <label>Requisition #</label>
                                        <input type="text" name="req_number" class="form-control" value="<?php echo $formatted_req_number; ?>" readonly>
                                    </div>
                                    <div id="itemFields">
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Item</label>
                                                <select name="stockin_id[]" class="form-control" required>
                                                    <?php
                                                    $itemQuery = "SELECT DISTINCT item FROM stock_in WHERE is_posted = 1 AND category = 'Engineering Equipment'";
                                                    $itemResult = mysqli_query($conn, $itemQuery);
                                                    while ($itemRow = mysqli_fetch_assoc($itemResult)) {
                                                        echo '<option value="' . htmlspecialchars($itemRow['item']) . '">' . htmlspecialchars($itemRow['item']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
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
                                        <td><?php echo $row['item']; ?></td>
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
                                                <button type="button" data-toggle="modal" data-target="#GMCeditRequest"
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
                newItemRow.classList.add('form-row', 'item-row', 'mb-2');

                newItemRow.innerHTML = `
                <div class="form-row item-row mb-2">
                    <div class="form-group col-md-6">
                        <label>Item</label>
                        <select name="stockin_id[]" class="form-control" required>
                        <?php
                        $itemQuery = "SELECT DISTINCT item FROM stock_in WHERE is_posted = 1 AND category = 'Engineering Equipment'";
                        $itemResult = mysqli_query($conn, $itemQuery);
                        while ($itemRow = mysqli_fetch_assoc($itemResult)) {
                            echo '<option value="' . htmlspecialchars($itemRow['item']) . '">' . htmlspecialchars($itemRow['item']) . '</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Quantity</label>
                        <input type="text" name="qty[]" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
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
                    url: 'fetch_request_items.php',
                    type: 'POST',
                    data: {
                        req_number: reqno
                    },
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        if (data) {
                            $('#requestedBy').val(data.requester_name);
                            $('#department').val(data.department);
                            $('#requisitionNumber').val(data.req_number);
                            $('#date').val(data.date);

                            // Populate the items in the table
                            let itemsHtml = '';
                            data.items.forEach(item => {
                                itemsHtml += `<tr>
                                                <td>${item.item}</td>
                                                <td>${item.qty}</td>
                                              </tr>`;
                            });
                            $('#requestDetailsBody').html(itemsHtml); // Set items in the table
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
                const printContents = `
                <div style="text-align: center;">
                    <img src="path/to/your/logo.png" alt="Logo" style="width: 150px; height: auto; margin-bottom: 20px;">
                    <h2>Requisition Form</h2>
                    <p><strong>Requested By:</strong> <span id="requesterName"></span></p>
                    <p><strong>Department:</strong> <span id="requesterDepartment"></span></p>
                    <p><strong>Date:</strong> <span id="requestDate"></span></p>
                    <hr>
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
                            ${document.querySelector('#requestDetailsBody').innerHTML}
                        </tbody>
                    </table>
                </div>
            `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write('<html><head><title>Print</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
                printWindow.document.write('h1, h2, h3, h4, h5, h6 { color: #333; text-align: center; }');
                printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
                printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }');
                printWindow.document.write('th { background-color: #f2f2f2; }');
                printWindow.document.write('p { margin: 5px 0; text-align: center; }');
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContents);
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
                            url: 'post_request.php',
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
        });
    </script>

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    ?>