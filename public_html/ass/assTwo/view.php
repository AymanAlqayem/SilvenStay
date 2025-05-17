<?php
require_once 'Product.php';
require_once 'dbconfig.inc.php';

$pdo = getPDOConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT product_id, product_name, category, description, price, rating, image_name, quantity
    FROM products WHERE product_id = :id
");
$stmt->execute([':id' => $id]);
$productData = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details - LEVON</title>
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
        <?php
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
            echo $product->displayProductPage(); // Display product details
        } else {
            echo "
            <article>
                <h1>Invalid Product ID</h1>
                <p>The product with ID <strong>$id</strong> could not be found.</p>
            </article>
            ";
        }
        ?>
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
