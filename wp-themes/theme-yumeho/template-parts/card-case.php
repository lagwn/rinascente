<?php
/**
 * Template Part: Case Study Card
 *
 * @package YUMEHO
 */

$facility_terms = get_the_terms( get_the_ID(), 'facility_type' );
$facility_slug  = ( $facility_terms && ! is_wp_error( $facility_terms ) ) ? $facility_terms[0]->slug : '';
$facility_name  = ( $facility_terms && ! is_wp_error( $facility_terms ) ) ? $facility_terms[0]->name : '';

$model_name     = get_post_meta( get_the_ID(), '_yumeho_model_name', true );
$meta_facility  = get_post_meta( get_the_ID(), '_yumeho_facility_name', true );
$meta_staff     = get_post_meta( get_the_ID(), '_yumeho_staff_name', true );
$meta_comment   = get_post_meta( get_the_ID(), '_yumeho_staff_comment', true );
$result_1_key   = get_post_meta( get_the_ID(), '_yumeho_result_1_key', true );
$result_1_val   = get_post_meta( get_the_ID(), '_yumeho_result_1_val', true );
$result_2_key   = get_post_meta( get_the_ID(), '_yumeho_result_2_key', true );
$result_2_val   = get_post_meta( get_the_ID(), '_yumeho_result_2_val', true );
$result_3_key   = get_post_meta( get_the_ID(), '_yumeho_result_3_key', true );
$result_3_val   = get_post_meta( get_the_ID(), '_yumeho_result_3_val', true );
?>

<article class="case-card" data-facility="<?php echo esc_attr( $facility_slug ); ?>">
    <div class="case-img-wrap">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'large', array( 'class' => 'case-img', 'alt' => get_the_title() . ' 導入イメージ' ) ); ?>
        <?php else : ?>
            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/case_hospital.webp' ); ?>" alt="<?php the_title_attribute(); ?>" class="case-img" decoding="async">
        <?php endif; ?>
    </div>
    <div class="case-body">
        <?php if ( $model_name ) : ?>
        <span class="case-tag"><?php echo esc_html( $model_name ); ?></span>
        <?php endif; ?>

        <h2 class="case-headline"><?php the_title(); ?></h2>

        <?php if ( $meta_facility ) : ?>
        <div class="case-facility"><?php echo esc_html( $meta_facility ); ?></div>
        <?php endif; ?>

        <?php if ( has_excerpt() ) : ?>
        <div class="case-meta"><?php echo esc_html( get_the_excerpt() ); ?></div>
        <?php endif; ?>

        <?php the_content(); ?>

        <?php if ( $result_1_key || $result_2_key || $result_3_key ) : ?>
        <div class="case-section-title">数値で見る変化</div>
        <div class="case-metrics">
            <?php if ( $result_1_key ) : ?>
            <div class="case-metrics__item">
                <span class="case-metrics__label"><?php echo esc_html( $result_1_key ); ?></span>
                <span class="case-metrics__value"><?php echo esc_html( $result_1_val ); ?></span>
            </div>
            <?php endif; ?>
            <?php if ( $result_2_key ) : ?>
            <div class="case-metrics__item">
                <span class="case-metrics__label"><?php echo esc_html( $result_2_key ); ?></span>
                <span class="case-metrics__value"><?php echo esc_html( $result_2_val ); ?></span>
            </div>
            <?php endif; ?>
            <?php if ( $result_3_key ) : ?>
            <div class="case-metrics__item">
                <span class="case-metrics__label"><?php echo esc_html( $result_3_key ); ?></span>
                <span class="case-metrics__value"><?php echo esc_html( $result_3_val ); ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $meta_comment ) : ?>
        <div class="case-pullquote"><?php echo esc_html( $meta_comment ); ?></div>
        <?php endif; ?>
    </div>
</article>
