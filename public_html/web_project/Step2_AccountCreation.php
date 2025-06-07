<?php
session_start();

// Redirect if step1 not completed
if (!isset($_SESSION['step1'])) {
    header("Location: Step1_Registration.php");
    exit;
}

$error = '';
$email = $_SESSION['step2']['email'] ?? $_SESSION['step1']['email'] ?? '';
$password = $_SESSION['step2']['password'] ?? '';
$confirm_password = $_SESSION['step2']['confirm_password'] ?? '';

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '2') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $pattern = '/^[0-9].{4,13}[a-z]$/';

    if (!preg_match($pattern, $password)) {
        $error = "Password must be 6–15 characters, start with a digit, and end with a lowercase letter";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $_SESSION['step2'] = [
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirm_password
        ];
        header("Location: Step3_ReviewAndConfirm.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Step 2</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">

    <main class="site-main">

        <section class="registration-container">

            <nav class="progress-steps">
                <span class="step completed">✓</span>
                <span class="step active">2</span>
                <span class="step">3</span>
            </nav>

            <header>
                <h1>Create Your E-Account</h1>
            </header>


            <?php if (!empty($error)): ?>
                <article class="error-message"><?= htmlspecialchars($error) ?></article>
            <?php endif; ?>

            <!-- Password Form -->

            <form action="Step2_AccountCreation.php" method="POST" class="registration-form">

                <input type="hidden" name="step" value="2">

                <fieldset class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="form-input" required
                           value="<?= htmlspecialchars($email) ?>">
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

                    <button type="button" class="backButton" onclick="window.location.href='Step1_Registration.php'">←
                        Back
                    </button>
                    <button type="submit" class="nextButton">Next Step →</button>
                </section>
            </form>
        </section>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>