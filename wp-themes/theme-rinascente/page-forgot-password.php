<?php
/**
 * Template Name: Forgot Password
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
    'forgot_lead'     => '会員登録時のメールアドレスを入力すると、パスワード再設定のご案内をお送りします。ローカル環境ではこの画面からそのまま次へ進めます。',
);

if ( is_user_logged_in() ) {
    wp_safe_redirect( $member_product ? rinascente_member_product_page_url( $member_product ) : rinascente_member_page_url() );
    exit;
}

$message          = '';
$error            = '';
$email_value      = '';
$local_reset_link = '';

if ( ! empty( $member_context['document_prefix'] ) ) {
    add_filter(
        'pre_get_document_title',
        static function () use ( $member_context ) {
            return $member_context['document_prefix'] . ' パスワード再設定 | Rinascente Shared Member Site';
        }
    );
}

if ( 'POST' === strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) && isset( $_POST['rinascente_forgot_password'] ) ) {
    $email_value = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );

    if ( ! isset( $_POST['rinascente_forgot_password_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rinascente_forgot_password_nonce'] ) ), 'rinascente_forgot_password' ) ) {
        $error = 'ページの有効期限が切れました。再読み込みして、もう一度お試しください。';
    } elseif ( ! is_email( $email_value ) ) {
        $error = 'メールアドレスを正しく入力してください。';
    } else {
        $user = get_user_by( 'email', $email_value );

        if ( ! $user instanceof WP_User ) {
            $error = 'このメールアドレスでは会員情報が見つかりませんでした。入力内容をご確認ください。';
        } else {
            $reset_key = get_password_reset_key( $user );

            if ( is_wp_error( $reset_key ) ) {
                $error = '再設定リンクを作成できませんでした。時間をおいてもう一度お試しください。';
            } else {
                $local_reset_link = add_query_arg(
                    array(
                        'key'   => $reset_key,
                        'login' => rawurlencode( $user->user_login ),
                    ),
                    rinascente_member_reset_password_url( $member_product )
                );

                if ( rinascente_is_local_environment() ) {
                    $message = 'ローカル確認用のため、この画面の下に再設定リンクを表示しています。リンクを開いて新しいパスワードを設定してください。';
                } else {
                    $subject = '【Rinascente】パスワード再設定のご案内';
                    $body    = "以下のURLからパスワードを再設定してください。\n\n" . $local_reset_link . "\n\nこのリンクの有効期限は24時間です。";
                    $message = wp_mail( $user->user_email, $subject, $body )
                        ? 'パスワード再設定用のURLを送信しました。メール内のリンクから新しいパスワードを設定してください。'
                        : 'メール送信に失敗したため、この画面の下に再設定リンクを表示しています。';
                }
            }
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
      <h1 style="font-size:2.2rem;color:var(--white);line-height:1.2;margin-bottom:20px;">パスワードをお忘れの方へ</h1>
      <p style="color:rgba(255,255,255,0.55);line-height:1.8;"><?php echo esc_html( $member_context['forgot_lead'] ); ?></p>
    </div>
  </div>
  <div class="login-right">
    <div class="login-box">
      <h2 style="font-size:1.35rem;font-weight:700;color:var(--white);margin-bottom:10px;">会員登録時のメールアドレスを入力</h2>
      <p style="font-size:0.85rem;color:rgba(255,255,255,0.45);margin-bottom:24px;">入力後、メールまたは画面の案内から再設定へ進めます。</p>
      <?php if ( $message ) : ?>
      <p style="color:#2ecc71;font-size:0.9rem;line-height:1.7;margin-bottom:16px;"><?php echo esc_html( $message ); ?></p>
      <?php endif; ?>
      <?php if ( $error ) : ?>
      <p style="color:#e74c3c;font-size:0.9rem;line-height:1.7;margin-bottom:16px;"><?php echo esc_html( $error ); ?></p>
      <?php endif; ?>
      <form method="post">
        <?php wp_nonce_field( 'rinascente_forgot_password', 'rinascente_forgot_password_nonce' ); ?>
        <input type="hidden" name="rinascente_forgot_password" value="1">
        <div style="margin-bottom:20px;">
          <label for="email" style="display:block;font-size:0.78rem;font-weight:700;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:8px;">メールアドレス</label>
          <input type="email" id="email" name="email" class="login-form-control" value="<?php echo esc_attr( $email_value ); ?>" placeholder="your-id@example.jp">
        </div>
        <button type="submit" class="btn btn-gold" style="width:100%;min-height:52px;font-size:0.95rem;">再設定リンクを送る</button>
      </form>
      <?php if ( $local_reset_link ) : ?>
      <div style="margin-top:18px;padding:16px;border:1px solid rgba(200,169,110,0.25);border-radius:var(--r-md);background:rgba(200,169,110,0.08);">
        <div style="font-size:0.74rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--gold);margin-bottom:8px;">ローカル確認用の再設定リンク</div>
        <a href="<?php echo esc_url( $local_reset_link ); ?>" style="word-break:break-all;color:var(--gold-light);font-size:0.82rem;"><?php echo esc_html( $local_reset_link ); ?></a>
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
