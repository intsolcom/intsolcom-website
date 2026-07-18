<?php
require_once __DIR__ . '/includes/config.php';

$page     = getPage('holding');
$sections = $page ? getSections($page['id']) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName    = setting('site_name', 'INTSOLCOM');
$siteTagline = setting('site_tagline', 'Technology & Operations Ecosystem');
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$metaTitle       = $page['meta_title']       ?? 'Ecosystem — INTSOLCOM';
$metaDescription = $page['meta_desc']         ?? 'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.';
$currentUrl      = SITE_URL . '/holding';

$lang    = currentLang();
$heroVideoId = setting('holding_hero_video_id', '');
$heroOverlay = $heroVideoId ? '' : '<div class="hero__grid"></div><div class="hero__overlay"></div>';
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
    "@type": "Corporation",
    "name": "<?= h($siteName) ?>",
    "url": "<?= h(SITE_URL) ?>",
    "description": "<?= h($metaDescription) ?>",
    "foundingLocation": { "@type": "Place", "address": { "@type": "PostalAddress", "addressLocality": "Wilmington", "addressRegion": "DE", "addressCountry": "US" } }
  }
  </script>
</head>
<body>

<nav class="nav nav--transparent" id="nav">
  <div class="container">
    <a href="/" class="nav__logo">
      <span style="color:<?= h(setting('logo_text_color','#0F172A')) ?>"><?= h($logoText) ?></span><span style="color:<?= h(setting('logo_accent_color','#00C896')) ?>"><?= h($logoAccent) ?></span>
    </a>
    <div class="nav__links">
      <?php foreach ($navItems as $ni): ?>
        <?php if ($ni['is_cta']): ?>
          <a href="<?= h($ni['url']) ?>" class="btn btn-accent btn-sm nav__cta"><?= ht($ni['text']) ?></a>
        <?php else: ?>
          <a href="<?= h($ni['url']) ?>" class="nav__link"><?= ht($ni['text']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <button class="nav__hamburger nav-toggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="nav__mobile nav-mobile">
    <div class="nav__mobile-links">
      <?php foreach ($navItems as $ni): ?>
        <a href="<?= h($ni['url']) ?>" class="nav__mobile-link"><?= ht($ni['text']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</nav>

<main>
  <!-- ============ HERO ============ -->
  <?php
  $heroSec = null;
  foreach ($sections as $s) { if ($s['type'] === 'hero') { $heroSec = $s; break; } }
  $heroFields = $heroSec['fields'] ?? [];
  ?>
  <section class="hero">
    <?php if ($heroVideoId): ?>
      <div class="video-bg" data-video-id="<?= h($heroVideoId) ?>" data-v-mute="1" data-v-loop="1" data-v-controls="0" data-v-speed="<?= h(setting('hero_video_speed','1')) ?>" style="position:absolute;inset:0;z-index:0;"></div>
    <?php else: ?>
      <div class="hero__grid"></div>
      <div class="hero__overlay"></div>
    <?php endif; ?>
    <div class="container">
      <div class="hero__content">
        <div class="hero__badge">
          <span class="hero__badge-dot"></span>
          <?= ht($heroFields['badge'] ?? 'Technology & Operations Ecosystem') ?>
        </div>
        <h1><?= ht($heroFields['title'] ?? 'The <em>Intsolcom</em> Business Ecosystem') ?></h1>
        <p class="hero__description"><?= ht($heroFields['subtitle'] ?? 'Two entities, one ecosystem. Strategic business development in the United States. Operational delivery in Colombia.') ?></p>
        <div class="hero__actions">
          <a href="/contact" class="btn btn-accent btn-lg"><?= ht('Partner with us') ?></a>
          <a href="/business-units" class="btn btn-outline-white btn-lg"><?= ht('Explore Business Units') ?></a>
        </div>
        <?php
        $stats = [
          ['value' => '50', 'suffix' => '+', 'label' => 'Team Members'],
          ['value' => '3', 'suffix' => '', 'label' => 'Software Platforms'],
          ['value' => '10', 'suffix' => '+', 'label' => 'Industries'],
          ['value' => 'US', 'suffix' => '+CO', 'label' => 'Presence'],
        ];
        ?>
        <div class="hero__metrics">
          <?php $first = true; foreach ($stats as $st): ?>
            <?php if (!$first): ?><div class="hero__metric-divider"></div><?php endif; $first = false; ?>
            <div>
              <div class="hero__metric-value"><?= h($st['value']) ?><span style="font-size:.75em;color:#00C896;"><?= h($st['suffix']) ?></span></div>
              <div class="hero__metric-label"><?= ht($st['label']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <?php
  $sectionNum = 0;
  foreach ($sections as $sec):
    if ($sec['type'] === 'hero') continue;
    $sectionNum++;
    $f = $sec['fields'] ?? [];
    $bgClass = ($sectionNum % 2 === 0) ? 'section-surface' : '';
  ?>

  <?php if ($sec['type'] === 'text_image'): ?>
    <section class="section <?= $bgClass ?>">
      <div class="container">
        <div class="grid-2" style="align-items:center;">
          <div class="<?= ($sectionNum % 2 !== 0) ? 'reveal-left' : 'reveal-right' ?>">
            <span class="section-label"><?= ht($f['label'] ?? '') ?></span>
            <h2 class="section-title"><?= ht($f['title'] ?? '') ?></h2>
            <p class="section-subtitle" style="max-width:none;"><?= ht($f['text'] ?? '') ?></p>
            <?php if (!empty($f['link_text'])): ?>
              <a href="<?= h($f['link_url'] ?? '#') ?>" class="btn btn-outline" style="margin-top:var(--space-6);"><?= ht($f['link_text']) ?></a>
            <?php endif; ?>
          </div>
          <div class="<?= ($sectionNum % 2 !== 0) ? 'reveal-right' : 'reveal-left' ?>" style="display:flex;align-items:center;justify-content:center;">
            <?php if (!empty($f['image'])): ?>
              <img src="<?= h(UPLOAD_URL . $f['image']) ?>" alt="<?= h($f['title'] ?? '') ?>" style="max-width:100%;border-radius:var(--radius-lg);box-shadow:var(--shadow-lg);" loading="lazy">
            <?php else: ?>
              <div style="width:100%;aspect-ratio:4/3;background:linear-gradient(135deg,rgba(0,200,150,.08),rgba(37,99,235,.08));border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;font-size:3rem;"><?= h($f['icon'] ?? '✦') ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

  <?php elseif ($sec['type'] === 'ecosystem'): ?>
    <section class="section <?= $bgClass ?>">
      <div class="container" style="text-align:center;">
        <span class="section-label reveal"><?= ht($f['label'] ?? 'Business Ecosystem') ?></span>
        <h2 class="section-title reveal"><?= ht($f['title'] ?? 'The Intsolcom Business Ecosystem') ?></h2>
        <p class="section-subtitle reveal" style="margin:0 auto var(--space-12);"><?= ht($f['text'] ?? 'Two entities, one ecosystem. Strategic business development in the United States. Operational delivery in Colombia.') ?></p>

        <div class="ecosystem reveal">
          <div class="ecosystem__root">
            <div class="ecosystem__root-card ecosystem__root-card--accent">
              INTSOL<span style="font-weight:300;">COM</span> LLC <span style="font-size:.75rem;margin-left:.5rem;opacity:.7;">USA</span>
            </div>
          </div>
          <div class="ecosystem__connector">
            <div class="ecosystem__line-v"></div>
          </div>
          <div class="ecosystem__branches">
            <?php
            $buList = db()->query("SELECT id, name, slug, icon, description FROM business_units WHERE status = 1 ORDER BY order_num ASC")->fetchAll();
            foreach ($buList as $idx => $bu):
            ?>
            <div class="ecosystem__branch reveal" style="transition-delay:<?= $idx * 0.1 ?>s;">
              <div class="ecosystem__branch-line"></div>
              <a href="/business-units/<?= h($bu['slug']) ?>" class="ecosystem__branch-card" style="text-decoration:none;color:inherit;">
                <div style="font-size:1.5rem;margin-bottom:.5rem;"><?= h($bu['icon']) ?></div>
                <h4><?= ht($bu['name']) ?></h4>
                <span><?= ht('Business Unit') ?></span>
              </a>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

  <?php elseif ($sec['type'] === 'stats'): ?>
    <section class="section <?= $bgClass ?>">
      <div class="container" style="text-align:center;">
        <span class="section-label reveal"><?= ht($f['label'] ?? 'By the Numbers') ?></span>
        <h2 class="section-title reveal"><?= ht($f['title'] ?? 'INTSOLCOM at a Glance') ?></h2>
        <div class="stats-band reveal" style="margin-top:var(--space-8);">
          <?php
          $statItems = !empty($f['stats']) ? json_decode($f['stats'], true) : [
            ['value' => '50', 'suffix' => '+', 'label' => 'Team Members'],
            ['value' => '3', 'suffix' => '', 'label' => 'Software Platforms'],
            ['value' => '10', 'suffix' => '+', 'label' => 'Industries Served'],
            ['value' => '2', 'suffix' => '', 'label' => 'Countries'],
          ];
          $sf = true;
          foreach ($statItems as $si):
            if (!$sf): ?><div class="stats-band__divider"></div><?php endif; $sf = false;
          ?>
          <div class="stats-band__item">
            <div class="stats-band__value" data-count="<?= h($si['value']) ?>" data-suffix="<?= h($si['suffix'] ?? '') ?>">0<?= h($si['suffix'] ?? '') ?></div>
            <div class="stats-band__label"><?= ht($si['label']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

  <?php elseif ($sec['type'] === 'cta'): ?>
    <section class="cta-section">
      <div class="cta-section__glow"></div>
      <div class="cta-section__glow cta-section__glow--right"></div>
      <div class="container">
        <h2><?= ht($f['title'] ?? "Let's Build Together") ?></h2>
        <p><?= ht($f['text'] ?? 'Partner with the Intsolcom business ecosystem for strategic business development in the United States and operational excellence in Colombia.') ?></p>
        <div class="cta-section__actions">
          <a href="/contact" class="btn btn-accent btn-lg"><?= ht($f['btn_text'] ?? 'Partner with us') ?></a>
          <?php if (!empty($f['btn2_text'])): ?>
            <a href="<?= h($f['btn2_url'] ?? '#') ?>" class="btn btn-outline-white btn-lg"><?= ht($f['btn2_text']) ?></a>
          <?php endif; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <?php endforeach; ?>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a href="/" class="footer__logo"><?= h($logoText) ?><span style="color:<?= h(setting('logo_accent_color','#00C896')) ?>"><?= h($logoAccent) ?></span></a>
        <p class="footer__desc"><?= ht(setting('footer_desc', 'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.')) ?></p>
        <div class="footer__social">
          <a href="<?= h(setting('social_linkedin','#')) ?>" class="footer__social-icon" aria-label="LinkedIn" target="_blank" rel="noopener">in</a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= ht('Company') ?></div>
        <div class="footer__links">
          <a href="/holding"><?= ht('Ecosystem') ?></a>
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
          <?php $colEmail = setting('contact_col_email','info@intsolcom.com'); ?>
          <a href="mailto:<?= h($colEmail) ?>"><?= h($colEmail) ?></a>
          <?php $usaPhone = setting('contact_usa_phone','+1 (302) 555-0199'); ?>
          <a href="tel:<?= h(preg_replace('/[^+\d]/','',$usaPhone)) ?>"><?= h($usaPhone) ?></a>
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
