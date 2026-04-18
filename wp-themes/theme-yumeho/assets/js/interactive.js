/**
 * YUMEHO Interactive Effects
 * Modern scroll-driven animations & micro-interactions
 */
(function () {
  'use strict';

  /* ── rAF scroll throttle helper ── */
  function onScroll(fn) {
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(function () {
          fn();
          ticking = false;
        });
      }
    }, { passive: true });
  }

  /* ═══════════════════════════════════════════
     2. NUMBER COUNT-UP ANIMATION
     ═══════════════════════════════════════════ */
  function initCountUp() {
    var nums = document.querySelectorAll('.zf-impact__num');
    if (!nums.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        if (el.dataset.counted) return;
        el.dataset.counted = '1';

        var text = el.textContent.trim();
        var target = parseFloat(text);
        if (isNaN(target)) return;

        var isDecimal = text.indexOf('.') !== -1;
        var duration = 1200;
        var start = performance.now();

        // Start from 0
        el.textContent = isDecimal ? '0.0' : '0';

        function tick(now) {
          var elapsed = now - start;
          var progress = Math.min(elapsed / duration, 1);
          // easeOutExpo
          var ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
          var current = target * ease;

          if (isDecimal) {
            el.textContent = current.toFixed(1);
          } else {
            el.textContent = Math.round(current);
          }

          if (progress < 1) {
            requestAnimationFrame(tick);
          } else {
            el.textContent = text; // restore exact text
          }
        }

        requestAnimationFrame(tick);
        observer.unobserve(el);
      });
    }, { threshold: 0.5 });

    nums.forEach(function (n) { observer.observe(n); });
  }

  /* ═══════════════════════════════════════════
     3. SCROLL PROGRESS BAR
     ═══════════════════════════════════════════ */
  function initProgressBar() {
    var bar = document.createElement('div');
    bar.className = 'scroll-progress';
    document.body.appendChild(bar);

    function update() {
      var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      var docHeight = document.documentElement.scrollHeight - window.innerHeight;
      var progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      bar.style.width = progress + '%';
    }

    onScroll(update);
    update();
  }

  /* ═══════════════════════════════════════════
     4. PARALLAX DEPTH ON IMAGES
     ═══════════════════════════════════════════ */
  function initParallax() {
    var images = document.querySelectorAll('.zigzag-feature__img img, .feature-visual');
    if (!images.length) return;

    function update() {
      images.forEach(function (img) {
        var rect = img.getBoundingClientRect();
        var windowH = window.innerHeight;
        if (rect.top > windowH || rect.bottom < 0) return;
        var center = rect.top + rect.height / 2;
        var offset = (center - windowH / 2) / windowH;
        img.style.transform = 'translateY(' + (offset * -12) + 'px)';
      });
    }

    onScroll(update);
  }

  /* ═══════════════════════════════════════════
     7. STAGGERED CARD ENTRANCE
     ═══════════════════════════════════════════ */
  function initStaggerCards() {
    var grids = document.querySelectorAll('.facility-grid, .editorial-grid');

    grids.forEach(function (grid) {
      var cards = grid.children;
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          Array.from(cards).forEach(function (card, i) {
            setTimeout(function () {
              card.classList.add('stagger-visible');
            }, i * 100);
          });
          observer.unobserve(entry.target);
        });
      }, { threshold: 0.2 });

      observer.observe(grid);
    });
  }

  /* ═══════════════════════════════════════════
     8. SMOOTH SECTION TRANSITIONS
     ═══════════════════════════════════════════ */
  function initSectionReveal() {
    var sections = document.querySelectorAll('.section, .ym-video-section');

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('section-revealed');
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -80px 0px' });

    sections.forEach(function (s) {
      s.classList.add('section-pre-reveal');
      observer.observe(s);
    });
  }

  /* ═══════════════════════════════════════════
     9. FLOATING ELEMENTS ON SCROLL
     ═══════════════════════════════════════════ */
  function initFloatingElements() {
    // Add floating decorative elements
    var hero = document.querySelector('.hero-editorial');
    if (!hero) return;

    var shapes = ['circle', 'ring', 'dot'];
    for (var i = 0; i < 6; i++) {
      var el = document.createElement('div');
      el.className = 'floating-shape floating-shape--' + shapes[i % 3];
      el.style.cssText = 'left:' + (Math.random() * 100) + '%;top:' + (Math.random() * 100) + '%;animation-delay:' + (Math.random() * 5) + 's;animation-duration:' + (6 + Math.random() * 4) + 's;';
      hero.appendChild(el);
    }
  }

  /* ═══════════════════════════════════════════
     10. TILT EFFECT ON HERO IMAGE
     ═══════════════════════════════════════════ */
  function initTiltEffect() {
    var card = document.querySelector('.feature-visual-wrap');
    if (!card) return;

    card.addEventListener('mousemove', function (e) {
      var rect = card.getBoundingClientRect();
      var x = (e.clientX - rect.left) / rect.width - 0.5;
      var y = (e.clientY - rect.top) / rect.height - 0.5;
      card.style.transform = 'perspective(800px) rotateY(' + (x * 8) + 'deg) rotateX(' + (-y * 5) + 'deg) scale(1.02)';
      card.style.transition = 'transform 0.1s ease';
    });

    card.addEventListener('mouseleave', function () {
      card.style.transform = '';
      card.style.transition = 'transform 0.5s ease';
    });
  }

  /* ═══════════════════════════════════════════
     11. VAST-STYLE BUTTON WIPE EFFECT
     ═══════════════════════════════════════════ */
  function initButtonWipe() {
    var btns = document.querySelectorAll('.btn-primary, .btn-secondary');
    btns.forEach(function (btn) {
      // Create wipe pseudo-layer
      if (btn.querySelector('.btn-wipe-bg')) return;
      var wipe = document.createElement('span');
      wipe.className = 'btn-wipe-bg';
      btn.style.position = 'relative';
      btn.style.overflow = 'hidden';
      btn.insertBefore(wipe, btn.firstChild);

      // Wrap all content in a span so it sits above the wipe layer
      var textNodes = Array.from(btn.childNodes).filter(function(n) { return n !== wipe; });
      textNodes.forEach(function(n) {
        if (n.nodeType === 3 && n.textContent.trim()) {
          var span = document.createElement('span');
          span.style.position = 'relative';
          span.style.zIndex = '1';
          span.textContent = n.textContent;
          btn.replaceChild(span, n);
        } else if (n.style) {
          n.style.position = 'relative';
          n.style.zIndex = '1';
        }
      });
    });
  }

  /* ═══════════════════════════════════════════
     12. VAST-STYLE NAV LINK UNDERLINE SWITCH
     ═══════════════════════════════════════════ */
  function initNavUnderline() {
    var links = document.querySelectorAll('.nav-link');
    links.forEach(function (link) {
      if (link.querySelector('.nav-line')) return;
      var line = document.createElement('span');
      line.className = 'nav-line';
      link.appendChild(line);
    });
  }

  /* ═══════════════════════════════════════════
     13. VAST-STYLE CARD CROSSHAIR HOVER
     ═══════════════════════════════════════════ */
  function initCardCrosshair() {
    var cards = document.querySelectorAll('.facility-card, .zigzag-feature');
    cards.forEach(function (card) {
      if (card.querySelector('.crosshair')) return;
      card.style.position = 'relative';
      card.style.overflow = 'hidden';

      // Create 4 crosshair lines
      ['top', 'right', 'bottom', 'left'].forEach(function (dir) {
        var line = document.createElement('span');
        line.className = 'crosshair crosshair--' + dir;
        card.appendChild(line);
      });
    });
  }

  /* ═══════════════════════════════════════════
     14. (removed - caused image display issues)
     ═══════════════════════════════════════════ */
  function initImageReveal() { }

  /* ═══════════════════════════════════════════
     15. HEADER HIDE/SHOW ON SCROLL DIRECTION
     ═══════════════════════════════════════════ */
  function initSmartHeader() {
    var header = document.querySelector('.header');
    if (!header) return;

    var lastScroll = 0;
    var threshold = 80;

    onScroll(function () {
      var current = window.pageYOffset;
      if (current < threshold) {
        header.classList.remove('header--hidden');
        header.classList.remove('header--scrolled');
        return;
      }
      header.classList.add('header--scrolled');
      if (current > lastScroll + 5) {
        header.classList.add('header--hidden');
      } else if (current < lastScroll - 5) {
        header.classList.remove('header--hidden');
      }
      lastScroll = current;
    });
  }


  /* ═══════════════════════════════════════════
     INIT ALL
     ═══════════════════════════════════════════ */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  function boot() {
    initCountUp();
    initProgressBar();
    initParallax();
    initStaggerCards();
    initSectionReveal();
    initFloatingElements();
    initTiltEffect();
    initButtonWipe();
    initNavUnderline();
    initCardCrosshair();
    initImageReveal();
    initSmartHeader();
  }
})();
