<?php
// Basic form submission handling
$submission_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';

    if ($name && $email && $message) {
        $submission_message = "Thank you, $name! Your message has been received. We'll get back to you at $email soon.";
    } else {
        $submission_message = "Please fill out all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - SilvenStay For Flat Rent</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="content-wrapper">
    <?php include 'nav.php'; ?>
    <main class="site-main contact-main">
        <section class="contact-hero">
            <h2>Get in Touch with SilvenStay</h2>
            <p>We're here to help you find your perfect home or answer any questions about renting flats with us.</p>
        </section>
        <section class="contact-content">
            <article class="contact-info">
                <h3>Contact Information</h3>
                <section class="info-grid">
                    <section class="info-card">
                        <h4>📍 Visit Us</h4>
                        <p>Al-Masyoun, Ramallah, Palestine</p>
                        <p>Building 12, Floor 3</p>
                    </section>
                    <section class="info-card">
                        <h4>📞 Call Us</h4>
                        <p><a href="tel:+972594276335">+972 59-4276335</a></p>
                        <p>Mon-Fri: 9 AM - 5 PM</p>
                    </section>
                    <section class="info-card">
                        <h4>📧 Email Us</h4>
                        <p><a href="mailto:support@silvenstay.com">support@silvenstay.com</a></p>
                        <p>Response within 24 hours</p>
                    </section>
                    <section class="info-card">
                        <h4>🌐 Follow Us</h4>
                        <p><a href="#">Facebook</a> | <a href="#">Instagram</a></p>
                        <p><a href="#">Twitter</a> | <a href="#">LinkedIn</a></p>
                    </section>
                </section>
            </article>
            <article class="contact-form-section">
                <h3>Send Us a Message</h3>
                <?php if ($submission_message): ?>
                    <p class="form-message"><?php echo $submission_message; ?></p>
                <?php endif; ?>
                <form method="POST" class="contact-form">
                    <input type="hidden" name="contact_form" value="1">
                    <fieldset class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" required>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" required>
                    </fieldset>
                    <fieldset class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </fieldset>
                    <button type="submit" class="contact-button">Send Message</button>
                </form>
            </article>
        </section>
        <section class="contact-map">
            <h3>Find Our Office</h3>
            <figure>
                <img src="images/map-placeholder.png" alt="Map of SilvenStay Office Location" class="map-image">
                <figcaption>Our office is located in the heart of Ramallah, easily accessible for all your rental needs.</figcaption>
            </figure>
        </section>
        <section class="faq-section">
            <h3>Frequently Asked Questions</h3>
            <article class="faq-list">
                <section class="faq-item">
                    <h4>How do I book a flat through SilvenStay?</h4>
                    <p>Use our search tool to find available flats, select your preferred property, and follow the booking process. Our team will guide you through the paperwork and payment.</p>
                </section>
                <section class="faq-item">
                    <h4>What are the payment options?</h4>
                    <p>We accept bank transfers, credit cards, and select digital wallets. Contact our support team for specific details or assistance.</p>
                </section>
                <section class="faq-item">
                    <h4>Can I view a flat before renting?</h4>
                    <p>Yes! Schedule a viewing through our website or by calling our support team. We offer both in-person and virtual tours.</p>
                </section>
                <section class="faq-item">
                    <h4>What if I need to cancel my booking?</h4>
                    <p>Cancellations are subject to the property owner's policy. Check the listing details or contact us for assistance with cancellations or refunds.</p>
                </section>
                <section class="faq-item">
                    <h4>Are utilities included in the rent?</h4>
                    <p>Utility inclusion varies by property. Each listing specifies whether utilities like water, electricity, or internet are included. Contact us for clarification.</p>
                </section>
            </article>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>