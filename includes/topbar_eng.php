<?php
if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
    $user_name = $_SESSION['auth_user']['user_name']; // Kuhaa ang name
} else {
    $user_name = "Guest"; 
}
?>

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Add Logo and Gensan Medical Center text -->
                    <div class="d-flex align-items-center">
                        <img src="../img/gmc-logo.jpg" alt="GMC Logo" style="width: 40px; height: 40px; border-radius: 50%;" class="mr-2">
                        <span class="mr-3" style="font-weight: bold;">Gensan Medical Center</span>
                    </div>
                    
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                   

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter" id="notificationCount">0</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown" id="notificationList">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <!-- Notifications will be dynamically added here -->
                            </div>
                        </li>

                        
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($user_name); ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

<script>
    // Function to update notifications
    function updateNotifications() {
        $.ajax({
            url: 'fetch_notifications.php', // Endpoint to fetch notifications
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#notificationCount').text(data.count); // Update notification count
                $('#notificationList').empty(); // Clear existing notifications
                data.notifications.forEach(function(notification) {
                    $('#notificationList').append(`
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="mr-3">
                                <div class="icon-circle ${notification.type === 'approved' ? 'bg-success' : 'bg-danger'}">
                                    <i class="fas fa-${notification.type === 'approved' ? 'check' : 'times'} text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">${notification.date}</div>
                                <span class="font-weight-bold">${notification.message}</span>
                            </div>
                        </a>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching notifications: ", error);
            }
        });
    }

    // Call updateNotifications on page load
    $(document).ready(function() {
        updateNotifications();
    });
</script>
