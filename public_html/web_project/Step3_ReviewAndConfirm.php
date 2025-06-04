<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '3') {
    if ($_SESSION['step2']['password'] !== $_SESSION['step2']['confirm_password']) {
        echo "<p style='color:red;'>Error: Passwords do not match.</p>";
        echo "<a href='Step2_AccountCreation.php'>← Back</a>";
        exit;
    }

    $customerID = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    $_SESSION['customer_id'] = $customerID;
    $_SESSION['is_registered'] = true;

    // Ensure session data is written before redirect
    session_write_close();
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Step 3</title>
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
                <span class="step completed">✓</span>
                <span class="step active">3</span>
            </nav>

            <header>
                <h1>Review Your Information</h1>
                <p>Please verify all details before submission</p>
            </header>

            <form action="Step3_ReviewAndConfirm.php" method="POST" class="registration-form">
                <input type="hidden" name="step" value="3">

                <section class="form-section">
                    <h2 class="form-section-title">Personal Details</h2>
                    <div class="review-group">
                        <div class="review-item">
                            <span class="review-icon">🪪</span>
                            <div class="review-content">
                                <dt>National ID</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['national_id'] ?? '') ?></dd>
                            </div>
                        </div>
                        <div class="review-item">
                            <span class="review-icon">👤</span>
                            <div class="review-content">
                                <dt>Full Name</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['name'] ?? '') ?></dd>
                            </div>
                        </div>
                        <div class="review-item">
                            <span class="review-icon">🏠</span>
                            <div class="review-content">
                                <dt>Address</dt>
                                <dd><?= htmlspecialchars(($_SESSION['step1']['flat_no'] ?? '') . ', ' . ($_SESSION['step1']['street'] ?? '') . ', ' . ($_SESSION['step1']['city'] ?? '') . ', ' . ($_SESSION['step1']['postal_code'] ?? '')) ?></dd>
                            </div>
                        </div>
                        <div class="review-item">
                            <span class="review-icon">🎂</span>
                            <div class="review-content">
                                <dt>Date of Birth</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['dob'] ?? '') ?></dd>
                            </div>
                        </div>
                        <div class="review-item">
                            <span class="review-icon">📧</span>
                            <div class="review-content">
                                <dt>Email</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['email'] ?? '') ?></dd>
                            </div>
                        </div>
                        <div class="review-item">
                            <span class="review-icon">📱</span>
                            <div class="review-content">
                                <dt>Mobile</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['mobile'] ?? '') ?></dd>
                            </div>
                        </div>
                        <?php if (!empty($_SESSION['step1']['telephone'])): ?>
                            <div class="review-item">
                                <span class="review-icon">☎️</span>
                                <div class="review-content">
                                    <dt>Telephone</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['telephone']) ?></dd>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section-title">Account Details</h2>
                    <div class="review-group">
                        <div class="review-item">
                            <span class="review-icon">📧</span>
                            <div class="review-content">
                                <dt>Email</dt>
                                <dd><?= htmlspecialchars($_SESSION['step2']['email'] ?? '') ?></dd>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="form-actions">
                    <button type="button" class="btn btn-back" onclick="window.location.href='Step2_AccountCreation.php'">←
                        Back
                    </button>
                    <button type="submit" class="btn btn-confirm">Confirm Registration</button>
                </section>
            </form>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>