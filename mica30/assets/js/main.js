document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle + SP Drawer
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    const spDrawer = document.getElementById('spDrawer');

    function isSPMenu() { return window.innerWidth <= 860; }

    function openDrawer() {
        if (!spDrawer) return;
        spDrawer.classList.add('open');
        spDrawer.setAttribute('aria-hidden', 'false');
        mobileMenuBtn.classList.add('is-open');
        document.body.classList.add('sp-menu-open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        if (!spDrawer) return;
        spDrawer.classList.remove('open');
        spDrawer.setAttribute('aria-hidden', 'true');
        mobileMenuBtn.classList.remove('is-open');
        document.body.classList.remove('sp-menu-open');
        document.body.style.overflow = '';
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            if (isSPMenu()) {
                spDrawer && spDrawer.classList.contains('open') ? closeDrawer() : openDrawer();
            } else if (navMenu) {
                navMenu.classList.toggle('open');
            }
        });
    }

    // ドロワー内リンクをタップしたら閉じる
    if (spDrawer) {
        spDrawer.querySelectorAll('a').forEach(a => {
            a.addEventListener('click', closeDrawer);
        });
    }

    // ESCキーで閉じる
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && spDrawer && spDrawer.classList.contains('open')) closeDrawer();
    });

    // Scroll Animation Observer
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll(
        '.animate-on-scroll, .anim-left, .anim-right, .anim-scale, .img-reveal, .section-heading, .footer'
    ).forEach(el => observer.observe(el));

    // Stagger children in grids
    document.querySelectorAll('.feature-grid, .facility-grid, .editorial-grid').forEach(grid => {
        const gridObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    Array.from(entry.target.children).forEach((child, i) => {
                        child.style.transitionDelay = (i * 0.1) + 's';
                        child.classList.add('visible');
                    });
                    gridObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        gridObs.observe(grid);
    });

    // Magnetic button effect
    if (matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.btn-primary.btn-lg, .btn-pulse').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
            });
            btn.addEventListener('mouseleave', () => { btn.style.transform = ''; });
        });
    }

    // Smooth header hide/show on scroll (rAF throttled)
    let lastScrollY = 0;
    const header = document.querySelector('.header');
    if (header) {
        let scrollTick = false;
        window.addEventListener('scroll', () => {
            if (!scrollTick) {
                scrollTick = true;
                requestAnimationFrame(() => {
                    const y = window.scrollY;
                    // ドロワー開時はヘッダーを固定
                    if (document.body.classList.contains('sp-menu-open')) {
                        header.style.transform = 'translateY(0)';
                    } else if (y > 200) {
                        header.style.transform = y > lastScrollY ? 'translateY(-100%)' : 'translateY(0)';
                        header.style.transition = 'transform 0.35s cubic-bezier(0.16,1,0.3,1)';
                    } else {
                        header.style.transform = 'translateY(0)';
                    }
                    lastScrollY = y;
                    scrollTick = false;
                });
            }
        }, { passive: true });
    }

    // FAQ Accordion (optional: collapse/expand)
    document.querySelectorAll('.faq-item').forEach(item => {
        const q = item.querySelector('.faq-question');
        const a = item.querySelector('.faq-answer');
        if (q && a) {
            q.style.cursor = 'pointer';
            q.addEventListener('click', () => {
                const isOpen = a.style.display !== 'none' && a.style.display !== '';
                a.style.display = isOpen ? 'none' : 'block';
            });
        }
    });

    // Contact form validation
    const contactForm = document.getElementById('mica30ContactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('contactName')?.value.trim();
            const org = document.getElementById('contactOrg')?.value.trim();
            const email = document.getElementById('contactEmail')?.value.trim();
            const phone = document.getElementById('contactPhone')?.value.trim();

            if (!org) { alert('医療機関・施設名を入力してください。'); return; }
            if (!name) { alert('担当者名を入力してください。'); return; }
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('有効なメールアドレスを入力してください。'); return;
            }
            if (!phone || !/^[0-9+\-()\s]{8,20}$/.test(phone)) {
                alert('有効な電話番号を入力してください。'); return;
            }

            // Show success state
            const btn = contactForm.querySelector('[type="submit"]');
            if (btn) {
                btn.textContent = '送信完了しました';
                btn.disabled = true;
            }
        });
    }
});
