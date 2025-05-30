<?php
// Determine the current page to set active link
$current_page = basename($_SERVER['PHP_SELF']);
?>

<nav class="site-nav">

    <ul>
        <li><a href="main.php" class="<?php echo $current_page === 'main.php' ? 'active' : ''; ?>">Home</a></li>
        <li><a href="flatSearch.php" class="<?php echo $current_page === 'flatSearch.php' ? 'active' : ''; ?>">Flat Search</a></li>
        <li><a href="viewMessages.php" class="<?php echo $current_page === 'viewMessages.php' ? 'active' : ''; ?>">View Messages</a></li>
        <li><a href="aboutUs.php" class="<?php echo $current_page === 'aboutUs.php' ? 'active' : ''; ?>">About Us</a></li>
        <li><a href="index.php" class="<?php echo $current_page === 'register.php' ? 'active' : ''; ?>">Register</a></li>
        <li><a href="login.php" class="<?php echo $current_page === 'login.php' ? 'active' : ''; ?>">Login</a></li>
    </ul>

</nav>