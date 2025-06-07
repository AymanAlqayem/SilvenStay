<?php
session_start();
require_once 'dbconfig.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    die("Only owners can view their flats.");
}

$userId = $_SESSION['user_id'];
$errors = [];

try {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("
        SELECT f.flat_id, f.reference_number, f.location, f.address, f.monthly_rent, f.bedrooms, f.bathrooms, f.size_sqm, 
               f.is_furnished, f.status, MIN(p.photo_path) as first_photo
        FROM flats f
        LEFT JOIN flat_photos p ON f.flat_id = p.flat_id
        WHERE f.owner_id = :owner_id
        GROUP BY f.flat_id
        ORDER BY f.flat_id DESC
    ");
    $stmt->execute(['owner_id' => $userId]);
    $flats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Database error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Flats | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .content-wrapper { padding: 20px; }
        .site-main { max-width: 1200px; margin: 0 auto; }
        h2 { margin-bottom: 1em; }
        .error { color: red; margin-bottom: 1em; }
        .flats-table { width: 100%; border-collapse: collapse; margin-bottom: 2em; }
        .flats-table th, .flats-table td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        .flats-table th { background-color: #f4f4f4; font-weight: bold; }
        .flats-table tr:nth-child(even) { background-color: #f9f9f9; }
        .flats-table tr:nth-child(odd) { background-color: #ffffff; }
        .flats-table img { max-width: 100px; height: auto; }
        .status-pending { color: goldenrod; }
        .status-approved { color: green; }
        .status-rented { color: blue; }
        .status-rejected { color: red; }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <h2>My Flats</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (empty($flats)): ?>
            <p>No flats found. <a href="offerFlat.php">Add a new flat</a>.</p>
        <?php else: ?>
            <table class="flats-table">
                <thead>
                <tr>
                    <th>Reference Number</th>
                    <th>Location</th>
                    <th>Address</th>
                    <th>Monthly Rent ($)</th>
                    <th>Bedrooms</th>
                    <th>Bathrooms</th>
                    <th>Size (sqm)</th>
                    <th>Furnished</th>
                    <th>Status</th>
                    <th>Photo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($flats as $flat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flat['reference_number']); ?></td>
                        <td><?php echo htmlspecialchars($flat['location']); ?></td>
                        <td><?php echo htmlspecialchars($flat['address']); ?></td>
                        <td><?php echo number_format($flat['monthly_rent'], 2); ?></td>
                        <td><?php echo htmlspecialchars($flat['bedrooms']); ?></td>
                        <td><?php echo htmlspecialchars($flat['bathrooms']); ?></td>
                        <td><?php echo htmlspecialchars($flat['size_sqm']); ?></td>
                        <td><?php echo $flat['is_furnished'] ? 'Yes' : 'No'; ?></td>
                        <td class="status-<?php echo strtolower($flat['status']); ?>">
                            <?php echo htmlspecialchars(ucfirst($flat['status'])); ?>
                        </td>
                        <td>
                            <?php if ($flat['first_photo']): ?>
                                <?php
                                $photo_path = 'flatImages/' . $flat['first_photo'] . '.jpg';
                                if (!file_exists($photo_path)) {
                                    $photo_path = 'flatImages/' . $flat['first_photo'] . '.png';
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Flat Photo">
                            <?php else: ?>
                                No Photo
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>