<?php
/**
 * YUMEHO Theme Functions
 *
 * @package YUMEHO
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'YUMEHO_VERSION', '1.0.0' );
define( 'YUMEHO_DIR', get_template_directory() );
define( 'YUMEHO_URI', get_template_directory_uri() );

if ( ! defined( 'YUMEHO_ENABLE_MICA30' ) ) {
    define( 'YUMEHO_ENABLE_MICA30', false );
}

function yumeho_mica30_enabled() {
    return defined( 'YUMEHO_ENABLE_MICA30' ) && YUMEHO_ENABLE_MICA30;
}

function yumeho_company_products_text() {
    $default_products = yumeho_mica30_enabled()
        ? "YUMEHO（歩行リハビリ支援システム）\nMICA30（多相電動式造影剤注入装置）"
        : 'YUMEHO（歩行リハビリ支援システム）';
    $products         = trim( (string) yumeho_theme_mod( 'company_products', $default_products ) );

    if ( yumeho_mica30_enabled() ) {
        return '' !== $products ? $products : $default_products;
    }

    $products = preg_replace( '/^.*MICA30.*(?:\R|$)/mu', '', $products );
    $products = trim( preg_replace( "/\R{2,}/u", "\n", (string) $products ) );

    return '' !== $products ? $products : $default_products;
}

/**
 * Resolve sibling-site URLs for Local and production-like environments.
 */
function yumeho_related_site_url( $site, $path = '/' ) {
    if ( 'yumeho' === $site ) {
        return home_url( $path );
    }

    $configured = yumeho_theme_mod( 'related_' . $site . '_url', '' );
    if ( $configured ) {
        return trailingslashit( $configured ) . ltrim( $path, '/' );
    }

    $host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
    if ( $host && ( str_ends_with( $host, '.local' ) || 'localhost' === $host ) ) {
        if ( 'corporate' === $site ) {
            return 'http://rinascente.local/' . ltrim( $path, '/' );
        }

        if ( 'mica30' === $site ) {
            return 'http://mica30.local/' . ltrim( $path, '/' );
        }
    }

    return '#';
}

function yumeho_asset_version( $relative_path ) {
    $absolute_path = get_template_directory() . '/' . ltrim( $relative_path, '/' );

    if ( file_exists( $absolute_path ) ) {
        return (string) filemtime( $absolute_path );
    }

    return YUMEHO_VERSION;
}

function yumeho_site_admin_menu_slug() {
    return 'yumeho-site-admin';
}

function yumeho_admin_hub_count_text( $post_type ) {
    $counts    = wp_count_posts( $post_type );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;

    return sprintf( '公開 %d件 / 下書き %d件', $published, $drafts );
}

