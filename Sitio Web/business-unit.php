<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
  header('Location: /business-units', true, 302);
  exit;
}

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');
$lang       = currentLang();

$units = [
  'intsolcom-sas' => [
    'name'        => 'INTSOLCOM SAS',
    'subtitle'    => 'Operational Delivery Center · Barranquilla, Colombia',
    'metaDesc'    => 'INTSOLCOM SAS is the operational delivery arm of Intsolcom. Nearshore BPO, AI data annotation, software development from Barranquilla, Colombia.',
    'heroLabel'   => 'Legal Entity — Colombia',
    'heroIcon'    => '🇨🇴',
    'about' => 'International Solutions Companies S.A.S. (commercially known as INTSOLCOM SAS) is the operational delivery arm of the Intsolcom ecosystem. Headquartered in Barranquilla, Colombia, it provides nearshore technology services, BPO operations, and AI data annotation to clients across the Americas.',
    'capabilities' => [
      ['title' => 'Software Development', 'desc' => 'Full-stack engineering teams: React, Node.js, Python, PHP, mobile'],
      ['title' => 'AI Data Operations', 'desc' => 'Data labeling, annotation, model training support for computer vision and NLP'],
      ['title' => 'BPO Services', 'desc' => 'Administrative support, customer operations, sales development, back office'],
      ['title' => 'Quality Assurance', 'desc' => 'Automated and manual QA, testing frameworks, CI/CD integration'],
      ['title' => 'Talent Management', 'desc' => 'Recruiting, training, certification, workforce planning'],
      ['title' => 'IT Support', 'desc' => 'Bilingual L1/L2/L3 technical support for enterprise clients'],
      ['title' => 'Training & Development', 'desc' => 'Continuous upskilling programs, English certification, tech bootcamps'],
      ['title' => 'Delivery Management', 'desc' => 'Project management, SLAs, reporting, client dashboards'],
    ],
    'reasons' => [
      ['title' => 'EST Timezone', 'desc' => 'Real-time collaboration with US teams. No graveyard shifts.'],
      ['title' => 'Bilingual C1-C2', 'desc' => 'Professional English across all roles. Not just technical English.'],
      ['title' => 'Cost Efficient', 'desc' => '60-70% savings vs equivalent US-based teams.'],
    ],
    'process' => [
      ['title' => 'Discovery', 'desc' => 'We analyze your needs, define roles, and align on scope and timelines.'],
      ['title' => 'Team Assembly', 'desc' => 'We recruit or assign the right bilingual talent from our Barranquilla center.'],
      ['title' => 'Onboarding', 'desc' => 'Teams are trained on your tools, processes, and culture. Ramp-up is fast.'],
      ['title' => 'Go Live', 'desc' => 'Full operational delivery with SLAs, reporting, and continuous improvement.'],
    ],
    'ctaText'  => 'Work with INTSOLCOM SAS',
    'ctaLink'  => 'https://marcasbpo.com/contact',
    'ctaExternal' => true,
  ],
  'business-operations' => [
    'name'        => 'Marcas BPO',
    'subtitle'    => 'Business Operations Brand · Powered by INTSOLCOM SAS',
    'metaDesc'    => 'Marcas BPO is the commercial brand for Intsolcom\'s BPO ecosystem. Administrative support, sales ops, AI data services, and more — delivered from Colombia.',
    'heroLabel'   => 'Commercial Brand — BPO Services',
    'heroIcon'    => '🏢',
    'about' => 'Marcas BPO is the commercial brand representing Intsolcom\'s business operations ecosystem. All operational delivery is executed by INTSOLCOM SAS in Barranquilla, Colombia. Marcas BPO is how clients access these services.',
    'capabilities' => [
      ['title' => 'Administrative Support', 'desc' => 'Calendar, email, data entry, CRM admin, reporting'],
      ['title' => 'Sales Operations', 'desc' => 'B2B sales, lead gen, appointment setting, pipeline management'],
      ['title' => 'Marketing Operations', 'desc' => 'Email marketing, lead nurturing, automation, campaigns'],
      ['title' => 'Customer Operations', 'desc' => 'Support, success, follow-up, omnichannel service desks'],
      ['title' => 'Back Office', 'desc' => 'Data processing, document management, operational support'],
      ['title' => 'AI Data Services', 'desc' => 'Video annotation, sports annotation, dataset operations, QA'],
    ],
    'ctaText'       => 'Explore BPO Services',
    'ctaLink'       => 'https://marcasbpo.com',
    'ctaExternal'   => true,
    'ctaSecondary'  => 'Build Your Team',
    'ctaSecondaryLink' => 'https://marcasbpo.com/buildyourteam',
    'note'          => 'For all commercial inquiries, visit marcashpo.com or contact info@marcashpo.com',
  ],
];

$unit = $units[$slug] ?? null;

