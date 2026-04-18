<?php
/**
 * Installation site management and homepage map data.
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'yumeho_register_installation_site_post_type', 15 );
function yumeho_register_installation_site_post_type() {
    register_post_type(
        'installation_site',
        array(
            'labels' => array(
                'name'               => '導入拠点',
                'singular_name'      => '導入拠点',
                'add_new'            => '新規追加',
                'add_new_item'       => '導入拠点を追加',
                'edit_item'          => '導入拠点を編集',
                'new_item'           => '新しい導入拠点',
                'view_item'          => '導入拠点を表示',
                'search_items'       => '導入拠点を検索',
                'not_found'          => '導入拠点が見つかりません',
                'not_found_in_trash' => 'ゴミ箱に導入拠点はありません',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => function_exists( 'yumeho_site_admin_menu_slug' ) ? yumeho_site_admin_menu_slug() : true,
            'menu_icon'          => 'dashicons-location-alt',
            'menu_position'      => 23,
            'supports'           => array( 'title', 'page-attributes' ),
            'show_in_rest'       => false,
        )
    );
}

add_action( 'init', 'yumeho_register_installation_site_taxonomies', 25 );
function yumeho_register_installation_site_taxonomies() {
    register_taxonomy_for_object_type( 'product_type', 'installation_site' );
    register_taxonomy_for_object_type( 'facility_type', 'installation_site' );
}

add_action( 'init', 'yumeho_ensure_installation_product_terms', 30 );
function yumeho_ensure_installation_product_terms() {
    if ( ! term_exists( 'yumeho', 'product_type' ) ) {
        wp_insert_term(
            'YUMEHO',
            'product_type',
            array(
                'slug' => 'yumeho',
            )
        );
    }

    if ( function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled() && ! term_exists( 'mica30', 'product_type' ) ) {
        wp_insert_term(
            'MICA30',
            'product_type',
            array(
                'slug' => 'mica30',
            )
        );
    }
}

add_filter( 'use_block_editor_for_post_type', 'yumeho_installation_site_disable_block_editor', 10, 2 );
function yumeho_installation_site_disable_block_editor( $use_block_editor, $post_type ) {
    if ( 'installation_site' === $post_type ) {
        return false;
    }

    return $use_block_editor;
}

add_filter( 'enter_title_here', 'yumeho_installation_site_title_placeholder', 10, 2 );
function yumeho_installation_site_title_placeholder( $title, $post ) {
    if ( $post instanceof WP_Post && 'installation_site' === $post->post_type ) {
        return '例: ○○病院 リハビリテーション科';
    }

    return $title;
}

function yumeho_installation_site_admin_is_post_screen( $post = null ) {
    return $post instanceof WP_Post && 'installation_site' === $post->post_type;
}

function yumeho_installation_site_meta_key( $key ) {
    return '_yumeho_installation_' . $key;
}

function yumeho_installation_site_meta_value( $post_id, $key, $default = '' ) {
    $value = get_post_meta( $post_id, yumeho_installation_site_meta_key( $key ), true );

    if ( '' === $value && '' !== $default ) {
        return $default;
    }

    return $value;
}

function yumeho_installation_site_bool_value( $post_id, $key, $default = true ) {
    $value = get_post_meta( $post_id, yumeho_installation_site_meta_key( $key ), true );

    if ( '' === $value ) {
        return $default;
    }

    return '1' === $value;
}

function yumeho_installation_site_prefectures() {
    return array(
        '北海道' => '北海道',
        '青森県' => '青森県',
        '岩手県' => '岩手県',
        '宮城県' => '宮城県',
        '秋田県' => '秋田県',
        '山形県' => '山形県',
        '福島県' => '福島県',
        '茨城県' => '茨城県',
        '栃木県' => '栃木県',
        '群馬県' => '群馬県',
        '埼玉県' => '埼玉県',
        '千葉県' => '千葉県',
        '東京都' => '東京都',
        '神奈川県' => '神奈川県',
        '新潟県' => '新潟県',
        '富山県' => '富山県',
        '石川県' => '石川県',
        '福井県' => '福井県',
        '山梨県' => '山梨県',
        '長野県' => '長野県',
        '岐阜県' => '岐阜県',
        '静岡県' => '静岡県',
        '愛知県' => '愛知県',
        '三重県' => '三重県',
        '滋賀県' => '滋賀県',
        '京都府' => '京都府',
        '大阪府' => '大阪府',
        '兵庫県' => '兵庫県',
        '奈良県' => '奈良県',
        '和歌山県' => '和歌山県',
        '鳥取県' => '鳥取県',
        '島根県' => '島根県',
        '岡山県' => '岡山県',
        '広島県' => '広島県',
        '山口県' => '山口県',
        '徳島県' => '徳島県',
        '香川県' => '香川県',
        '愛媛県' => '愛媛県',
        '高知県' => '高知県',
        '福岡県' => '福岡県',
        '佐賀県' => '佐賀県',
        '長崎県' => '長崎県',
        '熊本県' => '熊本県',
        '大分県' => '大分県',
        '宮崎県' => '宮崎県',
        '鹿児島県' => '鹿児島県',
        '沖縄県' => '沖縄県',
    );
}

function yumeho_installation_site_prefecture_centers() {
    return array(
        '北海道' => array( 'lat' => 43.06417, 'lng' => 141.34694 ),
        '青森県' => array( 'lat' => 40.82444, 'lng' => 140.74 ),
        '岩手県' => array( 'lat' => 39.70361, 'lng' => 141.1525 ),
        '宮城県' => array( 'lat' => 38.26889, 'lng' => 140.87194 ),
        '秋田県' => array( 'lat' => 39.71861, 'lng' => 140.1025 ),
        '山形県' => array( 'lat' => 38.24056, 'lng' => 140.36333 ),
        '福島県' => array( 'lat' => 37.75, 'lng' => 140.46778 ),
        '茨城県' => array( 'lat' => 36.34139, 'lng' => 140.44667 ),
        '栃木県' => array( 'lat' => 36.56583, 'lng' => 139.88361 ),
        '群馬県' => array( 'lat' => 36.39111, 'lng' => 139.06083 ),
        '埼玉県' => array( 'lat' => 35.85694, 'lng' => 139.64889 ),
        '千葉県' => array( 'lat' => 35.60472, 'lng' => 140.12333 ),
        '東京都' => array( 'lat' => 35.68944, 'lng' => 139.69167 ),
        '神奈川県' => array( 'lat' => 35.44778, 'lng' => 139.6425 ),
        '新潟県' => array( 'lat' => 37.90222, 'lng' => 139.02361 ),
        '富山県' => array( 'lat' => 36.69528, 'lng' => 137.21139 ),
        '石川県' => array( 'lat' => 36.59444, 'lng' => 136.62556 ),
        '福井県' => array( 'lat' => 36.06528, 'lng' => 136.22194 ),
        '山梨県' => array( 'lat' => 35.66389, 'lng' => 138.56833 ),
        '長野県' => array( 'lat' => 36.65139, 'lng' => 138.18111 ),
        '岐阜県' => array( 'lat' => 35.39111, 'lng' => 136.72222 ),
        '静岡県' => array( 'lat' => 34.97694, 'lng' => 138.38306 ),
        '愛知県' => array( 'lat' => 35.18028, 'lng' => 136.90667 ),
        '三重県' => array( 'lat' => 34.73028, 'lng' => 136.50861 ),
        '滋賀県' => array( 'lat' => 35.00444, 'lng' => 135.86833 ),
        '京都府' => array( 'lat' => 35.02139, 'lng' => 135.75556 ),
        '大阪府' => array( 'lat' => 34.68639, 'lng' => 135.52 ),
        '兵庫県' => array( 'lat' => 34.69139, 'lng' => 135.18306 ),
        '奈良県' => array( 'lat' => 34.68528, 'lng' => 135.83278 ),
        '和歌山県' => array( 'lat' => 34.22611, 'lng' => 135.1675 ),
        '鳥取県' => array( 'lat' => 35.50361, 'lng' => 134.23833 ),
        '島根県' => array( 'lat' => 35.47222, 'lng' => 133.05056 ),
        '岡山県' => array( 'lat' => 34.66167, 'lng' => 133.935 ),
        '広島県' => array( 'lat' => 34.39639, 'lng' => 132.45944 ),
        '山口県' => array( 'lat' => 34.18583, 'lng' => 131.47139 ),
        '徳島県' => array( 'lat' => 34.06583, 'lng' => 134.55944 ),
        '香川県' => array( 'lat' => 34.34028, 'lng' => 134.04333 ),
        '愛媛県' => array( 'lat' => 33.84167, 'lng' => 132.76611 ),
        '高知県' => array( 'lat' => 33.55972, 'lng' => 133.53111 ),
        '福岡県' => array( 'lat' => 33.60639, 'lng' => 130.41806 ),
        '佐賀県' => array( 'lat' => 33.24944, 'lng' => 130.29889 ),
        '長崎県' => array( 'lat' => 32.74472, 'lng' => 129.87361 ),
        '熊本県' => array( 'lat' => 32.78972, 'lng' => 130.74167 ),
        '大分県' => array( 'lat' => 33.23806, 'lng' => 131.6125 ),
        '宮崎県' => array( 'lat' => 31.91111, 'lng' => 131.42389 ),
        '鹿児島県' => array( 'lat' => 31.56028, 'lng' => 130.55806 ),
        '沖縄県' => array( 'lat' => 26.2125, 'lng' => 127.68111 ),
    );
}

function yumeho_installation_site_map_bounds() {
    return array(
        'min_lat'  => 30.0,
        'max_lat'  => 45.8,
        'min_lng'  => 129.0,
        'max_lng'  => 146.2,
        'left_min' => 23.0,
        'left_max' => 86.0,
        'top_min'  => 8.0,
        'top_max'  => 86.0,
    );
}

function yumeho_installation_site_map_position( $lat, $lng ) {
    $lat    = (float) $lat;
    $lng    = (float) $lng;
    $bounds = yumeho_installation_site_map_bounds();

    if ( $lat < $bounds['min_lat'] || $lat > $bounds['max_lat'] || $lng < $bounds['min_lng'] || $lng > $bounds['max_lng'] ) {
        return array();
    }

    $left = $bounds['left_min'] + ( ( $lng - $bounds['min_lng'] ) / max( 0.0001, $bounds['max_lng'] - $bounds['min_lng'] ) ) * ( $bounds['left_max'] - $bounds['left_min'] );
    $top  = $bounds['top_max'] - ( ( $lat - $bounds['min_lat'] ) / max( 0.0001, $bounds['max_lat'] - $bounds['min_lat'] ) ) * ( $bounds['top_max'] - $bounds['top_min'] );

    // Static map image uses a slightly non-linear projection around mainland Japan.
    // Apply a calibrated transform in the common operating area and keep the
    // broader linear fallback for coordinates outside that envelope.
    if ( $lng >= 129.8 && $lng <= 140.5 && $lat >= 33.0 && $lat <= 36.8 ) {
        $dx   = $lng - 134.67508333333333;
        $dy   = $lat - 34.846133333333334;
        $dx2  = $dx * $dx;
        $dy2  = $dy * $dy;
        $left = 40.51290508558851
            + ( 4.849782351624457 * $dx )
            + ( -4.0217301976297275 * $dy )
            + ( 0.5598321554167801 * $dx * $dy )
            + ( -0.33790177995494997 * $dx2 )
            + ( 13.662573904281773 * $dy2 )
            + ( -0.38454236716051904 * $dx2 * $dx )
            + ( -5.675685136677112 * $dy2 * $dy )
            + ( 1.8754518753070677 * $dx2 * $dy )
            + ( 2.363691993879798 * $dx * $dy2 );
        $top  = 71.27890000144657
            + ( -1.3687241391051113 * $dx )
            + ( 2.286560760019974 * $dy )
            + ( -0.07049991885057394 * $dx * $dy )
            + ( -0.027560567318884902 * $dx2 )
            + ( -9.66015585415838 * $dy2 )
            + ( 0.7791714393793154 * $dx2 * $dx )
            + ( 20.32794347833596 * $dy2 * $dy )
            + ( -4.549613526295272 * $dx2 * $dy )
            + ( -2.35488785963291 * $dx * $dy2 );
    }

    $left = max( $bounds['left_min'], min( $bounds['left_max'], $left ) );
    $top  = max( $bounds['top_min'], min( $bounds['top_max'], $top ) );

    return array(
        'left' => round( $left, 2 ),
        'top'  => round( $top, 2 ),
    );
}

function yumeho_installation_site_google_maps_api_key() {
    return trim( (string) yumeho_theme_mod( 'google_maps_api_key', '' ) );
}

add_action( 'customize_register', 'yumeho_installation_site_customize_register', 20 );
function yumeho_installation_site_customize_register( $wp_customize ) {
    $section_id = 'yumeho_analytics';

    if ( ! $wp_customize->get_section( $section_id ) ) {
        $section_id = 'yumeho_installation_map';
        $wp_customize->add_section(
            $section_id,
            array(
                'title'    => '導入拠点マップ設定',
                'priority' => 36,
            )
        );
    }

    $wp_customize->add_setting(
        'yumeho_google_maps_api_key',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );

    $wp_customize->add_control(
        'yumeho_google_maps_api_key',
        array(
            'label'       => 'Google Maps API Key（導入拠点マップ用）',
            'description' => '未入力でも利用できます。未設定時は都道府県中心 + 自動分散でピンを表示します。',
            'section'     => $section_id,
            'type'        => 'text',
        )
    );
}

function yumeho_installation_site_geocode_address( $address, $prefecture = '' ) {
    $api_key = yumeho_installation_site_google_maps_api_key();
    $address = trim( (string) $address );

    if ( '' === $api_key || '' === $address ) {
        return array();
    }

    $query_address = $address;

    if ( '' !== $prefecture && false === mb_strpos( $address, $prefecture ) ) {
        $query_address = $prefecture . ' ' . $address;
    }

    $endpoint = add_query_arg(
        array(
            'address'  => $query_address,
            'region'   => 'jp',
            'language' => 'ja',
            'key'      => $api_key,
        ),
        'https://maps.googleapis.com/maps/api/geocode/json'
    );

    $response = wp_remote_get(
        $endpoint,
        array(
            'timeout' => 4,
        )
    );

    if ( is_wp_error( $response ) ) {
        return array();
    }

    $payload = json_decode( wp_remote_retrieve_body( $response ), true );
    $result  = is_array( $payload['results'] ?? null ) ? $payload['results'][0] ?? null : null;

    if ( ! is_array( $result ) || empty( $result['geometry']['location'] ) ) {
        return array();
    }

    return array(
        'lat'    => round( (float) $result['geometry']['location']['lat'], 6 ),
        'lng'    => round( (float) $result['geometry']['location']['lng'], 6 ),
        'source' => 'google_geocoding',
    );
}

function yumeho_installation_site_offset_coordinates( $lat, $lng, $seed ) {
    $hash    = abs( (int) sprintf( '%u', crc32( (string) $seed ) ) );
    $slot    = $hash % 12;
    $ring    = (int) floor( $slot / 4 ) + 1;
    $angle   = deg2rad( $hash % 360 );
    $lat     = (float) $lat;
    $lng     = (float) $lng;
    $radius  = 0.12 * $ring;
    $lng_fix = max( 0.45, cos( deg2rad( $lat ) ) );

    return array(
        'lat' => round( $lat + sin( $angle ) * ( $radius * 0.45 ), 6 ),
        'lng' => round( $lng + cos( $angle ) * ( $radius / $lng_fix ), 6 ),
    );
}

function yumeho_installation_site_resolve_coordinates( $post_id, $prefecture, $address, $manual_lat = '', $manual_lng = '' ) {
    $prefecture = trim( (string) $prefecture );
    $address    = trim( (string) $address );
    $manual_lat = trim( (string) $manual_lat );
    $manual_lng = trim( (string) $manual_lng );

    if ( '' !== $manual_lat && '' !== $manual_lng && is_numeric( $manual_lat ) && is_numeric( $manual_lng ) ) {
        return array(
            'lat'    => round( (float) $manual_lat, 6 ),
            'lng'    => round( (float) $manual_lng, 6 ),
            'source' => 'manual',
        );
    }

    $geocoded = yumeho_installation_site_geocode_address( $address, $prefecture );
    if ( ! empty( $geocoded['lat'] ) && ! empty( $geocoded['lng'] ) ) {
        return $geocoded;
    }

    $centers = yumeho_installation_site_prefecture_centers();
    if ( empty( $centers[ $prefecture ] ) ) {
        return array();
    }

    $offset = yumeho_installation_site_offset_coordinates(
        $centers[ $prefecture ]['lat'],
        $centers[ $prefecture ]['lng'],
        implode( '|', array( $post_id, $prefecture, $address ) )
    );

    return array(
        'lat'    => $offset['lat'],
        'lng'    => $offset['lng'],
        'source' => 'prefecture_offset',
    );
}

function yumeho_installation_site_coordinate_source_label( $source ) {
    $map = array(
        'manual'           => '手動指定',
        'google_geocoding' => '住所から自動取得',
        'prefecture_offset'=> '都道府県中心 + 自動分散',
    );

    return $map[ $source ] ?? '未設定';
}

function yumeho_refresh_installation_site_coordinates( $post_id ) {
    $prefecture = yumeho_installation_site_meta_value( $post_id, 'prefecture' );
    $address    = yumeho_installation_site_meta_value( $post_id, 'address' );
    $manual_lat = yumeho_installation_site_meta_value( $post_id, 'manual_lat' );
    $manual_lng = yumeho_installation_site_meta_value( $post_id, 'manual_lng' );
    $resolved   = yumeho_installation_site_resolve_coordinates( $post_id, $prefecture, $address, $manual_lat, $manual_lng );

    if ( empty( $resolved['lat'] ) || empty( $resolved['lng'] ) ) {
        delete_post_meta( $post_id, yumeho_installation_site_meta_key( 'lat' ) );
        delete_post_meta( $post_id, yumeho_installation_site_meta_key( 'lng' ) );
        update_post_meta( $post_id, yumeho_installation_site_meta_key( 'coord_source' ), 'none' );
        return;
    }

    update_post_meta( $post_id, yumeho_installation_site_meta_key( 'lat' ), $resolved['lat'] );
    update_post_meta( $post_id, yumeho_installation_site_meta_key( 'lng' ), $resolved['lng'] );
    update_post_meta( $post_id, yumeho_installation_site_meta_key( 'coord_source' ), sanitize_key( $resolved['source'] ?? 'prefecture_offset' ) );
}

add_action( 'add_meta_boxes', 'yumeho_installation_site_meta_boxes', 30 );
function yumeho_installation_site_meta_boxes() {
    add_meta_box(
        'yumeho_installation_site_settings',
        '導入拠点設定',
        'yumeho_installation_site_meta_box_html',
        'installation_site',
        'normal',
        'high'
    );

    add_meta_box(
        'yumeho_installation_site_checklist',
        '公開チェック',
        'yumeho_installation_site_checklist_meta_box_html',
        'installation_site',
        'side',
        'high'
    );

    remove_meta_box( 'product_typediv', 'installation_site', 'side' );
    remove_meta_box( 'facility_typediv', 'installation_site', 'side' );
    remove_meta_box( 'slugdiv', 'installation_site', 'normal' );
    remove_meta_box( 'commentstatusdiv', 'installation_site', 'normal' );
    remove_meta_box( 'commentsdiv', 'installation_site', 'normal' );
    remove_meta_box( 'trackbacksdiv', 'installation_site', 'normal' );
    remove_meta_box( 'authordiv', 'installation_site', 'normal' );
    remove_meta_box( 'revisionsdiv', 'installation_site', 'normal' );
    remove_meta_box( 'postcustom', 'installation_site', 'normal' );
    remove_meta_box( 'pageparentdiv', 'installation_site', 'side' );
}

function yumeho_installation_site_checklist_meta_box_html( $post ) {
    if ( ! yumeho_installation_site_admin_is_post_screen( $post ) ) {
        return;
    }

    $items = array(
        'title'      => array( 'label' => '施設名', 'required' => true ),
        'product'    => array( 'label' => '対象製品', 'required' => true ),
        'prefecture' => array( 'label' => '都道府県', 'required' => true ),
        'address'    => array( 'label' => '住所', 'required' => true ),
        'order'      => array( 'label' => '並び順', 'required' => true ),
        'show_map'   => array( 'label' => '地図表示', 'required' => false ),
        'stats'      => array( 'label' => '件数集計', 'required' => false ),
    );
    ?>
    <div class="yumeho-install-site-checklist">
        <p class="description" style="margin-top:0;">施設名、製品、都道府県、住所、並び順が入っていれば、地図と件数表示の元データとして使えます。</p>
        <ul class="yumeho-install-site-checklist__list">
            <?php foreach ( $items as $key => $item ) : ?>
                <li class="yumeho-install-site-checklist__item" data-check="<?php echo esc_attr( $key ); ?>" data-required="<?php echo ! empty( $item['required'] ) ? '1' : '0'; ?>">
                    <span class="yumeho-install-site-checklist__badge">未確認</span>
                    <span class="yumeho-install-site-checklist__label"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php if ( empty( $item['required'] ) ) : ?>
                        <span class="yumeho-install-site-checklist__note">任意</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

add_action( 'edit_form_after_title', 'yumeho_installation_site_after_title_fields' );
function yumeho_installation_site_after_title_fields( $post ) {
    if ( ! yumeho_installation_site_admin_is_post_screen( $post ) ) {
        return;
    }

    $menu_order     = (int) $post->menu_order;
    $coord_source   = yumeho_installation_site_meta_value( $post->ID, 'coord_source', 'none' );
    $show_on_map    = yumeho_installation_site_bool_value( $post->ID, 'show_on_map', true );
    $count_in_stats = yumeho_installation_site_bool_value( $post->ID, 'include_in_stats', true );
    ?>
    <div class="yumeho-install-site-top-fields">
        <div class="yumeho-install-site-top-fields__guide">
            <p class="yumeho-install-site-top-fields__lead">
                導入拠点は <strong>施設名</strong>、<strong>対象製品</strong>、<strong>都道府県</strong>、<strong>住所</strong> を入れれば、地図ピンと件数表示の元データになります。
                細かい位置ズレがあるときだけ、下で緯度経度を調整してください。
            </p>
            <div class="yumeho-install-site-top-fields__grid">
                <div class="yumeho-install-site-top-card">
                    <h4>公開までの流れ</h4>
                    <ol>
                        <li>施設名を入れる</li>
                        <li>対象製品を選ぶ</li>
                        <li>都道府県と住所を入れる</li>
                        <li>必要なら地図表示や件数集計を切り替える</li>
                        <li>位置ズレがあるときだけ手動調整する</li>
                    </ol>
                </div>
                <div class="yumeho-install-site-top-card yumeho-install-site-top-card--settings">
                    <h4>並び順</h4>
                    <label for="menu_order"><strong>並び順</strong></label>
                    <input type="number" name="menu_order" id="menu_order" value="<?php echo esc_attr( (string) $menu_order ); ?>" min="0" step="1">
                    <p class="description">一覧では小さい数字ほど上に表示されます。迷ったら `10, 20, 30...` と入れると、あとから差し込みやすくなります。</p>
                </div>
                <div class="yumeho-install-site-top-card yumeho-install-site-top-card--hint">
                    <h4>現在の扱い</h4>
                    <ul>
                        <li>地図表示: <?php echo esc_html( $show_on_map ? '表示する' : '表示しない' ); ?></li>
                        <li>件数集計: <?php echo esc_html( $count_in_stats ? '含める' : '含めない' ); ?></li>
                        <li>位置の決め方: <?php echo esc_html( yumeho_installation_site_coordinate_source_label( $coord_source ) ); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function yumeho_installation_site_meta_box_html( $post ) {
    wp_nonce_field( 'yumeho_installation_site_save', 'yumeho_installation_site_nonce' );

    $public_label   = yumeho_installation_site_meta_value( $post->ID, 'public_label' );
    $prefecture     = yumeho_installation_site_meta_value( $post->ID, 'prefecture' );
    $address        = yumeho_installation_site_meta_value( $post->ID, 'address' );
    $show_on_map    = yumeho_installation_site_bool_value( $post->ID, 'show_on_map', true );
    $count_in_stats = yumeho_installation_site_bool_value( $post->ID, 'include_in_stats', true );
    $manual_lat     = yumeho_installation_site_meta_value( $post->ID, 'manual_lat' );
    $manual_lng     = yumeho_installation_site_meta_value( $post->ID, 'manual_lng' );
    $resolved_lat   = yumeho_installation_site_meta_value( $post->ID, 'lat' );
    $resolved_lng   = yumeho_installation_site_meta_value( $post->ID, 'lng' );
    $coord_source   = yumeho_installation_site_meta_value( $post->ID, 'coord_source', 'none' );
    $product_terms  = get_terms(
        array(
            'taxonomy'   => 'product_type',
            'hide_empty' => false,
        )
    );
    $selected_terms = wp_get_post_terms( $post->ID, 'product_type', array( 'fields' => 'ids' ) );
    $selected_term  = ! empty( $selected_terms ) ? (int) $selected_terms[0] : 0;
    ?>
    <div class="yumeho-install-site-form">
        <div class="yumeho-install-site-note">
            <p class="yumeho-install-site-note__title">管理の考え方</p>
            <p class="yumeho-install-site-note__text">1施設ごとに1件登録します。住所と都道府県をもとに地図上の位置を計算し、細かいズレがある場合だけ下の緯度経度で微調整します。</p>
        </div>

        <div class="yumeho-install-site-grid">
            <div class="yumeho-install-site-row">
                <label for="yumeho_installation_public_label">公開用表示名（任意）</label>
                <input type="text" id="yumeho_installation_public_label" name="yumeho_installation_public_label" value="<?php echo esc_attr( $public_label ); ?>" class="widefat" placeholder="未入力なら施設名をそのまま使用">
                <span class="description">地図やポップアップに表示する短い名前です。</span>
            </div>

            <div class="yumeho-install-site-row">
                <label for="yumeho_installation_product_term">対象製品</label>
                <select id="yumeho_installation_product_term" name="yumeho_installation_product_term" class="widefat">
                    <option value="">— 選択してください —</option>
                    <?php foreach ( $product_terms as $term ) : ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected_term, (int) $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="yumeho-install-site-grid">
            <div class="yumeho-install-site-row">
                <label for="yumeho_installation_prefecture">都道府県</label>
                <select id="yumeho_installation_prefecture" name="yumeho_installation_prefecture" class="widefat">
                    <option value="">— 選択してください —</option>
                    <?php foreach ( yumeho_installation_site_prefectures() as $value => $label ) : ?>
                    <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $prefecture, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="yumeho-install-site-row">
                <label for="yumeho_installation_address">住所</label>
                <input type="text" id="yumeho_installation_address" name="yumeho_installation_address" value="<?php echo esc_attr( $address ); ?>" class="widefat" placeholder="例: 福岡市中央区○○ 1-2-3">
                <span class="description">住所をもとに位置を計算します。細かいズレは下の手動調整で補正できます。</span>
            </div>
        </div>

        <div class="yumeho-install-site-checks">
            <label class="yumeho-install-site-check">
                <input type="checkbox" name="yumeho_installation_show_on_map" value="1" <?php checked( $show_on_map ); ?>>
                地図に表示する
            </label>
            <label class="yumeho-install-site-check">
                <input type="checkbox" name="yumeho_installation_include_in_stats" value="1" <?php checked( $count_in_stats ); ?>>
                件数に含める
            </label>
        </div>

        <details class="yumeho-install-site-advanced">
            <summary>位置を手動で調整する（必要なときだけ）</summary>
            <div class="yumeho-install-site-grid" style="margin-top:14px;">
                <div class="yumeho-install-site-row">
                    <label for="yumeho_installation_manual_lat">緯度（任意）</label>
                    <input type="text" id="yumeho_installation_manual_lat" name="yumeho_installation_manual_lat" value="<?php echo esc_attr( $manual_lat ); ?>" class="widefat" placeholder="例: 35.6762">
                </div>
                <div class="yumeho-install-site-row">
                    <label for="yumeho_installation_manual_lng">経度（任意）</label>
                    <input type="text" id="yumeho_installation_manual_lng" name="yumeho_installation_manual_lng" value="<?php echo esc_attr( $manual_lng ); ?>" class="widefat" placeholder="例: 139.6503">
                </div>
            </div>
        </details>

        <div class="yumeho-install-site-status">
            <div class="yumeho-install-site-status__item">
                <span class="yumeho-install-site-status__label">現在の座標</span>
                <strong><?php echo esc_html( ( '' !== $resolved_lat && '' !== $resolved_lng ) ? $resolved_lat . ', ' . $resolved_lng : '未設定' ); ?></strong>
            </div>
            <div class="yumeho-install-site-status__item">
                <span class="yumeho-install-site-status__label">位置の決め方</span>
                <strong><?php echo esc_html( yumeho_installation_site_coordinate_source_label( $coord_source ) ); ?></strong>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'save_post_installation_site', 'yumeho_save_installation_site_meta' );
function yumeho_save_installation_site_meta( $post_id ) {
    if ( ! isset( $_POST['yumeho_installation_site_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yumeho_installation_site_nonce'] ) ), 'yumeho_installation_site_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $text_fields = array(
        'public_label' => isset( $_POST['yumeho_installation_public_label'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_installation_public_label'] ) ) : '',
        'prefecture'   => isset( $_POST['yumeho_installation_prefecture'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_installation_prefecture'] ) ) : '',
        'address'      => isset( $_POST['yumeho_installation_address'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_installation_address'] ) ) : '',
        'manual_lat'   => isset( $_POST['yumeho_installation_manual_lat'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_installation_manual_lat'] ) ) : '',
        'manual_lng'   => isset( $_POST['yumeho_installation_manual_lng'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_installation_manual_lng'] ) ) : '',
    );

    foreach ( $text_fields as $key => $value ) {
        update_post_meta( $post_id, yumeho_installation_site_meta_key( $key ), $value );
    }

    update_post_meta( $post_id, yumeho_installation_site_meta_key( 'show_on_map' ), isset( $_POST['yumeho_installation_show_on_map'] ) ? '1' : '' );
    update_post_meta( $post_id, yumeho_installation_site_meta_key( 'include_in_stats' ), isset( $_POST['yumeho_installation_include_in_stats'] ) ? '1' : '' );

    $product_term_id = isset( $_POST['yumeho_installation_product_term'] ) ? absint( wp_unslash( $_POST['yumeho_installation_product_term'] ) ) : 0;
    if ( $product_term_id > 0 ) {
        wp_set_post_terms( $post_id, array( $product_term_id ), 'product_type', false );
    } elseif ( $default_yumeho_term = get_term_by( 'slug', 'yumeho', 'product_type' ) ) {
        wp_set_post_terms( $post_id, array( (int) $default_yumeho_term->term_id ), 'product_type', false );
    }

    yumeho_refresh_installation_site_coordinates( $post_id );
}

add_action( 'admin_head-post.php', 'yumeho_installation_site_admin_css' );
add_action( 'admin_head-post-new.php', 'yumeho_installation_site_admin_css' );
function yumeho_installation_site_admin_css() {
    global $post;

    if ( ! yumeho_installation_site_admin_is_post_screen( $post ) ) {
        return;
    }
    ?>
    <style>
    .post-type-installation_site #post-body-content {
        margin-bottom: 0;
    }
    .yumeho-install-site-top-fields {
        margin: 18px 0 20px;
        display: grid;
        gap: 18px;
    }
    .yumeho-install-site-top-fields__guide {
        padding: 18px 20px;
        border: 1px solid #cfe0f2;
        border-radius: 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }
    .yumeho-install-site-top-fields__lead {
        margin: 0 0 14px;
        line-height: 1.9;
    }
    .yumeho-install-site-top-fields__grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }
    .yumeho-install-site-top-card {
        padding: 16px 18px;
        border: 1px solid #dcdcde;
        border-radius: 14px;
        background: #fff;
    }
    .yumeho-install-site-top-card h4 {
        margin: 0 0 10px;
    }
    .yumeho-install-site-top-card ol,
    .yumeho-install-site-top-card ul {
        margin: 0;
        padding-left: 18px;
        display: grid;
        gap: 6px;
    }
    .yumeho-install-site-top-card input[type="number"] {
        width: 140px;
        margin: 6px 0 8px;
    }
    .yumeho-install-site-top-card--settings {
        border-color: #b8d4f0;
        background: #f7fbff;
    }
    .yumeho-install-site-top-card--hint {
        border-color: #d6e7fb;
        background: #f9fcff;
    }
    .yumeho-install-site-form { display:grid; gap:18px; }
    .yumeho-install-site-note {
        padding: 16px 18px;
        border: 1px solid #cfe4f6;
        border-radius: 12px;
        background: linear-gradient(180deg, #f8fcff 0%, #eef7ff 100%);
    }
    .yumeho-install-site-note__title {
        margin: 0 0 6px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #0068b7;
    }
    .yumeho-install-site-note__text {
        margin: 0;
        font-size: 13px;
        line-height: 1.9;
        color: #334155;
    }
    .yumeho-install-site-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .yumeho-install-site-row label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
    }
    .yumeho-install-site-checks {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .yumeho-install-site-check {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border: 1px solid #dcdcde;
        border-radius: 999px;
        background: #fff;
    }
    .yumeho-install-site-advanced {
        border: 1px solid #dcdcde;
        border-radius: 12px;
        background: #fff;
        padding: 14px 16px;
    }
    .yumeho-install-site-advanced summary {
        cursor: pointer;
        font-weight: 700;
    }
    .yumeho-install-site-status {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }
    .yumeho-install-site-status__item {
        padding: 14px 16px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
    }
    .yumeho-install-site-status__label {
        display: block;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: #64748b;
        text-transform: uppercase;
    }
    .yumeho-install-site-checklist__list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 10px;
    }
    .yumeho-install-site-checklist__item {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .yumeho-install-site-checklist__badge {
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
    .yumeho-install-site-checklist__item.is-done .yumeho-install-site-checklist__badge {
        background: rgba(0, 104, 183, 0.12);
        color: #00538f;
    }
    .yumeho-install-site-checklist__label {
        font-weight: 600;
    }
    .yumeho-install-site-checklist__note {
        color: #64748b;
        font-size: 12px;
    }
    @media (max-width: 960px) {
        .yumeho-install-site-top-fields__grid,
        .yumeho-install-site-grid,
        .yumeho-install-site-status {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

add_action( 'admin_footer-post.php', 'yumeho_installation_site_admin_footer_script' );
add_action( 'admin_footer-post-new.php', 'yumeho_installation_site_admin_footer_script' );
function yumeho_installation_site_admin_footer_script() {
    global $post;

    if ( ! yumeho_installation_site_admin_is_post_screen( $post ) ) {
        return;
    }
    ?>
    <script>
    (function($){
        function hasSelectValue(selector) {
            return $.trim($(selector).val() || '') !== '';
        }

        function hasChecked(selector) {
            return $(selector).is(':checked');
        }

        function updateChecklist() {
            var checks = {
                title: $.trim($('#title').val() || '') !== '',
                product: hasSelectValue('#yumeho_installation_product_term'),
                prefecture: hasSelectValue('#yumeho_installation_prefecture'),
                address: $.trim($('#yumeho_installation_address').val() || '') !== '',
                order: $.trim($('#menu_order').val() || '') !== '',
                show_map: hasChecked('input[name="yumeho_installation_show_on_map"]'),
                stats: hasChecked('input[name="yumeho_installation_include_in_stats"]')
            };

            $.each(checks, function(key, done){
                var $item = $('.yumeho-install-site-checklist__item[data-check="' + key + '"]');
                $item.toggleClass('is-done', !!done);
                $item.find('.yumeho-install-site-checklist__badge').text(done ? '入力済み' : '未確認');
            });
        }

        $('#title, #yumeho_installation_address, #menu_order').on('input change', updateChecklist);
        $('#yumeho_installation_product_term, #yumeho_installation_prefecture').on('change', updateChecklist);
        $('input[name="yumeho_installation_show_on_map"], input[name="yumeho_installation_include_in_stats"]').on('change', updateChecklist);

        updateChecklist();
    })(jQuery);
    </script>
    <?php
}

add_filter( 'manage_installation_site_posts_columns', 'yumeho_installation_site_admin_columns' );
function yumeho_installation_site_admin_columns( $columns ) {
    return array(
        'cb'              => $columns['cb'] ?? '',
        'title'           => '施設名',
        'product_type'    => '対象製品',
        'prefecture'      => '都道府県',
        'map_state'       => '地図',
        'stats_state'     => '件数',
        'coord_source'    => '位置の決め方',
        'date'            => '更新日',
    );
}

add_action( 'manage_installation_site_posts_custom_column', 'yumeho_installation_site_admin_column_content', 10, 2 );
function yumeho_installation_site_admin_column_content( $column, $post_id ) {
    if ( 'product_type' === $column ) {
        $terms = wp_get_post_terms( $post_id, 'product_type', array( 'fields' => 'names' ) );
        echo ! empty( $terms ) ? esc_html( implode( ', ', $terms ) ) : '—';
        return;
    }

    if ( 'prefecture' === $column ) {
        echo esc_html( yumeho_installation_site_meta_value( $post_id, 'prefecture', '—' ) );
        return;
    }

    if ( 'map_state' === $column ) {
        echo yumeho_installation_site_bool_value( $post_id, 'show_on_map', true ) ? '<span style="color:#0a7a2f;font-weight:700;">表示</span>' : '<span style="color:#6b7280;">非表示</span>';
        return;
    }

    if ( 'stats_state' === $column ) {
        echo yumeho_installation_site_bool_value( $post_id, 'include_in_stats', true ) ? '<span style="color:#0a7a2f;font-weight:700;">含める</span>' : '<span style="color:#6b7280;">除外</span>';
        return;
    }

    if ( 'coord_source' === $column ) {
        echo esc_html( yumeho_installation_site_coordinate_source_label( yumeho_installation_site_meta_value( $post_id, 'coord_source', 'none' ) ) );
    }
}

add_action( 'restrict_manage_posts', 'yumeho_installation_site_admin_filters', 10, 2 );
function yumeho_installation_site_admin_filters( $post_type, $which ) {
    if ( 'installation_site' !== $post_type || 'top' !== $which ) {
        return;
    }

    $product_terms = get_terms(
        array(
            'taxonomy'   => 'product_type',
            'hide_empty' => false,
        )
    );
    ?>
    <select name="yumeho_installation_product_filter">
        <option value="">すべての製品</option>
        <?php foreach ( $product_terms as $term ) : ?>
        <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( isset( $_GET['yumeho_installation_product_filter'] ) ? wp_unslash( $_GET['yumeho_installation_product_filter'] ) : '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="yumeho_installation_prefecture_filter">
        <option value="">すべての都道府県</option>
        <?php foreach ( yumeho_installation_site_prefectures() as $value => $label ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( isset( $_GET['yumeho_installation_prefecture_filter'] ) ? wp_unslash( $_GET['yumeho_installation_prefecture_filter'] ) : '', $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}

add_action( 'pre_get_posts', 'yumeho_installation_site_admin_filter_query' );
function yumeho_installation_site_admin_filter_query( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() || 'installation_site' !== $query->get( 'post_type' ) ) {
        return;
    }

    if ( ! empty( $_GET['yumeho_installation_product_filter'] ) ) {
        $query->set(
            'tax_query',
            array(
                array(
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => sanitize_key( wp_unslash( $_GET['yumeho_installation_product_filter'] ) ),
                ),
            )
        );
    }

    if ( ! empty( $_GET['yumeho_installation_prefecture_filter'] ) ) {
        $meta_query   = (array) $query->get( 'meta_query' );
        $meta_query[] = array(
            'key'   => yumeho_installation_site_meta_key( 'prefecture' ),
            'value' => sanitize_text_field( wp_unslash( $_GET['yumeho_installation_prefecture_filter'] ) ),
        );
        $query->set( 'meta_query', $meta_query );
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

add_filter( 'posts_search', 'yumeho_installation_site_admin_search', 10, 2 );
function yumeho_installation_site_admin_search( $search, $query ) {
    if ( ! is_admin() || ! ( $query instanceof WP_Query ) || ! $query->is_main_query() || 'installation_site' !== $query->get( 'post_type' ) || ! $query->is_search() ) {
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

    foreach ( array( 'public_label', 'prefecture', 'address' ) as $meta_key ) {
        $meta_clauses[] = '(pm.meta_key = %s AND pm.meta_value LIKE %s)';
        $meta_values[]  = yumeho_installation_site_meta_key( $meta_key );
        $meta_values[]  = $like;
    }

    $meta_search = $wpdb->prepare(
        'EXISTS (SELECT 1 FROM ' . $wpdb->postmeta . ' pm WHERE pm.post_id = ' . $wpdb->posts . '.ID AND (' . implode( ' OR ', $meta_clauses ) . '))',
        $meta_values
    );

    return $wpdb->prepare(
        " AND (({$wpdb->posts}.post_title LIKE %s) OR {$meta_search}) ",
        $like
    );
}

add_action( 'admin_notices', 'yumeho_installation_site_admin_notices' );
function yumeho_installation_site_admin_notices() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-installation_site' !== $screen->id ) {
        return;
    }

    $counts  = wp_count_posts( 'installation_site' );
    $context = yumeho_installation_map_context( 'yumeho' );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;
    ?>
    <div class="notice yumeho-install-site-list-guide">
        <div class="yumeho-install-site-list-guide__stats">
            <span class="yumeho-install-site-list-guide__stat"><strong><?php echo esc_html( (string) $published ); ?></strong> 公開中</span>
            <span class="yumeho-install-site-list-guide__stat"><strong><?php echo esc_html( (string) $drafts ); ?></strong> 下書き</span>
            <span class="yumeho-install-site-list-guide__stat"><strong><?php echo esc_html( (string) (int) ( $context['site_count'] ?? 0 ) ); ?></strong> 件数集計中</span>
            <span class="yumeho-install-site-list-guide__stat"><strong><?php echo esc_html( (string) (int) ( $context['prefecture_count'] ?? 0 ) ); ?></strong> 設置エリア</span>
        </div>
        <p class="yumeho-install-site-list-guide__text">一覧では施設名、対象製品、都道府県、地図表示、件数集計、位置の決め方をまとめて確認できます。検索は施設名、公開名、都道府県、住所に対応しています。</p>
    </div>
    <?php
}

add_action( 'admin_head-edit.php', 'yumeho_installation_site_list_admin_css' );
function yumeho_installation_site_list_admin_css() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-installation_site' !== $screen->id ) {
        return;
    }
    ?>
    <style>
    .post-type-installation_site .wrap > h1.wp-heading-inline {
        margin-bottom: 6px;
    }
    .post-type-installation_site .subsubsub {
        margin: 6px 0 8px;
    }
    .post-type-installation_site .tablenav.top {
        margin: 8px 0 10px;
        min-height: 36px;
    }
    .post-type-installation_site .tablenav.bottom {
        margin-top: 10px;
    }
    .post-type-installation_site .tablenav .actions select,
    .post-type-installation_site .tablenav .button,
    .post-type-installation_site .search-box input[type="search"] {
        min-height: 36px;
        border-radius: 10px;
    }
    .post-type-installation_site .search-box input[type="search"] {
        min-width: 280px;
        padding-inline: 14px;
    }
    .post-type-installation_site .wp-list-table {
        border: 1px solid #dbe4ee;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.04);
    }
    .post-type-installation_site .wp-list-table thead th,
    .post-type-installation_site .wp-list-table tfoot th {
        background: #f8fbff;
    }
    .post-type-installation_site .wp-list-table tbody tr:hover {
        background: #fcfdff;
    }
    .post-type-installation_site .wp-list-table tbody td,
    .post-type-installation_site .wp-list-table tbody th {
        padding-top: 10px;
        padding-bottom: 10px;
        vertical-align: middle;
    }
    .post-type-installation_site .column-title { width: 26%; }
    .post-type-installation_site .column-product_type { width: 14%; }
    .post-type-installation_site .column-prefecture { width: 12%; }
    .post-type-installation_site .column-map_state,
    .post-type-installation_site .column-stats_state,
    .post-type-installation_site .column-coord_source { width: 11%; }
    .yumeho-install-site-list-guide {
        border: 1px solid #cfe0f2;
        border-left: 4px solid #0068b7;
        border-radius: 14px;
        padding: 10px 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        margin: 10px 0 8px;
    }
    .yumeho-install-site-list-guide__stats {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 6px;
    }
    .yumeho-install-site-list-guide__stat {
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
    .yumeho-install-site-list-guide__text {
        margin: 0;
        color: #334155;
        font-size: 12px;
        line-height: 1.65;
    }
    </style>
    <?php
}

add_action( 'admin_footer-edit.php', 'yumeho_installation_site_list_admin_footer_script' );
function yumeho_installation_site_list_admin_footer_script() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-installation_site' !== $screen->id ) {
        return;
    }
    ?>
    <script>
    (function($){
        var $search = $('#post-search-input');
        if ($search.length) {
            $search.attr('placeholder', '施設名・公開名・住所で検索');
        }
    })(jQuery);
    </script>
    <?php
}

function yumeho_installation_map_context( $product_slug = 'yumeho' ) {
    $query_args = array(
        'post_type'      => 'installation_site',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array(
            'menu_order' => 'ASC',
            'date'       => 'DESC',
        ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => sanitize_key( $product_slug ),
            ),
        ),
        'no_found_rows'  => true,
    );

    $posts             = get_posts( $query_args );
    $locations         = array();
    $counted_locations = array();

    foreach ( $posts as $post ) {
        $post_id        = $post->ID;
        $prefecture     = trim( (string) yumeho_installation_site_meta_value( $post_id, 'prefecture' ) );
        $address        = trim( (string) yumeho_installation_site_meta_value( $post_id, 'address' ) );
        $public_label   = trim( (string) yumeho_installation_site_meta_value( $post_id, 'public_label' ) );
        $show_on_map    = yumeho_installation_site_bool_value( $post_id, 'show_on_map', true );
        $count_in_stats = yumeho_installation_site_bool_value( $post_id, 'include_in_stats', true );
        $lat            = yumeho_installation_site_meta_value( $post_id, 'lat' );
        $lng            = yumeho_installation_site_meta_value( $post_id, 'lng' );
        $coord_source   = yumeho_installation_site_meta_value( $post_id, 'coord_source', 'none' );
        $display_name   = '' !== $public_label ? $public_label : get_the_title( $post_id );
        $map_position   = ( '' !== $lat && '' !== $lng ) ? yumeho_installation_site_map_position( $lat, $lng ) : array();

        $payload = array(
            'id'             => $post_id,
            'name'           => $display_name,
            'facility_name'  => get_the_title( $post_id ),
            'prefecture'     => $prefecture,
            'address'        => $address,
            'lat'            => '' !== $lat ? (float) $lat : null,
            'lng'            => '' !== $lng ? (float) $lng : null,
            'coord_source'   => $coord_source,
            'show_on_map'    => $show_on_map,
            'count_in_stats' => $count_in_stats,
            'map_left'       => isset( $map_position['left'] ) ? (float) $map_position['left'] : null,
            'map_top'        => isset( $map_position['top'] ) ? (float) $map_position['top'] : null,
            'info'           => trim( $prefecture . ' / ' . $display_name ),
        );

        if ( $show_on_map && null !== $payload['lat'] && null !== $payload['lng'] ) {
            $locations[] = $payload;
        }

        if ( $count_in_stats ) {
            $counted_locations[] = $payload;
        }
    }

    $prefectures = array_values(
        array_unique(
            array_filter(
                array_map(
                    static function( $item ) {
                        return trim( (string) ( $item['prefecture'] ?? '' ) );
                    },
                    $counted_locations
                )
            )
        )
    );

    return array(
        'locations'        => $locations,
        'site_count'       => count( $counted_locations ),
        'prefecture_count' => count( $prefectures ),
        'prefectures'      => $prefectures,
    );
}

function yumeho_installation_site_default_seed_rows() {
    return array(
        array( 'title' => '福岡リハビリテーション病院', 'prefecture' => '福岡県', 'address' => '福岡市中央区舞鶴1-1-1', 'public_label' => '福岡リハビリテーション病院' ),
        array( 'title' => '北九州ケアセンター', 'prefecture' => '福岡県', 'address' => '北九州市小倉北区浅野1-1-1', 'public_label' => '北九州ケアセンター' ),
        array( 'title' => '広島回復期リハセンター', 'prefecture' => '広島県', 'address' => '広島市中区基町1-1', 'public_label' => '広島回復期リハセンター' ),
        array( 'title' => '呉デイリハステーション', 'prefecture' => '広島県', 'address' => '呉市中央1-1-1', 'public_label' => '呉デイリハステーション' ),
        array( 'title' => '島根総合病院', 'prefecture' => '島根県', 'address' => '松江市殿町1', 'public_label' => '島根総合病院' ),
        array( 'title' => '松江リハサポートセンター', 'prefecture' => '島根県', 'address' => '松江市学園1-1-1', 'public_label' => '松江リハサポートセンター' ),
        array( 'title' => '大阪歩行リハクリニック', 'prefecture' => '大阪府', 'address' => '大阪市北区梅田1-1-1', 'public_label' => '大阪歩行リハクリニック' ),
        array( 'title' => '堺リハケアステーション', 'prefecture' => '大阪府', 'address' => '堺市堺区南瓦町1-1', 'public_label' => '堺リハケアステーション' ),
        array( 'title' => '名古屋中央病院', 'prefecture' => '愛知県', 'address' => '名古屋市中区栄1-1-1', 'public_label' => '名古屋中央病院' ),
        array( 'title' => '一宮デイリハセンター', 'prefecture' => '愛知県', 'address' => '一宮市本町1-1-1', 'public_label' => '一宮デイリハセンター' ),
        array( 'title' => '東京リハケア病院', 'prefecture' => '東京都', 'address' => '東京都千代田区丸の内1-1-1', 'public_label' => '東京リハケア病院' ),
        array( 'title' => '八王子ウェルネスセンター', 'prefecture' => '東京都', 'address' => '東京都八王子市旭町1-1', 'public_label' => '八王子ウェルネスセンター' ),
    );
}