function yumeho_admin_hub_styles() {
    ?>
    <style>
    .yumeho-admin-hub {
        max-width: 1180px;
    }
    .yumeho-admin-hub__lead {
        margin: 12px 0 0;
        max-width: 72ch;
        color: #475569;
        line-height: 1.9;
    }
    .yumeho-admin-hub__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    .yumeho-admin-hub__card {
        padding: 20px 22px;
        border: 1px solid #d8e3f0;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    }
    .yumeho-admin-hub__eyebrow {
        margin: 0 0 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0068b7;
    }
    .yumeho-admin-hub__card h2 {
        margin: 0 0 8px;
        font-size: 18px;
        line-height: 1.5;
    }
    .yumeho-admin-hub__card p {
        margin: 0 0 14px;
        color: #475569;
        line-height: 1.8;
    }
    .yumeho-admin-hub__meta {
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
    .yumeho-admin-hub__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
    .yumeho-admin-hub__dashboard-list {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 8px;
    }
    .yumeho-admin-hub__dashboard-list a {
        font-weight: 600;
        text-decoration: none;
    }
    </style>
    <?php
}

function yumeho_render_site_admin_page() {
    $cards = array(
        array(
            'eyebrow'     => 'Map',
            'title'       => '導入拠点',
            'description' => '地図のピン、導入施設数、設置エリア数の元データを管理します。住所ベースで位置計算し、必要なときだけ微調整します。',
            'meta'        => yumeho_admin_hub_count_text( 'installation_site' ),
            'primary'     => array(
                'label' => '導入拠点一覧',
                'url'   => admin_url( 'edit.php?post_type=installation_site' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=installation_site' ),
            ),
        ),
        array(
            'eyebrow'     => 'Cases',
            'title'       => '導入事例',
            'description' => '導入事例と施設の声を更新します。一覧では施設名、地域、機種、掲載状態をまとめて確認できます。',
            'meta'        => yumeho_admin_hub_count_text( 'case_study' ),
            'primary'     => array(
                'label' => '導入事例一覧',
                'url'   => admin_url( 'edit.php?post_type=case_study' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=case_study' ),
            ),
        ),
        array(
            'eyebrow'     => 'FAQ',
            'title'       => 'FAQ',
            'description' => '質問と回答、カテゴリ、並び順を管理します。カテゴリ選択と公開チェックをひとつの画面に寄せています。',
            'meta'        => yumeho_admin_hub_count_text( 'faq' ),
            'primary'     => array(
                'label' => 'FAQ一覧',
                'url'   => admin_url( 'edit.php?post_type=faq' ),
            ),
            'secondary'   => array(
                'label' => '新規追加',
                'url'   => admin_url( 'post-new.php?post_type=faq' ),
            ),
        ),
        array(
            'eyebrow'     => 'Pricing',
            'title'       => '価格参照',
            'description' => 'YUMEHO 側の価格表示は、Rinascente 側の製品マスターを共通参照しています。値段の実更新先もここから確認できます。',
            'meta'        => 'Rinascente と共通',
            'primary'     => array(
                'label' => '価格参照を開く',
                'url'   => admin_url( 'admin.php?page=yumeho-pricing' ),
            ),
            'secondary'   => array(
                'label' => '固定ページ一覧',
                'url'   => admin_url( 'edit.php?post_type=page' ),
            ),
        ),
    );

    echo '<div class="wrap yumeho-admin-hub">';
    echo '<h1>サイト更新</h1>';
    echo '<p class="yumeho-admin-hub__lead">YUMEHO サイトで日常的に使う更新先をまとめています。導入拠点、導入事例、FAQ、価格確認の入口をここに集約して、迷わず開ける状態に整えています。</p>';
    yumeho_admin_hub_styles();
    echo '<div class="yumeho-admin-hub__grid">';
    foreach ( $cards as $card ) {
        echo '<section class="yumeho-admin-hub__card">';
        echo '<p class="yumeho-admin-hub__eyebrow">' . esc_html( $card['eyebrow'] ) . '</p>';
        echo '<h2>' . esc_html( $card['title'] ) . '</h2>';
        echo '<p>' . esc_html( $card['description'] ) . '</p>';
        echo '<span class="yumeho-admin-hub__meta">' . esc_html( $card['meta'] ) . '</span>';
        echo '<div class="yumeho-admin-hub__actions">';
        echo '<a class="button button-primary" href="' . esc_url( $card['primary']['url'] ) . '">' . esc_html( $card['primary']['label'] ) . '</a>';
        echo '<a class="button button-secondary" href="' . esc_url( $card['secondary']['url'] ) . '">' . esc_html( $card['secondary']['label'] ) . '</a>';
        echo '</div>';
        echo '</section>';
    }
    echo '</div></div>';
}

function yumeho_register_admin_hub_menus() {
    add_menu_page(
        'サイト更新',
        'サイト更新',
        'edit_pages',
        yumeho_site_admin_menu_slug(),
        'yumeho_render_site_admin_page',
        'dashicons-admin-site-alt3',
        5
    );

    add_submenu_page(
        yumeho_site_admin_menu_slug(),
        '更新ダッシュボード',
        '更新ダッシュボード',
        'edit_pages',
        yumeho_site_admin_menu_slug(),
        'yumeho_render_site_admin_page'
    );

    add_submenu_page(
        yumeho_site_admin_menu_slug(),
        '固定ページ',
        '固定ページ',
        'edit_pages',
        'edit.php?post_type=page'
    );

    add_submenu_page(
        yumeho_site_admin_menu_slug(),
        'テーマ設定（追跡コードなど）',
        'テーマ設定（追跡コードなど）',
        'edit_theme_options',
        'customize.php'
    );
}
add_action( 'admin_menu', 'yumeho_register_admin_hub_menus', 8 );

function yumeho_cleanup_admin_navigation() {
    remove_menu_page( 'edit.php' );
    remove_menu_page( 'edit-comments.php' );

    global $submenu;
    if ( isset( $submenu['themes.php'] ) && is_array( $submenu['themes.php'] ) ) {
        foreach ( $submenu['themes.php'] as $index => $item ) {
            if ( isset( $item[2] ) && 'customize.php' === $item[2] ) {
                $submenu['themes.php'][ $index ][0] = 'テーマ設定（追跡コードなど）';
            }
        }
    }
}
add_action( 'admin_menu', 'yumeho_cleanup_admin_navigation', 999 );

function yumeho_cleanup_admin_bar( $wp_admin_bar ) {
    if ( ! $wp_admin_bar instanceof WP_Admin_Bar ) {
        return;
    }

    $wp_admin_bar->remove_node( 'new-post' );
    $wp_admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'yumeho_cleanup_admin_bar', 999 );

function yumeho_dashboard_quick_links_widget() {
    echo '<div class="yumeho-admin-hub">';
    yumeho_admin_hub_styles();
    echo '<p style="margin-top:0;">YUMEHO の更新は、まずこの入口から入ると迷いにくくなります。</p>';
    echo '<ul class="yumeho-admin-hub__dashboard-list">';
    echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=' . yumeho_site_admin_menu_slug() ) ) . '">サイト更新</a> 導入拠点、導入事例、FAQ、価格参照の入口</li>';
    echo '<li><a href="' . esc_url( admin_url( 'edit.php?post_type=page' ) ) . '">固定ページ</a> 固定ページ本文やお問い合わせページなどの更新</li>';
    echo '</ul>';
    echo '</div>';
}

function yumeho_dashboard_cleanup() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    wp_add_dashboard_widget(
        'yumeho_admin_quick_links',
        'まず使う更新先',
        'yumeho_dashboard_quick_links_widget'
    );
}
add_action( 'wp_dashboard_setup', 'yumeho_dashboard_cleanup' );

function yumeho_default_ceiling_rail_length_options() {
    return array( 5, 10, 20 );
}

function yumeho_parse_product_rail_length_options( $value ) {
    if ( is_array( $value ) ) {
        $parts = $value;
    } else {
        $parts = preg_split( '/[\s,、\/]+/u', (string) $value );
    }

    if ( ! is_array( $parts ) ) {
        return array();
    }

    $options = array();

    foreach ( $parts as $part ) {
        if ( ! preg_match( '/(\d+)/u', (string) $part, $matches ) ) {
            continue;
        }

        $length = absint( $matches[1] );
        if ( $length > 0 ) {
            $options[ $length ] = $length;
        }
    }

    ksort( $options, SORT_NUMERIC );

    return array_values( $options );
}

function yumeho_system_rail_length_options( $item ) {
    $options = yumeho_parse_product_rail_length_options( $item['rail_length_options'] ?? array() );

    if ( 'ceiling' === ( $item['install_type'] ?? '' ) ) {
        return ! empty( $options ) ? $options : yumeho_default_ceiling_rail_length_options();
    }

    if ( 'stand' === ( $item['install_type'] ?? '' ) && ! empty( $options ) ) {
        return $options;
    }

    if ( 'stand' === ( $item['install_type'] ?? '' ) && ! empty( $item['max_rail_length'] ) ) {
        return array( (int) $item['max_rail_length'] );
    }

    return $options;
}

function yumeho_shared_product_catalog_fallback_items( $product_key = 'yumeho' ) {
    $product_key = sanitize_key( $product_key );
    if ( 'yumeho' !== $product_key ) {
        return array();
    }

    return array(
        array(
            'id'                 => 0,
            'title'              => 'スタンド型 PGT-9000',
            'slug'               => 'stand-pgt-9000',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'system',
            'category_label'     => '本体システム',
            'sort_order'         => 10,
            'code'               => 'pgt-9000',
            'display_name'       => 'スタンド型 PGT-9000',
            'short_name'         => 'PGT-9000',
            'spec'               => '2000×4000mm / 総レール長14m',
            'install_type'       => 'stand',
            'install_type_label' => 'スタンド型',
            'max_rail_length'    => 14,
            'rail_length_options' => array( 14 ),
            'pricing_option_key' => '',
            'unit_label'         => '台',
            'max_quantity'       => 1,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'スタンド型本体 / レール4m / G-Suit ハーネス1着',
            'unit_price'         => 1150000,
            'rail_price_per_m'   => 30000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'スタンド型 PGT-9001',
            'slug'               => 'stand-pgt-9001',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'system',
            'category_label'     => '本体システム',
            'sort_order'         => 20,
            'code'               => 'pgt-9001',
            'display_name'       => 'スタンド型 PGT-9001',
            'short_name'         => 'PGT-9001',
            'spec'               => '2000×6000mm / 総レール長20m',
            'install_type'       => 'stand',
            'install_type_label' => 'スタンド型',
            'max_rail_length'    => 20,
            'rail_length_options' => array( 20 ),
            'pricing_option_key' => '',
            'unit_label'         => '台',
            'max_quantity'       => 1,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'スタンド型本体 / レール6m / G-Suit ハーネス2着',
            'unit_price'         => 1150000,
            'rail_price_per_m'   => 30000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => '天井直付型 FCW-3000',
            'slug'               => 'ceiling-fcw-3000',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'system',
            'category_label'     => '本体システム',
            'sort_order'         => 30,
            'code'               => 'fcw-3000',
            'display_name'       => '天井直付型 FCW-3000',
            'short_name'         => 'FCW-3000',
            'spec'               => 'カスタム設計 / 周回・直線レール対応',
            'install_type'       => 'ceiling',
            'install_type_label' => '天井直付型',
            'max_rail_length'    => 0,
            'rail_length_options' => yumeho_default_ceiling_rail_length_options(),
            'pricing_option_key' => '',
            'unit_label'         => '台',
            'max_quantity'       => 1,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => '天井直付型本体 / レール構成一式 / G-Suit ハーネス2着',
            'unit_price'         => 950000,
            'rail_price_per_m'   => 30000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'G-Suit ハーネス',
            'slug'               => 'g-suit',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'harness',
            'category_label'     => 'ハーネス',
            'sort_order'         => 40,
            'code'               => 'g-suit',
            'display_name'       => 'G-Suit ハーネス',
            'short_name'         => 'G-Suit',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => '',
            'unit_label'         => '着',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'G-Suit ハーネス 1着',
            'unit_price'         => 0,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => '追加ハーネス',
            'slug'               => 'extra-harness',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'harness',
            'category_label'     => 'ハーネス',
            'sort_order'         => 50,
            'code'               => 'extra-harness',
            'display_name'       => '追加ハーネス',
            'short_name'         => '追加ハーネス',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'harness_extra',
            'unit_label'         => '着',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => '追加ハーネス 1着',
            'unit_price'         => 200000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'JRX',
            'slug'               => 'jrx',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'option',
            'category_label'     => 'オプション',
            'sort_order'         => 60,
            'code'               => 'jrx',
            'display_name'       => 'JRX（Junction Rail eXpress）方向転換システム',
            'short_name'         => 'JRX',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'jrx',
            'unit_label'         => '台',
            'max_quantity'       => 1,
            'selection_type'     => 'checkbox',
            'selection_type_label' => 'オン / オフ',
            'contract_template'  => 'JRX 方向転換システム',
            'unit_price'         => 350000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'T-Pulling',
            'slug'               => 't-pulling',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'option',
            'category_label'     => 'オプション',
            'sort_order'         => 70,
            'code'               => 't-pulling',
            'display_name'       => 'T-Pulling（プーリングシステム）',
            'short_name'         => 'T-Pulling',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'pulling',
            'unit_label'         => '台',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'T-Pulling オプション',
            'unit_price'         => 300000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'T-Sling',
            'slug'               => 't-sling',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'option',
            'category_label'     => 'オプション',
            'sort_order'         => 80,
            'code'               => 't-sling',
            'display_name'       => 'T-Sling（スリングシステム）',
            'short_name'         => 'T-Sling',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'sling',
            'unit_label'         => '台',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'T-Sling オプション',
            'unit_price'         => 250000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'G-Cord',
            'slug'               => 'g-cord',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'option',
            'category_label'     => 'オプション',
            'sort_order'         => 90,
            'code'               => 'g-cord',
            'display_name'       => 'G-Cord（自動高さ調整）',
            'short_name'         => 'G-Cord',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'gcord',
            'unit_label'         => '台',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'G-Cord オプション',
            'unit_price'         => 280000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => 'SnG',
            'slug'               => 'sng',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'option',
            'category_label'     => 'オプション',
            'sort_order'         => 100,
            'code'               => 'sng',
            'display_name'       => 'SnG（ロック機構）',
            'short_name'         => 'SnG',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'sng',
            'unit_label'         => '台',
            'max_quantity'       => 5,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => 'SnG オプション',
            'unit_price'         => 150000,
            'source_note'        => 'fallback',
        ),
        array(
            'id'                 => 0,
            'title'              => '歩行データ計測キット',
            'slug'               => 'walk-data-kit',
            'product_key'        => 'yumeho',
            'product_label'      => 'YUMEHO',
            'category'           => 'kit',
            'category_label'     => '周辺キット',
            'sort_order'         => 110,
            'code'               => 'walk-data-kit',
            'display_name'       => '歩行データ計測キット（PC連携）',
            'short_name'         => '計測キット',
            'spec'               => '',
            'install_type'       => '',
            'install_type_label' => '',
            'max_rail_length'    => 0,
            'pricing_option_key' => 'measure',
            'unit_label'         => '台',
            'max_quantity'       => 1,
            'selection_type'     => 'quantity',
            'selection_type_label' => '数量選択',
            'contract_template'  => '歩行データ計測キット（PC連携）',
            'unit_price'         => 200000,
            'source_note'        => 'fallback',
        ),
    );
}

function yumeho_normalize_shared_product_catalog_item( $item ) {
    if ( ! is_array( $item ) ) {
        return array();
    }

    $normalized = array(
        'id'                 => (int) ( $item['id'] ?? 0 ),
        'title'              => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
        'slug'               => sanitize_title( (string) ( $item['slug'] ?? '' ) ),
        'product_key'        => sanitize_key( (string) ( $item['product_key'] ?? '' ) ),
        'product_label'      => sanitize_text_field( (string) ( $item['product_label'] ?? '' ) ),
        'category'           => sanitize_key( (string) ( $item['category'] ?? '' ) ),
        'category_label'     => sanitize_text_field( (string) ( $item['category_label'] ?? '' ) ),
        'sort_order'         => absint( $item['sort_order'] ?? 999 ),
        'code'               => sanitize_key( (string) ( $item['code'] ?? '' ) ),
        'display_name'       => sanitize_text_field( (string) ( $item['display_name'] ?? '' ) ),
        'short_name'         => sanitize_text_field( (string) ( $item['short_name'] ?? '' ) ),
        'spec'               => sanitize_text_field( (string) ( $item['spec'] ?? '' ) ),
        'install_type'       => sanitize_key( (string) ( $item['install_type'] ?? '' ) ),
        'install_type_label' => sanitize_text_field( (string) ( $item['install_type_label'] ?? '' ) ),
        'max_rail_length'    => absint( $item['max_rail_length'] ?? 0 ),
        'rail_length_options' => yumeho_parse_product_rail_length_options( $item['rail_length_options'] ?? array() ),
        'unit_price'         => absint( $item['unit_price'] ?? 0 ),
        'rail_price_per_m'   => absint( $item['rail_price_per_m'] ?? 0 ),
        'pricing_option_key' => sanitize_key( (string) ( $item['pricing_option_key'] ?? '' ) ),
        'unit_label'         => sanitize_text_field( (string) ( $item['unit_label'] ?? '' ) ),
        'max_quantity'       => absint( $item['max_quantity'] ?? 0 ),
        'selection_type'     => sanitize_key( (string) ( $item['selection_type'] ?? 'quantity' ) ),
        'selection_type_label' => sanitize_text_field( (string) ( $item['selection_type_label'] ?? '' ) ),
        'contract_template'  => sanitize_textarea_field( (string) ( $item['contract_template'] ?? '' ) ),
        'source_note'        => sanitize_textarea_field( (string) ( $item['source_note'] ?? '' ) ),
    );

    if ( '' === $normalized['display_name'] ) {
        $normalized['display_name'] = $normalized['title'];
    }

    if ( '' === $normalized['short_name'] ) {
        $normalized['short_name'] = $normalized['display_name'];
    }

    if ( '' === $normalized['unit_label'] ) {
        $normalized['unit_label'] = 'harness' === $normalized['category'] ? '着' : '台';
    }

    if ( $normalized['max_quantity'] < 1 ) {
        $normalized['max_quantity'] = 'checkbox' === $normalized['selection_type'] ? 1 : 5;
    }

    return $normalized;
}

function yumeho_fetch_shared_product_catalog( $args = array() ) {
    $defaults = array(
        'product_key' => 'yumeho',
        'category'    => array(),
    );
    $args = wp_parse_args( $args, $defaults );

    $product_key = sanitize_key( (string) $args['product_key'] );
    $categories  = array_filter( array_map( 'sanitize_key', (array) $args['category'] ) );

    $cache_key = md5(
        wp_json_encode(
            array(
                'product_key' => $product_key,
                'category'    => $categories,
            )
        )
    );

    static $runtime_cache = array();
    if ( isset( $runtime_cache[ $cache_key ] ) ) {
        return $runtime_cache[ $cache_key ];
    }

    $transient_key = 'yumeho_shared_product_catalog_' . $cache_key;
    $cached_items  = get_transient( $transient_key );
    if ( false !== $cached_items && is_array( $cached_items ) ) {
        $runtime_cache[ $cache_key ] = $cached_items;
        return $cached_items;
    }

    $endpoint = yumeho_related_site_url( 'corporate', '/wp-json/rinascente/v1/product-catalog' );
    $items    = array();

    if ( $endpoint && '#' !== $endpoint ) {
        $request_url = add_query_arg(
            array(
                'product_key' => $product_key,
                'category'    => ! empty( $categories ) ? implode( ',', $categories ) : null,
            ),
            $endpoint
        );

        $response = wp_remote_get(
            $request_url,
            wp_parse_args(
                array(
                    'timeout'            => 5,
                    'reject_unsafe_urls' => true,
                ),
                function_exists( 'yumeho_related_site_request_args' ) ? yumeho_related_site_request_args() : array()
            )
        );

        if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
            $payload = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( is_array( $payload ) && ! empty( $payload['items'] ) && is_array( $payload['items'] ) ) {
                $items = array_values( array_filter( array_map( 'yumeho_normalize_shared_product_catalog_item', $payload['items'] ) ) );
            }
        }
    }

    if ( empty( $items ) ) {
        $items = array_values( array_filter( array_map( 'yumeho_normalize_shared_product_catalog_item', yumeho_shared_product_catalog_fallback_items( $product_key ) ) ) );
    }

    if ( ! empty( $categories ) ) {
        $items = array_values(
            array_filter(
                $items,
                static function( $item ) use ( $categories ) {
                    return in_array( $item['category'], $categories, true );
                }
            )
        );
    }

    $runtime_cache[ $cache_key ] = $items;
    set_transient( $transient_key, $items, 15 * MINUTE_IN_SECONDS );

    return $items;
}

function yumeho_shared_product_catalog_context( $product_key = 'yumeho' ) {
    $items           = yumeho_fetch_shared_product_catalog( array( 'product_key' => $product_key ) );
    $items_by_code   = array();
    $items_by_pricing = array();
    $systems         = array();
    $options         = array();
    $stand_systems   = array();
    $ceiling_system  = null;
    $harnesses       = array();

    foreach ( $items as $item ) {
        if ( '' !== $item['code'] ) {
            $items_by_code[ $item['code'] ] = $item;
        }

        if ( '' !== $item['pricing_option_key'] ) {
            $items_by_pricing[ $item['pricing_option_key'] ] = $item;
        }

        if ( 'system' === $item['category'] ) {
            $systems[] = $item;

            if ( 'stand' === $item['install_type'] ) {
                $stand_systems[] = $item;
            }

            if ( 'ceiling' === $item['install_type'] && null === $ceiling_system ) {
                $ceiling_system = $item;
            }
        }

        if ( in_array( $item['category'], array( 'option', 'kit' ), true ) ) {
            $options[] = $item;
        }

        if ( 'harness' === $item['category'] ) {
            $harnesses[] = $item;
        }
    }

    usort(
        $stand_systems,
        static function( $left, $right ) {
            return (int) $left['max_rail_length'] <=> (int) $right['max_rail_length'];
        }
    );

    return array(
        'items'            => $items,
        'by_code'          => $items_by_code,
        'by_pricing_key'   => $items_by_pricing,
        'systems'          => $systems,
        'stand_systems'    => $stand_systems,
        'ceiling_system'   => $ceiling_system,
        'options'          => $options,
        'harnesses'        => $harnesses,
    );
}

function yumeho_product_spec_parts( $spec ) {
    $parts = preg_split( '/\s*\/\s*/u', (string) $spec );
    if ( ! is_array( $parts ) ) {
        return array();
    }

    return array_values( array_filter( array_map( 'trim', $parts ) ) );
}

function yumeho_product_result_name( $item ) {
    $display_name = trim( (string) ( $item['display_name'] ?? '' ) );
    $spec_parts   = yumeho_product_spec_parts( $item['spec'] ?? '' );

    if ( 'stand' === ( $item['install_type'] ?? '' ) && ! empty( $spec_parts[0] ) ) {
        return $display_name . '（' . $spec_parts[0] . '）';
    }

    return $display_name;
}

function yumeho_product_rail_label( $item, $rail_length = 0 ) {
    if ( 'stand' === ( $item['install_type'] ?? '' ) && ! empty( $item['max_rail_length'] ) ) {
        return '総レール長 ' . (int) $item['max_rail_length'] . 'm';
    }

    if ( 'ceiling' === ( $item['install_type'] ?? '' ) && $rail_length > 0 ) {
        return '全長 ' . (int) $rail_length . 'm（カスタム設計）';
    }

    $spec_parts = yumeho_product_spec_parts( $item['spec'] ?? '' );
    return $spec_parts[1] ?? ( $spec_parts[0] ?? '' );
}

function yumeho_pricing_option_value( $pricing_key, $default = 0 ) {
    $catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );

    if ( 'ceiling_base' === $pricing_key ) {
        return absint( $catalog_context['ceiling_system']['unit_price'] ?? $default );
    }

    if ( 'stand_base' === $pricing_key ) {
        $stand_system = $catalog_context['stand_systems'][0] ?? array();
        return absint( $stand_system['unit_price'] ?? $default );
    }

    if ( 'rail_per_m' === $pricing_key ) {
        foreach ( (array) $catalog_context['systems'] as $item ) {
            $rail_price = absint( $item['rail_price_per_m'] ?? 0 );
            if ( $rail_price > 0 ) {
                return $rail_price;
            }
        }

        return (int) $default;
    }

    if ( isset( $catalog_context['by_pricing_key'][ $pricing_key ] ) ) {
        return absint( $catalog_context['by_pricing_key'][ $pricing_key ]['unit_price'] ?? $default );
    }

    return (int) $default;
}

function yumeho_pricing_catalog_config() {
    $catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
    $systems         = array();
    $options         = array();

    foreach ( $catalog_context['systems'] as $item ) {
        $systems[] = array(
            'code'          => $item['code'],
            'displayName'   => $item['display_name'],
            'shortName'     => $item['short_name'],
            'spec'          => $item['spec'],
            'installType'   => $item['install_type'],
            'maxRailLength' => (int) $item['max_rail_length'],
            'railLengthOptions' => yumeho_system_rail_length_options( $item ),
            'unitPrice'     => (int) ( $item['unit_price'] ?? 0 ),
            'railPricePerM' => (int) ( $item['rail_price_per_m'] ?? 0 ),
            'resultName'    => yumeho_product_result_name( $item ),
            'railLabel'     => yumeho_product_rail_label( $item ),
        );
    }

    foreach ( $catalog_context['options'] as $item ) {
        if ( '' === $item['pricing_option_key'] ) {
            continue;
        }

        $options[ $item['pricing_option_key'] ] = array(
            'code'          => $item['code'],
            'label'         => $item['display_name'],
            'shortLabel'    => $item['short_name'],
            'price'         => (int) ( $item['unit_price'] ?? 0 ),
            'unitLabel'     => $item['unit_label'],
            'maxQuantity'   => (int) $item['max_quantity'],
            'selectionType' => $item['selection_type'],
        );
    }

    $ceiling_system = $catalog_context['ceiling_system'] ?? array();
    $stand_system   = $catalog_context['stand_systems'][0] ?? array();
    $harness_item   = $catalog_context['by_pricing_key']['harness_extra'] ?? array();

    return array(
        'systems'       => $systems,
        'options'       => $options,
        'ceilingPrice'  => absint( $ceiling_system['unit_price'] ?? 950000 ),
        'standPrice'    => absint( $stand_system['unit_price'] ?? 1150000 ),
        'harnessPrice'  => absint( $harness_item['unit_price'] ?? 200000 ),
        'railPricePerM' => yumeho_pricing_option_value( 'rail_per_m', 30000 ),
    );
}

/**
 * コーポレートサイト(Rinascente)からコラム記事を取得する。
 * YUMEHO サイトでは「YUMEHO サイトでも公開する」がチェックされた記事のみ表示。
 *
 * @param int $limit 取得件数
 * @return array
 */
function yumeho_fetch_shared_columns( $limit = 6 ) {
    $endpoint = yumeho_related_site_url( 'corporate', '/wp-json/wp/v2/column' );
    if ( ! $endpoint || '#' === $endpoint ) {
        return array();
    }

    $request_url = add_query_arg(
        array(
            'per_page' => max( 1, (int) $limit * 2 ), // フィルタ前提なので多めに取得
            '_embed'   => '1',
            'orderby'  => 'date',
            'order'    => 'desc',
        ),
        $endpoint
    );

    $request_args = wp_parse_args(
        array(
            'timeout'            => 8,
            'reject_unsafe_urls' => true,
        ),
        function_exists( 'yumeho_related_site_request_args' ) ? yumeho_related_site_request_args() : array()
    );

    $cache_key = 'yumeho_shared_columns_v2_' . md5( wp_json_encode( array( $request_url, $request_args ) ) );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached && is_array( $cached ) ) {
        return $cached;
    }

    $response = wp_remote_get( $request_url, $request_args );
    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return array();
    }

    $items = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $items ) ) {
        return array();
    }

    $columns = array();
    foreach ( $items as $item ) {
        $meta = isset( $item['meta'] ) && is_array( $item['meta'] ) ? $item['meta'] : array();

        // YUMEHO サイトで公開フラグ
        $is_published = '1' === ( $meta['_rinascente_yumeho_publish'] ?? '' );
        if ( ! $is_published ) {
            continue;
        }

        $image_url = '';
        if ( ! empty( $item['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
            $image_url = (string) $item['_embedded']['wp:featuredmedia'][0]['source_url'];
        }

        $cat_name = '';
        if ( ! empty( $item['_embedded']['wp:term'] ) && is_array( $item['_embedded']['wp:term'] ) ) {
            foreach ( $item['_embedded']['wp:term'] as $term_group ) {
                if ( ! is_array( $term_group ) ) continue;
                foreach ( $term_group as $term ) {
                    if ( ! is_array( $term ) ) continue;
                    if ( isset( $term['taxonomy'] ) && 'column_category' === $term['taxonomy'] && '' === $cat_name ) {
                        $cat_name = (string) $term['name'];
                    }
                }
            }
        }

        $columns[] = array(
            'id'           => (int) ( $item['id'] ?? 0 ),
            'slug'         => (string) ( $item['slug'] ?? '' ),
            'title'        => wp_strip_all_tags( (string) ( $item['title']['rendered'] ?? '' ) ),
            'excerpt'      => wp_strip_all_tags( (string) ( $item['excerpt']['rendered'] ?? '' ) ),
            'content'      => (string) ( $item['content']['rendered'] ?? '' ),
            'link'         => esc_url_raw( (string) ( $item['link'] ?? '' ) ),
            'image_url'    => esc_url_raw( $image_url ),
            'category'     => $cat_name,
            'date'         => (string) ( $item['date'] ?? '' ),
            'modified'     => (string) ( $item['modified'] ?? '' ),
            'yumeho_lead'  => (string) ( $meta['_rinascente_yumeho_lead'] ?? '' ),
        );

        if ( count( $columns ) >= (int) $limit ) {
            break;
        }
    }

    set_transient( $cache_key, $columns, 15 * MINUTE_IN_SECONDS );

    return $columns;
}

/**
 * スラッグでコラム1件を取得する。
 *
 * @param string $slug コラムのスラッグ
 * @return array|null
 */
function yumeho_fetch_shared_column_by_slug( $slug ) {
    $endpoint = yumeho_related_site_url( 'corporate', '/wp-json/wp/v2/column' );
    if ( ! $endpoint || '#' === $endpoint || '' === $slug ) {
        return null;
    }

    $request_url = add_query_arg(
        array(
            'slug'   => sanitize_title( $slug ),
            '_embed' => '1',
        ),
        $endpoint
    );

    $request_args = wp_parse_args(
        array(
            'timeout'            => 8,
            'reject_unsafe_urls' => true,
        ),
        function_exists( 'yumeho_related_site_request_args' ) ? yumeho_related_site_request_args() : array()
    );

    $cache_key = 'yumeho_shared_column_single_v2_' . md5( wp_json_encode( array( $request_url, $request_args ) ) );
    $cached    = get_transient( $cache_key );
    if ( false !== $cached ) {
        return $cached;
    }

    $response = wp_remote_get( $request_url, $request_args );
    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return null;
    }

    $items = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $items ) || empty( $items ) ) {
        return null;
    }

    $item = $items[0];
    $meta = isset( $item['meta'] ) && is_array( $item['meta'] ) ? $item['meta'] : array();

    // 公開フラグチェック
    if ( '1' !== ( $meta['_rinascente_yumeho_publish'] ?? '' ) ) {
        return null;
    }

    $image_url = '';
    if ( ! empty( $item['_embedded']['wp:featuredmedia'][0]['source_url'] ) ) {
        $image_url = (string) $item['_embedded']['wp:featuredmedia'][0]['source_url'];
    }

    $cat_name = '';
    if ( ! empty( $item['_embedded']['wp:term'] ) && is_array( $item['_embedded']['wp:term'] ) ) {
        foreach ( $item['_embedded']['wp:term'] as $term_group ) {
            if ( ! is_array( $term_group ) ) continue;
            foreach ( $term_group as $term ) {
                if ( ! is_array( $term ) ) continue;
                if ( isset( $term['taxonomy'] ) && 'column_category' === $term['taxonomy'] && '' === $cat_name ) {
                    $cat_name = (string) $term['name'];
                }
            }
        }
    }

    $data = array(
        'id'           => (int) ( $item['id'] ?? 0 ),
        'slug'         => (string) ( $item['slug'] ?? '' ),
        'title'        => wp_strip_all_tags( (string) ( $item['title']['rendered'] ?? '' ) ),
        'excerpt'      => wp_strip_all_tags( (string) ( $item['excerpt']['rendered'] ?? '' ) ),
        'content'      => (string) ( $item['content']['rendered'] ?? '' ),
        'link'         => esc_url_raw( (string) ( $item['link'] ?? '' ) ),
        'image_url'    => esc_url_raw( $image_url ),
        'category'     => $cat_name,
        'date'         => (string) ( $item['date'] ?? '' ),
        'modified'     => (string) ( $item['modified'] ?? '' ),
        'yumeho_lead'  => (string) ( $meta['_rinascente_yumeho_lead'] ?? '' ),
    );

    set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

    return $data;
}

function yumeho_home_field_note_fallback() {
    return array(
        'title'        => 'FIELD NOTE',
        'meta'         => 'PROPOSAL HIGHLIGHTS',
        'items'        => array(
            '天井直付型とスタンド型を環境別に提案',
            '体重免荷と追従制御で恐怖心を軽減',
            'ハーネスサイズを複数運用し準備を短縮',
            '計測オプションで訓練成果をチーム共有',
            '補助金申請に必要な資料作成もサポート',
        ),
        'url'          => home_url( '/simulation/' ),
        'external_url' => '',
        'link_label'   => '3分で概算を確認する',
        'source'       => 'fallback',
    );
}

function yumeho_home_field_note_normalize_point( $text ) {
    $text = html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' );
    $text = wp_strip_all_tags( $text );
    $text = preg_replace( '/\s+/u', ' ', $text );
    $text = preg_replace( '/^[\-\x{2022}●○・▪■□◆◇★☆]+\s*/u', '', (string) $text );
    $text = preg_replace( '/^\d+\s*[\.\)]\s*/u', '', (string) $text );
    $text = trim( (string) $text, " \t\n\r\0\x0B-・、。:：" );

    if ( '' === $text ) {
        return '';
    }

    if ( function_exists( 'mb_strimwidth' ) ) {
        $text = mb_strimwidth( $text, 0, 64, '…', 'UTF-8' );
    }

    return trim( $text );
}