if (!$unit) {
  http_response_code(404);
  $metaTitle = 'Business Unit Not Found — ' . $siteName;
  $currentUrl = SITE_URL . '/business-units/' . $slug;
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Business unit not found at INTSOLCOM.">
  <meta property="og:title" content="<?= h($metaTitle) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= h($currentUrl) ?>">
  <meta property="og:site_name" content="<?= h($siteName) ?>">
  <meta name="twitter:card" content="summary_large_image">
  <title><?= h($metaTitle) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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

<main>
  <section class="section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;">
    <div>
      <div style="font-size:4rem;margin-bottom:16px;">🔍</div>
      <h1><?= ht('Unit Not Found') ?></h1>
      <p style="margin-top:1rem;color:var(--color-light);"><?= ht('The business unit you are looking for does not exist or has been removed.') ?></p>
      <a href="/business-units" class="btn btn-accent" style="margin-top:2rem;"><?= ht('Back to Business Units') ?></a>
    </div>
  </section>
</main>

<footer class="footer">
  <div class="container"><div class="footer__bottom"><span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span></div></div>
</footer>
<script src="/assets/js/main.js?v=1"></script>
</body></html>
<?php exit; }

$metaTitle       = $unit['name'] . ' — ' . $siteName;
$metaDescription = $unit['metaDesc'];
$currentUrl      = SITE_URL . '/business-units/' . $slug;

