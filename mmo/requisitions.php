<?php
include("../includes/header.php");
include("../includes/navbar_mmo.php");

if (!isset($_SESSION['auth_user']['user_id'])) {
    die("Error: User is not logged in. Please log in first.");
}

// Query to fetch requisition requests (only pending and approved)
$query = "SELECT request.req_number, request.date, request.status, users.fullname AS requester_name, users.department
          FROM request 
          JOIN users ON request.user_id = users.user_id
          WHERE request.req_id IN (SELECT MIN(req_id) 
          FROM request 
          GROUP BY req_number)
          AND request.status IN (0, 1)  
          ORDER BY 
              CASE 
                  WHEN request.status = 0 THEN 0 
                  ELSE 1 
              END, 
              request.date DESC"; 
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <?php include("../includes/topbar.php"); ?>

        <!-- ADD MODAL -->
        <div class="modal fade" id="GMCaddRequest" tabindex="-1" role="dialog" aria-labelledby="RequestModalLabel" aria-hidden="true">
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
                                        <input type="text" name="req_number" class="form-control" value="<?php 
                                        $req_number = 'REQ-' . mt_rand(10000, 99999); // 5-digit random number
                                        echo $req_number; ?>" readonly>
                                    </div>
                                    <div id="itemFields">
                                        <div class="form-row">  
                                            <div class="form-group col-md-6">
                                                <label>Item</label>
                                                <select name="stockin_id[]" class="form-control" required>
                                                    <?php
                                                    $itemQuery = "SELECT item FROM stockin";
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
                                        <button type="button" class="btn btn-sm btn-secondary" id="addRequest">Add Item</button>
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
        <!-- End of Add Modal -->

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
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Requisitions</h1>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#GMCaddRequest">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Add Request
                </button>
            </div>

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="card-datatable pt-0">
                        <table class="datatables-basic table" id="dataTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Requisition #</th>
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
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } elseif ($row['status'] == 2) {
                                                echo '<span class="badge badge-danger">Declined</span>';
                                            }
                                            ?>
                                        </td>

                                        <td>
                                            <button type="button" data-toggle="modal" data-target="#viewRequestModal" class="btn btn-sm btn-warning viewrequest-btn" data-req_number="<?php echo htmlspecialchars($row['req_number']); ?>">
                                                <i class="fa-solid fa-eye text-white"></i>
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
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('addRequest').addEventListener('click', function () {
                const itemFields = document.getElementById('itemFields');
                const newItemRow = document.createElement('div');
                newItemRow.classList.add('form-row', 'item-row', 'mb-2');

                newItemRow.innerHTML = `
                    <div class="form-group col-md-6">
                        <label>Item</label>
                        <select name="stockin_id[]" class="form-control" required>
                            <?php
                            // Fetch items from stockin table
                            $itemQuery = "SELECT item FROM stockin";
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
                    <button type="button" class="btn btn-danger btn-sm removeItem">X</button>`;

                itemFields.appendChild(newItemRow);

                newItemRow.querySelector('.removeItem').addEventListener('click', function () {
                    itemFields.removeChild(newItemRow); // Remove the item row
                });
            });

            // Update view modal functionality
            $('.viewrequest-btn').on('click', function () {
                const reqno = $(this).data('req_number');

                // AJAX call to fetch the requisition details
                $.ajax({
                    url: 'fetch_requisition_details.php',
                    type: 'POST',
                    data: {
                        req_number: reqno
                    },
                    dataType: 'json', 
                    success: function (data) {      
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
                    error: function (xhr, status, error) {
                        console.error("Error fetching requisition details: ", error);
                    }
                });
            });

            document.getElementById('printRequest').addEventListener('click', function () {
                const printContents = `
                    <html>
                    <head>
                        <title>.</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            h1, h2, h3, h4, h5, h6 { color: #333; text-align: center; }
                            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f2f2f2; }
                            p { margin: 5px 0; text-align: center; }
                            .header { text-align: center; margin-bottom: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>Requisition Form</h2>
                            <p><strong>Requested By:</strong> ${document.getElementById('requestedBy').value}</p>
                            <p><strong>Department:</strong> ${document.getElementById('department').value}</p>
                            <p><strong>Date:</strong> ${document.getElementById('date').value}</p>
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
                                <tbody>
                                    ${document.querySelector('#requestDetailsBody').innerHTML}
                                </tbody>
                            </table>
                        </div>
                    </body>
                    </html>
                `;

                const printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write(printContents);
                printWindow.document.close();
                printWindow.print();
                printWindow.close();
            });
        });
    </script>

    <?php
    include("../includes/scripts.php");
    include("../includes/footer.php");
    include("../includes/datatables.php");
    ?>