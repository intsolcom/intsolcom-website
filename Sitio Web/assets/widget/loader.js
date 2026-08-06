/*!
 * INTSOLCOM Blog Widget — Loader
 * @version 1.0.0
 * @license Proprietary
 */
(function() {
  'use strict';

  var containers = document.querySelectorAll('[data-blog-widget]');
  if (!containers.length) return;

  // Queue system: capture calls before widget.js loads
  var queue = [];
  var loaded = false;

  window.IntsolcomBlog = window.IntsolcomBlog || {
    widget: function(container, config) {
      if (loaded) {
        // Widget already loaded, dispatch directly
        window.dispatchEvent(new CustomEvent('intsolcom-blog:render', { detail: { container: container, config: config } }));
      } else {
        queue.push({ container: container, config: config });
      }
    },
    _flush: function() {
      loaded = true;
      queue.forEach(function(item) {
        window.dispatchEvent(new CustomEvent('intsolcom-blog:render', { detail: item }));
      });
      queue = [];
    }
  };

  // Process existing containers
  containers.forEach(function(el) {
    var config = {
      apiKey: el.getAttribute('data-api-key') || '',
      site: el.getAttribute('data-site') || 'intsolcom',
      locale: el.getAttribute('data-locale') || 'en',
      limit: parseInt(el.getAttribute('data-limit')) || 6,
      layout: el.getAttribute('data-layout') || 'grid',
      category: el.getAttribute('data-category') || 'all',
      tag: el.getAttribute('data-tag') || '',
      theme: el.getAttribute('data-theme') || 'light',
      showExcerpt: el.getAttribute('data-show-excerpt') !== 'false',
      showDate: el.getAttribute('data-show-date') !== 'false',
      showAuthor: el.getAttribute('data-show-author') !== 'false',
    };
    window.IntsolcomBlog.widget(el, config);
  });

  // Load widget core
  var baseUrl = (document.currentScript && document.currentScript.src)
    ? document.currentScript.src.replace(/\/loader\.js.*$/, '')
    : 'https://blog.intsolcom.com/widget';

  var s = document.createElement('script');
  s.src = baseUrl + '/widget.js';
  s.async = true;
  s.onload = function() { window.IntsolcomBlog._flush(); };
  document.head.appendChild(s);
})();
