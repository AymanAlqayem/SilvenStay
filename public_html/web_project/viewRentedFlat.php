<?php
session_start();
require_once 'dbconfig.inc.php';

// Restrict access to logged-in customers only
if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true || $_SESSION['user_type'] !== 'customer') {
    $_SESSION['message'] = "You must be logged in as a customer to view your rented flats.";
    header("Location: login.php");
    exit;
}

$pdo = getPDOConnection();

// Fetch rented flats
try {
    $stmt = $pdo->prepare("
        SELECT r.rental_id, r.flat_id, r.start_date, r.end_date, r.total_cost, r.status,
               f.reference_number, f.monthly_rent, f.location, f.address,
               u.user_id AS owner_id, u.name AS owner_name,
               (SELECT photo_path FROM flat_photos fp WHERE fp.flat_id = r.flat_id LIMIT 1) AS photo_path
        FROM rentals r
        JOIN flats f ON r.flat_id = f.flat_id
        JOIN users u ON f.owner_id = u.user_id
        WHERE r.customer_id = :customer_id
        ORDER BY r.start_date DESC
    ");
    $stmt->execute(['customer_id' => $_SESSION['user_id']]);
    $rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . htmlspecialchars($e->getMessage());
    $rentals = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Rented Flats | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Capitalize status text for better readability */
        .status-pending { text-transform: capitalize; }
        .status-current { text-transform: capitalize; }
        .status-past { text-transform: capitalize; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <section class="rentals-container">
            <article class="rentals-header">
                <h1 class="rentals-title">My Rented Flats</h1>
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

            <?php if (!empty($rentals)): ?>
                <table class="rental-table search-table zebra-stripe sortable">
                    <thead>
                    <tr>
                        <th>Flat Reference</th>
                        <th>Monthly Rent</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Owner</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rentals as $rental): ?>
                        <tr class="status-<?php echo htmlspecialchars($rental['status']); ?>">
                            <td>
                                <a href="flatDetails.php?flat_id=<?php echo (int)$rental['flat_id']; ?>" target="_blank"
                                   class="flat-button">
                                    <?php echo htmlspecialchars($rental['reference_number']); ?>
                                </a>
                            </td>
                            <td>$<?php echo number_format($rental['monthly_rent'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($rental['start_date'])); ?></td>
                            <td><?php echo date('M j, Y', strtotime($rental['end_date'])); ?></td>
                            <td><?php echo htmlspecialchars($rental['location']); ?></td>
                            <td><?php echo htmlspecialchars($rental['status']); ?></td>
                            <td>
                                <a href="userCard.php?user_id=<?php echo (int)$rental['owner_id']; ?>" target="_blank"
                                   class="owner-link">
                                    <?php echo htmlspecialchars($rental['owner_name']); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-rentals">
                    <div class="empty-rentals-icon">🏠</div>
                    <h3>You have no rented flats</h3>
                    <p>Start browsing our available flats to rent one</p>
                    <a href="main.php" class="btn btn-primary">Browse Flats</a>
                </div>
            <?php endif; ?>
        </section>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>