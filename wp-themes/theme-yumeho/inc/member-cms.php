<?php
/**
 * Member CMS helpers for YUMEHO.
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yumeho_member_product_choices() {
    $choices = array(
        'yumeho' => 'YUMEHO',
    );

    if ( function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled() ) {
        $choices['mica30'] = 'MICA30';
    }

    return $choices;
}

function yumeho_contract_status_choices() {
    return array(
        'ordered'   => '受注済み',
        'delivered' => '納品済み',
        'scheduled' => '納品予定',
        'support'   => '保守予定',
        'cancelled' => 'キャンセル',
    );
}

function yumeho_contract_payment_choices() {
    return array(
        'pending'  => '未決済',
        'paid'     => '支払済み',
        'partial'  => '一部入金',
        'refunded' => '返金済み',
    );
}

function yumeho_member_video_category_choices() {
    return array(
        'setup'   => '設置方法',
        'usage'   => '利用方法',
        'seminar' => 'セミナー',
        'support' => 'サポート',
    );
}

function yumeho_member_document_category_choices() {
    return array(
        'spec'     => '仕様書',
        'guide'    => '運用ガイド',
        'cost'     => 'コスト計算',
        'proposal' => '稟議資料',
        'manual'   => '取扱説明書',
        'subsidy'  => '補助金サポート',
    );
}

function yumeho_member_review_period_choices() {
    return array(
        '1_month'  => '1ヶ月以内',
        '3_months' => '1〜3ヶ月',
        '6_months' => '3〜6ヶ月',
        '1_year'   => '6ヶ月〜1年',
        'over_1y'  => '1年以上',
    );
}

function yumeho_member_star_string( $rating ) {
    $rating = max( 1, min( 5, (int) $rating ) );
    return str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
}

function yumeho_member_format_date( $value ) {
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

function yumeho_member_format_file_size( $bytes ) {
    $bytes = (int) $bytes;
    if ( $bytes <= 0 ) {
        return '';
    }

    $units = array( 'B', 'KB', 'MB', 'GB' );
    $power = min( (int) floor( log( $bytes, 1024 ) ), count( $units ) - 1 );
    $size  = $bytes / pow( 1024, $power );

    return sprintf( '%s%s', number_format_i18n( $size, $power > 0 ? 1 : 0 ), $units[ $power ] );
}

function yumeho_member_get_users() {
    return get_users(
        array(
            'role__in' => array( 'facility_member', 'administrator' ),
            'orderby'  => 'display_name',
            'order'    => 'ASC',
        )
    );
}

function yumeho_member_get_user_products( $user_id ) {
    $raw_products = get_user_meta( $user_id, '_yumeho_member_products', true );
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
                    'key'   => '_yumeho_member_user_id',
                    'value' => (string) $user_id,
                ),
            ),
        )
    );

    foreach ( $contract_ids as $contract_id ) {
        $product_key = get_post_meta( $contract_id, '_yumeho_product_key', true );
        if ( $product_key ) {
            $products[] = $product_key;
        }

        $terms = wp_get_post_terms( $contract_id, 'product_type', array( 'fields' => 'slugs' ) );
        if ( ! is_wp_error( $terms ) ) {
            $products = array_merge( $products, $terms );
        }
    }

    $valid_products = array_keys( yumeho_member_product_choices() );
    return array_values( array_intersect( array_unique( array_filter( $products ) ), $valid_products ) );
}

function yumeho_member_get_post_products( $post_id ) {
    $products = array();

    $meta_product = get_post_meta( $post_id, '_yumeho_product_key', true );
    if ( $meta_product ) {
        $products[] = $meta_product;
    }

    $terms = wp_get_post_terms( $post_id, 'product_type', array( 'fields' => 'slugs' ) );
    if ( ! is_wp_error( $terms ) ) {
        $products = array_merge( $products, $terms );
    }

    $valid_products = array_keys( yumeho_member_product_choices() );
    return array_values( array_intersect( array_unique( array_filter( $products ) ), $valid_products ) );
}

function yumeho_member_has_product_access( $user_id, $post_id ) {
    $post_products = yumeho_member_get_post_products( $post_id );
    if ( empty( $post_products ) ) {
        return true;
    }

    $member_products = yumeho_member_get_user_products( $user_id );
    if ( empty( $member_products ) ) {
        return false;
    }

    return (bool) array_intersect( $member_products, $post_products );
}

function yumeho_member_post_is_active( $post_id ) {
    $start_date = get_post_meta( $post_id, '_yumeho_start_date', true );
    $end_date   = get_post_meta( $post_id, '_yumeho_end_date', true );
    $today      = wp_date( 'Y-m-d' );

    if ( $start_date && $today < $start_date ) {
        return false;
    }

    if ( $end_date && $today > $end_date ) {
        return false;
    }

    return true;
}

function yumeho_member_document_file_data( $post_id ) {
    $attachment_id = absint( get_post_meta( $post_id, '_yumeho_attachment_id', true ) );
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
    $file_size = $file_path && file_exists( $file_path ) ? yumeho_member_format_file_size( filesize( $file_path ) ) : '';

    return array(
        'attachment_id' => $attachment_id,
        'path'          => $file_path,
        'mime'          => get_post_mime_type( $attachment_id ),
        'size'          => $file_size,
        'filename'      => wp_basename( (string) get_attached_file( $attachment_id ) ),
    );
}

function yumeho_member_document_download_url( $post_id ) {
    return add_query_arg(
        array(
            'action'      => 'yumeho_member_document_download',
            'document_id' => $post_id,
            '_wpnonce'    => wp_create_nonce( yumeho_member_document_download_nonce_action( $post_id ) ),
        ),
        admin_url( 'admin-post.php' )
    );
}

function yumeho_member_document_download_nonce_action( $post_id ) {
    return 'yumeho_member_document_download_' . absint( $post_id );
}

function yumeho_member_review_submit_url() {
    return admin_url( 'admin-post.php' );
}

function yumeho_member_review_notice() {
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

function yumeho_member_support_info() {
    return array(
        'company_name' => yumeho_theme_mod( 'support_name', yumeho_theme_mod( 'company_name', '株式会社Rinascente' ) ),
        'telephone'    => yumeho_theme_mod( 'support_tel', yumeho_theme_mod( 'company_tel', '' ) ),
        'email'        => '',
        'hours'        => yumeho_theme_mod( 'support_hours', yumeho_theme_mod( 'company_hours', '' ) ),
    );
}

function yumeho_member_get_contracts( $user_id ) {
    return get_posts(
        array(
            'post_type'      => 'contract',
            'post_status'    => array( 'publish', 'private' ),
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_yumeho_order_date',
            'order'          => 'DESC',
            'meta_query'     => array(
                array(
                    'key'   => '_yumeho_member_user_id',
                    'value' => (string) $user_id,
                ),
            ),
        )
    );
}

function yumeho_member_get_videos( $user_id, $category = '' ) {
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
                $current_category = get_post_meta( $post->ID, '_yumeho_video_category', true );
                if ( $category && $current_category !== $category ) {
                    return false;
                }

                return yumeho_member_post_is_active( $post->ID ) && yumeho_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function yumeho_member_get_documents( $user_id, $category = '' ) {
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
                $current_category = get_post_meta( $post->ID, '_yumeho_document_category', true );
                if ( $category && $current_category !== $category ) {
                    return false;
                }

                return yumeho_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function yumeho_member_get_reviews( $user_id ) {
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
                return yumeho_member_has_product_access( $user_id, $post->ID );
            }
        )
    );
}

function yumeho_member_review_summary( $posts ) {
    $summary = array();
    foreach ( yumeho_member_product_choices() as $product_key => $label ) {
        $summary[ $product_key ] = array(
            'label'         => $label,
            'count'         => 0,
            'total_rating'  => 0,
            'average'       => '0.0',
            'distribution'  => array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 ),
        );
    }

    foreach ( $posts as $post ) {
        $products = yumeho_member_get_post_products( $post->ID );
        if ( empty( $products ) ) {
            $products = array_keys( yumeho_member_product_choices() );
        }

        $rating = max( 1, min( 5, (int) get_post_meta( $post->ID, '_yumeho_review_rating', true ) ) );
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

function yumeho_register_member_content_post_types() {
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
            'public'              => true,
            'publicly_queryable'  => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-clipboard',
            'supports'            => array( 'title' ),
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
            'public'              => true,
            'publicly_queryable'  => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-video-alt3',
            'supports'            => array( 'title', 'editor', 'page-attributes' ),
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
            'public'              => true,
            'publicly_queryable'  => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-media-document',
            'supports'            => array( 'title', 'editor', 'page-attributes' ),
        )
    );

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
            'public'              => true,
            'publicly_queryable'  => false,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-star-filled',
            'supports'            => array( 'title', 'editor', 'author' ),
        )
    );
}
add_action( 'init', 'yumeho_register_member_content_post_types', 20 );

function yumeho_register_member_content_taxonomies() {
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

    foreach ( array( 'contract', 'member_video', 'member_document', 'member_review' ) as $post_type ) {
        register_taxonomy_for_object_type( 'product_type', $post_type );
    }
}
add_action( 'init', 'yumeho_register_member_content_taxonomies', 30 );

function yumeho_seed_member_terms() {
    foreach ( yumeho_member_product_choices() as $slug => $label ) {
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
add_action( 'init', 'yumeho_seed_member_terms', 40 );

function yumeho_member_content_meta_boxes() {
    add_meta_box( 'yumeho_contract_details', '契約・購入履歴', 'yumeho_contract_meta_box_html', 'contract', 'normal', 'high' );
    add_meta_box( 'yumeho_member_video_details', '動画詳細', 'yumeho_member_video_meta_box_html', 'member_video', 'normal', 'high' );
    add_meta_box( 'yumeho_member_document_details', '資料詳細', 'yumeho_member_document_meta_box_html', 'member_document', 'normal', 'high' );
    add_meta_box( 'yumeho_member_review_details', 'レビュー詳細', 'yumeho_member_review_meta_box_html', 'member_review', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'yumeho_member_content_meta_boxes' );

function yumeho_member_nonce_field() {
    wp_nonce_field( 'yumeho_member_content_save', 'yumeho_member_content_nonce' );
}

function yumeho_contract_meta_box_html( $post ) {
    yumeho_member_nonce_field();
    $users           = yumeho_member_get_users();
    $selected_user   = (int) get_post_meta( $post->ID, '_yumeho_member_user_id', true );
    $facility_name   = get_post_meta( $post->ID, '_yumeho_facility_name', true );
    $order_number    = get_post_meta( $post->ID, '_yumeho_order_number', true );
    $product_name    = get_post_meta( $post->ID, '_yumeho_product_name', true );
    $product_key     = get_post_meta( $post->ID, '_yumeho_product_key', true );
    $quantity        = get_post_meta( $post->ID, '_yumeho_quantity', true );
    $order_date      = get_post_meta( $post->ID, '_yumeho_order_date', true );
    $delivery_date   = get_post_meta( $post->ID, '_yumeho_delivery_date', true );
    $contract_date   = get_post_meta( $post->ID, '_yumeho_contract_date', true );
    $status          = get_post_meta( $post->ID, '_yumeho_contract_status', true );
    $payment_status  = get_post_meta( $post->ID, '_yumeho_payment_status', true );
    $contract_info   = get_post_meta( $post->ID, '_yumeho_contract_info', true );
    $notes           = get_post_meta( $post->ID, '_yumeho_contract_notes', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="yumeho_member_user_id">会員ユーザー</label></th>
            <td>
                <select name="yumeho_member_user_id" id="yumeho_member_user_id" class="regular-text">
                    <option value="">選択してください</option>
                    <?php foreach ( $users as $user ) : ?>
                        <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $selected_user, $user->ID ); ?>><?php echo esc_html( yumeho_member_user_name( $user ) . ' (' . $user->user_login . ')' ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_facility_name">施設名</label></th><td><input type="text" name="yumeho_facility_name" id="yumeho_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_order_number">注文番号</label></th><td><input type="text" name="yumeho_order_number" id="yumeho_order_number" value="<?php echo esc_attr( $order_number ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_product_name">製品名</label></th><td><input type="text" name="yumeho_product_name" id="yumeho_product_name" value="<?php echo esc_attr( $product_name ); ?>" class="regular-text"></td></tr>
        <tr>
            <th><label for="yumeho_product_key">対象製品</label></th>
            <td>
                <select name="yumeho_product_key" id="yumeho_product_key">
                    <option value="">共通</option>
                    <?php foreach ( yumeho_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_quantity">数量</label></th><td><input type="text" name="yumeho_quantity" id="yumeho_quantity" value="<?php echo esc_attr( $quantity ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_order_date">注文日</label></th><td><input type="date" name="yumeho_order_date" id="yumeho_order_date" value="<?php echo esc_attr( $order_date ); ?>"></td></tr>
        <tr><th><label for="yumeho_delivery_date">納品日</label></th><td><input type="date" name="yumeho_delivery_date" id="yumeho_delivery_date" value="<?php echo esc_attr( $delivery_date ); ?>"></td></tr>
        <tr><th><label for="yumeho_contract_date">契約日</label></th><td><input type="date" name="yumeho_contract_date" id="yumeho_contract_date" value="<?php echo esc_attr( $contract_date ); ?>"></td></tr>
        <tr>
            <th><label for="yumeho_contract_status">ステータス</label></th>
            <td>
                <select name="yumeho_contract_status" id="yumeho_contract_status">
                    <?php foreach ( yumeho_contract_status_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="yumeho_payment_status">支払いステータス</label></th>
            <td>
                <select name="yumeho_payment_status" id="yumeho_payment_status">
                    <?php foreach ( yumeho_contract_payment_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $payment_status, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_contract_info">契約情報</label></th><td><textarea name="yumeho_contract_info" id="yumeho_contract_info" rows="4" class="large-text"><?php echo esc_textarea( $contract_info ); ?></textarea></td></tr>
        <tr><th><label for="yumeho_contract_notes">備考</label></th><td><textarea name="yumeho_contract_notes" id="yumeho_contract_notes" rows="3" class="large-text"><?php echo esc_textarea( $notes ); ?></textarea></td></tr>
    </table>
    <?php
}

function yumeho_member_video_meta_box_html( $post ) {
    yumeho_member_nonce_field();
    $youtube_id    = get_post_meta( $post->ID, '_yumeho_youtube_id', true );
    $description   = get_post_meta( $post->ID, '_yumeho_video_description', true );
    $category      = get_post_meta( $post->ID, '_yumeho_video_category', true );
    $product_key   = get_post_meta( $post->ID, '_yumeho_product_key', true );
    $start_date    = get_post_meta( $post->ID, '_yumeho_start_date', true );
    $end_date      = get_post_meta( $post->ID, '_yumeho_end_date', true );
    ?>
    <table class="form-table">
        <tr><th><label for="yumeho_youtube_id">YouTube動画ID</label></th><td><input type="text" name="yumeho_youtube_id" id="yumeho_youtube_id" value="<?php echo esc_attr( $youtube_id ); ?>" class="regular-text" placeholder="M7lc1UVf-VE"></td></tr>
        <tr><th><label for="yumeho_video_description">説明文</label></th><td><textarea name="yumeho_video_description" id="yumeho_video_description" rows="4" class="large-text"><?php echo esc_textarea( $description ); ?></textarea></td></tr>
        <tr>
            <th><label for="yumeho_video_category">カテゴリ</label></th>
            <td>
                <select name="yumeho_video_category" id="yumeho_video_category">
                    <?php foreach ( yumeho_member_video_category_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="yumeho_video_product_key">対象製品</label></th>
            <td>
                <select name="yumeho_product_key" id="yumeho_video_product_key">
                    <option value="">共通</option>
                    <?php foreach ( yumeho_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_start_date">公開開始日</label></th><td><input type="date" name="yumeho_start_date" id="yumeho_start_date" value="<?php echo esc_attr( $start_date ); ?>"></td></tr>
        <tr><th><label for="yumeho_end_date">公開終了日</label></th><td><input type="date" name="yumeho_end_date" id="yumeho_end_date" value="<?php echo esc_attr( $end_date ); ?>"></td></tr>
    </table>
    <?php
}

function yumeho_member_document_meta_box_html( $post ) {
    yumeho_member_nonce_field();
    $attachment_id = absint( get_post_meta( $post->ID, '_yumeho_attachment_id', true ) );
    $category      = get_post_meta( $post->ID, '_yumeho_document_category', true );
    $product_key   = get_post_meta( $post->ID, '_yumeho_product_key', true );
    $updated_date  = get_post_meta( $post->ID, '_yumeho_document_updated_date', true );
    $program_name  = get_post_meta( $post->ID, '_yumeho_program_name', true );
    $subsidy_state = get_post_meta( $post->ID, '_yumeho_subsidy_status', true );
    $attachment    = $attachment_id ? get_post( $attachment_id ) : null;
    ?>
    <table class="form-table">
        <tr>
            <th><label for="yumeho_attachment_id">ファイル</label></th>
            <td>
                <input type="hidden" name="yumeho_attachment_id" id="yumeho_attachment_id" value="<?php echo esc_attr( $attachment_id ); ?>">
                <button type="button" class="button yumeho-media-select">メディアを選択</button>
                <button type="button" class="button-link-delete yumeho-media-clear" <?php disabled( ! $attachment_id ); ?>>解除</button>
                <p id="yumeho_attachment_preview" style="margin-top:8px;"><?php echo $attachment ? esc_html( $attachment->post_title ) : '未選択'; ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="yumeho_document_category">カテゴリ</label></th>
            <td>
                <select name="yumeho_document_category" id="yumeho_document_category">
                    <?php foreach ( yumeho_member_document_category_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $category, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="yumeho_document_product_key">対象製品</label></th>
            <td>
                <select name="yumeho_product_key" id="yumeho_document_product_key">
                    <option value="">共通</option>
                    <?php foreach ( yumeho_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_document_updated_date">更新日</label></th><td><input type="date" name="yumeho_document_updated_date" id="yumeho_document_updated_date" value="<?php echo esc_attr( $updated_date ); ?>"></td></tr>
        <tr><th><label for="yumeho_program_name">制度名</label></th><td><input type="text" name="yumeho_program_name" id="yumeho_program_name" value="<?php echo esc_attr( $program_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_subsidy_status">認定ステータス</label></th><td><input type="text" name="yumeho_subsidy_status" id="yumeho_subsidy_status" value="<?php echo esc_attr( $subsidy_state ); ?>" class="regular-text" placeholder="対象 / 非対象 / 認定済み"></td></tr>
    </table>
    <?php
}

function yumeho_member_review_meta_box_html( $post ) {
    yumeho_member_nonce_field();
    $users          = yumeho_member_get_users();
    $selected_user  = (int) get_post_meta( $post->ID, '_yumeho_member_user_id', true );
    $author_name    = get_post_meta( $post->ID, '_yumeho_author_name', true );
    $facility_name  = get_post_meta( $post->ID, '_yumeho_review_facility_name', true );
    $facility_type  = get_post_meta( $post->ID, '_yumeho_review_facility_type', true );
    $adoption       = get_post_meta( $post->ID, '_yumeho_adoption_period', true );
    $rating         = (int) get_post_meta( $post->ID, '_yumeho_review_rating', true );
    $tags           = get_post_meta( $post->ID, '_yumeho_review_tags', true );
    $helpful_count  = (int) get_post_meta( $post->ID, '_yumeho_helpful_count', true );
    $product_key    = get_post_meta( $post->ID, '_yumeho_product_key', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="yumeho_review_member_user_id">会員ユーザー</label></th>
            <td>
                <select name="yumeho_member_user_id" id="yumeho_review_member_user_id" class="regular-text">
                    <option value="">未紐付け</option>
                    <?php foreach ( $users as $user ) : ?>
                        <option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $selected_user, $user->ID ); ?>><?php echo esc_html( yumeho_member_user_name( $user ) . ' (' . $user->user_login . ')' ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_author_name">記入者名</label></th><td><input type="text" name="yumeho_author_name" id="yumeho_author_name" value="<?php echo esc_attr( $author_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_review_facility_name">施設名</label></th><td><input type="text" name="yumeho_review_facility_name" id="yumeho_review_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text"></td></tr>
        <tr><th><label for="yumeho_review_facility_type">施設種別</label></th><td><input type="text" name="yumeho_review_facility_type" id="yumeho_review_facility_type" value="<?php echo esc_attr( $facility_type ); ?>" class="regular-text"></td></tr>
        <tr>
            <th><label for="yumeho_adoption_period">導入時期</label></th>
            <td>
                <select name="yumeho_adoption_period" id="yumeho_adoption_period">
                    <?php foreach ( yumeho_member_review_period_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $adoption, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="yumeho_review_rating">評価</label></th>
            <td>
                <select name="yumeho_review_rating" id="yumeho_review_rating">
                    <?php for ( $i = 5; $i >= 1; --$i ) : ?>
                        <option value="<?php echo esc_attr( $i ); ?>" <?php selected( $rating, $i ); ?>><?php echo esc_html( $i . ' / 5' ); ?></option>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr><th><label for="yumeho_review_tags">タグ</label></th><td><input type="text" name="yumeho_review_tags" id="yumeho_review_tags" value="<?php echo esc_attr( $tags ); ?>" class="regular-text" placeholder="カンマ区切り"></td></tr>
        <tr><th><label for="yumeho_helpful_count">参考になった数</label></th><td><input type="number" min="0" name="yumeho_helpful_count" id="yumeho_helpful_count" value="<?php echo esc_attr( $helpful_count ); ?>"></td></tr>
        <tr>
            <th><label for="yumeho_review_product_key">対象製品</label></th>
            <td>
                <select name="yumeho_product_key" id="yumeho_review_product_key">
                    <option value="">共通</option>
                    <?php foreach ( yumeho_member_product_choices() as $value => $label ) : ?>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $product_key, $value ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

function yumeho_member_save_content_meta( $post_id, $post ) {
    if ( ! isset( $_POST['yumeho_member_content_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['yumeho_member_content_nonce'] ), 'yumeho_member_content_save' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! in_array( $post->post_type, array( 'contract', 'member_video', 'member_document', 'member_review' ), true ) ) {
        return;
    }

    $map = array(
        'member_user_id'        => 'absint',
        'facility_name'         => 'sanitize_text_field',
        'order_number'          => 'sanitize_text_field',
        'product_name'          => 'sanitize_text_field',
        'product_key'           => 'sanitize_key',
        'quantity'              => 'sanitize_text_field',
        'order_date'            => 'sanitize_text_field',
        'delivery_date'         => 'sanitize_text_field',
        'contract_date'         => 'sanitize_text_field',
        'contract_status'       => 'sanitize_key',
        'payment_status'        => 'sanitize_key',
        'contract_info'         => 'sanitize_textarea_field',
        'contract_notes'        => 'sanitize_textarea_field',
        'youtube_id'            => 'sanitize_text_field',
        'video_description'     => 'sanitize_textarea_field',
        'video_category'        => 'sanitize_key',
        'start_date'            => 'sanitize_text_field',
        'end_date'              => 'sanitize_text_field',
        'attachment_id'         => 'absint',
        'document_category'     => 'sanitize_key',
        'document_updated_date' => 'sanitize_text_field',
        'program_name'          => 'sanitize_text_field',
        'subsidy_status'        => 'sanitize_text_field',
        'author_name'           => 'sanitize_text_field',
        'review_facility_name'  => 'sanitize_text_field',
        'review_facility_type'  => 'sanitize_text_field',
        'adoption_period'       => 'sanitize_key',
        'review_rating'         => 'absint',
        'review_tags'           => 'sanitize_text_field',
        'helpful_count'         => 'absint',
    );

    foreach ( $map as $field => $sanitizer ) {
        $input_key = 'yumeho_' . $field;
        if ( ! isset( $_POST[ $input_key ] ) ) {
            continue;
        }

        $value = call_user_func( $sanitizer, wp_unslash( $_POST[ $input_key ] ) );
        update_post_meta( $post_id, '_yumeho_' . $field, $value );
    }

    $product_key = get_post_meta( $post_id, '_yumeho_product_key', true );
    if ( $product_key && isset( yumeho_member_product_choices()[ $product_key ] ) ) {
        wp_set_object_terms( $post_id, $product_key, 'product_type', false );
    } else {
        wp_set_object_terms( $post_id, array(), 'product_type', false );
    }
}
add_action( 'save_post', 'yumeho_member_save_content_meta', 10, 2 );

function yumeho_member_document_admin_assets( $hook ) {
    global $post_type;

    if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) || 'member_document' !== $post_type ) {
        return;
    }

    wp_enqueue_media();
    $script = <<<'JS'
(function($){
  $(function(){
    var frame;
    $('.yumeho-media-select').on('click', function(e){
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
        $('#yumeho_attachment_id').val(attachment.id);
        $('#yumeho_attachment_preview').text(attachment.title || attachment.filename);
        $('.yumeho-media-clear').prop('disabled', false);
      });
      frame.open();
    });
    $('.yumeho-media-clear').on('click', function(e){
      e.preventDefault();
      $('#yumeho_attachment_id').val('');
      $('#yumeho_attachment_preview').text('未選択');
      $(this).prop('disabled', true);
    });
  });
})(jQuery);
JS;
    wp_add_inline_script( 'jquery-core', $script );
}
add_action( 'admin_enqueue_scripts', 'yumeho_member_document_admin_assets' );

function yumeho_member_profile_fields( $user ) {
    $facility_name = get_user_meta( $user->ID, '_yumeho_member_facility_name', true );
    $facility_type = get_user_meta( $user->ID, '_yumeho_member_facility_type', true );
    $products      = (array) get_user_meta( $user->ID, '_yumeho_member_products', true );
    ?>
    <h2>会員ダッシュボード設定</h2>
    <table class="form-table">
        <tr>
            <th><label for="yumeho_member_facility_name">施設名</label></th>
            <td><input type="text" name="yumeho_member_facility_name" id="yumeho_member_facility_name" value="<?php echo esc_attr( $facility_name ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label for="yumeho_member_facility_type">施設種別</label></th>
            <td><input type="text" name="yumeho_member_facility_type" id="yumeho_member_facility_type" value="<?php echo esc_attr( $facility_type ); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th>契約製品</th>
            <td>
                <?php foreach ( yumeho_member_product_choices() as $value => $label ) : ?>
                    <label style="display:inline-block;margin-right:16px;">
                        <input type="checkbox" name="yumeho_member_products[]" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $products, true ) ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
    </table>
    <?php
}
add_action( 'show_user_profile', 'yumeho_member_profile_fields' );
add_action( 'edit_user_profile', 'yumeho_member_profile_fields' );

function yumeho_save_member_profile_fields( $user_id ) {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }

    update_user_meta( $user_id, '_yumeho_member_facility_name', isset( $_POST['yumeho_member_facility_name'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_member_facility_name'] ) ) : '' );
    update_user_meta( $user_id, '_yumeho_member_facility_type', isset( $_POST['yumeho_member_facility_type'] ) ? sanitize_text_field( wp_unslash( $_POST['yumeho_member_facility_type'] ) ) : '' );
    $products = isset( $_POST['yumeho_member_products'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['yumeho_member_products'] ) ) : array();
    update_user_meta( $user_id, '_yumeho_member_products', array_values( array_intersect( $products, array_keys( yumeho_member_product_choices() ) ) ) );
}
add_action( 'personal_options_update', 'yumeho_save_member_profile_fields' );
add_action( 'edit_user_profile_update', 'yumeho_save_member_profile_fields' );

function yumeho_member_document_download() {
    $document_id = isset( $_GET['document_id'] ) ? absint( wp_unslash( $_GET['document_id'] ) ) : 0;
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( yumeho_member_login_url( yumeho_member_page_url() ) );
        exit;
    }

    $nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
    if ( ! $document_id || ! wp_verify_nonce( $nonce, yumeho_member_document_download_nonce_action( $document_id ) ) ) {
        wp_die( '資料にアクセスできません。', '403 Forbidden', array( 'response' => 403 ) );
    }

    if (
        'member_document' !== get_post_type( $document_id ) ||
        'publish' !== get_post_status( $document_id ) ||
        ! yumeho_member_post_is_active( $document_id ) ||
        ! yumeho_member_has_product_access( get_current_user_id(), $document_id )
    ) {
        wp_die( '資料にアクセスできません。', '403 Forbidden', array( 'response' => 403 ) );
    }

    $file = yumeho_member_document_file_data( $document_id );
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
add_action( 'admin_post_yumeho_member_document_download', 'yumeho_member_document_download' );
add_action(
    'admin_post_nopriv_yumeho_member_document_download',
    static function () {
        wp_safe_redirect( yumeho_member_login_url( yumeho_member_page_url() ) );
        exit;
    }
);

function yumeho_member_review_submit() {
    if ( ! is_user_logged_in() ) {
        wp_safe_redirect( yumeho_member_login_url( yumeho_member_page_url() ) );
        exit;
    }

    if ( ! isset( $_POST['yumeho_member_review_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['yumeho_member_review_nonce'] ), 'yumeho_member_review_submit' ) ) {
        wp_safe_redirect( add_query_arg( 'review_status', 'error', yumeho_member_page_url() . '#reviews' ) );
        exit;
    }

    $current_user    = wp_get_current_user();
    $product_key     = isset( $_POST['review_product'] ) ? sanitize_key( wp_unslash( $_POST['review_product'] ) ) : '';
    $facility_name   = isset( $_POST['review_facility_name'] ) ? sanitize_text_field( wp_unslash( $_POST['review_facility_name'] ) ) : '';
    $facility_type   = isset( $_POST['review_facility_type'] ) ? sanitize_text_field( wp_unslash( $_POST['review_facility_type'] ) ) : '';
    $adoption        = isset( $_POST['review_adoption_period'] ) ? sanitize_key( wp_unslash( $_POST['review_adoption_period'] ) ) : '';
    $rating          = isset( $_POST['review_rating'] ) ? absint( wp_unslash( $_POST['review_rating'] ) ) : 0;
    $body            = isset( $_POST['review_body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_body'] ) ) : '';
    $tags            = isset( $_POST['review_tags'] ) ? sanitize_text_field( wp_unslash( $_POST['review_tags'] ) ) : '';
    $author_name     = yumeho_member_user_name( $current_user );
    $product_choices = yumeho_member_product_choices();
    $allowed_products = yumeho_member_get_user_products( $current_user->ID );

    if (
        ! isset( $product_choices[ $product_key ] ) ||
        ! in_array( $product_key, $allowed_products, true ) ||
        $rating < 1 ||
        $rating > 5 ||
        '' === $body ||
        '' === $facility_name
    ) {
        wp_safe_redirect( add_query_arg( 'review_status', 'error', yumeho_member_page_url() . '#reviews' ) );
        exit;
    }

    update_user_meta( $current_user->ID, '_yumeho_member_facility_name', $facility_name );
    update_user_meta( $current_user->ID, '_yumeho_member_facility_type', $facility_type );

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
        wp_safe_redirect( add_query_arg( 'review_status', 'error', yumeho_member_page_url() . '#reviews' ) );
        exit;
    }

    update_post_meta( $post_id, '_yumeho_member_user_id', $current_user->ID );
    update_post_meta( $post_id, '_yumeho_author_name', $author_name );
    update_post_meta( $post_id, '_yumeho_review_facility_name', $facility_name );
    update_post_meta( $post_id, '_yumeho_review_facility_type', $facility_type );
    update_post_meta( $post_id, '_yumeho_adoption_period', $adoption );
    update_post_meta( $post_id, '_yumeho_review_rating', $rating );
    update_post_meta( $post_id, '_yumeho_review_tags', $tags );
    update_post_meta( $post_id, '_yumeho_helpful_count', 0 );
    update_post_meta( $post_id, '_yumeho_product_key', $product_key );
    wp_set_object_terms( $post_id, $product_key, 'product_type', false );

    wp_safe_redirect( add_query_arg( 'review_status', 'success', yumeho_member_page_url() . '#reviews' ) );
    exit;
}
add_action( 'admin_post_yumeho_member_review_submit', 'yumeho_member_review_submit' );
add_action(
    'admin_post_nopriv_yumeho_member_review_submit',
    static function () {
        wp_safe_redirect( yumeho_member_login_url( yumeho_member_page_url() ) );
        exit;
    }
);
