<?php
// Determine the current page to set active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="site-nav">
    <ul>
        <li><a href="main.php" class="<?php echo $current_page === 'main.php' ? 'active' : ''; ?>">Home</a></li>
        <li><a href="#">Flat Search</a></li>
        <li><a href="#">View Messages</a></li>
        <li><a href="aboutUs.php">About Us</a></li>
        <li><a href="#">Register</a></li>
        <li><a href="#">Login</a></li>
    </ul>
</nav>