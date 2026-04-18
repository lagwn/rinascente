(function (window, document) {
    const fallbackExtensions = {
        case_dayservice: 'jpg',
        case_hospital: 'jpg',
        ceiling_type_walkmate_lineup: 'png',
        facility_dayservice: 'jpg',
        facility_hospital: 'jpg',
        facility_nursing: 'jpg',
        feature_continuity: 'jpg',
        feature_operation02: 'jpg',
        feature_safety: 'jpg',
        harness_bg: 'png',
        hero_visual: 'jpg',
        install_ceiling: 'jpg',
        install_stand: 'jpg',
        map_yumeho: 'png',
        play_rehab_scene: 'jpg',
        rail: 'jpg',
        sticker_gait: 'png',
        sticker_rail: 'png',
        sticker_report: 'png',
        sticker_safety: 'png'
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

    const getFallbackExtension = (url) => {
        if (typeof url !== 'string') {
            return '';
        }
        const cleanUrl = url.split(/[?#]/)[0];
        const match = cleanUrl.match(/\/?([^/?#]+)\.webp$/i);
        if (!match) {
            return '';
        }
        return fallbackExtensions[match[1]] || '';
    };

    const resolveAssetPath = (url) => {
        if (supportsWebP || typeof url !== 'string' || !/\.webp(?:[?#]|$)/i.test(url)) {
            return url;
        }
        const extension = getFallbackExtension(url);
        if (!extension) {
            return url;
        }
        return url.replace(/\.webp(?=([?#].*)?$)/i, `.${extension}`);
    };

    const resolveSrcset = (srcset) => {
        if (supportsWebP || typeof srcset !== 'string' || srcset.indexOf('.webp') === -1) {
            return srcset;
        }
        return srcset
            .split(',')
            .map((candidate) => {
                const trimmed = candidate.trim();
                if (!trimmed) {
                    return trimmed;
                }
                const parts = trimmed.split(/\s+/);
                parts[0] = resolveAssetPath(parts[0]);
                return parts.join(' ');
            })
            .join(', ');
    };

    const updateImageCandidate = (element, attributeName) => {
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
    };

    const applyImageFallbacks = (root = document) => {
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
    };

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

    window.YumehoCompat = {
        applyImageFallbacks,
        resolveAssetPath,
        resolveSrcset,
        supportsWebP
    };
})(window, document);

document.addEventListener('DOMContentLoaded', () => {
    if (window.YumehoCompat) {
        window.YumehoCompat.applyImageFallbacks();
    }

    let latestSimulationInput = null;
    const resolveCurrentDirectoryPath = () => {
        const pathname = window.location.pathname || '/';
        if (pathname.endsWith('/')) {
            return pathname === '/' ? '' : pathname.replace(/\/$/, '');
        }
        const slashIndex = pathname.lastIndexOf('/');
        if (slashIndex <= 0) {
            return '';
        }
        return pathname.slice(0, slashIndex);
    };
    const resolveApiBase = () => {
        if (typeof window.YUMEHO_API_BASE === 'string' && window.YUMEHO_API_BASE.trim()) {
            return window.YUMEHO_API_BASE.trim().replace(/\/$/, '');
        }
        return `${window.location.origin}${resolveCurrentDirectoryPath()}`;
    };
    const readFieldValue = (id) => {
        const input = document.getElementById(id);
        return input ? input.value.trim() : '';
    };
    const focusField = (id) => {
        const input = document.getElementById(id);
        if (input && typeof input.focus === 'function') {
            input.focus();
        }
    };
    const isValidEmail = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    const isValidPhone = (value) => /^[0-9+\-()\s]{8,20}$/.test(value);
    const collectCustomerInfo = () => {
        const customer = {
            facilityName: readFieldValue('customerFacility'),
            contactName: readFieldValue('customerName'),
            email: readFieldValue('customerEmail'),
            phone: readFieldValue('customerPhone'),
            note: readFieldValue('customerNote')
        };

        if (!customer.facilityName) {
            focusField('customerFacility');
            throw new Error('施設名・医療機関名を入力してください。');
        }
        if (!customer.contactName) {
            focusField('customerName');
            throw new Error('担当者様 氏名を入力してください。');
        }
        if (!customer.email) {
            focusField('customerEmail');
            throw new Error('メールアドレスを入力してください。');
        }
        if (!isValidEmail(customer.email)) {
            focusField('customerEmail');
            throw new Error('メールアドレスの形式が不正です。');
        }
        if (!customer.phone) {
            focusField('customerPhone');
            throw new Error('電話番号を入力してください。');
        }
        if (!isValidPhone(customer.phone)) {
            focusField('customerPhone');
            throw new Error('電話番号の形式が不正です。');
        }

        return customer;
    };

    // SP: マーキー高さをCSS変数に反映
    function updateMarqueeHeight() {
        if (window.innerWidth <= 640) {
            const marquee = document.querySelector('.top-marquee');
            if (marquee) {
                const h = marquee.offsetHeight;
                document.documentElement.style.setProperty('--sp-marquee-h', h + 'px');
            }
        }
    }
    updateMarqueeHeight();
    window.addEventListener('resize', updateMarqueeHeight);

    // Mobile Menu Toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navMenu = document.querySelector('.nav-menu');
    const spDrawer = document.getElementById('spDrawer');

    function isSPMenu() { return window.innerWidth <= 860; }

    function openDrawer() {
        if (!spDrawer || !mobileMenuBtn) {
            return;
        }
        spDrawer.classList.add('open');
        spDrawer.setAttribute('aria-hidden', 'false');
        mobileMenuBtn.classList.add('is-open');
        mobileMenuBtn.setAttribute('aria-label', 'メニューを閉じる');
        mobileMenuBtn.setAttribute('aria-expanded', 'true');
        document.body.classList.add('sp-menu-open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        if (!spDrawer || !mobileMenuBtn) {
            return;
        }
        spDrawer.classList.remove('open');
        spDrawer.setAttribute('aria-hidden', 'true');
        mobileMenuBtn.classList.remove('is-open');
        mobileMenuBtn.setAttribute('aria-label', 'メニューを開く');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('sp-menu-open');
        document.body.style.overflow = '';
    }

    if (mobileMenuBtn) {
        let lastMenuToggleAt = 0;
        const toggleMenu = (event) => {
            const now = Date.now();
            if (now - lastMenuToggleAt < 320) {
                return;
            }
            lastMenuToggleAt = now;

            if (event && event.type === 'touchend') {
                event.preventDefault();
            }

            if (isSPMenu()) {
                spDrawer && spDrawer.classList.contains('open') ? closeDrawer() : openDrawer();
            } else if (navMenu) {
                navMenu.classList.toggle('open');
            }
        };

        mobileMenuBtn.setAttribute('role', 'button');
        mobileMenuBtn.setAttribute('tabindex', '0');
        mobileMenuBtn.setAttribute('aria-label', 'メニューを開く');
        mobileMenuBtn.setAttribute('aria-expanded', 'false');
        if (spDrawer) {
            mobileMenuBtn.setAttribute('aria-controls', 'spDrawer');
        }

        mobileMenuBtn.addEventListener('click', toggleMenu);
        mobileMenuBtn.addEventListener('touchend', toggleMenu, { passive: false });
        mobileMenuBtn.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleMenu();
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
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeDrawer();
    });

    // Tab Switching (for Cases page)
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    if (tabButtons.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-tab');

                // Remove active class from all buttons and contents
                tabButtons.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Add active class to clicked button and target content
                btn.classList.add('active');
                document.getElementById(targetId).classList.add('active');
            });
        });
    }

    // --- Simulation Logic ---
    window.selectOption = function (input) {
        // Visual selection state
        const name = input.name;
        document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
            radio.parentElement.classList.remove('selected');
        });
        if (input.checked) {
            input.parentElement.classList.add('selected');
        }

        if (name === 'installType') {
            renderRailLengthChoices(input.value);
        }
    };

    const railLengthField = document.getElementById('railLength');
    const railLengthVal = document.getElementById('railLengthVal');
    const railLengthNote = document.getElementById('railLengthNote');
    const getSelectedInstallType = () => {
        const checked = document.querySelector('input[name="installType"]:checked');
        return checked ? checked.value : '';
    };
    const getRailChoices = (installType) => {
        if (typeof YumehoPricing !== 'object' || !YumehoPricing || typeof YumehoPricing.getRailLengthChoices !== 'function') {
            return [];
        }
        return YumehoPricing.getRailLengthChoices(installType);
    };
    const updateRailLengthDisplay = (installType) => {
        if (!railLengthField) {
            return;
        }

        const effectiveInstallType = installType || getSelectedInstallType() || '天井直付型';
        const choices = getRailChoices(effectiveInstallType);
        const selectedValue = parseInt(railLengthField.value, 10);
        const selectedChoice = choices.find(choice => Number(choice.value) === selectedValue) || null;

        if (railLengthVal) {
            railLengthVal.textContent = selectedChoice ? `${selectedChoice.value}m` : '';
        }

        if (railLengthNote) {
            if (effectiveInstallType === 'スタンド型') {
                const noteList = choices.map(choice => choice.note || choice.label).join('、');
                railLengthNote.textContent = noteList
                    ? `※スタンド型の場合：${noteList}`
                    : '※スタンド型はカタログ掲載モデルから選択します。';
            } else {
                railLengthNote.textContent = selectedChoice
                    ? `※天井直付型はカタログ掲載の標準レール長から選択します。現在の選択は ${selectedChoice.label} です。`
                    : '※天井直付型はカタログ掲載の標準レール長から選択します。';
            }
        }
    };
    const renderRailLengthChoices = (installType) => {
        if (!railLengthField) {
            return;
        }

        const effectiveInstallType = installType || getSelectedInstallType() || '天井直付型';
        const choices = getRailChoices(effectiveInstallType);
        if (!Array.isArray(choices) || choices.length === 0) {
            return;
        }

        const previousValue = parseInt(railLengthField.value, 10);
        railLengthField.innerHTML = '';

        choices.forEach(choice => {
            const option = document.createElement('option');
            option.value = String(choice.value);
            option.textContent = choice.label || `${choice.value}m`;
            railLengthField.appendChild(option);
        });

        const hasPreviousValue = choices.some(choice => Number(choice.value) === previousValue);
        railLengthField.value = String(hasPreviousValue ? previousValue : choices[0].value);
        updateRailLengthDisplay(effectiveInstallType);
    };

    if (railLengthField) {
        railLengthField.addEventListener('change', () => {
            updateRailLengthDisplay(getSelectedInstallType() || '天井直付型');
        });
        renderRailLengthChoices(getSelectedInstallType() || '天井直付型');
    }

    window.nextStep = function (currentStep) {
        // Validation: Check if radio is selected
        if (currentStep === 1) {
            if (!document.querySelector('input[name="facilityType"]:checked')) {
                alert('施設種別を選択してください。');
                return;
            }
        }
        if (currentStep === 2) {
            if (!document.querySelector('input[name="installType"]:checked')) {
                alert('設置方式を選択してください。');
                return;
            }

            renderRailLengthChoices(getSelectedInstallType());
        }

        document.getElementById(`step${currentStep}`).classList.remove('active');
        document.getElementById(`step${currentStep + 1}`).classList.add('active');

        // Update indicator
        document.getElementById(`stepIndicator${currentStep}`).classList.remove('active');
        document.getElementById(`stepIndicator${currentStep}`).style.color = 'var(--primary-color)'; // Completed style (simplified)

        const nextInd = document.getElementById(`stepIndicator${currentStep + 1}`);
        if (nextInd) nextInd.classList.add('active');

        // ステップインジケーターが見える位置にスクロール
        const progress = document.querySelector('.sim-progress');
        if (progress) {
            const headerH = document.querySelector('.header')?.offsetHeight || 60;
            const top = progress.getBoundingClientRect().top + window.scrollY - headerH - 20;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    };

    window.prevStep = function (currentStep) {
        document.getElementById(`step${currentStep}`).classList.remove('active');
        document.getElementById(`step${currentStep - 1}`).classList.add('active');

        // Update indicator
        const currInd = document.getElementById(`stepIndicator${currentStep}`);
        if (currInd) currInd.classList.remove('active');

        document.getElementById(`stepIndicator${currentStep - 1}`).classList.add('active');

        // ステップインジケーターが見える位置にスクロール
        const progress = document.querySelector('.sim-progress');
        if (progress) {
            const headerH = document.querySelector('.header')?.offsetHeight || 60;
            const top = progress.getBoundingClientRect().top + window.scrollY - headerH - 20;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    };

    window.showResult = function () {
        document.getElementById('step3').classList.remove('active');
        document.getElementById('result').classList.add('active');

        // ステップインジケーターが見える位置にスクロール
        const progress = document.querySelector('.sim-progress');
        if (progress) {
            const headerH = document.querySelector('.header')?.offsetHeight || 60;
            const top = progress.getBoundingClientRect().top + window.scrollY - headerH - 20;
            window.scrollTo({ top, behavior: 'smooth' });
        }

        // Gather Data
        const facility = document.querySelector('input[name="facilityType"]:checked').value;
        const install = document.querySelector('input[name="installType"]:checked').value;
        const length = parseInt(document.getElementById('railLength').value, 10);
        const options = {};
        document.querySelectorAll('input[data-selection-type="checkbox"]').forEach(input => {
            if (input.checked && input.dataset.option) {
                options[input.dataset.option] = 1;
            }
        });
        document.querySelectorAll('select[name="optionQty"]').forEach(sel => {
            const qty = parseInt(sel.value, 10);
            if (qty > 0) options[sel.dataset.option] = qty;
        });
        const harnessCountEl = document.getElementById('harnessCount');
        const harnessCount = harnessCountEl ? parseInt(harnessCountEl.value, 10) : 0;
        latestSimulationInput = {
            facilityType: facility,
            installType: install,
            railLength: length,
            options: options,
            harnessCount: harnessCount
        };

        let quote;
        try {
            quote = YumehoPricing.calculateQuote(latestSimulationInput);
        } catch (error) {
            alert(error.message || '見積計算でエラーが発生しました。');
            return;
        }

        const formattedPrice = YumehoPricing.formatJPY(quote.totalExcludingTax);

        // Build Result List
        const resultList = document.getElementById('resultList');
        resultList.innerHTML = '';

        const addItem = (label, value, isPrice = false) => {
            const li = document.createElement('li');
            if (isPrice) {
                li.style.backgroundColor = '#fffbeb';
                li.style.border = '2px solid #fbbf24';
                li.style.marginTop = '16px';
                li.innerHTML = `<span>${label}</span><strong style="font-size: 1.5rem; color: #d97706;">${value}</strong>`;
            } else {
                li.innerHTML = `<span>${label}</span><strong>${value}</strong>`;
            }
            resultList.appendChild(li);
        };

        addItem('推奨モデル', quote.modelName);
        addItem('レール構成', quote.railLabel);
        addItem('免荷ユニット', '1台 (標準)');
        const harnessLabel = quote.harnessCount > 0
            ? '標準一式 + 追加 ' + quote.harnessCount + '着'
            : '一式 (Mサイズ/Lサイズ)';
        addItem('ハーネスセット', harnessLabel);

        const optionKeys = Object.keys(quote.options);
        if (optionKeys.length > 0) {
            const optionLabel = optionKeys.map(k => {
                const label = window.YumehoPricing && typeof window.YumehoPricing.getOptionLabel === 'function'
                    ? window.YumehoPricing.getOptionLabel(k)
                    : k;
                return label + ' ×' + quote.options[k];
            }).join('、');
            addItem('追加オプション', optionLabel);
        }

        // Add Price Item
        addItem('概算費用（税別）', `${formattedPrice} 〜`, true);

        // Suggestion Text
        const suggestion = document.createElement('li');
        suggestion.style.display = 'block';
        suggestion.style.marginTop = '16px';
        suggestion.style.border = 'none';
        suggestion.style.backgroundColor = '#f0f8ff';
        suggestion.style.padding = '16px';
        suggestion.style.fontSize = '0.9rem';

        let text = `${facility}様での導入ですね。`;
        if (install === '天井直付型') {
            text += `天井走行型は足元がフラットで、車椅子からの移乗や歩行訓練が最もスムーズに行えます。`;
        } else {
            text += `スタンド型はレイアウト変更にも対応しやすく、導入ハードルが低いのが特徴です。`;
        }
        suggestion.innerHTML = text;
        resultList.appendChild(suggestion);
    };

    window.handleStripePayment = async function (e) {
        e.preventDefault();

        if (!latestSimulationInput) {
            alert('先に診断結果を表示してください。');
            return;
        }

        let customerInfo;
        try {
            customerInfo = collectCustomerInfo();
        } catch (validationError) {
            const message = validationError && validationError.message
                ? validationError.message
                : 'お客様情報を確認してください。';
            alert(message);
            const paymentStatus = document.getElementById('paymentStatus');
            if (paymentStatus) {
                paymentStatus.textContent = message;
            }
            return;
        }

        if (window.location.protocol === 'file:') {
            const guide = 'このページは file:// では決済できません。`npm start` を実行し、http://localhost:3000/simulation.html で開いてください。';
            alert(guide);
            const paymentStatus = document.getElementById('paymentStatus');
            if (paymentStatus) {
                paymentStatus.textContent = 'file:// では決済APIに接続できません。http://localhost:3000 で開いてください。';
            }
            return;
        }

        const payNowBtn = document.getElementById('payNowBtn');
        const paymentStatus = document.getElementById('paymentStatus');
        const defaultBtnText = payNowBtn ? payNowBtn.textContent : 'この金額でオンライン決済へ進む';
        const endpoint = `${resolveApiBase()}/api/create-checkout-session.php`;

        if (payNowBtn) {
            payNowBtn.textContent = '決済ページを準備中...';
            payNowBtn.classList.add('disabled');
            payNowBtn.setAttribute('aria-disabled', 'true');
        }

        if (paymentStatus) {
            paymentStatus.textContent = '';
        }

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    facilityType: latestSimulationInput.facilityType,
                    installType: latestSimulationInput.installType,
                    railLength: latestSimulationInput.railLength,
                    options: latestSimulationInput.options,
                    customer: customerInfo
                })
            });
            const rawBody = await response.text();
            let data = {};
            if (rawBody) {
                try {
                    data = JSON.parse(rawBody);
                } catch (_parseError) {
                    throw new Error(`決済APIの応答形式が不正です（${endpoint}）。`);
                }
            }

            if (!response.ok) {
                throw new Error(data.error || `決済セッションの作成に失敗しました（HTTP ${response.status}）。`);
            }

            if (!data.url) {
                throw new Error('決済URLが取得できませんでした。');
            }

            if (paymentStatus && data.mode === 'mock') {
                paymentStatus.textContent = 'Stripe鍵未設定のためモック決済へ遷移します。';
            }

            window.location.href = data.url;
        } catch (error) {
            const rawMessage = error && error.message ? error.message : '決済処理でエラーが発生しました。';
            const isNetworkError =
                error instanceof TypeError ||
                /failed to fetch/i.test(rawMessage) ||
                /network/i.test(rawMessage);
            const userMessage = isNetworkError
                ? `決済APIに接続できません。サーバー起動後に http://localhost:3000/simulation.html で開いてください。接続先: ${endpoint}`
                : rawMessage;

            if (paymentStatus) {
                paymentStatus.textContent = userMessage;
            }
            alert(userMessage);

            if (payNowBtn) {
                payNowBtn.textContent = defaultBtnText;
                payNowBtn.classList.remove('disabled');
                payNowBtn.removeAttribute('aria-disabled');
            }
        }
    };

    // --- Contact Page Pre-fill Logic ---
    if (/(?:^|\/)contact(?:\.html)?\/?$/.test(window.location.pathname)) {
        const params = new URLSearchParams(window.location.search);
        const msg = params.get('msg');
        const type = params.get('tmptype');
        const facility = params.get('facility');
        const contactName = params.get('contact_name') || params.get('name');
        const email = params.get('email');
        const tel = params.get('tel');

        if (msg) {
            const textarea = document.getElementById('message');
            if (textarea) textarea.value = msg;
        }

        if (facility) {
            const facilityInput = document.getElementById('facility');
            if (facilityInput) facilityInput.value = facility;
        }
        if (contactName) {
            const nameInput = document.getElementById('contact_name')
                || document.getElementById('field_contact_name')
                || document.getElementById('name')
                || document.getElementById('field_name');
            if (nameInput) nameInput.value = contactName;
        }
        if (email) {
            const emailInput = document.getElementById('email');
            if (emailInput) emailInput.value = email;
        }
        if (tel) {
            const telInput = document.getElementById('tel');
            if (telInput) telInput.value = tel;
        }

        if (type) {
            const normalizedType = type.replace(/\s+/g, '');
            const radios = Array.from(
                document.querySelectorAll('input[name="tmptype"], input[name="inquiry_type"]')
            );
            radios.forEach((radio) => {
                const label = radio.closest('label');
                const labelText = label ? label.textContent.replace(/\s+/g, '') : '';
                radio.checked = labelText.indexOf(normalizedType) !== -1;
            });
        }
    }
    // --- Scroll Animations (Intersection Observer) ---
    const observerOptions = {
        threshold: 0.12,
        rootMargin: "0px 0px -40px 0px"
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                obs.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Target all animated elements including footer
    document.querySelectorAll(
        '.animate-on-scroll, .anim-left, .anim-right, .anim-scale, .img-reveal, .section-heading, .footer'
    ).forEach(el => observer.observe(el));

    // --- Stagger children in grids ---
    document.querySelectorAll('.feature-grid, .facility-grid, .editorial-grid, .voice-scroll').forEach(grid => {
        const gridObs = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    const children = entry.target.children;
                    Array.from(children).forEach((child, i) => {
                        child.style.transitionDelay = (i * 0.1) + 's';
                        child.classList.add('visible');
                    });
                    gridObs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        gridObs.observe(grid);
    });

    // --- Lazy-load autoplay videos below the fold ---
    const lazyVideos = document.querySelectorAll('video[data-lazy-video]');
    const hydrateLazyVideo = (video) => {
        if (!video || video.dataset.videoReady === '1') {
            return;
        }

        const sources = video.querySelectorAll('source[data-src]');
        if (sources.length) {
            sources.forEach((source) => {
                source.src = source.dataset.src;
                source.removeAttribute('data-src');
            });
        } else if (video.dataset.src) {
            video.src = video.dataset.src;
            video.removeAttribute('data-src');
        }

        video.load();
        video.dataset.videoReady = '1';

        const playPromise = video.play();
        if (playPromise && typeof playPromise.catch === 'function') {
            playPromise.catch(() => {});
        }
    };

    if (lazyVideos.length > 0) {
        if ('IntersectionObserver' in window) {
            const lazyVideoObserver = new IntersectionObserver((entries, obs) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    hydrateLazyVideo(entry.target);
                    obs.unobserve(entry.target);
                });
            }, { rootMargin: '240px 0px' });

            lazyVideos.forEach((video) => lazyVideoObserver.observe(video));
        } else {
            lazyVideos.forEach(hydrateLazyVideo);
        }
    }

    // --- Magnetic button effect ---
    if (matchMedia('(hover: hover)').matches) {
        document.querySelectorAll('.btn-primary.btn-lg, .btn-pulse').forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                btn.style.transform = `translate(${x * 0.15}px, ${y * 0.15}px)`;
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.transform = '';
            });
        });
    }

    // --- Smooth header hide/show on scroll ---
    let lastScrollY = 0;
    const header = document.querySelector('.header');
    if (header) {
        window.addEventListener('scroll', () => {
            const y = window.scrollY;
            if (y > 200) {
                header.style.transform = y > lastScrollY ? 'translateY(-100%)' : 'translateY(0)';
                header.style.transition = 'transform 0.35s cubic-bezier(0.16,1,0.3,1)';
            } else {
                header.style.transform = 'translateY(0)';
            }
            lastScrollY = y;
        }, { passive: true });
    }

    // ── SP: JS-controlled horizontal scroll snap ──
    // dev-voice-insights の CSS snap を JS 制御に置き換え
    ['#devInsights'].forEach(function(sel) {
        var container = document.querySelector(sel);
        if (!container) return;

        // CSS snap を無効化 / overflow を確実に有効化
        container.style.scrollSnapType = 'none';
        container.style.webkitOverflowScrolling = 'auto';
        container.style.overflowX = 'auto';

        var cards = Array.prototype.slice.call(container.children);
        if (!cards.length) return;
        var currentIndex = 0;

        // ドットインジケーター
        var dots = document.querySelectorAll('#devInsightsDots .dev-insights-dot');
        function updateDots(index) {
            if (!dots || !dots.length) return;
            dots.forEach(function(d, i) {
                d.classList.toggle('dev-insights-dot--active', i === index);
            });
        }

        function getCardPos(index) {
            if (index < 0) index = 0;
            if (index >= cards.length) index = cards.length - 1;
            var cr = cards[index].getBoundingClientRect();
            var tr = container.getBoundingClientRect();
            return container.scrollLeft + cr.left - tr.left;
        }

        function snapTo(index) {
            if (index < 0) index = 0;
            if (index >= cards.length) index = cards.length - 1;
            currentIndex = index;
            updateDots(currentIndex);
            container.scrollTo({ left: getCardPos(index), behavior: 'smooth' });
        }

        // タッチ制御
        var touchStartX = 0, touchStartScroll = 0, isTouching = false;

        container.addEventListener('touchstart', function(e) {
            isTouching = true;
            touchStartX = e.touches[0].clientX;
            touchStartScroll = container.scrollLeft;
        }, { passive: true });

        container.addEventListener('touchmove', function(e) {
            if (!isTouching) return;
            container.scrollLeft = touchStartScroll + (touchStartX - e.touches[0].clientX);
        }, { passive: true });

        container.addEventListener('touchend', function(e) {
            if (!isTouching) return;
            isTouching = false;
            var dx = touchStartX - (e.changedTouches[0] ? e.changedTouches[0].clientX : touchStartX);
            if (dx > 40) snapTo(currentIndex + 1);
            else if (dx < -40) snapTo(currentIndex - 1);
            else snapTo(currentIndex);
        });

        // PC マウスドラッグ
        var isPointerFine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        if (isPointerFine) {
            var isDown = false, mouseStartX = 0, mouseScrollStart = 0;
            container.addEventListener('mousedown', function(e) {
                isDown = true;
                container.style.cursor = 'grabbing';
                mouseStartX = e.pageX;
                mouseScrollStart = container.scrollLeft;
                e.preventDefault();
            });
            window.addEventListener('mouseup', function(e) {
                if (!isDown) return;
                isDown = false;
                container.style.cursor = '';
                var dx = mouseStartX - e.pageX;
                if (dx > 40) snapTo(currentIndex + 1);
                else if (dx < -40) snapTo(currentIndex - 1);
                else snapTo(currentIndex);
            });
            window.addEventListener('mousemove', function(e) {
                if (!isDown) return;
                container.scrollLeft = mouseScrollStart - (e.pageX - mouseStartX);
            });
            container.style.cursor = 'grab';
        }
    });
});
