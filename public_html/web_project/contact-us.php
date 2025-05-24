<?php
// Check if form was submitted
$show_alert = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    // Simple validation - check required fields are not empty
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        $show_alert = true;

        // Here you would typically:
        // 1. Process the form data (send email, save to database, etc.)
        // 2. Clear the form if needed
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
        <!-- Hero Section -->
        <section class="contact-hero">
            <article class="hero-content">
                <h2>Connect With SilvenStay</h2>
                <p class="hero-text">Your journey to the perfect home starts with a conversation</p>

                <section class="hero-icons">
                    <article class="icon-circle">
                        <span>Property Experts</span>
                    </article>
                    <article class="icon-circle">
                        <span>24/7 Support</span>
                    </article>
                    <article class="icon-circle">
                        <span>Personalized Service</span>
                    </article>
                </section>
            </article>

            <figure class="hero-wave">
                <img src="images/wave.png" alt="hero image">
            </figure>
        </section>

        <!-- Contact Cards -->
        <section class="contact-content">

            <article class="contact-info">
                <h3><span class="highlight">Our</span> Contact Channels</h3>
                <section class="info-grid">
                    <article class="info-card">
                        <figure class="card-icon">
                        </figure>
                        <h4>Visit Our Office</h4>
                        <p>Al-Masyoun, Ramallah, Palestine</p>
                        <p>Building 12, Floor 3</p>
                        <a href="#" class="card-link">Get Directions </a>
                    </article>

                    <article class="info-card">
                        <figure class="card-icon">
                        </figure>
                        <h4>Call Our Team</h4>
                        <p><a href="tel:+972594276335">+972 59-4276335</a></p>
                        <p>Mon-Fri: 9 AM - 5 PM</p>
                    </article>

                    <article class="info-card">
                        <figure class="card-icon">
                        </figure>
                        <h4>Email Support</h4>
                        <p><a href="mailto:nabilayman021@gmail.com">nabilayman021@gmail.com</a></p>
                        <p>Response within 24 hours</p>
                    </article>

                    <article class="info-card">
                        <figure class="card-icon">
                        </figure>
                        <h4>Social Media</h4>
                        <nav class="social-links">
                            <a href="#" aria-label="Facebook">
                                <img src="images/facebook.png" alt="facebook icon">
                            </a>

                            <a href="#" aria-label="Instagram">
                                <img src="images/instagram.png" alt="instagram icon">
                            </a>

                            <a href="#" aria-label="LinkedIn">
                                <img src="images/linkedin.png" alt="linkedin icon">
                            </a>
                        </nav>
                        <p>Daily updates & offers</p>
                    </article>
                </section>
            </article>

            <!-- Contact Form -->
            <article class="contact-form-section">
                <header class="form-header">
                    <h3><span class="highlight">Send</span> Us a Message</h3>
                    <p>Fill out the form below and our team will respond promptly</p>
                </header>

                <form method="POST" class="contact-form">
                    <input type="hidden" name="contact_form" value="1">

                    <section class="input-row">
                        <fieldset class="input-field">
                            <input type="text" id="name" name="name" required placeholder=" ">
                            <label for="name">Your Name</label>
                        </fieldset>

                        <fieldset class="input-field">
                            <input type="email" id="email" name="email" required placeholder=" ">
                            <label for="email">Your Email</label>
                        </fieldset>
                    </section>

                    <fieldset class="input-field select-field">
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
            <header class="section-header">
                <h3><span class="highlight">Frequently</span> Asked Questions</h3>
                <p>Quick answers to common inquiries</p>
            </header>

            <article class="faq-accordion">
                <details class="faq-item">
                    <summary class="faq-question">
                        <h4>How do I book a flat through SilvenStay?</h4>
                    </summary>
                    <div class="faq-answer">
                        <p>Use our intuitive search tool to find available flats that match your criteria. Once you've
                            selected your preferred property, follow our streamlined booking process. Our dedicated team
                            will then guide you through the paperwork and payment process, ensuring a smooth transition
                            to your new home.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">
                        <h4>What payment options are available?</h4>
                    </summary>
                    <div class="faq-answer">
                        <p>We offer multiple secure payment methods including bank transfers, major credit cards (Visa,
                            MasterCard, American Express), and popular digital wallets. For specific details about
                            payment plans or assistance with transactions, our support team is available to help you
                            choose the best option for your situation.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">
                        <h4>Can I view a flat before renting?</h4>
                    </summary>
                    <div class="faq-answer">
                        <p>Absolutely! We encourage prospective tenants to view properties before committing. You can
                            schedule either in-person tours or virtual viewings through our website or by contacting our
                            support team directly. Our representatives can accompany you to show the property and answer
                            any questions you might have about the space.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-question">
                        <h4>What if I need to cancel my booking?</h4>
                    </summary>
                    <div class="faq-answer">
                        <p>Cancellation policies vary by property and are set by the individual property owners. Each
                            listing includes specific cancellation terms, which we recommend reviewing before booking.
                            If you need to cancel, contact our support team immediately for assistance. We'll work with
                            the property owner to facilitate the process and discuss any applicable refunds.</p>
                    </div>
                </details>
            </article>

            <footer class="faq-footer">
                <p>Didn't find your answer? Contact our support team for personalized assistance.<a
                            href="tel:+972594276335" class="cta-button secondary">Call Now </a></p>
            </footer>
        </section>

        <!-- Call to Action -->
        <section class="contact-cta">
            <article class="cta-content">
                <h3>Ready to Find Your Perfect Home?</h3>
                <p>Our team is standing by to help you discover properties that match your lifestyle and budget.</p>
                <nav class="cta-buttons">
                    <a href="tel:+972594276335" class="cta-button secondary">Call Now </a>
                </nav>
            </article>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>