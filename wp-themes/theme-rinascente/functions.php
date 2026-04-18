<?php
/**
 * Rinascente Theme Functions
 *
 * @package Rinascente
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'RINASCENTE_VERSION', '1.0.0' );

if ( ! defined( 'RINASCENTE_ENABLE_MEMBER_REVIEWS' ) ) {
    define( 'RINASCENTE_ENABLE_MEMBER_REVIEWS', false );
}

if ( ! defined( 'RINASCENTE_ENABLE_CASE_STUDY_ADMIN' ) ) {
    define( 'RINASCENTE_ENABLE_CASE_STUDY_ADMIN', false );
}

if ( ! defined( 'RINASCENTE_ENABLE_MICA30' ) ) {
    define( 'RINASCENTE_ENABLE_MICA30', false );
}

function rinascente_case_study_admin_enabled() {
    return defined( 'RINASCENTE_ENABLE_CASE_STUDY_ADMIN' ) && RINASCENTE_ENABLE_CASE_STUDY_ADMIN;
}

function rinascente_mica30_enabled() {
    return defined( 'RINASCENTE_ENABLE_MICA30' ) && RINASCENTE_ENABLE_MICA30;
}

function rinascente_shared_member_products_label() {
    return rinascente_mica30_enabled() ? 'YUMEHO / MICA30' : 'YUMEHO';
}

function rinascente_company_products_default() {
    return rinascente_mica30_enabled() ? 'YUMEHO, MICA30' : 'YUMEHO';
}

function rinascente_company_products_text_default() {
    return rinascente_mica30_enabled()
        ? "YUMEHO（歩行リハビリ支援システム）\nMICA30（多相電動式造影剤注入装置）"
        : 'YUMEHO（歩行リハビリ支援システム）';
}

function rinascente_company_products_text() {
    $default_products = rinascente_company_products_text_default();
    $products         = trim( (string) get_theme_mod( 'company_products', $default_products ) );

    if ( rinascente_mica30_enabled() ) {
        return '' !== $products ? $products : $default_products;
    }

    $products = preg_replace( '/^.*MICA30.*(?:\R|$)/mu', '', $products );
    $products = trim( preg_replace( "/\R{2,}/u", "\n", (string) $products ) );

    return '' !== $products ? $products : $default_products;
}

function rinascente_site_admin_menu_slug() {
    return 'rinascente-site-admin';
}

function rinascente_member_admin_menu_slug() {
    return 'rinascente-member-admin';
}

function rinascente_admin_hub_count_text( $post_type ) {
    $counts    = wp_count_posts( $post_type );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;

    return sprintf( '公開 %d件 / 下書き %d件', $published, $drafts );
}

function rinascente_admin_hub_styles() {
    ?>
    <style>
    .rinascente-admin-hub {
        max-width: 1180px;
    }
    .rinascente-admin-hub__lead {
        margin: 12px 0 0;
        max-width: 72ch;
        color: #475569;
        line-height: 1.9;
    }
    .rinascente-admin-hub__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    .rinascente-admin-hub__card {
        padding: 20px 22px;
        border: 1px solid #d8e3f0;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    .rinascente-admin-hub__eyebrow {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0068b7;
    }
    .rinascente-admin-hub__card h2,
    .rinascente-admin-hub__card h3 {
        margin: 0 0 8px;
        font-size: 18px;
        line-height: 1.5;
    }
    .rinascente-admin-hub__card p {
        margin: 0 0 14px;
        color: #475569;
        line-height: 1.8;
    }
    .rinascente-admin-hub__meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: #eef5fb;
        color: #00538f;
        font-size: 12px;
        font-weight: 700;
    }
    .rinascente-admin-hub__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
    .rinascente-admin-hub__dashboard-list {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 8px;
    }
    .rinascente-admin-hub__dashboard-list a {
        font-weight: 600;
        text-decoration: none;
    }
    </style>
    <?php
}

function rinascente_render_site_admin_page() {
    $company_post_id = rinascente_company_profile_post_id( true );
    $cards           = array(
        array(
            'eyebrow'     => 'Corporate',
            'title'       => 'ニュース',
            'description' => 'お知らせやプレス向けの更新を管理します。まずは一覧説明文、代表画像、カテゴリーを入れる運用にそろえています。',
            'meta'        => rinascente_admin_hub_count_text( 'news' ),
            'primary'     => array(
                'label' => 'ニュース一覧',
                'url'   => admin_url( 'edit.php?post_type=news' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=news' ),
            ),
        ),
        array(
            'eyebrow'     => 'Editorial',
            'title'       => 'コラム',
            'description' => 'コラム本文、画像、YUMEHO連携設定をまとめて管理します。本文テンプレートや表の挿入補助も使えます。',
            'meta'        => rinascente_admin_hub_count_text( 'column' ),
            'primary'     => array(
                'label' => 'コラム一覧',
                'url'   => admin_url( 'edit.php?post_type=column' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=column' ),
            ),
        ),
        array(
            'eyebrow'     => 'Profile',
            'title'       => '会社概要',
            'description' => '会社情報はここ1か所だけ更新すれば、会社概要ページや共有データに反映されます。',
            'meta'        => '1件だけ管理',
            'primary'     => array(
                'label' => '会社概要を編集',
                'url'   => $company_post_id ? admin_url( 'post.php?post=' . $company_post_id . '&action=edit' ) : admin_url( 'edit.php?post_type=company_profile' ),
            ),
            'secondary'   => array(
                'label' => '固定ページ一覧',
                'url'   => admin_url( 'edit.php?post_type=page' ),
            ),
        ),
        array(
            'eyebrow'     => 'Theme',
            'title'       => 'テーマ設定',
            'description' => '追跡コードや関連サイト URL など、ページ本文以外のテーマ設定を確認します。会社情報はここではなく会社概要で編集します。',
            'meta'        => '必要なときだけ',
            'primary'     => array(
                'label' => 'テーマ設定を開く',
                'url'   => admin_url( 'customize.php' ),
            ),
            'secondary'   => array(
                'label' => '固定ページ一覧',
                'url'   => admin_url( 'edit.php?post_type=page' ),
            ),
        ),
    );

    echo '<div class="wrap rinascente-admin-hub">';
    echo '<h1>サイト更新</h1>';
    echo '<p class="rinascente-admin-hub__lead">コーポレートサイトの更新作業を、用途ごとに迷わず開けるようにまとめています。日々の更新は下のカードから入ると、一覧と編集画面の往復が少なくなります。</p>';
    rinascente_admin_hub_styles();
    echo '<div class="rinascente-admin-hub__grid">';
    foreach ( $cards as $card ) {
        echo '<section class="rinascente-admin-hub__card">';
        echo '<p class="rinascente-admin-hub__eyebrow">' . esc_html( $card['eyebrow'] ) . '</p>';
        echo '<h2>' . esc_html( $card['title'] ) . '</h2>';
        echo '<p>' . esc_html( $card['description'] ) . '</p>';
        echo '<span class="rinascente-admin-hub__meta">' . esc_html( $card['meta'] ) . '</span>';
        echo '<div class="rinascente-admin-hub__actions">';
        echo '<a class="button button-primary" href="' . esc_url( $card['primary']['url'] ) . '">' . esc_html( $card['primary']['label'] ) . '</a>';
        echo '<a class="button button-secondary" href="' . esc_url( $card['secondary']['url'] ) . '">' . esc_html( $card['secondary']['label'] ) . '</a>';
        echo '</div>';
        echo '</section>';
    }
    echo '</div></div>';
}

function rinascente_render_member_admin_page() {
    $cards = array(
        array(
            'eyebrow'     => 'Contract',
            'title'       => '契約・購入履歴',
            'description' => '施設会員ごとの契約内容、注文番号、支払い状況を管理します。重複注文番号の検知もここで行います。',
            'meta'        => rinascente_admin_hub_count_text( 'contract' ),
            'primary'     => array(
                'label' => '契約一覧',
                'url'   => admin_url( 'edit.php?post_type=contract' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=contract' ),
            ),
        ),
        array(
            'eyebrow'     => 'Member',
            'title'       => '会員向けコンテンツ',
            'description' => '動画、資料、サポート情報はここから更新します。会員ページではここで入力した内容がそのまま表示に使われます。',
            'meta'        => sprintf(
                '動画 %d件 / 資料 %d件 / 情報 %d件',
                isset( wp_count_posts( 'member_video' )->publish ) ? (int) wp_count_posts( 'member_video' )->publish : 0,
                isset( wp_count_posts( 'member_document' )->publish ) ? (int) wp_count_posts( 'member_document' )->publish : 0,
                isset( wp_count_posts( 'member_notice' )->publish ) ? (int) wp_count_posts( 'member_notice' )->publish : 0
            ),
            'primary'     => array(
                'label' => '会員限定動画',
                'url'   => admin_url( 'edit.php?post_type=member_video' ),
            ),
            'secondary'   => array(
                'label' => '会員限定資料',
                'url'   => admin_url( 'edit.php?post_type=member_document' ),
            ),
        ),
        array(
            'eyebrow'     => 'Accounts',
            'title'       => '施設会員',
            'description' => '新しい施設会員の発行と、既存会員の確認をまとめています。施設会員追加フォームはここから入るように整理しました。',
            'meta'        => sprintf( '施設会員 %d人', count( get_users( array( 'role' => 'facility_member', 'fields' => 'ids' ) ) ) ),
            'primary'     => array(
                'label' => '施設会員を追加',
                'url'   => admin_url( 'admin.php?page=' . rinascente_member_create_page_slug() ),
            ),
            'secondary'   => array(
                'label' => 'ユーザー一覧',
                'url'   => admin_url( 'users.php' ),
            ),
        ),
    );

    echo '<div class="wrap rinascente-admin-hub">';
    echo '<h1>会員サイト運用</h1>';
    echo '<p class="rinascente-admin-hub__lead">会員向けの契約管理、配布コンテンツ、施設会員の管理をまとめています。製品マスターは左メニューの独立項目から管理できます。</p>';
    rinascente_admin_hub_styles();
    echo '<div class="rinascente-admin-hub__grid">';
    foreach ( $cards as $card ) {
        echo '<section class="rinascente-admin-hub__card">';
        echo '<p class="rinascente-admin-hub__eyebrow">' . esc_html( $card['eyebrow'] ) . '</p>';
        echo '<h2>' . esc_html( $card['title'] ) . '</h2>';
        echo '<p>' . esc_html( $card['description'] ) . '</p>';
        echo '<span class="rinascente-admin-hub__meta">' . esc_html( $card['meta'] ) . '</span>';
        echo '<div class="rinascente-admin-hub__actions">';
        echo '<a class="button button-primary" href="' . esc_url( $card['primary']['url'] ) . '">' . esc_html( $card['primary']['label'] ) . '</a>';
        echo '<a class="button button-secondary" href="' . esc_url( $card['secondary']['url'] ) . '">' . esc_html( $card['secondary']['label'] ) . '</a>';
        echo '</div>';
        echo '</section>';
    }
    echo '</div></div>';
}

function rinascente_register_admin_hub_menus() {
    add_menu_page(
        'サイト更新',
        'サイト更新',
        'edit_pages',
        rinascente_site_admin_menu_slug(),
        'rinascente_render_site_admin_page',
        'dashicons-admin-site-alt3',
        5
    );

    add_submenu_page(
        rinascente_site_admin_menu_slug(),
        '更新ダッシュボード',
        '更新ダッシュボード',
        'edit_pages',
        rinascente_site_admin_menu_slug(),
        'rinascente_render_site_admin_page'
    );

    add_submenu_page(
        rinascente_site_admin_menu_slug(),
        '固定ページ',
        '固定ページ',
        'edit_pages',
        'edit.php?post_type=page'
    );

    add_submenu_page(
        rinascente_site_admin_menu_slug(),
        'テーマ設定（追跡コードなど）',
        'テーマ設定（追跡コードなど）',
        'edit_theme_options',
        'customize.php'
    );

    add_menu_page(
        '会員サイト運用',
        '会員サイト運用',
        'edit_posts',
        rinascente_member_admin_menu_slug(),
        'rinascente_render_member_admin_page',
        'dashicons-groups',
        26
    );

    add_submenu_page(
        rinascente_member_admin_menu_slug(),
        '会員サイト運用',
        '運用ダッシュボード',
        'edit_posts',
        rinascente_member_admin_menu_slug(),
        'rinascente_render_member_admin_page'
    );
}
add_action( 'admin_menu', 'rinascente_register_admin_hub_menus', 8 );

function rinascente_cleanup_admin_navigation() {
    global $submenu;
    if ( isset( $submenu['themes.php'] ) && is_array( $submenu['themes.php'] ) ) {
        foreach ( $submenu['themes.php'] as $index => $item ) {
            if ( isset( $item[2] ) && 'customize.php' === $item[2] ) {
                $submenu['themes.php'][ $index ][0] = 'テーマ設定（追跡コードなど）';
            }
        }
    }
}
add_action( 'admin_menu', 'rinascente_cleanup_admin_navigation', 999 );

add_filter( 'custom_menu_order', '__return_true' );

function rinascente_move_core_admin_menus_below_rank_math( $menu_order ) {
    if ( ! is_array( $menu_order ) || empty( $menu_order ) ) {
        return $menu_order;
    }

    $rank_math_index = null;
    foreach ( $menu_order as $index => $menu_slug ) {
        if ( false !== strpos( (string) $menu_slug, 'rank-math' ) ) {
            $rank_math_index = $index;
            break;
        }
    }

    if ( null === $rank_math_index ) {
        return $menu_order;
    }

    $target_slugs = array(
        'edit.php',
        'upload.php',
        'edit.php?post_type=page',
        'edit-comments.php',
    );

    $available_targets = array_values( array_intersect( $target_slugs, $menu_order ) );
    if ( empty( $available_targets ) ) {
        return $menu_order;
    }

    $reordered_menu = array_values( array_diff( $menu_order, $available_targets ) );

    foreach ( $reordered_menu as $index => $menu_slug ) {
        if ( false !== strpos( (string) $menu_slug, 'rank-math' ) ) {
            array_splice( $reordered_menu, $index + 1, 0, $available_targets );
            return $reordered_menu;
        }
    }

    return $menu_order;
}
add_filter( 'menu_order', 'rinascente_move_core_admin_menus_below_rank_math', 20 );

function rinascente_cleanup_admin_bar( $wp_admin_bar ) {
    if ( ! $wp_admin_bar instanceof WP_Admin_Bar ) {
        return;
    }

    $wp_admin_bar->remove_node( 'new-post' );
    $wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'rinascente_cleanup_admin_bar', 999 );

function rinascente_dashboard_quick_links_widget() {
    echo '<div class="rinascente-admin-hub">';
    rinascente_admin_hub_styles();
    echo '<p style="margin-top:0;">日常の更新は、まずこの2つの入口から入ると迷いにくくなります。</p>';
    echo '<ul class="rinascente-admin-hub__dashboard-list">';
    echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=' . rinascente_site_admin_menu_slug() ) ) . '">サイト更新</a> ニュース、コラム、会社概要、固定ページの更新</li>';
    echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=product_master' ) ) . '">製品マスター</a> 商品名、価格、選択肢、レール長などの共通マスターを更新</li>';
    echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=' . rinascente_member_admin_menu_slug() ) ) . '">会員サイト運用</a> 契約、会員向けコンテンツ、施設会員の更新</li>';
    echo '</ul>';
    echo '</div>';
}

function rinascente_dashboard_cleanup() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    wp_add_dashboard_widget(
        'rinascente_admin_quick_links',
        'まず使う更新先',
        'rinascente_dashboard_quick_links_widget'
    );
}
add_action( 'wp_dashboard_setup', 'rinascente_dashboard_cleanup' );

function rinascente_raw_theme_mod( $key, $default = '' ) {
    $stylesheet = get_option( 'stylesheet' );
    $mods       = get_option( 'theme_mods_' . $stylesheet );

    if ( ! is_array( $mods ) || ! array_key_exists( $key, $mods ) ) {
        return $default;
    }

    $value = maybe_unserialize( $mods[ $key ] );

    return is_scalar( $value ) ? (string) $value : $default;
}

function rinascente_company_profile_fields() {
    return array(
        'company_name' => array(
            'label'       => '会社名',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '株式会社リナシェンテ',
        ),
        'company_name_en' => array(
            'label'       => '会社名（英語）',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => 'Rinascente Inc.',
        ),
        'company_ceo' => array(
            'label'       => '代表者名',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '',
        ),
        'company_founded' => array(
            'label'       => '設立年月',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '',
        ),
        'company_capital' => array(
            'label'       => '資本金',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '',
        ),
        'company_address' => array(
            'label'       => '所在地',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '',
        ),
        'company_tel' => array(
            'label'       => '電話番号',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '0859-00-1234',
        ),
        'company_fax' => array(
            'label'       => 'FAX番号',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '',
        ),
        'company_business' => array(
            'label'       => '事業内容',
            'type'        => 'textarea',
            'rows'        => 5,
            'section'     => 'company',
            'default'     => '医療・福祉機器の企画・販売',
        ),
        'company_products' => array(
            'label'       => '主要製品',
            'type'        => 'textarea',
            'rows'        => 4,
            'section'     => 'company',
            'default'     => rinascente_company_products_text_default(),
        ),
        'company_hours' => array(
            'label'       => '受付時間',
            'type'        => 'text',
            'section'     => 'company',
            'default'     => '平日 9:00〜17:00',
        ),
        'support_name' => array(
            'label'       => '会員サポート窓口名',
            'type'        => 'text',
            'section'     => 'support',
            'default'     => 'Rinascente カスタマーサポート',
        ),
        'support_tel' => array(
            'label'       => '会員サポート電話番号',
            'type'        => 'text',
            'section'     => 'support',
            'default'     => '',
        ),
        'support_hours' => array(
            'label'       => '会員サポート受付時間',
            'type'        => 'text',
            'section'     => 'support',
            'default'     => '平日 9:00〜17:00',
        ),
    );
}

function rinascente_company_profile_meta_key( $key ) {
    return '_rinascente_company_profile_' . sanitize_key( $key );
}

function rinascente_company_profile_post_id( $create = false ) {
    static $cached_post_id = null;
    $is_cli = defined( 'WP_CLI' ) && WP_CLI;

    if ( null !== $cached_post_id && ( ! $create || $cached_post_id > 0 ) ) {
        return $cached_post_id;
    }

    $option_key = 'rinascente_company_profile_post_id';
    $post_id    = (int) get_option( $option_key, 0 );

    if ( $post_id > 0 && 'company_profile' === get_post_type( $post_id ) ) {
        $cached_post_id = $post_id;
        return $cached_post_id;
    }

    $existing_ids = get_posts(
        array(
            'post_type'              => 'company_profile',
            'post_status'            => array( 'publish', 'future', 'draft', 'pending', 'private' ),
            'posts_per_page'         => 1,
            'fields'                 => 'ids',
            'orderby'                => array(
                'menu_order' => 'ASC',
                'date'       => 'ASC',
                'ID'         => 'ASC',
            ),
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'suppress_filters'       => false,
        )
    );

    if ( ! empty( $existing_ids ) ) {
        $cached_post_id = (int) $existing_ids[0];
        update_option( $option_key, $cached_post_id, false );
        return $cached_post_id;
    }

    if ( ! $create || ( ! is_admin() && ! $is_cli ) || ( ! $is_cli && ! current_user_can( 'edit_theme_options' ) ) ) {
        $cached_post_id = 0;
        return 0;
    }

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'company_profile',
            'post_status' => 'publish',
            'post_title'  => '会社概要',
            'menu_order'  => 0,
        ),
        true
    );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        $cached_post_id = 0;
        return 0;
    }

    $cached_post_id = (int) $post_id;
    update_option( $option_key, $cached_post_id, false );
    rinascente_seed_company_profile_post( $cached_post_id );

    return $cached_post_id;
}

function rinascente_seed_company_profile_post( $post_id ) {
    foreach ( rinascente_company_profile_fields() as $key => $field ) {
        $value = get_post_meta( $post_id, rinascente_company_profile_meta_key( $key ), true );
        if ( '' !== trim( (string) $value ) ) {
            continue;
        }

        $legacy_value = rinascente_raw_theme_mod( $key, '__rinascente_missing__' );
        if ( '__rinascente_missing__' === $legacy_value || '' === trim( (string) $legacy_value ) ) {
            $legacy_value = $field['default'] ?? '';
        }

        if ( '' !== trim( (string) $legacy_value ) ) {
            update_post_meta( $post_id, rinascente_company_profile_meta_key( $key ), $legacy_value );
        }
    }
}

function rinascente_company_profile_data() {
    static $cached_data = null;

    if ( null !== $cached_data ) {
        return $cached_data;
    }

    $post_id     = rinascente_company_profile_post_id();
    $cached_data = array();

    foreach ( rinascente_company_profile_fields() as $key => $field ) {
        $value = '';

        if ( $post_id > 0 ) {
            $value = get_post_meta( $post_id, rinascente_company_profile_meta_key( $key ), true );
        }

        if ( '' === trim( (string) $value ) ) {
            $legacy_value = rinascente_raw_theme_mod( $key, '__rinascente_missing__' );
            if ( '__rinascente_missing__' !== $legacy_value && '' !== trim( (string) $legacy_value ) ) {
                $value = $legacy_value;
            } else {
                $value = $field['default'] ?? '';
            }
        }

        $cached_data[ $key ] = (string) $value;
    }

    return $cached_data;
}

function rinascente_filter_company_theme_mod( $value ) {
    $filter_name = (string) current_filter();
    if ( ! str_starts_with( $filter_name, 'theme_mod_' ) ) {
        return $value;
    }

    $key  = substr( $filter_name, strlen( 'theme_mod_' ) );
    $data = rinascente_company_profile_data();

    if ( isset( $data[ $key ] ) && '' !== trim( (string) $data[ $key ] ) ) {
        return $data[ $key ];
    }

    return $value;
}

function rinascente_register_company_theme_mod_filters() {
    foreach ( array_keys( rinascente_company_profile_fields() ) as $key ) {
        add_filter( 'theme_mod_' . $key, 'rinascente_filter_company_theme_mod' );
    }
}
add_action( 'after_setup_theme', 'rinascente_register_company_theme_mod_filters', 20 );

function rinascente_ensure_company_profile_post() {
    if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    rinascente_company_profile_post_id( true );
}
add_action( 'admin_init', 'rinascente_ensure_company_profile_post' );

function rinascente_case_plain_summary( $content ) {
    $content = str_replace(
        array( '<br>', '<br/>', '<br />', '</p>', '</li>' ),
        ' ',
        (string) $content
    );
    $content = wp_strip_all_tags( $content, true );
    $content = preg_replace( '/\s+/u', ' ', $content );

    return trim( (string) $content );
}

function rinascente_shared_case_facility_name( $case ) {
    if ( ! is_array( $case ) ) {
        return '';
    }

    $facility_name = trim( (string) ( $case['facility_name'] ?? $case['facility'] ?? '' ) );
    if ( '' !== $facility_name ) {
        return $facility_name;
    }

    return trim( (string) ( $case['facility_type'] ?? '' ) );
}

function rinascente_shared_case_facility_type( $case ) {
    if ( ! is_array( $case ) ) {
        return '';
    }

    return trim( (string) ( $case['facility_type'] ?? '' ) );
}

function rinascente_shared_case_meta_parts( $case ) {
    if ( ! is_array( $case ) ) {
        return array();
    }

    $parts = array();

    foreach ( array( 'install_date', 'location' ) as $key ) {
        $value = trim( (string) ( $case[ $key ] ?? '' ) );
        if ( '' !== $value ) {
            $parts[] = $value;
        }
    }

    return $parts;
}

function rinascente_shared_case_feature_summary( $case ) {
    if ( ! is_array( $case ) ) {
        return '';
    }

    foreach ( array( 'change', 'excerpt', 'challenge', 'reason' ) as $key ) {
        $summary = rinascente_case_plain_summary( $case[ $key ] ?? '' );
        if ( '' !== $summary ) {
            return $summary;
        }
    }

    return '';
}

function rinascente_shared_case_metrics( $case ) {
    if ( ! is_array( $case ) ) {
        return array();
    }

    $metrics = array();

    for ( $index = 1; $index <= 3; $index++ ) {
        $label = trim( (string) ( $case[ 'metric_' . $index . '_label' ] ?? '' ) );
        $value = trim( (string) ( $case[ 'metric_' . $index . '_value' ] ?? '' ) );

        if ( '' === $label && '' === $value ) {
            continue;
        }

        $metrics[] = array(
            'label' => $label,
            'value' => $value,
        );
    }

    return $metrics;
}

function rinascente_contains_mica30_text( $value ) {
    $value = trim( wp_strip_all_tags( (string) $value ) );
    if ( '' === $value ) {
        return false;
    }

    return 1 === preg_match( '/MICA30/ui', $value );
}

function rinascente_asset_version( $relative_path ) {
    $absolute_path = get_template_directory() . '/' . ltrim( $relative_path, '/' );

    if ( file_exists( $absolute_path ) ) {
        return (string) filemtime( $absolute_path );
    }

    return RINASCENTE_VERSION;
}

function rinascente_prepare_news_content( $content ) {
    $company_name  = get_theme_mod( 'company_name', '株式会社リナシェンテ' );
    $support_tel   = trim( (string) get_theme_mod( 'support_tel', '' ) );
    $support_hours = trim( (string) get_theme_mod( 'support_hours', '' ) );

    if ( '' === $support_tel ) {
        $support_tel = get_theme_mod( 'company_tel', '0859-00-1234' );
    }

    if ( '' === $support_hours ) {
        $support_hours = get_theme_mod( 'company_hours', '平日 9:00〜17:00' );
    }

    return strtr(
        (string) $content,
        array(
            'Rinascente株式会社' => esc_html( $company_name ),
            '株式会社Rinascente' => esc_html( $company_name ),
            '0859-00-1234'       => esc_html( $support_tel ),
            '平日 9:00〜17:30'      => esc_html( $support_hours ),
            '平日 9:00〜17:00'      => esc_html( $support_hours ),
        )
    );
}

/**
 * Resolve sibling-site URLs for Local and production-like environments.
 */
