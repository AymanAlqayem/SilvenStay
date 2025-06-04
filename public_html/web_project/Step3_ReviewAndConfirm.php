<?php
session_start();

// Redirect if previous steps not completed
if (!isset($_SESSION['step1']) || !isset($_SESSION['step2'])) {
    header("Location: Step1_Registration.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '3') {
    // Verify passwords match (should already be validated)
    if ($_SESSION['step2']['password'] !== $_SESSION['step2']['confirm_password']) {
        die("Error: Passwords do not match");
    }

    // Generate user ID
    $user_type = $_SESSION['user_type'] ?? 'Customer';
    $id_prefix = $user_type === 'Owner' ? 'O' : 'C';
    $random_id = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT); // 8 digits + prefix = 9 chars
    $user_id = $id_prefix . $random_id;

    try {
        // Database connection - replace with your credentials
        $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL statement
        $stmt = $pdo->prepare("INSERT INTO users (
            user_id, user_type, email, password, name, national_id,
            flat_no, street, city, postal_code, dob, mobile, telephone,
            bank_name, bank_branch, bank_account, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        // Execute with all parameters
        $stmt->execute([
            $user_id,
            $user_type,
            $_SESSION['step2']['email'],
            password_hash($_SESSION['step2']['password'], PASSWORD_DEFAULT),
            $_SESSION['step1']['name'],
            $_SESSION['step1']['national_id'],
            $_SESSION['step1']['flat_no'],
            $_SESSION['step1']['street'],
            $_SESSION['step1']['city'],
            $_SESSION['step1']['postal_code'],
            $_SESSION['step1']['dob'],
            $_SESSION['step1']['mobile'],
            $_SESSION['step1']['telephone'] ?? null,
            $_SESSION['step1']['bank_name'] ?? null,
            $_SESSION['step1']['bank_branch'] ?? null,
            $_SESSION['step1']['bank_account'] ?? null
        ]);

        // Store user ID in session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['is_registered'] = true;

        // Redirect to success page
        header("Location: registration_success.php");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
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

<section class="content-wrapper">
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
                    <section class="review-group">
                        <section class="review-item">
                            <span class="review-icon">👤</span>
                            <div class="review-content">
                                <dt>User Type</dt>
                                <dd><?= htmlspecialchars($_SESSION['user_type']) ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">🪪</span>
                            <div class="review-content">
                                <dt>National ID</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['national_id']) ?></dd>
                            </div>
                        </section>
                        <div class="review-item">
                            <span class="review-icon">👤</span>
                            <div class="review-content">
                                <dt>Full Name</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['name']) ?></dd>
                            </div>
                        </div>
                        <section class="review-item">
                            <span class="review-icon">🏠</span>
                            <div class="review-content">
                                <dt>Address</dt>
                                <dd>
                                    <?= htmlspecialchars($_SESSION['step1']['flat_no']) ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['street']) ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['city']) ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['postal_code']) ?>
                                </dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">🎂</span>
                            <div class="review-content">
                                <dt>Date of Birth</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['dob']) ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">📧</span>
                            <div class="review-content">
                                <dt>Email</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['email']) ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">📱</span>
                            <div class="review-content">
                                <dt>Mobile</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['mobile']) ?></dd>
                            </div>
                        </section>
                        <?php if (!empty($_SESSION['step1']['telephone'])): ?>
                            <section class="review-item">
                                <span class="review-icon">☎️</span>
                                <div class="review-content">
                                    <dt>Telephone</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['telephone']) ?></dd>
                                </div>
                            </section>
                        <?php endif; ?>
                    </section>
                </section>

                <?php if ($_SESSION['user_type'] === 'Owner'): ?>
                    <section class="form-section">
                        <h2 class="form-section-title">Bank Details</h2>
                        <section class="review-group">
                            <section class="review-item">
                                <span class="review-icon">🏦</span>
                                <div class="review-content">
                                    <dt>Bank Name</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_name']) ?></dd>
                                </div>
                            </section>
                            <section class="review-item">
                                <span class="review-icon">🏦</span>
                                <div class="review-content">
                                    <dt>Bank Branch</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_branch']) ?></dd>
                                </div>
                            </section>
                            <section class="review-item">
                                <span class="review-icon">💳</span>
                                <div class="review-content">
                                    <dt>Account Number</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_account']) ?></dd>
                                </div>
                            </section>
                        </section>
                    </section>
                <?php endif; ?>

                <section class="form-section">
                    <h2 class="form-section-title">Account Details</h2>
                    <section class="review-group">
                        <section class="review-item">
                            <span class="review-icon">📧</span>
                            <div class="review-content">
                                <dt>Login Email</dt>
                                <dd><?= htmlspecialchars($_SESSION['step2']['email']) ?></dd>
                            </div>
                        </section>
                    </section>
                </section>

                <section class="form-actions">
                    <button type="button" class="btn btn-back"
                            onclick="window.location.href='Step2_AccountCreation.php'">← Back
                    </button>
                    <button type="submit" class="btn btn-confirm">Confirm Registration</button>
                </section>
            </form>
        </section>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>