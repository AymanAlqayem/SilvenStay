<?php
session_start();
require_once "dbconfig.inc.php";

$sessionId = session_id();
// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Handle add-to-cart from URL
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!$productId) {
        header("Location: cart.php?status=error");
        exit;
    }

    try {
        $pdo = getPDOConnection();

        // Get max available quantity from products
        $checkStmt = $pdo->prepare("SELECT quantity FROM products WHERE product_id = :product_id");
        $checkStmt->execute([':product_id' => $productId]);
        $available = $checkStmt->fetchColumn();

        if ($available === false) {
            header("Location: cart.php?status=error");
            exit;
        }

        // Check if item already in cart
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE session_id = :session_id AND product_id = :product_id");
        $stmt->execute([
            ':session_id' => $sessionId,
            ':product_id' => $productId
        ]);
        $existing = $stmt->fetchColumn();

        if ($existing !== false) {
            if ($existing < $available) {
                // Update quantity
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE session_id = :session_id AND product_id = :product_id");
                $stmt->execute([
                    ':session_id' => $sessionId,
                    ':product_id' => $productId
                ]);
            } else {
                // Quantity exceeds available stock
                header("Location: cart.php?status=limit");
                exit;
            }
        } else {
            // Insert new cart item
            $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (:session_id, :product_id, 1)");
            $stmt->execute([
                ':session_id' => $sessionId,
                ':product_id' => $productId
            ]);
        }

        header("Location: cart.php?status=added");
        exit;
    } catch (PDOException $e) {
        error_log("Add-to-cart failed: " . $e->getMessage());
        header("Location: cart.php?status=error");
        exit;
    }
}

// Load cart contents
$cartItems = [];
$total = 0;
$totalQuantity = 0;

try {
    $pdo = getPDOConnection();

    $stmt = $pdo->prepare("
        SELECT p.*, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.session_id = :session_id
    ");
    $stmt->execute([':session_id' => $sessionId]);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
        $totalQuantity += $row['quantity'];

        $cartItems[] = [
            'id' => $row['product_id'],
            'name' => $row['product_name'],
            'description' => $row['description'],
            'price' => $row['price'],
            'image' => preg_replace('/[^A-Za-z0-9_\-\.]/', '', $row['image_name']) ?: 'default.jpg',
            'quantity' => $row['quantity']
        ];
    }
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    header("Location: cart.php?status=error");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<?php if (isset($_GET['status'])): ?>
    <section
            class="custom-alert <?php echo ($_GET['status'] === 'limit' || $_GET['status'] === 'error' || $_GET['status'] === 'min') ? 'alert-warning' : 'alert-success'; ?>">
        <?php if ($_GET['status'] === 'limit'): ?>
            ⚠️ You can't add more than the available quantity.
        <?php elseif ($_GET['status'] === 'min'): ?>
            ⚠️ Quantity can't be less than 1.
        <?php elseif ($_GET['status'] === 'error'): ?>
            ⚠️ An error occurred. Please try again.
        <?php elseif ($_GET['status'] === 'added'): ?>
            ✅ Item added to cart successfully!
        <?php endif; ?>
    </section>
<?php endif; ?>

<section class="cart-container">
    <article class="cart-header">
        <h1>Shopping Cart</h1>
    </article>

    <section class="cart">
        <?php if (!empty($cartItems)) : ?>
            <ul class="cart-items">
                <?php foreach ($cartItems as $item) : ?>
                    <li class="cart-item">
                        <section class="item-image-container">
                            <img src="images/<?php echo htmlspecialchars($item['image']); ?>"
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                        </section>
                        <section class="item-details">
                            <article class="item-name"><?php echo htmlspecialchars($item['name']); ?></article>
                            <article
                                    class="item-description"><?php echo htmlspecialchars($item['description']); ?></article>
                            <article>
                                <span class="item-price">$<?php echo number_format($item['price'], 2); ?></span>
                            </article>
                            <section class="item-quantity">
                                <form action="update_cart.php" method="post">
                                    <input type="hidden" name="csrf_token"
                                           value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="action" value="decrease" class="quantity-btn decrease">
                                        -
                                    </button>
                                </form>
                                <span class="quantity"><?php echo $item['quantity']; ?></span>
                                <form action="update_cart.php" method="post">
                                    <input type="hidden" name="csrf_token"
                                           value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="action" value="increase" class="quantity-btn increase">
                                        +
                                    </button>
                                </form>
                            </section>
                        </section>
                        <form action="update_cart.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="action" value="remove" class="remove-btn" title="Remove item">
                                ×
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <section class="cart-footer">
                <article class="total-section">
                    <article class="subtotal">Subtotal (<?php echo $totalQuantity; ?> items)</article>
                    <article class="total-price">$<span><?php echo number_format($total, 2); ?></span></article>
                </article>
                <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
            </section>
        <?php else: ?>
            <section class="empty-cart">
                <article class="empty-cart-icon">🛒</article>
                <h3>Your Cart is Empty</h3>
                <p>Looks like you haven't added anything to your cart yet. Browse our collection and find something
                    special!</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </section>
        <?php endif; ?>
    </section>
</section>

<?php include 'footer.php'; ?>
</body>
</html>