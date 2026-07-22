<?php $_curLang = currentLang(); ?>
<span class="lang-toggle" id="lang-switch">
  <span class="lang-track">
    <a href="?lang=en" class="lang-opt<?= $_curLang==='en'?' active':'' ?>" data-lang="en" onclick="return switchLang('en',event)">EN</a>
    <a href="?lang=es" class="lang-opt<?= $_curLang==='es'?' active':'' ?>" data-lang="es" onclick="return switchLang('es',event)">ES</a>
    <span class="lang-thumb<?= $_curLang==='es'?' right':'' ?>"></span>
  </span>
</span>
<style>
.lang-toggle{display:inline-flex;align-items:center;margin-left:.75rem}
.lang-track{position:relative;display:flex;align-items:center;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:2px;gap:0;transition:all .3s}
.nav.scrolled .lang-track,.nav--scrolled .lang-track{background:rgba(15,23,42,.04);border-color:rgba(15,23,42,.1)}
.lang-opt{position:relative;z-index:1;display:inline-flex;align-items:center;justify-content:center;width:28px;height:22px;border-radius:999px;font-size:.62rem;font-weight:600;letter-spacing:.05em;color:rgba(255,255,255,.45);text-decoration:none;transition:all .3s cubic-bezier(.4,0,.2,1)}
.nav.scrolled .lang-opt,.nav--scrolled .lang-opt{color:rgba(15,23,42,.4)}
.lang-opt:hover{color:rgba(255,255,255,.8)}
.nav.scrolled .lang-opt:hover,.nav--scrolled .lang-opt:hover{color:rgba(15,23,42,.7)}
.lang-opt.active{color:#fff;font-weight:700;font-size:.68rem;width:32px;height:24px}
.nav.scrolled .lang-opt.active,.nav--scrolled .lang-opt.active{color:#0F172A}
.lang-thumb{position:absolute;z-index:0;top:2px;left:2px;width:28px;height:22px;border-radius:999px;background:var(--accent,#00C896);transition:all .3s cubic-bezier(.4,0,.2,1);box-shadow:0 1px 3px rgba(0,200,150,.3)}
.lang-thumb.right{left:30px;width:32px;height:24px}
@media(max-width:768px){.lang-toggle{margin-left:.25rem}}
</style>
<script>
function switchLang(lang,e){e.preventDefault();document.cookie='intsolcom_lang='+lang+';path=/;max-age=31536000;SameSite=Lax';var u=new URL(window.location.href);u.searchParams.set('lang',lang);window.location.href=u.toString();return false}
</script>
