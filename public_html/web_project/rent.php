<?php
session_start();
require 'auth.php';
require 'dbconfig.inc.php';

$pdo = getPDOConnection();

// Redirect non-logged in users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Only customers can access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'customer') {
    die("Only customers can rent flats.");
}

$flat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($flat_id <= 0) {
    die("Invalid flat ID.");
}

// Fetch flat details
$stmt = $pdo->prepare("
    SELECT f.*, d.title, d.description, u.name AS owner_name, u.owner_id, 
           CONCAT(u.flat_no, ', ', u.street, ', ', u.city) AS owner_address, 
           u.mobile_number AS owner_mobile
    FROM flats f
    LEFT JOIN flat_descriptions d ON f.flat_id = d.flat_id
    LEFT JOIN users u ON f.owner_id = u.user_id
    WHERE f.flat_id = :flat_id
");
$stmt->execute(['flat_id' => $flat_id]);
$flat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$flat) {
    die("Flat not found.");
}

// Check if user already rented this flat
$already_stmt = $pdo->prepare("
    SELECT rental_id FROM rentals
    WHERE flat_id = :flat_id AND customer_id = :customer_id AND status IN ('pending', 'current')
");
$already_stmt->execute([
    'flat_id' => $flat_id,
    'customer_id' => $_SESSION['user_id']
]);
$already_rented = $already_stmt->fetch();

$user_stmt = $pdo->prepare("SELECT name, mobile_number FROM users WHERE user_id = :user_id");
$user_stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = false;
$customer_message = '';
$owner_message = '';

// Only process form if not already rented
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already_rented) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $card_number = $_POST['card_number'] ?? '';
    $expiry = $_POST['expiry'] ?? '';
    $card_name = $_POST['card_name'] ?? '';

    if (!$start_date || !$end_date || !$card_number || !$expiry || !$card_name) {
        $errors[] = "All fields are required.";
    }

    if (!preg_match('/^\d{9}$/', $card_number)) {
        $errors[] = "Credit card number must be exactly 9 digits.";
    }

    if (strtotime($start_date) === false || strtotime($end_date) === false) {
        $errors[] = "Invalid start or end date.";
    } elseif (strtotime($start_date) >= strtotime($end_date)) {
        $errors[] = "End date must be after start date.";
    }

    if (!preg_match('/^\d{4}-\d{2}$/', $expiry)) {
        $errors[] = "Expiry date must be in YYYY-MM format.";
    }

    if (empty($errors)) {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = $start->diff($end);
        $months = $interval->m + ($interval->y * 12);
        if ($interval->d > 0) {
            $months += 1;
        }

        $total_cost = $months * $flat['monthly_rent'];
        $expiry_date_db = $expiry . '-01';

        $insert = $pdo->prepare("
            INSERT INTO rentals (flat_id, customer_id, start_date, end_date, total_cost, status)
            VALUES (:flat_id, :customer_id, :start_date, :end_date, :total_cost, 'pending')
        ");
        $insert->execute([
            'flat_id' => $flat_id,
            'customer_id' => $_SESSION['user_id'],
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_cost' => $total_cost
        ]);

        $rental_id = $pdo->lastInsertId();

        $payment = $pdo->prepare("
            INSERT INTO payments (rental_id, credit_card_number, expiry_date, cardholder_name, payment_date)
            VALUES (:rental_id, :card_number, :expiry_date, :card_name, NOW())
        ");
        $payment->execute([
            'rental_id' => $rental_id,
            'card_number' => $card_number,
            'expiry_date' => $expiry_date_db,
            'card_name' => $card_name
        ]);

        $customer_message = "Dear {$user['name']}, your rental for '{$flat['title']}' is confirmed. Please contact the owner, {$flat['owner_name']}, at {$flat['owner_mobile']}.";
        $owner_message = "Dear {$flat['owner_name']}, your flat '{$flat['title']}' has been rented by {$user['name']}. Contact: {$user['mobile_number']}.";
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent <?= htmlspecialchars($flat['title']) ?> | SilvenStay</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        .form-group { margin-bottom: 1em; }
        label { display: block; margin-bottom: 0.5em; }
        input[type="text"], input[type="date"], input[type="month"] { width: 100%; padding: 0.5em; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main class="site-main">
    <h2>Rent <?= htmlspecialchars($flat['title']) ?></h2>

    <?php if ($already_rented): ?>
        <div class="error">
            <p>You have already rented this flat. Please check your rentals for more info.</p>
        </div>
    <?php elseif (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="success">
            <p>Rental confirmed successfully!</p>
            <p><?= nl2br(htmlspecialchars($customer_message)) ?></p>
            <p><?= nl2br(htmlspecialchars($owner_message)) ?></p>
        </div>
    <?php endif; ?>

    <?php if (!$already_rented && !$success): ?>
        <form method="post" novalidate>
            <fieldset disabled>
                <legend>Flat Details</legend>
                <div class="form-group"><label>Reference:</label><input value="<?= $flat['reference_number'] ?>"></div>
                <div class="form-group"><label>Location:</label><input value="<?= $flat['location'] ?>"></div>
                <div class="form-group"><label>Address:</label><input value="<?= $flat['address'] ?>"></div>
                <div class="form-group"><label>Bedrooms:</label><input value="<?= $flat['bedrooms'] ?>"></div>
                <div class="form-group"><label>Bathrooms:</label><input value="<?= $flat['bathrooms'] ?>"></div>
                <div class="form-group"><label>Monthly Rent:</label><input value="$<?= $flat['monthly_rent'] ?>"></div>
            </fieldset>

            <fieldset>
                <legend>Rental Period</legend>
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" name="start_date" required />
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" name="end_date" required />
                </div>
            </fieldset>

            <fieldset>
                <legend>Payment</legend>
                <div class="form-group">
                    <label for="card_number">Card Number (9 digits):</label>
                    <input type="text" name="card_number" maxlength="9" pattern="\d{9}" required />
                </div>
                <div class="form-group">
                    <label for="expiry">Expiry (YYYY-MM):</label>
                    <input type="month" name="expiry" required />
                </div>
                <div class="form-group">
                    <label for="card_name">Name on Card:</label>
                    <input type="text" name="card_name" required />
                </div>
            </fieldset>

            <button type="submit">Confirm Rent</button>
        </form>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
