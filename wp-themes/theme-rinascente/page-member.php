<?php
/**
 * Template Name: Member Dashboard
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$member_page_url = rinascente_member_page_url();
$login_url       = rinascente_member_login_url( $member_page_url );
if ( ! is_user_logged_in() ) {
    wp_safe_redirect( $login_url );
    exit;
}

$viewer_user          = wp_get_current_user();
$preview_member_id    = current_user_can( 'manage_options' ) && isset( $_GET['preview_member'] ) ? absint( wp_unslash( $_GET['preview_member'] ) ) : 0;
$preview_member       = $preview_member_id ? get_user_by( 'id', $preview_member_id ) : null;
$is_member_preview    = $preview_member instanceof WP_User;
$current_member       = $is_member_preview ? $preview_member : $viewer_user;
$member_name          = rinascente_member_user_name( $current_member );
$member_email         = (string) $current_member->user_email;
$member_initial       = rinascente_member_user_initial( $current_member );
$member_role_label    = rinascente_member_user_role_label( $current_member );
$member_logout_url    = wp_logout_url( home_url( '/login/' ) );
$facility_name        = get_user_meta( $current_member->ID, '_rinascente_member_facility_name', true );
$facility_type        = get_user_meta( $current_member->ID, '_rinascente_member_facility_type', true );
$product_choices        = rinascente_member_product_choices();
$visible_product_choices = function_exists( 'rinascente_member_visible_product_choices' ) ? rinascente_member_visible_product_choices() : $product_choices;
$hidden_product_keys    = function_exists( 'rinascente_member_hidden_product_keys' ) ? rinascente_member_hidden_product_keys() : array();
$assigned_products      = rinascente_member_get_user_products( $current_member->ID );
$visible_assigned_products = array_values( array_diff( $assigned_products, $hidden_product_keys ) );
$available_products     = empty( $assigned_products ) ? $visible_product_choices : array_intersect_key( $visible_product_choices, array_flip( $visible_assigned_products ) );
$has_visible_products   = ! empty( $available_products );
$requested_product    = isset( $_GET['product'] ) ? sanitize_key( wp_unslash( $_GET['product'] ) ) : '';
$selected_product     = $has_visible_products && isset( $available_products[ $requested_product ] ) ? $requested_product : ( $has_visible_products ? array_key_first( $available_products ) : '' );
$selected_label       = $has_visible_products && isset( $available_products[ $selected_product ] ) ? $available_products[ $selected_product ] : '共通コンテンツ';
$member_reviews_enabled = function_exists( 'rinascente_member_reviews_enabled' ) && rinascente_member_reviews_enabled();
$review_notice          = $member_reviews_enabled ? rinascente_member_review_notice() : null;
$support_info         = rinascente_member_support_info();
$contract_statuses    = rinascente_contract_status_choices();
$payment_statuses     = rinascente_contract_payment_choices();
$review_periods       = $member_reviews_enabled ? rinascente_member_review_period_choices() : array();
$member_products_list = $has_visible_products ? implode( ' / ', array_values( $available_products ) ) : '共通コンテンツ';
$shared_products_label = function_exists( 'rinascente_shared_member_products_label' ) ? rinascente_shared_member_products_label() : 'YUMEHO';
$member_products_note = $has_visible_products
    ? sprintf( '%s の契約状況に応じて会員コンテンツを表示します。', $shared_products_label )
    : 'MICA30 は現在掲載準備中のため、会員サイトでは非表示にしています。共通のサポート情報のみ表示しています。';
$admin_links          = array(
    'member_create'   => function_exists( 'rinascente_member_create_page_url' ) ? rinascente_member_create_page_url() : admin_url( 'users.php' ),
    'contract'        => admin_url( 'edit.php?post_type=contract' ),
    'member_video'    => admin_url( 'edit.php?post_type=member_video' ),
    'member_document' => admin_url( 'edit.php?post_type=member_document' ),
    'member_notice'   => admin_url( 'edit.php?post_type=member_notice' ),
);

if ( $member_reviews_enabled ) {
    $admin_links['member_review'] = admin_url( 'edit.php?post_type=member_review' );
}

$member_product_tab_url = static function ( $product_key ) use ( $is_member_preview, $preview_member_id ) {
    $url = rinascente_member_product_page_url( $product_key );
    if ( $is_member_preview ) {
        $url = add_query_arg( 'preview_member', $preview_member_id, $url );
    }

    return $url;
};
$member_dashboard_url = $is_member_preview ? add_query_arg( 'preview_member', $preview_member_id, $member_page_url ) : $member_page_url;

$filter_by_product = static function ( $posts ) use ( $selected_product ) {
    return array_values(
        array_filter(
            $posts,
            static function ( $post ) use ( $selected_product ) {
                $post_products = rinascente_member_get_post_products( $post->ID );
                if ( empty( $post_products ) ) {
                    return true;
                }

                if ( '' === $selected_product ) {
                    return false;
                }

                return in_array( $selected_product, $post_products, true );
            }
        )
    );
};

$contracts         = $filter_by_product( rinascente_member_get_contracts( $current_member->ID ) );
$setup_videos      = $filter_by_product( rinascente_member_get_videos( $current_member->ID, 'setup' ) );
$usage_videos      = $filter_by_product( rinascente_member_get_videos( $current_member->ID, 'usage' ) );
$documents         = $filter_by_product( rinascente_member_get_documents( $current_member->ID ) );
$support_documents = array_values(
    array_filter(
        $documents,
        static function ( $post ) {
            return 'subsidy' === get_post_meta( $post->ID, '_rinascente_document_category', true );
        }
    )
);
$download_documents = array_values(
    array_filter(
        $documents,
        static function ( $post ) {
            return 'subsidy' !== get_post_meta( $post->ID, '_rinascente_document_category', true );
        }
    )
);
$support_notices = $filter_by_product( rinascente_member_get_support_notices( $current_member->ID ) );
$reviews         = $member_reviews_enabled ? $filter_by_product( rinascente_member_get_reviews( $current_member->ID ) ) : array();
$review_summary  = $member_reviews_enabled ? rinascente_member_review_summary( $reviews ) : array();
$summary         = $member_reviews_enabled ? ( $review_summary[ $selected_product ] ?? array(
    'label'        => $selected_label,
    'count'        => 0,
    'average'      => '0.0',
    'distribution' => array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 ),
) ) : null;
$public_case_url = $has_visible_products ? rinascente_related_site_url( 'yumeho', '/cases/' ) : home_url( '/contact/' );
$public_case_label = $has_visible_products ? $selected_label . ' の導入事例' : '共通サポート窓口';
$public_case_link_text = $has_visible_products ? '公開ページの導入事例を見る' : '共通のお問い合わせを見る';
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
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
  <header class="site-header" style="background:var(--black);border-bottom:1px solid rgba(255,255,255,0.07);">
    <div class="header-inner">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="" class="header-logo__img">
        <span class="header-logo__text">
          <span class="logo-wordmark">Rinascente</span>
          <span class="logo-sub">Shared Member Site</span>
        </span>
      </a>
      <button class="menu-toggle" aria-label="メニュー" aria-expanded="false">
        <span class="bar"></span><span class="bar"></span><span class="bar"></span>
      </button>
      <nav class="header-nav header-nav--desktop" role="navigation">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-item">Home</a>
        <a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>" class="nav-item">Cases</a>
        <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="header-cta">Contact</a>
        <a href="<?php echo esc_url( $member_logout_url ); ?>" class="btn btn-outline-light" style="min-height:auto;padding:8px 20px;font-size:0.8rem;">Logout</a>
      </nav>
    </div>
  </header>

  <nav class="mobile-nav" id="mobileNav" role="navigation" aria-hidden="true">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-nav__item">Home</a>
    <a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>" class="mobile-nav__item">Cases</a>
    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="mobile-nav__item">Contact</a>
    <a href="<?php echo esc_url( $member_logout_url ); ?>" class="mobile-nav__item">Logout</a>
  </nav>

  <div class="member-layout">
    <aside class="member-sidebar">
      <div class="sidebar-user">
        <div class="sidebar-avatar"><?php echo esc_html( $member_initial ); ?></div>
        <div class="sidebar-name"><?php echo esc_html( $member_name ); ?></div>
        <?php if ( $member_email && $member_email !== $member_name ) : ?>
        <div class="sidebar-role" style="margin-bottom:4px;"><?php echo esc_html( $member_email ); ?></div>
        <?php endif; ?>
        <div class="sidebar-role"><?php echo esc_html( $member_role_label ); ?></div>
        <a href="<?php echo esc_url( $member_logout_url ); ?>" class="btn btn-outline-light" style="margin-top:20px;display:inline-flex;min-height:auto;padding:8px 18px;font-size:0.8rem;">Logout</a>
      </div>

      <button class="sp-nav-toggle" id="spNavToggle">
        <span>MENU</span>
        <span class="sp-nav-toggle__arrow">▼</span>
      </button>
      <ul class="sidebar-nav" id="sidebarNav">
        <li class="sidebar-section-label">メニュー</li>
        <li><a href="#purchases" class="active"><span class="nav-icon">🛒</span>購入履歴</a></li>
        <li><a href="#videos"><span class="nav-icon">▶</span>会員限定動画</a></li>
        <li><a href="#downloads"><span class="nav-icon">📄</span>資料ダウンロード</a></li>
        <li><a href="#subsidy"><span class="nav-icon">💴</span>補助金サポート</a></li>
        <li><a href="#support"><span class="nav-icon">🔔</span>サポート情報</a></li>
        <?php if ( $member_reviews_enabled ) : ?>
        <li><a href="#reviews"><span class="nav-icon">★</span>製品レビュー</a></li>
        <?php endif; ?>
      </ul>
    </aside>

    <main class="member-main">
      <section class="member-section">
        <h1 class="member-section-title"><span>👋</span> 共通会員サイト</h1>
        <div class="member-top-grid">
          <div class="member-top-card">
            <div class="member-top-label">Facility</div>
            <div class="member-top-value"><?php echo esc_html( $facility_name ?: $member_name ); ?></div>
            <?php if ( $facility_type ) : ?>
            <div class="member-note" style="margin-top:8px;"><?php echo esc_html( $facility_type ); ?></div>
            <?php endif; ?>
          </div>
          <div class="member-top-card">
            <div class="member-top-label">Products</div>
            <div class="member-top-value"><?php echo esc_html( $member_products_list ); ?></div>
            <div class="member-note" style="margin-top:8px;"><?php echo esc_html( $member_products_note ); ?></div>
          </div>
          <div class="member-top-card">
            <div class="member-top-label">Public Cases</div>
            <div class="member-top-value"><?php echo esc_html( $public_case_label ); ?></div>
            <div class="member-note" style="margin-top:8px;"><a href="<?php echo esc_url( $public_case_url ); ?>" style="color:var(--charcoal);text-decoration:underline;"><?php echo esc_html( $public_case_link_text ); ?></a></div>
          </div>
        </div>

        <?php if ( ! $has_visible_products ) : ?>
        <div class="member-empty" style="margin-bottom:20px;border-left:4px solid var(--gold);">
          現在ご利用中の製品のうち、会員サイトで公開中のコンテンツはありません。共通の資料・サポート情報をご確認ください。
        </div>
        <?php endif; ?>

        <?php if ( $has_visible_products ) : ?>
        <div class="member-product-tabs">
          <?php foreach ( $available_products as $product_key => $product_label ) : ?>
          <a href="<?php echo esc_url( $member_product_tab_url( $product_key ) ); ?>" class="member-product-tab<?php echo $product_key === $selected_product ? ' is-active' : ''; ?>">
            <span><?php echo esc_html( $product_label ); ?></span>
            <?php if ( $product_key === $selected_product ) : ?><span>●</span><?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( current_user_can( 'manage_options' ) ) : ?>
        <div class="member-empty" style="margin-bottom:20px;">
          <div style="font-size:0.9rem;font-weight:700;color:var(--charcoal);margin-bottom:8px;">管理者クイックリンク</div>
          <div class="member-admin-links">
            <a href="<?php echo esc_url( $admin_links['member_create'] ); ?>" class="member-admin-link">施設会員を追加</a>
            <a href="<?php echo esc_url( $admin_links['contract'] ); ?>" class="member-admin-link">契約・購入履歴</a>
            <a href="<?php echo esc_url( $admin_links['member_video'] ); ?>" class="member-admin-link">会員限定動画</a>
            <a href="<?php echo esc_url( $admin_links['member_document'] ); ?>" class="member-admin-link">会員限定資料</a>
            <a href="<?php echo esc_url( $admin_links['member_notice'] ); ?>" class="member-admin-link">サポート情報</a>
            <?php if ( $member_reviews_enabled ) : ?>
            <a href="<?php echo esc_url( $admin_links['member_review'] ); ?>" class="member-admin-link">施設レビュー</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ( $is_member_preview ) : ?>
        <div class="member-empty" style="margin-bottom:20px;border-left:4px solid var(--gold);">
          <div style="font-size:0.9rem;font-weight:700;color:var(--charcoal);margin-bottom:8px;">管理者プレビュー中</div>
          <div class="member-note" style="margin-bottom:8px;"><?php echo esc_html( $facility_name ?: $member_name ); ?> の会員ページを表示しています。</div>
          <div class="member-admin-links">
            <a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $current_member->ID ) ); ?>" class="member-admin-link">会員情報を編集</a>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=contract&rinascente_contract_lookup=' . rawurlencode( $facility_name ?: $member_name ) ) ); ?>" class="member-admin-link">購入履歴を確認</a>
            <a href="<?php echo esc_url( rinascente_member_page_url() ); ?>" class="member-admin-link">自分の会員ページに戻る</a>
          </div>
        </div>
        <?php endif; ?>
      </section>

      <section class="member-section" id="purchases">
        <h2 class="member-section-title"><span>🛒</span> 購入履歴</h2>
        <?php if ( ! empty( $contracts ) ) : ?>
        <div class="table-scroll-hint">横にスクロール →</div>
        <div class="table-scroll-wrap">
          <table class="purchase-table">
            <thead>
              <tr>
                <th>注文番号</th>
                <th>製品名</th>
                <th>数量</th>
                <th>注文日</th>
                <th>納品日</th>
                <th>ステータス</th>
                <th>支払い</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ( $contracts as $contract ) : ?>
              <?php
              $order_number   = get_post_meta( $contract->ID, '_rinascente_order_number', true );
              $product_name   = get_post_meta( $contract->ID, '_rinascente_product_name', true );
              $quantity       = get_post_meta( $contract->ID, '_rinascente_quantity', true );
              $order_date     = get_post_meta( $contract->ID, '_rinascente_order_date', true );
              $delivery_date  = get_post_meta( $contract->ID, '_rinascente_delivery_date', true );
              $status         = get_post_meta( $contract->ID, '_rinascente_contract_status', true );
              $payment_status = get_post_meta( $contract->ID, '_rinascente_payment_status', true );
              ?>
              <tr>
                <td style="font-family:monospace;font-size:0.82rem;"><?php echo esc_html( $order_number ?: '—' ); ?></td>
                <td>
                  <strong><?php echo esc_html( $product_name ?: get_the_title( $contract ) ); ?></strong>
                  <?php if ( $info = get_post_meta( $contract->ID, '_rinascente_contract_info', true ) ) : ?>
                  <div class="member-note" style="margin-top:6px;"><?php echo esc_html( $info ); ?></div>
                  <?php endif; ?>
                </td>
                <td><?php echo esc_html( $quantity ?: '—' ); ?></td>
                <td><?php echo esc_html( rinascente_member_format_date( $order_date ) ); ?></td>
                <td><?php echo esc_html( rinascente_member_format_date( $delivery_date ) ); ?></td>
                <td><span class="status-badge <?php echo esc_attr( rinascente_member_status_badge_class( $status ) ); ?>"><?php echo esc_html( $contract_statuses[ $status ] ?? '確認中' ); ?></span></td>
                <td><?php echo esc_html( $payment_statuses[ $payment_status ] ?? '未設定' ); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else : ?>
        <div class="member-empty">
          まだ購入履歴が登録されていません。管理者は「契約・購入履歴」から会員ごとの注文情報を入力できます。
        </div>
        <?php endif; ?>
      </section>

      <section class="member-section" id="videos">
        <h2 class="member-section-title"><span>▶</span> 会員限定動画</h2>
        <div class="member-inline-grid">
          <div>
            <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#0068b7;margin-bottom:12px;">設置方法</div>
            <?php if ( ! empty( $setup_videos ) ) : ?>
            <div class="member-video-grid">
              <?php foreach ( $setup_videos as $video ) : ?>
              <?php
              $youtube_state        = rinascente_member_youtube_video_state( get_post_meta( $video->ID, '_rinascente_youtube_id', true ) );
              $video_is_available   = ! empty( $youtube_state['is_available'] );
              $video_thumbnail_url  = $video_is_available ? $youtube_state['thumbnail_url'] : get_template_directory_uri() . '/assets/img/case_hospital.webp';
              $video_status_message = $video_is_available ? '' : '現在は動画準備中です。';
              ?>
              <article class="member-video-card video-card<?php echo $video_is_available ? '' : ' is-unavailable'; ?>" data-video-id="<?php echo esc_attr( $video_is_available ? $youtube_state['video_id'] : '' ); ?>">
                <div class="member-video-thumb video-thumb">
                  <img src="<?php echo esc_url( $video_thumbnail_url ); ?>" alt="" decoding="async">
                  <?php if ( $video_is_available ) : ?>
                  <div class="video-play-btn"><div class="video-play-icon"></div></div>
                  <?php else : ?>
                  <div class="video-unavailable-badge">動画準備中</div>
                  <?php endif; ?>
                  <span class="video-product-tag"><?php echo esc_html( $selected_label ); ?></span>
                </div>
                <div class="member-video-meta video-meta">
                  <div class="video-title"><?php echo esc_html( get_the_title( $video ) ); ?></div>
                  <div class="video-desc"><?php echo esc_html( get_post_meta( $video->ID, '_rinascente_video_description', true ) ); ?></div>
                  <?php if ( $video_status_message ) : ?>
                  <div class="video-status-note"><?php echo esc_html( $video_status_message ); ?></div>
                  <?php endif; ?>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="member-empty">設置方法の動画はまだ公開されていません。</div>
            <?php endif; ?>
          </div>
          <div>
            <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.14em;text-transform:uppercase;color:#005f73;margin-bottom:12px;">利用方法</div>
            <?php if ( ! empty( $usage_videos ) ) : ?>
            <div class="member-video-grid">
              <?php foreach ( $usage_videos as $video ) : ?>
              <?php
              $youtube_state        = rinascente_member_youtube_video_state( get_post_meta( $video->ID, '_rinascente_youtube_id', true ) );
              $video_is_available   = ! empty( $youtube_state['is_available'] );
              $video_thumbnail_url  = $video_is_available ? $youtube_state['thumbnail_url'] : get_template_directory_uri() . '/assets/img/case_dayservice.webp';
              $video_status_message = $video_is_available ? '' : '現在は動画準備中です。';
              ?>
              <article class="member-video-card video-card<?php echo $video_is_available ? '' : ' is-unavailable'; ?>" data-video-id="<?php echo esc_attr( $video_is_available ? $youtube_state['video_id'] : '' ); ?>">
                <div class="member-video-thumb video-thumb">
                  <img src="<?php echo esc_url( $video_thumbnail_url ); ?>" alt="" decoding="async">
                  <?php if ( $video_is_available ) : ?>
                  <div class="video-play-btn"><div class="video-play-icon"></div></div>
                  <?php else : ?>
                  <div class="video-unavailable-badge">動画準備中</div>
                  <?php endif; ?>
                  <span class="video-product-tag"><?php echo esc_html( $selected_label ); ?></span>
                </div>
                <div class="member-video-meta video-meta">
                  <div class="video-title"><?php echo esc_html( get_the_title( $video ) ); ?></div>
                  <div class="video-desc"><?php echo esc_html( get_post_meta( $video->ID, '_rinascente_video_description', true ) ); ?></div>
                  <?php if ( $video_status_message ) : ?>
                  <div class="video-status-note"><?php echo esc_html( $video_status_message ); ?></div>
                  <?php endif; ?>
                </div>
              </article>
              <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="member-empty">利用方法の動画はまだ公開されていません。</div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <section class="member-section" id="downloads">
        <h2 class="member-section-title"><span>📄</span> 資料ダウンロード</h2>
        <?php if ( ! empty( $download_documents ) ) : ?>
        <div class="download-grid">
          <?php foreach ( $download_documents as $document ) : ?>
          <?php
          $file_data    = rinascente_member_document_file_data( $document->ID );
          $updated_date = get_post_meta( $document->ID, '_rinascente_document_updated_date', true );
          ?>
          <a href="<?php echo esc_url( rinascente_member_document_download_url( $document->ID ) ); ?>" class="download-card">
            <div class="dl-icon" style="background:#e8f1fb;">📄</div>
            <div class="dl-info">
              <div class="dl-title"><?php echo esc_html( get_the_title( $document ) ); ?></div>
              <div class="dl-meta"><?php echo esc_html( strtoupper( pathinfo( $file_data['filename'], PATHINFO_EXTENSION ) ?: 'FILE' ) ); ?> / <?php echo esc_html( $file_data['size'] ?: 'サイズ未設定' ); ?> / <?php echo esc_html( rinascente_member_format_date( $updated_date ) ); ?></div>
            </div>
            <span class="dl-btn">↓ DL</span>
          </a>
          <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="member-empty">ダウンロード資料はまだ登録されていません。</div>
        <?php endif; ?>
      </section>

      <section class="member-section" id="subsidy">
        <h2 class="member-section-title"><span>💴</span> 補助金サポート</h2>
        <div class="member-inline-grid">
          <div class="member-support-card">
            <div style="font-size:1rem;font-weight:700;color:var(--charcoal);margin-bottom:10px;"><?php echo esc_html( $selected_label ); ?> 向け補助金・助成金資料</div>
            <div class="member-note">補助金対象資料や申請書雛形は、会員限定資料の「補助金サポート」カテゴリから管理できます。</div>
            <?php if ( ! empty( $support_documents ) ) : ?>
            <div class="download-grid subsidy-downloads" style="grid-template-columns:1fr; margin-top:18px;">
              <?php foreach ( $support_documents as $document ) : ?>
              <?php $file_data = rinascente_member_document_file_data( $document->ID ); ?>
              <a href="<?php echo esc_url( rinascente_member_document_download_url( $document->ID ) ); ?>" class="download-card" style="padding:14px 18px;">
                <div class="dl-icon" style="background:#e8f5e9;font-size:1.1rem;">📝</div>
                <div class="dl-info">
                  <div class="dl-title" style="font-size:0.85rem;"><?php echo esc_html( get_the_title( $document ) ); ?></div>
                  <div class="dl-meta"><?php echo esc_html( strtoupper( pathinfo( $file_data['filename'], PATHINFO_EXTENSION ) ?: 'FILE' ) ); ?> / <?php echo esc_html( $file_data['size'] ?: 'サイズ未設定' ); ?></div>
                </div>
                <span class="dl-btn">↓ DL</span>
              </a>
              <?php endforeach; ?>
            </div>
            <?php else : ?>
            <div class="member-note" style="margin-top:16px;">まだ補助金資料は登録されていません。</div>
            <?php endif; ?>
          </div>
          <div class="member-support-card">
            <div style="font-size:1rem;font-weight:700;color:var(--charcoal);margin-bottom:10px;">申請サポート窓口</div>
            <div class="member-note">制度確認や申請準備は共通会員サイトからお問い合わせください。必要に応じて YUMEHO の担当へ接続します。</div>
            <div class="member-contact-list">
              <div class="member-contact-item"><span>会社名</span><strong><?php echo esc_html( $support_info['company_name'] ?: 'Rinascente' ); ?></strong></div>
              <div class="member-contact-item"><span>TEL</span><strong><?php echo esc_html( $support_info['telephone'] ?: '未設定' ); ?></strong></div>
              <div class="member-contact-item"><span>受付時間</span><strong><?php echo esc_html( $support_info['hours'] ?: '未設定' ); ?></strong></div>
            </div>
            <div style="margin-top:16px;"><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline-dark btn-sm">担当に相談する →</a></div>
          </div>
        </div>
      </section>

      <section class="member-section" id="support">
        <h2 class="member-section-title"><span>🔔</span> 優先サポート・保守情報</h2>
        <?php if ( ! empty( $support_notices ) ) : ?>
        <div class="notice-list">
          <?php foreach ( $support_notices as $notice ) : ?>
          <?php
          $notice_tone = get_post_meta( $notice->ID, '_rinascente_notice_tone', true );
          $notice_body = rinascente_member_notice_summary( $notice );
          ?>
          <article class="notice-card">
            <div class="notice-dot <?php echo esc_attr( rinascente_member_notice_tone_class( $notice_tone ) ); ?>"></div>
            <div class="notice-content">
              <div class="notice-title"><?php echo esc_html( get_the_title( $notice ) ); ?></div>
              <?php if ( '' !== $notice_body ) : ?>
              <div class="notice-body"><?php echo esc_html( $notice_body ); ?></div>
              <?php endif; ?>
              <div class="notice-date"><?php echo esc_html( get_the_date( 'Y.m.d', $notice ) ); ?></div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
        <?php else : ?>
        <div class="member-empty">
          サポート情報はまだ公開されていません。管理者は「サポート情報」から投稿できます。
        </div>
        <?php endif; ?>
      </section>

      <?php if ( $member_reviews_enabled ) : ?>
      <section class="member-section" id="reviews">
        <h2 class="member-section-title"><span>★</span> 製品レビュー</h2>
        <?php if ( $review_notice ) : ?>
        <div class="member-notice <?php echo 'success' === $review_notice['type'] ? 'is-success' : 'is-error'; ?>">
          <?php echo esc_html( $review_notice['message'] ); ?>
        </div>
        <?php endif; ?>

        <div class="review-summary">
          <div style="text-align:center;">
            <div class="review-score-big"><?php echo esc_html( $summary['average'] ); ?></div>
            <div class="stars"><?php echo esc_html( rinascente_member_star_string( (int) round( (float) $summary['average'] ) ) ); ?></div>
            <div style="font-size:0.78rem;color:var(--mid-gray);margin-top:4px;"><?php echo esc_html( $summary['count'] ); ?>件のレビュー</div>
          </div>
          <div style="flex:1;">
            <?php for ( $star = 5; $star >= 1; --$star ) : ?>
            <?php
            $count = $summary['distribution'][ $star ] ?? 0;
            $width = $summary['count'] > 0 ? round( ( $count / $summary['count'] ) * 100 ) : 0;
            ?>
            <div class="star-bar-row<?php echo 5 === $star ? '' : '" style="margin-top:5px;'; ?>">
              <span><?php echo esc_html( $star ); ?></span>
              <div class="star-bar-wrap"><div class="star-bar" style="width:<?php echo esc_attr( $width ); ?>%;"></div></div>
              <span><?php echo esc_html( $count ); ?></span>
            </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="member-review-grid">
          <?php if ( ! empty( $reviews ) ) : ?>
            <?php foreach ( $reviews as $review ) : ?>
            <?php
            $rating         = (int) get_post_meta( $review->ID, '_rinascente_review_rating', true );
            $author_name    = get_post_meta( $review->ID, '_rinascente_author_name', true );
            $review_facility = get_post_meta( $review->ID, '_rinascente_review_facility_name', true );
            $review_type    = get_post_meta( $review->ID, '_rinascente_review_facility_type', true );
            $adoption       = get_post_meta( $review->ID, '_rinascente_adoption_period', true );
            $tags           = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( $review->ID, '_rinascente_review_tags', true ) ) ) );
            $helpful_count  = (int) get_post_meta( $review->ID, '_rinascente_helpful_count', true );
            $review_initial = function_exists( 'mb_substr' )
                ? mb_strtoupper( mb_substr( $author_name ?: 'M', 0, 1 ) )
                : strtoupper( substr( $author_name ?: 'M', 0, 1 ) );
            ?>
            <article class="review-post">
              <div class="review-post-header">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                  <div class="reviewer-avatar"><?php echo esc_html( $review_initial ); ?></div>
                  <div>
                    <div class="reviewer-name"><?php echo esc_html( $author_name ?: '会員レビュー' ); ?></div>
                    <div class="reviewer-facility"><?php echo esc_html( trim( $review_facility . ( $review_type ? ' / ' . $review_type : '' ) ) ); ?></div>
                  </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                  <div class="review-stars"><?php echo esc_html( rinascente_member_star_string( $rating ) ); ?></div>
                  <div class="review-date"><?php echo esc_html( rinascente_member_format_date( $review->post_date ) ); ?></div>
                </div>
              </div>
              <div class="review-body"><?php echo esc_html( $review->post_content ); ?></div>
              <?php if ( ! empty( $tags ) ) : ?>
              <div class="review-tags">
                <?php foreach ( $tags as $tag ) : ?>
                <span class="review-tag"><?php echo esc_html( $tag ); ?></span>
                <?php endforeach; ?>
                <?php if ( $adoption && isset( $review_periods[ $adoption ] ) ) : ?>
                <span class="review-tag"><?php echo esc_html( $review_periods[ $adoption ] ); ?></span>
                <?php endif; ?>
              </div>
              <?php endif; ?>
              <div class="review-helpful">参考になった: <?php echo esc_html( $helpful_count ); ?></div>
            </article>
            <?php endforeach; ?>
          <?php else : ?>
          <div class="member-empty">まだレビューは投稿されていません。</div>
          <?php endif; ?>
        </div>

        <div class="review-write" style="margin-top:24px;">
          <div style="font-size:0.95rem;font-weight:700;color:var(--charcoal);margin-bottom:16px;">レビューを投稿する</div>
          <form method="post" action="<?php echo esc_url( rinascente_member_review_submit_url() ); ?>">
            <?php wp_nonce_field( 'rinascente_member_review_submit', 'rinascente_member_review_nonce' ); ?>
            <input type="hidden" name="action" value="rinascente_member_review_submit">
            <div class="member-form-grid">
              <div class="member-form-field">
                <label for="review_product">対象製品</label>
                <select id="review_product" name="review_product">
                  <?php foreach ( $available_products as $product_key => $product_label ) : ?>
                  <option value="<?php echo esc_attr( $product_key ); ?>" <?php selected( $selected_product, $product_key ); ?>><?php echo esc_html( $product_label ); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="member-form-field">
                <label for="review_rating">評価</label>
                <select id="review_rating" name="review_rating">
                  <option value="5">5 / 5</option>
                  <option value="4">4 / 5</option>
                  <option value="3">3 / 5</option>
                  <option value="2">2 / 5</option>
                  <option value="1">1 / 5</option>
                </select>
              </div>
              <div class="member-form-field">
                <label for="review_facility_name">施設名</label>
                <input id="review_facility_name" name="review_facility_name" type="text" value="<?php echo esc_attr( $facility_name ); ?>">
              </div>
              <div class="member-form-field">
                <label for="review_facility_type">施設種別</label>
                <input id="review_facility_type" name="review_facility_type" type="text" value="<?php echo esc_attr( $facility_type ); ?>">
              </div>
              <div class="member-form-field">
                <label for="review_adoption_period">導入時期</label>
                <select id="review_adoption_period" name="review_adoption_period">
                  <?php foreach ( $review_periods as $period_key => $period_label ) : ?>
                  <option value="<?php echo esc_attr( $period_key ); ?>"><?php echo esc_html( $period_label ); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="member-form-field">
                <label for="review_tags">タグ</label>
                <input id="review_tags" name="review_tags" type="text" placeholder="例: スタンド型, 病院, 効率改善">
              </div>
            </div>
            <div class="member-form-field" style="margin-top:14px;">
              <label for="review_body">レビュー本文</label>
              <textarea id="review_body" name="review_body" placeholder="製品の使用感や導入効果、他施設へのおすすめポイントをご記入ください。"></textarea>
            </div>
            <div style="margin-top:12px;display:flex;justify-content:flex-end;">
              <button type="submit" class="btn btn-outline-dark btn-sm">投稿する</button>
            </div>
          </form>
        </div>
      </section>
      <?php endif; ?>
    </main>
  </div>

  <div class="video-modal" id="videoModal">
    <div class="modal-inner">
      <button class="modal-close" id="modalClose">✕</button>
      <div class="modal-iframe-wrap">
        <iframe id="modalIframe" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
      </div>
    </div>
  </div>

  <script>
    (function() {
      var toggleBtn = document.getElementById('spNavToggle');
      var navList = document.getElementById('sidebarNav');
      if (toggleBtn && navList) {
        toggleBtn.addEventListener('click', function() {
          navList.classList.toggle('sp-open');
          toggleBtn.classList.toggle('is-open');
        });
      }

      var navLinks = document.querySelectorAll('#sidebarNav a');
      for (var i = 0; i < navLinks.length; i++) {
        navLinks[i].addEventListener('click', function(e) {
          var href = this.getAttribute('href');
          if (href && href.charAt(0) === '#') {
            e.preventDefault();
            for (var j = 0; j < navLinks.length; j++) navLinks[j].classList.remove('active');
            this.classList.add('active');
            if (navList) navList.classList.remove('sp-open');
            var target = document.querySelector(href);
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
      }
    })();

    (function() {
      var modal = document.getElementById('videoModal');
      var iframe = document.getElementById('modalIframe');
      var closeBtn = document.getElementById('modalClose');
      document.querySelectorAll('.video-card').forEach(function(card) {
        card.addEventListener('click', function() {
          var vid = card.dataset.videoId;
          if (!vid) return;
          iframe.src = 'https://www.youtube.com/embed/' + vid + '?autoplay=1&rel=0';
          modal.classList.add('open');
          document.body.style.overflow = 'hidden';
        });
      });
      function closeModal() {
        modal.classList.remove('open');
        iframe.src = '';
        document.body.style.overflow = '';
      }
      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (modal) modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
      document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeModal(); });
    })();
  </script>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="footer-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="Rinascente" style="height:32px;vertical-align:middle;margin-right:4px;">Rinascente</div>
          <div class="footer-tagline">Shared Member Site</div>
          <p>
            <span style="white-space:nowrap;"><?php echo esc_html( $shared_products_label ); ?> の会員コンテンツを</span><span style="white-space:nowrap;">一元管理しています。</span><br>
            <?php echo esc_html( get_theme_mod( 'company_address', '○○○○' ) ); ?><br>
            <span style="white-space:nowrap;">TEL: <?php echo esc_html( get_theme_mod( 'company_tel', '0859-00-1234' ) ); ?></span>
          </p>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>Member</h4>
          <ul>
            <li><a href="<?php echo esc_url( $member_dashboard_url ); ?>">マイページ</a></li>
            <?php foreach ( $available_products as $product_key => $product_label ) : ?>
            <li><a href="<?php echo esc_url( $member_product_tab_url( $product_key ) ); ?>"><?php echo esc_html( $product_label ); ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">お問い合わせ</a></li>
            <li><a href="<?php echo esc_url( $member_logout_url ); ?>">Logout</a></li>
          </ul>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>Public Sites</h4>
          <ul>
            <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Corporate</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>">YUMEHO</a></li>
            <li><a href="<?php echo esc_url( home_url( '/cases/' ) ); ?>">Cases</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Rinascente. All Rights Reserved.</span>
        <span><?php echo esc_html( get_theme_mod( 'company_name', 'Rinascente Inc.' ) ); ?></span>
      </div>
    </div>
  </footer>
<?php wp_footer(); ?>
</body>
</html>
