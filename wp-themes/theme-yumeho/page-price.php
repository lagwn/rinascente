<?php
/**
 * Template Name: 価格・見積
 *
 * @package YUMEHO
 */
$catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
$by_code         = $catalog_context['by_code'];
$stand_systems   = $catalog_context['stand_systems'];
$ceiling_system  = $catalog_context['ceiling_system'];
$extra_harness   = $catalog_context['by_pricing_key']['harness_extra'] ?? array(
    'display_name' => '追加ハーネス',
);

$stand_short_names = array();
foreach ( $stand_systems as $stand_system ) {
    $stand_short_names[] = $stand_system['short_name'] ?: $stand_system['display_name'];
}

$system_factor_text = trim( implode( ' / ', array_filter( $stand_short_names ) ) );
if ( $ceiling_system ) {
    $system_factor_text = $ceiling_system['display_name'] . ( $system_factor_text ? '、またはスタンド型 ' . $system_factor_text . 'のいずれか。' : '。' );
}

$option_factor_names = array();
foreach ( $catalog_context['options'] as $option_item ) {
    $option_factor_names[] = $option_item['short_name'] ?: $option_item['display_name'];
}
$option_factor_names[] = $extra_harness['display_name'];

$pgt9000 = $by_code['pgt-9000'] ?? array(
    'display_name' => 'スタンド型 PGT-9000',
    'spec'         => '2000×4000mm / 総レール長14m',
);
$pgt9001 = $by_code['pgt-9001'] ?? array(
    'display_name' => 'スタンド型 PGT-9001',
    'spec'         => '2000×6000mm / 総レール長20m',
);
$fcw3000 = $by_code['fcw-3000'] ?? array(
    'display_name' => '天井直付型 FCW-3000',
    'spec'         => 'カスタム周回レール設計',
);
$gsuit   = $by_code['g-suit'] ?? array(
    'display_name' => 'G-Suit ハーネス',
    'short_name'   => 'G-Suit',
);
$gcord   = $by_code['g-cord'] ?? array(
    'display_name' => 'G-Cord（自動高さ調整）',
    'short_name'   => 'G-Cord',
);
$jrx     = $by_code['jrx'] ?? array(
    'display_name' => 'JRX（Junction Rail eXpress）方向転換システム',
    'short_name'   => 'JRX',
);
$tpulling = $by_code['t-pulling'] ?? array(
    'display_name' => 'T-Pulling（プーリングシステム）',
    'short_name'   => 'T-Pulling',
);
$tsling  = $by_code['t-sling'] ?? array(
    'display_name' => 'T-Sling（スリングシステム）',
    'short_name'   => 'T-Sling',
);
$measure = $by_code['walk-data-kit'] ?? array(
    'display_name' => '歩行データ計測キット（PC連携）',
    'short_name'   => '計測キット',
);
$roi_wage_data = function_exists( 'yumeho_get_roi_hourly_wage_data' ) ? yumeho_get_roi_hourly_wage_data() : array( 'hourly_wage' => 1323 );
$roi_hourly_wage = (int) ( $roi_wage_data['hourly_wage'] ?? 1323 );
$price_roi_cost_man_yen = function_exists( 'yumeho_roi_cost_saving_man_yen' ) ? yumeho_roi_cost_saving_man_yen( $roi_hourly_wage, 8, 2, 200 ) : 423;
$price_roi_note = function_exists( 'yumeho_roi_hourly_wage_note_text' ) ? yumeho_roi_hourly_wage_note_text( $roi_wage_data, '※上記は導入施設の実績に基づく参考値です。' ) : '※上記は導入施設の実績に基づく参考値です。時給は介護職員（医療・福祉施設等）の平均値（1,323円）で試算しています。';

get_header();
?>

