<?php
require_once "dbconfig.inc.php";
require_once "Product.php";
$pdo = getPDOConnection();

$product = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :id");
    $stmt->execute([':id' => $id]);
    $productData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($productData) {
        $product = new Product(
            $productData['product_id'],
            $productData['product_name'],
            $productData['category'],
            $productData['description'],
            $productData['price'],
            $productData['rating'],
            $productData['image_name'],
            $productData['quantity']
        );
    } else {
        die("Product not found.");
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $description = trim($_POST['description']);

    // Update product
    $stmt = $pdo->prepare("UPDATE products SET price = :price, quantity = :quantity, description = :description WHERE product_id = :id");
    $stmt->execute([':price' => $price, ':quantity' => $quantity, ':description' => $description, ':id' => $id]);

    // Handle image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['productImage']['tmp_name'];
        if (strtolower(mime_content_type($tmpPath)) === 'image/jpeg') {
            $imagePath = "images/{$id}.jpeg";
            move_uploaded_file($tmpPath, $imagePath);
            $stmt = $pdo->prepare("UPDATE products SET image_name = :img WHERE product_id = :id");
            $stmt->execute([':img' => "{$id}.jpeg", ':id' => $id]);
        }
    }

    header("Location: products.php?message=updated");
    exit;
}
?>

<?php if ($product): ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Product</title>
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
        <br>

        <nav>
            🏠 <a href="products.php">Home</a> |
            ➕ <a href="products.php">Add Product</a> |
            🔍 <a href="products.php#search">Search</a>
        </nav>

        <br>
        <hr>
    </header>

    <main>
        <section>
            <figure>
                <img src="images/EditProduct.png" alt="Edit Product" width="350" height="200">
            </figure>
            <form action="edit.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend><strong>Product Record:</strong></legend>

                    <input type="hidden" name="id" value="<?= htmlspecialchars($product->getProductId()) ?>">

                    <table cellpadding="6">
                        <tr>
                            <td><label for="productId">Product ID:</label></td>
                            <td><input type="text" id="productId" name="productId"
                                       value="<?= htmlspecialchars($product->getProductId()) ?>" disabled size="40">
                            </td>
                        </tr>

                        <tr>
                            <td><label for="productName">Product Name:</label></td>
                            <td><input type="text" id="productName" name="productName"
                                       value="<?= htmlspecialchars($product->getProductName()) ?>" disabled size="40">
                            </td>
                        </tr>

                        <tr>
                            <td><label for="productCategory">Category:</label></td>
                            <td>
                                <select id="productCategory" name="productCategory" disabled>
                                    <option value="Formal Shirts" <?= $product->getCategory() == 'Formal Shirts' ? 'selected' : '' ?>>
                                        Formal Shirts
                                    </option>
                                    <option value="Casual T-Shirts" <?= $product->getCategory() == 'Casual T-Shirts' ? 'selected' : '' ?>>
                                        Casual T-Shirts
                                    </option>
                                    <option value="Jeans" <?= $product->getCategory() == 'Jeans' ? 'selected' : '' ?>>
                                        Jeans
                                    </option>
                                    <option value="Outerwear" <?= $product->getCategory() == 'Outerwear' ? 'selected' : '' ?>>
                                        Outerwear
                                    </option>
                                    <option value="Activewear" <?= $product->getCategory() == 'Activewear' ? 'selected' : '' ?>>
                                        Activewear
                                    </option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td><label for="price">Price:</label></td>
                            <td><input type="number" id="price" name="price" step="0.01"
                                       value="<?= htmlspecialchars($product->getPrice()) ?>" required></td>
                        </tr>

                        <tr>
                            <td><label for="quantity">Quantity:</label></td>
                            <td><input type="number" id="quantity" name="quantity"
                                       value="<?= htmlspecialchars($product->getQuantity()) ?>" required></td>
                        </tr>

                        <tr>
                            <td><label for="rating">Rating:</label></td>
                            <td><input type="number" id="rating" name="rating" min="1" max="5"
                                       value="<?= htmlspecialchars($product->getRating()) ?>" disabled></td>
                        </tr>

                        <tr>
                            <td><label for="description">Description:</label></td>
                            <td><textarea id="description" name="description" rows="4"
                                          cols="38"><?= htmlspecialchars($product->getDescription()) ?></textarea></td>
                        </tr>

                        <tr>
                            <td><label for="productImage">Product Photo:</label></td>
                            <td><input type="file" id="productImage" name="productImage" accept=".jpeg"></td>
                        </tr>

                        <tr>
                            <td>
                                <button type="submit">💾 Update</button>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </section>
        <br>
    </main>

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

<?php endif; ?>