<?php
session_start();
require_once 'database.inc.php';

$step = isset($_SESSION['reg_step']) ? $_SESSION['reg_step'] : 1;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($step == 1) {
        // Step 1: Personal Details
        $_SESSION['reg_data'] = [
            'national_id' => $_POST['national_id'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'dob' => $_POST['dob'] ?? '',
            'email' => $_POST['email'] ?? '',
            'mobile' => $_POST['mobile'] ?? '',
            'telephone' => $_POST['telephone'] ?? ''
        ];
        if (empty($_SESSION['reg_data']['national_id']) || empty($_SESSION['reg_data']['email'])) {
            $errors[] = "National ID and Email are required.";
        } else {
            $_SESSION['reg_step'] = 2;
            header('Location: register_customer.php');
            exit;
        }
    } elseif ($step == 2) {
        // Step 2: E-Account
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Username must be a valid email.";
        } elseif (strlen($password) < 6 || strlen($password) > 15 || !preg_match('/^\d.*[a-z]$/', $password)) {
            $errors[] = "Password must be 6-15 characters, start with a digit, and end with a lowercase letter.";
        } elseif ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        } else {
            // Check username uniqueness
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Username already exists.";
            } else {
                $_SESSION['reg_data']['username'] = $username;
                $_SESSION['reg_data']['password'] = $password;
                $_SESSION['reg_step'] = 3;
                header('Location: register_customer.php');
                exit;
            }
        }
    } elseif ($step == 3) {
        // Step 3: Confirm
        if (isset($_POST['confirm'])) {
            // Generate customer ID
            $customer_id = sprintf("%09d", rand(1, 999999999));
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (national_id, postal_code, date_of_birth, email, mobile_number, telephone_number, 
                    username, password, user_type, customer_id)
                    VALUES (:national_id, :postal_code, :dob, :email, :mobile, :telephone, :username, :password, 'customer', :customer_id)
                ");
                $stmt->execute([
                    'national_id' => $_SESSION['reg_data']['national_id'],
                    'postal_code' => $_SESSION['reg_data']['postal_code'],
                    'dob' => $_SESSION['reg_data']['dob'],
                    'email' => $_SESSION['reg_data']['email'],
                    'mobile' => $_SESSION['reg_data']['mobile'],
                    'telephone' => $_SESSION['reg_data']['telephone'],
                    'username' => $_SESSION['reg_data']['username'],
                    'password' => $_SESSION['reg_data']['password'], // In production, hash the password
                    'customer_id' => $customer_id
                ]);
                $message = "Registration successful! Customer ID: $customer_id";
                unset($_SESSION['reg_step'], $_SESSION['reg_data']);
            } catch (PDOException $e) {
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Birzeit Flat Rent</h1>
        <img src="images/logo.png" alt="Logo" style="height: 50px;">
        <div>
            <a href="about.php">About Us</a> |
            <a href="login.php">Login</a> |
            <a href="register_customer.php">Register</a>
        </div>
    </header>
    <nav>
        <ul>
            <li><a href="../../../../../Silvenstay/Project/main.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="search.php">Search Flats</a></li>
            <li><a href="register_customer.php" class="active">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <main>
        <h2>Customer Registration - Step <?php echo $step; ?></h2>
        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php elseif ($step == 1): ?>
            <form method="POST">
                <label for="national_id" class="required">National ID</label>
                <input type="text" id="national_id" name="national_id" required>
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob">
                <label for="email" class="required">Email</label>
                <input type="email" id="email" name="email" required>
                <label for="mobile">Mobile Number</label>
                <input type="text" id="mobile" name="mobile">
                <label for="telephone">Telephone Number</label>
                <input type="text" id="telephone" name="telephone">
                <button type="submit">Next</button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="POST">
                <label for="username" class="required">Username (Email)</label>
                <input type="email" id="username" name="username" required>
                <label for="password" class="required">Password</label>
                <input type="password" id="password" name="password" required>
                <label for="confirm_password" class="required">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <button type="submit">Next</button>
            </form>
        <?php elseif ($step == 3): ?>
            <form method="POST">
                <h3>Review Your Information</h3>
                <label>National ID</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['national_id']); ?>" readonly>
                <label>Postal Code</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['postal_code']); ?>" readonly>
                <label>Date of Birth</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['dob']); ?>" readonly>
                <label>Email</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['email']); ?>" readonly>
                <label>Mobile Number</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['mobile']); ?>" readonly>
                <label>Telephone Number</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['telephone']); ?>" readonly>
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($_SESSION['reg_data']['username']); ?>" readonly>
                <button type="submit" name="confirm">Confirm</button>
            </form>
        <?php endif; ?>
    </main>
    <footer>
        <img src="images/logo_small.png" alt="Small Logo" style="height: 30px;">
        <p>&copy; 2025 Birzeit Flat Rent. All rights reserved.</p>
        <p>Contact: info@birzeitflatrent.com | +970-123-456-789</p>
        <p><a href="contact.php">Contact Us</a></p>
    </footer>
</div>
</body>
</html>