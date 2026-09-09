<?php
require_once __DIR__ . '/includes/config.php';

$page     = getPage('technology');
$sections = $page ? getSections($page['id']) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 AND url != '/industries' ORDER BY sort_order ASC")->fetchAll();
$products = db()->query("SELECT * FROM products WHERE status = 1 ORDER BY order_num ASC")->fetchAll();

$siteName    = setting('site_name', 'INTSOLCOM');
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$metaTitle       = $page['meta_title']       ?? 'Technology Portfolio — INTSOLCOM';
$metaDescription = $page['meta_desc']         ?? 'Software platforms and AI products built for enterprise. Explore WONTIA AIP, WONTIA Food Security, and WONTIA IA Annotation Suite.';
$currentUrl      = SITE_URL . '/technology';
$lang            = currentLang();

$catColors = [
  'AI Platform' => ['#00C896', 'rgba(0,200,150,.08)'],
  'Food Security' => ['#F59E0B', 'rgba(245,158,11,.08)'],
  'AI Data' => ['#2563EB', 'rgba(37,99,235,.08)'],
];
$prodIcons = [
  'users' => '👥', 'brain' => '🧠', 'tags' => '🏷️',
  'chart' => '📊', 'shield' => '🛡️', 'globe' => '🌐', 'cpu' => '⚙️',
  'wheat' => '🌾',
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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/main.css?v=2">
  <link rel="canonical" href="<?= h($currentUrl) ?>">
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "<?= h($metaTitle) ?>",
    "description": "<?= h($metaDescription) ?>",
    "url": "<?= h($currentUrl) ?>",
    "isPartOf": { "@type": "WebSite", "name": "<?= h($siteName) ?>", "url": "<?= h(SITE_URL) ?>" }
  }
  </script>
</head>
<body>

