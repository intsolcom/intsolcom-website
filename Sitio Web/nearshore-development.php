<?php
require_once __DIR__ . '/includes/config.php';

$siteName    = 'INTSOLCOM';
$siteTagline = 'Technology & Operations Ecosystem';
$logoText    = setting('logo_text', 'INTSOL');
$logoAccent  = setting('logo_accent', 'COM');

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$metaTitle       = 'Nearshore Software Development — INTSOLCOM';
$metaDescription = 'Dedicated development teams from Barranquilla, Colombia. EST timezone. Spec Driven Development. 60-70% cost savings vs US hiring. Bilingual engineers. Real product engineering.';
$currentUrl      = SITE_URL . '/nearshore-development';
$lang            = currentLang();

$heroVideoId    = setting('neashore_hero_video_id', '');
?><!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0F172A">
<meta name="color-scheme" content="light dark">
<meta name="description" content="<?= h($metaDescription) ?>">
<meta name="author" content="INTSOLCOM LLC">

<title><?= h($metaTitle) ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/main.css?v=<?= filemtime(__DIR__.'/assets/css/main.css') ?>">
<link rel="canonical" href="<?= h($currentUrl) ?>">

<meta property="og:type" content="website">
<meta property="og:site_name" content="INTSOLCOM LLC">
<meta property="og:title" content="<?= h($metaTitle) ?>">
<meta property="og:description" content="<?= h($metaDescription) ?>">
<meta property="og:url" content="<?= h($currentUrl) ?>">
<meta property="og:image" content="<?= SITE_URL ?>/assets/uploads/og-default.jpg">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= h($metaTitle) ?>">
<meta name="twitter:description" content="<?= h($metaDescription) ?>">
<meta name="twitter:image" content="<?= SITE_URL ?>/assets/uploads/og-default.jpg">

<meta name="video-config" content='{"mute":1,"autoplay":1,"loop":1,"controls":0,"rel":0,"modestbranding":1,"showinfo":0,"iv_load_policy":3,"disablekb":1,"playsinline":1,"speed":1,"layout":"cover","voffset":0}'>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "name": "Nearshore Software Development",
  "provider": {
    "@type": "Organization",
    "name": "INTSOLCOM LLC",
    "url": "<?= SITE_URL ?>"
  },
  "description": "<?= h($metaDescription) ?>",
  "serviceType": "Software Development",
  "areaServed": {
    "@type": "Country",
    "name": "United States"
  },
  "offers": {
    "@type": "Offer",
    "description": "Dedicated nearshore development teams from Colombia"
  }
}
</script>

<style>
:root {
  --bg:#FFFFFF;
  --surface:#F8FAFC;
  --surface2:#E2E8F0;
  --dark:#0F172A;
  --mid:#475569;
  --light:#94A3B8;
  --accent:#00C896;
  --accent-dk:#00A67D;
  --accent-bg:rgba(0,200,150,0.07);
  --accent-brd:rgba(0,200,150,0.18);
  --secondary:#2563EB;
  --purple:#8B5CF6;
  --white:#FFFFFF;
  --font-display:'Inter',sans-serif;
  --font-body:'Inter',sans-serif;
  --nav-h:76;
  --nav-h-scroll:62;
  --nav-bg:transparent;
  --nav-bg-scroll:rgba(255,255,255,0.95);
  --nav-blur:20;
}

body.no-scroll { overflow: hidden; }

