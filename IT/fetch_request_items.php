<?php
include("../dbconn/conn.php"); 

if (isset($_POST['req_number'])) {
    $reqNO = $_POST['req_number'];

    $query = "SELECT u.fullname, u.department, r.date, si.item, r.qty
              FROM request r 
              JOIN stock_in si ON r.stockin_id = si.stockin_id 
              JOIN users u ON r.user_id = u.user_id  
              WHERE r.req_number = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $reqNO);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['item']) . "</td>
                <td>" . htmlspecialchars($row['qty']) . "</td>
              </tr>";
    }

    mysqli_stmt_close($stmt);
}
?>
