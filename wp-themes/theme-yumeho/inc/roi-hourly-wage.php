<?php
/**
 * ROI hourly wage helpers.
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function yumeho_roi_hourly_wage_fallback_data() {
    return array(
        'hourly_wage'     => 1323,
        'year_label'      => '令和6年',
        'survey_label'    => '厚生労働省「令和6年賃金構造基本統計調査（短時間労働者）第3表」',
        'occupation_label'=> '介護職員（医療・福祉施設等）',
        'source_url'      => 'https://www.e-stat.go.jp/stat-search/file-download?fileKind=4&statInfId=000040247975',
        'source_page_url' => 'https://www.mhlw.go.jp/toukei/list/chinginkouzou.html',
        'stat_inf_id'     => '000040247975',
        'resource_id'     => '',
        'retrieved_at'    => gmdate( 'c' ),
        'is_auto_resolved'=> false,
        'is_fallback'     => true,
    );
}

function yumeho_roi_hourly_wage_note_text( array $wage_data, $prefix = '※ 上記は導入施設の実績に基づく概算です。' ) {
    $fallback         = yumeho_roi_hourly_wage_fallback_data();
    $normalized       = wp_parse_args( $wage_data, $fallback );
    $hourly_wage      = number_format( (int) $normalized['hourly_wage'] );
    $survey_label     = trim( (string) $normalized['survey_label'] );
    $occupation_label = trim( (string) $normalized['occupation_label'] );
    $prefix           = trim( (string) $prefix );

    if ( $survey_label ) {
        return $prefix . '時給は' . $survey_label . 'における' . $occupation_label . 'の平均値（' . $hourly_wage . '円）で試算しています。';
    }

    return $prefix . '時給は' . $occupation_label . 'の平均値（' . $hourly_wage . '円）で試算しています。';
}

function yumeho_roi_cost_saving_man_yen( $hourly_wage, $hours_per_day, $reduced_staff, $days ) {
    $cost_saving = max( 0, (float) $hourly_wage ) * max( 0, (float) $hours_per_day ) * max( 0, (float) $reduced_staff ) * max( 0, (float) $days );

    return (int) round( $cost_saving / 10000 );
}

function yumeho_get_roi_hourly_wage_data( $force_refresh = false ) {
    $fallback   = yumeho_roi_hourly_wage_fallback_data();
    $cache_key  = 'yumeho_roi_hourly_wage_data_v1';
    $option_key = 'yumeho_roi_hourly_wage_last_known_v1';

    if ( ! $force_refresh ) {
        $cached = get_transient( $cache_key );
        if ( is_array( $cached ) && ! empty( $cached['hourly_wage'] ) ) {
            return wp_parse_args( $cached, $fallback );
        }
    }

    $latest = yumeho_fetch_roi_hourly_wage_data();
    if ( is_array( $latest ) && ! empty( $latest['hourly_wage'] ) ) {
        set_transient( $cache_key, $latest, 30 * DAY_IN_SECONDS );
        update_option( $option_key, $latest, false );
        return wp_parse_args( $latest, $fallback );
    }

    $last_known = get_option( $option_key, array() );
    if ( is_array( $last_known ) && ! empty( $last_known['hourly_wage'] ) ) {
        $last_known['is_fallback'] = true;
        return wp_parse_args( $last_known, $fallback );
    }

    return $fallback;
}

function yumeho_fetch_roi_hourly_wage_data() {
    $fallback = yumeho_roi_hourly_wage_fallback_data();
    $source_info = yumeho_resolve_roi_hourly_wage_source_info();
    $source_url  = $source_info['source_url'] ?? $fallback['source_url'];
    $response    = wp_remote_get(
        $source_url,
        array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'YUMEHO ROI wage fetcher/1.0',
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        return null;
    }

    $status_code = (int) wp_remote_retrieve_response_code( $response );
    $body        = wp_remote_retrieve_body( $response );

    if ( 200 !== $status_code || '' === $body ) {
        return null;
    }

    $parsed = yumeho_parse_estat_roi_hourly_wage_xlsx( $body, $fallback['occupation_label'] );
    if ( ! is_array( $parsed ) || empty( $parsed['hourly_wage'] ) ) {
        return null;
    }

    $year_label = ! empty( $parsed['year_label'] ) ? $parsed['year_label'] : $fallback['year_label'];

    return array(
        'hourly_wage'      => (int) $parsed['hourly_wage'],
        'year_label'       => $year_label,
        'survey_label'     => '厚生労働省「' . $year_label . '賃金構造基本統計調査（短時間労働者）第3表」',
        'occupation_label' => $fallback['occupation_label'],
        'source_url'       => $source_url,
        'source_page_url'  => $source_info['source_page_url'] ?? $fallback['source_page_url'],
        'stat_inf_id'      => $source_info['stat_inf_id'] ?? $fallback['stat_inf_id'],
        'resource_id'      => $source_info['resource_id'] ?? '',
        'retrieved_at'     => gmdate( 'c' ),
        'is_auto_resolved' => ! empty( $source_info['is_auto_resolved'] ),
        'is_fallback'      => false,
    );
}

function yumeho_resolve_roi_hourly_wage_source_info() {
    $fallback = yumeho_roi_hourly_wage_fallback_data();
    $app_id   = yumeho_get_estat_app_id();

    if ( '' === $app_id ) {
        return array(
            'source_url'       => $fallback['source_url'],
            'source_page_url'  => $fallback['source_page_url'],
            'stat_inf_id'      => $fallback['stat_inf_id'],
            'resource_id'      => '',
            'is_auto_resolved' => false,
        );
    }

    $query = array(
        'appId'      => $app_id,
        'lang'       => 'J',
        'statsCode'  => '00450091',
        'searchWord' => '賃金構造基本統計調査 AND 短時間労働者 AND 第３表 AND １時間当たり所定内給与額',
        'dataType'   => 'XLS,XLS_REP',
        'limit'      => 25,
    );

    $request_url = add_query_arg( $query, 'https://api.e-stat.go.jp/rest/3.0/app/json/getDataCatalog' );
    $response    = wp_remote_get(
        $request_url,
        array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'YUMEHO ROI wage resolver/1.0',
            ),
        )
    );

    if ( is_wp_error( $response ) ) {
        return array(
            'source_url'       => $fallback['source_url'],
            'source_page_url'  => $fallback['source_page_url'],
            'stat_inf_id'      => $fallback['stat_inf_id'],
            'resource_id'      => '',
            'is_auto_resolved' => false,
        );
    }

    $payload = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $payload ) ) {
        return array(
            'source_url'       => $fallback['source_url'],
            'source_page_url'  => $fallback['source_page_url'],
            'stat_inf_id'      => $fallback['stat_inf_id'],
            'resource_id'      => '',
            'is_auto_resolved' => false,
        );
    }

    $status = (int) ( $payload['GET_DATA_CATALOG']['RESULT']['STATUS'] ?? -1 );
    if ( 0 !== $status ) {
        return array(
            'source_url'       => $fallback['source_url'],
            'source_page_url'  => $fallback['source_page_url'],
            'stat_inf_id'      => $fallback['stat_inf_id'],
            'resource_id'      => '',
            'is_auto_resolved' => false,
        );
    }

    $catalog_entries = $payload['GET_DATA_CATALOG']['DATA_CATALOG_LIST_INF']['DATA_CATALOG_INF'] ?? array();
    if ( isset( $catalog_entries['@id'] ) ) {
        $catalog_entries = array( $catalog_entries );
    }

    $best_match = yumeho_pick_best_roi_hourly_wage_catalog_match( is_array( $catalog_entries ) ? $catalog_entries : array() );
    if ( empty( $best_match['source_url'] ) ) {
        return array(
            'source_url'       => $fallback['source_url'],
            'source_page_url'  => $fallback['source_page_url'],
            'stat_inf_id'      => $fallback['stat_inf_id'],
            'resource_id'      => '',
            'is_auto_resolved' => false,
        );
    }

    return array(
        'source_url'       => $best_match['source_url'],
        'source_page_url'  => $best_match['source_page_url'] ?? $fallback['source_page_url'],
        'stat_inf_id'      => $best_match['stat_inf_id'] ?? '',
        'resource_id'      => $best_match['resource_id'] ?? '',
        'is_auto_resolved' => true,
    );
}

function yumeho_pick_best_roi_hourly_wage_catalog_match( array $catalog_entries ) {
    $best_candidate = array();
    $best_sort_key  = null;

    foreach ( $catalog_entries as $catalog_entry ) {
        if ( ! is_array( $catalog_entry ) ) {
            continue;
        }

        $resources = $catalog_entry['RESOURCES']['RESOURCE'] ?? array();
        if ( isset( $resources['URL'] ) ) {
            $resources = array( $resources );
        }

        $dataset      = is_array( $catalog_entry['DATASET'] ?? null ) ? $catalog_entry['DATASET'] : array();
        $dataset_text = implode(
            "\n",
            array_filter(
                array(
                    yumeho_estat_text_value( $dataset['STAT_NAME'] ?? '' ),
                    yumeho_estat_text_value( $dataset['ORGANIZATION'] ?? '' ),
                    (string) ( $dataset['TITLE']['NAME'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_CATEGORY'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_SUB_CATEGORY1'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_SUB_CATEGORY2'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_SUB_CATEGORY3'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_SUB_CATEGORY4'] ?? '' ),
                    (string) ( $dataset['TITLE']['TABULATION_SUB_CATEGORY5'] ?? '' ),
                )
            )
        );

        foreach ( is_array( $resources ) ? $resources : array() as $resource ) {
            if ( ! is_array( $resource ) ) {
                continue;
            }

            $format = strtoupper( (string) ( $resource['FORMAT'] ?? '' ) );
            if ( ! in_array( $format, array( 'XLS', 'XLS_REP' ), true ) ) {
                continue;
            }

            $resource_text = implode(
                "\n",
                array_filter(
                    array(
                        (string) ( $resource['TITLE']['NAME'] ?? '' ),
                        (string) ( $resource['TITLE']['TABLE_NAME'] ?? '' ),
                        (string) ( $resource['TITLE']['TABLE_CATEGORY'] ?? '' ),
                        (string) ( $resource['TITLE']['TABLE_SUB_CATEGORY1'] ?? '' ),
                        (string) ( $resource['TITLE']['TABLE_SUB_CATEGORY2'] ?? '' ),
                        (string) ( $resource['TITLE']['TABLE_SUB_CATEGORY3'] ?? '' ),
                        (string) ( $resource['URL'] ?? '' ),
                    )
                )
            );

            $combined_text = $dataset_text . "\n" . $resource_text;
            $score         = yumeho_roi_catalog_match_score( $combined_text, $format, (string) ( $resource['URL'] ?? '' ) );
            $survey_date   = preg_replace( '/\D+/', '', (string) ( $dataset['TITLE']['SURVEY_DATE'] ?? '' ) );
            $release_date  = preg_replace( '/\D+/', '', (string) ( $resource['RELEASE_DATE'] ?? ( $dataset['RELEASE_DATE'] ?? '' ) ) );
            $updated_date  = preg_replace( '/\D+/', '', (string) ( $resource['LAST_MODIFIED_DATE'] ?? ( $dataset['LAST_MODIFIED_DATE'] ?? '' ) ) );
            $sort_key      = sprintf( '%04d-%08s-%08s', $score, $survey_date ?: '00000000', $updated_date ?: $release_date ?: '00000000' );

            if ( null === $best_sort_key || strcmp( $sort_key, $best_sort_key ) > 0 ) {
                $best_sort_key = $sort_key;
                $best_candidate = array(
                    'source_url'      => yumeho_normalize_estat_url( (string) ( $resource['URL'] ?? '' ) ),
                    'source_page_url' => yumeho_normalize_estat_url( (string) ( $dataset['LANDING_PAGE'] ?? '' ) ),
                    'stat_inf_id'     => (string) ( $catalog_entry['@id'] ?? '' ),
                    'resource_id'     => (string) ( $resource['@id'] ?? '' ),
                );
            }
        }
    }

    return $best_candidate;
}

function yumeho_roi_catalog_match_score( $text, $format, $url ) {
    $score = 0;
    $text  = (string) $text;

    $needles = array(
        '賃金構造基本統計調査'         => 120,
        '短時間労働者'               => 120,
        '第３表'                     => 100,
        '第3表'                      => 100,
        '１時間当たり所定内給与額'   => 100,
        '1時間当たり所定内給与額'    => 100,
        '職種（小分類）'             => 60,
        '職種(小分類)'               => 60,
    );

    foreach ( $needles as $needle => $points ) {
        if ( false !== mb_strpos( $text, $needle ) ) {
            $score += $points;
        }
    }

    if ( 'XLS' === $format ) {
        $score += 25;
    } elseif ( 'XLS_REP' === $format ) {
        $score += 15;
    }

    if ( false !== strpos( (string) $url, 'file-download' ) ) {
        $score += 10;
    }

    return $score;
}

function yumeho_estat_text_value( $value ) {
    if ( is_array( $value ) ) {
        return (string) ( $value['$'] ?? '' );
    }

    return (string) $value;
}

function yumeho_normalize_estat_url( $url ) {
    $url = trim( (string) $url );
    if ( '' === $url ) {
        return '';
    }

    return preg_replace( '#^http://#i', 'https://', $url );
}

function yumeho_get_estat_app_id() {
    if ( defined( 'YUMEHO_ESTAT_APP_ID' ) && YUMEHO_ESTAT_APP_ID ) {
        return trim( (string) YUMEHO_ESTAT_APP_ID );
    }

    $env_value = getenv( 'YUMEHO_ESTAT_APP_ID' );
    if ( ! $env_value ) {
        $env_value = getenv( 'ESTAT_APP_ID' );
    }
    if ( $env_value ) {
        return trim( (string) $env_value );
    }

    $config = yumeho_load_estat_app_config();
    $app_id = trim( (string) ( $config['estat']['appId'] ?? ( $config['estatAppId'] ?? '' ) ) );
    if ( '' !== $app_id ) {
        return $app_id;
    }

    if ( function_exists( 'yumeho_theme_mod' ) ) {
        $theme_mod = trim( (string) yumeho_theme_mod( 'estat_app_id', '' ) );
        if ( '' !== $theme_mod ) {
            return $theme_mod;
        }
    }

    return '';
}

function yumeho_load_estat_app_config() {
    static $cached = null;

    if ( null !== $cached ) {
        return $cached;
    }

    $cached = array();

    foreach ( yumeho_get_estat_app_config_paths() as $config_path ) {
        $raw = file_get_contents( $config_path );
        if ( ! is_string( $raw ) || '' === trim( $raw ) ) {
            continue;
        }

        $decoded = json_decode( $raw, true );
        if ( is_array( $decoded ) ) {
            $cached = yumeho_merge_estat_app_config( $cached, $decoded );
        }
    }

    return $cached;
}

function yumeho_merge_estat_app_config( array $preferred, array $fallback ) {
    $merged = $preferred;

    foreach ( $fallback as $key => $value ) {
        $has_existing_value = array_key_exists( $key, $merged );
        $existing_value     = $has_existing_value ? $merged[ $key ] : null;
        $is_blank_scalar    = $has_existing_value && ! is_array( $existing_value ) && '' === trim( (string) $existing_value );

        if ( ! $has_existing_value || $is_blank_scalar ) {
            $merged[ $key ] = $value;
            continue;
        }

        if ( is_array( $existing_value ) && is_array( $value ) ) {
            $merged[ $key ] = yumeho_merge_estat_app_config( $existing_value, $value );
        }
    }

    return $merged;
}

function yumeho_get_estat_app_config_paths() {
    $search_dirs = array( YUMEHO_DIR );
    $current_dir = YUMEHO_DIR;
    $paths       = array();

    for ( $i = 0; $i < 10; $i++ ) {
        $parent = dirname( $current_dir );
        if ( $parent === $current_dir ) {
            break;
        }
        $search_dirs[] = $parent;
        $current_dir   = $parent;
    }

    foreach ( $search_dirs as $dir ) {
        $candidates = array(
            $dir . '/yumeho/config/app.config.json',
            $dir . '/config/app.config.json',
        );

        foreach ( $candidates as $candidate ) {
            if ( file_exists( $candidate ) && ! in_array( $candidate, $paths, true ) ) {
                $paths[] = $candidate;
            }
        }
    }

    return $paths;
}

function yumeho_find_estat_app_config_path() {
    $paths = yumeho_get_estat_app_config_paths();
    if ( empty( $paths ) ) {
        return '';
    }

    return $paths[0];
}

function yumeho_parse_estat_roi_hourly_wage_xlsx( $xlsx_binary, $occupation_label ) {
    if ( ! class_exists( 'ZipArchive' ) || '' === $xlsx_binary ) {
        return null;
    }

    $temp_file = function_exists( 'wp_tempnam' ) ? wp_tempnam( 'yumeho-roi-hourly-wage.xlsx' ) : tempnam( sys_get_temp_dir(), 'yumeho-roi-' );
    if ( ! $temp_file ) {
        return null;
    }

    file_put_contents( $temp_file, $xlsx_binary );

    $zip = new ZipArchive();
    if ( true !== $zip->open( $temp_file ) ) {
        @unlink( $temp_file );
        return null;
    }

    $shared_strings_xml = (string) $zip->getFromName( 'xl/sharedStrings.xml' );
    $sheet_xml          = (string) $zip->getFromName( 'xl/worksheets/sheet1.xml' );
    $zip->close();
    @unlink( $temp_file );

    if ( '' === $sheet_xml ) {
        return null;
    }

    $shared_strings = yumeho_parse_estat_shared_strings_xml( $shared_strings_xml );
    $hourly_wage    = yumeho_parse_estat_hourly_wage_from_sheet_xml( $sheet_xml, $shared_strings, $occupation_label );

    if ( ! $hourly_wage ) {
        return null;
    }

    $year_label = '';
    if ( preg_match( '/(令和[0-9０-９]+年)賃金構造基本統計調査/u', $shared_strings_xml . "\n" . $sheet_xml, $matches ) ) {
        $year_label = $matches[1];
    }

    return array(
        'hourly_wage' => $hourly_wage,
        'year_label'  => $year_label,
    );
}

function yumeho_parse_estat_shared_strings_xml( $shared_strings_xml ) {
    if ( '' === $shared_strings_xml ) {
        return array();
    }

    $strings = array();

    if ( preg_match_all( '/<si\b[^>]*>(.*?)<\/si>/su', $shared_strings_xml, $items ) ) {
        foreach ( $items[1] as $item_fragment ) {
            $text = '';

            if ( preg_match_all( '/<t\b[^>]*>(.*?)<\/t>/su', $item_fragment, $text_matches ) ) {
                foreach ( $text_matches[1] as $text_fragment ) {
                    $text .= yumeho_decode_estat_xml_text( $text_fragment );
                }
            }

            $strings[] = $text;
        }
    }

    return $strings;
}

function yumeho_parse_estat_hourly_wage_from_sheet_xml( $sheet_xml, array $shared_strings, $occupation_label ) {
    if ( ! preg_match_all( '/<row\b[^>]*>(.*?)<\/row>/su', $sheet_xml, $row_matches ) ) {
        return null;
    }

    foreach ( $row_matches[1] as $row_fragment ) {
        $cells = array();

        if ( preg_match_all( '/<c\b([^>]*)r="([A-Z]+)\d+"([^>]*)>(.*?)<\/c>/su', $row_fragment, $cell_matches, PREG_SET_ORDER ) ) {
            foreach ( $cell_matches as $cell_match ) {
                $attributes = $cell_match[1] . ' ' . $cell_match[3];
                $column     = $cell_match[2];
                $type       = '';

                if ( preg_match( '/\bt="([^"]+)"/u', $attributes, $type_match ) ) {
                    $type = $type_match[1];
                }

                $value = yumeho_extract_estat_sheet_cell_value( $cell_match[4], $type, $shared_strings );
                if ( null !== $value && '' !== $value ) {
                    $cells[ $column ] = $value;
                }
            }
        }

        if ( ( $cells['B'] ?? '' ) === $occupation_label && isset( $cells['H'] ) && is_numeric( $cells['H'] ) ) {
            return (int) round( (float) $cells['H'] );
        }
    }

    return null;
}

function yumeho_extract_estat_sheet_cell_value( $cell_xml, $type, array $shared_strings ) {
    if ( 'inlineStr' === $type ) {
        $text = '';
        if ( preg_match_all( '/<t\b[^>]*>(.*?)<\/t>/su', $cell_xml, $text_matches ) ) {
            foreach ( $text_matches[1] as $text_fragment ) {
                $text .= yumeho_decode_estat_xml_text( $text_fragment );
            }
        }

        return $text;
    }

    if ( ! preg_match( '/<v[^>]*>(.*?)<\/v>/su', $cell_xml, $value_match ) ) {
        return null;
    }

    $raw_value = yumeho_decode_estat_xml_text( $value_match[1] );
    if ( 's' === $type ) {
        return $shared_strings[ (int) $raw_value ] ?? '';
    }

    if ( is_numeric( $raw_value ) ) {
        return 0 + $raw_value;
    }

    return $raw_value;
}

function yumeho_decode_estat_xml_text( $text ) {
    return html_entity_decode( str_replace( array( '&#10;', '&#13;' ), array( "\n", "\r" ), (string) $text ), ENT_QUOTES | ENT_XML1, 'UTF-8' );
}
