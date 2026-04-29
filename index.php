<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ComfyGo — Travel Made Comfortable</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles/index.css">
</head>

<body>

    <?php $active_page = 'home';
    include 'navbaar.php'; ?>

    <div id="hero">
        <p id="hero_tag">Bangladesh's Trusted Travel Agency</p>
        <h1 id="hero_title">Travel with <em>comfort</em>,<br>explore with ease.</h1>
        <p id="hero_sub">Handpicked destinations, certified guides, and trusted hotels across Bangladesh — all in one
            place.</p>
        <div id="hero_actions">
            <a href="about.php" id="btn_explore">Explore Destinations</a>
            <a href="contact.php" id="btn_contact">Talk to Us</a>
        </div>
        <div id="hero_stats">
            <div class="hero_stat">
                <span class="stat_num">3+</span>
                <span class="stat_label">Certified Cities</span>
            </div>
            <div class="hero_stat">
                <span class="stat_num">50+</span>
                <span class="stat_label">Partner Hotels</span>
            </div>
            <div class="hero_stat">
                <span class="stat_num">200+</span>
                <span class="stat_label">Happy Travellers</span>
            </div>
        </div>
    </div>

    <section id="features_sec">
        <p class="sec_tag">Why ComfyGo</p>
        <h2 class="sec_title">Everything you need for a perfect trip</h2>
        <p class="sec_sub">From planning to arrival, we handle every detail so you can simply enjoy the journey.</p>
        <div id="features_grid">
            <div class="feature_card">
                <h3>Verified & Certified</h3>
                <p>Every hotel, guide, and transport partner goes through our rigorous certification process before we
                    recommend them.</p>
            </div>
            <div class="feature_card">
                <h3>Local Expertise</h3>
                <p>Our guides are born and raised in their destinations — they know the hidden gems and stories behind
                    every sight.</p>
            </div>
            <div class="feature_card">
                <h3>24/7 Support</h3>
                <p>Travel confidently knowing our team is always reachable for last-minute changes or urgent needs.</p>
            </div>
            <div class="feature_card">
                <h3>Best Price Guarantee</h3>
                <p>We negotiate directly with hotels and service providers so you always get competitive rates — no
                    hidden fees.</p>
            </div>
            <div class="feature_card">
                <h3>Responsible Tourism</h3>
                <p>We partner with eco-conscious providers and support local communities across Bangladesh.</p>
            </div>
            <div class="feature_card">
                <h3>Fully Customisable</h3>
                <p>Solo, couple, family, or group — every itinerary is tailored to your pace, preferences, and budget.
                </p>
            </div>
        </div>
    </section>



    <section id="steps_sec">
        <p class="sec_tag">How It Works</p>
        <h2 class="sec_title">Plan your trip in 4 simple steps</h2>
        <div id="steps_grid">
            <div class="step">
                <div class="step_num">01</div>
                <h4>Choose a Destination</h4>
                <p>Browse our certified cities — Sylhet, Dhaka, or Chittagong.</p>
            </div>
            <div class="step">
                <div class="step_num">02</div>
                <h4>Pick Your Services</h4>
                <p>Select from certified hotels, guides, and transport options.</p>
            </div>
            <div class="step">
                <div class="step_num">03</div>
                <h4>Confirm & Book</h4>
                <p>Review your itinerary and complete your booking with ease.</p>
            </div>
            <div class="step">
                <div class="step_num">04</div>
                <h4>Travel with Comfort</h4>
                <p>Arrive, explore, and enjoy — with our team on standby.</p>
            </div>
        </div>
    </section>

    <section id="testimonials_sec">
        <p class="sec_tag">Testimonials</p>
        <h2 class="sec_title">What our travellers say</h2>
        <div id="testimonials_grid">
            <div class="testimonial_card">
                <p class="testimonial_text">"ComfyGo made our family trip to Sylhet absolutely seamless. The hotel was
                    stunning and our guide knew every hidden spot in Jaflong."</p>
                <div class="testimonial_author">
                    <div class="author_avatar">R</div>
                    <div>
                        <p class="author_name">Rahim Uddin</p>
                        <p class="author_loc">Dhaka, Bangladesh</p>
                    </div>
                </div>
            </div>
            <div class="testimonial_card">
                <p class="testimonial_text">"I was travelling solo for the first time and ComfyGo's team was incredibly
                    supportive. Everything was arranged perfectly."</p>
                <div class="testimonial_author">
                    <div class="author_avatar">N</div>
                    <div>
                        <p class="author_name">Nusrat Jahan</p>
                        <p class="author_loc">Sylhet, Bangladesh</p>
                    </div>
                </div>
            </div>
            <div class="testimonial_card">
                <p class="testimonial_text">"The certified guide in Chittagong was exceptional — knowledgeable, fun, and
                    punctual. Highly recommend ComfyGo."</p>
                <div class="testimonial_author">
                    <div class="author_avatar">K</div>
                    <div>
                        <p class="author_name">Karim Hassan</p>
                        <p class="author_loc">Chittagong, Bangladesh</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="cta_sec">
        <h2 id="cta_title">Ready to start your journey?</h2>
        <p id="cta_sub">Get in touch and let us craft the perfect trip for you.</p>
        <div id="cta_actions">
            <a href="contact.php" id="cta_btn_primary">Contact Us</a>
            <a href="about.php" id="cta_btn_outline">Learn More</a>
        </div>
    </div>

    <footer id="footer">
        <div id="footer_grid">
            <div id="footer_brand">
                <a href="index.php" id="footer_logo">Comfy<span>Go</span></a>
                <p>Bangladesh's trusted travel agency for comfortable, certified, and memorable journeys.</p>
            </div>
            <div class="footer_col">
                <h5>Explore</h5>
                <a href="about.php">About Us</a>
                <a href="about.php">Certified Hotels</a>
                <a href="about.php">Certified Guides</a>
                <a href="about.php">Destinations</a>
            </div>
            <div class="footer_col">
                <h5>Company</h5>
                <a href="about.php">Our Mission</a>
                <a href="contact.php">Contact</a>
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="terms_of_service.php">Terms of Service</a>
            </div>
            <div class="footer_col">
                <h5>Contact</h5>
                <a href="https://maps.google.com/?q=Sylhet,Bangladesh" target="_blank">Sylhet, Bangladesh</a>
                <a href="tel:+8801234567890">+880 1234 567890</a>
                <a href="mailto:info@comfygo.com">info@comfygo.com</a>
            </div>
        </div>
        <div id="footer_bottom">
            <p>&copy; <?php echo date('Y'); ?>