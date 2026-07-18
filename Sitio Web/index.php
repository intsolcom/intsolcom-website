<?php
require_once __DIR__ . '/includes/config.php';

$page = getPage('home');
$sections = $page ? getSections($page['id'] ?? 1) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();
$s = function(string $key, string $def = '') { return setting($key, $def); };
$curLang = currentLang();

$themeVars = implode(';', [
    "--bg:{$s('color_bg','#FFFFFF')}",
    "--surface:{$s('color_surface','#F8FAFC')}",
    "--surface2:{$s('color_surface2','#E2E8F0')}",
    "--dark:{$s('color_dark','#0F172A')}",
    "--mid:{$s('color_mid','#475569')}",
    "--light:{$s('color_light','#94A3B8')}",
    "--accent:{$s('color_accent','#00C896')}",
    "--accent-dk:{$s('color_accent_dk','#00A67D')}",
    "--accent-bg:rgba(0,200,150,0.07)",
    "--accent-brd:rgba(0,200,150,0.18)",
    "--secondary:{$s('color_secondary','#2563EB')}",
    "--purple:{$s('color_purple','#8B5CF6')}",
    "--white:{$s('color_white','#FFFFFF')}",
    "--font-display:'Inter',sans-serif",
    "--font-body:'Inter',sans-serif",
    "--nav-h:{$s('nav_h','76')}",
    "--nav-h-scroll:{$s('nav_h_scrolled','62')}",
    "--nav-bg:{$s('nav_bg','transparent')}",
    "--nav-bg-scroll:{$s('nav_bg_scrolled','rgba(255,255,255,0.95)')}",
    "--nav-blur:{$s('nav_blur','20')}",
]);

$metaTitle = $s('site_tagline')
    ? h($s('site_name','INTSOLCOM')) . ' — ' . h($s('site_tagline','Technology & Operations Ecosystem'))
    : 'INTSOLCOM LLC — Technology & Operations Ecosystem | AI | Business Operations | Software Products';
$metaDesc  = h($s('site_desc', 'INTSOLCOM LLC is a technology holding company that owns and operates specialized business units, software platforms and AI products.'));
$ogImage   = $s('og_image', SITE_URL . '/assets/uploads/og-default.jpg');

// Hardcoded sections (used as fallback when DB sections have no fields)
$hardSections = [
    ['type' => 'hero',            'sort' => 10],
    ['type' => 'ecosystem',       'sort' => 20],
    ['type' => 'stats',           'sort' => 30],
    ['type' => 'products_grid',   'sort' => 40],
    ['type' => 'capabilities',    'sort' => 50],
    ['type' => 'industries_grid', 'sort' => 60],
    ['type' => 'comparison',      'sort' => 70],
    ['type' => 'cta',             'sort' => 80],
    ['type' => 'testimonials',    'sort' => 90],
    ['type' => 'faq',             'sort' => 100],
];

if (empty($sections)) {
    $sections = $hardSections;
}

// DB testimonials
$testimonials = db()->query("SELECT * FROM testimonials WHERE visible = 1 ORDER BY sort_order ASC LIMIT 3")->fetchAll();

// DB clients for hero trust strip
$clients = db()->query("SELECT * FROM clients WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

// Products from DB
$products = db()->query("SELECT * FROM products WHERE status = 1 ORDER BY order_num ASC LIMIT 3")->fetchAll();

// Industry names for grid
$industryNames = [
    'Healthcare', 'Technology', 'Financial Services', 'AI & Data',
    'Retail', 'Logistics', 'Real Estate', 'Professional Services',
    'Manufacturing', 'Hospitality'
];
$industryIcons = [
    'Healthcare' => '\2665', 'Technology' => '\25CB',
    'Financial Services' => '\0024', 'AI & Data' => '\2601',
    'Retail' => '\263C', 'Logistics' => '\2192',
    'Real Estate' => '\2302', 'Professional Services' => '\25A0',
    'Manufacturing' => '\2699', 'Hospitality' => '\2605'
];
?><!DOCTYPE html>
<html lang="<?= $curLang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0F172A">
<meta name="color-scheme" content="light dark">
<title><?= $metaTitle ?></title>
<meta name="description" content="<?= $metaDesc ?>">
<meta name="author" content="INTSOLCOM LLC">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<meta property="og:type" content="website">
<meta property="og:site_name" content="INTSOLCOM LLC">
<meta property="og:title" content="<?= $metaTitle ?>">
<meta property="og:description" content="<?= $metaDesc ?>">
<meta property="og:image" content="<?= $ogImage ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $metaTitle ?>">
<meta name="twitter:description" content="<?= $metaDesc ?>">
<meta name="twitter:image" content="<?= $ogImage ?>">

<meta name="video-config" content='{"mute":1,"autoplay":1,"loop":1,"controls":0,"rel":0,"modestbranding":1,"showinfo":0,"iv_load_policy":3,"disablekb":1,"playsinline":1,"speed":1,"layout":"cover","voffset":0}'>

<link rel="stylesheet" href="/assets/css/main.css?v=<?= filemtime(__DIR__.'/assets/css/main.css') ?>">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "INTSOLCOM LLC",
  "url": "<?= SITE_URL ?>",
  "logo": "<?= SITE_URL ?>/assets/uploads/logo.png",
  "description": "<?= $metaDesc ?>",
  "foundingDate": "2024",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "1209 Orange Street",
    "addressLocality": "Wilmington",
    "addressRegion": "DE",
    "postalCode": "19801",
    "addressCountry": "US"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+1-302-555-0199",
    "contactType": "sales",
    "email": "info@intsolcom.com"
  },
  "sameAs": [
    "https://linkedin.com/company/intsolcom"
  ],
  "numberOfEmployees": {
    "@type": "QuantitativeValue",
    "value": "300+"
  },
  "knowsAbout": [
    "Software Development",
    "Artificial Intelligence",
    "Data Annotation",
    "Business Process Outsourcing",
    "Customer Relationship Management",
    "Decision Intelligence"
  ]
}
</script>

