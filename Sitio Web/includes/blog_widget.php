<?php
// ============================================================
// INTSOLCOM Blog Widget — Shortcode / Helper
// ============================================================
// Usage in any PHP page:
//   blog_widget(['limit'=>3,'layout'=>'grid','category'=>'technology']);
// Or:
//   echo blog_widget_html(6, 'grid');
// ============================================================

function blog_widget_html(int $limit = 6, string $layout = 'grid', string $category = 'all', string $tag = ''): string {
    $attrs = "data-blog-widget data-limit=\"$limit\" data-layout=\"$layout\" data-category=\"$category\"";
    if ($tag) $attrs .= " data-tag=\"$tag\"";
    $attrs .= ' data-api-key="pk_test_intsolcom_blog_2026" data-site="intsolcom"';

    return <<<HTML
<div $attrs>
  <noscript>
    <p style="text-align:center;padding:2rem;color:#64748b">
      Visit our blog at <a href="/blog" style="color:#00C896">intsolcom.com/blog</a>
    </p>
  </noscript>
</div>
<script async src="/assets/widget/loader.js"></script>
HTML;
}

function blog_widget(array $opts = []): void {
    echo blog_widget_html(
        $opts['limit'] ?? 6,
        $opts['layout'] ?? 'grid',
        $opts['category'] ?? 'all',
        $opts['tag'] ?? ''
    );
}
