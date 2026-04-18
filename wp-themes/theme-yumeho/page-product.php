<?php
/**
 * Template Name: 製品紹介
 *
 * @package YUMEHO
 */
$catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
$by_code         = $catalog_context['by_code'];
$product_fcw3000 = $by_code['fcw-3000'] ?? array(
    'display_name' => '天井直付型 FCW-3000',
    'short_name'   => 'FCW-3000',
);
$product_pgt9000 = $by_code['pgt-9000'] ?? array(
    'display_name' => 'スタンド型 PGT-9000',
    'short_name'   => 'PGT-9000',
    'spec'         => '2000×4000mm / 総レール長14m',
);
$product_pgt9001 = $by_code['pgt-9001'] ?? array(
    'display_name' => 'スタンド型 PGT-9001',
    'short_name'   => 'PGT-9001',
    'spec'         => '2000×6000mm / 総レール長20m',
);
$product_gsuit   = $by_code['g-suit'] ?? array(
    'display_name' => 'G-Suit ハーネス',
    'short_name'   => 'G-Suit',
);
$product_jrx     = $by_code['jrx'] ?? array(
    'display_name' => 'JRX（Junction Rail eXpress）方向転換システム',
    'short_name'   => 'JRX',
);
$product_tpulling = $by_code['t-pulling'] ?? array( 'display_name' => 'T-Pulling（プーリングシステム）' );
$product_tsling  = $by_code['t-sling'] ?? array( 'display_name' => 'T-Sling（スリングシステム）' );
$product_gcord   = $by_code['g-cord'] ?? array( 'display_name' => 'G-Cord（自動高さ調整）' );
$product_sng     = $by_code['sng'] ?? array( 'display_name' => 'SnG（ロック機構）' );
$product_measure = $by_code['walk-data-kit'] ?? array( 'display_name' => '歩行データ計測キット（PC連携）' );
$pgt9000_specs   = yumeho_product_spec_parts( $product_pgt9000['spec'] ?? '' );
$pgt9001_specs   = yumeho_product_spec_parts( $product_pgt9001['spec'] ?? '' );
$format_dimension_label = static function ( $label ) {
    $normalized = preg_replace( '/\s+/u', ' ', trim( (string) $label ) );
    $normalized = preg_replace( '/\s*[×x]\s*/u', '×', $normalized );
    $normalized = preg_replace( '/\s*mm$/iu', ' mm', $normalized );

    if ( preg_match( '/^(.*?)(?:\s+)?(mm)$/iu', $normalized, $matches ) ) {
        return array(
            'value' => trim( (string) $matches[1] ),
            'unit'  => strtolower( (string) $matches[2] ),
        );
    }

    return array(
        'value' => $normalized,
        'unit'  => '',
    );
};
$pgt9000_size = $format_dimension_label( $pgt9000_specs[0] ?? '2000×4000mm' );
$pgt9001_size = $format_dimension_label( $pgt9001_specs[0] ?? '2000×6000mm' );