function yumeho_home_field_note_extract_sentences( $text, $limit = 5 ) {
    $normalized = str_replace( array( "\r\n", "\r", '<br>', '<br/>', '<br />' ), array( "\n", "\n", "\n", "\n", "\n" ), (string) $text );
    $parts      = preg_split( '/(?:\n+|(?<=[。！？]))/u', wp_strip_all_tags( $normalized ) );
    $points     = array();

    foreach ( (array) $parts as $part ) {
        $point = yumeho_home_field_note_normalize_point( $part );
        if ( '' === $point ) {
            continue;
        }

        $points[] = $point;
        if ( count( $points ) >= (int) $limit ) {
            break;
        }
    }

    return $points;
}

function yumeho_home_field_note_extract_headings( $html, $limit = 5 ) {
    $matches = array();
    $points  = array();

    if ( ! preg_match_all( '/<(h2|h3)[^>]*>(.*?)<\/\1>/isu', (string) $html, $matches ) ) {
        return $points;
    }

    foreach ( $matches[2] as $heading_text ) {
        $point = yumeho_home_field_note_normalize_point( $heading_text );
        if ( '' === $point ) {
            continue;
        }

        $points[] = $point;
        if ( count( $points ) >= (int) $limit ) {
            break;
        }
    }

    return $points;
}

function yumeho_home_field_note_unique_points( array $points, $limit = 5 ) {
    $normalized_points = array();
    $seen              = array();

    foreach ( $points as $point ) {
        $clean = yumeho_home_field_note_normalize_point( $point );
        if ( '' === $clean ) {
            continue;
        }

        $key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $clean, 'UTF-8' ) : strtolower( $clean );
        if ( isset( $seen[ $key ] ) ) {
            continue;
        }

        $seen[ $key ]         = true;
        $normalized_points[]  = $clean;

        if ( count( $normalized_points ) >= (int) $limit ) {
            break;
        }
    }

    return $normalized_points;
}

function yumeho_build_home_field_note_points( array $column, $limit = 5 ) {
    $raw_points = array();

    if ( ! empty( $column['title'] ) ) {
        $raw_points[] = $column['title'];
    }

    $raw_points = array_merge( $raw_points, yumeho_home_field_note_extract_headings( $column['content'] ?? '', $limit ) );

    if ( count( $raw_points ) < (int) $limit && ! empty( $column['yumeho_lead'] ) ) {
        $raw_points = array_merge( $raw_points, yumeho_home_field_note_extract_sentences( $column['yumeho_lead'], $limit ) );
    }

    if ( count( $raw_points ) < (int) $limit && ! empty( $column['excerpt'] ) ) {
        $raw_points = array_merge( $raw_points, yumeho_home_field_note_extract_sentences( $column['excerpt'], $limit ) );
    }

    return yumeho_home_field_note_unique_points( $raw_points, $limit );
}

function yumeho_get_home_field_note_context( $columns = null ) {
    $fallback = yumeho_home_field_note_fallback();
    if ( ! is_array( $columns ) ) {
        $columns = yumeho_fetch_shared_columns( 1 );
    }

    if ( empty( $columns[0] ) || ! is_array( $columns[0] ) ) {
        return $fallback;
    }

    $column = $columns[0];
    $items  = yumeho_build_home_field_note_points( $column, 5 );

    if ( empty( $items ) ) {
        return $fallback;
    }

    $date_text = '';
    if ( ! empty( $column['date'] ) ) {
        $timestamp = strtotime( (string) $column['date'] );
        if ( $timestamp ) {
            $date_text = wp_date( 'Y.m.d', $timestamp );
        }
    }

    $meta_parts = array_filter(
        array(
            $date_text,
            trim( (string) ( $column['category'] ?? '' ) ),
        )
    );

    $url = $fallback['url'];
    if ( ! empty( $column['slug'] ) ) {
        $url = home_url( '/column/' . sanitize_title( (string) $column['slug'] ) . '/' );
    } elseif ( ! empty( $column['link'] ) ) {
        $url = esc_url_raw( (string) $column['link'] );
    }

    return array(
        'title'        => 'FIELD NOTE',
        'meta'         => ! empty( $meta_parts ) ? implode( ' / ', $meta_parts ) : 'LATEST COLUMN',
        'items'        => $items,
        'url'          => $url,
        'external_url' => esc_url_raw( (string) ( $column['link'] ?? '' ) ),
        'link_label'   => '最新コラムを読む',
        'source'       => 'column',
    );
}

/**
 * /column/ および /column/{slug}/ のリライトルールを追加
 */
add_action( 'init', 'yumeho_register_column_rewrite' );
function yumeho_register_column_rewrite() {
    add_rewrite_rule(
        '^column/?$',
        'index.php?yumeho_column_archive=1',
        'top'
    );
    add_rewrite_rule(
        '^column/page/([0-9]+)/?$',
        'index.php?yumeho_column_archive=1&yumeho_column_paged=$matches[1]',
        'top'
    );
    add_rewrite_rule(
        '^column/([^/]+)/?$',
        'index.php?yumeho_column_slug=$matches[1]',
        'top'
    );
}

add_filter( 'query_vars', 'yumeho_register_column_query_vars' );
function yumeho_register_column_query_vars( $vars ) {
    $vars[] = 'yumeho_column_archive';
    $vars[] = 'yumeho_column_paged';
    $vars[] = 'yumeho_column_slug';
    return $vars;
}

// テンプレート読み込み
add_filter( 'template_include', 'yumeho_column_template_include' );
function yumeho_column_template_include( $template ) {
    if ( get_query_var( 'yumeho_column_archive' ) ) {
        $custom = locate_template( 'yumeho-column-archive.php' );
        if ( $custom ) return $custom;
    }
    if ( get_query_var( 'yumeho_column_slug' ) ) {
        $custom = locate_template( 'yumeho-column-single.php' );
        if ( $custom ) return $custom;
    }
    return $template;
}

/**
 * Return a shared company/support value first, then fall back to
 * prefixed (`yumeho_*`) and legacy unprefixed theme mods.
 */
function yumeho_theme_mod( $key, $default = '' ) {
    $missing = '__yumeho_missing__';
    $is_shared_key = str_starts_with( $key, 'company_' ) || str_starts_with( $key, 'support_' );

    if ( $is_shared_key && function_exists( 'yumeho_shared_company_data' ) ) {
        $shared = yumeho_shared_company_data();
        if ( isset( $shared[ $key ] ) && '' !== $shared[ $key ] ) {
            return $shared[ $key ];
        }
    }

    $value = get_theme_mod( 'yumeho_' . $key, $missing );
    if ( $missing !== $value && '' !== $value ) {
        return $value;
    }

    $legacy = get_theme_mod( $key, $missing );
    if ( $missing !== $legacy && '' !== $legacy ) {
        return $legacy;
    }

    return $default;
}

function yumeho_prepare_legal_page_content( $content ) {
    $company_name = yumeho_theme_mod( 'company_name', '株式会社Rinascente' );
    $company_ceo  = yumeho_theme_mod( 'company_ceo', '代表取締役 ○○○○' );
    $company_tel  = yumeho_theme_mod( 'company_tel', '0859-00-1234' );
    $company_addr = yumeho_theme_mod( 'company_address', '' );
    $company_time = yumeho_theme_mod( 'company_hours', '平日 9:00〜17:00' );

    $replacements = array(
        '{{company_name}}'                  => esc_html( $company_name ),
        '{{company_ceo}}'                   => esc_html( $company_ceo ),
        '{{company_tel}}'                   => esc_html( $company_tel ),
        '{{company_address}}'               => esc_html( $company_addr ),
        '{{company_hours}}'                 => esc_html( $company_time ),
        '株式会社Rinascente'                => esc_html( $company_name ),
        '株式会社 Rinascente（リナシェンテ）' => esc_html( $company_name ),
        '0859-00-1234'                      => esc_html( $company_tel ),
        '平日 9:00〜17:00'                     => esc_html( $company_time ),
    );

    $content = strtr( (string) $content, $replacements );

    // Commercial-law pages often contain a placeholder paragraph that only says "代表取締役".
    // Replace that exact paragraph with the configured representative name.
    $content = preg_replace(
        '/<p>\s*代表取締役\s*<\/p>/u',
        '<p>' . esc_html( $company_ceo ) . '</p>',
        $content,
        1
    );

    return $content;
}