<style>
    .price-intro {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 56px;
        font-size: 0.92rem;
        line-height: 1.85;
        color: rgba(0,0,0,0.85);
    }
    .price-intro strong { color: var(--primary-color); font-weight: 700; }

    /* Simulation CTA banner */
    .price-sim-banner {
        display: flex;
        align-items: center;
        gap: 32px;
        background: linear-gradient(135deg, #062d5c 0%, var(--primary-color) 100%);
        border-radius: 12px;
        padding: 36px 40px;
        color: #fff;
        margin-bottom: 56px;
    }
    .price-sim-banner__body { flex: 1; }
    .price-sim-banner h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: #fff;
    }
    .price-sim-banner p {
        font-size: 0.85rem;
        color: rgba(255,255,255,0.72);
        margin-bottom: 0;
        line-height: 1.7;
    }
    .price-sim-banner .btn {
        flex-shrink: 0;
        background: #fff;
        color: var(--primary-color);
        border-color: #fff;
        font-weight: 700;
    }
    .price-sim-banner .btn:hover {
        background: rgba(255,255,255,0.88);
    }
    .price-sim-banner .btn::after { display: none; }

    /* Factor cards */
    .price-factors {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 56px;
    }
    .price-factor {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        border-top: 3px solid var(--primary-color);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .price-factor:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .price-factor__icon {
        font-size: 1.6rem;
        margin-bottom: 12px;
    }
    .price-factor h4 {
        font-size: 0.92rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--text-color);
    }
    .price-factor p {
        font-size: 0.82rem;
        line-height: 1.7;
        color: rgba(0,0,0,0.82);
        margin-bottom: 0;
    }

    /* Payment options */
    .price-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 56px;
    }
    .price-option {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        text-align: center;
        transition: transform 0.2s;
    }
    .price-option:hover { transform: translateY(-3px); }
    .price-option__label {
        display: inline-block;
        font-family: var(--font-logo);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--primary-color);
        background: rgba(0,104,183,0.07);
        border-radius: 3px;
        padding: 4px 12px;
        margin-bottom: 14px;
    }
    .price-option h4 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    .price-option p {
        font-size: 0.82rem;
        line-height: 1.7;
        color: rgba(0,0,0,0.82);
        margin-bottom: 0;
    }

    /* Model cases */
    .model-cases {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 56px;
    }
    .model-case {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .model-case:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .model-case__head {
        padding: 20px 28px;
        background: linear-gradient(135deg, rgba(0,104,183,0.06), rgba(0,104,183,0.02));
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .model-case__tag {
        font-family: var(--font-logo);
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        color: var(--primary-color);
        opacity: 0.85;
        margin-bottom: 4px;
    }
    .model-case__head h3 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .model-case__head p {
        font-size: 0.75rem;
        color: rgba(0,0,0,0.72);
        margin-bottom: 0;
    }
    .model-case__body {
        padding: 24px 28px;
    }
    .model-case__spec {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }
    .model-case__spec span {
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--primary-color);
        background: rgba(0,104,183,0.06);
        border-radius: 3px;
        padding: 3px 10px;
    }
    .model-case__body > p {
        font-size: 0.84rem;
        line-height: 1.75;
        color: rgba(0,0,0,0.82);
        margin-bottom: 0;
    }

    /* Checklist */
    .price-checklist {
        background: #fff;
        border-radius: 10px;
        padding: 32px 36px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        margin-bottom: 56px;
    }
    .price-checklist h3 {
        font-size: 1rem;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    .price-checklist__grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 28px;
    }
    .price-checklist__item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        color: rgba(0,0,0,0.85);
    }
    .price-checklist__item::before {
        content: "";
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1.5px solid rgba(0,104,183,0.3);
        background: rgba(0,104,183,0.04);
    }

    @media (max-width: 860px) {
        .price-factors, .price-options { grid-template-columns: 1fr; }
        .model-cases { grid-template-columns: 1fr; }
        .price-checklist__grid { grid-template-columns: 1fr; }
        .price-sim-banner { flex-direction: column; text-align: center; }
    }
</style>

