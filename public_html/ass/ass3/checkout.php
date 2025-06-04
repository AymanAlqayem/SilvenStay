<?php
session_start();
require_once "dbconfig.inc.php";

ini_set('display_errors', 0);
error_reporting(E_ALL);

$sessionId = session_id();
$error = null;
$success = false;
$orderId = null;
$subtotal = 0;
$tax = 0;
$total = 0;
$email = '';
$paymentMethod = '';
$cartItems = [];

// Generate  token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

error_log("Session ID: $sessionId");

try {
    $pdo = getPDOConnection();
    error_log("Database connection successful");

    // Fetch cart items
    $stmt = $pdo->prepare("
        SELECT p.product_id, p.product_name, p.price, p.quantity AS stock_quantity, 
               c.quantity AS cart_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.session_id = :session_id
    ");
    $stmt->execute([':session_id' => $sessionId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Cart items fetched: " . count($cartItems));

    if (!empty($cartItems)) {
        // Calculate subtotal
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['cart_quantity'];
        }
        $tax = $subtotal * 0.1; // 10% tax (configurable in future)
        $total = $subtotal + $tax;
    } else {
        $error = "Your cart is empty.";
    }

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_checkout'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = "Invalid CSRF token.";
            error_log("CSRF token validation failed");
        } else {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $paymentMethod = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

            // Validate inputs
            if (!$email) {
                $error = "Please provide a valid email address.";
            } elseif (!in_array($paymentMethod, ['credit_card', 'paypal'])) {
                $error = "Please select a valid payment method.";
            } elseif (empty($cartItems)) {
                $error = "Your cart is empty.";
            } else {
                $pdo->beginTransaction();
                error_log("Transaction started");

                // Check stock with FOR UPDATE to prevent race conditions
                $outOfStockItems = [];
                $stockStmt = $pdo->prepare("SELECT quantity FROM products WHERE product_id = :product_id FOR UPDATE");
                foreach ($cartItems as $item) {
                    $stockStmt->execute([':product_id' => $item['product_id']]);
                    $stock = $stockStmt->fetchColumn();
                    if ($item['cart_quantity'] > $stock) {
                        $outOfStockItems[] = $item['product_name'];
                    }
                }

                if (!empty($outOfStockItems)) {
                    $pdo->rollBack();
                    $error = "Not enough stock for: " . implode(", ", $outOfStockItems);
                    error_log("Stock check failed: " . $error);
                } else {
                    // Insert order record
                    $stmt = $pdo->prepare("
                        INSERT INTO orders (session_id, order_date, total_amount, email, payment_method, subtotal, tax)
                        VALUES (:session_id, NOW(), :total, :email, :payment_method, :subtotal, :tax)
                    ");
                    $stmt->execute([
                        ':session_id' => $sessionId,
                        ':total' => $total,
                        ':email' => $email,
                        ':payment_method' => $paymentMethod,
                        ':subtotal' => $subtotal,
                        ':tax' => $tax
                    ]);
                    $orderId = $pdo->lastInsertId();
                    error_log("Order inserted, ID: $orderId");

                    // Insert order items and update stock
                    $insertOrderItem = $pdo->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price)
                        VALUES (:order_id, :product_id, :quantity, :price)
                    ");
                    $updateStock = $pdo->prepare("
                        UPDATE products SET quantity = quantity - :qty WHERE product_id = :product_id
                    ");

                    foreach ($cartItems as $item) {
                        $insertOrderItem->execute([
                            ':order_id' => $orderId,
                            ':product_id' => $item['product_id'],
                            ':quantity' => $item['cart_quantity'],
                            ':price' => $item['price']
                        ]);
                        $updateStock->execute([
                            ':qty' => $item['cart_quantity'],
                            ':product_id' => $item['product_id']
                        ]);
                    }
                    error_log("Order items inserted, stock updated");

                    // Clear cart
                    $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = :session_id");
                    $stmt->execute([':session_id' => $sessionId]);
                    error_log("Cart cleared");

                    $pdo->commit();
                    error_log("Transaction committed");
                    $success = true;

                    // Regenerate CSRF token after successful submission
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
            }
        }
    }
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Checkout error: " . $e->getMessage());
    $error = "An unexpected error occurred. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Checkout | Your Store</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<?php if ($success): ?>
    <section class="confirmation-container">
        <article class="confirmation-icon">✓</article>
        <h1>Order Confirmed!</h1>
        <p>Thank you for your purchase. We've sent a confirmation email to
            <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
        <article class="order-number">Order #<?php echo htmlspecialchars($orderId); ?></article>
        <section class="order-details">
            <h3>Order Summary</h3>
            <article class="detail-row">
                <span>Payment Method:</span>
                <span><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $paymentMethod))); ?></span>
            </article>
            <article class="detail-row">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($subtotal, 2); ?></span>
            </article>
            <article class="detail-row">
                <span>Tax (10%):</span>
                <span>$<?php echo number_format($tax, 2); ?></span>
            </article>
            <article class="detail-row">
                <span>Total:</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </article>
        </section>
        <a href="products.php" class="btn-continue">Continue Shopping</a>
    </section>