function rinascente_related_site_url( $site, $path = '/' ) {
    if ( 'corporate' === $site ) {
        return home_url( $path );
    }

    $configured = get_theme_mod( 'related_' . $site . '_url', '' );
    if ( $configured ) {
        return trailingslashit( $configured ) . ltrim( $path, '/' );
    }

    $host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
    if ( $host && ( str_ends_with( $host, '.local' ) || 'localhost' === $host ) ) {
        if ( 'yumeho' === $site ) {
            return 'http://yumeho.local/' . ltrim( $path, '/' );
        }

        if ( 'mica30' === $site ) {
            return 'http://mica30.local/' . ltrim( $path, '/' );
        }
    }

    return '#';
}

function rinascente_related_site_request_args() {
    $request_args = array(
        'timeout'            => 8,
        'reject_unsafe_urls' => true,
    );

    $basic_auth_user = trim( (string) get_theme_mod( 'related_sites_basic_auth_user', '' ) );
    $basic_auth_pass = (string) get_theme_mod( 'related_sites_basic_auth_pass', '' );

    if ( '' !== $basic_auth_user && '' !== $basic_auth_pass ) {
        $request_args['headers'] = array(
            'Authorization' => 'Basic ' . base64_encode( $basic_auth_user . ':' . $basic_auth_pass ),
        );
    }

    return $request_args;
}

function rinascente_register_product_catalog_rest_route() {
    register_rest_route(
        'rinascente/v1',
        '/product-catalog',
        array(
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
            'args'                => array(
                'product_key' => array(
                    'sanitize_callback' => 'sanitize_key',
                ),
                'category'    => array(
                    'sanitize_callback' => static function( $value ) {
                        if ( is_array( $value ) ) {
                            return array_filter( array_map( 'sanitize_key', $value ) );
                        }

                        $parts = array_map( 'trim', explode( ',', (string) $value ) );
                        return array_filter( array_map( 'sanitize_key', $parts ) );
                    },
                ),
            ),
            'callback'            => 'rinascente_product_catalog_rest_response',
        )
    );
}
add_action( 'rest_api_init', 'rinascente_register_product_catalog_rest_route' );

function rinascente_product_catalog_rest_response( WP_REST_Request $request ) {
    $product_key = sanitize_key( (string) $request->get_param( 'product_key' ) );
    $categories  = $request->get_param( 'category' );

    if ( ! is_array( $categories ) ) {
        $categories = array();
    }

    $items = function_exists( 'rinascente_member_get_product_catalog_items' )
        ? rinascente_member_get_product_catalog_items(
            array(
                'post_status' => array( 'publish' ),
                'product_key' => $product_key,
                'category'    => $categories,
            )
        )
        : array();

    $items = array_values( array_filter( array_map( 'rinascente_public_product_catalog_item_data', $items ) ) );

    return rest_ensure_response(
        array(
            'version' => (string) get_option( 'rinascente_product_catalog_version', '' ),
            'count'   => count( $items ),
            'items'   => $items,
        )
    );
}

function rinascente_public_product_catalog_item_data( $item ) {
    if ( ! is_array( $item ) ) {
        return array();
    }

    return array(
        'id'                   => isset( $item['id'] ) ? (int) $item['id'] : 0,
        'title'                => isset( $item['title'] ) ? (string) $item['title'] : '',
        'slug'                 => isset( $item['slug'] ) ? (string) $item['slug'] : '',
        'product_key'          => isset( $item['product_key'] ) ? sanitize_key( $item['product_key'] ) : '',
        'product_label'        => isset( $item['product_label'] ) ? sanitize_text_field( $item['product_label'] ) : '',
        'category'             => isset( $item['category'] ) ? sanitize_key( $item['category'] ) : '',
        'category_label'       => isset( $item['category_label'] ) ? sanitize_text_field( $item['category_label'] ) : '',
        'sort_order'           => isset( $item['sort_order'] ) ? absint( $item['sort_order'] ) : 999,
        'code'                 => isset( $item['code'] ) ? sanitize_key( $item['code'] ) : '',
        'display_name'         => isset( $item['display_name'] ) ? sanitize_text_field( $item['display_name'] ) : '',
        'short_name'           => isset( $item['short_name'] ) ? sanitize_text_field( $item['short_name'] ) : '',
        'spec'                 => isset( $item['spec'] ) ? sanitize_text_field( $item['spec'] ) : '',
        'install_type'         => isset( $item['install_type'] ) ? sanitize_key( $item['install_type'] ) : '',
        'install_type_label'   => isset( $item['install_type_label'] ) ? sanitize_text_field( $item['install_type_label'] ) : '',
        'max_rail_length'      => isset( $item['max_rail_length'] ) ? absint( $item['max_rail_length'] ) : 0,
        'rail_length_options'  => array_values( array_map( 'absint', (array) ( $item['rail_length_options'] ?? array() ) ) ),
        'unit_price'           => isset( $item['unit_price'] ) ? (int) $item['unit_price'] : 0,
        'rail_price_per_m'     => isset( $item['rail_price_per_m'] ) ? (int) $item['rail_price_per_m'] : 0,
        'pricing_option_key'   => isset( $item['pricing_option_key'] ) ? sanitize_key( $item['pricing_option_key'] ) : '',
        'unit_label'           => isset( $item['unit_label'] ) ? sanitize_text_field( $item['unit_label'] ) : '',
        'max_quantity'         => isset( $item['max_quantity'] ) ? absint( $item['max_quantity'] ) : 0,
        'selection_type'       => isset( $item['selection_type'] ) ? sanitize_key( $item['selection_type'] ) : '',
        'selection_type_label' => isset( $item['selection_type_label'] ) ? sanitize_text_field( $item['selection_type_label'] ) : '',
    );
}

