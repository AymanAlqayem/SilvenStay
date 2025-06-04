<?php
// Get the current page's filename
$current_page = basename($_SERVER['PHP_SELF']);
// Get the URL fragment (e.g., 'search' from products.php#search)
$fragment = parse_url($_SERVER['REQUEST_URI'], PHP_URL_FRAGMENT);
?>

<nav class="main-nav">
    <ul>
        <li><a href="products.php" class="<?php echo $current_page === 'products.php' && !$fragment ? 'active' : ''; ?>"
               data-emoji="🏠">Home</a></li>
        <li><a href="add.php" class="<?php echo $current_page === 'add.php' ? 'active' : ''; ?>" data-emoji="➕">Add
                Product</a></li>
        <li><a href="products.php#search"
               class="<?php echo $current_page === 'products.php' && $fragment === 'search' ? 'active' : ''; ?>"
               data-emoji="🔍">Search</a></li>
        <li><a href="contactUs.php" class="<?php echo $current_page === 'contactUs.php' ? 'active' : ''; ?>"
               data-emoji="📞">Contact Us</a></li>
        <li><a href="cart.php" class="<?php echo $current_page === 'cart.php' ? 'active' : ''; ?>"
               data-emoji="🛒">Cart</a></li>
    </ul>
</nav>