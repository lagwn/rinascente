<?php
/**
 * Front Page Template — YUMEHO Homepage
 *
 * @package YUMEHO
 */
get_header();

$yumeho_mica30_enabled = function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled();
$catalog_context       = yumeho_shared_product_catalog_context( 'yumeho' );
$catalog_by_code       = $catalog_context['by_code'];
$roi_pgt9000           = $catalog_by_code['pgt-9000'] ?? array(
    'display_name'    => 'スタンド型 PGT-9000',
    'install_type'    => 'stand',
    'max_rail_length' => 14,
    'spec'            => '2000×4000mm / 総レール長14m',
);
$roi_pgt9001           = $catalog_by_code['pgt-9001'] ?? array(
    'display_name'    => 'スタンド型 PGT-9001',
    'install_type'    => 'stand',
    'max_rail_length' => 20,
    'spec'            => '2000×6000mm / 総レール長20m',
);
$roi_fcw3000           = $catalog_by_code['fcw-3000'] ?? array(
    'display_name' => '天井直付型 FCW-3000',
    'install_type' => 'ceiling',
    'spec'         => 'カスタム設計 / 周回・直線レール対応',
);
$roi_ceiling_basic     = ( $roi_fcw3000['display_name'] ?? '天井直付型 FCW-3000' ) . '（カスタム設計）';
$roi_ceiling_loop      = ( $roi_fcw3000['display_name'] ?? '天井直付型 FCW-3000' ) . '（周回レール）';
$roi_model_map         = array(
    'hospital'   => array( 'small' => $roi_ceiling_basic, 'large' => $roi_ceiling_basic, 'xl' => $roi_ceiling_loop ),
    'dayservice' => array( 'small' => yumeho_product_result_name( $roi_pgt9000 ), 'large' => $roi_ceiling_basic, 'xl' => $roi_ceiling_loop ),
    'nursing'    => array( 'small' => $roi_ceiling_basic, 'large' => $roi_ceiling_basic, 'xl' => $roi_ceiling_loop ),
    'clinic'     => array( 'small' => yumeho_product_result_name( $roi_pgt9000 ), 'large' => yumeho_product_result_name( $roi_pgt9001 ), 'xl' => $roi_ceiling_basic ),
);
$roi_default_reason    = '回復期リハ病棟で8名規模の訓練には、' . $roi_ceiling_basic . 'が最もコストパフォーマンスに優れています。';
$roi_wage_data         = function_exists( 'yumeho_get_roi_hourly_wage_data' ) ? yumeho_get_roi_hourly_wage_data() : array( 'hourly_wage' => 1323 );
$roi_hourly_wage       = (int) ( $roi_wage_data['hourly_wage'] ?? 1323 );
$roi_default_cost      = function_exists( 'yumeho_roi_cost_saving_man_yen' ) ? yumeho_roi_cost_saving_man_yen( $roi_hourly_wage, 8, 1, 240 ) : 254;
$roi_note_text         = function_exists( 'yumeho_roi_hourly_wage_note_text' ) ? yumeho_roi_hourly_wage_note_text( $roi_wage_data ) : '※ 上記は導入施設の実績に基づく概算です。時給は介護職員（医療・福祉施設等）の平均値（1,323円）で試算しています。';
$installation_map      = function_exists( 'yumeho_installation_map_context' )
    ? yumeho_installation_map_context( 'yumeho' )
    : array(
        'locations'        => array(),
        'site_count'       => 0,
        'prefecture_count' => 0,
    );
$installation_map_locations = is_array( $installation_map['locations'] ?? null ) ? $installation_map['locations'] : array();
$installation_site_count    = (int) ( $installation_map['site_count'] ?? 0 );
$installation_pref_count    = (int) ( $installation_map['prefecture_count'] ?? 0 );
$home_columns               = function_exists( 'yumeho_fetch_shared_columns' ) ? yumeho_fetch_shared_columns( 3 ) : array();
$home_field_note           = function_exists( 'yumeho_get_home_field_note_context' )
    ? yumeho_get_home_field_note_context( $home_columns )
    : array(
        'title'      => 'FIELD NOTE',
        'meta'       => 'PROPOSAL HIGHLIGHTS',
        'items'      => array(
            '天井直付型とスタンド型を環境別に提案',
            '体重免荷と追従制御で恐怖心を軽減',
            'ハーネスサイズを複数運用し準備を短縮',
            '計測オプションで訓練成果をチーム共有',
            '補助金申請に必要な資料作成もサポート',
        ),
        'url'        => home_url( '/simulation/' ),
        'link_label' => '3分で概算を確認する',
    );
?>

<style>
/* ── MV Full-screen Shrink Animation ── */
.mv-fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 99998;
    pointer-events: none;
    border-radius: 0px;
    overflow: hidden;
    will-change: top, left, width, height, border-radius;
    contain: layout style;
}
.mv-fullscreen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.mv-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 0 24px;
}
.mv-overlay::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,12,30,0.35) 0%, rgba(0,12,30,0.55) 50%, rgba(0,12,30,0.35) 100%);
    pointer-events: none;
}
.mv-overlay__kicker {
    position: relative;
    font-size: clamp(0.6rem, 1vw, 0.75rem);
    font-weight: 700;
    letter-spacing: 0.35em;
    color: rgba(255,255,255,0.6);
    margin-bottom: 20px;
    opacity: 0;
    transform: translateY(20px);
}
.mv-overlay__title {
    position: relative;
    font-size: clamp(1.6rem, 4.5vw, 3.2rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.5;
    letter-spacing: 0.04em;
    text-shadow: 0 2px 30px rgba(0,0,0,0.3);
    opacity: 0;
    transform: translateY(30px);
}
.mv-overlay__sub {
    position: relative;
    font-size: clamp(0.72rem, 1.2vw, 0.92rem);
    color: rgba(255,255,255,0.75);
    margin-top: 20px;
    line-height: 1.8;
    letter-spacing: 0.06em;
    opacity: 0;
    transform: translateY(20px);
}
.br-sp { display: none; }
@media (max-width: 640px) {
    .mv-overlay__title { font-size: clamp(1.3rem, 7vw, 1.7rem); }
    .mv-overlay__sub { font-size: 0.8rem; }
    .br-sp { display: block; }
}
.mv-overlay__line {
    position: relative;
    width: 40px;
    height: 2px;
    background: rgba(255,255,255,0.4);
    margin-top: 28px;
    opacity: 0;
    transform: scaleX(0);
}
body.mv-animating { overflow: hidden; }
body.mv-animating .feature-visual-wrap { visibility: hidden; }

/* ── Scroll Progress Bar ── */
.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), #00b4d8);
    z-index: 10000;
    transition: width 0.1s linear;
    border-radius: 0 2px 2px 0;
    box-shadow: 0 0 8px rgba(0,104,183,0.4);
}

/* ── Section Reveal ── */
.section-pre-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
}
.section-revealed {
    opacity: 1;
    transform: translateY(0);
}

/* ── Staggered Cards ── */
.facility-grid > *, .editorial-grid > * {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.stagger-visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

.ym-video-section {
    box-shadow: 0 -20px 60px rgba(0,0,0,0.08);
}

.feature-visual-wrap {
    transform-style: preserve-3d;
    will-change: transform;
}
.feature-overlay-card {
    transform: translateZ(30px);
}

/* ── Smart header ── */
.header {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                background 0.3s ease,
                box-shadow 0.3s ease;
}
.header--hidden { transform: translateY(-100%); }
.header--scrolled {
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    box-shadow: 0 1px 12px rgba(0,0,0,0.06);
}

.zf-impact__num[data-counted] { animation: numPulse 0.3s ease; }
@keyframes numPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.08); }
    100% { transform: scale(1); }
}
</style>

