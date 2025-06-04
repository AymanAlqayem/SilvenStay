<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SilvenStay For Flat Rent</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
<?php include 'header.php'; ?>

<div class="content-wrapper">
    <?php include 'nav.php'; ?>

    <main class="site-main">
        <?php if (isset($_SESSION['message'])): ?>
            <section class="message">
                <p><?= htmlspecialchars($_SESSION['message']) ?></p>
                <?php unset($_SESSION['message']); ?>
            </section>
        <?php endif; ?>

        <section class="promotional">
            <h2>Featured Properties</h2>

            <article class="promotion-grid">
                <figure class="promotion-card">
                    <figcaption>
                        <h3>Luxury Apartment in Downtown</h3>
                        <p>$1,200,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>

                <figure class="promotion-card">
                    <figcaption>
                        <h3>Modern Villa with Ocean View</h3>
                        <p>$2,500,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>

                <figure class="promotion-card">
                    <figcaption>
                        <h3>Cozy Studio in City Center</h3>
                        <p>$350,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>
            </article>
        </section>
    </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>