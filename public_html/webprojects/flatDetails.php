<?php
session_start();
require_once 'dbconfig.inc.php';

$flat_id = isset($_GET['flat_id']) ? intval($_GET['flat_id']) : 0;
if ($flat_id <= 0) {
    $_SESSION['message'] = "Invalid or missing flat ID.";
    header("Location: main.php");
    exit;
}

$pdo = getPDOConnection();

// Get flat details using named parameter
try {
    $stmt = $pdo->prepare("
        SELECT f.*, u.name AS owner_name, d.title, d.description
        FROM flats f
        LEFT JOIN flat_descriptions d ON f.flat_id = d.flat_id
        JOIN users u ON f.owner_id = u.user_id
        WHERE f.flat_id = :flat_id
    ");
    $stmt->execute(['flat_id' => $flat_id]);
    $flat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$flat) {
        $_SESSION['message'] = "Flat not found.";
        header("Location: main.php");
        exit;
    }

    // Get flat photos
    $photoStmt = $pdo->prepare("SELECT photo_path FROM flat_photos WHERE flat_id = :flat_id");
    $photoStmt->execute(['flat_id' => $flat_id]);
    $photos = $photoStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: Unable to fetch flat details.";
    error_log("Database error in flatDetails.php: " . $e->getMessage());
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($flat['title'] ?? 'Flat ' . $flat['reference_number']) ?> | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main class="site-main">

    <h2><?= htmlspecialchars($flat['title'] ?? 'Flat ' . $flat['reference_number']) ?></h2>

    <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?> - <?= htmlspecialchars($flat['address']) ?>
    </p>

    <p><strong>Rent:</strong> $<?= number_format($flat['monthly_rent'], 2) ?>/month</p>

    <p><strong>Size:</strong> <?= (int)$flat['size_sqm'] ?> sqm | <?= (int)$flat['bedrooms'] ?> BR
        | <?= (int)$flat['bathrooms'] ?> BA</p>

    <p><strong>Available From:</strong> <?= htmlspecialchars($flat['available_from']) ?></p>

    <p><strong>Owner:</strong> <?= htmlspecialchars($flat['owner_name'] ?? 'Unknown Owner') ?></p>

    <section class="photo-slider">

        <?php if (!empty($photos)): ?>
            <?php foreach ($photos as $photo): ?>
                <img src="flatImages/<?= htmlspecialchars($photo['photo_path']) ?>" alt="Flat photo">
            <?php endforeach; ?>
        <?php else: ?>
            <p>No photos available</p>
        <?php endif; ?>
    </section>

    <?php if (!empty($flat['description'])): ?>
        <h3>Description</h3>
        <ul class="bullet-points">
            <?php
            foreach (explode("\n", $flat['description']) as $bullet) {
                $cleanBullet = trim($bullet, "• \t\r\n");
                if (!empty($cleanBullet)) {
                    echo '<li>' . htmlspecialchars($cleanBullet) . '</li>';
                }
            }
            ?>
        </ul>
    <?php endif; ?>

    <h3>Amenities</h3>
    <ul class="bullet-points">
        <li><?= $flat['is_furnished'] ? 'Furnished' : 'Unfurnished' ?></li>
        <li><?= $flat['has_heating'] ? 'Heating available' : 'No heating' ?></li>
        <li><?= $flat['has_ac'] ? 'Air Conditioning' : 'No AC' ?></li>
        <li><?= $flat['has_access_control'] ? 'Access Control' : 'No access control' ?></li>
        <li><?= $flat['has_parking'] ? 'Parking' : 'No parking' ?></li>
        <li><?= $flat['has_backyard'] ? 'Backyard' : 'No backyard' ?></li>
        <li><?= $flat['has_playground'] ? 'Playground' : 'No playground' ?></li>
        <li><?= $flat['has_storage'] ? 'Storage space included' : 'No storage' ?></li>
    </ul>

    <?php if (!empty($flat['rental_conditions'])): ?>
        <h3>Rental Conditions</h3>
        <p><?= nl2br(htmlspecialchars($flat['rental_conditions'])) ?></p>
    <?php endif; ?>

    <!-- Action Buttons -->
    <section class="rent-buttons">
        <a href="rent.php?id=<?= (int)$flat_id ?>">Rent this Flat</a>
        <a href="requestAppointment.php?id=<?= (int)$flat_id ?>">Request Appointment</a>
    </section>

</main>

<?php include 'footer.php'; ?>
</body>
</html>