<!-- MV Fullscreen -->
<div class="mv-fullscreen" id="mvFullscreen">
    <picture style="display:block;width:100%;height:100%;">
        <source srcset="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.webp' ); ?>" type="image/webp">
        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.jpg' ); ?>" alt="" id="mvImage" fetchpriority="high" decoding="async">
    </picture>
    <div class="mv-overlay" id="mvOverlay">
        <p class="mv-overlay__kicker" id="mvKicker">GAIT REHABILITATION SYSTEM</p>
        <h2 class="mv-overlay__title" id="mvTitle">転倒の不安を取り除き、<br>「自分の足で歩く」<br class="br-sp">を支える。</h2>
        <p class="mv-overlay__sub" id="mvSub">介助3名 → 見守り1名。<br class="br-sp">訓練機会1.5倍。</p>
        <div class="mv-overlay__line" id="mvLine"></div>
    </div>
</div>

<script>
window.addEventListener('pagehide', function() {
    sessionStorage.setItem('yumeho_scrollY', window.scrollY || window.pageYOffset);
});
window.addEventListener('DOMContentLoaded', function(){
    var mvEl = document.getElementById('mvFullscreen');
    var mvImg = document.getElementById('mvImage');
    var target = document.querySelector('.feature-visual-wrap');
    if (!mvEl || !target) return;
    var isSP = window.innerWidth <= 640;
    function lockScroll() {
        if (!isSP) return;
        document.body.style.position = 'fixed';
        document.body.style.width = '100%';
        document.body.style.top = '0';
        document.body.style.overflow = 'hidden';
    }
    function unlockScroll() {
        if (!isSP) return;
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.top = '';
        document.body.style.overflow = '';
    }
    var prevScrollY = parseInt(sessionStorage.getItem('yumeho_scrollY') || '0', 10);
    sessionStorage.removeItem('yumeho_scrollY');
    var skipMV = prevScrollY > 50 || window.pageYOffset > 50 || document.documentElement.scrollTop > 50;
    if (skipMV) {
        document.body.classList.remove('mv-animating');
        mvEl.remove();
        return;
    }
    lockScroll();
    var kicker = document.getElementById('mvKicker');
    var title = document.getElementById('mvTitle');
    var sub = document.getElementById('mvSub');
    var line = document.getElementById('mvLine');
    var overlay = document.getElementById('mvOverlay');
    function startAnimation() {
        target.style.visibility = 'hidden';
        document.body.classList.remove('mv-animating');
        requestAnimationFrame(function(){
            var rect = target.getBoundingClientRect();
            var winW = window.innerWidth;
            var winH = window.innerHeight;
            document.body.classList.add('mv-animating');
            target.style.visibility = '';
            var ease = 'cubic-bezier(0.22, 1, 0.36, 1)';
            line.style.transition = 'opacity 0.5s ' + ease + ', transform 0.7s ' + ease;
            line.style.opacity = '1';
            line.style.transform = 'scaleX(1)';
            setTimeout(function(){
                kicker.style.transition = 'opacity 0.6s ' + ease + ', transform 0.6s ' + ease;
                kicker.style.opacity = '1';
                kicker.style.transform = 'translateY(0)';
            }, 120);
            setTimeout(function(){
                title.style.transition = 'opacity 0.7s ' + ease + ', transform 0.7s ' + ease;
                title.style.opacity = '1';
                title.style.transform = 'translateY(0)';
            }, 320);
            setTimeout(function(){
                sub.style.transition = 'opacity 0.6s ' + ease + ', transform 0.6s ' + ease;
                sub.style.opacity = '1';
                sub.style.transform = 'translateY(0)';
            }, 520);
            setTimeout(function(){
                var flyUp = 'opacity 0.5s ease-in, transform 0.6s ease-in';
                kicker.style.transition = flyUp;
                kicker.style.opacity = '0';
                kicker.style.transform = 'translateY(-30px)';
                title.style.transition = flyUp;
                title.style.opacity = '0';
                title.style.transform = 'translateY(-40px) scale(0.95)';
                sub.style.transition = flyUp;
                sub.style.opacity = '0';
                sub.style.transform = 'translateY(-25px)';
                line.style.transition = 'opacity 0.3s ease-in, transform 0.4s ease-in';
                line.style.opacity = '0';
                line.style.transform = 'scaleX(0)';
                overlay.style.transition = 'opacity 0.4s ease-in';
                overlay.style.opacity = '0';
                setTimeout(function(){
                    var timing = '0.75s cubic-bezier(0.4, 0, 0.0, 1)';
                    mvEl.style.transition = 'top '+timing+', left '+timing+', width '+timing+', height '+timing+', border-radius '+timing;
                    mvEl.style.top = rect.top + 'px';
                    mvEl.style.left = rect.left + 'px';
                    mvEl.style.width = rect.width + 'px';
                    mvEl.style.height = rect.height + 'px';
                    mvEl.style.borderRadius = '12px';
                    var done = false;
                    function finish() {
                        if (done) return;
                        done = true;
                        target.style.visibility = 'visible';
                        document.body.classList.remove('mv-animating');
                        unlockScroll();
                        mvEl.style.transition = 'opacity 0.2s ease';
                        mvEl.style.opacity = '0';
                        setTimeout(function(){ mvEl.remove(); }, 200);
                    }
                    mvEl.addEventListener('transitionend', function handler(e) {
                        if (e.propertyName !== 'width') return;
                        mvEl.removeEventListener('transitionend', handler);
                        finish();
                    });
                    setTimeout(finish, 900);
                }, 140);
            }, isSP ? 1600 : 1750);
        });
    }
    if (mvImg.complete && mvImg.naturalWidth > 0) {
        startAnimation();
    } else {
        mvImg.onload = startAnimation;
    }
});
</script>

