<?php
session_start();
require_once 'dbconfig.inc.php';

// Check if user_id is provided
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    $_SESSION['message'] = "Invalid user ID.";
    header("Location: viewRentedFlat.php");
    exit;
}

$pdo = getPDOConnection();

// Fetch owner details
try {
    $stmt = $pdo->prepare("
        SELECT name, city, mobile_number, email
        FROM users
        WHERE user_id = :user_id AND user_type = 'owner'
    ");
    $stmt->execute(['user_id' => (int)$_GET['user_id']]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$owner) {
        $_SESSION['message'] = "Owner not found.";
        header("Location: viewRentedFlat.php");
        exit;
    }

    // Format phone number if exists
    $phoneNumber = $owner['mobile_number'] ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $owner['mobile_number']) : 'Not provided';
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . htmlspecialchars($e->getMessage());
    header("Location: viewRentedFlat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Details | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <div class="owner-profile-container">
            <div class="owner-card">
                <div class="owner-header">
                    <div class="owner-avatar">
                        <?php
                        $initials = '';
                        $nameParts = explode(' ', $owner['name']);
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <div class="owner-title">
                        <h2><?php echo htmlspecialchars($owner['name']); ?></h2>
                        <p class="owner-location">
                            <?php echo htmlspecialchars($owner['city'] ?: 'Location not specified'); ?>
                        </p>
                    </div>
                </div>

                <div class="owner-details">
                    <div class="detail-item">
                        <div class="detail-icon">📞</div>
                        <div class="detail-content">
                            <h4>Contact Number</h4>
                            <p><?php echo htmlspecialchars($phoneNumber); ?></p>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">✉️</div>
                        <div class="detail-content">
                            <h4>Email Address</h4>
                            <p>
                                <a href="mailto:<?php echo htmlspecialchars($owner['email']); ?>" class="email-link">
                                    <?php echo htmlspecialchars($owner['email']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="owner-actions">
                    <a href="viewRentedFlat.php" class="btn btn-outline">Back to Rented Flats</a>
                    <a href="mailto:<?php echo htmlspecialchars($owner['email']); ?>" class="btn btn-primary">Contact
                        Owner</a>
                </div>
            </div>

            <div class="owner-testimonial">
                <div class="testimonial-header">
                    <span class="testimonial-icon">“</span>
                    <h3>About This Owner</h3>
                </div>
                <p class="testimonial-content">
                    This owner is a trusted member of our community, maintaining high standards for their properties and
                    committed to providing excellent service to their tenants.
                </p>
                <div class="trust-badges">
                    <div class="badge">
                        <span class="badge-icon">✔️</span>
                        <span>Verified Member</span>
                    </div>
                    <div class="badge">
                        <span class="badge-icon">🛡️</span>
                        <span>Secure Communication</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>