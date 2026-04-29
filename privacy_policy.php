<?php
$active_page = 'privacy';
include 'navbaar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy — ComfyGo</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/about.css">
  <style>

    /* ── Reset & Base ─────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --brand:       #1B6CA8;
      --brand-dark:  #134e7e;
      --brand-light: #e8f3fc;
      --accent:      #F4A535;
      --text:        #1a1a2e;
      --text-muted:  #5a6478;
      --border:      #dde6ef;
      --bg:          #f8fafd;
      --white:       #ffffff;
      --radius:      12px;
      --shadow:      0 4px 24px rgba(27,108,168,.10);
    }

    body {
      font-family: "DM Sans", sans-serif;
      background: var(--bg);
      color: var(--text);
      line-height: 1.7;
      -webkit-font-smoothing: antialiased;
    }

    /* ── Hero Banner ──────────────────────────────── */
    .policy-hero {
      background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 60%, #2a8fd4 100%);
      padding: 72px 20px 60px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .policy-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      pointer-events: none;
    }

    .policy-hero h1 {
      font-family: "Playfair Display", serif;
      font-size: clamp(2rem, 5vw, 3rem);
      font-weight: 700;
      color: var(--white);
      letter-spacing: -.5px;
      margin-bottom: 12px;
      position: relative;
    }

    .policy-hero .subtitle {
      font-size: .95rem;
      color: rgba(255,255,255,.78);
      font-weight: 300;
      letter-spacing: .5px;
      position: relative;
    }

    .policy-hero .badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(255,255,255,.15);
      border: 1px solid rgba(255,255,255,.25);
      backdrop-filter: blur(8px);
      color: var(--white);
      font-size: .8rem;
      font-weight: 500;
      padding: 5px 14px;
      border-radius: 20px;
      margin-bottom: 20px;
      position: relative;
    }

    .policy-hero .badge svg {
      width: 14px; height: 14px; fill: var(--accent);
    }

    /* ── Layout ───────────────────────────────────── */
    .policy-wrap {
      max-width: 860px;
      margin: 0 auto;
      padding: 56px 24px 80px;
      display: grid;
      grid-template-columns: 220px 1fr;
      gap: 48px;
      align-items: start;
    }

    @media (max-width: 720px) {
      .policy-wrap { grid-template-columns: 1fr; gap: 32px; }
      .policy-toc  { display: none; }
    }

    /* ── Table of Contents ────────────────────────── */
    .policy-toc {
      position: sticky;
      top: 24px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 22px 20px;
      box-shadow: var(--shadow);
    }

    .policy-toc .toc-title {
      font-family: "Playfair Display", serif;
      font-size: .95rem;
      font-weight: 600;
      color: var(--brand);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 14px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--brand-light);
    }

    .policy-toc ol {
      list-style: none;
      counter-reset: toc;
    }

    .policy-toc li {
      counter-increment: toc;
      margin-bottom: 8px;
    }

    .policy-toc a {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      font-size: .82rem;
      color: var(--text-muted);
      text-decoration: none;
      line-height: 1.4;
      transition: color .2s;
    }

    .policy-toc a::before {
      content: counter(toc, decimal-leading-zero);
      flex-shrink: 0;
      font-size: .72rem;
      font-weight: 600;
      color: var(--brand);
      background: var(--brand-light);
      padding: 1px 5px;
      border-radius: 4px;
      margin-top: 1px;
    }

    .policy-toc a:hover { color: var(--brand); }

    /* ── Main Content ─────────────────────────────── */
    .policy-body section {
      margin-bottom: 44px;
      scroll-margin-top: 24px;
    }

    .policy-body h2 {
      font-family: "Playfair Display", serif;
      font-size: 1.35rem;
      font-weight: 600;
      color: var(--brand-dark);
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .policy-body h2 .sec-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 28px;
      height: 28px;
      background: var(--brand);
      color: var(--white);
      font-family: "DM Sans", sans-serif;
      font-size: .75rem;
      font-weight: 700;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .policy-body p {
      font-size: .97rem;
      color: var(--text-muted);
      line-height: 1.85;
      margin-bottom: 12px;
    }

    .policy-body ul {
      list-style: none;
      margin: 4px 0 16px 0;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .policy-body li {
      font-size: .95rem;
      color: var(--text-muted);
      line-height: 1.7;
      padding: 10px 14px;
      background: var(--white);
      border: 1px solid var(--border);
      border-left: 3px solid var(--brand);
      border-radius: 6px;
    }

    .policy-body li strong {
      color: var(--text);
      font-weight: 600;
    }

    /* ── Info Card (intro) ────────────────────────── */
    .intro-card {
      background: linear-gradient(135deg, var(--brand-light) 0%, #f0f7ff 100%);
      border: 1px solid #c5ddf5;
      border-radius: var(--radius);
      padding: 22px 24px;
      margin-bottom: 44px;
    }

    .intro-card p {
      font-size: .97rem;
      color: #2d4a6b;
      margin: 0;
      line-height: 1.8;
    }

    /* ── Contact Card ─────────────────────────────── */
    .contact-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px 28px;
      box-shadow: var(--shadow);
      display: flex;
      flex-wrap: wrap;
      gap: 18px;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1 1 180px;
    }

    .contact-item .icon {
      width: 38px;
      height: 38px;
      background: var(--brand-light);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .contact-item .icon svg {
      width: 18px;
      height: 18px;
      fill: var(--brand);
    }

    .contact-item .label {
      font-size: .75rem;
      font-weight: 600;
      color: var(--brand);
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    .contact-item .value {
      font-size: .9rem;
      color: var(--text);
    }

    /* ── Footer note ──────────────────────────────── */
    .policy-footer-note {
      text-align: center;
      font-size: .8rem;
      color: #aab4c2;
      margin-top: 48px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
    }

  </style>
</head>
<body>

<!-- Hero -->
<div class="policy-hero">
  <span class="badge">
    <svg viewBox="0 0 20 20"><path d="M10 1l2.5 6.5H19l-5.5 4 2.1 6.5L10 14l-5.6 4 2.1-6.5L1 7.5h6.5z"/></svg>
    ComfyGo Legal
  </span>
  <h1>Privacy Policy</h1>
  <p class="subtitle">Last updated: April 29, 2026</p>
</div>

<!-- Body -->
<div class="policy-wrap">

  <!-- Table of Contents -->
  <aside class="policy-toc" aria-label="Table of contents">
    <div class="toc-title">Contents</div>
    <ol>
      <li><a href="#s1">Information We Collect</a></li>
      <li><a href="#s2">How We Use Your Info</a></li>
      <li><a href="#s3">Information Sharing</a></li>
      <li><a href="#s4">Data Security</a></li>
      <li><a href="#s5">Your Rights</a></li>
      <li><a href="#s6">Cookies &amp; Tracking</a></li>
      <li><a href="#s7">Children's Privacy</a></li>
      <li><a href="#s8">Third-Party Services</a></li>
      <li><a href="#s9">Policy Changes</a></li>
      <li><a href="#s10">Contact Us</a></li>
    </ol>
  </aside>

  <!-- Main -->
  <main class="policy-body">

    <div class="intro-card">
      <p>At <strong>ComfyGo</strong>, we are committed to protecting your privacy and ensuring your personal information is handled safely and responsibly. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our website and services.</p>
    </div>

    <section id="s1">
      <h2><span class="sec-num">1</span> Information We Collect</h2>
      <p>We collect information you provide directly to us when you:</p>
      <ul>
        <li>Create an account — name, email address, phone number, and user ID</li>
        <li>Make a booking — travel dates, hotel preferences, and guide selections</li>
        <li>Submit a contact or inquiry form</li>
        <li>Communicate with our support team</li>
      </ul>
    </section>

    <section id="s2">
      <h2><span class="sec-num">2</span> How We Use Your Information</h2>
      <p>We use the information we collect to:</p>
      <ul>
        <li>Provide, maintain, and continuously improve our services</li>
        <li>Process bookings and financial transactions securely</li>
        <li>Send booking confirmations, receipts, and service updates</li>
        <li>Respond to your comments, questions, and support requests</li>
        <li>Personalize your travel experience on our platform</li>
        <li>Monitor and ensure the security of our platform</li>
      </ul>
    </section>

    <section id="s3">
      <h2><span class="sec-num">3</span> Information Sharing</h2>
      <p>We do <strong>not</strong> sell your personal information. We may share your information only with:</p>
      <ul>
        <li><strong>Service Providers:</strong> Hotels, guides, and transportation partners required to fulfill your booking</li>
        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights, safety, or property</li>
        <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of company assets</li>
      </ul>
    </section>

    <section id="s4">
      <h2><span class="sec-num">4</span> Data Security</h2>
      <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. All passwords are stored using secure one-way hashing algorithms and are never stored in plain text.</p>
    </section>

    <section id="s5">
      <h2><span class="sec-num">5</span> Your Rights</h2>
      <p>You have the right to:</p>
      <ul>
        <li>Access and review your personal information at any time</li>
        <li>Correct or update inaccurate or incomplete data</li>
        <li>Request permanent deletion of your account and associated data</li>
        <li>Opt out of marketing and promotional communications</li>
      </ul>
    </section>

    <section id="s6">
      <h2><span class="sec-num">6</span> Cookies &amp; Tracking</h2>
      <p>We use cookies and similar tracking technologies to enhance your browsing experience, analyze site traffic, and deliver personalized content. You can manage or disable cookie preferences through your browser settings at any time.</p>
    </section>

    <section id="s7">
      <h2><span class="sec-num">7</span> Children's Privacy</h2>
      <p>Our services are not directed at children under the age of 13. We do not knowingly collect personal information from children under 13. If we become aware that we have inadvertently collected such data, we will promptly delete it.</p>
    </section>

    <section id="s8">
      <h2><span class="sec-num">8</span> Third-Party Services</h2>
      <p>Our website may contain links to third-party websites or services. We are not responsible for the privacy practices or content of those external sites and encourage you to review their respective privacy policies.</p>
    </section>

    <section id="s9">
      <h2><span class="sec-num">9</span> Changes to This Policy</h2>
      <p>We may update this Privacy Policy from time to time to reflect changes in our practices or legal requirements. We will notify users of any material changes by posting the updated policy on this page with a revised "Last updated" date.</p>
    </section>

    <section id="s10">
      <h2><span class="sec-num">10</span> Contact Us</h2>
      <p>If you have any questions, concerns, or requests regarding this Privacy Policy, please reach out to us:</p>
      <div class="contact-card">
        <div class="contact-item">
          <div class="icon">
            <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
          </div>
          <div>
            <div class="label">Email</div>
            <div class="value">privacy@comfygo.com</div>
          </div>
        </div>
        <div class="contact-item">
          <div class="icon">
            <svg viewBox="0 0 24 24"><path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.16 21 3 13.84 3 5a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/></svg>
          </div>
          <div>
            <div class="label">Phone</div>
            <div class="value">+880 1234 567890</div>
          </div>
        </div>
        <div class="contact-item">
          <div class="icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"/></svg>
          </div>
          <div>
            <div class="label">Address</div>
            <div class="value">Sylhet, Bangladesh</div>
          </div>
        </div>
      </div>
    </section>

    <p class="policy-footer-note">© <?php echo date('Y'); ?> ComfyGo. All rights reserved. This policy is effective as of April 29, 2026.</p>

  </main>
</div>

</body>
</html>