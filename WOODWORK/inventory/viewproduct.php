<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../connect.php"); // Include your database connection file

try {
    // Fetch data from the products table
    $fetchProductsSql = "SELECT * FROM products";
    $result = mysqli_query($conn, $fetchProductsSql);

    if (!$result) {
        throw new Exception("Error fetching products: " . mysqli_error($conn));
    }

    // Display the fetched data in a card format
    echo "<html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Product Display</title>
        <style>
            .card {
                border: 1px solid #ddd;
                padding: 10px;
                margin: 10px;
                width: 300px;
                display: inline-block;
            }
            img {
                width: 250px;
                height: 250px;
            }
            .btn {
                display: inline-block;
                padding: 5px 10px;
                background-color: #007bff;
                color: #fff;
                border: none;
                cursor: pointer;
                margin-right: 5px;
            }
        </style>
    </head>
    <body>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='card'>";
        echo "<h3>{$row['product_name']}</h3>";
        echo "<p>Type: {$row['product_type']}</p>";
        echo "<p>Description: {$row['description']}</p>";
        echo "<p>Price: {$row['price']}</p>";
        echo "<img src='{$row['image']}' alt='Product Image'>";
        echo "<button class='btn' onclick='editProduct({$row['product_id']})'>Edit</button>";
        echo "<button class='btn' onclick='deleteProduct({$row['product_id']})'>Delete</button>";
        echo "</div>";
    }

    echo "</body>
    <script>
        function editProduct(productId) {
            // Redirect to edit page with product ID
            window.location.href = 'edit.php?product_id=' + productId;
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                // Send AJAX request to delete product
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Reload the page after successful deletion
                            window.location.reload();
                        } else {
                            alert('Error deleting product: ' + xhr.responseText);
                        }
                    }
                };
                xhr.open('DELETE', 'delete_product.php?product_id=' + productId, true);
                xhr.send();
            }
        }
    </script>
    </html>";

    mysqli_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
