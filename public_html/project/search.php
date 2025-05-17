<?php
session_start();
require_once 'database.inc.php';

$flats = [];
try {
    $query = "SELECT * FROM flats WHERE status = 'approved' AND (available_to IS NULL OR available_to >= CURDATE()) ORDER BY monthly_rent ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $flats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Flats</title>
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
            <li><a href="search.php" class="active">Search Flats</a></li>
            <li><a href="register_customer.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Search Flats</h2>
            <form method="GET" action="search.php">
                <label for="price" class="required">Max Monthly Rent</label>
                <input type="number" id="price" name="price" min="0">
                <label for="location">Location</label>
                <input type="text" id="location" name="location">
                <label for="bedrooms">Bedrooms</label>
                <select id="bedrooms" name="bedrooms">
                    <option value="">Any</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3+</option>
                </select>
                <label for="bathrooms">Bathrooms</label>
                <select id="bathrooms" name="bathrooms">
                    <option value="">Any</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3+</option>
                </select>
                <label for="furnished">Furnished</label>
                <select id="furnished" name="furnished">
                    <option value="">Any</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
                <button type="submit">Search</button>
            </form>
            <h3>Available Flats</h3>
            <table>
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Rent</th>
                    <th>Available</th>
                    <th>Location</th>
                    <th>Bedrooms</th>
                    <th>Photo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($flats as $flat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($flat['reference_number']); ?></td>
                        <td>$<?php echo number_format($flat['monthly_rent'], 2); ?></td>
                        <td><?php echo htmlspecialchars($flat['available_from']); ?></td>
                        <td><?php echo htmlspecialchars($flat['location']); ?></td>
                        <td><?php echo htmlspecialchars($flat['bedrooms']); ?></td>
                        <td>
                            <a href="flat_detail.php?id=<?php echo $flat['flat_id']; ?>" target="_blank">
                                <img src="images/flat1.jpg" alt="Flat" style="width: 100px;">
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
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