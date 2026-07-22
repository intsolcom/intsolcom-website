<?php
require_once __DIR__ . '/includes/config.php';

$page     = getPage('contact');
$sections = $page ? getSections($page['id']) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');

$metaTitle       = $page['meta_title']       ?? 'Contact — INTSOLCOM';
$metaDescription = $page['meta_desc']         ?? 'Partner with a technology holding that delivers. Contact INTSOLCOM LLC (USA) or INTSOLCOM SAS (Colombia).';
$currentUrl      = SITE_URL . '/contact';
$lang            = currentLang();

$usaPhone   = setting('contact_usa_phone', '+1 (302) 555-0199');
$usaAddress = setting('contact_usa_address', '1209 Orange Street, Wilmington, DE 19801');
$colEmail   = setting('contact_col_email', 'info@intsolcom.com');
$colAddress = setting('contact_col_address', 'Carrera 53 #79-01, Barranquilla, Colombia');
$whatsapp   = setting('contact_whatsapp', '+573005550199');

$errors   = [];
$success  = false;
$formData = ['name' => '', 'email' => '', 'company' => '', 'phone' => '', 'country' => '', 'service_interest' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formData = [
    'name'             => trim($_POST['name']             ?? ''),
    'email'            => trim($_POST['email']            ?? ''),
    'company'          => trim($_POST['company']          ?? ''),
    'phone'            => trim($_POST['phone']            ?? ''),
    'country'          => trim($_POST['country']          ?? ''),
    'service_interest' => trim($_POST['service_interest'] ?? ''),
    'message'          => trim($_POST['message']          ?? ''),
  ];

  if ($formData['name'] === '')            $errors['name']             = t('Name is required.');
  if ($formData['email'] === '')           $errors['email']            = t('Email is required.');
  elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL))
                                            $errors['email']            = t('Please enter a valid email.');
  if ($formData['service_interest'] === '')$errors['service_interest'] = t('Please select a service interest.');
  if ($formData['message'] === '')          $errors['message']          = t('Message is required.');
  if (!empty($formData['phone']) && !preg_match('/^[+\d\s\-().]{7,20}$/', $formData['phone']))
                                            $errors['phone']            = t('Please enter a valid phone number.');

  if (empty($errors)) {
    try {
      $ins = db()->prepare("INSERT INTO lead_contacts (name, email, company, phone, country, service_interest, message, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $ins->execute([
        $formData['name'],
        $formData['email'],
        $formData['company'],
        $formData['phone'],
        $formData['country'],
        $formData['service_interest'],
        $formData['message'],
        'contact_page',
      ]);
      $success = true;
      $formData = ['name' => '', 'email' => '', 'company' => '', 'phone' => '', 'country' => '', 'service_interest' => '', 'message' => ''];
    } catch (Exception $e) {
      $errors['general'] = t('Something went wrong. Please try again or email us directly.');
    }
  }
}

