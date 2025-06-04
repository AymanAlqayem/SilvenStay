<?php
// Initialize toggle state for profile card
$show_card = isset($_POST['toggle_card']) && $_POST['toggle_card'] === 'show' ? 'show' : '';
// Get username from session if available, else default to 'Guest'
$username = isset($_SESSION['step1']['name']) && !empty($_SESSION['step1']['name']) ? htmlspecialchars($_SESSION['step1']['name']) : 'Guest';
?>

<header class="site-header">
    <section class="logo-section">
        <img src="images/logo.png" alt="SilvenStay Logo" class="logo">
        <h1>SilvenStay For Flat Rent.</h1>
    </section>

    <section class="header-links">
        <section class="auth-links">
            <a href="contact-us.php" class="auth-link">Contact Us</a>
            <a href="Step1_Registration.php" class="auth-link">Register</a>
            <a href="login.php" class="auth-link">Login</a>
            <a href="logout.php" class="auth-link">Logout</a>
        </section>

        <?php if (isset($_SESSION['is_registered']) && $_SESSION['is_registered'] === true): ?>
            <section class="basket-link">
                <button>
                    <img src="images/basket.png" alt="basket image" class="basket-image">
                </button>
            </section>
        <?php endif; ?>

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
    </section>
</header>