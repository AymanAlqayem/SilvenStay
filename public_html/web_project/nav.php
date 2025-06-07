<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_role = isset($_SESSION['user_type']) ? strtolower($_SESSION['user_type']) : 'guest';

$nav_links = [
    'guest' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Flat Search', 'href' => 'searchFlats.php'],
        ['text' => 'Contact Us', 'href' => 'contact-us.php']
    ],
    'customer' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Search', 'href' => 'searchFlats.php'],
        ['text' => 'View Rented Flats', 'href' => 'viewRentedFlat.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ],
    'owner' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'My Flats', 'href' => 'viewOwnerFlats.php'],
        ['text' => 'Offer Flat for Rent', 'href' => 'offerFlat.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ],
    'manager' => [
        ['text' => 'Home', 'href' => 'main.php'],
        ['text' => 'About Us', 'href' => 'aboutUs.php'],
        ['text' => 'Flats Inquire', 'href' => 'inquireFlats.php'],
        ['text' => 'View Messages', 'href' => 'messages.php'],
        ['text' => 'Profile', 'href' => 'profile.php']
    ]
];
?>

<nav class="site-nav">
    <ul>
        <?php foreach ($nav_links[$user_role] as $link): ?>
            <li>
                <a href="<?= $link['href']; ?>" class="<?= $current_page === $link['href'] ? 'active' : ''; ?>">
                    <?= $link['text']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
