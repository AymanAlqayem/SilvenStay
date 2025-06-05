<?php
session_start();

require_once 'dbconfig.inc.php';

// Redirect if previous steps not completed
if (!isset($_SESSION['step1']) || !isset($_SESSION['step2'])) {
    header("Location: Step1_Registration.php");
    exit;
}

// Initialize confirmation message
$confirmation_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '3') {
    // Verify passwords match
    if ($_SESSION['step2']['password'] !== $_SESSION['step2']['confirm_password']) {
        die("Error: Passwords do not match");
    }

    try {
        $pdo = getPDOConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Generate 9-digit ID (strictly numeric)
        $random_id = str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        $generated_id = $random_id;

        // Set role-specific ID based on user type
        $user_type = strtolower($_SESSION['user_type'] ?? 'customer');
        $customer_id = $user_type === 'customer' ? $generated_id : null;
        $owner_id = $user_type === 'owner' ? $generated_id : null;
        $manager_id = $user_type === 'manager' ? $generated_id : null;

        $stmt = $pdo->prepare("
            INSERT INTO users (
                national_id, name, flat_no, street, city, postal_code, 
                date_of_birth, email, mobile_number, telephone_number,
                bank_name, bank_branch, account_number,
                password, user_type,
                customer_id, owner_id, manager_id, profile_photo
            ) VALUES (
                :national_id, :name, :flat_no, :street, :city, :postal_code, 
                :date_of_birth, :email, :mobile_number, :telephone_number,
                :bank_name, :bank_branch, :account_number,
                :password, :user_type,
                :customer_id, :owner_id, :manager_id, :profile_photo
            )
        ");

        $stmt->execute([
            ':national_id' => $_SESSION['step1']['national_id'],
            ':name' => $_SESSION['step1']['name'],
            ':flat_no' => $_SESSION['step1']['flat_no'] ?? null,
            ':street' => $_SESSION['step1']['street'] ?? null,
            ':city' => $_SESSION['step1']['city'] ?? null,
            ':postal_code' => $_SESSION['step1']['postal_code'] ?? null,
            ':date_of_birth' => $_SESSION['step1']['dob'] ?? null,
            ':email' => $_SESSION['step2']['email'],
            ':mobile_number' => $_SESSION['step1']['mobile'] ?? null,
            ':telephone_number' => $_SESSION['step1']['telephone'] ?? null,
            ':bank_name' => $_SESSION['step1']['bank_name'] ?? null,
            ':bank_branch' => $_SESSION['step1']['bank_branch'] ?? null,
            ':account_number' => $_SESSION['step1']['bank_account'] ?? null,
            ':password' => password_hash($_SESSION['step2']['password'], PASSWORD_DEFAULT),
            ':user_type' => $user_type,
            ':customer_id' => $customer_id,
            ':owner_id' => $owner_id,
            ':manager_id' => $manager_id,
            ':profile_photo' => null
        ]);

        // Store confirmation details in session for use in registration_success.php
        $_SESSION['registration_success'] = [
            'name' => $_SESSION['step1']['name'],
            'user_type' => $user_type,
            'user_id' => $generated_id
        ];

        // Clear session data after successful registration
        unset($_SESSION['step1']);
        unset($_SESSION['step2']);
        unset($_SESSION['user_type']);

        // Redirect to registration_success.php
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
                                <dd><?= htmlspecialchars($_SESSION['user_type'] ?? 'Customer') ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">🪪</span>
                            <div class="review-content">
                                <dt>National ID</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['national_id'] ?? '') ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">👤</span>
                            <div class="review-content">
                                <dt>Full Name</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['name'] ?? '') ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">🏠</span>
                            <div class="review-content">
                                <dt>Address</dt>
                                <dd>
                                    <?= htmlspecialchars($_SESSION['step1']['flat_no'] ?? '') ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['street'] ?? '') ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['city'] ?? '') ?>,
                                    <?= htmlspecialchars($_SESSION['step1']['postal_code'] ?? '') ?>
                                </dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">🎂</span>
                            <div class="review-content">
                                <dt>Date of Birth</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['dob'] ?? '') ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">📧</span>
                            <div class="review-content">
                                <dt>Email (for login)</dt>
                                <dd><?= htmlspecialchars($_SESSION['step2']['email'] ?? '') ?></dd>
                            </div>
                        </section>
                        <section class="review-item">
                            <span class="review-icon">📱</span>
                            <div class="review-content">
                                <dt>Mobile</dt>
                                <dd><?= htmlspecialchars($_SESSION['step1']['mobile'] ?? '') ?></dd>
                            </div>
                        </section>
                        <?php if (!empty($_SESSION['step1']['telephone'])): ?>
                            <section class="review-item">
                                <span class="review-icon">☎️</span>
                                <div class="review-content">
                                    <dt>Telephone</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['telephone'] ?? '') ?></dd>
                                </div>
                            </section>
                        <?php endif; ?>
                    </section>
                </section>

                <?php if (strtolower($_SESSION['user_type'] ?? 'customer') === 'owner'): ?>
                    <section class="form-section">
                        <h2 class="form-section-title">Bank Details</h2>
                        <section class="review-group">
                            <section class="review-item">
                                <span class="review-icon">🏦</span>
                                <div class="review-content">
                                    <dt>Bank Name</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_name'] ?? '') ?></dd>
                                </div>
                            </section>
                            <section class="review-item">
                                <span class="review-icon">🏦</span>
                                <div class="review-content">
                                    <dt>Bank Branch</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_branch'] ?? '') ?></dd>
                                </div>
                            </section>
                            <section class="review-item">
                                <span class="review-icon">💳</span>
                                <div class="review-content">
                                    <dt>Account Number</dt>
                                    <dd><?= htmlspecialchars($_SESSION['step1']['bank_account'] ?? '') ?></dd>
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
                                <dd><?= htmlspecialchars($_SESSION['step2']['email'] ?? '') ?></dd>
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