<?php
/**
 * Structured Data for All Pages (JSON-LD + Meta Tags)
 * Generates Schema.org JSON-LD and basic meta tags for each page type
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_head', 'yumeho_output_structured_data_and_meta', 5 );

function yumeho_output_structured_data_and_meta() {
    // Output meta description (if not set by Rank Math)
    if ( ! class_exists( 'RankMath' ) ) {
        yumeho_output_meta_description();
    }

    // Output JSON-LD structured data
    yumeho_output_structured_data_json_ld();
}

function yumeho_output_meta_description() {
    $description = yumeho_get_page_description();
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
}

function yumeho_get_page_description() {
    $descriptions = array(
        'index'      => 'YUMEHO（ウォークメイト）は、転倒リスクを物理的に防ぎながら両手フリーで多様なリハビリ課題を実現する歩行支援リハビリシステムです。',
        'product'    => '天井直付型FCW-3000とスタンド型PGT-9000/PGT-9001。特許取得G-Suitハーネスとデュアルレールで安全な免荷歩行訓練を実現。',
        'simulation' => '施設種別・設置方式・オプションを選ぶだけで、YUMEHOの最適システム構成と概算費用を自動診断。',
        'cases'      => 'YUMEHOの導入事例。回復期リハビリ病院で訓練機会1.5倍、スタッフ3名→1名体制を実現。',
        'flow'       => 'お問い合わせ→ヒアリング→現地調査→レイアウト提案→設置工事→操作研修→稼働開始。最短2週間で対応。',
        'price'      => 'YUMEHOの価格・見積情報。購入・リース両対応。補助金・助成金活用で自己負担を軽減。',
        'subsidy'    => 'YUMEHOの導入に活用できる補助金・助成金制度ガイド。介護ロボット導入支援事業、ものづくり補助金等に対応。',
        'faq'        => 'YUMEHO（ウォークメイト）についてよくいただく質問と回答をまとめています。',
        'company'    => '株式会社Rinascente（リナシェンテ）の会社概要。医療・福祉機器の企画・販売を手がけています。',
        'contact'    => 'YUMEHOへの資料請求・お問い合わせ。2営業日以内に専任スタッフからご連絡いたします。',
    );

    $page_type = yumeho_get_current_page_type();
    return $descriptions[ $page_type ] ?? '';
}

function yumeho_output_structured_data_json_ld() {
    $schema = yumeho_get_current_page_schema();
    if ( empty( $schema ) ) {
        return;
    }

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array_filter( $schema ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}

function yumeho_get_current_page_type() {
    if ( is_front_page() ) {
        return 'index';
    }

    if ( is_page() ) {
        $template = basename( get_page_template_slug() );
        switch ( $template ) {
            case 'page-simulation.php':
                return 'simulation';
            case 'page-contact.php':
                return 'contact';
            default:
                // Generic page
                $slug = get_post_field( 'post_name', get_the_ID() );
                return $slug ?: 'page';
        }
    }

    if ( is_archive( 'case_study' ) ) {
        return 'cases';
    }

    return 'page';
}

function yumeho_get_current_page_schema() {
    $page_type = yumeho_get_current_page_type();

    switch ( $page_type ) {
        case 'index':
            return array(
                yumeho_schema_org(),
                yumeho_schema_product_entity( 'YUMEHO（ウォークメイト）', home_url( '/' ) ),
            );

        case 'product':
            return array(
                yumeho_schema_org(),
                array(
                    '@type'        => 'Product',
                    '@id'          => home_url( '/product/' ) . '#product',
                    'name'         => 'YUMEHO 製品ラインアップ',
                    'description'  => '天井直付型FCW-3000とスタンド型PGT-9000/PGT-9001。特許取得G-Suitハーネスとデュアルレールで安全な免荷歩行訓練を実現。',
                    'brand'        => array(
                        '@type' => 'Brand',
                        'name'  => 'YUMEHO',
                    ),
                    'manufacturer' => array(
                        '@id' => yumeho_related_site_url( 'corporate' ) . '#organization',
                    ),
                    'category'     => '医療機器・福祉機器',
                    'audience'     => array(
                        '@type'        => 'Audience',
                        'audienceType' => '病院・介護施設・デイサービス',
                    ),
                ),
            );

        case 'simulation':
            return array(
                yumeho_schema_org(),
                array(
                    '@type' => 'WebPage',
                    '@id'   => home_url( '/simulation/' ) . '#webpage',
                    'name'  => '導入シミュレーション',
                    'description' => '施設種別・設置方式・オプションを選ぶだけで、YUMEHOの最適システム構成と概算費用を自動診断。',
                    'url'   => home_url( '/simulation/' ),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        case 'cases':
            return array(
                yumeho_schema_org(),
                array(
                    '@type' => 'CollectionPage',
                    '@id'   => home_url( '/cases/' ) . '#collection',
                    'name'  => '導入事例',
                    'description' => 'YUMEHOの導入事例。回復期リハビリ病院で訓練機会1.5倍、スタッフ3名→1名体制を実現。',
                    'url'   => home_url( '/cases/' ),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        case 'contact':
            return array(
                yumeho_schema_org(),
                array(
                    '@type' => 'ContactPage',
                    '@id'   => home_url( '/contact/' ) . '#contact',
                    'name'  => '資料請求・お問い合わせ',
                    'description' => 'YUMEHOの資料請求・お問い合わせ。2営業日以内に専任スタッフからご連絡いたします。',
                    'url'   => home_url( '/contact/' ),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        default:
            // Generic WebPage
            return array(
                yumeho_schema_org(),
                array(
                    '@type' => 'WebPage',
                    '@id'   => get_permalink() . '#webpage',
                    'name'  => get_the_title(),
                    'url'   => get_permalink(),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );
    }
}
