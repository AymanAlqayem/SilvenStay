<?php
session_start();
require_once 'dbconfig.inc.php';

// Determine user role and ID, default to 'guest' if not set
$user_role = isset($_SESSION['user_type']) ? strtolower($_SESSION['user_type']) : 'guest';
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Redirect guests to login page
if ($user_role === 'guest') {
    header('Location: login.php');
    exit;
}

try {
    $pdo = getPDOConnection();

    // Handle actions (appointment updates, rental confirmations)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_role === 'owner') {
        if (isset($_POST['action']) && isset($_POST['appointment_id'])) {
            $appointment_id = (int)$_POST['appointment_id'];
            $action = $_POST['action'];

            if (in_array($action, ['approve', 'reject'])) {
                $status = $action === 'approve' ? 'approved' : 'rejected';
                $stmt = $pdo->prepare("UPDATE appointments SET status = :status WHERE appointment_id = :appointment_id AND flat_id IN (SELECT flat_id FROM flats WHERE owner_id = :user_id)");
                $stmt->execute([
                    ':status' => $status,
                    ':appointment_id' => $appointment_id,
                    ':user_id' => $user_id
                ]);
            }
        } elseif (isset($_POST['action']) && $_POST['action'] === 'confirm_rental' && isset($_POST['rental_id'])) {
            $rental_id = (int)$_POST['rental_id'];
            $stmt = $pdo->prepare("UPDATE rentals SET status = 'current' WHERE rental_id = :rental_id AND flat_id IN (SELECT flat_id FROM flats WHERE owner_id = :user_id)");
            $stmt->execute([
                ':rental_id' => $rental_id,
                ':user_id' => $user_id
            ]);
        }
        // Redirect to avoid form resubmission
        header("Location: messages.php");
        exit;
    }

    // Handle search input
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $search_param = $search ? "%$search%" : '%';

    // Initialize query based on user role
    $query = '';
    $params = [
        ':user_id' => $user_id,
        ':search_title' => $search_param,
        ':search_sender' => $search_param,
        ':search_body' => $search_param
    ];

    switch ($user_role) {
        case 'manager':
            $query = "
                SELECT m.message_id, m.title, m.sent_date, m.sender, m.message_body, m.is_read,
                       f.reference_number, f.address AS flat_details
                FROM messages m
                LEFT JOIN flats f ON m.flat_id = f.flat_id
                WHERE m.user_id = :user_id AND m.message_type IN ('approval', 'rental')
                AND (m.title LIKE :search_title OR m.sender LIKE :search_sender OR m.message_body LIKE :search_body)
                ORDER BY m.sent_date DESC
            ";
            break;

        case 'owner':
            $query = "
                SELECT m.message_id, m.title, m.sent_date, m.sender, m.message_body, m.is_read,
                       f.reference_number, f.address AS flat_details,
                       a.appointment_id, a.status AS appointment_status,
                       r.rental_id, r.start_date, r.end_date
                FROM messages m
                LEFT JOIN flats f ON m.flat_id = f.flat_id
                LEFT JOIN appointments a ON m.appointment_id = a.appointment_id
                LEFT JOIN rentals r ON m.rental_id = r.rental_id
                WHERE m.user_id = :user_id AND m.message_type IN ('appointment', 'rental')
                AND (m.title LIKE :search_title OR m.sender LIKE :search_sender OR m.message_body LIKE :search_body)
                ORDER BY m.sent_date DESC
            ";
            break;

        case 'customer':
            $query = "
                SELECT m.message_id, m.title, m.sent_date, m.sender, m.message_body, m.is_read,
                       f.reference_number, f.address AS flat_details,
                       a.appointment_id, a.status AS appointment_status,
                       r.rental_id, r.start_date, r.end_date,
                       u.mobile_number AS owner_mobile
                FROM messages m
                LEFT JOIN flats f ON m.flat_id = f.flat_id
                LEFT JOIN appointments a ON m.appointment_id = a.appointment_id
                LEFT JOIN rentals r ON m.rental_id = r.rental_id
                LEFT JOIN users u ON f.owner_id = u.user_id
                WHERE m.user_id = :user_id AND m.message_type IN ('appointment', 'rental')
                AND (m.title LIKE :search_title OR m.sender LIKE :search_sender OR m.message_body LIKE :search_body)
                ORDER BY m.sent_date DESC
            ";
            break;

        default:
            die('Invalid user role');
    }

    // Execute query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Messages</title>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>
