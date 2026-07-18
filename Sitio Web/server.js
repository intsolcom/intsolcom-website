'use strict';

const http = require('http');
const fs   = require('fs');
const path = require('path');
const zlib = require('zlib');
const crypto = require('crypto');

const PORT = process.env.PORT || 3000;
const ROOT = __dirname;
const TOKEN = crypto.randomBytes(8).toString('hex');

// ============================================================
// IN-MEMORY ASSET CACHE
// ============================================================
const ASSETS = {};
function preload(dir, base = '') {
  for (const name of fs.readdirSync(dir)) {
    const fp = path.join(dir, name);
    if (fs.statSync(fp).isDirectory()) { preload(fp, base + '/' + name); continue; }
    const ext = path.extname(name).toLowerCase();
    if (['.php','.sql','.md','.example'].includes(ext)) continue;
    const buf = fs.readFileSync(fp);
    const gz  = zlib.gzipSync(buf, { level: 9 });
    const etag = '"' + crypto.createHash('md5').update(buf).digest('hex') + '"';
    ASSETS[base + '/' + name] = { buf, gz, etag, mtime: fs.statSync(fp).mtime.toUTCString() };
  }
}
preload(path.join(ROOT, 'assets'), '/assets');

// ============================================================
// MIME MAP
// ============================================================
const MIME = {
  '.css':'text/css; charset=utf-8','.js':'application/javascript; charset=utf-8',
  '.png':'image/png','.jpg':'image/jpeg','.jpeg':'image/jpeg','.webp':'image/webp',
  '.svg':'image/svg+xml','.ico':'image/x-icon','.woff2':'font/woff2',
  '.json':'application/json; charset=utf-8','.xml':'application/xml; charset=utf-8',
  '.txt':'text/plain; charset=utf-8',
};

// ============================================================
// SETTINGS / CONTENT DATA (from DB seed)
// ============================================================
const S = {
  site_name:'INTSOLCOM',site_tagline:'Technology & Operations Ecosystem',
  site_desc:'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.',
  logo_text:'INTSOL',logo_accent:'COM',
  color_accent:'#00C896',color_accent_dk:'#00A67D',color_dark:'#0F172A',
  contact_usa_phone:'+1 (302) 555-0199',
  contact_usa_address:'390 NE 191st St, STE 17284, Miami, FL 33179',
  contact_col_email:'info@intsolcom.com',
  contact_col_address:'Carrera 53 #79-01, Barranquilla, Colombia',
  contact_whatsapp:'+573005550199',
  footer_desc:'The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.',
  footer_copyright:'\u00a9 2026 INTSOLCOM LLC',
  social_linkedin:'https://linkedin.com/company/intsolcom',
};

const PRODUCTS = [
  {name:'WONTIA CRM',slug:'wontia-crm',short_desc:'AI-powered CRM for service businesses \u2014 manage contacts, track deals, and automate workflows.',category:'CRM',icon:'\u{1F465}'},
  {name:'MACROPONDER',slug:'macroponder',short_desc:'AI-powered decision intelligence \u2014 model scenarios, detect bias, and make better strategic choices.',category:'AI Platform',icon:'\u{1F9E0}'},
  {name:'IA Annotation Manager',slug:'ia-annotation-manager',short_desc:'End-to-end platform for AI data annotation \u2014 manage projects, QC, and annotator performance.',category:'AI Platform',icon:'\u{1F3F7}\uFE0F'},
];

const UNITS = [
  {name:'INTSOLCOM SAS',slug:'intsolcom-sas',desc:'Operations & Delivery hub in Barranquilla, Colombia. Nearshore technology services for global clients.',icon:'\u{1F1E8}\u{1F1F4}',tag:'Colombia',caps:'Software Dev,AI Ops,QA Testing,IT Support'},
  {name:'Technology Division',slug:'technology-division',desc:'WONTIA CRM, MACROPONDER, and IA Annotation Manager \u2014 owned and operated software platforms.',icon:'\u2699\uFE0F',tag:'Product Division',caps:'WONTIA CRM,MACROPONDER,Annotation Manager'},
  {name:'Innovation Lab',slug:'innovation-lab',desc:'Research, development, and venture incubation. Exploring AI frontiers, automation, and emerging technologies.',icon:'\u{1F9EA}',tag:'R&D Lab',caps:'AI Research,Prototyping,Ventures'},
];

const NAV = [
  {text:'Technology',url:'/technology',cta:false},
  {text:'Business Units',url:'/business-units',cta:false},
  {text:'Industries',url:'/industries',cta:false},
  {text:'Resources',url:'/resources',cta:false},
  {text:'Contact',url:'/contact',cta:true},
];

const TESTIMONIALS = [
  {name:'Marcus D.',role:'CTO',company:'HealthTech Innovations',content:'INTSOLCOM built our entire AI annotation pipeline in 14 days. The quality control processes and workforce management alone saved us 6 months of internal development. Truly a technology partner, not just a vendor.',rating:5},
  {name:'Elena R.',role:'VP Operations',company:'Meridian Financial',content:'We moved our entire customer operations to INTSOLCOM SAS and saw a 60% cost reduction while improving CSAT scores by 12 points. Their WONTIA CRM platform gave us visibility we never had before.',rating:5},
  {name:'David K.',role:'Founder',company:'Stack AI Labs',content:'As a startup, we needed a partner who could scale with us. INTSOLCOM provided nearshore engineering teams that felt like our own employees. The ecosystem approach \u2014 technology plus operations \u2014 is the real differentiator.',rating:5},
];

// ============================================================
// TEMPLATE ENGINE
// ============================================================
const esc = s => String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

function navHTML(currentPath) {
  let links = '', mobile = '';
  for (const n of NAV) {
    if (n.cta) {
      links += `<a href="${n.url}" class="btn btn-accent nav__cta">${esc(n.text)}</a>`;
      mobile += `<a href="${n.url}" class="btn btn-accent nav__cta">${esc(n.text)}</a>`;
    } else {
      links += `<a href="${n.url}" class="nav__link${currentPath===n.url?' active':''}">${esc(n.text)}</a>`;
      mobile += `<a href="${n.url}" class="nav__mobile-link">${esc(n.text)}</a>`;
    }
  }
  return {links,mobile};
}

