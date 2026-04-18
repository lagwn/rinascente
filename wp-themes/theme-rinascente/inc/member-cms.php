<?php
/**
 * Member CMS helpers for Rinascente.
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function rinascente_member_product_choices() {
    $choices = array(
        'yumeho' => 'YUMEHO',
    );

    if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) {
        $choices['mica30'] = 'MICA30';
    }

    if ( post_type_exists( 'product_master' ) ) {
        $product_master_ids = get_posts(
            array(
                'post_type'      => 'product_master',
                'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
                'posts_per_page' => -1,
                'fields'         => 'ids',
            )
        );

        foreach ( $product_master_ids as $product_master_id ) {
            $product_key = sanitize_key( get_post_meta( $product_master_id, '_rinascente_product_key', true ) );
            if ( '' === $product_key || isset( $choices[ $product_key ] ) ) {
                continue;
            }

            $choices[ $product_key ] = strtoupper( str_replace( array( '-', '_' ), ' ', $product_key ) );
        }
    }

    return (array) apply_filters( 'rinascente_member_product_choices', $choices );
}

function rinascente_member_hidden_product_keys() {
    $hidden = array();

    if ( ! function_exists( 'rinascente_mica30_enabled' ) || ! rinascente_mica30_enabled() ) {
        $hidden[] = 'mica30';
    }

    $hidden = array_map( 'sanitize_key', $hidden );
    $hidden = array_values( array_unique( array_filter( $hidden ) ) );

    return (array) apply_filters( 'rinascente_member_hidden_product_keys', $hidden );
}

function rinascente_member_visible_product_choices() {
    $choices = rinascente_member_product_choices();

    foreach ( rinascente_member_hidden_product_keys() as $product_key ) {
        unset( $choices[ $product_key ] );
    }

    return $choices;
}

function rinascente_member_product_label( $product_key ) {
    $product_key = sanitize_key( $product_key );
    if ( '' === $product_key ) {
        return '';
    }

    $choices = rinascente_member_product_choices();
    if ( isset( $choices[ $product_key ] ) ) {
        return $choices[ $product_key ];
    }

    return strtoupper( str_replace( array( '-', '_' ), ' ', $product_key ) );
}

function rinascente_member_product_master_category_choices() {
    return array(
        'system'    => '本体システム',
        'harness'   => 'ハーネス',
        'option'    => 'オプション',
        'kit'       => '周辺キット',
        'accessory' => 'アクセサリー',
        'other'     => 'その他',
    );
}

function rinascente_member_product_install_type_choices() {
    return array(
        ''        => '共通 / その他',
        'ceiling' => '天井直付型',
        'stand'   => 'スタンド型',
    );
}

function rinascente_member_product_selection_type_choices() {
    return array(
        'quantity' => '数量選択',
        'checkbox' => 'オン / オフ',
    );
}

function rinascente_member_sanitize_price( $value ) {
    $value = preg_replace( '/[^\d]/', '', (string) $value );
    if ( '' === $value ) {
        return 0;
    }

    return (int) $value;
}

function rinascente_member_parse_rail_length_options( $value ) {
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

function rinascente_member_format_rail_length_options( $value ) {
    return implode( ',', rinascente_member_parse_rail_length_options( $value ) );
}

function rinascente_member_product_master_seed_version() {
    return '2026-04-12-2';
}

function rinascente_member_yumeho_product_master_seed_data() {
    return array(
        array(
            'title'       => 'スタンド型 PGT-9000',
            'product_key' => 'yumeho',
            'category'    => 'system',
            'sort_order'  => 10,
            'code'        => 'pgt-9000',
            'display_name'=> 'スタンド型 PGT-9000',
            'short_name'  => 'PGT-9000',
            'spec'        => '2000×4000mm / 総レール長14m',
            'install_type'=> 'stand',
            'max_rail_length' => 14,
            'rail_length_options' => array( 14 ),
            'unit_price'  => 1150000,
            'rail_price_per_m' => 30000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'スタンド型本体 / レール4m / G-Suit ハーネス1着',
        ),
        array(
            'title'       => 'スタンド型 PGT-9001',
            'product_key' => 'yumeho',
            'category'    => 'system',
            'sort_order'  => 20,
            'code'        => 'pgt-9001',
            'display_name'=> 'スタンド型 PGT-9001',
            'short_name'  => 'PGT-9001',
            'spec'        => '2000×6000mm / 総レール長20m',
            'install_type'=> 'stand',
            'max_rail_length' => 20,
            'rail_length_options' => array( 20 ),
            'unit_price'  => 1150000,
            'rail_price_per_m' => 30000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'スタンド型本体 / レール6m / G-Suit ハーネス2着',
        ),
        array(
            'title'       => '天井直付型 FCW-3000',
            'product_key' => 'yumeho',
            'category'    => 'system',
            'sort_order'  => 30,
            'code'        => 'fcw-3000',
            'display_name'=> '天井直付型 FCW-3000',
            'short_name'  => 'FCW-3000',
            'spec'        => 'カスタム設計 / 周回・直線レール対応',
            'install_type'=> 'ceiling',
            'rail_length_options' => array( 5, 10, 20 ),
            'unit_price'  => 950000,
            'rail_price_per_m' => 30000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => '天井直付型本体 / レール構成一式 / G-Suit ハーネス2着',
        ),
        array(
            'title'       => 'G-Suit ハーネス',
            'product_key' => 'yumeho',
            'category'    => 'harness',
            'sort_order'  => 40,
            'code'        => 'g-suit',
            'display_name'=> 'G-Suit ハーネス',
            'short_name'  => 'G-Suit',
            'unit_label'  => '着',
            'max_quantity'=> 5,
            'unit_price'  => 0,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ',
            'contract_template' => 'G-Suit ハーネス 1着',
        ),
        array(
            'title'       => '追加ハーネス',
            'product_key' => 'yumeho',
            'category'    => 'harness',
            'sort_order'  => 50,
            'code'        => 'extra-harness',
            'display_name'=> '追加ハーネス',
            'short_name'  => '追加ハーネス',
            'pricing_option_key' => 'harness_extra',
            'unit_label'  => '着',
            'max_quantity'=> 5,
            'selection_type' => 'quantity',
            'unit_price'  => 200000,
            'source_note' => 'YUMEHO 価格ページ / API価格設定',
            'contract_template' => '追加ハーネス 1着',
        ),
        array(
            'title'       => 'JRX',
            'product_key' => 'yumeho',
            'category'    => 'option',
            'sort_order'  => 60,
            'code'        => 'jrx',
            'display_name'=> 'JRX（Junction Rail eXpress）方向転換システム',
            'short_name'  => 'JRX',
            'pricing_option_key' => 'jrx',
            'unit_label'  => '台',
            'max_quantity'=> 1,
            'selection_type' => 'checkbox',
            'unit_price'  => 350000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'JRX 方向転換システム',
        ),
        array(
            'title'       => 'T-Pulling',
            'product_key' => 'yumeho',
            'category'    => 'option',
            'sort_order'  => 70,
            'code'        => 't-pulling',
            'display_name'=> 'T-Pulling（プーリングシステム）',
            'short_name'  => 'T-Pulling',
            'pricing_option_key' => 'pulling',
            'unit_label'  => '台',
            'max_quantity'=> 5,
            'selection_type' => 'quantity',
            'unit_price'  => 300000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'T-Pulling オプション',
        ),
        array(
            'title'       => 'T-Sling',
            'product_key' => 'yumeho',
            'category'    => 'option',
            'sort_order'  => 80,
            'code'        => 't-sling',
            'display_name'=> 'T-Sling（スリングシステム）',
            'short_name'  => 'T-Sling',
            'pricing_option_key' => 'sling',
            'unit_label'  => '台',
            'max_quantity'=> 5,
            'selection_type' => 'quantity',
            'unit_price'  => 250000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'T-Sling オプション',
        ),
        array(
            'title'       => 'G-Cord',
            'product_key' => 'yumeho',
            'category'    => 'option',
            'sort_order'  => 90,
            'code'        => 'g-cord',
            'display_name'=> 'G-Cord（自動高さ調整）',
            'short_name'  => 'G-Cord',
            'pricing_option_key' => 'gcord',
            'unit_label'  => '台',
            'max_quantity'=> 5,
            'selection_type' => 'quantity',
            'unit_price'  => 280000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'G-Cord オプション',
        ),
        array(
            'title'       => 'SnG',
            'product_key' => 'yumeho',
            'category'    => 'option',
            'sort_order'  => 100,
            'code'        => 'sng',
            'display_name'=> 'SnG（ロック機構）',
            'short_name'  => 'SnG',
            'pricing_option_key' => 'sng',
            'unit_label'  => '台',
            'max_quantity'=> 5,
            'selection_type' => 'quantity',
            'unit_price'  => 150000,
            'source_note' => 'YUMEHO 商品ページ / 価格ページ / シミュレーション',
            'contract_template' => 'SnG オプション',
        ),
        array(
            'title'       => '歩行データ計測キット',
            'product_key' => 'yumeho',
            'category'    => 'kit',
            'sort_order'  => 110,
            'code'        => 'walk-data-kit',
            'display_name'=> '歩行データ計測キット（PC連携）',
            'short_name'  => '歩行データ計測キット',
            'pricing_option_key' => 'measure',
            'unit_label'  => '台',
            'max_quantity'=> 1,
            'selection_type' => 'quantity',
            'unit_price'  => 200000,
            'source_note' => 'YUMEHO 商品ページ / シミュレーション / 補助金ページ',
            'contract_template' => '歩行データ計測キット（PC連携）',
        ),
    );
}

function rinascente_member_normalize_product_name( $name ) {
    $name = trim( (string) $name );
    if ( '' === $name ) {
        return '';
    }

    if ( function_exists( 'mb_strtolower' ) ) {
        $name = mb_strtolower( $name, 'UTF-8' );
    } else {
        $name = strtolower( $name );
    }

    $name = str_replace(
        array(
            'yumeho',
            'mica30',
            '（',
            '）',
            '(',
            ')',
            '[',
            ']',
            '［',
            '］',
            '/',
            '／',
            '　',
            ' ',
            '・',
            '＋',
            '+',
            ',',
            '.',
        ),
        '',
        $name
    );

    return $name;
}

function rinascente_member_get_product_master_posts( $args = array() ) {
    $defaults = array(
        'post_status' => array( 'publish' ),
        'product_key' => '',
    );
    $args     = wp_parse_args( $args, $defaults );

    $query_args = array(
        'post_type'      => 'product_master',
        'post_status'    => (array) $args['post_status'],
        'posts_per_page' => -1,
        'orderby'        => array(
            'meta_value_num' => 'ASC',
            'title'          => 'ASC',
        ),
        'meta_key'       => '_rinascente_product_catalog_sort_order',
        'order'          => 'ASC',
    );

    $meta_query = array();

    if ( '' !== $args['product_key'] ) {
        $meta_query[] = array(
            'key'   => '_rinascente_product_key',
            'value' => sanitize_key( $args['product_key'] ),
        );
    }

    if ( function_exists( 'rinascente_mica30_enabled' ) && ! rinascente_mica30_enabled() && '' === $args['product_key'] ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_rinascente_product_key',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => '_rinascente_product_key',
                'value'   => 'mica30',
                'compare' => '!=',
            ),
        );
    }

    if ( ! empty( $meta_query ) ) {
        if ( 1 === count( $meta_query ) ) {
            $query_args['meta_query'] = $meta_query[0];
        } else {
            $query_args['meta_query'] = array_merge( array( 'relation' => 'AND' ), $meta_query );
        }
    }

    return get_posts( $query_args );
}

function rinascente_member_find_product_master( $product_name, $product_key = '' ) {
    $normalized_name = rinascente_member_normalize_product_name( $product_name );
    if ( '' === $normalized_name ) {
        return null;
    }

    $candidates = rinascente_member_get_product_master_posts(
        array(
            'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future' ),
            'product_key' => $product_key,
        )
    );

    if ( empty( $candidates ) && '' !== $product_key ) {
        $candidates = rinascente_member_get_product_master_posts(
            array(
                'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future' ),
            )
        );
    }

    foreach ( $candidates as $candidate ) {
        $candidate_name = rinascente_member_normalize_product_name( $candidate->post_title );
        if ( $candidate_name === $normalized_name ) {
            return $candidate;
        }

        if ( false !== strpos( $candidate_name, $normalized_name ) || false !== strpos( $normalized_name, $candidate_name ) ) {
            return $candidate;
        }
    }

    return null;
}

function rinascente_member_upsert_product_master( $args ) {
    $defaults = array(
        'title'       => '',
        'product_key' => '',
        'category'    => 'other',
        'sort_order'  => 999,
        'code'        => '',
        'display_name'=> '',
        'short_name'  => '',
        'spec'        => '',
        'install_type'=> '',
        'max_rail_length' => 0,
        'unit_price'  => 0,
        'rail_price_per_m' => 0,
        'pricing_option_key' => '',
        'unit_label'  => '',
        'max_quantity'=> 0,
        'selection_type' => 'quantity',
        'source_note' => '',
        'contract_template' => '',
        'post_status' => 'publish',
    );
    $args     = wp_parse_args( $args, $defaults );

    $title = sanitize_text_field( $args['title'] );
    if ( '' === $title ) {
        return 0;
    }

    $product_key = sanitize_key( $args['product_key'] );
    $existing    = rinascente_member_find_product_master( $title, $product_key );

    if ( $existing ) {
        $post_id = (int) $existing->ID;
        wp_update_post(
            array(
                'ID'          => $post_id,
                'post_status' => $args['post_status'],
            )
        );
    } else {
        $post_id = wp_insert_post(
            array(
                'post_type'   => 'product_master',
                'post_status' => $args['post_status'],
                'post_title'  => $title,
            )
        );
    }

    if ( ! $post_id || is_wp_error( $post_id ) ) {
        return 0;
    }

    update_post_meta( $post_id, '_rinascente_product_key', $product_key );
    update_post_meta( $post_id, '_rinascente_product_catalog_category', sanitize_key( $args['category'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_sort_order', (int) $args['sort_order'] );
    update_post_meta( $post_id, '_rinascente_product_catalog_code', sanitize_key( $args['code'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_display_name', sanitize_text_field( $args['display_name'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_short_name', sanitize_text_field( $args['short_name'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_spec', sanitize_text_field( $args['spec'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_install_type', sanitize_key( $args['install_type'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_max_rail_length', (int) $args['max_rail_length'] );
    update_post_meta( $post_id, '_rinascente_product_catalog_rail_length_options', rinascente_member_format_rail_length_options( $args['rail_length_options'] ?? array() ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_unit_price', rinascente_member_sanitize_price( $args['unit_price'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_rail_price_per_m', rinascente_member_sanitize_price( $args['rail_price_per_m'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_pricing_option_key', sanitize_key( $args['pricing_option_key'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_unit_label', sanitize_text_field( $args['unit_label'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_max_quantity', (int) $args['max_quantity'] );
    update_post_meta( $post_id, '_rinascente_product_catalog_selection_type', sanitize_key( $args['selection_type'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_source', sanitize_textarea_field( $args['source_note'] ) );
    update_post_meta( $post_id, '_rinascente_product_catalog_contract_template', sanitize_textarea_field( $args['contract_template'] ) );

    return (int) $post_id;
}

function rinascente_member_product_catalog_item_data( $post ) {
    $post = get_post( $post );
    if ( ! $post instanceof WP_Post || 'product_master' !== $post->post_type ) {
        return array();
    }

    $product_key = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_key', true ) );
    $category    = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_catalog_category', true ) );
    $sort_order  = absint( get_post_meta( $post->ID, '_rinascente_product_catalog_sort_order', true ) );
    $code        = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_catalog_code', true ) );
    $display_name = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_display_name', true ) );
    $short_name  = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_short_name', true ) );
    $spec        = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_spec', true ) );
    $install_type = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_catalog_install_type', true ) );
    $max_rail_length = absint( get_post_meta( $post->ID, '_rinascente_product_catalog_max_rail_length', true ) );
    $rail_length_options = rinascente_member_parse_rail_length_options( get_post_meta( $post->ID, '_rinascente_product_catalog_rail_length_options', true ) );
    $unit_price  = rinascente_member_sanitize_price( get_post_meta( $post->ID, '_rinascente_product_catalog_unit_price', true ) );
    $rail_price_per_m = rinascente_member_sanitize_price( get_post_meta( $post->ID, '_rinascente_product_catalog_rail_price_per_m', true ) );
    $pricing_option_key = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_catalog_pricing_option_key', true ) );
    $unit_label  = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_unit_label', true ) );
    $max_quantity = absint( get_post_meta( $post->ID, '_rinascente_product_catalog_max_quantity', true ) );
    $selection_type = sanitize_key( get_post_meta( $post->ID, '_rinascente_product_catalog_selection_type', true ) );
    $source_note = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_source', true ) );
    $contract_template = trim( (string) get_post_meta( $post->ID, '_rinascente_product_catalog_contract_template', true ) );

    if ( '' === $display_name ) {
        $display_name = $post->post_title;
    }

    if ( '' === $short_name ) {
        $short_name = $post->post_title;
    }

    if ( '' === $unit_label ) {
        $unit_label = 'harness' === $category ? '着' : '台';
    }

    if ( $max_quantity < 1 ) {
        $max_quantity = 'checkbox' === $selection_type ? 1 : 5;
    }

    if ( '' === $selection_type ) {
        $selection_type = 'quantity';
    }

    return array(
        'id'                 => (int) $post->ID,
        'title'              => (string) $post->post_title,
        'slug'               => (string) $post->post_name,
        'product_key'        => $product_key,
        'product_label'      => rinascente_member_product_label( $product_key ),
        'category'           => $category,
        'category_label'     => rinascente_member_product_master_category_choices()[ $category ] ?? 'その他',
        'sort_order'         => $sort_order ? $sort_order : 999,
        'code'               => $code,
        'display_name'       => $display_name,
        'short_name'         => $short_name,
        'spec'               => $spec,
        'install_type'       => $install_type,
        'install_type_label' => rinascente_member_product_install_type_choices()[ $install_type ] ?? '',
        'max_rail_length'    => $max_rail_length,
        'rail_length_options' => $rail_length_options,
        'unit_price'         => $unit_price,
        'rail_price_per_m'   => $rail_price_per_m,
        'pricing_option_key' => $pricing_option_key,
        'unit_label'         => $unit_label,
        'max_quantity'       => $max_quantity,
        'selection_type'     => $selection_type,
        'selection_type_label' => rinascente_member_product_selection_type_choices()[ $selection_type ] ?? '数量選択',
        'contract_template'  => $contract_template,
        'source_note'        => $source_note,
    );
}

function rinascente_member_get_product_catalog_items( $args = array() ) {
    $defaults = array(
        'post_status' => array( 'publish' ),
        'product_key' => '',
        'category'    => array(),
    );
    $args = wp_parse_args( $args, $defaults );

    $categories = array_filter( array_map( 'sanitize_key', (array) $args['category'] ) );
    $posts      = rinascente_member_get_product_master_posts(
        array(
            'post_status' => (array) $args['post_status'],
            'product_key' => sanitize_key( $args['product_key'] ),
        )
    );

    $items = array();
    foreach ( $posts as $post ) {
        $item = rinascente_member_product_catalog_item_data( $post );
        if ( empty( $item ) ) {
            continue;
        }

        if ( ! empty( $categories ) && ! in_array( $item['category'], $categories, true ) ) {
            continue;
        }

        $items[] = $item;
    }

    return $items;
}

function rinascente_contract_status_choices() {
    return array(
        'ordered'   => '受注済み',
        'delivered' => '納品済み',
        'scheduled' => '納品予定',
        'support'   => '保守予定',
        'cancelled' => 'キャンセル',
    );
}

function rinascente_contract_payment_choices() {
    return array(
        'pending'  => '未決済',
        'paid'     => '支払済み',
        'partial'  => '一部入金',
        'refunded' => '返金済み',
    );
}

function rinascente_member_normalize_order_number( $order_number ) {
    $order_number = trim( (string) $order_number );
    if ( '' === $order_number ) {
        return '';
    }

    if ( function_exists( 'mb_strtolower' ) ) {
        $order_number = mb_strtolower( $order_number, 'UTF-8' );
    } else {
        $order_number = strtolower( $order_number );
    }

    return preg_replace( '/[\s　\-ー_]+/u', '', $order_number );
}

function rinascente_member_video_category_choices() {
    return array(
        'setup'   => '設置方法',
        'usage'   => '利用方法',
        'seminar' => 'セミナー',
        'support' => 'サポート',
    );
}

function rinascente_extract_youtube_video_id( $value ) {
    $value = trim( wp_strip_all_tags( (string) $value ) );
    if ( '' === $value ) {
        return '';
    }

    if ( preg_match( '/^[A-Za-z0-9_-]{11}$/', $value ) ) {
        return $value;
    }

    $patterns = array(
        '/(?:youtube\.com\/watch\?v=|youtube\.com\/watch\?.*?&v=)([A-Za-z0-9_-]{11})/i',
        '/youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i',
        '/youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/i',
        '/youtube\.com\/live\/([A-Za-z0-9_-]{11})/i',
        '/youtu\.be\/([A-Za-z0-9_-]{11})/i',
    );

    foreach ( $patterns as $pattern ) {
        if ( preg_match( $pattern, $value, $matches ) ) {
            return $matches[1];
        }
    }

    return sanitize_text_field( $value );
}

function rinascente_member_youtube_video_state( $value ) {
    $video_id = rinascente_extract_youtube_video_id( $value );

    $state = array(
        'video_id'      => $video_id,
        'is_available'  => false,
        'thumbnail_url' => '',
        'watch_url'     => '',
        'embed_url'     => '',
    );

    if ( '' === $video_id || ! preg_match( '/^[A-Za-z0-9_-]{11}$/', $video_id ) ) {
        return $state;
    }

    $state['watch_url'] = 'https://www.youtube.com/watch?v=' . rawurlencode( $video_id );
    $state['embed_url'] = 'https://www.youtube.com/embed/' . rawurlencode( $video_id );

    $cache_key = 'rinascente_yt_state_' . md5( $video_id );
    $cached    = get_transient( $cache_key );
    if ( is_array( $cached ) ) {
        return array_merge( $state, $cached );
    }

    $response = wp_remote_get(
        add_query_arg(
            array(
                'url'    => $state['watch_url'],
                'format' => 'json',
            ),
            'https://www.youtube.com/oembed'
        ),
        array(
            'timeout'            => 5,
            'redirection'        => 2,
            'reject_unsafe_urls' => true,
            'user-agent'         => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url( '/' ),
        )
    );

    if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
        $data = json_decode( (string) wp_remote_retrieve_body( $response ), true );
        if ( is_array( $data ) ) {
            $state['is_available'] = true;
            $state['thumbnail_url'] = ! empty( $data['thumbnail_url'] )
                ? esc_url_raw( (string) $data['thumbnail_url'] )
                : 'https://i.ytimg.com/vi/' . rawurlencode( $video_id ) . '/hqdefault.jpg';
        }
    }

    set_transient( $cache_key, $state, $state['is_available'] ? DAY_IN_SECONDS : HOUR_IN_SECONDS );

    return $state;
}

function rinascente_member_reviews_enabled() {
    return defined( 'RINASCENTE_ENABLE_MEMBER_REVIEWS' ) && RINASCENTE_ENABLE_MEMBER_REVIEWS;
}

function rinascente_member_notice_tone_choices() {
    return array(
        'maintenance' => '保守・運用',
        'urgent'      => '重要',
        'info'        => 'お知らせ',
        'neutral'     => '一般',
    );
}

function rinascente_member_notice_tone_class( $tone ) {
    $map = array(
        'maintenance' => 'maintenance',
        'urgent'      => 'urgent',
        'info'        => 'info',
        'neutral'     => 'neutral',
    );

    return $map[ $tone ] ?? 'maintenance';
}

function rinascente_member_document_category_choices() {
    return array(
        'spec'     => '仕様書',
        'guide'    => '運用ガイド',
        'cost'     => 'コスト計算',
        'proposal' => '稟議資料',
        'manual'   => '取扱説明書',
        'subsidy'  => '補助金サポート',
    );
}

function rinascente_member_document_category_guides() {
    return array(
        'spec'     => '製品仕様や型番確認で参照する資料向けです。版の差し替えが分かるように更新日も入れておくと運用しやすくなります。',
        'guide'    => '日々の運用手順や現場向けの案内資料に向いています。会員が迷わないよう、資料名は用途が伝わる表現がおすすめです。',
        'cost'     => '費用比較や見積もり検討で使う資料向けです。対象製品を絞ると、会員ページで探しやすくなります。',
        'proposal' => '稟議や導入検討に使う提案資料向けです。新旧の差し替えが多いので、更新日を揃えておくと確認しやすくなります。',
        'manual'   => '操作説明書や設置手順書向けです。公開期間を空欄にして常時参照できる状態にしておく運用が向いています。',
        'subsidy'  => '補助金・助成金まわりの資料向けです。制度名や認定ステータスも入れておくと、あとから差し替えるときに迷いません。',
    );
}

function rinascente_member_review_period_choices() {
    return array(
        '1_month'  => '1ヶ月以内',
        '3_months' => '1〜3ヶ月',
        '6_months' => '3〜6ヶ月',
        '1_year'   => '6ヶ月〜1年',
        'over_1y'  => '1年以上',
    );
}

function rinascente_member_status_badge_class( $status ) {
    $map = array(
        'delivered' => 'status-delivered',
        'support'   => 'status-support',
        'scheduled' => 'status-processing',
        'ordered'   => 'status-processing',
        'cancelled' => 'status-cancelled',
    );

    return $map[ $status ] ?? 'status-processing';
}

function rinascente_member_star_string( $rating ) {
    $rating = max( 1, min( 5, (int) $rating ) );
    return str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
}

function rinascente_member_format_date( $value ) {
    $value = trim( (string) $value );
    if ( '' === $value ) {
        return '—';
    }

    $timestamp = strtotime( $value );
    if ( false === $timestamp ) {
        return $value;
    }

    return wp_date( 'Y.m.d', $timestamp );
}

function rinascente_member_format_datetime( $value ) {
    $value = trim( (string) $value );
    if ( '' === $value ) {
        return '';
    }

    $timestamp = strtotime( $value );
    if ( false === $timestamp ) {
        return $value;
    }

    return wp_date( 'Y.m.d', $timestamp );
}

function rinascente_member_format_file_size( $bytes ) {
    $bytes = (int) $bytes;
    if ( $bytes <= 0 ) {
        return '';
    }

    $units = array( 'B', 'KB', 'MB', 'GB' );
    $power = min( (int) floor( log( $bytes, 1024 ) ), count( $units ) - 1 );
    $size  = $bytes / pow( 1024, $power );

    return sprintf( '%s%s', number_format_i18n( $size, $power > 0 ? 1 : 0 ), $units[ $power ] );
}

function rinascente_member_get_users() {
    return get_users(
        array(
            'role__in' => array( 'facility_member', 'administrator' ),
            'orderby'  => 'display_name',
            'order'    => 'ASC',
        )
    );
}

function rinascente_member_get_user_products( $user_id ) {
    $raw_products = get_user_meta( $user_id, '_rinascente_member_products', true );
    $products     = array();

    if ( is_array( $raw_products ) ) {
        $products = $raw_products;
    } elseif ( is_string( $raw_products ) && '' !== $raw_products ) {
        $products = array_map( 'trim', explode( ',', $raw_products ) );
    }

    $contract_ids = get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'private' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'   => '_rinascente_member_user_id',
                    'value' => (string) $user_id,
                ),
            ),
        )
    );

    foreach ( $contract_ids as $contract_id ) {
        $product_key = get_post_meta( $contract_id, '_rinascente_product_key', true );
        if ( $product_key ) {
            $products[] = $product_key;
        }

        $terms = wp_get_post_terms( $contract_id, 'product_type', array( 'fields' => 'slugs' ) );
        if ( ! is_wp_error( $terms ) ) {
            $products = array_merge( $products, $terms );
        }
    }

    $valid_products = array_keys( rinascente_member_product_choices() );
    $products       = array_values( array_intersect( array_unique( array_filter( $products ) ), $valid_products ) );

    return $products;
}

function rinascente_member_get_post_products( $post_id ) {
    $products = array();

    $meta_product = get_post_meta( $post_id, '_rinascente_product_key', true );
    if ( $meta_product ) {
        $products[] = $meta_product;
    }

    $terms = wp_get_post_terms( $post_id, 'product_type', array( 'fields' => 'slugs' ) );
    if ( ! is_wp_error( $terms ) ) {
        $products = array_merge( $products, $terms );
    }

    $valid_products = array_keys( rinascente_member_product_choices() );
    return array_values( array_intersect( array_unique( array_filter( $products ) ), $valid_products ) );
}

function rinascente_member_has_product_access( $user_id, $post_id ) {
    $post_products = rinascente_member_get_post_products( $post_id );
    if ( empty( $post_products ) ) {
        return true;
    }

    $member_products = rinascente_member_get_user_products( $user_id );
    if ( empty( $member_products ) ) {
        return false;
    }

    return (bool) array_intersect( $member_products, $post_products );
}

function rinascente_member_post_is_active( $post_id ) {
    $start_date = get_post_meta( $post_id, '_rinascente_start_date', true );
    $end_date   = get_post_meta( $post_id, '_rinascente_end_date', true );
    $today      = wp_date( 'Y-m-d' );

    if ( $start_date && $today < $start_date ) {
        return false;
    }

    if ( $end_date && $today > $end_date ) {
        return false;
    }

    return true;
}

function rinascente_member_document_file_data( $post_id ) {
    $attachment_id = absint( get_post_meta( $post_id, '_rinascente_attachment_id', true ) );
    if ( ! $attachment_id ) {
        return array(
            'attachment_id' => 0,
            'path'          => '',
            'mime'          => '',
            'size'          => '',
            'filename'      => '',
        );
    }

    $file_path = get_attached_file( $attachment_id );
    $file_size = $file_path && file_exists( $file_path ) ? rinascente_member_format_file_size( filesize( $file_path ) ) : '';

    return array(
        'attachment_id' => $attachment_id,
        'path'          => $file_path,
        'mime'          => get_post_mime_type( $attachment_id ),
        'size'          => $file_size,
        'filename'      => wp_basename( (string) get_attached_file( $attachment_id ) ),
    );
}

function rinascente_member_document_download_url( $post_id ) {
    return add_query_arg(
        array(
            'action'      => 'rinascente_member_document_download',
            'document_id' => $post_id,
            '_wpnonce'    => wp_create_nonce( rinascente_member_document_download_nonce_action( $post_id ) ),
        ),
        admin_url( 'admin-post.php' )
    );
}

function rinascente_member_document_download_nonce_action( $post_id ) {
    return 'rinascente_member_document_download_' . absint( $post_id );
}

function rinascente_member_review_submit_url() {
    return admin_url( 'admin-post.php' );
}

function rinascente_member_review_notice() {
    if ( ! rinascente_member_reviews_enabled() ) {
        return null;
    }

    $status = isset( $_GET['review_status'] ) ? sanitize_key( wp_unslash( $_GET['review_status'] ) ) : '';

    if ( 'success' === $status ) {
        return array(
            'type'    => 'success',
            'message' => 'レビューを送信しました。公開前に管理者が確認します。',
        );
    }

    if ( 'error' === $status ) {
        return array(
            'type'    => 'error',
            'message' => 'レビューを送信できませんでした。必須項目を確認してください。',
        );
    }

    return null;
}

function rinascente_member_support_info() {
    return array(
        'company_name' => get_theme_mod( 'support_name', get_theme_mod( 'company_name', '株式会社リナシェンテ' ) ),
        'telephone'    => get_theme_mod( 'support_tel', get_theme_mod( 'company_tel', '' ) ),
        'email'        => '',
        'hours'        => get_theme_mod( 'support_hours', get_theme_mod( 'company_hours', '' ) ),
    );
}

function rinascente_member_notice_summary( $post ) {
    $excerpt = trim( (string) $post->post_excerpt );
    if ( '' !== $excerpt ) {
        return $excerpt;
    }

    $content = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $post->post_content ) ) );
    return $content;
}

function rinascente_member_get_contracts( $user_id ) {
    return get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'private' ),
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_rinascente_order_date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_rinascente_member_user_id',
                    'value' => (string) $user_id,
                ),
            ),
        )
    );
}

function rinascente_member_get_contract_snapshot( $contract ) {
    $contract = get_post( $contract );
    if ( ! $contract instanceof WP_Post || 'contract' !== $contract->post_type ) {
        return array();
    }

    $status_choices  = rinascente_contract_status_choices();
    $payment_choices = rinascente_contract_payment_choices();
    $status          = get_post_meta( $contract->ID, '_rinascente_contract_status', true );
    $payment_status  = get_post_meta( $contract->ID, '_rinascente_payment_status', true );
    $member_user_id  = (int) get_post_meta( $contract->ID, '_rinascente_member_user_id', true );
    $product_master  = absint( get_post_meta( $contract->ID, '_rinascente_product_master_id', true ) );

    return array(
        'id'               => (int) $contract->ID,
        'title'            => get_the_title( $contract ),
        'editUrl'          => admin_url( 'post.php?post=' . $contract->ID . '&action=edit' ),
        'memberUserId'     => $member_user_id,
        'facilityName'     => (string) get_post_meta( $contract->ID, '_rinascente_facility_name', true ),
        'orderNumber'      => (string) get_post_meta( $contract->ID, '_rinascente_order_number', true ),
        'productName'      => (string) get_post_meta( $contract->ID, '_rinascente_product_name', true ),
        'productKey'       => (string) get_post_meta( $contract->ID, '_rinascente_product_key', true ),
        'productMasterId'  => $product_master,
        'quantity'         => (string) get_post_meta( $contract->ID, '_rinascente_quantity', true ),
        'orderDate'        => (string) get_post_meta( $contract->ID, '_rinascente_order_date', true ),
        'deliveryDate'     => (string) get_post_meta( $contract->ID, '_rinascente_delivery_date', true ),
        'contractDate'     => (string) get_post_meta( $contract->ID, '_rinascente_contract_date', true ),
        'status'           => $status,
        'statusLabel'      => $status_choices[ $status ] ?? '',
        'paymentStatus'    => $payment_status,
        'paymentLabel'     => $payment_choices[ $payment_status ] ?? '',
        'contractInfo'     => (string) get_post_meta( $contract->ID, '_rinascente_contract_info', true ),
        'notes'            => (string) get_post_meta( $contract->ID, '_rinascente_contract_notes', true ),
        'productLabel'     => (string) rinascente_member_product_label( get_post_meta( $contract->ID, '_rinascente_product_key', true ) ),
        'productTemplate'  => $product_master ? (string) get_post_meta( $product_master, '_rinascente_product_catalog_contract_template', true ) : '',
    );
}

function rinascente_member_contract_admin_data( $exclude_post_id = 0 ) {
    $exclude_post_id = absint( $exclude_post_id );
    $contracts       = get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'future' ),
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_rinascente_order_date',
            'order'          => 'DESC',
        )
    );
    $history         = array();
    $order_index     = array();

    foreach ( $contracts as $contract ) {
        if ( $exclude_post_id && (int) $contract->ID === $exclude_post_id ) {
            continue;
        }

        $snapshot = rinascente_member_get_contract_snapshot( $contract );
        if ( empty( $snapshot ) || empty( $snapshot['memberUserId'] ) ) {
            continue;
        }

        $member_user_id = (int) $snapshot['memberUserId'];
        if ( ! isset( $history[ $member_user_id ] ) ) {
            $history[ $member_user_id ] = array();
        }

        if ( count( $history[ $member_user_id ] ) < 5 ) {
            $history[ $member_user_id ][] = $snapshot;
        }

        $normalized_order_number = rinascente_member_normalize_order_number( $snapshot['orderNumber'] );
        if ( '' === $normalized_order_number ) {
            continue;
        }

        if ( ! isset( $order_index[ $normalized_order_number ] ) ) {
            $order_index[ $normalized_order_number ] = array();
        }

        $order_index[ $normalized_order_number ][] = array(
            'id'           => $snapshot['id'],
            'editUrl'      => $snapshot['editUrl'],
            'facilityName' => $snapshot['facilityName'],
            'productName'  => $snapshot['productName'],
            'orderNumber'  => $snapshot['orderNumber'],
            'statusLabel'  => $snapshot['statusLabel'],
            'paymentLabel' => $snapshot['paymentLabel'],
        );
    }

    return array(
        'history'    => $history,
        'orderIndex' => $order_index,
    );
}

function rinascente_member_contract_duplicate_posts( $order_number, $exclude_post_id = 0 ) {
    $normalized = rinascente_member_normalize_order_number( $order_number );
    if ( '' === $normalized ) {
        return array();
    }

    $contracts = get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'private', 'draft', 'pending', 'future' ),
            'posts_per_page' => -1,
        )
    );
    $matches   = array();

    foreach ( $contracts as $contract ) {
        if ( $exclude_post_id && (int) $contract->ID === (int) $exclude_post_id ) {
            continue;
        }

        $candidate = get_post_meta( $contract->ID, '_rinascente_order_number', true );
        if ( rinascente_member_normalize_order_number( $candidate ) === $normalized ) {
            $matches[] = $contract;
        }
    }

    return $matches;
}

function rinascente_member_find_user_ids_for_contract_lookup( $keyword ) {
    $keyword = trim( (string) $keyword );
    if ( '' === $keyword ) {
        return array();
    }

    if ( function_exists( 'mb_strtolower' ) ) {
        $needle = mb_strtolower( $keyword, 'UTF-8' );
    } else {
        $needle = strtolower( $keyword );
    }

    $matched_ids = array();

    foreach ( rinascente_member_get_users() as $user ) {
        $facility_name = (string) get_user_meta( $user->ID, '_rinascente_member_facility_name', true );
        $haystack      = implode(
            ' ',
            array_filter(
                array(
                    $facility_name,
                    rinascente_member_user_name( $user ),
                    $user->display_name,
                    $user->user_login,
                    $user->user_email,
                )
            )
        );

        if ( function_exists( 'mb_strtolower' ) ) {
            $haystack = mb_strtolower( $haystack, 'UTF-8' );
        } else {
            $haystack = strtolower( $haystack );
        }

        if ( false !== strpos( $haystack, $needle ) ) {
            $matched_ids[] = (int) $user->ID;
        }
    }

    return array_values( array_unique( $matched_ids ) );
}

function rinascente_member_get_videos( $user_id, $category = '' ) {
    $posts = get_posts(
        array(
            'post_type'      => 'member_video',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
        )
    );

    return array_values(
        array_filter(
            $posts,
            static function ( $post ) use ( $user_id, $category ) {
                $current_category = get_post_meta( $post->ID, '_rinascente_video_category', true );
                if ( $category && $current_category !== $category ) {
                    return false;
                }

                return rinascente_member_post_is_active( $post->ID ) && rinascente_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function rinascente_member_get_documents( $user_id, $category = '' ) {
    $posts = get_posts(
        array(
            'post_type'      => 'member_document',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
        )
    );

    return array_values(
        array_filter(
            $posts,
            static function ( $post ) use ( $user_id, $category ) {
                $current_category = get_post_meta( $post->ID, '_rinascente_document_category', true );
                if ( $category && $current_category !== $category ) {
                    return false;
                }

                return rinascente_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function rinascente_member_get_support_notices( $user_id ) {
    $posts = get_posts(
        array(
            'post_type'      => 'member_notice',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    return array_values(
        array_filter(
            $posts,
            static function ( $post ) use ( $user_id ) {
                return rinascente_member_post_is_active( $post->ID ) && rinascente_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function rinascente_member_get_reviews( $user_id ) {
    if ( ! rinascente_member_reviews_enabled() ) {
        return array();
    }

    $posts = get_posts(
        array(
            'post_type'      => 'member_review',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        )
    );

    return array_values(
        array_filter(
            $posts,
            static function ( $post ) use ( $user_id ) {
                return rinascente_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function rinascente_member_review_summary( $posts ) {
    $summary = array();
    foreach ( rinascente_member_product_choices() as $product_key => $label ) {
        $summary[ $product_key ] = array(
            'label'         => $label,
            'count'         => 0,
            'total_rating'  => 0,
            'average'       => '0.0',
            'distribution'  => array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 ),
        );
    }

    foreach ( $posts as $post ) {
        $products = rinascente_member_get_post_products( $post->ID );
        if ( empty( $products ) ) {
            $products = array_keys( rinascente_member_product_choices() );
        }

        $rating = max( 1, min( 5, (int) get_post_meta( $post->ID, '_rinascente_review_rating', true ) ) );
        foreach ( $products as $product_key ) {
            if ( ! isset( $summary[ $product_key ] ) ) {
                continue;
            }
            ++$summary[ $product_key ]['count'];
            $summary[ $product_key ]['total_rating'] += $rating;
            ++$summary[ $product_key ]['distribution'][ $rating ];
        }
    }

    foreach ( $summary as $product_key => $item ) {
        if ( $item['count'] > 0 ) {
            $summary[ $product_key ]['average'] = number_format_i18n( $item['total_rating'] / $item['count'], 1 );
        }
    }

    return $summary;
}

function rinascente_generate_contract_title( $post_id ) {
    if ( 'contract' !== get_post_type( $post_id ) ) {
        return '';
    }

    $facility_name = trim( (string) get_post_meta( $post_id, '_rinascente_facility_name', true ) );
    $product_name  = trim( (string) get_post_meta( $post_id, '_rinascente_product_name', true ) );
    $order_number  = trim( (string) get_post_meta( $post_id, '_rinascente_order_number', true ) );
    $product_key   = trim( (string) get_post_meta( $post_id, '_rinascente_product_key', true ) );

    if ( '' === $product_name && $product_key ) {
        $product_name = rinascente_member_product_label( $product_key );
    }

    $parts = array_filter(
        array(
            $facility_name,
            $product_name,
            $order_number,
        )
    );

    if ( empty( $parts ) ) {
        $parts[] = '契約・購入履歴';
        $parts[] = '#' . (int) $post_id;
    }

    return implode( ' / ', $parts );
}

function rinascente_register_member_content_post_types() {
    register_post_type(
        'contract',
        array(
            'labels' => array(
                'name'               => '契約・購入履歴',
                'singular_name'      => '契約・購入履歴',
                'add_new_item'       => '契約・購入履歴を追加',
                'edit_item'          => '契約・購入履歴を編集',
                'new_item'           => '新しい契約・購入履歴',
                'view_item'          => '契約・購入履歴を表示',
                'search_items'       => '契約・購入履歴を検索',
                'not_found'          => '契約・購入履歴が見つかりません',
                'not_found_in_trash' => 'ゴミ箱に契約・購入履歴はありません',
            ),
            'public'             => true,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : true,
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-clipboard',
            'supports'           => array(),
        )
    );

    register_post_type(
        'product_master',
        array(
            'labels' => array(
                'name'               => '製品マスター',
                'singular_name'      => '製品マスター',
                'add_new_item'       => '製品マスターを追加',
                'edit_item'          => '製品マスターを編集',
                'new_item'           => '新しい製品マスター',
                'view_item'          => '製品マスターを表示',
                'search_items'       => '製品マスターを検索',
                'not_found'          => '製品マスターが見つかりません',
                'not_found_in_trash' => 'ゴミ箱に製品マスターはありません',
            ),
            'public'             => false,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_rest'       => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-products',
            'supports'           => array( 'title' ),
            'menu_position'      => 31,
        )
    );

    register_post_type(
        'member_video',
        array(
            'labels' => array(
                'name'               => '会員限定動画',
                'singular_name'      => '会員限定動画',
                'add_new_item'       => '会員限定動画を追加',
                'edit_item'          => '会員限定動画を編集',
                'new_item'           => '新しい会員限定動画',
                'view_item'          => '会員限定動画を表示',
                'search_items'       => '会員限定動画を検索',
                'not_found'          => '会員限定動画が見つかりません',
                'not_found_in_trash' => 'ゴミ箱に会員限定動画はありません',
            ),
            'public'             => true,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : true,
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-video-alt3',
            'supports'           => array( 'title', 'editor', 'page-attributes' ),
        )
    );

    register_post_type(
        'member_document',
        array(
            'labels' => array(
                'name'               => '会員限定資料',
                'singular_name'      => '会員限定資料',
                'add_new_item'       => '会員限定資料を追加',
                'edit_item'          => '会員限定資料を編集',
                'new_item'           => '新しい会員限定資料',
                'view_item'          => '会員限定資料を表示',
                'search_items'       => '会員限定資料を検索',
                'not_found'          => '会員限定資料が見つかりません',
                'not_found_in_trash' => 'ゴミ箱に会員限定資料はありません',
            ),
            'public'             => true,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : true,
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => array( 'title', 'editor', 'page-attributes' ),
        )
    );

    if ( rinascente_member_reviews_enabled() ) {
        register_post_type(
            'member_review',
            array(
                'labels' => array(
                    'name'               => '施設レビュー',
                    'singular_name'      => '施設レビュー',
                    'add_new_item'       => '施設レビューを追加',
                    'edit_item'          => '施設レビューを編集',
                    'new_item'           => '新しい施設レビュー',
                    'view_item'          => '施設レビューを表示',
                    'search_items'       => '施設レビューを検索',
                    'not_found'          => '施設レビューが見つかりません',
                    'not_found_in_trash' => 'ゴミ箱に施設レビューはありません',
                ),
                'public'             => true,
                'publicly_queryable' => false,
                'has_archive'        => false,
                'exclude_from_search'=> true,
                'show_ui'            => true,
                'show_in_menu'       => function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : true,
                'show_in_rest'       => true,
                'menu_icon'          => 'dashicons-star-filled',
                'supports'           => array( 'title', 'editor', 'author' ),
            )
        );
    }

    register_post_type(
        'member_notice',
        array(
            'labels' => array(
                'name'               => 'サポート情報',
                'singular_name'      => 'サポート情報',
                'add_new_item'       => 'サポート情報を追加',
                'edit_item'          => 'サポート情報を編集',
                'new_item'           => '新しいサポート情報',
                'view_item'          => 'サポート情報を表示',
                'search_items'       => 'サポート情報を検索',
                'not_found'          => 'サポート情報が見つかりません',
                'not_found_in_trash' => 'ゴミ箱にサポート情報はありません',
            ),
            'public'             => true,
            'publicly_queryable' => false,
            'has_archive'        => false,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : true,
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-megaphone',
            'supports'           => array( 'title', 'editor', 'excerpt' ),
        )
    );
}
add_action( 'init', 'rinascente_register_member_content_post_types', 20 );

function rinascente_member_use_classic_editor_for_contract( $use_block_editor, $post_type ) {
    $classic_post_types = array_merge( array( 'contract' ), rinascente_member_content_edit_post_types() );

    if ( in_array( $post_type, $classic_post_types, true ) ) {
        return false;
    }

    return $use_block_editor;
}
add_filter( 'use_block_editor_for_post_type', 'rinascente_member_use_classic_editor_for_contract', 10, 2 );

function rinascente_member_contract_admin_cleanup() {
    $screen = get_current_screen();
    if ( ! $screen || 'contract' !== $screen->post_type ) {
        return;
    }

    remove_meta_box( 'slugdiv', 'contract', 'normal' );
    remove_post_type_support( 'contract', 'title' );
    remove_post_type_support( 'contract', 'editor' );
    remove_post_type_support( 'contract', 'autosave' );
}
add_action( 'current_screen', 'rinascente_member_contract_admin_cleanup' );

function rinascente_seed_product_master_content() {
    $seed_version = rinascente_member_product_master_seed_version();
    if ( get_option( 'rinascente_product_master_seed_version' ) === $seed_version ) {
        return;
    }

    foreach ( rinascente_member_yumeho_product_master_seed_data() as $item ) {
        rinascente_member_upsert_product_master( $item );
    }

    $contract_ids = get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    foreach ( $contract_ids as $contract_id ) {
        $product_name = trim( (string) get_post_meta( $contract_id, '_rinascente_product_name', true ) );
        $product_key  = sanitize_key( get_post_meta( $contract_id, '_rinascente_product_key', true ) );

        if ( '' === $product_name ) {
            continue;
        }

        $master = rinascente_member_find_product_master( $product_name, $product_key );
        if ( ! $master ) {
            $master_id = rinascente_member_upsert_product_master(
                array(
                    'title'       => $product_name,
                    'product_key' => $product_key,
                    'category'    => 'other',
                    'sort_order'  => 999,
                    'source_note' => '既存の契約・購入履歴から補完',
                )
            );
        } else {
            $master_id = (int) $master->ID;
        }

        if ( $master_id ) {
            update_post_meta( $contract_id, '_rinascente_product_master_id', $master_id );
        }
    }

    rinascente_touch_product_catalog_version();
    update_option( 'rinascente_product_master_seed_version', $seed_version, false );
}
add_action( 'admin_init', 'rinascente_seed_product_master_content', 20 );

function rinascente_touch_product_catalog_version() {
    update_option( 'rinascente_product_catalog_version', gmdate( 'c' ), false );
}

function rinascente_touch_product_catalog_version_on_save( $post_id ) {
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    rinascente_touch_product_catalog_version();
}
add_action( 'save_post_product_master', 'rinascente_touch_product_catalog_version_on_save', 20 );

function rinascente_touch_product_catalog_version_on_delete( $post_id ) {
    if ( 'product_master' !== get_post_type( $post_id ) ) {
        return;
    }

    rinascente_touch_product_catalog_version();
}
add_action( 'before_delete_post', 'rinascente_touch_product_catalog_version_on_delete', 20 );

function rinascente_register_member_content_taxonomies() {
    register_taxonomy(
        'case_format',
        'case_study',
        array(
            'labels' => array(
                'name'          => '事例形式',
                'singular_name' => '事例形式',
                'add_new_item'  => '事例形式を追加',
                'edit_item'     => '事例形式を編集',
            ),
            'public'       => true,
            'hierarchical' => true,
            'rewrite'      => array( 'slug' => 'case-format' ),
            'show_in_rest' => true,
        )
    );

    $member_post_types = array( 'contract', 'member_video', 'member_document', 'member_notice' );
    if ( rinascente_member_reviews_enabled() ) {
        $member_post_types[] = 'member_review';
    }

    foreach ( $member_post_types as $post_type ) {
        register_taxonomy_for_object_type( 'product_type', $post_type );
    }
}
add_action( 'init', 'rinascente_register_member_content_taxonomies', 30 );

function rinascente_seed_member_terms() {
    foreach ( rinascente_member_product_choices() as $slug => $label ) {
        if ( ! term_exists( $slug, 'product_type' ) ) {
            wp_insert_term( $label, 'product_type', array( 'slug' => $slug ) );
        }
    }

    $case_formats = array(
        'case-study' => '導入事例',
        'voice'      => '施設の声',
    );
    foreach ( $case_formats as $slug => $label ) {
        if ( ! term_exists( $slug, 'case_format' ) ) {
            wp_insert_term( $label, 'case_format', array( 'slug' => $slug ) );
        }
    }
}
add_action( 'init', 'rinascente_seed_member_terms', 40 );

function rinascente_member_content_meta_boxes() {
    add_meta_box( 'rinascente_contract_details', '契約・購入履歴', 'rinascente_contract_meta_box_html', 'contract', 'normal', 'high' );
    add_meta_box( 'rinascente_product_master_details', '製品マスターかんたん設定', 'rinascente_product_master_meta_box_html', 'product_master', 'normal', 'high' );
    add_meta_box( 'rinascente_member_video_details', '動画詳細', 'rinascente_member_video_meta_box_html', 'member_video', 'normal', 'high' );
    add_meta_box( 'rinascente_member_notice_details', 'サポート情報詳細', 'rinascente_member_notice_meta_box_html', 'member_notice', 'normal', 'high' );

    if ( rinascente_member_reviews_enabled() ) {
        add_meta_box( 'rinascente_member_review_details', 'レビュー詳細', 'rinascente_member_review_meta_box_html', 'member_review', 'normal', 'high' );
    }
}
add_action( 'add_meta_boxes', 'rinascente_member_content_meta_boxes' );

function rinascente_member_nonce_field() {
    wp_nonce_field( 'rinascente_member_content_save', 'rinascente_member_content_nonce' );
}

function rinascente_member_content_edit_post_types() {
    return array( 'member_video', 'member_document', 'member_notice' );
}

function rinascente_member_content_edit_config( $post_type ) {
    $configs = array(
        'member_video'    => array(
            'label'          => '会員限定動画',
            'lead'           => '会員限定動画は、タイトル、YouTube URL または動画ID、説明文、カテゴリを入れれば公開準備ができます。本文欄は使わず、この画面の入力内容だけで一覧と詳細表示を作ります。',
            'steps'          => array(
                '動画タイトル',
                'YouTube URL または 動画ID',
                '説明文',
                'カテゴリ',
                '表示順',
            ),
            'checklist'      => array(
                'title'       => array( 'label' => '動画タイトル', 'required' => true ),
                'youtube_id'  => array( 'label' => 'YouTube動画ID', 'required' => true ),
                'description' => array( 'label' => '説明文', 'required' => true ),
                'category'    => array( 'label' => 'カテゴリ', 'required' => true ),
                'product'     => array( 'label' => '対象製品', 'required' => false ),
                'order'       => array( 'label' => '表示順', 'required' => true ),
                'schedule'    => array( 'label' => '公開期間', 'required' => false ),
            ),
            'title_example'  => '例: PGT-9001 の始業前点検',
            'order_label'    => '表示順',
            'order_help'     => '設置方法・利用方法の一覧では、小さい数字の動画ほど上に表示されます。迷ったら `10, 20, 30...` のように入れると後から差し込みやすくなります。',
            'editor_message' => '本文欄は現在の会員ページでは使用していません。説明は「説明文」へ入力してください。',
            'show_order'     => true,
            'show_excerpt'   => false,
            'hide_editor'    => true,
        ),
        'member_document' => array(
            'label'          => '会員限定資料',
            'lead'           => '会員限定資料は、まず「資料名」と「ダウンロード用ファイル」を入れれば土台ができます。どの資料か分かるカテゴリと更新日を添えると、会員ページでも迷いなく使えます。',
            'steps'          => array(
                '資料名',
                'ダウンロード用ファイル',
                'カテゴリ',
                '更新日',
                '必要なら対象製品',
                '表示順',
            ),
            'checklist'      => array(
                'title'       => array( 'label' => '資料名', 'required' => true ),
                'file'        => array( 'label' => 'ファイル', 'required' => true ),
                'category'    => array( 'label' => 'カテゴリ', 'required' => true ),
                'updated'     => array( 'label' => '更新日', 'required' => true ),
                'product'     => array( 'label' => '対象製品', 'required' => false ),
                'order'       => array( 'label' => '表示順', 'required' => true ),
                'schedule'    => array( 'label' => '公開期間', 'required' => false ),
            ),
            'title_example'  => '例: YUMEHO 運用ガイド',
            'order_label'    => '表示順',
            'order_help'     => '資料一覧では、小さい数字ほど上に表示されます。よく使う資料や最新案内を先に見せたいときに調整してください。',
            'editor_message' => '本文欄は会員ページでは使っていません。資料名・ファイル・カテゴリ・更新日を入れれば、そのまま公開に使えます。',
            'show_order'     => true,
            'show_excerpt'   => false,
            'hide_editor'    => true,
        ),
        'member_notice'   => array(
            'label'          => 'サポート情報',
            'lead'           => 'サポート情報は、まず「タイトル」と「本文」を入れ、必要なら一覧用要約を足す流れが分かりやすいです。表示タイプを選ぶと、会員ページで重要度の違いが伝わりやすくなります。',
            'steps'          => array(
                'タイトル',
                '本文',
                '必要なら一覧用要約',
                '表示タイプ',
                '必要なら対象製品',
                '必要なら公開期間',
            ),
            'checklist'      => array(
                'title'       => array( 'label' => 'タイトル', 'required' => true ),
                'summary'     => array( 'label' => '本文または一覧用要約', 'required' => true ),
                'tone'        => array( 'label' => '表示タイプ', 'required' => true ),
                'product'     => array( 'label' => '対象製品', 'required' => false ),
                'schedule'    => array( 'label' => '公開期間', 'required' => false ),
            ),
            'title_example'  => '例: v2.3.1 への更新のお願い',
            'excerpt_label'  => '一覧用要約',
            'excerpt_help'   => '会員ページの一覧カードに優先表示する短い要約です。空欄でも公開できますが、本文の要点を先に見せたいときに入れておくと一覧が読みやすくなります。',
            'show_order'     => false,
            'show_excerpt'   => true,
            'hide_editor'    => false,
        ),
    );

    return $configs[ $post_type ] ?? array();
}

function rinascente_member_content_edit_is_target( $post ) {
    return $post instanceof WP_Post && in_array( $post->post_type, rinascente_member_content_edit_post_types(), true );
}

add_filter( 'enter_title_here', 'rinascente_member_content_title_placeholder', 10, 2 );
function rinascente_member_content_title_placeholder( $text, $post ) {
    if ( ! rinascente_member_content_edit_is_target( $post ) ) {
        return $text;
    }

    $config = rinascente_member_content_edit_config( $post->post_type );
    return $config['title_example'] ?? $text;
}

function rinascente_member_content_admin_meta_boxes() {
    foreach ( rinascente_member_content_edit_post_types() as $post_type ) {
        remove_meta_box( 'slugdiv', $post_type, 'normal' );
        remove_meta_box( 'commentstatusdiv', $post_type, 'normal' );
        remove_meta_box( 'commentsdiv', $post_type, 'normal' );
        remove_meta_box( 'trackbacksdiv', $post_type, 'normal' );
        remove_meta_box( 'authordiv', $post_type, 'normal' );
        remove_meta_box( 'revisionsdiv', $post_type, 'normal' );
        remove_meta_box( 'postcustom', $post_type, 'normal' );

        if ( in_array( $post_type, array( 'member_video', 'member_document' ), true ) ) {
            remove_meta_box( 'pageparentdiv', $post_type, 'side' );
        }

        if ( 'member_notice' === $post_type ) {
            remove_meta_box( 'postexcerpt', $post_type, 'normal' );
        }

        add_meta_box(
            'rinascente_member_content_checklist',
            '公開チェック',
            'rinascente_member_content_checklist_meta_box_html',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action( 'add_meta_boxes', 'rinascente_member_content_admin_meta_boxes', 30 );

function rinascente_member_content_checklist_meta_box_html( $post ) {
    if ( ! rinascente_member_content_edit_is_target( $post ) ) {
        return;
    }

    $config = rinascente_member_content_edit_config( $post->post_type );
    $items  = $config['checklist'] ?? array();
    ?>
    <div class="rinascente-member-content-checklist" data-post-type="<?php echo esc_attr( $post->post_type ); ?>">
        <p class="description" style="margin-top:0;">この画面で入力した内容が、そのまま会員ページの表示やダウンロード導線に使われます。</p>
        <ul class="rinascente-member-content-checklist__list">
            <?php foreach ( $items as $key => $item ) : ?>
                <li class="rinascente-member-content-checklist__item" data-check="<?php echo esc_attr( $key ); ?>" data-required="<?php echo ! empty( $item['required'] ) ? '1' : '0'; ?>">
                    <span class="rinascente-member-content-checklist__badge">未確認</span>
                    <span class="rinascente-member-content-checklist__label"><?php echo esc_html( $item['label'] ); ?></span>
                    <?php if ( empty( $item['required'] ) ) : ?>
                        <span class="rinascente-member-content-checklist__note">任意</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}

function rinascente_member_content_top_fields_html( $post ) {
    $config     = rinascente_member_content_edit_config( $post->post_type );
    $menu_order = (int) $post->menu_order;
    ?>
    <div class="rinascente-member-content-top-fields">
        <div class="rinascente-member-content-top-fields__guide">
            <p class="rinascente-member-content-top-fields__lead"><?php echo esc_html( $config['lead'] ?? '' ); ?></p>
            <div class="rinascente-member-content-top-fields__grid">
                <div class="rinascente-member-content-top-card">
                    <h4>最初に入れる内容</h4>
                    <ol>
                        <?php foreach ( (array) ( $config['steps'] ?? array() ) as $step ) : ?>
                            <li><?php echo esc_html( $step ); ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <?php if ( 'member_video' === $post->post_type ) : ?>
                <div class="rinascente-member-content-top-card rinascente-member-content-top-card--hint">
                    <h4>YouTube の入れ方</h4>
                    <ul>
                        <li>共有URLをそのまま貼っても大丈夫です</li>
                        <li>保存時に動画IDへ自動で整えます</li>
                        <li>下のプレビューで対象動画を確認できます</li>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( 'member_document' === $post->post_type ) : ?>
                <div class="rinascente-member-content-top-card rinascente-member-content-top-card--hint">
                    <h4>資料登録のコツ</h4>
                    <ul>
                        <li>PDFやWordなど、会員に渡す元ファイルをそのまま選べます</li>
                        <li>対象製品を空欄にすると、共通資料として全会員に表示されます</li>
                        <li>下の確認カードで、選んだファイル名と形式を見直せます</li>
                        <li>一覧の「複製して下書き」から、既存資料をもとに更新用の下書きを作れます</li>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( 'member_notice' === $post->post_type ) : ?>
                <div class="rinascente-member-content-top-card rinascente-member-content-top-card--hint">
                    <h4>サポート情報のコツ</h4>
                    <ul>
                        <li>まず本文を書き、一覧で短く見せたいときだけ一覧用要約を入れると迷いにくいです</li>
                        <li>緊急連絡は「重要」、通常の案内は「お知らせ」や「一般」が目安です</li>
                        <li>製品別の案内でなければ、対象製品は「共通」のままで大丈夫です</li>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $config['show_order'] ) ) : ?>
                <div class="rinascente-member-content-top-card rinascente-member-content-top-card--settings">
                    <h4><?php echo esc_html( $config['order_label'] ?? '表示順' ); ?></h4>
                    <label for="menu_order"><strong><?php echo esc_html( $config['order_label'] ?? '表示順' ); ?></strong></label>
                    <input type="number" name="menu_order" id="menu_order" value="<?php echo esc_attr( (string) $menu_order ); ?>" min="0" step="1">
                    <p class="description"><?php echo esc_html( $config['order_help'] ?? '' ); ?></p>
                </div>
                <?php endif; ?>

                <?php if ( ! empty( $config['show_excerpt'] ) ) : ?>
                <div class="rinascente-member-content-top-card rinascente-member-content-top-card--settings">
                    <h4><?php echo esc_html( $config['excerpt_label'] ?? '一覧用要約' ); ?></h4>
                    <label for="excerpt"><strong><?php echo esc_html( $config['excerpt_label'] ?? '一覧用要約' ); ?></strong></label>
                    <textarea id="excerpt" name="excerpt" rows="4" class="widefat"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
                    <p class="description"><?php echo esc_html( $config['excerpt_help'] ?? '' ); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

function rinascente_member_content_after_title_fields( $post ) {
    if ( ! rinascente_member_content_edit_is_target( $post ) ) {
        return;
    }

    if ( 'member_document' === $post->post_type ) {
        rinascente_member_document_meta_box_html( $post );
        return;
    }

    rinascente_member_content_top_fields_html( $post );
}
add_action( 'edit_form_after_title', 'rinascente_member_content_after_title_fields' );

function rinascente_contract_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $users                = rinascente_member_get_users();
    $selected_user        = (int) get_post_meta( $post->ID, '_rinascente_member_user_id', true );
    $facility_name        = get_post_meta( $post->ID, '_rinascente_facility_name', true );
    $order_number         = get_post_meta( $post->ID, '_rinascente_order_number', true );
    $product_name         = get_post_meta( $post->ID, '_rinascente_product_name', true );
    $product_key          = get_post_meta( $post->ID, '_rinascente_product_key', true );
    $selected_product_id  = absint( get_post_meta( $post->ID, '_rinascente_product_master_id', true ) );
    $quantity             = get_post_meta( $post->ID, '_rinascente_quantity', true );
    $order_date           = get_post_meta( $post->ID, '_rinascente_order_date', true );
    $delivery_date        = get_post_meta( $post->ID, '_rinascente_delivery_date', true );
    $contract_date        = get_post_meta( $post->ID, '_rinascente_contract_date', true );
    $status               = get_post_meta( $post->ID, '_rinascente_contract_status', true );
    $payment_status       = get_post_meta( $post->ID, '_rinascente_payment_status', true );
    $contract_info        = get_post_meta( $post->ID, '_rinascente_contract_info', true );
    $notes                = get_post_meta( $post->ID, '_rinascente_contract_notes', true );
    $after_save_action    = '';
    $product_master_posts = rinascente_member_get_product_master_posts(
        array(
            'post_status' => array( 'publish', 'draft', 'private', 'pending', 'future' ),
        )
    );
    $master_list_url      = admin_url( 'edit.php?post_type=product_master' );
    $master_create_url    = admin_url( 'post-new.php?post_type=product_master' );
    $admin_contract_data  = rinascente_member_contract_admin_data( (int) $post->ID );
    $history_json         = wp_json_encode( $admin_contract_data['history'] );
    $order_index_json     = wp_json_encode( $admin_contract_data['orderIndex'] );
    $current_post_id_json = wp_json_encode( (int) $post->ID );

    if ( ! $selected_user && isset( $_GET['member_user_id'] ) ) {
        $selected_user = absint( wp_unslash( $_GET['member_user_id'] ) );
    }

    if ( ! $product_key && isset( $_GET['product_key'] ) ) {
        $requested_product_key = sanitize_key( wp_unslash( $_GET['product_key'] ) );
        if ( '' !== $requested_product_key ) {
            $product_key = $requested_product_key;
        }
    }

    if ( ! $facility_name && $selected_user ) {
        $facility_name = (string) get_user_meta( $selected_user, '_rinascente_member_facility_name', true );
        if ( '' === $facility_name ) {
            $selected_user_object = get_user_by( 'id', $selected_user );
            if ( $selected_user_object instanceof WP_User ) {
                $facility_name = rinascente_member_user_name( $selected_user_object );
            }
        }
    }

    if ( ! $selected_product_id && '' !== $product_name ) {
        $matched_product = rinascente_member_find_product_master( $product_name, $product_key );
        if ( $matched_product ) {
            $selected_product_id = (int) $matched_product->ID;
        }
    }
    ?>
    <table class="form-table">
        <tr>
            <th><label for="rinascente_member_user_id">会員ユーザー</label></th>
            <td>
                <input type="hidden" name="rinascente_after_save_action" id="rinascente_after_save_action" value="<?php echo esc_attr( $after_save_action ); ?>">
                <input type="hidden" name="rinascente_duplicate_override" id="rinascente_duplicate_override" value="">
                <input type="search" id="rinascente_member_user_search" class="regular-text" placeholder="施設名・会社名 / ID / メールで検索" style="margin-bottom:8px;">
                <div class="rinascente-member-search-panel">
                    <div class="rinascente-member-search-panel__head">
                        <strong>検索結果</strong>
                        <span class="rinascente-member-search-panel__count" id="rinascente_member_user_search_feedback">検索すると候補がここに表示されます。</span>
                    </div>
                    <div id="rinascente_member_user_search_results" class="rinascente-member-search-results" aria-live="polite"></div>
                    <p class="description">候補をクリックすると「会員ユーザー」に反映されます。直接プルダウンから選んでも大丈夫です。</p>
                </div>
                <select name="rinascente_member_user_id" id="rinascente_member_user_id" class="regular-text">
                    <option value="">選択してください</option>
                    <?php foreach ( $users as $user ) : ?>
                        <?php
                        $user_facility_name = get_user_meta( $user->ID, '_rinascente_member_facility_name', true );
                        $user_display_name  = rinascente_member_user_name( $user );
                        $option_label       = $user_facility_name
                            ? $user_facility_name . ' / ' . $user_display_name . ' (' . $user->user_login . ')'
                            : $user_display_name . ' (' . $user->user_login . ')';
                        $search_value       = trim( implode( ' ', array_filter( array( $user_facility_name, $user_display_name, $user->user_login, $user->user_email ) ) ) );
                        ?>
                        <option value="<?php echo esc_attr( $user->ID ); ?>" data-facility-name="<?php echo esc_attr( $user_facility_name ?: $user_display_name ); ?>" data-display-name="<?php echo esc_attr( $user_display_name ); ?>" data-user-login="<?php echo esc_attr( $user->user_login ); ?>" data-user-email="<?php echo esc_attr( $user->user_email ); ?>" data-search="<?php echo esc_attr( $search_value ); ?>" <?php selected( $selected_user, $user->ID ); ?>><?php echo esc_html( $option_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <div id="rinascente_member_user_selected" class="rinascente-member-selected" aria-live="polite"></div>
                <div id="rinascente_member_contract_history" class="rinascente-member-history" aria-live="polite"></div>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_facility_name">施設名</label></th>
            <td>
                <input type="text" name="rinascente_facility_name" id="rinascente_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text" readonly>
                <button type="button" class="button-link" id="rinascente_facility_name_toggle" style="margin-left:10px;">手動で修正</button>
                <p class="description">会員ユーザーに合わせて自動入力されます。必要な時だけ手動修正に切り替えられます。</p>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_order_number">注文番号</label></th>
            <td>
                <input type="text" name="rinascente_order_number" id="rinascente_order_number" value="<?php echo esc_attr( $order_number ); ?>" class="regular-text">
                <div id="rinascente_order_number_warning" class="rinascente-order-warning" aria-live="polite"></div>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_product_key">対象製品</label></th>
            <td>
                <select name="rinascente_product_key" id="rinascente_product_key">
                    <option value="">共通</option>
                    <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_product_master_id">製品名</label></th>
            <td>
                <select name="rinascente_product_master_id" id="rinascente_product_master_id" class="regular-text">
                    <option value="">製品マスターから選択してください</option>
                    <?php foreach ( $product_master_posts as $product_master_post ) : ?>
                        <?php
                        $master_product_key = get_post_meta( $product_master_post->ID, '_rinascente_product_key', true );
                        $master_category    = get_post_meta( $product_master_post->ID, '_rinascente_product_catalog_category', true );
                        $master_template    = get_post_meta( $product_master_post->ID, '_rinascente_product_catalog_contract_template', true );
                        $master_status      = 'publish' === $product_master_post->post_status ? '' : '（非公開）';
                        $master_label       = $product_master_post->post_title . $master_status;
                        if ( isset( rinascente_member_product_master_category_choices()[ $master_category ] ) ) {
                            $master_label .= ' / ' . rinascente_member_product_master_category_choices()[ $master_category ];
                        }
                        ?>
                        <option value="<?php echo esc_attr( $product_master_post->ID ); ?>" data-product-key="<?php echo esc_attr( $master_product_key ); ?>" data-product-name="<?php echo esc_attr( $product_master_post->post_title ); ?>" data-contract-template="<?php echo esc_attr( $master_template ); ?>" <?php selected( $selected_product_id, $product_master_post->ID ); ?>><?php echo esc_html( $master_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="description" id="rinascente_product_master_feedback">対象製品を選ぶと候補を絞り込めます。製品の追加や修正は <a href="<?php echo esc_url( $master_list_url ); ?>">製品マスター</a> から行えます。<a href="<?php echo esc_url( $master_create_url ); ?>">新規追加</a></p>
                <?php if ( ! $selected_product_id && '' !== $product_name ) : ?>
                    <p class="description">現在の登録値: <?php echo esc_html( $product_name ); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <tr><th><label for="rinascente_quantity">数量</label></th><td><input type="text" name="rinascente_quantity" id="rinascente_quantity" value="<?php echo esc_attr( $quantity ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="rinascente_order_date">注文日</label></th><td><input type="date" name="rinascente_order_date" id="rinascente_order_date" value="<?php echo esc_attr( $order_date ); ?>"></td></tr>
        <tr><th><label for="rinascente_delivery_date">納品日</label></th><td><input type="date" name="rinascente_delivery_date" id="rinascente_delivery_date" value="<?php echo esc_attr( $delivery_date ); ?>"></td></tr>
        <tr><th><label for="rinascente_contract_date">契約日</label></th><td><input type="date" name="rinascente_contract_date" id="rinascente_contract_date" value="<?php echo esc_attr( $contract_date ); ?>"></td></tr>
        <tr>
            <th><label for="rinascente_contract_status">ステータス</label></th>
            <td>
                <select name="rinascente_contract_status" id="rinascente_contract_status">
                    <?php foreach ( rinascente_contract_status_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_payment_status">支払いステータス</label></th>
            <td>
                <select name="rinascente_payment_status" id="rinascente_payment_status">
                    <?php foreach ( rinascente_contract_payment_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $payment_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_contract_info">契約情報</label></th>
            <td>
                <textarea name="rinascente_contract_info" id="rinascente_contract_info" rows="4" class="large-text"><?php echo esc_textarea( $contract_info ); ?></textarea>
                <p class="description"><button type="button" class="button-link" id="rinascente_apply_contract_template">テンプレートを反映</button> 製品マスターに登録した契約情報のひな形を流し込みます。</p>
                <div id="rinascente_contract_clone_feedback" class="rinascente-contract-feedback" aria-live="polite"></div>
            </td>
        </tr>
        <tr><th><label for="rinascente_contract_notes">備考</label></th><td><textarea name="rinascente_contract_notes" id="rinascente_contract_notes" rows="3" class="large-text"><?php echo esc_textarea( $notes ); ?></textarea></td></tr>
    </table>
    <div class="rinascente-submit-actions rinascente-submit-actions--inline">
        <div class="rinascente-submit-actions__label">
            <strong>保存オプション</strong>
            <span class="description">入力内容を保存したあと、そのまま次の登録や会員ページ確認へ進めます。</span>
        </div>
        <div class="rinascente-submit-actions__buttons">
            <button type="button" class="button button-secondary rinascente-contract-save-action" data-after-save="new_contract">保存して次を登録</button>
            <button type="button" class="button button-secondary rinascente-contract-save-action" data-after-save="member_preview">保存して会員ページ確認</button>
        </div>
    </div>
    <script type="application/json" id="rinascente_contract_history_json"><?php echo wp_json_encode( $admin_contract_data['history'] ); ?></script>
    <script type="application/json" id="rinascente_contract_order_index_json"><?php echo wp_json_encode( $admin_contract_data['orderIndex'] ); ?></script>
    <script type="application/json" id="rinascente_contract_post_id_json"><?php echo wp_json_encode( (int) $post->ID ); ?></script>
    <?php
}

function rinascente_product_master_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $product_choices = rinascente_member_product_choices();
    $product_key = get_post_meta( $post->ID, '_rinascente_product_key', true );
    if ( '' === (string) $product_key && 1 === count( $product_choices ) ) {
        $product_key = (string) array_key_first( $product_choices );
    }
    $category    = get_post_meta( $post->ID, '_rinascente_product_catalog_category', true );
    $sort_order  = get_post_meta( $post->ID, '_rinascente_product_catalog_sort_order', true );
    $code        = get_post_meta( $post->ID, '_rinascente_product_catalog_code', true );
    $display_name = get_post_meta( $post->ID, '_rinascente_product_catalog_display_name', true );
    $short_name  = get_post_meta( $post->ID, '_rinascente_product_catalog_short_name', true );
    $spec        = get_post_meta( $post->ID, '_rinascente_product_catalog_spec', true );
    $install_type = get_post_meta( $post->ID, '_rinascente_product_catalog_install_type', true );
    $max_rail_length = get_post_meta( $post->ID, '_rinascente_product_catalog_max_rail_length', true );
    $rail_length_options = rinascente_member_parse_rail_length_options( get_post_meta( $post->ID, '_rinascente_product_catalog_rail_length_options', true ) );
    $unit_price  = get_post_meta( $post->ID, '_rinascente_product_catalog_unit_price', true );
    $rail_price_per_m = get_post_meta( $post->ID, '_rinascente_product_catalog_rail_price_per_m', true );
    $pricing_option_key = get_post_meta( $post->ID, '_rinascente_product_catalog_pricing_option_key', true );
    $unit_label  = get_post_meta( $post->ID, '_rinascente_product_catalog_unit_label', true );
    $max_quantity = get_post_meta( $post->ID, '_rinascente_product_catalog_max_quantity', true );
    $selection_type = get_post_meta( $post->ID, '_rinascente_product_catalog_selection_type', true );
    $source_note = get_post_meta( $post->ID, '_rinascente_product_catalog_source', true );
    $contract_template = get_post_meta( $post->ID, '_rinascente_product_catalog_contract_template', true );
    if ( '' === (string) $display_name ) {
        $display_name = $post->post_title;
    }
    if ( '' === (string) $short_name ) {
        $short_name = $display_name;
    }
    ?>
    <div class="rinascente-product-master-guide">
        <h3>かんたん設定ガイド</h3>
        <p>迷ったら、まずは <strong>どのサイトの商品か</strong>、<strong>区分</strong>、<strong>お客様向け商品名</strong>、<strong>短い呼び名</strong>、<strong>価格</strong> の 5 つだけで登録できます。</p>
        <ul>
            <li>本体システム: 価格に「基本価格」を入れ、必要なら設置方式やレール情報を追加</li>
            <li>オプション / ハーネス: 価格に「単価」を入れ、数量単位や最大数量を確認</li>
            <li>保存すると、管理用タイトルは「お客様向け商品名」と同じ内容に自動で揃います</li>
        </ul>
        <div id="rinascente_product_master_recommendation" class="rinascente-product-master-guide__recommendation" aria-live="polite"></div>
    </div>

    <div class="rinascente-product-master-section">
        <div class="rinascente-product-master-section__head">
            <h3>1. 最低限の入力</h3>
            <button type="button" class="button button-secondary" id="rinascente_product_master_apply_preset">区分に合わせておすすめ値を入れる</button>
        </div>
        <table class="form-table">
            <tr>
                <th><label for="rinascente_product_key">どのサイトの商品か</label></th>
                <td>
                    <input type="text" name="rinascente_product_key" id="rinascente_product_key" value="<?php echo esc_attr( $product_key ); ?>" class="regular-text" list="rinascente_product_key_suggestions" placeholder="例: yumeho">
                    <datalist id="rinascente_product_key_suggestions">
                        <?php foreach ( $product_choices as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </datalist>
                    <p class="description">通常は <code>yumeho</code> です。将来ほかの商品サイトを追加するときは、そのキーを入力します。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_category">区分</label></th>
                <td>
                    <select name="rinascente_product_catalog_category" id="rinascente_product_catalog_category">
                        <?php foreach ( rinascente_member_product_master_category_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">「本体システム」「ハーネス」「オプション」など、商品の種類を選びます。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_display_name">お客様向け商品名</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_display_name" id="rinascente_product_catalog_display_name" value="<?php echo esc_attr( $display_name ); ?>" class="large-text" placeholder="例: スタンド型 PGT-9000">
                    <p class="description">サイトや会員ページに出す正式な名前です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_short_name">短い呼び名</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_short_name" id="rinascente_product_catalog_short_name" value="<?php echo esc_attr( $short_name ); ?>" class="regular-text" placeholder="例: PGT-9000">
                    <p class="description">見積結果やカード表示で短く出したい名前です。未定なら商品名と同じで大丈夫です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_unit_price">価格（税別）</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_unit_price" id="rinascente_product_catalog_unit_price" value="<?php echo esc_attr( '' !== (string) $unit_price ? number_format( (int) $unit_price ) : '' ); ?>" class="regular-text" placeholder="例: 1,150,000">
                    <p class="description">本体システムなら基本価格、オプションやハーネスなら 1 個 / 1 台あたりの単価を入れます。</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="rinascente-product-master-section">
        <h3>2. 必要に応じて入力</h3>
        <table class="form-table">
            <tr>
                <th><label for="rinascente_product_catalog_spec">仕様補足</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_spec" id="rinascente_product_catalog_spec" value="<?php echo esc_attr( $spec ); ?>" class="large-text" placeholder="例: 2000×4000mm / 総レール長14m">
                    <p class="description">サイズや仕様の補足があるときだけ入れます。</p>
                </td>
            </tr>
            <tr class="rinascente-product-master-row rinascente-product-master-row--system">
                <th><label for="rinascente_product_catalog_install_type">設置方式</label></th>
                <td>
                    <select name="rinascente_product_catalog_install_type" id="rinascente_product_catalog_install_type">
                        <?php foreach ( rinascente_member_product_install_type_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $install_type, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">本体システムだけで使います。オプションやハーネスなら空欄のままで大丈夫です。</p>
                </td>
            </tr>
            <tr class="rinascente-product-master-row rinascente-product-master-row--system">
                <th><label for="rinascente_product_catalog_max_rail_length">自動選択するレール長の上限</label></th>
                <td>
                    <input type="number" min="0" name="rinascente_product_catalog_max_rail_length" id="rinascente_product_catalog_max_rail_length" value="<?php echo esc_attr( '' !== (string) $max_rail_length ? $max_rail_length : '0' ); ?>" class="small-text">
                    <span class="description">スタンド型の自動判定に使います。不要なら 0 のままで大丈夫です。</span>
                </td>
            </tr>
            <tr class="rinascente-product-master-row rinascente-product-master-row--system">
                <th><label for="rinascente_product_catalog_rail_length_options">選択式レール長</label></th>
                <td>
                    <div class="rinascente-rail-options-editor" data-rail-options-editor>
                        <div class="rinascente-rail-options-list" id="rinascente_product_catalog_rail_length_options">
                            <?php if ( ! empty( $rail_length_options ) ) : ?>
                                <?php foreach ( $rail_length_options as $rail_length_option ) : ?>
                                    <div class="rinascente-rail-options-row" data-rail-option-row>
                                        <input type="number" min="1" step="1" name="rinascente_product_catalog_rail_length_options[]" value="<?php echo esc_attr( (int) $rail_length_option ); ?>" class="small-text" data-rail-option-input>
                                        <span class="description">m</span>
                                        <button type="button" class="button-link-delete" data-rail-option-remove>削除</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="rinascente-rail-options-row" data-rail-option-row>
                                    <input type="number" min="1" step="1" name="rinascente_product_catalog_rail_length_options[]" value="" class="small-text" data-rail-option-input>
                                    <span class="description">m</span>
                                    <button type="button" class="button-link-delete" data-rail-option-remove>削除</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="button button-secondary" data-rail-option-add>長さを追加</button>
                    </div>
                    <p class="description">天井型などの選択式にしたい長さを、1件ずつ追加してください。YUMEHO の見積シミュレーションに自動反映されます。</p>
                </td>
            </tr>
            <tr class="rinascente-product-master-row rinascente-product-master-row--system">
                <th><label for="rinascente_product_catalog_rail_price_per_m">レール単価 / m</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_rail_price_per_m" id="rinascente_product_catalog_rail_price_per_m" value="<?php echo esc_attr( '' !== (string) $rail_price_per_m ? number_format( (int) $rail_price_per_m ) : '' ); ?>" class="regular-text" placeholder="例: 30,000">
                    <p class="description">本体システムだけで使います。レール価格がない商品は空欄で大丈夫です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_unit_label">数量の単位</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_unit_label" id="rinascente_product_catalog_unit_label" value="<?php echo esc_attr( $unit_label ); ?>" class="small-text" placeholder="台 / 着 / 個">
                    <p class="description">例: 本体は「台」、ハーネスは「着」です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_max_quantity">一度に選べる最大数</label></th>
                <td>
                    <input type="number" min="0" name="rinascente_product_catalog_max_quantity" id="rinascente_product_catalog_max_quantity" value="<?php echo esc_attr( '' !== (string) $max_quantity ? $max_quantity : '0' ); ?>" class="small-text">
                    <p class="description">1 個までなら 1、複数選べるなら 5 などを入れます。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_selection_type">YUMEHO での選び方</label></th>
                <td>
                    <select name="rinascente_product_catalog_selection_type" id="rinascente_product_catalog_selection_type">
                        <?php foreach ( rinascente_member_product_selection_type_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $selection_type ? $selection_type : 'quantity', $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">1つだけのオン / オフなら「オン / オフ」、数量を選ばせたいものは「数量選択」です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_sort_order">並び順</label></th>
                <td>
                    <input type="number" name="rinascente_product_catalog_sort_order" id="rinascente_product_catalog_sort_order" value="<?php echo esc_attr( '' !== $sort_order ? $sort_order : '999' ); ?>" class="small-text">
                    <span class="description">小さい数字ほど上に表示されます。迷ったらそのままで大丈夫です。</span>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_contract_template">契約情報のひな形</label></th>
                <td>
                    <textarea name="rinascente_product_catalog_contract_template" id="rinascente_product_catalog_contract_template" rows="3" class="large-text"><?php echo esc_textarea( $contract_template ); ?></textarea>
                    <p class="description">契約・購入履歴で「テンプレートを反映」を押したときに入る文です。</p>
                </td>
            </tr>
        </table>
    </div>

    <details class="rinascente-product-master-advanced">
        <summary>3. 連携の詳細（上級者向け）</summary>
        <p class="description">通常は空欄のままでも大丈夫です。既存の製品で使っている値を変更すると、ほかの画面の連携に影響することがあります。</p>
        <table class="form-table">
            <tr>
                <th><label for="rinascente_product_catalog_code">商品コード（半角）</label></th>
                <td>
                    <div class="rinascente-product-master-inline">
                        <input type="text" name="rinascente_product_catalog_code" id="rinascente_product_catalog_code" value="<?php echo esc_attr( $code ); ?>" class="regular-text" placeholder="例: pgt-9000">
                        <button type="button" class="button button-secondary" id="rinascente_product_catalog_code_generate">短縮名から自動入力</button>
                    </div>
                    <p class="description">他サイトから参照する安定したコードです。半角英数とハイフン推奨です。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_pricing_option_key">価格連携キー</label></th>
                <td>
                    <input type="text" name="rinascente_product_catalog_pricing_option_key" id="rinascente_product_catalog_pricing_option_key" value="<?php echo esc_attr( $pricing_option_key ); ?>" class="regular-text" placeholder="例: jrx">
                    <p class="description">YUMEHO の価格計算に使う内部キーです。既存製品を編集するときは、基本的に変更しないでください。</p>
                </td>
            </tr>
            <tr>
                <th><label for="rinascente_product_catalog_source">参照元メモ</label></th>
                <td>
                    <textarea name="rinascente_product_catalog_source" id="rinascente_product_catalog_source" rows="3" class="large-text"><?php echo esc_textarea( $source_note ); ?></textarea>
                    <p class="description">商品ページや見積資料など、元にした情報を残したいときのメモです。</p>
                </td>
            </tr>
        </table>
    </details>
    <?php
}

function rinascente_member_video_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $youtube_id    = get_post_meta( $post->ID, '_rinascente_youtube_id', true );
    $description   = get_post_meta( $post->ID, '_rinascente_video_description', true );
    $category      = get_post_meta( $post->ID, '_rinascente_video_category', true );
    $product_key   = get_post_meta( $post->ID, '_rinascente_product_key', true );
    $start_date    = get_post_meta( $post->ID, '_rinascente_start_date', true );
    $end_date      = get_post_meta( $post->ID, '_rinascente_end_date', true );
    $video_input   = rinascente_extract_youtube_video_id( $youtube_id );
    $preview_id    = preg_match( '/^[A-Za-z0-9_-]{11}$/', $video_input ) ? $video_input : '';
    $preview_state = rinascente_member_youtube_video_state( $preview_id );
    $preview_url   = ! empty( $preview_state['is_available'] ) ? $preview_state['watch_url'] : '';
    $thumb_url     = ! empty( $preview_state['is_available'] ) ? $preview_state['thumbnail_url'] : '';
    ?>
    <div class="rinascente-member-video-form">
        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>1. 動画情報</h3>
                <p>会員ページに出す動画そのものを登録します。まずは YouTube と説明文を入れれば土台が整います。</p>
            </div>
            <div class="rinascente-member-video-form__grid">
                <div class="rinascente-member-video-form__field rinascente-member-video-form__field--wide">
                    <label for="rinascente_youtube_id">YouTube URL または 動画ID</label>
                    <input type="text" name="rinascente_youtube_id" id="rinascente_youtube_id" value="<?php echo esc_attr( $video_input ); ?>" class="large-text" placeholder="https://www.youtube.com/watch?v=... または M7lc1UVf-VE">
                    <p class="description">共有URLをそのまま貼っても大丈夫です。保存時に動画IDへ整えて保存します。</p>
                </div>

                <div class="rinascente-member-video-preview<?php echo $preview_id && ! empty( $preview_state['is_available'] ) ? '' : ' is-empty'; ?>" id="rinascente_member_video_preview">
                    <div class="rinascente-member-video-preview__media">
                        <?php if ( $preview_id && ! empty( $preview_state['is_available'] ) ) : ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="" id="rinascente_member_video_preview_image">
                        <?php else : ?>
                        <div class="rinascente-member-video-preview__placeholder" id="rinascente_member_video_preview_placeholder"><?php echo esc_html( $preview_id ? 'この動画は現在確認できません。公開状態か動画IDを見直してください。' : 'YouTube 情報を入れると、ここで動画の確認ができます。' ); ?></div>
                        <img src="" alt="" id="rinascente_member_video_preview_image" hidden>
                        <?php endif; ?>
                    </div>
                    <div class="rinascente-member-video-preview__body">
                        <p class="rinascente-member-video-preview__label">動画プレビュー</p>
                        <strong id="rinascente_member_video_preview_id"><?php echo esc_html( $preview_id ? $preview_id : '未入力' ); ?></strong>
                        <p class="description" id="rinascente_member_video_preview_help">
                            <?php if ( $preview_id && ! empty( $preview_state['is_available'] ) ) : ?>
                                この動画IDで会員ページに表示されます。
                            <?php elseif ( $preview_id ) : ?>
                                YouTube 側で動画を確認できませんでした。公開状態か動画IDを見直してください。
                            <?php else : ?>
                                URLや動画IDが正しく入ると、サムネイルと確認リンクが表示されます。
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo esc_url( $preview_url ? $preview_url : '#' ); ?>" class="button button-secondary<?php echo $preview_url ? '' : ' disabled'; ?>" id="rinascente_member_video_preview_link" target="_blank" rel="noopener noreferrer"<?php echo $preview_url ? '' : ' aria-disabled="true" tabindex="-1"'; ?>>YouTubeで確認</a>
                    </div>
                </div>

                <div class="rinascente-member-video-form__field rinascente-member-video-form__field--wide">
                    <label for="rinascente_video_description">説明文</label>
                    <textarea name="rinascente_video_description" id="rinascente_video_description" rows="5" class="large-text" placeholder="この動画で何が分かるか、どんなときに見る動画かを短く書いてください。"><?php echo esc_textarea( $description ); ?></textarea>
                    <p class="description">会員ページの一覧と詳細で使う説明です。1〜3文くらいで用途が伝わると見やすくなります。</p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>2. 表示設定</h3>
                <p>どの種類の動画か、どの製品向けかをここで決めます。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_video_category">カテゴリ</label>
                    <select name="rinascente_video_category" id="rinascente_video_category" class="widefat">
                        <?php foreach ( rinascente_member_video_category_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">設置前の案内なら「設置方法」、日々の使い方なら「利用方法」が目安です。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_video_product_key">対象製品</label>
                    <select name="rinascente_product_key" id="rinascente_video_product_key" class="widefat">
                        <option value="">共通</option>
                        <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">製品をまたいで見せたい動画は「共通」のままで大丈夫です。</p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>3. 公開期間</h3>
                <p>空欄ならすぐ表示されます。期間を入れると、公開タイミングを予約できます。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_start_date">公開開始日</label>
                    <input type="date" name="rinascente_start_date" id="rinascente_start_date" value="<?php echo esc_attr( $start_date ); ?>">
                    <p class="description">指定日までは表示しません。すぐ見せる場合は空欄で問題ありません。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_end_date">公開終了日</label>
                    <input type="date" name="rinascente_end_date" id="rinascente_end_date" value="<?php echo esc_attr( $end_date ); ?>">
                    <p class="description">期限付きの案内動画だけ設定してください。常時公開なら空欄で大丈夫です。</p>
                </div>
            </div>
        </section>
    </div>
    <?php
}

function rinascente_member_document_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $attachment_id     = absint( get_post_meta( $post->ID, '_rinascente_attachment_id', true ) );
    $category          = get_post_meta( $post->ID, '_rinascente_document_category', true );
    $product_key       = get_post_meta( $post->ID, '_rinascente_product_key', true );
    $updated_date      = get_post_meta( $post->ID, '_rinascente_document_updated_date', true );
    $program_name      = get_post_meta( $post->ID, '_rinascente_program_name', true );
    $subsidy_state     = get_post_meta( $post->ID, '_rinascente_subsidy_status', true );
    $start_date        = get_post_meta( $post->ID, '_rinascente_start_date', true );
    $end_date          = get_post_meta( $post->ID, '_rinascente_end_date', true );
    $attachment        = $attachment_id ? get_post( $attachment_id ) : null;
    $file_data         = rinascente_member_document_file_data( $post->ID );
    $category_choices  = rinascente_member_document_category_choices();
    $category_guides   = rinascente_member_document_category_guides();
    $category_label    = $category_choices[ $category ] ?? '未設定';
    $category_guide    = $category_guides[ $category ] ?? '資料の用途に合うカテゴリを選ぶと、会員ページと一覧の両方で探しやすくなります。';
    $product_label     = $product_key ? rinascente_member_product_label( $product_key ) : '共通';
    $schedule_snapshot = rinascente_member_admin_schedule_snapshot( $post->ID );
    $summary_title     = get_the_title( $post );
    $summary_title     = '' !== trim( $summary_title ) ? $summary_title : '資料名を入れるとここに表示されます。';
    $subsidy_summary   = implode( ' / ', array_filter( array( trim( (string) $program_name ), trim( (string) $subsidy_state ) ) ) );
    if ( '' === $subsidy_summary ) {
        $subsidy_summary = 'subsidy' === $category ? '制度名や認定ステータスを入れると管理しやすくなります。' : '特になし';
    }
    $attachment_url = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
    $file_title     = $attachment ? $attachment->post_title : '';
    if ( '' === trim( $file_title ) ) {
        $file_title = $file_data['filename'];
    }
    $file_format = '';
    if ( $file_data['filename'] ) {
        $file_format = strtoupper( (string) pathinfo( $file_data['filename'], PATHINFO_EXTENSION ) );
    }
    if ( '' === $file_format && $file_data['mime'] ) {
        $file_format = strtoupper( (string) preg_replace( '/^.*\//', '', $file_data['mime'] ) );
    }
    $file_meta = array_filter(
        array(
            $file_data['filename'],
            $file_format,
            $file_data['size'],
        )
    );
    ?>
    <div class="rinascente-member-video-form">
        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>1. 資料名と基本設定</h3>
                <p>タイトル入力と、登録時によく見るガイドをこの中へまとめています。ここから続けて入力すれば、そのまま資料登録を完了できます。</p>
            </div>
            <div class="rinascente-member-document-title-head">
                <label for="title"><strong>資料名</strong></label>
                <p class="description">会員ページの一覧やダウンロードカードに表示される名前です。資料の用途が一目で伝わる短い名前がおすすめです。</p>
            </div>
            <div class="rinascente-member-document-title-slot" id="rinascente_member_document_title_slot"></div>
            <?php rinascente_member_content_top_fields_html( $post ); ?>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>2. 資料ファイル</h3>
                <p>会員ページからダウンロードしてもらうファイルを選びます。まずここを入れると、資料登録の中心が固まります。</p>
            </div>
            <div class="rinascente-member-video-form__grid">
                <div class="rinascente-member-video-form__field rinascente-member-video-form__field--wide">
                    <label for="rinascente_attachment_id">ダウンロード用ファイル</label>
                    <input type="hidden" name="rinascente_attachment_id" id="rinascente_attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>">
                    <div class="rinascente-member-document-actions">
                        <button type="button" class="button button-secondary rinascente-media-select">ファイルを選ぶ</button>
                        <button type="button" class="button-link-delete rinascente-media-clear" <?php disabled( ! $attachment_id ); ?>>解除</button>
                        <button type="button" class="button button-secondary rinascente-document-fill-title" <?php disabled( ! $attachment_id ); ?>>資料名に反映</button>
                        <button type="button" class="button button-secondary rinascente-document-set-today">更新日を今日にする</button>
                    </div>
                    <p class="description">PDFやWordなど、会員へ配布する最終ファイルを選んでください。差し替えたいときは、もう一度「ファイルを選ぶ」で上書きできます。</p>
                    <p class="rinascente-member-document-helper">ファイル選択時に、資料名が空欄ならファイル名から自動で補完します。更新日が未入力なら本日の日付も補います。</p>
                </div>

                <div class="rinascente-member-document-preview<?php echo $attachment_id ? '' : ' is-empty'; ?>" id="rinascente_member_document_preview">
                    <div class="rinascente-member-document-preview__icon" id="rinascente_member_document_preview_icon"><?php echo esc_html( $file_format ? $file_format : 'FILE' ); ?></div>
                    <div class="rinascente-member-document-preview__body">
                        <p class="rinascente-member-video-preview__label">選択中のファイル</p>
                        <strong id="rinascente_member_document_preview_name"><?php echo esc_html( $file_title ? $file_title : '未選択' ); ?></strong>
                        <p class="description" id="rinascente_member_document_preview_meta">
                            <?php echo esc_html( $file_meta ? implode( ' / ', $file_meta ) : 'ファイルを選ぶと、ここで形式とサイズを確認できます。' ); ?>
                        </p>
                        <p class="description" id="rinascente_member_document_preview_help">
                            <?php if ( $attachment_id ) : ?>
                                このファイルが会員ページからダウンロードされます。
                            <?php else : ?>
                                ファイルを選ぶと、会員向けに表示される資料情報をここで確認できます。
                            <?php endif; ?>
                        </p>
                        <a href="<?php echo esc_url( $attachment_url ? $attachment_url : '#' ); ?>" class="button button-secondary<?php echo $attachment_url ? '' : ' disabled'; ?>" id="rinascente_member_document_preview_link" target="_blank" rel="noopener noreferrer"<?php echo $attachment_url ? '' : ' aria-disabled="true" tabindex="-1"'; ?>>ファイルを確認</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>3. 表示設定</h3>
                <p>資料の種類、対象製品、更新日を決めます。会員一覧ではこの情報がそのまま案内に使われます。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_document_category">カテゴリ</label>
                    <select name="rinascente_document_category" id="rinascente_document_category" class="widefat">
                        <?php foreach ( rinascente_member_document_category_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">操作手順なら「取扱説明書」、申請まわりなら「稟議資料」など、会員が探しやすい種類を選んでください。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_document_product_key">対象製品</label>
                    <select name="rinascente_product_key" id="rinascente_document_product_key" class="widefat">
                        <option value="">共通</option>
                        <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">すべての会員に見せる資料は「共通」のままで大丈夫です。製品専用資料だけ対象製品を指定してください。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_document_updated_date">更新日</label>
                    <input type="date" name="rinascente_document_updated_date" id="rinascente_document_updated_date" value="<?php echo esc_attr( $updated_date ); ?>">
                    <p class="description">資料を差し替えた日や、会員に伝えたい版の基準日を入れておくと分かりやすくなります。</p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>4. 公開イメージ</h3>
                <p>会員ページにどう見えるかと、更新時に見直したい項目をまとめています。</p>
            </div>
            <div class="rinascente-member-document-summary" id="rinascente_member_document_summary">
                <div class="rinascente-member-document-summary__header">
                    <p class="rinascente-member-video-preview__label">Member Page Snapshot</p>
                    <strong id="rinascente_member_document_summary_title"><?php echo esc_html( $summary_title ); ?></strong>
                    <p class="description" id="rinascente_member_document_summary_file">
                        <?php echo esc_html( $file_meta ? implode( ' / ', $file_meta ) : 'ファイルを選ぶと、ここで形式とサイズを確認できます。' ); ?>
                    </p>
                </div>

                <div class="rinascente-member-document-summary__grid">
                    <div class="rinascente-member-document-summary__item">
                        <span>カテゴリ</span>
                        <strong id="rinascente_member_document_summary_category"><?php echo esc_html( $category_label ); ?></strong>
                    </div>

                    <div class="rinascente-member-document-summary__item">
                        <span>対象製品</span>
                        <strong id="rinascente_member_document_summary_product"><?php echo esc_html( $product_label ); ?></strong>
                    </div>

                    <div class="rinascente-member-document-summary__item">
                        <span>更新日</span>
                        <strong id="rinascente_member_document_summary_updated"><?php echo esc_html( '' !== trim( (string) $updated_date ) ? rinascente_member_format_date( $updated_date ) : '未設定' ); ?></strong>
                    </div>

                    <div class="rinascente-member-document-summary__item">
                        <span>公開状況</span>
                        <strong id="rinascente_member_document_summary_schedule"><?php echo esc_html( $schedule_snapshot['label'] ); ?></strong>
                    </div>

                    <div class="rinascente-member-document-summary__item rinascente-member-document-summary__item--wide">
                        <span>補足メモ</span>
                        <strong id="rinascente_member_document_summary_subsidy"><?php echo esc_html( $subsidy_summary ); ?></strong>
                    </div>
                </div>

                <div class="rinascente-member-document-summary__guide">
                    <span class="rinascente-member-document-summary__eyebrow">更新のポイント</span>
                    <p id="rinascente_member_document_summary_guide"><?php echo esc_html( $category_guide ); ?></p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>5. 制度・補助金メモ</h3>
                <p>補助金資料など、制度にひもづく資料だけ補足を入れてください。通常資料なら空欄でも問題ありません。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_program_name">制度名</label>
                    <input type="text" name="rinascente_program_name" id="rinascente_program_name" value="<?php echo esc_attr( $program_name ); ?>" class="regular-text" placeholder="例: 介護テクノロジー導入支援事業">
                    <p class="description">資料が特定の制度向けなら、その制度名を入れておくと検索や案内に役立ちます。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_subsidy_status">認定ステータス</label>
                    <input type="text" name="rinascente_subsidy_status" id="rinascente_subsidy_status" value="<?php echo esc_attr( $subsidy_state ); ?>" class="regular-text" placeholder="例: 対象 / 非対象 / 認定済み">
                    <p class="description">補助金の対象状況をひとことで残したいときだけ使ってください。不要なら空欄で構いません。</p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>6. 公開期間</h3>
                <p>空欄ならすぐ表示されます。期間を入れると、期限付きの資料だけ公開タイミングを調整できます。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_start_date">公開開始日</label>
                    <input type="date" name="rinascente_start_date" id="rinascente_start_date" value="<?php echo esc_attr( $start_date ); ?>">
                    <p class="description">指定日までは会員ページに出しません。すぐ公開する資料は空欄で大丈夫です。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_end_date">公開終了日</label>
                    <input type="date" name="rinascente_end_date" id="rinascente_end_date" value="<?php echo esc_attr( $end_date ); ?>">
                    <p class="description">期限が決まっている資料だけ設定してください。常時公開なら空欄のままで問題ありません。</p>
                </div>
            </div>
        </section>
    </div>
    <?php
}

function rinascente_member_review_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $users          = rinascente_member_get_users();
    $selected_user  = (int) get_post_meta( $post->ID, '_rinascente_member_user_id', true );
    $author_name    = get_post_meta( $post->ID, '_rinascente_author_name', true );
    $facility_name  = get_post_meta( $post->ID, '_rinascente_review_facility_name', true );
    $facility_type  = get_post_meta( $post->ID, '_rinascente_review_facility_type', true );
    $adoption       = get_post_meta( $post->ID, '_rinascente_adoption_period', true );
    $rating         = (int) get_post_meta( $post->ID, '_rinascente_review_rating', true );
    $tags           = get_post_meta( $post->ID, '_rinascente_review_tags', true );
    $helpful_count  = (int) get_post_meta( $post->ID, '_rinascente_helpful_count', true );
    $product_key    = get_post_meta( $post->ID, '_rinascente_product_key', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="rinascente_review_member_user_id">会員ユーザー</label></th>
            <td>
                <select name="rinascente_member_user_id" id="rinascente_review_member_user_id" class="regular-text">
                    <option value="">未紐付け</option>
                    <?php foreach ( $users as $user ) : ?>
                        <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $selected_user, $user->ID ); ?>><?php echo esc_html( rinascente_member_user_name( $user ) . ' (' . $user->user_login . ')' ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="rinascente_author_name">記入者名</label></th><td><input type="text" name="rinascente_author_name" id="rinascente_author_name" value="<?php echo esc_attr( $author_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="rinascente_review_facility_name">施設名</label></th><td><input type="text" name="rinascente_review_facility_name" id="rinascente_review_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="rinascente_review_facility_type">施設種別</label></th><td><input type="text" name="rinascente_review_facility_type" id="rinascente_review_facility_type" value="<?php echo esc_attr( $facility_type ); ?>" class="regular-text"></td></tr>
        <tr>
            <th><label for="rinascente_adoption_period">導入時期</label></th>
            <td>
                <select name="rinascente_adoption_period" id="rinascente_adoption_period">
                    <?php foreach ( rinascente_member_review_period_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $adoption, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="rinascente_review_rating">評価</label></th>
            <td>
                <select name="rinascente_review_rating" id="rinascente_review_rating">
                    <?php for ( $i = 5; $i >= 1; --$i ) : ?>
                        <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>><?php echo esc_html( $i . ' / 5' ); ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="rinascente_review_tags">タグ</label></th><td><input type="text" name="rinascente_review_tags" id="rinascente_review_tags" value="<?php echo esc_attr( $tags ); ?>" class="regular-text" placeholder="カンマ区切り"></td></tr>
        <tr><th><label for="rinascente_helpful_count">参考になった数</label></th><td><input type="number" min="0" name="rinascente_helpful_count" id="rinascente_helpful_count" value="<?php echo esc_attr( $helpful_count ); ?>"></td></tr>
        <tr>
            <th><label for="rinascente_review_product_key">対象製品</label></th>
            <td>
                <select name="rinascente_product_key" id="rinascente_review_product_key">
                    <option value="">共通</option>
                    <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function rinascente_member_notice_meta_box_html( $post ) {
    rinascente_member_nonce_field();
    $notice_tone = get_post_meta( $post->ID, '_rinascente_notice_tone', true );
    $product_key = get_post_meta( $post->ID, '_rinascente_product_key', true );
    $start_date  = get_post_meta( $post->ID, '_rinascente_start_date', true );
    $end_date    = get_post_meta( $post->ID, '_rinascente_end_date', true );
    $tone_label  = rinascente_member_notice_tone_choices()[ $notice_tone ] ?? '一般';
    $tone_notes  = array(
        'maintenance' => '保守や運用に関する案内として、ゴールド系の印象で表示されます。',
        'urgent'      => '重要な連絡として、赤系の強い見え方で表示されます。',
        'info'        => '通常のお知らせとして、青系の印象で表示されます。',
        'neutral'     => '分類を強く出さない一般連絡として、落ち着いた見え方で表示されます。',
    );
    $notice_help = $tone_notes[ $notice_tone ] ?? $tone_notes['neutral'];
    $summary     = rinascente_member_admin_compact_text( rinascente_member_notice_summary( $post ), 110 );
    $summary_source = '' !== trim( (string) $post->post_excerpt ) ? '一覧用要約を使用中' : ( '' !== trim( wp_strip_all_tags( (string) $post->post_content ) ) ? '本文の冒頭を自動使用' : '要約はまだ未入力です' );
    $product_label = '' !== trim( (string) $product_key ) ? rinascente_member_product_label( $product_key ) : '共通';
    ?>
    <div class="rinascente-member-video-form">
        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>1. 表示設定</h3>
                <p>どんな種類の案内か、どの製品向けかを決めます。ここで選んだ内容が会員ページの見え方にそのまま反映されます。</p>
            </div>
            <div class="rinascente-member-video-form__grid">
                <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                    <div class="rinascente-member-video-form__field">
                        <label for="rinascente_notice_tone">表示タイプ</label>
                        <select name="rinascente_notice_tone" id="rinascente_notice_tone" class="widefat">
                            <?php foreach ( rinascente_member_notice_tone_choices() as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $notice_tone, $value ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">重要な連絡は「重要」、保守案内は「保守・運用」、通常案内は「お知らせ」や「一般」が目安です。</p>
                    </div>

                    <div class="rinascente-member-video-form__field">
                        <label for="rinascente_notice_product_key">対象製品</label>
                        <select name="rinascente_product_key" id="rinascente_notice_product_key" class="widefat">
                            <option value="">共通</option>
                            <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description">全会員向けの案内は「共通」のままで大丈夫です。製品別連絡だけ対象製品を指定してください。</p>
                    </div>
                </div>

                <div class="rinascente-member-notice-preview" id="rinascente_member_notice_preview">
                    <p class="rinascente-member-video-preview__label">会員ページでの見え方</p>
                    <div class="rinascente-member-notice-preview__meta">
                        <span class="rinascente-member-notice-badge rinascente-member-notice-badge--<?php echo esc_attr( $notice_tone ? $notice_tone : 'neutral' ); ?>" id="rinascente_member_notice_preview_badge"><?php echo esc_html( $tone_label ); ?></span>
                        <span class="rinascente-member-notice-preview__source" id="rinascente_member_notice_preview_source"><?php echo esc_html( $summary_source ); ?></span>
                    </div>
                    <strong id="rinascente_member_notice_preview_title"><?php echo esc_html( get_the_title( $post ) ? get_the_title( $post ) : 'タイトル未入力' ); ?></strong>
                    <p class="description" id="rinascente_member_notice_preview_summary"><?php echo esc_html( '' !== $summary ? $summary : '本文か一覧用要約を入れると、ここに会員向けの見え方が表示されます。' ); ?></p>
                    <p class="description" id="rinascente_member_notice_preview_help"><?php echo esc_html( $notice_help . ' 対象製品: ' . $product_label ); ?></p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>2. 公開期間</h3>
                <p>空欄ならすぐ表示されます。期限付きの案内だけ、公開開始日や終了日を入れてください。</p>
            </div>
            <div class="rinascente-member-video-form__grid rinascente-member-video-form__grid--compact">
                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_start_date">公開開始日</label>
                    <input type="date" name="rinascente_start_date" id="rinascente_start_date" value="<?php echo esc_attr( $start_date ); ?>">
                    <p class="description">指定日までは会員ページに出しません。すぐ見せる案内なら空欄で大丈夫です。</p>
                </div>

                <div class="rinascente-member-video-form__field">
                    <label for="rinascente_end_date">公開終了日</label>
                    <input type="date" name="rinascente_end_date" id="rinascente_end_date" value="<?php echo esc_attr( $end_date ); ?>">
                    <p class="description">期間限定の案内だけ設定してください。常時見せたい情報なら空欄のままで問題ありません。</p>
                </div>
            </div>
        </section>

        <section class="rinascente-member-video-form__section">
            <div class="rinascente-member-video-form__section-head">
                <h3>3. 表示の考え方</h3>
                <p>一覧用要約と本文の役割を分けておくと、会員ページで読みやすくなります。</p>
            </div>
            <div class="rinascente-member-video-form__field">
                <ul class="rinascente-member-content-top-card__list">
                    <li>一覧用要約を入れると、一覧カードではその短い要約を優先して表示します</li>
                    <li>一覧用要約が空欄なら、本文の冒頭を短く切り出して表示します</li>
                    <li>一覧の並びは公開日が新しい順なので、急ぎの案内は公開日も合わせて確認してください</li>
                </ul>
            </div>
        </section>
    </div>
    <?php
}

function rinascente_member_save_content_meta( $post_id, $post ) {
    if ( ! isset( $_POST['rinascente_member_content_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['rinascente_member_content_nonce'] ), 'rinascente_member_content_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $editable_post_types = array( 'contract', 'product_master', 'member_video', 'member_document', 'member_notice' );
    if ( rinascente_member_reviews_enabled() ) {
        $editable_post_types[] = 'member_review';
    }

    if ( ! in_array( $post->post_type, $editable_post_types, true ) ) {
        return;
    }

    $map = array(
        'member_user_id'           => 'absint',
        'product_master_id'        => 'absint',
        'facility_name'            => 'sanitize_text_field',
        'order_number'             => 'sanitize_text_field',
        'product_name'             => 'sanitize_text_field',
        'product_key'              => 'sanitize_key',
        'product_catalog_category' => 'sanitize_key',
        'product_catalog_sort_order' => 'absint',
        'product_catalog_code'     => 'sanitize_key',
        'product_catalog_display_name' => 'sanitize_text_field',
        'product_catalog_short_name' => 'sanitize_text_field',
        'product_catalog_spec'     => 'sanitize_text_field',
        'product_catalog_install_type' => 'sanitize_key',
        'product_catalog_max_rail_length' => 'absint',
        'product_catalog_rail_length_options' => 'rinascente_member_format_rail_length_options',
        'product_catalog_unit_price' => 'rinascente_member_sanitize_price',
        'product_catalog_rail_price_per_m' => 'rinascente_member_sanitize_price',
        'product_catalog_pricing_option_key' => 'sanitize_key',
        'product_catalog_unit_label' => 'sanitize_text_field',
        'product_catalog_max_quantity' => 'absint',
        'product_catalog_selection_type' => 'sanitize_key',
        'product_catalog_contract_template' => 'sanitize_textarea_field',
        'product_catalog_source'   => 'sanitize_textarea_field',
        'quantity'                 => 'sanitize_text_field',
        'order_date'               => 'sanitize_text_field',
        'delivery_date'            => 'sanitize_text_field',
        'contract_date'            => 'sanitize_text_field',
        'contract_status'          => 'sanitize_key',
        'payment_status'           => 'sanitize_key',
        'contract_info'            => 'sanitize_textarea_field',
        'contract_notes'           => 'sanitize_textarea_field',
        'youtube_id'               => 'rinascente_extract_youtube_video_id',
        'video_description'        => 'sanitize_textarea_field',
        'video_category'           => 'sanitize_key',
        'start_date'               => 'sanitize_text_field',
        'end_date'                 => 'sanitize_text_field',
        'attachment_id'            => 'absint',
        'document_category'        => 'sanitize_key',
        'document_updated_date'    => 'sanitize_text_field',
        'program_name'             => 'sanitize_text_field',
        'subsidy_status'           => 'sanitize_text_field',
        'author_name'              => 'sanitize_text_field',
        'review_facility_name'     => 'sanitize_text_field',
        'review_facility_type'     => 'sanitize_text_field',
        'adoption_period'          => 'sanitize_key',
        'review_rating'            => 'absint',
        'review_tags'              => 'sanitize_text_field',
        'helpful_count'            => 'absint',
        'notice_tone'              => 'sanitize_key',
    );

    foreach ( $map as $field => $sanitizer ) {
        $input_key = 'rinascente_' . $field;
        if ( ! isset( $_POST[ $input_key ] ) ) {
            continue;
        }

        $value = call_user_func( $sanitizer, wp_unslash( $_POST[ $input_key ] ) );
        update_post_meta( $post_id, '_rinascente_' . $field, $value );
    }

    if ( 'contract' === $post->post_type ) {
        $product_master_id = absint( get_post_meta( $post_id, '_rinascente_product_master_id', true ) );
        if ( $product_master_id && 'product_master' === get_post_type( $product_master_id ) ) {
            $master_title = get_the_title( $product_master_id );
            $master_key   = sanitize_key( get_post_meta( $product_master_id, '_rinascente_product_key', true ) );

            if ( '' !== $master_title ) {
                update_post_meta( $post_id, '_rinascente_product_name', $master_title );
            }

            if ( $master_key ) {
                update_post_meta( $post_id, '_rinascente_product_key', $master_key );
            }
        }

        $generated_title = rinascente_generate_contract_title( $post_id );
        if ( '' !== $generated_title && $generated_title !== $post->post_title ) {
            remove_action( 'save_post', 'rinascente_member_save_content_meta', 10 );
            wp_update_post(
                array(
                    'ID'         => $post_id,
                    'post_title' => $generated_title,
                )
            );
            add_action( 'save_post', 'rinascente_member_save_content_meta', 10, 2 );
        }
    }

    if ( 'product_master' === $post->post_type ) {
        $display_name_value = trim( (string) get_post_meta( $post_id, '_rinascente_product_catalog_display_name', true ) );
        $short_name_value   = trim( (string) get_post_meta( $post_id, '_rinascente_product_catalog_short_name', true ) );
        $generated_title    = $display_name_value ?: $short_name_value;

        if ( '' === $display_name_value && '' !== $generated_title ) {
            update_post_meta( $post_id, '_rinascente_product_catalog_display_name', $generated_title );
        }

        if ( '' === $short_name_value && '' !== $generated_title ) {
            update_post_meta( $post_id, '_rinascente_product_catalog_short_name', $generated_title );
        }

        if ( '' !== $generated_title && $generated_title !== $post->post_title ) {
            remove_action( 'save_post', 'rinascente_member_save_content_meta', 10 );
            wp_update_post(
                array(
                    'ID'         => $post_id,
                    'post_title' => $generated_title,
                )
            );
            add_action( 'save_post', 'rinascente_member_save_content_meta', 10, 2 );
        }
    }

    if ( in_array( $post->post_type, array( 'contract', 'member_video', 'member_document', 'member_notice' ), true ) || ( 'member_review' === $post->post_type && rinascente_member_reviews_enabled() ) ) {
        $product_key = get_post_meta( $post_id, '_rinascente_product_key', true );
        if ( $product_key ) {
            wp_set_object_terms( $post_id, $product_key, 'product_type', false );
        } else {
            wp_set_object_terms( $post_id, array(), 'product_type', false );
        }
    }
}
add_action( 'save_post', 'rinascente_member_save_content_meta', 10, 2 );

function rinascente_member_document_admin_assets( $hook ) {
    global $post_type;

    if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) || ! in_array( $post_type, rinascente_member_content_edit_post_types(), true ) ) {
        return;
    }

    $style_handle = 'rinascente-member-content-admin';
    $styles       = <<<'CSS'
.post-type-member_video #slugdiv,
.post-type-member_document #slugdiv,
.post-type-member_notice #slugdiv,
.post-type-member_document #post-status-info,
.post-type-member_video #commentstatusdiv,
.post-type-member_document #commentstatusdiv,
.post-type-member_notice #commentstatusdiv,
.post-type-member_video #commentsdiv,
.post-type-member_document #commentsdiv,
.post-type-member_notice #commentsdiv,
.post-type-member_video #trackbacksdiv,
.post-type-member_document #trackbacksdiv,
.post-type-member_notice #trackbacksdiv,
.post-type-member_video #authordiv,
.post-type-member_document #authordiv,
.post-type-member_notice #authordiv,
.post-type-member_video #revisionsdiv,
.post-type-member_document #revisionsdiv,
.post-type-member_notice #revisionsdiv,
.post-type-member_video #postcustom,
.post-type-member_document #postcustom,
.post-type-member_notice #postcustom {
  display: none;
}

.post-type-member_video #pageparentdiv,
.post-type-member_document #pageparentdiv,
.post-type-member_notice #postexcerpt {
  display: none;
}

.post-type-member_video #postdivrich,
.post-type-member_document #postdivrich {
  display: none;
}

.rinascente-member-content-top-fields {
  margin: 18px 0 20px;
  display: grid;
  gap: 18px;
}

.rinascente-member-content-top-fields__guide {
  padding: 18px 20px;
  border: 1px solid #cfe0f2;
  border-radius: 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.rinascente-member-content-top-fields__lead {
  margin: 0 0 14px;
  line-height: 1.9;
}

.rinascente-member-content-top-fields__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}

.rinascente-member-content-top-card {
  padding: 16px 18px;
  border: 1px solid #dcdcde;
  border-radius: 14px;
  background: #fff;
}

.rinascente-member-content-top-card--settings {
  border-color: #b8d4f0;
  background: #f7fbff;
}

.rinascente-member-content-top-card h4 {
  margin: 0 0 10px;
}

.rinascente-member-content-top-card ol {
  margin: 0;
  padding-left: 18px;
  display: grid;
  gap: 6px;
}

.rinascente-member-content-top-card ul {
  margin: 0;
  padding-left: 18px;
  display: grid;
  gap: 6px;
}

.rinascente-member-content-top-card input[type="number"] {
  width: 140px;
  margin: 6px 0 8px;
}

.rinascente-member-content-top-card--hint {
  border-color: #d6e7fb;
  background: #f9fcff;
}

.rinascente-member-video-form {
  display: grid;
  gap: 18px;
}

.rinascente-member-video-form .rinascente-member-content-top-fields {
  margin: 0;
}

.rinascente-member-video-form__section {
  padding: 18px 20px;
  border: 1px solid #dcdcde;
  border-radius: 14px;
  background: #fff;
}

.rinascente-member-video-form__section-head {
  margin-bottom: 14px;
}

.rinascente-member-video-form__section-head h3 {
  margin: 0 0 6px;
}

.rinascente-member-video-form__section-head p {
  margin: 0;
  color: #475569;
  line-height: 1.8;
}

.rinascente-member-video-form__grid {
  display: grid;
  grid-template-columns: minmax(0, 1.4fr) minmax(280px, 1fr);
  gap: 16px;
  align-items: start;
}

.rinascente-member-video-form__grid--compact {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}

.rinascente-member-video-form__field {
  display: grid;
  gap: 6px;
}

.rinascente-member-video-form__field--wide {
  grid-column: 1 / -1;
}

.rinascente-member-video-form__field label {
  font-weight: 600;
}

.rinascente-member-video-preview {
  display: grid;
  gap: 14px;
  padding: 16px;
  border: 1px solid #d6e7fb;
  border-radius: 14px;
  background: #f8fbff;
}

.rinascente-member-video-preview__media {
  min-height: 160px;
  border-radius: 12px;
  overflow: hidden;
  background: #dbeafe;
}

.rinascente-member-video-preview__media img {
  display: block;
  width: 100%;
  height: auto;
}

.rinascente-member-video-preview__placeholder {
  display: grid;
  place-items: center;
  min-height: 160px;
  padding: 20px;
  text-align: center;
  color: #475569;
  line-height: 1.8;
}

.rinascente-member-video-preview__body {
  display: grid;
  gap: 6px;
}

.rinascente-member-video-preview__label {
  margin: 0;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #0068b7;
}

.rinascente-member-video-preview__body strong {
  font-size: 15px;
  line-height: 1.5;
}

.rinascente-member-video-preview .button.disabled {
  pointer-events: none;
  opacity: 0.55;
}

.rinascente-member-document-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}

.rinascente-member-document-helper {
  margin: 0;
  color: #475569;
  font-size: 12px;
  line-height: 1.7;
}

.rinascente-member-document-title-head {
  display: grid;
  gap: 6px;
  margin-bottom: 8px;
}

.rinascente-member-document-title-slot {
  margin-bottom: 16px;
}

body.post-type-member_document.rinascente-document-unified-ready #titlediv {
  display: none;
}

body.post-type-member_document.rinascente-document-unified-ready .rinascente-member-document-title-slot #titlediv {
  display: block !important;
  margin: 0;
}

body.post-type-member_document.rinascente-document-unified-ready .rinascente-member-document-title-slot #titlewrap {
  margin: 0;
}

body.post-type-member_document.rinascente-document-unified-ready .rinascente-member-document-title-slot #title {
  min-height: 52px;
  padding-inline: 16px;
  border-radius: 12px;
  border-color: #d0dbe7;
  box-shadow: none;
}

.rinascente-member-document-preview {
  display: grid;
  grid-template-columns: 88px minmax(0, 1fr);
  gap: 16px;
  align-items: start;
  padding: 16px;
  border: 1px solid #d6e7fb;
  border-radius: 14px;
  background: #f8fbff;
}

.rinascente-member-document-preview__icon {
  display: grid;
  place-items: center;
  min-height: 88px;
  padding: 12px;
  border-radius: 14px;
  background: #dbeafe;
  color: #00538f;
  font-size: 18px;
  font-weight: 800;
  letter-spacing: 0.06em;
}

.rinascente-member-document-preview__body {
  display: grid;
  gap: 6px;
}

.rinascente-member-document-preview__body strong {
  font-size: 15px;
  line-height: 1.5;
}

.rinascente-member-document-preview.is-empty .rinascente-member-document-preview__icon {
  color: #64748b;
  background: #e7eef8;
}

.rinascente-member-document-preview .button.disabled {
  pointer-events: none;
  opacity: 0.55;
}

.rinascente-member-document-summary {
  display: grid;
  gap: 14px;
  padding: 18px;
  border: 1px solid #d6e7fb;
  border-radius: 16px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.rinascente-member-document-summary__header {
  display: grid;
  gap: 6px;
}

.rinascente-member-document-summary__header strong {
  font-size: 17px;
  line-height: 1.55;
}

.rinascente-member-document-summary__grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.rinascente-member-document-summary__item {
  display: grid;
  gap: 4px;
  padding: 12px 14px;
  border: 1px solid #dbe4ee;
  border-radius: 12px;
  background: #fff;
}

.rinascente-member-document-summary__item span {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #64748b;
}

.rinascente-member-document-summary__item strong {
  font-size: 14px;
  line-height: 1.6;
  color: #0f172a;
}

.rinascente-member-document-summary__item--wide {
  grid-column: 1 / -1;
}

.rinascente-member-document-summary__guide {
  padding: 14px 16px;
  border: 1px solid rgba(0, 104, 183, 0.14);
  border-radius: 12px;
  background: rgba(0, 104, 183, 0.06);
}

.rinascente-member-document-summary__eyebrow {
  display: inline-block;
  margin-bottom: 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #0068b7;
}

.rinascente-member-document-summary__guide p {
  margin: 0;
  color: #334155;
  line-height: 1.75;
}

.rinascente-member-notice-preview {
  display: grid;
  gap: 10px;
  padding: 16px;
  border: 1px solid #d6e7fb;
  border-radius: 14px;
  background: #f8fbff;
}

.rinascente-member-notice-preview__meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

.rinascente-member-notice-preview__source {
  font-size: 12px;
  color: #64748b;
  line-height: 1.6;
}

.rinascente-member-notice-preview strong {
  font-size: 15px;
  line-height: 1.5;
}

.rinascente-member-notice-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 28px;
  padding: 0 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  line-height: 1;
}

.rinascente-member-notice-badge--maintenance {
  background: rgba(180, 124, 0, 0.14);
  color: #8a5a00;
}

.rinascente-member-notice-badge--urgent {
  background: rgba(220, 38, 38, 0.14);
  color: #b91c1c;
}

.rinascente-member-notice-badge--info {
  background: rgba(0, 104, 183, 0.12);
  color: #00538f;
}

.rinascente-member-notice-badge--neutral {
  background: #eef2f7;
  color: #475569;
}

.rinascente-member-content-top-card__list {
  margin: 0;
  padding-left: 18px;
  display: grid;
  gap: 6px;
}

.rinascente-member-content-checklist__list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 10px;
}

.rinascente-member-content-checklist__item {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.rinascente-member-content-checklist__badge {
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

.rinascente-member-content-checklist__item.is-done .rinascente-member-content-checklist__badge {
  background: rgba(0, 104, 183, 0.12);
  color: #00538f;
}

.rinascente-member-content-checklist__label {
  font-weight: 600;
}

.rinascente-member-content-checklist__note {
  color: #64748b;
  font-size: 12px;
}

.rinascente-member-content-editor-note {
  margin: 10px 0 0;
  padding: 12px 14px;
  border-radius: 12px;
  background: #f8fafc;
  border: 1px dashed #cbd5e1;
  color: #475569;
  font-size: 12px;
  line-height: 1.7;
}

.rinascente-member-editor-focus {
  margin: 12px 0 10px;
  padding: 14px 16px;
  border: 1px solid #d6e7fb;
  border-radius: 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.rinascente-member-editor-focus__eyebrow {
  display: inline-block;
  margin: 0 0 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #0068b7;
}

.rinascente-member-editor-focus h4 {
  margin: 0 0 6px;
  font-size: 15px;
  line-height: 1.5;
}

.rinascente-member-editor-focus p {
  margin: 0;
  color: #475569;
  line-height: 1.8;
}

.rinascente-member-content-focus-target {
  border: 1px solid #d8e3f0;
  border-radius: 14px;
  background: #fff;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.03);
}

@media (max-width: 1100px) {
  .rinascente-member-content-top-fields__grid {
    grid-template-columns: 1fr;
  }

  .rinascente-member-video-form__grid,
  .rinascente-member-video-form__grid--compact {
    grid-template-columns: 1fr;
  }

  .rinascente-member-document-summary__grid {
    grid-template-columns: 1fr;
  }
}
CSS;

    wp_register_style( $style_handle, false, array(), null );
    wp_enqueue_style( $style_handle );
    wp_add_inline_style( $style_handle, $styles );

    if ( 'member_document' === $post_type ) {
        wp_enqueue_media();
    }

    $post_type_json                 = wp_json_encode( $post_type );
    $config                         = rinascente_member_content_edit_config( $post_type );
    $editor_message                 = wp_json_encode( $config['editor_message'] ?? '' );
    $document_category_guides_json  = wp_json_encode( rinascente_member_document_category_guides() );
    $initial_document_attachment    = 'null';
    if ( 'member_document' === $post_type && isset( $GLOBALS['post'] ) && $GLOBALS['post'] instanceof WP_Post ) {
        $current_attachment_id = absint( get_post_meta( $GLOBALS['post']->ID, '_rinascente_attachment_id', true ) );
        if ( $current_attachment_id && function_exists( 'wp_prepare_attachment_for_js' ) ) {
            $prepared_attachment = wp_prepare_attachment_for_js( $current_attachment_id );
            if ( is_array( $prepared_attachment ) ) {
                $initial_document_attachment = wp_json_encode( $prepared_attachment );
            }
        }
    }
    $script         = <<<'JS'
(function($){
  $(function(){
    var postType = __POST_TYPE_JSON__;
    var documentCategoryGuides = __DOCUMENT_CATEGORY_GUIDES_JSON__ || {};
    var currentDocumentAttachment = __INITIAL_DOCUMENT_ATTACHMENT_JSON__;

    function editorValue() {
      if (window.tinymce) {
        var editor = window.tinymce.get('content');
        if (editor && !editor.isHidden()) {
          return $.trim(editor.getContent({ format: 'text' }));
        }
      }
      return $.trim($('#content').val() || '');
    }

    function hasSelectValue(selector) {
      return $.trim($(selector).val() || '') !== '';
    }

    function extractYoutubeId(value) {
      var raw = $.trim(value || '');
      if (!raw) {
        return '';
      }

      if (/^[A-Za-z0-9_-]{11}$/.test(raw)) {
        return raw;
      }

      var patterns = [
        /(?:youtube\.com\/watch\?v=|youtube\.com\/watch\?.*?&v=)([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/live\/([A-Za-z0-9_-]{11})/i,
        /youtu\.be\/([A-Za-z0-9_-]{11})/i
      ];

      for (var i = 0; i < patterns.length; i += 1) {
        var match = raw.match(patterns[i]);
        if (match && match[1]) {
          return match[1];
        }
      }

      return '';
    }

    function hasDateRange() {
      return $.trim($('#rinascente_start_date').val() || '') !== '' || $.trim($('#rinascente_end_date').val() || '') !== '';
    }

    function todayDateValue() {
      var now = new Date();
      var offset = now.getTimezoneOffset();
      return new Date(now.getTime() - (offset * 60000)).toISOString().slice(0, 10);
    }

    function moveDocumentFieldsIntoUnifiedLayout() {
      if (postType !== 'member_document') {
        return;
      }

      var $titleSlot = $('#rinascente_member_document_title_slot');
      var $titleDiv = $('#titlediv');

      if ($titleSlot.length && $titleDiv.length && !$titleSlot.find('#titlediv').length) {
        $titleSlot.append($titleDiv);
      }

      if ($titleSlot.find('#titlediv').length) {
        $('body').addClass('rinascente-document-unified-ready');
      }
    }

    function updateVideoPreview() {
      if (postType !== 'member_video') {
        return;
      }

      var raw = $.trim($('#rinascente_youtube_id').val() || '');
      var id = extractYoutubeId(raw);
      var $preview = $('#rinascente_member_video_preview');
      var $image = $('#rinascente_member_video_preview_image');
      var $placeholder = $('#rinascente_member_video_preview_placeholder');
      var $id = $('#rinascente_member_video_preview_id');
      var $help = $('#rinascente_member_video_preview_help');
      var $link = $('#rinascente_member_video_preview_link');

      if (!$preview.length) {
        return;
      }

      if (id) {
        $preview.removeClass('is-empty');
        $id.text(id);
        $help.text('サムネイルを確認しています。');
        $image.off('load.rinascentePreview error.rinascentePreview');
        $image.on('load.rinascentePreview', function() {
          $preview.removeClass('is-empty');
          $help.text('この動画IDで会員ページに表示されます。');
          if ($placeholder.length) {
            $placeholder.hide();
          }
          $link.attr('href', 'https://www.youtube.com/watch?v=' + id).removeClass('disabled').removeAttr('aria-disabled tabindex');
        });
        $image.on('error.rinascentePreview', function() {
          $preview.addClass('is-empty');
          $image.attr('src', '').prop('hidden', true);
          $help.text('YouTube 側で動画を確認できませんでした。公開状態か動画IDを見直してください。');
          if ($placeholder.length) {
            $placeholder.text('この動画は現在確認できません。公開状態か動画IDを見直してください。').show();
          }
          $link.attr('href', '#').addClass('disabled').attr('aria-disabled', 'true').attr('tabindex', '-1');
        });
        $image.attr('src', 'https://i.ytimg.com/vi/' + id + '/hqdefault.jpg').prop('hidden', false);
        if ($placeholder.length) {
          $placeholder.text('サムネイルを確認しています…').show();
        }
      } else {
        $preview.addClass('is-empty');
        $id.text(raw ? '動画IDを確認してください' : '未入力');
        $help.text(raw ? 'URLや動画IDを読み取れませんでした。共有URLか 11 文字の動画IDを入力してください。' : 'URLや動画IDが正しく入ると、サムネイルと確認リンクが表示されます。');
        $image.attr('src', '').prop('hidden', true);
        if ($placeholder.length) {
          $placeholder.text('YouTube 情報を入れると、ここで動画の確認ができます。').show();
        }
        $link.attr('href', '#').addClass('disabled').attr('aria-disabled', 'true').attr('tabindex', '-1');
      }
    }

    function documentFileTypeLabel(attachment) {
      if (!attachment) {
        return 'FILE';
      }

      var filename = $.trim(attachment.filename || '');
      if (filename && filename.indexOf('.') !== -1) {
        return filename.split('.').pop().toUpperCase();
      }

      if ($.trim(attachment.subtype || '')) {
        return $.trim(attachment.subtype).toUpperCase();
      }

      if ($.trim(attachment.mime || '').indexOf('/') !== -1) {
        return $.trim(attachment.mime).split('/').pop().toUpperCase();
      }

      return 'FILE';
    }

    function documentAttachmentTitle(attachment) {
      if (!attachment) {
        return '';
      }

      var title = $.trim(attachment.title || '');
      if (title) {
        return title;
      }

      var filename = $.trim(attachment.filename || '');
      if (!filename) {
        return '';
      }

      filename = filename.replace(/\.[^.]+$/, '');
      filename = filename.replace(/[_-]+/g, ' ');
      filename = filename.replace(/\s+/g, ' ');

      return $.trim(filename);
    }

    function documentAttachmentMetaParts(attachment) {
      if (!attachment) {
        return [];
      }

      var metaParts = [];
      if ($.trim(attachment.filename || '')) {
        metaParts.push($.trim(attachment.filename));
      }

      var typeLabel = documentFileTypeLabel(attachment);
      if (typeLabel) {
        metaParts.push(typeLabel);
      }

      var sizeLabel = $.trim(attachment.filesizeHumanReadable || attachment.filesize || '');
      if (sizeLabel) {
        metaParts.push(sizeLabel);
      }

      return metaParts;
    }

    function populateDocumentTitleFromAttachment(force) {
      if (postType !== 'member_document' || !currentDocumentAttachment) {
        return;
      }

      var $title = $('#title');
      if (!$title.length) {
        return;
      }

      if (!force && $.trim($title.val() || '') !== '') {
        return;
      }

      var nextTitle = documentAttachmentTitle(currentDocumentAttachment);
      if (nextTitle) {
        $title.val(nextTitle).trigger('input');
      }
    }

    function populateDocumentUpdatedDate(force) {
      if (postType !== 'member_document') {
        return;
      }

      var $field = $('#rinascente_document_updated_date');
      if (!$field.length) {
        return;
      }

      if (!force && $.trim($field.val() || '') !== '') {
        return;
      }

      $field.val(todayDateValue()).trigger('change');
    }

    function updateDocumentPreview(attachment) {
      if (postType !== 'member_document') {
        return;
      }

      if (typeof attachment !== 'undefined') {
        currentDocumentAttachment = attachment;
      }

      var hasFile = $.trim($('#rinascente_attachment_id').val() || '') !== '';
      var $preview = $('#rinascente_member_document_preview');
      if (!$preview.length) {
        return;
      }

      var $name = $('#rinascente_member_document_preview_name');
      var $meta = $('#rinascente_member_document_preview_meta');
      var $help = $('#rinascente_member_document_preview_help');
      var $icon = $('#rinascente_member_document_preview_icon');
      var $link = $('#rinascente_member_document_preview_link');
      $('.rinascente-document-fill-title').prop('disabled', !hasFile);

      if (attachment && hasFile) {
        var metaParts = documentAttachmentMetaParts(attachment);
        var title = documentAttachmentTitle(attachment) || '選択済みファイル';
        var typeLabel = documentFileTypeLabel(attachment);

        $preview.removeClass('is-empty');
        $icon.text(typeLabel || 'FILE');
        $name.text(title);
        $meta.text(metaParts.join(' / ') || 'ファイル情報');
        $help.text('このファイルが会員ページからダウンロードされます。');
        if ($.trim(attachment.url || '')) {
          $link.attr('href', attachment.url).removeClass('disabled').removeAttr('aria-disabled tabindex');
        }
        return;
      }

      if (hasFile) {
        $preview.removeClass('is-empty');
        $help.text('このファイルが会員ページからダウンロードされます。');
      } else {
        $preview.addClass('is-empty');
        $icon.text('FILE');
        $name.text('未選択');
        $meta.text('ファイルを選ぶと、ここで形式とサイズを確認できます。');
        $help.text('ファイルを選ぶと、会員向けに表示される資料情報をここで確認できます。');
        $link.attr('href', '#').addClass('disabled').attr('aria-disabled', 'true').attr('tabindex', '-1');
      }
    }

    function documentScheduleSummary() {
      var start = $.trim($('#rinascente_start_date').val() || '');
      var end = $.trim($('#rinascente_end_date').val() || '');
      var today = todayDateValue();

      if (start && today < start) {
        return '公開前';
      }

      if (end && today > end) {
        return '公開終了';
      }

      if (!start && !end) {
        return '常時公開';
      }

      return '公開中';
    }

    function documentCategoryGuide(category, programName, subsidyStatus) {
      var guide = documentCategoryGuides[category] || '資料の用途に合うカテゴリを選ぶと、会員ページでも一覧でも探しやすくなります。';

      if (category === 'subsidy' && !programName && !subsidyStatus) {
        guide += ' 制度名か認定ステータスも残しておくと、差し替えや問い合わせ対応がしやすくなります。';
      }

      return guide;
    }

    function updateDocumentSummary() {
      if (postType !== 'member_document') {
        return;
      }

      var title = $.trim($('#title').val() || '') || '資料名を入れるとここに表示されます。';
      var category = $.trim($('#rinascente_document_category').val() || '');
      var categoryText = $.trim($('#rinascente_document_category option:selected').text() || '') || '未設定';
      var productValue = $.trim($('#rinascente_document_product_key').val() || '');
      var productText = productValue ? ($.trim($('#rinascente_document_product_key option:selected').text() || '') || '未設定') : '共通';
      var updated = $.trim($('#rinascente_document_updated_date').val() || '');
      var program = $.trim($('#rinascente_program_name').val() || '');
      var subsidyStatus = $.trim($('#rinascente_subsidy_status').val() || '');
      var subsidySummary = $.trim([program, subsidyStatus].filter(Boolean).join(' / '));
      var fileSummary = 'ファイルを選ぶと、ここで形式とサイズを確認できます。';

      if (currentDocumentAttachment) {
        var metaParts = documentAttachmentMetaParts(currentDocumentAttachment);
        if (metaParts.length) {
          fileSummary = metaParts.join(' / ');
        }
      } else {
        var previewMeta = $.trim($('#rinascente_member_document_preview_meta').text() || '');
        if (previewMeta) {
          fileSummary = previewMeta;
        }
      }

      if (!subsidySummary) {
        subsidySummary = category === 'subsidy' ? '制度名や認定ステータスを入れると管理しやすくなります。' : '特になし';
      }

      $('#rinascente_member_document_summary_title').text(title);
      $('#rinascente_member_document_summary_file').text(fileSummary);
      $('#rinascente_member_document_summary_category').text(categoryText);
      $('#rinascente_member_document_summary_product').text(productText);
      $('#rinascente_member_document_summary_updated').text(updated || '未設定');
      $('#rinascente_member_document_summary_schedule').text(documentScheduleSummary());
      $('#rinascente_member_document_summary_subsidy').text(subsidySummary);
      $('#rinascente_member_document_summary_guide').text(documentCategoryGuide(category, program, subsidyStatus));
    }

    function noticeToneMeta(tone) {
      var labels = {
        maintenance: '保守・運用',
        urgent: '重要',
        info: 'お知らせ',
        neutral: '一般'
      };
      var notes = {
        maintenance: '保守や運用に関する案内として、ゴールド系の印象で表示されます。',
        urgent: '重要な連絡として、赤系の強い見え方で表示されます。',
        info: '通常のお知らせとして、青系の印象で表示されます。',
        neutral: '分類を強く出さない一般連絡として、落ち着いた見え方で表示されます。'
      };

      return {
        label: labels[tone] || labels.neutral,
        note: notes[tone] || notes.neutral,
        tone: labels[tone] ? tone : 'neutral'
      };
    }

    function compactText(value, limit) {
      var text = $.trim((value || '').replace(/\s+/g, ' '));
      var max = limit || 110;
      if (text.length <= max) {
        return text;
      }
      return text.slice(0, max - 1) + '…';
    }

    function updateNoticePreview() {
      if (postType !== 'member_notice') {
        return;
      }

      var title = $.trim($('#title').val() || '') || 'タイトル未入力';
      var excerpt = $.trim($('#excerpt').val() || '');
      var body = compactText(editorValue(), 110);
      var tone = $.trim($('#rinascente_notice_tone').val() || 'neutral');
      var toneMeta = noticeToneMeta(tone);
      var productText = $.trim($('#rinascente_notice_product_key option:selected').text() || '') || '共通';
      var summary = excerpt || body;
      var sourceText = excerpt ? '一覧用要約を使用中' : (body ? '本文の冒頭を自動使用' : '要約はまだ未入力です');
      var helpText = toneMeta.note + ' 対象製品: ' + productText;
      var $badge = $('#rinascente_member_notice_preview_badge');

      $('#rinascente_member_notice_preview_title').text(title);
      $('#rinascente_member_notice_preview_source').text(sourceText);
      $('#rinascente_member_notice_preview_summary').text(summary || '本文か一覧用要約を入れると、ここに会員向けの見え方が表示されます。');
      $('#rinascente_member_notice_preview_help').text(helpText);

      if ($badge.length) {
        $badge
          .removeClass('rinascente-member-notice-badge--maintenance rinascente-member-notice-badge--urgent rinascente-member-notice-badge--info rinascente-member-notice-badge--neutral')
          .addClass('rinascente-member-notice-badge--' + toneMeta.tone)
          .text(toneMeta.label);
      }
    }

    function ensureNoticeEntryFocus() {
      if (postType !== 'member_notice') {
        return;
      }

      var $titleDiv = $('#titlediv');
      var $editorDiv = $('#postdivrich');

      if ($titleDiv.length && !$('#rinascente_member_notice_title_focus').length) {
        $('<div id="rinascente_member_notice_title_focus" class="rinascente-member-editor-focus rinascente-member-editor-focus--title">'
          + '<span class="rinascente-member-editor-focus__eyebrow">1. タイトル</span>'
          + '<h4>まずここに、案内のタイトルを入れます</h4>'
          + '<p>会員ページや一覧で最初に見える見出しです。更新内容が一目で伝わる短い言い方がおすすめです。</p>'
          + '</div>').insertBefore($titleDiv);
        $titleDiv.addClass('rinascente-member-content-focus-target');
      }

      if ($editorDiv.length && !$('#rinascente_member_notice_body_focus').length) {
        $('<div id="rinascente_member_notice_body_focus" class="rinascente-member-editor-focus rinascente-member-editor-focus--body">'
          + '<span class="rinascente-member-editor-focus__eyebrow">2. 本文</span>'
          + '<h4>次にここへ、会員へ伝えたい本文を書きます</h4>'
          + '<p>詳しい案内や手順はこちらに入力します。一覧で短く見せたい要点だけ、上の「一覧用要約」に任意で入れてください。</p>'
          + '</div>').insertBefore($editorDiv);
        $editorDiv.addClass('rinascente-member-content-focus-target');
      }
    }

    function updateChecklist() {
      var checks = {};

      if (postType === 'member_video') {
        checks = {
          title: $.trim($('#title').val() || '') !== '',
          youtube_id: extractYoutubeId($('#rinascente_youtube_id').val()) !== '',
          description: $.trim($('#rinascente_video_description').val() || '') !== '',
          category: hasSelectValue('#rinascente_video_category'),
          product: hasSelectValue('#rinascente_video_product_key'),
          order: $.trim($('#menu_order').val() || '') !== '',
          schedule: hasDateRange()
        };
      } else if (postType === 'member_document') {
        checks = {
          title: $.trim($('#title').val() || '') !== '',
          file: $.trim($('#rinascente_attachment_id').val() || '') !== '',
          category: hasSelectValue('#rinascente_document_category'),
          updated: $.trim($('#rinascente_document_updated_date').val() || '') !== '',
          product: hasSelectValue('#rinascente_document_product_key'),
          order: $.trim($('#menu_order').val() || '') !== '',
          schedule: hasDateRange()
        };
      } else if (postType === 'member_notice') {
        checks = {
          title: $.trim($('#title').val() || '') !== '',
          summary: $.trim($('#excerpt').val() || '') !== '' || editorValue() !== '',
          tone: hasSelectValue('#rinascente_notice_tone'),
          product: hasSelectValue('#rinascente_notice_product_key'),
          schedule: hasDateRange()
        };
      }

      $.each(checks, function(key, done){
        var $item = $('.rinascente-member-content-checklist__item[data-check="' + key + '"]');
        $item.toggleClass('is-done', !!done);
        $item.find('.rinascente-member-content-checklist__badge').text(done ? '入力済み' : '未確認');
      });

      updateVideoPreview();
      updateDocumentPreview();
      updateDocumentSummary();
      updateNoticePreview();
    }

    $('#title, #menu_order, #rinascente_youtube_id, #rinascente_video_description, #rinascente_attachment_id, #rinascente_document_updated_date, #rinascente_program_name, #rinascente_subsidy_status, #excerpt, #rinascente_start_date, #rinascente_end_date').on('input change', updateChecklist);
    $('#rinascente_video_category, #rinascente_video_product_key, #rinascente_document_category, #rinascente_document_product_key, #rinascente_notice_tone, #rinascente_notice_product_key').on('change', updateChecklist);
    $('#content').on('input change', updateChecklist);

    if (window.tinymce && window.tinymce.on) {
      window.tinymce.on('AddEditor', function(event) {
        if (event.editor && event.editor.id === 'content') {
          event.editor.on('input change keyup SetContent', updateChecklist);
        }
      });
    }

    moveDocumentFieldsIntoUnifiedLayout();
    ensureNoticeEntryFocus();

    if (postType === 'member_video') {
      var message = __EDITOR_MESSAGE_JSON__;
      var \$editorArea = $('#postdivrich');
      if (message && \$editorArea.length && !$('.rinascente-member-content-editor-note').length) {
        $('<div class="rinascente-member-content-editor-note" />').text(message).insertAfter('#titlediv');
      }
    }

    if (postType === 'member_video') {
      $('#rinascente_youtube_id').on('blur', function(){
        var id = extractYoutubeId($(this).val());
        if (id) {
          $(this).val(id);
        }
        updateChecklist();
      });
    }

    if (postType === 'member_document') {
      var frame;
      $('.rinascente-document-fill-title').on('click', function(e){
        e.preventDefault();
        populateDocumentTitleFromAttachment(true);
        updateChecklist();
      });
      $('.rinascente-document-set-today').on('click', function(e){
        e.preventDefault();
        populateDocumentUpdatedDate(true);
        updateChecklist();
      });
      $('.rinascente-media-select').on('click', function(e){
        e.preventDefault();
        if (frame) {
          frame.open();
          return;
        }
        frame = wp.media({
          title: 'ファイルを選択',
          button: { text: 'このファイルを使う' },
          multiple: false
        });
        frame.on('select', function(){
          var attachment = frame.state().get('selection').first().toJSON();
          var titleWasBlank = $.trim($('#title').val() || '') === '';
          var dateWasBlank = $.trim($('#rinascente_document_updated_date').val() || '') === '';
          $('#rinascente_attachment_id').val(attachment.id);
          $('.rinascente-media-clear').prop('disabled', false);
          currentDocumentAttachment = attachment;
          if (titleWasBlank) {
            populateDocumentTitleFromAttachment(false);
          }
          if (dateWasBlank) {
            populateDocumentUpdatedDate(false);
          }
          updateDocumentPreview(attachment);
          updateChecklist();
        });
        frame.open();
      });
      $('.rinascente-media-clear').on('click', function(e){
        e.preventDefault();
        $('#rinascente_attachment_id').val('');
        $(this).prop('disabled', true);
        currentDocumentAttachment = null;
        updateDocumentPreview(null);
        updateChecklist();
      });
    }

    updateChecklist();
  });
})(jQuery);
JS;
    $script         = strtr(
        $script,
        array(
            '__POST_TYPE_JSON__'      => $post_type_json,
            '__EDITOR_MESSAGE_JSON__' => $editor_message,
            '__DOCUMENT_CATEGORY_GUIDES_JSON__' => $document_category_guides_json,
            '__INITIAL_DOCUMENT_ATTACHMENT_JSON__' => $initial_document_attachment,
        )
    );
    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'rinascente_member_document_admin_assets' );

function rinascente_product_master_admin_assets( $hook ) {
    global $post_type;

    if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) || 'product_master' !== $post_type ) {
        return;
    }

    $style_handle = 'rinascente-product-master-admin';
    $styles       = <<<'CSS'
.post-type-product_master #titlediv,
.post-type-product_master #slugdiv,
.post-type-product_master #post-status-info {
  display: none;
}

.rinascente-product-master-guide {
  margin: 8px 0 18px;
  padding: 18px 20px;
  border: 1px solid #c3d0df;
  border-radius: 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
}

.rinascente-product-master-guide h3,
.rinascente-product-master-section h3 {
  margin: 0 0 10px;
}

.rinascente-product-master-guide p,
.rinascente-product-master-guide ul {
  margin: 0;
}

.rinascente-product-master-guide ul {
  padding-left: 18px;
  margin-top: 10px;
  display: grid;
  gap: 6px;
}

.rinascente-product-master-guide__recommendation {
  margin-top: 14px;
  padding: 12px 14px;
  border-radius: 12px;
  background: #eef6ff;
  color: #0f3d66;
  font-weight: 600;
}

.rinascente-product-master-section {
  margin-top: 18px;
  padding: 18px 20px;
  border: 1px solid #dcdcde;
  border-radius: 14px;
  background: #fff;
}

.rinascente-product-master-section__head {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}

.rinascente-product-master-section .form-table {
  margin-top: 8px;
}

.rinascente-product-master-inline {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  align-items: center;
}

.rinascente-rail-options-editor {
  display: grid;
  gap: 10px;
}

.rinascente-rail-options-list {
  display: grid;
  gap: 8px;
  max-width: 420px;
}

.rinascente-rail-options-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

.rinascente-rail-options-row input[type="number"] {
  width: 90px;
}

.rinascente-product-master-advanced {
  margin-top: 18px;
  padding: 14px 18px 4px;
  border: 1px dashed #c3c4c7;
  border-radius: 14px;
  background: #fbfbfc;
}

.rinascente-product-master-advanced summary {
  cursor: pointer;
  font-weight: 700;
}

.rinascente-product-master-advanced[open] summary {
  margin-bottom: 10px;
}
CSS;

    wp_register_style( $style_handle, false, array(), null );
    wp_enqueue_style( $style_handle );
    wp_add_inline_style( $style_handle, $styles );

    $script = <<<'JS'
(function($){
  $(function(){
    var titleField = $('#title');
    var productKeyField = $('#rinascente_product_key');
    var categoryField = $('#rinascente_product_catalog_category');
    var displayNameField = $('#rinascente_product_catalog_display_name');
    var shortNameField = $('#rinascente_product_catalog_short_name');
    var unitPriceField = $('#rinascente_product_catalog_unit_price');
    var specField = $('#rinascente_product_catalog_spec');
    var installTypeField = $('#rinascente_product_catalog_install_type');
    var maxRailField = $('#rinascente_product_catalog_max_rail_length');
    var railOptionsList = $('#rinascente_product_catalog_rail_length_options');
    var railOptionsEditor = $('[data-rail-options-editor]');
    var railOptionsAddButton = $('[data-rail-option-add]');
    var railPriceField = $('#rinascente_product_catalog_rail_price_per_m');
    var unitLabelField = $('#rinascente_product_catalog_unit_label');
    var maxQuantityField = $('#rinascente_product_catalog_max_quantity');
    var selectionTypeField = $('#rinascente_product_catalog_selection_type');
    var sortOrderField = $('#rinascente_product_catalog_sort_order');
    var codeField = $('#rinascente_product_catalog_code');
    var generateCodeButton = $('#rinascente_product_catalog_code_generate');
    var applyPresetButton = $('#rinascente_product_master_apply_preset');
    var recommendationField = $('#rinascente_product_master_recommendation');

    if (!categoryField.length || !displayNameField.length) {
      return;
    }

    var presetMap = {
      system: {
        unitLabel: '台',
        maxQuantity: '1',
        selectionType: 'quantity',
        sortOrder: '10'
      },
      harness: {
        unitLabel: '着',
        maxQuantity: '5',
        selectionType: 'quantity',
        sortOrder: '40'
      },
      option: {
        unitLabel: '台',
        maxQuantity: '1',
        selectionType: 'quantity',
        sortOrder: '60'
      },
      kit: {
        unitLabel: '台',
        maxQuantity: '1',
        selectionType: 'quantity',
        sortOrder: '110'
      },
      accessory: {
        unitLabel: '個',
        maxQuantity: '1',
        selectionType: 'quantity',
        sortOrder: '120'
      }
    };

    var recommendationMap = {
      system: '本体システムです。価格は「基本価格」、必要なら設置方式・レール長・レール単価も入力してください。',
      harness: 'ハーネスです。価格は 1 着あたりの単価を入れ、数量単位は「着」にします。',
      option: 'オプションです。価格は単価を入れ、YUMEHO での選び方を確認してください。',
      kit: '周辺キットです。価格は単価を入れ、必要なら仕様補足を追加してください。',
      accessory: 'アクセサリーです。価格と単位だけでも登録できます。',
      other: 'その他の商品です。最低限は「商品名」「短い呼び名」「価格」で登録できます。'
    };

    function slugify(value) {
      return (value || '')
        .toString()
        .trim()
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    }

    function syncTitle() {
      var title = $.trim(displayNameField.val()) || $.trim(shortNameField.val());
      if (titleField.length) {
        titleField.val(title);
      }
    }

    function applyPreset(force) {
      var category = categoryField.val();
      var preset = presetMap[category];
      if (!preset) {
        return;
      }

      if (force || !$.trim(unitLabelField.val())) {
        unitLabelField.val(preset.unitLabel);
      }
      if (force || !$.trim(maxQuantityField.val()) || maxQuantityField.val() === '0') {
        maxQuantityField.val(preset.maxQuantity);
      }
      if (force || !$.trim(selectionTypeField.val())) {
        selectionTypeField.val(preset.selectionType);
      }
      if (force || !$.trim(sortOrderField.val()) || sortOrderField.val() === '999') {
        sortOrderField.val(preset.sortOrder);
      }
    }

    function updateRecommendation() {
      var category = categoryField.val() || 'other';
      recommendationField.text(recommendationMap[category] || recommendationMap.other);

      var showSystemFields = category === 'system';
      $('.rinascente-product-master-row--system').toggle(showSystemFields);

      if (!showSystemFields) {
        installTypeField.val('');
        maxRailField.val('0');
        if (railOptionsList.length) {
          railOptionsList.empty().append(createRailOptionRow(''));
        }
        railPriceField.val('');
      } else {
        ensureRailOptionRow();
      }
    }

    function maybeGenerateCode() {
      if ($.trim(codeField.val())) {
        return;
      }

      var candidate = slugify(shortNameField.val()) || slugify(displayNameField.val());
      if (candidate) {
        codeField.val(candidate);
      }
    }

    function createRailOptionRow(value) {
      var row = $('<div class="rinascente-rail-options-row" data-rail-option-row></div>');
      var input = $('<input type="number" min="1" step="1" name="rinascente_product_catalog_rail_length_options[]" class="small-text" data-rail-option-input>');

      if (value !== undefined && value !== null && value !== '') {
        input.val(value);
      }

      row.append(input);
      row.append('<span class="description">m</span>');
      row.append('<button type="button" class="button-link-delete" data-rail-option-remove>削除</button>');

      return row;
    }

    function ensureRailOptionRow() {
      if (!railOptionsList.length) {
        return;
      }

      if (!railOptionsList.find('[data-rail-option-row]').length) {
        railOptionsList.append(createRailOptionRow(''));
      }
    }

    displayNameField.on('input', function(){
      syncTitle();
      maybeGenerateCode();
    });

    shortNameField.on('input', function(){
      syncTitle();
      maybeGenerateCode();
    });

    categoryField.on('change', function(){
      applyPreset(false);
      updateRecommendation();
    });

    generateCodeButton.on('click', function(e){
      e.preventDefault();
      codeField.val(slugify(shortNameField.val()) || slugify(displayNameField.val()));
    });

    applyPresetButton.on('click', function(e){
      e.preventDefault();
      applyPreset(true);
      updateRecommendation();
    });

    railOptionsAddButton.on('click', function(e){
      e.preventDefault();
      if (!railOptionsList.length) {
        return;
      }

      railOptionsList.append(createRailOptionRow(''));
      railOptionsList.find('[data-rail-option-input]').last().trigger('focus');
    });

    railOptionsEditor.on('click', '[data-rail-option-remove]', function(e){
      e.preventDefault();
      var rows = railOptionsList.find('[data-rail-option-row]');

      if (rows.length <= 1) {
        rows.find('[data-rail-option-input]').first().val('').trigger('focus');
        return;
      }

      $(this).closest('[data-rail-option-row]').remove();
    });

    if (!$.trim(productKeyField.val()) && productKeyField.attr('list')) {
      var defaultKey = $('#rinascente_product_key_suggestions option').first().attr('value');
      if (defaultKey) {
        productKeyField.val(defaultKey);
      }
    }

    syncTitle();
    applyPreset(false);
    updateRecommendation();
    maybeGenerateCode();
    ensureRailOptionRow();
  });
})(jQuery);
JS;

    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'rinascente_product_master_admin_assets' );

function rinascente_member_contract_admin_assets( $hook ) {
    global $post_type;

    if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) || 'contract' !== $post_type ) {
        return;
    }

    $product_master_list_url   = admin_url( 'edit.php?post_type=product_master' );
    $product_master_create_url = admin_url( 'post-new.php?post_type=product_master' );
    $product_master_list_json  = wp_json_encode( $product_master_list_url );
    $product_master_create_json = wp_json_encode( $product_master_create_url );
    $style_handle              = 'rinascente-contract-admin';
    $styles                    = <<<'CSS'
.rinascente-member-search-panel {
  margin: 8px 0 12px;
  padding: 14px 16px;
  border: 1px solid #d0d7de;
  border-radius: 12px;
  background: #f7f9fc;
  max-width: 720px;
}

.rinascente-member-search-panel__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.rinascente-member-search-panel__count {
  font-size: 12px;
  color: #50575e;
}

.rinascente-member-search-results {
  display: grid;
  gap: 8px;
  margin-bottom: 8px;
}

.rinascente-member-search-result {
  display: block;
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #c3c4c7;
  border-radius: 10px;
  background: #fff;
  text-align: left;
  cursor: pointer;
  transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
}

.rinascente-member-search-result:hover,
.rinascente-member-search-result:focus {
  border-color: #2271b1;
  box-shadow: 0 0 0 2px rgba(34,113,177,.15);
  background: #f0f6fc;
}

.rinascente-member-search-result.is-selected {
  border-color: #2271b1;
  background: #eaf4ff;
}

.rinascente-member-search-result__facility {
  display: block;
  font-weight: 700;
  font-size: 13px;
  color: #1d2327;
}

.rinascente-member-search-result__meta {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: #50575e;
}

.rinascente-member-search-empty,
.rinascente-member-search-more {
  margin: 0;
  font-size: 12px;
  color: #646970;
}

.rinascente-member-selected {
  margin-top: 10px;
  padding: 12px 14px;
  border-left: 4px solid #2271b1;
  background: #fff;
  max-width: 720px;
}

.rinascente-member-selected strong {
  display: block;
  margin-bottom: 4px;
  color: #1d2327;
}

.rinascente-member-selected span {
  display: block;
  color: #50575e;
}

#rinascente_facility_name[readonly] {
  background: #f6f7f7;
  color: #50575e;
}

.rinascente-member-history {
  margin-top: 12px;
  max-width: 720px;
}

.rinascente-member-history__head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 8px;
}

.rinascente-member-history__grid {
  display: grid;
  gap: 10px;
}

.rinascente-member-history__card {
  padding: 12px 14px;
  border: 1px solid #d0d7de;
  border-radius: 12px;
  background: #fff;
}

.rinascente-member-history__title {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 6px;
}

.rinascente-member-history__title strong {
  color: #1d2327;
}

.rinascente-member-history__meta,
.rinascente-member-history__summary {
  margin: 4px 0 0;
  font-size: 12px;
  color: #50575e;
}

.rinascente-member-history__actions {
  margin-top: 10px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.rinascente-order-warning,
.rinascente-contract-feedback {
  margin-top: 8px;
}

.rinascente-inline-warning,
.rinascente-inline-success,
.rinascente-inline-info {
  margin: 0;
  padding: 10px 12px;
  border-radius: 10px;
  font-size: 12px;
  line-height: 1.7;
}

.rinascente-inline-warning {
  border: 1px solid #e2b203;
  background: #fff8d6;
  color: #5a4500;
}

.rinascente-inline-success {
  border: 1px solid #7ad03a;
  background: #f3fff0;
  color: #235c00;
}

.rinascente-inline-info {
  border: 1px solid #c3c4c7;
  background: #f6f7f7;
  color: #50575e;
}

.rinascente-submit-actions {
  margin-top: 16px;
  padding-top: 12px;
  border-top: 1px solid #dcdcde;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
}

.rinascente-submit-actions .button {
  min-height: 34px;
  line-height: 32px;
  justify-content: center;
}

.rinascente-submit-actions__label {
  display: grid;
  gap: 4px;
  min-width: 240px;
}

.rinascente-submit-actions__buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.rinascente-submit-actions--inline {
  max-width: 720px;
}

body.post-type-contract #titlediv,
body.post-type-contract #postdivrich,
body.post-type-contract #post-status-info,
body.post-type-contract #slugdiv {
  display: none;
}
CSS;

    wp_register_style( $style_handle, false, array(), null );
    wp_enqueue_style( $style_handle );
    wp_add_inline_style( $style_handle, $styles );

    $script = <<<'JS'
(function($){
  $(function(){
    var userField = $('#rinascente_member_user_id');
    var userSearchField = $('#rinascente_member_user_search');
    var feedbackField = $('#rinascente_member_user_search_feedback');
    var resultsField = $('#rinascente_member_user_search_results');
    var selectedUserField = $('#rinascente_member_user_selected');
    var historyField = $('#rinascente_member_contract_history');
    var facilityField = $('#rinascente_facility_name');
    var facilityToggle = $('#rinascente_facility_name_toggle');
    var orderField = $('#rinascente_order_number');
    var orderWarningField = $('#rinascente_order_number_warning');
    var productKeyField = $('#rinascente_product_key');
    var productMasterField = $('#rinascente_product_master_id');
    var productFeedbackField = $('#rinascente_product_master_feedback');
    var quantityField = $('#rinascente_quantity');
    var orderDateField = $('#rinascente_order_date');
    var deliveryDateField = $('#rinascente_delivery_date');
    var contractDateField = $('#rinascente_contract_date');
    var statusField = $('#rinascente_contract_status');
    var paymentStatusField = $('#rinascente_payment_status');
    var contractInfoField = $('#rinascente_contract_info');
    var notesField = $('#rinascente_contract_notes');
    var cloneFeedbackField = $('#rinascente_contract_clone_feedback');
    var applyTemplateButton = $('#rinascente_apply_contract_template');
    var afterSaveField = $('#rinascente_after_save_action');
    var duplicateOverrideField = $('#rinascente_duplicate_override');
    var publishButton = $('#publish');
    var postForm = $('#post');
    var productListUrl = __PRODUCT_MASTER_LIST_JSON__;
    var productCreateUrl = __PRODUCT_MASTER_CREATE_JSON__;
    var currentPostId = parseInt($('#rinascente_contract_post_id_json').text() || '0', 10) || 0;
    var contractHistory = JSON.parse($('#rinascente_contract_history_json').text() || '{}');
    var orderIndex = JSON.parse($('#rinascente_contract_order_index_json').text() || '{}');
    var contractIndex = {};
    var lastAppliedTemplate = '';
    var triggeredSaveAction = false;

    if (!userField.length || !facilityField.length) {
      return;
    }

    $.each(contractHistory, function(userId, items){
      $.each(items || [], function(_, item){
        contractIndex[String(item.id)] = item;
      });
    });

    function escapeHtml(value) {
      return $('<div>').text((value || '').toString()).html();
    }

    function normalizeOrderNumber(value) {
      return (value || '').toString().toLowerCase().replace(/[\s　\-ー_]+/g, '');
    }

    function selectedUserOption() {
      return userField.find('option:selected');
    }

    function setFacilityManualMode(isManual, syncNow) {
      facilityField.prop('readonly', !isManual);
      facilityToggle.text(isManual ? '自動入力に戻す' : '手動で修正');

      if (!isManual && syncNow) {
        applyFacilityName(true);
      }
    }

    function applyFacilityName(force) {
      var selected = selectedUserOption();
      var facilityName = selected.data('facility-name') || '';

      if (!selected.val()) {
        if (force) {
          facilityField.val('');
        }
        return;
      }

      if (force || !facilityField.val().trim()) {
        facilityField.val(facilityName);
      }
    }

    function selectedProductOption() {
      return productMasterField.find('option:selected');
    }

    function currentProductTemplate() {
      return (selectedProductOption().data('contract-template') || '').toString();
    }

    function renderSelectedUser() {
      if (!selectedUserField.length) {
        return;
      }

      var selected = selectedUserOption();

      if (!selected.val()) {
        selectedUserField.html('<strong>選択中の会員</strong><span>まだ選択されていません。</span>');
        return;
      }

      var facilityName = selected.data('facility-name') || '';
      var displayName = selected.data('display-name') || '';
      var userLogin = selected.data('user-login') || '';
      var userEmail = selected.data('user-email') || '';
      var details = [];

      if (displayName) {
        details.push('担当名: ' + displayName);
      }
      if (userLogin) {
        details.push('ID: ' + userLogin);
      }
      if (userEmail) {
        details.push('メール: ' + userEmail);
      }

      selectedUserField.html(
        '<strong>選択中の会員</strong>' +
        '<span>' + escapeHtml(facilityName || displayName || '未設定') + '</span>' +
        '<span>' + escapeHtml(details.join(' / ')) + '</span>'
      );
    }

    function renderContractHistory() {
      if (!historyField.length) {
        return;
      }

      var selected = selectedUserOption();
      var selectedUserId = selected.val();
      var contracts = selectedUserId ? (contractHistory[selectedUserId] || []) : [];

      if (!selectedUserId) {
        historyField.html('');
        return;
      }

      if (!contracts.length) {
        historyField.html(
          '<div class="rinascente-inline-info">' +
            '<strong>この会員の購入履歴</strong><br>まだ購入履歴は登録されていません。新規登録から始めてください。' +
          '</div>'
        );
        return;
      }

      var html = '<div class="rinascente-member-history__head"><strong>この会員の直近登録</strong><span class="description">直近' + contracts.length + '件を表示しています。</span></div>';
      html += '<div class="rinascente-member-history__grid">';
      $.each(contracts, function(_, item){
        var meta = [];
        if (item.orderDate) {
          meta.push('注文日: ' + item.orderDate);
        }
        if (item.statusLabel) {
          meta.push('状態: ' + item.statusLabel);
        }
        if (item.paymentLabel) {
          meta.push('支払: ' + item.paymentLabel);
        }

        html += '<div class="rinascente-member-history__card">';
        html +=   '<div class="rinascente-member-history__title">';
        html +=     '<strong>' + escapeHtml(item.orderNumber || '注文番号未設定') + ' / ' + escapeHtml(item.productName || '製品未設定') + '</strong>';
        html +=     '<a href="' + escapeHtml(item.editUrl) + '">編集</a>';
        html +=   '</div>';
        html +=   '<p class="rinascente-member-history__meta">' + escapeHtml(meta.join(' / ')) + '</p>';
        if (item.contractInfo) {
          html += '<p class="rinascente-member-history__summary">' + escapeHtml(item.contractInfo) + '</p>';
        }
        html +=   '<div class="rinascente-member-history__actions">';
        html +=     '<button type="button" class="button button-secondary" data-clone-contract-id="' + escapeHtml(item.id) + '">この内容を複製</button>';
        html +=   '</div>';
        html += '</div>';
      });
      html += '</div>';

      historyField.html(html);
    }

    function renderUserResults() {
      if (!resultsField.length) {
        return;
      }

      var keyword = (userSearchField.val() || '').toString().trim().toLowerCase();
      var selectedValue = userField.val();
      var matches = [];
      var limit = keyword ? 8 : 5;

      userField.find('option').each(function(){
        var option = $(this);
        var value = option.val();

        if (!value || option.prop('hidden')) {
          return;
        }

        matches.push({
          value: value,
          facilityName: option.data('facility-name') || '',
          displayName: option.data('display-name') || '',
          userLogin: option.data('user-login') || '',
          userEmail: option.data('user-email') || ''
        });
      });

      if (!matches.length) {
        resultsField.html('<p class="rinascente-member-search-empty">該当する会員が見つかりません。施設名・会社名、ID、メールアドレスで検索してください。</p>');
        return;
      }

      var html = matches.slice(0, limit).map(function(item){
        var classes = 'rinascente-member-search-result';
        if (item.value === selectedValue) {
          classes += ' is-selected';
        }

        var meta = [];
        if (item.displayName) {
          meta.push(item.displayName);
        }
        if (item.userLogin) {
          meta.push('ID: ' + item.userLogin);
        }
        if (item.userEmail) {
          meta.push(item.userEmail);
        }

        return '<button type="button" class="' + classes + '" data-user-value="' + escapeHtml(item.value) + '">' +
          '<span class="rinascente-member-search-result__facility">' + escapeHtml(item.facilityName || item.displayName || '名称未設定') + '</span>' +
          '<span class="rinascente-member-search-result__meta">' + escapeHtml(meta.join(' / ')) + '</span>' +
        '</button>';
      }).join('');

      if (matches.length > limit) {
        html += '<p class="rinascente-member-search-more">ほか ' + (matches.length - limit) + ' 件あります。必要ならプルダウンからも選択できます。</p>';
      }

      resultsField.html(html);
    }

    function resultButtons() {
      return resultsField.find('[data-user-value]');
    }

    function filterUsers() {
      if (!userSearchField.length) {
        return;
      }

      var keyword = (userSearchField.val() || '').toString().trim().toLowerCase();
      var visibleCount = 0;
      var selectedValue = userField.val();

      userField.find('option').each(function(){
        var option = $(this);
        var value = option.val();

        if (!value) {
          option.prop('hidden', false);
          return;
        }

        var searchValue = (option.data('search') || option.text() || '').toString().toLowerCase();
        var isMatch = !keyword || searchValue.indexOf(keyword) !== -1;
        var shouldShow = isMatch || value === selectedValue;

        option.prop('hidden', !shouldShow);

        if (shouldShow) {
          visibleCount += 1;
        }
      });

      if (feedbackField.length) {
        feedbackField.text(
          keyword
            ? (visibleCount ? visibleCount + '件ヒットしました。候補カードから選択できます。' : '該当する会員が見つかりません。')
            : (visibleCount ? '全' + visibleCount + '件の会員から選べます。' : '登録済みの会員がありません。')
        );
      }

      renderUserResults();
    }

    function filterProductMasters() {
      if (!productKeyField.length || !productMasterField.length) {
        return;
      }

      var selectedValue = productMasterField.val();
      var selectedOption = selectedValue ? productMasterField.find('option:selected') : $();
      var selectedKey = (selectedOption.data('product-key') || '').toString();
      var currentKey = (productKeyField.val() || '').toString();
      var visibleCount = 0;

      if (selectedValue && currentKey && selectedKey && selectedKey !== currentKey) {
        productMasterField.val('');
        selectedValue = '';
      }

      productMasterField.find('option').each(function(){
        var option = $(this);
        var value = option.val();

        if (!value) {
          option.prop('hidden', false);
          return;
        }

        var optionKey = (option.data('product-key') || '').toString();
        var shouldShow = !currentKey || optionKey === currentKey || value === selectedValue;

        option.prop('hidden', !shouldShow);

        if (shouldShow) {
          visibleCount += 1;
        }
      });

      if (productFeedbackField.length) {
        productFeedbackField.html(
          currentKey
            ? (visibleCount
                ? '対象製品に該当する製品マスターは' + visibleCount + '件です。製品の追加や修正は <a href="' + productListUrl + '">製品マスター</a> から行えます。'
                : 'この対象製品に紐づく製品マスターがありません。<a href="' + productCreateUrl + '">製品マスターを追加</a>してください。')
            : '対象製品を選ぶと候補を絞り込めます。製品の追加や修正は <a href="' + productListUrl + '">製品マスター</a> から行えます。'
        );
      }
    }

    function applyProductKeyFromMaster() {
      if (!productKeyField.length || !productMasterField.length) {
        return;
      }

      var selected = productMasterField.find('option:selected');
      var productKey = (selected.data('product-key') || '').toString();

      if (productKey) {
        productKeyField.val(productKey);
      }
    }

    function applyContractTemplate(force) {
      if (!contractInfoField.length) {
        return;
      }

      var template = currentProductTemplate();
      var currentValue = (contractInfoField.val() || '').toString().trim();

      if (!template) {
        return;
      }

      if (force || !currentValue || currentValue === lastAppliedTemplate) {
        contractInfoField.val(template);
        lastAppliedTemplate = template;

        if (cloneFeedbackField.length) {
          cloneFeedbackField.html('<p class="rinascente-inline-success">製品マスターの契約情報テンプレートを反映しました。</p>');
        }
      }
    }

    function renderDuplicateOrderWarning() {
      if (!orderField.length || !orderWarningField.length) {
        return [];
      }

      var orderNumber = normalizeOrderNumber(orderField.val());
      var matches = orderNumber && orderIndex[orderNumber] ? orderIndex[orderNumber] : [];
      matches = $.grep(matches, function(item){
        return parseInt(item.id, 10) !== currentPostId;
      });

      if (!orderField.val()) {
        orderWarningField.html('');
        duplicateOverrideField.val('');
        return [];
      }

      if (!matches.length) {
        orderWarningField.html('<p class="rinascente-inline-success">この注文番号の重複登録は見つかっていません。</p>');
        duplicateOverrideField.val('');
        return [];
      }

      var html = '<div class="rinascente-inline-warning"><strong>同じ注文番号の登録があります。</strong><br>';
      html += $.map(matches, function(item){
        var parts = [item.facilityName, item.productName, item.statusLabel].filter(Boolean);
        return '<a href="' + escapeHtml(item.editUrl) + '">#' + escapeHtml(item.id) + '</a> ' + escapeHtml(parts.join(' / '));
      }).join('<br>');
      html += '<br>続けて保存する場合は、保存時に確認ダイアログが表示されます。</div>';

      orderWarningField.html(html);
      return matches;
    }

    function cloneContract(contractId) {
      var contract = contractIndex[String(contractId)];
      if (!contract) {
        return;
      }

      if (productKeyField.length && contract.productKey) {
        productKeyField.val(contract.productKey);
      }
      filterProductMasters();

      if (productMasterField.length && contract.productMasterId) {
        productMasterField.val(String(contract.productMasterId));
      }

      applyProductKeyFromMaster();
      filterProductMasters();

      if (quantityField.length) {
        quantityField.val(contract.quantity || '');
      }
      if (statusField.length && contract.status) {
        statusField.val(contract.status);
      }
      if (paymentStatusField.length && contract.paymentStatus) {
        paymentStatusField.val(contract.paymentStatus);
      }
      if (contractInfoField.length) {
        contractInfoField.val(contract.contractInfo || '');
      }
      if (notesField.length) {
        notesField.val(contract.notes || '');
      }
      if (orderField.length) {
        orderField.val('');
      }
      if (orderDateField.length) {
        orderDateField.val('');
      }
      if (deliveryDateField.length) {
        deliveryDateField.val('');
      }
      if (contractDateField.length) {
        contractDateField.val('');
      }

      lastAppliedTemplate = '';
      renderDuplicateOrderWarning();

      if (cloneFeedbackField.length) {
        cloneFeedbackField.html('<p class="rinascente-inline-success">直近の登録内容を複製しました。注文番号と日付を確認して保存してください。</p>');
      }
    }

    userField.on('change', function(){
      setFacilityManualMode(false, false);
      applyFacilityName(true);
      filterUsers();
      renderSelectedUser();
      renderContractHistory();
    });

    if (userSearchField.length) {
      userSearchField.on('input', filterUsers);
      userSearchField.on('keydown', function(event){
        var buttons = resultButtons();

        if ('ArrowDown' === event.key && buttons.length) {
          event.preventDefault();
          buttons.first().focus();
        }

        if ('Enter' === event.key && 1 === buttons.length) {
          event.preventDefault();
          buttons.first().trigger('click');
        }
      });
    }

    if (resultsField.length) {
      resultsField.on('click', '[data-user-value]', function(){
        userField.val($(this).data('user-value')).trigger('change');
      });
      resultsField.on('keydown', '[data-user-value]', function(event){
        var buttons = resultButtons();
        var index = buttons.index(this);

        if ('ArrowDown' === event.key) {
          event.preventDefault();
          buttons.eq(Math.min(index + 1, buttons.length - 1)).focus();
        }

        if ('ArrowUp' === event.key) {
          event.preventDefault();
          if (index <= 0) {
            userSearchField.focus();
          } else {
            buttons.eq(index - 1).focus();
          }
        }

        if ('Escape' === event.key) {
          event.preventDefault();
          userSearchField.focus();
        }
      });
    }

    if (historyField.length) {
      historyField.on('click', '[data-clone-contract-id]', function(){
        cloneContract($(this).data('clone-contract-id'));
      });
    }

    if (facilityToggle.length) {
      facilityToggle.on('click', function(event){
        event.preventDefault();
        var isManual = facilityField.prop('readonly');
        setFacilityManualMode(isManual, !isManual);
      });
    }

    if (productKeyField.length) {
      productKeyField.on('change', filterProductMasters);
    }

    if (productMasterField.length) {
      productMasterField.on('change', function(){
        applyProductKeyFromMaster();
        filterProductMasters();
        applyContractTemplate(false);
      });
    }

    if (applyTemplateButton.length) {
      applyTemplateButton.on('click', function(event){
        event.preventDefault();
        applyContractTemplate(true);
      });
    }

    if (orderField.length) {
      orderField.on('input blur', renderDuplicateOrderWarning);
    }

    $('.rinascente-contract-save-action').on('click', function(event){
      event.preventDefault();

      if (!publishButton.length) {
        return;
      }

      afterSaveField.val($(this).data('after-save') || '');
      triggeredSaveAction = true;
      publishButton.trigger('click');
    });

    if (publishButton.length) {
      publishButton.on('click', function(){
        if (!triggeredSaveAction) {
          afterSaveField.val('');
        }
        triggeredSaveAction = false;
      });
    }

    if (postForm.length) {
      postForm.on('submit', function(event){
        var matches = renderDuplicateOrderWarning();

        if (matches.length && !duplicateOverrideField.val()) {
          if (!window.confirm('同じ注文番号の登録が見つかりました。このまま保存しますか？')) {
            event.preventDefault();
            return false;
          }

          duplicateOverrideField.val('1');
        }
      });
    }

    applyFacilityName(false);
    setFacilityManualMode(false, false);
    filterUsers();
    renderSelectedUser();
    renderContractHistory();
    applyProductKeyFromMaster();
    filterProductMasters();
    renderDuplicateOrderWarning();
  });
})(jQuery);
JS;
    $script = strtr(
        $script,
        array(
            '__PRODUCT_MASTER_LIST_JSON__'   => $product_master_list_json,
            '__PRODUCT_MASTER_CREATE_JSON__' => $product_master_create_json,
        )
    );

    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'rinascente_member_contract_admin_assets' );

function rinascente_contract_redirect_after_save( $location, $post_id ) {
    if ( 'contract' !== get_post_type( $post_id ) ) {
        return $location;
    }

    $member_user_id = (int) get_post_meta( $post_id, '_rinascente_member_user_id', true );
    $product_key    = sanitize_key( get_post_meta( $post_id, '_rinascente_product_key', true ) );
    $after_action   = isset( $_POST['rinascente_after_save_action'] ) ? sanitize_key( wp_unslash( $_POST['rinascente_after_save_action'] ) ) : '';

    if ( 'new_contract' === $after_action ) {
        $args = array(
            'post_type' => 'contract',
        );

        if ( $member_user_id ) {
            $args['member_user_id'] = $member_user_id;
        }

        if ( $product_key ) {
            $args['product_key'] = $product_key;
        }

        return add_query_arg( $args, admin_url( 'post-new.php' ) );
    }

    if ( 'member_preview' === $after_action && $member_user_id ) {
        $preview_url = add_query_arg( 'preview_member', $member_user_id, rinascente_member_page_url() );
        if ( $product_key ) {
            $preview_url = add_query_arg( 'product', $product_key, $preview_url );
        }

        return $preview_url;
    }

    $order_number = get_post_meta( $post_id, '_rinascente_order_number', true );
    $duplicates   = rinascente_member_contract_duplicate_posts( $order_number, $post_id );

    if ( ! empty( $duplicates ) ) {
        $location = add_query_arg( 'rinascente_contract_duplicate', implode( ',', wp_list_pluck( $duplicates, 'ID' ) ), $location );
    }

    return $location;
}
add_filter( 'redirect_post_location', 'rinascente_contract_redirect_after_save', 10, 2 );

function rinascente_contract_duplicate_admin_notice() {
    if ( ! is_admin() || empty( $_GET['rinascente_contract_duplicate'] ) ) {
        return;
    }

    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'contract' !== $screen->post_type ) {
        return;
    }

    $ids = array_values(
        array_filter(
            array_map(
                'absint',
                explode( ',', sanitize_text_field( wp_unslash( $_GET['rinascente_contract_duplicate'] ) ) )
            )
        )
    );

    if ( empty( $ids ) ) {
        return;
    }

    $links = array();
    foreach ( $ids as $id ) {
        $links[] = sprintf(
            '<a href="%s">#%d %s</a>',
            esc_url( admin_url( 'post.php?post=' . $id . '&action=edit' ) ),
            $id,
            esc_html( get_the_title( $id ) )
        );
    }

    printf(
        '<div class="notice notice-warning is-dismissible"><p><strong>同じ注文番号の契約が登録されています。</strong> %s</p></div>',
        implode( ' / ', $links )
    );
}
add_action( 'admin_notices', 'rinascente_contract_duplicate_admin_notice' );

function rinascente_contract_list_lookup_value() {
    if ( isset( $_GET['rinascente_contract_lookup'] ) ) {
        return sanitize_text_field( wp_unslash( $_GET['rinascente_contract_lookup'] ) );
    }

    if ( isset( $_GET['s'] ) ) {
        return sanitize_text_field( wp_unslash( $_GET['s'] ) );
    }

    return '';
}

function rinascente_contract_list_columns( $columns ) {
    $checkbox = $columns['cb'] ?? '';

    return array(
        'cb'              => $checkbox,
        'facility_name'   => '施設名・会員',
        'order_number'    => '注文番号',
        'product_name'    => '製品名',
        'product_key'     => '対象製品',
        'contract_status' => 'ステータス',
        'payment_status'  => '支払い状況',
        'order_date'      => '注文日',
        'date'            => '登録日',
    );
}
add_filter( 'manage_contract_posts_columns', 'rinascente_contract_list_columns' );

function rinascente_contract_list_primary_column( $default, $screen_id ) {
    if ( 'edit-contract' === $screen_id ) {
        return 'facility_name';
    }

    return $default;
}
add_filter( 'list_table_primary_column', 'rinascente_contract_list_primary_column', 10, 2 );

function rinascente_contract_status_badge_markup( $label, $variant = 'neutral' ) {
    $class_map = array(
        'product-yumeho'   => 'rinascente-admin-pill--product-yumeho',
        'product-mica30'   => 'rinascente-admin-pill--product-mica30',
        'status-ordered'   => 'rinascente-admin-pill--status-ordered',
        'status-delivered' => 'rinascente-admin-pill--status-delivered',
        'status-scheduled' => 'rinascente-admin-pill--status-scheduled',
        'status-support'   => 'rinascente-admin-pill--status-support',
        'status-cancelled' => 'rinascente-admin-pill--status-cancelled',
        'payment-pending'  => 'rinascente-admin-pill--payment-pending',
        'payment-paid'     => 'rinascente-admin-pill--payment-paid',
        'payment-partial'  => 'rinascente-admin-pill--payment-partial',
        'payment-refunded' => 'rinascente-admin-pill--payment-refunded',
        'neutral'          => 'rinascente-admin-pill--neutral',
    );

    $class = $class_map[ $variant ] ?? $class_map['neutral'];

    return sprintf(
        '<span class="rinascente-admin-pill %1$s">%2$s</span>',
        esc_attr( $class ),
        esc_html( $label )
    );
}

function rinascente_contract_list_column_content( $column, $post_id ) {
    $snapshot = rinascente_member_get_contract_snapshot( $post_id );
    if ( empty( $snapshot ) ) {
        echo '—';
        return;
    }

    if ( 'facility_name' === $column ) {
        $member_user  = ! empty( $snapshot['memberUserId'] ) ? get_user_by( 'id', (int) $snapshot['memberUserId'] ) : null;
        $member_login = $member_user instanceof WP_User ? $member_user->user_login : '';
        $member_email = $member_user instanceof WP_User ? $member_user->user_email : '';
        $facility     = $snapshot['facilityName'] ?: '未設定';
        $member_meta  = trim( implode( ' / ', array_filter( array( $member_login, $member_email ) ) ) );

        printf(
            '<strong><a href="%1$s">%2$s</a></strong>',
            esc_url( $snapshot['editUrl'] ),
            esc_html( $facility )
        );

        if ( $member_meta ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( $member_meta )
            );
        }
        return;
    }

    if ( 'order_number' === $column ) {
        echo '' !== $snapshot['orderNumber'] ? esc_html( $snapshot['orderNumber'] ) : '<span class="rinascente-admin-muted">未設定</span>';
        return;
    }

    if ( 'product_name' === $column ) {
        $product_name = $snapshot['productName'] ?: $snapshot['title'];
        echo esc_html( $product_name ?: '—' );
        if ( '' !== $snapshot['quantity'] ) {
            printf(
                '<div class="rinascente-admin-subtext">数量: %s</div>',
                esc_html( $snapshot['quantity'] )
            );
        }
        return;
    }

    if ( 'product_key' === $column ) {
        if ( $snapshot['productLabel'] ) {
            $variant = 'mica30' === $snapshot['productKey'] ? 'product-mica30' : 'product-yumeho';
            echo rinascente_contract_status_badge_markup( $snapshot['productLabel'], $variant );
        } else {
            echo '<span class="rinascente-admin-muted">共通</span>';
        }
        return;
    }

    if ( 'contract_status' === $column ) {
        $status_label = $snapshot['statusLabel'] ?: '未設定';
        $variant      = $snapshot['status'] ? 'status-' . $snapshot['status'] : 'neutral';
        echo rinascente_contract_status_badge_markup( $status_label, $variant );
        return;
    }

    if ( 'payment_status' === $column ) {
        $payment_label = $snapshot['paymentLabel'] ?: '未設定';
        $variant       = $snapshot['paymentStatus'] ? 'payment-' . $snapshot['paymentStatus'] : 'neutral';
        echo rinascente_contract_status_badge_markup( $payment_label, $variant );
        return;
    }

    if ( 'order_date' === $column ) {
        echo esc_html( rinascente_member_format_date( $snapshot['orderDate'] ) );
    }
}
add_action( 'manage_contract_posts_custom_column', 'rinascente_contract_list_column_content', 10, 2 );

function rinascente_contract_sortable_columns( $columns ) {
    $columns['facility_name']   = 'facility_name';
    $columns['order_number']    = 'order_number';
    $columns['product_name']    = 'product_name';
    $columns['product_key']     = 'product_key';
    $columns['contract_status'] = 'contract_status';
    $columns['payment_status']  = 'payment_status';
    $columns['order_date']      = 'order_date';

    return $columns;
}
add_filter( 'manage_edit-contract_sortable_columns', 'rinascente_contract_sortable_columns' );

function rinascente_contract_list_filters() {
    global $typenow;

    if ( 'contract' !== $typenow ) {
        return;
    }

    $lookup         = rinascente_contract_list_lookup_value();
    $product_key    = isset( $_GET['rinascente_contract_filter_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_product'] ) ) : '';
    $status         = isset( $_GET['rinascente_contract_filter_status'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_status'] ) ) : '';
    $payment_status = isset( $_GET['rinascente_contract_filter_payment'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_payment'] ) ) : '';
    ?>
    <input type="search" name="rinascente_contract_lookup" value="<?php echo esc_attr( $lookup ); ?>" placeholder="施設名・ID・メール・注文番号・製品名で検索">
    <select name="rinascente_contract_filter_product">
        <option value="">すべての対象製品</option>
        <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="rinascente_contract_filter_status">
        <option value="">すべてのステータス</option>
        <?php foreach ( rinascente_contract_status_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="rinascente_contract_filter_payment">
        <option value="">すべての支払い状況</option>
        <?php foreach ( rinascente_contract_payment_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $payment_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts', 'rinascente_contract_list_filters' );

function rinascente_contract_list_query_filters( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() || 'contract' !== $query->get( 'post_type' ) ) {
        return;
    }

    $lookup         = rinascente_contract_list_lookup_value();
    $product_key    = isset( $_GET['rinascente_contract_filter_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_product'] ) ) : '';
    $status         = isset( $_GET['rinascente_contract_filter_status'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_status'] ) ) : '';
    $payment_status = isset( $_GET['rinascente_contract_filter_payment'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_contract_filter_payment'] ) ) : '';
    $meta_query     = array( 'relation' => 'AND' );

    if ( '' !== $lookup && isset( $_GET['s'] ) && ! isset( $_GET['rinascente_contract_lookup'] ) ) {
        $query->set( 's', '' );
    }

    if ( $product_key ) {
        $meta_query[] = array(
            'key'   => '_rinascente_product_key',
            'value' => $product_key,
        );
    }

    if ( $status ) {
        $meta_query[] = array(
            'key'   => '_rinascente_contract_status',
            'value' => $status,
        );
    }

    if ( $payment_status ) {
        $meta_query[] = array(
            'key'   => '_rinascente_payment_status',
            'value' => $payment_status,
        );
    }

    if ( function_exists( 'rinascente_mica30_enabled' ) && ! rinascente_mica30_enabled() ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_rinascente_product_key',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => '_rinascente_product_key',
                'value'   => 'mica30',
                'compare' => '!=',
            ),
        );
    }

    if ( '' !== $lookup ) {
        $or_meta = array(
            'relation' => 'OR',
            array(
                'key'     => '_rinascente_facility_name',
                'value'   => $lookup,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_rinascente_order_number',
                'value'   => $lookup,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_rinascente_product_name',
                'value'   => $lookup,
                'compare' => 'LIKE',
            ),
        );

        $matched_user_ids = rinascente_member_find_user_ids_for_contract_lookup( $lookup );
        if ( ! empty( $matched_user_ids ) ) {
            $or_meta[] = array(
                'key'     => '_rinascente_member_user_id',
                'value'   => array_map( 'strval', $matched_user_ids ),
                'compare' => 'IN',
            );
        }

        $meta_query[] = $or_meta;
    }

    if ( count( $meta_query ) > 1 ) {
        $query->set( 'meta_query', $meta_query );
    }

    $orderby_map = array(
        'facility_name'   => '_rinascente_facility_name',
        'order_number'    => '_rinascente_order_number',
        'product_name'    => '_rinascente_product_name',
        'product_key'     => '_rinascente_product_key',
        'contract_status' => '_rinascente_contract_status',
        'payment_status'  => '_rinascente_payment_status',
        'order_date'      => '_rinascente_order_date',
    );

    $orderby = $query->get( 'orderby' );
    if ( isset( $orderby_map[ $orderby ] ) ) {
        $query->set( 'meta_key', $orderby_map[ $orderby ] );
        $query->set( 'orderby', 'meta_value' );

        if ( ! $query->get( 'order' ) ) {
            $query->set( 'order', 'ASC' );
        }
        return;
    }

    if ( ! $orderby ) {
        $query->set( 'meta_key', '_rinascente_order_date' );
        $query->set( 'orderby', 'meta_value' );
        $query->set( 'order', 'DESC' );
    }
}
add_action( 'pre_get_posts', 'rinascente_contract_list_query_filters', 20 );

function rinascente_contract_list_admin_assets( $hook ) {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( 'edit.php' !== $hook || ! $screen || 'contract' !== $screen->post_type ) {
        return;
    }

    $styles = <<<'CSS'
.post-type-contract .column-facility_name { width: 24%; }
.post-type-contract .column-order_number { width: 12%; }
.post-type-contract .column-product_name { width: 18%; }
.post-type-contract .column-product_key,
.post-type-contract .column-contract_status,
.post-type-contract .column-payment_status,
.post-type-contract .column-order_date { width: 10%; }
.post-type-contract .tablenav.top .search-box {
  display: none;
}
.rinascente-admin-subtext {
  margin-top: 4px;
  color: #646970;
  font-size: 12px;
  line-height: 1.45;
}
.rinascente-admin-muted {
  color: #8c8f94;
}
.rinascente-admin-pill {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 0 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  line-height: 1;
}
.rinascente-admin-pill--product-yumeho {
  background: #eef7ff;
  color: #0b57a4;
}
.rinascente-admin-pill--product-mica30 {
  background: #edf8f3;
  color: #0f6d46;
}
.rinascente-admin-pill--status-ordered,
.rinascente-admin-pill--status-scheduled,
.rinascente-admin-pill--payment-pending,
.rinascente-admin-pill--payment-partial {
  background: #fff5e6;
  color: #9a5a00;
}
.rinascente-admin-pill--status-delivered,
.rinascente-admin-pill--payment-paid {
  background: #edf8f3;
  color: #0f6d46;
}
.rinascente-admin-pill--status-support,
.rinascente-admin-pill--payment-refunded,
.rinascente-admin-pill--neutral {
  background: #f0f6fc;
  color: #385674;
}
.rinascente-admin-pill--status-cancelled {
  background: #fdeeee;
  color: #b42318;
}
CSS;

    wp_register_style( 'rinascente-contract-list-admin', false, array(), null );
    wp_enqueue_style( 'rinascente-contract-list-admin' );
    wp_add_inline_style( 'rinascente-contract-list-admin', $styles );
}
add_action( 'admin_enqueue_scripts', 'rinascente_contract_list_admin_assets' );

function rinascente_contract_row_actions( $actions, $post ) {
    if ( $post instanceof WP_Post && 'contract' === $post->post_type ) {
        unset( $actions['inline hide-if-no-js'] );
    }

    return $actions;
}
add_filter( 'post_row_actions', 'rinascente_contract_row_actions', 10, 2 );

function rinascente_contract_bulk_actions( $actions ) {
    unset( $actions['edit'] );

    return $actions;
}
add_filter( 'bulk_actions-edit-contract', 'rinascente_contract_bulk_actions' );

function rinascente_member_document_duplicate_url( $post_id ) {
    return wp_nonce_url(
        add_query_arg(
            array(
                'action' => 'rinascente_duplicate_member_document',
                'post'   => absint( $post_id ),
            ),
            admin_url( 'admin.php' )
        ),
        'rinascente_duplicate_member_document_' . absint( $post_id )
    );
}

function rinascente_member_document_row_actions( $actions, $post ) {
    if ( ! ( $post instanceof WP_Post ) || 'member_document' !== $post->post_type ) {
        return $actions;
    }

    unset( $actions['inline hide-if-no-js'] );

    if ( current_user_can( 'edit_post', $post->ID ) ) {
        $actions['rinascente_duplicate_member_document'] = sprintf(
            '<a href="%s">複製して下書き</a>',
            esc_url( rinascente_member_document_duplicate_url( $post->ID ) )
        );
    }

    return $actions;
}
add_filter( 'post_row_actions', 'rinascente_member_document_row_actions', 10, 2 );

function rinascente_member_document_bulk_actions( $actions ) {
    unset( $actions['edit'] );

    return $actions;
}
add_filter( 'bulk_actions-edit-member_document', 'rinascente_member_document_bulk_actions' );

function rinascente_contract_disable_months_dropdown( $disable, $post_type ) {
    if ( 'contract' === $post_type ) {
        return true;
    }

    return $disable;
}
add_filter( 'disable_months_dropdown', 'rinascente_contract_disable_months_dropdown', 10, 2 );

function rinascente_product_master_columns( $columns ) {
    return array(
        'cb'            => $columns['cb'] ?? '',
        'title'         => '商品名',
        'product_scope' => '対象製品・区分',
        'pricing_setup' => '価格・設定',
        'sort_order'    => '表示順',
        'date'          => '更新日',
    );
}
add_filter( 'manage_product_master_posts_columns', 'rinascente_product_master_columns' );

function rinascente_product_master_column_content( $column, $post_id ) {
    if ( 'product_scope' === $column ) {
        $product_key = get_post_meta( $post_id, '_rinascente_product_key', true );
        $catalog_code = get_post_meta( $post_id, '_rinascente_product_catalog_code', true );
        $category     = get_post_meta( $post_id, '_rinascente_product_catalog_category', true );
        $category_map = rinascente_member_product_master_category_choices();
        $variant      = 'mica30' === $product_key ? 'product-mica30' : ( 'yumeho' === $product_key ? 'product-yumeho' : 'neutral' );

        if ( '' !== (string) $product_key ) {
            echo rinascente_contract_status_badge_markup( rinascente_member_product_label( $product_key ), $variant ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            echo '<span class="rinascente-admin-muted">未設定</span>';
        }

        if ( isset( $category_map[ $category ] ) ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( $category_map[ $category ] )
            );
        }

        if ( '' !== trim( (string) $catalog_code ) ) {
            printf(
                '<div class="rinascente-admin-subtext">連携コード: %s</div>',
                esc_html( $catalog_code )
            );
        }
        return;
    }

    if ( 'pricing_setup' === $column ) {
        $unit_price      = rinascente_member_sanitize_price( get_post_meta( $post_id, '_rinascente_product_catalog_unit_price', true ) );
        $rail_price      = rinascente_member_sanitize_price( get_post_meta( $post_id, '_rinascente_product_catalog_rail_price_per_m', true ) );
        $unit_label      = trim( (string) get_post_meta( $post_id, '_rinascente_product_catalog_unit_label', true ) );
        $max_quantity    = absint( get_post_meta( $post_id, '_rinascente_product_catalog_max_quantity', true ) );
        $selection_type  = get_post_meta( $post_id, '_rinascente_product_catalog_selection_type', true );
        $selection_label = rinascente_member_product_selection_type_choices()[ $selection_type ] ?? '';
        $labels = array();
        if ( $unit_price > 0 ) {
            $labels[] = '¥' . number_format( $unit_price );
        }
        if ( $rail_price > 0 ) {
            $labels[] = 'レール ¥' . number_format( $rail_price ) . '/m';
        }

        echo ! empty( $labels ) ? esc_html( implode( ' / ', $labels ) ) : '<span class="rinascente-admin-muted">未設定</span>';

        $details = array();
        if ( '' !== $unit_label ) {
            $details[] = '単位: ' . $unit_label;
        }
        if ( $max_quantity > 0 ) {
            $details[] = '最大数: ' . $max_quantity;
        }
        if ( '' !== $selection_label ) {
            $details[] = $selection_label;
        }

        if ( ! empty( $details ) ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( implode( ' / ', $details ) )
            );
        }
        return;
    }

    if ( 'sort_order' === $column ) {
        $sort_order = get_post_meta( $post_id, '_rinascente_product_catalog_sort_order', true );
        echo '' !== (string) $sort_order ? esc_html( (string) $sort_order ) : '999';
    }
}
add_action( 'manage_product_master_posts_custom_column', 'rinascente_product_master_column_content', 10, 2 );

function rinascente_product_master_sortable_columns( $columns ) {
    $columns['sort_order'] = 'sort_order';
    return $columns;
}
add_filter( 'manage_edit-product_master_sortable_columns', 'rinascente_product_master_sortable_columns' );

function rinascente_product_master_list_filters() {
    global $typenow;

    if ( 'product_master' !== $typenow ) {
        return;
    }

    $product_key = isset( $_GET['rinascente_product_master_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_product_master_product'] ) ) : '';
    $category    = isset( $_GET['rinascente_product_master_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_product_master_category'] ) ) : '';
    ?>
    <select name="rinascente_product_master_product">
        <option value="">すべての対象製品</option>
        <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="rinascente_product_master_category">
        <option value="">すべての区分</option>
        <?php foreach ( rinascente_member_product_master_category_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts', 'rinascente_product_master_list_filters' );

function rinascente_product_master_admin_order( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() || 'product_master' !== $query->get( 'post_type' ) ) {
        return;
    }

    $product_key = isset( $_GET['rinascente_product_master_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_product_master_product'] ) ) : '';
    $category    = isset( $_GET['rinascente_product_master_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_product_master_category'] ) ) : '';
    $meta_query  = (array) $query->get( 'meta_query' );

    if ( ! isset( $meta_query['relation'] ) ) {
        $meta_query['relation'] = 'AND';
    }

    if ( $product_key ) {
        $meta_query[] = array(
            'key'   => '_rinascente_product_key',
            'value' => $product_key,
        );
    }

    if ( $category ) {
        $meta_query[] = array(
            'key'   => '_rinascente_product_catalog_category',
            'value' => $category,
        );
    }

    $query->set( 'meta_key', '_rinascente_product_catalog_sort_order' );

    if ( function_exists( 'rinascente_mica30_enabled' ) && ! rinascente_mica30_enabled() ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_rinascente_product_key',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     => '_rinascente_product_key',
                'value'   => 'mica30',
                'compare' => '!=',
            ),
        );
    }

    if ( count( $meta_query ) > 1 ) {
        $query->set( 'meta_query', $meta_query );
    }

    if ( 'sort_order' === $query->get( 'orderby' ) ) {
        $query->set( 'orderby', 'meta_value_num' );
        if ( ! $query->get( 'order' ) ) {
            $query->set( 'order', 'ASC' );
        }
        return;
    }

    if ( $query->get( 'orderby' ) ) {
        return;
    }

    $query->set(
        'orderby',
        array(
            'meta_value_num' => 'ASC',
            'title'          => 'ASC',
        )
    );
    $query->set( 'order', 'ASC' );
}
add_action( 'pre_get_posts', 'rinascente_product_master_admin_order' );

function rinascente_product_master_admin_notices() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-product_master' !== $screen->id ) {
        return;
    }

    $counts    = wp_count_posts( 'product_master' );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;
    ?>
    <div class="notice rinascente-product-master-list-guide">
        <div class="rinascente-product-master-list-guide__stats">
            <span class="rinascente-product-master-list-guide__stat"><strong><?php echo esc_html( (string) $published ); ?></strong> 公開中</span>
            <span class="rinascente-product-master-list-guide__stat"><strong><?php echo esc_html( (string) $drafts ); ?></strong> 下書き</span>
        </div>
        <p class="rinascente-product-master-list-guide__text">ここで設定した商品名、価格、選択設定は YUMEHO の価格表示やシミュレーション、契約・購入履歴の製品選択にそのまま反映されます。</p>
    </div>
    <?php
}
add_action( 'admin_notices', 'rinascente_product_master_admin_notices' );

function rinascente_product_master_list_admin_assets( $hook ) {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( 'edit.php' !== $hook || ! $screen || 'product_master' !== $screen->post_type ) {
        return;
    }

    $styles = <<<'CSS'
.post-type-product_master .wrap > h1.wp-heading-inline {
  margin-bottom: 6px;
}
.post-type-product_master .subsubsub {
  margin: 6px 0 8px;
}
.post-type-product_master .tablenav.top {
  margin: 8px 0 10px;
  min-height: 36px;
}
.post-type-product_master .tablenav.bottom {
  margin-top: 10px;
}
.post-type-product_master .tablenav .actions select,
.post-type-product_master .tablenav .button,
.post-type-product_master .search-box input[type="search"] {
  min-height: 36px;
  border-radius: 10px;
}
.post-type-product_master .search-box input[type="search"] {
  min-width: 240px;
  padding-inline: 14px;
}
.post-type-product_master .wp-list-table {
  border: 1px solid #dbe4ee;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 8px 28px rgba(15, 23, 42, 0.04);
}
.post-type-product_master .wp-list-table .column-rank_math_seo_details,
.post-type-product_master .wp-list-table .column-rank_math_title,
.post-type-product_master .wp-list-table .column-rank_math_description {
  display: none;
}
.post-type-product_master .wp-list-table thead th,
.post-type-product_master .wp-list-table tfoot th {
  background: #f8fbff;
}
.post-type-product_master .wp-list-table tbody tr:hover {
  background: #fcfdff;
}
.post-type-product_master .wp-list-table tbody td,
.post-type-product_master .wp-list-table tbody th {
  padding-top: 10px;
  padding-bottom: 10px;
  vertical-align: middle;
}
.post-type-product_master .column-title { width: 28%; }
.post-type-product_master .column-product_scope { width: 24%; }
.post-type-product_master .column-pricing_setup { width: 28%; }
.post-type-product_master .column-sort_order { width: 8%; }
.post-type-product_master .column-date { width: 12%; }
.post-type-product_master .column-title .row-title {
  font-size: 14px;
  line-height: 1.55;
}
.rinascente-product-master-list-guide {
  border: 1px solid #cfe0f2;
  border-left: 4px solid #0068b7;
  border-radius: 14px;
  padding: 10px 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
  margin: 10px 0 8px;
}
.rinascente-product-master-list-guide__stats {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 6px;
}
.rinascente-product-master-list-guide__stat {
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
.rinascente-product-master-list-guide__text,
.rinascente-admin-subtext {
  color: #334155;
  font-size: 12px;
  line-height: 1.65;
}
.rinascente-admin-subtext {
  margin-top: 4px;
}
.rinascente-admin-muted {
  color: #8c8f94;
}
.rinascente-admin-pill {
  display: inline-flex;
  align-items: center;
  min-height: 28px;
  padding: 0 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 700;
  line-height: 1;
}
.rinascente-admin-pill--product-yumeho {
  background: #eef7ff;
  color: #0b57a4;
}
.rinascente-admin-pill--product-mica30 {
  background: #edf8f3;
  color: #0f6d46;
}
.rinascente-admin-pill--neutral {
  background: #f0f6fc;
  color: #385674;
}
CSS;

    wp_register_style( 'rinascente-product-master-list-admin', false, array(), null );
    wp_enqueue_style( 'rinascente-product-master-list-admin' );
    wp_add_inline_style( 'rinascente-product-master-list-admin', $styles );
}
add_action( 'admin_enqueue_scripts', 'rinascente_product_master_list_admin_assets' );

function rinascente_product_master_list_admin_footer_script() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'edit-product_master' !== $screen->id ) {
        return;
    }
    ?>
    <script>
    (function($){
      var $search = $('#post-search-input');
      if ($search.length) {
        $search.attr('placeholder', '商品名・短い呼び名で検索');
      }
    })(jQuery);
    </script>
    <?php
}
add_action( 'admin_footer-edit.php', 'rinascente_product_master_list_admin_footer_script' );

function rinascente_hide_pending_mica30_member_content_from_admin_lists( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( ! function_exists( 'rinascente_mica30_enabled' ) || rinascente_mica30_enabled() ) {
        return;
    }

    $post_type = $query->get( 'post_type' );
    if ( ! in_array( $post_type, array( 'member_video', 'member_document', 'member_notice', 'member_review' ), true ) ) {
        return;
    }

    $meta_query = (array) $query->get( 'meta_query' );
    if ( ! isset( $meta_query['relation'] ) ) {
        $meta_query['relation'] = 'AND';
    }

    $meta_query[] = array(
        'relation' => 'OR',
        array(
            'key'     => '_rinascente_product_key',
            'compare' => 'NOT EXISTS',
        ),
        array(
            'key'     => '_rinascente_product_key',
            'value'   => 'mica30',
            'compare' => '!=',
        ),
    );

    $query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'rinascente_hide_pending_mica30_member_content_from_admin_lists' );

function rinascente_member_admin_product_badge_markup( $product_key ) {
    $product_key = sanitize_key( (string) $product_key );
    if ( '' === $product_key ) {
        return '<span class="rinascente-admin-pill rinascente-admin-pill--neutral">共通</span>';
    }

    $variant = 'mica30' === $product_key ? 'product-mica30' : ( 'yumeho' === $product_key ? 'product-yumeho' : 'neutral' );
    return rinascente_contract_status_badge_markup( rinascente_member_product_label( $product_key ), $variant );
}

function rinascente_member_admin_compact_text( $text, $length = 84 ) {
    $text = wp_strip_all_tags( (string) $text, true );
    $text = preg_replace( '/\s+/u', ' ', $text );
    $text = trim( (string) $text );

    if ( '' === $text ) {
        return '';
    }

    return wp_html_excerpt( $text, $length, '…' );
}

function rinascente_member_admin_schedule_snapshot( $post_id ) {
    $start_date = trim( (string) get_post_meta( $post_id, '_rinascente_start_date', true ) );
    $end_date   = trim( (string) get_post_meta( $post_id, '_rinascente_end_date', true ) );
    $today      = wp_date( 'Y-m-d' );

    if ( '' !== $start_date && $today < $start_date ) {
        return array(
            'label'   => '公開前',
            'variant' => 'neutral',
            'detail'  => '開始: ' . rinascente_member_format_date( $start_date ),
        );
    }

    if ( '' !== $end_date && $today > $end_date ) {
        return array(
            'label'   => '終了',
            'variant' => 'status-cancelled',
            'detail'  => '終了: ' . rinascente_member_format_date( $end_date ),
        );
    }

    if ( '' === $start_date && '' === $end_date ) {
        return array(
            'label'   => '常時公開',
            'variant' => 'status-support',
            'detail'  => '期間指定なし',
        );
    }

    $parts = array();
    if ( '' !== $start_date ) {
        $parts[] = rinascente_member_format_date( $start_date );
    }
    if ( '' !== $end_date ) {
        $parts[] = rinascente_member_format_date( $end_date );
    }

    return array(
        'label'   => '公開中',
        'variant' => 'status-delivered',
        'detail'  => ! empty( $parts ) ? implode( ' 〜 ', $parts ) : '',
    );
}

function rinascente_member_content_admin_post_types() {
    return array( 'member_video', 'member_document', 'member_notice' );
}

function rinascente_member_content_admin_post_type_label( $post_type ) {
    $labels = array(
        'member_video'    => '会員限定動画',
        'member_document' => '会員限定資料',
        'member_notice'   => 'サポート情報',
    );

    return $labels[ $post_type ] ?? $post_type;
}

function rinascente_member_content_list_columns_base( $columns, $title_label = 'タイトル' ) {
    return array(
        'cb'               => $columns['cb'] ?? '',
        'title'            => $title_label,
        'content_overview' => '内容',
        'product_key'      => '対象製品',
        'schedule'         => '公開状況',
        'date'             => '更新日',
    );
}

function rinascente_member_video_list_columns( $columns ) {
    return rinascente_member_content_list_columns_base( $columns, '動画タイトル' );
}
add_filter( 'manage_member_video_posts_columns', 'rinascente_member_video_list_columns' );

function rinascente_member_document_list_columns( $columns ) {
    return rinascente_member_content_list_columns_base( $columns, '資料名' );
}
add_filter( 'manage_member_document_posts_columns', 'rinascente_member_document_list_columns' );

function rinascente_member_notice_list_columns( $columns ) {
    return rinascente_member_content_list_columns_base( $columns, 'タイトル' );
}
add_filter( 'manage_member_notice_posts_columns', 'rinascente_member_notice_list_columns' );

function rinascente_member_content_list_column_content( $column, $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( ! in_array( $post_type, rinascente_member_content_admin_post_types(), true ) ) {
        return;
    }

    if ( 'product_key' === $column ) {
        echo rinascente_member_admin_product_badge_markup( get_post_meta( $post_id, '_rinascente_product_key', true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return;
    }

    if ( 'schedule' === $column ) {
        $schedule = rinascente_member_admin_schedule_snapshot( $post_id );
        echo rinascente_contract_status_badge_markup( $schedule['label'], $schedule['variant'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( '' !== trim( (string) $schedule['detail'] ) ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( $schedule['detail'] )
            );
        }

        if ( 'member_document' === $post_type ) {
            $updated_date = trim( (string) get_post_meta( $post_id, '_rinascente_document_updated_date', true ) );
            if ( '' !== $updated_date ) {
                printf(
                    '<div class="rinascente-admin-subtext">資料更新日: %s</div>',
                    esc_html( rinascente_member_format_date( $updated_date ) )
                );
            }
        }
        return;
    }

    if ( 'content_overview' !== $column ) {
        return;
    }

    if ( 'member_video' === $post_type ) {
        $category       = get_post_meta( $post_id, '_rinascente_video_category', true );
        $youtube_id     = trim( (string) get_post_meta( $post_id, '_rinascente_youtube_id', true ) );
        $description    = trim( (string) get_post_meta( $post_id, '_rinascente_video_description', true ) );
        $category_label = rinascente_member_video_category_choices()[ $category ] ?? 'カテゴリ未設定';

        echo esc_html( $category_label );

        if ( '' !== $youtube_id ) {
            printf(
                '<div class="rinascente-admin-subtext">YouTube ID: %s</div>',
                esc_html( $youtube_id )
            );
        }

        if ( '' !== $description ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( rinascente_member_admin_compact_text( $description ) )
            );
        }
        return;
    }

    if ( 'member_document' === $post_type ) {
        $category        = get_post_meta( $post_id, '_rinascente_document_category', true );
        $category_label  = rinascente_member_document_category_choices()[ $category ] ?? 'カテゴリ未設定';
        $program_name    = trim( (string) get_post_meta( $post_id, '_rinascente_program_name', true ) );
        $subsidy_status  = trim( (string) get_post_meta( $post_id, '_rinascente_subsidy_status', true ) );
        $file            = rinascente_member_document_file_data( $post_id );

        echo esc_html( $category_label );

        if ( ! empty( $file['filename'] ) ) {
            $file_label = $file['filename'];
            if ( ! empty( $file['size'] ) ) {
                $file_label .= ' / ' . $file['size'];
            }
            printf(
                '<div class="rinascente-admin-subtext">ファイル: %s</div>',
                esc_html( $file_label )
            );
        } else {
            echo '<div class="rinascente-admin-subtext rinascente-admin-muted">ファイル未設定</div>';
        }

        if ( '' !== $program_name || '' !== $subsidy_status ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( implode( ' / ', array_filter( array( $program_name, $subsidy_status ) ) ) )
            );
        }
        return;
    }

    if ( 'member_notice' === $post_type ) {
        $tone          = get_post_meta( $post_id, '_rinascente_notice_tone', true );
        $tone_label    = rinascente_member_notice_tone_choices()[ $tone ] ?? '一般';
        $tone_variant  = 'maintenance' === $tone ? 'status-support' : ( 'urgent' === $tone ? 'status-cancelled' : ( 'info' === $tone ? 'product-yumeho' : 'neutral' ) );
        $notice_summary = rinascente_member_notice_summary( get_post( $post_id ) );

        echo rinascente_contract_status_badge_markup( $tone_label, $tone_variant ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( '' !== trim( (string) $notice_summary ) ) {
            printf(
                '<div class="rinascente-admin-subtext">%s</div>',
                esc_html( rinascente_member_admin_compact_text( $notice_summary ) )
            );
        }
    }
}
add_action( 'manage_member_video_posts_custom_column', 'rinascente_member_content_list_column_content', 10, 2 );
add_action( 'manage_member_document_posts_custom_column', 'rinascente_member_content_list_column_content', 10, 2 );
add_action( 'manage_member_notice_posts_custom_column', 'rinascente_member_content_list_column_content', 10, 2 );

function rinascente_member_content_list_filters() {
    global $typenow;

    if ( ! in_array( $typenow, rinascente_member_content_admin_post_types(), true ) ) {
        return;
    }

    $product_key = isset( $_GET['rinascente_member_filter_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_filter_product'] ) ) : '';
    ?>
    <select name="rinascente_member_filter_product">
        <option value="">すべての対象製品</option>
        <option value="common" <?php selected( $product_key, 'common' ); ?>>共通</option>
        <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php

    if ( 'member_video' === $typenow ) {
        $category = isset( $_GET['rinascente_member_video_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_video_category'] ) ) : '';
        ?>
        <select name="rinascente_member_video_category">
            <option value="">すべてのカテゴリ</option>
            <?php foreach ( rinascente_member_video_category_choices() as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    } elseif ( 'member_document' === $typenow ) {
        $category = isset( $_GET['rinascente_member_document_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_document_category'] ) ) : '';
        $file_state = isset( $_GET['rinascente_member_document_file_state'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_document_file_state'] ) ) : '';
        ?>
        <select name="rinascente_member_document_category">
            <option value="">すべてのカテゴリ</option>
            <?php foreach ( rinascente_member_document_category_choices() as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="rinascente_member_document_file_state">
            <option value="">すべてのファイル状態</option>
            <option value="has_file" <?php selected( $file_state, 'has_file' ); ?>>ファイルあり</option>
            <option value="missing_file" <?php selected( $file_state, 'missing_file' ); ?>>ファイル未設定</option>
        </select>
        <?php
    } elseif ( 'member_notice' === $typenow ) {
        $tone = isset( $_GET['rinascente_member_notice_tone'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_notice_tone'] ) ) : '';
        ?>
        <select name="rinascente_member_notice_tone">
            <option value="">すべての表示タイプ</option>
            <?php foreach ( rinascente_member_notice_tone_choices() as $value => $label ) : ?>
                <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $tone, $value ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}
add_action( 'restrict_manage_posts', 'rinascente_member_content_list_filters' );

function rinascente_member_content_list_query_filters( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $post_type = $query->get( 'post_type' );
    if ( ! in_array( $post_type, rinascente_member_content_admin_post_types(), true ) ) {
        return;
    }

    $meta_query  = (array) $query->get( 'meta_query' );
    if ( ! isset( $meta_query['relation'] ) ) {
        $meta_query['relation'] = 'AND';
    }

    $product_key = isset( $_GET['rinascente_member_filter_product'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_filter_product'] ) ) : '';
    if ( 'common' === $product_key ) {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_rinascente_product_key',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'   => '_rinascente_product_key',
                'value' => '',
            ),
        );
    } elseif ( '' !== $product_key ) {
        $meta_query[] = array(
            'key'   => '_rinascente_product_key',
            'value' => $product_key,
        );
    }

    if ( 'member_video' === $post_type ) {
        $category = isset( $_GET['rinascente_member_video_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_video_category'] ) ) : '';
        if ( '' !== $category ) {
            $meta_query[] = array(
                'key'   => '_rinascente_video_category',
                'value' => $category,
            );
        }

        if ( ! $query->get( 'orderby' ) ) {
            $query->set(
                'orderby',
                array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                )
            );
        }
    } elseif ( 'member_document' === $post_type ) {
        $category = isset( $_GET['rinascente_member_document_category'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_document_category'] ) ) : '';
        if ( '' !== $category ) {
            $meta_query[] = array(
                'key'   => '_rinascente_document_category',
                'value' => $category,
            );
        }

        $file_state = isset( $_GET['rinascente_member_document_file_state'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_document_file_state'] ) ) : '';
        if ( 'has_file' === $file_state ) {
            $meta_query[] = array(
                'key'     => '_rinascente_attachment_id',
                'value'   => 0,
                'type'    => 'NUMERIC',
                'compare' => '>',
            );
        } elseif ( 'missing_file' === $file_state ) {
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_rinascente_attachment_id',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'   => '_rinascente_attachment_id',
                    'value' => '',
                ),
                array(
                    'key'     => '_rinascente_attachment_id',
                    'value'   => 0,
                    'type'    => 'NUMERIC',
                    'compare' => '=',
                ),
            );
        }

        if ( ! $query->get( 'orderby' ) ) {
            $query->set(
                'orderby',
                array(
                    'menu_order' => 'ASC',
                    'date'       => 'DESC',
                )
            );
        }
    } elseif ( 'member_notice' === $post_type ) {
        $tone = isset( $_GET['rinascente_member_notice_tone'] ) ? sanitize_key( wp_unslash( $_GET['rinascente_member_notice_tone'] ) ) : '';
        if ( '' !== $tone ) {
            $meta_query[] = array(
                'key'   => '_rinascente_notice_tone',
                'value' => $tone,
            );
        }

        if ( ! $query->get( 'orderby' ) ) {
            $query->set( 'orderby', 'date' );
            $query->set( 'order', 'DESC' );
        }
    }

    if ( count( $meta_query ) > 1 ) {
        $query->set( 'meta_query', $meta_query );
    }
}
add_action( 'pre_get_posts', 'rinascente_member_content_list_query_filters', 20 );

function rinascente_member_document_admin_overview_counts() {
    static $counts = null;

    if ( null !== $counts ) {
        return $counts;
    }

    $counts = array(
        'missing_file' => 0,
        'common'       => 0,
        'subsidy'      => 0,
        'scheduled'    => 0,
        'expired'      => 0,
    );

    $document_ids = get_posts(
        array(
            'post_type'      => 'member_document',
            'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    $today = current_time( 'Y-m-d' );

    foreach ( $document_ids as $document_id ) {
        $attachment_id = absint( get_post_meta( $document_id, '_rinascente_attachment_id', true ) );
        $product_key   = trim( (string) get_post_meta( $document_id, '_rinascente_product_key', true ) );
        $category      = trim( (string) get_post_meta( $document_id, '_rinascente_document_category', true ) );
        $start_date    = trim( (string) get_post_meta( $document_id, '_rinascente_start_date', true ) );
        $end_date      = trim( (string) get_post_meta( $document_id, '_rinascente_end_date', true ) );

        if ( $attachment_id <= 0 ) {
            $counts['missing_file']++;
        }

        if ( '' === $product_key ) {
            $counts['common']++;
        }

        if ( 'subsidy' === $category ) {
            $counts['subsidy']++;
        }

        if ( '' !== $start_date && $today < $start_date ) {
            $counts['scheduled']++;
        }

        if ( '' !== $end_date && $today > $end_date ) {
            $counts['expired']++;
        }
    }

    return $counts;
}

function rinascente_member_content_admin_notices() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || ! in_array( $screen->post_type, rinascente_member_content_admin_post_types(), true ) || 0 !== strpos( $screen->id, 'edit-' ) ) {
        return;
    }

    $post_type = $screen->post_type;
    $counts    = wp_count_posts( $post_type );
    $published = isset( $counts->publish ) ? (int) $counts->publish : 0;
    $drafts    = isset( $counts->draft ) ? (int) $counts->draft : 0;

    $messages = array(
        'member_video'    => '一覧では動画カテゴリ、YouTube ID、対象製品、公開期間をまとめて確認できます。設置方法やサポートなどの分類で絞り込みもできます。',
        'member_document' => '一覧では資料カテゴリ、ファイル有無、対象製品、公開期間をまとめて確認できます。ファイル状態での絞り込みと、行メニューの「複製して下書き」で更新用の下書き作成もできます。',
        'member_notice'   => '一覧では表示タイプ、対象製品、現在の公開状況をまとめて確認できます。重要・保守・お知らせの種類で絞り込みできます。',
    );
    $document_links = array();
    $document_extra_stats = array();

    if ( 'member_document' === $post_type ) {
        $document_counts = rinascente_member_document_admin_overview_counts();
        $document_links  = array(
            array(
                'label'   => '新規資料を追加',
                'url'     => admin_url( 'post-new.php?post_type=member_document' ),
                'primary' => true,
            ),
            array(
                'label' => sprintf( 'ファイル未設定 %d件', (int) $document_counts['missing_file'] ),
                'url'   => add_query_arg(
                    array(
                        'post_type'                             => 'member_document',
                        'rinascente_member_document_file_state' => 'missing_file',
                    ),
                    admin_url( 'edit.php' )
                ),
            ),
            array(
                'label' => sprintf( '共通資料 %d件', (int) $document_counts['common'] ),
                'url'   => add_query_arg(
                    array(
                        'post_type'                       => 'member_document',
                        'rinascente_member_filter_product' => 'common',
                    ),
                    admin_url( 'edit.php' )
                ),
            ),
            array(
                'label' => sprintf( '補助金資料 %d件', (int) $document_counts['subsidy'] ),
                'url'   => add_query_arg(
                    array(
                        'post_type'                         => 'member_document',
                        'rinascente_member_document_category' => 'subsidy',
                    ),
                    admin_url( 'edit.php' )
                ),
            ),
        );
        $document_extra_stats = array(
            sprintf( '公開前 %d件', (int) $document_counts['scheduled'] ),
            sprintf( '公開終了 %d件', (int) $document_counts['expired'] ),
        );
    }
    ?>
    <div class="notice rinascente-member-content-list-guide">
        <div class="rinascente-member-content-list-guide__stats">
            <span class="rinascente-member-content-list-guide__stat"><strong><?php echo esc_html( (string) $published ); ?></strong> 公開中</span>
            <span class="rinascente-member-content-list-guide__stat"><strong><?php echo esc_html( (string) $drafts ); ?></strong> 下書き</span>
        </div>
        <p class="rinascente-member-content-list-guide__text"><?php echo esc_html( $messages[ $post_type ] ?? '' ); ?></p>
        <?php if ( ! empty( $document_links ) ) : ?>
        <div class="rinascente-member-content-list-guide__actions">
            <?php foreach ( $document_links as $link ) : ?>
                <a class="button <?php echo ! empty( $link['primary'] ) ? 'button-primary' : 'button-secondary'; ?>" href="<?php echo esc_url( $link['url'] ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ( ! empty( $document_extra_stats ) ) : ?>
        <div class="rinascente-member-content-list-guide__stats rinascente-member-content-list-guide__stats--compact">
            <?php foreach ( $document_extra_stats as $extra_stat ) : ?>
                <span class="rinascente-member-content-list-guide__stat"><?php echo esc_html( $extra_stat ); ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}
add_action( 'admin_notices', 'rinascente_member_content_admin_notices' );

function rinascente_member_content_list_admin_assets( $hook ) {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( 'edit.php' !== $hook || ! $screen || ! in_array( $screen->post_type, rinascente_member_content_admin_post_types(), true ) ) {
        return;
    }

    $styles = <<<'CSS'
.post-type-member_video .wrap > h1.wp-heading-inline,
.post-type-member_document .wrap > h1.wp-heading-inline,
.post-type-member_notice .wrap > h1.wp-heading-inline {
  margin-bottom: 6px;
}
.post-type-member_video .subsubsub,
.post-type-member_document .subsubsub,
.post-type-member_notice .subsubsub {
  margin: 6px 0 8px;
}
.post-type-member_video .tablenav.top,
.post-type-member_document .tablenav.top,
.post-type-member_notice .tablenav.top {
  margin: 8px 0 10px;
  min-height: 36px;
}
.post-type-member_video .tablenav.bottom,
.post-type-member_document .tablenav.bottom,
.post-type-member_notice .tablenav.bottom {
  margin-top: 10px;
}
.post-type-member_video .tablenav .actions select,
.post-type-member_video .tablenav .button,
.post-type-member_video .search-box input[type="search"],
.post-type-member_document .tablenav .actions select,
.post-type-member_document .tablenav .button,
.post-type-member_document .search-box input[type="search"],
.post-type-member_notice .tablenav .actions select,
.post-type-member_notice .tablenav .button,
.post-type-member_notice .search-box input[type="search"] {
  min-height: 36px;
  border-radius: 10px;
}
.post-type-member_video .search-box input[type="search"],
.post-type-member_document .search-box input[type="search"],
.post-type-member_notice .search-box input[type="search"] {
  min-width: 240px;
  padding-inline: 14px;
}
.post-type-member_video .wp-list-table,
.post-type-member_document .wp-list-table,
.post-type-member_notice .wp-list-table {
  border: 1px solid #dbe4ee;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 8px 28px rgba(15, 23, 42, 0.04);
}
.post-type-member_video .wp-list-table .column-rank_math_seo_details,
.post-type-member_video .wp-list-table .column-rank_math_title,
.post-type-member_video .wp-list-table .column-rank_math_description,
.post-type-member_document .wp-list-table .column-rank_math_seo_details,
.post-type-member_document .wp-list-table .column-rank_math_title,
.post-type-member_document .wp-list-table .column-rank_math_description,
.post-type-member_notice .wp-list-table .column-rank_math_seo_details,
.post-type-member_notice .wp-list-table .column-rank_math_title,
.post-type-member_notice .wp-list-table .column-rank_math_description {
  display: none;
}
.post-type-member_video .wp-list-table thead th,
.post-type-member_video .wp-list-table tfoot th,
.post-type-member_document .wp-list-table thead th,
.post-type-member_document .wp-list-table tfoot th,
.post-type-member_notice .wp-list-table thead th,
.post-type-member_notice .wp-list-table tfoot th {
  background: #f8fbff;
}
.post-type-member_video .wp-list-table tbody tr:hover,
.post-type-member_document .wp-list-table tbody tr:hover,
.post-type-member_notice .wp-list-table tbody tr:hover {
  background: #fcfdff;
}
.post-type-member_video .wp-list-table tbody td,
.post-type-member_video .wp-list-table tbody th,
.post-type-member_document .wp-list-table tbody td,
.post-type-member_document .wp-list-table tbody th,
.post-type-member_notice .wp-list-table tbody td,
.post-type-member_notice .wp-list-table tbody th {
  padding-top: 10px;
  padding-bottom: 10px;
  vertical-align: middle;
}
.post-type-member_video .column-title,
.post-type-member_document .column-title,
.post-type-member_notice .column-title {
  width: 26%;
}
.post-type-member_video .column-content_overview,
.post-type-member_document .column-content_overview,
.post-type-member_notice .column-content_overview {
  width: 36%;
}
.post-type-member_video .column-product_key,
.post-type-member_document .column-product_key,
.post-type-member_notice .column-product_key {
  width: 12%;
}
.post-type-member_video .column-schedule,
.post-type-member_document .column-schedule,
.post-type-member_notice .column-schedule {
  width: 14%;
}
.post-type-member_video .column-date,
.post-type-member_document .column-date,
.post-type-member_notice .column-date {
  width: 12%;
}
.post-type-member_video .column-title .row-title,
.post-type-member_document .column-title .row-title,
.post-type-member_notice .column-title .row-title {
  font-size: 14px;
  line-height: 1.55;
}
.rinascente-member-content-list-guide {
  border: 1px solid #cfe0f2;
  border-left: 4px solid #0068b7;
  border-radius: 14px;
  padding: 10px 14px;
  background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
  margin: 10px 0 8px;
}
.rinascente-member-content-list-guide__stats {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 6px;
}
.rinascente-member-content-list-guide__stat {
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
.rinascente-member-content-list-guide__text,
.rinascente-admin-subtext {
  color: #334155;
  font-size: 12px;
  line-height: 1.65;
}
.rinascente-member-content-list-guide__actions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}
.rinascente-member-content-list-guide__stats--compact {
  margin-top: 10px;
  margin-bottom: 0;
}
.rinascente-admin-subtext {
  margin-top: 4px;
}
.rinascente-admin-muted {
  color: #8c8f94;
}
CSS;

    wp_register_style( 'rinascente-member-content-list-admin', false, array(), null );
    wp_enqueue_style( 'rinascente-member-content-list-admin' );
    wp_add_inline_style( 'rinascente-member-content-list-admin', $styles );
}
add_action( 'admin_enqueue_scripts', 'rinascente_member_content_list_admin_assets' );

function rinascente_member_content_list_admin_footer_script() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || ! in_array( $screen->post_type, rinascente_member_content_admin_post_types(), true ) || 0 !== strpos( $screen->id, 'edit-' ) ) {
        return;
    }

    $placeholders = array(
        'member_video'    => '動画タイトル・説明文・YouTube IDで検索',
        'member_document' => '資料名・本文・制度名で検索',
        'member_notice'   => 'タイトル・本文・抜粋で検索',
    );
    $placeholder = $placeholders[ $screen->post_type ] ?? '検索';
    ?>
    <script>
    (function($){
      var $search = $('#post-search-input');
      if ($search.length) {
        $search.attr('placeholder', <?php echo wp_json_encode( $placeholder ); ?>);
      }
    })(jQuery);
    </script>
    <?php
}
add_action( 'admin_footer-edit.php', 'rinascente_member_content_list_admin_footer_script' );

function rinascente_member_admin_posts_search( $search, $query ) {
    if ( ! is_admin() || ! ( $query instanceof WP_Query ) || ! $query->is_main_query() || ! $query->is_search() ) {
        return $search;
    }

    $post_type = $query->get( 'post_type' );
    if ( ! in_array( $post_type, array( 'product_master', 'member_video', 'member_document' ), true ) ) {
        return $search;
    }

    global $wpdb;

    $search_term = trim( (string) $query->get( 's' ) );
    if ( '' === $search_term ) {
        return $search;
    }

    $like         = '%' . $wpdb->esc_like( $search_term ) . '%';
    $meta_key_map = array(
        'product_master'  => array(
            '_rinascente_product_catalog_code',
            '_rinascente_product_catalog_display_name',
            '_rinascente_product_catalog_short_name',
        ),
        'member_video'    => array(
            '_rinascente_youtube_id',
            '_rinascente_video_description',
        ),
        'member_document' => array(
            '_rinascente_program_name',
            '_rinascente_subsidy_status',
        ),
    );

    $meta_keys = $meta_key_map[ $post_type ] ?? array();
    if ( empty( $meta_keys ) ) {
        return $search;
    }

    $meta_clauses = array();
    $meta_values  = array();
    foreach ( $meta_keys as $meta_key ) {
        $meta_clauses[] = '(pm.meta_key = %s AND pm.meta_value LIKE %s)';
        $meta_values[]  = $meta_key;
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
add_filter( 'posts_search', 'rinascente_member_admin_posts_search', 10, 2 );

function rinascente_duplicate_member_document_action() {
    $source_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
    if ( ! $source_id || 'member_document' !== get_post_type( $source_id ) ) {
        wp_die( '複製元の資料が見つかりません。' );
    }

    if ( ! current_user_can( 'edit_post', $source_id ) || ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'この資料を複製する権限がありません。' );
    }

    check_admin_referer( 'rinascente_duplicate_member_document_' . $source_id );

    $source_post = get_post( $source_id );
    if ( ! $source_post instanceof WP_Post ) {
        wp_die( '複製元の資料が読み込めませんでした。' );
    }

    $new_post_id = wp_insert_post(
        array(
            'post_type'    => 'member_document',
            'post_status'  => 'draft',
            'post_title'   => sprintf( '%s（コピー）', $source_post->post_title ),
            'post_content' => $source_post->post_content,
            'post_excerpt' => $source_post->post_excerpt,
            'menu_order'   => (int) $source_post->menu_order,
        ),
        true
    );

    if ( is_wp_error( $new_post_id ) ) {
        wp_die( '資料の複製に失敗しました。' );
    }

    $all_meta = get_post_meta( $source_id );
    foreach ( $all_meta as $meta_key => $values ) {
        if ( in_array( $meta_key, array( '_edit_lock', '_edit_last' ), true ) ) {
            continue;
        }

        delete_post_meta( $new_post_id, $meta_key );
        foreach ( $values as $value ) {
            add_post_meta( $new_post_id, $meta_key, maybe_unserialize( $value ) );
        }
    }

    wp_safe_redirect(
        add_query_arg(
            array(
                'post'                                 => $new_post_id,
                'action'                               => 'edit',
                'rinascente_member_document_duplicated' => $source_id,
            ),
            admin_url( 'post.php' )
        )
    );
    exit;
}
add_action( 'admin_action_rinascente_duplicate_member_document', 'rinascente_duplicate_member_document_action' );

function rinascente_member_document_duplicate_admin_notice() {
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( ! $screen || 'member_document' !== $screen->post_type || 'post' !== $screen->base || empty( $_GET['rinascente_member_document_duplicated'] ) ) {
        return;
    }
    ?>
    <div class="notice notice-success is-dismissible">
        <p>資料を複製して下書きを作成しました。ファイル、更新日、公開期間を見直してから保存してください。</p>
    </div>
    <?php
}
add_action( 'admin_notices', 'rinascente_member_document_duplicate_admin_notice' );

function rinascente_member_create_page_slug() {
    return 'rinascente-member-create';
}

function rinascente_member_create_page_url( $args = array() ) {
    $url = add_query_arg(
        'page',
        rinascente_member_create_page_slug(),
        admin_url( 'users.php' )
    );

    if ( ! empty( $args ) ) {
        $url = add_query_arg( $args, $url );
    }

    return $url;
}

function rinascente_member_create_form_state_key( $user_id = 0 ) {
    $user_id = $user_id ? (int) $user_id : get_current_user_id();
    return 'rinascente_member_create_state_' . $user_id;
}

function rinascente_register_member_admin_pages() {
    add_submenu_page(
        function_exists( 'rinascente_member_admin_menu_slug' ) ? rinascente_member_admin_menu_slug() : 'users.php',
        '施設会員を追加',
        '施設会員を追加',
        'create_users',
        rinascente_member_create_page_slug(),
        'rinascente_render_member_create_page'
    );
}
add_action( 'admin_menu', 'rinascente_register_member_admin_pages' );

function rinascente_create_facility_member( $args ) {
    $facility_name = sanitize_text_field( $args['facility_name'] ?? '' );
    $user_login    = sanitize_user( $args['user_login'] ?? '', true );
    $user_email    = sanitize_email( $args['user_email'] ?? '' );
    $user_password = (string) ( $args['user_password'] ?? '' );

    if ( '' === $facility_name ) {
        return new WP_Error( 'missing_facility_name', '施設名・会社名を入力してください。' );
    }

    if ( '' === $user_login ) {
        return new WP_Error( 'missing_login', 'IDを入力してください。' );
    }

    if ( ! validate_username( $user_login ) ) {
        return new WP_Error( 'invalid_login', 'IDに使用できない文字が含まれています。半角英数字・記号で入力してください。' );
    }

    if ( username_exists( $user_login ) ) {
        return new WP_Error( 'login_exists', 'そのIDはすでに使用されています。' );
    }

    if ( '' === $user_email || ! is_email( $user_email ) ) {
        return new WP_Error( 'invalid_email', '有効なメールアドレスを入力してください。' );
    }

    if ( email_exists( $user_email ) ) {
        return new WP_Error( 'email_exists', 'そのメールアドレスはすでに使用されています。' );
    }

    if ( '' === trim( $user_password ) ) {
        return new WP_Error( 'missing_password', 'PASSWORDを入力してください。' );
    }

    $user_id = wp_insert_user(
        array(
            'user_login'   => $user_login,
            'user_pass'    => $user_password,
            'user_email'   => $user_email,
            'display_name' => $facility_name,
            'nickname'     => $facility_name,
            'role'         => 'facility_member',
        )
    );

    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    update_user_meta( $user_id, '_rinascente_member_facility_name', $facility_name );
    update_user_meta( $user_id, '_rinascente_member_facility_type', '' );
    update_user_meta( $user_id, '_rinascente_member_products', array() );

    return (int) $user_id;
}

function rinascente_handle_member_create_admin_post() {
    if ( ! current_user_can( 'create_users' ) ) {
        wp_die( 'この操作を実行する権限がありません。' );
    }

    check_admin_referer( 'rinascente_member_create_action', 'rinascente_member_create_nonce' );

    $form_values = array(
        'facility_name' => sanitize_text_field( wp_unslash( $_POST['rinascente_member_facility_name'] ?? '' ) ),
        'user_login'    => sanitize_user( wp_unslash( $_POST['rinascente_member_login'] ?? '' ), true ),
        'user_email'    => sanitize_email( wp_unslash( $_POST['rinascente_member_email'] ?? '' ) ),
    );
    $user_password = (string) wp_unslash( $_POST['rinascente_member_password'] ?? '' );

    $created_user = rinascente_create_facility_member(
        array(
            'facility_name' => $form_values['facility_name'],
            'user_login'    => $form_values['user_login'],
            'user_email'    => $form_values['user_email'],
            'user_password' => $user_password,
        )
    );

    if ( is_wp_error( $created_user ) ) {
        set_transient(
            rinascente_member_create_form_state_key( get_current_user_id() ),
            array(
                'form_values'   => $form_values,
                'error_message' => $created_user->get_error_message(),
            ),
            5 * MINUTE_IN_SECONDS
        );

        wp_safe_redirect(
            rinascente_member_create_page_url(
                array(
                    'member_create_status' => 'error',
                )
            )
        );
        exit;
    }

    delete_transient( rinascente_member_create_form_state_key( get_current_user_id() ) );

    wp_safe_redirect(
        rinascente_member_create_page_url(
            array(
                'member_create_status' => 'success',
                'created_user'         => $created_user,
            )
        )
    );
    exit;
}
add_action( 'admin_post_rinascente_create_facility_member', 'rinascente_handle_member_create_admin_post' );

function rinascente_render_member_create_page() {
    if ( ! current_user_can( 'create_users' ) ) {
        wp_die( 'このページにアクセスする権限がありません。' );
    }

    $form_values = array(
        'facility_name' => '',
        'user_login'    => '',
        'user_email'    => '',
    );
    $error_message = '';
    $form_state    = get_transient( rinascente_member_create_form_state_key( get_current_user_id() ) );
    if ( is_array( $form_state ) ) {
        $form_values   = array_merge( $form_values, $form_state['form_values'] ?? array() );
        $error_message = (string) ( $form_state['error_message'] ?? '' );
        delete_transient( rinascente_member_create_form_state_key( get_current_user_id() ) );
    }

    $created_user_id = isset( $_GET['created_user'] ) ? absint( wp_unslash( $_GET['created_user'] ) ) : 0;
    $created_user    = $created_user_id ? get_user_by( 'id', $created_user_id ) : false;
    ?>
    <div class="wrap">
        <h1>施設会員を追加</h1>
        <p>施設名・会社名、ID、PASSWORD、メールアドレスの4項目だけで会員を追加できます。契約製品は後から「契約・購入履歴」またはユーザー編集画面で設定できます。</p>

        <?php if ( $created_user instanceof WP_User ) : ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    会員を追加しました。
                    ID: <code><?php echo esc_html( $created_user->user_login ); ?></code>
                    /
                    メール: <code><?php echo esc_html( $created_user->user_email ); ?></code>
                </p>
                <p>
                    <a href="<?php echo esc_url( get_edit_user_link( $created_user->ID ) ); ?>">この会員を編集</a>
                </p>
            </div>
        <?php endif; ?>

        <?php if ( '' !== $error_message ) : ?>
            <div class="notice notice-error">
                <p><?php echo esc_html( $error_message ); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'rinascente_member_create_action', 'rinascente_member_create_nonce' ); ?>
            <input type="hidden" name="action" value="rinascente_create_facility_member">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="rinascente_member_facility_name">施設名・会社名</label></th>
                        <td><input name="rinascente_member_facility_name" type="text" id="rinascente_member_facility_name" value="<?php echo esc_attr( $form_values['facility_name'] ); ?>" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="rinascente_member_login">ID</label></th>
                        <td><input name="rinascente_member_login" type="text" id="rinascente_member_login" value="<?php echo esc_attr( $form_values['user_login'] ); ?>" class="regular-text" autocomplete="off" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="rinascente_member_password">PASSWORD</label></th>
                        <td><input name="rinascente_member_password" type="text" id="rinascente_member_password" value="" class="regular-text" autocomplete="new-password" required></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="rinascente_member_email">メールアドレス</label></th>
                        <td><input name="rinascente_member_email" type="email" id="rinascente_member_email" value="<?php echo esc_attr( $form_values['user_email'] ); ?>" class="regular-text" required></td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button( '施設会員を追加' ); ?>
        </form>
    </div>
    <?php
}

function rinascente_member_profile_fields( $user ) {
    $facility_name = get_user_meta( $user->ID, '_rinascente_member_facility_name', true );
    $facility_type = get_user_meta( $user->ID, '_rinascente_member_facility_type', true );
    $products      = (array) get_user_meta( $user->ID, '_rinascente_member_products', true );
    ?>
    <h2>会員ダッシュボード設定</h2>
    <table class="form-table">
        <tr>
            <th><label for="rinascente_member_facility_name">施設名</label></th>
            <td><input type="text" name="rinascente_member_facility_name" id="rinascente_member_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="rinascente_member_facility_type">施設種別</label></th>
            <td><input type="text" name="rinascente_member_facility_type" id="rinascente_member_facility_type" value="<?php echo esc_attr( $facility_type ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>契約製品</th>
            <td>
                <?php foreach ( rinascente_member_product_choices() as $value => $label ) : ?>
                    <label style="display:inline-block;margin-right:16px;">
                        <input type="checkbox" name="rinascente_member_products[]" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $products, true ) ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'rinascente_member_profile_fields' );
add_action( 'edit_user_profile', 'rinascente_member_profile_fields' );

function rinascente_save_member_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }

    update_user_meta( $user_id, '_rinascente_member_facility_name', isset( $_POST['rinascente_member_facility_name'] ) ? sanitize_text_field( wp_unslash( $_POST['rinascente_member_facility_name'] ) ) : '' );
    update_user_meta( $user_id, '_rinascente_member_facility_type', isset( $_POST['rinascente_member_facility_type'] ) ? sanitize_text_field( wp_unslash( $_POST['rinascente_member_facility_type'] ) ) : '' );
    $products = isset( $_POST['rinascente_member_products'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['rinascente_member_products'] ) ) : array();
    update_user_meta( $user_id, '_rinascente_member_products', array_values( array_intersect( $products, array_keys( rinascente_member_product_choices() ) ) ) );
}
add_action( 'personal_options_update', 'rinascente_save_member_profile_fields' );
add_action( 'edit_user_profile_update', 'rinascente_save_member_profile_fields' );

function rinascente_member_document_download() {
    $document_id = isset( $_GET['document_id'] ) ? absint( wp_unslash( $_GET['document_id'] ) ) : 0;
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( rinascente_member_login_url( rinascente_member_page_url() ) );
        exit;
    }

    $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
    if ( ! $document_id || ! wp_verify_nonce( $nonce, rinascente_member_document_download_nonce_action( $document_id ) ) ) {
        wp_die( '資料にアクセスできません。', '403 Forbidden', array( 'response' => 403 ) );
    }

    if (
        'member_document' !== get_post_type( $document_id ) ||
        'publish' !== get_post_status( $document_id ) ||
        ! rinascente_member_post_is_active( $document_id ) ||
        ! rinascente_member_has_product_access( get_current_user_id(), $document_id )
    ) {
        wp_die( '資料にアクセスできません。', '403 Forbidden', array( 'response' => 403 ) );
    }

    $file = rinascente_member_document_file_data( $document_id );
    if ( empty( $file['path'] ) || ! file_exists( $file['path'] ) ) {
        wp_die( 'ファイルが見つかりません。', '404 Not Found', array( 'response' => 404 ) );
    }

    nocache_headers();
    header( 'Content-Description: File Transfer' );
    header( 'Content-Type: ' . ( $file['mime'] ?: 'application/octet-stream' ) );
    header( 'Content-Disposition: attachment; filename="' . rawurlencode( $file['filename'] ) . '"' );
    header( 'Content-Length: ' . filesize( $file['path'] ) );
    readfile( $file['path'] );
    exit;
}
add_action( 'admin_post_rinascente_member_document_download', 'rinascente_member_document_download' );
add_action(
    'admin_post_nopriv_rinascente_member_document_download',
    static function () {
        wp_safe_redirect( rinascente_member_login_url( rinascente_member_page_url() ) );
        exit;
    }
);

function rinascente_member_review_submit() {
    if ( ! rinascente_member_reviews_enabled() ) {
        wp_safe_redirect( rinascente_member_page_url() );
        exit;
    }

    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( rinascente_member_login_url( rinascente_member_page_url() ) );
        exit;
    }

    if ( ! isset( $_POST['rinascente_member_review_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['rinascente_member_review_nonce'] ), 'rinascente_member_review_submit' ) ) {
        wp_safe_redirect( add_query_arg( 'review_status', 'error', rinascente_member_page_url() . '#reviews' ) );
        exit;
    }

    $current_user   = wp_get_current_user();
    $product_key    = isset( $_POST['review_product'] ) ? sanitize_key( wp_unslash( $_POST['review_product'] ) ) : '';
    $facility_name  = isset( $_POST['review_facility_name'] ) ? sanitize_text_field( wp_unslash( $_POST['review_facility_name'] ) ) : '';
    $facility_type  = isset( $_POST['review_facility_type'] ) ? sanitize_text_field( wp_unslash( $_POST['review_facility_type'] ) ) : '';
    $adoption       = isset( $_POST['review_adoption_period'] ) ? sanitize_key( wp_unslash( $_POST['review_adoption_period'] ) ) : '';
    $rating         = isset( $_POST['review_rating'] ) ? absint( wp_unslash( $_POST['review_rating'] ) ) : 0;
    $body           = isset( $_POST['review_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_body'] ) ) : '';
    $tags           = isset( $_POST['review_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['review_tags'] ) ) : '';
    $author_name    = rinascente_member_user_name( $current_user );
    $product_choices = rinascente_member_product_choices();
    $allowed_products = rinascente_member_get_user_products( $current_user->ID );

    if (
        ! isset( $product_choices[ $product_key ] ) ||
        ! in_array( $product_key, $allowed_products, true ) ||
        $rating < 1 ||
        $rating > 5 ||
        '' === $body ||
        '' === $facility_name
    ) {
        wp_safe_redirect( add_query_arg( 'review_status', 'error', rinascente_member_page_url() . '#reviews' ) );
        exit;
    }

    update_user_meta( $current_user->ID, '_rinascente_member_facility_name', $facility_name );
    update_user_meta( $current_user->ID, '_rinascente_member_facility_type', $facility_type );

    $post_id = wp_insert_post(
        array(
            'post_type'    => 'member_review',
            'post_status'  => 'pending',
            'post_title'   => sprintf( '%s / %s / %s', $facility_name, $product_choices[ $product_key ], wp_date( 'Y.m.d' ) ),
            'post_content' => $body,
            'post_author'  => $current_user->ID,
        ),
        true
    );

    if ( is_wp_error( $post_id ) ) {
        wp_safe_redirect( add_query_arg( 'review_status', 'error', rinascente_member_page_url() . '#reviews' ) );
        exit;
    }

    update_post_meta( $post_id, '_rinascente_member_user_id', $current_user->ID );
    update_post_meta( $post_id, '_rinascente_author_name', $author_name );
    update_post_meta( $post_id, '_rinascente_review_facility_name', $facility_name );
    update_post_meta( $post_id, '_rinascente_review_facility_type', $facility_type );
    update_post_meta( $post_id, '_rinascente_adoption_period', $adoption );
    update_post_meta( $post_id, '_rinascente_review_rating', $rating );
    update_post_meta( $post_id, '_rinascente_review_tags', $tags );
    update_post_meta( $post_id, '_rinascente_helpful_count', 0 );
    update_post_meta( $post_id, '_rinascente_product_key', $product_key );
    wp_set_object_terms( $post_id, $product_key, 'product_type', false );

    wp_safe_redirect( add_query_arg( 'review_status', 'success', rinascente_member_page_url() . '#reviews' ) );
    exit;
}

if ( rinascente_member_reviews_enabled() ) {
    add_action( 'admin_post_rinascente_member_review_submit', 'rinascente_member_review_submit' );
    add_action(
        'admin_post_nopriv_rinascente_member_review_submit',
        static function () {
            wp_safe_redirect( rinascente_member_login_url( rinascente_member_page_url() ) );
            exit;
        }
    );
}