function rinascente_post_has_product_key( $post, $product_key ) {
    $post = get_post( $post );
    if ( ! $post instanceof WP_Post ) {
        return false;
    }

    $product_key = sanitize_key( $product_key );
    if ( '' === $product_key ) {
        return false;
    }

    if ( $product_key === sanitize_key( (string) get_post_meta( $post->ID, '_rinascente_product_key', true ) ) ) {
        return true;
    }

    if ( taxonomy_exists( 'product_type' ) ) {
        $term_slugs = wp_get_object_terms(
            $post->ID,
            'product_type',
            array(
                'fields' => 'slugs',
            )
        );

        if ( ! is_wp_error( $term_slugs ) && in_array( $product_key, $term_slugs, true ) ) {
            return true;
        }
    }

    return false;
}

function rinascente_is_mica30_related_post( $post ) {
    if ( rinascente_mica30_enabled() ) {
        return false;
    }

    $post = get_post( $post );
    if ( ! $post instanceof WP_Post ) {
        return false;
    }

    if ( rinascente_post_has_product_key( $post, 'mica30' ) ) {
        return true;
    }

    if ( rinascente_contains_mica30_text( $post->post_title ) ) {
        return true;
    }

    if ( in_array( $post->post_type, array( 'news', 'case_study' ), true ) && rinascente_contains_mica30_text( $post->post_excerpt ) ) {
        return true;
    }

    return false;
}

function rinascente_hidden_mica30_post_ids( $post_type ) {
    if ( rinascente_mica30_enabled() ) {
        return array();
    }

    $post_type = sanitize_key( $post_type );
    if ( '' === $post_type ) {
        return array();
    }

    $posts = get_posts(
        array(
            'post_type'              => $post_type,
            'post_status'            => 'publish',
            'posts_per_page'         => -1,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'suppress_filters'       => false,
        )
    );

    $hidden_ids = array();
    foreach ( $posts as $post_id ) {
        if ( rinascente_is_mica30_related_post( $post_id ) ) {
            $hidden_ids[] = (int) $post_id;
        }
    }

    return $hidden_ids;
}

function rinascente_exclude_hidden_post_ids_from_query_args( $args, $post_type ) {
    $hidden_ids = rinascente_hidden_mica30_post_ids( $post_type );
    if ( empty( $hidden_ids ) ) {
        return $args;
    }

    $existing_ids       = isset( $args['post__not_in'] ) ? array_map( 'absint', (array) $args['post__not_in'] ) : array();
    $args['post__not_in'] = array_values( array_unique( array_merge( $existing_ids, $hidden_ids ) ) );

    return $args;
}

function rinascente_exclude_hidden_mica30_from_main_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() || rinascente_mica30_enabled() ) {
        return;
    }

    $post_type   = $query->get( 'post_type' );
    $hidden_ids  = array();

    if ( $query->is_post_type_archive( 'news' ) || 'news' === $post_type ) {
        $hidden_ids = array_merge( $hidden_ids, rinascente_hidden_mica30_post_ids( 'news' ) );
    }

    if ( $query->is_post_type_archive( 'case_study' ) || 'case_study' === $post_type ) {
        $hidden_ids = array_merge( $hidden_ids, rinascente_hidden_mica30_post_ids( 'case_study' ) );
    }

    if ( empty( $hidden_ids ) ) {
        return;
    }

    $existing_ids = array_map( 'absint', (array) $query->get( 'post__not_in' ) );
    $query->set( 'post__not_in', array_values( array_unique( array_merge( $existing_ids, $hidden_ids ) ) ) );
}
add_action( 'pre_get_posts', 'rinascente_exclude_hidden_mica30_from_main_query' );

function rinascente_redirect_hidden_mica30_content() {
    if ( is_admin() || wp_doing_ajax() || rinascente_mica30_enabled() || ! is_singular() ) {
        return;
    }

    $post = get_queried_object();
    if ( ! ( $post instanceof WP_Post ) || ! rinascente_is_mica30_related_post( $post ) ) {
        return;
    }

    if ( 'news' === $post->post_type ) {
        $target = get_post_type_archive_link( 'news' );
    } elseif ( 'case_study' === $post->post_type ) {
        $target = home_url( '/cases/' );
    } else {
        $target = home_url( '/' );
    }

    wp_safe_redirect( $target ?: home_url( '/' ), 302 );
    exit;
}
add_action( 'template_redirect', 'rinascente_redirect_hidden_mica30_content', 20 );

function rinascente_fetch_shared_cases( $site = 'yumeho', $limit = 3 ) {
    $endpoint = rinascente_related_site_url( $site, '/wp-json/wp/v2/case_study' );
    if ( ! $endpoint || '#' === $endpoint ) {
        return array();
    }

    $request_url = add_query_arg(
        array(
            'per_page' => max( 1, (int) $limit ),
            '_embed'   => '1',
            'orderby'  => 'date',
            'order'    => 'desc',
        ),
        $endpoint
    );

    $cache_key = 'rinascente_shared_cases_v2_' . md5( $request_url );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached && is_array( $cached ) ) {
        return $cached;
    }

    $response = wp_remote_get( $request_url, rinascente_related_site_request_args() );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return array();
    }

    $items = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $items ) ) {
        return array();
    }

    $cases = array();
    foreach ( $items as $item ) {
        $product_name  = strtoupper( $site );
        $product_slug  = $site;
        $facility_type = '';
        $image_url     = '';

        if ( ! empty( $item['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
            $image_url = (string) $item['_embedded']['wp:featuredmedia'][0]['source_url'];
        }

        if ( ! empty( $item['_embedded']['wp:term'] ) && is_array( $item['_embedded']['wp:term'] ) ) {
            foreach ( $item['_embedded']['wp:term'] as $term_group ) {
                if ( ! is_array( $term_group ) ) {
                    continue;
                }

                foreach ( $term_group as $term ) {
                    if ( ! is_array( $term ) ) {
                        continue;
                    }

                    if ( isset( $term['taxonomy'] ) && 'product_type' === $term['taxonomy'] ) {
                        $product_name = (string) $term['name'];
                        $product_slug = (string) $term['slug'];
                    }

                    if ( isset( $term['taxonomy'] ) && 'facility_type' === $term['taxonomy'] && '' === $facility_type ) {
                        $facility_type = (string) $term['name'];
                    }
                }
            }
        }

        // メタフィールドを取得（YUMEHO 側で REST API 公開済み）
        $meta = isset( $item['meta'] ) && is_array( $item['meta'] ) ? $item['meta'] : array();

        $get_meta = function( $key, $default = '' ) use ( $meta ) {
            $full_key = '_yumeho_case_' . $key;
            if ( isset( $meta[ $full_key ] ) ) {
                return is_array( $meta[ $full_key ] ) ? (string) reset( $meta[ $full_key ] ) : (string) $meta[ $full_key ];
            }
            return $default;
        };

        $facility_name = $get_meta( 'facility_name' );

        // excerpt が空の場合は「変化（After）」or「課題」をフォールバック
        $excerpt = wp_strip_all_tags( (string) ( $item['excerpt']['rendered'] ?? '' ) );
        if ( '' === $excerpt ) {
            $excerpt = rinascente_case_plain_summary( $get_meta( 'change' ) );
            if ( '' === $excerpt ) {
                $excerpt = rinascente_case_plain_summary( $get_meta( 'challenge' ) );
            }
        }

        $cases[] = array(
            'id'             => (int) ( $item['id'] ?? 0 ),
            'title'          => wp_strip_all_tags( (string) ( $item['title']['rendered'] ?? '' ) ),
            'excerpt'        => $excerpt,
            'link'           => esc_url_raw( (string) ( $item['link'] ?? '' ) ),
            'image_url'      => esc_url_raw( $image_url ),
            'product_name'   => $product_name,
            'product_slug'   => $product_slug,
            'facility'       => $facility_name ?: $facility_type,
            'facility_name'  => $facility_name,
            'facility_type'  => $facility_type,
            'site'           => $site,
            // 詳細メタデータ（新規追加）
            'install_date'   => $get_meta( 'install_date' ),
            'location'       => $get_meta( 'location' ),
            'product_model'  => $get_meta( 'product_model' ),
            'challenge'      => $get_meta( 'challenge' ),
            'reason'         => $get_meta( 'reason' ),
            'change'         => $get_meta( 'change' ),
            'ringi_process'  => $get_meta( 'ringi_process' ),
            'metric_1_label' => $get_meta( 'metric_1_label' ),
            'metric_1_value' => $get_meta( 'metric_1_value' ),
            'metric_2_label' => $get_meta( 'metric_2_label' ),
            'metric_2_value' => $get_meta( 'metric_2_value' ),
            'metric_3_label' => $get_meta( 'metric_3_label' ),
            'metric_3_value' => $get_meta( 'metric_3_value' ),
            'pullquote'      => $get_meta( 'pullquote' ),
            'pullquote_speaker' => $get_meta( 'pullquote_speaker' ),
            'is_featured'    => '1' === $get_meta( 'is_featured' ),
            'is_hidden'      => '1' === $get_meta( 'is_hidden' ),
        );
    }

    // 非表示フィルター + おすすめ優先
    $cases = array_filter( $cases, function( $c ) {
        return ! $c['is_hidden'];
    } );
    usort( $cases, function( $a, $b ) {
        if ( $a['is_featured'] !== $b['is_featured'] ) {
            return $a['is_featured'] ? -1 : 1;
        }
        return 0;
    } );
    $cases = array_values( $cases );

    set_transient( $cache_key, $cases, 5 * MINUTE_IN_SECONDS );

    return $cases;
}

function rinascente_register_facility_member_role() {
    if ( null === get_role( 'facility_member' ) ) {
        add_role(
            'facility_member',
            '施設会員',
            array(
                'read' => true,
            )
        );
    }
}
add_action( 'init', 'rinascente_register_facility_member_role' );

function rinascente_member_page_url() {
    return home_url( '/member/' );
}

function rinascente_member_product_page_url( $product = '' ) {
    $url = rinascente_member_page_url();
    if ( $product ) {
        $url = add_query_arg( 'product', sanitize_key( $product ), $url );
    }

    return $url;
}

function rinascente_member_requested_product() {
    $candidates = array();

    if ( isset( $_REQUEST['product'] ) && is_string( $_REQUEST['product'] ) ) {
        $candidates[] = wp_unslash( $_REQUEST['product'] );
    }

    if ( isset( $_REQUEST['redirect_to'] ) && is_string( $_REQUEST['redirect_to'] ) ) {
        $redirect_to = wp_unslash( $_REQUEST['redirect_to'] );
        $query       = wp_parse_url( $redirect_to, PHP_URL_QUERY );

        if ( is_string( $query ) && '' !== $query ) {
            parse_str( $query, $query_args );
            if ( isset( $query_args['product'] ) && is_string( $query_args['product'] ) ) {
                $candidates[] = $query_args['product'];
            }
        }
    }

    foreach ( $candidates as $candidate ) {
        $product = sanitize_key( $candidate );
        if ( in_array( $product, array( 'yumeho', 'mica30' ), true ) ) {
            return $product;
        }
    }

    return '';
}

function rinascente_member_context( $product = '' ) {
    $product     = $product ? sanitize_key( $product ) : rinascente_member_requested_product();
    $is_yumeho   = 'yumeho' === $product;
    $home_url    = $is_yumeho ? rinascente_related_site_url( 'yumeho' ) : home_url( '/' );
    $contact_url = $is_yumeho ? rinascente_related_site_url( 'yumeho', '/contact/' ) : home_url( '/contact/' );

    return array(
        'key'             => $product,
        'is_yumeho'       => $is_yumeho,
        'product_label'   => $is_yumeho ? 'YUMEHO' : 'Rinascente',
        'home_url'        => $home_url ?: home_url( '/' ),
        'contact_url'     => $contact_url ?: home_url( '/contact/' ),
        'back_label'      => $is_yumeho ? 'YUMEHOサイトへ戻る' : 'コーポレートサイトへ戻る',
        'logo_sub'        => $is_yumeho ? 'YUMEHO Shared Member Site' : 'Shared Member Site',
        'document_prefix' => $is_yumeho ? 'YUMEHO 会員' : 'Rinascente Member',
        'shared_note'     => $is_yumeho ? 'この画面はRinascente共通会員サイトです。YUMEHO会員の方もこちらからログインできます。' : '',
        'login_heading'   => $is_yumeho ? 'YUMEHO会員向けの<br><span style="color:var(--gold-light);">限定情報にアクセス。</span>' : '会員限定の<br><span style="color:var(--gold-light);">情報にアクセス。</span>',
        'login_lead'      => $is_yumeho ? 'YUMEHO会員の方は、製品資料、詳細仕様、補助金申請サポート資料、セミナー情報などをこの共通会員サイトから確認できます。' : '会員の方は、製品の最新情報、詳細な技術仕様書、補助金申請サポート資料、セミナー・研修情報などにアクセスできます。',
        'login_form_note' => $is_yumeho ? 'YUMEHO会員の方もこの画面からログインできます。' : '',
        'contact_note'    => $is_yumeho ? '会員登録がお済みでない方は、YUMEHOのお問い合わせフォームからご登録いただけます。' : '会員登録がお済みでない方は、お問い合わせフォームよりお申し込みください。',
        'forgot_lead'     => $is_yumeho ? 'YUMEHO会員の方も、登録メールアドレスを入力すると再設定の案内を受け取れます。Rinascente共通会員サイトからそのまま次へ進めます。' : '会員登録時のメールアドレスを入力すると、パスワード再設定のご案内をお送りします。ローカル環境ではこの画面からそのまま次へ進めます。',
        'reset_lead'      => $is_yumeho ? 'YUMEHO会員の方も、この画面で8文字以上の新しいパスワードを設定できます。設定後はそのままログインできます。' : 'リンクを開いたら、8文字以上の新しいパスワードを入力してください。設定後はそのままログインできます。',
    );
}

function rinascente_member_login_url( $redirect_to = '', $product = '' ) {
    $login_url = home_url( '/login/' );

    if ( $redirect_to ) {
        $login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $login_url );
    }

    if ( $product ) {
        $login_url = add_query_arg( 'product', sanitize_key( $product ), $login_url );
    }

    return $login_url;
}

