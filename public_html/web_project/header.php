<?php
// Initialize toggle state for profile card
$show_card = isset($_POST['toggle_card']) && $_POST['toggle_card'] === 'show' ? 'show' : '';
// Get username from session if available, else default to 'Guest'
$username = isset($_SESSION['step1']['name']) && !empty($_SESSION['step1']['name']) ? htmlspecialchars($_SESSION['step1']['name']) :
    (isset($_SESSION['user_id']) && isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true ? htmlspecialchars($_SESSION['user_id']) : 'Guest');
// Determine user role from session, default to 'guest' if not set
$user_role = isset($_SESSION['user_type']) ? strtolower($_SESSION['user_type']) :
    (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true ? 'customer' : 'guest');

// Define header links based on user role
$header_links = [
    'guest' => [
        ['text' => 'Contact Us', 'href' => 'contact-us.php'],
        ['text' => 'Register', 'href' => 'Step1_Registration.php'],
        ['text' => 'Login', 'href' => 'login.php']
    ],
    'customer' => [
        ['text' => 'Contact Us', 'href' => 'contact-us.php'],
        ['text' => 'Login', 'href' => 'login.php'],
        ['text' => 'Logout', 'href' => 'logout.php']
    ],
    'owner' => [
        ['text' => 'Contact Us', 'href' => 'contact-us.php'],
        ['text' => 'Login', 'href' => 'login.php'],
        ['text' => 'Logout', 'href' => 'logout.php']
    ],
    'manager' => [
        ['text' => 'Login', 'href' => 'login.php'],
        ['text' => 'Logout', 'href' => 'logout.php']
    ]
];
?>

<header class="site-header">
    <section class="logo-section">
        <img src="images/logo.png" alt="SilvenStay Logo" class="logo">
        <h1>SilvenStay For Flat Rent.</h1>
    </section>

    <section class="header-links">
        <section class="auth-links">
            <?php foreach ($header_links[$user_role] as $link): ?>
                <a href="<?php echo $link['href']; ?>" class="auth-link"><?php echo $link['text']; ?></a>
            <?php endforeach; ?>
        </section>

        <?php if (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'customer'): ?>
            <section class="basket-link">
                <a href="basket.php">
                    <img src="images/basket.png" alt="basket image" class="basket-image">
                </a>
            </section>
        <?php endif; ?>

        <?php if (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true): ?>
            <section class="user-card">
                <form method="POST" class="profile-form">
                    <input type="hidden" name="toggle_card" value="<?php echo $show_card === 'show' ? '' : 'show'; ?>">
                    <button type="submit" class="profile-icon-button">
                        <img src="images/profileIcon.png" alt="Profile Icon" class="profile-icon">
                    </button>
                </form>

                <section class="user-card-info <?php echo $show_card; ?>">
                    <img src="images/profileIcon.png" alt="User Photo" class="user-photo">
                    <span class="username"><?php echo $username; ?></span>
                    <a href="profile.php" class="profile-link">View Profile</a>
                </section>
            </section>
        <?php endif; ?>
    </section>
</header>