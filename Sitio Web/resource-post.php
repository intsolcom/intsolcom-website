<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
  header('Location: /resources', true, 302);
  exit;
}

$stmt = db()->prepare("SELECT * FROM resources WHERE slug = ? AND status = 1 LIMIT 1");
$stmt->execute([$slug]);
$resource = $stmt->fetch();

if (!$resource) {
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
<main><section class="section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;"><div><h1><?= ht('Resource Not Found') ?></h1><p style="margin-top:1rem;"><?= ht('The resource you are looking for does not exist.') ?></p><a href="/resources" class="btn btn-accent" style="margin-top:2rem;"><?= ht('View All Resources') ?></a></div></section></main>
<footer class="footer"><div class="container"><div class="footer__bottom"><span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span></div></div></footer>
<script src="/assets/js/main.js?v=1"></script>
</body></html>
<?php exit; }

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');

$metaTitle       = $resource['meta_title']  ?? $resource['title'] . ' — ' . $siteName;
$metaDescription = $resource['meta_desc']   ?? substr(strip_tags($resource['excerpt'] ?? ''), 0, 160);
$currentUrl      = SITE_URL . '/resources/' . $resource['slug'];
$lang            = currentLang();

$typeColors = ['article' => ['#00C896','rgba(0,200,150,.08)'],'whitepaper' => ['#8B5CF6','rgba(139,92,246,.08)'],'guide' => ['#2563EB','rgba(37,99,235,.08)']];
$typeLabels = ['article' => 'Article','whitepaper' => 'Whitepaper','guide' => 'Guide'];
$rtc = $typeColors[$resource['type']] ?? ['#00C896','rgba(0,200,150,.08)'];

$relStmt = db()->prepare("SELECT * FROM resources WHERE status = 1 AND type = ? AND id != ? ORDER BY published_at DESC LIMIT 3");
$relStmt->execute([$resource['type'], $resource['id']]);
$related = $relStmt->fetchAll();

db()->prepare("UPDATE resources SET views = views + 1 WHERE id = ?")->execute([$resource['id']]);
?>
<!DOCTYPE html>
<html lang="<?= h($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= h($metaDescription) ?>">
  <meta property="og:title" content="<?= h($metaTitle) ?>">
  <meta property="og:description" content="<?= h($metaDescription) ?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?= h($currentUrl) ?>">
  <meta property="og:site_name" content="<?= h($siteName) ?>">
  <?php if (!empty($resource['cover_image'])): ?>
  <meta property="og:image" content="<?= h(UPLOAD_URL . $resource['cover_image']) ?>">
  <?php endif; ?>
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
    "@type": "Article",
    "headline": "<?= h($resource['title']) ?>",
    "description": "<?= h(strip_tags($resource['excerpt'] ?? '')) ?>",
    "author": { "@type": "Person", "name": "<?= h($resource['author'] ?? 'INTSOLCOM') ?>" },
    "datePublished": "<?= h($resource['published_at']) ?>",
    "url": "<?= h($currentUrl) ?>"
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
          <span style="color:<?= h($rtc[0]) ?>;"><?= ht($typeLabels[$resource['type']] ?? $resource['type']) ?></span>
        </div>
        <h1><?= ht($resource['title']) ?></h1>
        <div style="display:flex;align-items:center;gap:var(--space-6);color:var(--color-light);font-size:0.9375rem;margin-top:var(--space-4);">
          <?php if ($resource['author']): ?><span><?= ht($resource['author']) ?></span><?php endif; ?>
          <?php if ($resource['read_time']): ?><span><?= h($resource['read_time']) ?> min read</span><?php endif; ?>
          <?php if ($resource['published_at']): ?><span><?= h(date('M j, Y', strtotime($resource['published_at']))) ?></span><?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container-sm">
      <?php if (!empty($resource['cover_image'])): ?>
        <img src="<?= h(UPLOAD_URL . $resource['cover_image']) ?>" alt="<?= h($resource['title']) ?>" style="width:100%;border-radius:var(--radius-lg);margin-bottom:var(--space-8);box-shadow:var(--shadow-md);" loading="lazy">
      <?php endif; ?>
      <?php if (!empty($resource['excerpt'])): ?>
        <p style="font-size:1.25rem;line-height:1.65;color:var(--color-mid);margin-bottom:var(--space-8);font-weight:500;"><?= ht($resource['excerpt']) ?></p>
      <?php endif; ?>
      <div style="line-height:1.75;font-size:1.0625rem;color:var(--color-mid);">
        <?= $resource['content'] ?>
      </div>

      <div style="margin-top:var(--space-10);padding-top:var(--space-8);border-top:1px solid var(--color-surface2);">
        <a href="/resources" class="btn btn-ghost">← <?= ht('Back to Resources') ?></a>
      </div>
    </div>
  </section>

  <?php if (!empty($related)): ?>
  <section class="section section-surface">
    <div class="container">
      <div class="reveal" style="margin-bottom:var(--space-8);">
        <span class="section-label"><?= ht('Related') ?></span>
        <h2 class="section-title" style="font-size:1.75rem;"><?= ht('More ' . ($typeLabels[$resource['type']] ?? 'Resources') . 's') ?></h2>
      </div>
      <div class="grid-3">
        <?php foreach ($related as $idx => $rel):
          $rrtc = $typeColors[$rel['type']] ?? ['#00C896','rgba(0,200,150,.08)'];
        ?>
        <a href="/resources/<?= h($rel['slug']) ?>" class="card card-hover reveal" style="text-decoration:none;color:inherit;transition-delay:<?= $idx * 0.05 ?>s;">
          <span class="eco-card__badge" style="color:<?= h($rrtc[0]) ?>;background:<?= h($rrtc[1]) ?>;"><?= ht($typeLabels[$rel['type']] ?? $rel['type']) ?></span>
          <h3 style="font-size:1.0625rem;margin-top:var(--space-3);"><?= ht($rel['title']) ?></h3>
          <p style="font-size:0.875rem;"><?= ht($rel['excerpt']) ?></p>
          <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.75rem;color:var(--color-light);margin-top:var(--space-3);">
            <?php if ($rel['read_time']): ?><span><?= h($rel['read_time']) ?> min read</span><?php endif; ?>
            <?php if ($rel['published_at']): ?><span><?= h(date('M j, Y', strtotime($rel['published_at']))) ?></span><?php endif; ?>
          </div>
        </a>
        <?php endforeach; ?>
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