function rinascente_member_forgot_password_url( $product = '' ) {
    $url = home_url( '/forgot-password/' );
    if ( $product ) {
        $url = add_query_arg( 'product', sanitize_key( $product ), $url );
    }

    return $url;
}

function rinascente_member_reset_password_url( $product = '' ) {
    $url = home_url( '/reset-password/' );
    if ( $product ) {
        $url = add_query_arg( 'product', sanitize_key( $product ), $url );
    }

    return $url;
}

function rinascente_resolve_login_identifier( $identifier ) {
    $candidate = trim( (string) $identifier );
    if ( '' === $candidate ) {
        return '';
    }

    if ( is_email( $candidate ) ) {
        $user = get_user_by( 'email', $candidate );
        if ( $user instanceof WP_User ) {
            return $user->user_login;
        }
    }

    return sanitize_user( $candidate );
}

function rinascente_member_user_name( $user ) {
    if ( ! ( $user instanceof WP_User ) ) {
        return '';
    }

    $display_name = trim( (string) $user->display_name );
    if ( '' !== $display_name ) {
        return $display_name;
    }

    if ( '' !== (string) $user->user_email ) {
        return (string) $user->user_email;
    }

    return (string) $user->user_login;
}

function rinascente_member_user_initial( $user ) {
    $name = rinascente_member_user_name( $user );
    if ( '' === $name ) {
        return 'M';
    }

    if ( function_exists( 'mb_substr' ) ) {
        $initial = mb_substr( $name, 0, 1, 'UTF-8' );
        return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initial, 'UTF-8' ) : $initial;
    }

    return strtoupper( substr( $name, 0, 1 ) );
}

function rinascente_member_user_role_label( $user ) {
    if ( ! ( $user instanceof WP_User ) ) {
        return 'Member';
    }

    $roles = (array) $user->roles;
    if ( in_array( 'administrator', $roles, true ) ) {
        return 'Administrator';
    }
    if ( in_array( 'facility_member', $roles, true ) ) {
        return 'Facility Member';
    }

    return 'Member';
}

function rinascente_is_facility_member( $user = null ) {
    $candidate = $user instanceof WP_User ? $user : wp_get_current_user();
    if ( ! ( $candidate instanceof WP_User ) || ! $candidate->exists() ) {
        return false;
    }

    return in_array( 'facility_member', (array) $candidate->roles, true );
}

function rinascente_is_local_environment() {
    $host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

    return (bool) $host && ( 'localhost' === $host || str_ends_with( $host, '.local' ) );
}

add_filter(
    'show_admin_bar',
    function ( $show ) {
        if ( current_user_can( 'manage_options' ) ) {
            return $show;
        }

        return false;
    }
);

function rinascente_restrict_member_admin_area() {
    if ( ! is_admin() || wp_doing_ajax() || current_user_can( 'manage_options' ) ) {
        return;
    }

    $allowed_member_admin_actions = array(
        'rinascente_member_document_download',
    );
    $requested_action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';

    if ( 'admin-post.php' === wp_basename( $_SERVER['PHP_SELF'] ?? '' ) && in_array( $requested_action, $allowed_member_admin_actions, true ) ) {
        return;
    }

    if ( rinascente_is_facility_member() ) {
        wp_safe_redirect( rinascente_member_page_url() );
        exit;
    }
}
add_action( 'admin_init', 'rinascente_restrict_member_admin_area' );


/* =========================================================================
   1. Theme Setup
   ========================================================================= */
add_action( 'after_setup_theme', 'rinascente_setup' );
function rinascente_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 280,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'header-nav' => 'ヘッダーナビ',
        'mobile-nav' => 'モバイルナビ',
        'footer-nav' => 'フッターナビ',
    ) );
}


/* =========================================================================
   2. Enqueue Styles & Scripts
   ========================================================================= */
add_action( 'wp_enqueue_scripts', 'rinascente_enqueue' );
function rinascente_enqueue() {
    // Google Fonts
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;700&family=Zen+Kaku+Gothic+New:wght@300;400;500;700&display=swap',
        array(),
        null
    );

    // TypeKit
    wp_enqueue_style(
        'typekit',
        'https://use.typekit.net/uor0jvw.css',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'rinascente-style',
        get_template_directory_uri() . '/assets/css/style.css',
        array( 'google-fonts', 'typekit' ),
        rinascente_asset_version( 'assets/css/style.css' )
    );

    // Site switcher
    wp_enqueue_style(
        'rinascente-site-switcher',
        get_template_directory_uri() . '/assets/css/site-switcher.css',
        array( 'rinascente-style' ),
        rinascente_asset_version( 'assets/css/site-switcher.css' )
    );

    // Main JS
    wp_enqueue_script(
        'rinascente-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array(),
        rinascente_asset_version( 'assets/js/main.js' ),
        true
    );
    wp_script_add_data( 'rinascente-main', 'strategy', 'defer' );
}

function rinascente_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array(
            'href'        => 'https://fonts.gstatic.com',
            'crossorigin' => 'anonymous',
        );
        $urls[] = 'https://use.typekit.net';
        $urls[] = 'https://p.typekit.net';
    }

    if ( 'dns-prefetch' === $relation_type ) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = 'https://fonts.gstatic.com';
        $urls[] = 'https://use.typekit.net';
        $urls[] = 'https://p.typekit.net';
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'rinascente_resource_hints', 10, 2 );


/* =========================================================================
   3. Custom Post Types
   ========================================================================= */
add_action( 'init', 'rinascente_register_cpt' );
function rinascente_register_cpt() {

    // News
    register_post_type( 'news', array(
        'labels' => array(
            'name'               => 'ニュース',
            'singular_name'      => 'ニュース',
            'add_new'            => '新規追加',
            'add_new_item'       => 'ニュースを追加',
            'edit_item'          => 'ニュースを編集',
            'new_item'           => '新規ニュース',
            'view_item'          => 'ニュースを表示',
            'search_items'       => 'ニュースを検索',
            'not_found'          => 'ニュースが見つかりません',
            'not_found_in_trash' => 'ゴミ箱にニュースはありません',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'press' ),
        'menu_icon'    => 'dashicons-megaphone',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_menu' => rinascente_site_admin_menu_slug(),
        'show_in_rest' => true,
    ) );

    // Column
    register_post_type( 'column', array(
        'labels' => array(
            'name'               => 'コラム',
            'singular_name'      => 'コラム',
            'add_new'            => '新規追加',
            'add_new_item'       => 'コラムを追加',
            'edit_item'          => 'コラムを編集',
            'new_item'           => '新規コラム',
            'view_item'          => 'コラムを表示',
            'search_items'       => 'コラムを検索',
            'not_found'          => 'コラムが見つかりません',
            'not_found_in_trash' => 'ゴミ箱にコラムはありません',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'column' ),
        'menu_icon'    => 'dashicons-edit',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_menu' => rinascente_site_admin_menu_slug(),
        'show_in_rest' => true,
    ) );

    // Case Study
    register_post_type( 'case_study', array(
        'labels' => array(
            'name'               => '導入事例',
            'singular_name'      => '導入事例',
            'add_new'            => '新規追加',
            'add_new_item'       => '事例を追加',
            'edit_item'          => '事例を編集',
            'new_item'           => '新規事例',
            'view_item'          => '事例を表示',
            'search_items'       => '事例を検索',
            'not_found'          => '事例が見つかりません',
            'not_found_in_trash' => 'ゴミ箱に事例はありません',
        ),
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'cases' ),
        'menu_icon'          => 'dashicons-building',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'show_ui'            => rinascente_case_study_admin_enabled(),
        'show_in_menu'       => rinascente_case_study_admin_enabled(),
        'show_in_admin_bar'  => rinascente_case_study_admin_enabled(),
        'show_in_rest'       => rinascente_case_study_admin_enabled(),
    ) );

    // Company Profile
    register_post_type( 'company_profile', array(
        'labels' => array(
            'name'               => '会社概要',
            'singular_name'      => '会社概要',
            'edit_item'          => '会社概要を編集',
            'view_item'          => '会社概要を表示',
            'search_items'       => '会社概要を検索',
            'not_found'          => '会社概要が見つかりません',
            'not_found_in_trash' => 'ゴミ箱に会社概要はありません',
        ),
        'public'              => false,
        'publicly_queryable'  => false,
        'exclude_from_search' => true,
        'has_archive'         => false,
        'show_ui'             => true,
        'show_in_menu'        => rinascente_site_admin_menu_slug(),
        'show_in_admin_bar'   => true,
        'menu_icon'           => 'dashicons-id-alt',
        'menu_position'       => 22,
        'supports'            => array(),
        'show_in_rest'        => false,
    ) );
}

function rinascente_company_profile_admin_redirect() {
    $screen = get_current_screen();

    if ( ! $screen || 'edit-company_profile' !== $screen->id ) {
        return;
    }

    $post_id = rinascente_company_profile_post_id( true );
    if ( $post_id > 0 ) {
        wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
        exit;
    }
}
add_action( 'current_screen', 'rinascente_company_profile_admin_redirect' );

function rinascente_company_profile_menu_cleanup() {
    remove_submenu_page( rinascente_site_admin_menu_slug(), 'post-new.php?post_type=company_profile' );
    remove_submenu_page( 'edit.php?post_type=company_profile', 'post-new.php?post_type=company_profile' );
}
add_action( 'admin_menu', 'rinascente_company_profile_menu_cleanup', 99 );

function rinascente_company_profile_post_row_actions( $actions, $post ) {
    if ( $post instanceof WP_Post && 'company_profile' === $post->post_type ) {
        unset( $actions['inline hide-if-no-js'], $actions['trash'], $actions['view'] );
    }

    return $actions;
}
add_filter( 'post_row_actions', 'rinascente_company_profile_post_row_actions', 10, 2 );

function rinascente_company_profile_post_updated_messages( $messages ) {
    $messages['company_profile'] = array(
        0  => '',
        1  => '会社概要を更新しました。',
        4  => '会社概要を更新しました。',
        6  => '会社概要を公開しました。',
        7  => '会社概要を保存しました。',
        10 => '会社概要の下書きを更新しました。',
    );

    return $messages;
}
add_filter( 'post_updated_messages', 'rinascente_company_profile_post_updated_messages' );

function rinascente_company_profile_meta_boxes() {
    add_meta_box(
        'rinascente_company_profile_fields',
        '会社概要の内容',
        'rinascente_company_profile_meta_box_html',
        'company_profile',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_company_profile', 'rinascente_company_profile_meta_boxes' );

function rinascente_company_profile_edit_screen_cleanup() {
    remove_post_type_support( 'company_profile', 'title' );
    remove_post_type_support( 'company_profile', 'editor' );
    remove_post_type_support( 'company_profile', 'thumbnail' );
    remove_post_type_support( 'company_profile', 'excerpt' );

    remove_meta_box( 'titlediv', 'company_profile', 'normal' );
    remove_meta_box( 'postdivrich', 'company_profile', 'normal' );
    remove_meta_box( 'slugdiv', 'company_profile', 'normal' );
    remove_meta_box( 'postexcerpt', 'company_profile', 'normal' );
    remove_meta_box( 'trackbacksdiv', 'company_profile', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'company_profile', 'normal' );
    remove_meta_box( 'commentsdiv', 'company_profile', 'normal' );
    remove_meta_box( 'authordiv', 'company_profile', 'normal' );
    remove_meta_box( 'revisionsdiv', 'company_profile', 'normal' );
    remove_meta_box( 'postcustom', 'company_profile', 'normal' );
}
add_action( 'add_meta_boxes_company_profile', 'rinascente_company_profile_edit_screen_cleanup', 5 );

function rinascente_is_company_profile_screen() {
    if ( ! function_exists( 'get_current_screen' ) ) {
        return false;
    }

    $screen = get_current_screen();

    return $screen && 'company_profile' === $screen->post_type;
}

function rinascente_company_profile_admin_styles( $hook ) {
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    if ( ! rinascente_is_company_profile_screen() ) {
        return;
    }

    $style_handle = 'rinascente-company-profile-admin';
    $styles       = <<<'CSS'
#titlediv,
#postdivrich,
#wp-content-wrap,
.editor-toolbar,
.wp-editor-tools,
.quicktags-toolbar,
#minor-publishing-actions,
#misc-publishing-actions .misc-pub-post-status,
#misc-publishing-actions .misc-pub-visibility,
#misc-publishing-actions .misc-pub-curtime {
  display: none !important;
}
CSS;

    wp_register_style( $style_handle, false, array(), null );
    wp_enqueue_style( $style_handle );
    wp_add_inline_style( $style_handle, $styles );
}
add_action( 'admin_enqueue_scripts', 'rinascente_company_profile_admin_styles', 30 );

function rinascente_company_profile_admin_head() {
    if ( ! rinascente_is_company_profile_screen() ) {
        return;
    }
    ?>
    <style>
      #titlediv,
      #postdivrich,
      #wp-content-wrap,
      .editor-toolbar,
      .wp-editor-tools,
      .quicktags-toolbar {
        display: none !important;
      }
    </style>
    <?php
}
add_action( 'admin_head', 'rinascente_company_profile_admin_head' );

function rinascente_company_profile_meta_box_html( $post ) {
    wp_nonce_field( 'rinascente_company_profile_save', 'rinascente_company_profile_nonce' );

    $sections = array(
        'company' => array(
            'title'       => '会社情報',
            'description' => '会社概要ページやフッター、法務ページなどで使う基本情報です。',
        ),
        'support' => array(
            'title'       => '会員サポート情報',
            'description' => '会員向けページや案内メールで使うサポート窓口です。空欄の項目は会社情報側を使います。',
        ),
    );

    $data = rinascente_company_profile_data();
    ?>
    <div class="rinascente-company-profile-fields">
        <?php foreach ( $sections as $section_key => $section ) : ?>
            <div class="rinascente-company-profile-fields__section">
                <h2><?php echo esc_html( $section['title'] ); ?></h2>
                <p class="description"><?php echo esc_html( $section['description'] ); ?></p>
                <table class="form-table" role="presentation">
                    <tbody>
                        <?php foreach ( rinascente_company_profile_fields() as $key => $field ) : ?>
                            <?php if ( $section_key !== $field['section'] ) : ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <tr>
                                <th scope="row">
                                    <label for="<?php echo esc_attr( 'rinascente_company_profile_' . $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                </th>
                                <td>
                                    <?php if ( 'textarea' === $field['type'] ) : ?>
                                        <textarea
                                            id="<?php echo esc_attr( 'rinascente_company_profile_' . $key ); ?>"
                                            name="<?php echo esc_attr( 'rinascente_company_profile_' . $key ); ?>"
                                            rows="<?php echo esc_attr( (string) ( $field['rows'] ?? 4 ) ); ?>"
                                            class="large-text"
                                        ><?php echo esc_textarea( $data[ $key ] ?? '' ); ?></textarea>
                                    <?php else : ?>
                                        <input
                                            type="<?php echo esc_attr( $field['type'] ); ?>"
                                            id="<?php echo esc_attr( 'rinascente_company_profile_' . $key ); ?>"
                                            name="<?php echo esc_attr( 'rinascente_company_profile_' . $key ); ?>"
                                            value="<?php echo esc_attr( $data[ $key ] ?? '' ); ?>"
                                            class="regular-text"
                                        >
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
}

function rinascente_save_company_profile_meta( $post_id ) {
    if ( ! isset( $_POST['rinascente_company_profile_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rinascente_company_profile_nonce'] ) ), 'rinascente_company_profile_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    update_option( 'rinascente_company_profile_post_id', (int) $post_id, false );

    foreach ( rinascente_company_profile_fields() as $key => $field ) {
        $input_name = 'rinascente_company_profile_' . $key;
        $raw_value  = isset( $_POST[ $input_name ] ) ? wp_unslash( $_POST[ $input_name ] ) : '';

        if ( 'textarea' === $field['type'] ) {
            $value = sanitize_textarea_field( $raw_value );
        } elseif ( 'email' === $field['type'] ) {
            $value = sanitize_email( $raw_value );
        } else {
            $value = sanitize_text_field( $raw_value );
        }

        if ( '' === trim( (string) $value ) ) {
            delete_post_meta( $post_id, rinascente_company_profile_meta_key( $key ) );
            continue;
        }

        update_post_meta( $post_id, rinascente_company_profile_meta_key( $key ), $value );
    }
}
add_action( 'save_post_company_profile', 'rinascente_save_company_profile_meta' );


/* =========================================================================
   3.5 Column メタフィールド (YUMEHO 連動用)
   ========================================================================= */

// メタフィールドを REST API で公開
add_action( 'init', 'rinascente_register_column_meta' );
function rinascente_register_column_meta() {
    register_post_meta( 'column', '_rinascente_yumeho_lead', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => '__return_true',
    ) );
    register_post_meta( 'column', '_rinascente_yumeho_publish', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => '__return_true',
    ) );
}

