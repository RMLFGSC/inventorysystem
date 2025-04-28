        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <div style="margin-top: 20px;"></div>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="../admin/index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="asset.php">
                    <i class="fas fa-fw fa-list"></i>
                    <span>Fixed Asset</span></a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="../admin/stockin.php">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Inventory</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="requisitions.php">
                    <i class="fas fa-fw fa-list-alt"></i>
                    <span>Requisitions</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="issuance.php">
                    <i class="fas fa-fw fa-share-square"></i>
                    <span>Issuance</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="users.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span></a>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#reports"
                    aria-expanded="true" aria-controls="reports">
                    <i class="fas fa-fw fa-boxes"></i>
                    <span>Reports</span>
                </a>
                <div id="reports" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="inv_report.php">Inventory Report</a>
                        <a class="collapse-item" href="stockin_report.php">Stock-in Report</a>
                        <a class="collapse-item" href="asset_reports.php">Fixed Asset Report</a>
                        <a class="collapse-item" href="request_report.php">Request Report</a>
                        <a class="collapse-item" href="issuance_report.php">Issuance Report</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">


            <!-- Divider -->

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>


        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="../login/login.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>