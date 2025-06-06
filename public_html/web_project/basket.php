<?php
session_start();
require_once 'dbconfig.inc.php';

// Restrict access to logged-in customers only
if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true || $_SESSION['user_type'] !== 'customer') {
    $_SESSION['message'] = "You must be logged in as a customer to view your ongoing rentals.";
    header("Location: login.php");
    exit;
}

$pdo = getPDOConnection();

// Fetch ongoing rentals
try {
    $stmt = $pdo->prepare("
        SELECT r.rental_id, r.flat_id, r.start_date, r.end_date, r.total_cost, r.status,
               f.reference_number, f.address, f.monthly_rent,
               u.name AS owner_name
        FROM rentals r
        JOIN flats f ON r.flat_id = f.flat_id
        LEFT JOIN users u ON f.owner_id = u.user_id
        WHERE r.customer_id = :customer_id AND r.status = 'current'
        ORDER BY r.start_date DESC
    ");
    $stmt->execute(['customer_id' => $_SESSION['user_id']]);
    $ongoing_rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: Unable to fetch ongoing rentals.";
    error_log("Database error in basket.php: " . $e->getMessage());
    $ongoing_rentals = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Ongoing Rentals | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <section class="basket-container">
            <article class="basket-header">
                <h1 class="basket-title">Your Ongoing Rentals</h1>
                <a href="main.php" class="btn btn-outline">Browse Flats</a>
            </article>

            <?php if (isset($_SESSION['message'])): ?>
                <section class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
                    <span class="alert-close" onclick="this.parentElement.style.display='none';">×</span>
                </section>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (!empty($ongoing_rentals)): ?>
                <section class="basket-summary">
                    <article class="summary-row">
                        <span>Ongoing rentals:</span>
                        <span><?php echo count($ongoing_rentals); ?></span>
                    </article>
                    <?php
                    $totalCost = array_sum(array_column($ongoing_rentals, 'total_cost'));
                    ?>
                    <div class="summary-row summary-total">
                        <span>Total Cost:</span>
                        <span>$<?php echo number_format($totalCost, 2); ?></span>
                    </div>
                </section>

                <section class="basket-items">
                    <?php foreach ($ongoing_rentals as $rental): ?>
                        <section class="basket-item">
                            <article class="item-header">
                                <h3>
                                    <a href="flatDetails.php?flat_id=<?= (int)$rental['flat_id'] ?>"
                                       class="flat-button">
                                        <?= htmlspecialchars($rental['reference_number']) ?>
                                    </a>
                                </h3>
                                <span>$<?= number_format($rental['total_cost'], 2) ?></span>
                            </article>
                            <section class="item-body">
                                <div class="item-image">
                                    <?php
                                    $stmt_photo = $pdo->prepare("SELECT photo_path FROM flat_photos WHERE flat_id = :flat_id LIMIT 1");
                                    $stmt_photo->execute(['flat_id' => $rental['flat_id']]);
                                    $photo = $stmt_photo->fetch(PDO::FETCH_ASSOC);
                                    $image_path = $photo ? 'flatImages/' . htmlspecialchars($photo['photo_path']) : 'flatImages/placeholder-image.jpg';
                                    ?>
                                    <img src="<?= $image_path ?>"
                                         alt="<?= htmlspecialchars($rental['reference_number']) ?>">
                                </div>
                                <div class="item-details">
                                    <div class="detail-group">
                                        <h4>Address</h4>
                                        <p class="detail-value"><?= htmlspecialchars($rental['address']) ?></p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Monthly Rent</h4>
                                        <p class="detail-value">$<?= number_format($rental['monthly_rent'], 2) ?></p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Rental Period</h4>
                                        <p class="detail-value">
                                            <?= date('M j, Y', strtotime($rental['start_date'])) ?> -
                                            <?= date('M j, Y', strtotime($rental['end_date'])) ?>
                                        </p>
                                    </div>
                                    <div class="detail-group">
                                        <h4>Owner</h4>
                                        <p class="detail-value"><?= htmlspecialchars($rental['owner_name'] ?? 'Unknown Owner') ?></p>
                                    </div>
                                </div>
                            </section>
                            <section class="item-actions">
                                <button class="btn btn-primary"
                                        onclick="openCheckoutModal(<?php echo $rental['rental_id']; ?>, '<?php echo htmlspecialchars($rental['reference_number']); ?>', <?php echo $rental['total_cost']; ?>)">
                                    Proceed to Checkout
                                </button>
                            </section>
                        </section>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <div class="empty-basket">
                    <div class="empty-basket-icon">🏠</div>
                    <h3>You have no ongoing rentals</h3>
                    <p>Start browsing our available flats to rent one</p>
                    <a href="main.php" class="btn btn-primary">Browse Flats</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</section>

<!-- Checkout Modal -->
<div id="checkoutModal" class="payment-modal">
    <div class="payment-modal-content">
        <div class="modal-header">
            <h2>Checkout</h2>
            <span class="modal-close" onclick="closeCheckoutModal()">×</span>
        </div>
        <div id="modal-body">
            <div class="checkout-summary">
                <p><strong>Reference Number:</strong> <span id="modal-reference"></span></p>
                <p><strong>Total Cost:</strong> $<span id="modal-total"></span></p>
            </div>
            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                <div class="payment-method">
                    <input type="radio" name="payment" value="credit_card" checked>
                    <label>Credit/Debit Card</label>
                </div>
                <div class="payment-method">
                    <input type="radio" name="payment" value="paypal">
                    <label>PayPal</label>
                </div>
                <div class="payment-method">
                    <input type="radio" name="payment" value="bank_transfer">
                    <label>Bank Transfer</label>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary btn-confirm" onclick="confirmCheckout()">Confirm Payment</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openCheckoutModal(rentalId, referenceNumber, totalCost) {
        console.log("Button clicked"); // Debugging
        document.getElementById('modal-reference').textContent = referenceNumber;
        document.getElementById('modal-total').textContent = totalCost.toFixed(2);
        document.getElementById('checkoutModal').style.display = 'flex';
        document.getElementById('checkoutModal').dataset.rentalId = rentalId;
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').style.display = 'none';
    }

    function confirmCheckout() {
        const rentalId = document.getElementById('checkoutModal').dataset.rentalId;
        const paymentMethod = document.querySelector('input[name="payment"]:checked').value;
        const referenceNumber = document.getElementById('modal-reference').textContent;

        // Create success alert
        const successAlert = document.createElement('div');
        successAlert.className = 'alert-success';
        successAlert.innerHTML = `
        <span class="alert-success-icon">✅</span>
        <span>Payment for ${referenceNumber} processed successfully using ${paymentMethod}!</span>
    `;

        // Add alert to the basket-container
        const basketContainer = document.querySelector('.basket-container');
        basketContainer.insertBefore(successAlert, basketContainer.firstChild);

        // Close modal
        closeCheckoutModal();

        // Remove alert after 5 seconds
        setTimeout(() => {
            successAlert.remove();
        }, 5000);
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById('checkoutModal');
        if (event.target === modal) {
            closeCheckoutModal();
        }
    }
</script>

<?php include 'footer.php'; ?>
</body>
</html>