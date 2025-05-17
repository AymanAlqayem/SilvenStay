<?php
require_once "dbconfig.inc.php";
$pdo = getPDOConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['productName']);
    $category = trim($_POST['productCategory']);
    $price = floatval($_POST['productPrice']);
    $quantity = intval($_POST['productQuantity']);
    $rating = floatval($_POST['productRating']);
    $description = trim($_POST['productDescription']);

    // Validate image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['productImage']['tmp_name'];
        $fileName = $_FILES['productImage']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check if the file is a JPEG
        if ($fileExtension === 'jpeg') {
            try {
                // Insert product first without image
                $query = "INSERT INTO products (product_name, category, price, quantity, rating, description) 
                    VALUES (:name, :category, :price, :quantity, :rating, :description)";

                $stmt = $pdo->prepare($query);

                $stmt->execute([
                    ':name' => $name,
                    ':category' => $category,
                    ':price' => $price,
                    ':quantity' => $quantity,
                    ':rating' => $rating,
                    ':description' => $description
                ]);

                // Get inserted ID
                $lastId = $pdo->lastInsertId();

                // Set new file name
                $destination = __DIR__ . "/images/{$lastId}.jpeg";

                // Move uploaded file
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    // Save image name to DB
                    $updateStmt = $pdo->prepare("UPDATE products SET image_name = :image WHERE product_id = :id");
                    $updateStmt->execute([
                        ':image' => "{$lastId}.jpeg",
                        ':id' => $lastId
                    ]);

                    echo "<p>✅ Product added successfully!</p>";
                } else {
                    echo "<p>❌ Failed to move uploaded file.</p>";
                }

            } catch (PDOException $e) {
            }
        } else {
            echo "<p>❌ Only JPEG images are allowed.</p>";
        }
    } else {
        echo "<p>❌ Please upload a valid JPEG image.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
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
        ➕ <a href="add.php">Add Product</a> |
        🔍 <a href="products.php#search">Search</a>
    </nav>

    <br>
    <hr>
</header>

<main>
    <section>
        <h2>Let's Add New Products !</h2>

        <figure>
            <img src="images/addNew.png" alt="New Product" width="450" height="200">
        </figure>

        <form action="add.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend><strong>🛍️ Add New Product</strong></legend>

                <p>
                    <label for="productName">Product Name:</label><br>
                    <input type="text" name="productName" id="productName" placeholder="Enter product name" required
                           aria-label="Product name">
                </p>

                <p>
                    <label for="productCategory">Product Category:</label><br>
                    <select name="productCategory" id="productCategory" required aria-label="Product category">
                        <option value="">-- Choose Category --</option>
                        <option value="Formal Shirts">Formal Shirts 👔</option>
                        <option value="Casual T-Shirts">Casual T-Shirts 👕</option>
                        <option value="Jeans">Jeans 👖</option>
                        <option value="Outerwear">Outerwear 🧥</option>
                        <option value="Activewear">Activewear 🏃</option>
                    </select>
                </p>

                <p>
                    <label for="productPrice">Product Price (USD):</label><br>
                    <input type="number" name="productPrice" id="productPrice" placeholder="Enter product price" min="1"
                           step="1" required aria-label="Product price">
                </p>

                <p>
                    <label for="productQuantity">Product Quantity:</label><br>
                    <input type="number" name="productQuantity" id="productQuantity" min="1" max="1000" step="1"
                           value="1" required aria-label="Product quantity">
                </p>

                <p>
                    <label for="productRating">Product Rating (0.0 – 5.0):</label><br>
                    <input type="number" name="productRating" id="productRating" min="0" max="5" step="0.1" value="1.0"
                           required aria-label="Product rating">
                </p>

                <p>
                    <label for="productDescription">Product Description:</label><br>
                    <textarea name="productDescription" id="productDescription" rows="4"
                              placeholder="Describe the product in a few lines..." required
                              aria-label="Product description"></textarea>
                </p>

                <p>
                    <label for="productImage">Upload Product Image (JPEG only):</label><br>
                    <input type="file" name="productImage" id="productImage" accept=".jpeg" required
                           aria-label="Product image upload">
                </p>

                <p>
                    <button type="submit">➕ Add Product</button>
                </p>
            </fieldset>
        </form>
    </section>
</main>

<hr>

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