<nav class="nav nav--transparent" id="nav">
  <div class="container">
    <a href="/" class="nav__logo">
      <span class="nav__logo-text"><?= h($logoText) ?></span><span class="nav__logo-accent"><?= h($logoAccent) ?></span>
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
          <?= ht('Products') ?>
        </div>
        <h1><?= ht('Technology Portfolio') ?></h1>
        <p class="hero__description"><?= ht('Software platforms and AI products built for enterprise.') ?></p>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:var(--space-16);">
    <div class="container">
      <?php if (empty($products)): ?>
        <div class="text-center" style="padding:var(--space-16) 0;">
          <p style="color:var(--color-light);font-size:1.125rem;"><?= ht('No products found.') ?></p>
        </div>
      <?php else: ?>
        <div class="grid-3">
          <?php foreach ($products as $idx => $prod):
            $iconKey = $prod['icon'] ?? 'cpu';
            $iconChar = $prodIcons[$iconKey] ?? '⚙️';
            $cat = $prod['category'] ?? '';
            $catStyle = $catColors[$cat] ?? ['#00C896', 'rgba(0,200,150,.08)'];
            $gradClass = ($idx % 3 === 0) ? '' : (($idx % 3 === 1) ? 'product-card__gradient--purple' : 'product-card__gradient--blue');
          ?>
          <div class="product-card reveal" style="transition-delay:<?= $idx * 0.05 ?>s;">
            <div class="product-card__gradient <?= $gradClass ?>"></div>
            <div class="product-card__header">
              <div class="card__icon" style="font-size:1.5rem;"><?= h($iconChar) ?></div>
              <?php if ($cat): ?>
                <span class="eco-card__badge" style="color:<?= h($catStyle[0]) ?>;background:<?= h($catStyle[1]) ?>;"><?= ht($cat) ?></span>
              <?php endif; ?>
              <h3><?= ht($prod['name']) ?></h3>
            </div>
            <div class="product-card__body">
              <p><?= ht($prod['short_desc'] ?? $prod['description']) ?></p>
              <a href="/technology/<?= h($prod['slug']) ?>" class="btn btn-ghost btn-sm" style="margin-top:var(--space-4);padding-left:0;">
                <?= ht('Explore Product') ?> →
              </a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="section" style="padding-top:0;">
    <div class="container">
      <div class="section-header reveal">
        <span class="section-label"><?= ht('The WONTIA Ecosystem') ?></span>
        <h2 class="section-title">One intelligence. <em>Three products.</em></h2>
        <p class="section-subtitle" style="max-width:640px;margin:0 auto;">
          <?= ht('All our products run on the same WONTIA + TIA core — friendly, focused, and built to deliver measurable outcomes.') ?>
        </p>
      </div>
      <div class="grid-3" style="margin-top:var(--space-10);">
        <div class="card card-hover reveal">
          <div class="card__icon card__icon--blue">🏷️</div>
          <span class="eco-card__badge" style="color:#2563EB;background:rgba(37,99,235,.08);"><?= ht('AI Data') ?></span>
          <h3 style="margin-top:var(--space-3);"><?= ht('WONTIA IA ANNOTATION SUITE') ?></h3>
          <p><?= ht('AI data annotation at scale. Manage projects, verify quality, and measure your annotation teams — with its own landing page at iaam.com.') ?></p>
          <a href="https://iaam.com" target="_blank" rel="noopener" style="color:#2563EB;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;margin-top:var(--space-4);"><?= ht('Visit iaam.com') ?> ↗</a>
        </div>
        <div class="card card-hover reveal" style="transition-delay:.05s;">
          <div class="card__icon">🧠</div>
          <span class="eco-card__badge" style="color:#00C896;background:rgba(0,200,150,.08);"><?= ht('AI Platform') ?></span>
          <h3 style="margin-top:var(--space-3);"><?= ht('WONTIA AIP') ?></h3>
          <p><?= ht('Your intelligence layer. WONTIA AIP powers every WONTIA product with TIA — Technology of Applied Intelligence. Understand context, make decisions, execute actions.') ?></p>
          <a href="https://wontia.com" target="_blank" rel="noopener" style="color:#00C896;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;margin-top:var(--space-4);"><?= ht('Visit wontia.com') ?> ↗</a>
        </div>
        <div class="card card-hover reveal" style="transition-delay:.1s;">
          <div class="card__icon" style="background:rgba(245,158,11,.08);">🌾</div>
          <span class="eco-card__badge" style="color:#F59E0B;background:rgba(245,158,11,.08);"><?= ht('Food Security') ?></span>
          <h3 style="margin-top:var(--space-3);"><?= ht('WONTIA FOOD SECURITY') ?></h3>
          <p><?= ht('Applied intelligence for food security. Detect risk, prioritize response, coordinate action, and measure impact — the same WONTIA intelligence, feeding people better.') ?></p>
          <a href="https://wontia.com/#food-security" target="_blank" rel="noopener" style="color:#F59E0B;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;margin-top:var(--space-4);"><?= ht('Learn more') ?> ↗</a>
        </div>
      </div>
      <div class="text-center reveal" style="margin-top:var(--space-10);">
        <a href="https://wontia.com" target="_blank" rel="noopener" class="btn btn-accent btn-lg"><?= ht('Explore the WONTIA ecosystem') ?> ↗</a>
      </div>
    </div>
  </section>

  <section class="section section-surface" style="margin-top:var(--space-16);">
    <div class="container" style="text-align:center;">
      <span class="section-label reveal section-label--purple"><?= ht('Coming Soon') ?></span>
      <h2 class="section-title reveal"><?= ht('Future Products') ?></h2>
      <p class="section-subtitle reveal" style="margin:0 auto var(--space-8);"><?= ht('Our technology roadmap is always expanding. New platforms and AI products are in development to address emerging enterprise challenges.') ?></p>
      <div class="grid-auto-sm" style="margin-top:var(--space-10);">
        <div class="card reveal" style="text-align:center;border-style:dashed;border-color:var(--color-surface2);">
          <div class="card__icon card__icon--blue" style="margin:0 auto var(--space-4);">🔮</div>
          <h3 style="font-size:1rem;"><?= ht('AI Governance Suite') ?></h3>
          <p style="font-size:0.875rem;"><?= ht('Enterprise-grade AI governance, compliance, and monitoring platform.') ?></p>
        </div>
        <div class="card reveal" style="text-align:center;border-style:dashed;border-color:var(--color-surface2);transition-delay:.1s;">
          <div class="card__icon card__icon--purple" style="margin:0 auto var(--space-4);">🚀</div>
          <h3 style="font-size:1rem;"><?= ht('Supply Chain AI') ?></h3>
          <p style="font-size:0.875rem;"><?= ht('Predictive logistics and intelligent supply chain orchestration.') ?></p>
        </div>
        <div class="card reveal" style="text-align:center;border-style:dashed;border-color:var(--color-surface2);transition-delay:.2s;">
          <div class="card__icon" style="margin:0 auto var(--space-4);">💡</div>
          <h3 style="font-size:1rem;"><?= ht('Your Idea Here') ?></h3>
          <p style="font-size:0.875rem;"><?= ht('Have a product vision? Partner with INTSOLCOM to bring it to life.') ?></p>
        </div>
      </div>
    </div>
  </section>

  <section class="cta-section" style="margin-top:var(--space-16);">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= ht('Ready to see our technology in action?') ?></h2>
      <p><?= ht('Schedule a demo with our team and discover how our platforms can transform your operations.') ?></p>
      <div class="cta-section__actions">
        <a href="/contact" class="btn btn-accent btn-lg"><?= ht('Request a Demo') ?></a>
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

<script src="/assets/js/main.js?v=2"></script>
</body>
</html>
