<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
  header('Location: /business-units', true, 302);
  exit;
}

$stmt = db()->prepare("SELECT * FROM business_units WHERE slug = ? AND status = 1 LIMIT 1");
$stmt->execute([$slug]);
$unit = $stmt->fetch();

if (!$unit) {
  http_response_code(404);
  $page     = getPage('business-units');
  $sections = $page ? getSections($page['id']) : [];
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
<main><section class="section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;">
  <div><h1><?= ht('Business Unit Not Found') ?></h1><p style="margin-top:1rem;"><?= ht('The business unit you are looking for does not exist or has been removed.') ?></p><a href="/business-units" class="btn btn-accent" style="margin-top:2rem;"><?= ht('View All Business Units') ?></a></div>
</section></main>
<footer class="footer">
  <div class="container"><div class="footer__bottom"><span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span></div></div>
</footer>
<script src="/assets/js/main.js?v=1"></script>
</body></html>
<?php exit; }

$page     = getPage('business-units');
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName    = setting('site_name', 'INTSOLCOM');
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$metaTitle       = $unit['hero_title']      ?: ($unit['name'] . ' — ' . $siteName);
$metaDescription = $unit['hero_subtitle']   ?: substr(strip_tags($unit['description'] ?? ''), 0, 160);
$currentUrl      = SITE_URL . '/business-units/' . $unit['slug'];
$lang            = currentLang();

$capabilities = json_decode($unit['capabilities'] ?? '[]', true) ?: [];
$benefits     = json_decode($unit['benefits']     ?? '[]', true) ?: [];
$process      = json_decode($unit['process']      ?? '[]', true) ?: [];
$technologies = json_decode($unit['technologies'] ?? '[]', true) ?: [];
$industries   = json_decode($unit['industries']   ?? '[]', true) ?: [];

$buIcons = [
  'building' => '🏢', 'users' => '👥', 'brain' => '🧠', 'tags' => '🏷️',
  'chart' => '📊', 'shield' => '🛡️', 'globe' => '🌐', 'cpu' => '⚙️',
];
$iconChar = $buIcons[$unit['icon']] ?? '🏢';
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
    "@type": "Organization",
    "name": "<?= h($unit['name']) ?>",
    "description": "<?= h(strip_tags($unit['description'] ?? '')) ?>",
    "url": "<?= h($currentUrl) ?>",
    "parentOrganization": { "@type": "Organization", "name": "<?= h($siteName) ?>" }
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
          <?= ht('Business Unit') ?>
        </div>
        <h1><?= ht($unit['hero_title'] ?: $unit['name']) ?></h1>
        <p class="hero__description"><?= ht($unit['hero_subtitle'] ?: '') ?></p>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container-sm">
      <div class="reveal">
        <span class="section-label"><?= ht('About') ?></span>
        <p style="font-size:1.125rem;line-height:1.75;"><?= ht($unit['description']) ?></p>
      </div>
    </div>
  </section>

  <?php if (!empty($capabilities)): ?>
  <section class="section section-surface">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label"><?= ht('Capabilities') ?></span>
        <h2 class="section-title"><?= ht('What We Deliver') ?></h2>
      </div>
      <div class="grid-2">
        <?php foreach ($capabilities as $idx => $cap): ?>
        <div class="capability-card reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
          <div class="capability-card__icon"><?= h($iconChar) ?></div>
          <div class="capability-card__content">
            <h4><?= ht($cap['title']) ?></h4>
            <p><?= ht($cap['desc']) ?></p>
          </div>
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
        <h2 class="section-title"><?= ht('Why Choose Us') ?></h2>
      </div>
      <div class="grid-3">
        <?php foreach ($benefits as $idx => $b): ?>
        <div class="card card-hover reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
          <div class="card__icon card__icon--purple">✦</div>
          <h3><?= ht($b['title']) ?></h3>
          <p><?= ht($b['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($process)): ?>
  <section class="section section-surface">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label section-label--blue"><?= ht('Process') ?></span>
        <h2 class="section-title"><?= ht('How We Work') ?></h2>
      </div>
      <div class="grid-4">
        <?php foreach ($process as $idx => $step): ?>
        <div class="eco-card reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
          <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#00C896,#2563EB);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.125rem;margin:0 auto var(--space-4);"><?= h($step['step'] ?? ($idx + 1)) ?></div>
          <h3 style="font-size:1.0625rem;"><?= ht($step['title']) ?></h3>
          <p style="font-size:0.875rem;"><?= ht($step['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if (!empty($technologies) || !empty($industries)): ?>
  <section class="section">
    <div class="container">
      <?php if (!empty($technologies)): ?>
      <div class="reveal" style="margin-bottom:var(--space-10);">
        <span class="section-label"><?= ht('Technologies') ?></span>
        <div class="product-card__tags" style="margin-top:var(--space-4);">
          <?php foreach ($technologies as $tech): ?>
            <span class="product-card__tag" style="font-size:0.875rem;padding:0.35rem 0.875rem;"><?= ht($tech) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php if (!empty($industries)): ?>
      <div class="reveal">
        <span class="section-label section-label--purple"><?= ht('Industries') ?></span>
        <div class="product-card__tags" style="margin-top:var(--space-4);">
          <?php foreach ($industries as $ind): ?>
            <span class="product-card__tag" style="background:rgba(139,92,246,.08);color:#8B5CF6;font-size:0.875rem;padding:0.35rem 0.875rem;"><?= ht($ind) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="cta-section">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= ht('Interested in this Business Unit?') ?></h2>
      <p><?= ht('Let\'s discuss how ' . $unit['name'] . ' can support your business goals.') ?></p>
      <div class="cta-section__actions">
        <a href="/contact" class="btn btn-accent btn-lg"><?= ht('Contact us') ?></a>
        <a href="/business-units" class="btn btn-outline-white btn-lg"><?= ht('All Business Units') ?></a>
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
      <div>
        <div class="footer__heading"><?= ht('Company') ?></div>
        <div class="footer__links">
          <a href="/holding"><?= ht('Holding') ?></a>
          <a href="/business-units"><?= ht('Business Units') ?></a>
          <a href="/contact"><?= ht('Contact') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= ht('Solutions') ?></div>
        <div class="footer__links">
          <a href="/technology"><?= ht('Technology') ?></a>
          <a href="/industries"><?= ht('Industries') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= ht('Resources') ?></div>
        <div class="footer__links">
          <a href="/resources"><?= ht('Insights') ?></a>
          <a href="/blog"><?= ht('Blog') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= ht('Contact') ?></div>
        <div class="footer__links">
          <a href="mailto:<?= h(setting('contact_col_email','info@intsolcom.com')) ?>"><?= h(setting('contact_col_email','info@intsolcom.com')) ?></a>
          <a href="tel:<?= h(preg_replace('/[^+\d]/','',setting('contact_usa_phone','+1 (302) 555-0199'))) ?>"><?= h(setting('contact_usa_phone','+1 (302) 555-0199')) ?></a>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span>
      <div class="footer__bottom-links">
        <a href="/privacy"><?= ht('Privacy Policy') ?></a>
        <a href="/terms"><?= ht('Terms of Service') ?></a>
        <a href="/sitemap.xml"><?= ht('Sitemap') ?></a>
      </div>
    </div>
  </div>
</footer>

<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<script src="/assets/js/main.js?v=1"></script>
</body>
</html>