// アンダースコア始まりメタを REST に公開
add_filter( 'is_protected_meta', 'rinascente_allow_column_meta_rest', 10, 3 );
function rinascente_allow_column_meta_rest( $protected, $meta_key, $meta_type ) {
    if ( 'post' === $meta_type && in_array( $meta_key, array( '_rinascente_yumeho_lead', '_rinascente_yumeho_publish' ), true ) ) {
        return false;
    }
    return $protected;
}

add_action( 'save_post_column', 'rinascente_save_column_meta' );
function rinascente_save_column_meta( $post_id ) {
    if ( ! isset( $_POST['rinascente_column_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['rinascente_column_meta_nonce'], 'rinascente_column_meta_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta(
        $post_id,
        '_rinascente_yumeho_publish',
        isset( $_POST['rinascente_column_yumeho_publish'] ) ? '1' : ''
    );

    if ( isset( $_POST['rinascente_column_yumeho_lead'] ) ) {
        update_post_meta(
            $post_id,
            '_rinascente_yumeho_lead',
            wp_kses_post( wp_unslash( $_POST['rinascente_column_yumeho_lead'] ) )
        );
    }
}


/* =========================================================================
   4. Custom Taxonomies
   ========================================================================= */
add_action( 'init', 'rinascente_register_taxonomies' );
function rinascente_register_taxonomies() {

    // News Category
    register_taxonomy( 'news_category', 'news', array(
        'labels' => array(
            'name'          => 'ニュースカテゴリー',
            'singular_name' => 'ニュースカテゴリー',
            'add_new_item'  => '新規カテゴリーを追加',
            'edit_item'     => 'カテゴリーを編集',
            'search_items'  => 'カテゴリーを検索',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'press-category' ),
        'show_in_rest' => true,
    ) );

    // Column Category
    register_taxonomy( 'column_category', 'column', array(
        'labels' => array(
            'name'          => 'コラムカテゴリー',
            'singular_name' => 'コラムカテゴリー',
            'add_new_item'  => '新規カテゴリーを追加',
            'edit_item'     => 'カテゴリーを編集',
            'search_items'  => 'カテゴリーを検索',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'column-category' ),
        'show_in_rest' => true,
    ) );

    // Product Type (for Case Study)
    register_taxonomy( 'product_type', 'case_study', array(
        'labels' => array(
            'name'          => '製品タイプ',
            'singular_name' => '製品タイプ',
            'add_new_item'  => '製品タイプを追加',
            'edit_item'     => '製品タイプを編集',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'product-type' ),
        'show_in_rest' => true,
    ) );

    // Facility Type (for Case Study)
    register_taxonomy( 'facility_type', 'case_study', array(
        'labels' => array(
            'name'          => '施設タイプ',
            'singular_name' => '施設タイプ',
            'add_new_item'  => '施設タイプを追加',
            'edit_item'     => '施設タイプを編集',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'facility-type' ),
        'show_ui'      => rinascente_case_study_admin_enabled(),
        'show_in_rest' => rinascente_case_study_admin_enabled(),
    ) );

    register_taxonomy( 'case_format', 'case_study', array(
        'labels' => array(
            'name'          => '事例フォーマット',
            'singular_name' => '事例フォーマット',
            'add_new_item'  => 'フォーマットを追加',
            'edit_item'     => 'フォーマットを編集',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'case-format' ),
        'show_ui'      => rinascente_case_study_admin_enabled(),
        'show_in_rest' => rinascente_case_study_admin_enabled(),
    ) );
}

function rinascente_redirect_disabled_case_study_admin() {
    if ( ! is_admin() || rinascente_case_study_admin_enabled() ) {
        return;
    }

    $post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
    $taxonomy  = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';

    if ( empty( $post_type ) && isset( $_GET['post'] ) ) {
        $post_type = get_post_type( absint( wp_unslash( $_GET['post'] ) ) );
    }

    if ( 'case_study' === $post_type || in_array( $taxonomy, array( 'facility_type', 'case_format' ), true ) ) {
        wp_safe_redirect( admin_url() );
        exit;
    }
}
add_action( 'admin_init', 'rinascente_redirect_disabled_case_study_admin' );


/* =========================================================================
   5.5 Case Study Meta Box
   ========================================================================= */
add_action( 'add_meta_boxes', 'rinascente_case_meta_box' );
function rinascente_case_meta_box() {
    add_meta_box(
        'rinascente_case_details',
        '導入事例 詳細情報',
        'rinascente_case_meta_box_html',
        'case_study',
        'normal',
        'high'
    );
}

function rinascente_case_meta_box_html( $post ) {
    wp_nonce_field( 'rinascente_case_meta_save', 'rinascente_case_meta_nonce' );

    $fields = array(
        'model_name'    => '導入モデル',
        'facility_name' => '施設名',
        'staff_name'    => '担当者名・役職',
        'staff_comment' => '担当者コメント',
        'result_1_key'  => '成果1 ラベル',
        'result_1_val'  => '成果1 値',
        'result_2_key'  => '成果2 ラベル',
        'result_2_val'  => '成果2 値',
        'result_3_key'  => '成果3 ラベル',
        'result_3_val'  => '成果3 値',
    );

    echo '<table class="form-table">';
    foreach ( $fields as $key => $label ) {
        $value      = get_post_meta( $post->ID, '_rinascente_' . $key, true );
        $field_type = ( 'staff_comment' === $key ) ? 'textarea' : 'input';

        echo '<tr>';
        echo '<th><label for="rinascente_' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th>';
        echo '<td>';
        if ( 'textarea' === $field_type ) {
            echo '<textarea id="rinascente_' . esc_attr( $key ) . '" name="rinascente_' . esc_attr( $key ) . '" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
        } else {
            echo '<input type="text" id="rinascente_' . esc_attr( $key ) . '" name="rinascente_' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text">';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

function rinascente_save_case_meta( $post_id ) {
    if ( ! isset( $_POST['rinascente_case_meta_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['rinascente_case_meta_nonce'], 'rinascente_case_meta_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $fields = array(
        'model_name',
        'facility_name',
        'staff_name',
        'staff_comment',
        'result_1_key',
        'result_1_val',
        'result_2_key',
        'result_2_val',
        'result_3_key',
        'result_3_val',
    );

    foreach ( $fields as $key ) {
        if ( isset( $_POST[ 'rinascente_' . $key ] ) ) {
            update_post_meta(
                $post_id,
                '_rinascente_' . $key,
                sanitize_text_field( wp_unslash( $_POST[ 'rinascente_' . $key ] ) )
            );
        }
    }
}
add_action( 'save_post_case_study', 'rinascente_save_case_meta' );


function rinascente_save_news_meta( $post_id ) {
    if ( ! isset( $_POST['rinascente_news_meta_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['rinascente_news_meta_nonce'] ) ), 'rinascente_news_meta_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $display_title = isset( $_POST['rinascente_display_title'] )
        ? sanitize_textarea_field( wp_unslash( $_POST['rinascente_display_title'] ) )
        : '';

    if ( '' === trim( $display_title ) ) {
        delete_post_meta( $post_id, '_rinascente_display_title' );
    } else {
        update_post_meta( $post_id, '_rinascente_display_title', $display_title );
    }

    $press_featured = isset( $_POST['rinascente_press_featured'] ) ? '1' : '';

    if ( '1' === $press_featured ) {
        $other_featured_ids = get_posts(
            array(
                'post_type'              => 'news',
                'post_status'            => 'any',
                'posts_per_page'         => -1,
                'fields'                 => 'ids',
                'post__not_in'           => array( $post_id ),
                'meta_key'               => '_rinascente_press_featured',
                'meta_value'             => '1',
                'no_found_rows'          => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'suppress_filters'       => false,
            )
        );

        foreach ( $other_featured_ids as $other_featured_id ) {
            delete_post_meta( $other_featured_id, '_rinascente_press_featured' );
        }

        update_post_meta( $post_id, '_rinascente_press_featured', '1' );
    } else {
        delete_post_meta( $post_id, '_rinascente_press_featured' );
    }
}
add_action( 'save_post_news', 'rinascente_save_news_meta' );

function rinascente_get_manual_press_featured_news_id() {
    $featured_ids = get_posts(
        array(
            'post_type'              => 'news',
            'post_status'            => 'publish',
            'posts_per_page'         => 1,
            'fields'                 => 'ids',
            'meta_key'               => '_rinascente_press_featured',
            'meta_value'             => '1',
            'orderby'                => array(
                'date' => 'DESC',
                'ID'   => 'DESC',
            ),
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'suppress_filters'       => false,
        )
    );

    if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
        $hidden_filtered_args = rinascente_exclude_hidden_post_ids_from_query_args(
            array(
                'post__in' => $featured_ids,
            ),
            'news'
        );

        if ( isset( $hidden_filtered_args['post__not_in'] ) ) {
            $featured_ids = array_values( array_diff( array_map( 'absint', $featured_ids ), array_map( 'absint', (array) $hidden_filtered_args['post__not_in'] ) ) );
        }
    }

    return ! empty( $featured_ids ) ? (int) $featured_ids[0] : 0;
}

function rinascente_get_press_featured_news_query() {
    $manual_featured_id = rinascente_get_manual_press_featured_news_id();

    if ( $manual_featured_id ) {
        return new WP_Query(
            array(
                'post_type'      => 'news',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'post__in'       => array( $manual_featured_id ),
                'orderby'        => 'post__in',
            )
        );
    }

    $featured_news_args = array(
        'post_type'      => 'news',
        'posts_per_page' => 1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'news_category',
                'field'    => 'name',
                'terms'    => '会社情報',
            ),
        ),
    );

    if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
        $featured_news_args = rinascente_exclude_hidden_post_ids_from_query_args( $featured_news_args, 'news' );
    }

    $featured_news = new WP_Query( $featured_news_args );

    if ( $featured_news->have_posts() ) {
        return $featured_news;
    }

    $fallback_featured_args = array(
        'post_type'      => 'news',
        'posts_per_page' => 1,
    );

    if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
        $fallback_featured_args = rinascente_exclude_hidden_post_ids_from_query_args( $fallback_featured_args, 'news' );
    }

    return new WP_Query( $fallback_featured_args );
}

function rinascente_editorial_post_types() {
    return array( 'news', 'column' );
}

add_filter( 'use_block_editor_for_post_type', 'rinascente_editorial_use_classic_editor', 10, 2 );
function rinascente_editorial_use_classic_editor( $use_block_editor, $post_type ) {
    if ( in_array( $post_type, rinascente_editorial_post_types(), true ) ) {
        return false;
    }

    return $use_block_editor;
}

add_action( 'add_meta_boxes', 'rinascente_editorial_admin_meta_boxes', 30 );
function rinascente_editorial_admin_meta_boxes() {
    foreach ( rinascente_editorial_post_types() as $post_type ) {
        remove_meta_box( 'slugdiv', $post_type, 'normal' );
        remove_meta_box( 'commentstatusdiv', $post_type, 'normal' );
        remove_meta_box( 'commentsdiv', $post_type, 'normal' );
        remove_meta_box( 'trackbacksdiv', $post_type, 'normal' );
        remove_meta_box( 'authordiv', $post_type, 'normal' );
        remove_meta_box( 'postcustom', $post_type, 'normal' );
        remove_meta_box( 'revisionsdiv', $post_type, 'normal' );
        remove_meta_box( 'pageparentdiv', $post_type, 'side' );
        remove_meta_box( 'formatdiv', $post_type, 'side' );
        remove_meta_box( 'postexcerpt', $post_type, 'normal' );
        remove_meta_box( 'postimagediv', $post_type, 'side' );
        remove_meta_box( 'news_categorydiv', $post_type, 'side' );
        remove_meta_box( 'column_categorydiv', $post_type, 'side' );
        add_meta_box(
            'rinascente_editorial_checklist',
            '公開チェック',
            'rinascente_editorial_checklist_meta_box_html',
            $post_type,
            'side',
            'high'
        );
    }
}

function rinascente_editorial_taxonomy_panel_html( $post, $taxonomy_slug ) {
    $taxonomy = get_taxonomy( $taxonomy_slug );
    if ( ! $taxonomy ) {
        return;
    }

    $box = array(
        'id'       => $taxonomy_slug . 'div',
        'title'    => $taxonomy->labels->name,
        'callback' => ! empty( $taxonomy->meta_box_cb ) ? $taxonomy->meta_box_cb : 'post_categories_meta_box',
        'args'     => array(
            'taxonomy' => $taxonomy_slug,
        ),
    );

    $callback = $box['callback'];
    if ( is_string( $callback ) && function_exists( $callback ) ) {
        call_user_func( $callback, $post, $box );
    }
}

add_action( 'edit_form_after_title', 'rinascente_editorial_after_title_fields' );
function rinascente_editorial_after_title_fields( $post ) {
    if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, rinascente_editorial_post_types(), true ) ) {
        return;
    }

    $taxonomy_slug = 'news' === $post->post_type ? 'news_category' : 'column_category';
    ?>
    <div class="rinascente-editorial-top-fields">
        <div class="rinascente-editorial-top-fields__primary">
            <div class="rinascente-editorial-top-fields__excerpt">
                <label for="excerpt"><strong>一覧説明文</strong></label>
                <p class="description">一覧ページやカードで表示する短い説明文です。まずここを入れておくと、更新内容が伝わりやすくなります。</p>
                <textarea id="excerpt" name="excerpt" rows="4" class="widefat"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
            </div>
            <?php rinascente_editorial_featured_image_panel_html( $post ); ?>
            <div class="rinascente-editorial-top-fields__taxonomy">
                <label><strong><?php echo esc_html( 'news' === $post->post_type ? 'ニュースカテゴリー' : 'コラムカテゴリー' ); ?></strong></label>
                <p class="description">公開ページで使う分類です。まず最初に選んでおくと整理しやすくなります。</p>
                <div id="taxonomy-<?php echo esc_attr( $taxonomy_slug ); ?>" class="rinascente-editorial-taxonomy-panel">
                    <?php rinascente_editorial_taxonomy_panel_html( $post, $taxonomy_slug ); ?>
                </div>
            </div>
            <?php rinascente_editorial_optional_settings_panel_html( $post ); ?>
        </div>
        <div class="rinascente-editorial-top-fields__guide">
            <?php rinascente_editorial_guide_meta_box_html( $post ); ?>
        </div>
    </div>
    <?php
}

function rinascente_editorial_template_html( $post_type ) {
    if ( 'news' === $post_type ) {
        return "<h2>概要</h2>\n<p></p>\n<h2>背景</h2>\n<p></p>\n<h2>詳細</h2>\n<p></p>\n<h2>お問い合わせ</h2>\n<p></p>";
    }

    return "<h2>はじめに</h2>\n<p></p>\n<h2>課題</h2>\n<p></p>\n<h2>ポイント</h2>\n<p></p>\n<h2>まとめ</h2>\n<p></p>";
}

function rinascente_editorial_spec_table_html() {
    return '<table class="spec-table">'
        . "\n<tbody>\n"
        . "<tr><th>モデル名</th><td>例：PGT-9001</td></tr>\n"
        . "<tr><th>設置面積（最小）</th><td>例：幅1,400mm × 奥行2,600mm</td></tr>\n"
        . "<tr><th>本体質量</th><td>例：約62kg（キャスター含む）</td></tr>\n"
        . "<tr><th>レール有効長</th><td>例：3,000mm〜6,000mm（延長ユニット対応）</td></tr>\n"
        . "<tr><th>吊り下げ荷重</th><td>例：最大150kg</td></tr>\n"
        . "<tr><th>対応ハーネス</th><td>例：G-SUITシリーズ全機種</td></tr>\n"
        . "<tr><th>希望小売価格</th><td>例：オープン価格（お問い合わせください）</td></tr>\n"
        . "</tbody>\n</table>\n<p></p>";
}

function rinascente_editorial_featured_image_panel_html( $post ) {
    if ( ! $post instanceof WP_Post ) {
        return;
    }
    ?>
    <div class="rinascente-editorial-top-fields__featured">
        <label><strong>一覧・詳細で使う代表画像</strong></label>
        <p class="description">一覧カードと詳細ページのメイン画像に使います。本文でも同じ画像を使いたいときは、右側の「設定済みの代表画像を本文に入れる」からそのまま再利用できます。</p>
        <div id="postimagediv" class="rinascente-editorial-featured-image__picker">
            <?php post_thumbnail_meta_box( $post ); ?>
        </div>
    </div>
    <?php
}

function rinascente_editorial_optional_settings_panel_html( $post ) {
    if ( ! $post instanceof WP_Post ) {
        return;
    }

    if ( 'news' === $post->post_type ) {
        wp_nonce_field( 'rinascente_news_meta_save', 'rinascente_news_meta_nonce' );

        $display_title       = get_post_meta( $post->ID, '_rinascente_display_title', true );
        $press_featured      = get_post_meta( $post->ID, '_rinascente_press_featured', true );
        $current_featured_id = rinascente_get_manual_press_featured_news_id();
        ?>
        <div class="rinascente-editorial-top-fields__options">
            <label><strong>表示オプション</strong></label>
            <p class="description">必要なときだけ使う任意設定です。通常は空欄のままでも問題ありません。</p>

            <div class="rinascente-editorial-option">
                <label for="rinascente_display_title"><strong>詳細ページ用タイトル</strong></label>
                <p class="description">詳細ページだけ見出しを変えたいときに入力します。空欄なら通常のタイトルを使います。</p>
                <textarea id="rinascente_display_title" name="rinascente_display_title" rows="3" class="widefat"><?php echo esc_textarea( $display_title ); ?></textarea>
            </div>

            <div class="rinascente-editorial-option">
                <label for="rinascente_press_featured" class="rinascente-editorial-option__check">
                    <input type="checkbox" id="rinascente_press_featured" name="rinascente_press_featured" value="1" <?php checked( $press_featured, '1' ); ?>>
                    <span><strong>Press の Featured に表示する</strong></span>
                </label>
                <p class="description">一覧の先頭で強調したいニュースがあるときだけ使います。Featured にできるのは1件だけです。</p>
                <?php if ( $current_featured_id && (int) $current_featured_id !== (int) $post->ID ) : ?>
                <p class="rinascente-editorial-option__note">現在の Featured: <a href="<?php echo esc_url( get_edit_post_link( $current_featured_id ) ); ?>"><?php echo esc_html( get_the_title( $current_featured_id ) ); ?></a></p>
                <?php elseif ( '1' === $press_featured ) : ?>
                <p class="rinascente-editorial-option__note rinascente-editorial-option__note--active">このニュースが現在の Featured です。</p>
                <?php endif; ?>
            </div>
        </div>
        <?php

        return;
    }

    if ( 'column' === $post->post_type ) {
        wp_nonce_field( 'rinascente_column_meta_save', 'rinascente_column_meta_nonce' );

        $yumeho_lead    = get_post_meta( $post->ID, '_rinascente_yumeho_lead', true );
        $yumeho_publish = get_post_meta( $post->ID, '_rinascente_yumeho_publish', true );
        ?>
        <div class="rinascente-editorial-top-fields__options">
            <label><strong>連携オプション</strong></label>
            <p class="description">YUMEHO サイトにも出したいときだけ設定します。通常のコラム公開には必須ではありません。</p>

            <div class="rinascente-editorial-option">
                <label for="rinascente_column_yumeho_publish" class="rinascente-editorial-option__check">
                    <input type="checkbox" id="rinascente_column_yumeho_publish" name="rinascente_column_yumeho_publish" value="1" <?php checked( $yumeho_publish, '1' ); ?>>
                    <span><strong>YUMEHO サイトでも公開する</strong></span>
                </label>
                <p class="description">オンにすると、同じ本文をYUMEHOサイトにも表示します。</p>
            </div>

            <div class="rinascente-editorial-option">
                <label for="rinascente_column_yumeho_lead"><strong>YUMEHO 向けの冒頭文</strong> <span class="description">任意</span></label>
                <p class="description">YUMEHO 側だけに一言添えたいときに入力します。空欄でも公開できます。</p>
                <textarea id="rinascente_column_yumeho_lead" name="rinascente_column_yumeho_lead" rows="3" class="widefat" placeholder="YUMEHO サイトでだけ見せたい導入文があれば入力してください。"><?php echo esc_textarea( $yumeho_lead ); ?></textarea>
            </div>
        </div>
        <?php
    }
}

function rinascente_editorial_guide_meta_box_html( $post ) {
    $post_type = $post instanceof WP_Post ? $post->post_type : 'news';
    $is_news   = 'news' === $post_type;
    ?>
    <div class="rinascente-editorial-guide" data-post-type="<?php echo esc_attr( $post_type ); ?>">
        <p class="rinascente-editorial-guide__lead">
            まず左側で <strong>一覧用説明文</strong>、<strong>代表画像</strong>、<strong>カテゴリー</strong> を入れ、そのあと本文を整える流れが分かりやすいです。
            公開前は右の <strong>公開チェック</strong> が全部そろっていれば大丈夫です。
        </p>
        <div class="rinascente-editorial-guide__grid">
            <div class="rinascente-editorial-guide__card">
                <h4>本文テンプレート・表を入れる</h4>
                <p>本文の土台をすばやく作りたいときに使います。テンプレートや表を、そのまま本文へ入れられます。</p>
                <button
                    type="button"
                    class="button button-secondary rinascente-editorial-template-button"
                    data-template="<?php echo esc_attr( rinascente_editorial_template_html( $post_type ) ); ?>"
                >
                    <?php echo $is_news ? 'ニュース用テンプレートを入れる' : 'コラム用テンプレートを入れる'; ?>
                </button>
                <div class="rinascente-editorial-insert-buttons" style="margin-top:12px;">
                    <button
                        type="button"
                        class="button button-secondary rinascente-editorial-insert-template-button"
                        data-insert-template="<?php echo esc_attr( rinascente_editorial_spec_table_html() ); ?>"
                    >
                        仕様表を入れる
                    </button>
                </div>
                <p class="description" style="margin-top:10px;">製品仕様の表を、カーソル位置にそのまま入れます。</p>
                <div class="rinascente-editorial-table-builder">
                    <span class="rinascente-editorial-table-builder__title">自由な表を入れる</span>
                    <div class="rinascente-editorial-table-builder__controls">
                        <div class="rinascente-editorial-table-builder__field">
                            <label for="rinascenteTableRows">行数</label>
                            <input type="number" id="rinascenteTableRows" class="small-text rinascente-editorial-table-rows" min="1" max="12" value="4">
                        </div>
                        <div class="rinascente-editorial-table-builder__field">
                            <label for="rinascenteTableCols">列数</label>
                            <input type="number" id="rinascenteTableCols" class="small-text rinascente-editorial-table-cols" min="1" max="6" value="2">
                        </div>
                        <button type="button" class="button button-secondary rinascente-editorial-table-button">表を挿入</button>
                    </div>
                    <p class="description" style="margin-top:8px;">行数と列数を指定して、表のひな型を入れます。左端の列は見出し用です。</p>
                </div>
            </div>
            <div class="rinascente-editorial-guide__card">
                <h4>本文に画像を入れる</h4>
                <p>本文専用の画像を選ぶか、上で設定した代表画像をそのまま再利用するかで選びます。</p>
                <div class="rinascente-editorial-insert-buttons">
                    <button type="button" class="button button-secondary rinascente-editorial-image-button" data-image-action="library">新しく画像を選んで入れる</button>
                    <button type="button" class="button button-secondary rinascente-editorial-image-button" data-image-action="featured">設定済みの代表画像を本文に入れる</button>
                </div>
                <p class="description" style="margin-top:10px;">上はメディアライブラリから本文用の画像を選ぶボタンです。下は「一覧・詳細で使う代表画像」に設定した画像を、そのまま本文に入れるボタンです。</p>
            </div>
        </div>
    </div>
    <?php
}

function rinascente_editorial_checklist_meta_box_html( $post ) {
    $post_type = $post instanceof WP_Post ? $post->post_type : 'news';
    $items     = array(
        'title'     => 'タイトル',
        'excerpt'   => '一覧用説明文',
        'content'   => '本文',
        'thumbnail' => '一覧・詳細の画像',
        'category'  => 'news' === $post_type ? 'ニュースカテゴリー' : 'コラムカテゴリー',
        'date'      => '公開日時',
    );
    ?>
    <div class="rinascente-editorial-checklist" data-post-type="<?php echo esc_attr( $post_type ); ?>">
        <p class="description" style="margin-top:0;">公開に必要な項目だけに絞っています。ここが全部 OK なら公開準備完了です。</p>
        <ul class="rinascente-editorial-checklist__list">
            <?php foreach ( $items as $key => $label ) : ?>
                <li class="rinascente-editorial-checklist__item" data-check="<?php echo esc_attr( $key ); ?>">
                    <span class="rinascente-editorial-checklist__badge">未確認</span>
                    <span class="rinascente-editorial-checklist__label"><?php echo esc_html( $label ); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

function rinascente_editorial_term_list( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return '—';
    }

    return implode(
        ', ',
        array_map(
            static function( $term ) {
                return $term->name;
            },
            $terms
        )
    );
}

add_filter( 'manage_news_posts_columns', 'rinascente_news_admin_columns' );
function rinascente_news_admin_columns( $columns ) {
    return array(
        'cb'            => $columns['cb'] ?? '',
        'thumbnail'     => '画像',
        'title'         => 'タイトル',
        'news_category' => 'カテゴリー',
        'excerpt_state' => '一覧説明',
        'display_state' => '詳細見出し',
        'press_featured' => 'Featured',
        'date'          => '公開日',
    );
}

add_action( 'manage_news_posts_custom_column', 'rinascente_news_admin_column_content', 10, 2 );
function rinascente_news_admin_column_content( $column, $post_id ) {
    if ( 'thumbnail' === $column ) {
        $thumb = get_the_post_thumbnail( $post_id, array( 72, 72 ), array( 'style' => 'width:64px;height:64px;object-fit:cover;border-radius:8px;' ) );
        echo $thumb ? $thumb : '—';
        return;
    }

    if ( 'news_category' === $column ) {
        echo esc_html( rinascente_editorial_term_list( $post_id, 'news_category' ) );
        return;
    }

    if ( 'excerpt_state' === $column ) {
        echo has_excerpt( $post_id ) ? '<span style="color:#0a7a2f;font-weight:700;">入力済み</span>' : '<span style="color:#9a6700;">未入力</span>';
        return;
    }

    if ( 'display_state' === $column ) {
        $display_title = get_post_meta( $post_id, '_rinascente_display_title', true );
        echo '' !== trim( (string) $display_title ) ? '<span style="color:#0a7a2f;font-weight:700;">入力済み</span>' : '<span style="color:#6b7280;">通常タイトルを使用</span>';
        return;
    }

    if ( 'press_featured' === $column ) {
        $featured = get_post_meta( $post_id, '_rinascente_press_featured', true );
        echo '1' === $featured ? '<span style="color:#0a7a2f;font-weight:700;">表示中</span>' : '<span style="color:#6b7280;">通常</span>';
    }
}

add_filter( 'manage_column_posts_columns', 'rinascente_column_admin_columns' );
function rinascente_column_admin_columns( $columns ) {
    return array(
        'cb'            => $columns['cb'] ?? '',
        'thumbnail'     => '画像',
        'title'         => 'タイトル',
        'column_category' => 'カテゴリー',
        'excerpt_state' => '一覧説明',
        'yumeho_sync'   => 'YUMEHO連動',
        'yumeho_lead'   => 'YUMEHOリード',
        'date'          => '公開日',
    );
}

add_action( 'manage_column_posts_custom_column', 'rinascente_column_admin_column_content', 10, 2 );
function rinascente_column_admin_column_content( $column, $post_id ) {
    if ( 'thumbnail' === $column ) {
        $thumb = get_the_post_thumbnail( $post_id, array( 72, 72 ), array( 'style' => 'width:64px;height:64px;object-fit:cover;border-radius:8px;' ) );
        echo $thumb ? $thumb : '—';
        return;
    }

    if ( 'column_category' === $column ) {
        echo esc_html( rinascente_editorial_term_list( $post_id, 'column_category' ) );
        return;
    }

    if ( 'excerpt_state' === $column ) {
        echo has_excerpt( $post_id ) ? '<span style="color:#0a7a2f;font-weight:700;">入力済み</span>' : '<span style="color:#9a6700;">未入力</span>';
        return;
    }

    if ( 'yumeho_sync' === $column ) {
        $publish = get_post_meta( $post_id, '_rinascente_yumeho_publish', true );
        echo '1' === $publish ? '<span style="color:#0a7a2f;font-weight:700;">連動中</span>' : '<span style="color:#6b7280;">未連動</span>';
        return;
    }

    if ( 'yumeho_lead' === $column ) {
        $lead = get_post_meta( $post_id, '_rinascente_yumeho_lead', true );
        echo '' !== trim( (string) $lead ) ? '<span style="color:#0a7a2f;font-weight:700;">入力済み</span>' : '<span style="color:#6b7280;">未入力</span>';
    }
}

add_action( 'restrict_manage_posts', 'rinascente_editorial_admin_filters' );
function rinascente_editorial_admin_filters() {
    global $typenow;

    if ( 'news' === $typenow ) {
        wp_dropdown_categories(
            array(
                'show_option_all' => 'すべてのニュースカテゴリー',
                'taxonomy'        => 'news_category',
                'name'            => 'news_category',
                'orderby'         => 'name',
                'selected'        => isset( $_GET['news_category'] ) ? absint( wp_unslash( $_GET['news_category'] ) ) : 0,
                'hierarchical'    => true,
                'depth'           => 3,
                'show_count'      => false,
                'hide_empty'      => false,
            )
        );
        ?>
        <select name="rinascente_press_featured_filter">
            <option value="">Featured: すべて</option>
            <option value="1" <?php selected( isset( $_GET['rinascente_press_featured_filter'] ) ? wp_unslash( $_GET['rinascente_press_featured_filter'] ) : '', '1' ); ?>>表示中のみ</option>
            <option value="0" <?php selected( isset( $_GET['rinascente_press_featured_filter'] ) ? wp_unslash( $_GET['rinascente_press_featured_filter'] ) : '', '0' ); ?>>通常のみ</option>
        </select>
        <?php
    }

    if ( 'column' === $typenow ) {
        wp_dropdown_categories(
            array(
                'show_option_all' => 'すべてのコラムカテゴリー',
                'taxonomy'        => 'column_category',
                'name'            => 'column_category',
                'orderby'         => 'name',
                'selected'        => isset( $_GET['column_category'] ) ? absint( wp_unslash( $_GET['column_category'] ) ) : 0,
                'hierarchical'    => true,
                'depth'           => 3,
                'show_count'      => false,
                'hide_empty'      => false,
            )
        );
        ?>
        <select name="rinascente_yumeho_sync">
            <option value="">YUMEHO連動: すべて</option>
            <option value="1" <?php selected( isset( $_GET['rinascente_yumeho_sync'] ) ? wp_unslash( $_GET['rinascente_yumeho_sync'] ) : '', '1' ); ?>>連動中のみ</option>
            <option value="0" <?php selected( isset( $_GET['rinascente_yumeho_sync'] ) ? wp_unslash( $_GET['rinascente_yumeho_sync'] ) : '', '0' ); ?>>未連動のみ</option>
        </select>
        <?php
    }
}

add_action( 'pre_get_posts', 'rinascente_editorial_admin_filter_query' );
function rinascente_editorial_admin_filter_query( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $post_type = $query->get( 'post_type' );

    if ( 'news' === $post_type && ! empty( $_GET['news_category'] ) ) {
        $term = get_term_by( 'id', absint( wp_unslash( $_GET['news_category'] ) ), 'news_category' );
        if ( $term && ! is_wp_error( $term ) ) {
            $query->set( 'news_category', $term->slug );
        }
    }

    if ( 'news' === $post_type && isset( $_GET['rinascente_press_featured_filter'] ) && '' !== (string) wp_unslash( $_GET['rinascente_press_featured_filter'] ) ) {
        $meta_query = (array) $query->get( 'meta_query' );
        $filter     = '1' === (string) wp_unslash( $_GET['rinascente_press_featured_filter'] ) ? '1' : '0';

        if ( '1' === $filter ) {
            $meta_query[] = array(
                'key'   => '_rinascente_press_featured',
                'value' => '1',
            );
        } else {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_rinascente_press_featured',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => '_rinascente_press_featured',
                    'value'   => '1',
                    'compare' => '!=',
                ),
            );
        }

        $query->set( 'meta_query', $meta_query );
    }

    if ( 'column' === $post_type ) {
        if ( ! empty( $_GET['column_category'] ) ) {
            $term = get_term_by( 'id', absint( wp_unslash( $_GET['column_category'] ) ), 'column_category' );
            if ( $term && ! is_wp_error( $term ) ) {
                $query->set( 'column_category', $term->slug );
            }
        }

        if ( isset( $_GET['rinascente_yumeho_sync'] ) && '' !== (string) wp_unslash( $_GET['rinascente_yumeho_sync'] ) ) {
            $sync_value = '1' === (string) wp_unslash( $_GET['rinascente_yumeho_sync'] ) ? '1' : '';
            $query->set(
                'meta_query',
                array(
                    array(
                        'key'   => '_rinascente_yumeho_publish',
                        'value' => $sync_value,
                    ),
                )
            );
        }
    }
}

function rinascente_editorial_admin_assets( $hook ) {
    global $post_type;

    if ( ! in_array( $post_type, rinascente_editorial_post_types(), true ) ) {
        return;
    }

    $style_handle = 'rinascente-editorial-admin';
    $styles       = <<<'CSS'
body.post-type-news #slugdiv,
body.post-type-column #slugdiv,
body.post-type-news #commentstatusdiv,
body.post-type-column #commentstatusdiv,
body.post-type-news #commentsdiv,
body.post-type-column #commentsdiv,
body.post-type-news #trackbacksdiv,
body.post-type-column #trackbacksdiv,
body.post-type-news #authordiv,
body.post-type-column #authordiv,
body.post-type-news #postcustom,
body.post-type-column #postcustom,
body.post-type-news #revisionsdiv,
body.post-type-column #revisionsdiv {
  display: none;
}

.rinascente-editorial-guide {
  margin: 4px 0;
}

.rinascente-editorial-top-fields {
  margin: 18px 0 20px;
  display: grid;
  gap: 18px;
}

.rinascente-editorial-top-fields__primary {
  display: grid;
  gap: 18px;
}

.rinascente-editorial-top-fields__excerpt,
.rinascente-editorial-top-fields__taxonomy,
.rinascente-editorial-top-fields__featured,
.rinascente-editorial-top-fields__options {
  padding: 16px 18px;
  border: 1px solid #dcdcde;
  border-radius: 14px;
  background: #fff;
}

.rinascente-editorial-top-fields__excerpt label,
.rinascente-editorial-top-fields__taxonomy label,
.rinascente-editorial-top-fields__featured label,
.rinascente-editorial-top-fields__options label {
  display: inline-block;
  margin-bottom: 8px;
  font-size: 14px;
}

.rinascente-editorial-top-fields__excerpt .description,
.rinascente-editorial-top-fields__taxonomy .description,
.rinascente-editorial-top-fields__featured .description,
.rinascente-editorial-top-fields__options .description {
  margin: 0 0 10px;
}

.rinascente-editorial-option + .rinascente-editorial-option {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.rinascente-editorial-option__check {
  display: flex !important;
  align-items: flex-start;
  gap: 8px;
}

.rinascente-editorial-option__check input {
  margin-top: 2px;
}

.rinascente-editorial-option__note {
  margin-top: 10px !important;
  padding: 10px 12px;
  border-radius: 10px;
  background: #f6f7f7;
  font-size: 12px;
  line-height: 1.7;
  color: #50575e;
}

.rinascente-editorial-option__note--active {
  background: #ecfdf3;
  color: #0a7a2f;
  font-weight: 700;
}

.rinascente-editorial-featured-image__picker p {
  margin: 0 0 10px;
}

.rinascente-editorial-featured-image__picker img {
  display: block;
  max-width: 240px;
  width: 100%;
  height: auto;
  border-radius: 12px;
  margin-bottom: 12px;
}

.rinascente-editorial-featured-image__picker .button,
.rinascente-editorial-featured-image__picker a {
  font-weight: 600;
}

.rinascente-editorial-guide__lead {
  margin: 0 0 16px;
  padding: 16px 18px;
  border: 1px solid #cfe0f2;
  border-radius: 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
  line-height: 1.9;
}

.rinascente-editorial-guide__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.rinascente-editorial-guide__card {
  padding: 16px 18px;
  border: 1px solid #dcdcde;
  border-radius: 14px;
  background: #fff;
}

.rinascente-editorial-guide__card--accent {
  border-color: #b8d4f0;
  background: #f4f9ff;
}

.rinascente-editorial-guide__card h4 {
  margin: 0 0 10px;
  font-size: 14px;
}

.rinascente-editorial-guide__card p,
.rinascente-editorial-guide__card ol {
  margin: 0;
}

.rinascente-editorial-insert-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.rinascente-editorial-table-builder {
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid #e5e7eb;
}

.rinascente-editorial-table-builder__title {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}

.rinascente-editorial-table-builder__controls {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, auto));
  gap: 8px;
  align-items: end;
}

.rinascente-editorial-table-builder__field {
  display: grid;
  gap: 4px;
}

.rinascente-editorial-table-builder__field label {
  font-size: 12px;
  font-weight: 600;
  color: #4b5563;
}

.rinascente-editorial-table-builder__field input[type="number"] {
  width: 88px;
}

.rinascente-editorial-guide__card ol {
  padding-left: 18px;
  display: grid;
  gap: 6px;
}

.rinascente-editorial-checklist__list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 10px;
}

.rinascente-editorial-checklist__item {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 8px;
  align-items: center;
  padding: 10px 12px;
  border: 1px solid #dcdcde;
  border-radius: 12px;
  background: #fff;
}

.rinascente-editorial-checklist__badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 56px;
  padding: 4px 8px;
  border-radius: 999px;
  background: #f6f7f7;
  color: #50575e;
  font-size: 11px;
  font-weight: 700;
}