function footerHTML() {
  return `
<footer class="footer">
  <div class="container">
    <div class="footer__grid">
      <div class="footer__brand">
        <a href="/" class="footer__logo">${esc(S.logo_text)}<span style="color:#00C896;">${esc(S.logo_accent)}</span></a>
        <p class="footer__desc">${esc(S.footer_desc)}</p>
        <div class="footer__social">
          <a href="${esc(S.social_linkedin)}" class="footer__social-icon" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading">Company</h4>
        <div class="footer__links">
          <a href="/holding">About Us</a>
          <a href="/business-units">Business Units</a>
          <a href="/industries">Industries</a>
          <a href="/resources">Resources</a>
          <a href="/contact">Contact</a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading">Products</h4>
        <div class="footer__links">
          <a href="/technology/wontia-crm">WONTIA CRM</a>
          <a href="/technology/macroponder">MACROPONDER</a>
          <a href="/technology/ia-annotation-manager">IA Annotation Manager</a>
          <a href="/technology">All Products</a>
        </div>
      </div>
      <div>
        <h4 class="footer__heading">Contact</h4>
        <div class="footer__links">
          <span style="color:#fff;font-weight:600;font-size:.8125rem;">USA</span>
          <span style="font-size:.8125rem;color:#94A3B8;line-height:1.5;">${esc(S.contact_usa_address)}</span>
          <span style="font-size:.8125rem;color:#94A3B8;">${esc(S.contact_usa_phone)}</span>
          <span style="color:#fff;font-weight:600;font-size:.8125rem;margin-top:.5rem;">Colombia</span>
          <span style="font-size:.8125rem;color:#94A3B8;line-height:1.5;">${esc(S.contact_col_address)}</span>
          <a href="mailto:${esc(S.contact_col_email)}" style="font-size:.8125rem;color:#00C896;">${esc(S.contact_col_email)}</a>
          <a href="https://wa.me/${S.contact_whatsapp.replace(/[+ ()-]/g,'')}" style="font-size:.8125rem;color:#00C896;">WhatsApp</a>
        </div>
      </div>
    </div>
    <div class="footer__bottom">
      <span>${esc(S.footer_copyright)}</span>
      <div class="footer__bottom-links">
        <a href="/privacy">Privacy Policy</a>
        <a href="/terms">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>`;
}

