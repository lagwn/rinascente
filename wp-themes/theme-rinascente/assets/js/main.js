/* ============================================
   RINASCENTE — Main JavaScript
   ============================================ */

(function (window, document) {
  const fallbackExtensions = {
    case_01: 'jpg',
    case_03: 'jpg',
    case_dayservice: 'jpg',
    case_hospital: 'jpg',
    domain_healthcare_1x: 'jpg',
    domain_healthcare_2x: 'jpg',
    domain_new_1x: 'jpg',
    domain_new_2x: 'jpg',
    domain_travel_1x: 'jpg',
    domain_travel_2x: 'jpg',
    mica30: 'jpg',
    mv_bg: 'jpg',
    play_rehab_scene: 'jpg',
    yumeho: 'jpg'
  };

  const supportsWebP = (() => {
    try {
      const canvas = document.createElement('canvas');
      if (!canvas || typeof canvas.toDataURL !== 'function') {
        return false;
      }
      return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    } catch (_error) {
      return false;
    }
  })();

  function getFallbackExtension(url) {
    if (typeof url !== 'string') return '';
    const cleanUrl = url.split(/[?#]/)[0];
    const match = cleanUrl.match(/\/?([^/?#]+)\.webp$/i);
    if (!match) return '';
    return fallbackExtensions[match[1]] || '';
  }

  function resolveAssetPath(url) {
    if (supportsWebP || typeof url !== 'string' || !/\.webp(?:[?#]|$)/i.test(url)) {
      return url;
    }
    const extension = getFallbackExtension(url);
    if (!extension) {
      return url;
    }
    return url.replace(/\.webp(?=([?#].*)?$)/i, `.${extension}`);
  }

  function resolveSrcset(srcset) {
    if (supportsWebP || typeof srcset !== 'string' || srcset.indexOf('.webp') === -1) {
      return srcset;
    }
    return srcset
      .split(',')
      .map((candidate) => {
        const trimmed = candidate.trim();
        if (!trimmed) return trimmed;
        const parts = trimmed.split(/\s+/);
        parts[0] = resolveAssetPath(parts[0]);
        return parts.join(' ');
      })
      .join(', ');
  }

  function updateImageCandidate(element, attributeName) {
    const currentValue = element.getAttribute(attributeName);
    if (!currentValue || currentValue.indexOf('.webp') === -1) {
      return;
    }
    const nextValue = attributeName === 'srcset'
      ? resolveSrcset(currentValue)
      : resolveAssetPath(currentValue);
    if (!nextValue || nextValue === currentValue) {
      return;
    }
    element.setAttribute(attributeName, nextValue);
    if (attributeName in element) {
      element[attributeName] = nextValue;
    }
  }

  function applyImageFallbacks(root = document) {
    if (supportsWebP || !root || typeof root.querySelectorAll !== 'function') {
      return;
    }

    root.querySelectorAll('source[srcset]').forEach((source) => {
      updateImageCandidate(source, 'srcset');
      if ((source.getAttribute('type') || '').toLowerCase() === 'image/webp') {
        source.removeAttribute('type');
      }
    });

    root.querySelectorAll('img[src], img[srcset], video[poster]').forEach((element) => {
      updateImageCandidate(element, 'src');
      updateImageCandidate(element, 'srcset');
      updateImageCandidate(element, 'poster');
    });
  }

  document.addEventListener('error', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLImageElement)) {
      return;
    }
    const currentSource = target.currentSrc || target.getAttribute('src') || '';
    const fallbackSource = resolveAssetPath(currentSource);
    if (!fallbackSource || fallbackSource === currentSource) {
      return;
    }
    target.srcset = '';
    target.setAttribute('srcset', '');
    target.src = fallbackSource;
    target.setAttribute('src', fallbackSource);
  }, true);

  window.RinascenteCompat = {
    applyImageFallbacks,
    resolveAssetPath,
    resolveSrcset,
    supportsWebP
  };
})(window, document);

document.addEventListener('DOMContentLoaded', () => {
  if (window.RinascenteCompat) {
    window.RinascenteCompat.applyImageFallbacks();
  }

  // ---- Scroll animations ----
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.fade-up, .fade-in').forEach(el => observer.observe(el));

  // ---- Stagger reveal for grid items ----
  const staggerObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const parent = entry.target;
      const items = parent.querySelectorAll(':scope > .case-card, :scope > .card-dark, :scope > .card-light, :scope > .domain-card');
      items.forEach((item, i) => {
        item.style.transitionDelay = `${i * 0.06}s`;
        item.classList.add('visible');
      });
      staggerObserver.unobserve(parent);
    });
  }, { threshold: 0.05 });

  document.querySelectorAll('[data-stagger]').forEach(el => {
    el.querySelectorAll(':scope > .case-card, :scope > .card-dark, :scope > .card-light, :scope > .domain-card').forEach(item => {
      item.classList.add('fade-up');
    });
    staggerObserver.observe(el);
  });

  // ---- Stat grid stagger ----
  const statObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.querySelectorAll('.stat-item').forEach((item, i) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = `opacity 0.55s cubic-bezier(.5,.01,.15,1) ${i * 0.07}s, transform 0.55s cubic-bezier(.5,.01,.15,1) ${i * 0.07}s`;
        requestAnimationFrame(() => {
          item.style.opacity = '1';
          item.style.transform = 'translateY(0)';
        });
      });
      statObserver.unobserve(entry.target);
    });
  }, { threshold: 0.2 });

  document.querySelectorAll('.stat-grid').forEach(el => statObserver.observe(el));

  // ---- Header scroll state ----
  const header = document.querySelector('.site-header');
  if (header) {
    // Detect if the first content section has a light background
    const firstSection = document.querySelector('main > section, main > .article-hero, main > div > section');
    const isLightBg = (el) => {
      const bg = getComputedStyle(el).backgroundColor;
      const m = bg.match(/rgba?\(\s*(\d+)/);
      return m && parseInt(m[1], 10) > 180;
    };
    const alwaysScrolled = firstSection && (
      firstSection.classList.contains('bg-cream') ||
      firstSection.classList.contains('bg-white') ||
      firstSection.classList.contains('bg-light') ||
      isLightBg(firstSection)
    );
    if (alwaysScrolled) {
      header.classList.add('scrolled');
    }
    let scrollTick = false;
    const onScroll = () => {
      if (alwaysScrolled) return;
      if (!scrollTick) {
        scrollTick = true;
        requestAnimationFrame(() => {
          header.classList.toggle('scrolled', window.scrollY > 40);
          scrollTick = false;
        });
      }
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ---- Mobile menu toggle ----
  const toggle = document.querySelector('.menu-toggle');
  const mobileNav = document.getElementById('mobileNav');
  if (toggle && mobileNav) {
    function closeMobileMenu() {
      mobileNav.classList.remove('open');
      mobileNav.setAttribute('aria-hidden', 'true');
      toggle.classList.remove('is-active');
      toggle.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('menu-open');
    }
    function openMobileMenu() {
      mobileNav.classList.add('open');
      mobileNav.setAttribute('aria-hidden', 'false');
      toggle.classList.add('is-active');
      toggle.setAttribute('aria-expanded', 'true');
      document.body.classList.add('menu-open');
    }
    toggle.addEventListener('click', () => {
      if (mobileNav.classList.contains('open')) {
        closeMobileMenu();
      } else {
        openMobileMenu();
      }
    });
    // Close when clicking a nav link
    mobileNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });
  }

  // ---- Active nav link ----
  const currentPath = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-item').forEach(link => {
    const href = link.getAttribute('href') || '';
    if (href && href.includes(currentPath) && currentPath !== '') {
      link.classList.add('active');
    }
  });

  // ---- Contact form (3-step: input → confirm → complete) ----
  const contactForm = document.getElementById('rinascenteContactForm');
  const stepInput    = document.getElementById('formStepInput');
  const stepConfirm  = document.getElementById('formStepConfirm');
  const stepComplete = document.getElementById('formStepComplete');
  const formSteps    = document.getElementById('formSteps');

  function setActiveStep(num) {
    if (!formSteps) return;
    formSteps.querySelectorAll('.form-step').forEach(s => {
      const n = parseInt(s.dataset.step);
      s.classList.remove('active', 'done');
      if (n === num) s.classList.add('active');
      else if (n < num) s.classList.add('done');
    });
  }

  if (contactForm && stepInput && stepConfirm && stepComplete) {
    // Step 1 → Step 2
    contactForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name  = document.getElementById('cName')?.value.trim();
      const org   = document.getElementById('cOrg')?.value.trim();
      const email = document.getElementById('cEmail')?.value.trim();
      const phone = document.getElementById('cPhone')?.value.trim();
      const agree = document.getElementById('cAgree')?.checked;

      if (!name)  { alert('お名前を入力してください。'); return; }
      if (!org)   { alert('会社名・組織名を入力してください。'); return; }
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('有効なメールアドレスを入力してください。'); return;
      }
      if (!phone || !/^[0-9+\-()\s]{8,20}$/.test(phone)) {
        alert('有効な電話番号を入力してください。'); return;
      }
      if (!agree) { alert('個人情報の取扱いについて同意してください。'); return; }

      // Populate confirm table
      document.getElementById('cfName').textContent  = name;
      document.getElementById('cfOrg').textContent   = org;
      document.getElementById('cfEmail').textContent  = email;
      document.getElementById('cfPhone').textContent  = phone;
      document.getElementById('cfType').textContent   = document.getElementById('cType')?.value || '未選択';
      document.getElementById('cfMsg').textContent    = document.getElementById('cMsg')?.value.trim() || '（未入力）';

      stepInput.style.display   = 'none';
      stepConfirm.style.display = 'block';
      setActiveStep(2);
      window.scrollTo({ top: stepConfirm.closest('.section, main')?.offsetTop || 0, behavior: 'smooth' });
    });

    // Step 2 → Back to Step 1
    document.getElementById('btnBack')?.addEventListener('click', () => {
      stepConfirm.style.display = 'none';
      stepInput.style.display   = 'block';
      setActiveStep(1);
    });

    // Step 2 → Step 3
    document.getElementById('btnSend')?.addEventListener('click', () => {
      stepConfirm.style.display  = 'none';
      stepComplete.style.display = 'block';
      setActiveStep(3);
      window.scrollTo({ top: stepComplete.closest('.section, main')?.offsetTop || 0, behavior: 'smooth' });
    });
  }

  // ---- Login form ----
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      const id  = document.getElementById('loginId')?.value.trim();
      const pw  = document.getElementById('loginPw')?.value.trim();
      const err = document.getElementById('loginError');
      if (!id || !pw) {
        e.preventDefault();
        if (err) err.textContent = 'IDとパスワードを入力してください。';
        return;
      }

      if (err) err.textContent = '';
    });
  }

  // ---- Number counter animation ----
  const statNums = document.querySelectorAll('.stat-num[data-target]');
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseFloat(el.dataset.target);
      const suffix = el.dataset.suffix || '';
      const prefix = el.dataset.prefix || '';
      const decimals = el.dataset.decimals || 0;
      const duration = 1800;
      const start = performance.now();

      const tick = (now) => {
        const elapsed = now - start;
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
        if (progress < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
      counterObserver.unobserve(el);
    });
  }, { threshold: 0.5 });

  statNums.forEach(el => counterObserver.observe(el));

  // ---- Innovation slider: JS-controlled snap (touch + mouse) ----
  document.querySelectorAll('.innovation-track').forEach(function(track) {
    var cards = Array.prototype.slice.call(track.querySelectorAll('.innovation-card'));
    if (!cards.length) return;

    var currentIndex = 0;

    function getCardPosition(index) {
      if (index < 0) index = 0;
      if (index >= cards.length) index = cards.length - 1;
      var cardRect  = cards[index].getBoundingClientRect();
      var trackRect = track.getBoundingClientRect();
      return track.scrollLeft + cardRect.left - trackRect.left;
    }

    function snapTo(index) {
      if (index < 0) index = 0;
      if (index >= cards.length) index = cards.length - 1;
      currentIndex = index;
      track.scrollTo({ left: getCardPosition(index), behavior: 'smooth' });
    }

    // --- Touch swipe ---
    var touchStartX = 0, touchStartScroll = 0, isTouching = false;

    track.addEventListener('touchstart', function(e) {
      isTouching = true;
      touchStartX = e.touches[0].clientX;
      touchStartScroll = track.scrollLeft;
    }, { passive: true });

    track.addEventListener('touchmove', function(e) {
      if (!isTouching) return;
      var dx = touchStartX - e.touches[0].clientX;
      track.scrollLeft = touchStartScroll + dx;
    }, { passive: true });

    track.addEventListener('touchend', function(e) {
      if (!isTouching) return;
      isTouching = false;
      var dx = touchStartX - (e.changedTouches[0]?.clientX || touchStartX);
      var threshold = 40;
      if (dx > threshold) {
        snapTo(currentIndex + 1);
      } else if (dx < -threshold) {
        snapTo(currentIndex - 1);
      } else {
        snapTo(currentIndex);
      }
    });

    // --- Mouse drag (PC only) ---
    var isPointerFine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    if (isPointerFine) {
      var isDown = false, mouseStartX = 0, mouseScrollStart = 0;

      track.addEventListener('mousedown', function(e) {
        isDown = true;
        track.classList.add('is-dragging');
        mouseStartX = e.pageX;
        mouseScrollStart = track.scrollLeft;
        e.preventDefault();
      });

      window.addEventListener('mouseup', function(e) {
        if (!isDown) return;
        isDown = false;
        track.classList.remove('is-dragging');
        var dx = mouseStartX - e.pageX;
        var threshold = 40;
        if (dx > threshold) {
          snapTo(currentIndex + 1);
        } else if (dx < -threshold) {
          snapTo(currentIndex - 1);
        } else {
          snapTo(currentIndex);
        }
      });

      window.addEventListener('mousemove', function(e) {
        if (!isDown) return;
        track.scrollLeft = mouseScrollStart - (e.pageX - mouseStartX);
      });
    }
  });

});
