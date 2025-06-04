<?php
require_once "dbconfig.inc.php";
require_once "Product.php";

try {
    // Establish PDO connection
    $pdo = getPDOConnection();

    // Set up pagination
    $productsPerPage = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $productsPerPage;

    // Define base queries
    $query = "SELECT * FROM products";
    $countQuery = "SELECT COUNT(*) FROM products";
    $conditions = [];
    $params = [];

    // Process form input
    $productName = isset($_POST['productName']) ? trim($_POST['productName']) : '';
    $productPrice = isset($_POST['productPrice']) ? trim($_POST['productPrice']) : '';
    $productCategory = isset($_POST['productCategory']) ? trim($_POST['productCategory']) : '';

    // Build WHERE clause for search
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
            $countQuery .= " WHERE " . implode(" AND ", $conditions);
        }
    }

    // Count total products for pagination
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $productsPerPage);

    // Add pagination to query
    $query .= " ORDER BY product_id ASC LIMIT :offset, :limit";
    $stmt = $pdo->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$productsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch results
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include_once "header.php"; ?>


<?php include_once "nav.php"; ?>


<main class="container">
    <!-- Search panel -->
    <aside class="search-panel" id="search">
        <section>
            <h2>🛍️ Explore & Find Your Perfect Fit</h2>
            <p>Use the form below to filter our catalog!</p>
            <form method="POST" action="products.php">
                <fieldset>
                    <legend>🔍 Advanced Product Search</legend>
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
        </section>
    </aside>

    <!-- Product grid with pagination -->
    <section class="product-grid">
        <section class="product-items">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <section class="product-image-container">
                            <img src="images/<?php echo htmlspecialchars($product->getImageName() ?: 'default.jpg'); ?>"
                                 alt="Product Image">
                        </section>
                        <section class="product-info">
                            <h3 class="product-id">🆔 <?php echo htmlspecialchars($product->getProductId()); ?></h3>
                            <span class="product-name"
                                  tabindex="0"><?php echo htmlspecialchars($product->getProductName() ?: 'Unknown Product'); ?></span>
                            <section class="tooltip">
                                <h2 class="<?php echo $product->getQuantity() <= 5 ? 'low-stock' : 'normal-stock'; ?>">
                                    Quantity: <?php echo htmlspecialchars($product->getQuantity() ?: 0); ?>
                                </h2>
                                <p><?php echo htmlspecialchars($product->getDescription() ?: 'No description available.'); ?></p>
                            </section>
                            <span class="category-badge">
                                    <?php echo htmlspecialchars($product->getCategory() ?: 'Uncategorized'); ?>
                                </span>
                            <span class="price">$<?php echo htmlspecialchars($product->getPrice() ?: '0.00'); ?></span>
                            <nav class="action-buttons">
                                <a href="view.php?id=<?php echo htmlspecialchars($product->getProductId()); ?>"
                                   class="view-button">View</a>
                                <a href="cart.php?action=add&id=<?php echo htmlspecialchars($product->getProductId()); ?>"
                                   class="add-to-cart-button">Add to Cart</a>
                            </nav>
                        </section>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found matching your search. Try adjusting your filters or <a href="products.php">view all
                        products</a>.</p>
            <?php endif; ?>
        </section>
        <nav class="pagination">
            <?php if ($page > 1): ?>
                <a href="products.php?page=<?php echo $page - 1; ?>" class="previous">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="products.php?page=<?php echo $page + 1; ?>" class="next">Next</a>
            <?php endif; ?>
        </nav>
    </section>
</main>

<?php include_once "footer.php"; ?>
</body>
</html>