.rinascente-editorial-checklist__item.is-complete .rinascente-editorial-checklist__badge {
  background: #ecfdf3;
  color: #0a7a2f;
}

.rinascente-editorial-checklist__item.is-missing .rinascente-editorial-checklist__badge {
  background: #fff8d6;
  color: #9a6700;
}

.rinascente-editorial-checklist__label {
  font-weight: 600;
}

.rinascente-editorial-checklist__note {
  color: #6b7280;
  font-size: 11px;
}

.post-type-news .column-thumbnail,
.post-type-column .column-thumbnail {
  width: 90px;
}

.post-type-news .column-news_category,
.post-type-column .column-column_category {
  width: 15%;
}

.post-type-news .column-excerpt_state,
.post-type-news .column-display_state,
.post-type-news .column-press_featured,
.post-type-column .column-excerpt_state,
.post-type-column .column-yumeho_sync,
.post-type-column .column-yumeho_lead {
  width: 11%;
}

@media (max-width: 1080px) {
  .rinascente-editorial-guide__grid {
    grid-template-columns: 1fr;
  }
}
CSS;

    wp_register_style( $style_handle, false, array(), null );
    wp_enqueue_style( $style_handle );
    wp_add_inline_style( $style_handle, $styles );

    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    wp_enqueue_media();

    $script = <<<'JS'
