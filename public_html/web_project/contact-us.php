<?php
// Handle contact form submission
$show_alert = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    // Sanitize and fetch inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate fields
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Simulate successful form submission (fake success)
        $show_alert = true;
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

    <main class="site-main">

        <!-- Hero Section -->
        <section class="contact-hero">

            <article class="hero-content">
                <h2>Connect With SilvenStay</h2>
                <p class="hero-text">Your journey to the perfect home starts with a conversation</p>
                <section class="hero-icons">
                    <article class="icon-circle"><span>Property Experts</span></article>
                    <article class="icon-circle"><span>24/7 Support</span></article>
                    <article class="icon-circle"><span>Personalized Service</span></article>
                </section>
            </article>

            <figure class="hero-wave">
                <img src="images/wave.png" alt="hero image">
            </figure>

        </section>


        <!-- Contact Info Cards -->
        <section class="contact-content">

            <article class="contact-info">

                <h3><span class="highlight">Our</span> Contact Channels</h3>

                <section class="info-grid">
                    <article class="info-card">
                        <h4>Visit Our Office</h4>
                        <p>Al-Masyoun, Ramallah, Palestine</p>
                        <p>Building 12, Floor 3</p>
                    </article>

                    <article class="info-card">
                        <h4>Call Our Team</h4>
                        <p><a href="tel:+972594276335">+972 594276335</a></p>
                        <p>Mon-Fri: 9 AM - 4 PM</p>
                    </article>

                    <article class="info-card">
                        <h4>Email Support</h4>
                        <p><a href="mailto:nabilayman021@gmail.com">nabilayman021@gmail.com</a></p>
                        <p>Response within 24 hours</p>
                    </article>

                    <article class="info-card">
                        <h4>Social Media</h4>
                        <nav class="social-links">
                            <a href="https://www.facebook.com/ayman.nabil.58726" target="_blank"><img
                                        src="images/facebook.png" alt="facebook icon"></a>
                            <a href="http://instagram.com/ayman_tarifi" target="_blank"><img src="images/instagram.png"
                                                                                             alt="instagram icon"></a>
                            <a href="https://www.linkedin.com/in/ayman-alqayem-9012a232a/" target="_blank"><img
                                        src="images/linkedin.png" alt="linkedin icon"></a>
                        </nav>
                        <p>Daily updates & offers</p>
                    </article>

                </section>

            </article>

            <!-- Contact Form -->
            <article>
                <header>
                    <h3><span class="highlight">Send</span> Us a Message</h3>
                    <p>Fill out the form below and our team will respond promptly</p>
                </header>

                <form method="POST">
                    <input type="hidden" name="contact_form" value="1">

                    <section>
                        <fieldset class="input-field">
                            <input type="text" id="name" name="name" required placeholder=" ">
                            <label for="name">Your Name</label>
                        </fieldset>

                        <fieldset class="input-field">
                            <input type="email" id="email" name="email" required placeholder=" ">
                            <label for="email">Your Email</label>
                        </fieldset>
                    </section>

                    <fieldset class="input-field">
                        <select id="subject" name="subject" required>
                            <option value="" disabled selected></option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Property Viewing">Property Viewing</option>
                            <option value="Booking Question">Booking Question</option>
                            <option value="Payment Issue">Payment Issue</option>
                            <option value="Other">Other</option>
                        </select>
                        <label for="subject">Subject</label>
                        <span class="select-icon"></span>
                    </fieldset>

                    <fieldset class="input-field textarea-field">
                        <textarea id="message" name="message" rows="5" required placeholder=" "></textarea>
                        <label for="message">Your Message</label>
                    </fieldset>

                    <footer class="form-actions">
                        <button type="submit" class="submit-button">
                            <span>Send Message</span>
                        </button>
                        <p class="privacy-note">We respect your privacy and will never share your information</p>
                    </footer>
                </form>

                <?php if ($show_alert): ?>
                    <div class="form-success-alert">
                        <p>✓ Message sent successfully! We will contact you soon.</p>
                    </div>
                <?php endif; ?>
            </article>
        </section>


        <!-- FAQ Section -->
        <section class="faq-section">

            <header>
                <h3><span class="highlight">Frequently</span> Asked Questions</h3>
                <p>Quick answers to common inquiries</p>
            </header>

            <article class="faq-accordion">
                <details class="faq-item">
                    <summary class="faq-question"><h4>How do I book a flat through SilvenStay?</h4></summary>
                    <div class="faq-answer">
                        <p>Use our search tool to find flats, then follow the booking process. Our team will guide you
                            through the paperwork.</p>
                    </div>
                </details>
                <details class="faq-item">
                    <summary class="faq-question"><h4>What payment options are available?</h4></summary>
                    <div class="faq-answer">
                        <p>We accept bank transfers, credit cards, and digital wallets. Contact us for specific
                            details.</p>
                    </div>
                </details>
                <details class="faq-item">
                    <summary class="faq-question"><h4>Can I view a flat before renting?</h4></summary>
                    <div class="faq-answer">
                        <p>Yes, we offer both in-person and virtual tours. Schedule through our site or call
                            support.</p>
                    </div>
                </details>
                <details class="faq-item">
                    <summary class="faq-question"><h4>What if I need to cancel my booking?</h4></summary>
                    <div class="faq-answer">
                        <p>Policies vary by property. Contact us as soon as possible to review terms and refund
                            options.</p>
                    </div>
                </details>
            </article>

            <footer>
                <p>Didn't find your answer? <a href="tel:+972594276335">Call our support team</a></p>
            </footer>
        </section>

        <!-- CTA -->
        <section class="contact-cta">
            <article class="cta-content">
                <h3>Ready to Find Your Perfect Home?</h3>
                <p>Our team is ready to help you find a space that fits your lifestyle.</p>
            </article>
        </section>

    </main>
</section>

<?php include 'footer.php'; ?>
</body>
</html>
