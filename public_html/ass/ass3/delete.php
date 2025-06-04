<?php
require_once "dbconfig.inc.php";
$pdo = getPDOConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = intval($_GET['id']);

    // Delete product from database
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = :id");
    $success = $stmt->execute([':id' => $productId]);

    if ($success) {
        // Delete image file
        $imagePath = __DIR__ . "/images/{$productId}.jpeg";
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        header("Location: products.php?message=deleted");
    } else {
        header("Location: products.php?message=delete_failed");
    }
} else {
    header("Location: products.php?message=invalid_id");
}

exit;