function yumeho_register_facility_member_role() {
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
add_action( 'init', 'yumeho_register_facility_member_role' );

function yumeho_member_page_url() {
    return yumeho_shared_member_site_url( '/member/', array( 'product' => 'yumeho' ) );
}

function yumeho_shared_member_site_url( $path = '/', $args = array() ) {
    $base_url = yumeho_related_site_url( 'corporate', $path );

    if ( ! empty( $args ) ) {
        $base_url = add_query_arg( $args, $base_url );
    }

    return $base_url;
}

function yumeho_member_login_url( $redirect_to = '' ) {
    $login_url = yumeho_shared_member_site_url( '/login/', array( 'product' => 'yumeho' ) );

    if ( $redirect_to ) {
        $login_url = add_query_arg( 'redirect_to', rawurlencode( $redirect_to ), $login_url );
    }

    return $login_url;
}

function yumeho_member_forgot_password_url() {
    return yumeho_shared_member_site_url( '/forgot-password/', array( 'product' => 'yumeho' ) );
}

function yumeho_member_reset_password_url() {
    return yumeho_shared_member_site_url( '/reset-password/', array( 'product' => 'yumeho' ) );
}

function yumeho_resolve_login_identifier( $identifier ) {
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

function yumeho_member_user_name( $user ) {
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

function yumeho_member_user_initial( $user ) {
    $name = yumeho_member_user_name( $user );
    if ( '' === $name ) {
        return 'M';
    }

    if ( function_exists( 'mb_substr' ) ) {
        $initial = mb_substr( $name, 0, 1, 'UTF-8' );
        return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $initial, 'UTF-8' ) : $initial;
    }

    return strtoupper( substr( $name, 0, 1 ) );
}

function yumeho_member_user_role_label( $user ) {
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

function yumeho_is_facility_member( $user = null ) {
    $candidate = $user instanceof WP_User ? $user : wp_get_current_user();
    if ( ! ( $candidate instanceof WP_User ) || ! $candidate->exists() ) {
        return false;
    }

    return in_array( 'facility_member', (array) $candidate->roles, true );
}

function yumeho_is_local_environment() {
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

function yumeho_restrict_member_admin_area() {
    if ( ! is_admin() || wp_doing_ajax() || current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( yumeho_is_facility_member() ) {
        wp_redirect( yumeho_member_page_url() );
        exit;
    }
}
add_action( 'admin_init', 'yumeho_restrict_member_admin_area' );

/* ==========================================================================
   1. Theme Setup
   ========================================================================== */

function yumeho_setup() {
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
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    register_nav_menus( array(
        'header-nav'     => 'Header Navigation',
        'sp-drawer-nav'  => 'SP Drawer Navigation',
        'footer-nav'     => 'Footer Navigation',
    ) );
}
add_action( 'after_setup_theme', 'yumeho_setup' );

/* ==========================================================================
   2. Enqueue Styles & Scripts
   ========================================================================== */

function yumeho_enqueue_assets() {
    // TypeKit
    wp_enqueue_style(
        'yumeho-typekit',
        'https://use.typekit.net/uor0jvw.css',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style(
        'yumeho-style',
        YUMEHO_URI . '/assets/css/style.css',
        array( 'yumeho-typekit' ),
        yumeho_asset_version( 'assets/css/style.css' )
    );

    // Site switcher
    wp_enqueue_style(
        'yumeho-site-switcher',
        YUMEHO_URI . '/assets/css/site-switcher.css',
        array( 'yumeho-style' ),
        yumeho_asset_version( 'assets/css/site-switcher.css' )
    );

    // Main JS
    wp_enqueue_script(
        'yumeho-main',
        YUMEHO_URI . '/assets/js/main.js',
        array(),
        yumeho_asset_version( 'assets/js/main.js' ),
        true
    );
    wp_script_add_data( 'yumeho-main', 'strategy', 'defer' );

    // Interactive.js (scroll animations, micro-interactions) — 全ページ
    wp_enqueue_script(
        'yumeho-interactive',
        YUMEHO_URI . '/assets/js/interactive.js',
        array(),
        yumeho_asset_version( 'assets/js/interactive.js' ),
        true
    );
    wp_script_add_data( 'yumeho-interactive', 'strategy', 'defer' );

    // Background effect JS (homepage only)
    if ( is_front_page() ) {
        wp_enqueue_script(
            'yumeho-bg-effect',
            YUMEHO_URI . '/assets/js/background-effect.js',
            array(),
            yumeho_asset_version( 'assets/js/background-effect.js' ),
            true
        );
        wp_script_add_data( 'yumeho-bg-effect', 'strategy', 'defer' );
    }

    // Pricing JS (simulation page only)
    if ( is_page_template( 'page-simulation.php' ) ) {
        wp_enqueue_script(
            'yumeho-pricing',
            YUMEHO_URI . '/assets/js/pricing.js',
            array(),
            yumeho_asset_version( 'assets/js/pricing.js' ),
            true
        );
        wp_script_add_data( 'yumeho-pricing', 'strategy', 'defer' );

        wp_localize_script( 'yumeho-pricing', 'yumehoPricing', yumeho_pricing_catalog_config() );
    }
}
add_action( 'wp_enqueue_scripts', 'yumeho_enqueue_assets' );

function yumeho_resource_hints( $urls, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $urls[] = 'https://use.typekit.net';
        $urls[] = 'https://p.typekit.net';
    }

    if ( 'dns-prefetch' === $relation_type ) {
        $urls[] = 'https://use.typekit.net';
        $urls[] = 'https://p.typekit.net';
    }

    return $urls;
}
add_filter( 'wp_resource_hints', 'yumeho_resource_hints', 10, 2 );

function yumeho_preload_front_page_assets() {
    if ( ! is_front_page() ) {
        return;
    }

    $relative_path  = file_exists( get_template_directory() . '/assets/img/hero_visual.webp' )
        ? 'assets/img/hero_visual.webp'
        : 'assets/img/hero_visual.jpg';
    $hero_image_url = YUMEHO_URI . '/' . $relative_path;
    $hero_image_url = add_query_arg(
        'ver',
        rawurlencode( yumeho_asset_version( $relative_path ) ),
        $hero_image_url
    );

    $type_attr = str_ends_with( $relative_path, '.webp' ) ? ' type="image/webp"' : '';

    echo '<link rel="preload" as="image" href="' . esc_url( $hero_image_url ) . '"' . $type_attr . '>' . "\n";
}
add_action( 'wp_head', 'yumeho_preload_front_page_assets', 1 );

/* ==========================================================================
   3. Custom Post Types
   ========================================================================== */

function yumeho_register_post_types() {
    // Case Study
    register_post_type( 'case_study', array(
        'labels' => array(
            'name'               => '導入事例',
            'singular_name'      => '導入事例',
            'add_new'            => '新規追加',
            'add_new_item'       => '導入事例を追加',
            'edit_item'          => '導入事例を編集',
            'new_item'           => '新しい導入事例',
            'view_item'          => '導入事例を表示',
            'search_items'       => '導入事例を検索',
            'not_found'          => '導入事例が見つかりません',
            'not_found_in_trash' => 'ゴミ箱に導入事例はありません',
        ),
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => array( 'slug' => 'cases' ),
        'menu_icon'    => 'dashicons-building',
        'supports'     => array( 'title', 'custom-fields' ), // custom-fields は REST API でメタを返すために必要
        'show_in_menu' => yumeho_site_admin_menu_slug(),
        'show_in_rest' => true, // REST API 公開（Rinascente からの取得用）
    ) );

    // FAQ
    register_post_type( 'faq', array(
        'labels' => array(
            'name'               => 'FAQ',
            'singular_name'      => 'FAQ',
            'add_new'            => '新規追加',
            'add_new_item'       => 'FAQを追加',
            'edit_item'          => 'FAQを編集',
            'new_item'           => '新しいFAQ',
            'view_item'          => 'FAQを表示',
            'search_items'       => 'FAQを検索',
            'not_found'          => 'FAQが見つかりません',
            'not_found_in_trash' => 'ゴミ箱にFAQはありません',
        ),
        'public'              => true,
        'publicly_queryable'  => false,
        'has_archive'         => false,
        'rewrite'             => array( 'slug' => 'faq' ),
        'menu_icon'           => 'dashicons-editor-help',
        'supports'            => array( 'title', 'editor', 'page-attributes' ),
        'show_in_menu'        => yumeho_site_admin_menu_slug(),
        'show_in_rest'        => true,
    ) );
}
add_action( 'init', 'yumeho_register_post_types' );

/* ==========================================================================
   4. Custom Taxonomies
   ========================================================================== */

function yumeho_register_taxonomies() {
    // Facility Type (for case_study)
    register_taxonomy( 'facility_type', 'case_study', array(
        'labels' => array(
            'name'          => '施設種別',
            'singular_name' => '施設種別',
            'add_new_item'  => '施設種別を追加',
            'search_items'  => '施設種別を検索',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'facility-type' ),
        'show_in_rest' => true,
    ) );

    // FAQ Category (for faq)
    register_taxonomy( 'faq_category', 'faq', array(
        'labels' => array(
            'name'          => 'FAQカテゴリ',
            'singular_name' => 'FAQカテゴリ',
            'add_new_item'  => 'FAQカテゴリを追加',
            'search_items'  => 'FAQカテゴリを検索',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'faq-category' ),
        'show_in_rest' => true,
    ) );

    // Product Type (shared)
    register_taxonomy( 'product_type', array( 'case_study', 'faq' ), array(
        'labels' => array(
            'name'          => '製品タイプ',
            'singular_name' => '製品タイプ',
            'add_new_item'  => '製品タイプを追加',
            'search_items'  => '製品タイプを検索',
        ),
        'public'       => true,
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'product-type' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'yumeho_register_taxonomies' );

/* ==========================================================================
   5. Case Study Meta Box
   ========================================================================== */

// case_study は Classic Editor を使用（REST API は有効のまま）
add_filter( 'use_block_editor_for_post_type', 'yumeho_case_disable_gutenberg', 10, 2 );
function yumeho_case_disable_gutenberg( $use_block_editor, $post_type ) {
    if ( in_array( $post_type, array( 'case_study', 'faq' ), true ) ) {
        return false;
    }
    return $use_block_editor;
}

function yumeho_case_product_model_options() {
    $catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
    $options         = array();

    foreach ( $catalog_context['systems'] as $item ) {
        if ( '' === $item['display_name'] ) {
            continue;
        }

        $options[] = $item['display_name'];
    }

    if ( empty( $options ) ) {
        return array(
            '天井直付型 FCW-3000',
            'スタンド型 PGT-9000',
            'スタンド型 PGT-9001',
        );
    }

    return array_values( array_unique( $options ) );
}

function yumeho_case_generate_title( $facility_name = '', $product_model = '', $location = '' ) {
    $facility_name = trim( wp_strip_all_tags( (string) $facility_name ) );
    $product_model = trim( wp_strip_all_tags( (string) $product_model ) );
    $location      = trim( wp_strip_all_tags( (string) $location ) );

    if ( '' !== $facility_name && '' !== $product_model ) {
        return $facility_name . ' / ' . $product_model;
    }

    if ( '' !== $facility_name ) {
        return $facility_name;
    }

    if ( '' !== $product_model && '' !== $location ) {
        return $location . ' / ' . $product_model;
    }

    if ( '' !== $product_model ) {
        return $product_model;
    }

    if ( '' !== $location ) {
        return $location . ' の導入事例';
    }

    return '新しい導入事例';
}

function yumeho_case_format_rich_text( $content ) {
    $content = trim( (string) $content );
    if ( '' === $content ) {
        return '';
    }

    $content = wp_kses_post( $content );

    if ( ! preg_match( '/<(?:p|ul|ol|li|blockquote|h[1-6]|div|br)\b/i', $content ) ) {
        $content = wpautop( $content );
    }

    return $content;
}

function yumeho_case_plain_summary( $content ) {
    $content = str_replace(
        array( '<br>', '<br/>', '<br />', '</p>', '</li>' ),
        ' ',
        (string) $content
    );
    $content = wp_strip_all_tags( $content, true );
    $content = preg_replace( '/\s+/u', ' ', $content );

    return trim( (string) $content );
}

function yumeho_case_editor_field( $field_name, $value, $args = array() ) {
    $defaults  = array(
        'rows'        => 4,
        'description' => '',
    );
    $args      = wp_parse_args( $args, $defaults );
    $editor_id = 'yumeho_case_' . $field_name . '_editor';

    wp_editor(
        $value,
        $editor_id,
        array(
            'textarea_name' => 'yumeho_case_' . $field_name,
            'textarea_rows' => (int) $args['rows'],
            'media_buttons' => false,
            'teeny'         => true,
            'quicktags'     => true,
            'tinymce'       => array(
                'toolbar1' => 'bold,italic,bullist,numlist,link,undo,redo',
                'toolbar2' => '',
            ),
        )
    );

    if ( '' !== trim( (string) $args['description'] ) ) {
        echo '<span class="description">' . esc_html( $args['description'] ) . '</span>';
    }
}

function yumeho_case_meta_box() {
    add_meta_box(
        'yumeho_case_unified',
        '導入事例 入力フォーム',
        'yumeho_case_unified_html',
        'case_study',
        'normal',
        'high'
    );

}

// 他のメタボックスを全て削除（サイドバー & normal）
add_action( 'admin_menu', 'yumeho_remove_case_meta_boxes' );
function yumeho_remove_case_meta_boxes() {
    // サイドバー
    remove_meta_box( 'facility_typediv', 'case_study', 'side' );
    remove_meta_box( 'product_typediv', 'case_study', 'side' );
    remove_meta_box( 'case_formatdiv', 'case_study', 'side' );
    remove_meta_box( 'tagsdiv-facility_type', 'case_study', 'side' );
    remove_meta_box( 'tagsdiv-product_type', 'case_study', 'side' );
    remove_meta_box( 'tagsdiv-case_format', 'case_study', 'side' );
    remove_meta_box( 'postimagediv', 'case_study', 'side' );
    remove_meta_box( 'pageparentdiv', 'case_study', 'side' );
    remove_meta_box( 'slugdiv', 'case_study', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'case_study', 'normal' );
    remove_meta_box( 'commentsdiv', 'case_study', 'normal' );
    remove_meta_box( 'authordiv', 'case_study', 'normal' );
    remove_meta_box( 'postcustom', 'case_study', 'normal' );
    remove_meta_box( 'postexcerpt', 'case_study', 'normal' );
    remove_meta_box( 'trackbacksdiv', 'case_study', 'normal' );
    remove_meta_box( 'revisionsdiv', 'case_study', 'normal' );
}
add_action( 'add_meta_boxes', 'yumeho_case_meta_box', 20 );

// メタボックスを常に開く + カラム数を固定
add_action( 'admin_head-post.php', 'yumeho_case_admin_css' );
add_action( 'admin_head-post-new.php', 'yumeho_case_admin_css' );
function yumeho_case_admin_css() {
    global $post;
    if ( ! $post || 'case_study' !== $post->post_type ) return;
    ?>
    <style>
    .post-type-case_study #titlediv,
    .post-type-case_study #post-body-content .wp-editor-wrap,
    .post-type-case_study #post-body-content > #postdivrich,
    .post-type-case_study #post-body-content > .wp-editor-tools {
        display: none;
    }
    .post-type-case_study #post-body-content {
        margin-bottom: 0;
        min-height: 0;
    }
    /* メタボックスを常時開く（開閉ボタン非表示・コンテンツ強制表示） */
    .post-type-case_study #yumeho_case_unified .handlediv,
    .post-type-case_study #yumeho_case_unified .postbox-header .handle-actions { display:none; }
    .post-type-case_study #yumeho_case_unified.closed .inside { display:block !important; }
    .post-type-case_study #yumeho_case_unified .postbox-header { cursor: default; }

    /* 見た目 */
    .yumeho-case-form { display:grid; gap:28px; }
    .yumeho-case-summary {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(260px, 1fr);
        gap: 18px;
        align-items: start;
        border: 1px solid #cfe4f6;
        border-radius: 10px;
        background: linear-gradient(180deg, #f8fcff 0%, #eef7ff 100%);
        padding: 20px 22px;
    }
    .yumeho-case-summary__eyebrow {
        display: inline-block;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: #0068b7;
    }
    .yumeho-case-summary__progress {
        font-size: 20px;
        font-weight: 700;
        line-height: 1.3;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .yumeho-case-summary__title {
        font-size: 13px;
        line-height: 1.7;
        color: #334155;
        margin-bottom: 10px;
    }
    .yumeho-case-summary__actions {
        display: grid;
        gap: 10px;
        align-content: start;
    }
    .yumeho-case-summary__actions .button {
        justify-content: center;
        text-align: center;
    }
    .yumeho-case-checklist {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .yumeho-case-check {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.04);
        color: #475569;
        font-size: 12px;
        font-weight: 600;
    }
    .yumeho-case-check::before {
        content: " ";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #cbd5e1;
        flex: 0 0 auto;
    }
    .yumeho-case-check--done {
        background: rgba(0, 104, 183, 0.1);
        color: #00538f;
    }
    .yumeho-case-check--done::before {
        background: #0068b7;
    }
    .yumeho-case-section {
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 20px 24px;
        background: #fafafa;
    }
    .yumeho-case-section h3 {
        margin: 0 0 16px;
        padding-bottom: 10px;
        border-bottom: 2px solid #0068b7;
        font-size: 14px;
        color: #0068b7;
        font-weight: 700;
        letter-spacing: 0.04em;
    }
    .yumeho-case-row { margin-bottom: 16px; }
    .yumeho-case-row:last-child { margin-bottom: 0; }
    .yumeho-case-row label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 13px;
    }
    .yumeho-case-row input[type="text"],
    .yumeho-case-row textarea,
    .yumeho-case-row select { width: 100%; }
    .yumeho-case-row .wp-editor-wrap {
        background: #fff;
        border: 1px solid #d0d7de;
        border-radius: 6px;
        overflow: hidden;
    }
    .yumeho-case-row .wp-editor-tools {
        padding: 0 8px;
        background: #fff;
    }
    .yumeho-case-row .mce-top-part::before,
    .yumeho-case-row .quicktags-toolbar {
        border-bottom-color: #e5e7eb;
    }
    .yumeho-case-row .description {
        color: #666;
        font-size: 12px;
        display: block;
        margin-top: 4px;
    }
    .yumeho-case-inline-note {
        margin-top: 8px;
        padding: 10px 12px;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #d9e8f5;
        color: #334155;
        font-size: 12px;
    }
    .yumeho-case-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .yumeho-case-metric {
        border: 1px solid #ddd;
        background: #fff;
        padding: 14px;
        border-radius: 4px;
        margin-bottom: 12px;
    }
    .yumeho-case-metric legend {
        font-weight: 600;
        font-size: 12px;
        padding: 0 6px;
        color: #0068b7;
    }
    .yumeho-image-box {
        border: 2px dashed #ccc;
        background: #fff;
        padding: 20px;
        text-align: center;
        border-radius: 6px;
    }
    .yumeho-image-box img {
        max-width: 320px;
        max-height: 180px;
        display: block;
        margin: 0 auto 12px;
        border-radius: 4px;
    }
    .yumeho-case-checkbox {
        display: block;
        padding: 10px 14px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 8px;
        cursor: pointer;
    }
    .yumeho-case-checkbox input { margin-right: 8px; }
    @media (max-width: 1024px) {
        .yumeho-case-summary,
        .yumeho-case-grid2 {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

function yumeho_case_field_value( $post_id, $key ) {
    return get_post_meta( $post_id, '_yumeho_case_' . $key, true );
}

/* === 統合メタボックスのHTML出力 === */
function yumeho_case_unified_html( $post ) {
    wp_nonce_field( 'yumeho_case_meta_save', 'yumeho_case_meta_nonce' );
    wp_enqueue_media(); // メディアアップローダー

    $current_title = 'auto-draft' === $post->post_status ? '' : get_the_title( $post );
    $facility_name  = yumeho_case_field_value( $post->ID, 'facility_name' );
    $install_date   = yumeho_case_field_value( $post->ID, 'install_date' );
    $location       = yumeho_case_field_value( $post->ID, 'location' );
    $product_model  = yumeho_case_field_value( $post->ID, 'product_model' );
    $image_fit      = yumeho_case_field_value( $post->ID, 'image_fit' );
    $challenge      = yumeho_case_field_value( $post->ID, 'challenge' );
    $reason         = yumeho_case_field_value( $post->ID, 'reason' );
    $change_txt     = yumeho_case_field_value( $post->ID, 'change' );
    $ringi          = yumeho_case_field_value( $post->ID, 'ringi_process' );
    $quote          = yumeho_case_field_value( $post->ID, 'pullquote' );
    $speaker        = yumeho_case_field_value( $post->ID, 'pullquote_speaker' );
    $is_featured    = yumeho_case_field_value( $post->ID, 'is_featured' );
    $is_hidden      = yumeho_case_field_value( $post->ID, 'is_hidden' );

    // メイン画像
    $thumb_id  = (int) get_post_thumbnail_id( $post->ID );
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium' ) : '';

    // 施設種別（taxonomy）
    $facility_terms = wp_get_post_terms( $post->ID, 'facility_type', array( 'fields' => 'ids' ) );
    $selected_facility_term = ! empty( $facility_terms ) ? (int) $facility_terms[0] : 0;
    $all_facility_terms = get_terms( array( 'taxonomy' => 'facility_type', 'hide_empty' => false ) );
    $title_fallback = yumeho_case_generate_title( $facility_name, $product_model, $location );
    $display_title  = '' !== trim( $current_title ) ? $current_title : $title_fallback;
    $required_checks = array(
        '施設名'     => '' !== trim( (string) $facility_name ),
        '機種'       => '' !== trim( (string) $product_model ),
        '施設種別'   => $selected_facility_term > 0,
        '導入年月'   => '' !== trim( (string) $install_date ),
        'メイン画像' => $thumb_id > 0,
        '課題'       => '' !== trim( wp_strip_all_tags( (string) $challenge ) ),
        '決め手'     => '' !== trim( wp_strip_all_tags( (string) $reason ) ),
        '変化'       => '' !== trim( wp_strip_all_tags( (string) $change_txt ) ),
    );
    $completed_count = count( array_filter( $required_checks ) );
    $duplicate_url   = $post->ID
        ? wp_nonce_url(
            admin_url( 'admin.php?action=yumeho_duplicate_case_study&post=' . $post->ID ),
            'yumeho_duplicate_case_study_' . $post->ID
        )
        : '';
    ?>
    <div class="yumeho-case-form">

        <div class="yumeho-case-summary">
            <div>
                <span class="yumeho-case-summary__eyebrow">公開チェック</span>
                <div class="yumeho-case-summary__progress"><span id="yumeho_case_progress"><?php echo esc_html( $completed_count . ' / ' . count( $required_checks ) ); ?></span> 項目入力済み</div>
                <div class="yumeho-case-summary__title">
                    現在の見出し:
                    <strong id="yumeho_case_title_preview"><?php echo esc_html( $display_title ); ?></strong>
                </div>
                <p class="description" style="margin:0 0 12px;">上から順に入れていけば公開に必要な内容が揃います。迷ったら、まず「基本情報」「メイン画像」「課題・決め手・変化」まで入れてください。</p>
                <div class="yumeho-case-checklist">
                    <?php foreach ( $required_checks as $label => $is_done ) : ?>
                    <span class="yumeho-case-check<?php echo $is_done ? ' yumeho-case-check--done' : ''; ?>" data-check-target="<?php echo esc_attr( $label ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="yumeho-case-summary__actions">
                <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=case_study' ) ); ?>" class="button">一覧に戻る</a>
                <?php if ( 'auto-draft' !== $post->post_status && $post->ID ) : ?>
                <a href="<?php echo esc_url( get_preview_post_link( $post ) ); ?>" class="button button-secondary" target="_blank" rel="noopener noreferrer">プレビュー</a>
                <?php endif; ?>
                <?php if ( 'publish' === $post->post_status && $post->ID ) : ?>
                <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="button button-secondary" target="_blank" rel="noopener noreferrer">公開ページを見る</a>
                <?php endif; ?>
                <?php if ( $duplicate_url ) : ?>
                <a href="<?php echo esc_url( $duplicate_url ); ?>" class="button button-primary">この事例を複製して下書きを作る</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- 施設情報 -->
        <div class="yumeho-case-section">
            <h3>① 基本情報</h3>

            <div class="yumeho-case-row">
                <label>事例タイトル</label>
                <input type="text" name="yumeho_case_editor_title" id="yumeho_case_editor_title" value="<?php echo esc_attr( $current_title ); ?>" placeholder="未入力なら自動生成">
                <span class="description">一覧・詳細ページの見出しです。空欄のときは施設名と機種から自動生成します。</span>
                <div class="yumeho-case-inline-note">自動タイトル候補: <strong id="yumeho_case_title_hint"><?php echo esc_html( $title_fallback ); ?></strong></div>
            </div>

            <div class="yumeho-case-row">
                <label>施設名</label>
                <input type="text" name="yumeho_case_facility_name" id="yumeho_case_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" placeholder="例: A総合病院 リハビリテーション科 様">
            </div>

            <div class="yumeho-case-grid2">
                <div class="yumeho-case-row">
                    <label>導入年月</label>
                    <input type="text" name="yumeho_case_install_date" id="yumeho_case_install_date" value="<?php echo esc_attr( $install_date ); ?>" placeholder="例: 2024年4月導入">
                </div>
                <div class="yumeho-case-row">
                    <label>所在地</label>
                    <input type="text" name="yumeho_case_location" id="yumeho_case_location" value="<?php echo esc_attr( $location ); ?>" placeholder="例: 東京都">
                </div>
            </div>

            <div class="yumeho-case-grid2">
                <div class="yumeho-case-row">
                    <label>対象製品</label>
                    <select name="yumeho_case_product_model" id="yumeho_case_product_model">
                        <option value="">— 選択してください —</option>
                        <?php foreach ( yumeho_case_product_model_options() as $opt ) : ?>
                        <option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $product_model, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="yumeho-case-row">
                    <label>施設種別</label>
                    <select name="yumeho_case_facility_term" id="yumeho_case_facility_term">
                        <option value="">— 選択してください —</option>
                        <?php foreach ( $all_facility_terms as $term ) : ?>
                        <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_facility_term, (int) $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="description">タブ切替で使用されます</span>
                </div>
            </div>
        </div>

        <!-- メイン画像 -->
        <div class="yumeho-case-section">
            <h3>② メイン画像</h3>
            <div class="yumeho-image-box">
                <div id="yumeho_case_image_preview">
                    <?php if ( $thumb_url ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
                    <?php else : ?>
                        <p style="color:#999;margin:0 0 12px;">画像が選択されていません</p>
                    <?php endif; ?>
                </div>
                <input type="hidden" name="yumeho_case_thumb_id" id="yumeho_case_thumb_id" value="<?php echo esc_attr( $thumb_id ); ?>">
                <button type="button" class="button button-primary" id="yumeho_case_image_select">画像を選択</button>
                <button type="button" class="button" id="yumeho_case_image_remove" <?php if ( ! $thumb_id ) echo 'style="display:none;"'; ?>>画像を削除</button>
            </div>
            <div class="yumeho-case-row" style="margin-top:12px;">
                <label>画像の表示方法</label>
                <select name="yumeho_case_image_fit">
                    <option value="cover (標準・全面)" <?php selected( $image_fit, 'cover (標準・全面)' ); ?>>cover (標準・全面)</option>
                    <option value="contain (背景白・余白あり)" <?php selected( $image_fit, 'contain (背景白・余白あり)' ); ?>>contain (背景白・余白あり)</option>
                </select>
            </div>
            <script>
            (function($){
                var frame;
                $('#yumeho_case_image_select').on('click', function(e){
                    e.preventDefault();
                    if (frame) { frame.open(); return; }
                    frame = wp.media({
                        title: '画像を選択',
                        button: { text: 'この画像を使う' },
                        library: { type: 'image' },
                        multiple: false
                    });
                    frame.on('select', function(){
                        var att = frame.state().get('selection').first().toJSON();
                        $('#yumeho_case_thumb_id').val(att.id);
                        $('#yumeho_case_image_preview').html('<img src="' + (att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url) + '" alt="">');
                        $('#yumeho_case_image_remove').show();
                    });
                    frame.open();
                });
                $('#yumeho_case_image_remove').on('click', function(e){
                    e.preventDefault();
                    $('#yumeho_case_thumb_id').val('');
                    $('#yumeho_case_image_preview').html('<p style="color:#999;margin:0 0 12px;">画像が選択されていません</p>');
                    $(this).hide();
                });
            })(jQuery);
            </script>
        </div>

        <!-- 課題・決め手・変化 -->
        <div class="yumeho-case-section">
            <h3>③ 課題・決め手・変化</h3>
            <div class="yumeho-case-row">
                <label>課題（Before）</label>
                <?php yumeho_case_editor_field( 'challenge', $challenge, array( 'rows' => 4, 'description' => 'Enter で改行すると、そのまま公開ページに反映されます。' ) ); ?>
            </div>
            <div class="yumeho-case-row">
                <label>決め手</label>
                <?php yumeho_case_editor_field( 'reason', $reason, array( 'rows' => 4, 'description' => '箇条書きもそのまま反映できます。' ) ); ?>
            </div>
            <div class="yumeho-case-row">
                <label>変化（After）</label>
                <?php yumeho_case_editor_field( 'change', $change_txt, array( 'rows' => 4, 'description' => '段落や改行を WYSIWYG の見たままで登録できます。' ) ); ?>
            </div>
        </div>

        <!-- 数値で見る変化 -->
        <div class="yumeho-case-section">
            <h3>④ 数値で見る変化</h3>
            <span class="description" style="display:block;margin-bottom:12px;">3つまで登録可能。値は WYSIWYG で改行・箇条書き入力できます。</span>
            <?php for ( $i = 1; $i <= 3; $i++ ) :
                $label_val = yumeho_case_field_value( $post->ID, 'metric_' . $i . '_label' );
                $value_val = yumeho_case_field_value( $post->ID, 'metric_' . $i . '_value' );
            ?>
            <fieldset class="yumeho-case-metric">
                <legend>成果 <?php echo $i; ?></legend>
                <div class="yumeho-case-grid2">
                    <div class="yumeho-case-row">
                        <label>ラベル</label>
                        <input type="text" name="yumeho_case_metric_<?php echo $i; ?>_label" value="<?php echo esc_attr( $label_val ); ?>" placeholder="例: 見守り体制">
                    </div>
                    <div class="yumeho-case-row">
                        <label>値</label>
                        <?php yumeho_case_editor_field( 'metric_' . $i . '_value', $value_val, array( 'rows' => 3, 'description' => '例: 3名 → 1名。改行や箇条書きも使えます。' ) ); ?>
                    </div>
                </div>
            </fieldset>
            <?php endfor; ?>
        </div>

        <!-- 経緯・声 -->
        <div class="yumeho-case-section">
            <h3>⑤ 経緯・お客様の声</h3>
            <div class="yumeho-case-row">
                <label>導入決定までの経緯</label>
                <?php yumeho_case_editor_field( 'ringi_process', $ringi, array( 'rows' => 5, 'description' => '時系列で改行しながら入力すると、そのまま読みやすく表示されます。' ) ); ?>
            </div>
            <div class="yumeho-case-row">
                <label>お客様の声</label>
                <?php yumeho_case_editor_field( 'pullquote', $quote, array( 'rows' => 3, 'description' => '短い引用も改行込みで整えられます。' ) ); ?>
            </div>
            <div class="yumeho-case-row">
                <label>発言者</label>
                <input type="text" name="yumeho_case_pullquote_speaker" value="<?php echo esc_attr( $speaker ); ?>" placeholder="例: 患者様の声 / 院長">
            </div>
        </div>

        <!-- 表示設定 -->
        <div class="yumeho-case-section">
            <h3>⑥ 表示設定</h3>
            <label class="yumeho-case-checkbox">
                <input type="checkbox" name="yumeho_case_is_featured" value="1" <?php checked( $is_featured, '1' ); ?>>
                <strong>おすすめ事例として出す</strong>
                <span class="description" style="margin-left:24px;">トップページや注目枠に優先表示します。</span>
            </label>
            <label class="yumeho-case-checkbox">
                <input type="checkbox" name="yumeho_case_is_hidden" value="1" <?php checked( $is_hidden, '1' ); ?>>
                <strong>一覧には出さない</strong>
                <span class="description" style="margin-left:24px;">詳細URLは残したまま、一覧ページと注目枠から外します。</span>
            </label>
        </div>

    </div>
    <?php
}

function yumeho_save_case_meta( $post_id ) {
    if ( ! isset( $_POST['yumeho_case_meta_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['yumeho_case_meta_nonce'], 'yumeho_case_meta_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    $text_fields = array(
        'facility_name', 'install_date', 'location',
        'product_model', 'image_fit',
        'pullquote_speaker',
        'metric_1_label', 'metric_2_label', 'metric_3_label',
    );
    foreach ( $text_fields as $key ) {
        if ( isset( $_POST[ 'yumeho_case_' . $key ] ) ) {
            update_post_meta( $post_id, '_yumeho_case_' . $key, sanitize_text_field( wp_unslash( $_POST[ 'yumeho_case_' . $key ] ) ) );
        }
    }

    $textarea_fields = array(
        'challenge', 'reason', 'change',
        'ringi_process', 'pullquote',
        'metric_1_value', 'metric_2_value', 'metric_3_value',
    );
    foreach ( $textarea_fields as $key ) {
        if ( isset( $_POST[ 'yumeho_case_' . $key ] ) ) {
            update_post_meta( $post_id, '_yumeho_case_' . $key, wp_kses_post( wp_unslash( $_POST[ 'yumeho_case_' . $key ] ) ) );
        }
    }

    update_post_meta( $post_id, '_yumeho_case_is_featured', isset( $_POST['yumeho_case_is_featured'] ) ? '1' : '' );
    update_post_meta( $post_id, '_yumeho_case_is_hidden',   isset( $_POST['yumeho_case_is_hidden'] )   ? '1' : '' );

    // メイン画像（アイキャッチとして保存）
    if ( isset( $_POST['yumeho_case_thumb_id'] ) ) {
        $thumb_id = intval( $_POST['yumeho_case_thumb_id'] );
        if ( $thumb_id > 0 ) {
            set_post_thumbnail( $post_id, $thumb_id );
        } else {
            delete_post_thumbnail( $post_id );
        }
    }

    // 施設種別（taxonomy として保存）
    if ( isset( $_POST['yumeho_case_facility_term'] ) ) {
        $term_id = intval( $_POST['yumeho_case_facility_term'] );
        if ( $term_id > 0 ) {
            wp_set_post_terms( $post_id, array( $term_id ), 'facility_type', false );
        } else {
            wp_set_post_terms( $post_id, array(), 'facility_type', false );
        }
    }

    $yumeho_product_term = get_term_by( 'slug', 'yumeho', 'product_type' );
    if ( $yumeho_product_term instanceof WP_Term ) {
        wp_set_post_terms( $post_id, array( (int) $yumeho_product_term->term_id ), 'product_type', false );
    }

    $current_post = get_post( $post_id );
    if ( $current_post instanceof WP_Post ) {
        $requested_title = isset( $_POST['yumeho_case_editor_title'] )
            ? sanitize_text_field( wp_unslash( $_POST['yumeho_case_editor_title'] ) )
            : $current_post->post_title;

        if ( '' === trim( $requested_title ) ) {
            $requested_title = yumeho_case_generate_title(
                isset( $_POST['yumeho_case_facility_name'] ) ? wp_unslash( $_POST['yumeho_case_facility_name'] ) : '',
                isset( $_POST['yumeho_case_product_model'] ) ? wp_unslash( $_POST['yumeho_case_product_model'] ) : '',
                isset( $_POST['yumeho_case_location'] ) ? wp_unslash( $_POST['yumeho_case_location'] ) : ''
            );
        }

        if ( '' !== $requested_title && $requested_title !== $current_post->post_title ) {
            remove_action( 'save_post_case_study', 'yumeho_save_case_meta' );
            wp_update_post(
                array(
                    'ID'         => $post_id,
                    'post_title' => $requested_title,
                )
            );
            add_action( 'save_post_case_study', 'yumeho_save_case_meta' );
        }
    }
}
add_action( 'save_post_case_study', 'yumeho_save_case_meta' );

add_action( 'admin_footer-post.php', 'yumeho_case_admin_footer_script' );
add_action( 'admin_footer-post-new.php', 'yumeho_case_admin_footer_script' );
function yumeho_case_admin_footer_script() {
    global $post;
    if ( ! $post || 'case_study' !== $post->post_type ) {
        return;
    }
    ?>
    <script>
    (function($){
        var selectors = {
            title: $('#yumeho_case_editor_title'),
            facility: $('#yumeho_case_facility_name'),
            installDate: $('#yumeho_case_install_date'),
            location: $('#yumeho_case_location'),
            productModel: $('#yumeho_case_product_model'),
            facilityTerm: $('#yumeho_case_facility_term'),
            thumbId: $('#yumeho_case_thumb_id')
        };

        var totalChecks = $('.yumeho-case-check').length;

        function generateTitle() {
            var manual = $.trim(selectors.title.val());
            if (manual) return manual;

            var facility = $.trim(selectors.facility.val());
            var model = $.trim(selectors.productModel.val());
            var location = $.trim(selectors.location.val());

            if (facility && model) return facility + ' / ' + model;
            if (facility) return facility;
            if (model && location) return location + ' / ' + model;
            if (model) return model;
            if (location) return location + ' の導入事例';
            return '新しい導入事例';
        }

        function isFilled($field) {
            if (!$field.length) return false;
            if ($field.is('select')) return $.trim($field.val()) !== '';
            return $.trim($field.val()) !== '';
        }

        function editorValue(editorId, fallbackSelector) {
            if (window.tinymce) {
                var editor = window.tinymce.get(editorId);
                if (editor && !editor.isHidden()) {
                    return $.trim(editor.getContent({ format: 'text' }));
                }
            }

            var $fallback = $(fallbackSelector);
            return $fallback.length ? $.trim($fallback.val()) : '';
        }

        function updateChecklist() {
            var checks = {
                '施設名': isFilled(selectors.facility),
                '機種': isFilled(selectors.productModel),
                '施設種別': isFilled(selectors.facilityTerm),
                '導入年月': isFilled(selectors.installDate),
                'メイン画像': $.trim(selectors.thumbId.val()) !== '',
                '課題': editorValue('yumeho_case_challenge_editor', 'textarea[name="yumeho_case_challenge"]') !== '',
                '決め手': editorValue('yumeho_case_reason_editor', 'textarea[name="yumeho_case_reason"]') !== '',
                '変化': editorValue('yumeho_case_change_editor', 'textarea[name="yumeho_case_change"]') !== ''
            };

            var completed = 0;
            $.each(checks, function(label, done) {
                var $chip = $('.yumeho-case-check[data-check-target="' + label + '"]');
                $chip.toggleClass('yumeho-case-check--done', !!done);
                if (done) completed += 1;
            });

            $('#yumeho_case_progress').text(completed + ' / ' + totalChecks);
            $('#yumeho_case_title_preview').text(generateTitle());
            $('#yumeho_case_title_hint').text(generateTitle());
        }

        function bindEditor(editorId) {
            if (!window.tinymce) return;
            var editor = window.tinymce.get(editorId);
            if (!editor || editor._yumehoChecklistBound) return;

            editor.on('input change keyup SetContent', updateChecklist);
            editor._yumehoChecklistBound = true;
        }

        selectors.title
            .add(selectors.facility)
            .add(selectors.installDate)
            .add(selectors.location)
            .add(selectors.productModel)
            .add(selectors.facilityTerm)
            .add(selectors.thumbId)
            .on('input change', updateChecklist);

        [
            'yumeho_case_challenge_editor',
            'yumeho_case_reason_editor',
            'yumeho_case_change_editor'
        ].forEach(bindEditor);

        if (window.tinymce && window.tinymce.on) {
            window.tinymce.on('AddEditor', function(event) {
                bindEditor(event.editor.id);
            });
        }

        updateChecklist();
    })(jQuery);
    </script>
    <?php
}

function yumeho_case_admin_query_context( $query ) {
    if ( ! is_admin() || ! ( $query instanceof WP_Query ) || ! $query->is_main_query() ) {
        return false;
    }

    $post_type = $query->get( 'post_type' );

    if ( is_array( $post_type ) ) {
        return in_array( 'case_study', $post_type, true );
    }

    return 'case_study' === $post_type;
}

add_filter( 'manage_case_study_posts_columns', 'yumeho_case_admin_columns' );
function yumeho_case_admin_columns( $columns ) {
    return array(
        'cb'            => isset( $columns['cb'] ) ? $columns['cb'] : '',
        'thumbnail'     => '画像',
        'title'         => '事例タイトル',
        'case_overview' => '施設・導入情報',
        'case_flags'    => '表示',
        'date'          => '更新日',
    );
}

add_action( 'manage_case_study_posts_custom_column', 'yumeho_case_admin_column_content', 10, 2 );
function yumeho_case_admin_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'thumbnail':
            if ( has_post_thumbnail( $post_id ) ) {
                echo get_the_post_thumbnail( $post_id, array( 72, 48 ), array( 'style' => 'width:72px;height:48px;object-fit:cover;border-radius:6px;display:block;' ) );
            } else {
                echo '<span style="color:#94a3b8;">未設定</span>';
            }
            break;

        case 'case_overview':
            $facility_name = trim( (string) yumeho_case_field_value( $post_id, 'facility_name' ) );
            $product_model = trim( (string) yumeho_case_field_value( $post_id, 'product_model' ) );
            $install_date  = trim( (string) yumeho_case_field_value( $post_id, 'install_date' ) );
            $location      = trim( (string) yumeho_case_field_value( $post_id, 'location' ) );
            $terms = get_the_terms( $post_id, 'facility_type' );
            $facility_type = '';
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                $facility_type = $terms[0]->name;
            }

            $primary_parts = array_filter(
                array(
                    $facility_type,
                    $product_model,
                )
            );
            $secondary_parts = array_filter(
                array(
                    $install_date,
                    $location,
                )
            );
            ?>
            <div class="yumeho-case-overview">
                <div class="yumeho-case-overview__facility"><?php echo esc_html( $facility_name ?: '施設名未設定' ); ?></div>
                <?php if ( ! empty( $primary_parts ) ) : ?>
                <div class="yumeho-case-overview__chips">
                    <?php foreach ( $primary_parts as $part ) : ?>
                    <span class="yumeho-case-admin-chip"><?php echo esc_html( $part ); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if ( ! empty( $secondary_parts ) ) : ?>
                <div class="yumeho-case-overview__meta"><?php echo esc_html( implode( ' ・ ', $secondary_parts ) ); ?></div>
                <?php else : ?>
                <div class="yumeho-case-overview__meta yumeho-case-overview__meta--muted">導入年月・地域は未設定です</div>
                <?php endif; ?>
            </div>
            <?php
            break;

        case 'case_flags':
            $badges = array();

            if ( '1' === yumeho_case_field_value( $post_id, 'is_featured' ) ) {
                $badges[] = '<span class="yumeho-case-admin-badge yumeho-case-admin-badge--featured">おすすめ</span>';
            }

            if ( '1' === yumeho_case_field_value( $post_id, 'is_hidden' ) ) {
                $badges[] = '<span class="yumeho-case-admin-badge yumeho-case-admin-badge--hidden">一覧非表示</span>';
            } else {
                $badges[] = '<span class="yumeho-case-admin-badge yumeho-case-admin-badge--visible">公開中</span>';
            }

            echo implode( ' ', $badges ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            break;
    }
}

add_filter( 'manage_edit-case_study_sortable_columns', 'yumeho_case_admin_sortable_columns' );
function yumeho_case_admin_sortable_columns( $columns ) {
    return $columns;
}

add_action( 'pre_get_posts', 'yumeho_case_admin_query_filters' );
function yumeho_case_admin_query_filters( $query ) {
    if ( ! yumeho_case_admin_query_context( $query ) ) {
        return;
    }

    $meta_query = (array) $query->get( 'meta_query' );
    $model      = isset( $_GET['case_model'] ) ? sanitize_text_field( wp_unslash( $_GET['case_model'] ) ) : '';
    $visibility = isset( $_GET['case_visibility'] ) ? sanitize_text_field( wp_unslash( $_GET['case_visibility'] ) ) : '';
    $orderby    = $query->get( 'orderby' );

    if ( '' !== $model ) {
        $meta_query[] = array(
            'key'   => '_yumeho_case_product_model',
            'value' => $model,
        );
    }

    if ( 'featured' === $visibility ) {
        $meta_query[] = array(
            'key'   => '_yumeho_case_is_featured',
            'value' => '1',
        );
    } elseif ( 'hidden' === $visibility ) {
        $meta_query[] = array(
            'key'   => '_yumeho_case_is_hidden',
            'value' => '1',
        );
    } elseif ( 'visible' === $visibility ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_yumeho_case_is_hidden',
                'value'   => '1',
                'compare' => '!=',
            ),
            array(
                'key'     => '_yumeho_case_is_hidden',
                'compare' => 'NOT EXISTS',
            ),
        );
    }

    if ( ! empty( $meta_query ) ) {
        $query->set( 'meta_query', $meta_query );
    }

    if ( 'facility_name' === $orderby ) {
        $query->set( 'meta_key', '_yumeho_case_facility_name' );
        $query->set( 'orderby', 'meta_value' );
    } elseif ( 'product_model' === $orderby ) {
        $query->set( 'meta_key', '_yumeho_case_product_model' );
        $query->set( 'orderby', 'meta_value' );
    }
}

add_action( 'restrict_manage_posts', 'yumeho_case_admin_filters_ui', 10, 2 );
function yumeho_case_admin_filters_ui( $post_type, $which ) {
    if ( 'case_study' !== $post_type || 'top' !== $which ) {
        return;
    }

    $selected_model      = isset( $_GET['case_model'] ) ? sanitize_text_field( wp_unslash( $_GET['case_model'] ) ) : '';
    $selected_visibility = isset( $_GET['case_visibility'] ) ? sanitize_text_field( wp_unslash( $_GET['case_visibility'] ) ) : '';
    $selected_facility   = isset( $_GET['facility_type'] ) ? sanitize_text_field( wp_unslash( $_GET['facility_type'] ) ) : '';

    wp_dropdown_categories(
        array(
            'show_option_all' => 'すべての施設種別',
            'taxonomy'        => 'facility_type',
            'name'            => 'facility_type',
            'orderby'         => 'name',
            'selected'        => $selected_facility,
            'hierarchical'    => true,
            'depth'           => 1,
            'show_count'      => false,
            'hide_empty'      => false,
            'value_field'     => 'slug',
        )
    );
    ?>
    <select name="case_model">
        <option value="">すべての機種</option>
        <?php foreach ( yumeho_case_product_model_options() as $option ) : ?>
        <option value="<?php echo esc_attr( $option ); ?>" <?php selected( $selected_model, $option ); ?>><?php echo esc_html( $option ); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="case_visibility">
        <option value="">表示状態すべて</option>
        <option value="featured" <?php selected( $selected_visibility, 'featured' ); ?>>おすすめのみ</option>
        <option value="visible" <?php selected( $selected_visibility, 'visible' ); ?>>一覧表示のみ</option>
        <option value="hidden" <?php selected( $selected_visibility, 'hidden' ); ?>>一覧非表示のみ</option>
    </select>
    <?php
}

add_filter( 'posts_search', 'yumeho_case_admin_search', 10, 2 );
function yumeho_case_admin_search( $search, $query ) {
    if ( ! yumeho_case_admin_query_context( $query ) || ! $query->is_search() ) {
        return $search;
    }

    global $wpdb;

    $search_term = trim( (string) $query->get( 's' ) );
    if ( '' === $search_term ) {
        return $search;
    }

    $like         = '%' . $wpdb->esc_like( $search_term ) . '%';
    $meta_clauses = array();
    $meta_values  = array();

    foreach ( array( 'facility_name', 'location', 'product_model', 'challenge', 'reason', 'change', 'pullquote', 'pullquote_speaker' ) as $meta_key ) {
        $meta_clauses[] = '(pm.meta_key = %s AND pm.meta_value LIKE %s)';
        $meta_values[]  = '_yumeho_case_' . $meta_key;
        $meta_values[]  = $like;
    }

    $meta_search = $wpdb->prepare(
        'EXISTS (SELECT 1 FROM ' . $wpdb->postmeta . ' pm WHERE pm.post_id = ' . $wpdb->posts . '.ID AND (' . implode( ' OR ', $meta_clauses ) . '))',
        $meta_values
    );

    return $wpdb->prepare(
        " AND (({$wpdb->posts}.post_title LIKE %s) OR ({$wpdb->posts}.post_content LIKE %s) OR {$meta_search}) ",
        $like,
        $like
    );
}

add_filter( 'post_row_actions', 'yumeho_case_admin_row_actions', 10, 2 );
function yumeho_case_admin_row_actions( $actions, $post ) {
    if ( ! $post instanceof WP_Post || 'case_study' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
        return $actions;
    }

    $actions['duplicate_case_study'] = sprintf(
        '<a href="%s">複製して新規作成</a>',
        esc_url(
            wp_nonce_url(
                admin_url( 'admin.php?action=yumeho_duplicate_case_study&post=' . $post->ID ),
                'yumeho_duplicate_case_study_' . $post->ID
            )
        )
    );

    return $actions;
}

add_action( 'admin_action_yumeho_duplicate_case_study', 'yumeho_duplicate_case_study_action' );
function yumeho_duplicate_case_study_action() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'この操作を実行する権限がありません。' );
    }

    $post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
    if ( ! $post_id || ! wp_verify_nonce( isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '', 'yumeho_duplicate_case_study_' . $post_id ) ) {
        wp_die( '不正なリクエストです。' );
    }

    $source_post = get_post( $post_id );
    if ( ! $source_post instanceof WP_Post || 'case_study' !== $source_post->post_type ) {
        wp_die( '導入事例が見つかりません。' );
    }

    $new_post_id = wp_insert_post(
        array(
            'post_type'    => 'case_study',
            'post_status'  => 'draft',
            'post_title'   => $source_post->post_title . '（複製）',
            'post_content' => $source_post->post_content,
            'post_author'  => get_current_user_id(),
        )
    );

    if ( is_wp_error( $new_post_id ) || ! $new_post_id ) {
        wp_die( '複製の作成に失敗しました。' );
    }

    $all_meta = get_post_meta( $post_id );
    foreach ( $all_meta as $meta_key => $values ) {
        if ( in_array( $meta_key, array( '_edit_lock', '_edit_last' ), true ) ) {
            continue;
        }

        foreach ( (array) $values as $meta_value ) {
            add_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value ) );
        }
    }

    foreach ( get_object_taxonomies( 'case_study' ) as $taxonomy ) {
        $term_ids = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
        if ( ! is_wp_error( $term_ids ) ) {
            wp_set_post_terms( $new_post_id, $term_ids, $taxonomy, false );
        }
    }

    wp_safe_redirect(
        add_query_arg(
            array(
                'post'       => $new_post_id,
                'action'     => 'edit',
                'duplicated' => 1,
            ),
            admin_url( 'post.php' )
        )
    );
    exit;
}

add_action( 'admin_notices', 'yumeho_case_admin_notices' );
function yumeho_case_admin_notices() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'case_study' !== $screen->post_type ) {
        return;
    }

    if ( isset( $_GET['duplicated'] ) && '1' === (string) $_GET['duplicated'] ) {
        echo '<div class="notice notice-success is-dismissible"><p>導入事例を複製しました。内容を調整して保存してください。</p></div>';
    }

    if ( 'edit-case_study' === $screen->id ) {
        $counts = wp_count_posts( 'case_study' );
        $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
        $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;
        ?>
        <div class="notice yumeho-case-list-guide">
            <div class="yumeho-case-list-guide__stats">
                <span class="yumeho-case-list-guide__stat"><strong><?php echo esc_html( (string) $published ); ?></strong> 公開中</span>
                <span class="yumeho-case-list-guide__stat"><strong><?php echo esc_html( (string) $drafts ); ?></strong> 下書き</span>
            </div>
            <p class="yumeho-case-list-guide__text">一覧では「事例タイトル」で公開見出しを確認し、「施設・導入情報」で施設名、施設種別、機種、導入年月、地域をまとめて確認できます。検索はタイトル、施設名、地域、機種、課題文にも対応しています。</p>
        </div>
        <?php
    }
}

add_action( 'admin_head-edit.php', 'yumeho_case_list_admin_css' );
function yumeho_case_list_admin_css() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-case_study' !== $screen->id ) {
        return;
    }
    ?>
    <style>
    .post-type-case_study .wrap > h1.wp-heading-inline {
        margin-bottom: 6px;
    }
    .post-type-case_study .subsubsub {
        margin: 6px 0 8px;
    }
    .post-type-case_study .tablenav.top {
        margin: 8px 0 10px;
        min-height: 36px;
    }
    .post-type-case_study .tablenav.bottom {
        margin-top: 10px;
    }
    .post-type-case_study .tablenav .actions select,
    .post-type-case_study .tablenav .button,
    .post-type-case_study .search-box input[type="search"] {
        min-height: 36px;
        border-radius: 10px;
    }
    .post-type-case_study .tablenav .actions,
    .post-type-case_study .search-box {
        margin-bottom: 0;
    }
    .post-type-case_study .search-box input[type="search"] {
        min-width: 280px;
        padding-inline: 14px;
    }
    .post-type-case_study .wp-list-table {
        border: 1px solid #dbe4ee;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.04);
    }
    .post-type-case_study .wp-list-table .column-rank_math_seo_details,
    .post-type-case_study .wp-list-table .column-rank_math_title,
    .post-type-case_study .wp-list-table .column-rank_math_description {
        display: none;
    }
    .post-type-case_study .wp-list-table thead th,
    .post-type-case_study .wp-list-table tfoot th {
        background: #f8fbff;
    }
    .post-type-case_study .wp-list-table tbody tr:hover {
        background: #fcfdff;
    }
    .post-type-case_study .wp-list-table tbody td,
    .post-type-case_study .wp-list-table tbody th {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .post-type-case_study .column-thumbnail { width: 88px; }
    .post-type-case_study .column-title { width: 30%; }
    .post-type-case_study .column-case_overview { width: 30%; }
    .post-type-case_study .column-case_flags { width: 16%; }
    .post-type-case_study .column-date { width: 12%; }
    .post-type-case_study td,
    .post-type-case_study th {
        vertical-align: middle;
    }
    .post-type-case_study .column-title .row-title {
        font-size: 14px;
        line-height: 1.55;
    }
    .post-type-case_study .column-title .row-actions {
        margin-top: 4px;
    }
    .post-type-case_study .column-thumbnail img {
        border-radius: 10px !important;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }
    .yumeho-case-overview {
        display: grid;
        gap: 7px;
    }
    .yumeho-case-overview__facility {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.55;
    }
    .yumeho-case-overview__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .yumeho-case-admin-chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        background: #eef6ff;
        color: #00538f;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
    }
    .yumeho-case-overview__meta {
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }
    .yumeho-case-overview__meta--muted {
        color: #94a3b8;
    }
    .yumeho-case-admin-badge {
        display: inline-flex;
        align-items: center;
        margin: 0 6px 6px 0;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
    }
    .yumeho-case-admin-badge--featured {
        background: rgba(0, 104, 183, 0.12);
        color: #00538f;
    }
    .yumeho-case-admin-badge--visible {
        background: rgba(15, 118, 110, 0.12);
        color: #0f766e;
    }
    .yumeho-case-admin-badge--hidden {
        background: rgba(148, 163, 184, 0.18);
        color: #475569;
    }
    .yumeho-case-list-guide {
        border: 1px solid #cfe0f2;
        border-left: 4px solid #0068b7;
        border-radius: 14px;
        padding: 10px 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        margin: 10px 0 8px;
    }
    .yumeho-case-list-guide__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 6px;
    }
    .yumeho-case-list-guide__stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(0, 104, 183, 0.08);
        color: #00538f;
        font-size: 12px;
        font-weight: 700;
    }
    .yumeho-case-list-guide__text {
        margin: 0;
        color: #334155;
        font-size: 12px;
        line-height: 1.65;
    }
    @media (max-width: 1200px) {
        .post-type-case_study .search-box input[type="search"] {
            min-width: 220px;
        }
    }
    </style>
    <?php
}

