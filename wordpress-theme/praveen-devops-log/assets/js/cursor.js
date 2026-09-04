/* cursor.js - quiet signal cursor
 * Precise 1:1 dot + soft orbit ring + restrained action label.
 * Fine pointers only; disabled for touch and prefers-reduced-motion. */
(function () {
  'use strict';
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!fine || reduce) return;

  var root = document.createElement('div');
  root.className = 'cursor';
  root.setAttribute('aria-hidden', 'true');
  root.innerHTML =
    '<div class="cursor__ring"></div>' +
    '<div class="cursor__dot"></div>' +
    '<div class="cursor__label"></div>';
  document.body.appendChild(root);
  document.body.classList.add('has-cursor');

  var dot = root.querySelector('.cursor__dot');
  var ring = root.querySelector('.cursor__ring');
  var label = root.querySelector('.cursor__label');

  var tx = innerWidth / 2, ty = innerHeight / 2;
  var rx = tx, ry = ty;
  var idleTimer = null, raf = null;
  var visible = false;
  var currentTarget = null;

  function setLabel(text) {
    if (!text) {
      root.classList.remove('has-label');
      label.textContent = '';
      return;
    }
    label.textContent = text;
    root.classList.add('has-label');
  }

  function move(e) {
    tx = e.clientX; ty = e.clientY;
    if (!visible) {
      rx = tx; ry = ty;
      visible = true;
      root.classList.add('is-visible');
    }
    // Dot pins to the real pointer 1:1 so clicks feel native.
    dot.style.transform = 'translate3d(' + tx + 'px,' + ty + 'px,0) translate(-50%,-50%)';
    root.classList.remove('is-idle', 'is-out');
    clearTimeout(idleTimer);
    idleTimer = setTimeout(function () { root.classList.add('is-idle'); }, 2200);
  }

  function frame() {
    rx += (tx - rx) * 0.32; ry += (ty - ry) * 0.32;
    ring.style.transform = 'translate3d(' + rx + 'px,' + ry + 'px,0) translate(-50%,-50%)';
    label.style.transform = 'translate3d(' + (rx + 18) + 'px,' + (ry + 20) + 'px,0)';
    raf = requestAnimationFrame(frame);
  }

  document.addEventListener('pointermove', move, { passive: true });
  document.addEventListener('pointerdown', function () { root.classList.add('is-down'); });
  document.addEventListener('pointerup', function () { root.classList.remove('is-down'); });
  document.addEventListener('pointerleave', function () { root.classList.add('is-out'); });
  document.addEventListener('pointerenter', function () { root.classList.remove('is-out'); });

  // Hover intent: enlarge softly on commands, label from data-cursor.
  document.addEventListener('pointerover', function (e) {
    var t = e.target.closest('a, button, [data-cursor], input, textarea, select, .marquee__item');
    if (!t) {
      currentTarget = null;
      root.classList.remove('is-hover', 'is-text');
      setLabel('');
      return;
    }
    if (t.matches('input, textarea, select')) {
      currentTarget = null;
      root.classList.remove('is-hover');
      root.classList.add('is-text');
      setLabel('');
      return;
    }
    currentTarget = t;
    root.classList.add('is-hover');
    root.classList.remove('is-text');
    setLabel(t.getAttribute('data-cursor'));
  }, { passive: true });

  document.addEventListener('pointerout', function (e) {
    if (!currentTarget) return;
    if (e.relatedTarget && currentTarget.contains(e.relatedTarget)) return;
    currentTarget = null;
    root.classList.remove('is-hover', 'is-text');
    setLabel('');
  }, { passive: true });

  frame();
})();
