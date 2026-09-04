/* main.js — nav, live clock, magnetic, decode, reveals, marquee, meters,
 * and the hero infra-topology canvas. Vanilla JS; Lenis is progressive. */
(function () {
  'use strict';
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ── Smooth scroll (Lenis) ─────────────────────────────────── */
  var lenis = null;
  if (window.Lenis && !reduce) {
    lenis = new Lenis({ duration: 1.1, easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); }, smoothWheel: true });
    (function raf(t) { lenis.raf(t); requestAnimationFrame(raf); })(0);
  }
  function scrollToEl(el) {
    if (lenis) lenis.scrollTo(el, { offset: -76 });
    else el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
  }
  $$('a[href^="#"]').forEach(function (a) {
    var id = a.getAttribute('href');
    if (id.length < 2) return;
    a.addEventListener('click', function (e) {
      var el = $(id);
      if (!el) return;
      e.preventDefault();
      closeMenu();
      scrollToEl(el);
      history.replaceState(null, '', id);
    });
  });

  /* ── Navigation ────────────────────────────────────────────── */
  var header = $('#siteHeader'), nav = $('#nav'), toggle = $('#navToggle');
  function onScroll() {
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 24);
    if (nav) {
      var max = Math.max(1, document.documentElement.scrollHeight - innerHeight);
      nav.style.setProperty('--scroll', Math.min(1, window.scrollY / max).toFixed(4));
    }
  }
  onScroll(); window.addEventListener('scroll', onScroll, { passive: true });

  function closeMenu() { if (nav) nav.classList.remove('is-open'); if (toggle) toggle.setAttribute('aria-expanded', 'false'); }
  if (toggle) toggle.addEventListener('click', function () {
    var open = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeMenu(); });

  var links = $$('.nav__link');
  var sections = links
    .filter(function (l) { return (l.getAttribute('href') || '').charAt(0) === '#'; })
    .map(function (l) { return $(l.getAttribute('href')); })
    .filter(Boolean);
  if (sections.length && 'IntersectionObserver' in window) {
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        links.forEach(function (l) {
          var on = l.getAttribute('href') === '#' + en.target.id;
          l.classList.toggle('is-active', on);
          if (on) l.setAttribute('aria-current', 'page'); else l.removeAttribute('aria-current');
        });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(function (s) { spy.observe(s); });
  }

  /* ── Theme toggle (dark ⇄ light) ───────────────────────────── */
  var themeBtn = $('#themeToggle');
  var themeMeta = $('meta[name="theme-color"]');
  var themeAnimT = null;
  function applyTheme(t, persist) {
    document.documentElement.setAttribute('data-theme', t);
    if (persist) { try { localStorage.setItem('theme', t); } catch (e) {} }
    if (themeMeta) themeMeta.setAttribute('content', t === 'light' ? '#f2f5f2' : '#08090b');
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

  /* ── Magnetic buttons ──────────────────────────────────────── */
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

    /* 3D tilt on the identity card — cursor steers the plane, ±4° */
    var idcard = $('.idcard');
    if (idcard) {
      idcard.addEventListener('pointermove', function (e) {
        var r = idcard.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width;
        var y = (e.clientY - r.top) / r.height;
        idcard.style.setProperty('--ry', ((x - 0.5) * 8).toFixed(2) + 'deg');
        idcard.style.setProperty('--rx', ((0.5 - y) * 8).toFixed(2) + 'deg');
      }, { passive: true });
      idcard.addEventListener('pointerleave', function () {
        idcard.style.setProperty('--rx', '0deg');
        idcard.style.setProperty('--ry', '0deg');
      });
    }

    $$('.idcard, .principle, .spec, .stack-card, .crow, .aw, .edu, .copyline').forEach(function (el) {
      el.classList.add('has-spotlight');
      if (!el.querySelector(':scope > .spotlight')) {
        var s = document.createElement('span');
        s.className = 'spotlight';
        s.setAttribute('aria-hidden', 'true');
        el.appendChild(s);
      }
      el.addEventListener('pointermove', function (e) {
        var b = el.getBoundingClientRect();
        el.style.setProperty('--px', (((e.clientX - b.left) / b.width) * 100).toFixed(2) + '%');
        el.style.setProperty('--py', (((e.clientY - b.top) / b.height) * 100).toFixed(2) + '%');
      }, { passive: true });
    });
  }

  /* ── Text decode (scramble → settle, L→R) ─────────────────── */
  var GLYPHS = '#</>{}[]=+*^:~10';
  function decode(el, duration) {
    var final = el.getAttribute('data-decode') || el.textContent;
    var len = final.length, t0 = null;
    duration = duration || 900;
    function frame(now) {
      if (t0 === null) t0 = now;
      var p = Math.min((now - t0) / duration, 1);
      var settled = Math.floor(p * len);
      var out = final.slice(0, settled);
      for (var i = settled; i < len; i++) {
        out += final[i] === ' ' ? ' ' : GLYPHS[(Math.random() * GLYPHS.length) | 0];
      }
      el.textContent = out;
      if (p < 1) requestAnimationFrame(frame);
      else el.textContent = final;
    }
    requestAnimationFrame(frame);
  }
  function runDecodes() {
    if (reduce) return;
    $$('[data-decode]').forEach(function (el, i) {
      var final = el.getAttribute('data-decode') || el.textContent;
      el.textContent = final; // width-stable: same glyph count from frame 1
      setTimeout(function () { decode(el, 850 + i * 150); }, i * 130);
    });
  }
  var decoded = false;
  function heroIn() {
    if (decoded) return; decoded = true;
    var hero = $('#hero') || $('.hero') || $('#home');
    if (hero) hero.classList.add('is-in');
    runDecodes();
  }
  document.addEventListener('preloader:done', heroIn);
  // Fallbacks: preloader already gone, or something stalled.
  if (!document.getElementById('preloader')) heroIn();
  setTimeout(heroIn, 4600);

  // Hovering a title line re-runs the scramble (throttled per line).
  if (finePointer && !reduce) {
    $$('.hero__title [data-decode]').forEach(function (el) {
      var last = 0;
      el.parentElement.addEventListener('pointerenter', function () {
        var now = Date.now();
        if (!decoded || now - last < 2600) return;
        last = now;
        decode(el, 650);
      });
    });
  }

  /* ── Marquee: duplicate track for seamless loop ────────────── */
  var track = $('#marqueeTrack');
  if (track) {
    var firstHalf = track.children.length;
    track.innerHTML += track.innerHTML;
    Array.prototype.slice.call(track.children, firstHalf).forEach(function (c) {
      c.setAttribute('aria-hidden', 'true');
    });
  }

  /* ── Stack: card ↔ yaml cross-highlight ───────────────────── */
  var spec = $('#stack .spec');
  var stackCards = $$('#stack .stack-card');
  if (spec && stackCards.length) {
    var lnByKey = {};
    $$('.ln', spec).forEach(function (ln) {
      var k = ln.querySelector('.k');
      if (k) lnByKey[k.textContent.trim()] = ln;
    });
    var cardsByKey = {};
    stackCards.forEach(function (card) {
      var keys = (card.getAttribute('data-keys') || '').split(',');
      keys.forEach(function (key) { cardsByKey[key.trim()] = card; });
      card.addEventListener('pointerenter', function () {
        spec.classList.add('is-linking');
        keys.forEach(function (key) {
          var ln = lnByKey[key.trim()];
          if (ln) ln.classList.add('is-hot');
        });
      });
      card.addEventListener('pointerleave', function () {
        spec.classList.remove('is-linking');
        $$('.ln.is-hot', spec).forEach(function (l) { l.classList.remove('is-hot'); });
      });
    });
    /* reverse: yaml line lights its card; `mode: automate_everything` lights all */
    Object.keys(lnByKey).forEach(function (key) {
      var targets = key === 'mode' ? stackCards : (cardsByKey[key] ? [cardsByKey[key]] : []);
      if (!targets.length) return;
      lnByKey[key].addEventListener('pointerenter', function () {
        targets.forEach(function (c) { c.classList.add('is-hot'); });
      });
      lnByKey[key].addEventListener('pointerleave', function () {
        targets.forEach(function (c) { c.classList.remove('is-hot'); });
      });
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
    }, { rootMargin: '0px 0px -12% 0px' });
    $$('[data-reveal], [data-stagger]').forEach(function (el) { rev.observe(el); });

    /* segmented meters — fill segment by segment when visible */
    var segObs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var seg = en.target;
        segObs.unobserve(seg);
        var bars = $$('i', seg);
        var n = parseInt(seg.getAttribute('data-fill') || '0', 10);
        bars.forEach(function (b, i) {
          if (i < n) setTimeout(function () {
            b.classList.add('on');
            seg.classList.add('is-on');
          }, reduce ? 0 : 60 * i + 150);
        });
      });
    }, { rootMargin: '0px 0px -10% 0px' });
    $$('.seg').forEach(function (s) { segObs.observe(s); });
  } else {
    $$('[data-reveal], [data-stagger]').forEach(function (el) { el.classList.add('is-visible'); });
    $$('.seg').forEach(function (seg) {
      seg.classList.add('is-on');
      var n = parseInt(seg.getAttribute('data-fill') || '0', 10);
      $$('i', seg).forEach(function (b, i) { if (i < n) b.classList.add('on'); });
    });
  }

  /* ── Copy email to clipboard ───────────────────────────────── */
  $$('[data-copy]').forEach(function (btn) {
    var t = null;
    btn.addEventListener('click', function () {
      var text = btn.getAttribute('data-copy');
      function done() {
        btn.classList.add('is-copied');
        clearTimeout(t);
        t = setTimeout(function () { btn.classList.remove('is-copied'); }, 1600);
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(function () { fallback(); });
      } else { fallback(); }
      function fallback() {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        document.body.removeChild(ta);
      }
    });
  });

  /* ── Hero canvas — the real sky over Gurugram, live ──────────
   * A south-facing panorama of the night sky at 28.4595°N,
   * 77.0266°E (the coordinates on the id card), computed from
   * the actual date and time and refreshed every 30s. Bright-star
   * catalog + constellation lines; N/E/S/W compass on the
   * horizon; a rare meteor. Subtle by design; theme-aware. */
  var canvas = $('#heroCanvas');
  if (canvas && canvas.getContext) {
    var ctx = canvas.getContext('2d');
    var DPR = Math.min(window.devicePixelRatio || 1, 1.5);
    var W = 0, H = 0, frameN = 0;
    var dust = [], sky = [], meteors = [], phs = [], twk = [], lastSky = 0;
    var mouse = { x: -9999, y: -9999, active: false };
    var ox = 0, oy = 0;
    var RAD = Math.PI / 180, LAT = 28.4595 * RAD, LON = 77.0266;

    /* Bright-star catalog (J2000): [RA hours, Dec°, magnitude] */
    var CAT = [
      [5.919, 7.41, 0.5], [5.242, -8.2, 0.13], [5.419, 6.35, 1.64],
      [5.679, -1.94, 1.77], [5.604, -1.2, 1.69], [5.533, -0.3, 2.23],
      [5.796, -9.67, 2.09], [6.752, -16.72, -1.46], [6.378, -17.96, 1.98],
      [7.14, -26.39, 1.83], [6.977, -28.97, 1.5], [7.401, -29.3, 2.45],
      [11.062, 61.75, 1.79], [11.031, 56.38, 2.37], [11.897, 53.69, 2.44],
      [12.257, 57.03, 3.31], [12.9, 55.96, 1.77], [13.399, 54.93, 2.27],
      [13.792, 49.31, 1.86], [0.153, 59.15, 2.27], [0.675, 56.54, 2.23],
      [0.945, 60.72, 2.47], [1.43, 60.24, 2.68], [1.907, 63.67, 3.38],
      [16.49, -26.43, 1.09], [16.005, -22.62, 2.32], [16.091, -19.81, 2.62],
      [16.836, -34.29, 2.29], [17.56, -37.1, 1.63], [17.622, -43.0, 1.87],
      [12.443, -63.1, 0.76], [12.795, -59.69, 1.25], [12.519, -57.11, 1.64],
      [12.252, -58.75, 2.79], [4.599, 16.51, 0.85], [5.438, 28.61, 1.68],
      [5.627, 21.14, 3.0], [7.577, 31.89, 1.57], [7.755, 28.03, 1.14],
      [6.629, 16.4, 1.92], [10.139, 11.97, 1.35], [10.333, 19.84, 2.28],
      [11.235, 20.52, 2.56], [11.818, 14.57, 2.13], [20.69, 45.28, 1.25],
      [20.371, 40.26, 2.2], [19.512, 27.96, 3.18], [19.75, 45.13, 2.87],
      [20.77, 33.97, 2.46], [18.616, 38.78, 0.03], [18.835, 33.36, 3.52],
      [18.982, 32.69, 3.24], [19.846, 8.87, 0.77], [19.771, 10.61, 2.72],
      [19.922, 6.41, 3.71], [5.278, 45.99, 0.08], [5.995, 44.95, 1.9],
      [5.995, 37.21, 2.65], [4.95, 33.17, 2.69], [23.079, 15.21, 2.49],
      [23.063, 28.08, 2.42], [0.14, 29.09, 2.06], [0.22, 15.18, 2.83],
      [14.66, -60.83, -0.27], [14.064, -60.37, 0.61], [6.399, -52.7, -0.74],
      [14.261, 19.18, -0.05], [13.42, -11.16, 0.97], [7.655, 5.22, 0.34],
      [22.961, -29.62, 1.16], [1.629, -57.24, 0.46], [2.53, 89.26, 1.98]
    ];
    var CONS = [
      { n: 'ORION', l: [[0, 2], [2, 5], [5, 4], [4, 3], [3, 0], [3, 6], [5, 1], [1, 6]] },
      { n: 'CANIS MAJOR', l: [[7, 8], [7, 9], [9, 10], [9, 11]] },
      { n: 'URSA MAJOR', l: [[12, 13], [13, 14], [14, 15], [15, 12], [15, 16], [16, 17], [17, 18]] },
      { n: 'CASSIOPEIA', l: [[19, 20], [20, 21], [21, 22], [22, 23]] },
      { n: 'SCORPIUS', l: [[26, 25], [25, 24], [24, 27], [27, 28], [28, 29]] },
      { n: 'CRUX', l: [[30, 32], [31, 33]] },
      { n: 'TAURUS', l: [[34, 35], [34, 36]] },
      { n: 'GEMINI', l: [[37, 38], [38, 39]] },
      { n: 'LEO', l: [[40, 41], [41, 42], [42, 43]] },
      { n: 'CYGNUS', l: [[44, 45], [45, 46], [47, 45], [45, 48]] },
      { n: 'LYRA', l: [[49, 50], [50, 51], [51, 49]] },
      { n: 'AQUILA', l: [[53, 52], [52, 54]] },
      { n: 'AURIGA', l: [[55, 56], [56, 57], [57, 58], [58, 55]] },
      { n: 'PEGASUS', l: [[59, 60], [60, 61], [61, 62], [62, 59]] },
      { n: 'CENTAURUS', l: [[63, 64]] }
    ];
    function palette() {
      return document.documentElement.getAttribute('data-theme') === 'light'
        ? { star: '13,18,16', acc: '13,122,78', dim: 0.8 }
        : { star: '244,246,248', acc: '62,207,142', dim: 1 };
    }
    var PAL = palette();
    window.addEventListener('themechange', function () {
      PAL = palette();
      if (reduce) draw(false);
    });

    function size() {
      var r = canvas.getBoundingClientRect();
      W = r.width; H = r.height;
      canvas.width = Math.floor(W * DPR); canvas.height = Math.floor(H * DPR);
      ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
      build();
    }
    function build() {
      dust = []; meteors = []; phs = []; twk = [];
      var n = Math.min(150, Math.round((W * H) / 9000));
      for (var i = 0; i < n; i++) {
        var deep = Math.random() < 0.55;
        dust.push({
          x: Math.random() * W, y: Math.random() * H,
          r: 0.4 + Math.pow(Math.random(), 2.2) * (deep ? 0.6 : 1.0),
          a: 0.1 + Math.random() * (deep ? 0.2 : 0.3),
          tw: 0.35 + Math.random() * 1.15,
          ph: Math.random() * 6.283,
          green: Math.random() < 0.12,
          deep: deep
        });
      }
      for (i = 0; i < CAT.length; i++) {
        phs.push(Math.random() * 6.283);
        twk.push(0.3 + Math.random() * 0.9);
      }
      computeSky();
      lastSky = Date.now();
    }
    /* Equatorial → horizontal for Mysuru, now; south-centred
     * cylindrical panorama: x = azimuth, y = altitude. */
    function computeSky() {
      var d = Date.now() / 86400000 + 2440587.5 - 2451545.0;
      var lst = ((280.46061837 + 360.98564736629 * d + LON) % 360 + 360) % 360;
      for (var i = 0; i < CAT.length; i++) {
        var ra = CAT[i][0] * 15, dec = CAT[i][1] * RAD;
        var ha = (((lst - ra + 540) % 360) - 180) * RAD;
        var sA = Math.sin(dec) * Math.sin(LAT) + Math.cos(dec) * Math.cos(LAT) * Math.cos(ha);
        var alt = Math.asin(sA);
        var cA = (Math.sin(dec) - sA * Math.sin(LAT)) / (Math.cos(alt) * Math.cos(LAT));
        var az = Math.acos(Math.max(-1, Math.min(1, cA)));
        if (Math.sin(ha) > 0) az = 6.2832 - az;
        var rel = (((az / RAD) - 180 + 540) % 360) - 180;
        sky[i] = {
          up: alt > -0.02,
          x: (rel / 360 + 0.5) * W,
          y: H * (0.97 - (alt / RAD / 90) * 0.93),
          m: CAT[i][2]
        };
      }
    }
    function spawnMeteor() {
      var ltr = Math.random() < 0.7;
      meteors.push({
        x: W * (0.1 + Math.random() * 0.6), y: H * (0.04 + Math.random() * 0.3),
        vx: (ltr ? 1 : -1) * (5.5 + Math.random() * 3), vy: 2.2 + Math.random() * 1.6,
        life: 0, max: 36 + ((Math.random() * 22) | 0)
      });
    }

    function draw(animate) {
      ctx.clearRect(0, 0, W, H);
      var t = performance.now() / 1000;
      var dim = PAL.dim, i;

      /* parallax drifts toward the pointer, far layer less */
      var ptx = mouse.active ? ((mouse.x / W) - 0.5) * 2 : 0;
      var pty = mouse.active ? ((mouse.y / H) - 0.5) * 2 : 0;
      if (animate) { ox += (ptx - ox) * 0.03; oy += (pty - oy) * 0.03; }

      /* atmosphere — faint dust, two parallax depths */
      for (i = 0; i < dust.length; i++) {
        var s = dust[i];
        var al = s.a;
        if (animate) al *= 0.7 + 0.3 * Math.sin(s.ph + t * s.tw * 3.2);
        var px = s.x + ox * (s.deep ? 4 : 9);
        var py = s.y + oy * (s.deep ? 3 : 6);
        ctx.fillStyle = 'rgba(' + (s.green ? PAL.acc : PAL.star) + ',' + (al * dim).toFixed(3) + ')';
        ctx.beginPath(); ctx.arc(px, py, s.r, 0, 6.2832); ctx.fill();
      }

      /* the real sky — constellation lines, stars, names */
      var sx = ox * 8, sy = oy * 5;
      ctx.save();
      ctx.translate(sx, sy);
      for (i = 0; i < CONS.length; i++) {
        var c = CONS[i], seen = {}, upN = 0, total = 0, cxm = 0, cym = 0, j, k;
        ctx.strokeStyle = 'rgba(' + PAL.star + ',' + (0.11 * dim).toFixed(3) + ')';
        ctx.lineWidth = 1;
        ctx.beginPath();
        for (j = 0; j < c.l.length; j++) {
          for (k = 0; k < 2; k++) {
            var idx = c.l[j][k];
            if (!seen[idx]) {
              seen[idx] = 1; total++;
              if (sky[idx].up) { upN++; cxm += sky[idx].x; cym += sky[idx].y; }
            }
          }
          var A = sky[c.l[j][0]], B = sky[c.l[j][1]];
          if (!A.up || !B.up || Math.abs(A.x - B.x) > W * 0.5) continue;
          ctx.moveTo(A.x, A.y); ctx.lineTo(B.x, B.y);
        }
        ctx.stroke();
        if (upN && upN >= total * 0.6) {
          ctx.font = '600 9px "JetBrains Mono", monospace';
          ctx.textAlign = 'center';
          ctx.fillStyle = 'rgba(' + PAL.star + ',' + (0.26 * dim).toFixed(3) + ')';
          ctx.fillText(c.n, cxm / upN, cym / upN + 16);
        }
      }
      for (i = 0; i < sky.length; i++) {
        var st = sky[i];
        if (!st || !st.up) continue;
        var r = Math.max(0.6, Math.min(2.6, 2.2 - st.m * 0.42));
        var a2 = Math.max(0.3, Math.min(0.95, 0.82 - st.m * 0.16));
        if (animate) a2 *= 0.78 + 0.22 * Math.sin(phs[i] + t * twk[i] * 3.4);
        if (mouse.active) {
          var dx = st.x + sx - mouse.x, dy = st.y + sy - mouse.y;
          var d2 = dx * dx + dy * dy;
          if (d2 < 8100) a2 = Math.min(1, a2 + (1 - Math.sqrt(d2) / 90) * 0.4);
        }
        if (st.m < 0.2) {
          ctx.fillStyle = 'rgba(' + PAL.acc + ',' + (0.07 * dim).toFixed(3) + ')';
          ctx.beginPath(); ctx.arc(st.x, st.y, r + 5, 0, 6.2832); ctx.fill();
        }
        ctx.fillStyle = 'rgba(' + PAL.star + ',' + (a2 * dim).toFixed(3) + ')';
        ctx.beginPath(); ctx.arc(st.x, st.y, r, 0, 6.2832); ctx.fill();
      }
      ctx.restore();

      /* compass on the horizon — the panorama faces south */
      var DIRS = [['N', 0], ['E', 90], ['S', 180], ['W', 270]], di, dj;
      ctx.font = '600 9px "JetBrains Mono", monospace';
      ctx.textAlign = 'center';
      for (di = 0; di < 4; di++) {
        var drel = ((DIRS[di][1] - 180 + 540) % 360) - 180;
        var bx = (drel / 360 + 0.5) * W;
        for (dj = -1; dj <= 1; dj++) {
          var xx = bx + dj * W;
          if (xx < -20 || xx > W + 20) continue;
          ctx.strokeStyle = 'rgba(' + PAL.acc + ',' + (0.5 * dim).toFixed(3) + ')';
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(xx, H - 30); ctx.lineTo(xx, H - 23); ctx.stroke();
          ctx.fillStyle = 'rgba(' + PAL.acc + ',' + (0.55 * dim).toFixed(3) + ')';
          ctx.fillText(DIRS[di][0], xx, H - 11);
        }
      }

      /* deploy streaks — a quiet meteor every ~10s */
      for (i = meteors.length - 1; i >= 0; i--) {
        var m = meteors[i];
        if (animate) { m.x += m.vx; m.y += m.vy; m.life++; }
        if (m.life >= m.max) { meteors.splice(i, 1); continue; }
        var k = Math.sin(3.1416 * (m.life / m.max));
        var tailX = m.x - m.vx * 9, tailY = m.y - m.vy * 9;
        var lg = ctx.createLinearGradient(m.x, m.y, tailX, tailY);
        lg.addColorStop(0, 'rgba(' + PAL.acc + ',' + (0.55 * k * dim).toFixed(3) + ')');
        lg.addColorStop(1, 'rgba(' + PAL.acc + ',0)');
        ctx.strokeStyle = lg;
        ctx.lineWidth = 1.4;
        ctx.beginPath(); ctx.moveTo(m.x, m.y); ctx.lineTo(tailX, tailY); ctx.stroke();
        ctx.fillStyle = 'rgba(' + PAL.star + ',' + (0.85 * k * dim).toFixed(3) + ')';
        ctx.beginPath(); ctx.arc(m.x, m.y, 1.3, 0, 6.2832); ctx.fill();
      }
    }

    var running = false;
    function loop() {
      if (document.hidden) { running = false; return; }
      frameN++;
      if (Date.now() - lastSky > 30000) { computeSky(); lastSky = Date.now(); }
      if (frameN % 320 === 0 && Math.random() < 0.55 && meteors.length < 2) spawnMeteor();
      draw(true);
      requestAnimationFrame(loop);
    }
    function start() { if (!running && !reduce) { running = true; requestAnimationFrame(loop); } }
    document.addEventListener('visibilitychange', function () { if (!document.hidden) start(); });

    canvas.style.pointerEvents = 'none';
    document.addEventListener('pointermove', function (e) {
      var r = canvas.getBoundingClientRect();
      if (e.clientY < r.bottom && e.clientY > r.top) { mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.active = true; }
      else { mouse.active = false; }
    }, { passive: true });

    var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(size, 200); });
    size();
    if (reduce) draw(false); else start();
  }
})();
