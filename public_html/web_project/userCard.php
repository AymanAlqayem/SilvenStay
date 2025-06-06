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

try {
    // Fetch user details (handles both owner and customer)
    $stmt = $pdo->prepare("
        SELECT name, city, mobile_number, email, user_type
        FROM users
        WHERE user_id = :user_id AND user_type IN ('owner', 'customer')
    ");
    $stmt->execute(['user_id' => (int)$_GET['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['message'] = "User not found.";
        header("Location: viewRentedFlat.php");
        exit;
    }

    // Format phone number if exists
    $phoneNumber = $user['mobile_number'] ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['mobile_number']) : 'Not provided';

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
    <title><?php echo $user['user_type'] === 'owner' ? 'Owner' : 'Customer'; ?> Details | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <div class="user-profile-container">
            <div class="<?php echo $user['user_type'] === 'owner' ? 'owner-card' : 'customer-card'; ?>">
                <div class="user-header">
                    <div class="user-avatar <?php echo $user['user_type'] === 'owner' ? 'owner-avatar' : 'customer-avatar'; ?>">
                        <?php
                        $initials = '';
                        $nameParts = explode(' ', $user['name']);
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <div class="user-title">
                        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                        <p class="user-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($user['city'] ?: 'Location not specified'); ?>
                        </p>
                    </div>
                </div>

                <div class="user-details">
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
                                <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="email-link">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="user-actions">
                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="contact-button">
                        Contact <?php echo $user['user_type'] === 'owner' ? 'Owner' : 'Customer'; ?>
                    </a>
                </div>
            </div>

            <div class="<?php echo $user['user_type'] === 'owner' ? 'owner-testimonial' : 'customer-testimonial'; ?>">
                <div class="testimonial-header">
                    <span class="testimonial-icon">“</span>
                    <h3>About This <?php echo $user['user_type'] === 'owner' ? 'Owner' : 'Customer'; ?></h3>
                </div>
                <p class="testimonial-content">
                    <?php
                    echo $user['user_type'] === 'owner'
                        ? 'This owner is a trusted member of our community, maintaining high standards for their properties and committed to providing excellent service to their tenants.'
                        : 'This customer is a valued member of our community, known for their reliability and positive engagement with property owners.';
                    ?>
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