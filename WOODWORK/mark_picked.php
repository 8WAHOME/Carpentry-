<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once("connect.php");

// Function to mark an order as picked
function markOrderAsPicked($orderId)
{
    global $conn;
    $sql = "UPDATE Orders SET picked_status = 1 WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    // Call function to mark the order as picked
    markOrderAsPicked($orderId);
    // Redirect back to the orders page
    header("Location: orders.php");
    exit;
} else {
    // Redirect back to the orders page if no valid order_id is provided
    header("Location: orders.php");
    exit;
}
?>
