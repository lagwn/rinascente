<?php
/**
 * Template Part: News Card
 *
 * Used in archive-news.php loop.
 *
 * @package Rinascente
 */

$categories  = get_the_terms( get_the_ID(), 'news_category' );
$cat_name    = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
$title       = get_the_title();
$badge_class = 'badge badge-light';
if ( $cat_name === '製品情報' ) {
    $badge_class = 'badge badge-blue';
} elseif ( $cat_name === '導入事例' ) {
    $badge_class = 'badge badge-blue';
}
?>

<a href="<?php the_permalink(); ?>" class="press-list-item" data-category="<?php echo esc_attr( $cat_name ); ?>">
  <div class="press-list-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></div>
  <div>
    <?php if ( $cat_name ) : ?>
    <div class="press-list-tags">
      <span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $cat_name ); ?></span>
    </div>
    <?php endif; ?>
    <div class="press-list-title"><?php echo esc_html( $title ); ?></div>
    <?php if ( has_excerpt() || get_the_excerpt() ) : ?>
    <div class="press-list-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></div>
    <?php endif; ?>
  </div>
</a>
