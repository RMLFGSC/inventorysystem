<?php
if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
    $user_name = $_SESSION['auth_user']['user_name']; 
    $logged_in_user_id = $_SESSION['auth_user']['user_id']; 
} else {
    $user_name = "Guest"; 
    $logged_in_user_id = null;
}

// Fetch unread request notifications for Admin
$notification_query = "SELECT
                            r.req_id,
                            GROUP_CONCAT(r.item_request SEPARATOR ', ') AS item_requests,
                            r.date,
                            u.fullname,
                            r.is_read
                        FROM request r
                        JOIN users u ON r.user_id = u.user_id
                        WHERE r.is_read = 0 AND r.is_posted = 1
                        GROUP BY r.req_number, r.date, u.fullname, r.is_read
                        ORDER BY r.date DESC
                        LIMIT 5"; 

// Count unread notifications
$notification_count_query = "SELECT COUNT(DISTINCT req_number) AS unread_count FROM request WHERE is_read = 0 AND is_posted = 1";

$notification_count_result = mysqli_query($conn, $notification_count_query);
$unread_notification_count = mysqli_fetch_assoc($notification_count_result)['unread_count'] ?? 0;

// Fetch notifications for the current user
$notification_result = mysqli_query($conn, $notification_query);
$notifications = mysqli_fetch_all($notification_result, MYSQLI_ASSOC);

// Filter notifications to only include approved and declined requests
$notifications = array_filter($notifications, function($notification) {
    return isset($notification['status']) && ($notification['status'] == 1 || $notification['status'] == 2);
});

// Add a variable to track if there are new notifications
$new_notifications = !empty($notifications);

// Fetch approved and declined requests for the current user
if ($logged_in_user_id) {
    $approved_requests_query = "
        SELECT 
            req_id, 
            issued_by, 
            declined_by, 
            date_issued, 
            date, 
            date_declined, 
            status, 
            is_read,
            GROUP_CONCAT(item_request SEPARATOR ', ') AS item_requests
        FROM request 
        WHERE user_id = '$logged_in_user_id' 
          AND (status = 1 OR status = 2) 
        GROUP BY req_number, status
        ORDER BY date_issued DESC
    "; 
    $approved_requests_result = mysqli_query($conn, $approved_requests_query);
    while ($row = mysqli_fetch_assoc($approved_requests_result)) {
        $notifications[] = [
            'req_id' => $row['req_id'],
            'issued_by' => $row['issued_by'], 
            'declined_by' => $row['declined_by'], 
            'date_issued' => $row['date_issued'], 
            'date' => $row['date'], 
            'date_declined' => $row['date_declined'], 
            'status' => $row['status'], 
            'is_read' => $row['is_read'],
            'item_requests' => $row['item_requests']
        ];
    }
}
?>

<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Logo and Title -->
    <div class="d-flex align-items-center">
        <img src="../img/gmc-logo.jpg" alt="GMC Logo" style="width: 40px; height: 40px; border-radius: 50%;" class="mr-2">
        <span class="mr-3" style="font-weight: bold;">Gensan Medical Center</span>
    </div>

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <?php if ($unread_notification_count > 0): ?>  
                    <span class="badge badge-danger badge-counter"><?php echo $unread_notification_count; ?></span>
                <?php endif; ?>
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header" style="background-color: #4CAF50; color: white; font-weight: bold; padding: 10px; border-radius: 5px 5px 0 0;">
                    NOTIFICATION
                </h6>

                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <?php 
                            $date = $notification['date_declined'] ?? $notification['date_issued'] ?? $notification['date'] ?? 'No Date Available';
                            $formatted_date = $date !== 'No Date Available' ? date("F j, Y", strtotime($date)) : 'Unknown Date';
                            $is_read_class = isset($notification['is_read']) && $notification['is_read'] ? 'text-gray-600' : 'font-weight-bold';
                        ?>
                        <a class="dropdown-item d-flex align-items-center" href="request.php?req_id=<?php echo htmlspecialchars($notification['req_id']); ?>&highlight=1" data-req-id="<?php echo htmlspecialchars($notification['req_id']); ?>" onclick="removeNotification(this); return false;">
                            <div class="mr-3">
                                <div class="icon-circle bg-info">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500"><?php echo htmlspecialchars($formatted_date); ?></div>
                                <span class="<?php echo $is_read_class; ?>">
                                    <?php if (isset($notification['issued_by']) || isset($notification['declined_by'])): ?>
                                        <?php echo htmlspecialchars($notification['issued_by'] ?? $notification['declined_by']); ?>
                                        <?php if (isset($notification['status'])): ?>
                                            <?php if ($notification['status'] == 2): ?>
                                                declined your request for <?php echo htmlspecialchars($notification['item_requests']); ?>
                                            <?php elseif ($notification['status'] == 1): ?>
                                                approved your request for <?php echo htmlspecialchars($notification['item_requests']); ?>
                                            <?php else: ?>
                                                requested!
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo htmlspecialchars($notification['item_requests'] ?? 'New Request'); ?> requested!
                                    <?php endif; ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a class="dropdown-item text-center small text-gray-500" href="#">No new notifications</a>
                <?php endif; ?>

            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($user_name); ?></span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
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
    function removeNotification(element) {
        // Get the req_id from the notification element directly
        const reqId = element.getAttribute('data-req-id');

        // Send an AJAX request to mark the notification as read
        fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ req_id: reqId })
        })
        .then(response => {
            if (response.ok) {
                // Remove the notification item
                element.closest('.dropdown-item').remove();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>


