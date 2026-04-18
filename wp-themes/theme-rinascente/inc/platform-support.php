<?php
/**
 * Platform support helpers for Rinascente.
 *
 * @package Rinascente
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function rinascente_register_related_site_controls( $wp_customize ) {
    $wp_customize->add_section(
        'rinascente_related_sites',
        array(
            'title'    => '関連サイト URL',
            'priority' => 36,
        )
    );

    $fields = array(
        'related_yumeho_url' => 'YUMEHO サイト URL',
    );

    if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) {
        $fields['related_mica30_url'] = 'MICA30 サイト URL';
    }

    foreach ( $fields as $key => $label ) {
        $wp_customize->add_setting(
            $key,
            array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            )
        );
        $wp_customize->add_control(
            $key,
            array(
                'label'   => $label,
                'section' => 'rinascente_related_sites',
                'type'    => 'url',
            )
        );
    }
}
add_action( 'customize_register', 'rinascente_register_related_site_controls', 40 );

function rinascente_company_info_payload() {
    $privacy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
    $terms_page      = get_page_by_path( 'terms' );
    $company_data    = function_exists( 'rinascente_company_profile_data' ) ? rinascente_company_profile_data() : array();

    return array(
        'company_name'       => $company_data['company_name'] ?? get_theme_mod( 'company_name', '株式会社リナシェンテ' ),
        'company_name_en'    => $company_data['company_name_en'] ?? '',
        'company_ceo'        => $company_data['company_ceo'] ?? get_theme_mod( 'company_ceo', '' ),
        'company_founded'    => $company_data['company_founded'] ?? get_theme_mod( 'company_founded', '' ),
        'company_capital'    => $company_data['company_capital'] ?? '',
        'company_zip'        => '',
        'company_address'    => $company_data['company_address'] ?? get_theme_mod( 'company_address', '' ),
        'company_tel'        => $company_data['company_tel'] ?? get_theme_mod( 'company_tel', '0859-00-1234' ),
        'company_fax'        => $company_data['company_fax'] ?? '',
        'company_email'      => '',
        'company_business'   => $company_data['company_business'] ?? get_theme_mod( 'company_business', '' ),
        'company_products'   => function_exists( 'rinascente_company_products_text' ) ? rinascente_company_products_text() : ( $company_data['company_products'] ?? get_theme_mod( 'company_products', '' ) ),
        'company_hours'      => $company_data['company_hours'] ?? get_theme_mod( 'company_hours', '' ),
        'support_name'       => $company_data['support_name'] ?? get_theme_mod( 'support_name', '' ),
        'support_tel'        => $company_data['support_tel'] ?? get_theme_mod( 'support_tel', '' ),
        'support_email'      => '',
        'support_hours'      => $company_data['support_hours'] ?? get_theme_mod( 'support_hours', '' ),
        'related_yumeho_url' => get_theme_mod( 'related_yumeho_url', rinascente_related_site_url( 'yumeho' ) ),
        'related_mica30_url' => ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) ? get_theme_mod( 'related_mica30_url', rinascente_related_site_url( 'mica30' ) ) : '',
        'privacy_policy_url' => $privacy_page_id ? get_permalink( $privacy_page_id ) : home_url( '/privacy-policy/' ),
        'terms_url'          => $terms_page ? get_permalink( $terms_page ) : home_url( '/terms/' ),
        'member_url'         => function_exists( 'rinascente_member_page_url' ) ? rinascente_member_page_url() : home_url( '/member/' ),
        'login_url'          => function_exists( 'rinascente_member_login_url' ) ? rinascente_member_login_url( home_url( '/member/' ) ) : home_url( '/login/' ),
    );
}

function rinascente_register_company_info_endpoint() {
    register_rest_route(
        'rinascente/v1',
        '/company-info',
        array(
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => '__return_true',
            'callback'            => static function () {
                return rest_ensure_response( rinascente_company_info_payload() );
            },
        )
    );
}
add_action( 'rest_api_init', 'rinascente_register_company_info_endpoint' );

function rinascente_default_social_image_url() {
    $preferred = array(
        'assets/img/ogp-rinascente.jpg',
        'assets/img/yumeho.webp',
        'assets/img/logo.svg',
    );

    foreach ( $preferred as $relative_path ) {
        $absolute_path = trailingslashit( get_template_directory() ) . ltrim( $relative_path, '/' );
        if ( file_exists( $absolute_path ) ) {
            return trailingslashit( get_template_directory_uri() ) . ltrim( $relative_path, '/' );
        }
    }

    return '';
}

function rinascente_filter_default_social_image( $image ) {
    if ( ! empty( $image ) ) {
        return $image;
    }

    return rinascente_default_social_image_url();
}
add_filter( 'rank_math/opengraph/facebook/image', 'rinascente_filter_default_social_image' );
add_filter( 'rank_math/opengraph/twitter/image', 'rinascente_filter_default_social_image' );

function rinascente_site_brand_name() {
    return 'Rinascente';
}

function rinascente_site_brand_aliases() {
    $aliases = array( 'リナシェンテ', 'Rinascente Inc.' );
    $host    = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

    if ( is_string( $host ) ) {
        $host = strtolower( preg_replace( '/^www\./', '', $host ) );
        if ( $host && ! preg_match( '/(^|\.)(local|localhost)$/', $host ) ) {
            $aliases[] = $host;
        }
    }

    return array_values( array_unique( array_filter( array_map( 'trim', $aliases ) ) ) );
}

function rinascente_brand_display_name() {
    return 'Rinascente（リナシェンテ）';
}

function rinascente_text_contains( $text, $needle ) {
    if ( ! is_string( $text ) || ! is_string( $needle ) || '' === $text || '' === $needle ) {
        return false;
    }

    if ( function_exists( 'mb_stripos' ) ) {
        return false !== mb_stripos( $text, $needle, 0, 'UTF-8' );
    }

    return false !== stripos( $text, $needle );
}

function rinascente_enrich_brand_mentions( $text ) {
    if ( ! is_string( $text ) || '' === trim( $text ) ) {
        return $text;
    }

    if ( rinascente_text_contains( $text, rinascente_brand_display_name() ) ) {
        return $text;
    }

    if ( rinascente_text_contains( $text, 'Rinascente' ) && ! rinascente_text_contains( $text, 'リナシェンテ' ) ) {
        return (string) preg_replace( '/Rinascente/u', rinascente_brand_display_name(), $text, 1 );
    }

    if ( rinascente_text_contains( $text, 'リナシェンテ' ) && ! rinascente_text_contains( $text, 'Rinascente' ) ) {
        return (string) preg_replace( '/リナシェンテ/u', rinascente_brand_display_name(), $text, 1 );
    }

    return $text;
}

function rinascente_normalize_keyword_list( $keywords ) {
    if ( is_string( $keywords ) ) {
        $keywords = preg_split( '/[\n\r,、|\/]+/u', $keywords );
    }

    $normalized = array();

    foreach ( (array) $keywords as $keyword ) {
        $keyword = trim( wp_strip_all_tags( (string) $keyword ) );
        if ( '' === $keyword ) {
            continue;
        }
        $index = function_exists( 'mb_strtolower' ) ? mb_strtolower( $keyword, 'UTF-8' ) : strtolower( $keyword );
        $normalized[ $index ] = $keyword;
    }

    return array_values( $normalized );
}

function rinascente_post_term_keywords( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
    if ( is_wp_error( $terms ) ) {
        return array();
    }

    return rinascente_normalize_keyword_list( $terms );
}

function rinascente_current_archive_term() {
    $term = get_queried_object();
    return ( $term instanceof WP_Term ) ? $term : null;
}

function rinascente_term_archive_fallback_description( $term = null ) {
    $term = $term instanceof WP_Term ? $term : rinascente_current_archive_term();
    if ( ! $term ) {
        return '';
    }

    $description = trim( wp_strip_all_tags( (string) $term->description ) );
    if ( '' !== $description ) {
        return $description;
    }

    switch ( $term->taxonomy ) {
        case 'column_category':
            return sprintf( '「%s」に関する医療・福祉現場向けのコラム一覧です。実務のヒントや最新動向をまとめて確認できます。', $term->name );

        case 'news_category':
            return sprintf( '「%s」に関する Rinascente（リナシェンテ）のニュース・プレスリリース一覧です。関連する製品情報や企業発表を確認できます。', $term->name );
    }

    return '';
}

function rinascente_current_seo_keywords() {
    $keywords = array( rinascente_site_brand_name(), 'リナシェンテ' );

    if ( is_front_page() || is_home() ) {
        $keywords = array_merge( $keywords, array( '医療機器メーカー', '福祉機器メーカー', 'ヘルスケア', 'YUMEHO', '夢歩' ) );
    } elseif ( is_page( 'identity' ) ) {
        $keywords = array_merge( $keywords, array( '企業理念', 'ブランドビジョン', 'Vision 2030', '再生' ) );
    } elseif ( is_post_type_archive( 'news' ) || is_page( 'press' ) ) {
        $keywords = array_merge( $keywords, array( 'プレスリリース', 'ニュース', '製品情報' ) );
    } elseif ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $keywords = array_merge( $keywords, array( '導入事例', '医療機器導入事例', '介護施設導入事例', 'YUMEHO', '夢歩' ) );
    } elseif ( is_page( 'contact' ) ) {
        $keywords = array_merge( $keywords, array( 'お問い合わせ', '製品相談', '事業提携', 'デモ依頼' ) );
    }

    if ( is_singular() ) {
        $post_id   = get_queried_object_id();
        $keywords[] = get_the_title( $post_id );
        $keywords   = array_merge(
            $keywords,
            rinascente_post_term_keywords( $post_id, 'product_type' ),
            rinascente_post_term_keywords( $post_id, 'news_category' ),
            rinascente_post_term_keywords( $post_id, 'facility_type' ),
            rinascente_post_term_keywords( $post_id, 'case_format' )
        );

        $facility_name = get_post_meta( $post_id, '_rinascente_facility_name', true );
        if ( $facility_name ) {
            $keywords[] = $facility_name;
        }
    }

    $term = rinascente_current_archive_term();
    if ( $term ) {
        $keywords[] = $term->name;

        switch ( $term->taxonomy ) {
            case 'column_category':
                $keywords = array_merge( $keywords, array( 'コラム', '医療コラム', '福祉コラム', '現場ノウハウ' ) );
                break;

            case 'news_category':
                $keywords = array_merge( $keywords, array( 'Press', 'プレスリリース', '企業発表', '最新情報' ) );
                break;
        }
    }

    return array_slice( rinascente_normalize_keyword_list( $keywords ), 0, 16 );
}

function rinascente_internal_pathway_links( $context = 'default' ) {
    $links = array();

    switch ( $context ) {
        case 'column':
            $links = array(
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要とブランドの考え方を見る',
                    'url'         => home_url( '/identity/' ),
                    'description' => 'Rinascenteの理念や事業の方向性、ブランドの背景をまとめて確認できます。',
                ),
                array(
                    'eyebrow'     => '導入事例',
                    'title'       => '導入事例で活用シーンを見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'コラムの内容が実際の現場でどう活かされているかを、施設事例から確認できます。',
                ),
                array(
                    'eyebrow'     => 'Press',
                    'title'       => 'Pressで最新のお知らせを確認する',
                    'url'         => home_url( '/press/' ),
                    'description' => '製品情報やリリース情報など、最新の更新をまとめて確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや事業相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '導入相談、取材相談、事業連携など、目的に応じてそのまま問い合わせできます。',
                ),
            );
            break;

        case 'news':
            $links = array(
                array(
                    'eyebrow'     => 'Press',
                    'title'       => 'Press一覧から関連するお知らせを続けて読む',
                    'url'         => home_url( '/press/' ),
                    'description' => '同じテーマの更新や前後のお知らせを一覧からまとめて確認できます。',
                ),
                array(
                    'eyebrow'     => '導入事例',
                    'title'       => '導入事例で現場への広がりを見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'ニュースで触れた内容が現場でどう活用されているかを、事例ベースで確認できます。',
                ),
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要と事業内容を確認する',
                    'url'         => home_url( '/identity/' ),
                    'description' => 'Rinascenteの事業背景や、取り組み全体の位置づけを整理できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや取材相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '製品相談や広報・連携に関する問い合わせ先へそのまま進めます。',
                ),
            );
            break;

        case 'case_study':
            $links = array(
                array(
                    'eyebrow'     => '他事例比較',
                    'title'       => '他の導入事例も見て比較する',
                    'url'         => home_url( '/cases/' ),
                    'description' => '施設種別や導入背景の違いを見比べながら、自社サービスとの接点を探せます。',
                ),
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要とブランドの考え方を見る',
                    'url'         => home_url( '/identity/' ),
                    'description' => '導入事例の背景にある事業方針やブランドの考え方を確認できます。',
                ),
                array(
                    'eyebrow'     => 'Press',
                    'title'       => 'Pressで関連する発表を確認する',
                    'url'         => home_url( '/press/' ),
                    'description' => '製品情報やお知らせなど、導入事例と合わせて見たい更新を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや導入相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '事例を見たうえで相談したい内容を、そのまま問い合わせにつなげられます。',
                ),
            );
            break;

        case 'column_archive':
            $links = array(
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要とブランドの考え方を見る',
                    'url'         => home_url( '/identity/' ),
                    'description' => 'Rinascenteの理念や事業背景、ブランドの方向性をまとめて確認できます。',
                ),
                array(
                    'eyebrow'     => '導入事例',
                    'title'       => '導入事例で現場の活用シーンを見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'コラムで触れた内容が現場でどう活かされているかを、事例ベースで確認できます。',
                ),
                array(
                    'eyebrow'     => 'Press',
                    'title'       => 'Pressで最新のお知らせを確認する',
                    'url'         => home_url( '/press/' ),
                    'description' => '製品情報や企業発表など、関連する最新の更新を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや事業相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '導入相談、事業連携、広報・取材相談など、目的に応じた問い合わせへ進めます。',
                ),
            );
            break;

        case 'news_archive':
            $links = array(
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要と事業内容を確認する',
                    'url'         => home_url( '/identity/' ),
                    'description' => 'ニュースの背景にある事業全体の位置づけや、ブランドの考え方を整理できます。',
                ),
                array(
                    'eyebrow'     => '導入事例',
                    'title'       => '導入事例で現場への広がりを見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'ニュースで触れた内容が現場でどう活用されているかを、事例から確認できます。',
                ),
                array(
                    'eyebrow'     => 'コラム',
                    'title'       => 'コラムで周辺テーマを深掘りする',
                    'url'         => get_post_type_archive_link( 'column' ) ?: home_url( '/column/' ),
                    'description' => '医療・福祉の現場動向や関連テーマを、より詳しい解説記事で確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや取材相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '製品相談や広報・連携に関する問い合わせ先へ、そのまま進めます。',
                ),
            );
            break;

        case 'case_study_archive':
            $links = array(
                array(
                    'eyebrow'     => '企業情報',
                    'title'       => '会社概要とブランドの考え方を見る',
                    'url'         => home_url( '/identity/' ),
                    'description' => '導入事例の背景にある事業方針や、ブランド全体の考え方を確認できます。',
                ),
                array(
                    'eyebrow'     => 'Press',
                    'title'       => 'Pressで関連する発表を確認する',
                    'url'         => home_url( '/press/' ),
                    'description' => '製品情報や企業発表など、導入事例と合わせて見たい更新を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => 'コラム',
                    'title'       => 'コラムで導入背景を深掘りする',
                    'url'         => get_post_type_archive_link( 'column' ) ?: home_url( '/column/' ),
                    'description' => '事例に関連する医療・福祉現場の動向や、周辺テーマの解説を確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => 'お問い合わせや導入相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '事例を見たうえで相談したい内容を、そのまま問い合わせにつなげられます。',
                ),
            );
            break;
    }

    $current_url = home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
    $current_url = untrailingslashit( preg_replace( '/[?#].*$/', '', (string) $current_url ) );

    return array_values(
        array_filter(
            $links,
            static function( $link ) use ( $current_url ) {
                if ( empty( $link['url'] ) ) {
                    return false;
                }

                $link_url = untrailingslashit( preg_replace( '/[?#].*$/', '', (string) $link['url'] ) );
                return $link_url !== $current_url;
            }
        )
    );
}

function rinascente_render_internal_pathways( $context = 'default', $args = array() ) {
    $links = rinascente_internal_pathway_links( $context );
    if ( empty( $links ) ) {
        return;
    }

    $args = wp_parse_args(
        $args,
        array(
            'title' => '関連して確認したいページ',
            'intro' => '読み進めた次の行動につながりやすいページをまとめました。',
        )
    );

    static $style_printed = false;
    if ( ! $style_printed ) {
        $style_printed = true;
        ?>
        <style>
        .rinascente-pathways {
            margin-top: clamp(32px, 4vw, 56px);
            padding: clamp(28px, 4vw, 40px);
            background: linear-gradient(135deg, rgba(200,169,110,0.08), rgba(244,239,230,0.95));
            border: 1px solid rgba(200,169,110,0.22);
            border-radius: 16px;
        }
        .rinascente-pathways__header {
            margin-bottom: 24px;
        }
        .rinascente-pathways__eyebrow {
            display: inline-block;
            margin-bottom: 10px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--gold-deep);
            text-transform: uppercase;
        }
        .rinascente-pathways__title {
            margin: 0 0 10px;
            font-size: clamp(1.15rem, 1.8vw, 1.4rem);
            font-weight: 700;
            color: var(--charcoal);
        }
        .rinascente-pathways__intro {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.9;
            color: rgba(0,0,0,0.68);
        }
        .rinascente-pathways__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .rinascente-pathways__card {
            display: block;
            padding: 20px 22px;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(200,169,110,0.20);
            border-radius: 14px;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .rinascente-pathways__card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.06);
            border-color: rgba(137,102,41,0.35);
        }
        .rinascente-pathways__card-eyebrow {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--gold-deep);
        }
        .rinascente-pathways__card-title {
            display: block;
            margin-bottom: 10px;
            font-size: 0.96rem;
            font-weight: 700;
            line-height: 1.7;
            color: var(--charcoal);
        }
        .rinascente-pathways__card-desc {
            display: block;
            font-size: 0.84rem;
            line-height: 1.8;
            color: rgba(0,0,0,0.68);
        }
        @media (max-width: 767px) {
            .rinascente-pathways__grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
    }
    ?>
    <div class="rinascente-pathways">
        <div class="rinascente-pathways__header">
            <span class="rinascente-pathways__eyebrow">Next Steps</span>
            <h2 class="rinascente-pathways__title"><?php echo esc_html( $args['title'] ); ?></h2>
            <p class="rinascente-pathways__intro"><?php echo esc_html( $args['intro'] ); ?></p>
        </div>
        <div class="rinascente-pathways__grid">
            <?php foreach ( $links as $link ) : ?>
            <a href="<?php echo esc_url( $link['url'] ); ?>" class="rinascente-pathways__card">
                <span class="rinascente-pathways__card-eyebrow"><?php echo esc_html( $link['eyebrow'] ); ?></span>
                <span class="rinascente-pathways__card-title"><?php echo esc_html( $link['title'] ); ?></span>
                <span class="rinascente-pathways__card-desc"><?php echo esc_html( $link['description'] ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function rinascente_schema_has_type( $data, $type ) {
    foreach ( (array) $data as $entity ) {
        if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
            continue;
        }

        $entity_types = is_array( $entity['@type'] ) ? $entity['@type'] : array( $entity['@type'] );
        if ( in_array( $type, $entity_types, true ) ) {
            return true;
        }
    }

    return false;
}

function rinascente_breadcrumb_items() {
    if ( is_front_page() || is_home() ) {
        return array();
    }

    $items = array(
        array(
            'name' => 'Home',
            'url'  => home_url( '/' ),
        ),
    );

    if ( is_post_type_archive( 'news' ) || is_page( 'press' ) ) {
        $items[] = array(
            'name' => 'Press',
            'url'  => get_post_type_archive_link( 'news' ) ?: get_permalink(),
        );
        return $items;
    }

    if ( is_tax( 'news_category' ) ) {
        $term    = rinascente_current_archive_term();
        $items[] = array(
            'name' => 'Press',
            'url'  => get_post_type_archive_link( 'news' ) ?: home_url( '/press/' ),
        );
        if ( $term ) {
            $items[] = array(
                'name' => $term->name,
                'url'  => get_term_link( $term ),
            );
        }
        return $items;
    }

    if ( is_singular( 'news' ) ) {
        $items[] = array(
            'name' => 'Press',
            'url'  => get_post_type_archive_link( 'news' ) ?: home_url( '/press/' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
        return $items;
    }

    if ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $items[] = array(
            'name' => '導入事例',
            'url'  => get_post_type_archive_link( 'case_study' ) ?: get_permalink(),
        );
        return $items;
    }

    if ( is_singular( 'case_study' ) ) {
        $items[] = array(
            'name' => '導入事例',
            'url'  => get_post_type_archive_link( 'case_study' ) ?: home_url( '/cases/' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
        return $items;
    }

    if ( is_post_type_archive( 'column' ) ) {
        $items[] = array(
            'name' => 'コラム',
            'url'  => get_post_type_archive_link( 'column' ),
        );
        return $items;
    }

    if ( is_tax( 'column_category' ) ) {
        $term    = rinascente_current_archive_term();
        $items[] = array(
            'name' => 'コラム',
            'url'  => get_post_type_archive_link( 'column' ) ?: home_url( '/column/' ),
        );
        if ( $term ) {
            $items[] = array(
                'name' => $term->name,
                'url'  => get_term_link( $term ),
            );
        }
        return $items;
    }

    if ( is_singular( 'column' ) ) {
        $items[] = array(
            'name' => 'コラム',
            'url'  => get_post_type_archive_link( 'column' ) ?: home_url( '/column/' ),
        );
        $items[] = array(
            'name' => get_the_title(),
            'url'  => get_permalink(),
        );
        return $items;
    }

    if ( is_page() ) {
        $page_id   = get_queried_object_id();
        $ancestors = array_reverse( get_post_ancestors( $page_id ) );

        foreach ( $ancestors as $ancestor_id ) {
            $items[] = array(
                'name' => get_the_title( $ancestor_id ),
                'url'  => get_permalink( $ancestor_id ),
            );
        }

        $items[] = array(
            'name' => get_the_title( $page_id ),
            'url'  => get_permalink( $page_id ),
        );
    }

    return $items;
}

function rinascente_schema_breadcrumb_list() {
    $items = rinascente_breadcrumb_items();
    if ( count( $items ) < 2 ) {
        return array();
    }

    $list = array();
    foreach ( $items as $index => $item ) {
        $list[] = array(
            '@type'    => 'ListItem',
            'position' => $index + 1,
            'name'     => $item['name'],
            'item'     => $item['url'],
        );
    }

    return array(
        '@type'           => 'BreadcrumbList',
        '@id'             => home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ) . '#breadcrumb',
        'itemListElement' => $list,
    );
}

function rinascente_schema_website() {
    return array(
        '@type'         => 'WebSite',
        '@id'           => home_url( '/' ) . '#website',
        'url'           => home_url( '/' ),
        'name'          => rinascente_site_brand_name(),
        'alternateName' => rinascente_site_brand_aliases(),
        'inLanguage'    => 'ja-JP',
        'publisher'     => array(
            '@id' => home_url( '/' ) . '#organization',
        ),
    );
}

function rinascente_enrich_existing_website_schema( $data ) {
    foreach ( (array) $data as $key => $entity ) {
        if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
            continue;
        }

        $entity_types = is_array( $entity['@type'] ) ? $entity['@type'] : array( $entity['@type'] );
        if ( ! in_array( 'WebSite', $entity_types, true ) ) {
            continue;
        }

        $data[ $key ]['name']          = rinascente_site_brand_name();
        $data[ $key ]['alternateName'] = rinascente_site_brand_aliases();
        $data[ $key ]['inLanguage']    = 'ja-JP';
        if ( empty( $data[ $key ]['publisher'] ) ) {
            $data[ $key ]['publisher'] = array( '@id' => home_url( '/' ) . '#organization' );
        }
    }

    return $data;
}

function rinascente_enrich_existing_breadcrumb_schema( $data ) {
    $breadcrumb = rinascente_schema_breadcrumb_list();
    if ( empty( $breadcrumb ) ) {
        return $data;
    }

    foreach ( (array) $data as $key => $entity ) {
        if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
            continue;
        }

        $entity_types = is_array( $entity['@type'] ) ? $entity['@type'] : array( $entity['@type'] );
        if ( ! in_array( 'BreadcrumbList', $entity_types, true ) ) {
            continue;
        }

        $data[ $key ]['@id']             = $breadcrumb['@id'];
        $data[ $key ]['itemListElement'] = $breadcrumb['itemListElement'];
    }

    return $data;
}

function rinascente_pending_mica30_seo_overrides() {
    if ( ! function_exists( 'rinascente_mica30_enabled' ) || rinascente_mica30_enabled() ) {
        return array();
    }

    if ( is_front_page() || is_home() ) {
        return array(
            'description'         => 'Rinascente（リナシェンテ）は、歩行リハビリ支援システム YUMEHO を展開する医療福祉機器メーカーです。「再生」の理念のもと、病院・介護施設の課題解決と、ヘルスケア領域の拡張に取り組んでいます。',
            'og_description'      => 'YUMEHO を展開する医療福祉機器メーカー。再生の理念で現場課題を解決し続けます。',
            'twitter_description' => 'YUMEHO を展開する医療福祉機器メーカー。再生の理念で現場課題を解決し続けます。',
            'keywords'            => 'Rinascente,リナシェンテ,医療機器,福祉機器,YUMEHO,歩行リハビリ,Healthcare',
        );
    }

    if ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        return array(
            'title'               => '導入事例 | Rinascente YUMEHOの成果実績',
            'description'         => 'YUMEHOの導入事例。20施設以上の採用実績。歩行訓練機会1.5倍増、スタッフ負担40％削減、利用者参加率31％向上。病院回復期病棟、介護老健施設、デイサービスでの実際の成果をご紹介。',
            'og_title'            => '導入事例 | Rinascente YUMEHO',
            'og_description'      => '20施設以上の採用実績。訓練機会1.5倍、スタッフ負担40％削減の成果を数値で紹介。',
            'twitter_title'       => '導入事例 | Rinascente YUMEHO',
            'twitter_description' => '20施設以上の採用実績。訓練機会1.5倍、スタッフ負担40％削減の成果を数値で紹介。',
            'keywords'            => 'Rinascente,導入事例,YUMEHO,病院,介護施設,訓練機会,スタッフ削減,成果実績',
        );
    }

    if ( is_post_type_archive( 'news' ) || is_page( 'press' ) ) {
        return array(
            'description'         => 'Rinascente（リナシェンテ）グループの最新ニュース。YUMEHO の製品情報、認証取得、導入事例、事業展開に関するプレスリリースをお届けします。',
            'og_description'      => 'YUMEHO の製品情報、認証取得、事業展開の最新ニュースをお届けします。',
            'twitter_description' => 'YUMEHO の製品情報、認証取得、事業展開の最新ニュースをお届けします。',
            'keywords'            => 'Rinascente,プレスリリース,ニュース,YUMEHO,新製品,認証,医療機器',
        );
    }

    if ( is_page( 'contact' ) ) {
        return array(
            'description'         => 'Rinascente（リナシェンテ）へのお問い合わせ。YUMEHO の製品相談、デモ依頼、事業提携のご提案、採用に関するお問い合わせを受け付けています。TEL: 0859-00-1234（平日 9:00-17:00）',
            'og_description'      => '製品相談・デモ依頼・事業提携のご提案を受付中。TEL: 0859-00-1234（平日 9:00-17:00）',
            'twitter_description' => '製品相談・デモ依頼・事業提携のご提案を受付中。TEL: 0859-00-1234（平日 9:00-17:00）',
            'keywords'            => 'Rinascente,お問い合わせ,問い合わせ,製品相談,デモ,事業提携,採用,連絡先',
        );
    }

    return array();
}

function rinascente_rank_math_override_meta_value( $value, $key ) {
    $overrides = rinascente_pending_mica30_seo_overrides();
    if ( isset( $overrides[ $key ] ) && '' !== (string) $overrides[ $key ] ) {
        return $overrides[ $key ];
    }

    return $value;
}

function rinascente_rank_math_title_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'title' );
}
add_filter( 'rank_math/frontend/title', 'rinascente_rank_math_title_override' );

function rinascente_rank_math_brand_title_enrichment( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/frontend/title', 'rinascente_rank_math_brand_title_enrichment', 30 );

function rinascente_rank_math_description_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'description' );
}
add_filter( 'rank_math/frontend/description', 'rinascente_rank_math_description_override' );

function rinascente_rank_math_brand_description_enrichment( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/frontend/description', 'rinascente_rank_math_brand_description_enrichment', 30 );

function rinascente_rank_math_keywords_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'keywords' );
}
add_filter( 'rank_math/frontend/keywords', 'rinascente_rank_math_keywords_override' );

function rinascente_rank_math_dynamic_keywords( $value ) {
    $keywords = array_merge(
        rinascente_normalize_keyword_list( $value ),
        rinascente_current_seo_keywords()
    );

    return implode( ',', rinascente_normalize_keyword_list( $keywords ) );
}
add_filter( 'rank_math/frontend/keywords', 'rinascente_rank_math_dynamic_keywords', 30 );

function rinascente_rank_math_facebook_title_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'og_title' );
}
add_filter( 'rank_math/opengraph/facebook/og_title', 'rinascente_rank_math_facebook_title_override' );

function rinascente_rank_math_brand_og_title( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/facebook/og_title', 'rinascente_rank_math_brand_og_title', 30 );

function rinascente_rank_math_facebook_description_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'og_description' );
}
add_filter( 'rank_math/opengraph/facebook/og_description', 'rinascente_rank_math_facebook_description_override' );

function rinascente_rank_math_brand_og_description( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/facebook/og_description', 'rinascente_rank_math_brand_og_description', 30 );

function rinascente_rank_math_twitter_title_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'twitter_title' );
}
add_filter( 'rank_math/opengraph/twitter/twitter_title', 'rinascente_rank_math_twitter_title_override' );

function rinascente_rank_math_brand_twitter_title( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/twitter/twitter_title', 'rinascente_rank_math_brand_twitter_title', 30 );

function rinascente_rank_math_twitter_description_override( $value ) {
    return rinascente_rank_math_override_meta_value( $value, 'twitter_description' );
}
add_filter( 'rank_math/opengraph/twitter/twitter_description', 'rinascente_rank_math_twitter_description_override' );

function rinascente_rank_math_brand_twitter_description( $value ) {
    return rinascente_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/twitter/twitter_description', 'rinascente_rank_math_brand_twitter_description', 30 );

function rinascente_sanitize_pending_mica30_json_ld( $value ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $item ) {
            $value[ $key ] = rinascente_sanitize_pending_mica30_json_ld( $item );
        }

        return $value;
    }

    if ( ! is_string( $value ) || ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) ) {
        return $value;
    }

    $value = str_replace(
        array(
            '歩行リハビリ支援システムYUMEHO・造影剤注入装置MICA30',
            '歩行リハビリ支援システムYUMEHO・造影剤注入装置',
            'YUMEHO・MICA30の',
            'YUMEHO・MICA30を',
            'YUMEHO・MICA30',
            'YUMEHO / MICA30',
            'YUMEHO,MICA30',
            '造影剤注入装置MICA30',
            'MICA30',
        ),
        array(
            '歩行リハビリ支援システム YUMEHO',
            '歩行リハビリ支援システム YUMEHO',
            'YUMEHOの',
            'YUMEHOを',
            'YUMEHO',
            'YUMEHO',
            'YUMEHO',
            '',
            '',
        ),
        $value
    );

    $value = str_replace(
        array(
            '・造影剤注入装置',
            '造影剤注入装置',
        ),
        '',
        $value
    );

    $value = preg_replace( '/\s{2,}/u', ' ', $value );
    $value = preg_replace( '/,{2,}/u', ',', $value );
    $value = preg_replace( '/・{2,}/u', '・', $value );
    $value = preg_replace( '/([A-Za-z])\s+の/u', '$1の', $value );

    return trim( (string) $value, " \t\n\r\0\x0B,・/" );
}

function rinascente_rank_math_json_ld_override_pending_mica30( $data, $jsonld ) {
    if ( empty( rinascente_pending_mica30_seo_overrides() ) ) {
        return $data;
    }

    return rinascente_sanitize_pending_mica30_json_ld( $data );
}
add_filter( 'rank_math/json_ld', 'rinascente_rank_math_json_ld_override_pending_mica30', 25, 2 );

function rinascente_resolve_legacy_html_target( $path ) {
    $normalized = trim( (string) $path, '/' );
    if ( '' === $normalized ) {
        return '';
    }

    foreach ( array( 'rinascentes', 'rinascente' ) as $prefix ) {
        if ( $normalized === $prefix || str_starts_with( $normalized, $prefix . '/' ) ) {
            $normalized = ltrim( substr( $normalized, strlen( $prefix ) ), '/' );
            break;
        }
    }

    $normalized = preg_replace( '/\.html?$/i', '', $normalized );
    if ( '' === $normalized || 'index' === $normalized ) {
        return home_url( '/' );
    }

    $archive_map = array(
        'press' => get_post_type_archive_link( 'news' ),
        'cases' => get_post_type_archive_link( 'case_study' ),
    );

    if ( isset( $archive_map[ $normalized ] ) && $archive_map[ $normalized ] ) {
        return $archive_map[ $normalized ];
    }

    $lookups = array( $normalized );
    $leaf    = wp_basename( $normalized );
    if ( $leaf && $leaf !== $normalized ) {
        $lookups[] = $leaf;
    }

    foreach ( array( 'page', 'news', 'case_study' ) as $post_type ) {
        foreach ( $lookups as $lookup ) {
            $post = get_page_by_path( $lookup, OBJECT, $post_type );
            if ( $post instanceof WP_Post ) {
                return get_permalink( $post );
            }
        }
    }

    return '';
}

function rinascente_redirect_legacy_html_requests() {
    if ( is_admin() || wp_doing_ajax() || is_feed() || is_preview() ) {
        return;
    }

    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return;
    }

    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
    $path        = wp_parse_url( $request_uri, PHP_URL_PATH );
    if ( ! is_string( $path ) || ! preg_match( '/\.html?$/i', $path ) ) {
        return;
    }

    $target = rinascente_resolve_legacy_html_target( $path );
    if ( ! $target ) {
        return;
    }

    $requested_url = home_url( $path );
    if ( untrailingslashit( $requested_url ) === untrailingslashit( $target ) ) {
        return;
    }

    wp_safe_redirect( $target, 301 );
    exit;
}
add_action( 'template_redirect', 'rinascente_redirect_legacy_html_requests', 1 );

function rinascente_cookie_consent_state() {
    if ( empty( $_COOKIE['rinascente_cookie_consent'] ) ) {
        return '';
    }

    return sanitize_key( wp_unslash( $_COOKIE['rinascente_cookie_consent'] ) );
}

function rinascente_tracking_allowed() {
    return 'accepted' === rinascente_cookie_consent_state();
}

function rinascente_render_cookie_banner() {
    if ( is_admin() || wp_doing_ajax() || rinascente_cookie_consent_state() ) {
        return;
    }

    $privacy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
    $terms_page      = get_page_by_path( 'terms' );
    $privacy_url     = $privacy_page_id ? get_permalink( $privacy_page_id ) : home_url( '/privacy-policy/' );
    $terms_url       = $terms_page ? get_permalink( $terms_page ) : home_url( '/terms/' );
    ?>
    <div class="cookie-consent" id="rinascenteCookieConsent" role="dialog" aria-live="polite" aria-label="Cookie利用の確認">
      <div class="cookie-consent__body">
        <div class="cookie-consent__text">
          サイト改善とアクセス解析のため Cookie を使用します。
          <a href="<?php echo esc_url( $privacy_url ); ?>">プライバシーポリシー</a>
          ・
          <a href="<?php echo esc_url( $terms_url ); ?>">利用規約</a>
          をご確認ください。
        </div>
        <div class="cookie-consent__actions">
          <button type="button" class="cookie-consent__btn cookie-consent__btn--ghost" data-consent="rejected">拒否</button>
          <button type="button" class="cookie-consent__btn" data-consent="accepted">同意</button>
        </div>
      </div>
    </div>
    <style>
      .cookie-consent { position:fixed; left:16px; right:16px; bottom:16px; z-index:9999; display:flex; justify-content:center; }
      .cookie-consent__body { width:min(100%, 680px); display:flex; align-items:center; justify-content:space-between; gap:12px; background:rgba(10,12,16,.94); color:#fff; padding:12px 14px; border-radius:14px; box-shadow:0 14px 36px rgba(0,0,0,.22); }
      .cookie-consent__text { font-size:.75rem; line-height:1.5; color:rgba(255,255,255,.86); }
      .cookie-consent__text a { color:#f0d6a8; text-decoration:underline; }
      .cookie-consent__actions { display:flex; align-items:center; gap:8px; flex-shrink:0; }
      .cookie-consent__btn { appearance:none; border:0; border-radius:999px; padding:9px 14px; font-size:.7rem; font-weight:700; letter-spacing:.08em; cursor:pointer; background:#f0d6a8; color:#111; min-height:36px; min-width:84px; }
      .cookie-consent__btn--ghost { background:rgba(255,255,255,.08); color:#fff; }
      @media (max-width: 767px) {
        .cookie-consent { left:10px; right:10px; bottom:10px; }
        .cookie-consent__body { flex-direction:column; align-items:stretch; gap:10px; padding:10px 12px; border-radius:12px; }
        .cookie-consent__text { font-size:.7rem; line-height:1.45; }
        .cookie-consent__actions { width:100%; display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:8px; }
        .cookie-consent__btn { width:100%; min-width:0; padding:9px 10px; font-size:.68rem; min-height:34px; }
      }
    </style>
    <script>
      (function() {
        var root = document.getElementById('rinascenteCookieConsent');
        if (!root) return;
        root.querySelectorAll('[data-consent]').forEach(function(button) {
          button.addEventListener('click', function() {
            var value = button.getAttribute('data-consent');
            var secure = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = 'rinascente_cookie_consent=' + value + '; Max-Age=15552000; Path=/; SameSite=Lax' + secure;
            window.location.reload();
          });
        });
      })();
    </script>
    <?php
}
add_action( 'wp_footer', 'rinascente_render_cookie_banner', 100 );

add_filter( 'rank_math/frontend/show_keywords', '__return_true' );

function rinascente_schema_org() {
    $company         = rinascente_company_info_payload();
    $mica30_enabled = function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled();
    $brands         = array(
        array(
            '@type'       => 'Brand',
            'name'        => 'YUMEHO',
            'alternateName' => array( '夢歩' ),
            'url'         => rinascente_related_site_url( 'yumeho' ),
            'description' => '歩行リハビリ支援システム',
        ),
    );
    $knows_about    = array(
        '歩行リハビリテーション',
        '医療機器',
        '福祉機器',
        '介護ロボット',
    );

    if ( $mica30_enabled ) {
        $brands[]      = array(
            '@type'       => 'Brand',
            'name'        => 'MICA30',
            'description' => '造影剤注入装置',
        );
        $knows_about[] = '造影剤注入装置';
    }

    return array(
        '@type'          => 'Organization',
        '@id'            => home_url( '/' ) . '#organization',
        'name'           => $company['company_name'] ?? get_theme_mod( 'company_name', '株式会社リナシェンテ' ),
        'legalName'      => $company['company_name'] ?? get_theme_mod( 'company_name', '株式会社リナシェンテ' ),
        'alternateName'  => rinascente_site_brand_aliases(),
        'url'            => home_url( '/' ),
        'logo'           => array(
            '@type' => 'ImageObject',
            '@id'   => home_url( '/' ) . '#logo',
            'url'   => get_template_directory_uri() . '/assets/img/logo.svg',
        ),
        'description'    => $mica30_enabled ? '医療・福祉機器の企画・販売。YUMEHO・MICA30を展開。' : '医療・福祉機器の企画・販売。YUMEHO を展開。',
        'telephone'      => $company['company_tel'] ?? get_theme_mod( 'company_tel', '0859-00-1234' ),
        'foundingDate'   => preg_replace( '/[^0-9]/', '', (string) ( $company['company_founded'] ?? get_theme_mod( 'company_founded', '2026' ) ) ),
        'address'        => array(
            '@type'          => 'PostalAddress',
            'streetAddress'  => $company['company_address'] ?? get_theme_mod( 'company_address', '' ),
            'addressCountry' => 'JP',
        ),
        'contactPoint'   => array(
            '@type'             => 'ContactPoint',
            'telephone'         => $company['company_tel'] ?? get_theme_mod( 'company_tel', '0859-00-1234' ),
            'contactType'       => 'customer support',
            'availableLanguage' => 'Japanese',
        ),
        'brand'          => $brands,
        'knowsAbout'     => $knows_about,
    );
}

function rinascente_schema_article_publisher() {
    return array(
        '@id' => home_url( '/' ) . '#organization',
    );
}

function rinascente_term_entity( WP_Term $term ) {
    if ( 'product_type' === $term->taxonomy ) {
        if ( 'yumeho' === $term->slug ) {
            return array(
                '@type'         => 'Brand',
                'name'          => 'YUMEHO',
                'alternateName' => array( '夢歩' ),
                'url'           => rinascente_related_site_url( 'yumeho' ),
            );
        }

        return array(
            '@type' => 'Brand',
            'name'  => $term->name,
        );
    }

    return array(
        '@type'            => 'DefinedTerm',
        'name'             => $term->name,
        'inDefinedTermSet' => home_url( '/#' . $term->taxonomy ),
    );
}

function rinascente_post_schema_mentions( $post_id ) {
    $entities = array();

    foreach ( array( 'product_type', 'news_category', 'facility_type', 'case_format' ) as $taxonomy ) {
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            continue;
        }

        foreach ( $terms as $term ) {
            if ( $term instanceof WP_Term ) {
                $entities[ $term->taxonomy . ':' . $term->slug ] = rinascente_term_entity( $term );
            }
        }
    }

    return array_values( $entities );
}

function rinascente_add_page_schema( $data, $jsonld ) {
    $base_org = rinascente_schema_org();
    $data     = rinascente_enrich_existing_website_schema( $data );
    $data     = rinascente_enrich_existing_breadcrumb_schema( $data );
    $term     = rinascente_current_archive_term();

    if ( ! rinascente_schema_has_type( $data, 'WebSite' ) ) {
        $data['rinascente_site'] = rinascente_schema_website();
    }

    if ( ! rinascente_schema_has_type( $data, 'BreadcrumbList' ) ) {
        $breadcrumb = rinascente_schema_breadcrumb_list();
        if ( ! empty( $breadcrumb ) ) {
            $data['rinascente_breadcrumbs'] = $breadcrumb;
        }
    }

    if ( is_page( 'identity' ) ) {
        $data['rinascente_about_page'] = array(
            '@type'      => 'AboutPage',
            'name'       => get_the_title(),
            'url'        => get_permalink(),
            'description'=> 'Rinascenteの企業理念とブランドビジョン。',
            'about'      => $base_org,
            'isPartOf'   => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage' => 'ja-JP',
            'keywords'   => implode( ', ', rinascente_current_seo_keywords() ),
        );
    }

    if ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $data['rinascente_cases_page'] = array(
            '@type'       => 'CollectionPage',
            'name'        => '導入事例',
            'url'         => get_post_type_archive_link( 'case_study' ),
            'description' => ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) ? 'YUMEHO・MICA30の導入事例と成果実績を紹介する一覧ページです。' : 'YUMEHO の導入事例と成果実績を紹介する一覧ページです。',
            'about'       => $base_org,
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', rinascente_current_seo_keywords() ),
        );
    }

    if ( is_post_type_archive( 'news' ) || is_page( 'press' ) ) {
        $data['rinascente_press_page'] = array(
            '@type'       => 'CollectionPage',
            'name'        => 'Press',
            'url'         => get_post_type_archive_link( 'news' ),
            'description' => 'Rinascenteのニュース・プレスリリース一覧です。',
            'about'       => $base_org,
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', rinascente_current_seo_keywords() ),
        );
    }

    if ( $term && in_array( $term->taxonomy, array( 'column_category', 'news_category' ), true ) ) {
        $page_name = 'column_category' === $term->taxonomy ? 'コラム' : 'Press';

        $data[ 'rinascente_term_' . $term->taxonomy . '_' . $term->term_id ] = array(
            '@type'       => 'CollectionPage',
            'name'        => $term->name . ' | ' . $page_name,
            'url'         => get_term_link( $term ),
            'description' => rinascente_term_archive_fallback_description( $term ),
            'about'       => $base_org,
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', rinascente_current_seo_keywords() ),
        );
    }

    if ( is_page( 'contact' ) ) {
        $data['rinascente_contact_page'] = array(
            '@type'       => 'ContactPage',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => 'Rinascenteへのお問い合わせページです。',
            'publisher'   => rinascente_schema_article_publisher(),
            'about'       => $base_org,
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', rinascente_current_seo_keywords() ),
        );
    }

    return $data;
}
add_filter( 'rank_math/json_ld', 'rinascente_add_page_schema', 20, 2 );

function rinascente_enhance_article_schema( $entity ) {
    $post_id = get_queried_object_id();

    $entity['inLanguage']      = 'ja-JP';
    $entity['mainEntityOfPage'] = get_permalink( $post_id );
    $entity['isPartOf']        = array( '@id' => home_url( '/' ) . '#website' );
    $entity['keywords']        = implode( ', ', rinascente_current_seo_keywords() );

    $mentions = rinascente_post_schema_mentions( $post_id );
    if ( ! empty( $mentions ) ) {
        $entity['mentions'] = $mentions;
    }

    if ( is_singular( 'news' ) ) {
        $entity['@type']         = 'NewsArticle';
        $entity['publisher']     = rinascente_schema_article_publisher();
        $entity['datePublished'] = get_post_time( DATE_W3C, true );
        $entity['dateModified']  = get_post_modified_time( DATE_W3C, true );
    }

    if ( is_singular( 'case_study' ) ) {
        $facility_name = get_post_meta( get_the_ID(), '_rinascente_facility_name', true );
        if ( $facility_name ) {
            $entity['about'] = array(
                '@type' => 'MedicalOrganization',
                'name'  => $facility_name,
            );
        } elseif ( ! empty( $mentions ) ) {
            $entity['about'] = $mentions[0];
        }

        $entity['publisher'] = rinascente_schema_article_publisher();
    }

    return $entity;
}
add_filter( 'rank_math/snippet/rich_snippet_article_entity', 'rinascente_enhance_article_schema', 20 );

function rinascente_seed_core_taxonomy_terms() {
    $product_types = array(
        'yumeho' => 'YUMEHO',
    );
    if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) {
        $product_types['mica30'] = 'MICA30';
    }
    foreach ( $product_types as $slug => $label ) {
        if ( taxonomy_exists( 'product_type' ) && ! term_exists( $slug, 'product_type' ) ) {
            wp_insert_term( $label, 'product_type', array( 'slug' => $slug ) );
        }
    }

    $news_categories = array(
        'company'  => '会社情報',
        'product'  => '製品情報',
        'case'     => '導入事例',
        'award'    => '受賞・認証',
        'business' => '事業展開',
    );
    foreach ( $news_categories as $slug => $label ) {
        if ( taxonomy_exists( 'news_category' ) && ! term_exists( $slug, 'news_category' ) ) {
            wp_insert_term( $label, 'news_category', array( 'slug' => $slug ) );
        }
    }

    $facility_types = array(
        'hospital'      => '病院',
        'care-facility' => '介護老人保健施設',
        'dayservice'    => 'デイサービス',
        'clinic'        => 'クリニック',
    );
    foreach ( $facility_types as $slug => $label ) {
        if ( taxonomy_exists( 'facility_type' ) && ! term_exists( $slug, 'facility_type' ) ) {
            wp_insert_term( $label, 'facility_type', array( 'slug' => $slug ) );
        }
    }

    $case_formats = array(
        'case-study' => '導入事例',
        'voice'      => '施設の声',
    );
    foreach ( $case_formats as $slug => $label ) {
        if ( taxonomy_exists( 'case_format' ) && ! term_exists( $slug, 'case_format' ) ) {
            wp_insert_term( $label, 'case_format', array( 'slug' => $slug ) );
        }
    }
}
add_action( 'init', 'rinascente_seed_core_taxonomy_terms', 35 );
