<?php
session_start();
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

<div class="content-wrapper">
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

            <form action="AccountCreation.php" method="POST" class="registration-form">
                <input type="hidden" name="step" value="1">

                <fieldset class="form-group">
                    <label for="national_id" class="form-label required">National ID</label>
                    <input type="number" id="national_id" name="national_id" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['national_id'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="name" class="form-label required">Full Name</label>
                    <input type="text" id="name" name="name" class="form-input" required pattern="[A-Za-z ]+"
                           title="Only alphabetic characters and spaces allowed"
                           value="<?= htmlspecialchars($_SESSION['step1']['name'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="flat_no" class="form-label required">Flat/House No</label>
                    <input type="text" id="flat_no" name="flat_no" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['flat_no'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="street" class="form-label required">Street Name</label>
                    <input type="text" id="street" name="street" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['street'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="city" class="form-label required">City</label>
                    <input type="text" id="city" name="city" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['city'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="postal_code" class="form-label required">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['postal_code'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="dob" class="form-label required">Date of Birth</label>
                    <input type="date" id="dob" name="dob" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['dob'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" id="email" name="email" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['email'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="mobile" class="form-label required">Mobile Number</label>
                    <input type="number" id="mobile" name="mobile" class="form-input" required
                           value="<?= htmlspecialchars($_SESSION['step1']['mobile'] ?? '') ?>">
                </fieldset>

                <fieldset class="form-group">
                    <label for="telephone" class="form-label">Telephone Number</label>
                    <input type="tel" id="telephone" name="telephone" class="form-input"
                           value="<?= htmlspecialchars($_SESSION['step1']['telephone'] ?? '') ?>">
                </fieldset>

                <section class="form-actions">
                    <button type="submit" class="btn btn-next">Next Step →</button>
                </section>
            </form>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>