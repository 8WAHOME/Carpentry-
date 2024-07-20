<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../connect.php"); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Get product ID from the query parameters
        $productId = isset($_GET["product_id"]) ? (int)$_GET["product_id"] : null;

        if (!$productId) {
            throw new Exception("Invalid product ID");
        }

        // Fetch product details from the database
        $getProductSql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $getProductSql);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result || mysqli_num_rows($result) == 0) {
            throw new Exception("Product not found");
        }

        // Display the form for editing the product
        $product = mysqli_fetch_assoc($result);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Product</title>
        </head>
        <body>
            <h1>Edit Product</h1>
            <form action="edit.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <label for="product_name">Product Name:</label>
                <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>" required><br>
                <label for="product_type">Product Type:</label>
                <input type="text" name="product_type" value="<?php echo $product['product_type']; ?>" required><br>
                <label for="description">Description:</label>
                <textarea name="description"><?php echo $product['description']; ?></textarea><br>
                <label for="price">Price:</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required><br>
                <input type="submit" value="Update Product">
            </form>
        </body>
        </html>
        <?php

        mysqli_close($conn);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get form data
        $productId = isset($_POST["product_id"]) ? (int)$_POST["product_id"] : null;
        $productName = isset($_POST["product_name"]) ? trim($_POST["product_name"]) : null;
        $productType = isset($_POST["product_type"]) ? trim($_POST["product_type"]) : null;
        $description = isset($_POST["description"]) ? trim($_POST["description"]) : null;
        $price = isset($_POST["price"]) ? (float)$_POST["price"] : null;

        // Server-side validation
        if (!$productId || empty($productName) || empty($productType) || empty($price)) {
            throw new Exception("Error: Invalid or missing data for updating the product.");
        }

        // Update product details in the database
        $updateProductSql = "UPDATE products SET product_name = ?, product_type = ?, description = ?, price = ? WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $updateProductSql);
        mysqli_stmt_bind_param($stmt, "ssdsi", $productName, $productType, $description, $price, $productId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Product updated successfully. Redirecting...";
            // Redirect to index.html after 2 seconds
            header("refresh:2;url=index.html");
        } else {
            throw new Exception("Product not found or could not be updated");
        }

        mysqli_close($conn);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method";
}
?>
