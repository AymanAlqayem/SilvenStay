<?php
session_start();
require_once 'database.inc.php';

$flat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$flat = null;
$photos = [];
$marketing = [];

try {
    $stmt = $pdo->prepare("SELECT * FROM flats WHERE flat_id = :flat_id AND status = 'approved'");
    $stmt->execute(['flat_id' => $flat_id]);
    $flat = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($flat) {
        $stmt = $pdo->prepare("SELECT * FROM flat_photos WHERE flat_id = :flat_id");
        $stmt->execute(['flat_id' => $flat_id]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM flat_marketing WHERE flat_id = :flat_id");
        $stmt->execute(['flat_id' => $flat_id]);
        $marketing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flat Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Birzeit Flat Rent</h1>
        <img src="images/logo.png" alt="Logo" style="height: 50px;">
        <div>
            <a href="about.php">About Us</a> |
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a> |
                <?php if ($_SESSION['user_type'] == 'customer'): ?>
                    <a href="basket.php">Basket</a> |
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> |
                <a href="register_customer.php">Register</a>
            <?php endif; ?>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="../../../../../Silvenstay/Project/main.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="search.php">Search Flats</a></li>
            <li><a href="register_customer.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <main>
        <?php if ($flat): ?>
            <h2>Flat <?php echo htmlspecialchars($flat['reference_number']); ?></h2>
            <div class="flatcard">
                <div>
                    <?php foreach ($photos as $photo): ?>
                        <figure>
                            <img src="<?php echo htmlspecialchars($photo['photo_path']); ?>" alt="Flat Photo">
                            <figcaption>Flat Image</figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
                <div>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($flat['address']); ?></p>
                    <p><strong>Price:</strong> $<?php echo number_format($flat['monthly_rent'], 2); ?>/month</p>
                    <p><strong>Rental Conditions:</strong> <?php echo htmlspecialchars($flat['rental_conditions']); ?></p>
                    <p><strong>Bedrooms:</strong> <?php echo htmlspecialchars($flat['bedrooms']); ?></p>
                    <p><strong>Bathrooms:</strong> <?php echo htmlspecialchars($flat['bathrooms']); ?></p>
                    <p><strong>Size:</strong> <?php echo htmlspecialchars($flat['size_sqm']); ?> sqm</p>
                    <p><strong>Heating:</strong> <?php echo $flat['has_heating'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Air Conditioning:</strong> <?php echo $flat['has_ac'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Access Control:</strong> <?php echo $flat['has_access_control'] ? 'Yes' : 'No'; ?></p>
                    <p><strong>Features:</strong>
                        <?php
                        $features = [];
                        if ($flat['has_parking']) $features[] = 'Parking';
                        if ($flat['has_backyard']) $features[] = 'Backyard';
                        if ($flat['has_playground']) $features[] = 'Playground';
                        if ($flat['has_storage']) $features[] = 'Storage';
                        echo implode(', ', $features);
                        ?>
                    </p>
                    <nav>
                        <ul>
                            <li><a href="appointment.php?flat_id=<?php echo $flat['flat_id']; ?>">Request Viewing</a></li>
                            <li><a href="rent.php?flat_id=<?php echo $flat['flat_id']; ?>">Rent Flat</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <aside>
                <h3>Nearby Information</h3>
                <?php foreach ($marketing as $item): ?>
                    <p><strong><?php echo htmlspecialchars($item['title']); ?>:</strong>
                        <?php echo htmlspecialchars($item['description']); ?>
                        <?php if ($item['url']): ?>
                            <a href="<?php echo htmlspecialchars($item['url']); ?>" class="external" target="_blank">More Info</a>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </aside>
        <?php else: ?>
            <p>Flat not found or not available.</p>
        <?php endif; ?>
    </main>
    <footer>
        <img src="images/logo_small.png" alt="Small Logo" style="height: 30px;">
        <p>&copy; 2025 Birzeit Flat Rent. All rights reserved.</p>
        <p>Contact: info@birzeitflatrent.com | +970-123-456-789</p>
        <p><a href="contact.php">Contact Us</a></p>
    </footer>
</div>
</body>
</html>