$hasReasons  = !empty($unit['reasons']);
$hasProcess  = !empty($unit['process']);
$hasSecondary = !empty($unit['ctaSecondary']);
$hasNote     = !empty($unit['note']);
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
    "@type": "Organization",
    "name": "<?= h($unit['name']) ?>",
    "description": "<?= h($metaDescription) ?>",
    "url": "<?= h($currentUrl) ?>",
    "parentOrganization": { "@type": "Organization", "name": "<?= h($siteName) ?>" }
  }
  </script>
  <style>
    .bu-detail-hero {
      padding: 120px 0 60px;
      text-align: center;
      background: var(--color-surface, #F8FAFC);
      position: relative;
      overflow: hidden;
    }
    .bu-detail-hero::before {
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
    .bu-detail-hero__icon {
      font-size: 3rem;
      margin-bottom: 16px;
      display: inline-block;
      position: relative;
    }
    .bu-detail-hero__label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8125rem;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--color-accent, #00C896);
      margin-bottom: 12px;
      position: relative;
    }
    .bu-detail-hero__label::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--color-accent, #00C896);
    }
    .bu-detail-hero h1 {
      font-size: clamp(2rem, 4.5vw, 3.25rem);
      font-weight: 800;
      color: #0F172A;
      margin: 0 auto 12px;
      max-width: 700px;
      position: relative;
    }
    .bu-detail-hero__sub {
      font-size: 1.125rem;
      color: #64748B;
      position: relative;
    }

    .section-bu { padding: 80px 0; background: #fff; }
    .section-bu--alt { background: #F8FAFC; }
    .container-md { max-width: 960px; margin: 0 auto; padding: 0 24px; }
    .container-lg { max-width: 1100px; margin: 0 auto; padding: 0 24px; }

    .section-label-bu {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.8125rem;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: var(--color-accent, #00C896);
      margin-bottom: 16px;
    }
    .section-label-bu::before {
      content: '';
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--color-accent, #00C896);
    }
    .section-label-bu--purple { color: #8B5CF6; }
    .section-label-bu--purple::before { background: #8B5CF6; }
    .section-label-bu--blue { color: #2563EB; }
    .section-label-bu--blue::before { background: #2563EB; }
    .section-label-bu--amber { color: #F59E0B; }
    .section-label-bu--amber::before { background: #F59E0B; }
    .section-title-bu {
      font-size: clamp(1.5rem, 3vw, 2rem);
      font-weight: 700;
      color: #0F172A;
      margin-bottom: 12px;
    }
    .section-lede {
      font-size: 1.125rem;
      line-height: 1.75;
      color: #475569;
      max-width: 800px;
    }

    .cap-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 32px; }
    .cap-grid--cols3 { grid-template-columns: repeat(3, 1fr); }
    .cap-grid--cols2 { grid-template-columns: repeat(2, 1fr); }
    .cap-card {
      background: #fff;
      border: 1px solid #E2E8F0;
      border-radius: 14px;
      padding: 28px 24px;
      box-shadow: 0 1px 3px rgba(0,0,0,.03);
      transition: box-shadow .2s ease, border-color .2s ease;
    }
    .cap-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,.06);
      border-color: #CBD5E1;
    }
    .cap-card h3 {
      font-size: 1.0625rem;
      font-weight: 700;
      color: #0F172A;
      margin: 0 0 8px;
    }
    .cap-card p {
      font-size: 0.875rem;
      line-height: 1.6;
      color: #64748B;
      margin: 0;
    }

    .reason-card {
      background: #fff;
      border: 1px solid #E2E8F0;
      border-radius: 14px;
      padding: 32px 28px;
      text-align: center;
      box-shadow: 0 1px 3px rgba(0,0,0,.03);
      transition: box-shadow .2s ease, border-color .2s ease;
    }
    .reason-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,.06);
      border-color: #CBD5E1;
    }
    .reason-card__num {
      width: 44px; height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00C896, #2563EB);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.125rem;
      margin: 0 auto 16px;
    }
    .reason-card h3 { font-size: 1.0625rem; font-weight: 700; color: #0F172A; margin: 0 0 8px; }
    .reason-card p { font-size: 0.875rem; line-height: 1.6; color: #64748B; margin: 0; }

    .process-row {
      display: flex;
      gap: 20px;
      margin-top: 32px;
    }
    .process-step {
      flex: 1;
      text-align: center;
      position: relative;
    }
    .process-step::after {
      content: '';
      position: absolute;
      top: 22px;
      right: -10px;
      width: 20px;
      height: 2px;
      background: #CBD5E1;
    }
    .process-step:last-child::after { display: none; }
    .process-step__circle {
      width: 44px; height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, #00C896, #2563EB);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.125rem;
      margin: 0 auto 14px;
      position: relative;
      z-index: 1;
    }
    .process-step h3 { font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 6px; }
    .process-step p { font-size: 0.8125rem; line-height: 1.5; color: #64748B; margin: 0; }

    .bu-note {
      padding: 16px 20px;
      background: #F1F5F9;
      border-left: 3px solid #2563EB;
      border-radius: 0 8px 8px 0;
      font-size: 0.9375rem;
      color: #475569;
      margin-top: 40px;
    }

    @media (max-width: 900px) {
      .cap-grid { grid-template-columns: repeat(2, 1fr); }
      .cap-grid--cols3, .cap-grid--cols2 { grid-template-columns: repeat(2, 1fr); }
      .process-row { flex-direction: column; gap: 24px; }
      .process-step::after { display: none; }
    }
    @media (max-width: 560px) {
      .cap-grid, .cap-grid--cols3, .cap-grid--cols2 { grid-template-columns: 1fr; }
      .bu-detail-hero { padding: 100px 0 40px; }
      .section-bu { padding: 48px 0; }
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
  <section class="bu-detail-hero">
    <div class="container">
      <div class="bu-detail-hero__icon"><?= h($unit['heroIcon']) ?></div>
      <div class="bu-detail-hero__label"><?= ht($unit['heroLabel']) ?></div>
      <h1><?= ht($unit['name']) ?></h1>
      <p class="bu-detail-hero__sub"><?= ht($unit['subtitle']) ?></p>
    </div>
  </section>

  <section class="section-bu">
    <div class="container-md">
      <div class="section-label-bu">About</div>
      <h2 class="section-title-bu"><?= ht($unit['name']) ?></h2>
      <p class="section-lede"><?= ht($unit['about']) ?></p>
    </div>
  </section>

  <section class="section-bu section-bu--alt">
    <div class="container-lg">
      <div class="section-label-bu section-label-bu--purple">Capabilities</div>
      <h2 class="section-title-bu">What We Deliver</h2>
      <?php $capCols = count($unit['capabilities']) <= 3 ? 'cap-grid--cols3' : (count($unit['capabilities']) === 2 ? 'cap-grid--cols2' : ''); ?>
      <div class="cap-grid <?= $capCols ?>">
        <?php foreach ($unit['capabilities'] as $cap): ?>
        <div class="cap-card">
          <h3><?= ht($cap['title']) ?></h3>
          <p><?= ht($cap['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php if ($hasReasons): ?>
  <section class="section-bu">
    <div class="container-lg">
      <div class="section-label-bu section-label-bu--amber">Why Colombia</div>
      <h2 class="section-title-bu">Key Advantages</h2>
      <div class="cap-grid cap-grid--cols3" style="margin-top:32px;">
        <?php foreach ($unit['reasons'] as $idx => $reason): ?>
        <div class="reason-card">
          <div class="reason-card__num"><?= $idx + 1 ?></div>
          <h3><?= ht($reason['title']) ?></h3>
          <p><?= ht($reason['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($hasProcess): ?>
  <section class="section-bu section-bu--alt">
    <div class="container-lg">
      <div class="section-label-bu section-label-bu--blue">Process</div>
      <h2 class="section-title-bu">How We Work</h2>
      <div class="process-row">
        <?php foreach ($unit['process'] as $idx => $step): ?>
        <div class="process-step">
          <div class="process-step__circle"><?= $idx + 1 ?></div>
          <h3><?= ht($step['title']) ?></h3>
          <p><?= ht($step['desc']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <section class="cta-section">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= ht('Ready to get started?') ?></h2>
      <p><?= ht('Let\'s discuss how ' . $unit['name'] . ' can support your business goals.') ?></p>
      <div class="cta-section__actions">
        <a href="<?= h($unit['ctaLink']) ?>" class="btn btn-accent btn-lg" <?= $unit['ctaExternal'] ? 'target="_blank" rel="noopener"' : '' ?>><?= ht($unit['ctaText']) ?> →</a>
        <?php if ($hasSecondary): ?>
        <a href="<?= h($unit['ctaSecondaryLink']) ?>" class="btn btn-outline-white btn-lg" target="_blank" rel="noopener"><?= ht($unit['ctaSecondary']) ?> →</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <?php if ($hasNote): ?>
  <section class="section-bu" style="padding-top:0;padding-bottom:80px;">
    <div class="container-md">
      <div class="bu-note"><?= ht($unit['note']) ?></div>
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
