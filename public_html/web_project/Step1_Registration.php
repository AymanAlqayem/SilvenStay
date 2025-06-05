<?php
session_start();

require_once 'dbconfig.inc.php';

// Initialize form fields
$step1 = $_SESSION['step1'] ?? [];
$user_type = $_SESSION['user_type'] ?? 'Customer';

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] == '1') {
    // Store all form values in session even if validation fails
    $step1 = [
        'national_id' => $_POST['national_id'] ?? '',
        'name' => $_POST['name'] ?? '',
        'flat_no' => $_POST['flat_no'] ?? '',
        'street' => $_POST['street'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'dob' => $_POST['dob'] ?? '',
        'email' => $_POST['email'] ?? '',
        'mobile' => $_POST['mobile'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'bank_name' => $_POST['bank_name'] ?? '',
        'bank_branch' => $_POST['bank_branch'] ?? '',
        'bank_account' => $_POST['bank_account'] ?? ''
    ];
    $_SESSION['step1'] = $step1;
    $_SESSION['user_type'] = $_POST['user_type'] ?? 'Customer';
    $user_type = $_SESSION['user_type'];

    // Validate required fields
    $required_fields = [
        'national_id', 'name', 'flat_no', 'street', 'city',
        'postal_code', 'dob', 'email', 'mobile'
    ];

    $errors = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }

    // Additional validation for owners
    if ($_POST['user_type'] === 'Owner') {
        $owner_fields = ['bank_name', 'bank_branch', 'bank_account'];
        foreach ($owner_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required for owners";
            }
        }
    }

    // Check email uniqueness
    $email = $_POST['email'] ?? '';
    $email_error = '';
    if (!empty($email)) {
        try {
            $pdo = getPDOConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $email_error = "This email is already registered";
                // Clear only the email field in the session
                $_SESSION['step1']['email'] = '';
            }
        } catch (PDOException $e) {
            $email_error = "Database error occurred";
            $_SESSION['step1']['email'] = '';
        }
    }

    if (empty($errors) && empty($email_error)) {
        header("Location: Step2_AccountCreation.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration - Step 1</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <section class="registration-container">
            <nav class="progress-steps" aria-label="Registration progress">
                <span class="step active">1</span>
                <span class="step">2</span>
                <span class="step">3</span>
            </nav>

            <header>
                <h1>Personal Details</h1>
            </header>

            <?php if (!empty($email_error)): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠</span>
                    <span><?= htmlspecialchars($email_error) ?></span>
                    <span class="alert-close" onclick="this.parentElement.style.display='none';">×</span>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="Step1_Registration.php" method="POST" class="registration-form">
                <input type="hidden" name="step" value="1">

                <fieldset class="form-group">
                    <label for="user_type" class="form-label required">User Type</label>
                    <select id="user_type" name="user_type" class="form-input" required onchange="toggleBankDetails()">
                        <option value="Customer" <?= $user_type === 'Customer' ? 'selected' : '' ?>>Customer</option>
                        <option value="Owner" <?= $user_type === 'Owner' ? 'selected' : '' ?>>Owner</option>
                    </select>
                </fieldset>

                <fieldset class="form-group">
                    <label for="national_id" class="form-label required">National ID</label>
                    <input type="text" id="national_id" name="national_id" class="form-input" required
                           value="<?= htmlspecialchars($step1['national_id'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="name" class="form-label required">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" required pattern="[A-Za-z ]+"
                           title="Only alphabetic characters and spaces allowed"
                           value="<?= htmlspecialchars($step1['name'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="flat_no" class="form-label required">Flat/House No</label>
                    <input type="text" id="flat_no" name="flat_no" class="form-input" required
                           value="<?= htmlspecialchars($step1['flat_no'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="street" class="form-label required">Street Name</label>
                    <input type="text" id="street" name="street" class="form-input" required
                           value="<?= htmlspecialchars($step1['street'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="city" class="form-label required">City</label>
                    <input type="text" id="city" name="city" class="form-input" required
                           value="<?= htmlspecialchars($step1['city'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="postal_code" class="form-label required">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input" required
                           value="<?= htmlspecialchars($step1['postal_code'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="dob" class="form-label required">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-input" required
                           value="<?= htmlspecialchars($step1['dob'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="form-input" required
                           value="<?= htmlspecialchars($step1['email'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="mobile" class="form-label required">Mobile Number</label>
                    <input type="number" id="mobile" name="mobile" class="form-input" required
                           value="<?= htmlspecialchars($step1['mobile'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="telephone" class="form-label">Telephone Number</label>
                    <input type="number" id="telephone" name="telephone" class="form-input"
                           value="<?= htmlspecialchars($step1['telephone'] ?? '') ?>">
                </fieldset>

                <section id="bank-details" style="<?= $user_type === 'Owner' ? '' : 'display: none;' ?>">
                    <fieldset class="form-group">
                        <label for="bank_name" class="form-label required">Bank Name</label>
                        <input type="text" id="bank_name" name="bank_name" class="form-input"
                               value="<?= htmlspecialchars($step1['bank_name'] ?? '') ?>"
                            <?= $user_type === 'Owner' ? 'required' : '' ?>>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="bank_branch" class="form-label required">Bank Branch</label>
                        <input type="text" id="bank_branch" name="bank_branch" class="form-input"
                               value="<?= htmlspecialchars($step1['bank_branch'] ?? '') ?>"
                            <?= $user_type === 'Owner' ? 'required' : '' ?>>
                    </fieldset>

                    <fieldset class="form-group">
                        <label for="bank_account" class="form-label required">Bank Account Number</label>
                        <input type="text" id="bank_account" name="bank_account" class="form-input"
                               value="<?= htmlspecialchars($step1['bank_account'] ?? '') ?>"
                            <?= $user_type === 'Owner' ? 'required' : '' ?>>
                    </fieldset>
                </section>

                <section class="form-actions">
                    <button type="submit" class="btn btn-next">Next Step →</button>
                </section>
            </form>
        </section>
    </main>
</section>

<script>
    function toggleBankDetails() {
        const userType = document.getElementById('user_type').value;
        const bankDetails = document.getElementById('bank-details');
        const bankInputs = bankDetails.querySelectorAll('input');

        if (userType === 'Owner') {
            bankDetails.style.display = 'block';
            bankInputs.forEach(input => input.required = true);
        } else {
            bankDetails.style.display = 'none';
            bankInputs.forEach(input => input.required = false);
        }
    }

    // Initialize bank details visibility on page load
    document.addEventListener('DOMContentLoaded', function () {
        toggleBankDetails();
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>