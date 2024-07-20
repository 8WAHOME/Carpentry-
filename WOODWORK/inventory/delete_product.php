<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../connect.php"); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    try {
        // Get product ID from the query parameters
        $productId = isset($_GET["product_id"]) ? (int)$_GET["product_id"] : null;

        if (!$productId) {
            throw new Exception("Invalid product ID");
        }

        // Delete the product from the database
        $deleteProductSql = "DELETE FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $deleteProductSql);
        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "Product deleted successfully";
        } else {
            throw new Exception("Product not found or could not be deleted");
        }

        mysqli_close($conn);
    } catch (Exception $e) {
        http_response_code(500);
        echo "Error: " . $e->getMessage();
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method";
}
?>