(function($){
  $(function(){
    var body = $('body');
    var postType = body.hasClass('post-type-news') ? 'news' : (body.hasClass('post-type-column') ? 'column' : '');
    if (!postType) {
      return;
    }

    function setHeadingText(selector, text) {
      var target = $(selector).find('.hndle span, .postbox-header h2, .handle-order-higher').first();
      if (target.length) {
        target.text(text);
      }
    }

    function setTitlePromptText(text) {
      $('#title').attr('placeholder', '').attr('aria-label', text || '');
      var prompt = $('#title-prompt-text');
      if (prompt.length) {
        prompt.text(text || '');
      }
    }

    function getEditorText() {
      if (window.tinymce && window.tinymce.get('content') && !window.tinymce.get('content').isHidden()) {
        return $.trim(window.tinymce.get('content').getContent({ format: 'text' }));
      }
      return $.trim($('#content').val() || '');
    }

    function setEditorContent(html) {
      if (window.tinymce && window.tinymce.get('content') && !window.tinymce.get('content').isHidden()) {
        window.tinymce.get('content').setContent(html);
        return;
      }
      $('#content').val(html);
    }

    function insertEditorContent(html) {
      if (!html) {
        return;
      }

      if (window.tinymce && window.tinymce.get('content') && !window.tinymce.get('content').isHidden()) {
        window.tinymce.get('content').execCommand('mceInsertContent', false, html);
        return;
      }

      var textarea = $('#content').get(0);
      if (!textarea) {
        return;
      }

      var value = textarea.value || '';
      var start = typeof textarea.selectionStart === 'number' ? textarea.selectionStart : value.length;
      var end = typeof textarea.selectionEnd === 'number' ? textarea.selectionEnd : value.length;
      textarea.value = value.slice(0, start) + html + value.slice(end);
      textarea.selectionStart = textarea.selectionEnd = start + html.length;
      $(textarea).trigger('input');
    }

    function escapeHtml(value) {
      return $('<div>').text((value || '').toString()).html();
    }

    function clampNumber(value, min, max, fallback) {
      var number = parseInt(value, 10);
      if (isNaN(number)) {
        number = fallback;
      }

      return Math.min(max, Math.max(min, number));
    }

    function buildTableHtml(rows, cols) {
      var safeRows = clampNumber(rows, 1, 12, 4);
      var safeCols = clampNumber(cols, 1, 6, 2);
      var lines = ['<table class="spec-table">', '<tbody>'];

      for (var row = 0; row < safeRows; row += 1) {
        lines.push('<tr>');

        if (safeCols > 1) {
          lines.push('<th>見出し' + (row + 1) + '</th>');

          for (var cell = 1; cell < safeCols; cell += 1) {
            lines.push('<td>内容</td>');
          }
        } else {
          lines.push('<td>内容</td>');
        }

        lines.push('</tr>');
      }

      lines.push('</tbody>');
      lines.push('</table>');
      lines.push('<p></p>');

      return lines.join('\n');
    }

    function buildImageHtml(attachment) {
      if (!attachment) {
        return '';
      }

      var image = attachment.sizes && (attachment.sizes.large || attachment.sizes.full || attachment.sizes.medium) ? (attachment.sizes.large || attachment.sizes.full || attachment.sizes.medium) : attachment;
      var src = image && image.url ? image.url : '';
      if (!src) {
        return '';
      }

      var alt = attachment.alt || attachment.title || '';
      var caption = attachment.caption || '';
      var html = '<p><img src="' + escapeHtml(src) + '" alt="' + escapeHtml(alt) + '"></p>';

      if (caption) {
        html += '\n<p class="wp-caption-text">' + escapeHtml(caption) + '</p>';
      }

      return html + '\n';
    }

    function insertAttachmentImage(attachment) {
      var html = buildImageHtml(attachment);
      if (!html) {
        window.alert('この画像は本文に挿入できませんでした。');
        return;
      }

      insertEditorContent(html);
      updateChecklist();
    }

    function openLibraryFrame() {
      var frame = wp.media({
        title: '本文に入れる画像を選択',
        library: { type: 'image' },
        button: { text: '本文に挿入' },
        multiple: false
      });

      frame.on('select', function(){
        var attachment = frame.state().get('selection').first().toJSON();
        insertAttachmentImage(attachment);
      });

      frame.open();
    }

    function insertFeaturedImage() {
      var attachmentId = $('#_thumbnail_id').val();
      if (!attachmentId) {
        window.alert('先に「一覧・詳細の画像」を設定してください。');
        return;
      }

      var attachment = wp.media.attachment(attachmentId);
      attachment.fetch().then(function(){
        insertAttachmentImage(attachment.toJSON());
      });
    }

    function hasCategorySelection() {
      if (postType === 'news') {
        return $('#news_categorychecklist input:checked, #taxonomy-news_category input:checked, #news_categorydiv input:checked').length > 0;
      }
      return $('#column_categorychecklist input:checked, #taxonomy-column_category input:checked, #column_categorydiv input:checked').length > 0;
    }

    function hasThumbnail() {
      return $('#_thumbnail_id').val() || $('#postimagediv img').length > 0;
    }

    function hasPublishDate() {
      return $.trim($('#aa').val()) && $.trim($('#mm').val()) && $.trim($('#jj').val());
    }

    function setChecklistState(key, complete) {
      var item = $('.rinascente-editorial-checklist__item[data-check="' + key + '"]');
      if (!item.length) {
        return;
      }
      item.toggleClass('is-complete', !!complete);
      item.toggleClass('is-missing', !complete);
      item.find('.rinascente-editorial-checklist__badge').text(complete ? 'OK' : '未入力');
    }

    function updateChecklist() {
      setChecklistState('title', $.trim($('#title').val()).length > 0);
      setChecklistState('excerpt', $.trim($('#excerpt').val()).length > 0);
      setChecklistState('content', getEditorText().length > 0);
      setChecklistState('thumbnail', !!hasThumbnail());
      setChecklistState('category', hasCategorySelection());
      setChecklistState('date', !!hasPublishDate());
    }

    setHeadingText('#postexcerpt', '一覧用説明文');
    setHeadingText('#postimagediv', '一覧・詳細の画像');
    setTitlePromptText(postType === 'news' ? '例: YUMEHO、新型モデルの販売を開始' : '例: 歩行リハビリの質を高めるポイント');
    $('#excerpt').attr('placeholder', postType === 'news' ? '一覧に表示する短い説明文を入力してください' : 'コラム一覧に表示する要約を入力してください');
    if (postType === 'news') {
      $('label[for="rinascente_display_title"]').text('詳細ページ用タイトル（任意）');
    }

    $('.rinascente-editorial-template-button').on('click', function(e){
      e.preventDefault();
      var template = $(this).data('template') || '';
      if (!template) {
        return;
      }

      var currentContent = getEditorText();
      if (currentContent.length > 0 && !window.confirm('今の本文をテンプレートで置き換えますか？')) {
        return;
      }

      setEditorContent(template);
      updateChecklist();
    });

    $('.rinascente-editorial-insert-template-button').on('click', function(e){
      e.preventDefault();
      var template = $(this).data('insert-template') || '';
      if (!template) {
        return;
      }

      insertEditorContent(template);
      updateChecklist();
    });

    $('.rinascente-editorial-table-button').on('click', function(e){
      e.preventDefault();

      var wrapper = $(this).closest('.rinascente-editorial-table-builder');
      var rows = wrapper.find('.rinascente-editorial-table-rows').val();
      var cols = wrapper.find('.rinascente-editorial-table-cols').val();

      insertEditorContent(buildTableHtml(rows, cols));
      updateChecklist();
    });

    $('.rinascente-editorial-image-button').on('click', function(e){
      e.preventDefault();
      var action = $(this).data('image-action');

      if (action === 'featured') {
        insertFeaturedImage();
        return;
      }

      openLibraryFrame();
    });

    $('#title, #excerpt').on('input', updateChecklist);
    $('#news_categorychecklist, #column_categorychecklist, #taxonomy-news_category, #taxonomy-column_category, #postimagediv, #timestampdiv').on('change click', updateChecklist);

    if (window.tinymce && window.tinymce.get('content')) {
      window.tinymce.get('content').on('input keyup change', updateChecklist);
    }

    setTimeout(updateChecklist, 200);
  });
})(jQuery);
JS;

    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'rinascente_editorial_admin_assets' );


