<?php
session_start();
require_once "dbconfig.inc.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
    $productId = (int)$_POST['id'];
    $sessionId = session_id();
    $status = "";

    try {
        $pdo = getPDOConnection();

        // Get current cart quantity
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE session_id = :session_id AND product_id = :product_id");
        $stmt->execute([':session_id' => $sessionId, ':product_id' => $productId]);
        $cartRow = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get available product quantity
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE product_id = :product_id");
        $stmt->execute([':product_id' => $productId]);
        $productRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$productRow) {
            header("Location: cart.php");
            exit;
        }

        $stock = (int)$productRow['quantity'];
        $currentQty = (int)($cartRow['quantity'] ?? 0);

        switch ($_POST['action']) {
            case 'increase':
                if ($currentQty < $stock) {
                    $stmt = $pdo->prepare("
                        UPDATE cart SET quantity = quantity + 1 
                        WHERE session_id = :session_id AND product_id = :product_id
                    ");
                    $stmt->execute([
                        ':session_id' => $sessionId,
                        ':product_id' => $productId,
                    ]);
                } else {
                    $status = "limit"; // max reached
                }
                break;

            case 'decrease':
                if ($currentQty > 1) {
                    $stmt = $pdo->prepare("
                        UPDATE cart SET quantity = quantity - 1 
                        WHERE session_id = :session_id AND product_id = :product_id
                    ");
                    $stmt->execute([
                        ':session_id' => $sessionId,
                        ':product_id' => $productId,
                    ]);
                } else {
                    $status = "min"; // can't go below 1
                }
                break;

            case 'remove':
                $stmt = $pdo->prepare("
                    DELETE FROM cart 
                    WHERE session_id = :session_id AND product_id = :product_id
                ");
                $stmt->execute([
                    ':session_id' => $sessionId,
                    ':product_id' => $productId,
                ]);
                break;
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    // Redirect back with status
    header("Location: cart.php" . ($status ? "?status={$status}" : ""));
    exit;
}
?>
