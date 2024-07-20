<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../connect.php"); // Include your database connection file

function sanitizeInput($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}

try {
    // Get form data with checks for isset()
    $productName = isset($_POST["product_name"]) ? sanitizeInput($conn, $_POST["product_name"]) : null;
    $productType = isset($_POST["product_type"]) ? sanitizeInput($conn, $_POST["product_type"]) : null;
    $description = isset($_POST["description"]) ? sanitizeInput($conn, $_POST["description"]) : null;
    $price = isset($_POST["price"]) ? (float)$_POST["price"] : null;

    // Server-side validation
    if (empty($productName) || empty($productType) || empty($price)) {
        throw new Exception("Error: Please fill in required fields (Product Name, Product Type, Price).");
    }

    // Handle image upload
    $targetDir = "image/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            throw new Exception("Error: File is not an image.");
            $uploadOk = 0;
        }
    }

    // Check file size
    if ($_FILES["image"]["size"] > 500000) {
        throw new Exception("Error: File is too large.");
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        throw new Exception("Error: Only JPG, JPEG, PNG, and GIF files are allowed.");
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        throw new Exception("Error: Your file was not uploaded.");
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            echo "Image uploaded successfully.";
        } else {
            throw new Exception("Error uploading image.");
        }
    }

    // Insert data into the products table
$insertProductSql = "INSERT INTO products (product_name, product_type, description, price, image)
VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $insertProductSql);
mysqli_stmt_bind_param($stmt, "sssds", $productName, $productType, $description, $price, $targetFile);
mysqli_stmt_execute($stmt);


    echo "Product data inserted successfully!";
    
    mysqli_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