$serviceOptions = [
  'technology'        => t('Technology / Software'),
  'ai_data'           => t('AI & Data Operations'),
  'business_services' => t('Business Services'),
  'consulting'        => t('Consulting'),
  'partnership'       => t('Partnership / Investment'),
  'other'             => t('Other'),
];
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= h($metaDescription) ?>">
  <meta property="og:title" content="<?= h($metaTitle) ?>">
  <meta property="og:description" content="<?= h($metaDescription) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= h($currentUrl) ?>">
  <meta property="og:site_name" content="<?= h($siteName) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= h($metaTitle) ?>">
  <meta name="twitter:description" content="<?= h($metaDescription) ?>">
  <title><?= h($metaTitle) ?> — <?= h($siteName) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/main.css?v=1">
  <link rel="canonical" href="<?= h($currentUrl) ?>">
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ContactPage",
    "name": "Contact INTSOLCOM",
    "description": "<?= h($metaDescription) ?>",
    "url": "<?= h($currentUrl) ?>"
  }
  </script>
  <style>
    .contact-form .form-input.error,
    .contact-form .form-textarea.error,
    .contact-form select.form-input.error { border-color:#ef4444; }
    .form-error-msg { color:#ef4444; font-size:.8125rem; margin-top:.25rem; }
    .form-success { background:rgba(0,200,150,.08); border:1px solid rgba(0,200,150,.2); border-radius:var(--radius-lg); padding:var(--space-6); text-align:center; }
    .form-success h3 { color:#00A67D; margin-bottom:.5rem; }
    .contact-cards { display:flex; flex-direction:column; gap:var(--space-6); }
    .contact-card-item { background:var(--color-white); border:1px solid var(--color-surface2); border-radius:var(--radius-lg); padding:var(--space-6); }
    .contact-card-item h4 { font-size:1rem; margin-bottom:.25rem; }
    .contact-card-item p { font-size:.875rem; }
    .contact-quick-actions { display:flex; gap:var(--space-3); flex-wrap:wrap; margin-top:var(--space-4); }
    .contact-quick-actions .btn { font-size:.8125rem; }
  </style>
</head>
<body>

<nav class="nav" id="nav">
  <div class="container">
    <a href="/" class="nav__logo">
      <span style="color:<?= h(setting('logo_text_color','#0F172A')) ?>"><?= h($logoText) ?></span><span style="color:<?= h(setting('logo_accent_color','#00C896')) ?>"><?= h($logoAccent) ?></span>
    </a>
    <div class="nav__links">
      <?php foreach ($navItems as $ni): ?>
        <?php if ($ni['is_cta']): ?><a href="<?= h($ni['url']) ?>" class="btn btn-accent btn-sm nav__cta"><?= ht($ni['text']) ?></a>
        <?php else: ?><a href="<?= h($ni['url']) ?>" class="nav__link"><?= ht($ni['text']) ?></a><?php endif; ?>
      <?php endforeach; ?>
    </div>
    <button class="nav__hamburger nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
  <div class="nav__mobile nav-mobile"><div class="nav__mobile-links">
    <?php foreach ($navItems as $ni): ?><a href="<?= h($ni['url']) ?>" class="nav__mobile-link"><?= ht($ni['text']) ?></a><?php endforeach; ?>
  </div></div>
</nav>

<main>
  <section class="hero" style="min-height:auto;padding-top:var(--space-40);padding-bottom:var(--space-16);">
    <div class="hero__grid"></div>
    <div class="hero__overlay"></div>
    <div class="container">
      <div class="hero__content" style="max-width:700px;">
        <div class="hero__badge"><span class="hero__badge-dot"></span> <?= t('Get in Touch') ?></div>
        <h1><?= t("Let's talk") ?></h1>
        <p class="hero__description"><?= t('Partner with a technology holding that delivers') ?></p>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="grid-2" style="align-items:flex-start;gap:var(--space-12);">
        <div class="reveal-left contact-form">
          <?php if ($success): ?>
            <div class="form-success">
              <div style="font-size:2rem;margin-bottom:.5rem;">✓</div>
              <h3><?= t('Thank you!') ?></h3>
              <p><?= t('Your message has been received. Our team will get back to you within 24 hours.') ?></p>
            </div>
          <?php else: ?>
              <?php if (!empty($errors['general'])): ?>
                <div class="form-error-msg" style="margin-bottom:var(--space-4);"><?= ht($errors['general']) ?></div>
              <?php endif; ?>
              <form method="POST" action="">
                <div class="form-group">
                  <label class="form-label" for="name"><?= t('Name') ?> *</label>
                  <input type="text" id="name" name="name" class="form-input <?= isset($errors['name']) ? 'error' : '' ?>" value="<?= h($formData['name']) ?>" placeholder="<?= t('Your full name') ?>" required>
                  <?php if (isset($errors['name'])): ?><div class="form-error-msg"><?= ht($errors['name']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label" for="email"><?= t('Email') ?> *</label>
                  <input type="email" id="email" name="email" class="form-input <?= isset($errors['email']) ? 'error' : '' ?>" value="<?= h($formData['email']) ?>" placeholder="<?= t('you@company.com') ?>" required>
                  <?php if (isset($errors['email'])): ?><div class="form-error-msg"><?= ht($errors['email']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label" for="company"><?= t('Company') ?></label>
                  <input type="text" id="company" name="company" class="form-input" value="<?= h($formData['company']) ?>" placeholder="<?= t('Company name') ?>">
                </div>
                <div class="grid-2" style="gap:var(--space-4);">
                  <div class="form-group">
                    <label class="form-label" for="phone"><?= t('Phone') ?></label>
                    <input type="tel" id="phone" name="phone" class="form-input <?= isset($errors['phone']) ? 'error' : '' ?>" value="<?= h($formData['phone']) ?>" placeholder="+1 (555) 000-0000">
                    <?php if (isset($errors['phone'])): ?><div class="form-error-msg"><?= ht($errors['phone']) ?></div><?php endif; ?>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="country"><?= t('Country') ?></label>
                    <input type="text" id="country" name="country" class="form-input" value="<?= h($formData['country']) ?>" placeholder="<?= t('Your country') ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label class="form-label" for="service_interest"><?= t('Service Interest') ?> *</label>
                  <select id="service_interest" name="service_interest" class="form-input <?= isset($errors['service_interest']) ? 'error' : '' ?>" required>
                    <option value=""><?= t('Select an option...') ?></option>
                    <?php foreach ($serviceOptions as $val => $label): ?>
                      <option value="<?= h($val) ?>" <?= $formData['service_interest'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                  </select>
                  <?php if (isset($errors['service_interest'])): ?><div class="form-error-msg"><?= ht($errors['service_interest']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                  <label class="form-label" for="message"><?= t('Message') ?> *</label>
                  <textarea id="message" name="message" class="form-textarea <?= isset($errors['message']) ? 'error' : '' ?>" rows="5" placeholder="<?= t('Tell us about your project or inquiry...') ?>" required><?= h($formData['message']) ?></textarea>
                  <?php if (isset($errors['message'])): ?><div class="form-error-msg"><?= ht($errors['message']) ?></div><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-accent btn-lg" style="width:100%;"><?= t('Send Message') ?></button>
              </form>
          <?php endif; ?>
        </div>

        <div class="reveal-right contact-cards">
          <div class="contact-card-item">
            <h4>INTSOLCOM LLC <span style="font-size:.75rem;color:var(--color-light);">(USA)</span></h4>
            <?php if ($usaAddress): ?><p style="color:var(--color-mid);"><?= ht($usaAddress) ?></p><?php endif; ?>
            <?php if ($usaPhone): ?><p style="color:var(--color-mid);margin-top:.25rem;"><?= ht($usaPhone) ?></p><?php endif; ?>
          </div>
          <div class="contact-card-item">
            <h4>INTSOLCOM SAS <span style="font-size:.75rem;color:var(--color-light);">(Colombia)</span></h4>
            <?php if ($colAddress): ?><p style="color:var(--color-mid);"><?= ht($colAddress) ?></p><?php endif; ?>
            <?php if ($colEmail): ?><p style="color:var(--color-mid);margin-top:.25rem;"><?= h($colEmail) ?></p><?php endif; ?>
          </div>
          <div class="contact-card-item" style="background:var(--color-surface);">
            <h4><?= t('Quick Actions') ?></h4>
            <div class="contact-quick-actions">
              <a href="tel:<?= h(preg_replace('/[^+\d]/','',$usaPhone)) ?>" class="btn btn-outline btn-sm">📞 <?= t('Call US') ?></a>
              <?php if ($whatsapp): ?>
              <a href="https://wa.me/<?= h(preg_replace('/[^+\d]/','',$whatsapp)) ?>" class="btn btn-outline btn-sm" target="_blank" rel="noopener">💬 <?= t('WhatsApp') ?></a>
              <?php endif; ?>
              <?php if ($colEmail): ?>
              <a href="mailto:<?= h($colEmail) ?>" class="btn btn-outline btn-sm">✉️ <?= t('Email') ?></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a href="/" class="footer__logo"><?= h($logoText) ?><span style="color:<?= h(setting('logo_accent_color','#00C896')) ?>"><?= h($logoAccent) ?></span></a>
        <p class="footer__desc"><?= ht(setting('footer_desc', 'INTSOLCOM LLC is a technology holding company. We build and operate software platforms, AI products, and intelligent business services.')) ?></p>
        <div class="footer__social">
          <a href="<?= h(setting('social_linkedin','#')) ?>" class="footer__social-icon" aria-label="LinkedIn" target="_blank" rel="noopener">in</a>
        </div>
      </div>
      <div><div class="footer__heading"><?= t('Company') ?></div><div class="footer__links"><a href="/holding"><?= t('Holding') ?></a><a href="/business-units"><?= t('Business Units') ?></a><a href="/contact"><?= t('Contact') ?></a></div></div>
      <div><div class="footer__heading"><?= t('Solutions') ?></div><div class="footer__links"><a href="/technology"><?= t('Technology') ?></a><a href="/industries"><?= t('Industries') ?></a></div></div>
      <div><div class="footer__heading"><?= t('Resources') ?></div><div class="footer__links"><a href="/resources"><?= t('Insights') ?></a><a href="/blog"><?= t('Blog') ?></a></div></div>
      <div><div class="footer__heading"><?= t('Contact') ?></div><div class="footer__links"><a href="mailto:<?= h(setting('contact_col_email','info@intsolcom.com')) ?>"><?= h(setting('contact_col_email','info@intsolcom.com')) ?></a><a href="tel:<?= h(preg_replace('/[^+\d]/','',setting('contact_usa_phone','+1 (302) 555-0199'))) ?>"><?= h(setting('contact_usa_phone','+1 (302) 555-0199')) ?></a></div></div>
    </div>
    <div class="footer__bottom">
      <span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span>
      <div class="footer__bottom-links"><a href="/privacy"><?= t('Privacy Policy') ?></a><a href="/terms"><?= t('Terms of Service') ?></a><a href="/sitemap.xml"><?= t('Sitemap') ?></a></div>
    </div>
  </div>
</footer>

<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<script src="/assets/js/main.js?v=1"></script>
</body>
</html>
