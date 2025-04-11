<?php
if (isset($_SESSION['auth']) && $_SESSION['auth'] == true) {
    $user_name = $_SESSION['auth_user']['user_name']; 
    $logged_in_user_id = $_SESSION['auth_user']['user_id']; 
} else {
    $user_name = "Guest"; 
    $logged_in_user_id = null;
}


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

$notification_count_query = "SELECT COUNT(DISTINCT req_number) AS unread_count FROM request WHERE is_read = 0 AND is_posted = 1";
$notification_count_result = mysqli_query($conn, $notification_count_query);
$unread_notification_count = mysqli_fetch_assoc($notification_count_result)['unread_count'] ?? 0;

$notification_result = mysqli_query($conn, $notification_query);
$notifications = mysqli_fetch_all($notification_result, MYSQLI_ASSOC);

// Function to mark a notification as read
if (isset($_GET['req_id'])) {
    $req_id_to_mark_read = $_GET['req_id'];
    $update_query = "UPDATE request SET is_read = 1 WHERE req_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $req_id_to_mark_read);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Automatically mark as read if approved or declined
    if (isset($_GET['action']) && in_array($_GET['action'], ['approve', 'decline'])) {
        $update_query = "UPDATE request SET is_read = 1 WHERE req_id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "i", $req_id_to_mark_read);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Mark all items under the same request number as read
    $update_all_items_query = "UPDATE request SET is_read = 1 WHERE req_number = (SELECT req_number FROM request WHERE req_id = ?)";
    $stmt = mysqli_prepare($conn, $update_all_items_query);
    mysqli_stmt_bind_param($stmt, "i", $req_id_to_mark_read);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <div class="d-flex align-items-center">
                <img src="../img/gmc-logo.jpg" alt="GMC Logo" style="width: 40px; height: 40px; border-radius: 50%;" class="mr-2">
                <span class="mr-3" style="font-weight: bold;">Gensan Medical Center</span>
            </div>

            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <?php if ($unread_notification_count > 0): ?>  
                            <span class="badge badge-danger badge-counter"><?php echo $unread_notification_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">
                            Notification
                        </h6>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <a class="dropdown-item d-flex align-items-center" href="issuance.php?req_id=<?php echo $notification['req_id']; ?>&highlight=1">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-info">
                                            <i class="fas fa-bell text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500"><?php echo htmlspecialchars(date("F j, Y", strtotime($notification['date']))); ?></div>
                                        <span class="<?php echo $notification['is_read'] ? 'text-gray-600' : 'font-weight-bold'; ?>">
                                            <?php echo htmlspecialchars($notification['fullname']); ?> requested!
                                        </span>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            
                        <?php else: ?>
                            <a class="dropdown-item text-center small text-gray-500" href="#">No new notification</a>
                        <?php endif; ?>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($user_name); ?></span>
                        <img class="img-profile rounded-circle"
                            src="img/undraw_profile.svg">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>

            </ul>

        </nav>