add_action( 'admin_footer-edit.php', 'yumeho_case_list_admin_footer_script' );
function yumeho_case_list_admin_footer_script() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-case_study' !== $screen->id ) {
        return;
    }
    ?>
    <script>
    (function($){
        var $search = $('#post-search-input');
        if ($search.length) {
            $search.attr('placeholder', 'タイトル・施設名・地域・機種で検索');
        }
    })(jQuery);
    </script>
    <?php
}

/* ==========================================================================
   6. FAQ Admin
   ========================================================================== */

function yumeho_admin_taxonomy_panel_html( $post, $taxonomy_slug ) {
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

function yumeho_faq_admin_is_post_screen( $post = null ) {
    return $post instanceof WP_Post && 'faq' === $post->post_type;
}

function yumeho_faq_answer_summary( $post_id ) {
    $content = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ), true );
    $content = preg_replace( '/\s+/u', ' ', $content );

    return trim( wp_html_excerpt( (string) $content, 90, '…' ) );
}

function yumeho_faq_term_list( $post_id, $taxonomy ) {
    $terms = get_the_terms( $post_id, $taxonomy );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        return array();
    }

    return array_values(
        array_filter(
            array_map(
                static function( $term ) {
                    return $term instanceof WP_Term ? $term->name : '';
                },
                $terms
            )
        )
    );
}

