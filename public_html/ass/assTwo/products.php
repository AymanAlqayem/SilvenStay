<?php
require_once "dbconfig.inc.php";
require_once "Product.php";

try {
    // Establish the PDO connection
    $pdo = getPDOConnection();

    // Base query
    $query = "SELECT * FROM products";
    $conditions = [];
    $params = [];

    // Get form input
    $productName = isset($_POST['productName']) ? trim($_POST['productName']) : '';
    $productPrice = isset($_POST['productPrice']) ? trim($_POST['productPrice']) : '';
    $productCategory = isset($_POST['productCategory']) ? trim($_POST['productCategory']) : '';

    // Build WHERE clause if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($productName !== '') {
            $conditions[] = "product_name LIKE :productName";
            $params[':productName'] = '%' . $productName . '%';
        }

        if ($productCategory !== '') {
            $conditions[] = "TRIM(category) = :productCategory";
            $params[':productCategory'] = $productCategory;
        }

        if ($productPrice !== '' && is_numeric($productPrice)) {
            $conditions[] = "price <= :productPrice";
            $params[':productPrice'] = $productPrice;
        }

        if (count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
    }

    // Execute prepared statement
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert rows to Product objects
    $products = [];
    foreach ($rows as $row) {
        $products[] = new Product(
            $row['product_id'],
            $row['product_name'],
            $row['category'],
            $row['description'],
            $row['price'],
            $row['rating'],
            $row['image_name'],
            $row['quantity']
        );
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LEVON - Wear Your Style, Anywhere</title>
</head>
<body>

<header>
    <h1>👕👗 WELCOME TO LEVON 👠🧥</h1>
    <h2>"Wear Your Style, Anywhere"</h2>

    <figure>
        <img src="images/logo.png" alt="Levon logo" width="200" height="180">
        <figcaption><strong>Your Fashion Destination</strong></figcaption>
    </figure>

    <hr>
    <br><br>

    <nav>
        🏠 <a href="products.php">Home</a> |
        ➕ <a href="add.php">Add Product</a> |
        🔍 <a href="#search">Search</a>
    </nav>
    <br>
    <hr>
</header>

<main>
    <section id="search">
        <h2>🛍️ Explore & Find Your Perfect Fit</h2>
        <p>Use the form below to filter our catalog!</p>

        <figure>
            <img src="images/search.png" alt="search" width="320" height="220">
        </figure>
        <br>

        <section>
            <fieldset>
                <legend>🔍 Advanced Product Search</legend>

                <form method="POST" action="products.php">
                    <fieldset>
                        <legend>✨ Search by Your Preferences</legend>

                        <p>
                            <label for="productName">🧾 Product Name:</label><br>
                            <input type="text" name="productName" id="productName"
                                   placeholder="e.g., Classic Denim Jacket"
                                   value="<?php echo htmlspecialchars($productName); ?>">
                        </p>

                        <p>
                            <label for="productPrice">💰 Max Price:</label><br>
                            <input type="text" name="productPrice" id="productPrice" placeholder="e.g., 80"
                                   value="<?php echo htmlspecialchars($productPrice); ?>">
                        </p>

                        <p>
                            <label for="productCategory">📂 Category:</label><br>
                            <select name="productCategory" id="productCategory">
                                <option value="">-- Choose Category --</option>
                                <option value="Formal Shirts" <?php if ($productCategory === 'Formal Shirts') echo 'selected'; ?>>
                                    Formal Shirts
                                </option>
                                <option value="Casual T-Shirts" <?php if ($productCategory === 'Casual T-Shirts') echo 'selected'; ?>>
                                    Casual T-Shirts
                                </option>
                                <option value="Jeans" <?php if ($productCategory === 'Jeans') echo 'selected'; ?>>Jeans
                                </option>
                                <option value="Outerwear" <?php if ($productCategory === 'Outerwear') echo 'selected'; ?>>
                                    Outerwear
                                </option>
                                <option value="Activewear" <?php if ($productCategory === 'Activewear') echo 'selected'; ?>>
                                    Activewear
                                </option>
                            </select>
                        </p>

                        <p>
                            <button type="submit">🔎 Filter Products</button>
                        </p>
                    </fieldset>
                </form>

                <br>

                <section>
                    <table border="1" cellpadding="10" cellspacing="0" width="100%">
                        <caption>🛒 Product Listings</caption>
                        <thead>
                        <tr>
                            <th width="200">📷 Product Image</th>
                            <th width="80">🆔 Product ID</th>
                            <th width="150">🔖 Product Name</th>
                            <th width="150">🏷️ Category</th>
                            <th width="80">💵 Price</th>
                            <th width="80">📦 Quantity</th>
                            <th width="120">⚙️ Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $product): ?>
                                <?php echo $product->displayInTable(); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" align="center">No products found matching your search.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="7" align="center"><i>🔄 Last updated: <?php echo date("F d, Y"); ?></i></td>
                        </tr>
                        </tfoot>

                    </table>
                </section>
            </fieldset>
        </section>

    </section>
</main>

<hr>
<br>

<footer>
    <address>
        <strong>📍 Store Address:</strong> Palestine, Ramallah, Israa Complex, second floor |
        <a href="tel:+972594276335">📞 Customer Support</a> |
        <a href="mailto:nabilayman021@gmail.com">📧 Email</a> |
        <a href="contactUs.php">📬 Contact Us</a>
    </address>
</footer>

</body>
</html>