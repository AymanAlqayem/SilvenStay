<?php
session_start();
require_once 'dbconfig.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    die("Only owners can list flats.");
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

// Ensure flatImages directory exists
$imageDir = 'flatImages/';

if (!is_dir($imageDir)) {
    mkdir($imageDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Flat Details
    $location = $_POST['location'] ?? '';
    $address = $_POST['address'] ?? '';
    $monthly_rent = $_POST['monthly_rent'] ?? '';
    $available_from = $_POST['available_from'] ?? '';
    $available_to = $_POST['available_to'] ?? '';
    $bedrooms = $_POST['bedrooms'] ?? '';
    $bathrooms = $_POST['bathrooms'] ?? '';
    $size_sqm = $_POST['size_sqm'] ?? '';
    $rental_conditions = $_POST['rental_conditions'] ?? '';
    $is_furnished = isset($_POST['is_furnished']) ? 1 : 0;
    $has_heating = isset($_POST['has_heating']) ? 1 : 0;
    $has_ac = isset($_POST['has_ac']) ? 1 : 0;
    $has_access_control = isset($_POST['has_access_control']) ? 1 : 0;
    $has_parking = isset($_POST['has_parking']) ? 1 : 0;
    $has_backyard = isset($_POST['has_backyard']) ? 1 : 0;
    $has_playground = isset($_POST['has_playground']) ? 1 : 0;
    $has_storage = isset($_POST['has_storage']) ? 1 : 0;

    // Step 2: Marketing Info (Optional)
    $marketing_title = $_POST['marketing_title'] ?? '';
    $marketing_description = $_POST['marketing_description'] ?? '';
    $nearby_url = $_POST['nearby_url'] ?? '';

    // Step 3: Preview Timetables
    $appointment_dates = $_POST['appointment_dates'] ?? [];
    $appointment_times = $_POST['appointment_times'] ?? [];
    $telephone_numbers = $_POST['telephone_numbers'] ?? [];

    // Validate Step 1
    if (!$location || !$address || !$monthly_rent || !$available_from || !$bedrooms || !$bathrooms || !$size_sqm) {
        $errors[] = "All flat details are required.";
    }
    if (!is_numeric($monthly_rent) || $monthly_rent <= 0) {
        $errors[] = "Monthly rent must be a positive number.";
    }
    if (!is_numeric($bedrooms) || $bedrooms < 0) {
        $errors[] = "Bedrooms must be a non-negative number.";
    }
    if (!is_numeric($bathrooms) || $bathrooms < 0) {
        $errors[] = "Bathrooms must be a non-negative number.";
    }
    if (!is_numeric($size_sqm) || $size_sqm <= 0) {
        $errors[] = "Size must be a positive number.";
    }
    if (strtotime($available_from) === false) {
        $errors[] = "Invalid available from date.";
    }
    if ($available_to && strtotime($available_to) === false) {
        $errors[] = "Invalid available to date.";
    } elseif ($available_to && strtotime($available_from) >= strtotime($available_to)) {
        $errors[] = "Available to date must be after available from date.";
    }

    // Validate Photos
    $photos = $_FILES['photos'] ?? [];
    $photo_paths = [];
    if (empty($photos['name'][0]) && empty($errors)) {
        $errors[] = "At least three photos are required.";
    } else {
        foreach ($photos['name'] as $index => $name) {
            if ($photos['error'][$index] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($ext, ['png'])) {
                    $errors[] = "Photo '$name' must be PNG.";
                } elseif ($photos['size'][$index] > 5 * 1024 * 1024) {
                    $errors[] = "Photo '$name' exceeds 5MB.";
                }
            }
        }
        if (count($photos['name']) < 3) {
            $errors[] = "At least three valid photos must be uploaded.";
        }
    }

    // Validate Step 3
    if (count($appointment_dates) < 1) {
        $errors[] = "At least one availability slot is required.";
    } else {
        foreach ($appointment_dates as $index => $date) {
            if (!strtotime($date) || !preg_match('/^\d{2}:\d{2}$/', $appointment_times[$index]) || !preg_match('/^\d{10,15}$/', $telephone_numbers[$index])) {
                $errors[] = "Invalid availability slot at entry " . ($index + 1) . ".";
            }
        }
    }

    // Validate Marketing Info URL (if provided)
    if ($nearby_url && !filter_var($nearby_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Invalid nearby URL.";
    }

    if (empty($errors)) {
        try {
            $pdo = getPDOConnection();
            $pdo->beginTransaction();

            // Generate temporary reference_number (e.g., TMP001)
            do {
                $temp_ref = 'TMP' . sprintf("%03d", mt_rand(1, 999));
                $check_ref = $pdo->prepare("SELECT flat_id FROM flats WHERE reference_number = :reference_number");
                $check_ref->execute(['reference_number' => $temp_ref]);
            } while ($check_ref->fetch());

            // Insert flat
            $flat_stmt = $pdo->prepare("
                INSERT INTO flats (owner_id, location, address, monthly_rent, bedrooms, bathrooms, size_sqm, is_furnished, has_heating, has_ac, has_access_control, has_parking, has_backyard, has_playground, has_storage, rental_conditions, status, available_from, available_to, reference_number)
                VALUES (:owner_id, :location, :address, :monthly_rent, :bedrooms, :bathrooms, :size_sqm, :is_furnished, :has_heating, :has_ac, :has_access_control, :has_parking, :has_backyard, :has_playground, :has_storage, :rental_conditions, 'pending', :available_from, :available_to, :reference_number)
            ");
            $flat_stmt->execute([
                'owner_id' => $userId,
                'location' => $location,
                'address' => $address,
                'monthly_rent' => $monthly_rent,
                'bedrooms' => $bedrooms,
                'bathrooms' => $bathrooms,
                'size_sqm' => $size_sqm,
                'is_furnished' => $is_furnished,
                'has_heating' => $has_heating,
                'has_ac' => $has_ac,
                'has_access_control' => $has_access_control,
                'has_parking' => $has_parking,
                'has_backyard' => $has_backyard,
                'has_playground' => $has_playground,
                'has_storage' => $has_storage,
                'rental_conditions' => $rental_conditions,
                'available_from' => $available_from,
                'available_to' => $available_to ?: NULL,
                'reference_number' => $temp_ref
            ]);
            $flat_id = $pdo->lastInsertId();

            // Process and store photos
            foreach ($photos['name'] as $index => $name) {
                if ($photos['error'][$index] === UPLOAD_ERR_OK) {
                    $ext = 'png';
                    $photo_name = "$flat_id." . ($index + 1) . '.' . $ext;
                    $destination = $imageDir . $photo_name;
                    if (move_uploaded_file($photos['tmp_name'][$index], $destination)) {
                        $photo_paths[] = $photo_name;
                        // Insert into flat_photos
                        $photo_stmt = $pdo->prepare("INSERT INTO flat_photos (flat_id, photo_path) VALUES (:flat_id, :photo_path)");
                        $photo_stmt->execute(['flat_id' => $flat_id, 'photo_path' => $photo_name]);
                    } else {
                        $errors[] = "Failed to upload photo '$name'.";
                    }
                }
            }
            if (count($photo_paths) < 3) {
                throw new Exception("At least three valid photos must be uploaded.");
            }

            // Insert marketing info (if provided)
            if ($marketing_title || $marketing_description || $nearby_url) {
                $marketing_stmt = $pdo->prepare("
                    INSERT INTO flat_marketing (flat_id, title, description, url)
                    VALUES (:flat_id, :title, :description, :url)
                ");
                $marketing_stmt->execute([
                    'flat_id' => $flat_id,
                    'title' => $marketing_title,
                    'description' => $marketing_description,
                    'url' => $nearby_url
                ]);
            }

            // Insert availability slots
            $slot_stmt = $pdo->prepare("
                INSERT INTO flat_availability_slots (flat_id, appointment_date, appointment_time, telephone_number, is_booked)
                VALUES (:flat_id, :appointment_date, :appointment_time, :telephone_number, FALSE)
            ");
            foreach ($appointment_dates as $index => $date) {
                $slot_stmt->execute([
                    'flat_id' => $flat_id,
                    'appointment_date' => $date,
                    'appointment_time' => $appointment_times[$index],
                    'telephone_number' => $telephone_numbers[$index]
                ]);
            }

            // Notify manager
            $manager_id = 9; // Hardcoded for simplicity (Grace Lee)
            $owner_stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = :user_id");
            $owner_stmt->execute(['user_id' => $userId]);
            $owner = $owner_stmt->fetch(PDO::FETCH_ASSOC);

            $message_stmt = $pdo->prepare("
                INSERT INTO messages (user_id, title, message_body, sender, sent_date, message_type, flat_id)
                VALUES (:user_id, :title, :message_body, :sender, NOW(), 'approval', :flat_id)
            ");
            $message_stmt->execute([
                'user_id' => $manager_id,
                'title' => "New Flat Submission",
                'message_body' => "A new flat (ID: $flat_id, Ref: $temp_ref) has been submitted by {$owner['name']} for approval.",
                'sender' => "System on behalf of {$owner['name']}",
                'flat_id' => $flat_id
            ]);

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error: " . htmlspecialchars($e->getMessage());
            // Clean up uploaded photos on failure
            foreach ($photo_paths as $path) {
                $full_path = $imageDir . $path;
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Offer Flat for Rent | SilvenStay</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<fieldset class="content-wrapper">
    <main class="site-main">
        <h2>Offer Flat for Rent</h2>

        <?php if (!empty($errors)): ?>
            <aside class="error-notice">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        <?php endif; ?>

        <?php if ($success): ?>
            <aside class="success-notice">
                <p>Your flat has been submitted for manager approval. You will be notified once reviewed.</p>
            </aside>
        <?php else: ?>
            <form id="flat-form" method="POST" enctype="multipart/form-data">
                <!-- Step 1: Flat Details -->
                <fieldset class="flat-form-step active" id="step-1">
                    <h3>Step 1: Flat Details</h3>
                    <fieldset class="input-container">
                        <label for="location">Location:</label>
                        <input type="text" name="location" id="location"
                               value="<?php echo htmlspecialchars($location ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address"
                               value="<?php echo htmlspecialchars($address ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="monthly_rent">Monthly Rent ($):</label>
                        <input type="number" name="monthly_rent" id="monthly_rent" step="0.01"
                               value="<?php echo htmlspecialchars($monthly_rent ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="available_from">Available From:</label>
                        <input type="date" name="available_from" id="available_from"
                               value="<?php echo htmlspecialchars($available_from ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="available_to">Available To (Optional):</label>
                        <input type="date" name="available_to" id="available_to"
                               value="<?php echo htmlspecialchars($available_to ?? ''); ?>">
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="bedrooms">Bedrooms:</label>
                        <input type="number" name="bedrooms" id="bedrooms"
                               value="<?php echo htmlspecialchars($bedrooms ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="bathrooms">Bathrooms:</label>
                        <input type="number" name="bathrooms" id="bathrooms"
                               value="<?php echo htmlspecialchars($bathrooms ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="size_sqm">Size (sqm):</label>
                        <input type="number" name="size_sqm" id="size_sqm"
                               value="<?php echo htmlspecialchars($size_sqm ?? ''); ?>" required>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="rental_conditions">Rental Conditions:</label>
                        <textarea name="rental_conditions"
                                  id="rental_conditions"><?php echo htmlspecialchars($rental_conditions ?? ''); ?></textarea>
                    </fieldset>

                    <!-- Inside Step 1: Flat Details -->
                    <fieldset class="checkbox-container">
                        <legend>Amenities</legend>
                        <fieldset class="checkbox-grid">
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="is_furnished"
                                       id="is_furnished" <?php echo isset($_POST['is_furnished']) ? 'checked' : ''; ?>>
                                <label for="is_furnished">Furnished</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_heating"
                                       id="has_heating" <?php echo isset($_POST['has_heating']) ? 'checked' : ''; ?>>
                                <label for="has_heating">Heating</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_ac"
                                       id="has_ac" <?php echo isset($_POST['has_ac']) ? 'checked' : ''; ?>>
                                <label for="has_ac">Air Conditioning</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_access_control"
                                       id="has_access_control" <?php echo isset($_POST['has_access_control']) ? 'checked' : ''; ?>>
                                <label for="has_access_control">Access Control</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_parking"
                                       id="has_parking" <?php echo isset($_POST['has_parking']) ? 'checked' : ''; ?>>
                                <label for="has_parking">Parking</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_backyard"
                                       id="has_backyard" <?php echo isset($_POST['has_backyard']) ? 'checked' : ''; ?>>
                                <label for="has_backyard">Backyard</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_playground"
                                       id="has_playground" <?php echo isset($_POST['has_playground']) ? 'checked' : ''; ?>>
                                <label for="has_playground">Playground</label>
                            </fieldset>
                            <fieldset class="checkbox-item">
                                <input type="checkbox" name="has_storage"
                                       id="has_storage" <?php echo isset($_POST['has_storage']) ? 'checked' : ''; ?>>
                                <label for="has_storage">Storage</label>
                            </fieldset>
                        </fieldset>
                    </fieldset>

                    <fieldset class="input-container">
                        <label for="photos">Photos (min 3, PNG only, max 5MB):</label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/png" required>
                        <?php if (!empty($errors) && !empty($photos['name'][0])): ?>
                            <p>Note: Please re-upload photos due to validation errors.</p>
                        <?php endif; ?>
                    </fieldset>
                    <nav class="step-controls">
                        <button type="button" onclick="nextStep(2)">Next</button>
                    </nav>
                </fieldset>

                <!-- Step 2: Marketing Info -->
                <fieldset class="flat-form-step" id="step-2">
                    <h3>Step 2: Marketing Info (Optional)</h3>
                    <fieldset class="input-container">
                        <label for="marketing_title">Title:</label>
                        <input type="text" name="marketing_title" id="marketing_title"
                               value="<?php echo htmlspecialchars($marketing_title ?? ''); ?>">
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="marketing_description">Description:</label>
                        <textarea name="marketing_description"
                                  id="marketing_description"><?php echo htmlspecialchars($marketing_description ?? ''); ?></textarea>
                    </fieldset>
                    <fieldset class="input-container">
                        <label for="nearby_url">Nearby URL:</label>
                        <input type="url" name="nearby_url" id="nearby_url"
                               value="<?php echo htmlspecialchars($nearby_url ?? ''); ?>">
                    </fieldset>
                    <nav class="step-controls">
                        <button type="button" onclick="prevStep(1)">Previous</button>
                        <button type="button" onclick="nextStep(3)">Next</button>
                    </nav>
                </fieldset>

                <!-- Step 3: Availability Slots -->
                <fieldset class="flat-form-step" id="step-3">
                    <h3>Step 3: Availability Slots</h3>
                    <fieldset id="slot-entries">
                        <?php
                        if (!empty($appointment_dates)) {
                            foreach ($appointment_dates as $index => $date) {
                                ?>
                                <fieldset class="slot-entry">
                                    <fieldset class="input-container">
                                        <label for="appointment_dates_<?php echo $index; ?>">Date:</label>
                                        <input type="date" name="appointment_dates[]"
                                               id="appointment_dates_<?php echo $index; ?>"
                                               value="<?php echo htmlspecialchars($date); ?>" required>
                                    </fieldset>
                                    <fieldset class="input-container">
                                        <label for="appointment_times_<?php echo $index; ?>">Time:</label>
                                        <input type="time" name="appointment_times[]"
                                               id="appointment_times_<?php echo $index; ?>"
                                               value="<?php echo htmlspecialchars($appointment_times[$index] ?? ''); ?>"
                                               required>
                                    </fieldset>
                                    <fieldset class="input-container">
                                        <label for="telephone_numbers_<?php echo $index; ?>">Telephone Number:</label>
                                        <input
                                                type="text"
                                                name="telephone_numbers[]"
                                                id="telephone_numbers_<?php echo $index; ?>"
                                                value="<?php echo htmlspecialchars($telephone_numbers[$index] ?? ''); ?>"
                                                required
                                                pattern="\d{10}"
                                                maxlength="10"
                                                inputmode="numeric"
                                                title="Please enter exactly 10 digits">

                                    </fieldset>
                                    <span class="slot-remove" onclick="removeSlot(this)">Remove</span>
                                </fieldset>
                                <?php
                            }
                        } else {
                            ?>
                            <fieldset class="slot-entry">
                                <fieldset class="input-container">
                                    <label for="appointment_dates_0">Date:</label>
                                    <input type="date" name="appointment_dates[]" id="appointment_dates_0" required>
                                </fieldset>
                                <fieldset class="input-container">
                                    <label for="appointment_times_0">Time:</label>
                                    <input type="time" name="appointment_times[]" id="appointment_times_0" required>
                                </fieldset>
                                <fieldset class="input-container">
                                    <label for="telephone_numbers_0">Telephone Number:</label>
                                    <input type="text" name="telephone_numbers[]" id="telephone_numbers_0"
                                           pattern="\d{10,15}" required>
                                </fieldset>
                                <span class="slot-remove" onclick="removeSlot(this)">Remove</span>
                            </fieldset>
                            <?php
                        }
                        ?>
                    </fieldset>
                    <button type="button" class="slot-add-button" onclick="addSlot()">Add Another Slot</button>
                    <nav class="step-controls">
                        <button type="button" onclick="prevStep(2)">Previous</button>
                        <button type="submit">Submit</button>
                    </nav>
                </fieldset>
            </form>

            <script>
                function nextStep(step) {
                    document.querySelector('.flat-form-step.active').classList.remove('active');
                    document.getElementById('step-' + step).classList.add('active');
                }

                function prevStep(step) {
                    document.querySelector('.flat-form-step.active').classList.remove('active');
                    document.getElementById('step-' + step).classList.add('active');
                }

                function addSlot() {
                    const container = document.getElementById('slot-entries');
                    const entryCount = document.querySelectorAll('.slot-entry').length;
                    const entry = document.createElement('fieldset');
                    entry.className = 'slot-entry';
                    entry.innerHTML = `
                        <fieldset class="input-container">
                            <label for="appointment_dates_${entryCount}">Date:</label>
                            <input type="date" name="appointment_dates[]" id="appointment_dates_${entryCount}" required>
                        </fieldset>
                        <fieldset class="input-container">
                            <label for="appointment_times_${entryCount}">Time:</label>
                            <input type="time" name="appointment_times[]" id="appointment_times_${entryCount}" required>
                        </fieldset>
                        <fieldset class="input-container">
                            <label for="telephone_numbers_${entryCount}">Telephone Number:</label>
                            <input type="text" name="telephone_numbers[]" id="telephone_numbers_${entryCount}" pattern="\d{10,15}" required>
                        </fieldset>
                        <span class="slot-remove" onclick="removeSlot(this)">Remove</span>
                    `;
                    container.appendChild(entry);
                }

                function removeSlot(element) {
                    if (document.querySelectorAll('.slot-entry').length > 1) {
                        element.parentElement.remove();
                    }
                }
            </script>
        <?php endif; ?>
    </main>
</fieldset>

<?php include 'footer.php'; ?>
</body>
</html>