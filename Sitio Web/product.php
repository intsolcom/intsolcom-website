<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
  header('Location: /technology', true, 302);
  exit;
}

$stmt = db()->prepare("SELECT * FROM products WHERE slug = ? AND status = 1 LIMIT 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
  http_response_code(404);
  $navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();
  $siteName = setting('site_name', 'INTSOLCOM');
  $logoText = setting('logo_text', 'INTSOL');
  $logoAccent = setting('logo_accent', 'COM');
  $lang = currentLang();
?><!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 — <?= h($siteName) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/main.css?v=1">
</head>
<body>
<nav class="nav" id="nav"><div class="container">
  <a href="/" class="nav__logo"><span style="color:<?= h(setting('logo_text_color','#0F172A')) ?>"><?= h($logoText) ?></span><span style="color:<?= h(setting('logo_accent_color','#00C896')) ?>"><?= h($logoAccent) ?></span></a>
  <div class="nav__links"><?php foreach ($navItems as $ni): ?><?php if ($ni['is_cta']): ?><a href="<?= h($ni['url']) ?>" class="btn btn-accent btn-sm nav__cta"><?= ht($ni['text']) ?></a><?php else: ?><a href="<?= h($ni['url']) ?>" class="nav__link"><?= ht($ni['text']) ?></a><?php endif; ?><?php endforeach; ?></div>
  <button class="nav__hamburger nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
</div><div class="nav__mobile nav-mobile"><div class="nav__mobile-links"><?php foreach ($navItems as $ni): ?><a href="<?= h($ni['url']) ?>" class="nav__mobile-link"><?= ht($ni['text']) ?></a><?php endforeach; ?></div></div></nav>
<main><section class="section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;"><div><h1><?= ht('Product Not Found') ?></h1><p style="margin-top:1rem;"><?= ht('The product you are looking for does not exist or has been removed.') ?></p><a href="/technology" class="btn btn-accent" style="margin-top:2rem;"><?= ht('View All Products') ?></a></div></section></main>
<footer class="footer"><div class="container"><div class="footer__bottom"><span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span></div></div></footer>
<script src="/assets/js/main.js?v=1"></script>
</body></html>
<?php exit; }

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName    = setting('site_name', 'INTSOLCOM');
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$metaTitle       = $product['hero_title']     ?: ($product['name'] . ' — ' . $siteName);
$metaDescription = $product['hero_subtitle']  ?: substr(strip_tags($product['short_desc'] ?? $product['description'] ?? ''), 0, 160);
$currentUrl      = SITE_URL . '/technology/' . $product['slug'];
$lang            = currentLang();

$cat       = $product['category'] ?? '';
$features  = json_decode($product['features']  ?? '[]', true) ?: [];
$benefits  = json_decode($product['benefits']  ?? '[]', true) ?: [];
$useCases  = json_decode($product['use_cases'] ?? '[]', true) ?: [];

