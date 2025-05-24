<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About SilvenStay - Your Trusted Flat Rental Platform</title>
    <link rel="stylesheet" href="styles.css">
<!--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">-->
</head>
<body>
<?php include 'header.php'; ?>
<section class="content-wrapper">
    <?php include 'nav.php'; ?>

    <main class="site-main about-main">
        <!-- Hero Section -->
        <section class="about-hero">
            <article class="hero-content">
                <h1>Our Story in <span class="highlight">Sheltering Dreams</span></h1>
                <p>From a small idea to Palestine's premier flat rental platform</p>
            </article>
            <figure class="hero-image">
                <img src="images/about-hero.jpg" alt="Modern apartment building in Ramallah">
            </figure>
        </section>

        <!-- Mission Section -->
        <section class="mission-section">
            <article class="mission-card">
                <h2>Our Mission</h2>
                <figure>
                    <img src="images/home.png" alt="Modern apartment building in Ramallah">
                </figure>
                <p>To simplify the rental process while creating meaningful connections between property owners and tenants across Palestine.</p>
            </article>

            <article class="mission-card">
                <h2>Our Promise</h2>
                <figure class="hero-image">
                    <img src="images/handshake.png" alt="Modern apartment building in Ramallah">
                </figure>
                <p>Transparent pricing, verified listings, and personalized support at every step of your rental journey.</p>
            </article>

            <article class="mission-card">
                <h2>Our Reach</h2>
                <figure class="hero-image">
                    <img src="images/map.png" alt="Modern apartment building in Ramallah">
                </figure>
                <p>Serving all major Palestinian cities with plans to expand throughout the region.</p>
            </article>
        </section>

        <!-- Timeline Section -->
        <section class="timeline-section">
            <h2>Our <span class="highlight">Journey</span></h2>
            <article class="timeline">
                <div class="timeline-item">
                    <time>2015</time>
                    <h3>Humble Beginnings</h3>
                    <p>Founded in Ramallah with just 12 properties in our portfolio</p>
                </div>
                <div class="timeline-item">
                    <time>2017</time>
                    <h3>Tech Platform Launched</h3>
                    <p>Developed our proprietary matching algorithm</p>
                </div>
                <div class="timeline-item">
                    <time>2019</time>
                    <h3>Expansion</h3>
                    <p>Extended services to 5 major Palestinian cities</p>
                </div>
                <div class="timeline-item">
                    <time>2022</time>
                    <h3>Milestone Achieved</h3>
                    <p>10,000+ successful rental matches made</p>
                </div>
            </article>
        </section>

        <!-- Team Section -->
        <section class="team-section">
            <h2>Meet Our <span class="highlight">Team</span></h2>
            <p class="section-intro">Passionate professionals dedicated to revolutionizing rentals in Palestine</p>

            <article class="team-grid">
                <figure class="team-member">
                    <img src="images/team1.jpg" alt="Kareem Al-Masri, Founder">
                    <figcaption>
                        <h3>Kareem Al-Masri</h3>
                        <p>Founder & CEO</p>
                        <nav class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </nav>
                    </figcaption>
                </figure>

                <figure class="team-member">
                    <img src="images/team2.jpg" alt="Layla Nasser, Head of Operations">
                    <figcaption>
                        <h3>Layla Nasser</h3>
                        <p>Head of Operations</p>
                        <nav class="member-social">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </nav>
                    </figcaption>
                </figure>

                <figure class="team-member">
                    <img src="images/team3.jpg" alt="Omar Khalid, Tech Lead">
                    <figcaption>
                        <h3>Omar Khalid</h3>
                        <p>Technology Director</p>
                        <nav class="member-social">
                            <a href="#"><i class="fab fa-github"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                        </nav>
                    </figcaption>
                </figure>
            </article>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <h2>Our Core <span class="highlight">Values</span></h2>

            <article class="values-grid">
                <section class="value-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Trust</h3>
                    <figure class="hero-image">
                        <img src="images/shield3.png" alt="Modern apartment building in Ramallah">
                    </figure>
                    <p>Every listing is personally verified by our team</p>
                </section>

                <section class="value-card">
                    <h3>Care</h3>
                    <figure class="hero-image">
                        <img src="images/heart.png" alt="Modern apartment building in Ramallah">
                    </figure>
                    <p>We treat every client like family</p>
                </section>

                <section class="value-card">
                    <h3>Innovation</h3>
                    <figure class="hero-image">
                        <img src="images/lightbulb.png" alt="Modern apartment building in Ramallah">
                    </figure>
                    <p>Continuously improving our services</p>
                </section>

                <section class="value-card">
                    <h3>Fairness</h3>
                    <figure class="hero-image">
                        <img src="images/hand.png" alt="Modern apartment building in Ramallah">
                    </figure>
                    <p>Transparent pricing with no hidden fees</p>
                </section>
            </article>
        </section>

        <!-- CTA Section -->
        <section class="about-cta">
            <h2>Ready to Find Your Perfect Home?</h2>
            <a href="properties.php" class="cta-button">Browse Listings</a>
            <a href="contact.php" class="cta-button secondary">Contact Us</a>
        </section>
    </main>
</section>
<?php include 'footer.php'; ?>
</body>
</html>