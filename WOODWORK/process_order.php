<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("connect.php");

function sanitizeInput($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

try {
    // Get form data
    $firstName = sanitizeInput($conn, $_POST["first_name"]);
    $lastName = sanitizeInput($conn, $_POST["last_name"]);
    $email = sanitizeInput($conn, $_POST["email"]);
    $phoneNumber = sanitizeInput($conn, $_POST["phone_number"]);
    $address = sanitizeInput($conn, $_POST["address"]);
    $productType = sanitizeInput($conn, $_POST["product_type"]);
    $woodType = sanitizeInput($conn, $_POST["wood_type"]);
    $dimensions = sanitizeInput($conn, $_POST["dimensions"]);
    $quantity = (int)$_POST["quantity"];

    // Server-side validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber) || empty($address) ||
        empty($productType) || empty($woodType) || empty($dimensions) || $quantity <= 0) {
        throw new Exception("Error: Please fill in all fields and ensure quantity is a positive integer.");
    }

    // Check if customer exists
    $sql = "SELECT customer_id, order_count FROM Customers WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Customer exists, retrieve ID and order count
        $customerId = $row["customer_id"];
        $orderCount = $row["order_count"] + 1;

        // Update order count
        $updateOrderCountSql = "UPDATE Customers SET order_count = ? WHERE customer_id = ?";
        $stmt = mysqli_prepare($conn, $updateOrderCountSql);
        mysqli_stmt_bind_param($stmt, "ii", $orderCount, $customerId);
        mysqli_stmt_execute($stmt);
    } else {
        // Customer doesn't exist, create new entry
        $addCustomerSql = "INSERT INTO Customers (first_name, last_name, email, phone_number, address, order_count)
                          VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = mysqli_prepare($conn, $addCustomerSql);
        mysqli_stmt_bind_param($stmt, "sssss", $firstName, $lastName, $email, $phoneNumber, $address);
        mysqli_stmt_execute($stmt);

        // Get the newly inserted customer ID
        $customerId = mysqli_insert_id($conn);
    }

    // Process order details (example: serialize data)
    $orderDetails = serialize(array(
        "product_type" => $productType,
        "wood_type" => $woodType,
        "dimensions" => $dimensions,
        "quantity" => $quantity
    ));

    // Get current date
    $orderDate = date("Y-m-d");

    // Calculate pick-up date (assuming 3 days from the order date)
    $pickUpDate = date('Y-m-d', strtotime($orderDate . ' + 3 days'));

    // Initial order status
    $orderStatus = "Pending";

    // Create a new order entry with pick-up date
    $insertOrderSql = "INSERT INTO Orders (customer_id, order_date, pick_up_date, order_details, order_status, picked_status)
                      VALUES (?, ?, ?, ?, ?, ?)";

    // Create a variable for picked_status
    $pickedStatus = 0;

    $stmt = mysqli_prepare($conn, $insertOrderSql);
    mysqli_stmt_bind_param($stmt, "issisi", $customerId, $orderDate, $pickUpDate, $orderDetails, $orderStatus, $pickedStatus);
    mysqli_stmt_execute($stmt);

    echo "Order placed successfully!";

    // Retrieve the latest order details
    $latestOrderSql = "SELECT Orders.*, Customers.first_name, Customers.last_name
                       FROM Orders
                       JOIN Customers ON Orders.customer_id = Customers.customer_id
                       WHERE Orders.customer_id = ?
                       ORDER BY Orders.order_date DESC
                       LIMIT 1";

    $latestOrderStmt = mysqli_prepare($conn, $latestOrderSql);
    mysqli_stmt_bind_param($latestOrderStmt, "i", $customerId);
    mysqli_stmt_execute($latestOrderStmt);
    $latestOrderResult = mysqli_stmt_get_result($latestOrderStmt);

    if ($latestOrderResult && $latestOrderRow = mysqli_fetch_assoc($latestOrderResult)) {
        // Display the latest order details
        echo "<h2>Latest Order Details</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Order ID</th><th>Customer Name</th><th>Order Date</th><th>Pick-up Date</th><th>Order Details</th><th>Order Status</th><th>Picked</th></tr>";
        echo "<tr>";
        echo "<td>{$latestOrderRow['order_id']}</td>";
        echo "<td>{$latestOrderRow['first_name']} {$latestOrderRow['last_name']}</td>";
        echo "<td>{$latestOrderRow['order_date']}</td>";
        echo "<td>{$latestOrderRow['pick_up_date']}</td>";
        echo "<td>{$latestOrderRow['order_details']}</td>";
        echo "<td>{$latestOrderRow['order_status']}</td>";
        echo "<td>";
        if ($latestOrderRow['picked_status'] == 1) {
            echo 'Picked';
        } else {
            echo "<a href='mark_picked.php?order_id={$latestOrderRow['order_id']}'>Mark as Picked</a>";
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "Error displaying latest order details: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} catch (Exception $e) {
    echo "Error placing order: " . $e->getMessage();
}
?>
