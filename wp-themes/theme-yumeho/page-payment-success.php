<?php
/**
 * Template Name: Payment Success
 *
 * @package YUMEHO
 */
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo esc_url( YUMEHO_URI . '/assets/img/favicon.png' ); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url( YUMEHO_URI . '/assets/img/favicon.png' ); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $yumeho_mica30_enabled = function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled(); ?>
    <div class="top-marquee" aria-hidden="true">
        <div class="marquee-track">
            <span>YUMEHO</span>
            <span>SAFETY FIRST</span>
            <span>GAIT REHABILITATION SYSTEM</span>
            <span>FOR HOSPITALS &amp; CARE FACILITIES</span>
            <span>YUMEHO</span>
            <span>SAFETY FIRST</span>
            <span>GAIT REHABILITATION SYSTEM</span>
            <span>FOR HOSPITALS &amp; CARE FACILITIES</span>
            <span>YUMEHO</span>
            <span>SAFETY FIRST</span>
            <span>GAIT REHABILITATION SYSTEM</span>
            <span>FOR HOSPITALS &amp; CARE FACILITIES</span>
            <span>YUMEHO</span>
            <span>SAFETY FIRST</span>
            <span>GAIT REHABILITATION SYSTEM</span>
            <span>FOR HOSPITALS &amp; CARE FACILITIES</span>
        </div>
    </div>

    <header class="header">
        <div class="container header-inner">
            <div class="header-brand">
                <a href="<?php echo esc_url( home_url('/') ); ?>" class="logo">YUMEHO</a>
                <div class="site-switcher">
                    <a href="<?php echo esc_url( yumeho_related_site_url( 'corporate' ) ); ?>" class="ss-link ss-corp">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        Corporate
                    </a>
                    <span class="ss-divider"></span>
                    <a href="<?php echo esc_url( home_url('/') ); ?>" class="ss-link ss-product active">YUMEHO</a>
                    <?php if ( $yumeho_mica30_enabled ) : ?>
                    <a href="<?php echo esc_url( yumeho_related_site_url( 'mica30' ) ); ?>" class="ss-link ss-product">MICA30</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mobile-menu-btn">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <nav class="header-nav">
                <ul class="nav-menu">
                    <li><a href="<?php echo esc_url( home_url('/') ); ?>" class="nav-link">トップ</a></li>
                    <li><a href="<?php echo esc_url( home_url('/product/') ); ?>" class="nav-link">製品紹介</a></li>
                    <li><a href="<?php echo esc_url( home_url('/simulation/') ); ?>" class="nav-link">導入シミュレーション</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="btn btn-primary" style="white-space: nowrap;">資料請求・お問い合わせ</a>
            </div>
        </div>
    </header>
    <!-- SP ドロワーメニュー -->
    <div class="sp-drawer" id="spDrawer" aria-hidden="true">
        <nav class="sp-drawer__nav">
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="sp-drawer__link">トップ</a>
            <a href="<?php echo esc_url( home_url('/product/') ); ?>" class="sp-drawer__link">製品紹介</a>
            <a href="<?php echo esc_url( home_url('/simulation/') ); ?>" class="sp-drawer__link">導入シミュレーション</a>
            <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="sp-drawer__link">導入事例</a>
            <a href="<?php echo esc_url( home_url('/flow/') ); ?>" class="sp-drawer__link">導入フロー</a>
            <a href="<?php echo esc_url( home_url('/price/') ); ?>" class="sp-drawer__link">価格・見積</a>
            <a href="<?php echo esc_url( home_url('/subsidy/') ); ?>" class="sp-drawer__link">補助金ガイド</a>
            <a href="<?php echo esc_url( home_url('/faq/') ); ?>" class="sp-drawer__link">FAQ</a>
            <a href="<?php echo esc_url( home_url('/company/') ); ?>" class="sp-drawer__link">会社概要</a>
        </nav>
        <div class="sp-drawer__sites">
            <a href="<?php echo esc_url( yumeho_related_site_url( 'corporate' ) ); ?>" class="sp-drawer__site">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Corporate
            </a>
            <a href="<?php echo esc_url( home_url('/') ); ?>" class="sp-drawer__site sp-drawer__site--active">YUMEHO</a>
            <?php if ( $yumeho_mica30_enabled ) : ?>
            <a href="<?php echo esc_url( yumeho_related_site_url( 'mica30' ) ); ?>" class="sp-drawer__site">MICA30</a>
            <?php endif; ?>
        </div>
        <div class="sp-drawer__footer">
            <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="btn btn-primary sp-drawer__cta">資料請求・お問い合わせ</a>
        </div>
    </div>

    <section class="hero bg-light">
        <div class="container text-center">
            <h1 class="hero-title">ご注文を受け付けました</h1>
            <p class="hero-subtitle" id="paymentSummary">入金状況を確認しています...</p>
            <p id="paymentDetail" style="margin-bottom: 24px; color: #334155; font-size: 0.95rem;"></p>
            <div class="hero-actions">
                <button type="button" class="btn btn-secondary" id="refreshStatusBtn">入金状況を更新</button>
                <a href="<?php echo esc_url( home_url('/simulation/') ); ?>" class="btn btn-secondary">シミュレーションへ戻る</a>
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="btn btn-primary">追加相談をする</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="footer-cta-sp">
                    <span class="floating-cta__pulse"></span>
                    資料請求・お問い合わせ
                </a>
            <p class="copyright">&copy; 2026 YUMEHO All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        (function () {
            const params = new URLSearchParams(window.location.search);
            const sessionId = params.get('session_id');
            const mock = params.get('mock');
            const amount = params.get('amount');
            const summaryEl = document.getElementById('paymentSummary');
            const detailEl = document.getElementById('paymentDetail');
            const refreshBtn = document.getElementById('refreshStatusBtn');
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

            function renderMessage(statusData) {
                if (!summaryEl || !detailEl) return;

                if (mock === '1') {
                    summaryEl.textContent = 'モック決済のため即時完了として扱っています。';
                    detailEl.textContent = amount ? `決済金額: ¥${Number(amount).toLocaleString('ja-JP')}（税別）` : '';
                    return;
                }

                if (!statusData) {
                    summaryEl.textContent = '決済情報の確認ができませんでした。';
                    detailEl.textContent = '時間をおいて「入金状況を更新」を押してください。';
                    return;
                }

                if (statusData.paymentStatus === 'paid') {
                    summaryEl.textContent = '入金が確認できました。ありがとうございます。';
                } else if (statusData.paymentStatus === 'awaiting_bank_transfer' || statusData.stripePaymentStatus === 'unpaid') {
                    summaryEl.textContent = '銀行振込の入金待ちです。着金後に自動反映されます。';
                } else if (statusData.paymentStatus === 'failed') {
                    summaryEl.textContent = '決済は失敗しました。お手数ですが再度お試しください。';
                } else if (statusData.paymentStatus === 'expired') {
                    summaryEl.textContent = '決済期限が切れました。再度お手続きをお願いします。';
                } else {
                    summaryEl.textContent = 'お申し込みを受け付けました。入金状況を確認中です。';
                }

                const amountText = Number.isFinite(statusData.amountTotal)
                    ? `¥${Number(statusData.amountTotal).toLocaleString('ja-JP')}`
                    : '-';
                detailEl.textContent = `ステータス: ${statusData.paymentStatus || '-'} / 金額: ${amountText} / 更新時刻: ${statusData.updatedAt || '-'}`;
            }

            async function fetchPaymentStatus() {
                if (!sessionId || mock === '1') {
                    renderMessage(null);
                    return;
                }

                try {
                    const apiBase = resolveApiBase();
                    const statusUrl = `${apiBase}/api/payment-status.php?session_id=${encodeURIComponent(sessionId)}`;
                    const res = await fetch(statusUrl);
                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status}`);
                    }
                    const data = await res.json();
                    renderMessage(data);
                } catch (_err) {
                    renderMessage(null);
                }
            }

            if (refreshBtn) {
                refreshBtn.addEventListener('click', fetchPaymentStatus);
            }

            fetchPaymentStatus();
        }());
    </script>
    <?php wp_footer(); ?>
</body>

</html>
