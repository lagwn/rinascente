<?php
/**
 * Template Name: Reset Password
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$member_product = function_exists( 'rinascente_member_requested_product' ) ? rinascente_member_requested_product() : '';
$member_context = function_exists( 'rinascente_member_context' ) ? rinascente_member_context( $member_product ) : array(
    'product_label'   => 'Rinascente',
    'shared_note'     => '',
    'home_url'        => home_url( '/' ),
    'document_prefix' => 'Rinascente Member',
    'logo_sub'        => 'Shared Member Site',
    'reset_lead'      => 'リンクを開いたら、8文字以上の新しいパスワードを入力してください。設定後はそのままログインできます。',
);

if ( is_user_logged_in() ) {
    wp_safe_redirect( $member_product ? rinascente_member_product_page_url( $member_product ) : rinascente_member_page_url() );
    exit;
}

$login_value = sanitize_text_field( wp_unslash( $_REQUEST['login'] ?? '' ) );
$key_value   = sanitize_text_field( wp_unslash( $_REQUEST['key'] ?? '' ) );
$error       = '';
$reset_user  = null;

if ( ! empty( $member_context['document_prefix'] ) ) {
    add_filter(
        'pre_get_document_title',
        static function () use ( $member_context ) {
            return $member_context['document_prefix'] . ' 新しいパスワード設定 | Rinascente Shared Member Site';
        }
    );
}

if ( '' !== $login_value && '' !== $key_value ) {
    $reset_user = check_password_reset_key( $key_value, $login_value );
    if ( is_wp_error( $reset_user ) ) {
        $reset_user = null;
        $error      = 'この再設定リンクは期限切れか、すでに新しいリンクが発行されています。もう一度「パスワードをお忘れの方」からお手続きください。';
    }
} else {
    $error = '再設定リンクの情報が不足しています。もう一度「パスワードをお忘れの方」からお手続きください。';
}

if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['rinascente_reset_password'] ) ) {
    $login_value = sanitize_text_field( wp_unslash( $_POST['login'] ?? '' ) );
    $key_value   = sanitize_text_field( wp_unslash( $_POST['key'] ?? '' ) );
    $password    = (string) wp_unslash( $_POST['password'] ?? '' );

    if ( ! isset( $_POST['rinascente_reset_password_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rinascente_reset_password_nonce'] ) ), 'rinascente_reset_password' ) ) {
        $error = 'ページの有効期限が切れました。再読み込みして、もう一度入力してください。';
    } else {
        $reset_user = check_password_reset_key( $key_value, $login_value );
        if ( is_wp_error( $reset_user ) ) {
            $reset_user = null;
            $error      = 'この再設定リンクは期限切れか、すでに新しいリンクが発行されています。もう一度「パスワードをお忘れの方」からお手続きください。';
        } elseif ( strlen( $password ) < 8 ) {
            $error = 'パスワードは8文字以上で入力してください。';
        } else {
            reset_password( $reset_user, $password );
            wp_safe_redirect( add_query_arg( 'reset', 'success', rinascente_member_login_url( '', $member_product ) ) );
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
<div class="login-wrap" style="min-height:100vh;">
  <div class="login-left">
    <div style="max-width:480px;">
      <a href="<?php echo esc_url( $member_context['home_url'] ); ?>" style="display:inline-block;margin-bottom:56px;">
        <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:2rem;font-weight:300;letter-spacing:0.06em;color:var(--white);">Rinascente</div>
        <div style="font-size:0.58rem;font-weight:700;letter-spacing:0.22em;text-transform:uppercase;color:var(--gold);margin-top:2px;"><?php echo esc_html( $member_context['logo_sub'] ); ?></div>
      </a>
      <?php if ( ! empty( $member_context['shared_note'] ) ) : ?>
      <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(200,169,110,0.28);border-radius:999px;background:rgba(200,169,110,0.08);font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--gold-light);margin-bottom:20px;"><?php echo esc_html( $member_context['product_label'] ); ?> Member Access</div>
      <?php endif; ?>
      <h1 style="font-size:2.2rem;color:var(--white);line-height:1.2;margin-bottom:20px;">新しいパスワードを設定</h1>
      <p style="color:rgba(255,255,255,0.55);line-height:1.8;"><?php echo esc_html( $member_context['reset_lead'] ); ?></p>
    </div>
  </div>
  <div class="login-right">
    <div class="login-box">
      <h2 style="font-size:1.35rem;font-weight:700;color:var(--white);margin-bottom:10px;">新しいパスワードを入力</h2>
      <?php if ( $error ) : ?>
      <p style="color:#e74c3c;font-size:0.9rem;line-height:1.7;margin-bottom:16px;"><?php echo esc_html( $error ); ?></p>
      <?php endif; ?>
      <?php if ( $reset_user instanceof WP_User ) : ?>
      <p style="font-size:0.85rem;color:rgba(255,255,255,0.45);margin-bottom:24px;">半角英数字を含む8文字以上がおすすめです。</p>
      <form method="post">
        <?php wp_nonce_field( 'rinascente_reset_password', 'rinascente_reset_password_nonce' ); ?>
        <input type="hidden" name="rinascente_reset_password" value="1">
        <input type="hidden" name="login" value="<?php echo esc_attr( $login_value ); ?>">
        <input type="hidden" name="key" value="<?php echo esc_attr( $key_value ); ?>">
        <div style="margin-bottom:20px;">
          <label for="password" style="display:block;font-size:0.78rem;font-weight:700;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:8px;">新しいパスワード</label>
          <input type="password" id="password" name="password" class="login-form-control" placeholder="8文字以上">
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;min-height:52px;font-size:0.95rem;">パスワードを更新する</button>
      </form>
      <?php else : ?>
      <p style="font-size:0.85rem;color:rgba(255,255,255,0.45);margin-bottom:24px;">再設定リンクが無効な場合は、あらためて再設定をリクエストしてください。</p>
      <div style="margin-top:24px;text-align:center;">
        <a href="<?php echo esc_url( rinascente_member_forgot_password_url( $member_product ) ); ?>" style="font-size:0.8rem;color:var(--gold-light);">再設定をリクエストする</a>
      </div>
      <?php endif; ?>
      <div style="margin-top:24px;text-align:center;">
        <a href="<?php echo esc_url( rinascente_member_login_url( '', $member_product ) ); ?>" style="font-size:0.8rem;color:rgba(255,255,255,0.4);">ログイン画面に戻る</a>
      </div>
    </div>
  </div>
</div>
<?php wp_footer(); ?>
</body>
</html>
