<?php
/**
 * 導入事例一覧カード（要約表示）
 *
 * @package YUMEHO
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$pid = get_the_ID();

$facility   = get_post_meta( $pid, '_yumeho_case_facility_name', true );
$install    = get_post_meta( $pid, '_yumeho_case_install_date', true );
$location   = get_post_meta( $pid, '_yumeho_case_location', true );
$model      = get_post_meta( $pid, '_yumeho_case_product_model', true );
$image_fit  = get_post_meta( $pid, '_yumeho_case_image_fit', true );
$challenge  = get_post_meta( $pid, '_yumeho_case_challenge', true );
$reason     = get_post_meta( $pid, '_yumeho_case_reason', true );
$change_txt = get_post_meta( $pid, '_yumeho_case_change', true );

$facility_terms = get_the_terms( $pid, 'facility_type' );
$facility_type  = ( $facility_terms && ! is_wp_error( $facility_terms ) ) ? $facility_terms[0]->name : '';

$metric_label = get_post_meta( $pid, '_yumeho_case_metric_1_label', true );
$metric_value = get_post_meta( $pid, '_yumeho_case_metric_1_value', true );

$summary_source = $change_txt ?: get_the_excerpt() ?: $challenge ?: $reason ?: get_the_content( null, false, $pid );
$summary_plain  = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $summary_source ) ) );
$summary_text   = $summary_plain;

if ( function_exists( 'mb_strimwidth' ) ) {
    $summary_text = mb_strimwidth( $summary_plain, 0, 120, '…', 'UTF-8' );
} elseif ( strlen( $summary_plain ) > 120 ) {
    $summary_text = substr( $summary_plain, 0, 120 ) . '…';
}

$metric_value_plain = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $metric_value ) ) );
$meta_parts         = array_filter( array( $location, $install ) );
$meta_text          = implode( ' ・ ', $meta_parts );
$card_label         = $facility ?: get_the_title();

$image_url = get_the_post_thumbnail_url( $pid, 'large' );
if ( ! $image_url ) {
    $image_url = YUMEHO_URI . '/assets/img/case_hospital.webp';
}

$image_style = ( false !== strpos( (string) $image_fit, 'contain' ) )
    ? 'object-fit:contain;background:#f5f5f5;'
    : '';
?>
<article class="case-archive-card">
    <a href="<?php the_permalink(); ?>" class="case-archive-card__link" aria-label="<?php echo esc_attr( $card_label . ' の導入事例を見る' ); ?>">
        <div class="case-archive-card__media">
            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $card_label ); ?> 導入イメージ" class="case-archive-card__image"<?php echo $image_style ? ' style="' . esc_attr( $image_style ) . '"' : ''; ?>>
        </div>

        <div class="case-archive-card__body">
            <div class="case-archive-card__chips">
                <?php if ( $facility_type ) : ?>
                <span class="case-archive-card__chip"><?php echo esc_html( $facility_type ); ?></span>
                <?php endif; ?>
                <?php if ( $model ) : ?>
                <span class="case-archive-card__chip case-archive-card__chip--accent"><?php echo esc_html( $model ); ?></span>
                <?php endif; ?>
            </div>

            <?php if ( $metric_value_plain ) : ?>
            <div class="case-archive-card__highlight">
                <span class="case-archive-card__highlight-value"><?php echo esc_html( $metric_value_plain ); ?></span>
                <?php if ( $metric_label ) : ?>
                <span class="case-archive-card__highlight-label"><?php echo esc_html( $metric_label ); ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <h2 class="case-archive-card__title"><?php the_title(); ?></h2>

            <?php if ( $summary_text ) : ?>
            <p class="case-archive-card__summary"><?php echo esc_html( $summary_text ); ?></p>
            <?php endif; ?>

            <div class="case-archive-card__facility">
                <div class="case-archive-card__facility-row">
                    <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/man.svg' ); ?>" alt="" class="case-archive-card__icon" loading="lazy" decoding="async">
                    <div>
                        <?php if ( $facility ) : ?>
                        <div class="case-archive-card__facility-name"><?php echo esc_html( $facility ); ?></div>
                        <?php endif; ?>
                        <?php if ( $meta_text ) : ?>
                        <div class="case-archive-card__facility-meta"><?php echo esc_html( $meta_text ); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="case-archive-card__cta">
                <span>導入事例の詳細を見る</span>
                <span class="case-archive-card__cta-arrow" aria-hidden="true">&rarr;</span>
            </div>
        </div>
    </a>
</article>
