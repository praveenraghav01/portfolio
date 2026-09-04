/* main.js — nav, theme toggle, clock, magnetic/spotlight, reveals,
 * copy-to-clipboard, code-block copy button, TOC scrollspy, search panel.
 * Vanilla JS, no dependencies — same conventions as the main site's main.js,
 * trimmed of the homepage-only pieces (hero canvas, git graph, marquee). */
(function () {
  'use strict';
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ── Smooth in-page anchors (TOC, back-to-top, #comments) ──── */
  $$('a[href^="#"]').forEach(function (a) {
    var id = a.getAttribute('href');
    if (id.length < 2) return;
    a.addEventListener('click', function (e) {
      var el = $(id);
      if (!el) return;
      e.preventDefault();
      closeMenu();
      el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
      history.replaceState(null, '', id);
    });
  });

  /* ── Navigation ────────────────────────────────────────────── */
  var header = $('#siteHeader'), nav = $('#nav'), toggle = $('#navToggle');
  function onScroll() { if (header) header.classList.toggle('is-scrolled', window.scrollY > 24); }
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true });

  function closeMenu() { if (nav) nav.classList.remove('is-open'); if (toggle) toggle.setAttribute('aria-expanded', 'false'); }
  if (toggle) toggle.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeMenu(); closeSearch(); } });

  /* ── Search panel ──────────────────────────────────────────── */
  var searchToggle = $('#searchToggle'), searchPanel = $('#searchPanel');
  function closeSearch() { if (searchPanel) searchPanel.classList.remove('is-open'); if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false'); }
  if (searchToggle && searchPanel) {
    searchToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = searchPanel.classList.toggle('is-open');
      searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) { var input = $('input[type="search"]', searchPanel); if (input) input.focus(); }
    });
    document.addEventListener('click', function (e) {
      if (!searchPanel.contains(e.target) && e.target !== searchToggle) closeSearch();
    });
  }

  /* ── Theme toggle (dark ⇄ light) ───────────────────────────── */
  var themeBtn = $('#themeToggle');
  var themeMeta = $('meta[name="theme-color"]');
  var themeAnimT = null;
  function applyTheme(t, persist) {
    document.documentElement.setAttribute('data-theme', t);
    if (persist) { try { localStorage.setItem('theme', t); } catch (e) {} }
    if (themeBtn) themeBtn.setAttribute('aria-label', t === 'light' ? 'Switch to dark theme' : 'Switch to light theme');
    window.dispatchEvent(new CustomEvent('themechange'));
  }
  applyTheme(document.documentElement.getAttribute('data-theme') || 'light', false);
  if (themeBtn) themeBtn.addEventListener('click', function () {
    var next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    if (!reduce) {
      document.documentElement.classList.add('theme-anim');
      clearTimeout(themeAnimT);
      themeAnimT = setTimeout(function () { document.documentElement.classList.remove('theme-anim'); }, 420);
    }
    applyTheme(next, true);
  });

  /* ── Live clock (IST) ──────────────────────────────────────── */
  var clocks = $$('[data-clock]');
  if (clocks.length) {
    var fmt = null;
    try { fmt = new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Kolkata', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false }); } catch (e) {}
    var tick = function () {
      var t = fmt ? fmt.format(new Date()) : new Date().toTimeString().slice(0, 8);
      clocks.forEach(function (c) { c.textContent = t; });
    };
    tick(); setInterval(tick, 1000);
  }

  /* ── Magnetic buttons + spotlight ──────────────────────────── */
  if (finePointer && !reduce) {
    $$('.magnetic').forEach(function (el) {
      var strength = 0.32, r = 90;
      el.addEventListener('pointermove', function (e) {
        var b = el.getBoundingClientRect();
        var mx = e.clientX - (b.left + b.width / 2);
        var my = e.clientY - (b.top + b.height / 2);
        var f = Math.max(0, 1 - Math.hypot(mx, my) / (r + b.width / 2));
        el.style.setProperty('--mx', (mx * strength * f).toFixed(1) + 'px');
        el.style.setProperty('--my', (my * strength * f).toFixed(1) + 'px');
      });
      el.addEventListener('pointerleave', function () { el.style.setProperty('--mx', '0px'); el.style.setProperty('--my', '0px'); });
    });

    $$('.logrow, .pdl-related__card, .pdl-postnav__card, .pdl-toc, .codeblock').forEach(function (el) {
      el.classList.add('has-spotlight');
      if (!el.querySelector(':scope > .spotlight')) {
        var s = document.createElement('span');
        s.className = 'spotlight'; s.setAttribute('aria-hidden', 'true');
        el.appendChild(s);
      }
      el.addEventListener('pointermove', function (e) {
        var b = el.getBoundingClientRect();
        el.style.setProperty('--px', (((e.clientX - b.left) / b.width) * 100).toFixed(2) + '%');
        el.style.setProperty('--py', (((e.clientY - b.top) / b.height) * 100).toFixed(2) + '%');
      }, { passive: true });
    });
  }

  /* ── Reveal on scroll ──────────────────────────────────────── */
  if ('IntersectionObserver' in window) {
    var rev = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        en.target.classList.add('is-visible');
        rev.unobserve(en.target);
      });
    }, { rootMargin: '0px 0px -10% 0px' });
    $$('[data-reveal], [data-stagger]').forEach(function (el) { rev.observe(el); });
  } else {
    $$('[data-reveal], [data-stagger]').forEach(function (el) { el.classList.add('is-visible'); });
  }

  /* ── TOC scrollspy ──────────────────────────────────────────── */
  var tocLinks = $$('.pdl-toc a');
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var headings = tocLinks.map(function (a) { return $(a.getAttribute('href')); }).filter(Boolean);
    var tocObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        tocLinks.forEach(function (a) { a.classList.toggle('is-current', a.getAttribute('href') === '#' + en.target.id); });
      });
    }, { rootMargin: '-15% 0px -70% 0px' });
    headings.forEach(function (h) { tocObs.observe(h); });
  }

  /* ── Copy to clipboard: share links + code blocks ──────────── */
  function copyText(text, onDone) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(onDone).catch(function () { fallback(); });
    } else { fallback(); }
    function fallback() {
      var ta = document.createElement('textarea');
      ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
      document.body.appendChild(ta); ta.select();
      try { document.execCommand('copy'); onDone(); } catch (e) {}
      document.body.removeChild(ta);
    }
  }
  $$('[data-copy]').forEach(function (btn) {
    var t = null;
    btn.addEventListener('click', function () {
      copyText(btn.getAttribute('data-copy'), function () {
        btn.classList.add('is-copied');
        clearTimeout(t);
        t = setTimeout(function () { btn.classList.remove('is-copied'); }, 1600);
      });
    });
  });
  $$('.codeblock__copy').forEach(function (btn) {
    var t = null;
    btn.addEventListener('click', function () {
      var code = btn.closest('.codeblock').querySelector('code');
      if (!code) return;
      copyText(code.textContent, function () {
        var was = btn.textContent;
        btn.textContent = 'copied'; btn.classList.add('is-copied');
        clearTimeout(t);
        t = setTimeout(function () { btn.textContent = was; btn.classList.remove('is-copied'); }, 1600);
      });
    });
  });
})();