$catColors = ['CRM' => ['#00C896','rgba(0,200,150,.08)'], 'AI Platform' => ['#8B5CF6','rgba(139,92,246,.08)']];
$catStyle  = $catColors[$cat] ?? ['#00C896','rgba(0,200,150,.08)'];
$prodIcons = ['users'=>'👥','brain'=>'🧠','tags'=>'🏷️','chart'=>'📊','shield'=>'🛡️','globe'=>'🌐','cpu'=>'⚙️'];
$iconKey   = $product['icon'] ?? 'cpu';
$iconChar  = $prodIcons[$iconKey] ?? '⚙️';
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
    "@type": "SoftwareApplication",
    "name": "<?= h($product['name']) ?>",
    "description": "<?= h(strip_tags($product['description'] ?? '')) ?>",
    "url": "<?= h($currentUrl) ?>",
    "applicationCategory": "BusinessApplication",
    "offers": { "@type": "Offer", "url": "<?= h($product['demo_cta_url'] ?? $currentUrl) ?>" }
  }
  </script>
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
        <div class="hero__badge">
          <span class="hero__badge-dot"></span>
          <?php if ($cat): ?><span style="color:<?= h($catStyle[0]) ?>;"><?= ht($cat) ?></span><?php else: ?><?= ht('Product') ?><?php endif; ?>
        </div>
        <h1><?= ht($product['hero_title'] ?: $product['name']) ?></h1>
        <p class="hero__description"><?= ht($product['hero_subtitle'] ?: '') ?></p>
        <?php if (!empty($product['demo_cta_url'])): ?>
        <div class="hero__actions">
          <a href="<?= h($product['demo_cta_url']) ?>" class="btn btn-accent btn-lg"><?= ht($product['demo_cta_text'] ?: 'Request a Demo') ?></a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if (!empty($product['overview'])): ?>
  <section class="section">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label"><?= ht('Overview') ?></span>
        <div style="font-size:1.125rem;line-height:1.75;color:var(--color-mid);"><?= $product['overview'] ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($product['problem'])): ?>
  <section class="section section-surface">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label" style="color:var(--color-purple);"><?= ht('The Problem') ?></span>
        <h2 class="section-title" style="margin-bottom:var(--space-4);"><?= ht('The Challenge') ?></h2>
        <div style="line-height:1.75;color:var(--color-mid);"><?= $product['problem'] ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($product['solution'])): ?>
  <section class="section">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label" style="color:var(--color-accent);"><?= ht('The Solution') ?></span>
        <h2 class="section-title" style="margin-bottom:var(--space-4);"><?= ht('How We Solve It') ?></h2>
        <div style="line-height:1.75;color:var(--color-mid);"><?= $product['solution'] ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($features)): ?>
  <section class="section section-surface">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label"><?= ht('Features') ?></span>
        <h2 class="section-title"><?= ht('Everything You Need') ?></h2>
      </div>
      <div class="grid-3">
        <?php foreach ($features as $idx => $feat): ?>
        <div class="card card-hover reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
          <div class="card__icon">✦</div>
          <h3><?= ht($feat['title']) ?></h3>
          <p><?= ht($feat['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($benefits)): ?>
  <section class="section">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label section-label--purple"><?= ht('Benefits') ?></span>
        <h2 class="section-title"><?= ht('The Impact') ?></h2>
      </div>
      <div class="grid-3">
        <?php foreach ($benefits as $idx => $b): ?>
        <div class="card card-hover reveal" style="transition-delay:<?= $idx * 0.05 ?>s;text-align:center;">
          <div class="card__icon card__icon--purple" style="margin:0 auto var(--space-5);">★</div>
          <h3><?= ht($b['title']) ?></h3>
          <p><?= ht($b['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($useCases)): ?>
  <section class="section section-surface">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label section-label--blue"><?= ht('Use Cases') ?></span>
        <h2 class="section-title"><?= ht('Who Uses It') ?></h2>
      </div>
      <div class="grid-3">
        <?php foreach ($useCases as $idx => $uc): ?>
        <div class="card card-hover reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
          <div class="card__icon card__icon--blue">🎯</div>
          <h3><?= ht($uc['title']) ?></h3>
          <p><?= ht($uc['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($product['architecture'])): ?>
  <section class="section">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label"><?= ht('Architecture') ?></span>
        <h2 class="section-title" style="margin-bottom:var(--space-4);"><?= ht('Technical Architecture') ?></h2>
        <div style="line-height:1.75;color:var(--color-mid);"><?= $product['architecture'] ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($product['roadmap'])): ?>
  <section class="section section-surface">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label section-label--purple"><?= ht('Roadmap') ?></span>
        <h2 class="section-title" style="margin-bottom:var(--space-4);"><?= ht('What\'s Next') ?></h2>
        <div style="line-height:1.75;color:var(--color-mid);"><?= $product['roadmap'] ?></div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($product['demo_cta_url'])): ?>
  <section class="cta-section">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= ht('Ready to see ' . $product['name'] . ' in action?') ?></h2>
      <p><?= ht('Schedule a personalized demo with our product team.') ?></p>
      <div class="cta-section__actions">
        <a href="<?= h($product['demo_cta_url']) ?>" class="btn btn-accent btn-lg"><?= ht($product['demo_cta_text'] ?: 'Request a Demo') ?></a>
        <a href="/technology" class="btn btn-outline-white btn-lg"><?= ht('All Products') ?></a>
      </div>
    </div>
  </section>
  <?php endif; ?>
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
      <div><div class="footer__heading"><?= ht('Company') ?></div><div class="footer__links"><a href="/holding"><?= ht('Holding') ?></a><a href="/business-units"><?= ht('Business Units') ?></a><a href="/contact"><?= ht('Contact') ?></a></div></div>
      <div><div class="footer__heading"><?= ht('Solutions') ?></div><div class="footer__links"><a href="/technology"><?= ht('Technology') ?></a><a href="/industries"><?= ht('Industries') ?></a></div></div>
      <div><div class="footer__heading"><?= ht('Resources') ?></div><div class="footer__links"><a href="/resources"><?= ht('Insights') ?></a><a href="/blog"><?= ht('Blog') ?></a></div></div>
      <div><div class="footer__heading"><?= ht('Contact') ?></div><div class="footer__links"><a href="mailto:<?= h(setting('contact_col_email','info@intsolcom.com')) ?>"><?= h(setting('contact_col_email','info@intsolcom.com')) ?></a><a href="tel:<?= h(preg_replace('/[^+\d]/','',setting('contact_usa_phone','+1 (302) 555-0199'))) ?>"><?= h(setting('contact_usa_phone','+1 (302) 555-0199')) ?></a></div></div>
    </div>
    <div class="footer__bottom">
      <span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span>
      <div class="footer__bottom-links"><a href="/privacy"><?= ht('Privacy Policy') ?></a><a href="/terms"><?= ht('Terms of Service') ?></a><a href="/sitemap.xml"><?= ht('Sitemap') ?></a></div>
    </div>
  </div>
</footer>

<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<script src="/assets/js/main.js?v=1"></script>
</body>
</html>
