<?php
session_start();
require_once 'dbconfig.inc.php';

// Validate user_id
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    $_SESSION['message'] = "Invalid user ID.";
    header("Location: viewRentedFlat.php");
    exit;
}

$pdo = getPDOConnection();

try {
    $stmt = $pdo->prepare("
        SELECT name, city, mobile_number, email, user_type
        FROM users
        WHERE user_id = :user_id AND user_type IN ('owner', 'customer')
    ");
    $stmt->execute(['user_id' => (int)$_GET['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['message'] = "User not found.";
        header("Location: viewRentedFlat.php");
        exit;
    }

    $phoneNumber = $user['mobile_number']
        ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $user['mobile_number'])
        : 'Not provided';

} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . htmlspecialchars($e->getMessage());
    header("Location: viewRentedFlat.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= ucfirst($user['user_type']) ?> Info | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<main class="card-wrapper">
    <section class="user-single-card <?= $user['user_type'] ?>">

        <header class="card-header">
            <h2><?= htmlspecialchars($user['name']) ?></h2>
        </header>

        <section class="card-details">

            <article class="info-row">
                <span class="icon">📞</span>
                <article>
                    <h4>Phone</h4>
                    <p><?= htmlspecialchars($phoneNumber) ?></p>
                </article>

            </article>

            <section class="info-row">

                <span class="icon">✉️</span>

                <article>
                    <h4>Email</h4>
                    <p>
                        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="email-link">
                            <?= htmlspecialchars($user['email']) ?>
                        </a>
                    </p>
                </article>

            </section>
        </section>

        <footer class="card-footer">

            <a href="mailto:<?= htmlspecialchars($user['email']) ?>" class="contact-button">
                Contact <?= ucfirst($user['user_type']) ?>
            </a>
        </footer>
    </section>
</main>

</body>
</html>
