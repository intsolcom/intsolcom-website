<?php
// ============================================================
// INTSOLCOM — Language Switcher (include in <nav>)
// ============================================================
$_curLang = currentLang();
?>
<div class="lang-switch" id="lang-switch">
  <button class="lang-btn <?= $_curLang==='en'?'active':'' ?>" data-lang="en" aria-label="Switch to English" title="English">
    <span class="lang-flag">🇺🇸</span>
    <span class="lang-label">EN</span>
  </button>
  <span class="lang-sep">|</span>
  <button class="lang-btn <?= $_curLang==='es'?'active':'' ?>" data-lang="es" aria-label="Cambiar a Español" title="Español">
    <span class="lang-flag">🇪🇸</span>
    <span class="lang-label">ES</span>
  </button>
</div>
<style>
.lang-switch{
  display:flex;align-items:center;gap:.35rem;
  font-size:.72rem;font-weight:600;letter-spacing:.03em;
  margin-left:.75rem;
  background:rgba(255,255,255,.07);
  border:1px solid rgba(255,255,255,.12);
  border-radius:9999px;
  padding:.2rem .35rem;
  backdrop-filter:blur(8px);
  -webkit-backdrop-filter:blur(8px);
  transition:background .25s,border-color .25s;
}
.nav.scrolled .lang-switch,
.nav--scrolled .lang-switch{
  background:rgba(15,23,42,.04);
  border-color:rgba(15,23,42,.1);
}
.lang-btn{
  background:none;border:none;cursor:pointer;
  display:flex;align-items:center;gap:.3rem;
  padding:.28rem .55rem;border-radius:9999px;
  transition:all .2s;
  font-family:var(--font-body),'Inter',sans-serif;font-size:inherit;font-weight:inherit;letter-spacing:inherit;
  color:rgba(255,255,255,.55);
}
.nav.scrolled .lang-btn,
.nav--scrolled .lang-btn{
  color:#64748B;
}
.lang-btn.active{
  color:#fff !important;
  background:rgba(0,200,150,.25);
  box-shadow:0 0 0 1px rgba(0,200,150,.3);
}
.nav.scrolled .lang-btn.active,
.nav--scrolled .lang-btn.active{
  color:#0F172A !important;
  background:rgba(0,200,150,.12);
  box-shadow:0 0 0 1px rgba(0,200,150,.2);
}
.lang-btn:hover{color:#fff;}
.nav.scrolled .lang-btn:hover,
.nav--scrolled .lang-btn:hover{color:#0F172A;}
.lang-flag{font-size:.95rem;line-height:1;}
.lang-label{font-size:.7rem;}
.lang-sep{color:rgba(255,255,255,.25);font-size:.65rem;user-select:none;}
.nav.scrolled .lang-sep,
.nav--scrolled .lang-sep{color:rgba(15,23,42,.2);}
.nav-mobile .lang-switch{
  margin:1rem 0 0;font-size:.9rem;justify-content:center;
  background:rgba(15,23,42,.04);
  border-color:rgba(15,23,42,.1);
}
.nav-mobile .lang-btn{color:#64748B;}
.nav-mobile .lang-btn.active{color:#0F172A !important;background:rgba(0,200,150,.15);}
.nav-mobile .lang-btn:hover{color:#0F172A;}
.nav-mobile .lang-sep{color:rgba(15,23,42,.25);}
@media (max-width:768px){
  .lang-switch{margin-left:0;font-size:.75rem;}
  .lang-label{font-size:.72rem;}
}
</style>
<script>
(function(){
  var btns = document.querySelectorAll('#lang-switch .lang-btn');
  btns.forEach(function(btn){
    btn.addEventListener('click', function(){
      var lang = btn.getAttribute('data-lang');
      var url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.location.href = url.toString();
    });
  });
})();
</script>
