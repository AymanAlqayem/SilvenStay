<?php
session_start();
require_once 'dbconfig.inc.php';

// Check if registration success data is available
if (!isset($_SESSION['registration_success'])) {
    header("Location: Step1_Registration.php");
    exit;
}

// Extract success data
$name = htmlspecialchars($_SESSION['registration_success']['name']);
$user_type = htmlspecialchars($_SESSION['registration_success']['user_type']);
$user_id = htmlspecialchars($_SESSION['registration_success']['user_id']);

// Clear registration success data from session after displaying
unset($_SESSION['registration_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration Success</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <section class="success-container">
            <div class="content">
                <span class="checkmark">✓</span>
                <h1>Welcome Aboard, <?= $name ?>!</h1>
                <p>Your registration was successful! 🎉</p>
                <p>We're excited to have you join our community as a <?= ucfirst($user_type) ?>.
                    Your <?= ucfirst($user_type) ?> ID is: <?= $user_id ?>.</p>
                <p>Please log in to continue your journey with us.</p>
                <form method="GET" action="login.php">
                    <button type="submit" name="continue" class="home-link">Let's Login</button>
                </form>
            </div>
        </section>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>