<?php
// Determine the current page to set active link
$current_page = basename($_SERVER['PHP_SELF']);
// Determine user role from session, default to 'guest' if not set
$user_role = isset($_SESSION['user_type']) ? strtolower($_SESSION['user_type']) : 'guest';

// Define navigation links based on user role
$nav_links = [
    'guest' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Flat Search', 'href' => 'flatSearch.php'],
        ['text' => 'Contact Us', 'href' => 'contact-us.php']
    ],
    'customer' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Search', 'href' => 'flatSearch.php'],
        ['text' => 'View Rented Flats', 'href' => 'viewRentedFlat.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ],
    'owner' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'My Flats', 'href' => 'myFlats.php'],
        ['text' => 'Offer Flat for Rent', 'href' => 'offerFlat.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ],
    'manager' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Flats Inquire', 'href' => 'flatsInquire.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ]
];
?>

<nav class="site-nav">
    <ul>
        <?php foreach ($nav_links[$user_role] as $link): ?>
            <li>
                <a href="<?php echo $link['href']; ?>"
                   class="<?php echo $current_page === $link['href'] ? 'active' : ''; ?>">
                    <?php echo $link['text']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>