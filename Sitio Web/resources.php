<?php
require_once __DIR__ . '/includes/config.php';

$page     = getPage('resources');
$sections = $page ? getSections($page['id']) : [];
$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');

$metaTitle       = $page['meta_title']       ?? 'Resources & Insights — INTSOLCOM';
$metaDescription = $page['meta_desc']         ?? 'Operational guides, industry analysis, and technology insights from the INTSOLCOM team.';
$currentUrl      = SITE_URL . '/resources';
$lang            = currentLang();

$typeFilter = $_GET['type'] ?? '';
$pageNum    = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 9;
$offset     = ($pageNum - 1) * $perPage;

$where  = "WHERE status = 1";
$params = [];
if ($typeFilter && in_array($typeFilter, ['article','whitepaper','guide'])) {
  $where .= " AND type = ?";
  $params[] = $typeFilter;
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM resources $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$resStmt = db()->prepare("SELECT * FROM resources $where ORDER BY featured DESC, published_at DESC LIMIT $perPage OFFSET $offset");
$resStmt->execute($params);
$resources = $resStmt->fetchAll();

$featured = [];
if ($pageNum === 1 && !$typeFilter) {
  $fStmt = db()->prepare("SELECT * FROM resources WHERE status = 1 AND featured = 1 ORDER BY published_at DESC LIMIT 1");
  $fStmt->execute();
  $featured = $fStmt->fetch() ?: [];
}

$typeColors = [
  'article'    => ['#00C896', 'rgba(0,200,150,.08)'],
  'whitepaper' => ['#8B5CF6', 'rgba(139,92,246,.08)'],
  'guide'      => ['#2563EB', 'rgba(37,99,235,.08)'],
];
$typeLabels = ['article' => 'Article', 'whitepaper' => 'Whitepaper', 'guide' => 'Guide'];

$activeTypes = db()->query("SELECT type, COUNT(*) as cnt FROM resources WHERE status = 1 GROUP BY type")->fetchAll();
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
  <link rel="canonical" href="<?= h($currentUrl . ($typeFilter ? "?type=$typeFilter" : '')) ?>">
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
        <div class="hero__badge"><span class="hero__badge-dot"></span> <?= ht('Knowledge') ?></div>
        <h1><?= ht('Resources & Insights') ?></h1>
        <p class="hero__description"><?= ht('Operational guides, industry analysis, and technology insights from the INTSOLCOM team.') ?></p>
      </div>
    </div>
  </section>

  <section class="section" style="padding-top:var(--space-12);">
    <div class="container">
      <div class="flex justify-center gap-4" style="margin-bottom:var(--space-12);flex-wrap:wrap;">
        <a href="/resources" class="btn <?= $typeFilter ? 'btn-ghost' : 'btn-accent' ?> btn-sm"><?= ht('All') ?></a>
        <?php foreach ($activeTypes as $at):
          $t = $at['type'];
          $tc = $typeColors[$t] ?? ['#00C896', 'rgba(0,200,150,.08)'];
        ?>
        <a href="/resources?type=<?= h($t) ?>" class="btn <?= $typeFilter === $t ? 'btn-accent' : 'btn-outline' ?> btn-sm">
          <?= ht($typeLabels[$t] ?? ucfirst($t)) ?> <span style="opacity:.6;margin-left:4px;"><?= h($at['cnt']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($featured)): ?>
      <div class="reveal" style="margin-bottom:var(--space-16);">
        <?php $ftc = $typeColors[$featured['type']] ?? ['#00C896', 'rgba(0,200,150,.08)']; ?>
        <a href="/resources/<?= h($featured['slug']) ?>" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-8);align-items:center;background:var(--color-white);border:1px solid var(--color-surface2);border-radius:var(--radius-lg);overflow:hidden;text-decoration:none;color:inherit;transition:box-shadow var(--duration-base) var(--ease-out);" onmouseover="this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
          <div style="background:linear-gradient(135deg,<?= h($ftc[1]) ?>,rgba(15,23,42,.03));aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;font-size:3rem;">
            <?php if (!empty($featured['cover_image'])): ?>
              <img src="<?= h(UPLOAD_URL . $featured['cover_image']) ?>" alt="<?= h($featured['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>📄<?php endif; ?>
          </div>
          <div style="padding:var(--space-8);">
            <span class="eco-card__badge" style="color:<?= h($ftc[0]) ?>;background:<?= h($ftc[1]) ?>;"><?= ht('Featured') ?></span>
            <h2 style="font-size:1.5rem;margin-top:var(--space-3);margin-bottom:var(--space-3);"><?= ht($featured['title']) ?></h2>
            <p style="font-size:0.9375rem;margin-bottom:var(--space-4);"><?= ht($featured['excerpt']) ?></p>
            <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.8125rem;color:var(--color-light);">
              <?php if ($featured['author']): ?><span><?= ht($featured['author']) ?></span><?php endif; ?>
              <?php if ($featured['read_time']): ?><span><?= h($featured['read_time']) ?> min read</span><?php endif; ?>
              <?php if ($featured['published_at']): ?><span><?= h(date('M j, Y', strtotime($featured['published_at']))) ?></span><?php endif; ?>
            </div>
          </div>
        </a>
      </div>
      <?php endif; ?>

      <?php if (empty($resources)): ?>
        <div class="text-center" style="padding:var(--space-16) 0;">
          <p style="color:var(--color-light);font-size:1.125rem;"><?= ht('No resources found') ?><?= $typeFilter ? ' for this category.' : '.' ?></p>
        </div>
      <?php else: ?>
        <div class="grid-3">
          <?php foreach ($resources as $idx => $res):
            $rtc = $typeColors[$res['type']] ?? ['#00C896', 'rgba(0,200,150,.08)'];
          ?>
          <a href="/resources/<?= h($res['slug']) ?>" class="product-card reveal" style="text-decoration:none;color:inherit;transition-delay:<?= $idx * 0.03 ?>s;">
            <div class="product-card__gradient" style="background:linear-gradient(90deg,<?= h($rtc[0]) ?>,transparent);opacity:.3;"></div>
            <div class="product-card__header">
              <?php if (!empty($res['cover_image'])): ?>
                <img src="<?= h(UPLOAD_URL . $res['cover_image']) ?>" alt="<?= h($res['title']) ?>" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--radius-md);margin-bottom:var(--space-4);" loading="lazy">
              <?php endif; ?>
              <span class="eco-card__badge" style="color:<?= h($rtc[0]) ?>;background:<?= h($rtc[1]) ?>;margin-bottom:var(--space-3);"><?= ht($typeLabels[$res['type']] ?? $res['type']) ?></span>
              <h3 style="font-size:1.125rem;"><?= ht($res['title']) ?></h3>
            </div>
            <div class="product-card__body">
              <p style="font-size:0.875rem;"><?= ht($res['excerpt']) ?></p>
              <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.75rem;color:var(--color-light);margin-top:var(--space-4);">
                <?php if ($res['author']): ?><span><?= ht($res['author']) ?></span><?php endif; ?>
                <?php if ($res['read_time']): ?><span><?= h($res['read_time']) ?> min read</span><?php endif; ?>
                <?php if ($res['published_at']): ?><span><?= h(date('M j, Y', strtotime($res['published_at']))) ?></span><?php endif; ?>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-4" style="margin-top:var(--space-12);">
          <?php if ($pageNum > 1): ?>
            <a href="?<?= h(($typeFilter ? "type=$typeFilter&" : '') . 'page=' . ($pageNum - 1)) ?>" class="btn btn-outline btn-sm">← <?= ht('Previous') ?></a>
          <?php endif; ?>
          <span style="display:flex;align-items:center;font-size:0.9375rem;color:var(--color-mid);padding:0 var(--space-4);">
            <?= h("$pageNum / $totalPages") ?>
          </span>
          <?php if ($pageNum < $totalPages): ?>
            <a href="?<?= h(($typeFilter ? "type=$typeFilter&" : '') . 'page=' . ($pageNum + 1)) ?>" class="btn btn-outline btn-sm"><?= ht('Next') ?> →</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </section>

  <section class="cta-section" style="margin-top:var(--space-16);">
    <div class="cta-section__glow"></div>
    <div class="cta-section__glow cta-section__glow--right"></div>
    <div class="container">
      <h2><?= ht('Want more insights?') ?></h2>
      <p><?= ht('Subscribe to receive operational guides and technology insights from the INTSOLCOM team.') ?></p>
      <div class="cta-section__actions">
        <a href="/contact" class="btn btn-accent btn-lg"><?= ht('Get in touch') ?></a>
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

<script src="/assets/js/main.js?v=1"></script>
</body>
</html>
