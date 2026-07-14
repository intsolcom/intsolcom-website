<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

$siteUrl = SITE_URL;

$pages = db()->query("SELECT slug, created_at FROM pages WHERE status = 1")->fetchAll();
$busUnits = db()->query("SELECT slug FROM business_units WHERE status = 1")->fetchAll();
$products = db()->query("SELECT slug FROM products WHERE status = 1")->fetchAll();
$industries = db()->query("SELECT slug FROM industries WHERE status = 1")->fetchAll();
$resources = db()->query("SELECT slug, published_at FROM resources WHERE status = 1")->fetchAll();

$slugToPath = [
  'home'             => '',
  'holding'          => 'holding',
  'business-units'   => 'business-units',
  'technology'       => 'technology',
  'industries'       => 'industries',
  'resources'        => 'resources',
  'contact'          => 'contact',
];

$staticRoutes = [
  'blog'      => 'blog',
];

function sitemapDate(string $dt = null): string {
  if ($dt) return date('Y-m-d', strtotime($dt));
  return date('Y-m-d');
}

function sitemapLoc(string $url): string {
  return htmlspecialchars($url, ENT_XML1, 'UTF-8');
}
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

  <?php foreach ($pages as $page):
    $path = $slugToPath[$page['slug']] ?? null;
    if ($path === null) continue;
    $loc = $path === '' ? $siteUrl : rtrim($siteUrl, '/') . '/' . $path;
  ?>
  <url>
    <loc><?= sitemapLoc($loc) ?></loc>
    <lastmod><?= sitemapDate($page['created_at']) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority><?= ($path === '') ? '1.0' : '0.8' ?></priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($staticRoutes as $path): ?>
  <url>
    <loc><?= sitemapLoc(rtrim($siteUrl, '/') . '/' . $path) ?></loc>
    <lastmod><?= sitemapDate() ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($busUnits as $bu): ?>
  <url>
    <loc><?= sitemapLoc(rtrim($siteUrl, '/') . '/business-units/' . $bu['slug']) ?></loc>
    <lastmod><?= sitemapDate() ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($products as $prod): ?>
  <url>
    <loc><?= sitemapLoc(rtrim($siteUrl, '/') . '/technology/' . $prod['slug']) ?></loc>
    <lastmod><?= sitemapDate() ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($industries as $ind): ?>
  <url>
    <loc><?= sitemapLoc(rtrim($siteUrl, '/') . '/industries/' . $ind['slug']) ?></loc>
    <lastmod><?= sitemapDate() ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

  <?php foreach ($resources as $res):
    $resDate = $res['published_at'] ? sitemapDate($res['published_at']) : sitemapDate();
  ?>
  <url>
    <loc><?= sitemapLoc(rtrim($siteUrl, '/') . '/resources/' . $res['slug']) ?></loc>
    <lastmod><?= $resDate ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>

</urlset>
