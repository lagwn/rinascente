<?php
/**
 * Template Name: Payment Cancel
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
            <h1 class="hero-title">決済はキャンセルされました</h1>
            <p class="hero-subtitle">内容を見直して、再度お申し込みいただけます。</p>
            <div class="hero-actions">
                <a href="<?php echo esc_url( home_url('/simulation/') ); ?>" class="btn btn-primary">シミュレーションをやり直す</a>
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="btn btn-secondary">担当者に相談する</a>
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

    <?php wp_footer(); ?>
</body>

</html>
