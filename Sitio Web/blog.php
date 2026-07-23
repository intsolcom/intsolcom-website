<?php
require_once __DIR__ . '/includes/config.php';

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');

$metaTitle       = 'Insights & Resources — ' . $siteName;
$metaDescription = 'Technology, operations, and business insights from the INTSOLCOM team.';
$currentUrl      = SITE_URL . '/blog';
$lang            = currentLang();

$pageNum  = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 9;
$offset   = ($pageNum - 1) * $perPage;

$countStmt = db()->prepare("SELECT COUNT(*) FROM resources WHERE type = 'article' AND status = 1");
$countStmt->execute();
$total = $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$posts = db()->query("SELECT * FROM resources WHERE type = 'article' AND status = 1 ORDER BY published_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();

$featured = [];
if ($pageNum === 1) {
    $f = db()->query("SELECT * FROM resources WHERE type = 'article' AND status = 1 AND featured = 1 ORDER BY published_at DESC LIMIT 1")->fetch();
    $featured = $f ?: [];
}
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
                <div class="hero__badge"><span class="hero__badge-dot"></span> <?= ht('Articles') ?></div>
                <h1><?= ht('Insights & Resources') ?></h1>
                <p class="hero__description"><?= ht('Technology, operations, and business insights from the INTSOLCOM team.') ?></p>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top:var(--space-12);">
        <div class="container">
            <?php if (empty($posts) && empty($featured)): ?>
                <div class="text-center reveal" style="padding:var(--space-20) var(--space-8);">
                    <div style="font-size:4rem;margin-bottom:var(--space-6);">&#128221;</div>
                    <h2 style="font-size:1.5rem;color:var(--color-dark);margin-bottom:var(--space-3);"><?= ht('No articles yet.') ?></h2>
                    <p style="color:var(--color-light);font-size:1.125rem;"><?= ht('Check back soon for technology insights, operational guides, and business resources.') ?></p>
                </div>
            <?php else: ?>
                <?php if (!empty($featured)): ?>
                <div class="reveal" style="margin-bottom:var(--space-16);">
                    <a href="/blog/<?= h($featured['slug']) ?>" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-8);align-items:center;background:var(--color-white);border:1px solid var(--color-surface2);border-radius:var(--radius-lg);overflow:hidden;text-decoration:none;color:inherit;transition:box-shadow var(--duration-base);" onmouseover="this.style.boxShadow='var(--shadow-lg)'" onmouseout="this.style.boxShadow='var(--shadow-sm)'">
                        <div style="aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                            <?php if (!empty($featured['cover_image'])): ?>
                                <img src="<?= h(UPLOAD_URL . $featured['cover_image']) ?>" alt="<?= h($featured['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,rgba(0,200,150,.08),rgba(15,23,42,.03));display:flex;align-items:center;justify-content:center;font-size:3rem;">&#128221;</div>
                            <?php endif; ?>
                        </div>
                        <div style="padding:var(--space-8);">
                            <span class="eco-card__badge" style="color:#00C896;background:rgba(0,200,150,.08);"><?= ht('Featured') ?></span>
                            <h2 style="font-size:1.5rem;margin-top:var(--space-3);margin-bottom:var(--space-3);"><?= ht($featured['title']) ?></h2>
                            <p style="font-size:0.9375rem;color:var(--color-mid);margin-bottom:var(--space-5);line-height:1.6;"><?= ht($featured['excerpt']) ?></p>
                            <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.8125rem;color:var(--color-light);margin-bottom:var(--space-4);">
                                <?php if ($featured['author']): ?><span><?= ht($featured['author']) ?></span><span>·</span><?php endif; ?>
                                <?php if ($featured['published_at']): ?><span><?= h(date('M j, Y', strtotime($featured['published_at']))) ?></span><span>·</span><?php endif; ?>
                                <?php if ($featured['read_time']): ?><span><?= (int)$featured['read_time'] ?> min read</span><?php endif; ?>
                            </div>
                            <span style="color:#00C896;font-weight:600;font-size:0.9375rem;"><?= ht('Read Article') ?> →</span>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php if (!empty($posts)): ?>
                <div class="grid-3">
                    <?php foreach ($posts as $idx => $post): ?>
                    <a href="/blog/<?= h($post['slug']) ?>" class="product-card reveal" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;transition-delay:<?= $idx * 0.03 ?>s;">
                        <div class="product-card__gradient" style="background:linear-gradient(90deg,#00C896,transparent);opacity:.3;"></div>
                        <div class="product-card__header" style="padding:0;">
                            <?php if (!empty($post['cover_image'])): ?>
                                <img src="<?= h(UPLOAD_URL . $post['cover_image']) ?>" alt="<?= h($post['title']) ?>" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--radius-md) var(--radius-md) 0 0;margin-bottom:var(--space-4);" loading="lazy">
                            <?php endif; ?>
                            <div style="padding:0 var(--space-5);">
                                <h3 style="font-size:1.125rem;"><?= ht($post['title']) ?></h3>
                            </div>
                        </div>
                        <div class="product-card__body" style="display:flex;flex-direction:column;flex:1;">
                            <p style="font-size:0.875rem;flex:1;"><?= h(mb_strlen($post['excerpt']) > 120 ? mb_substr($post['excerpt'], 0, 120) . '...' : $post['excerpt']) ?></p>
                            <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.75rem;color:var(--color-light);margin-top:var(--space-4);">
                                <?php if ($post['author']): ?><span><?= ht($post['author']) ?></span><?php endif; ?>
                                <?php if ($post['read_time']): ?><span><?= (int)$post['read_time'] ?> min read</span><?php endif; ?>
                                <?php if ($post['published_at']): ?><span><?= h(date('M j, Y', strtotime($post['published_at']))) ?></span><?php endif; ?>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="flex justify-center gap-4" style="margin-top:var(--space-12);">
                    <?php if ($pageNum > 1): ?>
                        <a href="?page=<?= $pageNum - 1 ?>" class="btn btn-outline btn-sm">← <?= ht('Previous') ?></a>
                    <?php endif; ?>
                    <span style="display:flex;align-items:center;font-size:0.9375rem;color:var(--color-mid);padding:0 var(--space-4);"><?= h("Page $pageNum of $totalPages") ?></span>
                    <?php if ($pageNum < $totalPages): ?>
                        <a href="?page=<?= $pageNum + 1 ?>" class="btn btn-outline btn-sm"><?= ht('Next') ?> →</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="cta-section" style="margin-top:var(--space-16);">
        <div class="cta-section__glow"></div>
        <div class="cta-section__glow cta-section__glow--right"></div>
        <div class="container">
            <h2><?= ht('Want more insights?') ?></h2>
            <p><?= ht('Subscribe to receive articles, guides, and technology insights.') ?></p>
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
