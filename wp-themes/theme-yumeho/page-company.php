<?php
/**
 * Template Name: 会社概要
 *
 * @package YUMEHO
 */
get_header();

$shared_company_data = function_exists( 'yumeho_shared_company_data' ) ? yumeho_shared_company_data() : array();
$use_shared_company  = ! empty( $shared_company_data );
$company_value       = static function( $key ) use ( $shared_company_data, $use_shared_company ) {
    if ( $use_shared_company ) {
        return trim( (string) ( $shared_company_data[ $key ] ?? '' ) );
    }

    return trim( (string) yumeho_theme_mod( $key, '' ) );
};

$company_name = $company_value( 'company_name' );
$company_rows = array();

foreach (
    array(
        'company_name_en' => array( 'label' => '会社名（英語）', 'type' => 'text' ),
        'company_ceo'     => array( 'label' => '代表者', 'type' => 'text', 'prefix' => '代表取締役 ' ),
        'company_founded' => array( 'label' => '設立', 'type' => 'text' ),
        'company_capital' => array( 'label' => '資本金', 'type' => 'text' ),
        'company_address' => array( 'label' => '所在地', 'type' => 'text' ),
        'company_tel'     => array( 'label' => '電話番号', 'type' => 'tel' ),
        'company_fax'     => array( 'label' => 'FAX番号', 'type' => 'tel' ),
        'company_business'=> array( 'label' => '事業内容', 'type' => 'multiline' ),
        'company_products'=> array( 'label' => '主要製品', 'type' => 'multiline' ),
        'company_hours'   => array( 'label' => '受付時間', 'type' => 'text' ),
    ) as $key => $config
) {
    $value = trim(
        (string) (
            'company_products' === $key
                ? ( $use_shared_company
                    ? ( $shared_company_data['company_products'] ?? '' )
                    : ( function_exists( 'yumeho_company_products_text' ) ? yumeho_company_products_text() : yumeho_theme_mod( $key, '' ) )
                )
                : $company_value( $key )
        )
    );

    if ( '' === $value ) {
        continue;
    }

    if ( ! empty( $config['prefix'] ) ) {
        $value = $config['prefix'] . $value;
    }

    $company_rows[] = array(
        'label' => $config['label'],
        'type'  => $config['type'],
        'value' => $value,
    );
}
?>

<style>
    .company-table { width:100%; border-collapse:collapse; }
    .company-table tr { border-bottom:1px solid rgba(0,0,0,0.06); }
    .company-table tr:last-child { border-bottom:none; }
    .company-table th { text-align:left; font-weight:700; font-size:0.88rem; color:var(--primary-color); padding:20px 24px 20px 0; width:160px; vertical-align:top; white-space:nowrap; }
    .company-table td { font-size:0.9rem; line-height:1.8; color:rgba(0,0,0,0.88); padding:20px 0; }
    .company-mission { max-width:800px; margin:0 auto 56px; text-align:center; }
    .company-mission__en { font-family:var(--font-logo); font-size:clamp(1.4rem,2.5vw,2rem); font-weight:700; color:var(--primary-color); letter-spacing:0.06em; margin-bottom:8px; }
    .company-mission__ja { font-size:0.88rem; color:rgba(0,0,0,0.78); letter-spacing:0.1em; margin-bottom:24px; }
    .company-mission p { font-size:0.92rem; line-height:1.9; color:rgba(0,0,0,0.85); max-width:600px; margin:0 auto; }
    @media (max-width:640px) { .company-table th { width:100px; padding:14px 12px 14px 0; } .company-table td { padding:14px 0; } }
</style>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">COMPANY</p>
            <h1 class="hero-title">会社概要</h1>
            <p class="hero-subtitle">医療・福祉機器の開発・販売を通じて、人の生活を支えるソリューションを届けます</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="company-mission animate-on-scroll">
                <div class="company-mission__en">Rinascente</div>
                <div class="company-mission__ja">復活する。再生する。</div>
                <p>医療・福祉の現場で、<br class="br-sp">もう一度歩く喜びを。<br class="br-sp">もう一度自分らしく生きる力を。<br>私たちは「再生」をテーマに、<br class="br-sp">人と技術の新しい関係を創造します。</p>
            </div>

            <div class="section-heading" style="max-width:800px;margin:0 auto 40px;">
                <p class="section-kicker">Overview</p>
                <h2 class="section-title">基本情報</h2>
            </div>
            <div class="animate-on-scroll" style="background:#fff;border-radius:12px;padding:36px 40px;box-shadow:0 2px 16px rgba(0,0,0,0.04);max-width:800px;margin:0 auto 56px;">
                <table class="company-table">
                    <?php if ( '' !== $company_name ) : ?>
                    <tr>
                        <th>会社名</th>
                        <td><?php echo esc_html( $company_name ); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ( $company_rows as $row ) : ?>
                    <tr>
                        <th><?php echo esc_html( $row['label'] ); ?></th>
                        <td>
                            <?php if ( 'tel' === $row['type'] ) : ?>
                                <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', (string) $row['value'] ) ); ?>" style="color:var(--primary-color);font-weight:700;"><?php echo esc_html( $row['value'] ); ?></a>
                            <?php elseif ( 'multiline' === $row['type'] ) : ?>
                                <?php echo nl2br( esc_html( (string) $row['value'] ) ); ?>
                            <?php else : ?>
                                <?php echo esc_html( (string) $row['value'] ); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="text-center" style="margin-top:48px;">
                <p style="margin-bottom:20px;font-size:0.9rem;color:rgba(0,0,0,0.82);">製品に関するお問い合わせはお気軽にどうぞ。</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">資料請求・お問い合わせ</a>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
