<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SilvenStay For Flat Rent</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="content-wrapper">
    <?php include 'nav.php'; ?>
    <main class="site-main">
        <section class="promotional">
            <h2>Featured Properties</h2>
            <article class="property-grid">
                <figure class="property-card">
                    <figcaption>
                        <h3>Luxury Apartment in Downtown</h3>
                        <p>$1,200,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>
                <figure class="property-card">
                    <figcaption>
                        <h3>Modern Villa with Ocean View</h3>
                        <p>$2,500,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>
                <figure class="property-card">
                    <figcaption>
                        <h3>Cozy Studio in City Center</h3>
                        <p>$350,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>
            </article>
        </section>
        <section class="search-form">
            <h2>Find Your Perfect Home</h2>
            <form>
                <fieldset class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </fieldset>
                <fieldset class="form-group">
                    <label for="price-range">Price Range</label>
                    <select id="price-range" name="price-range" required>
                        <option value="">Select</option>
                        <option value="100-300">$100k - $300k</option>
                        <option value="300-600">$300k - $600k</option>
                        <option value="600+">$600k+</option>
                    </select>
                </fieldset>
                <fieldset class="form-group">
                    <label for="bedrooms">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" min="1" max="10" required>
                </fieldset>
                <button type="submit" class="search-button">Search Properties</button>
            </form>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>