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
<title>Terms of Service — INTSOLCOM</title>
<meta name="description" content="Terms of Service for INTSOLCOM LLC and INTSOLCOM SAS. Usage terms for our website, products, and services.">
<meta property="og:title" content="Terms of Service — INTSOLCOM">
<meta property="og:description" content="Usage terms for our website, products, and services.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= SITE_URL ?>/terms">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css?v=<?= filemtime(__DIR__.'/assets/css/main.css') ?>">
<link rel="canonical" href="<?= SITE_URL ?>/terms">
<style>
.terms-content { max-width:720px; margin:0 auto; padding:2rem 0; line-height:1.8; }
.terms-content h1 { font-size:clamp(1.8rem,4vw,2.5rem); font-weight:800; color:#0F172A; margin-bottom:.5rem; }
.terms-content h2 { font-size:1.25rem; font-weight:700; color:#0F172A; margin:2rem 0 .75rem; }
.terms-content p,.terms-content li { color:#475569; font-size:.95rem; margin-bottom:.75rem; }
.terms-content ul { padding-left:1.25rem; }
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
    <div class="terms-content">
      <h1>Terms of Service</h1>
      <p class="last-updated">Last updated: January 2026</p>

      <h2>1. Acceptance of Terms</h2>
      <p>By accessing or using the INTSOLCOM website (intsolcom.com) and any related services, you agree to be bound by these Terms of Service. If you do not agree, please do not use our website or services.</p>

      <h2>2. Services Description</h2>
      <p>INTSOLCOM LLC and INTSOLCOM SAS (collectively, "INTSOLCOM," "we," "us") provide technology products, software platforms, business process outsourcing, and nearshore development services. Detailed service descriptions and agreements are provided separately for each engagement.</p>

      <h2>3. Intellectual Property</h2>
      <p>All content on this website, including text, graphics, logos, images, and software, is the property of INTSOLCOM or its licensors and is protected by United States and international intellectual property laws.</p>
      <p>WONTIA, MACROPONDER, IA Annotation Manager, and Marcas BPO are trademarks of INTSOLCOM. All rights reserved.</p>

      <h2>4. Use of Website</h2>
      <p>You agree not to:</p>
      <ul>
        <li>Use the website for any unlawful purpose</li>
        <li>Attempt to gain unauthorized access to our systems</li>
        <li>Interfere with the proper functioning of the website</li>
        <li>Scrape, data mine, or extract content without permission</li>
        <li>Misrepresent your identity or affiliation</li>
      </ul>

      <h2>5. Limitation of Liability</h2>
      <p>INTSOLCOM provides this website and its content on an "as is" basis. We make no warranties, express or implied, regarding the accuracy, completeness, or availability of the website.</p>
      <p>To the fullest extent permitted by law, INTSOLCOM shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of this website.</p>

      <h2>6. Third-Party Links</h2>
      <p>Our website may contain links to third-party websites (such as marcasbpo.com). We are not responsible for the content or practices of these external sites.</p>

      <h2>7. Governing Law</h2>
      <p>These Terms shall be governed by and construed in accordance with the laws of the State of Delaware, United States, without regard to its conflict of law provisions.</p>

      <h2>8. Changes to Terms</h2>
      <p>We reserve the right to modify these Terms at any time. Changes will be effective immediately upon posting. Continued use of the website constitutes acceptance of the modified Terms.</p>

      <h2>9. Contact</h2>
      <p>For questions about these Terms:</p>
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
