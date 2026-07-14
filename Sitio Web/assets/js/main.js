/* ============================================================
   INTSOLCOM LLC — Global JavaScript v1.0
   Technology Holding Company
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {

  /* ==========================================================
     1. CUSTOM CURSOR — dot + ring, hidden on mobile (< 768px)
     ========================================================== */
  const dot  = document.querySelector('.cursor-dot');
  const ring = document.querySelector('.cursor-ring');
  if (dot && ring && window.innerWidth > 768) {
    let mx = 0, my = 0, rx = 0, ry = 0;
    document.addEventListener('mousemove', e => {
      mx = e.clientX; my = e.clientY;
      dot.style.left = mx + 'px'; dot.style.top = my + 'px';
    });
    (function animRing() {
      rx += (mx - rx) * 0.12; ry += (my - ry) * 0.12;
      ring.style.left = rx + 'px'; ring.style.top = ry + 'px';
      requestAnimationFrame(animRing);
    })();
    document.querySelectorAll('a, button, [role="button"], .hover-target').forEach(el => {
      el.addEventListener('mouseenter', () => ring.classList.add('hov'));
      el.addEventListener('mouseleave', () => ring.classList.remove('hov'));
    });
    window.addEventListener('resize', () => {
      if (window.innerWidth <= 768) { dot.style.display = 'none'; ring.style.display = 'none'; }
      else { dot.style.display = ''; ring.style.display = ''; }
    });
  }

  /* ==========================================================
     2. NAVIGATION — scroll class, mobile toggle
     ========================================================== */
  const nav = document.querySelector('.nav');
  if (nav) {
    const tick = () => nav.classList.toggle('scrolled', window.scrollY > 50);
    window.addEventListener('scroll', tick, { passive: true }); tick();
  }

  const navToggle   = document.querySelector('.nav-toggle');
  const navMobile   = document.querySelector('.nav-mobile');
  if (navToggle && navMobile) {
    navToggle.addEventListener('click', () => {
      navToggle.classList.toggle('open');
      navMobile.classList.toggle('open');
      document.body.classList.toggle('no-scroll');
    });
    navMobile.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
      navToggle.classList.remove('open');
      navMobile.classList.remove('open');
      document.body.classList.remove('no-scroll');
    }));
  }

  /* ==========================================================
     3. SCROLL REVEAL — .reveal, .reveal-left, .reveal-right
     ========================================================== */
  const revealObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const d = +(e.target.dataset.delay || 0);
        setTimeout(() => e.target.classList.add('visible'), d);
        revealObserver.unobserve(e.target);
      }
    });
  }, { threshold: 0.08 });
  document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => revealObserver.observe(el));

  /* ==========================================================
     4. COUNTER ANIMATION — [data-count] with easeOutCubic
     ========================================================== */
  const counterObserver = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el     = e.target;
      const target = parseFloat(el.dataset.count);
      const suffix = el.dataset.suffix   || '';
      const prefix = el.dataset.prefix   || '';
      const dec    = +(el.dataset.decimals || 0);
      const dur    = 1800;
      const t0     = performance.now();
      (function tick(now) {
        const p     = Math.min((now - t0) / dur, 1);
        const ease  = 1 - Math.pow(1 - p, 3); // easeOutCubic
        el.textContent = prefix + (ease * target).toFixed(dec) + suffix;
        if (p < 1) requestAnimationFrame(tick);
      })(performance.now());
      counterObserver.unobserve(el);
    });
  }, { threshold: 0.5 });
  document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));

  /* ==========================================================
     5. VIDEO BACKGROUND SYSTEM
     ========================================================== */
  const VC = window.MBPO_VIDEO || (() => {
    const meta = document.querySelector('meta[name="video-config"]');
    if (!meta) return {};
    try { return JSON.parse(meta.getAttribute('content') || '{}'); } catch (_) { return {}; }
  })();

  const vDefaults = {
    mute:           VC.mute           !== undefined ? VC.mute           : 1,
    autoplay:       VC.autoplay       !== undefined ? VC.autoplay       : 1,
    loop:           VC.loop           !== undefined ? VC.loop           : 1,
    controls:       VC.controls       !== undefined ? VC.controls       : 0,
    rel:            VC.rel            !== undefined ? VC.rel            : 0,
    modestbranding: VC.modestbranding !== undefined ? VC.modestbranding : 1,
    showinfo:       VC.showinfo       !== undefined ? VC.showinfo       : 0,
    iv_load_policy: VC.iv_load_policy !== undefined ? VC.iv_load_policy : 3,
    disablekb:      VC.disablekb      !== undefined ? VC.disablekb      : 1,
    playsinline:    1,
    enablejsapi:    1,
    speed:          VC.speed          !== undefined ? VC.speed          : 1,
    start:          VC.start          || 0,
    end:            VC.end            || 0,
    layout:         VC.layout         || 'cover',
    voffset:        VC.voffset        || 0,
  };

  function buildYTSrc(id, overrides) {
    const cfg = Object.assign({}, vDefaults, overrides || {});
    const p = new URLSearchParams({
      autoplay:       cfg.autoplay,
      mute:           cfg.mute,
      loop:           cfg.loop,
      playlist:       id,
      controls:       cfg.controls,
      rel:            cfg.rel,
      modestbranding: cfg.modestbranding,
      showinfo:       cfg.showinfo,
      iv_load_policy: cfg.iv_load_policy,
      disablekb:      cfg.disablekb,
      playsinline:    cfg.playsinline,
      enablejsapi:    cfg.enablejsapi,
      origin:         location.origin,
    });
    if (cfg.start > 0) p.set('start', cfg.start);
    if (cfg.end   > 0) p.set('end',   cfg.end);
    return `https://www.youtube.com/embed/${id}?${p.toString()}`;
  }

  function applyVideoLayout(iframe, layout, voffset) {
    if (layout === 'boxed') {
      iframe.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;border:none;pointer-events:none;';
    } else if (voffset && voffset !== 0) {
      iframe.style.top = (50 + voffset) + '%';
    }
  }

  function applySpeed(iframe, speed) {
    if (!speed || speed === 1) return;
    try {
      iframe.contentWindow.postMessage(JSON.stringify({
        event: 'command',
        func:  'setPlaybackRate',
        args:  [parseFloat(speed)]
      }), '*');
    } catch (_) {}
  }

  function setupVideoPlayer(iframe, videoWrapper, id) {
    let player = null;
    const onReady = () => {
      player.playVideo();
      const speed = parseFloat(videoWrapper.dataset.vSpeed || VC.speed || 1);
      if (speed !== 1) player.setPlaybackRate(speed);
    };

    const onApiReady = () => {
      player = new YT.Player(iframe, {
        events: { onReady },
      });
    };

    if (window.YT && window.YT.Player) {
      onApiReady();
    } else {
      if (!document.getElementById('yt-api-script')) {
        const script = document.createElement('script');
        script.id = 'yt-api-script';
        script.src = 'https://www.youtube.com/iframe_api';
        document.head.appendChild(script);
      }
      const prev = window.onYouTubeIframeAPIReady;
      window.onYouTubeIframeAPIReady = () => {
        if (prev) prev();
        if (videoWrapper.isConnected) onApiReady();
      };
    }
  }

  document.querySelectorAll('.video-bg, [data-video-id]').forEach(wrapper => {
    const id = wrapper.dataset.videoId || wrapper.dataset.video || '';
    if (!id || !id.trim()) return;

    const overrides = {
      controls: wrapper.dataset.vControls !== undefined ? +wrapper.dataset.vControls : undefined,
      mute:     wrapper.dataset.vMute     !== undefined ? +wrapper.dataset.vMute     : undefined,
      loop:     wrapper.dataset.vLoop     !== undefined ? +wrapper.dataset.vLoop     : undefined,
      rel:      wrapper.dataset.vRel      !== undefined ? +wrapper.dataset.vRel      : undefined,
      speed:    wrapper.dataset.vSpeed    !== undefined ? +wrapper.dataset.vSpeed    : undefined,
      start:    wrapper.dataset.vStart    !== undefined ? +wrapper.dataset.vStart    : undefined,
      end:      wrapper.dataset.vEnd      !== undefined ? +wrapper.dataset.vEnd      : undefined,
      layout:   wrapper.dataset.vLayout   || undefined,
      voffset:  wrapper.dataset.vOffset   !== undefined ? +wrapper.dataset.vOffset   : undefined,
    };
    Object.keys(overrides).forEach(k => overrides[k] === undefined && delete overrides[k]);
    const cfg = Object.assign({}, vDefaults, overrides);

    if (getComputedStyle(wrapper).position === 'static') {
      wrapper.style.position = 'relative';
    }
    wrapper.style.overflow = 'hidden';

    const videoObs = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          if (!wrapper.querySelector('iframe')) {
            const iframe = document.createElement('iframe');
            iframe.src   = buildYTSrc(id, overrides);
            iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
            iframe.setAttribute('allowfullscreen', 'false');
            iframe.setAttribute('loading', 'lazy');
            iframe.setAttribute('title', '');
            iframe.setAttribute('aria-hidden', 'true');
            iframe.setAttribute('tabindex', '-1');
            applyVideoLayout(iframe, cfg.layout, cfg.voffset);
            wrapper.appendChild(iframe);
            if (cfg.speed && cfg.speed !== 1) {
              iframe.addEventListener('load', () => setTimeout(() => applySpeed(iframe, cfg.speed), 1500));
            }
            setupVideoPlayer(iframe, wrapper, id);
          } else {
            const iframe = wrapper.querySelector('iframe');
            iframe.contentWindow && iframe.contentWindow.postMessage(JSON.stringify({
              event: 'command', func: 'playVideo', args: []
            }), '*');
          }
        } else {
          const iframe = wrapper.querySelector('iframe');
          iframe && iframe.contentWindow && iframe.contentWindow.postMessage(JSON.stringify({
            event: 'command', func: 'pauseVideo', args: []
          }), '*');
        }
      });
    }, { threshold: 0, rootMargin: '200px' });

    videoObs.observe(wrapper);
  });

  /* ==========================================================
     6. ECOSYSTEM DIAGRAM ANIMATION
     ========================================================== */
  const ecoDiag = document.querySelector('.eco-diagram');
  if (ecoDiag) {
    const ecoObserver = new IntersectionObserver(entries => {
      if (!entries[0].isIntersecting) return;

      const cards = ecoDiag.querySelectorAll('.eco-card');
      cards.forEach((card, i) => {
        setTimeout(() => card.classList.add('visible'), i * 150);
      });

      const paths = ecoDiag.querySelectorAll('svg path, .eco-line');
      paths.forEach((path, i) => {
        const length = path.getTotalLength ? path.getTotalLength() : 0;
        if (length) {
          path.style.strokeDasharray  = length;
          path.style.strokeDashoffset = length;
          path.style.transition = 'stroke-dashoffset 1.2s ease ' + (i * 100 + 300) + 'ms';
          path.style.strokeDashoffset = '0';
        } else {
          setTimeout(() => path.classList.add('visible'), i * 100 + 300);
        }
      });

      ecoObserver.unobserve(ecoDiag);
    }, { threshold: 0.2 });
    ecoObserver.observe(ecoDiag);
  }

  /* ==========================================================
     7. FAQ ACCORDION — global toggleFaq(btn)
     ========================================================== */
  window.toggleFaq = function(btn) {
    const content = btn.nextElementSibling;
    const icon    = btn.querySelector('.faq-icon, .accordion-icon, svg, i');
    if (!content) return;
    const isOpen = content.style.display === 'block' || content.classList.contains('open');
    if (isOpen) {
      content.style.display = 'none';
      content.classList.remove('open');
      btn.classList.remove('active');
      if (icon) icon.style.transform = '';
    } else {
      content.style.display = 'block';
      content.classList.add('open');
      btn.classList.add('active');
      if (icon) icon.style.transform = 'rotate(180deg)';
    }
  };

  document.querySelectorAll('.faq-question, .accordion-header').forEach(btn => {
    btn.addEventListener('click', () => window.toggleFaq(btn));
  });

  /* ==========================================================
     8. STICKY NAV SCROLL — active link highlighting
     ========================================================== */
  const sections = document.querySelectorAll('section[id], [data-nav-section]');
  const navLinks = document.querySelectorAll('.nav-links a, .nav-mobile a');
  if (sections.length && navLinks.length) {
    const highlightNav = () => {
      let current = '';
      const offset = 100;
      sections.forEach(sec => {
        if (sec.getBoundingClientRect().top <= offset) {
          current = sec.getAttribute('id') || sec.dataset.navSection;
        }
      });
      navLinks.forEach(a => {
        a.classList.remove('active');
        const href = a.getAttribute('href') || '';
        if (href === '#' + current || href === current) a.classList.add('active');
      });
    };
    window.addEventListener('scroll', highlightNav, { passive: true });
    // Also set based on path
    const path = window.location.pathname.split('/').pop() || 'index.html';
    navLinks.forEach(a => {
      const h = a.getAttribute('href') || '';
      if (h === path || (path === '' && h === 'index.html') || h === path.replace('.html', ''))
        a.classList.add('active');
    });
  }

  /* ==========================================================
     9. SMOOTH ANCHOR SCROLL
     ========================================================== */
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        window.scrollTo({
          top: target.getBoundingClientRect().top + window.scrollY - 90,
          behavior: 'smooth'
        });
      }
    });
  });

  /* ==========================================================
     10. SCROLL TO TOP BUTTON
     ========================================================== */
  const scrollTopBtn = document.querySelector('#scroll-top');
  if (scrollTopBtn) {
    window.addEventListener('scroll', () => {
      scrollTopBtn.classList.toggle('visible', window.scrollY > 400);
    }, { passive: true });
    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ==========================================================
     11. STATS TICKER — .stats-band counter animation
     ========================================================== */
  const statsBand = document.querySelector('.stats-band');
  if (statsBand) {
    const statsCounters = statsBand.querySelectorAll('[data-count]');
    if (statsCounters.length) {
      const statsObs = new IntersectionObserver(entries => {
        entries.forEach(e => {
          if (!e.isIntersecting) return;
          const el     = e.target;
          const target = parseFloat(el.dataset.count);
          const suffix = el.dataset.suffix   || '';
          const prefix = el.dataset.prefix   || '';
          const dec    = +(el.dataset.decimals || 0);
          const dur    = 1800;
          const t0     = performance.now();
          (function tick(now) {
            const p    = Math.min((now - t0) / dur, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            el.textContent = prefix + (ease * target).toFixed(dec) + suffix;
            if (p < 1) requestAnimationFrame(tick);
          })(performance.now());
          statsObs.unobserve(el);
        });
      }, { threshold: 0.5 });
      statsCounters.forEach(el => statsObs.observe(el));
    }
  }

  /* ==========================================================
     12. PARTICLE / GRADIENT ANIMATION — .gradient-bg blobs
     ========================================================== */
  document.querySelectorAll('.gradient-bg').forEach(el => {
    const prop = el.dataset.gradientSpeed || '20';
    el.style.animation = `gradientShift ${prop}s ease-in-out infinite alternate`;
  });

  const gStyle = document.createElement('style');
  gStyle.textContent = `
    @keyframes gradientShift {
      0%   { background-position: 0% 50%; }
      50%  { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    @keyframes blobFloat1 {
      0%,100% { transform: translate(0, 0) scale(1); }
      33%     { transform: translate(30px, -30px) scale(1.05); }
      66%     { transform: translate(-20px, 20px) scale(0.95); }
    }
    @keyframes blobFloat2 {
      0%,100% { transform: translate(0, 0) scale(1); }
      33%     { transform: translate(-30px, 20px) scale(1.05); }
      66%     { transform: translate(20px, -20px) scale(0.95); }
    }
    @keyframes blobFloat3 {
      0%,100% { transform: translate(0, 0) scale(1); }
      50%     { transform: translate(20px, 30px) scale(1.08); }
    }
    .gradient-blob-1 { animation: blobFloat1 8s ease-in-out infinite; }
    .gradient-blob-2 { animation: blobFloat2 10s ease-in-out infinite; }
    .gradient-blob-3 { animation: blobFloat3 12s ease-in-out infinite; }
  `;
  document.head.appendChild(gStyle);

  /* ==========================================================
     13. FORM VALIDATION
     ========================================================== */
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', e => {
      let valid = true;
      const required = form.querySelectorAll('[required]');
      required.forEach(field => {
        field.classList.remove('error');
        const errMsg = field.nextElementSibling?.classList.contains('form-error') ? field.nextElementSibling : null;
        if (errMsg) errMsg.remove();

        if (!field.value.trim()) {
          valid = false;
          field.classList.add('error');
          const span = document.createElement('span');
          span.className = 'form-error';
          span.textContent = field.dataset.errorMsg || 'This field is required';
          field.after(span);
        } else if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value.trim())) {
          valid = false;
          field.classList.add('error');
          const span = document.createElement('span');
          span.className = 'form-error';
          span.textContent = 'Please enter a valid email address';
          field.after(span);
        }
      });

      if (!valid) {
        e.preventDefault();
        return;
      }

      // Loading state
      const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (submitBtn) {
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        const origText = submitBtn.textContent;
        submitBtn.dataset.origText = origText;
        submitBtn.textContent = submitBtn.dataset.loadingText || 'Sending...';
        setTimeout(() => {
          submitBtn.classList.remove('loading');
          submitBtn.disabled = false;
          submitBtn.textContent = submitBtn.dataset.origText || origText;
        }, 3000);
      }
    });
  });

  /* ==========================================================
     14. LOADING STATES — button .loading class capability
     ========================================================== */
  document.querySelectorAll('[data-loading]').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.classList.contains('loading')) return;
      this.classList.add('loading');
      this.disabled = true;
      const orig = this.textContent;
      this.dataset.origText = orig;
      this.textContent = this.dataset.loadingText || 'Loading...';
      const done = () => {
        this.classList.remove('loading');
        this.disabled = false;
        this.textContent = this.dataset.origText || orig;
      };
      const timeout = parseInt(this.dataset.loadingTimeout) || 3000;
      setTimeout(done, timeout);
    });
  });

  /* ==========================================================
     15. PRODUCT CARD TILT — subtle 3D hover effect
     ========================================================== */
  document.querySelectorAll('.product-card').forEach(card => {
    card.addEventListener('mousemove', e => {
      const rect   = card.getBoundingClientRect();
      const x      = e.clientX - rect.left;
      const y      = e.clientY - rect.top;
      const cx     = rect.width  / 2;
      const cy     = rect.height / 2;
      const tiltX  = ((y - cy) / cy) * -8;
      const tiltY  = ((x - cx) / cx) *  8;
      card.style.transform = `perspective(600px) rotateX(${tiltX}deg) rotateY(${tiltY}deg) scale3d(1.02,1.02,1.02)`;
    });
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'perspective(600px) rotateX(0) rotateY(0) scale3d(1,1,1)';
    });
    card.style.transition = 'transform 0.1s ease-out';
  });

  /* ==========================================================
     SUBTLE PARALLAX
     ========================================================== */
  const parallaxEls = document.querySelectorAll('[data-parallax]');
  if (parallaxEls.length) {
    window.addEventListener('scroll', () => {
      const sy = window.scrollY;
      parallaxEls.forEach(el => {
        const speed = parseFloat(el.dataset.parallax || 0.15);
        el.style.transform = `translateY(${sy * speed}px)`;
      });
    }, { passive: true });
  }

});
