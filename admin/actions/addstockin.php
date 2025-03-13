<?php
session_start();
include("../../dbconn/conn.php");

if (isset($_POST['addStockin'])) {
    $controlNO = $_POST['controlNO'];
    $cat_name = $_POST['category'];
    $dop = $_POST['dop'];
    $dr = $_POST['dr'];
    $item_names = $_POST['item'];
    $qtys = $_POST['qty'];
    $warranties = isset($_POST['warranty']) ? $_POST['warranty'] : [];

    for ($i = 0; $i < count($item_names); $i++) {
        $item = mysqli_real_escape_string($conn, $item_names[$i]);
        $qty = intval($qtys[$i]);
        $warranty = in_array($i + 1, $warranties) ? 1 : 0; 

        $query = "INSERT INTO stock_in (controlNO, item, category, qty, dop, dr, warranty) 
                  VALUES ('$controlNO', '$item', '$cat_name', '$qty', '$dop', '$dr', '$warranty')";
        
        $query_run = mysqli_query($conn, $query);

        if (!$query_run) {
            echo "Error: " . mysqli_error($conn);
        }
    }
    header("Location: stockin.php");
    exit();
}



?>