<?php
session_start();
require_once 'dbconfig.inc.php';

// Restrict access to logged-in customers only
if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true || $_SESSION['user_type'] !== 'customer') {
    $_SESSION['message'] = "You must be logged in as a customer to view the basket.";
    header("Location: login.php");
    exit;
}

$pdo = getPDOConnection();

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_rental_id'])) {
    $pending_rental_id = (int) $_POST['cancel_rental_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pending_rentals WHERE pending_rental_id = :pending_rental_id AND customer_id = :customer_id");
        $stmt->execute(['pending_rental_id' => $pending_rental_id, 'customer_id' => $_SESSION['user_id']]);
        $_SESSION['message'] = "Rental removed from basket successfully.";
        header("Location: basket.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error cancelling rental: " . htmlspecialchars($e->getMessage());
    }
}

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_to_checkout'])) {
    $credit_card_number = trim($_POST['credit_card_number'] ?? '');
    $expiry_date = trim($_POST['expiry_date'] ?? '');
    $cardholder_name = trim($_POST['cardholder_name'] ?? '');

    // Validate payment inputs
    $errors = [];
    if (!preg_match('/^\d{9}$/', $credit_card_number)) {
        $errors[] = "Credit card number must be exactly 9 digits.";
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry_date) || strtotime($expiry_date) < time()) {
        $errors[] = "Invalid or expired expiry date (YYYY-MM-DD).";
    }
    if (empty($cardholder_name) || strlen($cardholder_name) > 100) {
        $errors[] = "Cardholder name is required and must be 100 characters or less.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Fetch all pending rentals for the user
            $stmt = $pdo->prepare("
                SELECT pr.pending_rental_id, pr.flat_id, pr.start_date, pr.end_date, pr.total_cost
                FROM pending_rentals pr
                WHERE pr.customer_id = :customer_id
            ");
            $stmt->execute(['customer_id' => $_SESSION['user_id']]);
            $pending_rentals_to_confirm = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($pending_rentals_to_confirm)) {
                $_SESSION['message'] = "Your basket is empty. Nothing to checkout.";
            } else {
                // Check flat availability
                foreach ($pending_rentals_to_confirm as $rental) {
                    $stmt = $pdo->prepare("
                        SELECT status, available_from, available_to, reference_number
                        FROM flats
                        WHERE flat_id = :flat_id
                    ");
                    $stmt->execute(['flat_id' => $rental['flat_id']]);
                    $flat = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$flat) {
                        $errors[] = "Flat {$rental['flat_id']} not found.";
                        continue;
                    }

                    if ($flat['status'] === 'rented') {
                        $errors[] = "Flat {$flat['reference_number']} is already rented.";
                    }

                    if ($flat['available_from'] > $rental['start_date'] || ($flat['available_to'] !== null && $flat['available_to'] < $rental['end_date'])) {
                        $errors[] = "Flat {$flat['reference_number']} is not available for the selected dates.";
                    }

                    // Check for overlapping rentals
                    $stmt = $pdo->prepare("
                        SELECT COUNT(*) as count
                        FROM rentals
                        WHERE flat_id = :flat_id
                        AND (
                            (start_date <= :end_date AND end_date >= :start_date)
                            OR (start_date >= :start_date AND end_date <= :end_date)
                            OR (:start_date >= start_date AND :end_date <= end_date)
                        )
                        AND status IN ('pending', 'current')
                    ");
                    $stmt->execute([
                        'flat_id' => $rental['flat_id'],
                        'start_date' => $rental['start_date'],
                        'end_date' => $rental['end_date']
                    ]);
                    $overlap = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($overlap['count'] > 0) {
                        $errors[] = "Flat {$flat['reference_number']} has conflicting rental dates.";
                    }
                }

                if (!empty($errors)) {
                    $pdo->rollBack();
                    $_SESSION['message'] = implode(" ", $errors);
                } else {
                    // Insert into rentals table
                    $stmt_rental = $pdo->prepare("
                        INSERT INTO rentals (flat_id, customer_id, start_date, end_date, total_cost, status)
                        VALUES (:flat_id, :customer_id, :start_date, :end_date, :total_cost, 'pending')
                    ");

                    // Insert into payments table
                    $stmt_payment = $pdo->prepare("
                        INSERT INTO payments (rental_id, credit_card_number, expiry_date, cardholder_name, payment_date)
                        VALUES (:rental_id, :credit_card_number, :expiry_date, :cardholder_name, NOW())
                    ");

                    foreach ($pending_rentals_to_confirm as $rental) {
                        // Insert rental
                        $stmt_rental->execute([
                            'flat_id' => $rental['flat_id'],
                            'customer_id' => $_SESSION['user_id'],
                            'start_date' => $rental['start_date'],
                            'end_date' => $rental['end_date'],
                            'total_cost' => $rental['total_cost']
                        ]);

                        // Get the last inserted rental_id
                        $rental_id = $pdo->lastInsertId();

                        // Insert payment
                        $stmt_payment->execute([
                            'rental_id' => $rental_id,
                            'credit_card_number' => $credit_card_number,
                            'expiry_date' => $expiry_date,
                            'cardholder_name' => $cardholder_name
                        ]);
                    }

                    // Delete from pending_rentals
                    $stmt = $pdo->prepare("DELETE FROM pending_rentals WHERE customer_id = :customer_id");
                    $stmt->execute(['customer_id' => $_SESSION['user_id']]);

                    $pdo->commit();
                    $_SESSION['message'] = "Checkout successful! Your rentals have been confirmed and payment processed.";
                }
            }
            header("Location: basket.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['message'] = "Error during checkout: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $_SESSION['message'] = implode(" ", $errors);
    }
}

// Fetch pending rentals
try {
    $stmt = $pdo->prepare("
        SELECT pr.pending_rental_id, pr.flat_id, pr.start_date, pr.end_date, pr.total_cost, pr.created_at,
               f.reference_number, f.address, f.monthly_rent
        FROM pending_rentals pr
        JOIN flats f ON pr.flat_id = f.flat_id
        WHERE pr.customer_id = :customer_id
        ORDER BY pr.created_at DESC
    ");
    $stmt->execute(['customer_id' => $_SESSION['user_id']]);
    $pending_rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . htmlspecialchars($e->getMessage());
    $pending_rentals = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Basket | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .payment-modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .payment-modal-content h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
        }
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .btn-primary:hover, .btn-secondary:hover {
            opacity: 0წ

            .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <section class="basket-container">
            <article class="basket-header">
                <h1 class="basket-title">Your Rental Basket</h1>
                <?php if (!empty($pending_rentals)): ?>
                    <a href="main.php" class="btn btn-outline">Continue Browsing</a>
                <?php endif; ?>
            </article>

            <?php if (isset($_SESSION['message'])): ?>
                <section class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
                    <span class="alert-close" onclick="this.parentElement.style.display='none';">×</span>
                </section>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (!empty($pending_rentals)): ?>
                <section class="basket-summary">
                    <article class="summary-row">
                        <span>Items in basket:</span>
                        <span><?php echo count($pending_rentals); ?></span>
                    </article>
                    <?php
                    $totalCost = array_sum(array_column($pending_rentals, 'total_cost'));
                    ?>
                    <div class="summary-row summary-total">
                        <span>Estimated Total:</span>
                        <span>$<?php echo number_format($totalCost, 2); ?></span>
                    </div>
                </section>

                <section class="basket-items">
                    <?php foreach ($pending_rentals as $rental): ?>
                        <section class="basket-item">
                            <article class="item-header">
                                <h3><?php echo htmlspecialchars($rental['reference_number']); ?></h3>
                                <span>$<?php echo number_format($rental['total_cost'], 2); ?></span>
                            </article>

                            /* ... (The rest of the HTML remains unchanged) ... */
                            <section class="item-body">
                                <div class="item-image">
                                    <?php
                                    $stmt_photo = $pdo->prepare("SELECT photo_path FROM flat_photos WHERE flat_id = :flat_id LIMIT 1");
                                    $stmt_photo->execute(['flat_id' => $rental['flat_id']]);
                                    $photo = $stmt_photo->fetch(PDO::FETCH_ASSOC);
                                    $image_path = $photo ? htmlspecialchars($photo['photo_path']) : 'placeholder-image.jpg';
                                    ?>
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($rental['reference_number']); ?>">
                                </div>
                                <div class="item-details">
                                    <div class="detail-group">
                                        <h4>Address</h4>
                                        <p class="detail-value"><?php echo htmlspecialchars($rental['address']); ?></p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Monthly Rent</h4>
                                        <p class="detail-value">$<?php echo number_format($rental['monthly_rent'], 2); ?></p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Rental Period</h4>
                                        <p class="detail-value">
                                            <?php echo date('M j, Y', strtotime($rental['start_date'])); ?> -
                                            <?php echo date('M j, Y', strtotime($rental['end_date'])); ?>
                                        </p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Added On</h4>
                                        <p class="detail-value"><?php echo date('M j, Y g:i a', strtotime($rental['created_at'])); ?></p>
                                    </div>
                                </div>
                            </section>
                            <div class="item-actions">
                                <form action="basket.php" method="POST">
                                    <input type="hidden" name="cancel_rental_id" value="<?php echo $rental['pending_rental_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            </div>
                        </section>
                    <?php endforeach; ?>

                    <section class="proceed-in-basket">
                        <button class="btn btn-primary" onclick="showPaymentForm()">Proceed to Checkout</button>
                    </section>
                </section>

                <div class="payment-modal" id="paymentModal">
                    <div class="payment-modal-content">
                        <h2>Payment Details</h2>
                        <form action="basket.php" method="POST">
                            <input type="hidden" name="proceed_to_checkout" value="1">
                            <div class="form-group">
                                <label for="credit_card_number">Credit Card Number (9 digits)</label>
                                <input type="text" id="credit_card_number" name="credit_card_number" maxlength="9" pattern="\d{9}" required>
                            </div>
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date" required>
                            </div>
                            <div class="form-group">
                                <label for="cardholder_name">Cardholder Name</label>
                                <input type="text" id="cardholder_name" name="cardholder_name" maxlength="100" required>
                            </div>
                            <div class="modal-actions">
                                <button type="button" class="btn btn-secondary" onclick="hidePaymentForm()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Confirm Payment</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-basket">
                    <div class="empty-basket-icon">🛒</div>
                    <h3>Your basket is empty</h3>
                    <p>Start browsing our available flats to add rentals to your basket</p>
                    <a href="main.php">Browse Flats</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</section>

<script>
    function showPaymentForm() {
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function hidePaymentForm() {
        document.getElementById('paymentModal').style.display = 'none';
    }
</script>
<?php include 'footer.php'; ?>
</body>
</html>