add_filter( 'enter_title_here', 'yumeho_faq_title_placeholder', 10, 2 );
function yumeho_faq_title_placeholder( $text, $post ) {
    if ( yumeho_faq_admin_is_post_screen( $post ) ) {
        return '例: 天井直付型とスタンド型の違いは？';
    }

    return $text;
}

add_action( 'add_meta_boxes', 'yumeho_faq_admin_meta_boxes', 30 );
function yumeho_faq_admin_meta_boxes() {
    remove_meta_box( 'faq_categorydiv', 'faq', 'side' );
    remove_meta_box( 'product_typediv', 'faq', 'side' );
    remove_meta_box( 'slugdiv', 'faq', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'faq', 'normal' );
    remove_meta_box( 'commentsdiv', 'faq', 'normal' );
    remove_meta_box( 'trackbacksdiv', 'faq', 'normal' );
    remove_meta_box( 'authordiv', 'faq', 'normal' );
    remove_meta_box( 'revisionsdiv', 'faq', 'normal' );
    remove_meta_box( 'pageparentdiv', 'faq', 'side' );

    add_meta_box(
        'yumeho_faq_checklist',
        '公開チェック',
        'yumeho_faq_checklist_meta_box_html',
        'faq',
        'side',
        'high'
    );
}

function yumeho_faq_checklist_meta_box_html( $post ) {
    if ( ! $post instanceof WP_Post ) {
        return;
    }

    $items = array(
        'title'    => array( 'label' => '質問タイトル', 'required' => true ),
        'content'  => array( 'label' => '回答本文', 'required' => true ),
        'category' => array( 'label' => 'FAQカテゴリ', 'required' => true ),
        'product'  => array( 'label' => '対象製品', 'required' => false ),
        'order'    => array( 'label' => '並び順', 'required' => true ),
    );
    ?>
    <div class="yumeho-faq-checklist">
        <p class="description" style="margin-top:0;">質問、回答、カテゴリ、並び順が入っていれば公開準備はほぼ完了です。</p>
        <ul class="yumeho-faq-checklist__list">
            <?php foreach ( $items as $key => $item ) : ?>
                <li class="yumeho-faq-checklist__item" data-check="<?php echo esc_attr( $key ); ?>" data-required="<?php echo $item['required'] ? '1' : '0'; ?>">
                    <span class="yumeho-faq-checklist__badge">未確認</span>
                    <span class="yumeho-faq-checklist__label"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php if ( ! $item['required'] ) : ?>
                        <span class="yumeho-faq-checklist__note">任意</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

add_action( 'edit_form_after_title', 'yumeho_faq_after_title_fields' );
function yumeho_faq_after_title_fields( $post ) {
    if ( ! yumeho_faq_admin_is_post_screen( $post ) ) {
        return;
    }

    $menu_order = (int) $post->menu_order;
    ?>
    <div class="yumeho-faq-top-fields">
        <div class="yumeho-faq-top-fields__guide">
            <p class="yumeho-faq-top-fields__lead">
                FAQ は <strong>質問タイトル</strong>、<strong>回答本文</strong>、<strong>FAQカテゴリ</strong>、<strong>並び順</strong> が入っていれば公開できます。
                まず質問と回答を入れ、最後にカテゴリと並び順を決める流れが分かりやすいです。
            </p>
            <div class="yumeho-faq-top-fields__grid">
                <div class="yumeho-faq-top-card">
                    <h4>公開までの流れ</h4>
                    <ol>
                        <li>質問タイトル</li>
                        <li>回答本文</li>
                        <li>FAQカテゴリ</li>
                        <li>並び順</li>
                    </ol>
                </div>
                <div class="yumeho-faq-top-card yumeho-faq-top-card--settings">
                    <h4>並び順</h4>
                    <label for="menu_order"><strong>並び順</strong></label>
                    <input type="number" name="menu_order" id="menu_order" value="<?php echo esc_attr( (string) $menu_order ); ?>" min="0" step="1">
                    <p class="description">トップページと FAQ ページでは、小さい数字の質問ほど上に表示されます。迷ったら `10, 20, 30...` のように入れると後から差し込みやすくなります。</p>
                </div>
                <div class="yumeho-faq-top-card yumeho-faq-top-card--hint">
                    <h4>質問の書き方</h4>
                    <ul>
                        <li>会員や見込み顧客が検索しそうな言い方で書くと見つけやすくなります</li>
                        <li>質問は1件につき1テーマに絞ると、回答も短く分かりやすくなります</li>
                        <li>対象製品は迷ったときだけ使い分ければ十分です</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="yumeho-faq-top-fields__taxonomy-grid">
            <div class="yumeho-faq-taxonomy-card">
                <label><strong>FAQカテゴリ</strong></label>
                <p class="description">FAQページの絞り込みタブで使います。</p>
                <div class="yumeho-faq-taxonomy-panel">
                    <?php yumeho_admin_taxonomy_panel_html( $post, 'faq_category' ); ?>
                </div>
            </div>
            <div class="yumeho-faq-taxonomy-card">
                <label><strong>対象製品</strong></label>
                <p class="description">YUMEHO 以外の製品が増えたときの整理に使います。通常は YUMEHO を選べば大丈夫です。</p>
                <div class="yumeho-faq-taxonomy-panel">
                    <?php yumeho_admin_taxonomy_panel_html( $post, 'product_type' ); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'admin_head-post.php', 'yumeho_faq_admin_css' );
add_action( 'admin_head-post-new.php', 'yumeho_faq_admin_css' );
function yumeho_faq_admin_css() {
    global $post;
    if ( ! yumeho_faq_admin_is_post_screen( $post ) ) {
        return;
    }
    ?>
    <style>
    .post-type-faq #slugdiv,
    .post-type-faq #commentstatusdiv,
    .post-type-faq #commentsdiv,
    .post-type-faq #trackbacksdiv,
    .post-type-faq #authordiv,
    .post-type-faq #revisionsdiv,
    .post-type-faq #pageparentdiv {
        display: none;
    }
    .yumeho-faq-top-fields {
        margin: 18px 0 20px;
        display: grid;
        gap: 18px;
    }
    .yumeho-faq-top-fields__guide {
        padding: 18px 20px;
        border: 1px solid #cfe0f2;
        border-radius: 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }
    .yumeho-faq-top-fields__lead {
        margin: 0 0 14px;
        line-height: 1.9;
    }
    .yumeho-faq-top-fields__grid,
    .yumeho-faq-top-fields__taxonomy-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .yumeho-faq-top-card,
    .yumeho-faq-taxonomy-card {
        padding: 16px 18px;
        border: 1px solid #dcdcde;
        border-radius: 14px;
        background: #fff;
    }
    .yumeho-faq-top-card--settings {
        border-color: #b8d4f0;
        background: #f7fbff;
    }
    .yumeho-faq-top-card--hint {
        border-color: #d6e7fb;
        background: #f9fcff;
    }
    .yumeho-faq-top-card h4,
    .yumeho-faq-taxonomy-card label {
        display: block;
        margin: 0 0 10px;
    }
    .yumeho-faq-top-card ol,
    .yumeho-faq-top-card ul {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 6px;
    }
    .yumeho-faq-top-card input[type="number"] {
        width: 140px;
        margin: 6px 0 8px;
    }
    .yumeho-faq-taxonomy-panel .categorydiv,
    .yumeho-faq-taxonomy-panel .tagsdiv {
        margin: 0;
    }
    .yumeho-faq-taxonomy-panel .tabs,
    .yumeho-faq-taxonomy-panel .hide-if-no-js,
    .yumeho-faq-taxonomy-panel .wp-hidden-children,
    .yumeho-faq-taxonomy-panel .tagcloud-link {
        display: none !important;
    }
    .yumeho-faq-taxonomy-panel .tabs-panel {
        display: block !important;
        max-height: none;
        padding: 0;
        border: 0;
        overflow: visible;
    }
    .yumeho-faq-taxonomy-panel ul.categorychecklist {
        margin: 0;
        display: grid;
        gap: 8px;
    }
    .yumeho-faq-taxonomy-panel ul.categorychecklist li {
        margin: 0;
    }
    .yumeho-faq-taxonomy-panel ul.categorychecklist label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
    }
    .yumeho-faq-checklist__list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 10px;
    }
    .yumeho-faq-checklist__item {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .yumeho-faq-checklist__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        padding: 3px 8px;
        border-radius: 999px;
        background: #eef2f7;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
    }
    .yumeho-faq-checklist__item.is-done .yumeho-faq-checklist__badge {
        background: rgba(0, 104, 183, 0.12);
        color: #00538f;
    }
    .yumeho-faq-checklist__label {
        font-weight: 600;
    }
    .yumeho-faq-checklist__note {
        color: #64748b;
        font-size: 12px;
    }
    @media (max-width: 1100px) {
        .yumeho-faq-top-fields__grid,
        .yumeho-faq-top-fields__taxonomy-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

add_action( 'admin_footer-post.php', 'yumeho_faq_admin_footer_script' );
add_action( 'admin_footer-post-new.php', 'yumeho_faq_admin_footer_script' );
function yumeho_faq_admin_footer_script() {
    global $post;
    if ( ! yumeho_faq_admin_is_post_screen( $post ) ) {
        return;
    }
    ?>
    <script>
    (function($){
        function editorValue() {
            if (window.tinymce) {
                var editor = window.tinymce.get('content');
                if (editor && !editor.isHidden()) {
                    return $.trim(editor.getContent({ format: 'text' }));
                }
            }
            return $.trim($('#content').val() || '');
        }

        function hasChecked(selector) {
            return $(selector + ' input[type="checkbox"]:checked').length > 0;
        }

        function updateChecklist() {
            var checks = {
                title: $.trim($('#title').val() || '') !== '',
                content: editorValue() !== '',
                category: hasChecked('#taxonomy-faq_category'),
                product: hasChecked('#taxonomy-product_type'),
                order: $.trim($('#menu_order').val() || '') !== ''
            };

            $.each(checks, function(key, done){
                var $item = $('.yumeho-faq-checklist__item[data-check="' + key + '"]');
                $item.toggleClass('is-done', !!done);
                $item.find('.yumeho-faq-checklist__badge').text(done ? '入力済み' : '未確認');
            });
        }

        $('#title, #menu_order').on('input change', updateChecklist);
        $('#taxonomy-faq_category, #taxonomy-product_type').on('change', 'input[type="checkbox"]', updateChecklist);
        $('#content').on('input change', updateChecklist);

        if (window.tinymce && window.tinymce.on) {
            window.tinymce.on('AddEditor', function(event) {
                if (event.editor && event.editor.id === 'content') {
                    event.editor.on('input change keyup SetContent', updateChecklist);
                }
            });
        }

        updateChecklist();
    })(jQuery);
    </script>
    <?php
}

function yumeho_faq_admin_reorder_is_available() {
    if ( ! is_admin() ) {
        return false;
    }

    $post_type = '';
    if ( isset( $_GET['post_type'] ) ) {
        $post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) );
    }

    if ( 'faq' !== $post_type ) {
        return false;
    }

    if ( isset( $_GET['s'] ) && '' !== trim( (string) wp_unslash( $_GET['s'] ) ) ) {
        return false;
    }

    if ( isset( $_GET['faq_category'] ) && '' !== trim( (string) wp_unslash( $_GET['faq_category'] ) ) ) {
        return false;
    }

    if ( isset( $_GET['yumeho_faq_product_type'] ) && '' !== trim( (string) wp_unslash( $_GET['yumeho_faq_product_type'] ) ) ) {
        return false;
    }

    if ( isset( $_GET['paged'] ) && (int) wp_unslash( $_GET['paged'] ) > 1 ) {
        return false;
    }

    if ( isset( $_GET['orderby'] ) ) {
        $orderby = sanitize_key( wp_unslash( $_GET['orderby'] ) );
        if ( '' !== $orderby && 'menu_order' !== $orderby ) {
            return false;
        }
    }

    if ( isset( $_GET['order'] ) ) {
        $order = strtoupper( sanitize_key( wp_unslash( $_GET['order'] ) ) );
        if ( '' !== $order && 'ASC' !== $order ) {
            return false;
        }
    }

    if ( isset( $_GET['post_status'] ) ) {
        $post_status = sanitize_key( wp_unslash( $_GET['post_status'] ) );
        if ( '' !== $post_status && 'all' !== $post_status ) {
            return false;
        }
    }

    return true;
}