<?php else: ?>
    <section class="checkout-container">
        <header class="checkout-header">
            <h1>Checkout</h1>
        </header>
        <?php if ($error): ?>
            <article class="error-message"><?php echo htmlspecialchars($error); ?></article>
        <?php endif; ?>
        <?php if (!empty($cartItems)): ?>
            <main class="order-summary">
                <h2>Your Order</h2>
                <ul class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="cart-item">
                            <section class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                <div class="item-price">$<?php echo number_format($item['price'], 2); ?></div>
                                <div class="item-quantity">Quantity: <?php echo $item['cart_quantity']; ?></div>
                            </section>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </main>
            <aside class="payment-summary">
                <h2>Payment Summary</h2>
                <article class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value">$<?php echo number_format($subtotal, 2); ?></span>
                </article>
                <article class="summary-row">
                    <span class="summary-label">Shipping</span>
                    <span class="summary-value">$0.00</span>
                </article>
                <article class="summary-row">
                    <span>Tax (10%)</span>
                    <span class="summary-value">$<?php echo number_format($tax, 2); ?></span>
                </article>
                <article class="summary-row total-row">
                    <span class="summary-label">Total</span>
                    <span class="total-value">$<?php echo number_format($total, 2); ?></span>
                </article>
                <form method="post" class="checkout-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <section class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?php echo htmlspecialchars($email); ?>" required>
                    </section>
                    <section class="payment-methods">
                        <h3>Checkout</h3>
                        <label class="payment-method">
                            <input type="radio" name="payment_method"
                                   value="credit_card" <?php echo $paymentMethod === 'credit_card' ? 'checked' : ''; ?>
                                   required>
                            <span class="payment-content">
        <img src="images/creditcard.png" alt="Credit card icon" class="payment-icon">
        <span class="payment-text">Pay with Credit Card</span>
    </span>
                        </label>

                        <label class="payment-method">
                            <input type="radio" name="payment_method"
                                   value="paypal" <?php echo $paymentMethod === 'paypal' ? 'checked' : ''; ?>>
                            <span class="payment-content">
        <img src="images/paypal.png" alt="PayPal icon" class="payment-icon">
        <span class="payment-text">Pay with PayPal</span>
    </span>
                        </label>
                    </section>
                    <button type="submit" name="confirm_checkout" class="btn-checkout">Complete Purchase</button>
                </form>
                <a href="cart.php" class="back-link">← Back to Cart</a>
            </aside>
        <?php else: ?>
            <article class="error-message">
                Your cart is empty. <a href="products.php">Go shopping</a>
            </article>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php include 'footer.php'; ?>
</body>
</html>