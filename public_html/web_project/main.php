<?php
session_start();
require_once 'dbconfig.inc.php';

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9; // 9 flats per page
$offset = ($page - 1) * $limit;

$pdo = getPDOConnection();

// Get total count of approved flats
$totalStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM flats WHERE status = 'approved'");
$totalStmt->execute();
$totalFlats = (int)$totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalFlats / $limit);

// Fetch flats with description and first photo
$sql = "
    SELECT f.flat_id, f.location, f.address, f.monthly_rent, f.bedrooms, f.bathrooms, f.size_sqm,
           d.title, d.summary,
           (SELECT photo_path FROM flat_photos WHERE flat_id = f.flat_id ORDER BY photo_id ASC LIMIT 1) AS photo
    FROM flats f
    JOIN flat_descriptions d ON f.flat_id = d.flat_id
    WHERE f.status = 'approved'
    ORDER BY f.flat_id ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$flats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>SilvenStay For Flat Rent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="styles.css"/>
</head>
<body>

<?php include 'header.php'; ?>

<div class="content-wrapper">
    <?php include 'nav.php'; ?>

    <main class="site-main">
        <?php if (isset($_SESSION['message'])): ?>
            <section class="message">
                <p><?= htmlspecialchars($_SESSION['message']) ?></p>
                <?php unset($_SESSION['message']); ?>
            </section>
        <?php endif; ?>

        <section class="promotional">
            <h2>Available Flats</h2>

            <article class="promotion-grid">
                <?php if (count($flats) === 0): ?>
                    <p>No flats found.</p>
                <?php else: ?>
                    <?php foreach ($flats as $flat): ?>
                        <a href="flatDetails.php?id=<?= urlencode($flat['flat_id']) ?>" class="promotion-card">
                            <img src="flatImages/<?= htmlspecialchars($flat['photo'] ?? 'default.jpg') ?>"
                                 alt="Flat Image" loading="lazy"/>
                            <figcaption>
                                <h3><?= htmlspecialchars($flat['title']) ?></h3>
                                <p><?= htmlspecialchars($flat['summary']) ?></p>
                                <p><strong>$<?= number_format($flat['monthly_rent'], 2) ?>/mo</strong></p>
                                <small>
                                    <?= (int)$flat['bedrooms'] ?> BR &bull;
                                    <?= (int)$flat['bathrooms'] ?> BA &bull;
                                    <?= (int)$flat['size_sqm'] ?> sqm &bull;
                                    <?= htmlspecialchars($flat['location']) ?>
                                </small>
                            </figcaption>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </article>

            <div class="pagination" aria-label="Pagination Navigation">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="btn" aria-label="Previous Page">Previous</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="btn" aria-label="Next Page">Next</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