<style>
    .roi-intro {
        text-align: center;
        max-width: 640px;
        margin: 0 auto 40px;
        font-size: 0.92rem;
        line-height: 1.85;
        color: rgba(0,0,0,0.85);
    }
    .roi-comparison {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 48px;
    }
    .roi-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 28px 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .roi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .roi-card--before {
        border-top: 3px solid rgba(0,0,0,0.15);
    }
    .roi-card--after {
        border-top: 3px solid var(--primary-color);
    }
    .roi-card__label {
        display: inline-block;
        font-family: var(--font-logo);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        border-radius: 3px;
        padding: 4px 12px;
        margin-bottom: 16px;
    }
    .roi-card--before .roi-card__label {
        color: rgba(0,0,0,0.78);
        background: rgba(0,0,0,0.05);
    }
    .roi-card--after .roi-card__label {
        color: var(--primary-color);
        background: rgba(0,104,183,0.07);
    }
    .roi-card__list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .roi-card__list li {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-size: 0.84rem;
        line-height: 1.6;
    }
    .roi-card__list li:last-child {
        border-bottom: none;
    }
    .roi-card__list-label {
        color: rgba(0,0,0,0.82);
        flex-shrink: 0;
        margin-right: 12px;
    }
    .roi-card__list-value {
        font-weight: 700;
        color: var(--text-color);
        text-align: right;
    }
    .roi-card--after .roi-card__list-value {
        color: var(--primary-color);
    }
    .roi-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }
    .roi-metric {
        background: #fff;
        border-radius: 10px;
        padding: 28px 24px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        border-top: 3px solid var(--primary-color);
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .roi-metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .roi-metric__icon {
        font-size: 1.6rem;
        margin-bottom: 12px;
    }
    .roi-metric h4 {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 4px;
        color: var(--primary-color);
    }
    .roi-metric__sub {
        font-size: 0.78rem;
        color: rgba(0,0,0,0.78);
        margin-bottom: 12px;
    }
    .roi-metric__value {
        font-family: var(--font-logo);
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-color);
        line-height: 1.3;
        margin-bottom: 4px;
    }
    .roi-metric__detail {
        font-size: 0.72rem;
        color: rgba(0,0,0,0.72);
        line-height: 1.6;
    }
    .roi-note {
        font-size: 0.78rem;
        color: rgba(0,0,0,0.72);
        line-height: 1.8;
        margin-bottom: 32px;
        text-align: center;
    }
    .roi-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        background: linear-gradient(135deg, rgba(0,104,183,0.06), rgba(0,104,183,0.02));
        border-radius: 10px;
        padding: 28px 36px;
        margin-bottom: 56px;
    }
    .roi-cta p {
        font-size: 0.9rem;
        color: rgba(0,0,0,0.85);
        margin-bottom: 0;
    }
    .roi-cta .btn { flex-shrink: 0; }

    @media (max-width: 860px) {
        .roi-comparison { grid-template-columns: 1fr; }
        .roi-metrics { grid-template-columns: 1fr; }
        .roi-cta { flex-direction: column; text-align: center; }
    }
