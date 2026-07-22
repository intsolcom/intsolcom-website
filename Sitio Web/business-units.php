<?php
require_once __DIR__ . '/includes/config.php';

$page     = getPage('business-units');
$sections = $page ? getSections($page['id']) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName    = setting('site_name', 'INTSOLCOM');
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$metaTitle       = 'Business Units — INTSOLCOM';
$metaDescription = 'INTSOLCOM SAS (Colombia operations) and Marcas BPO (commercial brand). Two specialized units forming the Intsolcom business ecosystem.';
$currentUrl      = SITE_URL . '/business-units';
$lang            = currentLang();

$busUnits = [
  [
    'slug'         => 'intsolcom-sas',
    'icon'         => '🇨🇴',
    'name'         => t('INTSOLCOM SAS'),
    'tag'          => t('Legal Entity — Colombia'),
    'subtitle'     => t('Operational Delivery Center · Barranquilla, Colombia'),
    'description'  => t('International Solutions Companies S.A.S. is the operational engine of the Intsolcom ecosystem. Based in Barranquilla, Colombia, it executes all BPO operations, AI data annotation, software development, and talent management. Bilingual teams. EST timezone. Enterprise-grade delivery.'),
    'capabilities' => [t('BPO Operations'), t('AI Data Annotation'), t('Software Development'), t('Quality Assurance'), t('Talent Management'), t('Training')],
    'link'         => '/business-units/intsolcom-sas',
    'cta'          => t('Explore INTSOLCOM SAS'),
    'colorAccent'  => '#2563EB',
  ],
  [
    'slug'         => 'business-operations',
    'icon'         => '🏢',
    'name'         => t('Marcas BPO'),
    'tag'          => t('Commercial Brand — BPO Services'),
    'subtitle'     => t('Business Operations Brand · Powered by INTSOLCOM SAS'),
    'description'  => t("Marcas BPO is the commercial brand through which clients access Intsolcom's business operations ecosystem. From administrative support to AI data services, Marcas BPO represents the full spectrum of operational capabilities delivered from Colombia."),
    'capabilities' => [t('Administrative Support'), t('Sales Operations'), t('Customer Operations'), t('Back Office'), t('AI Data Services'), t('Marketing Operations')],
    'link'         => '/business-units/business-operations',
    'cta'          => t('Explore Marcas BPO'),
    'colorAccent'  => '#00C896',
  ],
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
  <title><?= h($metaTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/main.css?v=1">
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
  <style>
    .bu-hero {
      padding: 120px 0 60px;
      text-align: center;
      background: var(--color-surface, #F8FAFC);
      position: relative;
      overflow: hidden;
    }
    .bu-hero::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 20%, rgba(0,200,150,.06) 0%, transparent 50%),
                  radial-gradient(circle at 70% 60%, rgba(37,99,235,.05) 0%, transparent 50%);
      pointer-events: none;
    }
    .bu-hero__eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8125rem;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--color-accent, #00C896);
      margin-bottom: 16px;
      position: relative;
    }
    .bu-hero__eyebrow::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--color-accent, #00C896);
    }
    .bu-hero h1 {
      font-size: clamp(2rem, 4.5vw, 3.25rem);
      font-weight: 800;
      line-height: 1.15;
      color: var(--color-dark, #0F172A);
      max-width: 800px;
      margin: 0 auto 20px;
      position: relative;
    }
    .bu-hero h1 em {
      font-style: normal;
      color: var(--color-accent, #00C896);
    }
    .bu-hero__desc {
      font-size: 1.125rem;
      line-height: 1.7;
      color: var(--color-light, #64748B);
      max-width: 680px;
      margin: 0 auto;
      position: relative;
    }

    .bu-section {
      padding: 80px 0;
      background: #fff;
    }
    .bu-container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 0 24px;
    }
    .bu-card {
      display: flex;
      align-items: flex-start;
      gap: 28px;
      background: #fff;
      border: 1px solid #E2E8F0;
      border-radius: 16px;
      padding: 36px 32px;
      margin-bottom: 24px;
      box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.03);
      transition: box-shadow .25s ease, border-color .25s ease;
    }
    .bu-card:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,.06), 0 12px 32px rgba(0,0,0,.06);
      border-color: #CBD5E1;
    }
    .bu-card:last-child { margin-bottom: 0; }
    .bu-card__icon {
      flex-shrink: 0;
      width: 64px; height: 64px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      background: #F1F5F9;
      border-radius: 14px;
      line-height: 1;
    }
    .bu-card__body { flex: 1; min-width: 0; }
    .bu-card__tag {
      display: inline-block;
      font-size: 0.75rem;
      font-weight: 600;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--color-accent, #00C896);
      margin-bottom: 6px;
    }
    .bu-card__name {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0F172A;
      margin: 0 0 4px;
      line-height: 1.3;
    }
    .bu-card__subtitle {
      font-size: 0.9375rem;
      color: #64748B;
      margin-bottom: 14px;
    }
    .bu-card__desc {
      font-size: 0.9375rem;
      line-height: 1.7;
      color: #475569;
      margin-bottom: 16px;
    }
    .bu-card__caps {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 18px;
    }
    .bu-card__cap {
      font-size: 0.8125rem;
      font-weight: 500;
      background: #ECFDF5;
      color: #059669;
      padding: 5px 14px;
      border-radius: 9999px;
      white-space: nowrap;
    }
    .bu-card__cta {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.9375rem;
      font-weight: 600;
      color: var(--color-accent, #00C896);
      text-decoration: none;
      transition: gap .2s ease;
    }
    .bu-card__cta:hover { gap: 10px; }

    @media (max-width: 640px) {
      .bu-card {
        flex-direction: column;
        gap: 16px;
        padding: 24px 20px;
      }
      .bu-card__icon {
        width: 48px; height: 48px;
        font-size: 1.5rem;
        border-radius: 12px;
      }
      .bu-hero { padding: 100px 0 40px; }
      .bu-section { padding: 48px 0; }
    }
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
  <section class="bu-hero">
    <div class="container">
      <div class="bu-hero__eyebrow"><?= t('Business Units') ?></div>
      <h1><?= t('Specialized divisions operating within the') ?> <em><?= t('Intsolcom ecosystem.') ?></em></h1>
      <p class="bu-hero__desc"><?= t('Each business unit serves a distinct function — from operational delivery in Colombia to commercial service brands. Together they form a complete ecosystem of technology and business services.') ?></p>
    </div>
  </section>

  <section class="bu-section">
    <div class="bu-container">
      <?php foreach ($busUnits as $bu): ?>
      <div class="bu-card">
        <div class="bu-card__icon"><?= h($bu['icon']) ?></div>
        <div class="bu-card__body">
          <span class="bu-card__tag" style="color:<?= h($bu['colorAccent']) ?>"><?= ht($bu['tag']) ?></span>
          <h2 class="bu-card__name"><?= ht($bu['name']) ?></h2>
          <p class="bu-card__subtitle"><?= ht($bu['subtitle']) ?></p>
          <p class="bu-card__desc"><?= ht($bu['description']) ?></p>
          <div class="bu-card__caps">
            <?php foreach ($bu['capabilities'] as $cap): ?>
              <span class="bu-card__cap"><?= ht($cap) ?></span>
            <?php endforeach; ?>
          </div>
          <a href="<?= h($bu['link']) ?>" class="bu-card__cta"><?= ht($bu['cta']) ?> →</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="cta-section" style="margin-top:0;">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= t('Partner with INTSOLCOM') ?></h2>
      <p><?= t("Every business unit is backed by INTSOLCOM's resources, expertise, and commitment to operational excellence.") ?></p>
      <div class="cta-section__actions">
        <a href="/contact" class="btn btn-accent btn-lg"><?= t('Get in touch') ?></a>
        <a href="/technology" class="btn btn-outline-white btn-lg"><?= t('Explore Technology') ?></a>
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
        <div class="footer__heading"><?= t('Company') ?></div>
        <div class="footer__links">
          <a href="/holding"><?= t('Holding') ?></a>
          <a href="/business-units"><?= t('Business Units') ?></a>
          <a href="/contact"><?= t('Contact') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= t('Solutions') ?></div>
        <div class="footer__links">
          <a href="/technology"><?= t('Technology') ?></a>
          <a href="/industries"><?= t('Industries') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= t('Resources') ?></div>
        <div class="footer__links">
          <a href="/resources"><?= t('Insights') ?></a>
          <a href="/blog"><?= t('Blog') ?></a>
        </div>
      </div>
      <div>
        <div class="footer__heading"><?= t('Contact') ?></div>
        <div class="footer__links">
          <a href="mailto:<?= h(setting('contact_col_email','info@intsolcom.com')) ?>"><?= h(setting('contact_col_email','info@intsolcom.com')) ?></a>
          <a href="tel:<?= h(preg_replace('/[^+\d]/','',setting('contact_usa_phone','+1 (302) 555-0199'))) ?>"><?= h(setting('contact_usa_phone','+1 (302) 555-0199')) ?></a>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span>
      <div class="footer__bottom-links">
        <a href="/privacy"><?= t('Privacy Policy') ?></a>
        <a href="/terms"><?= t('Terms of Service') ?></a>
        <a href="/sitemap.xml"><?= t('Sitemap') ?></a>
      </div>
    </div>
  </div>
</footer>

<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<script src="/assets/js/main.js?v=1"></script>
</body>
</html>
