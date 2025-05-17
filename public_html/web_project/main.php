<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SilvenStay For Flat Rent</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <section class="logo-section">
        <img src="images/logo.png" alt="SilvenStay Logo" class="logo">
        <h1>SilvenStay For Flat Rent.</h1>
    </section>


    <section class="header-links">
        <a href="#" class="about-link">About Us</a>

        <section class="user-card">
            <img src="https://via.placeholder.com/40" alt="User Photo" class="user-photo">
            <span class="username">JohnDoe</span>
            <a href="#" class="profile-link"><i class="fas fa-user"></i></a>
        </section>


        <a href="#" class="basket-link">
            <img src="images/basket.png" alt="basket image" class="basket-image">
        </a>
        <section class="auth-links">
            <a href="#" class="register-link">Register</a>
            <a href="#" class="login-link">Login</a>
            <a href="#" class="logout-link">Logout</a>
        </section>
    </section>
</header>

<div class="content-wrapper">

    <nav class="site-nav">
        <ul>
            <li><a href="#" class="active"> Home</a></li>
            <li><a href="#">Flat Search</a></li>
            <li><a href="#"> View Messages</a></li>
            <li><a href="#"> About Us</a></li>
            <li><a href="#">Register</a></li>
            <li><a href="#">Login</a></li>
        </ul>
    </nav>

    <main class="site-main">
        <section class="promotional">
            <h2>Featured Properties</h2>
            <article class="property-grid">
                <figure class="property-card">
                    <!--                    <img src="" alt="Luxury Apartment">-->
                    <figcaption>
                        <h3>Luxury Apartment in Downtown</h3>
                        <p>$1,200,000</p>
                        <button>View Details</button>
                    </figcaption>
                </figure>

                <figure class="property-card">
                    <!--                    <img src="" alt="Modern Villa">-->
                    <figcaption>
                        <h3>Modern Villa with Ocean View</h3>
                        <p>$2,500,000</p>
                        <button>View Details</button>
                    </figcaption>

                </figure>
                <figure class="property-card">
                    <!--                    <img src="" alt="Cozy Studio">-->
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
</div>

<footer class="site-footer">

    <section class="footer-content">
        <section class="footer-logo">
            <img src="images/logo.png" alt="SilvenStyle Logo">
            <span>A sophisticated, modern place to stay — where luxury and comfort meet.</span>
        </section>
        <address class="footer-info">
            <p>&copy; 2025 SilvenStay. All rights reserved.</p>
            <p>📍Palestine - Ramallah</p>
            <p><a href="mailto:nabilayman021@gmail.com">📧 Email</a></p>
            <p><a href="tel:+972594276335">📞 Customer Support</a></p>
            <a href="#" class="contact-link">📬 Contact Us</a>
        </address>
    </section>
</footer>
</body>
</html>