function yumeho_faq_update_menu_order( $ordered_ids ) {
    global $wpdb;

    $ordered_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $ordered_ids ) ) ) );
    if ( empty( $ordered_ids ) ) {
        return 0;
    }

    $updated = 0;
    $order   = 10;

    foreach ( $ordered_ids as $post_id ) {
        if ( 'faq' !== get_post_type( $post_id ) ) {
            continue;
        }

        $wpdb->update(
            $wpdb->posts,
            array( 'menu_order' => $order ),
            array( 'ID' => $post_id ),
            array( '%d' ),
            array( '%d' )
        );

        clean_post_cache( $post_id );
        $order += 10;
        $updated++;
    }

    return $updated;
}

add_action( 'wp_ajax_yumeho_faq_reorder', 'yumeho_faq_reorder_ajax' );
function yumeho_faq_reorder_ajax() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => '並び替えを保存する権限がありません。' ), 403 );
    }

    check_ajax_referer( 'yumeho_faq_reorder', 'nonce' );

    $ordered_ids = isset( $_POST['ordered_ids'] ) ? (array) wp_unslash( $_POST['ordered_ids'] ) : array();
    $updated     = yumeho_faq_update_menu_order( $ordered_ids );

    if ( 0 === $updated ) {
        wp_send_json_error( array( 'message' => '保存するFAQが見つかりませんでした。' ), 400 );
    }

    wp_send_json_success(
        array(
            'message' => 'FAQの順番を保存しました。',
            'updated' => $updated,
        )
    );
}

add_filter( 'manage_faq_posts_columns', 'yumeho_faq_admin_columns' );
function yumeho_faq_admin_columns( $columns ) {
    return array(
        'cb'             => $columns['cb'] ?? '',
        'sort_handle'    => '移動',
        'title'          => '質問',
        'faq_category'   => 'FAQカテゴリ',
        'product_type'   => '対象製品',
        'answer_summary' => '回答プレビュー',
        'menu_order'     => '並び順',
        'date'           => '更新日',
    );
}

add_action( 'manage_faq_posts_custom_column', 'yumeho_faq_admin_column_content', 10, 2 );
function yumeho_faq_admin_column_content( $column, $post_id ) {
    if ( 'sort_handle' === $column ) {
        echo '<button type="button" class="button-link yumeho-faq-sort-handle" aria-label="順番をドラッグして変更" title="ドラッグして順番を変更"><span class="dashicons dashicons-menu"></span></button>';
        return;
    }

    if ( 'faq_category' === $column ) {
        $terms = yumeho_faq_term_list( $post_id, 'faq_category' );
        echo ! empty( $terms ) ? esc_html( implode( ' / ', $terms ) ) : '—';
        return;
    }

    if ( 'product_type' === $column ) {
        $terms = yumeho_faq_term_list( $post_id, 'product_type' );
        echo ! empty( $terms ) ? esc_html( implode( ' / ', $terms ) ) : '<span style="color:#64748b;">未設定</span>';
        return;
    }

    if ( 'answer_summary' === $column ) {
        $summary = yumeho_faq_answer_summary( $post_id );
        echo '' !== $summary ? esc_html( $summary ) : '<span style="color:#9ca3af;">未入力</span>';
        return;
    }

    if ( 'menu_order' === $column ) {
        echo esc_html( (string) (int) get_post_field( 'menu_order', $post_id ) );
    }
}

add_filter( 'manage_edit-faq_sortable_columns', 'yumeho_faq_admin_sortable_columns' );
function yumeho_faq_admin_sortable_columns( $columns ) {
    $columns['menu_order'] = 'menu_order';
    return $columns;
}

add_action( 'restrict_manage_posts', 'yumeho_faq_admin_filters_ui', 10, 2 );
function yumeho_faq_admin_filters_ui( $post_type, $which ) {
    if ( 'faq' !== $post_type || 'top' !== $which ) {
        return;
    }

    $selected_category = isset( $_GET['faq_category'] ) ? absint( wp_unslash( $_GET['faq_category'] ) ) : 0;
    $selected_product  = isset( $_GET['yumeho_faq_product_type'] ) ? sanitize_key( wp_unslash( $_GET['yumeho_faq_product_type'] ) ) : '';

    wp_dropdown_categories(
        array(
            'show_option_all' => 'すべてのFAQカテゴリ',
            'taxonomy'        => 'faq_category',
            'name'            => 'faq_category',
            'orderby'         => 'name',
            'selected'        => $selected_category,
            'hierarchical'    => true,
            'depth'           => 1,
            'show_count'      => false,
            'hide_empty'      => false,
        )
    );
    ?>
    <select name="yumeho_faq_product_type">
        <option value="">すべての対象製品</option>
        <?php foreach ( get_terms( array( 'taxonomy' => 'product_type', 'hide_empty' => false ) ) as $term ) : ?>
            <?php if ( $term instanceof WP_Term ) : ?>
            <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected_product, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
    <?php
}

add_action( 'pre_get_posts', 'yumeho_faq_admin_query_filters' );
function yumeho_faq_admin_query_filters( $query ) {
    if ( ! is_admin() || ! ( $query instanceof WP_Query ) || ! $query->is_main_query() || 'faq' !== $query->get( 'post_type' ) ) {
        return;
    }

    $tax_query = (array) $query->get( 'tax_query' );

    if ( ! empty( $_GET['faq_category'] ) ) {
        $term = get_term_by( 'id', absint( wp_unslash( $_GET['faq_category'] ) ), 'faq_category' );
        if ( $term instanceof WP_Term ) {
            $tax_query[] = array(
                'taxonomy' => 'faq_category',
                'field'    => 'slug',
                'terms'    => $term->slug,
            );
        }
    }

    if ( ! empty( $_GET['yumeho_faq_product_type'] ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => sanitize_key( wp_unslash( $_GET['yumeho_faq_product_type'] ) ),
        );
    }

    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 && ! isset( $tax_query['relation'] ) ) {
            $tax_query['relation'] = 'AND';
        }
        $query->set( 'tax_query', $tax_query );
    }

    if ( 'menu_order' === $query->get( 'orderby' ) ) {
        $query->set(
            'orderby',
            array(
                'menu_order' => strtoupper( (string) ( $query->get( 'order' ) ?: 'ASC' ) ),
                'date'       => 'DESC',
            )
        );
        return;
    }

    if ( yumeho_faq_admin_reorder_is_available() ) {
        $query->set( 'posts_per_page', -1 );
    }

    if ( ! $query->get( 'orderby' ) ) {
        $query->set(
            'orderby',
            array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            )
        );
        $query->set( 'order', 'ASC' );
    }
}

add_action( 'admin_notices', 'yumeho_faq_admin_notices' );
function yumeho_faq_admin_notices() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-faq' !== $screen->id ) {
        return;
    }

    $counts = wp_count_posts( 'faq' );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;
    $can_drag  = yumeho_faq_admin_reorder_is_available();
    ?>
    <div class="notice yumeho-faq-list-guide">
        <div class="yumeho-faq-list-guide__stats">
            <span class="yumeho-faq-list-guide__stat"><strong><?php echo esc_html( (string) $published ); ?></strong> 公開中</span>
            <span class="yumeho-faq-list-guide__stat"><strong><?php echo esc_html( (string) $drafts ); ?></strong> 下書き</span>
        </div>
        <p class="yumeho-faq-list-guide__text">
            <?php if ( $can_drag ) : ?>
                一覧では「質問」「FAQカテゴリ」「対象製品」「回答プレビュー」をまとめて確認できます。左端のハンドルをドラッグすると、FAQページの表示順をそのまま入れ替えられます。
            <?php else : ?>
                一覧では「質問」「FAQカテゴリ」「対象製品」「回答プレビュー」をまとめて確認できます。ドラッグでの並び替えは、検索や絞り込みを外したFAQ一覧で使えます。
            <?php endif; ?>
        </p>
    </div>
    <?php
}

add_action( 'admin_enqueue_scripts', 'yumeho_faq_list_admin_enqueue' );
function yumeho_faq_list_admin_enqueue() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-faq' !== $screen->id ) {
        return;
    }
}

add_action( 'admin_head-edit.php', 'yumeho_faq_list_admin_css' );
function yumeho_faq_list_admin_css() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-faq' !== $screen->id ) {
        return;
    }
    ?>
    <style>
    .post-type-faq .wrap > h1.wp-heading-inline {
        margin-bottom: 6px;
    }
    .post-type-faq .subsubsub {
        margin: 6px 0 8px;
    }
    .post-type-faq .tablenav.top {
        margin: 8px 0 10px;
        min-height: 36px;
    }
    .post-type-faq .tablenav.bottom {
        margin-top: 10px;
    }
    .post-type-faq .tablenav .actions select,
    .post-type-faq .tablenav .button,
    .post-type-faq .search-box input[type="search"] {
        min-height: 36px;
        border-radius: 10px;
    }
    .post-type-faq .search-box input[type="search"] {
        min-width: 260px;
        padding-inline: 14px;
    }
    .post-type-faq .wp-list-table {
        border: 1px solid #dbe4ee;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.04);
    }
    .post-type-faq .wp-list-table .column-rank_math_seo_details,
    .post-type-faq .wp-list-table .column-rank_math_title,
    .post-type-faq .wp-list-table .column-rank_math_description {
        display: none;
    }
    .post-type-faq .wp-list-table thead th,
    .post-type-faq .wp-list-table tfoot th {
        background: #f8fbff;
    }
    .post-type-faq .wp-list-table tbody tr:hover {
        background: #fcfdff;
    }
    .post-type-faq .wp-list-table tbody td,
    .post-type-faq .wp-list-table tbody th {
        padding-top: 10px;
        padding-bottom: 10px;
        vertical-align: middle;
    }
    .post-type-faq .column-sort_handle {
        width: 56px;
        text-align: center;
        cursor: grab;
    }
    .post-type-faq .column-title { width: 28%; }
    .post-type-faq .column-faq_category { width: 14%; }
    .post-type-faq .column-product_type { width: 12%; }
    .post-type-faq .column-answer_summary { width: 30%; }
    .post-type-faq .column-menu_order { width: 8%; }
    .post-type-faq .column-date { width: 10%; }
    .post-type-faq .yumeho-faq-sort-handle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        color: #0068b7;
        cursor: grab;
    }
    .post-type-faq .yumeho-faq-sort-handle .dashicons {
        width: 18px;
        height: 18px;
        font-size: 18px;
    }
    .post-type-faq .yumeho-faq-sort-handle:focus {
        box-shadow: 0 0 0 2px rgba(0, 104, 183, 0.18);
        outline: none;
    }
    .post-type-faq .is-dragging .column-sort_handle,
    .post-type-faq .is-dragging .yumeho-faq-sort-handle,
    .post-type-faq .yumeho-faq-sort-handle:active {
        cursor: grabbing;
    }
    .post-type-faq .yumeho-faq-sort-disabled .yumeho-faq-sort-handle {
        opacity: 0.35;
        cursor: default;
        pointer-events: none;
    }
    .post-type-faq .yumeho-faq-sort-placeholder td,
    .post-type-faq .yumeho-faq-sort-placeholder th {
        background: rgba(0, 104, 183, 0.06);
        border-top: 2px dashed rgba(0, 104, 183, 0.35);
        border-bottom: 2px dashed rgba(0, 104, 183, 0.35);
        height: 52px;
    }
    .post-type-faq .yumeho-faq-sort-saving {
        opacity: 0.58;
    }
    .post-type-faq tr.yumeho-faq-row-draggable {
        cursor: grab;
    }
    .post-type-faq tr.yumeho-faq-row-draggable.is-dragging {
        opacity: 0.45;
    }
    .post-type-faq .column-title .row-title {
        font-size: 14px;
        line-height: 1.55;
    }
    .yumeho-faq-list-guide {
        border: 1px solid #cfe0f2;
        border-left: 4px solid #0068b7;
        border-radius: 14px;
        padding: 10px 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        margin: 10px 0 8px;
    }
    .yumeho-faq-list-guide__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 6px;
    }
    .yumeho-faq-list-guide__stat {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(0, 104, 183, 0.08);
        color: #00538f;
        font-size: 12px;
        font-weight: 700;
    }
    .yumeho-faq-list-guide__text {
        margin: 0;
        color: #334155;
        font-size: 12px;
        line-height: 1.65;
    }
    </style>
    <?php
}

add_action( 'admin_footer-edit.php', 'yumeho_faq_list_admin_footer_script' );
function yumeho_faq_list_admin_footer_script() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-faq' !== $screen->id ) {
        return;
    }

    $drag_enabled = yumeho_faq_admin_reorder_is_available();
    $ajax_url     = admin_url( 'admin-ajax.php' );
    $nonce        = wp_create_nonce( 'yumeho_faq_reorder' );
    ?>
    <script>
    (function($){
        var $search = $('#post-search-input');
        var dragEnabled = <?php echo $drag_enabled ? 'true' : 'false'; ?>;
        var ajaxUrl = <?php echo wp_json_encode( $ajax_url ); ?>;
        var reorderNonce = <?php echo wp_json_encode( $nonce ); ?>;
        var isSaving = false;

        if ($search.length) {
            $search.attr('placeholder', '質問・回答文で検索');
        }

        function showStatus(type, message) {
            var noticeClass = type === 'error' ? 'notice-error' : 'notice-success';
            var $notice = $('#yumeho-faq-sort-status');

            if (!$notice.length) {
                $notice = $('<div id="yumeho-faq-sort-status" class="notice inline is-dismissible"><p></p></div>');
                $('.wrap h1.wp-heading-inline').first().after($notice);
            }

            $notice.removeClass('notice-success notice-error').addClass(noticeClass);
            $notice.find('p').text(message);
        }

        function refreshOrderNumbers($rows) {
            $rows.each(function(index){
                $(this).find('.column-menu_order').text((index + 1) * 10);
            });
        }

        var $tableBody = $('.post-type-faq .wp-list-table tbody');
        var $rows = $tableBody.children('tr[id^="post-"]');
        var dragArmedRow = null;
        var draggedRow = null;
        var startIndex = -1;

        if (!dragEnabled) {
            $rows.addClass('yumeho-faq-sort-disabled');
            return;
        }

        if (!$rows.length) {
            return;
        }

        function collectOrderedIds() {
            var orderedIds = [];

            $tableBody.children('tr[id^="post-"]').each(function(){
                orderedIds.push(parseInt(String(this.id).replace('post-', ''), 10));
            });

            return orderedIds;
        }

        function saveOrder() {
            if (isSaving) {
                return;
            }

            var orderedIds = collectOrderedIds();
            var $orderedRows = $tableBody.children('tr[id^="post-"]');

            if (!orderedIds.length) {
                return;
            }

            isSaving = true;
            $orderedRows.addClass('yumeho-faq-sort-saving');

            $.post(ajaxUrl, {
                action: 'yumeho_faq_reorder',
                nonce: reorderNonce,
                ordered_ids: orderedIds
            }).done(function(response){
                if (!response || !response.success) {
                    showStatus('error', response && response.data && response.data.message ? response.data.message : '並び順を保存できませんでした。');
                    return;
                }

                refreshOrderNumbers($orderedRows);
                showStatus('success', response.data && response.data.message ? response.data.message : 'FAQの順番を保存しました。');
            }).fail(function(){
                showStatus('error', '並び順の保存中に通信エラーが発生しました。時間をおいてもう一度お試しください。');
            }).always(function(){
                isSaving = false;
                $orderedRows.removeClass('yumeho-faq-sort-saving');
            });
        }

        $rows.attr('draggable', 'true').addClass('yumeho-faq-row-draggable');

        $tableBody.on('mousedown', '.column-sort_handle, .column-sort_handle *', function() {
            dragArmedRow = $(this).closest('tr[id^="post-"]')[0] || null;
        });

        $(document).on('mouseup', function() {
            dragArmedRow = null;
        });

        $tableBody.on('dragstart', 'tr[id^="post-"]', function(event) {
            if (isSaving || dragArmedRow !== this) {
                event.preventDefault();
                return false;
            }

            draggedRow = this;
            startIndex = $(this).index();
            $(this).addClass('is-dragging');

            if (event.originalEvent && event.originalEvent.dataTransfer) {
                event.originalEvent.dataTransfer.effectAllowed = 'move';
                try {
                    event.originalEvent.dataTransfer.setData('text/plain', this.id);
                } catch (error) {}
            }
        });

        $tableBody.on('dragover', 'tr[id^="post-"]', function(event) {
            if (!draggedRow || this === draggedRow) {
                return;
            }

            event.preventDefault();

            var rect = this.getBoundingClientRect();
            var shouldInsertBefore = (event.originalEvent.clientY - rect.top) < (rect.height / 2);

            if (shouldInsertBefore) {
                this.parentNode.insertBefore(draggedRow, this);
            } else {
                this.parentNode.insertBefore(draggedRow, this.nextSibling);
            }
        });

        $tableBody.on('drop', 'tr[id^="post-"]', function(event) {
            if (!draggedRow) {
                return;
            }

            event.preventDefault();
        });

        $tableBody.on('dragend', 'tr[id^="post-"]', function() {
            if (!draggedRow) {
                dragArmedRow = null;
                return;
            }

            var currentIndex = $(draggedRow).index();

            $(draggedRow).removeClass('is-dragging');

            if (startIndex !== currentIndex) {
                saveOrder();
            }

            draggedRow = null;
            dragArmedRow = null;
            startIndex = -1;
        });
    })(jQuery);
    </script>
    <?php
}

