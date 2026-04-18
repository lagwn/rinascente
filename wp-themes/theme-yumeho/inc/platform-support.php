<?php
/**
 * Platform support helpers for YUMEHO.
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yumeho_register_related_site_controls( $wp_customize ) {
    $wp_customize->add_section(
        'yumeho_related_sites',
        array(
            'title'    => '関連サイト URL',
            'priority' => 36,
        )
    );

    $fields = array(
        'related_corporate_url' => 'コーポレートサイト URL',
    );

    if ( function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled() ) {
        $fields['related_mica30_url'] = 'MICA30 サイト URL';
    }

    foreach ( $fields as $key => $label ) {
        $wp_customize->add_setting(
            'yumeho_' . $key,
            array(
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            )
        );
        $wp_customize->add_control(
            'yumeho_' . $key,
            array(
                'label'   => $label,
                'section' => 'yumeho_related_sites',
                'type'    => 'url',
            )
        );
    }

    $wp_customize->add_setting(
        'yumeho_related_sites_basic_auth_user',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'yumeho_related_sites_basic_auth_user',
        array(
            'label'       => '関連サイト Basic認証 ID',
            'description' => 'staging など、関連サイトに Basic 認証がある場合のみ入力します。',
            'section'     => 'yumeho_related_sites',
            'type'        => 'text',
        )
    );

    $wp_customize->add_setting(
        'yumeho_related_sites_basic_auth_pass',
        array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    $wp_customize->add_control(
        'yumeho_related_sites_basic_auth_pass',
        array(
            'label'   => '関連サイト Basic認証 PW',
            'section' => 'yumeho_related_sites',
            'type'    => 'password',
        )
    );
}
add_action( 'customize_register', 'yumeho_register_related_site_controls', 40 );

function yumeho_related_site_request_args() {
    $request_args = array(
        'timeout'            => 8,
        'reject_unsafe_urls' => true,
    );

    $basic_auth_user = trim( (string) get_theme_mod( 'yumeho_related_sites_basic_auth_user', '' ) );
    $basic_auth_pass = (string) get_theme_mod( 'yumeho_related_sites_basic_auth_pass', '' );

    if ( '' !== $basic_auth_user && '' !== $basic_auth_pass ) {
        $request_args['headers'] = array(
            'Authorization' => 'Basic ' . base64_encode( $basic_auth_user . ':' . $basic_auth_pass ),
        );
    }

    return $request_args;
}

function yumeho_default_social_image_url() {
    $preferred = array(
        'assets/img/ogp-yumeho.jpg',
        'assets/img/hero_visual.jpg',
        'assets/img/favicon.png',
    );

    foreach ( $preferred as $relative_path ) {
        $absolute_path = trailingslashit( get_template_directory() ) . ltrim( $relative_path, '/' );
        if ( file_exists( $absolute_path ) ) {
            return trailingslashit( get_template_directory_uri() ) . ltrim( $relative_path, '/' );
        }
    }

    return '';
}

function yumeho_filter_default_social_image( $image ) {
    if ( ! empty( $image ) ) {
        return $image;
    }

    return yumeho_default_social_image_url();
}
add_filter( 'rank_math/opengraph/facebook/image', 'yumeho_filter_default_social_image' );
add_filter( 'rank_math/opengraph/twitter/image', 'yumeho_filter_default_social_image' );

function yumeho_site_brand_name() {
    return 'YUMEHO';
}

function yumeho_site_brand_aliases() {
    $aliases = array( '夢歩' );
    $host    = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

    if ( is_string( $host ) ) {
        $host = strtolower( preg_replace( '/^www\./', '', $host ) );
        if ( $host && ! preg_match( '/(^|\.)(local|localhost)$/', $host ) ) {
            $aliases[] = $host;
        }
    }

    return array_values( array_unique( array_filter( array_map( 'trim', $aliases ) ) ) );
}

function yumeho_brand_display_name() {
    return 'YUMEHO（夢歩）';
}

function yumeho_text_contains( $text, $needle ) {
    if ( ! is_string( $text ) || ! is_string( $needle ) || '' === $text || '' === $needle ) {
        return false;
    }

    if ( function_exists( 'mb_stripos' ) ) {
        return false !== mb_stripos( $text, $needle, 0, 'UTF-8' );
    }

    return false !== stripos( $text, $needle );
}

function yumeho_enrich_brand_mentions( $text ) {
    if ( ! is_string( $text ) || '' === trim( $text ) ) {
        return $text;
    }

    if ( yumeho_text_contains( $text, yumeho_brand_display_name() ) ) {
        return $text;
    }

    if ( yumeho_text_contains( $text, 'YUMEHO' ) && ! yumeho_text_contains( $text, '夢歩' ) ) {
        return (string) preg_replace( '/YUMEHO/u', yumeho_brand_display_name(), $text, 1 );
    }

    if ( yumeho_text_contains( $text, '夢歩' ) && ! yumeho_text_contains( $text, 'YUMEHO' ) ) {
        return (string) preg_replace( '/夢歩/u', yumeho_brand_display_name(), $text, 1 );
    }

    return $text;
}

function yumeho_normalize_keyword_list( $keywords ) {
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

function yumeho_post_term_keywords( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
    if ( is_wp_error( $terms ) ) {
        return array();
    }

    return yumeho_normalize_keyword_list( $terms );
}

function yumeho_current_archive_term() {
    $term = get_queried_object();
    return ( $term instanceof WP_Term ) ? $term : null;
}

function yumeho_term_archive_fallback_description( $term = null ) {
    $term = $term instanceof WP_Term ? $term : yumeho_current_archive_term();
    if ( ! $term ) {
        return '';
    }

    $description = trim( wp_strip_all_tags( (string) $term->description ) );
    if ( '' !== $description ) {
        return $description;
    }

    switch ( $term->taxonomy ) {
        case 'facility_type':
            return sprintf( '%sでの YUMEHO（夢歩）導入事例をまとめた一覧です。運用方法や導入後の変化、現場での活用イメージを確認できます。', $term->name );

        case 'case_format':
            return sprintf( 'YUMEHO（夢歩）の「%s」に関する導入事例一覧です。導入背景や訓練体制、現場での変化をまとめて確認できます。', $term->name );
    }

    return '';
}

function yumeho_current_seo_keywords() {
    $keywords = array( 'YUMEHO', '夢歩' );

    if ( is_front_page() || is_home() ) {
        $keywords = array_merge( $keywords, array( '歩行リハビリ支援システム', '歩行訓練', '転倒防止', '免荷装置', '病院', '介護施設' ) );
    } elseif ( is_page( 'product' ) ) {
        $keywords = array_merge( $keywords, array( '製品紹介', '天井直付型', 'スタンド型', 'FCW-3000', 'PGT-9000', 'PGT-9001' ) );
    } elseif ( is_page( 'simulation' ) ) {
        $keywords = array_merge( $keywords, array( '導入シミュレーション', '見積', '概算費用', '構成診断' ) );
    } elseif ( is_page( 'flow' ) ) {
        $keywords = array_merge( $keywords, array( '導入の流れ', '現地調査', '設置工事', '操作研修' ) );
    } elseif ( is_page( 'price' ) ) {
        $keywords = array_merge( $keywords, array( '価格', '見積', 'リース', 'ROI', '導入費用' ) );
    } elseif ( is_page( 'subsidy' ) ) {
        $keywords = array_merge( $keywords, array( '補助金', '助成金', '介護ロボット導入支援', '申請サポート' ) );
    } elseif ( is_page( 'faq' ) ) {
        $keywords = array_merge( $keywords, array( 'FAQ', 'よくある質問', '設置', '費用', 'デモ' ) );
    } elseif ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $keywords = array_merge( $keywords, array( '導入事例', '病院', '介護施設', '施設の声' ) );
    } elseif ( is_page( 'contact' ) ) {
        $keywords = array_merge( $keywords, array( '資料請求', 'お問い合わせ', 'カタログ', 'デモ依頼' ) );
    } elseif ( is_page( 'company' ) ) {
        $keywords = array_merge( $keywords, array( 'Rinascente', 'リナシェンテ', '会社概要' ) );
    }

    if ( is_singular( 'case_study' ) ) {
        $post_id    = get_queried_object_id();
        $keywords[] = get_the_title( $post_id );
        $keywords   = array_merge(
            $keywords,
            yumeho_post_term_keywords( $post_id, 'product_type' ),
            yumeho_post_term_keywords( $post_id, 'facility_type' ),
            yumeho_post_term_keywords( $post_id, 'case_format' )
        );

        $facility_name = get_post_meta( $post_id, '_yumeho_facility_name', true );
        if ( $facility_name ) {
            $keywords[] = $facility_name;
        }
    }

    $term = yumeho_current_archive_term();
    if ( $term ) {
        $keywords[] = $term->name;

        switch ( $term->taxonomy ) {
            case 'facility_type':
                $keywords = array_merge( $keywords, array( '導入事例', '施設別導入事例', '歩行訓練', '歩行リハビリ' ) );
                break;

            case 'case_format':
                $keywords = array_merge( $keywords, array( '導入事例', '現場事例', '訓練体制', '導入背景' ) );
                break;
        }
    }

    return array_slice( yumeho_normalize_keyword_list( $keywords ), 0, 16 );
}

function yumeho_internal_pathway_links( $context = 'default' ) {
    $links = array();

    switch ( $context ) {
        case 'faq':
            $links = array(
                array(
                    'eyebrow'     => '製品構成',
                    'title'       => '設置条件と製品構成をまとめて確認する',
                    'url'         => home_url( '/product/' ),
                    'description' => '天井直付型とスタンド型の違い、設置面積、対応ハーネスをひとまとめで確認できます。',
                ),
                array(
                    'eyebrow'     => '費用感',
                    'title'       => '価格と導入費用の目安を確認する',
                    'url'         => home_url( '/price/' ),
                    'description' => '製品構成ごとの価格感や、導入前に見ておきたい費用の考え方を確認できます。',
                ),
                array(
                    'eyebrow'     => '補助制度',
                    'title'       => '補助金・助成金の活用方法を見る',
                    'url'         => home_url( '/subsidy/' ),
                    'description' => '介護ロボット導入支援や申請準備の流れを、導入検討の初期段階から把握できます。',
                ),
                array(
                    'eyebrow'     => '現場事例',
                    'title'       => '導入事例から運用イメージをつかむ',
                    'url'         => home_url( '/cases/' ),
                    'description' => '病院や介護施設での使い方、訓練体制、導入後の変化を具体的に確認できます。',
                ),
            );
            break;

        case 'case_study':
            $links = array(
                array(
                    'eyebrow'     => '他施設比較',
                    'title'       => '他の導入事例も見て比較する',
                    'url'         => home_url( '/cases/' ),
                    'description' => '施設種別や運用体制の違いを見比べながら、自施設に近いパターンを探せます。',
                ),
                array(
                    'eyebrow'     => '費用感',
                    'title'       => '価格と導入費用の考え方を確認する',
                    'url'         => home_url( '/price/' ),
                    'description' => '導入規模に応じた費用感や、見積前に整理したいポイントを確認できます。',
                ),
                array(
                    'eyebrow'     => '補助制度',
                    'title'       => '補助金・助成金の対象を確認する',
                    'url'         => home_url( '/subsidy/' ),
                    'description' => '予算計画に役立つ補助制度や、申請時に押さえたい条件を確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => '資料請求やデモ相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '施設条件に合わせた提案や、現場に合う構成の相談へそのまま進めます。',
                ),
            );
            break;

        case 'column':
            $links = array(
                array(
                    'eyebrow'     => 'FAQ',
                    'title'       => 'よくある質問をまとめて確認する',
                    'url'         => home_url( '/faq/' ),
                    'description' => '設置条件、費用、デモ、運用面でよくある確認事項を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => '現場事例',
                    'title'       => '導入事例で現場の使い方を見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'コラムの内容を実際の運用に落とし込んだときのイメージを、施設事例から確認できます。',
                ),
                array(
                    'eyebrow'     => '概算確認',
                    'title'       => '価格シミュレーションで概算を確認する',
                    'url'         => home_url( '/simulation/' ),
                    'description' => '施設タイプや運用条件に合わせて、導入効果と費用感の概算を短時間で把握できます。',
                ),
                array(
                    'eyebrow'     => '補助制度',
                    'title'       => '補助金の活用方法を確認する',
                    'url'         => home_url( '/subsidy/' ),
                    'description' => '導入検討のタイミングで合わせて確認したい補助制度や申請支援の流れを整理できます。',
                ),
            );
            break;

        case 'cases_archive':
            $links = array(
                array(
                    'eyebrow'     => '製品構成',
                    'title'       => '製品ページで設置タイプと構成を確認する',
                    'url'         => home_url( '/product/' ),
                    'description' => '天井直付型とスタンド型の違い、対応ハーネス、設置条件を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => '概算確認',
                    'title'       => '導入シミュレーションで概算を確認する',
                    'url'         => home_url( '/simulation/' ),
                    'description' => '施設タイプや運用条件に合わせて、費用感と導入効果の概算をすばやく把握できます。',
                ),
                array(
                    'eyebrow'     => 'FAQ',
                    'title'       => '設置や費用のよくある質問を見る',
                    'url'         => home_url( '/faq/' ),
                    'description' => '導入前によく確認される設置条件、費用、デモ、運用面の質問をまとめて確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => '資料請求や導入相談をする',
                    'url'         => home_url( '/contact/' ),
                    'description' => '施設条件に合わせた提案やデモ相談、具体的な導入検討へそのまま進めます。',
                ),
            );
            break;

        case 'column_archive':
            $links = array(
                array(
                    'eyebrow'     => 'FAQ',
                    'title'       => '導入前のよくある質問を確認する',
                    'url'         => home_url( '/faq/' ),
                    'description' => '設置条件、費用、デモ、運用面でよくある確認事項を一覧で確認できます。',
                ),
                array(
                    'eyebrow'     => '現場事例',
                    'title'       => '導入事例で現場の使い方を見る',
                    'url'         => home_url( '/cases/' ),
                    'description' => 'コラムの内容が実際の運用でどう活かされているかを、施設事例から確認できます。',
                ),
                array(
                    'eyebrow'     => '補助制度',
                    'title'       => '補助金・助成金の活用方法を確認する',
                    'url'         => home_url( '/subsidy/' ),
                    'description' => '導入検討の初期段階で整理したい補助制度や申請支援の流れを確認できます。',
                ),
                array(
                    'eyebrow'     => '相談',
                    'title'       => '資料請求や相談へ進む',
                    'url'         => home_url( '/contact/' ),
                    'description' => 'コラムで情報収集したあと、そのまま資料請求や個別相談につなげられます。',
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

function yumeho_render_internal_pathways( $context = 'default', $args = array() ) {
    $links = yumeho_internal_pathway_links( $context );
    if ( empty( $links ) ) {
        return;
    }

    $args = wp_parse_args(
        $args,
        array(
            'title' => '次に確認したいページ',
            'intro' => '導入検討を進めるときに一緒に見ておきたい情報をまとめました。',
        )
    );

    static $style_printed = false;
    if ( ! $style_printed ) {
        $style_printed = true;
        ?>
        <style>
        .yumeho-pathways {
            position: relative;
            left: 50%;
            width: min(1480px, calc(100vw - 72px));
            box-sizing: border-box;
            transform: translateX(-50%);
            margin-top: clamp(32px, 4vw, 56px);
            padding: clamp(28px, 4vw, 40px);
            background: linear-gradient(135deg, rgba(0,104,183,0.05), rgba(72,202,228,0.06));
            border: 1px solid rgba(0,104,183,0.10);
            border-radius: 16px;
        }
        .yumeho-pathways__header {
            margin-bottom: 24px;
        }
        .yumeho-pathways__eyebrow {
            display: inline-block;
            margin-bottom: 10px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--primary-color);
            text-transform: uppercase;
        }
        .yumeho-pathways__title {
            margin: 0 0 10px;
            font-size: clamp(1.15rem, 1.8vw, 1.4rem);
            font-weight: 700;
            color: var(--text-color);
        }
        .yumeho-pathways__intro {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.9;
            color: rgba(0,0,0,0.7);
        }
        .yumeho-pathways__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }
        .yumeho-pathways__card {
            display: block;
            padding: 20px 22px;
            background: #fff;
            border: 1px solid rgba(0,104,183,0.10);
            border-radius: 14px;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }
        .yumeho-pathways__card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.06);
            border-color: rgba(0,104,183,0.22);
        }
        .yumeho-pathways__card-eyebrow {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--primary-color);
        }
        .yumeho-pathways__card-title {
            display: block;
            margin-bottom: 10px;
            font-size: 0.96rem;
            font-weight: 700;
            line-height: 1.7;
            color: var(--text-color);
        }
        .yumeho-pathways__card-desc {
            display: block;
            font-size: 0.84rem;
            line-height: 1.8;
            color: rgba(0,0,0,0.68);
        }
        @media (max-width: 767px) {
            .yumeho-pathways {
                width: calc(100vw - 32px);
                padding: 24px 20px;
            }
            .yumeho-pathways__grid {
                grid-template-columns: 1fr;
            }
        }
        </style>
        <?php
    }
    ?>
    <div class="yumeho-pathways">
        <div class="yumeho-pathways__header">
            <span class="yumeho-pathways__eyebrow">Next Steps</span>
            <h2 class="yumeho-pathways__title"><?php echo esc_html( $args['title'] ); ?></h2>
            <p class="yumeho-pathways__intro"><?php echo esc_html( $args['intro'] ); ?></p>
        </div>
        <div class="yumeho-pathways__grid">
            <?php foreach ( $links as $link ) : ?>
            <a href="<?php echo esc_url( $link['url'] ); ?>" class="yumeho-pathways__card">
                <span class="yumeho-pathways__card-eyebrow"><?php echo esc_html( $link['eyebrow'] ); ?></span>
                <span class="yumeho-pathways__card-title"><?php echo esc_html( $link['title'] ); ?></span>
                <span class="yumeho-pathways__card-desc"><?php echo esc_html( $link['description'] ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

function yumeho_schema_has_type( $data, $type ) {
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

function yumeho_breadcrumb_items() {
    if ( is_front_page() || is_home() ) {
        return array();
    }

    $items = array(
        array(
            'name' => 'Home',
            'url'  => home_url( '/' ),
        ),
    );

    if ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $items[] = array(
            'name' => '導入事例',
            'url'  => get_post_type_archive_link( 'case_study' ) ?: get_permalink(),
        );
        return $items;
    }

    if ( is_tax( array( 'facility_type', 'case_format' ) ) ) {
        $term    = yumeho_current_archive_term();
        $items[] = array(
            'name' => '導入事例',
            'url'  => get_post_type_archive_link( 'case_study' ) ?: home_url( '/cases/' ),
        );
        if ( $term ) {
            $items[] = array(
                'name' => $term->name,
                'url'  => get_term_link( $term ),
            );
        }
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

function yumeho_schema_breadcrumb_list() {
    $items = yumeho_breadcrumb_items();
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

function yumeho_schema_website() {
    return array(
        '@type'         => 'WebSite',
        '@id'           => home_url( '/' ) . '#website',
        'url'           => home_url( '/' ),
        'name'          => yumeho_site_brand_name(),
        'alternateName' => yumeho_site_brand_aliases(),
        'inLanguage'    => 'ja-JP',
        'publisher'     => array(
            '@id' => yumeho_related_site_url( 'corporate' ) . '#organization',
        ),
    );
}

function yumeho_enrich_existing_website_schema( $data ) {
    foreach ( (array) $data as $key => $entity ) {
        if ( ! is_array( $entity ) || empty( $entity['@type'] ) ) {
            continue;
        }

        $entity_types = is_array( $entity['@type'] ) ? $entity['@type'] : array( $entity['@type'] );
        if ( ! in_array( 'WebSite', $entity_types, true ) ) {
            continue;
        }

        $data[ $key ]['name']          = yumeho_site_brand_name();
        $data[ $key ]['alternateName'] = yumeho_site_brand_aliases();
        $data[ $key ]['inLanguage']    = 'ja-JP';
        if ( empty( $data[ $key ]['publisher'] ) ) {
            $data[ $key ]['publisher'] = array( '@id' => yumeho_related_site_url( 'corporate' ) . '#organization' );
        }
    }

    return $data;
}

function yumeho_enrich_existing_breadcrumb_schema( $data ) {
    $breadcrumb = yumeho_schema_breadcrumb_list();
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

function yumeho_pending_mica30_seo_overrides() {
    if ( ! function_exists( 'yumeho_mica30_enabled' ) || yumeho_mica30_enabled() ) {
        return array();
    }

    if ( is_page( 'company' ) ) {
        return array(
            'description'         => '株式会社Rinascente（リナシェンテ）の会社概要。医療・福祉の現場で「再生」をテーマに、YUMEHO 歩行リハビリ支援システムの企画・販売を行っています。',
            'og_description'      => '医療・福祉機器の企画・販売。YUMEHO の開発元。再生をテーマに現場課題を解決。',
            'twitter_description' => '医療・福祉機器の企画・販売。YUMEHO の開発元。再生をテーマに現場課題を解決。',
            'keywords'            => 'Rinascente,リナシェンテ,会社概要,医療機器,福祉機器,YUMEHO',
        );
    }

    return array();
}

function yumeho_rank_math_override_meta_value( $value, $key ) {
    $overrides = yumeho_pending_mica30_seo_overrides();
    if ( isset( $overrides[ $key ] ) && '' !== (string) $overrides[ $key ] ) {
        return $overrides[ $key ];
    }

    return $value;
}

function yumeho_rank_math_description_override( $value ) {
    return yumeho_rank_math_override_meta_value( $value, 'description' );
}
add_filter( 'rank_math/frontend/description', 'yumeho_rank_math_description_override' );

function yumeho_rank_math_brand_title_enrichment( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/frontend/title', 'yumeho_rank_math_brand_title_enrichment', 30 );

function yumeho_rank_math_brand_description_enrichment( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/frontend/description', 'yumeho_rank_math_brand_description_enrichment', 30 );

function yumeho_rank_math_keywords_override( $value ) {
    return yumeho_rank_math_override_meta_value( $value, 'keywords' );
}
add_filter( 'rank_math/frontend/keywords', 'yumeho_rank_math_keywords_override' );

function yumeho_rank_math_dynamic_keywords( $value ) {
    $keywords = array_merge(
        yumeho_normalize_keyword_list( $value ),
        yumeho_current_seo_keywords()
    );

    return implode( ',', yumeho_normalize_keyword_list( $keywords ) );
}
add_filter( 'rank_math/frontend/keywords', 'yumeho_rank_math_dynamic_keywords', 30 );

function yumeho_rank_math_facebook_description_override( $value ) {
    return yumeho_rank_math_override_meta_value( $value, 'og_description' );
}
add_filter( 'rank_math/opengraph/facebook/og_description', 'yumeho_rank_math_facebook_description_override' );

function yumeho_rank_math_brand_og_title( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/facebook/og_title', 'yumeho_rank_math_brand_og_title', 30 );

function yumeho_rank_math_brand_og_description( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/facebook/og_description', 'yumeho_rank_math_brand_og_description', 30 );

function yumeho_rank_math_twitter_description_override( $value ) {
    return yumeho_rank_math_override_meta_value( $value, 'twitter_description' );
}
add_filter( 'rank_math/opengraph/twitter/twitter_description', 'yumeho_rank_math_twitter_description_override' );

function yumeho_rank_math_brand_twitter_title( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/twitter/twitter_title', 'yumeho_rank_math_brand_twitter_title', 30 );

function yumeho_rank_math_brand_twitter_description( $value ) {
    return yumeho_enrich_brand_mentions( $value );
}
add_filter( 'rank_math/opengraph/twitter/twitter_description', 'yumeho_rank_math_brand_twitter_description', 30 );

function yumeho_sanitize_pending_mica30_json_ld( $value ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $item ) {
            $value[ $key ] = yumeho_sanitize_pending_mica30_json_ld( $item );
        }

        return $value;
    }

    if ( ! is_string( $value ) || ( function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled() ) ) {
        return $value;
    }

    $value = str_replace(
        array(
            'YUMEHO歩行リハビリ支援システム・MICA30造影剤注入装置',
            'YUMEHO・MICA30',
            'YUMEHO,MICA30',
            'MICA30',
        ),
        array(
            'YUMEHO 歩行リハビリ支援システム',
            'YUMEHO',
            'YUMEHO',
            '',
        ),
        $value
    );

    $value = str_replace( '・造影剤注入装置', '', $value );
    $value = preg_replace( '/\s{2,}/u', ' ', $value );
    $value = preg_replace( '/,{2,}/u', ',', $value );

    return trim( (string) $value, " \t\n\r\0\x0B,・/" );
}

function yumeho_rank_math_json_ld_override_pending_mica30( $data, $jsonld ) {
    if ( empty( yumeho_pending_mica30_seo_overrides() ) ) {
        return $data;
    }

    return yumeho_sanitize_pending_mica30_json_ld( $data );
}
add_filter( 'rank_math/json_ld', 'yumeho_rank_math_json_ld_override_pending_mica30', 25, 2 );

function yumeho_resolve_legacy_html_target( $path ) {
    $normalized = trim( (string) $path, '/' );
    if ( '' === $normalized ) {
        return '';
    }

    if ( 'yumeho' === $normalized || str_starts_with( $normalized, 'yumeho/' ) ) {
        $normalized = ltrim( substr( $normalized, strlen( 'yumeho' ) ), '/' );
    }

    $normalized = preg_replace( '/\.html?$/i', '', $normalized );
    if ( '' === $normalized || 'index' === $normalized ) {
        return home_url( '/' );
    }

    $archive_map = array(
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

    foreach ( array( 'page', 'case_study', 'faq' ) as $post_type ) {
        foreach ( $lookups as $lookup ) {
            $post = get_page_by_path( $lookup, OBJECT, $post_type );
            if ( $post instanceof WP_Post ) {
                return get_permalink( $post );
            }
        }
    }

    return '';
}

function yumeho_redirect_legacy_html_requests() {
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

    $target = yumeho_resolve_legacy_html_target( $path );
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
add_action( 'template_redirect', 'yumeho_redirect_legacy_html_requests', 1 );

function yumeho_shared_company_data() {
    static $runtime_cache = null;
    if ( is_array( $runtime_cache ) ) {
        return $runtime_cache;
    }

    $cache_key = 'yumeho_shared_company_data';
    $cached    = get_transient( $cache_key );
    if ( false !== $cached && is_array( $cached ) ) {
        $runtime_cache = $cached;
        return $cached;
    }

    $corporate_base = get_theme_mod( 'yumeho_related_corporate_url', '' );
    if ( ! $corporate_base ) {
        $corporate_base = get_theme_mod( 'related_corporate_url', '' );
    }

    if ( ! $corporate_base ) {
        $host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
        if ( $host && ( str_ends_with( $host, '.local' ) || 'localhost' === $host ) ) {
            $corporate_base = 'http://rinascente.local/';
        }
    }

    if ( ! $corporate_base ) {
        $runtime_cache = array();
        return $runtime_cache;
    }

    $response = wp_remote_get(
        untrailingslashit( $corporate_base ) . '/wp-json/rinascente/v1/company-info',
        yumeho_related_site_request_args()
    );

    if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        $runtime_cache = array();
        return $runtime_cache;
    }

    $payload = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $payload ) ) {
        $runtime_cache = array();
        return $runtime_cache;
    }

    set_transient( $cache_key, $payload, 15 * MINUTE_IN_SECONDS );
    $runtime_cache = $payload;
    return $runtime_cache;
}

function yumeho_cookie_consent_state() {
    if ( empty( $_COOKIE['yumeho_cookie_consent'] ) ) {
        return '';
    }

    return sanitize_key( wp_unslash( $_COOKIE['yumeho_cookie_consent'] ) );
}

function yumeho_tracking_allowed() {
    return 'accepted' === yumeho_cookie_consent_state();
}

function yumeho_render_cookie_banner() {
    if ( is_admin() || wp_doing_ajax() || yumeho_cookie_consent_state() ) {
        return;
    }

    $privacy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
    $terms_page      = get_page_by_path( 'terms' );
    $privacy_url     = $privacy_page_id ? get_permalink( $privacy_page_id ) : home_url( '/privacy-policy/' );
    $terms_url       = $terms_page ? get_permalink( $terms_page ) : home_url( '/terms/' );
    ?>
    <div class="cookie-consent" id="yumehoCookieConsent" role="dialog" aria-live="polite" aria-label="Cookie利用の確認">
      <div class="cookie-consent__body">
        <div class="cookie-consent__text">
          解析とお問い合わせ改善のため Cookie を使用します。
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
      .cookie-consent__body { width:min(100%, 680px); display:flex; align-items:center; justify-content:space-between; gap:12px; background:rgba(7,17,28,.94); color:#fff; padding:12px 14px; border-radius:14px; box-shadow:0 14px 36px rgba(0,0,0,.22); }
      .cookie-consent__text { font-size:.75rem; line-height:1.5; color:rgba(255,255,255,.86); }
      .cookie-consent__text a { color:#8fd3ff; text-decoration:underline; }
      .cookie-consent__actions { display:flex; align-items:center; gap:8px; flex-shrink:0; }
      .cookie-consent__btn { appearance:none; border:0; border-radius:999px; padding:9px 14px; font-size:.7rem; font-weight:700; letter-spacing:.08em; cursor:pointer; background:#00a2ff; color:#fff; min-height:36px; min-width:84px; }
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
        var root = document.getElementById('yumehoCookieConsent');
        if (!root) return;
        root.querySelectorAll('[data-consent]').forEach(function(button) {
          button.addEventListener('click', function() {
            var value = button.getAttribute('data-consent');
            var secure = window.location.protocol === 'https:' ? '; Secure' : '';
            document.cookie = 'yumeho_cookie_consent=' + value + '; Max-Age=15552000; Path=/; SameSite=Lax' + secure;
            window.location.reload();
          });
        });
      })();
    </script>
    <?php
}
add_action( 'wp_footer', 'yumeho_render_cookie_banner', 100 );

add_filter( 'rank_math/frontend/show_keywords', '__return_true' );

function yumeho_register_case_format_taxonomy() {
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
}
add_action( 'init', 'yumeho_register_case_format_taxonomy', 20 );

function yumeho_seed_core_taxonomy_terms() {
    $product_terms = array(
        'yumeho' => 'YUMEHO',
    );
    if ( function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled() ) {
        $product_terms['mica30'] = 'MICA30';
    }
    foreach ( $product_terms as $slug => $label ) {
        if ( taxonomy_exists( 'product_type' ) && ! term_exists( $slug, 'product_type' ) ) {
            wp_insert_term( $label, 'product_type', array( 'slug' => $slug ) );
        }
    }

    $facility_terms = array(
        'hospital'      => '病院',
        'care-facility' => '介護老人保健施設',
        'dayservice'    => 'デイサービス',
        'clinic'        => 'クリニック',
    );
    foreach ( $facility_terms as $slug => $label ) {
        if ( taxonomy_exists( 'facility_type' ) && ! term_exists( $slug, 'facility_type' ) ) {
            wp_insert_term( $label, 'facility_type', array( 'slug' => $slug ) );
        }
    }

    $faq_terms = array(
        'install'   => '導入・設置',
        'cost'      => '費用・補助金',
        'operation' => '運用・安全性',
        'demo'      => 'デモ・見学',
    );
    foreach ( $faq_terms as $slug => $label ) {
        if ( taxonomy_exists( 'faq_category' ) && ! term_exists( $slug, 'faq_category' ) ) {
            wp_insert_term( $label, 'faq_category', array( 'slug' => $slug ) );
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
add_action( 'init', 'yumeho_seed_core_taxonomy_terms', 35 );

function yumeho_schema_org() {
    $company = yumeho_shared_company_data();

    return array(
        '@type'        => 'Organization',
        '@id'          => yumeho_related_site_url( 'corporate' ) . '#organization',
        'name'         => $company['company_name'] ?? yumeho_theme_mod( 'company_name', '株式会社Rinascente' ),
        'legalName'    => $company['company_name'] ?? yumeho_theme_mod( 'company_name', '株式会社Rinascente' ),
        'alternateName'=> array( 'Rinascente', 'リナシェンテ' ),
        'url'          => yumeho_related_site_url( 'corporate' ),
        'telephone'    => $company['company_tel'] ?? yumeho_theme_mod( 'company_tel', '0859-00-1234' ),
        'address'      => array(
            '@type'          => 'PostalAddress',
            'streetAddress'  => $company['company_address'] ?? yumeho_theme_mod( 'company_address', '' ),
            'addressCountry' => 'JP',
        ),
        'contactPoint' => array(
            '@type'             => 'ContactPoint',
            'telephone'         => $company['company_tel'] ?? yumeho_theme_mod( 'company_tel', '0859-00-1234' ),
            'contactType'       => 'sales',
            'availableLanguage' => 'Japanese',
        ),
    );
}

function yumeho_schema_product_entity( $name = 'YUMEHO（夢歩）', $url = '' ) {
    if ( ! $url ) {
        $url = home_url( '/' );
    }

    return array(
        '@type'        => 'Product',
        '@id'          => home_url( '/' ) . '#product',
        'name'         => $name,
        'alternateName'=> yumeho_site_brand_aliases(),
        'url'          => $url,
        'description'  => '転倒リスクを物理的に防ぎ、両手フリーで多様なリハビリ課題を実現する歩行リハビリ支援システム。',
        'brand'        => array(
            '@type'         => 'Brand',
            'name'          => 'YUMEHO',
            'alternateName' => yumeho_site_brand_aliases(),
            'url'           => home_url( '/' ),
        ),
        'manufacturer' => array(
            '@id' => yumeho_related_site_url( 'corporate' ) . '#organization',
        ),
        'category'     => '医療機器・福祉機器',
        'audience'     => array(
            '@type'        => 'Audience',
            'audienceType' => '病院・介護施設・デイサービス',
        ),
    );
}

function yumeho_term_entity( WP_Term $term ) {
    if ( 'product_type' === $term->taxonomy ) {
        return array(
            '@type'         => 'Brand',
            'name'          => 'YUMEHO',
            'alternateName' => yumeho_site_brand_aliases(),
            'url'           => home_url( '/' ),
        );
    }

    return array(
        '@type'            => 'DefinedTerm',
        'name'             => $term->name,
        'inDefinedTermSet' => home_url( '/#' . $term->taxonomy ),
    );
}

function yumeho_post_schema_mentions( $post_id ) {
    $entities = array();

    foreach ( array( 'product_type', 'facility_type', 'case_format' ) as $taxonomy ) {
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( empty( $terms ) || is_wp_error( $terms ) ) {
            continue;
        }

        foreach ( $terms as $term ) {
            if ( $term instanceof WP_Term ) {
                $entities[ $term->taxonomy . ':' . $term->slug ] = yumeho_term_entity( $term );
            }
        }
    }

    return array_values( $entities );
}

function yumeho_add_page_schema( $data ) {
    $data = yumeho_enrich_existing_website_schema( $data );
    $data = yumeho_enrich_existing_breadcrumb_schema( $data );
    $term = yumeho_current_archive_term();

    if ( ! yumeho_schema_has_type( $data, 'WebSite' ) ) {
        $data['yumeho_site'] = yumeho_schema_website();
    }

    if ( ! yumeho_schema_has_type( $data, 'BreadcrumbList' ) ) {
        $breadcrumb = yumeho_schema_breadcrumb_list();
        if ( ! empty( $breadcrumb ) ) {
            $data['yumeho_breadcrumbs'] = $breadcrumb;
        }
    }

    if ( is_page( 'product' ) ) {
        $data['yumeho_product_page'] = array_merge(
            yumeho_schema_product_entity( 'YUMEHO（夢歩） 製品紹介', get_permalink() ),
            array(
                'isPartOf'   => array( '@id' => home_url( '/' ) . '#website' ),
                'inLanguage' => 'ja-JP',
                'keywords'   => implode( ', ', yumeho_current_seo_keywords() ),
            )
        );
    }

    if ( is_page( array( 'simulation', 'flow', 'price', 'subsidy' ) ) ) {
        $data['yumeho_' . get_post_field( 'post_name', get_the_ID() ) . '_page'] = array(
            '@type'       => 'WebPage',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => wp_strip_all_tags( get_the_excerpt() ?: get_the_title() ),
            'about'       => yumeho_schema_product_entity(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    if ( is_post_type_archive( 'case_study' ) || is_page( 'cases' ) ) {
        $data['yumeho_cases_page'] = array(
            '@type'       => 'CollectionPage',
            'name'        => '導入事例',
            'url'         => get_post_type_archive_link( 'case_study' ),
            'description' => 'YUMEHO の導入事例一覧ページです。',
            'about'       => yumeho_schema_product_entity(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    if ( $term && in_array( $term->taxonomy, array( 'facility_type', 'case_format' ), true ) ) {
        $data[ 'yumeho_term_' . $term->taxonomy . '_' . $term->term_id ] = array(
            '@type'       => 'CollectionPage',
            'name'        => $term->name . 'の導入事例',
            'url'         => get_term_link( $term ),
            'description' => yumeho_term_archive_fallback_description( $term ),
            'about'       => yumeho_schema_product_entity(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    if ( is_page( 'faq' ) ) {
        $faq_query = new WP_Query(
            array(
                'post_type'      => 'faq',
                'posts_per_page' => -1,
                'orderby'        => 'menu_order',
                'order'          => 'ASC',
                'fields'         => 'ids',
            )
        );
        $main_entity = array();
        foreach ( $faq_query->posts as $faq_id ) {
            $main_entity[] = array(
                '@type'          => 'Question',
                'name'           => get_the_title( $faq_id ),
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( get_post_field( 'post_content', $faq_id ) ),
                ),
            );
        }
        wp_reset_postdata();

        $data['yumeho_faq_page'] = array(
            '@type'       => 'FAQPage',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => 'YUMEHO に関するよくある質問の一覧です。',
            'mainEntity'  => $main_entity,
            'about'       => yumeho_schema_product_entity(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    if ( is_page( 'company' ) ) {
        $data['yumeho_company_page'] = array(
            '@type'       => 'AboutPage',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => '株式会社Rinascente の会社概要ページです。',
            'about'       => yumeho_schema_org(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    if ( is_page( 'contact' ) ) {
        $data['yumeho_contact_page'] = array(
            '@type'       => 'ContactPage',
            'name'        => get_the_title(),
            'url'         => get_permalink(),
            'description' => 'YUMEHO への資料請求・お問い合わせページです。',
            'publisher'   => yumeho_schema_org(),
            'about'       => yumeho_schema_product_entity(),
            'isPartOf'    => array( '@id' => home_url( '/' ) . '#website' ),
            'inLanguage'  => 'ja-JP',
            'keywords'    => implode( ', ', yumeho_current_seo_keywords() ),
        );
    }

    return $data;
}
add_filter( 'rank_math/json_ld', 'yumeho_add_page_schema', 20 );

function yumeho_enhance_article_schema( $entity ) {
    $post_id = get_queried_object_id();

    $entity['inLanguage']       = 'ja-JP';
    $entity['mainEntityOfPage'] = get_permalink( $post_id );
    $entity['isPartOf']         = array( '@id' => home_url( '/' ) . '#website' );
    $entity['keywords']         = implode( ', ', yumeho_current_seo_keywords() );

    $mentions = yumeho_post_schema_mentions( $post_id );
    if ( ! empty( $mentions ) ) {
        $entity['mentions'] = $mentions;
    }

    if ( is_singular( 'case_study' ) ) {
        $facility_name = get_post_meta( get_the_ID(), '_yumeho_facility_name', true );
        if ( $facility_name ) {
            $entity['about'] = array(
                '@type' => 'MedicalOrganization',
                'name'  => $facility_name,
            );
        } elseif ( ! empty( $mentions ) ) {
            $entity['about'] = $mentions[0];
        }

        $entity['publisher'] = yumeho_schema_org();
    }

    return $entity;
}
add_filter( 'rank_math/snippet/rich_snippet_article_entity', 'yumeho_enhance_article_schema', 20 );
