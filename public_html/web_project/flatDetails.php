<?php
require 'dbconfig.inc.php';

$flat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($flat_id <= 0) {
    die("Invalid flat ID.");
}

$pdo = getPDOConnection();

// Get flat details using named parameter
$stmt = $pdo->prepare("
    SELECT f.*, d.title, d.description
    FROM flats f
    JOIN flat_descriptions d ON f.flat_id = d.flat_id
    WHERE f.flat_id = :flat_id
");
$stmt->execute(['flat_id' => $flat_id]);
$flat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flat) {
    die("Flat not found.");
}

// Get flat photos
$photoStmt = $pdo->prepare("SELECT photo_path FROM flat_photos WHERE flat_id = :flat_id");
$photoStmt->execute(['flat_id' => $flat_id]);
$photos = $photoStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($flat['title']) ?> | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main class="site-main">
    <h2><?= htmlspecialchars($flat['title']) ?></h2>
    <p><strong>Location:</strong> <?= htmlspecialchars($flat['location']) ?> - <?= htmlspecialchars($flat['address']) ?>
    </p>
    <p><strong>Rent:</strong> $<?= number_format($flat['monthly_rent'], 2) ?>/month</p>
    <p><strong>Size:</strong> <?= $flat['size_sqm'] ?> sqm | <?= $flat['bedrooms'] ?> BR | <?= $flat['bathrooms'] ?> BA
    </p>
    <p><strong>Available From:</strong> <?= htmlspecialchars($flat['available_from']) ?></p>

    <section class="photo-slider">
        <?php foreach ($photos as $photo): ?>
            <img src="flatImages/<?= htmlspecialchars($photo['photo_path']) ?>" alt="Flat photo">
        <?php endforeach; ?>
    </section>

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
    <div class="rent-buttons">
        <a href="rent.php?id=<?= $flat_id ?>">Rent this Flat</a>
        <a href="requestAppointment.php?id=<?= $flat_id ?>">Request Appointment</a>
    </div>

</main>

<?php include 'footer.php'; ?>
</body>
</html>