function pageHead(title, desc, url, extraMeta = '') {
  return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="theme-color" content="${S.color_dark}">
<meta name="color-scheme" content="light dark">
<title>${esc(title)}</title>
<meta name="description" content="${esc(desc)}">
<meta name="author" content="INTSOLCOM LLC">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<meta property="og:type" content="website">
<meta property="og:site_name" content="INTSOLCOM LLC">
<meta property="og:title" content="${esc(title)}">
<meta property="og:description" content="${esc(desc)}">
<meta property="og:url" content="${esc(url)}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="${esc(title)}">
<meta name="twitter:description" content="${esc(desc)}">
<link rel="canonical" href="${esc(url)}">
<link rel="stylesheet" href="/assets/css/main.css">
${extraMeta}
<style>
:root {
  --bg:#FFFFFF;--surface:#F8FAFC;--surface2:#E2E8F0;--dark:#0F172A;--mid:#475569;
  --light:#94A3B8;--accent:#00C896;--accent-dk:#00A67D;--accent-bg:rgba(0,200,150,0.07);
  --accent-brd:rgba(0,200,150,0.18);--secondary:#2563EB;--purple:#8B5CF6;--white:#FFFFFF;
  --font-display:'Inter',sans-serif;--font-body:'Inter',sans-serif;
}
.nav.scrolled { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); box-shadow: 0 1px 2px rgba(15,23,42,.04); padding: .75rem 0; }
.nav.scrolled .nav__logo { color: #0F172A; }
.nav.scrolled .nav__link { color: #475569; }
.nav.scrolled .nav__hamburger span { background: #0F172A; }
.nav-mobile.open { opacity: 1; pointer-events: auto; }
.nav-toggle.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.nav-toggle.open span:nth-child(2) { opacity: 0; }
.nav-toggle.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.cursor-ring.hov { transform: translate(-50%,-50%) scale(1.6); border-color: rgba(0,200,150,.4); background: rgba(0,200,150,.06); }
.video-bg { position: absolute; inset: 0; overflow: hidden; z-index: 0; }
.video-bg iframe { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); min-width: 100%; min-height: 100%; width: auto; height: auto; pointer-events: none; border: none; }
.video-overlay { position: absolute; inset: 0; z-index: 1; background: radial-gradient(ellipse at 50% 0%, rgba(0,200,150,0.08) 0%, transparent 60%), linear-gradient(180deg, rgba(15,23,42,0.6) 0%, rgba(15,23,42,0.85) 50%, rgba(15,23,42,0.95) 100%); }
.eco-diagram { position: relative; padding: 2rem 0; }
.eco-top { display: flex; justify-content: center; margin-bottom: 1rem; }
.eco-top-card { background: #0F172A; color: #fff; border: 2px solid #00C896; padding: 1.25rem 2.5rem; border-radius: 16px; font-weight: 700; font-size: 1.125rem; text-align: center; box-shadow: 0 8px 32px rgba(0,200,150,0.15); }
.eco-connectors { display: flex; justify-content: center; gap: 3rem; position: relative; margin-bottom: 1rem; flex-wrap: wrap; }
.eco-vline { width: 2px; height: 40px; background: rgba(0,200,150,0.3); }
.eco-branches { display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap; }
.eco-branch { display: flex; flex-direction: column; align-items: center; gap: .75rem; flex: 1; min-width: 200px; max-width: 280px; }
.eco-branch-line { width: 2px; height: 30px; background: rgba(0,200,150,0.3); }
.eco-card { opacity: 0; transform: translateY(20px); transition: opacity .5s ease, transform .5s ease; }
.eco-card.visible { opacity: 1; transform: translateY(0); }
.eco-card-capabilities { display: flex; flex-wrap: wrap; gap: .35rem; margin-top: .75rem; justify-content: center; }
.eco-card-cap { font-size: .68rem; background: rgba(0,200,150,0.08); color: #00C896; padding: .2rem .55rem; border-radius: 20px; white-space: nowrap; }
.eco-label-sub { font-size: .78rem; color: #94A3B8; margin-top: .25rem; }
.eco-card-tag { font-size: .68rem; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: #00C896; margin-bottom: .5rem; }
body.no-scroll { overflow: hidden; }
#scroll-top { position: fixed; bottom: 2rem; right: 2rem; width: 44px; height: 44px; border-radius: 50%; background: #0F172A; color: #fff; font-size: 1.25rem; border: none; cursor: pointer; z-index: 300; opacity: 0; visibility: hidden; transition: all .3s ease; box-shadow: 0 4px 16px rgba(0,0,0,.15); display: flex; align-items: center; justify-content: center; }
#scroll-top.visible { opacity: 1; visibility: visible; }
#scroll-top:hover { background: #00C896; color: #0F172A; transform: translateY(-2px); }
.page-hero { padding: 8rem 0 5rem; background: #0F172A; color: #fff; text-align: center; position: relative; overflow: hidden; }
.page-hero::before { content:''; position:absolute; top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at 30% 50%, rgba(0,200,150,.08) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(139,92,246,.06) 0%, transparent 50%);animation:heroPulse 8s ease-in-out infinite; }
@keyframes heroPulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }
</style>
</head>
<body>
<div class="cursor-dot"></div>
<div class="cursor-ring"></div>`;
}

function pageEnd(currentPath) {
  const ws = S.contact_whatsapp.replace(/[+ ()-]/g,'');
  return `
${footerHTML()}
<button id="scroll-top" aria-label="Scroll to top">&uarr;</button>
<script>
window.MBPO_VIDEO={mute:1,autoplay:1,loop:1,controls:0,rel:0,modestbranding:1,showinfo:0,iv_load_policy:3,disablekb:1,playsinline:1,speed:1,layout:'cover',voffset:0};
window.MBPO_FX={revealThreshold:0.08,counterDuration:1800,parallaxSpeed:0.15};
</script>
<script src="/assets/js/main.js"></script>
<script>
function toggleFaq(btn){
  var c=btn.nextElementSibling,i=btn.querySelector('.faq__icon');
  if(!c)return;
  if(c.style.display==='block'||c.classList.contains('open')){
    c.style.display='none';c.classList.remove('open');btn.classList.remove('active');
    btn.parentElement&&btn.parentElement.classList.remove('active');
    btn.setAttribute('aria-expanded','false');
    if(i){i.style.transform='';i.textContent='+';}
  }else{
    c.style.display='block';c.classList.add('open');btn.classList.add('active');
    btn.parentElement&&btn.parentElement.classList.add('active');
    btn.setAttribute('aria-expanded','true');
    if(i){i.style.transform='rotate(45deg)';i.textContent='\\u00D7';}
  }
}
document.querySelectorAll('.faq__question').forEach(function(b){b.addEventListener('click',function(){toggleFaq(this);});});
</script>
<div class="chat-widget" style="position:fixed;bottom:2rem;left:2rem;z-index:300;">
  <a href="https://wa.me/${ws}" target="_blank" rel="noopener" style="display:flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:#25D366;color:#fff;box-shadow:0 4px 20px rgba(37,211,102,.3);text-decoration:none;font-size:1.5rem;" aria-label="WhatsApp">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </a>
</div>
</body>
</html>`;
}

function navBar(currentPath) {
  const {links,mobile} = navHTML(currentPath);
  return `
<nav class="nav nav--transparent" id="nav">
  <div class="container">
    <a href="/" class="nav__logo">${esc(S.logo_text)}<span style="color:${S.color_accent}">${esc(S.logo_accent)}</span></a>
    <div class="nav__links">${links}</div>
    <div class="nav__hamburger nav-toggle"><span></span><span></span><span></span></div>
  </div>
  <div class="nav__mobile nav-mobile"><div class="nav__mobile-links">${mobile}</div></div>
</nav>`;
}

// ============================================================
// PAGE RENDERERS
// ============================================================

function buildCapabilities() {
  const names = ['Nearshore Teams','AI & Automation','Business Intelligence','Data Annotation','Sales Operations','Customer Operations','Executive Support','Recruiting'];
  const icons = ['\u{1F310}','\u{1F916}','\u{1F4C8}','\u{1F3F7}\uFE0F','\u{1F4BC}','\u{1F4DE}','\u{1F4CB}','\u{1F50D}'];
  const descs = [
    'Full-stack engineering and operations teams in Colombia, time-zone aligned with North America.',
    'Custom AI solutions including LLM integration, computer vision, and business process automation.',
    'Data warehousing, analytics dashboards, and predictive modeling for strategic decisions.',
    'Multi-modal data labeling at scale: images, video, text, audio, and 3D point clouds.',
    'CRM management, lead qualification, pipeline analytics powered by WONTIA CRM.',
    'Bilingual customer support, ticket management, NPS tracking, and multi-channel service desks.',
    'Dedicated virtual assistants for calendar management, research, and executive communications.',
    'End-to-end talent acquisition: sourcing, screening, assessments, and onboarding.',
  ];
  let html = '';
  for (let i = 0; i < names.length; i++) {
    html += '<div class="capability-card reveal" data-delay="' + (i*80) + '">';
    html += '<div class="capability-card__icon">' + icons[i] + '</div>';
    html += '<div class="capability-card__content"><h4>' + esc(names[i]) + '</h4><p>' + esc(descs[i]) + '</p></div></div>';
  }
  return html;
}

function buildUnitsDiagram() {
  const units = [
    {name:'INTSOLCOM SAS',desc:'Operational delivery hub in Barranquilla, Colombia. Nearshore BPO, AI annotation, QA, and talent solutions.',icon:'\u{1F1E8}\u{1F1F4}',tag:'Operational Delivery',caps:'BPO,AI Annotation,QA,Talent'},
    {name:'Technology & Products',desc:'WONTIA CRM, MACROPONDER, and IA Annotation Manager \u2014 owned and operated software platforms.',icon:'\u2699\uFE0F',tag:'Product Division',caps:'WONTIA CRM,MACROPONDER,Annotation Manager'},
    {name:'Business Development \u2014 USA',desc:'Strategic commercial presence in the United States \u2014 client relationships, partnerships, and market expansion.',icon:'\u{1F1FA}\u{1F1F8}',tag:'Commercial & Strategy',caps:'Sales,Partnerships,Strategy'},
  ];
  let html = '';
  for (let i = 0; i < units.length; i++) {
    const u = units[i];
    const caps = u.caps.split(',').map(function(c) { return '<span class="eco-card-cap">' + esc(c.trim()) + '</span>'; }).join('');
    html += '<div class="eco-branch"><div class="eco-branch-line"></div>';
    html += '<div class="eco-card reveal" data-delay="' + (200+i*100) + '">';
    html += '<div class="eco-card-tag">' + esc(u.tag) + '</div>';
    html += '<div class="card__icon" style="font-size:1.5rem;margin:0 auto .75rem;">' + u.icon + '</div>';
    html += '<h3>' + esc(u.name) + '</h3>';
    html += '<p style="font-size:.875rem;color:#475569;">' + esc(u.desc) + '</p>';
    html += '<div class="eco-card-capabilities">' + caps + '</div>';
    html += '</div></div>';
  }
  return html;
}

function buildProductsGrid() {
  const prods = [
    {icon:'\u{1F465}',cat:'CRM',name:'WONTIA CRM',desc:'Intelligent CRM platform for service-based businesses. Contact management, pipeline tracking, and AI-powered insights.',slug:'wontia-crm',grad:''},
    {icon:'\u{1F9E0}',cat:'AI Platform',name:'MACROPONDER',desc:'Decision intelligence platform. Scenario modeling, bias detection, and collaborative strategic analysis powered by AI.',slug:'macroponder',grad:'--purple'},
    {icon:'\u{1F3F7}\uFE0F',cat:'AI Platform',name:'IA Annotation Manager',desc:'End-to-end annotation management platform. Project management, quality control, and workforce analytics at scale.',slug:'ia-annotation-manager',grad:'--blue'},
  ];
  let html = '';
  for (let i = 0; i < prods.length; i++) {
    const p = prods[i];
    html += '<div class="product-card reveal" data-delay="' + (i*100) + '">';
    html += '<div class="product-card__gradient' + (p.grad ? ' product-card__gradient' + p.grad : '') + '"></div>';
    html += '<div class="product-card__header"><div class="card__icon" style="font-size:1.5rem;">' + p.icon + '</div>';
    html += '<span class="product-card__tag">' + esc(p.cat) + '</span><h3>' + esc(p.name) + '</h3>';
    html += '<p>' + esc(p.desc) + '</p>';
    html += '<a href="/technology/' + esc(p.slug) + '" style="color:#00C896;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;margin-top:.5rem;">Explore \u2192</a>';
    html += '</div></div>';
  }
  return html;
}

function buildTestimonials() {
  const testimonials = [
    {name:'Marcus D.',role:'CTO',company:'HealthTech Innovations',content:'INTSOLCOM built our entire AI annotation pipeline in 14 days. The quality control processes and workforce management alone saved us 6 months of internal development. Truly a technology partner, not just a vendor.',rating:5},
    {name:'Elena R.',role:'VP Operations',company:'Meridian Financial',content:'We moved our entire customer operations to INTSOLCOM SAS and saw a 60% cost reduction while improving CSAT scores by 12 points. Their WONTIA CRM platform gave us visibility we never had before.',rating:5},
    {name:'David K.',role:'Founder',company:'Stack AI Labs',content:'As a startup, we needed a partner who could scale with us. INTSOLCOM provided nearshore engineering teams that felt like our own employees. The ecosystem approach \u2014 technology plus operations \u2014 is the real differentiator.',rating:5},
  ];
  const colors = ['#00C896','#8B5CF6','#2563EB'];
  let html = '';
  for (let i = 0; i < testimonials.length; i++) {
    const t = testimonials[i];
    html += '<div class="testimonial-card reveal" data-delay="' + (i*100) + '">';
    html += '<div class="testimonial-card__stars">';
    for (let s = 0; s < 5; s++) html += s < t.rating ? '\u2605' : '\u2606';
    html += '</div>';
    html += '<p class="testimonial-card__quote">"' + esc(t.content) + '"</p>';
    html += '<div class="testimonial-card__author">';
    html += '<div class="testimonial-card__avatar" style="background:' + colors[i] + ';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;">' + esc(t.name.charAt(0).toUpperCase()) + '</div>';
    html += '<div><div class="testimonial-card__name">' + esc(t.name) + '</div><div class="testimonial-card__role">' + esc(t.role) + ', ' + esc(t.company) + '</div></div>';
    html += '</div></div>';
  }
  return html;
}

function buildFAQs() {
  const faqs = [
    ['What is INTSOLCOM?','The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia. We own and operate software platforms and business services. Unlike traditional outsourcing firms, we build proprietary technology and integrate it with operational excellence to deliver superior outcomes for our clients.'],
    ['Where are you located?','Our strategic operations are managed from the United States, and our primary delivery hub \u2014 INTSOLCOM SAS \u2014 is located in Barranquilla, Colombia. This dual presence gives us U.S. business development and governance with nearshore delivery capabilities in the EST time zone.'],
    ['What makes you different from BPO companies?','We are a business ecosystem, not a BPO. The key difference: we own the technology we deploy. From WONTIA CRM to the IA Annotation Manager, we build and continuously improve our own platforms. This means clients benefit from technology-driven efficiency, not just labor arbitrage.'],
    ['What industries do you serve?','We serve clients across Healthcare, Technology, Financial Services, AI & Data, Retail, Logistics, Real Estate, Professional Services, Manufacturing, and Hospitality.'],
    ['How do I partner with INTSOLCOM?','Fill out our contact form or reach out via WhatsApp. We will schedule a 30-minute discovery call to understand your needs and prepare a tailored proposal.'],
  ];
  let html = '';
  for (let i = 0; i < faqs.length; i++) {
    const q = faqs[i][0], a = faqs[i][1];
    html += '<div class="faq__item reveal" data-delay="' + (i*60) + '">';
    html += '<button class="faq__question" onclick="toggleFaq(this)" aria-expanded="false">';
    html += '<span>' + esc(q) + '</span><span class="faq__icon">+</span></button>';
    html += '<div class="faq__answer" style="display:none;"><div class="faq__answer-inner">' + esc(a) + '</div></div></div>';
  }
  return html;
}

function renderHome() {
  const title = `${S.site_name} \u2014 ${S.site_tagline} | AI | Business Operations | Software Products`;
  const desc  = S.site_desc;
  const url   = 'https://intsolcom.com';
  const cp    = '/';

  return pageHead(title, desc, url) + navBar(cp) + `
<section class="section hero" id="hero">
  <div class="hero__grid"></div>
  <div class="video-overlay"></div>
  <div class="container relative">
    <div class="hero__content">
      <div class="hero__badge reveal"><span class="hero__badge-dot"></span>Technology & Operations Ecosystem</div>
      <h1 class="reveal" data-delay="100">We build and operate <em>technology companies.</em></h1>
      <p class="hero__description reveal" data-delay="200">The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia. We build technology products and operate business services.</p>
      <div class="hero__actions reveal" data-delay="300">
        <a href="/holding" class="btn btn-accent btn-lg">Explore Our Ecosystem \u2192</a>
        <a href="/technology" class="btn btn-outline-white btn-lg">Meet Our Products</a>
      </div>
      <div class="hero__metrics reveal" data-delay="400">
        <div class="hero__metric"><div class="hero__metric-value">55%</div><div class="hero__metric-label">Cost Reduction</div></div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric"><div class="hero__metric-value">14 days</div><div class="hero__metric-label">Deployment</div></div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric"><div class="hero__metric-value">98%</div><div class="hero__metric-label">Client Retention</div></div>
        <div class="hero__metric-divider"></div>
        <div class="hero__metric"><div class="hero__metric-value">300+</div><div class="hero__metric-label">Professionals</div></div>
      </div>
      <div class="hero__trust reveal" data-delay="500">
        <span class="hero__trust-text">Trusted by innovative companies worldwide</span>
      </div>
    </div>
  </div>
  <div class="gradient-blob gradient-blob--1 gradient-blob--dark"></div>
  <div class="gradient-blob gradient-blob--3 gradient-blob--dark" style="top:60%;right:5%"></div>
</section>

<section class="section section-surface" id="ecosystem">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Business Ecosystem</span>
      <h2 class="section-title">The Intsolcom Ecosystem</h2>
      <p class="section-subtitle">Two entities, one ecosystem \u2014 combining U.S. strategic presence with Colombian operational delivery.</p>
    </div>
    <div class="eco-diagram">
      <div class="eco-top reveal">
        <div class="eco-top-card">The Intsolcom <span style="color:#00C896;">Ecosystem</span><div style="font-size:.72rem;font-weight:400;color:rgba(255,255,255,.6);margin-top:.2rem;">United States &amp; Colombia</div></div>
      </div>
      <div class="eco-connectors reveal" data-delay="100"><div class="eco-vline"></div><div class="eco-vline"></div><div class="eco-vline"></div></div>
      <div class="eco-branches">
        ${buildUnitsDiagram()}
      </div>
    </div>
  </div>
</section>

<section class="section section-dark" id="stats">
  <div class="container">
    <div class="stats-band">
      <div class="stats-band__item reveal"><div class="stats-band__value stats-band__value--accent"><span data-count="65" data-suffix="%"></span></div><div class="stats-band__label">Average Cost Savings</div></div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="100"><div class="stats-band__value"><span data-count="14"></span><span class="stats-band__suffix">d</span></div><div class="stats-band__label">Deployment Time</div></div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="200"><div class="stats-band__value stats-band__value--accent"><span data-count="98.4" data-decimals="1" data-suffix="%"></span></div><div class="stats-band__label">Annotation Accuracy</div></div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="300"><div class="stats-band__value"><span data-count="500" data-suffix="+"></span></div><div class="stats-band__label">Projects Delivered</div></div>
      <div class="stats-band__divider"></div>
      <div class="stats-band__item reveal" data-delay="400"><div class="stats-band__value stats-band__value--accent"><span data-count="300" data-suffix="+"></span></div><div class="stats-band__label">Professionals Worldwide</div></div>
    </div>
  </div>
</section>

<section class="section" id="products">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Software Products</span>
      <h2 class="section-title">Platforms we own and operate</h2>
      <p class="section-subtitle">Purpose-built software products developed in-house, continuously improved, and deployed at enterprise scale.</p>
    </div>
    <div class="grid-3">${buildProductsGrid()}</div>
    <div class="text-center mt-16 reveal"><a href="/technology" class="btn btn-outline btn-lg">View All Products \u2192</a></div>
  </div>
  <div class="gradient-blob gradient-blob--2"></div>
</section>

<section class="section section-surface" id="capabilities">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Capabilities</span>
      <h2 class="section-title">Technology-enabled operations at scale</h2>
      <p class="section-subtitle">Our ecosystem combines human expertise with proprietary technology to deliver results across functions.</p>
    </div>
    <div class="grid-4">${buildCapabilities()}</div>
  </div>
</section>

<section class="section section-surface" id="comparison">
  <div class="container">
    <div class="section-header reveal">
      <span class="section-label">Why Intsolcom</span>
      <h2 class="section-title">The ecosystem advantage</h2>
      <p class="section-subtitle">The Intsolcom business ecosystem delivers fundamentally different outcomes than traditional outsourcing providers.</p>
    </div>
    <div class="comparison">
      <div class="comparison__col comparison__col--traditional reveal-left">
        <div class="comparison__header"><div class="comparison__header-icon">\u274C</div><h3>Traditional Outsourcing</h3></div>
        <div class="comparison__list">${['Transactional vendor relationships','Siloed teams with no integration','Manual, repetitive processes','Generic, one-size-fits-all approach','Opaque operations and reporting','Limited technology capabilities'].map(s=>`<div class="comparison__item">${esc(s)}</div>`).join('')}</div>
      </div>
      <div class="comparison__col comparison__col--intsol reveal-right">
        <div class="comparison__header"><div class="comparison__header-icon">\u2713</div>      <h3>The Intsolcom Ecosystem</h3></div>
        <div class="comparison__list">${['Strategic partnership model','Integrated ecosystem approach','AI-enabled, automated workflows','Tailored solutions per client','Transparent, real-time dashboards','Proprietary technology stack'].map(s=>`<div class="comparison__item">${esc(s)}</div>`).join('')}</div>
      </div>
    </div>
    <div class="text-center mt-12 reveal"><a href="/holding" class="btn btn-accent btn-lg">See the Difference \u2192</a></div>
  </div>
</section>

<section class="cta-section" id="cta">
  <div class="cta-section__glow"></div><div class="cta-section__glow cta-section__glow--right"></div>
  <div class="container-sm reveal">
    <h2>Ready to work with the Intsolcom ecosystem?</h2>
    <p>Let's discuss how INTSOLCOM can accelerate your growth through technology and operational excellence.</p>
    <div class="cta-section__actions">
      <a href="/contact" class="btn btn-accent btn-lg">Start a Conversation \u2192</a>
      <a href="/technology" class="btn btn-outline-white btn-lg">Explore Products</a>
    </div>
    <p style="margin-top:1.5rem;font-size:.8125rem;color:rgba(255,255,255,.35);">No commitment. Strategic consultation.</p>
  </div>
</section>

<section class="section" id="testimonials">
  <div class="container">
    <div class="section-header reveal"><span class="section-label section-label--purple">Client Results</span><h2 class="section-title">What our partners say</h2></div>
    <div class="grid-3">${buildTestimonials()}</div>
  </div>
</section>

<section class="section section-surface" id="faq">
  <div class="container">
    <div class="section-header reveal"><span class="section-label">FAQ</span><h2 class="section-title">Frequently asked questions</h2></div>
    <div class="faq">${buildFAQs()}</div>
  </div>
</section>
` + pageEnd(cp);
}

function renderHolding() {
  return pageHead('Ecosystem \u2014 INTSOLCOM','The Intsolcom business ecosystem combines strategic presence in the United States with specialized operational delivery capabilities in Colombia.','https://intsolcom.com/holding') + navBar('/holding') + `
<section class="page-hero"><div class="container"><h1 style="font-size:clamp(2.2rem,5vw,3.5rem);font-weight:800;position:relative;z-index:1;">The <em style="font-style:normal;color:#00C896;">Intsolcom</em> Business Ecosystem</h1><p style="font-size:1.15rem;color:rgba(255,255,255,.55);max-width:600px;margin:1rem auto 0;position:relative;z-index:1;">Two entities, one ecosystem. Strategic business development in the United States. Operational delivery in Colombia.</p></div></section>
<section class="section"><div class="container"><div class="grid-2">
  <div><div class="section-label">Mission</div><h2 class="section-title">Build technology products and operate business services that transform how companies work \u2014 combining strategic presence in the United States with operational excellence in Colombia.</h2><p class="section-subtitle">We integrate proprietary technology platforms with operational delivery capabilities to create a unified business ecosystem. Our U.S. presence manages client relationships and strategy, while our Colombian operations deliver world-class execution.</p></div>
  <div><div class="section-label">Vision</div><h2 class="section-title">Be the leading business ecosystem bridging U.S. strategic capabilities with Colombian operational excellence.</h2><p class="section-subtitle">Our dual-entity model \u2014 strategic business development in the United States and operational delivery in Colombia \u2014 gives clients the best of both worlds: American business acumen with nearshore efficiency and quality.</p></div>
</div></div></section>
<section class="section section-dark"><div class="container"><div class="section-header reveal"><span class="section-label">Business Model</span><h2 class="section-title" style="color:#fff;">Two Pillars, One Ecosystem</h2><p style="color:rgba(255,255,255,.5);max-width:600px;margin:0 auto;font-size:.95rem;">The Intsolcom business ecosystem is built on two foundational pillars that work together seamlessly.</p></div><div class="grid-2" style="margin-top:2rem;gap:1.5rem;">
  <div class="card" style="background:rgba(255,255,255,.05);color:#fff;padding:2rem;">
    <div style="font-size:2rem;margin-bottom:1rem;">\u{1F4BC}</div>
    <h3 style="color:#00C896;font-size:1.25rem;margin-bottom:.75rem;">Business Operations</h3>
    <p style="color:rgba(255,255,255,.6);margin-bottom:1.25rem;font-size:.9rem;">Strategic and operational business services managed across two entities in the Intsolcom ecosystem.</p>
    <div style="display:flex;flex-direction:column;gap:.75rem;">
      <div style="background:rgba(0,200,150,.08);padding:1rem;border-radius:8px;">
        <div style="font-weight:600;font-size:.85rem;color:#00C896;margin-bottom:.25rem;">\u{1F1FA}\u{1F1F8} Strategic Operations \u2014 USA</div>
        <p style="font-size:.8rem;color:rgba(255,255,255,.5);">Commercial strategy, client partnerships, business development, and market expansion from the United States.</p>
      </div>
      <div style="background:rgba(0,200,150,.08);padding:1rem;border-radius:8px;">
        <div style="font-weight:600;font-size:.85rem;color:#00C896;margin-bottom:.25rem;">\u{1F1E8}\u{1F1F4} Delivery Operations \u2014 Colombia</div>
        <p style="font-size:.8rem;color:rgba(255,255,255,.5);">Nearshore BPO, AI annotation, QA, and talent solutions delivered from Barranquilla, Colombia.</p>
      </div>
    </div>
  </div>
  <div class="card" style="background:rgba(255,255,255,.05);color:#fff;padding:2rem;">
    <div style="font-size:2rem;margin-bottom:1rem;">\u2699\uFE0F</div>
    <h3 style="color:#8B5CF6;font-size:1.25rem;margin-bottom:.75rem;">Technology & Products</h3>
    <p style="color:rgba(255,255,255,.6);margin-bottom:1.25rem;font-size:.9rem;">Proprietary software platforms built, owned, and operated by the Intsolcom ecosystem.</p>
    <div style="background:rgba(139,92,246,.08);padding:1rem;border-radius:8px;">
      <div style="font-weight:600;font-size:.85rem;color:#8B5CF6;margin-bottom:.25rem;">\u{1F4E6} Product Portfolio</div>
      <p style="font-size:.8rem;color:rgba(255,255,255,.5);">WONTIA CRM, MACROPONDER, and IA Annotation Manager \u2014 continuously developed and deployed at enterprise scale.</p>
    </div>
  </div>
</div></div></section>
<section class="cta-section"><div class="cta-section__glow"></div><div class="container-sm reveal"><h2>Ready to work with the Intsolcom ecosystem?</h2><p>Let's discuss how the Intsolcom ecosystem can accelerate your growth through technology products and operational capabilities across the United States and Colombia.</p><div class="cta-section__actions"><a href="/contact" class="btn btn-accent btn-lg">Start a Conversation \u2192</a></div></div></section>
` + pageEnd('/holding');
}

function renderTechnology() {
  let cards = '';
  const cats = {CRM:['#00C896','rgba(0,200,150,.08)'],'AI Platform':['#8B5CF6','rgba(139,92,246,.08)']};
  for (const p of PRODUCTS) {
    const [cColor,cBg] = cats[p.category]||['#00C896','rgba(0,200,150,.08)'];
    cards += `
    <div class="product-card reveal">
      <div class="product-card__header">
        <div class="card__icon" style="font-size:2rem;">${p.icon}</div>
        <span class="product-card__tag" style="background:${cBg};color:${cColor};">${esc(p.category)}</span>
        <h3>${esc(p.name)}</h3>
        <p>${esc(p.short_desc)}</p>
        <a href="/technology/${esc(p.slug)}" class="btn btn-outline btn-lg" style="margin-top:.75rem;">Explore Product \u2192</a>
      </div>
    </div>`;
  }
  return pageHead('Technology Portfolio \u2014 INTSOLCOM','Software platforms and AI products built for enterprise. Explore WONTIA CRM, MACROPONDER decision intelligence, and IA Annotation Manager.','https://intsolcom.com/technology') + navBar('/technology') + `
<section class="page-hero"><div class="container"><h1 style="font-size:clamp(2.2rem,5vw,3.5rem);font-weight:800;position:relative;z-index:1;">Technology <em style="font-style:normal;color:#00C896;">Portfolio</em></h1><p style="font-size:1.15rem;color:rgba(255,255,255,.55);max-width:600px;margin:1rem auto 0;position:relative;z-index:1;">Software platforms and AI products built for enterprise.</p></div></section>
<section class="section"><div class="container"><div class="grid-3">${cards}</div></div></section>
<section class="section section-surface"><div class="container"><div class="section-header reveal"><span class="section-label">Future Products</span><h2 class="section-title">The architecture allows <em>unlimited expansion</em></h2><p class="section-subtitle">Our technology ecosystem is designed for growth. New products are continuously developed, acquired, and integrated into the INTSOLCOM portfolio.</p></div></div></section>
` + pageEnd('/technology');
}

function renderContact() {
  const ws = S.contact_whatsapp.replace(/[+ ()-]/g,'');
  return pageHead('Contact \u2014 INTSOLCOM','Partner with the Intsolcom ecosystem. Contact INTSOLCOM LLC (USA) or INTSOLCOM SAS (Colombia).','https://intsolcom.com/contact') + navBar('/contact') + `
<section class="page-hero"><div class="container"><h1 style="font-size:clamp(2.2rem,5vw,3.5rem);font-weight:800;position:relative;z-index:1;">Let's <em style="font-style:normal;color:#00C896;">talk</em></h1><p style="font-size:1.15rem;color:rgba(255,255,255,.55);max-width:600px;margin:1rem auto 0;position:relative;z-index:1;">Partner with the Intsolcom ecosystem.</p></div></section>
<section class="section"><div class="container"><div class="grid-2">
  <div>
    <form method="post" action="/contact" style="display:flex;flex-direction:column;gap:1rem;">
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Name *</label><input type="text" name="name" required placeholder="Your full name" style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Email *</label><input type="email" name="email" required placeholder="you@company.com" style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Company</label><input type="text" name="company" placeholder="Company name" style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Phone</label><input type="tel" name="phone" placeholder="+1 555 0000" style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Country</label><input type="text" name="country" placeholder="United States" style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Interest *</label><select name="service_interest" required style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;"><option value="">Select...</option><option>Software Products</option><option>Nearshore Teams</option><option>AI & Automation</option><option>Data Annotation</option><option>Business Operations</option><option>Strategic Partnership</option><option>Other</option></select></div>
      <div><label style="display:block;font-size:.8rem;color:#475569;margin-bottom:4px;">Message *</label><textarea name="message" rows="5" required placeholder="Tell us about your project..." style="width:100%;padding:12px 16px;border:1px solid #E2E8F0;border-radius:8px;font-family:inherit;font-size:.95rem;resize:vertical;"></textarea></div>
      <button type="submit" class="btn btn-accent btn-lg" style="width:100%;justify-content:center;">Send Message \u2192</button>
    </form>
  </div>
  <div>
    <div class="card" style="margin-bottom:1.5rem;"><h3 style="color:#0F172A;margin-bottom:.5rem;">\u{1F1FA}\u{1F1F8} United States</h3><p style="color:#475569;font-size:.9rem;margin-bottom:.25rem;"><strong>INTSOLCOM LLC</strong></p><p style="color:#475569;font-size:.875rem;margin-bottom:.25rem;">${esc(S.contact_usa_address)}</p><p style="color:#475569;font-size:.875rem;">${esc(S.contact_usa_phone)}</p></div>
    <div class="card" style="margin-bottom:1.5rem;"><h3 style="color:#0F172A;margin-bottom:.5rem;">\u{1F1E8}\u{1F1F4} Colombia</h3><p style="color:#475569;font-size:.9rem;margin-bottom:.25rem;"><strong>INTSOLCOM SAS</strong></p><p style="color:#475569;font-size:.875rem;margin-bottom:.25rem;">${esc(S.contact_col_address)}</p><p style="color:#475569;font-size:.875rem;">${esc(S.contact_col_email)}</p></div>
    <div style="display:flex;flex-direction:column;gap:.75rem;">
      <a href="tel:${esc(S.contact_usa_phone)}" class="btn btn-outline btn-lg" style="justify-content:center;">\u{1F4DE} Call USA Office</a>
      <a href="https://wa.me/${ws}" target="_blank" class="btn btn-accent btn-lg" style="justify-content:center;">\u{1F4AC} WhatsApp</a>
      <a href="mailto:${esc(S.contact_col_email)}" class="btn btn-outline btn-lg" style="justify-content:center;">\u2709\uFE0F Email Us</a>
    </div>
  </div>
</div></div></section>
` + pageEnd('/contact');
}

function render404(pathname) {
  return pageHead('404 \u2014 INTSOLCOM','Page not found','https://intsolcom.com'+pathname) + navBar(pathname) + `
<section class="section" style="min-height:60vh;display:flex;align-items:center;text-align:center;">
  <div class="container"><h1 style="font-size:clamp(3rem,8vw,6rem);font-weight:800;color:#0F172A;">404</h1><p style="font-size:1.2rem;color:#475569;margin:1rem 0 2rem;">Page not found.</p><a href="/" class="btn btn-accent btn-lg">Back to Home \u2192</a></div>
</section>` + pageEnd(pathname);
}

// ============================================================
// ROUTER
// ============================================================
const ROUTES = {
  '/':                  renderHome,
  '/index.html':        renderHome,
  '/home':              renderHome,
  '/holding':           renderHolding,
  '/technology':        renderTechnology,
  '/contact':           renderContact,
};

// ============================================================
// HTTP SERVER
// ============================================================
const server = http.createServer((req, res) => {
  const url = new URL(req.url, 'http://localhost');
  let pathname = url.pathname.replace(/\/+$/, '') || '/';

  // Asset serving
  if (pathname.startsWith('/assets/')) {
    const asset = ASSETS[pathname];
    if (asset) {
      const ext = path.extname(pathname).toLowerCase();
      const mime = MIME[ext] || 'application/octet-stream';
      const enc = req.headers['accept-encoding'] || '';
      const useGzip = enc.includes('gzip');
      const tag = req.headers['if-none-match'];

      if (tag === asset.etag) {
        res.writeHead(304, { 'ETag': asset.etag, 'Cache-Control': 'public, max-age=3600' });
        return res.end();
      }

      const headers = {
        'Content-Type': mime,
        'ETag': asset.etag,
        'Cache-Control': 'public, max-age=3600',
        'Last-Modified': asset.mtime,
        'Access-Control-Allow-Origin': '*',
      };
      if (useGzip) {
        headers['Content-Encoding'] = 'gzip';
        headers['Vary'] = 'Accept-Encoding';
        res.writeHead(200, headers);
        return res.end(asset.gz);
      }
      headers['Content-Length'] = asset.buf.length;
      res.writeHead(200, headers);
      return res.end(asset.buf);
    }
    res.writeHead(404);
    return res.end('Not Found');
  }

  // Sitemap
  if (pathname === '/sitemap.xml') {
    const urls = ['','/holding','/technology','/business-units','/industries','/resources','/contact','/technology/wontia-crm','/technology/macroponder','/technology/ia-annotation-manager'];
    const xml = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' + urls.map(u => `  <url><loc>https://intsolcom.com${u}</loc><lastmod>2026-07-14</lastmod><changefreq>weekly</changefreq><priority>${u===''?'1.0':'0.8'}</priority></url>`).join('\n') + '\n</urlset>';
    res.writeHead(200, {'Content-Type':'application/xml; charset=utf-8','Cache-Control':'public, max-age=3600'});
    return res.end(xml);
  }

  // Dynamic routes: /technology/:slug, /business-units/:slug
  const techMatch = pathname.match(/^\/technology\/([^/]+)$/);
  if (techMatch) {
    const slug = techMatch[1];
    const prod = PRODUCTS.find(p => p.slug === slug);
    if (prod) {
      res.writeHead(200, {'Content-Type':'text/html; charset=utf-8'});
      return res.end(pageHead(`${esc(prod.name)} \u2014 INTSOLCOM Technology`,prod.short_desc,`https://intsolcom.com/technology/${slug}`) + navBar('/technology') + `
<section class="page-hero"><div class="container"><h1 style="font-size:clamp(2.2rem,5vw,3.5rem);font-weight:800;position:relative;z-index:1;">${esc(prod.name)}</h1><span style="display:inline-block;background:rgba(0,200,150,.12);color:#00C896;padding:4px 14px;border-radius:20px;font-size:.75rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:1rem;position:relative;z-index:1;">${esc(prod.category)}</span><p style="font-size:1.15rem;color:rgba(255,255,255,.55);max-width:600px;margin:1rem auto 0;position:relative;z-index:1;">${esc(prod.short_desc)}</p></div></section>
<section class="section"><div class="container"><div class="section-header reveal"><span class="section-label">Overview</span><h2 class="section-title">Built for <em>enterprise scale</em></h2></div><p style="max-width:720px;margin:0 auto;font-size:1.05rem;color:#475569;line-height:1.8;">${esc(prod.short_desc)} INTSOLCOM develops and maintains this platform as part of its technology portfolio, continuously investing in features, performance, and integrations.</p></div></section>
<section class="cta-section"><div class="cta-section__glow"></div><div class="container-sm reveal"><h2>Interested in ${esc(prod.name)}?</h2><p>Schedule a demo with our product team to see how it can transform your operations.</p><div class="cta-section__actions"><a href="/contact" class="btn btn-accent btn-lg">Request a Demo \u2192</a></div></div></section>
` + pageEnd('/technology'));
    }
  }

  // Page routes
  const renderer = ROUTES[pathname];
  if (renderer) {
    res.writeHead(200, {'Content-Type':'text/html; charset=utf-8'});
    return res.end(renderer());
  }

  // 404
  res.writeHead(404, {'Content-Type':'text/html; charset=utf-8'});
  res.end(render404(pathname));
});

// ============================================================
// START
// ============================================================
server.listen(PORT, () => {
  console.log('\n\u2554\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2557');
  console.log('\u2551  INTSOLCOM \u2014 Technology & Operations Ecosystem    \u2551');
  console.log('\u2551                                                    \u2551');
  console.log('\u2551  Local:  http://localhost:' + String(PORT).padEnd(26) + '\u2551');
  console.log('\u2551  Token:  ' + TOKEN.padEnd(26) + '\u2551');
  console.log('\u255a\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u2550\u255d');
  console.log('\n  Pages:');
  for (const [path] of Object.entries(ROUTES)) {
    console.log('    http://localhost:'+PORT+path);
  }
  console.log('\n  Press Ctrl+C to stop\n');
});
