<?php
require_once __DIR__ . '/includes/config.php';
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();
$lang = currentLang();
?><!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0F172A">
<title>Privacy Policy — INTSOLCOM</title>
<meta name="description" content="INTSOLCOM LLC and INTSOLCOM SAS Privacy Policy. How we collect, use, and protect your information.">
<meta property="og:title" content="Privacy Policy — INTSOLCOM">
<meta property="og:description" content="How we collect, use, and protect your information.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= SITE_URL ?>/privacy">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css?v=<?= filemtime(__DIR__.'/assets/css/main.css') ?>">
<link rel="canonical" href="<?= SITE_URL ?>/privacy">
<style>
.privacy-content { max-width:720px; margin:0 auto; padding:2rem 0; line-height:1.8; }
.privacy-content h1 { font-size:clamp(1.8rem,4vw,2.5rem); font-weight:800; color:#0F172A; margin-bottom:.5rem; }
.privacy-content h2 { font-size:1.25rem; font-weight:700; color:#0F172A; margin:2rem 0 .75rem; }
.privacy-content p,.privacy-content li { color:#475569; font-size:.95rem; margin-bottom:.75rem; }
.privacy-content ul { padding-left:1.25rem; }
.last-updated { color:#94A3B8; font-size:.8rem; }
</style>
</head>
<body>
<div class="cursor-dot"></div><div class="cursor-ring"></div>
<nav class="nav" id="nav">
  <div class="container">
    <a href="/" class="nav__logo"><?= h(setting('logo_text','INTSOL')) ?><span style="color:#00C896"><?= h(setting('logo_accent','COM')) ?></span></a>
    <div class="nav__links">
      <?php foreach($navItems as $ni): if(!$ni['is_cta']): ?>
        <a href="<?= h($ni['url']) ?>" class="nav__link"><?= ht($ni['text']) ?></a>
      <?php endif; endforeach; ?>
      <?php foreach($navItems as $ni): if($ni['is_cta']): ?>
        <a href="<?= h($ni['url']) ?>" class="btn btn-accent nav__cta"><?= ht($ni['text']) ?></a>
      <?php endif; endforeach; ?>
    </div>
  </div>
</nav>

<section class="section">
  <div class="container">
    <div class="privacy-content">
      <h1>Privacy Policy</h1>
      <p class="last-updated">Last updated: January 2026</p>

      <h2>1. Information We Collect</h2>
      <p>When you contact us through our website forms, we collect the information you voluntarily provide, including your name, email address, company name, phone number, and message content.</p>
      <p>We also collect standard web analytics data including page views, browser type, and referring URLs through standard server logs.</p>

      <h2>2. How We Use Your Information</h2>
      <p>We use the information you provide to:</p>
      <ul>
        <li>Respond to your inquiries and provide the services you request</li>
        <li>Send relevant information about our products and services (with your consent)</li>
        <li>Improve our website and user experience</li>
        <li>Comply with legal obligations</li>
      </ul>

      <h2>3. Information Sharing</h2>
      <p>We do not sell, trade, or rent your personal information to third parties. We may share information with our operational entities (including INTSOLCOM SAS in Colombia) solely for the purpose of delivering the services you have requested.</p>
      <p>We may disclose information when required by law or to protect our rights.</p>

      <h2>4. Data Security</h2>
      <p>We implement appropriate technical and organizational measures to protect your personal data against unauthorized access, alteration, disclosure, or destruction. Our website uses SSL/TLS encryption for all data transmission.</p>

      <h2>5. Cookies</h2>
      <p>Our website may use essential cookies for functionality. We do not use tracking cookies or third-party advertising networks. You can disable cookies in your browser settings.</p>

      <h2>6. Your Rights</h2>
      <p>You have the right to access, correct, or delete your personal information. You may also object to or restrict certain processing of your data. To exercise these rights, contact us at the email below.</p>

      <h2>7. International Data Transfers</h2>
      <p>As a company with operations in the United States and Colombia, your data may be transferred between these jurisdictions. We ensure appropriate safeguards are in place for any such transfers.</p>

      <h2>8. Contact Us</h2>
      <p>For privacy-related inquiries, contact us at:</p>
      <p><strong>INTSOLCOM LLC</strong><br>
      390 NE 191st St, STE 17284<br>
      Miami, FL 33179<br>
      Email: info@intsolcom.com</p>
    </div>
  </div>
</section>

<script src="/assets/js/main.js?v=<?= filemtime(__DIR__.'/assets/js/main.js') ?>"></script>
</body>
</html>
