<?php
session_start();
require_once 'dbconfig.inc.php';

if (!isset($_SESSION['is_registered']) || $_SESSION['is_registered'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You are not logged in.";
    header("Location: main.php");
    exit;
}

$pdo = getPDOConnection();

// Fetch user
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['message'] = "User not found.";
        header("Location: main.php");
        exit;
    }

    $_SESSION['step1'] = [
        'national_id' => $user['national_id'],
        'name' => $user['name'],
        'flat_no' => $user['flat_no'],
        'street' => $user['street'],
        'city' => $user['city'],
        'postal_code' => $user['postal_code'],
        'dob' => $user['date_of_birth'],
        'email' => $user['email'],
        'mobile' => $user['mobile_number'],
        'telephone' => $user['telephone_number'],
        'profile_photo' => $user['profile_photo']
    ];
    $_SESSION['customer_id'] = $user['customer_id'];
    $_SESSION['owner_id'] = $user['owner_id'];
    $_SESSION['manager_id'] = $user['manager_id'] ?? null;
    $_SESSION['user_type'] = $user['user_type'];
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . $e->getMessage();
    error_log("Profile fetch failed: " . $e->getMessage());
    header("Location: main.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $national_id = trim($_POST['national_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $flat_no = trim($_POST['flat_no'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $bank_name = trim($_POST['bank_name'] ?? '');
    $bank_branch = trim($_POST['bank_branch'] ?? '');
    $account_number = trim($_POST['account_number'] ?? '');
    $profile_photo = $user['profile_photo']; // Keep existing photo by default

    // Validate required fields
    if (empty($national_id) || empty($name) || empty($flat_no) || empty($street) || empty($city) ||
        empty($postal_code) || empty($dob) || empty($email) || empty($mobile)) {
        $_SESSION['message'] = "Please fill in all required fields.";
        header("Location: profile.php");
        exit;
    }

    // Validate name (only letters and spaces)
    if (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $_SESSION['error_type'] = 'name';
        $_SESSION['message'] = "Oops! Your name should only contain letters and spaces. Let's keep it simple and elegant!";
        header("Location: profile.php");
        exit;
    }

    // Check email uniqueness (excluding current user)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND user_id != :user_id");
    $stmt->execute(['email' => $email, 'user_id' => $_SESSION['user_id']]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['error_type'] = 'email';
        $_SESSION['message'] = "This email is already taken. Please choose a unique email address.";
        header("Location: profile.php");
        exit;
    }

    // Handle profile photo upload
    if (!empty($_FILES['profile_photo']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        $upload_dir = 'images/';
        $file_type = $_FILES['profile_photo']['type'];
        $file_size = $_FILES['profile_photo']['size'];
        $file_tmp = $_FILES['profile_photo']['tmp_name'];
        $file_name = basename($_FILES['profile_photo']['name']);

        // Sanitize filename
        $file_name = preg_replace("/[^A-Za-z0-9._-]/", '', $file_name);

        // Check for filename conflict
        $base_name = pathinfo($file_name, PATHINFO_FILENAME);
        $extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $counter = 1;
        $new_file_name = $file_name;
        while (file_exists($upload_dir . $new_file_name)) {
            $new_file_name = $base_name . '_' . $counter . '.' . $extension;
            $counter++;
        }
        $upload_path = $upload_dir . $new_file_name;

        // Validate image
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['message'] = "Only JPEG, PNG, or GIF images are allowed.";
            header("Location: profile.php");
            exit;
        }

        if ($file_size > $max_size) {
            $_SESSION['message'] = "Image size must not exceed 5MB.";
            header("Location: profile.php");
            exit;
        }

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $upload_path)) {
            $_SESSION['message'] = "Failed to upload image.";
            header("Location: profile.php");
            exit;
        }

        // Delete old profile photo if it exists and is different
        if (!empty($user['profile_photo']) && $user['profile_photo'] !== $new_file_name && file_exists($upload_dir . $user['profile_photo'])) {
            unlink($upload_dir . $user['profile_photo']);
        }

        $profile_photo = $new_file_name;
    }

    // Perform update
    try {
        if ($_SESSION['user_type'] === 'owner') {
            $stmt = $pdo->prepare(
                "UPDATE users SET 
                    national_id = :national_id, 
                    name = :name, 
                    flat_no = :flat_no, 
                    street = :street, 
                    city = :city, 
                    postal_code = :postal_code, 
                    date_of_birth = :dob, 
                    email = :email, 
                    mobile_number = :mobile, 
                    telephone_number = :telephone, 
                    username = :username, 
                    bank_name = :bank_name, 
                    bank_branch = :bank_branch, 
                    account_number = :account_number, 
                    profile_photo = :profile_photo
                 WHERE user_id = :user_id"
            );
            $stmt->execute([
                'national_id' => $national_id,
                'name' => $name,
                'flat_no' => $flat_no,
                'street' => $street,
                'city' => $city,
                'postal_code' => $postal_code,
                'dob' => $dob,
                'email' => $email,
                'mobile' => $mobile,
                'telephone' => $telephone,
                'username' => $email,
                'bank_name' => $bank_name,
                'bank_branch' => $bank_branch,
                'account_number' => $account_number,
                'profile_photo' => $profile_photo,
                'user_id' => $_SESSION['user_id']
            ]);
        } else {
            $stmt = $pdo->prepare(
                "UPDATE users SET 
                    national_id = :national_id, 
                    name = :name, 
                    flat_no = :flat_no, 
                    street = :street, 
                    city = :city, 
                    postal_code = :postal_code, 
                    date_of_birth = :dob, 
                    email = :email, 
                    mobile_number = :mobile, 
                    telephone_number = :telephone, 
                    username = :username, 
                    profile_photo = :profile_photo
                 WHERE user_id = :user_id"
            );
            $stmt->execute([
                'national_id' => $national_id,
                'name' => $name,
                'flat_no' => $flat_no,
                'street' => $street,
                'city' => $city,
                'postal_code' => $postal_code,
                'dob' => $dob,
                'email' => $email,
                'mobile' => $mobile,
                'telephone' => $telephone,
                'username' => $email,
                'profile_photo' => $profile_photo,
                'user_id' => $_SESSION['user_id']
            ]);
        }

        $_SESSION['step1'] = [
            'national_id' => $national_id,
            'name' => $name,
            'flat_no' => $flat_no,
            'street' => $street,
            'city' => $city,
            'postal_code' => $postal_code,
            'dob' => $dob,
            'email' => $email,
            'mobile' => $mobile,
            'telephone' => $telephone,
            'profile_photo' => $profile_photo
        ];

        $_SESSION['message'] = "Profile updated successfully.";
        header("Location: main.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        error_log("Profile update failed: " . $e->getMessage());
        header("Location: profile.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Account Settings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<div class="content-wrapper">
    <main class="site-main">
        <div class="registration-container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-error">
                    <span class="alert-icon">⚠️</span>
                    <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
                    <span class="alert-close" onclick="this.parentElement.style.display='none'">×</span>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['error_type']); ?>
            <?php endif; ?>

            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if (!empty($user['profile_photo'])): ?>
                        <img src="images/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo"
                             class="profile-photo">
                    <?php else: ?>
                        <?= substr(htmlspecialchars($user['name'] ?? ''), 0, 1) ?>
                    <?php endif; ?>
                </div>
                <div class="profile-info">
                    <h1><?= htmlspecialchars($user['name'] ?? '') ?></h1>
                    <p>Member since <?= date('F Y', strtotime($user['registration_date'] ?? 'now')) ?></p>
                </div>
            </div>

            <form action="profile.php" method="POST" class="registration-form" enctype="multipart/form-data">
                <section class="form-section">
                    <h2 class="form-section-title">Personal Information</h2>

                    <div class="form-grid">
                        <fieldset class="form-group">
                            <label class="form-label">User ID</label>
                            <input type="text" class="form-input" readonly
                                   value="<?= htmlspecialchars($user['user_id']) ?>">
                        </fieldset>

                        <?php if ($user['user_type'] === 'customer'): ?>
                            <fieldset class="form-group">
                                <label class="form-label">Customer ID</label>
                                <input type="text" class="form-input" readonly
                                       value="<?= htmlspecialchars($user['customer_id']) ?>">
                            </fieldset>
                        <?php elseif ($user['user_type'] === 'owner'): ?>
                            <fieldset class="form-group">
                                <label class="form-label">Owner ID</label>
                                <input type="text" class="form-input" readonly
                                       value="<?= htmlspecialchars($user['owner_id']) ?>">
                            </fieldset>
                        <?php endif; ?>

                        <fieldset class="form-group">
                            <label class="form-label">National ID</label>
                            <input type="number" name="national_id" class="form-input" required
                                   value="<?= htmlspecialchars($user['national_id']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-input" required
                                   value="<?= htmlspecialchars($user['name']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="profile_photo" class="form-input"
                                   accept="image/jpeg,image/png,image/gif">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Flat No</label>
                            <input type="text" name="flat_no" class="form-input"
                                   value="<?= htmlspecialchars($user['flat_no']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Street</label>
                            <input type="text" name="street" class="form-input"
                                   value="<?= htmlspecialchars($user['street']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-input"
                                   value="<?= htmlspecialchars($user['city']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-input"
                                   value="<?= htmlspecialchars($user['postal_code']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-input"
                                   value="<?= htmlspecialchars($user['date_of_birth']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Email (for login/contact)</label>
                            <input type="email" name="email" class="form-input" required
                                   value="<?= htmlspecialchars($user['email']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Mobile</label>
                            <input type="number" name="mobile" class="form-input"
                                   value="<?= htmlspecialchars($user['mobile_number']) ?>">
                        </fieldset>

                        <fieldset class="form-group">
                            <label class="form-label">Telephone</label>
                            <input type="number" name="telephone" class="form-input"
                                   value="<?= htmlspecialchars($user['telephone_number']) ?>">
                        </fieldset>

                        <?php if ($user['user_type'] === 'owner'): ?>
                            <fieldset class="form-group">
                                <label class="form-label">Bank Name</label>
                                <input type="text" name="bank_name" class="form-input"
                                       value="<?= htmlspecialchars($user['bank_name']) ?>">
                            </fieldset>

                            <fieldset class="form-group">
                                <label class="form-label">Bank Branch</label>
                                <input type="text" name="bank_branch" class="form-input"
                                       value="<?= htmlspecialchars($user['bank_branch']) ?>">
                            </fieldset>

                            <fieldset class="form-group">
                                <label class="form-label">Account Number</label>
                                <input type="text" name="account_number" class="form-input"
                                       value="<?= htmlspecialchars($user['account_number']) ?>">
                            </fieldset>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="form-button">Update Profile</button>
                </section>
            </form>
        </div>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>