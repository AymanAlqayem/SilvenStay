<?php
session_start();

// Redirect to main.php if not logged in
if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true) {
    $_SESSION['message'] = "You are not logged in.";
    header("Location: main.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update session data with new values
    $_SESSION['step1'] = [
        'national_id' => $_POST['national_id'] ?? '',
        'name' => $_POST['name'] ?? '',
        'flat_no' => $_POST['flat_no'] ?? '',
        'street' => $_POST['street'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal_code' => $_POST['postal_code'] ?? '',
        'dob' => $_POST['dob'] ?? '',
        'email' => $_POST['email_step1'] ?? '',
        'mobile' => $_POST['mobile'] ?? '',
        'telephone' => $_POST['telephone'] ?? ''
    ];
    $_SESSION['step2'] = [
        'email' => $_POST['email_step2'] ?? '',
        'password' => $_SESSION['step2']['password'], // Preserve existing password
        'confirm_password' => $_SESSION['step2']['confirm_password'] // Preserve existing confirm_password
    ];

    // Set success message
    $_SESSION['message'] = "Profile updated successfully.";
    session_write_close();
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Account Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<div class="content-wrapper">
    <main class="site-main">
        <div class="registration-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= substr(htmlspecialchars($_SESSION['step1']['name'] ?? ''), 0, 1) ?>
                </div>
                <div class="profile-info">
                    <h1><?= htmlspecialchars($_SESSION['step1']['name'] ?? '') ?></h1>
                    <p>Member since <?= date('F Y', strtotime($_SESSION['registration_date'] ?? 'now')) ?></p>
                </div>
            </div>

            <form action="profile.php" method="POST" class="registration-form">
                <section class="form-section">
                    <h2 class="form-section-title">Personal Information</h2>

                    <div class="form-grid">
                        <fieldset class="form-group">
                            <label for="customer_id" class="form-label">Customer ID</label>
                            <div class="input-icon">
                                <input type="text" id="customer_id" name="customer_id" class="form-input" readonly
                                       value="<?= htmlspecialchars($_SESSION['customer_id'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="national_id" class="form-label required">National ID</label>
                            <div class="input-icon">
                                <input type="number" id="national_id" name="national_id" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['national_id'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="name" class="form-label required">Full Name</label>
                            <div class="input-icon">
                                <input type="text" id="name" name="name" class="form-input" required
                                       pattern="[A-Za-z ]+"
                                       title="Only alphabetic characters and spaces allowed"
                                       value="<?= htmlspecialchars($_SESSION['step1']['name'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="dob" class="form-label required">Date of Birth</label>
                            <div class="input-icon">
                                <input type="date" id="dob" name="dob" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['dob'] ?? '') ?>">
                            </div>
                        </fieldset>
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section-title">Address Information</h2>

                    <div class="form-grid">
                        <fieldset class="form-group">
                            <label for="flat_no" class="form-label required">Flat/House No</label>
                            <div class="input-icon">
                                <input type="text" id="flat_no" name="flat_no" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['flat_no'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="street" class="form-label required">Street Name</label>
                            <div class="input-icon">
                                <input type="text" id="street" name="street" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['street'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="city" class="form-label required">City</label>
                            <div class="input-icon">
                                <input type="text" id="city" name="city" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['city'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="postal_code" class="form-label required">Postal Code</label>
                            <div class="input-icon">
                                <input type="text" id="postal_code" name="postal_code" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['postal_code'] ?? '') ?>">
                            </div>
                        </fieldset>
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section-title">Contact Information</h2>

                    <div class="form-grid">
                        <fieldset class="form-group">
                            <label for="email_step1" class="form-label required">Personal Email</label>
                            <div class="input-icon">
                                <input type="email" id="email_step1" name="email_step1" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['email'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="mobile" class="form-label required">Mobile Number</label>
                            <div class="input-icon">
                                <input type="number" id="mobile" name="mobile" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step1']['mobile'] ?? '') ?>">
                            </div>
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="telephone" class="form-label">Telephone Number</label>
                            <div class="input-icon">
                                <input type="tel" id="telephone" name="telephone" class="form-input"
                                       value="<?= htmlspecialchars($_SESSION['step1']['telephone'] ?? '') ?>">
                            </div>
                        </fieldset>
                    </div>
                </section>

                <section class="form-section">
                    <h2 class="form-section-title">Account Security</h2>

                    <div class="form-grid">
                        <fieldset class="form-group">
                            <label for="email_step2" class="form-label required">Login Email</label>
                            <div class="input-icon">
                                <input type="email" id="email_step2" name="email_step2" class="form-input" required
                                       value="<?= htmlspecialchars($_SESSION['step2']['email'] ?? '') ?>">
                            </div>
                            <small class="form-hint">This is your login ID</small>
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Password</label>
                            <button type="button" class="btn-change-password"
                                    onclick="window.location.href='changePass.php'">Change Password
                            </button>
                        </fieldset>
                    </div>
                </section>

                <section class="form-actions">
                    <button type="button" class="btn btn-back" onclick="window.location.href='main.php'">Cancel</button>
                    <button type="submit" class="btn btn-confirm">Save Changes</button>
                </section>
            </form>
        </div>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>