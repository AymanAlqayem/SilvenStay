<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Contact Us</title>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'nav.php'; ?>

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

<?php include 'footer.php'; ?>

</body>
</html>