<style>
:root { <?= $themeVars ?> }

/* Bridge: JS uses .scrolled, CSS uses .nav--scrolled */
.nav.scrolled {
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 1px 2px rgba(15,23,42,.04);
  padding: .75rem 0;
}
.nav.scrolled .nav__logo { color: #0F172A; }
.nav.scrolled .nav__link { color: #475569; }
.nav.scrolled .nav__hamburger span { background: #0F172A; }

/* Bridge: JS toggles .open, CSS uses .active on mobile */
.nav-mobile.open { opacity: 1; pointer-events: auto; }
.nav-toggle.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.nav-toggle.open span:nth-child(2) { opacity: 0; }
.nav-toggle.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* Bridge: JS toggles .hov on cursor-ring, CSS uses .cursor-ring--hover */
.cursor-ring.hov { transform: translate(-50%,-50%) scale(1.6); border-color: rgba(0,200,150,.4); background: rgba(0,200,150,.06); }

/* Hero video wrapper */
.video-bg { position: absolute; inset: 0; overflow: hidden; z-index: 0; }
.video-bg iframe { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); min-width: 100%; min-height: 100%; width: auto; height: auto; pointer-events: none; border: none; }
.video-overlay { position: absolute; inset: 0; z-index: 1; background: radial-gradient(ellipse at 50% 0%, rgba(0,200,150,0.08) 0%, transparent 60%), linear-gradient(180deg, rgba(15,23,42,0.6) 0%, rgba(15,23,42,0.85) 50%, rgba(15,23,42,0.95) 100%); }

/* Ecosystem diagram connectors */
.eco-diagram { position: relative; padding: var(--space-8) 0; }
.eco-top { display: flex; justify-content: center; margin-bottom: var(--space-4); }
.eco-top-card { background: #0F172A; color: #fff; border: 2px solid #00C896; padding: var(--space-5) var(--space-10); border-radius: 16px; font-weight: 700; font-size: 1.125rem; text-align: center; box-shadow: 0 8px 32px rgba(0,200,150,0.15); }
.eco-connectors { display: flex; justify-content: center; gap: var(--space-12); position: relative; margin-bottom: var(--space-4); flex-wrap: wrap; }
.eco-vline { width: 2px; height: 40px; background: rgba(0,200,150,0.3); }
.eco-branches { display: flex; justify-content: center; gap: var(--space-6); flex-wrap: wrap; }
.eco-branch { display: flex; flex-direction: column; align-items: center; gap: var(--space-3); flex: 1; min-width: 200px; max-width: 280px; }
.eco-branch-line { width: 2px; height: 30px; background: rgba(0,200,150,0.3); }
.eco-card { opacity: 0; transform: translateY(20px); transition: opacity .5s ease, transform .5s ease; }
.eco-card.visible { opacity: 1; transform: translateY(0); }
.eco-card-capabilities { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .75rem; justify-content: center; }
.eco-card-cap { font-size: .68rem; background: rgba(0,200,150,0.08); color: #00C896; padding: .2rem .55rem; border-radius: 20px; white-space: nowrap; }

/* No-scroll body */
body.no-scroll { overflow: hidden; }

/* Scroll to top */
#scroll-top { position: fixed; bottom: 2rem; right: 2rem; width: 44px; height: 44px; border-radius: 50%; background: #0F172A; color: #fff; font-size: 1.25rem; border: none; cursor: pointer; z-index: 300; opacity: 0; visibility: hidden; transition: all .3s ease; box-shadow: 0 4px 16px rgba(0,0,0,.15); display: flex; align-items: center; justify-content: center; }
#scroll-top.visible { opacity: 1; visibility: visible; }
#scroll-top:hover { background: #00C896; color: #0F172A; transform: translateY(-2px); }

/* Chat widget placeholder */
.chat-widget { position: fixed; bottom: 2rem; left: 2rem; z-index: 300; }

/* Visual section connectors */
.eco-label-sub { font-size: .78rem; color: #94A3B8; margin-top: .25rem; }
.eco-card-tag { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #00C896; margin-bottom: .5rem; }
</style>
</head>
<body>

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<!-- ============================================================
     NAVIGATION
     ============================================================ -->
<nav class="nav nav--transparent">
  <div class="container">
    <a href="/" class="nav__logo">
      <?= h($s('logo_text','INTSOL')) ?><span style="color:var(--accent)"><?= h($s('logo_accent','COM')) ?></span>
    </a>
    <div class="nav__links">
      <?php foreach ($navItems as $item): ?>
        <?php if ($item['is_cta']): ?>
          <a href="<?= h($item['url']) ?>" class="btn btn-accent nav__cta"><?= ht($item['text']) ?></a>
        <?php else: ?>
          <a href="<?= h($item['url']) ?>" class="nav__link"><?= ht($item['text']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php require __DIR__ . '/includes/lang_switch.php'; ?>
    </div>
    <div class="nav__hamburger nav-toggle">
      <span></span><span></span><span></span>
    </div>
  </div>
  <!-- Mobile Overlay -->
  <div class="nav__mobile nav-mobile">
    <div class="nav__mobile-links">
      <?php foreach ($navItems as $item): ?>
        <?php if ($item['is_cta']): ?>
          <a href="<?= h($item['url']) ?>" class="btn btn-accent nav__cta"><?= ht($item['text']) ?></a>
        <?php else: ?>
          <a href="<?= h($item['url']) ?>" class="nav__mobile-link"><?= ht($item['text']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php require __DIR__ . '/includes/lang_switch.php'; ?>
    </div>
  </div>
</nav>

<!-- ============================================================
     SECTIONS LOOP
     ============================================================ -->
<?php foreach ($sections as $sec):
    $type = $sec['type'] ?? 'unknown';
    $fields = $sec['fields'] ?? [];
?>

<?php if ($type === 'hero'): ?>
<!-- HERO SECTION -->
<section class="section hero" id="hero" data-nav-section="home">
  <?php $vId = $fields['video_id'] ?? $s('hero_video_id',''); ?>
  <?php if ($vId): ?>
    <div class="video-bg" data-video-id="<?= h($vId) ?>" data-v-layout="cover" data-v-offset="-10"></div>
    <div class="video-overlay"></div>
  <?php else: ?>
    <div class="hero__grid"></div>
  <?php endif; ?>
  <div class="container relative">
    <div class="hero__content">
      <div class="hero__badge reveal">
        <span class="hero__badge-dot"></span>
        <?= ht($fields['eyebrow'] ?? 'Technology & Operations Ecosystem') ?>
      </div>
      <h1 class="reveal" data-delay="100">
        <?= ht($fields['headline'] ?? 'We build and operate') ?> <em><?= ht($fields['headline_em'] ?? 'technology companies.') ?></em>
      </h1>
      <p class="hero__description reveal" data-delay="200">
        <?= ht($fields['description'] ?? 'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia. We build technology products and operate business services at scale.') ?>
      </p>
      <div class="hero__actions reveal" data-delay="300">
        <a href="/holding" class="btn btn-accent btn-lg">
          <?= t('Explore Our Ecosystem') ?> →
        </a>
        <a href="/technology" class="btn btn-outline-white btn-lg">
          <?= t('Meet Our Products') ?>
        </a>
      </div>
      <div class="hero__metrics reveal" data-delay="400">
        <div class="hero__metric">
          <div class="hero__metric-value">55%</div>
          <div class="hero__metric-label"><?= t('Cost Reduction') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">14 days</div>
          <div class="hero__metric-label"><?= t('Deployment') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">98%</div>
          <div class="hero__metric-label"><?= t('Client Retention') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">300+</div>
          <div class="hero__metric-label"><?= t('Professionals') ?></div>
        </div>
      </div>
      <div class="hero__trust reveal" data-delay="500">
        <span class="hero__trust-text"><?= t('Trusted by innovative companies worldwide') ?></span>
        <div class="hero__trust-logos">
          <?php if ($clients): foreach ($clients as $c): ?>
            <span style="font-size:.8rem; color:rgba(255,255,255,.5); letter-spacing:.04em; font-weight:600;"><?= h($c['name']) ?></span>
          <?php endforeach; else: ?>
            <span style="font-size:.8rem; color:rgba(255,255,255,.5); letter-spacing:.04em; font-weight:600;">HEALTHCARE TECH</span>
            <span style="font-size:.8rem; color:rgba(255,255,255,.5); letter-spacing:.04em; font-weight:600;">FINTECH LABS</span>
            <span style="font-size:.8rem; color:rgba(255,255,255,.5); letter-spacing:.04em; font-weight:600;">LOGISTICS AI</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <!-- Ambient gradient blobs -->
  <div class="gradient-blob gradient-blob--1 gradient-blob--dark"></div>
  <div class="gradient-blob gradient-blob--3 gradient-blob--dark" style="top:60%;right:5%"></div>
</section>

<?php elseif ($type === 'ecosystem'): ?>
<!-- ECOSYSTEM SECTION -->
<section class="section section-surface" id="ecosystem" data-nav-section="ecosystem">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('The Intsolcom Business Ecosystem') ?></span>
      <h2 class="section-title"><?= t('A technology holding designed for the future.') ?></h2>
      <p class="section-subtitle"><?= t('Two entities working together. Strategic business development in the United States. Operational delivery in Colombia.') ?></p>
    </div>
    <div class="eco-diagram">
      <!-- Root -->
      <div class="eco-top reveal">
        <div class="eco-top-card">
          <span style="color:#fff; font-weight:600;">The Intsolcom Ecosystem</span>
          <div style="font-size:.72rem; font-weight:400; color:rgba(255,255,255,.6); margin-top:.2rem;">United States &amp; Colombia</div>
        </div>
      </div>
      <!-- Vertical connectors -->
      <div class="eco-connectors reveal" data-delay="100">
        <div class="eco-vline"></div>
        <div class="eco-vline"></div>
        <div class="eco-vline"></div>
      </div>
      <!-- Branches -->
      <div class="eco-branches">
        <div class="eco-branch">
          <div class="eco-branch-line"></div>
          <div class="eco-card reveal" data-delay="200">
            <div class="eco-card-tag"><?= t('Operational Delivery Center') ?></div>
            <div class="card__icon" style="font-size:1.5rem;margin:0 auto .75rem;">&#127464;&#127476;</div>
            <h3>INTSOLCOM SAS &mdash; Colombia</h3>
            <p style="font-size:.875rem;color:#475569;"><?= t('Part of the Intsolcom ecosystem. Nearshore operations and delivery hub in Barranquilla, Colombia.') ?></p>
            <div class="eco-card-capabilities">
              <span class="eco-card-cap"><?= t('BPO Operations') ?></span>
              <span class="eco-card-cap"><?= t('AI Data Annotation') ?></span>
              <span class="eco-card-cap"><?= t('QA & Training') ?></span>
              <span class="eco-card-cap"><?= t('Talent Management') ?></span>
            </div>
          </div>
        </div>
        <div class="eco-branch">
          <div class="eco-branch-line"></div>
          <div class="eco-card reveal" data-delay="300">
            <div class="eco-card-tag"><?= t('Product Division') ?></div>
            <div class="card__icon" style="font-size:1.5rem;margin:0 auto .75rem;">&#9881;</div>
            <h3><?= t('Technology & Products') ?></h3>
            <p style="font-size:.875rem;color:#475569;"><?= t('WONTIA CRM, MACROPONDER, and IA Annotation Manager — software products developed and operated within the ecosystem.') ?></p>
            <div class="eco-card-capabilities">
              <span class="eco-card-cap">WONTIA CRM</span>
              <span class="eco-card-cap">MACROPONDER</span>
              <span class="eco-card-cap"><?= t('IA Annotation Manager') ?></span>
            </div>
          </div>
        </div>
        <div class="eco-branch">
          <div class="eco-branch-line"></div>
          <div class="eco-card reveal" data-delay="400">
            <div class="eco-card-tag"><?= t('Commercial') ?></div>
            <div class="card__icon" style="font-size:1.5rem;margin:0 auto .75rem;">&#128300;</div>
            <h3><?= t('Business Development — USA') ?></h3>
            <p style="font-size:.875rem;color:#475569;"><?= t('Strategic business development in the United States. Partnerships, international sales, and innovation management.') ?></p>
            <div class="eco-card-capabilities">
              <span class="eco-card-cap"><?= t('Strategic Partnerships') ?></span>
              <span class="eco-card-cap"><?= t('International Sales') ?></span>
              <span class="eco-card-cap"><?= t('Product Management') ?></span>
              <span class="eco-card-cap"><?= t('Innovation') ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php elseif ($type === 'stats'): ?>
<!-- STATS BAND -->
<section class="section section-dark" id="stats" data-nav-section="stats">
  <div class="container">
    <div class="stats-band">
      <div class="stats-band__item reveal">
        <div class="stats-band__value stats-band__value--accent">
          <span data-count="65" data-suffix="%"></span>
        </div>
        <div class="stats-band__label"><?= t('Average Cost Savings') ?></div>
      </div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="100">
        <div class="stats-band__value">
          <span data-count="14"></span><span class="stats-band__suffix">d</span>
        </div>
        <div class="stats-band__label"><?= t('Deployment Time') ?></div>
      </div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="200">
        <div class="stats-band__value stats-band__value--accent">
          <span data-count="98.4" data-decimals="1" data-suffix="%"></span>
        </div>
        <div class="stats-band__label"><?= t('Annotation Accuracy') ?></div>
      </div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="300">
        <div class="stats-band__value">
          <span data-count="500" data-suffix="+"></span>
        </div>
        <div class="stats-band__label"><?= t('Projects Delivered') ?></div>
      </div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="400">
        <div class="stats-band__value stats-band__value--accent">
          <span data-count="300" data-suffix="+"></span>
        </div>
        <div class="stats-band__label"><?= t('Professionals Worldwide') ?></div>
      </div>
    </div>
  </div>
</section>

<?php elseif ($type === 'products_grid'): ?>
<!-- PRODUCTS GRID -->
<section class="section" id="products" data-nav-section="products">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Technology & Products') ?></span>
      <h2 class="section-title"><?= t('Technology products built for business impact') ?></h2>
      <p class="section-subtitle"><?= t('Purpose-built software products developed in-house, continuously improved, and deployed at enterprise scale.') ?></p>
    </div>
    <div class="grid-3">
      <?php
      $productDefaults = [
        ['icon' => '&#128101;', 'cat' => 'CRM', 'name' => 'WONTIA CRM', 'desc' => 'Intelligent CRM platform for service-based businesses. Contact management, pipeline tracking, and AI-powered insights.', 'url' => '/technology/wontia-crm', 'gradient' => ''],
        ['icon' => '&#129504;', 'cat' => 'AI Platform', 'name' => 'MACROPONDER', 'desc' => 'Decision intelligence platform. Scenario modeling, bias detection, and collaborative strategic analysis powered by AI.', 'url' => '/technology/macroponder', 'gradient' => '--purple'],
        ['icon' => '&#127991;', 'cat' => 'AI Platform', 'name' => 'IA Annotation Manager', 'desc' => 'End-to-end annotation management platform. Project management, quality control, and workforce analytics at scale.', 'url' => '/technology/ia-annotation-manager', 'gradient' => '--blue'],
      ];
      $displayProducts = $products ?: $productDefaults;
      foreach ($displayProducts as $idx => $prd):
        $pName = is_array($prd) ? ($prd['name'] ?? '') : '';
        $pSlug = is_array($prd) ? ($prd['slug'] ?? '') : '';
        $pDesc = is_array($prd) ? ($prd['short_desc'] ?? '') : '';
        $pCat  = is_array($prd) ? ($prd['category'] ?? '') : '';
        $pIcon = is_array($prd) ? ($prd['icon'] ?? '') : '';
        $pUrl  = is_array($prd) ? '/technology/' . $pSlug : '';
        $pGrad = $idx === 0 ? '' : ($idx === 1 ? '--purple' : '--blue');

        // Map icon strings to emoji
        $iconMap = ['users' => '&#128101;', 'brain' => '&#129504;', 'tags' => '&#127991;', 'building' => '&#127970;'];
        $iconDisplay = $iconMap[$pIcon] ?? $productDefaults[$idx]['icon'] ?? '&#9881;';
      ?>
      <div class="product-card reveal" data-delay="<?= $idx * 100 ?>">
        <div class="product-card__gradient<?= $pGrad ? ' product-card__gradient' . $pGrad : '' ?>"></div>
        <div class="product-card__header">
          <div class="card__icon" style="font-size:1.5rem;"><?= $iconDisplay ?></div>
          <div class="product-card__tags">
            <span class="product-card__tag"><?= h($pCat ?: $productDefaults[$idx]['cat']) ?></span>
          </div>
          <h3><?= h($pName ?: $productDefaults[$idx]['name']) ?></h3>
        </div>
        <div class="product-card__body">
          <p><?= h($pDesc ?: $productDefaults[$idx]['desc']) ?></p>
          <a href="<?= h($pUrl ?: $productDefaults[$idx]['url']) ?>" style="color:#00C896; font-weight:600; font-size:.875rem; display:inline-flex; align-items:center; gap:.35rem; margin-top:.5rem;">
            <?= t('Explore') ?> →
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-16 reveal">
      <a href="/technology" class="btn btn-outline btn-lg"><?= t('View All Products') ?> →</a>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--2"></div>
</section>

<?php elseif ($type === 'capabilities'): ?>
<!-- CAPABILITIES -->
<section class="section section-surface" id="capabilities" data-nav-section="capabilities">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Capabilities') ?></span>
      <h2 class="section-title"><?= t('Technology-enabled operations at scale') ?></h2>
      <p class="section-subtitle"><?= t('Our ecosystem combines human expertise with proprietary technology to deliver results across functions.') ?></p>
    </div>
    <div class="grid-4">
      <?php
      $capabilities = [
        ['icon' => '&#127758;', 'title' => t('Nearshore Teams'), 'desc' => t('Full-stack engineering and operations teams in Colombia, time-zone aligned with North America. Bilingual, pre-vetted, and managed.')],
        ['icon' => '&#129302;', 'title' => t('AI & Automation'), 'desc' => t('Custom AI solutions including LLM integration, computer vision, NLP pipelines, and business process automation.')],
        ['icon' => '&#128200;', 'title' => t('Business Intelligence'), 'desc' => t('Data warehousing, analytics dashboards, and predictive modeling to drive data-informed strategic decisions.')],
        ['icon' => '&#127991;', 'title' => t('Data Annotation'), 'desc' => t('Multi-modal data labeling at scale: images, video, text, audio, and 3D point clouds with structured QC workflow.')],
        ['icon' => '&#128188;', 'title' => t('Sales Operations'), 'desc' => t('CRM management, lead qualification, pipeline analytics, and sales enablement powered by WONTIA CRM.')],
        ['icon' => '&#128222;', 'title' => t('Customer Operations'), 'desc' => t('Bilingual customer support, ticket management, NPS tracking, and multi-channel service desks.')],
        ['icon' => '&#128203;', 'title' => t('Executive Support'), 'desc' => t('Dedicated virtual assistants for calendar management, travel coordination, research, and executive communications.')],
        ['icon' => '&#128269;', 'title' => t('Recruiting'), 'desc' => t('End-to-end talent acquisition: sourcing, screening, technical assessments, and onboarding for global teams.')],
      ];
      foreach ($capabilities as $idx => $cap):
      ?>
      <div class="capability-card reveal" data-delay="<?= $idx * 80 ?>">
        <div class="capability-card__icon"><?= $cap['icon'] ?></div>
        <div class="capability-card__content">
          <h4><?= $cap['title'] ?></h4>
          <p><?= $cap['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php elseif ($type === 'industries_grid'): ?>
<!-- INDUSTRIES GRID -->
<section class="section" id="industries" data-nav-section="industries">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label text-purple"><?= t('Industries') ?></span>
      <h2 class="section-title"><?= t('Enterprise solutions across sectors') ?></h2>
      <p class="section-subtitle"><?= t('Our technology and operational expertise serves organizations across diverse industries.') ?></p>
    </div>
    <div class="grid-auto">
      <?php foreach ($industryNames as $idx => $ind): ?>
      <div class="industry-card reveal" data-delay="<?= $idx * 40 ?>">
        <div class="industry-card__icon"><?= $industryIcons[$ind] ?? '&#9632;' ?></div>
        <h3><?= t($ind) ?></h3>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--1" style="top:30%;right:-80px;"></div>
</section>

<?php elseif ($type === 'comparison'): ?>
<!-- COMPARISON SECTION -->
<section class="section section-surface" id="comparison" data-nav-section="comparison">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Why INTSOLCOM') ?></span>
      <h2 class="section-title"><?= t('The ecosystem difference') ?></h2>
      <p class="section-subtitle"><?= t('The Intsolcom ecosystem delivers fundamentally different outcomes than traditional service providers.') ?></p>
    </div>
    <div class="comparison">
      <div class="comparison__col comparison__col--traditional reveal-left">
        <div class="comparison__header">
          <div class="comparison__header-icon">&#10060;</div>
          <h3><?= t('Traditional Service Providers') ?></h3>
        </div>
        <div class="comparison__list">
          <?php foreach ([
            t('Transactional vendor relationships'),
            t('Siloed teams with no integration'),
            t('Manual, repetitive processes'),
            t('Generic, one-size-fits-all approach'),
            t('Opaque operations and reporting'),
            t('Limited technology capabilities'),
          ] as $item): ?>
            <div class="comparison__item"><?= $item ?></div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="comparison__col comparison__col--intsol reveal-right">
        <div class="comparison__header">
          <div class="comparison__header-icon">&#10003;</div>
          <h3><?= t('The Intsolcom Ecosystem') ?></h3>
        </div>
        <div class="comparison__list">
          <?php foreach ([
            t('Collaborative ecosystem partnership'),
            t('Integrated technology & operations'),
            t('AI-enabled, automated workflows'),
            t('Solutions tailored to your business'),
            t('Transparent, real-time dashboards'),
            t('Unified technology & ops delivery'),
          ] as $item): ?>
            <div class="comparison__item"><?= $item ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <div class="text-center mt-12 reveal">
      <a href="/holding" class="btn btn-accent btn-lg"><?= t('See the Difference') ?> →</a>
    </div>
  </div>
</section>

<?php elseif ($type === 'cta'): ?>
<!-- CTA SECTION -->
<section class="cta-section" id="cta" data-nav-section="cta">
  <div class="cta-section__glow"></div>
  <div class="cta-section__glow cta-section__glow--right"></div>
  <div class="container-sm reveal">
    <h2><?= t('Ready to work with the Intsolcom ecosystem?') ?></h2>
    <p><?= t("Let's discuss how INTSOLCOM can accelerate your growth through technology and operational excellence.") ?></p>
    <div class="cta-section__actions">
      <a href="/contact" class="btn btn-accent btn-lg"><?= t('Start a Conversation') ?> →</a>
      <a href="/technology" class="btn btn-outline-white btn-lg"><?= t('Explore Products') ?></a>
    </div>
    <p style="margin-top:var(--space-6); font-size:.8125rem; color:rgba(255,255,255,.35);"><?= t('No commitment. Strategic consultation.') ?></p>
  </div>
  <!-- Particles -->
  <div class="particles">
    <?php for ($i = 0; $i < 10; $i++): ?>
      <div class="particle" style="left:<?= rand(5, 90) ?>%; top:<?= rand(5, 90) ?>%;"></div>
    <?php endfor; ?>
  </div>
</section>

<?php elseif ($type === 'testimonials'): ?>
<!-- TESTIMONIALS -->
<section class="section" id="testimonials" data-nav-section="testimonials">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label section-label--purple"><?= t('Client Results') ?></span>
      <h2 class="section-title"><?= t('What our partners say') ?></h2>
    </div>
    <div class="grid-3">
      <?php
      $placeholderTestimonials = [
        ['name' => 'Marcus D.', 'role' => 'CTO', 'company' => 'HealthTech Innovations', 'content' => 'INTSOLCOM built our entire AI annotation pipeline in 14 days. The quality control processes and workforce management alone saved us 6 months of internal development. Truly a technology partner, not just a vendor.', 'rating' => 5],
        ['name' => 'Elena R.', 'role' => 'VP Operations', 'company' => 'Meridian Financial', 'content' => 'We moved our entire customer operations to INTSOLCOM SAS and saw a 60% cost reduction while improving CSAT scores by 12 points. Their WONTIA CRM platform gave us visibility we never had before.', 'rating' => 5],
        ['name' => 'David K.', 'role' => 'Founder', 'company' => 'Stack AI Labs', 'content' => 'As a startup, we needed a partner who could scale with us. INTSOLCOM provided nearshore engineering teams that felt like our own employees. The ecosystem approach — technology plus operations — is the real differentiator.', 'rating' => 5],
      ];
      $displayTestimonials = $testimonials ?: $placeholderTestimonials;
      foreach ($displayTestimonials as $tidx => $tm):
        $tName = is_array($tm) ? ($tm['name'] ?? '') : '';
        $tRole = is_array($tm) ? ($tm['role'] ?? '') : '';
        $tCompany = is_array($tm) ? ($tm['company'] ?? '') : '';
        $tContent = is_array($tm) ? ($tm['content'] ?? '') : '';
        $tRating = is_array($tm) ? ($tm['rating'] ?? 5) : 5;
      ?>
      <div class="testimonial-card reveal" data-delay="<?= $tidx * 100 ?>">
        <div class="testimonial-card__stars">
          <?= str_repeat('★', (int)$tRating) ?><?= str_repeat('☆', 5 - (int)$tRating) ?>
        </div>
        <p class="testimonial-card__quote">"<?= h($tContent ?: $placeholderTestimonials[$tidx]['content']) ?>"</p>
        <div class="testimonial-card__author">
          <div class="testimonial-card__avatar" style="background:<?= ['#00C896','#8B5CF6','#2563EB'][$tidx % 3] ?>; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:1rem;">
            <?= h(strtoupper(substr($tName ?: $placeholderTestimonials[$tidx]['name'], 0, 1))) ?>
          </div>
          <div>
            <div class="testimonial-card__name"><?= h($tName ?: $placeholderTestimonials[$tidx]['name']) ?></div>
            <div class="testimonial-card__role"><?= h(($tRole ?: $placeholderTestimonials[$tidx]['role']) . ', ' . ($tCompany ?: $placeholderTestimonials[$tidx]['company'])) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php elseif ($type === 'faq'): ?>
<!-- FAQ SECTION -->
<section class="section section-surface" id="faq" data-nav-section="faq">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('FAQ') ?></span>
      <h2 class="section-title"><?= t('Frequently asked questions') ?></h2>
    </div>
    <div class="faq">
      <?php
      $faqs = [
        [t('What is INTSOLCOM?'), t('The Intsolcom business ecosystem combines two entities working together: Intsolcom, LLC in the United States (strategic and commercial hub) and INTSOLCOM SAS in Colombia (operational delivery center). We build proprietary technology products and operate business services at scale. Unlike traditional outsourcing firms, the Intsolcom ecosystem integrates proprietary technology with operational excellence to deliver superior outcomes.')],
        [t('Where are you located?'), t('Our holding company is registered in Delaware, USA. Our primary operations hub — INTSOLCOM SAS — is located in Barranquilla, Colombia with a satellite office in Bogotá. This dual presence gives us U.S. corporate governance with nearshore delivery capabilities in the EST time zone.')],
        [t('What makes you different from BPO companies?'), t('We are a technology holding company, not a BPO. The key difference: we own the technology we deploy. From WONTIA CRM to the IA Annotation Manager, we build and continuously improve our own platforms. This means clients benefit from technology-driven efficiency, not just labor arbitrage. Our integrated ecosystem — technology + operations + R&D — creates compounding value over time.')],
        [t('What industries do you serve?'), t('We serve clients across Healthcare, Technology, Financial Services, AI & Data, Retail, Logistics, Real Estate, Professional Services, Manufacturing, and Hospitality. Our solutions are industry-agnostic by design, with customization layers for sector-specific requirements.')],
        [t('How do I partner with INTSOLCOM?'), t('The process is straightforward: fill out our contact form or reach out via WhatsApp. We will schedule a 30-minute discovery call to understand your needs, map the right solution from our ecosystem, and prepare a tailored proposal. There is no commitment required for the initial consultation.')],
        [t('Can I license your software products independently?'), t('Yes. WONTIA CRM, MACROPONDER, and IA Annotation Manager are available as standalone SaaS products. You can license them independently of our managed services. Visit the Technology page for details or contact us for a demo.')],
        [t('Do you offer staff augmentation or managed teams?'), t('Both. Through INTSOLCOM SAS, we provide dedicated nearshore teams (software engineers, AI specialists, QA, support) that work as an extension of your organization. We also offer fully managed service packages where we handle end-to-end delivery of specific functions using our technology stack.')],
      ];
      foreach ($faqs as $fidx => $faq):
      ?>
      <div class="faq__item reveal" data-delay="<?= $fidx * 60 ?>">
        <button class="faq__question" onclick="toggleFaq(this)" aria-expanded="false">
          <span><?= $faq[0] ?></span>
          <span class="faq__icon">+</span>
        </button>
        <div class="faq__answer" style="display:none;">
          <div class="faq__answer-inner"><?= $faq[1] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php endif; ?>

<?php endforeach; ?>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a href="/" class="footer__logo">
          <?= h($s('logo_text','INTSOL')) ?><span style="color:#00C896;"><?= h($s('logo_accent','COM')) ?></span>
        </a>
        <p class="footer__desc"><?= ht($s('footer_desc', 'INTSOLCOM LLC is a technology holding company. We build and operate software platforms, AI products, and intelligent business services.')) ?></p>
        <div class="footer__social">
          <a href="<?= h($s('social_linkedin','https://linkedin.com/company/intsolcom')) ?>" class="footer__social-icon" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Company') ?></h4>
        <div class="footer__links">
          <a href="/holding"><?= t('About Us') ?></a>
          <a href="/business-units"><?= t('Business Units') ?></a>
          <a href="/industries"><?= t('Industries') ?></a>
          <a href="/resources"><?= t('Resources') ?></a>
          <a href="/contact"><?= t('Contact') ?></a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Products') ?></h4>
        <div class="footer__links">
          <a href="/technology/wontia-crm">WONTIA CRM</a>
          <a href="/technology/macroponder">MACROPONDER</a>
          <a href="/technology/ia-annotation-manager"><?= t('IA Annotation Manager') ?></a>
          <a href="/technology"><?= t('All Products') ?></a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Contact') ?></h4>
        <div class="footer__links">
          <a style="color:#fff;font-weight:600;font-size:.8125rem;">Intsolcom, LLC — United States (Strategic &amp; Commercial)</a>
          <span style="font-size:.8125rem;color:#94A3B8;line-height:1.5;"><?= h($s('contact_usa_address','390 NE 191st St, STE 17284, Miami, FL 33179')) ?></span>
          <span style="font-size:.8125rem;color:#94A3B8;"><?= h($s('contact_usa_phone','+1 (302) 555-0199')) ?></span>
          <a style="color:#fff;font-weight:600;font-size:.8125rem;margin-top:.5rem;">Intsolcom SAS — Colombia (Operational Delivery)</a>
          <span style="font-size:.8125rem;color:#94A3B8;line-height:1.5;"><?= h($s('contact_col_address','Carrera 53 #79-01, Barranquilla, Colombia')) ?></span>
          <a href="<?= h('mailto:' . $s('contact_col_email','info@intsolcom.com')) ?>" style="font-size:.8125rem;color:#00C896;"><?= h($s('contact_col_email','info@intsolcom.com')) ?></a>
          <a href="<?= h('https://wa.me/' . str_replace(['+',' ','-','(',')'],'',$s('contact_whatsapp','+573005550199'))) ?>" style="font-size:.8125rem;color:#00C896;">WhatsApp</a>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= h($s('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span>
      <div class="footer__bottom-links">
        <a href="/privacy"><?= t('Privacy Policy') ?></a>
        <a href="/terms"><?= t('Terms of Service') ?></a>
      </div>
    </div>
  </div>
</footer>

<!-- Scroll to top -->
<button id="scroll-top" aria-label="<?= t('Scroll to top') ?>">&uarr;</button>

<!-- Scripts -->
<script>
window.MBPO_VIDEO = {
  mute: 1, autoplay: 1, loop: 1, controls: 0, rel: 0,
  modestbranding: 1, showinfo: 0, iv_load_policy: 3, disablekb: 1,
  playsinline: 1, speed: 1, layout: 'cover', voffset: 0
};
window.MBPO_FX = {
  revealThreshold: 0.08,
  counterDuration: 1800,
  parallaxSpeed: 0.15
};
</script>
<script src="/assets/js/main.js?v=<?= filemtime(__DIR__.'/assets/js/main.js') ?>"></script>

<script>
// FAQ toggle — global function for inline onclick + accordion header click
function toggleFaq(btn) {
  var content = btn.nextElementSibling;
  var icon = btn.querySelector('.faq__icon');
  if (!content) return;
  var isOpen = content.style.display === 'block' || content.classList.contains('open');
  if (isOpen) {
    content.style.display = 'none';
    content.classList.remove('open');
    btn.classList.remove('active');
    btn.parentElement.classList.remove('active');
    btn.setAttribute('aria-expanded', 'false');
    if (icon) { icon.style.transform = ''; icon.textContent = '+'; }
  } else {
    content.style.display = 'block';
    content.classList.add('open');
    btn.classList.add('active');
    btn.parentElement.classList.add('active');
    btn.setAttribute('aria-expanded', 'true');
    if (icon) { icon.style.transform = 'rotate(45deg)'; icon.textContent = '\u00D7'; }
  }
}
// Also attach to all FAQ buttons
document.querySelectorAll('.faq__question').forEach(function(btn) {
  btn.addEventListener('click', function() { toggleFaq(this); });
});
</script>

<!-- Chat Widget -->
<div class="chat-widget" id="chat-widget">
  <a href="<?= h('https://wa.me/' . str_replace(['+',' ','-','(',')'],'',$s('contact_whatsapp','+573005550199'))) ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:#25D366;color:#fff;box-shadow:0 4px 20px rgba(37,211,102,.3);text-decoration:none;font-size:1.5rem;" aria-label="WhatsApp Chat">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </a>
</div>

</body>
</html>
