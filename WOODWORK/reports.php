<?php
require_once("connect.php"); // Include your database connection file

try {
    // Query to count the number of unique emails in the customers table
    $countCustomersSql = "SELECT COUNT(DISTINCT email) AS total_customers FROM customers";
    $result = mysqli_query($conn, $countCustomersSql);

    if (!$result) {
        throw new Exception("Error fetching total customers: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $totalCustomers = $row['total_customers'];

    // Query to count the total number of products
    $countProductsSql = "SELECT COUNT(*) AS total_products FROM products";
    $result = mysqli_query($conn, $countProductsSql);

    if (!$result) {
        throw new Exception("Error fetching total products: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $totalProducts = $row['total_products'];

    // Query to count the total number of orders marked as picked (total sales)
    $totalSalesSql = "SELECT COUNT(*) AS total_sales FROM orders WHERE picked_status = 1";
    $result = mysqli_query($conn, $totalSalesSql);

    if (!$result) {
        throw new Exception("Error calculating total sales: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $totalSales = $row['total_sales'];

    // Query to count the total number of orders
    $countOrdersSql = "SELECT COUNT(*) AS total_orders FROM orders";
    $result = mysqli_query($conn, $countOrdersSql);

    if (!$result) {
        throw new Exception("Error fetching total orders: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);
    $totalOrders = $row['total_orders'];

    mysqli_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit; // Exit the script if an error occurs
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .card {
            border: 2px solid #ddd;
            padding: 20px;
            margin: 20px;
            width: 200px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
        }

        h3 {
            margin-bottom: 20px;
            color: #555;
        }

        /* Different background colors for each card */
        .card-customers {
            background-color: #87CEEB; /* Sky Blue */
        }

        .card-products {
            background-color: #90EE90; /* Light Green */
        }

        .card-sales {
            background-color: #FFD700; /* Gold */
        }

        .card-orders {
            background-color: #FFA07A; /* Light Salmon */
        }

        a {
            display: block;
            text-decoration: none;
            color: #007bff;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="card card-customers">
    <h2>Customers</h2>
    <h3>Total Customers: <?php echo $totalCustomers; ?></h3>
</div>

<div class="card card-products">
    <h2>Products</h2>
    <h3>Total Products: <?php echo $totalProducts; ?></h3>
   
</div>

<div class="card card-sales">
    <h2>Total Sales</h2>
    <h3>Total Sales: <?php echo $totalSales; ?></h3>
</div>

<div class="card card-orders">
    <h2>Orders</h2>
    <h3>Total Orders: <?php echo $totalOrders; ?></h3>
</div>

</body>
</html>
