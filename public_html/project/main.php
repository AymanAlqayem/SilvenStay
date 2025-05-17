<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birzeit Flat Rent</title>
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
            <li><a href="main.php" class="active">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="search.php">Search Flats</a></li>
            <li><a href="register_customer.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <main>
        <h2>Welcome to Birzeit Flat Rent</h2>
        <p>Explore our latest flats for rent!</p>
        <figure>
            <img src="images/flat1.jpg" alt="New Flat">
            <figcaption>Newly added flat in Ramallah</figcaption>
        </figure>
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