<div class="container">
    <h2>Messages</h2>
    <form class="search-form" method="GET" action="messages.php">
        <input type="text" name="search" placeholder="Search messages..."
               value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
    </form>
    <table class="message-table">
        <thead>
        <tr>
            <th>Title</th>
            <th>Date</th>
            <th>Sender</th>
            <th>Message</th>
            <?php if ($user_role === 'manager'): ?>
                <th>Flat Details</th>
            <?php elseif ($user_role === 'owner'): ?>
                <th>Flat Details</th>
                <th>Action</th>
            <?php elseif ($user_role === 'customer'): ?>
                <th>Flat Details</th>
                <th>Rental Details</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                    <?php echo htmlspecialchars($message['title']); ?>
                </td>
                <td><?php echo date('Y-m-d H:i', strtotime($message['sent_date'])); ?></td>
                <td><?php echo htmlspecialchars($message['sender']); ?></td>
                <td><?php echo htmlspecialchars($message['message_body']); ?></td>
                <?php if ($user_role === 'manager'): ?>
                    <td>
                        <?php echo $message['reference_number'] ? htmlspecialchars($message['reference_number'] . ' - ' . $message['flat_details']) : 'N/A'; ?>
                    </td>
                <?php elseif ($user_role === 'owner'): ?>
                    <td>
                        <?php echo $message['reference_number'] ? htmlspecialchars($message['reference_number'] . ' - ' . $message['flat_details']) : 'N/A'; ?>
                    </td>
                    <td>
                        <?php if ($message['appointment_id'] && $message['appointment_status'] === 'pending'): ?>
                            <form method="POST" action="messages.php" style="display:inline;">
                                <input type="hidden" name="appointment_id"
                                       value="<?php echo $message['appointment_id']; ?>">
                                <input type="hidden" name="action" value="approve">
                                <input type="submit" class="action-btn accept" value="Accept">
                            </form>
                            <form method="POST" action="messages.php" style="display:inline;">
                                <input type="hidden" name="appointment_id"
                                       value="<?php echo $message['appointment_id']; ?>">
                                <input type="hidden" name="action" value="reject">
                                <input type="submit" class="action-btn reject" value="Reject">
                            </form>
                        <?php elseif ($message['rental_id'] && $message['message_type'] === 'rental'): ?>
                            <form method="POST" action="messages.php" style="display:inline;">
                                <input type="hidden" name="rental_id" value="<?php echo $message['rental_id']; ?>">
                                <input type="hidden" name="action" value="confirm_rental">
                                <input type="submit" class="action-btn accept" value="Accept">
                            </form>
                        <?php endif; ?>
                    </td>
                <?php elseif ($user_role === 'customer'): ?>
                    <td>
                        <?php echo $message['reference_number'] ? htmlspecialchars($message['reference_number'] . ' - ' . $message['flat_details']) : 'N/A'; ?>
                    </td>
                    <td>
                        <?php if ($message['rental_id']): ?>
                            Period: <?php echo date('Y-m-d', strtotime($message['start_date'])) . ' to ' . date('Y-m-d', strtotime($message['end_date'])); ?>
                            <br>
                            Key Collection: Contact owner at <?php echo htmlspecialchars($message['owner_mobile']); ?>
                        <?php elseif ($message['appointment_id']): ?>
                            Status: <?php echo ucfirst($message['appointment_status']); ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include 'footer.php'; ?>
</body>
</html>