/* Hero specific */
.hero { position: relative; overflow: hidden; min-height: 100vh; display: flex; align-items: center; background: #0F172A; padding-top: 140px; padding-bottom: 100px; }
.hero__content { position: relative; z-index: 2; max-width: 800px; }
.hero__badge { display: inline-flex; align-items: center; gap: .5rem; background: rgba(0,200,150,.08); border: 1px solid rgba(0,200,150,.18); color: #00C896; font-size: .8125rem; font-weight: 600; padding: .4rem 1rem; border-radius: 9999px; margin-bottom: var(--space-6); letter-spacing: .04em; text-transform: uppercase; }
.hero__badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #00C896; animation: pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
.hero h1 { color: #fff; font-size: clamp(2.5rem, 5vw, 4rem); margin-bottom: var(--space-6); }
.hero h1 em { color: #00C896; font-style: normal; }
.hero__description { font-size: clamp(1rem, 2vw, 1.25rem); color: rgba(255,255,255,0.7); max-width: 36rem; margin-bottom: var(--space-8); line-height: 1.7; }
.hero__actions { display: flex; gap: var(--space-4); flex-wrap: wrap; margin-bottom: var(--space-10); }
.hero__metrics { display: flex; gap: var(--space-6); flex-wrap: wrap; }
.hero__metric { text-align: center; }
.hero__metric-value { font-size: 1.5rem; font-weight: 700; color: #00C896; line-height: 1.2; }
.hero__metric-label { font-size: .8125rem; color: rgba(255,255,255,0.5); margin-top: .2rem; }
.hero__metric-divider { width: 1px; background: rgba(255,255,255,.15); align-self: stretch; margin: 0 .5rem; }
.hero__trust { margin-top: var(--space-8); display: flex; align-items: center; gap: var(--space-4); flex-wrap: wrap; }
.hero__trust-badge { display: inline-flex; align-items: center; gap: .5rem; font-size: .75rem; color: rgba(255,255,255,.4); background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: .45rem 1rem; border-radius: 9999px; letter-spacing: .02em; }
.hero__trust-badge-dot { width: 5px; height: 5px; border-radius: 50%; background: rgba(0,200,150,.6); }
.hero__grid { position: absolute; inset: 0; background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.03) 1px, transparent 0); background-size: 48px 48px; }
.video-bg { position: absolute; inset: 0; overflow: hidden; z-index: 0; }
.video-bg iframe { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); min-width: 100%; min-height: 100%; width: auto; height: auto; pointer-events: none; border: none; }
.video-overlay { position: absolute; inset: 0; z-index: 1; background: radial-gradient(ellipse at 50% 0%, rgba(0,200,150,0.08) 0%, transparent 60%), linear-gradient(180deg, rgba(15,23,42,0.6) 0%, rgba(15,23,42,0.85) 50%, rgba(15,23,42,0.95) 100%); }

/* Section patterns */
.section { padding: var(--section-pad) 0; position: relative; overflow: hidden; }
.section-surface { background: var(--surface); }
.section-dark { background: #0F172A; color: #fff; }
.section-dark h1, .section-dark h2, .section-dark h3, .section-dark h4 { color: #fff; }
.section-dark p { color: rgba(255,255,255,0.7); }
.section-label { display: inline-block; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: #00C896; margin-bottom: var(--space-4); }
.section-label--accent { color: #00C896; }
.section-label--purple { color: #8B5CF6; }
.section-title { font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 700; letter-spacing: -0.03em; margin-bottom: var(--space-5); color: #0F172A; }
.section-title em { color: #00C896; font-style: normal; }
.section-subtitle { font-size: 1.125rem; color: #475569; max-width: 640px; margin-bottom: var(--space-10); line-height: 1.6; }
.section-header { text-align: center; margin-bottom: var(--space-12); }
.section-header .section-subtitle { margin-left: auto; margin-right: auto; }
.section-title--purple em { color: #8B5CF6; }

/* Buttons */
.btn { display: inline-flex; align-items: center; gap: .5rem; padding: .75rem 1.5rem; border-radius: var(--radius-md); font-weight: 600; font-size: .9375rem; transition: all var(--duration-base) var(--ease-out); cursor: pointer; text-decoration: none; }
.btn-accent { background: #00C896; color: #0F172A; border: none; }
.btn-accent:hover { background: #00A67D; transform: translateY(-1px); box-shadow: 0 4px 20px rgba(0,200,150,.25); }
.btn-outline { border: 1.5px solid var(--surface2); color: #0F172A; background: transparent; }
.btn-outline:hover { border-color: #00C896; color: #00C896; }
.btn-outline-white { border: 1.5px solid rgba(255,255,255,.3); color: #fff; background: transparent; }
.btn-outline-white:hover { border-color: #00C896; color: #00C896; background: rgba(0,200,150,.06); }
.btn-lg { padding: .9rem 2rem; font-size: 1rem; border-radius: var(--radius-lg); }
.btn-sm { padding: .5rem 1rem; font-size: .8125rem; }

/* Grids */
.grid-2, .grid-3, .grid-4 { display: grid; gap: var(--grid-gap); }
.grid-2 { grid-template-columns: repeat(2, 1fr); }
.grid-3 { grid-template-columns: repeat(3, 1fr); }
.grid-4 { grid-template-columns: repeat(4, 1fr); }
.grid-6 { display: grid; grid-template-columns: repeat(6, 1fr); gap: var(--space-4); }
.grid-auto { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--grid-gap); }

.text-center { text-align: center; }
.relative { position: relative; }
.mt-12 { margin-top: var(--space-12); }
.mt-16 { margin-top: var(--space-16); }
.mt-20 { margin-top: var(--space-20); }
.mb-8 { margin-bottom: var(--space-8); }

/* Gradient blobs */
.gradient-blob { position: absolute; border-radius: 50%; filter: blur(100px); opacity: .12; pointer-events: none; z-index: 0; }
.gradient-blob--1 { width: 600px; height: 600px; background: radial-gradient(circle, #00C896 0%, transparent 70%); top: -100px; right: -100px; }
.gradient-blob--2 { width: 500px; height: 500px; background: radial-gradient(circle, #8B5CF6 0%, transparent 70%); bottom: -100px; left: -100px; }
.gradient-blob--3 { width: 400px; height: 400px; background: radial-gradient(circle, #2563EB 0%, transparent 70%); }
.gradient-blob--dark { opacity: .08; }

/* Reveal animations */
.reveal { opacity: 0; transform: translateY(24px); transition: opacity .6s var(--ease-out), transform .6s var(--ease-out); }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-left { opacity: 0; transform: translateX(-30px); transition: opacity .6s var(--ease-out), transform .6s var(--ease-out); }
.reveal-left.visible { opacity: 1; transform: translateX(0); }
.reveal-right { opacity: 0; transform: translateX(30px); transition: opacity .6s var(--ease-out), transform .6s var(--ease-out); }
.reveal-right.visible { opacity: 1; transform: translateX(0); }

/* CTA Section */
.cta-section { position: relative; background: #0F172A; padding: var(--section-pad) var(--container-pad); text-align: center; overflow: hidden; }
.cta-section__glow { position: absolute; width: 500px; height: 500px; border-radius: 50%; filter: blur(120px); pointer-events: none; }
.cta-section__glow:first-child { background: rgba(0,200,150,.12); top: -100px; left: -100px; }
.cta-section__glow--right { background: rgba(37,99,235,.08); bottom: -100px; right: -100px; }
.cta-section h2 { color: #fff; font-size: clamp(2rem, 4vw, 3rem); margin-bottom: var(--space-5); position: relative; z-index: 1; }
.cta-section p { color: rgba(255,255,255,0.6); font-size: 1.125rem; max-width: 600px; margin: 0 auto var(--space-8); position: relative; z-index: 1; line-height: 1.6; }
.cta-section__actions { display: flex; gap: var(--space-4); justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }

/* Stats band */
.stats-band { display: flex; gap: var(--space-8); justify-content: center; flex-wrap: wrap; align-items: center; }
.stats-band__item { text-align: center; }
.stats-band__value { font-size: 2.25rem; font-weight: 700; color: #0F172A; line-height: 1.1; }
.stats-band__value--accent { color: #00C896; }
.stats-band__label { font-size: .875rem; color: #475569; margin-top: .25rem; }
.stats-band__divider { width: 1px; height: 48px; background: rgba(0,0,0,.08); }

/* Methodology cards */
.meth-card { background: #fff; border: 1px solid var(--surface2); border-radius: var(--radius-lg); padding: var(--space-6); text-align: center; position: relative; overflow: hidden; transition: all var(--duration-base) var(--ease-out); }
.meth-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,.06); border-color: rgba(0,200,150,.2); }
.meth-card__num { font-size: 3.5rem; font-weight: 800; color: rgba(0,200,150,.1); position: absolute; top: .5rem; right: 1rem; line-height: 1; pointer-events: none; }
.meth-card__icon { font-size: 1.75rem; margin-bottom: var(--space-3); }
.meth-card h4 { font-size: 1.05rem; margin-bottom: .35rem; color: #0F172A; }
.meth-card p { font-size: .875rem; color: #475569; }

/* Feature card (Colombia) */
.feature-card { background: #fff; border: 1px solid var(--surface2); border-radius: var(--radius-lg); padding: var(--space-6); transition: all var(--duration-base) var(--ease-out); }
.feature-card:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,.04); border-color: rgba(0,200,150,.15); }
.feature-card__icon { font-size: 2rem; margin-bottom: var(--space-3); }
.feature-card h4 { font-size: 1.05rem; margin-bottom: .35rem; }
.feature-card p { font-size: .875rem; color: #475569; }

/* Tech stack */
.tech-cat { margin-bottom: var(--space-8); }
.tech-cat h4 { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #00C896; margin-bottom: var(--space-3); }
.tech-tags { display: flex; flex-wrap: wrap; gap: .5rem; }
.tech-tag { font-size: .8125rem; padding: .35rem .85rem; border-radius: 9999px; background: rgba(0,200,150,.06); color: #0F172A; border: 1px solid rgba(0,200,150,.12); font-weight: 500; transition: all var(--duration-fast); }
.tech-tag:hover { background: rgba(0,200,150,.12); border-color: rgba(0,200,150,.25); }

/* Timeline */
.timeline { display: flex; justify-content: space-between; position: relative; margin-bottom: var(--space-12); }
.timeline::before { content: ''; position: absolute; top: 28px; left: 40px; right: 40px; height: 2px; background: linear-gradient(90deg, #00C896 0%, #8B5CF6 50%, #2563EB 100%); z-index: 0; }
.timeline-step { text-align: center; position: relative; z-index: 1; flex: 1; }
.timeline-step__dot { width: 56px; height: 56px; border-radius: 50%; background: #0F172A; border: 3px solid #00C896; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-3); color: #00C896; font-size: 1.25rem; font-weight: 700; position: relative; z-index: 2; }
.timeline-step__label { font-size: .8125rem; font-weight: 600; color: #0F172A; margin-bottom: .25rem; }
.timeline-step__time { font-size: .75rem; color: #94A3B8; margin-bottom: .35rem; }
.timeline-step__desc { font-size: .8125rem; color: #475569; max-width: 180px; margin: 0 auto; line-height: 1.4; }

/* FAQ section */
.faq { max-width: 800px; margin: 0 auto; }
.faq__item { border-bottom: 1px solid var(--surface2); }
.faq__question { width: 100%; text-align: left; padding: var(--space-5) 0; font-size: 1rem; font-weight: 600; color: #0F172A; background: none; border: none; cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: var(--space-4); transition: color var(--duration-fast); }
.faq__question:hover { color: #00C896; }
.faq__question.active { color: #00C896; }
.faq__icon { font-size: 1.25rem; font-weight: 700; color: #00C896; transition: transform var(--duration-fast) var(--ease-out); flex-shrink: 0; }
.faq__answer { overflow: hidden; max-height: 0; transition: max-height .4s var(--ease-out); }
.faq__answer.open { max-height: 300px; }
.faq__answer-inner { padding: 0 0 var(--space-5) 0; font-size: .9375rem; color: #475569; line-height: 1.65; }

/* Trust badges row */
.trust-badges { display: flex; gap: var(--space-4); flex-wrap: wrap; justify-content: center; margin-top: var(--space-10); }
.trust-badge { font-size: .75rem; color: #94A3B8; background: rgba(0,200,150,.05); border: 1px solid rgba(0,200,150,.12); padding: .4rem 1rem; border-radius: 9999px; font-weight: 500; }

/* Marcas BPO logo area */
.marcas-logo-area { text-align: center; margin-top: var(--space-8); }
.marcas-logo-area img { max-height: 48px; opacity: .7; transition: opacity var(--duration-fast); }
.marcas-logo-area img:hover { opacity: 1; }

/* Responsive */
@media (max-width: 1024px) {
  .grid-3, .grid-4 { grid-template-columns: repeat(2, 1fr); }
  .grid-6 { grid-template-columns: repeat(3, 1fr); }
  .timeline { flex-wrap: wrap; gap: var(--space-8); justify-content: center; }
  .timeline::before { display: none; }
  .timeline-step { flex: 0 0 150px; }
}
@media (max-width: 768px) {
  :root { --section-pad: 4rem; }
  .hero { min-height: auto; padding-top: 120px; padding-bottom: 60px; }
  .hero__metrics { gap: var(--space-4); }
  .hero__metric-divider { display: none; }
  .hero__actions { flex-direction: column; }
  .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
  .grid-6 { grid-template-columns: repeat(2, 1fr); }
  .stats-band { flex-direction: column; gap: var(--space-6); }
  .stats-band__divider { width: 48px; height: 1px; }
}
</style>
</head>
<body>

<!-- Custom Cursor -->
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>

<!-- ============================================================
     NAVIGATION
     ============================================================ -->
<nav class="nav nav--transparent" id="nav">
  <div class="container">
    <a href="/" class="nav__logo">
      <?= h($logoText) ?><span style="color:#00C896;"><?= h($logoAccent) ?></span>
    </a>
    <div class="nav__links">
      <?php foreach ($navItems as $item): ?>
        <?php if ($item['is_cta']): ?>
          <a href="<?= h($item['url']) ?>" class="btn btn-accent btn-sm nav__cta"><?= ht($item['text']) ?></a>
        <?php else: ?>
          <a href="<?= h($item['url']) ?>" class="nav__link"><?= ht($item['text']) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
      <?php require __DIR__ . '/includes/lang_switch.php'; ?>
    </div>
    <button class="nav__hamburger nav-toggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="nav__mobile nav-mobile">
    <div class="nav__mobile-links">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= h($item['url']) ?>" class="nav__mobile-link"><?= ht($item['text']) ?></a>
      <?php endforeach; ?>
      <?php require __DIR__ . '/includes/lang_switch.php'; ?>
    </div>
  </div>
</nav>

<main>

<!-- ============================================================
     SECTION 1: HERO
     ============================================================ -->
<section class="hero">
  <?php if ($heroVideoId): ?>
    <div class="video-bg" data-video-id="<?= h($heroVideoId) ?>" data-v-layout="cover" data-v-offset="-10"></div>
    <div class="video-overlay"></div>
  <?php else: ?>
    <div class="hero__grid"></div>
  <?php endif; ?>
  <div class="container">
    <div class="hero__content">
      <div class="hero__badge reveal">
        <span class="hero__badge-dot"></span>
        <?= t('Nearshore Software Development') ?>
      </div>
      <h1 class="reveal" style="transition-delay:100ms;">
        <?= t('Build software with') ?> <em><?= t('professional methodology.') ?></em>
      </h1>
      <p class="hero__description reveal" style="transition-delay:200ms;">
        <?= t('Dedicated development teams operating from Barranquilla, Colombia. Same timezone as the US. Bilingual engineers. Spec Driven Development. No freelancers. No generic agencies. Real product engineering.') ?>
      </p>
      <div class="hero__trust reveal" style="transition-delay:300ms;">
        <span class="hero__trust-badge"><span class="hero__trust-badge-dot"></span> <?= t('SDD Methodology') ?></span>
        <span class="hero__trust-badge"><span class="hero__trust-badge-dot"></span> <?= t('Dedicated Teams') ?></span>
        <span class="hero__trust-badge"><span class="hero__trust-badge-dot"></span> <?= t('EST Timezone') ?></span>
        <span class="hero__trust-badge"><span class="hero__trust-badge-dot"></span> <?= t('C1-C2 English') ?></span>
      </div>
      <div class="hero__actions reveal" style="transition-delay:350ms;">
        <a href="https://marcasbpo.com/buildyourteam" target="_blank" rel="noopener" class="btn btn-accent btn-lg"><?= t('Build Your Team') ?> &rarr;</a>
        <a href="https://marcasbpo.com/contact" target="_blank" rel="noopener" class="btn btn-outline-white btn-lg"><?= t('Talk to Us') ?></a>
      </div>
      <div class="hero__metrics reveal" style="transition-delay:450ms;">
        <div class="hero__metric">
          <div class="hero__metric-value">14 days</div>
          <div class="hero__metric-label"><?= t('Avg Deployment') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">60-70%</div>
          <div class="hero__metric-label"><?= t('Cost Savings vs US Hiring') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">300+</div>
          <div class="hero__metric-label"><?= t('Bilingual Pros') ?></div>
        </div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric">
          <div class="hero__metric-value">98%</div>
          <div class="hero__metric-label"><?= t('Client Retention') ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--1 gradient-blob--dark"></div>
  <div class="gradient-blob gradient-blob--3 gradient-blob--dark" style="top:60%;right:5%;"></div>
</section>

<!-- ============================================================
     SECTION 2: WHAT IS SPEC DRIVEN DEVELOPMENT
     ============================================================ -->
<section class="section section-surface" id="methodology">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Methodology') ?></span>
      <h2 class="section-title"><?= t('Spec Driven Development.') ?> <em><?= t('Explained simply.') ?></em></h2>
      <p class="section-subtitle"><?= t('Before a single line of code is written, we define exactly what you need. You validate. We build. No surprises.') ?></p>
    </div>

    <div class="grid-3">
      <?php
      $steps = [
        ['num' => '1', 'icon' => '&#128203;', 'title' => t('SPEC'), 'desc' => t('We define together EXACTLY what you need. Zero ambiguity. You approve before any code is written.')],
        ['num' => '2', 'icon' => '&#127912;', 'title' => t('DESIGN'), 'desc' => t('Architecture, UX, UI — everything designed first. You see mockups, not promises.')],
        ['num' => '3', 'icon' => '&#9000;', 'title' => t('DEVELOP'), 'desc' => t('The team builds against the specification. No scope creep. No surprises.')],
        ['num' => '4', 'icon' => '&#9881;', 'title' => t('TEST'), 'desc' => t('Every feature validated against what you approved. Nothing ships without testing.')],
        ['num' => '5', 'icon' => '&#128640;', 'title' => t('DEPLOY'), 'desc' => t('Published on your infrastructure or ours. You decide.')],
        ['num' => '6', 'icon' => '&#128200;', 'title' => t('OPTIMIZE'), 'desc' => t("Continuous improvement based on real usage data. We don't disappear after launch.")],
      ];
      foreach ($steps as $idx => $s):
      ?>
      <div class="meth-card reveal" style="transition-delay:<?= $idx * 80 ?>ms;">
        <div class="meth-card__num"><?= $s['num'] ?></div>
        <div class="meth-card__icon"><?= $s['icon'] ?></div>
        <h4><?= $s['title'] ?></h4>
        <p><?= $s['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <p class="text-center mt-12 reveal" style="font-size:.9375rem; color:#475569; font-weight:500;">
      <?= t('We build what') ?> <strong style="color:#0F172A;"><?= t('YOU') ?></strong> <?= t('need, not what WE think you need.') ?>
    </p>
  </div>
  <div class="gradient-blob gradient-blob--3" style="top:40%;right:-100px;"></div>
</section>

<!-- ============================================================
     SECTION 3: WHY COLOMBIA
     ============================================================ -->
<section class="section" id="why-colombia">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label section-label--purple"><?= t('Nearshore Hub') ?></span>
      <h2 class="section-title section-title--purple"><?= t('Colombia.') ?> <em><?= t('The development hub for the Americas.') ?></em></h2>
    </div>

    <div class="grid-3">
      <?php
      $reasons = [
        ['icon' => '&#127464;&#127476;', 'title' => t('EST Timezone'), 'desc' => t('Your team works when you work. Daily standups at 9 AM your time. No 5 AM calls. No midnight handoffs.')],
        ['icon' => '&#128172;', 'title' => t('Truly Bilingual'), 'desc' => t("C1-C2 English. Not 'technical English'. Real communication with your stakeholders.")],
        ['icon' => '&#9989;', 'title' => t('Pre-Vetted Talent'), 'desc' => t("We don't forward resumes. We present engineers who passed our technical assessment.")],
        ['icon' => '&#129309;', 'title' => t('Cultural Fit'), 'desc' => t('Colombia shares a work culture with the US. Zero cultural friction. Just results.')],
        ['icon' => '&#128176;', 'title' => t('Cost Efficient'), 'desc' => t('60-70% less than equivalent US-based teams. Same quality, better economics.')],
        ['icon' => '&#9992;', 'title' => t('3 Hours from Miami'), 'desc' => t("Direct flights. Visit your team whenever you want. They're not on the other side of the world.")],
      ];
      foreach ($reasons as $idx => $r):
      ?>
      <div class="feature-card reveal" style="transition-delay:<?= $idx * 60 ?>ms;">
        <div class="feature-card__icon"><?= $r['icon'] ?></div>
        <h4><?= $r['title'] ?></h4>
        <p><?= $r['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--2" style="opacity:.06;"></div>
</section>

<!-- ============================================================
     SECTION 4: TECH STACK
     ============================================================ -->
<section class="section section-surface" id="tech-stack">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Technology') ?></span>
      <h2 class="section-title"><?= t('The stack your') ?> <em><?= t('project needs.') ?></em></h2>
      <p class="section-subtitle"><?= t('Modern technology. No legacy lock-in. No vendor marriage.') ?></p>
    </div>

    <div class="grid-2 reveal" style="gap:var(--space-10);">
      <?php
      $stack = [
        ['cat' => 'Frontend', 'techs' => ['React', 'Next.js', 'Vue', 'Angular', 'TypeScript', 'Tailwind']],
        ['cat' => 'Backend', 'techs' => ['Node.js', 'Python', 'PHP/Laravel', 'Go', 'Java', 'Express']],
        ['cat' => 'Cloud', 'techs' => ['AWS', 'Azure', 'GCP', 'Docker', 'Kubernetes', 'Terraform']],
        ['cat' => 'AI/ML', 'techs' => ['TensorFlow', 'PyTorch', 'OpenAI', 'Claude', 'LangChain', 'HuggingFace']],
        ['cat' => 'Data', 'techs' => ['PostgreSQL', 'MongoDB', 'Redis', 'Elasticsearch', 'MySQL', 'MariaDB']],
        ['cat' => 'Mobile', 'techs' => ['React Native', 'Flutter', 'Swift', 'Kotlin']],
        ['cat' => 'DevOps', 'techs' => ['CI/CD', 'GitHub Actions', 'PM2', 'Traefik', 'Nginx']],
      ];
      foreach ($stack as $cat):
      ?>
      <div class="tech-cat">
        <h4><?= $cat['cat'] ?></h4>
        <div class="tech-tags">
          <?php foreach ($cat['techs'] as $t): ?>
            <span class="tech-tag"><?= $t ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 5: HOW IT WORKS
     ============================================================ -->
<section class="section" id="process">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Process') ?></span>
      <h2 class="section-title"><?= t('Start building') ?> <em><?= t('in 2 weeks.') ?></em></h2>
    </div>

    <div class="timeline reveal">
      <?php
      $timeline = [
        ['num' => '1', 'label' => t('Discovery Call'), 'time' => t('2 days'), 'desc' => t('We learn about your project, goals, and team needs. 30 minutes.')],
        ['num' => '2', 'label' => t('Free Spec Review'), 'time' => t('3-5 days'), 'desc' => t('We prepare a technical specification. You review and approve.')],
        ['num' => '3', 'label' => t('Team Proposal'), 'time' => t('1 week'), 'desc' => t('Engineers selected for your project. Resumes + tech assessment results.')],
        ['num' => '4', 'label' => t('SDD Sprint 1'), 'time' => t('2 weeks'), 'desc' => t('Development begins against the approved spec. Daily updates.')],
        ['num' => '5', 'label' => t('First Demo'), 'time' => t('Continuous'), 'desc' => t('Working software delivered. Iterate, improve, scale.')],
      ];
      foreach ($timeline as $step):
      ?>
      <div class="timeline-step">
        <div class="timeline-step__dot"><?= $step['num'] ?></div>
        <div class="timeline-step__label"><?= $step['label'] ?></div>
        <div class="timeline-step__time"><?= $step['time'] ?></div>
        <div class="timeline-step__desc"><?= $step['desc'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-12 reveal">
      <a href="https://marcasbpo.com/contact" target="_blank" rel="noopener" class="btn btn-accent btn-lg"><?= t('Start with a free spec review') ?> &rarr;</a>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 6: MARCAS BPO
     ============================================================ -->
<section class="section section-surface" id="marcas-bpo">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Our Operations') ?></span>
      <h2 class="section-title"><?= t('Powered by') ?> <em><?= t('Marcas BPO') ?></em></h2>
    </div>

    <div class="reveal" style="max-width:720px; margin:0 auto;">
      <p style="font-size:1.0625rem; line-height:1.75; color:#475569; margin-bottom:var(--space-5);">
        <?= t('Marcas BPO is our operational brand. While INTSOLCOM handles strategy, partnerships, and technology products from the United States, Marcas BPO executes all development and BPO operations from Barranquilla, Colombia.') ?>
      </p>
      <p style="font-size:1.0625rem; line-height:1.75; color:#475569; margin-bottom:var(--space-5);">
        <?= t('When you hire a development team through us, your commercial relationship is with INTSOLCOM LLC (USA). Your team works from our offices in Colombia, managed by Marcas BPO.') ?>
      </p>
      <p style="font-size:1.0625rem; line-height:1.75; color:#475569; margin-bottom:var(--space-6);">
        <strong style="color:#0F172A;"><?= t('The best of both worlds: American contract. Colombian talent.') ?></strong>
      </p>
    </div>

    <div class="marcas-logo-area reveal">
      <a href="https://marcasbpo.com" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:var(--space-4); padding:var(--space-5) var(--space-10); background:#0F172A; border-radius:var(--radius-lg); text-decoration:none; transition:all var(--duration-base);">
        <span style="color:#fff; font-size:1.5rem; font-weight:700; letter-spacing:-.02em;">Marcas BPO</span>
        <span style="color:#00C896; font-size:.8125rem; font-weight:500;">&rarr;</span>
      </a>
    </div>

    <div class="text-center mt-12 reveal">
      <a href="https://marcasbpo.com" target="_blank" rel="noopener" class="btn btn-outline btn-lg"><?= t('Learn more about our operations') ?> &rarr;</a>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTION 7: TRUST & FAQ
     ============================================================ -->
<section class="section" id="trust-faq">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label"><?= t('Trust') ?></span>
      <h2 class="section-title"><?= t('Questions') ?> <em><?= t('you probably have.') ?></em></h2>
    </div>

    <div class="faq reveal">
      <?php
      $faqs = [
        ['q' => t('How do you protect my code and IP?'), 'a' => t('We sign NDAs as standard. Your IP is yours. Always. We can work within your VPN, your repos, your infrastructure.')],
        ['q' => t('What if I need to scale the team?'), 'a' => t('Add developers in days, not months. We maintain a pre-vetted talent pool ready to join your project.')],
        ['q' => t('Can I visit the offices in Colombia?'), 'a' => t('Absolutely. Barranquilla is a 3-hour flight from Miami. We encourage visits.')],
        ['q' => t('What guarantees do I have on code quality?'), 'a' => t("Spec Driven Development means every deliverable is tested against the spec YOU approved. If it doesn't match, we fix it.")],
        ['q' => t('How do you handle timezone differences?'), 'a' => t("We don't. Colombia is EST. We work when you work.")],
        ['q' => t("What's the minimum engagement?"), 'a' => t('We recommend starting with a 3-month team to prove value. No long-term lock-in.')],
      ];
      foreach ($faqs as $fidx => $faq):
      ?>
      <div class="faq__item">
        <button class="faq__question" onclick="toggleFaq(this)" aria-expanded="false">
          <span><?= ht($faq['q']) ?></span>
          <span class="faq__icon">+</span>
        </button>
        <div class="faq__answer" style="max-height:0;">
          <div class="faq__answer-inner"><?= ht($faq['a']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="trust-badges reveal">
      <span class="trust-badge"><?= t('NDA Protected') ?></span>
      <span class="trust-badge"><?= t('IP Protection') ?></span>
      <span class="trust-badge"><?= t('Dedicated Teams') ?></span>
      <span class="trust-badge"><?= t('No Lock-in') ?></span>
      <span class="trust-badge"><?= t('Documented Methodology') ?></span>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--1" style="top:20%;left:-100px;"></div>
</section>

<!-- ============================================================
     SECTION 8: FINAL CTA
     ============================================================ -->
<section class="cta-section">
  <div class="cta-section__glow"></div>
  <div class="cta-section__glow cta-section__glow--right"></div>
  <div class="container-sm reveal">
    <h2><?= t('Ready to build with a professional nearshore team?') ?></h2>
    <p><?= t("Schedule a 15-minute call. We'll review your project, answer your questions, and prepare a free spec review. No commitment.") ?></p>
    <div class="cta-section__actions">
      <a href="https://marcasbpo.com/contact" target="_blank" rel="noopener" class="btn btn-accent btn-lg"><?= t('Schedule a Call') ?> &rarr;</a>
      <a href="https://marcasbpo.com/buildyourteam" target="_blank" rel="noopener" class="btn btn-outline-white btn-lg"><?= t('Build Your Team') ?> &rarr;</a>
    </div>
    <p style="margin-top:var(--space-6); font-size:.8125rem; color:rgba(255,255,255,.35);">
      <?= t('Or email us directly:') ?>
      <a href="mailto:info@marcasbpo.com" style="color:rgba(255,255,255,.5); text-decoration:underline;">info@marcasbpo.com</a>
    </p>
  </div>
</section>

</main>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a href="/" class="footer__logo">
          <?= h($logoText) ?><span style="color:#00C896;"><?= h($logoAccent) ?></span>
        </a>
        <p class="footer__desc"><?= ht(setting('footer_desc', 'INTSOLCOM LLC is a technology holding company. We build and operate software platforms, AI products, and intelligent business services.')) ?></p>
        <div class="footer__social">
          <a href="<?= h(setting('social_linkedin','https://linkedin.com/company/intsolcom')) ?>" class="footer__social-icon" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Company') ?></h4>
        <div class="footer__links">
          <a href="/holding"><?= t('About Us') ?></a>
          <a href="/nearshore-development"><?= t('Nearshore Development') ?></a>
          <a href="/business-units"><?= t('Business Units') ?></a>
          <a href="/contact"><?= t('Contact') ?></a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Solutions') ?></h4>
        <div class="footer__links">
          <a href="/technology"><?= t('Technology') ?></a>
          <a href="/industries"><?= t('Industries') ?></a>
          <a href="/resources"><?= t('Resources') ?></a>
          <a href="/blog"><?= t('Blog') ?></a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading"><?= t('Contact') ?></h4>
        <div class="footer__links">
          <?php $colEmail = setting('contact_col_email','info@intsolcom.com'); ?>
          <a href="https://marcasbpo.com" target="_blank" rel="noopener" style="color:#00C896; font-weight:600; font-size:.8125rem;">Marcas BPO</a>
          <span style="font-size:.8125rem;color:#94A3B8;">Carrera 53 #79-01, Barranquilla, Colombia</span>
          <a href="mailto:info@marcasbpo.com" style="font-size:.8125rem;color:#00C896;">info@marcasbpo.com</a>
          <a href="mailto:<?= h($colEmail) ?>" style="font-size:.8125rem;color:#94A3B8;"><?= h($colEmail) ?></a>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span><?= ht(setting('footer_copyright','&copy; 2026 INTSOLCOM LLC')) ?></span>
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
<script>window.MBPO_VIDEO={mute:1,autoplay:1,loop:1,controls:0,rel:0,modestbranding:1,showinfo:0,iv_load_policy:3,disablekb:1,playsinline:1,speed:1,layout:'cover',voffset:0};window.MBPO_FX={revealThreshold:0.08,counterDuration:1800,parallaxSpeed:0.15};</script>
<script src="/assets/js/main.js?v=<?= filemtime(__DIR__.'/assets/js/main.js') ?>"></script>

<script>
function toggleFaq(btn){var a=btn.nextElementSibling;var i=btn.querySelector('.faq__icon');if(!a)return;if(a.classList.contains('open')){a.style.maxHeight='0';a.classList.remove('open');btn.classList.remove('active');btn.setAttribute('aria-expanded','false');if(i){i.style.transform='';i.textContent='+';}}else{a.style.maxHeight=a.scrollHeight+'px';a.classList.add('open');btn.classList.add('active');btn.setAttribute('aria-expanded','true');if(i){i.style.transform='rotate(45deg)';i.textContent='\u00D7';}}}
document.querySelectorAll('.faq__question').forEach(function(btn){btn.addEventListener('click',function(){toggleFaq(this);});});
</script>

<!-- Chat Widget -->
<div class="chat-widget" id="chat-widget" style="position:fixed;bottom:2rem;left:2rem;z-index:300;">
  <?php $whatsapp = setting('contact_whatsapp','+573005550199'); ?>
  <a href="<?= h('https://wa.me/' . str_replace(['+',' ','-','(',')'],'',$whatsapp)) ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:#25D366;color:#fff;box-shadow:0 4px 20px rgba(37,211,102,.3);text-decoration:none;font-size:1.5rem;" aria-label="WhatsApp Chat">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </a>
</div>

</body>
</html>
