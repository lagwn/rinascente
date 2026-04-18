<?php
/**
 * Template Name: Member Login
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$member_product = function_exists( 'rinascente_member_requested_product' ) ? rinascente_member_requested_product() : '';
$member_context = function_exists( 'rinascente_member_context' ) ? rinascente_member_context( $member_product ) : array(
    'key'             => '',
    'is_yumeho'       => false,
    'product_label'   => 'Rinascente',
    'home_url'        => home_url( '/' ),
    'contact_url'     => home_url( '/contact/' ),
    'back_label'      => 'コーポレートサイトへ戻る',
    'logo_sub'        => 'Shared Member Site',
    'document_prefix' => 'Rinascente Member',
    'shared_note'     => '',
    'login_heading'   => '会員限定の<br><span style="color:var(--gold-light);">情報にアクセス。</span>',
    'login_lead'      => '会員の方は、製品の最新情報、詳細な技術仕様書、補助金申請サポート資料、セミナー・研修情報などにアクセスできます。',
    'login_form_note' => '',
    'contact_note'    => '会員登録がお済みでない方は、お問い合わせフォームよりお申し込みください。',
);
$rinascente_member_page_url = $member_product ? rinascente_member_product_page_url( $member_product ) : rinascente_member_page_url();
$rinascente_login_action_url = $member_product ? add_query_arg( 'product', $member_product, get_permalink() ) : get_permalink();
$requested_redirect = '';
if ( isset( $_REQUEST['redirect_to'] ) && is_string( $_REQUEST['redirect_to'] ) ) {
    $requested_redirect = wp_unslash( $_REQUEST['redirect_to'] );
}
$redirect_to = wp_validate_redirect( $requested_redirect, $rinascente_member_page_url );
if ( '' === $redirect_to ) {
    $redirect_to = $rinascente_member_page_url;
}

if ( ! empty( $member_context['document_prefix'] ) ) {
    add_filter(
        'pre_get_document_title',
        static function () use ( $member_context ) {
            return $member_context['document_prefix'] . ' ログイン | Rinascente Shared Member Site';
        }
    );
}

if ( is_user_logged_in() ) {
    wp_safe_redirect( $redirect_to, 303, 'Rinascente Member Login' );
    exit;
}

$login_identifier = '';
$login_error = '';
$login_notice = '';
if ( isset( $_GET['reset'] ) && 'success' === $_GET['reset'] ) {
    $login_notice = 'パスワードを更新しました。新しいパスワードでログインできます。';
}
if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['rinascente_member_login'] ) ) {
    $login_identifier = sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) );
    $password = (string) wp_unslash( $_POST['pwd'] ?? '' );

    if ( ! isset( $_POST['rinascente_member_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rinascente_member_login_nonce'] ) ), 'rinascente_member_login' ) ) {
        $login_error = 'セッションの有効期限が切れました。ページを再読み込みしてもう一度お試しください。';
    } elseif ( '' === $login_identifier || '' === $password ) {
        $login_error = 'IDとパスワードを入力してください。';
    } else {
        $user = wp_signon(
            array(
                'user_login'    => rinascente_resolve_login_identifier( $login_identifier ),
                'user_password' => $password,
                'remember'      => true,
            ),
            is_ssl()
        );

        if ( is_wp_error( $user ) ) {
            $error_code = $user->get_error_code();
            $login_error = 'too_many_attempts' === $error_code
                ? $user->get_error_message()
                : 'IDまたはパスワードが正しくありません。';
        } else {
            wp_safe_redirect( $redirect_to, 303, 'Rinascente Member Login' );
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ); ?>">
  <link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/img/favicon.png' ); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> style="background:var(--black);">
<?php wp_body_open(); ?>

  <header class="site-header" style="background:var(--black);border-bottom:1px solid rgba(255,255,255,0.07);">
    <div class="header-inner">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="header-logo">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="" class="header-logo__img">
        <span class="header-logo__text">
          <span class="logo-wordmark">Rinascente</span>
          <span class="logo-sub">復活する。再生する。</span>
        </span>
      </a>
      <button class="menu-toggle" aria-label="メニュー" aria-expanded="false">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      </button>
      <nav class="header-nav header-nav--desktop" role="navigation">
        <a href="<?php echo esc_url( home_url('/') ); ?>" class="nav-item">Home</a>
        <a href="<?php echo esc_url( home_url('/identity/') ); ?>" class="nav-item">Identity</a>
        <a href="<?php echo esc_url( home_url('/press/') ); ?>" class="nav-item">Press</a>
        <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="nav-item">Cases</a>
        <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="header-cta">Contact</a>
        <a href="<?php echo esc_url( home_url('/login/') ); ?>" class="btn btn-outline-light" style="min-height:auto;padding:8px 20px;font-size:0.8rem;">Login</a>
      </nav>
    </div>
  </header>

  <!-- Mobile fullscreen nav -->
  <nav class="mobile-nav" id="mobileNav" role="navigation" aria-hidden="true">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="mobile-nav__item">Home</a>
    <a href="<?php echo esc_url( home_url('/identity/') ); ?>" class="mobile-nav__item">Identity</a>
    <a href="<?php echo esc_url( home_url('/press/') ); ?>" class="mobile-nav__item">Press</a>
    <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="mobile-nav__item">Cases</a>
    <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="mobile-nav__item">Contact</a>
    <a href="<?php echo esc_url( home_url('/login/') ); ?>" class="mobile-nav__item">Login</a>
    <div class="mobile-nav__divider"></div>
    <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>" class="nav-external" target="_blank" rel="noopener">
      <span class="nav-external__label">Product Site</span>
      <span class="nav-external__name">YUMEHO</span>
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" style="flex-shrink:0;"><path d="M4 1.5H2.5A1 1 0 001.5 2.5v7a1 1 0 001 1h7a1 1 0 001-1V8M7.5 1.5h3v3M6 6l4.5-4.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </a>
  </nav>

  <div class="login-wrap">

    <!-- Left: Brand -->
    <div class="login-left">
      <div style="max-width:480px;">
        <a href="<?php echo esc_url( $member_context['home_url'] ); ?>" style="display:inline-block;margin-bottom:56px;">
          <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:2rem;font-weight:300;letter-spacing:0.06em;color:var(--white);">Rinascente</div>
          <div style="font-size:0.58rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:var(--gold);margin-top:2px;"><?php echo esc_html( $member_context['logo_sub'] ); ?></div>
        </a>

        <?php if ( ! empty( $member_context['shared_note'] ) ) : ?>
        <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(200,169,110,0.28);border-radius:999px;background:rgba(200,169,110,0.08);font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--gold-light);margin-bottom:20px;">
          <?php echo esc_html( $member_context['product_label'] ); ?> Member Access
        </div>
        <?php endif; ?>

        <h2 style="
          font-family:var(--font-body);
          font-size:clamp(2.2rem,4vw,3.5rem);
          font-style:normal;
          font-weight:700;
          color:var(--white);
          line-height:1.1;
          letter-spacing:-0.01em;
          margin-bottom:24px;
        ">
          <?php echo wp_kses( $member_context['login_heading'], array( 'br' => array(), 'span' => array( 'style' => array() ) ) ); ?>
        </h2>

        <p style="color:rgba(255,255,255,0.55);font-size:0.95rem;line-height:1.75;margin-bottom:32px;">
          <?php echo esc_html( $member_context['login_lead'] ); ?>
        </p>

        <div style="display:grid;gap:12px;">
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(200,169,110,0.15);border:1px solid rgba(200,169,110,0.35);display:grid;place-items:center;flex-shrink:0;">
              <span style="color:var(--gold);font-size:0.75rem;">✓</span>
            </div>
            <span style="font-size:0.88rem;color:rgba(255,255,255,0.65);">製品詳細仕様書・稟議用資料のダウンロード</span>
          </div>
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(200,169,110,0.15);border:1px solid rgba(200,169,110,0.35);display:grid;place-items:center;flex-shrink:0;">
              <span style="color:var(--gold);font-size:0.75rem;">✓</span>
            </div>
            <span style="font-size:0.88rem;color:rgba(255,255,255,0.65);">補助金・助成金申請サポート資料</span>
          </div>
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(200,169,110,0.15);border:1px solid rgba(200,169,110,0.35);display:grid;place-items:center;flex-shrink:0;">
              <span style="color:var(--gold);font-size:0.75rem;">✓</span>
            </div>
            <span style="font-size:0.88rem;color:rgba(255,255,255,0.65);">優先的なサポート・保守情報の受信</span>
          </div>
          <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(200,169,110,0.15);border:1px solid rgba(200,169,110,0.35);display:grid;place-items:center;flex-shrink:0;">
              <span style="color:var(--gold);font-size:0.75rem;">✓</span>
            </div>
            <span style="font-size:0.88rem;color:rgba(255,255,255,0.65);">セミナー・事例研究会への優先参加</span>
          </div>
        </div>

        <div style="margin-top:40px;padding-top:32px;border-top:1px solid rgba(255,255,255,0.08);">
          <p style="font-size:0.82rem;color:rgba(255,255,255,0.4);">
            会員登録がお済みでない方は、<a href="<?php echo esc_url( $member_context['contact_url'] ); ?>" style="color:var(--gold);border-bottom:1px solid rgba(200,169,110,0.4);">お問い合わせフォーム</a>よりお申し込みください。
          </p>
        </div>
      </div>
    </div>

    <!-- Right: Login Form -->
    <div class="login-right">
      <div class="login-box">
        <div style="margin-bottom:40px;">
          <h1 style="font-size:1.4rem;font-weight:700;color:var(--white);margin-bottom:6px;">ログイン</h1>
          <p style="font-size:0.85rem;color:rgba(255,255,255,0.45);"><?php echo esc_html( $member_context['login_form_note'] ?: '会員IDとパスワードを入力してください。' ); ?></p>
        </div>

        <form id="loginForm" method="post" action="<?php echo esc_url( $rinascente_login_action_url ); ?>" novalidate>
          <?php wp_nonce_field( 'rinascente_member_login', 'rinascente_member_login_nonce' ); ?>
          <input type="hidden" name="rinascente_member_login" value="1">
          <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
          <div style="margin-bottom:20px;">
            <label style="display:block;font-size:0.78rem;font-weight:700;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:8px;">会員ID / メールアドレス</label>
            <input type="text" id="loginId" name="log" class="login-form-control" placeholder="your-id@example.jp" autocomplete="username" value="<?php echo esc_attr( $login_identifier ); ?>">
          </div>
          <div style="margin-bottom:8px;">
            <label style="display:block;font-size:0.78rem;font-weight:700;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:8px;">パスワード</label>
            <input type="password" id="loginPw" name="pwd" class="login-form-control" placeholder="••••••••" autocomplete="current-password">
          </div>
          <div style="text-align:right;margin-bottom:28px;">
            <a href="<?php echo esc_url( rinascente_member_forgot_password_url( $member_product ) ); ?>" style="font-size:0.78rem;color:rgba(255,255,255,0.4);border-bottom:1px solid rgba(255,255,255,0.2);">パスワードをお忘れの方</a>
          </div>

          <div style="background:rgba(200,169,110,0.08);border:1px solid rgba(200,169,110,0.25);border-radius:var(--r-md);padding:14px 16px;margin-bottom:16px;font-size:0.78rem;color:rgba(255,255,255,0.55);line-height:1.8;">
            <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:var(--gold);margin-bottom:6px;">ローカル確認用アカウント<?php echo $member_context['is_yumeho'] ? '（共通会員サイト）' : ''; ?></div>
            <div>ID：<span style="color:rgba(255,255,255,0.85);font-family:monospace;">demo@rinascente.jp</span></div>
            <div>PW：<span style="color:rgba(255,255,255,0.85);font-family:monospace;">Rinascente2026</span></div>
          </div>
          <?php if ( $login_notice ) : ?>
          <p style="color:#2ecc71;font-size:0.83rem;margin-bottom:12px;"><?php echo esc_html( $login_notice ); ?></p>
          <?php endif; ?>
          <p id="loginError" style="color:#e74c3c;font-size:0.83rem;margin-bottom:12px;min-height:1.2em;"><?php echo esc_html( $login_error ); ?></p>

          <button type="submit" class="btn btn-gold" style="width:100%;min-height:52px;font-size:0.95rem;">
            ログイン →
          </button>
        </form>

        <div style="margin-top:32px;padding-top:24px;border-top:1px solid rgba(255,255,255,0.08);text-align:center;">
          <p style="font-size:0.82rem;color:rgba(255,255,255,0.35);">
            <?php echo esc_html( $member_context['contact_note'] ); ?><br>
            <a href="<?php echo esc_url( $member_context['contact_url'] ); ?>" style="color:var(--gold);border-bottom:1px solid rgba(200,169,110,0.35);">お問い合わせフォームへ進む</a>
          </p>
        </div>

        <div style="margin-top:24px;text-align:center;">
          <a href="<?php echo esc_url( $member_context['home_url'] ); ?>" style="font-size:0.78rem;color:rgba(255,255,255,0.3);display:inline-flex;align-items:center;gap:6px;">← <?php echo esc_html( $member_context['back_label'] ); ?></a>
        </div>
      </div>
    </div>

  </div>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="footer-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="Rinascente" style="height:32px;vertical-align:middle;margin-right:4px;">Rinascente</div>
          <div class="footer-tagline">復活する。再生する。</div>
          <p><span style="white-space:nowrap;">医療・福祉機器の企画・販売を中心に、</span><span style="white-space:nowrap;">人の生活を支えるソリューションを届けます。</span><br>○○○○<br><span style="white-space:nowrap;">TEL: 0859-00-1234</span></p>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>Corporate</h4>
          <ul>
            <li><a href="<?php echo esc_url( home_url('/') ); ?>">Home</a></li>
            <li><a href="<?php echo esc_url( home_url('/identity/') ); ?>">Identity</a></li>
            <li><a href="<?php echo esc_url( home_url('/press/') ); ?>">Press</a></li>
            <li><a href="<?php echo esc_url( home_url('/cases/') ); ?>">Cases</a></li>
            <li><a href="<?php echo esc_url( home_url('/contact/') ); ?>">Contact</a></li>
            <li><a href="<?php echo esc_url( home_url('/login/') ); ?>">Member Login</a></li>
          </ul>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>YUMEHO</h4>
          <ul>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>">トップ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/product/' ) ); ?>">製品紹介</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/simulation/' ) ); ?>">導入シミュレーション</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/cases/' ) ); ?>">導入事例</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/flow/' ) ); ?>">導入フロー</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/price/' ) ); ?>">価格・見積</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/subsidy/' ) ); ?>">補助金ガイド</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/faq/' ) ); ?>">FAQ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/contact/' ) ); ?>">お問い合わせ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/company/' ) ); ?>">会社概要</a></li>
          </ul>
        </div>
        <?php if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) : ?>
        <div class="footer-col footer-col--desktop">
          <h4>MICA30</h4>
          <ul>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>">トップ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/product/' ) ); ?>">製品詳細</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/cases/' ) ); ?>">導入事例</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/voices/' ) ); ?>">ご利用施設の声</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/price/' ) ); ?>">価格・導入</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/flow/' ) ); ?>">導入の流れ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/faq/' ) ); ?>">FAQ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/company/' ) ); ?>">会社概要</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/contact/' ) ); ?>">お問い合わせ</a></li>
          </ul>
        </div>
        <?php endif; ?>
      </div>
      <div class="footer-bottom">
        <span>&copy; 2026 Rinascente. All Rights Reserved.</span>
        <span>Rinascente Inc.</span>
      </div>
    </div>
  </footer>
<?php wp_footer(); ?>
</body>
</html>
