<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../connect.php"); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            // Redirect to index.php after 2 seconds
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
