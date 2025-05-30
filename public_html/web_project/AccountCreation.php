<?php
session_start();

$error = '';
$username = '';
$password = '';
$confirm_password = '';

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Save username and passwords temporarily for repopulating fields
    $_SESSION['step2']['username'] = $username;
    $_SESSION['step2']['password'] = $password;
    $_SESSION['step2']['confirm_password'] = $confirm_password;

    $pattern = '/^[0-9].{4,13}[a-z]$/';

    if (!preg_match($pattern, $password)) {
        // Do nothing; just stay silently on the page (no error shown for this case)
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Valid case
        $_SESSION['step2']['password'] = $password;
        $_SESSION['step2']['confirm_password'] = $confirm_password;
        header("Location: ReviewAndConfirm.php");
        exit;
    }
}

// If coming in fresh, try to fill the username with step1 email
if (empty($_SESSION['step2']['username']) && isset($_SESSION['step1']['email'])) {
    $username = $_SESSION['step1']['email'];
} else {
    $username = $_SESSION['step2']['username'] ?? '';
}

$password = $_SESSION['step2']['password'] ?? '';
$confirm_password = $_SESSION['step2']['confirm_password'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Step 2</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<div class="content-wrapper">
    <main class="site-main">
        <section class="registration-container">
            <nav class="progress-steps" aria-label="Registration progress">
                <span class="step completed">✓</span>
                <span class="step active">2</span>
                <span class="step">3</span>
            </nav>

            <header>
                <h1>Create Your E-Account</h1>
            </header>

            <form action="AccountCreation.php" method="POST" class="registration-form">
                <input type="hidden" name="step" value="2">

                <?php if (!empty($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <fieldset class="form-group">
                    <label for="username" class="form-label required">Username (Email)</label>
                    <input type="email" id="username" name="username" class="form-input" required
                           value="<?= htmlspecialchars($username) ?>">
                    <small class="form-hint">This will be your login ID</small>
                </fieldset>

                <fieldset class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input type="password" id="password" name="password" class="form-input" required
                           pattern="^[0-9].{4,13}[a-z]$"
                           title="Password must be 6–15 characters, start with digit, and end with lowercase letter"
                           value="<?= htmlspecialchars($password) ?>">
                    <small class="form-hint">6–15 characters, start with digit, end with lowercase letter</small>
                </fieldset>

                <fieldset class="form-group">
                    <label for="confirm_password" class="form-label required">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-input" required
                           value="<?= htmlspecialchars($confirm_password) ?>">
                </fieldset>

                <section class="form-actions">
                    <button type="button" class="btn btn-back" onclick="window.location.href='index.php'">← Back</button>
                    <button type="submit" class="btn btn-next">Next Step →</button>
                </section>
            </form>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>