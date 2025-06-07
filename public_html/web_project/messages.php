<?php
session_start();
require_once 'dbconfig.inc.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    die("Not authenticated.");
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$search = $_GET['search'] ?? '';
$errors = [];
$success_message = '';

// Generate CSRF token for action forms
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = getPDOConnection();
    $pdo = getPDOConnection();

    // Handle owner and manager actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $pdo->beginTransaction();

        if ($userType === 'owner' && isset($_POST['rental_id']) && isset($_POST['flat_id'])) {
            $rental_id = intval($_POST['rental_id']);
            $flat_id = intval($_POST['flat_id']);
            $action = $_POST['action'];

            // Verify the rental belongs to the owner's flat
            $check_stmt = $pdo->prepare("
                SELECT r.rental_id, r.customer_id, f.owner_id
                FROM rentals r
                JOIN flats f ON r.flat_id = f.flat_id
                WHERE r.rental_id = :rental_id AND r.flat_id = :flat_id AND f.owner_id = :owner_id AND r.status = 'pending'
            ");
            $check_stmt->execute([
                'rental_id' => $rental_id,
                'flat_id' => $flat_id,
                'owner_id' => $userId
            ]);
            $rental = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$rental) {
                $errors[] = "Invalid rental or not authorized.";
            } else {
                if ($action === 'accept_rental') {
                    // Update rental status
                    $update_stmt = $pdo->prepare("UPDATE rentals SET status = 'current' WHERE rental_id = :rental_id");
                    $update_stmt->execute(['rental_id' => $rental_id]);

                    // Fetch customer and flat details for the message
                    $details_stmt = $pdo->prepare("
                        SELECT u.name AS customer_name, f.reference_number
                        FROM users u
                        JOIN rentals r ON r.customer_id = u.user_id
                        JOIN flats f ON r.flat_id = f.flat_id
                        WHERE r.rental_id = :rental_id
                    ");
                    $details_stmt->execute(['rental_id' => $rental_id]);
                    $details = $details_stmt->fetch(PDO::FETCH_ASSOC);

                    // Send confirmation message to customer
                    $message_stmt = $pdo->prepare("
                        INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id, rental_id)
                        VALUES (:user_id, :title, :message_body, :sender, NOW(), 'rental', :flat_id, :rental_id)
                    ");
                    $message_stmt->execute([
                        'user_id' => $rental['customer_id'],
                        'title' => "Rental Request Accepted",
                        'message_body' => "Dear {$details['customer_name']}, your rental request for flat {$details['reference_number']} has been accepted.",
                        'sender' => "System on behalf of Owner",
                        'flat_id' => $flat_id,
                        'rental_id' => $rental_id
                    ]);

                    $success_message = "Rental request accepted successfully.";
                } elseif ($action === 'reject_rental') {
                    // Delete associated messages first
                    $delete_messages = $pdo->prepare("DELETE FROM messages WHERE rental_id = :rental_id");
                    $delete_messages->execute(['rental_id' => $rental_id]);

                    // Delete payment and rental
                    $delete_payment = $pdo->prepare("DELETE FROM payments WHERE rental_id = :rental_id");
                    $delete_payment->execute(['rental_id' => $rental_id]);

                    $delete_rental = $pdo->prepare("DELETE FROM rentals WHERE rental_id = :rental_id");
                    $delete_rental->execute(['rental_id' => $rental_id]);

                    // Fetch customer and flat details for the message
                    $details_stmt = $pdo->prepare("
                        SELECT u.name AS customer_name, f.reference_number
                        FROM users u
                        JOIN flats f ON f.flat_id = :flat_id
                        WHERE u.user_id = :customer_id
                    ");
                    $details_stmt->execute([
                        'flat_id' => $flat_id,
                        'customer_id' => $rental['customer_id']
                    ]);
                    $details = $details_stmt->fetch(PDO::FETCH_ASSOC);

                    // Send rejection message to customer without referencing rental_id
                    $message_stmt = $pdo->prepare("
                        INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id)
                        VALUES (:user_id, :title, :message_body, :sender, NOW(), 'rental', :flat_id)
                    ");
                    $message_stmt->execute([
                        'user_id' => $rental['customer_id'],
                        'title' => "Rental Request Rejected",
                        'message_body' => "Dear {$details['customer_name']}, your rental request for flat {$details['reference_number']} has been rejected.",
                        'sender' => "System on behalf of Owner",
                        'flat_id' => $flat_id
                    ]);

                    $success_message = "Rental request rejected successfully.";
                }
            }
        } elseif ($userType === 'manager' && isset($_POST['flat_id'])) {
            $flat_id = intval($_POST['flat_id']);
            $action = $_POST['action'];

            // Verify the flat exists
            $check_stmt = $pdo->prepare("
                SELECT flat_id, owner_id, reference_number
                FROM flats
                WHERE flat_id = :flat_id AND status = 'pending'
            ");
            $check_stmt->execute(['flat_id' => $flat_id]);
            $flat = $check_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$flat) {
                $errors[] = "Invalid flat or not pending.";
            } else {
                if ($action === 'accept_flat') {
                    // Generate unique 6-digit reference number
                    do {
                        $reference_number = sprintf("%06d", mt_rand(100000, 999999));
                        $check_ref = $pdo->prepare("SELECT flat_id FROM flats WHERE reference_number = :reference_number");
                        $check_ref->execute(['reference_number' => $reference_number]);
                    } while ($check_ref->fetch());

                    // Update flat status and reference number
                    $update_stmt = $pdo->prepare("
                        UPDATE flats
                        SET status = 'approved', reference_number = :reference_number, approved_by = :manager_id, approval_date = NOW()
                        WHERE flat_id = :flat_id
                    ");
                    $update_stmt->execute([
                        'flat_id' => $flat_id,
                        'reference_number' => $reference_number,
                        'manager_id' => $userId
                    ]);

                    // Send confirmation message to owner
                    $message_stmt = $pdo->prepare("
                        INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id)
                        VALUES (:user_id, :title, :message_body, :sender, NOW(), 'approval', :flat_id)
                    ");
                    $message_stmt->execute([
                        'user_id' => $flat['owner_id'],
                        'title' => "Flat Approval",
                        'message_body' => "Your flat {$reference_number} has been approved by the manager.",
                        'sender' => "System on behalf of Manager",
                        'flat_id' => $flat_id
                    ]);

                    $success_message = "Flat approved successfully.";
                } elseif ($action === 'reject_flat') {
                    // Update flat status to rejected
                    $update_stmt = $pdo->prepare("
                        UPDATE flats
                        SET status = 'rejected'
                        WHERE flat_id = :flat_id
                    ");
                    $update_stmt->execute(['flat_id' => $flat_id]);

                    // Send rejection message to owner with flat_id = NULL
                    $message_stmt = $pdo->prepare("
                        INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id)
                        VALUES (:user_id, :title, :message_body, :sender, NOW(), 'approval', NULL)
                    ");
                    $message_stmt->execute([
                        'user_id' => $flat['owner_id'],
                        'title' => "Flat Rejection",
                        'message_body' => "Your flat submission (ID: $flat_id, Ref: {$flat['reference_number']}) has been rejected by the manager.",
                        'sender' => "System on behalf of Manager"
                    ]);

                    $success_message = "Flat rejected successfully.";
                }
            }
        }

        $pdo->commit();
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $errors[] = "Invalid or missing CSRF token.";
    }

    // Fetch messages based on user type
    $sql = "SELECT m.*, f.reference_number, r.status AS rental_status, f.status AS flat_status
            FROM messages m
            LEFT JOIN flats f ON m.flat_id = f.flat_id
            LEFT JOIN rentals r ON m.rental_id = r.rental_id
            WHERE m.user_id = :user_id";
    $params = [':user_id' => $userId];

    if ($userType === 'customer') {
        $sql .= " AND m.message_type = 'rental'";
    } elseif ($userType === 'owner') {
        $sql .= " AND m.message_type IN ('rental', 'approval')";
    } elseif ($userType === 'manager') {
        $sql .= " AND m.message_type = 'approval'";
    }

    if (!empty($search)) {
        $sql .= " AND (m.title LIKE :search OR m.message_body LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY m.sent_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mark messages as read
    $update_read = $pdo->prepare("UPDATE messages SET is_read = TRUE WHERE user_id = :user_id AND is_read = FALSE");
    $update_read->execute(['user_id' => $userId]);

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $errors[] = "Database error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Messages Inbox | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">
    <main class="site-main">
        <div class="messages-container">
            <div class="messages-header">
                <h2>Inbox for User ID: <?php echo htmlspecialchars($userId); ?>
                    (<?php echo htmlspecialchars($userType); ?>)</h2>
                <div class="messages-controls">
                    <form class="search-form" method="GET" action="">
                        <input
                                type="text"
                                name="search"
                                placeholder="Search messages..."
                                value="<?php echo htmlspecialchars($search); ?>"
                                autocomplete="off"
                        />
                    </form>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="success">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>

            <?php if (empty($messages)): ?>
                <div class="empty-messages">
                    <div class="empty-icon">📭</div>
                    <h3>No messages found</h3>
                    <p>You have no messages at the moment.</p>
                </div>
            <?php else: ?>
                <table class="messages-table">
                    <thead>
                    <tr>
                        <th class="sortable">Type</th>
                        <th class="sortable">Title</th>
                        <th class="sortable">Date</th>
                        <th class="sortable">Sender</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                            <td><?php echo htmlspecialchars($msg['message_type']); ?></td>
                            <td class="message-title">
                                <?php echo htmlspecialchars($msg['title']); ?>
                            </td>
                            <td class="message-date">
                                <?php echo date('Y-m-d H:i', strtotime($msg['sent_date'])); ?>
                            </td>
                            <td class="message-sender">
                                <?php echo htmlspecialchars($msg['sender']); ?>
                            </td>
                            <td class="message-preview">
                                <?php
                                $preview = strip_tags($msg['message_body']);
                                echo htmlspecialchars(mb_strimwidth($preview, 0, 120, '...'));
                                ?>
                            </td>
                            <td class="action-buttons">
                                <?php if ($userType === 'owner' && $msg['message_type'] === 'rental' && isset($msg['rental_id']) && $msg['rental_status'] === 'pending'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="rental_id"
                                               value="<?php echo (int)$msg['rental_id']; ?>">
                                        <input type="hidden" name="flat_id" value="<?php echo (int)$msg['flat_id']; ?>">
                                        <input type="hidden" name="action" value="accept_rental">
                                        <input type="hidden" name="csrf_token"
                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit">Accept</button>
                                    </form>
                                    <form method="POST" action="">
                                        <input type="hidden" name="rental_id"
                                               value="<?php echo (int)$msg['rental_id']; ?>">
                                        <input type="hidden" name="flat_id" value="<?php echo (int)$msg['flat_id']; ?>">
                                        <input type="hidden" name="action" value="reject_rental">
                                        <input type="hidden" name="csrf_token"
                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit">Reject</button>
                                    </form>
                                <?php elseif ($userType === 'manager' && $msg['message_type'] === 'approval' && isset($msg['flat_id']) && $msg['flat_status'] === 'pending'): ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="flat_id" value="<?php echo (int)$msg['flat_id']; ?>">
                                        <input type="hidden" name="action" value="accept_flat">
                                        <input type="hidden" name="csrf_token"
                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit">Accept</button>
                                    </form>
                                    <form method="POST" action="">
                                        <input type="hidden" name="flat_id" value="<?php echo (int)$msg['flat_id']; ?>">
                                        <input type="hidden" name="action" value="reject_flat">
                                        <input type="hidden" name="csrf_token"
                                               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>