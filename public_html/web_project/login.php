<?php session_start(); ?>

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
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['login_error']); ?>
                    <?php unset($_SESSION['login_error']); ?>
                </div>
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
                <span class="toggle-password">
                    </span>
            </section>

            <section class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </section>

            <button type="submit" class="btn-login">Login</button>

            <section class="register-link">
                Don't have an account? <a href="Step1_Registration.php">Register here</a>
            </section>
        </form>
    </section>

    <aside class="login-right">
        <div class="welcome-message">
            <h2>New to Our Platform?</h2>
            <p>Join thousands of happy customers who found their perfect home through us</p>
            <a href="Step1_Registration.php" class="btn-register">Create Account</a>
        </div>
    </aside>
</main>
</body>
</html>