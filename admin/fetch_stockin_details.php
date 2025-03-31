<?php
include("../conn.php");


if (isset($_POST['controlNO'])) {
    $controlNO = $_POST['controlNO'];

    $query = "SELECT * FROM stock_in  
              WHERE controlNO = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $controlNO);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>" . htmlspecialchars($controlNO) . "</td>
                <td>" . htmlspecialchars($row['serialNO']) . "</td>
                <td>" . htmlspecialchars($row['item']) . "</td>
                <td>" . htmlspecialchars($row['orig_qty']) . "</td>
                <td>" . htmlspecialchars($row['category']) . "</td>
                <td>" . htmlspecialchars($row['dop']) . "</td>
                <td>" . htmlspecialchars($row['dr']) . "</td>
                <td>" . ($row['warranty'] ? 'Yes' : 'No') . "</td>
              </tr>";
    }

    mysqli_stmt_close($stmt);
}

?>