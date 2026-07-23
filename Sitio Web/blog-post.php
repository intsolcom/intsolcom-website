<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /blog', true, 302);
    exit;
}

$stmt = db()->prepare("SELECT * FROM resources WHERE slug = ? AND status = 1 AND type = 'article' LIMIT 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
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
<main><section class="section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;text-align:center;"><div><h1><?= ht('Article Not Found') ?></h1><p style="margin-top:1rem;color:var(--color-mid);"><?= ht('The blog post you are looking for does not exist or has been removed.') ?></p><a href="/blog" class="btn btn-accent" style="margin-top:2rem;">← <?= ht('Back to Blog') ?></a></div></section></main>
<footer class="footer"><div class="container"><div class="footer__bottom"><span><?= ht(setting('footer_copyright','© 2026 INTSOLCOM LLC')) ?></span></div></div></footer>
<script src="/assets/js/main.js?v=1"></script>
</body></html>
<?php exit; }

$navItems = db()->query("SELECT * FROM nav_items WHERE visible = 1 ORDER BY sort_order ASC")->fetchAll();

$siteName   = setting('site_name', 'INTSOLCOM');
$logoText   = setting('logo_text', 'INTSOL');
$logoAccent = setting('logo_accent', 'COM');

$metaTitle       = !empty($post['meta_title']) ? $post['meta_title'] : ($post['title'] . ' — ' . $siteName);
$metaDescription = !empty($post['meta_desc']) ? $post['meta_desc'] : mb_substr(strip_tags($post['excerpt'] ?? ''), 0, 160);
$currentUrl      = SITE_URL . '/blog/' . $post['slug'];
$lang            = currentLang();

$relStmt = db()->prepare("SELECT * FROM resources WHERE type = 'article' AND status = 1 AND id != ? ORDER BY published_at DESC LIMIT 3");
$relStmt->execute([$post['id']]);
$related = $relStmt->fetchAll();

db()->prepare("UPDATE resources SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

$shareUrl = urlencode($currentUrl);
$shareTitle = urlencode($post['title']);
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
    <?php if (!empty($post['cover_image'])): ?>
    <meta property="og:image" content="<?= h(UPLOAD_URL . $post['cover_image']) ?>">
    <?php endif; ?>
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
        "@type": "Article",
        "headline": "<?= h($post['title']) ?>",
        "description": "<?= h(strip_tags($post['excerpt'] ?? '')) ?>",
        "author": { "@type": "Person", "name": "<?= h($post['author'] ?? 'INTSOLCOM') ?>" },
        "datePublished": "<?= h($post['published_at']) ?>",
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
    <article class="section" style="padding-top:var(--space-40);padding-bottom:0;">
        <div class="container-sm">
            <div class="hero__badge" style="margin-bottom:var(--space-4);">
                <span class="hero__badge-dot"></span> <?= ht('Article') ?>
            </div>
            <h1 style="font-size:clamp(1.75rem,3vw,2.5rem);font-weight:800;line-height:1.2;color:var(--color-dark);margin-bottom:var(--space-5);"><?= ht($post['title']) ?></h1>

            <div style="display:flex;align-items:center;gap:var(--space-3);color:var(--color-light);font-size:0.9375rem;margin-bottom:var(--space-6);flex-wrap:wrap;">
                <?php if ($post['author']): ?><span style="color:var(--color-dark);font-weight:600;"><?= ht($post['author']) ?></span><span>·</span><?php endif; ?>
                <?php if ($post['published_at']): ?><span><?= h(date('M j, Y', strtotime($post['published_at']))) ?></span><span>·</span><?php endif; ?>
                <?php if ($post['read_time']): ?><span><?= (int)$post['read_time'] ?> min read</span><?php endif; ?>
                <span style="margin-left:auto;display:flex;gap:var(--space-2);">
                    <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--color-surface2);color:var(--color-mid);text-decoration:none;font-size:0.75rem;transition:background .2s;" onmouseover="this.style.background='var(--color-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--color-surface2)';this.style.color='var(--color-mid)'" aria-label="Share on Twitter">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $shareUrl ?>&title=<?= $shareTitle ?>" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--color-surface2);color:var(--color-mid);text-decoration:none;font-size:0.75rem;transition:background .2s;" onmouseover="this.style.background='var(--color-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--color-surface2)';this.style.color='var(--color-mid)'" aria-label="Share on LinkedIn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--color-surface2);color:var(--color-mid);text-decoration:none;font-size:0.75rem;transition:background .2s;" onmouseover="this.style.background='var(--color-dark)';this.style.color='#fff'" onmouseout="this.style.background='var(--color-surface2)';this.style.color='var(--color-mid)'" aria-label="Share on Facebook">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </span>
            </div>
        </div>
    </article>

    <?php if (!empty($post['cover_image'])): ?>
    <section class="section" style="padding-top:0;padding-bottom:0;">
        <div class="container">
            <img src="<?= h(UPLOAD_URL . $post['cover_image']) ?>" alt="<?= h($post['title']) ?>" style="width:100%;border-radius:var(--radius-lg);box-shadow:var(--shadow-md);" loading="lazy">
        </div>
    </section>
    <?php endif; ?>

    <section class="section">
        <div class="container-sm">
            <?php if (!empty($post['excerpt'])): ?>
                <p style="font-size:1.25rem;line-height:1.65;color:var(--color-mid);margin-bottom:var(--space-8);font-weight:500;"><?= ht($post['excerpt']) ?></p>
            <?php endif; ?>
            <div class="blog-content" style="line-height:1.75;font-size:1.0625rem;color:var(--color-mid);">
                <?= $post['content'] ?>
            </div>
            <div style="margin-top:var(--space-10);padding-top:var(--space-8);border-top:1px solid var(--color-surface2);">
                <a href="/blog" class="btn btn-ghost">← <?= ht('Back to Blog') ?></a>
            </div>
        </div>
    </section>

    <?php if (!empty($related)): ?>
    <section class="section section-surface">
        <div class="container">
            <div class="reveal" style="margin-bottom:var(--space-8);">
                <span class="section-label"><?= ht('Related') ?></span>
                <h2 class="section-title" style="font-size:1.75rem;"><?= ht('More Articles') ?></h2>
            </div>
            <div class="grid-3">
                <?php foreach ($related as $idx => $rel): ?>
                <a href="/blog/<?= h($rel['slug']) ?>" class="card card-hover reveal" style="text-decoration:none;color:inherit;transition-delay:<?= $idx * 0.05 ?>s;">
                    <?php if (!empty($rel['cover_image'])): ?>
                        <div style="width:100%;aspect-ratio:16/9;overflow:hidden;border-radius:var(--radius-md);margin-bottom:var(--space-4);">
                            <img src="<?= h(UPLOAD_URL . $rel['cover_image']) ?>" alt="<?= h($rel['title']) ?>" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                        </div>
                    <?php endif; ?>
                    <span class="eco-card__badge" style="color:#00C896;background:rgba(0,200,150,.08);"><?= ht('Article') ?></span>
                    <h3 style="font-size:1.0625rem;margin-top:var(--space-3);"><?= ht($rel['title']) ?></h3>
                    <p style="font-size:0.875rem;"><?= ht(mb_strlen($rel['excerpt']) > 100 ? mb_substr($rel['excerpt'], 0, 100) . '...' : $rel['excerpt']) ?></p>
                    <div style="display:flex;align-items:center;gap:var(--space-4);font-size:0.75rem;color:var(--color-light);margin-top:var(--space-3);">
                        <?php if ($rel['read_time']): ?><span><?= (int)$rel['read_time'] ?> min read</span><?php endif; ?>
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
