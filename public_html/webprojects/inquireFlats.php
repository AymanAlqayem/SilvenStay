<?php
session_start();

// Restrict access to managers
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php");
    exit;
}

// Database connection
require_once 'dbconfig.inc.php';

try {
    $pdo = getPDOConnection();

    // Initialize filter variables
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
    $location = isset($_GET['location']) ? $_GET['location'] : '';
    $future_date = isset($_GET['future_date']) ? $_GET['future_date'] : '';
    $owner_id = isset($_GET['owner_id']) ? (int)$_GET['owner_id'] : '';
    $customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'monthly_rent';
    $sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

    // Fetch locations for dropdown
    $locations_query = "SELECT DISTINCT location FROM flats ORDER BY location";
    $locations_stmt = $pdo->query($locations_query);
    $locations = $locations_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch owners for dropdown (user_type = 'owner')
    $owners_query = "SELECT user_id, name AS full_name 
                     FROM users WHERE user_type = 'owner' ORDER BY name";
    $owners_stmt = $pdo->query($owners_query);
    $owners = $owners_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch customers for dropdown (user_type = 'customer')
    $customers_query = "SELECT user_id, name AS full_name 
                       FROM users WHERE user_type = 'customer' ORDER BY name";
    $customers_stmt = $pdo->query($customers_query);
    $customers = $customers_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Build SQL query for all flats except rejected
    $sql = "SELECT f.flat_id, f.reference_number, f.monthly_rent, rh.start_date, rh.end_date, f.location,
                   o.name AS owner_name, o.user_id AS owner_id,
                   c.name AS customer_name, c.user_id AS customer_id,
                   f.status
            FROM flats f
            INNER JOIN users o ON f.owner_id = o.user_id
            LEFT JOIN rentals rh ON f.flat_id = rh.flat_id
            LEFT JOIN users c ON rh.customer_id = c.user_id
            WHERE f.status IN ('pending', 'approved', 'rented')";
    $params = [];

    // Apply filters
    if ($start_date) {
        $sql .= " AND (rh.start_date >= :start_date OR rh.start_date IS NULL)";
        $params[':start_date'] = $start_date;
    }
    if ($end_date) {
        $sql .= " AND (rh.end_date <= :end_date OR rh.end_date IS NULL)";
        $params[':end_date'] = $end_date;
    }
    if ($location && $location !== 'Any') {
        $sql .= " AND f.location = :location";
        $params[':location'] = $location;
    }
    if ($future_date) {
        $sql .= " AND (f.available_from <= :future_date OR rh.end_date <= :future_date OR rh.end_date IS NULL)";
        $params[':future_date'] = $future_date;
    }
    if ($owner_id) {
        $sql .= " AND f.owner_id = :owner_id";
        $params[':owner_id'] = $owner_id;
    }
    if ($customer_id) {
        $sql .= " AND rh.customer_id = :customer_id";
        $params[':customer_id'] = $customer_id;
    }
    if ($status && $status !== 'Any') {
        $sql .= " AND f.status = :status";
        $params[':status'] = $status;
    }

    // Sorting
    $valid_columns = ['reference_number', 'monthly_rent', 'start_date', 'end_date', 'location', 'owner_name', 'customer_name', 'status'];
    if (!in_array($sort_column, $valid_columns)) {
        $sort_column = 'monthly_rent';
    }
    $sql .= " ORDER BY $sort_column $sort_order";

    // Prepare and execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Connection Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flat Inquiry - Manager</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

<section class="content-wrapper">

    <main class="site-main">

        <section class="inquiry-section">

            <h2>Flat Inquiry</h2>

            <form action="inquireFlats.php" method="GET" class="inquiry-form">
                <section class="inquiry-grid">
                    <article class="form-group">
                        <label for="start_date">Rental Start Date</label>
                        <input type="date" name="start_date" id="start_date"
                               value="<?php echo htmlspecialchars($start_date); ?>">
                    </article>

                    <article class="form-group">
                        <label for="end_date">Rental End Date</label>
                        <input type="date" name="end_date" id="end_date"
                               value="<?php echo htmlspecialchars($end_date); ?>">
                    </article>

                    <article class="form-group">
                        <label for="location">Location</label>
                        <select name="location" id="location">
                            <option value="Any" <?php echo $location === 'Any' ? 'selected' : ''; ?>>Any Location
                            </option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $location === $loc ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($loc); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </article>

                    <article class="form-group">
                        <label for="future_date">Future Availability Date</label>
                        <input type="date" name="future_date" id="future_date"
                               value="<?php echo htmlspecialchars($future_date); ?>">
                    </article>

                    <article class="form-group">
                        <label for="owner_id">Owner</label>
                        <select name="owner_id" id="owner_id">
                            <option value="" <?php echo $owner_id === '' ? 'selected' : ''; ?>>Any Owner</option>
                            <?php foreach ($owners as $owner): ?>
                                <option value="<?php echo $owner['user_id']; ?>" <?php echo $owner_id == $owner['user_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($owner['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </article>

                    <article class="form-group">
                        <label for="customer_id">Customer</label>
                        <select name="customer_id" id="customer_id">
                            <option value="" <?php echo $customer_id === '' ? 'selected' : ''; ?>>Any Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['user_id']; ?>" <?php echo $customer_id == $customer['user_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($customer['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </article>

                    <article class="form-group">
                        <label for="status">Flat Status</label>
                        <select name="status" id="status">
                            <option value="Any" <?php echo $status === 'Any' ? 'selected' : ''; ?>>Any Status</option>
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending
                            </option>
                            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved
                            </option>
                            <option value="rented" <?php echo $status === 'rented' ? 'selected' : ''; ?>>Rented</option>
                        </select>
                    </article>

                    <section class="form-group submit-group">
                        <button type="submit" class="submit-button">Search Flats</button>
                    </section>
                </section>
            </form>

            <table class="results-table">
                <thead>
                <tr>
                    <?php
                    $columns = [
                        'reference_number' => 'Ref No.',
                        'monthly_rent' => 'Monthly Rent (£)',
                        'start_date' => 'Rental Start',
                        'end_date' => 'Rental End',
                        'location' => 'Location',
                        'owner_name' => 'Owner',
                        'customer_name' => 'Customer',
                        'status' => 'Status'
                    ];
                    foreach ($columns as $col => $label) {
                        $new_order = ($sort_column === $col && $sort_order === 'ASC') ? 'desc' : 'asc';
                        $icon = ($sort_column === $col) ? ($sort_order === 'ASC' ? '▲' : '▼') : '';
                        echo "<th><a href='?sort=$col&order=$new_order&start_date=" . urlencode($start_date) .
                            "&end_date=" . urlencode($end_date) . "&location=" . urlencode($location) .
                            "&future_date=" . urlencode($future_date) . "&owner_id=" . urlencode($owner_id) .
                            "&customer_id=" . urlencode($customer_id) . "&status=" . urlencode($status) .
                            "' class='sort-link'>$label $icon</a></th>";
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($result) > 0): ?>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td>
                                <a href="flatDetails.php?flat_id=<?php echo urlencode($row['flat_id']); ?>"
                                   class="flat-button" target="_blank">
                                    <?php echo htmlspecialchars($row['reference_number']); ?>
                                </a>
                            </td>
                            <td> $ <?php echo number_format($row['monthly_rent'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['start_date'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date'] ?: 'Ongoing'); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td>
                                <a href="userCard.php?user_id=<?php echo $row['owner_id']; ?>"
                                   class="owner-link" target="_blank">
                                    <?php echo htmlspecialchars($row['owner_name']); ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($row['customer_name']): ?>
                                    <a href="userCard.php?user_id=<?php echo $row['customer_id']; ?>"
                                       class="owner-link" target="_blank">
                                        <?php echo htmlspecialchars($row['customer_name']); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="no-results">No flats found matching your criteria.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>