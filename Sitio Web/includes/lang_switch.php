<?php
$_curLang = currentLang();
?>
<span class="lang-switch" id="lang-switch">
  <a href="?lang=en" class="lang-link<?= $_curLang==='en'?' active':'' ?>" data-lang="en" onclick="switchLang('en',event)">EN</a>
  <span class="lang-sep">|</span>
  <a href="?lang=es" class="lang-link<?= $_curLang==='es'?' active':'' ?>" data-lang="es" onclick="switchLang('es',event)">ES</a>
</span>
<style>
.lang-switch{display:inline-flex;align-items:center;gap:1px;font-size:.65rem;font-weight:600;letter-spacing:.04em;margin-left:.6rem;opacity:.7;transition:opacity .2s}
.lang-switch:hover{opacity:1}
.lang-link{color:inherit;text-decoration:none;padding:2px 3px;border-radius:3px;transition:all .15s}
.lang-link:hover{color:var(--accent,#00C896)}
.lang-link.active{color:var(--accent,#00C896);font-weight:700}
.lang-sep{opacity:.3;font-size:.55rem;user-select:none}
.nav.scrolled .lang-switch,.nav--scrolled .lang-switch{color:#475569}
@media(max-width:768px){.lang-switch{font-size:.7rem}}
</style>
<script>
function switchLang(lang,e){e.preventDefault();document.cookie='intsolcom_lang='+lang+';path=/;max-age=31536000;SameSite=Lax';var u=new URL(window.location.href);u.searchParams.set('lang',lang);window.location.href=u.toString();}
</script>
