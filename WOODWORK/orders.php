<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
require_once("connect.php");

// Function to change order status to Finished
function markOrderAsFinished($orderId)
{
    global $conn;
    $sql = "UPDATE Orders SET order_status = 'Finished' WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
}

// Function to delete an order
function deleteOrder($orderId)
{
    global $conn;
    $sql = "DELETE FROM Orders WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
}

// Function to retrieve order count for a specific customer
function getOrderCountForCustomer($customerId)
{
    global $conn;
    $sql = "SELECT COUNT(*) AS order_count FROM Orders WHERE customer_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $customerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['order_count'];
}

// Placeholder for obtaining customer ID, you need to replace this with actual customer ID retrieval logic
$customer_id = 1; // For example, you may obtain it from session or another source

// Insert form data into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission
    if (isset($_POST['mark_finished']) && isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
        markOrderAsFinished($orderId);
    } elseif (isset($_POST['delete_order']) && isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];
        deleteOrder($orderId);
    } elseif (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['phone_number']) && isset($_POST['address']) && isset($_POST['product_type']) && isset($_POST['wood_type']) && isset($_POST['dimensions']) && isset($_POST['pick_up_date'])) {
        // Escape user inputs for security
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $product_type = mysqli_real_escape_string($conn, $_POST['product_type']);
        $wood_type = mysqli_real_escape_string($conn, $_POST['wood_type']);
        $dimensions = mysqli_real_escape_string($conn, $_POST['dimensions']);
        $pick_up_date = mysqli_real_escape_string($conn, $_POST['pick_up_date']);

        // Check if the customer exists based on email
        $customer_check_sql = "SELECT * FROM Customers WHERE email = '$email'";
        $customer_check_result = mysqli_query($conn, $customer_check_sql);

        if (mysqli_num_rows($customer_check_result) == 0) {
            // Customer doesn't exist, insert new customer
            $insert_customer_sql = "INSERT INTO Customers (first_name, last_name, email, phone_number, address, order_count)
                                    VALUES ('$first_name', '$last_name', '$email', '$phone_number', '$address', 0)";
            if (!mysqli_query($conn, $insert_customer_sql)) {
                die("Error inserting new customer: " . mysqli_error($conn));
            }
            // Get the ID of the newly inserted customer
            $customer_id = mysqli_insert_id($conn);
        } else {
            // Customer already exists, retrieve their ID
            $customer_data = mysqli_fetch_assoc($customer_check_result);
            $customer_id = $customer_data['customer_id'];
        }

        // Insert order
        $insert_order_sql = "INSERT INTO Orders (customer_id, order_date, order_status, order_details, pick_up_date, picked_status)
                            VALUES ('$customer_id', NOW(), 'Pending', 'Product Type: $product_type, Wood Type: $wood_type, Dimensions: $dimensions', '$pick_up_date', '0')";
        if (!mysqli_query($conn, $insert_order_sql)) {
            die("Error inserting new order: " . mysqli_error($conn));
        }

        // Increment order count for the customer
        $orderCount = getOrderCountForCustomer($customer_id);
        $orderCount++; // Increment the count
        $update_customer_sql = "UPDATE Customers SET order_count = $orderCount WHERE customer_id = $customer_id";
        if (!mysqli_query($conn, $update_customer_sql)) {
            die("Error updating customer order count: " . mysqli_error($conn));
        }
        echo "Order added successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 20px;
        }

        h1,
        h2 {
            text-align: center;
            color: #007bff;
        }

        form,
        table {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input,
        textarea,
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 50px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <h1>Order Management</h1>

    <!-- Add Order Form -->
    <h2>Add New Order</h2>
    <form id="orderForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="return validatePickupDate();">
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="address">Address:</label>
        <textarea id="address" name="address" rows="3" required></textarea>

        <label for="product_type">Product Type:</label>
        <input type="text" id="product_type" name="product_type" required>

        <label for="wood_type">Wood Type:</label>
        <input type="text" id="wood_type" name="wood_type" required>

        <label for="dimensions">Dimensions:</label>
        <input type="text" id="dimensions" name="dimensions" required>

        <label for="pick_up_date">Pick-up Date:</label>
        <input type="date" id="pick_up_date" name="pick_up_date" required>

        <button type="submit">Place Order</button>
    </form>

    <!-- View Orders -->
    <h2>View Orders</h2>
    <table border="1">
        <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Date</th>
            <th>Pick-up Date</th>
            <th>Order Details</th>
            <th>Order Status</th>
            <th>Picked</th>
            <th>Actions</th> <!-- New column for actions -->
        </tr>
        <!-- Populate this table with orders from the database -->
        <?php
        // Retrieve orders from the database
        $sql = "SELECT Orders.*, Customers.first_name, Customers.last_name
            FROM Orders
            JOIN Customers ON Orders.customer_id = Customers.customer_id
            ORDER BY Orders.order_date DESC";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Error retrieving orders: " . mysqli_error($conn));
        }

        while ($row = mysqli_fetch_assoc($result)) :
        ?>
            <tr>
                <!-- Display order information -->
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
                <td><?php echo $row['order_date']; ?></td>
                <td><?php echo $row['pick_up_date']; ?></td>
                <td><?php echo $row['order_details']; ?></td>
                <td><?php echo $row['order_status']; ?></td>
                <td>
                    <?php
                    if ($row['picked_status'] == 1) {
                        echo 'Picked';
                    } else {
                        echo '<a href="mark_picked.php?order_id=' . $row['order_id'] . '">Mark as Picked</a>';
                    }
                    ?>
                </td>
                <td>
                    <!-- Buttons for marking as finished and deleting an order -->
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <button type="submit" name="mark_finished">Mark as Finished</button>
                    </form>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" style="display: inline;">
                        <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                        <button type="submit" name="delete_order">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        // JavaScript function to validate pick-up date
        function validatePickupDate() {
            var pickupDateInput = document.getElementById('pick_up_date');
            var pickupDate = new Date(pickupDateInput.value);
            var today = new Date();

            // Compare pick-up date with today's date
            if (pickupDate < today) {
                alert("Please choose a pick-up date that is today or later.");
                pickupDateInput.value = ''; // Clear invalid date
                return false; // Prevent form submission
            }
            return true; // Proceed with form submission
        }
    </script>

</body>

</html>

<?php
mysqli_close($conn);
?>