/* =========================================================================
   5. Customizer — Analytics
   ========================================================================= */
add_action( 'customize_register', 'rinascente_customizer' );
function rinascente_customizer( $wp_customize ) {
    // --- Analytics Section ---
    $wp_customize->add_section( 'rinascente_analytics', array(
        'title'    => 'アナリティクス設定',
        'priority' => 35,
    ) );

    $analytics_fields = array(
        'ga4_measurement_id' => array( 'label' => 'GA4 測定ID',       'default' => '', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
        'gtm_container_id'   => array( 'label' => 'GTM コンテナID',   'default' => '', 'sanitize' => 'sanitize_text_field', 'type' => 'text' ),
        'slack_webhook_url'  => array( 'label' => 'Slack Webhook URL', 'default' => '', 'sanitize' => 'esc_url_raw', 'type' => 'password' ),
    );

    foreach ( $analytics_fields as $key => $field ) {
        $wp_customize->add_setting( $key, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['sanitize'],
        ) );
        $wp_customize->add_control( $key, array(
            'label'   => $field['label'],
            'section' => 'rinascente_analytics',
            'type'    => $field['type'],
        ) );
    }

    $wp_customize->add_section(
        'rinascente_related_sites',
        array(
            'title'       => '関連サイト URL',
            'description' => 'YUMEHO など連携先サイトの URL と、staging 用の Basic 認証情報を設定します。',
            'priority'    => 36,
        )
    );

    $related_fields = array(
        'related_yumeho_url' => array(
            'label'    => 'YUMEHO サイト URL',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'related_mica30_url' => array(
            'label'    => 'MICA30 サイト URL',
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        'related_sites_basic_auth_user' => array(
            'label'       => '関連サイト Basic認証 ID',
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => 'staging など、関連サイトに Basic 認証がある場合のみ入力します。',
        ),
        'related_sites_basic_auth_pass' => array(
            'label'    => '関連サイト Basic認証 PW',
            'type'     => 'password',
            'sanitize' => 'sanitize_text_field',
        ),
    );

    foreach ( $related_fields as $key => $field ) {
        if ( 'related_mica30_url' === $key && function_exists( 'rinascente_mica30_enabled' ) && ! rinascente_mica30_enabled() ) {
            continue;
        }

        $wp_customize->add_setting(
            $key,
            array(
                'default'           => '',
                'sanitize_callback' => $field['sanitize'],
            )
        );
        $wp_customize->add_control(
            $key,
            array(
                'label'       => $field['label'],
                'description' => isset( $field['description'] ) ? $field['description'] : '',
                'section'     => 'rinascente_related_sites',
                'type'        => $field['type'],
            )
        );
    }
}


/* =========================================================================
   6. GA4 / GTM Insertion
   ========================================================================= */
add_action( 'wp_head', 'rinascente_analytics_head', 1 );
function rinascente_analytics_head() {
    if ( function_exists( 'rinascente_tracking_allowed' ) && ! rinascente_tracking_allowed() ) {
        return;
    }

    // GTM head snippet
    $gtm_id = get_theme_mod( 'gtm_container_id', '' );
    if ( $gtm_id ) : ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_attr( $gtm_id ); ?>');</script>
<!-- End Google Tag Manager -->
    <?php endif;

    // GA4
    $ga4_id = get_theme_mod( 'ga4_measurement_id', '' );
    if ( $ga4_id ) : ?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_attr( $ga4_id ); ?>');</script>
<!-- End GA4 -->
    <?php endif;
}

add_action( 'wp_body_open', 'rinascente_gtm_body', 1 );
function rinascente_gtm_body() {
    if ( function_exists( 'rinascente_tracking_allowed' ) && ! rinascente_tracking_allowed() ) {
        return;
    }

    $gtm_id = get_theme_mod( 'gtm_container_id', '' );
    if ( $gtm_id ) : ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php endif;
}


/* =========================================================================
   7. Security Hardening
   ========================================================================= */

// Remove WordPress version from head
remove_action( 'wp_head', 'wp_generator' );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

// Block REST API user enumeration for unauthenticated requests
add_filter( 'rest_endpoints', 'rinascente_disable_user_endpoints' );
function rinascente_disable_user_endpoints( $endpoints ) {
    if ( ! is_user_logged_in() ) {
        if ( isset( $endpoints['/wp/v2/users'] ) ) {
            unset( $endpoints['/wp/v2/users'] );
        }
        if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
            unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }
    }
    return $endpoints;
}

// Login attempt rate limiter (simple transient-based)
add_filter( 'authenticate', 'rinascente_limit_login_attempts', 30, 3 );
function rinascente_limit_login_attempts( $user, $username, $password ) {
    if ( empty( $username ) ) {
        return $user;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'login_attempts_' . md5( $ip );
    $attempts = get_transient( $transient_key );

    if ( $attempts !== false && $attempts >= 5 ) {
        return new WP_Error(
            'too_many_attempts',
            'ログイン試行回数が上限に達しました。15分後に再度お試しください。'
        );
    }

    return $user;
}

add_action( 'wp_login_failed', 'rinascente_track_failed_login' );
function rinascente_track_failed_login( $username ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'login_attempts_' . md5( $ip );
    $attempts = get_transient( $transient_key );
    $attempts = $attempts ? $attempts + 1 : 1;
    set_transient( $transient_key, $attempts, 15 * MINUTE_IN_SECONDS );
}

add_action( 'wp_login', 'rinascente_clear_login_attempts', 10, 2 );
function rinascente_clear_login_attempts( $user_login, $user ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'login_attempts_' . md5( $ip );
    delete_transient( $transient_key );
}


/* =========================================================================
   8. Performance — Remove Unnecessary Output
   ========================================================================= */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );


/* =========================================================================
   9. Slack Security Notification
   ========================================================================= */
function send_slack_security_alert( $message ) {
    $webhook_url = esc_url_raw( get_theme_mod( 'slack_webhook_url', '' ) );
    if ( empty( $webhook_url ) ) {
        return;
    }

    $site_name = get_bloginfo( 'name' );
    $payload = array(
        'text' => "[{$site_name}] " . $message,
    );

    wp_remote_post( $webhook_url, array(
        'body'               => wp_json_encode( $payload ),
        'headers'            => array( 'Content-Type' => 'application/json' ),
        'timeout'            => 10,
        'blocking'           => false,
        'sslverify'          => true,
        'reject_unsafe_urls' => true,
    ) );
}

// Failed login
add_action( 'wp_login_failed', 'rinascente_slack_failed_login' );
function rinascente_slack_failed_login( $username ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    send_slack_security_alert( "ログイン失敗: ユーザー名 `{$username}` / IP: {$ip}" );
}

// Admin login
add_action( 'wp_login', 'rinascente_slack_admin_login', 10, 2 );
function rinascente_slack_admin_login( $user_login, $user ) {
    if ( in_array( 'administrator', (array) $user->roles, true ) ) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        send_slack_security_alert( "管理者ログイン: `{$user_login}` / IP: {$ip}" );
    }
}

// Wordfence email filter -> Slack
add_filter( 'wp_mail', 'rinascente_wordfence_to_slack' );
function rinascente_wordfence_to_slack( $args ) {
    if ( isset( $args['subject'] ) && stripos( $args['subject'], 'wordfence' ) !== false ) {
        send_slack_security_alert( "Wordfence通知: {$args['subject']}" );
    }
    return $args;
}

// Plugin/theme update
add_action( 'upgrader_process_complete', 'rinascente_slack_update_complete', 10, 2 );
function rinascente_slack_update_complete( $upgrader, $options ) {
    $type = $options['type'] ?? 'unknown';
    $action = $options['action'] ?? 'unknown';
    send_slack_security_alert( "更新完了: {$type} ({$action})" );
}

// User registration
add_action( 'user_register', 'rinascente_slack_user_register' );
function rinascente_slack_user_register( $user_id ) {
    $user = get_userdata( $user_id );
    send_slack_security_alert( "新規ユーザー登録: `{$user->user_login}` ({$user->user_email})" );
}

// Role change
add_action( 'set_user_role', 'rinascente_slack_role_change', 10, 3 );
function rinascente_slack_role_change( $user_id, $role, $old_roles ) {
    $user = get_userdata( $user_id );
    $old  = implode( ', ', $old_roles );
    send_slack_security_alert( "権限変更: `{$user->user_login}` {$old} → {$role}" );
}


/* =========================================================================
   10. Form Handler Include
   ========================================================================= */
require_once get_template_directory() . '/inc/class-form-handler.php';
require_once get_template_directory() . '/inc/form-config-rinascente.php';
require_once get_template_directory() . '/inc/member-cms.php';
require_once get_template_directory() . '/inc/platform-support.php';


/* =========================================================================
   11. Rank Math カスタム変数（会社情報を title / description で使用可能にする）
   ========================================================================= */
add_action( 'rank_math/vars/register_extra_replacements', 'rinascente_rankmath_vars' );
function rinascente_rankmath_vars() {
    // %company_name% — 会社名
    rank_math_register_var_replacement(
        'company_name',
        array(
            'name'        => '会社名',
            'description' => 'カスタマイザーの会社名',
            'variable'    => 'company_name',
            'example'     => get_theme_mod( 'company_name', '株式会社リナシェンテ' ),
        ),
        function() {
            return get_theme_mod( 'company_name', '株式会社リナシェンテ' );
        }
    );

    // %company_tel% — 電話番号
    rank_math_register_var_replacement(
        'company_tel',
        array(
            'name'        => '電話番号',
            'description' => 'カスタマイザーの電話番号',
            'variable'    => 'company_tel',
            'example'     => get_theme_mod( 'company_tel', '0859-00-1234' ),
        ),
        function() {
            return get_theme_mod( 'company_tel', '0859-00-1234' );
        }
    );

    // %company_address% — 所在地
    rank_math_register_var_replacement(
        'company_address',
        array(
            'name'        => '所在地',
            'description' => 'カスタマイザーの所在地',
            'variable'    => 'company_address',
            'example'     => get_theme_mod( 'company_address', '' ),
        ),
        function() {
            return get_theme_mod( 'company_address', '' );
        }
    );

    // %company_hours% — 受付時間
    rank_math_register_var_replacement(
        'company_hours',
        array(
            'name'        => '受付時間',
            'description' => 'カスタマイザーの受付時間',
            'variable'    => 'company_hours',
            'example'     => get_theme_mod( 'company_hours', '平日 9:00〜17:00' ),
        ),
        function() {
            return get_theme_mod( 'company_hours', '平日 9:00〜17:00' );
        }
    );
}


/* =========================================================================
   12. Organization Schema (Front Page)
   ========================================================================= */
add_action( 'wp_head', 'rinascente_organization_schema' );
function rinascente_organization_schema() {
    if ( ! is_front_page() ) {
        return;
    }

    $organization = function_exists( 'rinascente_schema_org' ) ? rinascente_schema_org() : array();

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array_filter(
            array(
                $organization,
            )
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

include_once get_template_directory() . '/inc/structured-data-pages.php';

/* =========================================================================
   12. WebP Upload Support
   ========================================================================= */
add_filter( 'mime_types', 'rinascente_allow_webp' );
function rinascente_allow_webp( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}

add_filter( 'upload_mimes', 'rinascente_upload_webp' );
function rinascente_upload_webp( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}


/* =========================================================================
   13. Xserver SMTP Configuration
   ========================================================================= */
add_action( 'phpmailer_init', 'rinascente_smtp_config' );
function rinascente_smtp_config( $phpmailer ) {
    // Uncomment and fill in for production on Xserver
    // $phpmailer->isSMTP();
    // $phpmailer->Host       = 'sv0000.xserver.jp';   // Xserver SMTP host
    // $phpmailer->SMTPAuth   = true;
    // $phpmailer->Port       = 587;
    // $phpmailer->SMTPSecure = 'tls';
    // $phpmailer->Username   = 'info@example.com';    // SMTP username
    // $phpmailer->Password   = 'your-password-here';  // SMTP password
    // $phpmailer->From       = 'info@example.com';
    // $phpmailer->FromName   = get_theme_mod( 'company_name', 'Rinascente' );
}
