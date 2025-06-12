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
    die("Only customers can request appointments.");
}

$flat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($flat_id <= 0) {
    die("Invalid flat ID.");
}

// Fetch flat details
$stmt = $pdo->prepare("
    SELECT f.*, d.title, d.description, u.name AS owner_name, u.owner_id, u.user_id AS owner_user_id, 
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

// Validate owner_id exists in users table
if (!$flat['owner_user_id']) {
    die("Error: The owner of this flat is not registered in the system. Please contact support.");
}

// Check if user already requested an appointment for this flat
$already_stmt = $pdo->prepare("
    SELECT appointment_id FROM appointments
    WHERE flat_id = :flat_id AND customer_id = :customer_id AND status IN ('pending', 'approved')
");
$already_stmt->execute([
    'flat_id' => $flat_id,
    'customer_id' => $_SESSION['user_id']
]);
$already_requested = $already_stmt->fetch();

$user_stmt = $pdo->prepare("SELECT name, mobile_number FROM users WHERE user_id = :user_id");
$user_stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

$errors = [];
$success = false;
$customer_message = '';
$owner_message = '';

// Fetch all slots (available and booked) for display
$slots_stmt = $pdo->prepare("
    SELECT slot_id, appointment_date, appointment_time, is_booked 
    FROM flat_availability_slots 
    WHERE flat_id = :flat_id 
    AND appointment_date >= CURDATE()
    ORDER BY appointment_date, appointment_time
");
$slots_stmt->execute(['flat_id' => $flat_id]);
$all_slots = $slots_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already_requested) {
    $slot_id = isset($_POST['slot_id']) ? intval($_POST['slot_id']) : 0;

    if ($slot_id <= 0) {
        $errors[] = "Please select a valid time slot.";
    }

    // Verify the selected slot is valid and available
    $slot_check_stmt = $pdo->prepare("
        SELECT slot_id, appointment_date, appointment_time 
        FROM flat_availability_slots 
        WHERE slot_id = :slot_id AND flat_id = :flat_id AND is_booked = FALSE 
        AND appointment_date >= CURDATE()
    ");
    $slot_check_stmt->execute([
        'slot_id' => $slot_id,
        'flat_id' => $flat_id
    ]);
    $selected_slot = $slot_check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$selected_slot) {
        $errors[] = "The selected time slot is not available or has been booked.";
    }

    if (empty($errors)) {
        $appointment_date = $selected_slot['appointment_date'];
        $appointment_time = $selected_slot['appointment_time'];

        // Start a transaction
        try {
            $pdo->beginTransaction();

            // Insert into appointments table
            $insert = $pdo->prepare("
                INSERT INTO appointments (flat_id, customer_id, slot_id, status)
                VALUES (:flat_id, :customer_id, :slot_id, 'pending')
            ");
            $insert->execute([
                'flat_id' => $flat_id,
                'customer_id' => $_SESSION['user_id'],
                'slot_id' => $slot_id
            ]);

            $appointment_id = $pdo->lastInsertId();

            // Mark the slot as booked
            $update_slot = $pdo->prepare("
                UPDATE flat_availability_slots 
                SET is_booked = TRUE 
                WHERE slot_id = :slot_id
            ");
            $update_slot->execute(['slot_id' => $slot_id]);

            // Send message to owner
            $message = $pdo->prepare("
                INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id, appointment_id)
                VALUES (:user_id, :title, :message_body, :sender, NOW(), 'appointment', :flat_id, :appointment_id)
            ");
            $message->execute([
                'user_id' => $flat['owner_user_id'],
                'title' => "New Appointment Request for {$flat['title']}",
                'message_body' => "Dear {$flat['owner_name']}, an appointment request has been submitted by {$user['name']} to view your flat '{$flat['title']}' (Ref: {$flat['reference_number']}) on {$appointment_date} at {$appointment_time}. Please review and confirm or reject this request.",
                'sender' => "System on behalf of {$user['name']}",
                'flat_id' => $flat_id,
                'appointment_id' => $appointment_id
            ]);

            // Send message to customer
            $customer_message_stmt = $pdo->prepare("
                INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id, appointment_id)
                VALUES (:user_id, :title, :message_body, :sender, NOW(), 'appointment', :flat_id, :appointment_id)
            ");
            $customer_message_stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'title' => "Appointment Request Submitted for {$flat['title']}",
                'message_body' => "Dear {$user['name']}, your appointment request to view '{$flat['title']}' (Ref: {$flat['reference_number']}) on {$appointment_date} at {$appointment_time} is pending approval. You will be notified once the owner responds.",
                'sender' => "System",
                'flat_id' => $flat_id,
                'appointment_id' => $appointment_id
            ]);

            $pdo->commit();

            $customer_message = "Your appointment request for '{$flat['title']}' (Ref: {$flat['reference_number']}) on {$appointment_date} at {$appointment_time} has been submitted successfully.";
            $owner_message = "A new appointment request for '{$flat['title']}' (Ref: {$flat['reference_number']}) has been sent to the owner for review.";
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Failed to process appointment request: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Appointment for <?= htmlspecialchars($flat['title']) ?> | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<main class="site-main">

    <h2>Request Appointment for <?= htmlspecialchars($flat['title']) ?></h2>

    <?php if ($already_requested): ?>
        <section class="error">
            <p>You have already requested an appointment for this flat. Please check your appointments for more
                info.</p>
        </section>

    <?php elseif (!empty($errors)): ?>
        <section class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php elseif ($success): ?>
        <section class="success">
            <p>Appointment request submitted successfully!</p>
            <p><?= nl2br(htmlspecialchars($customer_message)) ?></p>
            <p><?= nl2br(htmlspecialchars($owner_message)) ?></p>
        </section>
    <?php endif; ?>

    <?php if (!$already_requested && !$success): ?>
        <form method="post" novalidate>

            <fieldset disabled>
                <legend>Flat Details</legend>

                <article class="form-group">
                    <label>Reference:</label>
                    <input value="<?= htmlspecialchars($flat['reference_number']) ?>">
                </article>

                <article class="form-group">
                    <label>Location:</label>
                    <input value="<?= htmlspecialchars($flat['location']) ?>">
                </article>

                <article class="form-group">
                    <label>Address:</label>
                    <input value="<?= htmlspecialchars($flat['address']) ?>">
                </article>

                <article class="form-group">
                    <label>Bedrooms:</label>
                    <input value="<?= htmlspecialchars($flat['bedrooms']) ?>">
                </article>

                <article class="form-group">
                    <label>Bathrooms:</label>
                    <input value="<?= htmlspecialchars($flat['bathrooms']) ?>">
                </article>

                <article class="form-group">
                    <label>Monthly Rent:</label>
                    <input value="$<?= htmlspecialchars(number_format($flat['monthly_rent'], 2)) ?>">
                </article>

            </fieldset>

            <fieldset>
                <legend>Available Appointment Slots</legend>
                <?php if (empty($all_slots)): ?>
                    <p>No available appointment slots for this flat.</p>
                <?php else: ?>
                    <table class="slots-table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($all_slots as $slot): ?>
                            <tr class="<?= $slot['is_booked'] ? 'slot-taken' : 'slot-available' ?>">
                                <td><?= htmlspecialchars($slot['appointment_date']) ?></td>
                                <td><?= htmlspecialchars($slot['appointment_time']) ?></td>
                                <td><?= $slot['is_booked'] ? 'Taken' : 'Available' ?></td>
                                <td>
                                    <?php if (!$slot['is_booked']): ?>
                                        <button type="submit" name="slot_id"
                                                value="<?= htmlspecialchars($slot['slot_id']) ?>" class="slot-book-btn">
                                            Book
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="slot-book-btn" disabled>Taken</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </fieldset>
        </form>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>