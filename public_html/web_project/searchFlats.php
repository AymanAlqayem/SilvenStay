<?php
require_once 'dbconfig.inc.php';

try {
    $pdo = getPDOConnection();

    // Initialize filter variables
    $location = isset($_GET['location']) ? $_GET['location'] : '';
    $min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
    $max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;
    $bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';
    $bathrooms = isset($_GET['bathrooms']) ? $_GET['bathrooms'] : '';
    $furnished = isset($_GET['furnished']) ? 1 : 0;
    $sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'monthly_rent';
    $sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

    // Build SQL query
    $sql = "SELECT f.flat_id, f.reference_number, f.monthly_rent, f.available_from, f.location, f.bedrooms, 
                   (SELECT photo_path FROM flat_photos fp WHERE fp.flat_id = f.flat_id LIMIT 1) AS photo_path
            FROM flats f
            WHERE f.status != 'rented'";
    $params = [];

    if ($location && $location !== 'Any') {
        $sql .= " AND f.location = :location";
        $params[':location'] = $location;
    }
    if ($min_price !== null) {
        $sql .= " AND f.monthly_rent >= :min_price";
        $params[':min_price'] = $min_price;
    }
    if ($max_price !== null) {
        $sql .= " AND f.monthly_rent <= :max_price";
        $params[':max_price'] = $max_price;
    }
    if ($bedrooms && $bedrooms !== 'Any') {
        if ($bedrooms === '3+') {
            $sql .= " AND f.bedrooms >= 3";
        } else {
            $sql .= " AND f.bedrooms = :bedrooms";
            $params[':bedrooms'] = (int)$bedrooms;
        }
    }
    if ($bathrooms && $bathrooms !== 'Any') {
        if ($bathrooms === '3+') {
            $sql .= " AND f.bathrooms >= 3";
        } else {
            $sql .= " AND f.bathrooms = :bathrooms";
            $params[':bathrooms'] = (int)$bathrooms;
        }
    }
    if ($furnished) {
        $sql .= " AND f.is_furnished = :furnished";
        $params[':furnished'] = 1;
    }

    $valid_columns = ['reference_number', 'monthly_rent', 'available_from', 'location', 'bedrooms'];
    if (!in_array($sort_column, $valid_columns)) {
        $sort_column = 'monthly_rent';
    }
    $sql .= " ORDER BY f.$sort_column $sort_order";

    // Prepare and execute query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch unique locations for dropdown
    $locations_query = "SELECT DISTINCT location FROM flats WHERE status != 'rented' ORDER BY location";
    $locations_stmt = $pdo->query($locations_query);
    $locations = $locations_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    die("Connection Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Perfect Flat</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'header.php' ?>
<?php include 'nav.php' ?>

<section class="content-wrapper">

    <main class="site-main">

        <section class="search-section">

            <h2>Find Your Perfect Flat</h2>

            <form action="searchFlats.php" method="GET" class="search-form">
                <section class="search-grid">

                    <section class="form-group">
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
                    </section>

                    <section class="form-group">
                        <label for="min_price">Min Price (£)</label>
                        <input type="number" name="min_price" id="min_price"
                               value="<?php echo htmlspecialchars($min_price ?? ''); ?>" min="0" step="50">
                    </section>

                    <section class="form-group">
                        <label for="max_price">Max Price (£)</label>
                        <input type="number" name="max_price" id="max_price"
                               value="<?php echo htmlspecialchars($max_price ?? ''); ?>" min="0" step="50">
                    </section>

                    <section class="form-group">
                        <label for="bedrooms">Bedrooms</label>
                        <select name="bedrooms" id="bedrooms">
                            <option value="Any" <?php echo $bedrooms === 'Any' ? 'selected' : ''; ?>>Any</option>
                            <option value="1" <?php echo $bedrooms === '1' ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo $bedrooms === '2' ? 'selected' : ''; ?>>2</option>
                            <option value="3+" <?php echo $bedrooms === '3+' ? 'selected' : ''; ?>>3+</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="bathrooms">Bathrooms</label>
                        <select name="bathrooms" id="bathrooms">
                            <option value="Any" <?php echo $bathrooms === 'Any' ? 'selected' : ''; ?>>Any</option>
                            <option value="1" <?php echo $bathrooms === '1' ? 'selected' : ''; ?>>1</option>
                            <option value="2" <?php echo $bathrooms === '2' ? 'selected' : ''; ?>>2</option>
                            <option value="3+" <?php echo $bathrooms === '3+' ? 'selected' : ''; ?>>3+</option>
                        </select>
                    </section>

                    <section class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="furnished" <?php echo $furnished ? 'checked' : ''; ?>>
                            Furnished Only
                        </label>
                    </section>

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
                        'available_from' => 'Availability',
                        'location' => 'Location',
                        'bedrooms' => 'Bedrooms',
                        'photo_path' => 'Photo'
                    ];
                    foreach ($columns as $col => $label) {
                        $new_order = ($sort_column === $col && $sort_order === 'ASC') ? 'desc' : 'asc';
                        $icon = ($sort_column === $col) ? ($sort_order === 'ASC' ? '▲' : '▼') : '';
                        echo "<th><a href='?sort=$col&order=$new_order&location=" . urlencode($location) .
                            "&min_price=" . urlencode($min_price ?? '') . "&max_price=" . urlencode($max_price ?? '') .
                            "&bedrooms=" . urlencode($bedrooms) . "&bathrooms=" . urlencode($bathrooms) .
                            "&furnished=$furnished' class='sort-link'>$label $icon</a></th>";
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php if (count($result) > 0): ?>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                            <td>£<?php echo number_format($row['monthly_rent'], 2); ?></td>
                            <td>
                                <?php
                                $avail_date = $row['available_from'] <= date('Y-m-d') ? 'Available Now' : date('d M Y', strtotime($row['available_from']));
                                echo htmlspecialchars($avail_date);
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['bedrooms']); ?></td>
                            <td>
                                <?php if ($row['photo_path']): ?>
                                    <a href="flatDetails.php?flat_id=<?php echo urlencode($row['flat_id']); ?>"
                                       target="_blank">
                                        <img src="flatImages/<?php echo htmlspecialchars($row['photo_path']); ?>"
                                             alt="Flat Photo"
                                             class="table-image">
                                    </a>
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="no-results">No flats found matching your criteria.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</section>
<?php include 'footer.php' ?>
</body>
</html>