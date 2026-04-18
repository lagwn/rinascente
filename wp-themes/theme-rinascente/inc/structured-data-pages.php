<?php
/**
 * Structured Data for All Pages (JSON-LD + Meta Tags)
 * Generates Schema.org JSON-LD and basic meta tags for each page type
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_head', 'rinascente_output_structured_data_and_meta', 5 );

function rinascente_output_structured_data_and_meta() {
    // Output meta description (if not set by Rank Math)
    if ( ! class_exists( 'RankMath' ) ) {
        rinascente_output_meta_description();
    }

    // Output JSON-LD structured data
    rinascente_output_structured_data_json_ld();
}

function rinascente_output_meta_description() {
    $description = rinascente_get_page_description();
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }
}

function rinascente_get_page_description() {
    $descriptions = array(
        'index'      => '株式会社Rinascente。歩行支援リハビリシステム「YUMEHO」、多相電動式造影剤注入装置「MICA30」を展開。医療・福祉機器の企画・販売。',
        'identity'   => 'Rinascenteの企業理念とブランドアイデンティティ。Vision 2030「人が、何度でも立ち上がれる世界へ。」',
        'cases'      => '株式会社Rinascenteの導入事例。20施設以上の採用実績。YUMEHO・MICA30の導入で施設の課題を解決。',
        'press'      => 'Rinascenteグループの最新ニュース・プレスリリース。新製品発表、認証取得、事業拡張情報などを発信。',
        'contact'    => 'Rinascenteへのお問い合わせ。製品相談・デモ依頼・事業提携を受付中。',
    );

    $page_type = rinascente_get_current_page_type();
    return $descriptions[ $page_type ] ?? '';
}

function rinascente_output_structured_data_json_ld() {
    $schema = rinascente_get_current_page_schema();
    if ( empty( $schema ) ) {
        return;
    }

    $graph = array(
        '@context' => 'https://schema.org',
        '@graph'   => array_filter( $schema ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}

function rinascente_get_current_page_type() {
    if ( is_front_page() ) {
        return 'index';
    }

    if ( is_page() ) {
        $template = basename( get_page_template_slug() );
        switch ( $template ) {
            case 'page-identity.php':
                return 'identity';
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

    if ( is_archive( 'news' ) || is_post_type_archive( 'news' ) ) {
        return 'press';
    }

    if ( is_singular( 'news' ) ) {
        return 'news';
    }

    return 'page';
}

function rinascente_get_current_page_schema() {
    $page_type = rinascente_get_current_page_type();

    switch ( $page_type ) {
        case 'index':
            return array(
                rinascente_schema_org(),
            );

        case 'identity':
            return array(
                rinascente_schema_org(),
                array(
                    '@type' => 'AboutPage',
                    '@id'   => home_url( '/identity/' ) . '#about',
                    'name'  => 'Corporate Identity',
                    'description' => 'Rinascenteの企業理念とブランドアイデンティティ。Vision 2030「人が、何度でも立ち上がれる世界へ。」',
                    'url'   => home_url( '/identity/' ),
                    'about' => array(
                        '@type' => 'Organization',
                        '@id'   => home_url( '/' ) . '#organization',
                    ),
                ),
            );

        case 'cases':
            return array(
                rinascente_schema_org(),
                array(
                    '@type' => 'CollectionPage',
                    '@id'   => home_url( '/cases/' ) . '#collection',
                    'name'  => '導入事例',
                    'description' => 'YUMEHO・MICA30の導入事例。20施設以上の採用実績。',
                    'url'   => home_url( '/cases/' ),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        case 'press':
            return array(
                rinascente_schema_org(),
                array(
                    '@type' => 'CollectionPage',
                    '@id'   => home_url( '/press/' ) . '#collection',
                    'name'  => 'Press',
                    'description' => 'Rinascenteグループの最新ニュース・プレスリリース。',
                    'url'   => home_url( '/press/' ),
                    'isPartOf' => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        case 'news':
            return array(
                rinascente_schema_org(),
                array(
                    '@type'           => 'NewsArticle',
                    '@id'             => get_permalink() . '#article',
                    'headline'        => get_the_title(),
                    'description'     => get_the_excerpt(),
                    'datePublished'   => get_the_date( 'Y-m-d' ),
                    'dateModified'    => get_the_modified_date( 'Y-m-d' ),
                    'author'          => array(
                        '@type' => 'Organization',
                        '@id'   => home_url( '/' ) . '#organization',
                    ),
                    'url'             => get_permalink(),
                    'isPartOf'        => array(
                        '@type' => 'WebSite',
                        '@id'   => home_url( '/' ),
                    ),
                ),
            );

        case 'contact':
            return array(
                rinascente_schema_org(),
                array(
                    '@type' => 'ContactPage',
                    '@id'   => home_url( '/contact/' ) . '#contact',
                    'name'  => 'お問い合わせ',
                    'description' => 'Rinascenteへのお問い合わせ。製品相談・デモ依頼・事業提携を受付中。',
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
                rinascente_schema_org(),
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