</style>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">PRICE</p>
            <h1 class="hero-title">価格・お見積もりについて</h1>
            <p class="hero-subtitle">施設行境・設置方式・オプションに応じた最適なプランをご提案します</p>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 960px; margin: 0 auto;">

            <p class="price-intro">YUMEHOは施設の広さ・設置方式・オプションにより構成が異なるため、<br class="br-pc"><strong>定価表示はしておりません。</strong><br class="br-pc">まずはお気軽に概算見積りをご依頼ください。</p>

            <!-- Simulation CTA -->
            <div class="price-sim-banner animate-on-scroll">
                <div class="price-sim-banner__body">
                    <h3>あなたの施設に最適な構成は？</h3>
                    <p>簡単な質問に答えるだけで、推奨システム構成と概算仕様がわかります。</p>
                </div>
                <a href="<?php echo esc_url( home_url( '/simulation/' ) ); ?>" class="btn btn-lg">導入シミュレーションを試す</a>
            </div>

            <!-- Price Factors -->
            <div class="section-heading">
                <p class="section-kicker">Pricing Factors</p>
                <h2 class="section-title">価格の変動要素</h2>
            </div>
            <div class="price-factors">
                <div class="price-factor animate-on-scroll">
                    <div class="price-factor__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <h4>設置方式の選択</h4>
                    <p><?php echo esc_html( $system_factor_text ?: '天井直付型 FCW-3000、またはスタンド型 PGT-9000 / PGT-9001のいずれか。' ); ?></p>
                </div>
                <div class="price-factor animate-on-scroll delay-100">
                    <div class="price-factor__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <h4>レールの長さと形状</h4>
                    <p>直線のみ、またはカーブを含む周回コースなど、レールの構成。</p>
                </div>
                <div class="price-factor animate-on-scroll delay-200">
                    <div class="price-factor__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    </div>
                    <h4>オプションの有無</h4>
                    <p><?php echo esc_html( implode( '、', array_filter( $option_factor_names ) ) ); ?> など。</p>
                </div>
            </div>

            <!-- Payment Options -->
            <div class="section-heading">
                <p class="section-kicker">Payment Options</p>
                <h2 class="section-title">導入形態の選択肢</h2>
            </div>
            <div class="price-options">
                <div class="price-option animate-on-scroll">
                    <div class="price-option__label">PURCHASE</div>
                    <h4>購入</h4>
                    <p>一括導入でランニングコストを最小化</p>
                </div>
                <div class="price-option animate-on-scroll delay-100">
                    <div class="price-option__label">LEASE</div>
                    <h4>リース</h4>
                    <p>初期費用を抑えた月額利用</p>
                </div>
                <div class="price-option animate-on-scroll delay-200">
                    <div class="price-option__label">PHASED</div>
                    <h4>段階導入</h4>
                    <p>スタンド型で開始 → 天井型へ拡張</p>
                </div>
            </div>

            <!-- ROI Simulation -->
            <div class="section-heading">
                <p class="section-kicker">ROI</p>
                <h2 class="section-title">導入効果シミュレーション</h2>
            </div>
            <p class="roi-intro">YUMEHOの導入により期待される人件費削減効果と訓練機会増加のインパクトを試算します。</p>

            <!-- Before / After comparison -->
            <div class="roi-comparison">
                <div class="roi-card roi-card--before animate-on-scroll">
                    <div class="roi-card__label">BEFORE</div>
                    <ul class="roi-card__list">
                        <li>
                            <span class="roi-card__list-label">歩行訓練の見守り体制</span>
                            <span class="roi-card__list-value">介助スタッフ 2〜3名</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">1日あたりの訓練可能患者数</span>
                            <span class="roi-card__list-value">約8名</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">スタッフの身体的負担</span>
                            <span class="roi-card__list-value">高い（腰痛リスク）</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">転倒事故への不安</span>
                            <span class="roi-card__list-value">常時（精神的負担大）</span>
                        </li>
                    </ul>
                </div>
                <div class="roi-card roi-card--after animate-on-scroll delay-100">
                    <div class="roi-card__label">AFTER — YUMEHO導入後</div>
                    <ul class="roi-card__list">
                        <li>
                            <span class="roi-card__list-label">歩行訓練の見守り体制</span>
                            <span class="roi-card__list-value">見守り 1名</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">1日あたりの訓練可能患者数</span>
                            <span class="roi-card__list-value">約12名（1.5倍）</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">スタッフの身体的負担</span>
                            <span class="roi-card__list-value">大幅軽減</span>
                        </li>
                        <li>
                            <span class="roi-card__list-label">転倒事故への不安</span>
                            <span class="roi-card__list-value">ハーネスが物理的に防止</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Annual cost reduction metrics -->
            <div class="section-heading">
                <p class="section-kicker">Cost Reduction</p>
                <h2 class="section-title">年間コスト削減の概算</h2>
            </div>
            <div class="roi-metrics">
                <div class="roi-metric animate-on-scroll">
                    <div class="roi-metric__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4l5 7 5-7"/><path d="M12 11v9"/><path d="M7 14h10"/><path d="M7 17h10"/></svg>
                    </div>
                    <h4>人件費削減</h4>
                    <p class="roi-metric__sub">スタッフ2名分の工数削減</p>
                    <div class="roi-metric__value">年間約<?php echo esc_html( number_format_i18n( $price_roi_cost_man_yen ) ); ?>万円相当</div>
                    <p class="roi-metric__detail">時給<?php echo esc_html( number_format_i18n( $roi_hourly_wage ) ); ?>円 &times; 8h &times; 2名 &times; 200日</p>
                </div>
                <div class="roi-metric animate-on-scroll delay-100">
                    <div class="roi-metric__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <h4>訓練機会増加</h4>
                    <p class="roi-metric__sub">同スタッフ数で1.5倍の訓練提供</p>
                    <div class="roi-metric__value">年間約1,200回</div>
                    <p class="roi-metric__detail">の追加訓練機会</p>
                </div>
                <div class="roi-metric animate-on-scroll delay-200">
                    <div class="roi-metric__icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h4>事故リスク低減</h4>
                    <p class="roi-metric__sub">転倒事故ゼロを実現した施設あり</p>
                    <div class="roi-metric__value">事故対応コスト</div>
                    <p class="roi-metric__detail">保険料の削減効果</p>
                </div>
            </div>

            <p class="roi-note"><?php echo esc_html( $price_roi_note ); ?> 実際の効果は施設の運用状況により異なります。詳細なシミュレーションは個別にご提案いたします。</p>

            <div class="roi-cta animate-on-scroll">
                <p>この導入効果を稟議資料にまとめたい方は</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary">稟議用資料のご相談 &rarr;</a>
            </div>

            <!-- Model Cases -->
            <div class="section-heading">
                <p class="section-kicker">Model Cases</p>
                <h2 class="section-title">モデルケース（参考例）</h2>
            </div>
            <div class="model-cases">
                <div class="model-case animate-on-scroll">
                    <div class="model-case__head">
                        <div class="model-case__tag">CASE A</div>
                        <h3>小規模導入プラン</h3>
                        <p>デイサービス / 個別機能訓練室</p>
                    </div>
                    <div class="model-case__body">
                        <div class="model-case__spec">
                            <span><?php echo esc_html( $pgt9000['display_name'] ); ?></span>
                            <span><?php echo esc_html( $pgt9000['spec'] ); ?></span>
                            <span>本体1台</span>
                            <span><?php echo esc_html( $gsuit['short_name'] ?: $gsuit['display_name'] ); ?>（ハーネス）M×1</span>
                        </div>
                        <p>設置工事不要で導入後すぐに利用可能。限られたスペースでの歩行訓練に最適なミニマムプランです。</p>
                    </div>
                </div>
                <div class="model-case animate-on-scroll delay-100">
                    <div class="model-case__head">
                        <div class="model-case__tag">CASE B</div>
                        <h3>中規模導入プラン</h3>
                        <p>デイケア / 介護老人保健施設</p>
                    </div>
                    <div class="model-case__body">
                        <div class="model-case__spec">
                            <span><?php echo esc_html( $pgt9001['display_name'] ); ?></span>
                            <span><?php echo esc_html( $pgt9001['spec'] ); ?></span>
                            <span>本体1台</span>
                            <span><?php echo esc_html( $gsuit['short_name'] ?: $gsuit['display_name'] ); ?> M・L各1</span>
                            <span><?php echo esc_html( $gcord['display_name'] ); ?></span>
                        </div>
                        <p>広めの訓練スペースで複数利用者の歩行訓練が可能。<?php echo esc_html( $gcord['short_name'] ?: $gcord['display_name'] ); ?>で自然な歩行動作をサポートします。</p>
                    </div>
                </div>
                <div class="model-case animate-on-scroll delay-200">
                    <div class="model-case__head">
                        <div class="model-case__tag">CASE C</div>
                        <h3>大規模導入プラン</h3>
                        <p>リハビリテーション病院 / 回復期病棟</p>
                    </div>
                    <div class="model-case__body">
                        <div class="model-case__spec">
                            <span><?php echo esc_html( $fcw3000['display_name'] ); ?></span>
                            <span><?php echo esc_html( $fcw3000['spec'] ?: 'カスタム周回レール設計' ); ?></span>
                            <span>本体1台 + <?php echo esc_html( $jrx['short_name'] ?: $jrx['display_name'] ); ?></span>
                            <span><?php echo esc_html( $gsuit['short_name'] ?: $gsuit['display_name'] ); ?> M・L各1</span>
                            <span><?php echo esc_html( ( $tpulling['short_name'] ?: $tpulling['display_name'] ) . ' + ' . ( $tsling['short_name'] ?: $tsling['display_name'] ) . ' + ' . ( $measure['short_name'] ?: $measure['display_name'] ) ); ?></span>
                        </div>
                        <p>周回コースによる集団リハビリ・プレイ型リハビリが可能。<?php echo esc_html( ( $tpulling['short_name'] ?: $tpulling['display_name'] ) . '・' . ( $tsling['short_name'] ?: $tsling['display_name'] ) ); ?>で多方向の治療運動にも対応する本格構成です。</p>
                    </div>
                </div>
            </div>

            <!-- Checklist -->
            <div class="section-heading">
                <p class="section-kicker">Checklist</p>
                <h2 class="section-title">設置要件チェックポイント</h2>
            </div>
            <div class="price-checklist animate-on-scroll">
                <h3>現地調査前にご確認いただくと、スムーズにお見積りをご提案できます。</h3>
                <div class="price-checklist__grid">
                    <div class="price-checklist__item">天井高（推奨：2.4m以上）</div>
                    <div class="price-checklist__item">設置スペースの幅（最小1.6m）</div>
                    <div class="price-checklist__item">障害物の有無（配管・エアコン・柱 等）</div>
                    <div class="price-checklist__item">機材搬入経路の確保</div>
                    <div class="price-checklist__item">電源の位置と容量</div>
                </div>
            </div>

            <div class="text-center" style="margin-top: 48px;">
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: rgba(0,0,0,0.82);">具体的な金額や導入形態のご相談は、<br class="br-sp">お気軽にお問い合わせください。</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">見積相談・資料請求はこちら</a>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
