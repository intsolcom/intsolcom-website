/*!
 * INTSOLCOM Blog Widget — Web Component
 * @version 1.0.0
 */
(function() {
  'use strict';

  var API_BASE = 'https://blog.intsolcom.com/api/v2';
  var currentScript = document.currentScript;
  if (currentScript && currentScript.src) {
    var u = new URL(currentScript.src);
    API_BASE = u.origin + '/api/v2';
  }

  // Theme CSS
  var THEME = '\
    :host { display:block; font-family:Inter,-apple-system,sans-serif; }\
    .blog-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.5rem; }\
    .blog-hero { display:grid; grid-template-columns:2fr 1fr; gap:1.5rem; }\
    .blog-hero .card:first-child { grid-row:span 2; }\
    .blog-list { display:flex; flex-direction:column; gap:1rem; }\
    .card { background:var(--blog-bg,#fff); border-radius:var(--blog-radius,12px); overflow:hidden; border:1px solid rgba(0,0,0,.06); transition:transform .2s,box-shadow .2s; cursor:pointer; }\
    .card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.08); }\
    .card-img { width:100%; aspect-ratio:16/10; object-fit:cover; background:#f1f5f9; }\
    .card-body { padding:1rem 1.25rem; }\
    .card-cat { display:inline-block; font-size:.68rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; padding:2px 8px; border-radius:20px; margin-bottom:.5rem; color:var(--blog-primary,#00C896); background:rgba(0,200,150,.08); }\
    .card-title { font-size:.95rem; font-weight:700; color:var(--blog-text,#0F172A); line-height:1.35; margin:.35rem 0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }\
    .card-excerpt { font-size:.8rem; color:#64748b; line-height:1.5; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }\
    .card-meta { display:flex; align-items:center; gap:.5rem; font-size:.7rem; color:#94a3b8; margin-top:.5rem; }\
    .card-meta span { display:flex; align-items:center; gap:3px; }\
    .featured-card { position:relative; }\
    .featured-card .card-img { aspect-ratio:auto; height:280px; }\
    .featured-badge { position:absolute; top:12px; left:12px; background:var(--blog-primary,#00C896); color:#fff; font-size:.65rem; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:.04em; text-transform:uppercase; }\
    .pagination { display:flex; align-items:center; justify-content:center; gap:.5rem; margin-top:2rem; }\
    .pagination button { padding:.5rem 1rem; border:1px solid rgba(0,0,0,.1); border-radius:8px; background:#fff; cursor:pointer; font-size:.8rem; font-family:inherit; transition:all .15s; }\
    .pagination button:hover:not(:disabled) { border-color:var(--blog-primary,#00C896); color:var(--blog-primary,#00C896); }\
    .pagination button:disabled { opacity:.4; cursor:default; }\
    .skeleton { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; border-radius:8px; }\
    .skeleton-img { width:100%; aspect-ratio:16/10; }\
    .skeleton-title { height:20px; width:80%; margin:.75rem 1rem .5rem; }\
    .skeleton-text { height:14px; width:90%; margin:.25rem 1rem; }\
    .skeleton-meta { height:12px; width:50%; margin:.5rem 1rem 1rem; }\
    @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }\
    .error-state { text-align:center; padding:2rem; color:#94a3b8; }\
    .empty-state { text-align:center; padding:2rem; color:#94a3b8; }\
    noscript { display:block; text-align:center; padding:2rem; }\
    @media(max-width:768px){ .blog-grid,.blog-hero{ grid-template-columns:1fr; } .blog-hero .card:first-child{ grid-row:auto; } }\
  ';

  class IntsolcomBlogWidget extends HTMLElement {
    constructor() {
      super();
      this.attachShadow({ mode: 'open' });
      this._config = {};
      this._page = 1;
      this._loading = false;
    }

    connectedCallback() {
      this.render();
    }

    setConfig(config) {
      this._config = Object.assign({
        apiKey: '', site: 'intsolcom', locale: 'en', limit: 6,
        layout: 'grid', category: 'all', tag: '', page: 1,
        showExcerpt: true, showDate: true, showAuthor: true,
      }, config);
      this._page = this._config.page || 1;
      this.loadPosts();
    }

    async loadPosts() {
      if (this._loading) return;
      this._loading = true;
      this.showSkeleton();

      var params = new URLSearchParams({
        per_page: this._config.limit,
        page: this._page,
        exclude_body: 'true',
        sort: 'latest',
      });
      if (this._config.category && this._config.category !== 'all') params.set('category', this._config.category);
      if (this._config.tag) params.set('tag', this._config.tag);

      try {
        var resp = await fetch(API_BASE + '/posts?' + params.toString(), {
          headers: { 'X-API-Key': this._config.apiKey, 'Accept': 'application/json' }
        });
        if (!resp.ok) throw new Error('API error ' + resp.status);
        var json = await resp.json();
        if (json.ok) this.renderPosts(json.data);
        else this.showError(json.error || 'Failed to load posts');
      } catch (e) {
        this.showError('Could not load blog posts');
      }
      this._loading = false;
    }

    showSkeleton() {
      var cards = '';
      for (var i = 0; i < this._config.limit; i++) {
        cards += '<div class="card"><div class="skeleton skeleton-img"></div><div class="skeleton skeleton-title"></div><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-meta"></div></div>';
      }
      this.shadowRoot.innerHTML = '<style>' + THEME + '</style><div class="blog-' + this._config.layout + '">' + cards + '</div>';
    }

    renderPosts(data) {
      var posts = data.items || [];
      var pagination = data.pagination || {};
      if (!posts.length) {
        this.shadowRoot.innerHTML = '<style>' + THEME + '</style><div class="empty-state">No posts yet.</div>';
        return;
      }

      var layoutClass = this._config.layout === 'hero' ? 'blog-hero' : (this._config.layout === 'list' ? 'blog-list' : 'blog-grid');
      var html = '<style>' + THEME + '</style><div class="' + layoutClass + '">';

      posts.forEach(function(p, i) {
        var isFeatured = i === 0 && p.featured;
        var url = 'https://blog.intsolcom.com/' + p.slug;
        html += '<a class="card' + (isFeatured ? ' featured-card' : '') + '" href="' + url + '" target="_blank" rel="noopener">';
        if (isFeatured) html += '<span class="featured-badge">Featured</span>';
        if (p.cover_image) html += '<img class="card-img" src="' + p.cover_image + '" alt="' + (p.title || '') + '" loading="lazy">';
        html += '<div class="card-body">';
        if (p.cat_name) html += '<span class="card-cat" style="background:' + (p.cat_color || '#00C896') + '15;color:' + (p.cat_color || '#00C896') + '">' + p.cat_name + '</span>';
        html += '<div class="card-title">' + (p.title || '') + '</div>';
        if (p.excerpt) html += '<div class="card-excerpt">' + p.excerpt + '</div>';
        html += '<div class="card-meta">';
        if (p.author_name) html += '<span>' + p.author_name + '</span>';
        if (p.published_at) html += '<span>' + new Date(p.published_at).toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}) + '</span>';
        if (p.read_time) html += '<span>' + p.read_time + ' min read</span>';
        html += '</div></div></a>';
      });

      html += '</div>';

      // Pagination
      if (pagination.total_pages > 1) {
        var self = this;
        html += '<div class="pagination">';
        html += '<button ' + (pagination.has_prev ? '' : 'disabled') + ' onclick="this.closest(\'intsolcom-blog\').prevPage()">← Prev</button>';
        html += '<span style="font-size:.78rem;color:#94a3b8">' + pagination.page + ' / ' + pagination.total_pages + '</span>';
        html += '<button ' + (pagination.has_next ? '' : 'disabled') + ' onclick="this.closest(\'intsolcom-blog\').nextPage()">Next →</button>';
        html += '</div>';
      }

      this.shadowRoot.innerHTML = html;
    }

    prevPage() { if (this._page > 1) { this._page--; this.loadPosts(); } }
    nextPage() { this._page++; this.loadPosts(); }
    showError(msg) { this.shadowRoot.innerHTML = '<style>' + THEME + '</style><div class="error-state">' + msg + '</div>'; }
  }

  if (!customElements.get('intsolcom-blog')) {
    customElements.define('intsolcom-blog', IntsolcomBlogWidget);
  }

  // Listen for render events from loader queue
  window.addEventListener('intsolcom-blog:render', function(e) {
    var container = e.detail.container;
    var config = e.detail.config;

    // Replace the container div with our web component
    var widget = document.createElement('intsolcom-blog');
    // Copy data attributes to the widget
    for (var key in config) {
      if (config.hasOwnProperty(key)) widget.setAttribute('data-' + key, config[key]);
    }
    widget.setConfig(config);

    // Preserve noscript content
    var noscript = container.querySelector('noscript');
    if (noscript) widget.appendChild(noscript.cloneNode(true));

    container.parentNode.replaceChild(widget, container);
  });

  // Flush queue
  if (window.IntsolcomBlog && window.IntsolcomBlog._flush) {
    window.IntsolcomBlog._flush();
  }
})();
