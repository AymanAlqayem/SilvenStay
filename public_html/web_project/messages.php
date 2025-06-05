<?php
session_start();
require_once 'dbconfig.inc.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    die("Not authenticated.");
}

$userId = $_SESSION['user_id'];
$userType = $_SESSION['user_type'];
$search = $_GET['search'] ?? '';

try {
    $pdo = getPDOConnection();

    // Base SQL query
    $sql = "SELECT * FROM messages WHERE user_id = :user_id";
    $params = [':user_id' => $userId];

    if (!empty($search)) {
        $sql .= " AND (title LIKE :search OR message_body LIKE :search)";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY sent_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: Log number of messages retrieved
    error_log("Messages retrieved: " . count($messages));

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database error: " . htmlspecialchars($e->getMessage()));
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
                <h2>Inbox for User ID: <?php echo htmlspecialchars($userId); ?></h2>
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
                        <th class="sortable">Title</th>
                        <th class="sortable">Date</th>
                        <th class="sortable">Sender</th>
                        <th>Message</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr class="<?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
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