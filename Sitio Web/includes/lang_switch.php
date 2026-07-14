<?php
// ============================================================
// INTSOLCOM — Language Switcher (include in <nav>)
// ============================================================
$_curLang = currentLang();
?>
<div class="lang-switch" id="lang-switch">
  <button class="lang-btn <?= $_curLang==='en'?'active':'' ?>" data-lang="en">EN</button>
  <span class="lang-sep">/</span>
  <button class="lang-btn <?= $_curLang==='es'?'active':'' ?>" data-lang="es">ES</button>
</div>
<style>
.lang-switch{
  display:flex;align-items:center;gap:.3rem;
  font-size:.72rem;font-weight:600;letter-spacing:.05em;
  margin-left:.5rem;
}
.lang-btn{
  background:none;border:none;cursor:pointer;
  color:var(--nav-link-color);
  padding:.2rem .35rem;border-radius:4px;
  transition:color .2s,background .2s;
  font-family:var(--font-body);font-size:inherit;font-weight:inherit;letter-spacing:inherit;
}
.lang-btn.active{color:var(--nav-link-color-active);background:rgba(0,184,125,.1);}
.lang-btn:hover{color:var(--accent);}
.lang-sep{color:var(--nav-link-color);opacity:.4;font-size:.7rem;}
.nav-mobile .lang-switch{margin:1rem 0 0;font-size:.85rem;}
</style>
<script>
document.querySelectorAll('.lang-btn').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const lang = btn.dataset.lang;
    const url = new URL(window.location.href);
    url.searchParams.set('lang', lang);
    window.location.href = url.toString();
  });
});
</script>
