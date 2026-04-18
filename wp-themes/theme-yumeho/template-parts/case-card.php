<?php
/**
 * 導入事例カード（動的レンダリング）
 *
 * 必須: $post をループ内で呼び出すこと
 * 使い方:
 *   while ( have_posts() ) : the_post();
 *       get_template_part( 'template-parts/case-card' );
 *   endwhile;
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$pid = get_the_ID();

$facility   = get_post_meta( $pid, '_yumeho_case_facility_name', true );
$install    = get_post_meta( $pid, '_yumeho_case_install_date', true );
$location   = get_post_meta( $pid, '_yumeho_case_location', true );
$model      = get_post_meta( $pid, '_yumeho_case_product_model', true );
$image_fit  = get_post_meta( $pid, '_yumeho_case_image_fit', true );
$challenge  = get_post_meta( $pid, '_yumeho_case_challenge', true );
$reason     = get_post_meta( $pid, '_yumeho_case_reason', true );
$change_txt = get_post_meta( $pid, '_yumeho_case_change', true );
$ringi      = get_post_meta( $pid, '_yumeho_case_ringi_process', true );
$quote      = get_post_meta( $pid, '_yumeho_case_pullquote', true );
$speaker    = get_post_meta( $pid, '_yumeho_case_pullquote_speaker', true );
$challenge_html = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $challenge ) : wpautop( wp_kses_post( $challenge ) );
$reason_html    = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $reason ) : wpautop( wp_kses_post( $reason ) );
$change_html    = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $change_txt ) : wpautop( wp_kses_post( $change_txt ) );
$ringi_html     = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $ringi ) : wpautop( wp_kses_post( $ringi ) );
$quote_html     = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $quote ) : wpautop( wp_kses_post( $quote ) );

// メイン画像（アイキャッチ）
$image_url = get_the_post_thumbnail_url( $pid, 'large' );
if ( ! $image_url ) {
    $image_url = YUMEHO_URI . '/assets/img/case_hospital.webp';
}
$image_style = ( false !== strpos( $image_fit, 'contain' ) )
    ? 'object-fit:contain;background:#f5f5f5;'
    : '';

// メタ表示（導入年月 + 所在地）
$meta_parts = array_filter( array( $install, $location ) );
$meta_text  = implode( ' ─ ', $meta_parts );
?>
<article class="case-card">
    <div class="case-img-wrap">
        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $facility ); ?> 導入イメージ" class="case-img"<?php echo $image_style ? ' style="' . esc_attr( $image_style ) . '"' : ''; ?>>
    </div>
    <div class="case-body">
        <?php if ( $model ) : ?>
        <span class="case-tag"><?php echo esc_html( $model ); ?></span>
        <?php endif; ?>

        <h2 class="case-headline"><?php the_title(); ?></h2>

        <?php if ( $facility ) : ?>
        <div class="case-facility"><?php echo esc_html( $facility ); ?></div>
        <?php endif; ?>

        <?php if ( $meta_text ) : ?>
        <div class="case-meta"><?php echo esc_html( $meta_text ); ?></div>
        <?php endif; ?>

        <?php if ( $challenge ) : ?>
        <div class="case-section-title">課題</div>
        <div class="case-rich-text"><?php echo $challenge_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif; ?>

        <?php if ( $reason ) : ?>
        <div class="case-section-title">決め手</div>
        <div class="case-rich-text"><?php echo $reason_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif; ?>

        <?php if ( $change_txt ) : ?>
        <div class="case-section-title">変化</div>
        <div class="case-rich-text"><?php echo $change_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif; ?>

        <?php
        // 数値成果（3項目）
        $metrics = array();
        for ( $i = 1; $i <= 3; $i++ ) {
            $label = get_post_meta( $pid, '_yumeho_case_metric_' . $i . '_label', true );
            $value = get_post_meta( $pid, '_yumeho_case_metric_' . $i . '_value', true );
            if ( $label || $value ) {
                $metrics[] = array( 'label' => $label, 'value' => $value );
            }
        }
        if ( ! empty( $metrics ) ) :
        ?>
        <div class="case-section-title">数値で見る変化</div>
        <div class="case-metrics">
            <?php foreach ( $metrics as $m ) : ?>
            <?php
            $metric_html = function_exists( 'yumeho_case_format_rich_text' ) ? yumeho_case_format_rich_text( $m['value'] ) : wpautop( wp_kses_post( $m['value'] ) );
            ?>
            <div class="case-metrics__item">
                <span class="case-metrics__label"><?php echo esc_html( $m['label'] ); ?></span>
                <div class="case-metrics__value"><?php echo $metric_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $ringi ) : ?>
        <div class="case-ringi-process">
            <div class="case-section-title">導入決定までの経緯</div>
            <div class="case-rich-text case-rich-text--compact"><?php echo $ringi_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        </div>
        <?php endif; ?>

        <?php if ( $quote ) : ?>
        <div class="case-pullquote">
            <div class="case-pullquote__text"><?php echo $quote_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            <?php if ( $speaker ) : ?>
            <div class="case-pullquote__speaker">─ <?php echo esc_html( $speaker ); ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</article>