/* === REST API でメタを公開（Rinascente が取得するため） === */
function yumeho_register_case_rest_meta() {
    $meta_keys = array(
        'facility_name', 'install_date', 'location',
        'product_model', 'image_fit',
        'challenge', 'reason', 'change',
        'metric_1_label', 'metric_1_value',
        'metric_2_label', 'metric_2_value',
        'metric_3_label', 'metric_3_value',
        'ringi_process',
        'pullquote', 'pullquote_speaker',
        'is_featured', 'is_hidden',
    );
    foreach ( $meta_keys as $key ) {
        register_post_meta( 'case_study', '_yumeho_case_' . $key, array(
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'auth_callback' => '__return_true', // 公開メタとして読み取り許可
        ) );
    }
}
add_action( 'init', 'yumeho_register_case_rest_meta' );

// アンダースコア始まりのメタも REST API で返すよう許可
add_filter( 'is_protected_meta', 'yumeho_allow_case_meta_rest', 10, 3 );
function yumeho_allow_case_meta_rest( $protected, $meta_key, $meta_type ) {
    if ( 'post' === $meta_type && 0 === strpos( $meta_key, '_yumeho_case_' ) ) {
        return false;
    }
    return $protected;
}

/* ==========================================================================
   6. Pricing Admin Page
   ========================================================================== */

function yumeho_pricing_admin_menu() {
    add_submenu_page(
        yumeho_site_admin_menu_slug(),
        'YUMEHO 共通価格参照',
        '価格参照（Rinascente編集）',
        'edit_pages',
        'yumeho-pricing',
        'yumeho_pricing_admin_page'
    );
}
add_action( 'admin_menu', 'yumeho_pricing_admin_menu' );

function yumeho_pricing_admin_page() {
    if ( ! current_user_can( 'edit_pages' ) ) return;

    $catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
    $ceiling_system  = $catalog_context['ceiling_system'];
    $stand_systems   = $catalog_context['stand_systems'];
    $pricing_items   = $catalog_context['by_pricing_key'];
    $product_master_admin_url = yumeho_related_site_url( 'corporate', '/wp-admin/edit.php?post_type=product_master' );
    ?>
    <div class="wrap">
        <h1>YUMEHO 共通価格参照</h1>
        <div class="notice notice-info inline">
            <p>YUMEHO の価格は、Rinascente 側の <strong>製品マスター</strong> で一元管理しています。YUMEHO のシミュレーション、価格ページ、API 見積はこの共通マスターを即時参照します。</p>
            <?php if ( $product_master_admin_url && '#' !== $product_master_admin_url ) : ?>
                <p><a href="<?php echo esc_url( $product_master_admin_url ); ?>" class="button button-primary">Rinascente の製品マスターを開く</a></p>
            <?php endif; ?>
        </div>

        <table class="widefat striped" style="max-width: 980px; margin-top: 20px;">
            <thead>
                <tr>
                    <th>区分</th>
                    <th>製品名</th>
                    <th>価格</th>
                    <th>備考</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( (array) $catalog_context['systems'] as $item ) : ?>
                    <tr>
                        <td>本体システム</td>
                        <td><?php echo esc_html( $item['display_name'] ); ?></td>
                        <td>
                            <?php
                            $labels = array();
                            if ( ! empty( $item['unit_price'] ) ) {
                                $labels[] = '¥' . number_format_i18n( (int) $item['unit_price'] );
                            }
                            if ( ! empty( $item['rail_price_per_m'] ) ) {
                                $labels[] = 'レール ¥' . number_format_i18n( (int) $item['rail_price_per_m'] ) . '/m';
                            }
                            echo esc_html( $labels ? implode( ' / ', $labels ) : '—' );
                            ?>
                        </td>
                        <td><?php echo esc_html( $item['spec'] ?: '—' ); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php foreach ( (array) $pricing_items as $item ) : ?>
                    <tr>
                        <td><?php echo esc_html( $item['category_label'] ?: 'その他' ); ?></td>
                        <td><?php echo esc_html( $item['display_name'] ); ?></td>
                        <td><?php echo esc_html( ! empty( $item['unit_price'] ) ? '¥' . number_format_i18n( (int) $item['unit_price'] ) : '—' ); ?></td>
                        <td><?php echo esc_html( $item['unit_label'] ? '単位: ' . $item['unit_label'] : '—' ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* ==========================================================================
   7. Customizer — Analytics
   ========================================================================== */

function yumeho_customizer_register( $wp_customize ) {
    // Analytics Section
    $wp_customize->add_section( 'yumeho_analytics', array(
        'title'    => 'アナリティクス設定',
        'priority' => 35,
    ) );

    $analytics_fields = array(
        'ga4_measurement_id' => 'GA4 Measurement ID',
        'gtm_container_id'   => 'GTM Container ID',
        'slack_webhook_url'  => 'Slack Webhook URL',
    );

    foreach ( $analytics_fields as $key => $label ) {
        $sanitize = 'slack_webhook_url' === $key ? 'esc_url_raw' : 'sanitize_text_field';
        $type     = 'slack_webhook_url' === $key ? 'password' : 'text';
        $wp_customize->add_setting( 'yumeho_' . $key, array(
            'default'           => '',
            'sanitize_callback' => $sanitize,
        ) );
        $wp_customize->add_control( 'yumeho_' . $key, array(
            'label'   => $label,
            'section' => 'yumeho_analytics',
            'type'    => $type,
        ) );
    }
}
add_action( 'customize_register', 'yumeho_customizer_register' );

/* ==========================================================================
   8. GA4 / GTM Insertion
   ========================================================================== */

function yumeho_insert_gtm_head() {
    if ( function_exists( 'yumeho_tracking_allowed' ) && ! yumeho_tracking_allowed() ) {
        return;
    }

    $gtm_id = get_theme_mod( 'yumeho_gtm_container_id' );
    if ( $gtm_id ) : ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js( $gtm_id ); ?>');</script>
<!-- End Google Tag Manager -->
    <?php endif;

    $ga4_id = get_theme_mod( 'yumeho_ga4_measurement_id' );
    if ( $ga4_id && ! $gtm_id ) : ?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4_id ); ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga4_id ); ?>');</script>
<!-- End GA4 -->
    <?php endif;
}
add_action( 'wp_head', 'yumeho_insert_gtm_head', 1 );

function yumeho_output_runtime_config() {
    printf(
        "<script>window.YUMEHO_API_BASE=%s;</script>\n",
        wp_json_encode( untrailingslashit( home_url( '/' ) ) )
    );
}
add_action( 'wp_head', 'yumeho_output_runtime_config', 2 );

function yumeho_insert_gtm_body() {
    if ( function_exists( 'yumeho_tracking_allowed' ) && ! yumeho_tracking_allowed() ) {
        return;
    }

    $gtm_id = get_theme_mod( 'yumeho_gtm_container_id' );
    if ( $gtm_id ) : ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <?php endif;
}
add_action( 'wp_body_open', 'yumeho_insert_gtm_body', 1 );

/* ==========================================================================
   9. Security Hardening
   ========================================================================== */

// Remove WordPress version
remove_action( 'wp_head', 'wp_generator' );

// Disable XML-RPC
add_filter( 'xmlrpc_enabled', '__return_false' );

// Block REST API user enumeration
function yumeho_block_rest_user_enum( $result, $server, $request ) {
    $route = $request->get_route();
    if ( preg_match( '/\/wp\/v2\/users/', $route ) && ! current_user_can( 'list_users' ) ) {
        return new WP_Error(
            'rest_forbidden',
            'User enumeration is disabled.',
            array( 'status' => 403 )
        );
    }
    return $result;
}
add_filter( 'rest_pre_dispatch', 'yumeho_block_rest_user_enum', 10, 3 );

// Login attempt limiter
function yumeho_login_attempt_limit() {
    $ip             = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key  = 'yumeho_login_attempts_' . md5( $ip );
    $attempts       = get_transient( $transient_key );

    if ( $attempts !== false && $attempts >= 5 ) {
        wp_die(
            'ログイン試行回数の上限に達しました。15分後に再度お試しください。',
            'Login Locked',
            array( 'response' => 429 )
        );
    }
}
add_action( 'wp_login_failed', function() {
    $ip            = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'yumeho_login_attempts_' . md5( $ip );
    $attempts      = get_transient( $transient_key );
    $attempts      = $attempts ? $attempts + 1 : 1;
    set_transient( $transient_key, $attempts, 15 * MINUTE_IN_SECONDS );
} );
add_action( 'wp_login', function() {
    $ip            = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'yumeho_login_attempts_' . md5( $ip );
    delete_transient( $transient_key );
} );
add_action( 'login_init', 'yumeho_login_attempt_limit' );

/* ==========================================================================
   10. Performance: Remove Emoji Scripts
   ========================================================================== */

function yumeho_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'yumeho_disable_emojis' );

/* ==========================================================================
   11. Wordfence Slack Notification
   ========================================================================== */

function yumeho_send_slack_security_alert( $message ) {
    $webhook_url = esc_url_raw( yumeho_theme_mod( 'slack_webhook_url' ) );
    if ( empty( $webhook_url ) ) return;

    $site_name = get_bloginfo( 'name' );
    $payload   = array(
        'text' => "[{$site_name}] Security Alert: {$message}",
    );

    wp_remote_post( $webhook_url, array(
        'body'               => wp_json_encode( $payload ),
        'headers'            => array( 'Content-Type' => 'application/json' ),
        'timeout'            => 5,
        'blocking'           => false,
        'sslverify'          => true,
        'reject_unsafe_urls' => true,
    ) );
}

add_action( 'wp_login_failed', function( $username ) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    yumeho_send_slack_security_alert( "ログイン失敗: ユーザー名 `{$username}` / IP: {$ip}" );
} );

add_action( 'wp_login', function( $user_login, $user ) {
    if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
        return;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    yumeho_send_slack_security_alert( "管理者ログイン: `{$user_login}` / IP: {$ip}" );
}, 10, 2 );

add_filter( 'wp_mail', function( $args ) {
    if ( isset( $args['subject'] ) && stripos( $args['subject'], 'wordfence' ) !== false ) {
        yumeho_send_slack_security_alert( "Wordfence通知: {$args['subject']}" );
    }
    return $args;
} );

add_action( 'upgrader_process_complete', function( $upgrader, $options ) {
    $type   = $options['type'] ?? 'unknown';
    $action = $options['action'] ?? 'unknown';
    yumeho_send_slack_security_alert( "更新完了: {$type} ({$action})" );
}, 10, 2 );

add_action( 'user_register', function( $user_id ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return;
    }
    yumeho_send_slack_security_alert( "新規ユーザー登録: `{$user->user_login}` ({$user->user_email})" );
} );

add_action( 'set_user_role', function( $user_id, $role, $old_roles ) {
    $user = get_userdata( $user_id );
    if ( ! $user ) {
        return;
    }
    yumeho_send_slack_security_alert( "権限変更: `{$user->user_login}` " . implode( ', ', (array) $old_roles ) . " → {$role}" );
}, 10, 3 );

// Wordfence hooks
add_action( 'wordfence_security_event', function( $event ) {
    yumeho_send_slack_security_alert( "Event: {$event}" );
} );

add_action( 'wordfence_blocked_ip', function( $ip ) {
    yumeho_send_slack_security_alert( "Blocked IP: {$ip}" );
} );

add_action( 'wordfence_login_lockout', function( $ip ) {
    yumeho_send_slack_security_alert( "Login lockout triggered for IP: {$ip}" );
} );

add_action( 'wordfence_scan_complete', function() {
    yumeho_send_slack_security_alert( 'Wordfence scan completed.' );
} );

add_action( 'wordfence_new_issues', function( $issues ) {
    $count = is_array( $issues ) ? count( $issues ) : 0;
    yumeho_send_slack_security_alert( "Wordfence found {$count} new issue(s)." );
} );

/* ==========================================================================
   12. Form Handler
   ========================================================================== */

if ( file_exists( YUMEHO_DIR . '/inc/class-form-handler.php' ) ) {
    require_once YUMEHO_DIR . '/inc/class-form-handler.php';
}

if ( file_exists( YUMEHO_DIR . '/inc/form-config-yumeho.php' ) ) {
    require_once YUMEHO_DIR . '/inc/form-config-yumeho.php';
}

if ( file_exists( YUMEHO_DIR . '/inc/roi-hourly-wage.php' ) ) {
    require_once YUMEHO_DIR . '/inc/roi-hourly-wage.php';
}

if ( file_exists( YUMEHO_DIR . '/inc/platform-support.php' ) ) {
    require_once YUMEHO_DIR . '/inc/platform-support.php';
}

if ( file_exists( YUMEHO_DIR . '/inc/installation-map.php' ) ) {
    require_once YUMEHO_DIR . '/inc/installation-map.php';
}

/* ==========================================================================
   13. Rank Math カスタム変数（会社情報を title / description で使用可能にする）
   ========================================================================== */
add_action( 'rank_math/vars/register_extra_replacements', 'yumeho_rankmath_vars' );
function yumeho_rankmath_vars() {
    rank_math_register_var_replacement(
        'company_name',
        array(
            'name'        => '会社名',
            'description' => 'カスタマイザーの会社名',
            'variable'    => 'company_name',
            'example'     => yumeho_theme_mod( 'company_name', '株式会社Rinascente' ),
        ),
        function() {
            return yumeho_theme_mod( 'company_name', '株式会社Rinascente' );
        }
    );

    rank_math_register_var_replacement(
        'company_tel',
        array(
            'name'        => '電話番号',
            'description' => 'カスタマイザーの電話番号',
            'variable'    => 'company_tel',
            'example'     => yumeho_theme_mod( 'company_tel', '0859-00-1234' ),
        ),
        function() {
            return yumeho_theme_mod( 'company_tel', '0859-00-1234' );
        }
    );

    rank_math_register_var_replacement(
        'company_address',
        array(
            'name'        => '所在地',
            'description' => 'カスタマイザーの所在地',
            'variable'    => 'company_address',
            'example'     => yumeho_theme_mod( 'company_address', '' ),
        ),
        function() {
            return yumeho_theme_mod( 'company_address', '' );
        }
    );

    rank_math_register_var_replacement(
        'company_hours',
        array(
            'name'        => '受付時間',
            'description' => 'カスタマイザーの受付時間',
            'variable'    => 'company_hours',
            'example'     => yumeho_theme_mod( 'company_hours', '平日 9:00〜17:00' ),
        ),
        function() {
            return yumeho_theme_mod( 'company_hours', '平日 9:00〜17:00' );
        }
    );
}


/* ==========================================================================
   14. Product Schema (Front Page)
   ========================================================================== */

function yumeho_product_schema() {
    if ( ! is_front_page() ) {
        return;
    }

    $product = function_exists( 'yumeho_schema_product_entity' )
        ? yumeho_schema_product_entity( 'YUMEHO（夢歩）', home_url( '/' ) )
        : array();
    $organization = function_exists( 'yumeho_schema_org' )
        ? yumeho_schema_org()
        : array();

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array_filter(
            array(
                $organization,
                $product,
            )
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'yumeho_product_schema' );

include_once YUMEHO_DIR . '/inc/structured-data-pages.php';

/* ==========================================================================
   14. WebP Upload Support
   ========================================================================== */

function yumeho_allow_webp_upload( $mimes ) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter( 'upload_mimes', 'yumeho_allow_webp_upload' );

function yumeho_webp_file_check( $data, $file, $filename, $mimes ) {
    $ext = pathinfo( $filename, PATHINFO_EXTENSION );
    if ( 'webp' === strtolower( $ext ) ) {
        $data['ext']  = 'webp';
        $data['type'] = 'image/webp';
    }
    return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'yumeho_webp_file_check', 10, 4 );

/* ==========================================================================
   15. Xserver SMTP Configuration (Placeholder)
   ========================================================================== */

function yumeho_smtp_config( $phpmailer ) {
    // Uncomment and configure for Xserver SMTP
    // $phpmailer->isSMTP();
    // $phpmailer->Host       = 'sv*****.xserver.jp';
    // $phpmailer->SMTPAuth   = true;
    // $phpmailer->Port       = 587;
    // $phpmailer->Username   = 'info@example.com';
    // $phpmailer->Password   = 'YOUR_PASSWORD_HERE';
    // $phpmailer->SMTPSecure = 'tls';
    // $phpmailer->From       = 'info@example.com';
    // $phpmailer->FromName   = 'YUMEHO';
}
add_action( 'phpmailer_init', 'yumeho_smtp_config' );
