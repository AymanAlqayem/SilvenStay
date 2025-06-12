<?php
session_start();
require_once 'dbconfig.inc.php';

$pdo = getPDOConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');


    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields.";
    } else {
        try {
            // Select user by email only
            $stmt = $pdo->prepare("
                SELECT user_id, user_type, password, customer_id, owner_id, manager_id, name 
                FROM users 
                WHERE email = :email
            ");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check password using password_verify()
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['is_registered'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['step1']['name'] = $user['name'];
                $role_id = $user[$user['user_type'] . '_id'];
                $_SESSION['role_id'] = $role_id;

                if (!empty($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header("Location: $redirect");
                } else {
                    header("Location: main.php");
                }
                exit;
            } else {
                $_SESSION['login_error'] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome Back | Account Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main class="login-container">

    <section class="login-left">

        <article class="login-header">
            <h1>Welcome back!</h1>
            <p>Please login to your account</p>
        </article>

        <form action="login.php" method="POST" class="login-form">

            <?php if (isset($_SESSION['login_error'])): ?>

                <article class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['login_error']); ?>
                    <?php unset($_SESSION['login_error']); ?>
                </article>

            <?php endif; ?>

            <section class="input-group">

                <label for="username">Email</label>
                <input type="email" id="username" name="username" required
                       placeholder="Enter your email" autofocus>

            </section>

            <section class="input-group">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required
                       placeholder="Enter your password">
                <span class="toggle-password"></span>

            </section>

            <button type="submit" class="btn-login">Login</button>

            <section class="register-link">
                Don't have an account? <a href="Step1_Registration.php">Register here</a>
            </section>

        </form>
    </section>


    <aside class="login-right">
        <article class="welcome-message">
            <h2>New to Our Platform?</h2>
            <p>Join thousands of happy customers who found their perfect home through us</p>
            <a href="Step1_Registration.php" class="btn-register">Create Account</a>
        </article>
    </aside>

</main>
</body>
</html>