get_header();
?>

    <section class="hero hero--product bg-light">
        <div class="container text-center">
            <p class="hero-en">PRODUCT</p>
            <h1 class="hero-title">製品紹介</h1>
            <p class="hero-subtitle">転倒リスクを抑え、両手フリーで<br class="br-sp">多様なリハビリ課題を実現する<br class="br-sp">歩行支援システム</p>
        </div>
    </section>

    <!-- Overview -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">YUMEHOとは</h2>
            <div class="grid grid-2 gap-lg items-center">
                <div class="text-content">
                    <h3>転倒の不安から解放される、<br class="br-sp">新しい歩行訓練</h3>
                    <p>YUMEHOは、患者様の転倒リスクを物理的に防ぎながら、自然な歩行姿勢をサポートする歩行リハビリ支援システムです。</p>
                    <p>特許取得済のG-Suit（ハーネス）とデュアルレールによる免荷装置で、体重負担を軽減しつつ安全に歩行練習が可能です。<strong>両手がフリーになるため</strong>、ボール投げやバドミントンなどの「プレイ型リハビリ」も実施できます。介助スタッフの身体的負担も軽減し、見守り中心の運用が可能になります。</p>
                </div>
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/ceiling_type_walkmate.jpg' ); ?>" alt="YUMEHO 天井型設置イメージ" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);">
                </div>
            </div>
        </div>
    </section>

    <!-- Product Lineup -->
    <section class="section bg-light">
        <div class="container">
            <h2 class="section-title">製品ラインナップ</h2>
            <p class="text-center product-lineup-desc" style="margin-bottom: 48px;">施設の環境や目的に合わせて、天井型またはスタンド型をお選びいただけます。</p>

            <div class="grid grid-2 gap-lg">
                <div class="card">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/ceiling_type_walkmate_lineup.webp' ); ?>" alt="<?php echo esc_attr( $product_fcw3000['display_name'] ); ?>" style="width: 100%; border-radius: 8px; margin-bottom: 20px;" decoding="async">
                    <h3 style="color: var(--primary-color); margin-bottom: 8px;"><?php echo esc_html( $product_fcw3000['display_name'] ); ?></h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 16px;">大規模施設向け / カスタム形状対応</p>
                    <p>天井にレールを直接設置する大規模タイプ。足元がすっきりと広く使え、集団リハビリやプレイ型リハビリに最適です。施設の形状やサイズに合わせたカスタム設計が可能です。</p>
                    <ul style="list-style: disc; padding-left: 20px; margin-top: 12px;">
                        <li>周回コース・直線コース等、自由なレール設計</li>
                        <li>集団リハビリ・プレイ型リハビリに対応</li>
                        <li>サポート柱付きで長期安定性を確保</li>
                    </ul>
                </div>
                <div class="card">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/standalone_type_walkmate.jpg' ); ?>" alt="<?php echo esc_attr( $product_pgt9000['display_name'] ); ?>" style="width: 100%; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="color: var(--primary-color); margin-bottom: 8px;">スタンド型（<?php echo esc_html( ( $product_pgt9000['short_name'] ?: 'PGT-9000' ) . ' / ' . ( $product_pgt9001['short_name'] ?: 'PGT-9001' ) ); ?>）</h3>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 16px;">小規模施設向け / 天井工事不要 / 移設可能</p>
                    <p>自立フレームで設置するコンパクトタイプ。天井工事が不要なため、テナント物件や既存施設への後付け導入が容易です。中央の平行棒が装着と立ち上がりを補助します。</p>
                    <div class="product-inline-table-wrap">
                    <table class="product-inline-table" style="width:100%; margin-top:16px; border-collapse:collapse; font-size:0.9rem;">
                        <thead>
                            <tr style="background:var(--surface-alt);">
                                <th style="padding:10px 12px; border:1px solid var(--line-color); text-align:left;">モデル</th>
                                <th style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;"><?php echo esc_html( $product_pgt9000['short_name'] ?: 'PGT-9000' ); ?></th>
                                <th style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;"><?php echo esc_html( $product_pgt9001['short_name'] ?: 'PGT-9001' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding:10px 12px; border:1px solid var(--line-color);">サイズ</td>
                                <td style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;">
                                    <span class="product-dimension product-dimension--stack-sp">
                                        <span class="product-dimension__value"><?php echo esc_html( $pgt9000_size['value'] ); ?></span>
                                        <?php if ( '' !== $pgt9000_size['unit'] ) : ?>
                                            <span class="product-dimension__unit"><?php echo esc_html( $pgt9000_size['unit'] ); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;">
                                    <span class="product-dimension product-dimension--stack-sp">
                                        <span class="product-dimension__value"><?php echo esc_html( $pgt9001_size['value'] ); ?></span>
                                        <?php if ( '' !== $pgt9001_size['unit'] ) : ?>
                                            <span class="product-dimension__unit"><?php echo esc_html( $pgt9001_size['unit'] ); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:10px 12px; border:1px solid var(--line-color);">総レール長</td>
                                <td style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;"><?php echo esc_html( yumeho_product_rail_label( $product_pgt9000 ) ?: '総レール長 14m' ); ?></td>
                                <td style="padding:10px 12px; border:1px solid var(--line-color); text-align:center;"><?php echo esc_html( yumeho_product_rail_label( $product_pgt9001 ) ?: '総レール長 20m' ); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                    <ul style="list-style: disc; padding-left: 20px; margin-top: 12px;">
                        <li>最小幅1.6mのスペースから設置可能</li>
                        <li>移設・配置変更が簡単</li>
                        <li>段階導入（スタンド型→天井型）にも対応</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">主な特長</h2>

            <div class="feature-item grid grid-2 gap-lg items-center" style="margin-bottom: 64px;">
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/rail.webp' ); ?>" alt="デュアルレールシステム" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);" decoding="async">
                </div>
                <div class="text-content">
                    <h3>1. デュアルレールによる安定走行</h3>
                    <p>精密仕上げの<strong>デュアルレール</strong>を採用。シングルレールで起こりやすい繋ぎ目の隙間やガタつきを解消し、スムーズな移動を実現します。</p>
                    <div class="product-inline-table-wrap">
                    <table class="product-inline-table" style="width:100%; margin-top:16px; border-collapse:collapse; font-size:0.85rem;">
                        <tbody>
                            <tr><td style="padding:8px 12px; border:1px solid var(--line-color); background:var(--surface-alt); font-weight:600;">レール数</td><td style="padding:8px 12px; border:1px solid var(--line-color);">2本（デュアル構造）</td></tr>
                            <tr><td style="padding:8px 12px; border:1px solid var(--line-color); background:var(--surface-alt); font-weight:600;">ホイール数</td><td style="padding:8px 12px; border:1px solid var(--line-color);">8輪</td></tr>
                            <tr><td style="padding:8px 12px; border:1px solid var(--line-color); background:var(--surface-alt); font-weight:600;">レール耐荷重</td><td style="padding:8px 12px; border:1px solid var(--line-color);">2,000 kg</td></tr>
                            <tr><td style="padding:8px 12px; border:1px solid var(--line-color); background:var(--surface-alt); font-weight:600;">ホイール耐荷重</td><td style="padding:8px 12px; border:1px solid var(--line-color);">1,700 kg</td></tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="feature-item grid grid-2 gap-lg items-center" style="margin-bottom: 64px;">
                <div class="text-content">
                    <h3>2. <?php echo esc_html( $product_gsuit['display_name'] ); ?>の快適設計</h3>
                    <p>特許取得済の<?php echo esc_html( $product_gsuit['short_name'] ?: $product_gsuit['display_name'] ); ?>は、<strong>股間を圧迫しない</strong>腰・太もも巻き付け設計。簡単なアクセサリーで素早く装着でき、長時間のリハビリでも不快感がありません。</p>
                    <ul style="list-style: disc; padding-left: 20px; margin-top: 12px;">
                        <li>股間を圧迫しない腰巻き付け構造</li>
                        <li>簡単なアクセサリーで素早く装着</li>
                        <li>体圧分散機構で長時間でも快適</li>
                        <li>M/Lサイズ展開、小児用・特大用も対応可能</li>
                    </ul>
                </div>
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/gsuit_harness.jpg' ); ?>" alt="G-Suitハーネス装着イメージ" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);">
                </div>
            </div>

            <div class="feature-item grid grid-2 gap-lg items-center" style="margin-bottom: 64px;">
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/install_flexible4.jpg' ); ?>" alt="天井レール設置イメージ" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);">
                </div>
                <div class="text-content">
                    <h3>3. 施設に合わせた柔軟な設置</h3>
                    <p>天井強度が確保できる施設には「<?php echo esc_html( $product_fcw3000['display_name'] ); ?>」、天井工事が難しい施設には「スタンド型（<?php echo esc_html( ( $product_pgt9000['short_name'] ?: 'PGT-9000' ) . '/' . ( $product_pgt9001['short_name'] ?: 'PGT-9001' ) ); ?>）」をご提案。</p>
                    <p style="margin-top: 12px;"><strong>最小幅1.6mから設置可能。</strong>限られたスペースでも導入できます。</p>
                </div>
            </div>

            <div class="feature-item grid grid-2 gap-lg items-center" style="margin-bottom: 64px;">
                <div class="text-content">
                    <h3>4. プレイ型リハビリで参加意欲を引き出す</h3>
                    <p>デュアルレールとG-Suitにより<strong>両手がフリー</strong>になるため、ボール投げ、バドミントン、課題動作などの<strong>遊びを取り入れたリハビリ</strong>が可能になります。</p>
                    <p style="margin-top: 12px;">受動的なリハビリから、利用者様自身が主体的に動く「プレイ型リハビリ」へ。楽しさが継続率を高め、自然な社会的交流も生まれます。</p>
                </div>
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/play_rehab_scene.webp' ); ?>" alt="プレイ型リハビリの様子" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);" decoding="async">
                </div>
            </div>

            <div class="feature-item grid grid-2 gap-lg items-center" style="margin-bottom: 64px;">
                <div class="image-content">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/jrx_system.jpg' ); ?>" alt="JRXシステム" style="width: 100%; border-radius: 8px; border: 1px solid var(--line-color);">
                </div>
                <div class="text-content">
                    <h3>5. <?php echo esc_html( $product_jrx['display_name'] ); ?></h3>
                    <p>レールの分岐点での<strong>スムーズな方向転換</strong>を実現する独自機構。精密な回転コンポーネントによりホイールの引っかかりを防ぎ、ワイヤレスリモコンで操作可能です。</p>
                    <ul style="list-style: disc; padding-left: 20px; margin-top: 12px;">
                        <li>精密回転コンポーネントでスムーズな方向転換</li>
                        <li>ワイヤレスリモコン操作対応</li>
                        <li>G-Suit・Pulling・Slingのドッキング収納ポイント搭載</li>
                        <li>回転時のホイール干渉を防止</li>
                    </ul>
                </div>
            </div>

            <!-- Options -->
            <h3 class="section-title" style="font-size: 1.5rem; margin-bottom: 40px;">オプション製品</h3>
            <div class="grid grid-2 gap-lg" style="margin-bottom: 0;">
                <div class="option-card" style="padding: 24px; background: var(--surface-alt); border-radius: 12px; border: 1px solid var(--line-color);">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/option_tpulling2.jpg' ); ?>" alt="T-Pulling" style="width:100%; border-radius:8px; margin-bottom:16px;">
                    <h4 style="color: var(--primary-color); margin-bottom: 8px;"><?php echo esc_html( $product_tpulling['display_name'] ); ?></h4>
                    <p style="font-size: 0.9rem;">滑車ベースのリハビリ装置。水平・垂直・円運動に対応し、上肢・下肢の多方向トレーニングが可能です。</p>
                </div>
                <div class="option-card" style="padding: 24px; background: var(--surface-alt); border-radius: 12px; border: 1px solid var(--line-color);">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/option_tsling2.jpg' ); ?>" alt="T-Sling" style="width:100%; border-radius:8px; margin-bottom:16px;">
                    <h4 style="color: var(--primary-color); margin-bottom: 8px;"><?php echo esc_html( $product_tsling['display_name'] ); ?></h4>
                    <p style="font-size: 0.9rem;">天井固定型のリハビリ装置。水平方向の動きを活用した治療的運動が可能です。</p>
                </div>
                <div class="option-card" style="padding: 24px; background: var(--surface-alt); border-radius: 12px; border: 1px solid var(--line-color);">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/option_gcord2.jpg' ); ?>" alt="G-Cord" style="width:100%; border-radius:8px; margin-bottom:16px;">
                    <h4 style="color: var(--primary-color); margin-bottom: 8px;"><?php echo esc_html( $product_gcord['display_name'] ); ?></h4>
                    <p style="font-size: 0.9rem;">自動高さ調整機能により、自然な歩行動作をサポート。歩行時の上下動に追従し、快適な免荷を実現します。</p>
                </div>
                <div class="option-card" style="padding: 24px; background: var(--surface-alt); border-radius: 12px; border: 1px solid var(--line-color);">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/option_sng2.jpg' ); ?>" alt="SnG" style="width:100%; border-radius:8px; margin-bottom:16px;">
                    <h4 style="color: var(--primary-color); margin-bottom: 8px;"><?php echo esc_html( $product_sng['display_name'] ); ?></h4>
                    <p style="font-size: 0.9rem;">正確な位置保持のためのロック機構。特定のポジションでの固定が必要な訓練に活用できます。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Comparison -->
    <section class="section bg-light">
        <div class="container">
            <div class="section-heading animate-on-scroll">
                <p class="section-kicker">Comparison</p>
                <h2 class="section-title">他のリハビリ支援方法との違い</h2>
            </div>
            <p class="text-center product-comparison-desc" style="max-width: 720px; margin: 0 auto 48px; line-height: 1.9;">YUMEHOは、従来の歩行リハビリ支援方法と比較して、安全性・運用効率・患者体験のすべてにおいて優位性を持ちます。</p>
            <p class="comparison-scroll-hint"><span>&larr;</span> スワイプで全項目を確認 <span>&rarr;</span></p>
            <div class="comparison-wrap" style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.92rem; line-height: 1.7; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06);">
                    <thead>
                        <tr>
                            <th style="padding: 16px 20px; text-align: left; background: #f4f6fa; border-bottom: 2px solid var(--line-color); font-weight: 700; min-width: 140px;"></th>
                            <th style="padding: 16px 20px; text-align: center; background: #f4f6fa; border-bottom: 2px solid var(--line-color); font-weight: 700;">従来の平行棒</th>
                            <th style="padding: 16px 20px; text-align: center; background: #f4f6fa; border-bottom: 2px solid var(--line-color); font-weight: 700;">シングルレール型装置</th>
                            <th style="padding: 16px 20px; text-align: center; background: rgba(0,104,183,0.08); border-bottom: 2px solid var(--primary-color); font-weight: 700; color: var(--primary-color);">YUMEHO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); font-weight: 600;">移動範囲</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">平行棒内のみ（2〜3m）</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">直線レール上のみ</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center; background: rgba(0,104,183,0.05); font-weight: 600;">自由設計（直線・曲線・周回）</td>
                        </tr>
                        <tr>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); font-weight: 600;">両手の自由度</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">手すり把持が必要</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">片手で支持が必要な場合あり</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center; background: rgba(0,104,183,0.05); font-weight: 600;">完全フリー（プレイ型リハビリ可能）</td>
                        </tr>
                        <tr>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); font-weight: 600;">必要スタッフ数</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">介助2〜3名</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">見守り1〜2名</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center; background: rgba(0,104,183,0.05); font-weight: 600;">見守り1名</td>
                        </tr>
                        <tr>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); font-weight: 600;">転倒防止</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">スタッフの身体介助</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center;">シングルレール追従</td>
                            <td style="padding: 14px 20px; border-bottom: 1px solid var(--line-color); text-align: center; background: rgba(0,104,183,0.05); font-weight: 600;">デュアルレール + G-Suit免荷</td>
                        </tr>
                        <tr>
                            <td style="padding: 14px 20px; font-weight: 600;">走行の滑らかさ</td>
                            <td style="padding: 14px 20px; text-align: center;">-</td>
                            <td style="padding: 14px 20px; text-align: center;">継ぎ目で振動あり</td>
                            <td style="padding: 14px 20px; text-align: center; background: rgba(0,104,183,0.05); font-weight: 600;">精密仕上げで振動なし</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="product-comparison-note" style="font-size: 0.8rem; color: #888; margin-top: 16px; text-align: right;">※競合製品との比較ではなく、リハビリ支援方法のカテゴリ比較です。</p>
        </div>
    </section>

    <!-- Safety -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">安全性へのこだわり</h2>
            <p class="text-center product-section-desc" style="margin-bottom: 48px;">稟議の壁を越えるために、安全設計の思想を透明に開示します。</p>
            <div class="grid grid-3 gap-lg">
                <div class="card">
                    <h3 style="font-size: 1.25rem; color: var(--primary-color);">転倒防止の設計思想</h3>
                    <p>デュアルレールによる荷重支持とG-Suitの落下防止機構で、バランスを崩しても即座に体を支えます。</p>
                </div>
                <div class="card">
                    <h3 style="font-size: 1.25rem; color: var(--primary-color);">運用時の見守り基準</h3>
                    <p>安全運用のための推奨スタッフ配置、要注意の利用者様への配慮事項、インシデント発生時の対応フローなど、運用ガイドラインを提供します。</p>
                </div>
                <div class="card">
                    <h3 style="font-size: 1.25rem; color: var(--primary-color);">点検・メンテナンス</h3>
                    <p>定期点検の頻度と内容、消耗品（ハーネス交換等）の案内、保守契約プランを明確にし、導入後も安心してご使用いただけます。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ringi Support -->
    <section class="section">
        <div class="container">
            <div class="section-heading animate-on-scroll">
                <p class="section-kicker">Ringi Support</p>
                <h2 class="section-title">稟議・導入承認サポート</h2>
            </div>
            <p class="ringi-intro animate-on-scroll">YUMEHOの導入検討に必要な資料を、専任スタッフがワンストップでご用意いたします。稟議書の作成から委員会向けプレゼン資料まで、承認プロセスをトータルでサポートします。</p>

            <div class="ringi-grid">
                <div class="ringi-card animate-on-scroll hover-lift">
                    <div class="ringi-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <h3 class="ringi-card-title">製品仕様サマリー（1枚）</h3>
                    <p class="ringi-card-desc">稟議添付用に最適化された1枚のA4仕様書。製品概要・安全設計・導入実績を簡潔にまとめています。</p>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ringi-card-link">資料請求する</a>
                </div>

                <div class="ringi-card animate-on-scroll hover-lift">
                    <div class="ringi-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 class="ringi-card-title">安全設計根拠書</h3>
                    <p class="ringi-card-desc">デュアルレール・G-Suitの安全機構、転倒防止メカニズムの技術的根拠を詳述した資料です。</p>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ringi-card-link">資料請求する</a>
                </div>

                <div class="ringi-card animate-on-scroll hover-lift">
                    <div class="ringi-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
                    </div>
                    <h3 class="ringi-card-title">導入効果エビデンス</h3>
                    <p class="ringi-card-desc">導入施設での実測データに基づく効果資料。スタッフ削減数・訓練機会増加率・患者満足度を収録。</p>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ringi-card-link">資料請求する</a>
                </div>

                <div class="ringi-card animate-on-scroll hover-lift">
                    <div class="ringi-card-icon">
                        <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </div>
                    <h3 class="ringi-card-title">設置要件チェックシート</h3>
                    <p class="ringi-card-desc">天井高・スペース・電源・搬入経路など、導入前に確認すべき項目を網羅したチェックリストです。</p>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ringi-card-link">資料請求する</a>
                </div>

                <div class="ringi-card animate-on-scroll hover-lift">
                    <div class="ringi-card-icon">
                        <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="ringi-card-title">概算見積フォーマット</h3>
                    <p class="ringi-card-desc">施設タイプ・設置方式・オプションに応じた概算見積。予算確保の根拠資料としてご活用いただけます。</p>
                    <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="ringi-card-link">資料請求する</a>
                </div>
            </div>

            <div class="ringi-highlight animate-on-scroll">
                <h3 class="ringi-highlight-title">稟議が通りやすくなる3つのポイント</h3>
                <ol>
                    <li>導入施設での定量的な成果データ（訓練機会1.5倍・スタッフ削減）</li>
                    <li>補助金・助成金の活用で実質負担を軽減</li>
                    <li>段階導入（スタンド型→天井型）で初期投資を分散</li>
                </ol>
            </div>

            <div class="ringi-cta animate-on-scroll" style="text-align:center;">
                <p style="font-size: 0.95rem; line-height: 1.8; color: #555; margin-bottom: 20px;">稟議書の作成でお困りの方は、お気軽にご相談ください。過去の承認事例もご紹介できます。</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">稟議サポートを相談する &rarr;</a>
            </div>
        </div>
    </section>

    <!-- System Configuration -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">システム構成</h2>
            <div class="grid grid-3 gap-lg">
                <div class="card">
                    <h3 style="font-size: 1.25rem;">基本ユニット</h3>
                    <ul style="list-style: disc; padding-left: 20px;">
                        <li>走行レール（直線/カーブ）</li>
                        <li>免荷ユニット本体</li>
                        <li>専用ハーネス（M/Lサイズ）</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 style="font-size: 1.25rem;">設置オプション</h3>
                    <ul style="list-style: disc; padding-left: 20px;">
                        <li>自立スタンドフレーム</li>
                        <li>梁固定用ブラケット</li>
                        <li>天井埋込用部材</li>
                    </ul>
                </div>
                <div class="card">
                    <h3 style="font-size: 1.25rem;">機能拡張・オプション</h3>
                    <ul style="list-style: disc; padding-left: 20px;">
                        <li><?php echo esc_html( $product_tpulling['display_name'] ); ?></li>
                        <li><?php echo esc_html( $product_tsling['display_name'] ); ?></li>
                        <li><?php echo esc_html( $product_gcord['display_name'] ); ?></li>
                        <li><?php echo esc_html( $product_sng['display_name'] ); ?></li>
                        <li><?php echo esc_html( $product_measure['display_name'] ); ?></li>
                        <li>小児用・特大用ハーネス</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="section">
        <div class="container text-center product-bottom-cta">
            <h2>詳しい仕様や寸法図、<br class="br-sp">稟議用資料をご希望の方へ</h2>
            <p>製品カタログ、導入事例集、安全運用ガイド、設置要件チェックシート等、導入検討に必要な資料一式をお送りします。</p>
            <div class="hero-actions">
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">資料請求・デモ依頼</a>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
