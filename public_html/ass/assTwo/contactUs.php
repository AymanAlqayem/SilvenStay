<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
</head>
<body>

<header>
    <h1>👕👗 WELCOME TO LEVON 👠🧥</h1>
    <h2>"Wear Your Style, Anywhere"</h2>
    <figure>
        <img src="images/logo.png" alt="Levon logo" width="150" height="150">
        <figcaption><strong>Your Fashion Destination</strong></figcaption>
    </figure>

    <hr>
    <br>

    <nav>
        🏠 <a href="products.php">Home</a> |
        ➕ <a href="products.php">Add Product</a> |
        🔍 <a href="products.php/search">Search</a>
    </nav>

    <br>
    <hr>
</header>

<main>
    <section>
        <h2>Contact Us</h2>
        <p>We’d love to hear from you! Please fill out the form below:</p>

        <figure>
            <img src="images/contactUs.png" alt="Levon logo" width="250" height="210">
        </figure>

        <form action="contactUs.php" method="post">
            <fieldset>
                <legend>Contact Information 📝</legend>

                <label for="fullName">Full Name: 👤</label><br>
                <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required><br><br>

                <label for="email">Email Address: 📧</label><br>
                <input type="email" id="email" name="email" placeholder="Enter your email" required><br><br>

                <label for="phone">Phone Number: 📞</label><br>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required><br><br>
            </fieldset>

            <fieldset>
                <legend>Message Details 🗨️</legend>

                <label for="subject">Subject: 📌</label><br>
                <select name="subject" id="subject" required>
                    <option value="">-- Choose Topic --</option>
                    <option value="product">Product Inquiry 🛍️</option>
                    <option value="order">Order Status 📦</option>
                    <option value="support">Technical Support 💻</option>
                    <option value="feedback">Feedback ✍️</option>
                    <option value="other">Other 🗨️</option>
                </select><br><br>

                <label for="message">Your Message: ✉️</label><br>
                <textarea id="message" name="message" rows="5" cols="40" required
                          placeholder="Write your message here"></textarea><br><br>
            </fieldset>

            <fieldset>
                <legend>Preferred Contact Method 📱</legend>

                <p>Please choose your preferred method of contact:</p>

                <label for="contactEmail">
                    <input type="radio" id="contactEmail" name="contactMethod" value="email" checked>
                    Email 📧
                </label><br>

                <label for="contactPhone">
                    <input type="radio" id="contactPhone" name="contactMethod" value="phone">
                    Phone 📞
                </label><br>
            </fieldset>

            <br>
            <button type="submit">📧 Send Message</button>
        </form>
    </section>

</main>

<footer>
    <br><br>
    <address>
        <strong>📍 Store Address:</strong> Palestine, Ramallah, Israa Complex, second floor |
        <a href="tel:+972594276335">📞 Customer Support</a> |
        <a href="mailto:nabilayman021@gmail.com">📧 Email</a> |
        <a href="contactUs.php">📬 Contact Us</a>
    </address>
</footer>

</body>
</html>