<main>
    <!-- Hero Editorial -->
    <section class="hero hero-editorial">
        <div class="container hero-editorial-grid">
            <aside class="hero-sticker-rail animate-on-scroll delay-200" aria-label="機能ステッカー">
                <figure class="sticker-note icon-pop">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_safety.webp?v=20260413b' ); ?>" alt="転倒予防" decoding="async">
                    <figcaption>転倒予防</figcaption>
                </figure>
                <figure class="sticker-note icon-pop delay-100">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_rail.webp?v=20260413b' ); ?>" alt="導線設計" decoding="async">
                    <figcaption>導線設計</figcaption>
                </figure>
                <figure class="sticker-note icon-pop delay-200">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_gait.webp?v=20260413b' ); ?>" alt="歩行改善" decoding="async">
                    <figcaption>歩行改善</figcaption>
                </figure>
                <figure class="sticker-note icon-pop delay-300">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_report.webp?v=20260413b' ); ?>" alt="可視化" decoding="async">
                    <figcaption>可視化</figcaption>
                </figure>
            </aside>

            <article class="hero-feature animate-on-scroll">
                <p class="feature-kicker">YUMEHO EDITION 2026</p>
                <h1 class="hero-title">転倒の不安を取り除き、<br>「自分の足で歩く」を支える。</h1>
                    <p class="hero-subtitle">
                        介助3名 → 見守り1名。訓練機会1.5倍。<br>転倒を物理的に防ぐ設計で、スタッフも患者も安心できる<br class="br-pc">歩行<span class="text-no-wrap">リハビリ支援システム</span>。</p>
                <div class="hero-proof">
                    <div class="hero-proof__item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>「転倒ゼロで訓練量が増えた」<br>-- PT・リハ科長</span>
                    </div>
                    <div class="hero-proof__item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>全国<?php echo esc_html( number_format_i18n( $installation_site_count ) ); ?>施設で導入実績</span>
                    </div>
                </div>

                <div class="feature-visual-wrap hover-lift img-reveal">
                    <picture style="display:block;">
                        <source srcset="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.webp' ); ?>" type="image/webp">
                        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.jpg' ); ?>" alt="YUMEHOの導入シーン" class="feature-visual" decoding="async">
                    </picture>
                    <div class="feature-overlay-card">
                        <p class="overlay-kicker">導入現場レポート</p>
                        <h2>見守り1名体制で<br>歩行訓練の機会を1.5倍に</h2>
                        <p>デュアルレールとG-Suitの安全設計で介助負担を大幅に軽減。スタッフが指導に集中できる環境を構築。</p>
                    </div>
                </div>

                <div class="hero-actions">
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg btn-pulse">資料請求</a>
                    <a href="<?php echo esc_url( home_url( '/simulation/' ) ); ?>" class="btn btn-secondary btn-lg">導入シミュレーション</a>
                </div>
            </article>

                <div class="hero-side-col animate-on-scroll delay-300">
                    <aside class="hero-side-panel" aria-label="導入ポイント">
                        <div class="side-panel-head">
                        <p class="side-panel-title"><?php echo esc_html( (string) ( $home_field_note['title'] ?? 'FIELD NOTE' ) ); ?></p>
                        <p class="side-panel-date"><?php echo esc_html( (string) ( $home_field_note['meta'] ?? 'PROPOSAL HIGHLIGHTS' ) ); ?></p>
                    </div>
                    <ul class="side-checklist">
                        <?php foreach ( (array) ( $home_field_note['items'] ?? array() ) as $field_note_item ) : ?>
                        <li><?php echo esc_html( (string) $field_note_item ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo esc_url( (string) ( $home_field_note['url'] ?? home_url( '/simulation/' ) ) ); ?>" class="btn btn-tertiary"><?php echo esc_html( (string) ( $home_field_note['link_label'] ?? '3分で概算を確認する' ) ); ?></a>
                </aside>

                <nav class="hero-site-nav" aria-label="サイト切替">
                    <p class="hero-site-nav__label">SITE</p>
                    <a href="<?php echo esc_url( yumeho_related_site_url( 'corporate' ) ); ?>" class="hero-site-nav__item hero-site-nav__item--corp">
                        <span class="hero-site-nav__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        </span>
                        <span class="hero-site-nav__name">Rinascente</span>
                        <span class="hero-site-nav__sub">Corporate</span>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="hero-site-nav__item hero-site-nav__item--active">
                        <span class="hero-site-nav__icon">&rarr;</span>
                        <span class="hero-site-nav__name">YUMEHO</span>
                        <span class="hero-site-nav__sub">歩行リハビリ支援</span>
                    </a>
                    <?php if ( $yumeho_mica30_enabled ) : ?>
                    <a href="<?php echo esc_url( yumeho_related_site_url( 'mica30' ) ); ?>" class="hero-site-nav__item">
                        <span class="hero-site-nav__icon">&rarr;</span>
                        <span class="hero-site-nav__name">MICA30</span>
                        <span class="hero-site-nav__sub">造影剤注入装置</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </section>

    <!-- SP only: sticker icons above video -->
    <div class="sticker-icons-sp" aria-label="機能ステッカー">
        <figure class="sticker-note">
            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_safety.webp?v=20260413b' ); ?>" alt="転倒予防" decoding="async">
            <figcaption>転倒予防</figcaption>
        </figure>
        <figure class="sticker-note">
            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_rail.webp?v=20260413b' ); ?>" alt="導線設計" decoding="async">
            <figcaption>導線設計</figcaption>
        </figure>
        <figure class="sticker-note">
            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_gait.webp?v=20260413b' ); ?>" alt="歩行改善" decoding="async">
            <figcaption>歩行改善</figcaption>
        </figure>
        <figure class="sticker-note">
            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/sticker_report.webp?v=20260413b' ); ?>" alt="可視化" decoding="async">
            <figcaption>可視化</figcaption>
        </figure>
    </div>

    <!-- Video Feature -->
    <section class="ym-video-section">
        <div class="ym-video-wrap">
            <video class="ym-video-bg" autoplay muted loop playsinline preload="none" poster="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.jpg' ); ?>" aria-hidden="true" data-lazy-video>
                <source data-src="<?php echo esc_url( YUMEHO_URI . '/assets/movie/yumeho_short_lite.mp4' ); ?>" type="video/mp4">
            </video>
            <div class="ym-video-overlay"></div>
            <div class="ym-video-content">
                <p class="ym-video-kicker">PRODUCT FILM</p>
                <h2 class="ym-video-title">YUMEHOの世界を、<br>映像で体感する。</h2>
                <p class="ym-video-desc">「転倒が怖くて、一歩が踏み出せない」--<br>その声に応えたいという想いから、<br class="br-sp">YUMEHOは生まれました。<br>現場のリアルと開発者の思いを、<br class="br-sp">映像でお伝えします。</p>
                <button class="ym-play-btn" id="ymPlayBtn" aria-label="フル動画を再生">
                    <span class="ym-play-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="28" height="28"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                    <span>フル動画を見る</span>
                </button>
            </div>
            <div class="ym-video-badge">FULL VER. 5 min</div>
        </div>
    </section>

    <!-- Video Modal -->
    <div class="ym-modal" id="ymModal" role="dialog" aria-modal="true" aria-label="YUMEHO フル動画">
        <div class="ym-modal-backdrop" id="ymModalBackdrop"></div>
        <div class="ym-modal-inner">
            <button class="ym-modal-close" id="ymModalClose" aria-label="閉じる">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="28" height="28"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
            <video class="ym-modal-video" id="ymModalVideo" controls preload="none" poster="<?php echo esc_url( YUMEHO_URI . '/assets/img/hero_visual.jpg' ); ?>" data-src="<?php echo esc_url( YUMEHO_URI . '/assets/movie/yumeho_long_lite.mp4' ); ?>"></video>
        </div>
    </div>

    <script>
    (function(){
        const btn   = document.getElementById('ymPlayBtn');
        const modal = document.getElementById('ymModal');
        const vid   = document.getElementById('ymModalVideo');
        const close = document.getElementById('ymModalClose');
        const back  = document.getElementById('ymModalBackdrop');
        function prepareModalVideo(startTime) {
            const source = vid.getAttribute('data-src');
            if (source && !vid.getAttribute('src')) {
                vid.setAttribute('src', source);
                vid.load();
            }

            if (typeof startTime === 'number' && Number.isFinite(startTime)) {
                const setStartTime = function() {
                    try {
                        vid.currentTime = startTime;
                    } catch (error) {
                        // Ignore seek timing errors until metadata is ready.
                    }
                };
                if (vid.readyState >= 1) {
                    setStartTime();
                } else {
                    vid.addEventListener('loadedmetadata', setStartTime, { once: true });
                }
            }
        }
        function openModal(startTime) {
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            prepareModalVideo(startTime);
            const playPromise = vid.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(function(){});
            }
        }
        function closeModal() { modal.classList.remove('open'); document.body.style.overflow = ''; vid.pause(); vid.currentTime = 0; }
        window.openYumehoModalVideo = openModal;
        btn.addEventListener('click', openModal);
        close.addEventListener('click', closeModal);
        back.addEventListener('click', closeModal);
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
        document.querySelector('.ym-video-wrap').addEventListener('click', function(e){ if (!e.target.closest('.ym-play-btn')) openModal(); });
    })();
    </script>

    <!-- Features Section -->
    <section class="section section-features">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Why YUMEHO</p>
                <h2 class="section-title">YUMEHOが選ばれる3つの理由</h2>
            </div>
            <div class="zigzag-feature anim-left">
                <div class="zigzag-feature__img img-reveal">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/feature_safety.webp' ); ?>" alt="安全性" loading="lazy" decoding="async">
                </div>
                <div class="zigzag-feature__body">
                    <p class="feature-number">01</p>
                    <div class="zf-impact">
                        <div class="zf-impact__hero">
                            <span class="zf-impact__pre">転倒事故</span>
                            <span class="zf-impact__num">0</span>
                            <span class="zf-impact__unit">件</span>
                        </div>
                        <span class="zf-impact__note">*導入施設での実績</span>
                    </div>
                    <h3>安全性<br><span class="zigzag-feature__sub">転倒リスクを抑え、安心して訓練量を確保</span></h3>
                    <p>デュアルレールと免荷追従機構で転倒を物理的に防止。患者様が恐怖心なく一歩を踏み出せる環境をつくり、見守りスタッフの精神的負担も大幅に軽減します。</p>
                </div>
            </div>
            <div class="zigzag-feature zigzag-feature--reverse anim-right">
                <div class="zigzag-feature__img img-reveal">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/feature_operation02.webp' ); ?>" alt="運用性" loading="lazy" decoding="async">
                </div>
                <div class="zigzag-feature__body">
                    <p class="feature-number">02</p>
                    <div class="zf-impact">
                        <div class="zf-impact__hero">
                            <span class="zf-impact__pre">介助</span>
                            <span class="zf-impact__num zf-impact__num--strike">3</span>
                            <span class="zf-impact__arrow">&rarr;</span>
                            <span class="zf-impact__num zf-impact__num--accent">1</span>
                            <span class="zf-impact__unit">名</span>
                        </div>
                    </div>
                    <h3>運用性<br><span class="zigzag-feature__sub">見守り負担を減らし、訓練機会を増やす</span></h3>
                    <p>G-Suitの簡単な装着とレール追従の安全設計で、介助2〜3名体制から見守り1名体制へ。同じスタッフ数でより多くの患者様にリハビリを提供できます。</p>
                </div>
            </div>
            <div class="zigzag-feature anim-left">
                <div class="zigzag-feature__img img-reveal">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/feature_continuity.webp' ); ?>" alt="継続性" loading="lazy" decoding="async">
                </div>
                <div class="zigzag-feature__body">
                    <p class="feature-number">03</p>
                    <div class="zf-impact">
                        <div class="zf-impact__hero">
                            <span class="zf-impact__pre">訓練機会</span>
                            <span class="zf-impact__num zf-impact__num--accent">1.5</span>
                            <span class="zf-impact__unit">倍</span>
                        </div>
                    </div>
                    <h3>継続性<br><span class="zigzag-feature__sub">プレイ型リハビリで参加意欲を引き出す</span></h3>
                    <p>両手がフリーになるため、ボール投げやバドミントンなどの「遊び」を取り入れたリハビリが実現。楽しさが継続率を高め、利用者様の自発的な参加を促します。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Technology: Rail Mechanism -->
    <section class="section tc" style="background:#fff;">
        <div class="container">
            <div class="section-heading" style="text-align:center;">
                <p class="section-kicker">Technology</p>
                <h2 class="section-title">なぜ「天井走行式デュアルレール」<br class="br-sp">なのか</h2>
            </div>
            <div class="tc-lead animate-on-scroll">
                <p class="tc-lead__main">据置型フレームの中を往復するだけの歩行訓練は、<br>もう過去のものです。</p>
                <p class="tc-lead__sub">ゴムサスペンション方式の制約を、天井走行式デュアルレールが根本から解消します。</p>
            </div>
            <div class="tc-strip animate-on-scroll">
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg></div>
                    <div class="tc-strip__old">最大 4m</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new">距離<strong>無制限</strong></div><div class="tc-strip__label">歩行距離</div>
                </div>
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M22 12A10 10 0 1112 2"/><path d="M22 2L12 12"/></svg></div>
                    <div class="tc-strip__old">直線のみ</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new"><strong>曲線・分岐</strong></div><div class="tc-strip__label">動線設計</div>
                </div>
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                    <div class="tc-strip__old">ゴム弾力</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new"><strong>即時ブレーキ</strong></div><div class="tc-strip__label">転倒防止</div>
                </div>
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg></div>
                    <div class="tc-strip__old">荷重変動</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new"><strong>精密追従</strong></div><div class="tc-strip__label">免荷制御</div>
                </div>
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 000-7.78z"/></svg></div>
                    <div class="tc-strip__old">手すり必須</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new"><strong>両手フリー</strong></div><div class="tc-strip__label">動作自由度</div>
                </div>
                <div class="tc-strip__item">
                    <div class="tc-strip__icon"><svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></div>
                    <div class="tc-strip__old">1〜数名</div><span class="tc-strip__arrow">&#9660;</span>
                    <div class="tc-strip__new"><strong>複数同時</strong></div><div class="tc-strip__label">同時利用</div>
                </div>
            </div>
            <div class="tc-features">
                <article class="tc-feat tc-feat--wide animate-on-scroll">
                    <div class="tc-feat__img img-reveal"><img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/dual.jpg' ); ?>" alt="デュアルレール機構" loading="lazy"></div>
                    <div class="tc-feat__body"><span class="tc-feat__num">01</span><h3>デュアルレール走行機構</h3><p>2本の精密アルミレールの間をランナーが走行。曲線・分岐・周回レイアウトに対応し、施設の形状に合わせた自由な歩行動線を設計。直線4mで折り返す据置型とは、根本的に異なるアプローチです。</p></div>
                </article>
                <article class="tc-feat animate-on-scroll">
                    <div class="tc-feat__img img-reveal"><img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/rail.webp' ); ?>" alt="免荷追従+ブレーキ" loading="lazy" decoding="async"></div>
                    <div class="tc-feat__body"><span class="tc-feat__num">02</span><h3>免荷追従 + 即時ブレーキ</h3><p>歩行者に追従しながら体重を免荷。急な姿勢崩れにはブレーキが瞬時に作動し、落下を物理的に阻止します。</p></div>
                </article>
                <article class="tc-feat animate-on-scroll">
                    <div class="tc-feat__img img-reveal"><img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/gsuit_closeup.jpg' ); ?>" alt="G-Suit" loading="lazy"></div>
                    <div class="tc-feat__body"><span class="tc-feat__num">03</span><h3>G-Suit ハーネス</h3><p>股間部の圧迫を排除した独自設計。長時間の訓練でも快適で、「もう一度歩きたい」の意欲を保ちます。</p></div>
                </article>
            </div>
            <div class="text-center" style="margin-top:8px;"><a href="<?php echo esc_url( home_url( '/product/' ) ); ?>" class="btn btn-secondary">技術詳細を見る &rarr;</a></div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Facilities</p>
                <h2 class="section-title">想定導入施設</h2>
            </div>
            <div class="facility-grid">
                <article class="facility-card animate-on-scroll hover-lift" style="padding:0;overflow:hidden;">
                    <div style="overflow:hidden;">
                        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/AdobeStock_306631837.jpeg' ); ?>" alt="病院での歩行リハビリ" style="width:100%;height:180px;object-fit:cover;display:block;" loading="lazy">
                    </div>
                    <div style="padding:20px;">
                        <h3>病院（回復期・外来リハ）</h3>
                        <p>安全性と訓練効率の向上で、早期離床から応用歩行まで。スタッフ工数を削減しつつ訓練量を確保し、稟議に必要なエビデンスもサポートします。</p>
                    </div>
                </article>
                <article class="facility-card animate-on-scroll hover-lift delay-100" style="padding:0;overflow:hidden;">
                    <div style="overflow:hidden;">
                        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/AdobeStock_450772149.jpeg' ); ?>" alt="老健・特養でのリハビリ" style="width:100%;height:180px;object-fit:cover;display:block;" loading="lazy">
                    </div>
                    <div style="padding:20px;">
                        <h3>老健・特養</h3>
                        <p>集団運用で回る設計で、レクリエーション×機能訓練を両立。転倒事故予防とスタッフ負担軽減を同時に実現し、ご家族の安心にもつながります。</p>
                    </div>
                </article>
                <article class="facility-card animate-on-scroll hover-lift delay-200" style="padding:0;overflow:hidden;">
                    <div style="overflow:hidden;">
                        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/AdobeStock_1475829899.jpeg' ); ?>" alt="デイサービスでのリハビリ" style="width:100%;height:180px;object-fit:cover;display:block;" loading="lazy">
                    </div>
                    <div style="padding:20px;">
                        <h3>デイサービス</h3>
                        <p>プレイ型リハビリで利用者様の参加率と継続意欲を向上。「ここに来れば安全に歩ける」が口コミでの集客力につながります。</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section" style="background:#fff;">
        <div class="container">
            <div class="install-map-section animate-on-scroll">
                <div class="install-map-content">
                    <div class="section-heading" style="text-align:left;">
                        <p class="section-kicker">Nationwide</p>
                        <h2 class="section-title">全国の導入施設</h2>
                    </div>
                    <p class="install-map-lead">YUMEHOは全国の病院・介護施設・デイサービスで<br>ご利用いただいています。</p>
                    <div class="install-map-stats">
                        <div class="install-map-stat">
                            <span class="install-map-stat__num" id="installSiteCount"><?php echo esc_html( (string) $installation_site_count ); ?></span>
                            <span class="install-map-stat__unit">施設</span>
                            <span class="install-map-stat__label">導入実績</span>
                        </div>
                        <div class="install-map-stat">
                            <span class="install-map-stat__num" id="installPrefectureCount"><?php echo esc_html( (string) $installation_pref_count ); ?></span>
                            <span class="install-map-stat__unit">都道府県</span>
                            <span class="install-map-stat__label">設置エリア</span>
                        </div>
                    </div>
                    <a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>" class="btn btn-secondary" style="margin-top:20px;">導入事例を見る &rarr;</a>
                </div>
                <div class="install-map-wrap" id="installMapWrap">
                    <div class="install-map-container<?php echo ! empty( $installation_map_locations ) ? ' has-markers' : ' is-empty'; ?>" id="installMapFallback">
                        <picture>
                            <source srcset="<?php echo esc_url( YUMEHO_URI . '/assets/img/map_yumeho.webp' ); ?>" type="image/webp">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/map_yumeho.png' ); ?>" alt="YUMEHO 全国導入マップ" class="install-map-img" loading="lazy" decoding="async">
                        </picture>
                        <div class="install-map-marker-layer">
                            <?php foreach ( $installation_map_locations as $location ) : ?>
                                <?php if ( null === ( $location['map_left'] ?? null ) || null === ( $location['map_top'] ?? null ) ) : ?>
                                    <?php continue; ?>
                                <?php endif; ?>
                                <button
                                    type="button"
                                    class="map-pulse"
                                    style="left:<?php echo esc_attr( (string) $location['map_left'] ); ?>%;top:<?php echo esc_attr( (string) $location['map_top'] ); ?>%;"
                                    title="<?php echo esc_attr( (string) ( $location['info'] ?? $location['name'] ?? '' ) ); ?>"
                                    aria-label="<?php echo esc_attr( (string) ( $location['info'] ?? $location['name'] ?? '' ) ); ?>"
                                    data-map-name="<?php echo esc_attr( (string) ( $location['name'] ?? '' ) ); ?>"
                                    data-map-info="<?php echo esc_attr( (string) ( $location['info'] ?? '' ) ); ?>"
                                ></button>
                            <?php endforeach; ?>
                        </div>
                        <?php if ( empty( $installation_map_locations ) ) : ?>
                        <div class="install-map-empty">管理画面の「導入拠点」で施設を登録すると、ここにピンが表示されます。</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
    (function() {
        var mapWrap = document.getElementById('installMapWrap');
        if (!mapWrap) return;

        var pins = mapWrap.querySelectorAll('.map-pulse');
        if (!pins.length) return;

        var triggered = false;
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting || triggered) return;
                triggered = true;
                var arr = Array.from(pins);
                for (var i = arr.length - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var tmp = arr[i];
                    arr[i] = arr[j];
                    arr[j] = tmp;
                }
                arr.forEach(function(pin, i) {
                    setTimeout(function() { pin.classList.add('active'); }, i * 180);
                });
                observer.unobserve(mapWrap);
            });
        }, { threshold: 0.35 });

        observer.observe(mapWrap);
    })();
    </script>

    <!-- Voices from Facilities -->
    <section class="section" style="background:#fff;">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Voices</p>
                <h2 class="section-title">ご利用施設の声</h2>
            </div>
            <?php
            // 導入事例から最大6件を取得（おすすめ優先）
            $voice_query = new WP_Query( array(
                'post_type'      => 'case_study',
                'posts_per_page' => 6,
                'meta_query'     => array(
                    'relation' => 'OR',
                    array( 'key' => '_yumeho_case_is_hidden', 'value' => '1', 'compare' => '!=' ),
                    array( 'key' => '_yumeho_case_is_hidden', 'compare' => 'NOT EXISTS' ),
                ),
                'meta_key'       => '_yumeho_case_is_featured',
                'orderby'        => array(
                    'meta_value' => 'DESC',
                    'date'       => 'DESC',
                ),
            ) );
            ?>
            <?php if ( $voice_query->have_posts() ) : ?>
            <div class="voice-scroll-wrap">
                <div class="voice-scroll">
                    <?php while ( $voice_query->have_posts() ) : $voice_query->the_post();
                        $vid          = get_the_ID();
                        $facility     = get_post_meta( $vid, '_yumeho_case_facility_name', true );
                        $location     = get_post_meta( $vid, '_yumeho_case_location', true );
                        $change_txt   = get_post_meta( $vid, '_yumeho_case_change', true );
                        $metric_label = get_post_meta( $vid, '_yumeho_case_metric_1_label', true );
                        $metric_value = get_post_meta( $vid, '_yumeho_case_metric_1_value', true );
                        $speaker      = get_post_meta( $vid, '_yumeho_case_pullquote_speaker', true );
                        $thumb_url    = get_the_post_thumbnail_url( $vid, 'large' );
                        if ( ! $thumb_url ) {
                            $thumb_url = YUMEHO_URI . '/assets/img/case_hospital.webp';
                        }
                        // metric_value から HTML を除去して短くする
                        $metric_value_clean = function_exists( 'yumeho_case_plain_summary' ) ? yumeho_case_plain_summary( $metric_value ) : wp_strip_all_tags( str_replace( array( '<br>', '<br/>', '<br />' ), ' ', $metric_value ) );
                        $change_summary     = function_exists( 'yumeho_case_plain_summary' ) ? yumeho_case_plain_summary( $change_txt ) : wp_strip_all_tags( $change_txt );
                    ?>
                    <article class="voice-card animate-on-scroll">
                        <a href="<?php the_permalink(); ?>" class="voice-card__link" aria-label="<?php echo esc_attr( trim( ( $facility ?: get_the_title() ) . ' の導入事例を見る' ) ); ?>">
                        <div class="voice-card__media">
                            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $facility ); ?>" loading="lazy">
                        </div>
                        <div class="voice-card__body">
                            <?php if ( $metric_value_clean ) : ?>
                            <div class="voice-stat">
                                <span class="voice-stat__num"><?php echo esc_html( $metric_value_clean ); ?></span>
                                <?php if ( $metric_label ) : ?>
                                <span class="voice-stat__label"><?php echo esc_html( $metric_label ); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            <p class="voice-card__summary"><?php echo esc_html( $change_summary ); ?></p>
                            <div class="voice-card__person">
                                <div class="voice-card__person-row">
                                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/man.svg' ); ?>" alt="" class="voice-card__person-icon" loading="lazy">
                                    <div>
                                        <div class="voice-card__person-name"><?php echo esc_html( $speaker ?: $facility ); ?></div>
                                        <?php if ( $location || $facility ) : ?>
                                        <div class="voice-card__person-meta"><?php echo esc_html( trim( $location . ' ' . $facility ) ); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="voice-card__cta">
                                <span>導入事例の詳細を見る</span>
                                <span class="voice-card__cta-arrow" aria-hidden="true">&rarr;</span>
                            </div>
                        </div>
                        </a>
                    </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
            <?php endif; ?>
            <div class="text-center" style="margin-top:32px;">
                <a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>" class="btn btn-secondary">導入事例をすべて見る &rarr;</a>
            </div>
        </div>
    </section>

    <!-- Mid-page CTA Banner -->
    <section class="lp-mid-cta-y">
        <div class="container">
            <div class="lp-mid-cta-y__inner animate-on-scroll">
                <div class="lp-mid-cta-y__text">
                    <h3>まだ検討段階でも大丈夫。<br class="br-sp">まずは無料デモ体験から。</h3>
                    <p>実際のG-Suitを装着してレール走行を体感できます。<br class="br-sp">オンラインデモも対応可能です。</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary lp-mid-cta-y__btn">デモを予約する &rarr;</a>
            </div>
        </div>
    </section>

    <!-- Column Section -->
    <?php
    if ( ! empty( $home_columns ) ) :
    ?>
    <section class="section" style="background:#fff;">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Column</p>
                <h2 class="section-title">コラム</h2>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:32px;" class="yumeho-home-column-grid">
                <?php foreach ( $home_columns as $col ) :
                    $local_url = home_url( '/column/' . $col['slug'] . '/' );
                ?>
                <a href="<?php echo esc_url( $local_url ); ?>" class="animate-on-scroll" style="display:flex;flex-direction:column;background:#fff;border:1px solid var(--line-color);border-radius:12px;padding:28px 24px;text-decoration:none;color:inherit;transition:transform 0.3s,box-shadow 0.3s,border-color 0.3s;">
                    <?php if ( $col['category'] ) : ?>
                    <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.12em;color:var(--primary-color);margin-bottom:12px;text-transform:uppercase;"><?php echo esc_html( $col['category'] ); ?></div>
                    <?php endif; ?>
                    <h3 style="font-size:1.05rem;font-weight:700;line-height:1.55;color:var(--text-color);margin:0 0 14px;flex:1;"><?php echo esc_html( $col['title'] ); ?></h3>
                    <?php if ( $col['excerpt'] ) : ?>
                    <p style="font-size:0.85rem;line-height:1.75;color:rgba(0,0,0,0.65);margin:0 0 18px;display:-webkit-box;-webkit-line-clamp:3;line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;"><?php echo esc_html( $col['excerpt'] ); ?></p>
                    <?php endif; ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.75rem;color:rgba(0,0,0,0.5);padding-top:14px;border-top:1px solid var(--line-color);">
                        <span><?php echo esc_html( wp_date( 'Y.m.d', strtotime( $col['date'] ) ) ); ?></span>
                        <span style="font-weight:700;color:var(--primary-color);">続きを読む →</span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <div class="text-center" style="margin-top:32px;">
                <a href="<?php echo esc_url( home_url( '/column/' ) ); ?>" class="btn btn-secondary">コラムをすべて見る &rarr;</a>
            </div>
        </div>
        <style>
            @media (max-width: 860px) {
                .yumeho-home-column-grid { grid-template-columns: 1fr !important; }
            }
        </style>
    </section>
    <?php endif; ?>

    <!-- ROI Section -->
    <section class="section roi-home-section">
        <div class="roi-home-bg-deco" aria-hidden="true">
            <svg viewBox="0 0 1200 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="80" r="180" fill="rgba(0,104,183,0.025)"/>
                <circle cx="1100" cy="500" r="220" fill="rgba(0,104,183,0.02)"/>
                <line x1="200" y1="0" x2="800" y2="600" stroke="rgba(0,104,183,0.04)" stroke-width="1"/>
                <line x1="400" y1="0" x2="1000" y2="600" stroke="rgba(0,104,183,0.03)" stroke-width="1"/>
                <circle cx="600" cy="300" r="4" fill="rgba(0,104,183,0.12)"/>
                <circle cx="300" cy="150" r="3" fill="rgba(220,80,80,0.15)"/>
                <circle cx="900" cy="120" r="3" fill="rgba(0,104,183,0.1)"/>
                <circle cx="150" cy="400" r="2.5" fill="rgba(220,160,0,0.15)"/>
                <circle cx="1050" cy="250" r="2.5" fill="rgba(0,104,183,0.1)"/>
            </svg>
        </div>
        <div class="container">
            <div class="roi-home-header animate-on-scroll">
                <div class="section-heading">
                    <p class="section-kicker">ROI</p>
                    <h2 class="section-title">導入効果シミュレーション</h2>
                </div>
                <p class="roi-home-lead">YUMEHOの導入により期待される人件費削減効果と訓練機会増加のインパクトを試算します。</p>
            </div>

            <!-- Before / After comparison -->
            <div class="roi-home-comparison">
                <div class="roi-home-card roi-home-card--before animate-on-scroll">
                    <div class="roi-home-card__badge">BEFORE</div>
                    <div class="roi-home-card__items">
                        <div class="roi-home-item"><span class="roi-home-item__label">歩行訓練の見守り体制</span><span class="roi-home-item__value">介助スタッフ 2〜3名</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">1日あたりの訓練可能患者数</span><span class="roi-home-item__value">約8名</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">スタッフの身体的負担</span><span class="roi-home-item__value">高い（腰痛リスク）</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">転倒事故への不安</span><span class="roi-home-item__value">常時（精神的負担大）</span></div>
                    </div>
                </div>

                <div class="roi-home-arrow animate-on-scroll" aria-hidden="true">
                    <svg viewBox="0 0 48 48" width="48" height="48"><circle cx="24" cy="24" r="23" fill="var(--primary-color)" opacity="0.08"/><path d="M18 24h12M26 18l6 6-6 6" stroke="var(--primary-color)" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                <div class="roi-home-card roi-home-card--after animate-on-scroll delay-100">
                    <div class="roi-home-card__badge roi-home-card__badge--after">AFTER -- YUMEHO導入後</div>
                    <div class="roi-home-card__items">
                        <div class="roi-home-item"><span class="roi-home-item__label">歩行訓練の見守り体制</span><span class="roi-home-item__value roi-home-item__value--highlight">見守り 1名</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">1日あたりの訓練可能患者数</span><span class="roi-home-item__value roi-home-item__value--highlight">約12名（1.5倍）</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">スタッフの身体的負担</span><span class="roi-home-item__value roi-home-item__value--highlight">大幅軽減</span></div>
                        <div class="roi-home-item"><span class="roi-home-item__label">転倒事故への不安</span><span class="roi-home-item__value roi-home-item__value--highlight">ハーネスが物理的に防止</span></div>
                    </div>
                </div>
            </div>

            <div class="roi-sim" id="roiSim">
                <div class="section-heading" style="text-align:center;margin-bottom:clamp(28px,4vw,44px);">
                    <p class="section-kicker">Cost Reduction Simulator</p>
                    <h2 class="section-title">御施設の導入効果を試算する</h2>
                    <p style="font-size:.9rem;color:rgba(0,0,0,.45);margin-top:8px;">条件を選ぶだけで、人件費削減額・訓練機会増加数・推奨モデルが自動算出されます。</p>
                </div>

                <div class="roi-sim__inputs">
                    <label class="roi-sim__field">
                        <span class="roi-sim__field-label">施設タイプ</span>
                        <select id="roiFacility">
                            <option value="hospital">病院（回復期リハ病棟）</option>
                            <option value="dayservice">通所リハ / デイサービス</option>
                            <option value="nursing">介護老人保健施設</option>
                            <option value="clinic">クリニック / 診療所</option>
                        </select>
                    </label>
                    <label class="roi-sim__field">
                        <span class="roi-sim__field-label">1日の歩行訓練 対象者数</span>
                        <select id="roiPatients">
                            <option value="5">約5名</option>
                            <option value="8" selected>約8名</option>
                            <option value="12">約12名</option>
                            <option value="16">約16名</option>
                            <option value="20">20名以上</option>
                        </select>
                    </label>
                    <label class="roi-sim__field">
                        <span class="roi-sim__field-label">現在の見守りスタッフ数</span>
                        <select id="roiStaff">
                            <option value="2" selected>2名</option>
                            <option value="3">3名</option>
                            <option value="4">4名以上</option>
                        </select>
                    </label>
                    <label class="roi-sim__field">
                        <span class="roi-sim__field-label">年間稼働日数</span>
                        <select id="roiDays">
                            <option value="200">約200日</option>
                            <option value="240" selected>約240日</option>
                            <option value="280">約280日</option>
                            <option value="365">365日</option>
                        </select>
                    </label>
                </div>

                <div class="roi-sim__results" id="roiResults">
                    <div class="roi-sim__result-card roi-sim__result-card--primary">
                        <div class="roi-sim__result-icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4l5 7 5-7"/><path d="M12 11v9"/><path d="M7 14h10"/><path d="M7 17h10"/></svg>
                        </div>
                        <div class="roi-sim__result-label">年間 人件費削減額</div>
                        <div class="roi-sim__result-value" id="roiCostSaving">約<?php echo esc_html( number_format_i18n( $roi_default_cost ) ); ?>万円</div>
                        <div class="roi-sim__result-detail" id="roiCostDetail">時給<?php echo esc_html( number_format_i18n( $roi_hourly_wage ) ); ?>円 x 8h x 1名削減 x 240日</div>
                    </div>
                    <div class="roi-sim__result-card">
                        <div class="roi-sim__result-icon roi-sim__result-icon--teal">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="roi-sim__result-label">年間 訓練機会 増加数</div>
                        <div class="roi-sim__result-value" id="roiTraining">+960回</div>
                        <div class="roi-sim__result-detail" id="roiTrainingDetail">1日あたり +4名 x 240日</div>
                    </div>
                    <div class="roi-sim__result-card">
                        <div class="roi-sim__result-icon roi-sim__result-icon--green">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="roi-sim__result-label">転倒事故リスク</div>
                        <div class="roi-sim__result-value">大幅低減</div>
                        <div class="roi-sim__result-detail">ハーネスが物理的に転倒を防止</div>
                    </div>
                </div>

                <div class="roi-sim__recommend" id="roiRecommend">
                    <div class="roi-sim__recommend-badge">RECOMMENDED MODEL</div>
                    <div class="roi-sim__recommend-body">
                        <div class="roi-sim__recommend-name" id="roiModelName"><?php echo esc_html( $roi_ceiling_basic ); ?></div>
                        <div class="roi-sim__recommend-reason" id="roiModelReason"><?php echo esc_html( $roi_default_reason ); ?></div>
                    </div>
                    <a href="<?php echo esc_url( home_url( '/simulation/' ) ); ?>" class="btn btn-primary" style="white-space:nowrap;flex-shrink:0;">詳細シミュレーション &rarr;</a>
                </div>

                <p class="roi-home-note" id="roiHomeNote" style="margin-top:24px;"><?php echo esc_html( $roi_note_text ); ?></p>
            </div>

            <div class="roi-home-cta animate-on-scroll">
                <p>この導入効果を稟議資料にまとめたい方は</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">稟議用資料のご相談 &rarr;</a>
            </div>
        </div>
    </section>

    <!-- Developer's Voice -->
    <section class="section dev-voice-section">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">Developer's Voice</p>
                <h2 class="section-title">開発者の声</h2>
            </div>
            <div class="dev-voice-grid animate-on-scroll">
                <div class="dev-voice-main">
                    <div class="dev-voice-img-wrap img-reveal">
                        <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/developer_portrait.jpg' ); ?>" alt="YUMEHO開発者" class="dev-voice-img" loading="lazy">
                        <div class="dev-voice-img-overlay"></div>
                    </div>
                    <div class="dev-voice-body">
                        <svg class="dev-voice-quote-icon" viewBox="0 0 32 32" width="36" height="36" fill="none">
                            <path d="M6 18h6l-4 8h4l4-8V8H6v10zm14 0h6l-4 8h4l4-8V8H20v10z" fill="var(--primary-color)" opacity="0.15"/>
                        </svg>
                        <blockquote class="dev-voice-blockquote">
                            「自分の足で、歩きたい」--<br>
                            その願いに応えるために、<br class="sp-only">YUMEHOは生まれました。
                        </blockquote>
                        <p class="dev-voice-text">患者さんたちの願いは、もう一度自分の足で歩くこと。その想いに応えたくて、天井レールとG-Suitによる免荷追従機構を開発しました。転倒の恐怖を取り除き、安心して一歩を踏み出せる環境をつくること。それが私たちの原点です。</p>
                        <p class="dev-voice-text">娯楽に見えるかもしれませんが、ボール投げやボウリングなどの「遊び」を取り入れたリハビリは、身体活動に加えて認知活動にもつながります。笑顔と達成感が、回復への意欲を引き出す--そういう仕組みをつくりたかったのです。</p>
                        <div class="dev-voice-signature">
                            <div class="dev-voice-sig-line"></div>
                            <div>
                                <div class="dev-voice-sig-name">YUMEHO 開発チーム</div>
                                <div class="dev-voice-sig-title">Walkmate / VINIZ</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dev-insights-wrap">
                    <p class="dev-insights-hint" aria-hidden="true">スワイプして見る <span>&rarr;</span></p>
                    <div class="dev-voice-insights" id="devInsights">
                        <div class="dev-insight-card anim-left">
                            <div class="dev-insight-num">01</div>
                            <h3 class="dev-insight-title">安全への執念</h3>
                            <p>天井レールのブレーキング機構と手動制御装置を自ら実演しながら繰り返しテスト。「転倒を物理的に防ぐ」という設計思想は、一切の妥協なく磨き上げました。</p>
                        </div>
                        <div class="dev-insight-card anim-right">
                            <div class="dev-insight-num">02</div>
                            <h3 class="dev-insight-title">現場への寄り添い</h3>
                            <p>開発者自らG-Suitを装着し、患者様と同じ体験を重ねる。「日本の患者さんがもう一度歩けるように」--その願いを叶えるために、現場の声を設計に反映し続けています。</p>
                        </div>
                        <div class="dev-insight-card anim-left">
                            <div class="dev-insight-num">03</div>
                            <h3 class="dev-insight-title">リハビリの再定義</h3>
                            <p>「訓練」ではなく「遊び」の中に回復を組み込む。身体活動と認知活動を融合させたプレイ型リハビリで、利用者様の笑顔と達成感を回復への意欲に変えています。</p>
                        </div>
                    </div>
                    <div class="dev-insights-dots" id="devInsightsDots" aria-hidden="true">
                        <span class="dev-insights-dot dev-insights-dot--active"></span>
                        <span class="dev-insights-dot"></span>
                        <span class="dev-insights-dot"></span>
                    </div>
                </div>
            </div>
            <div class="dev-voice-video-link animate-on-scroll" style="margin-top:40px;">
                <button class="dev-voice-play-btn" id="devVoicePlayBtn">
                    <span class="dev-voice-play-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                    <span>開発者インタビューを動画で見る（5分）</span>
                </button>
            </div>
            <script>
            document.getElementById('devVoicePlayBtn')?.addEventListener('click', function(){
                if (typeof window.openYumehoModalVideo === 'function') {
                    window.openYumehoModalVideo(100);
                }
            });
            </script>
        </div>
    </section>

    <section class="section" style="background:#fff;">
        <div class="container">
            <div class="section-heading">
                <p class="section-kicker">FAQ</p>
                <h2 class="section-title">よくある質問</h2>
            </div>
            <div class="faq-mini" style="max-width:800px;margin:0 auto;">
                <?php
                $home_faq_query = new WP_Query(
                    array(
                        'post_type'      => 'faq',
                        'posts_per_page' => 6,
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                    )
                );

                if ( $home_faq_query->have_posts() ) :
                    $faq_index = 0;
                    while ( $home_faq_query->have_posts() ) :
                        $home_faq_query->the_post();
                        ?>
                <div class="faq-mini__item<?php echo 0 === $faq_index ? ' open' : ''; ?>" onclick="this.classList.toggle('open')">
                    <div class="faq-mini__q"><?php the_title(); ?></div>
                    <div class="faq-mini__a"><?php echo wp_strip_all_tags( get_the_content() ); ?></div>
                </div>
                        <?php
                        $faq_index++;
                    endwhile;
                    wp_reset_postdata();
                else :
                    ?>
                <div class="faq-mini__item open">
                    <div class="faq-mini__q">FAQは現在準備中です</div>
                    <div class="faq-mini__a">公開準備が整い次第、こちらに掲載します。</div>
                </div>
                <?php endif; ?>
                <div class="text-center" style="margin-top:28px;">
                    <a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>" class="btn btn-secondary">すべての質問を見る</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background:#f8fafe;">
        <div class="container" style="max-width:800px;">
            <?php if ( $yumeho_mica30_enabled ) : ?>
            <div class="animate-on-scroll cross-product-grid" style="background:#fff;border-radius:14px;padding:36px 40px;box-shadow:0 2px 20px rgba(0,0,0,0.05);display:grid;grid-template-columns:1fr 1.5fr;gap:32px;align-items:center;">
                <div>
                    <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.14em;color:rgba(0,95,115,0.6);text-transform:uppercase;margin-bottom:8px;">Another Product</p>
                    <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:10px;">MICA30 もご検討ですか？</h3>
                    <p style="font-size:0.85rem;line-height:1.7;color:rgba(0,0,0,0.85);">血管造影診断のための精密造影剤注入装置。リハビリ部門と放射線科の両方を持つ病院様に、セット導入のご提案も可能です。</p>
                    <a href="<?php echo esc_url( yumeho_related_site_url( 'mica30' ) ); ?>" class="btn btn-secondary" style="margin-top:16px;font-size:0.85rem;">MICA30 サイトへ &rarr;</a>
                </div>
                <div style="background:linear-gradient(135deg,rgba(0,95,115,0.04),rgba(0,95,115,0.08));border-radius:10px;padding:28px;text-align:center;">
                    <p style="font-family:var(--font-logo);font-size:2rem;font-weight:700;color:#005f73;letter-spacing:0.06em;">MICA30</p>
                    <p style="font-size:0.78rem;color:rgba(0,95,115,0.6);margin-top:4px;">多相電動式造影剤注入装置</p>
                    <div style="display:flex;justify-content:center;gap:16px;margin-top:16px;">
                        <div style="text-align:center;"><span style="font-family:var(--font-logo);font-size:1.3rem;font-weight:700;color:#005f73;">0.05</span><span style="font-size:0.7rem;color:#005f73;">mL</span><br><span style="font-size:0.65rem;color:rgba(0,0,0,0.7);">精密制御</span></div>
                        <div style="text-align:center;"><span style="font-family:var(--font-logo);font-size:1.3rem;font-weight:700;color:#005f73;">2</span><span style="font-size:0.7rem;color:#005f73;">系統</span><br><span style="font-size:0.65rem;color:rgba(0,0,0,0.7);">気泡センサー</span></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section section-cta">
        <div class="container cta-panel text-center animate-on-scroll">
            <p class="section-kicker">Get Started</p>
            <h2>まずは資料請求・デモ体験から。<br>貴施設に合った導入プランを<br class="br-sp">ご提案します。</h2>
            <p>仕様資料、導入事例集、稟議用サマリー、概算見積のご依頼を1つのフォームで承ります。</p>
            <div class="hero-actions">
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">資料請求・デモ依頼（無料）</a>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-secondary btn-lg">現地調査・見積相談</a>
            </div>
        </div>
    </section>

    <div class="scroll-progress" id="scrollProgress"></div>
    <script>
    window.addEventListener('scroll', () => {
        const h = document.documentElement;
        const pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
        document.getElementById('scrollProgress').style.width = pct + '%';
    });

    (() => {
        const $ = id => document.getElementById(id);
        const facility = $('roiFacility');
        const patients = $('roiPatients');
        const staff = $('roiStaff');
        const days = $('roiDays');
        if (!facility) return;

        const HOURLY_WAGE = <?php echo (int) $roi_hourly_wage; ?>;
        const HOURS_PER_DAY = 8;

        const models = <?php echo wp_json_encode( $roi_model_map, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>;

        const facilityLabels = {
            hospital: '回復期リハ病棟',
            dayservice: '通所リハ / デイサービス',
            nursing: '介護老人保健施設',
            clinic: 'クリニック / 診療所',
        };

        const reasons = {
            hospital: {
                small: '病棟内での短距離訓練に最適。省スペースで導入しやすい構成です。',
                large: '回復期リハ病棟の訓練規模に最適。コストパフォーマンスに優れた構成です。',
                xl: '大規模病棟の複数患者同時訓練に対応。周回動線で実歩行に近い訓練が可能です。',
            },
            dayservice: {
                small: 'スペースが限られる通所施設にはスタンド型がおすすめ。工事不要で導入できます。',
                large: '10名以上の訓練には天井型が効率的。曲線動線でバリエーション豊かな訓練が可能です。',
                xl: '大規模施設の利用者数に対応。天井型6mで十分な歩行距離を確保できます。',
            },
            nursing: {
                small: '小規模フロアでの訓練に適した構成。安全性を確保しながらコストを抑えられます。',
                large: 'フロアの形状に合わせたレイアウトが可能。介護スタッフの負担を大幅に軽減します。',
                xl: '複数フロアでの導入にも対応。施設全体のリハビリ体制を強化できます。',
            },
            clinic: {
                small: '工事不要のスタンド型でクリニックの限られたスペースに最適です。',
                large: '訓練者数が多い場合もスタンド型で対応可能。移動・設置が柔軟です。',
                xl: '天井型を導入することで本格的な歩行リハビリ環境を構築できます。',
            },
        };

        function calc() {
            const f = facility.value;
            const p = parseInt(patients.value, 10);
            const s = parseInt(staff.value, 10);
            const d = parseInt(days.value, 10);

            const reducedStaff = Math.max(s - 1, 1);
            const costSaving = reducedStaff * HOURLY_WAGE * HOURS_PER_DAY * d;
            const costManYen = Math.round(costSaving / 10000);
            const additionalPerDay = Math.round(p * 0.5);
            const trainingIncrease = additionalPerDay * d;
            const size = p <= 8 ? 'small' : p <= 14 ? 'large' : 'xl';
            const modelName = models[f][size];
            const reason = reasons[f][size];

            $('roiCostSaving').textContent = `約${costManYen}万円`;
            $('roiCostDetail').textContent = `時給${HOURLY_WAGE.toLocaleString()}円 x ${HOURS_PER_DAY}h x ${reducedStaff}名削減 x ${d}日`;
            $('roiTraining').textContent = `+${trainingIncrease.toLocaleString()}回`;
            $('roiTrainingDetail').textContent = `1日あたり +${additionalPerDay}名 x ${d}日`;
            $('roiModelName').textContent = modelName;
            $('roiModelReason').textContent = `${facilityLabels[f]}で${p}名規模の訓練には、${modelName}が${reason}`;
        }

        [facility, patients, staff, days].forEach(el => el.addEventListener('change', calc));
        calc();
    })();
    </script>
</main>

<?php get_footer(); ?>
