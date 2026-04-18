<?php
/**
 * Single News Article Template
 *
 * @package Rinascente
 */

get_header();

while ( have_posts() ) : the_post();

    $post_id     = get_the_ID();
    $categories  = get_the_terms( $post_id, 'news_category' );
    $cat_name    = $categories && ! is_wp_error( $categories ) ? $categories[0]->name : '';
    $badge_class = 'badge badge-light';
    if ( '製品情報' === $cat_name ) {
        $badge_class = 'badge badge-blue';
    } elseif ( '導入事例' === $cat_name ) {
        $badge_class = 'badge badge-blue';
    } elseif ( false !== strpos( $cat_name, 'MICA' ) ) {
        $badge_class = 'badge badge-teal';
    }

    $display_title = trim( (string) get_post_meta( $post_id, '_rinascente_display_title', true ) );
    if ( '' === $display_title ) {
        $display_title = get_the_title();
    }

    $raw_content = get_the_content();
    if ( has_blocks( $raw_content ) ) {
        $article_content = apply_filters( 'the_content', $raw_content );
    } else {
        $article_content = do_shortcode( shortcode_unautop( $raw_content ) );
        if ( ! preg_match( '/<(?:p|h[1-6]|ul|ol|table|div|figure|blockquote)\b/i', $article_content ) ) {
            $article_content = wpautop( $article_content );
        }
    }
    $article_content = preg_replace( '/<p>\s*<\/p>/i', '', (string) $article_content );
    $article_content = function_exists( 'rinascente_prepare_news_content' )
        ? rinascente_prepare_news_content( $article_content )
        : $article_content;

    $news_query_args = array(
        'post_type'              => 'news',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => 'date',
        'order'                  => 'ASC',
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    );
    if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
        $news_query_args = rinascente_exclude_hidden_post_ids_from_query_args( $news_query_args, 'news' );
    }
    $ordered_news_ids = get_posts( $news_query_args );
    $current_index = array_search( $post_id, $ordered_news_ids, true );
    $prev_post_id  = ( false !== $current_index && $current_index > 0 ) ? $ordered_news_ids[ $current_index - 1 ] : 0;
    $next_post_id  = ( false !== $current_index && $current_index < count( $ordered_news_ids ) - 1 ) ? $ordered_news_ids[ $current_index + 1 ] : 0;
?>
  <div class="article-hero">
    <div class="container" style="max-width:760px;">
      <div style="margin-bottom:20px;">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'news' ) ); ?>" class="article-back-link">&larr; Press一覧に戻る</a>
      </div>
      <div class="article-meta">
        <?php if ( $cat_name ) : ?>
        <span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $cat_name ); ?></span>
        <?php endif; ?>
        <span class="article-date"><?php echo esc_html( get_the_date( 'Y年n月j日' ) ); ?></span>
      </div>
      <h1 class="article-title"><?php echo wp_kses( nl2br( esc_html( $display_title ) ), array( 'br' => array() ) ); ?></h1>
    </div>
  </div>

  <section class="section bg-white" style="padding-top:0;">
    <div class="container">
      <div class="article-body">
        <?php echo $article_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

        <?php
        rinascente_render_internal_pathways(
            'news',
            array(
                'title' => 'このお知らせの次に確認したいページ',
                'intro' => '関連するPressや導入事例、企業情報へつながる導線をまとめています。',
            )
        );
        ?>

        <div class="article-nav">
          <?php if ( $prev_post_id ) : ?>
          <a href="<?php echo esc_url( get_permalink( $prev_post_id ) ); ?>" class="article-nav-link">&larr; 前の記事</a>
          <?php else : ?>
          <span class="article-nav-link article-nav-link--disabled">&larr; 前の記事</span>
          <?php endif; ?>

          <a href="<?php echo esc_url( get_post_type_archive_link( 'news' ) ); ?>" class="btn btn-outline-dark btn-sm">Press一覧</a>

          <?php if ( $next_post_id ) : ?>
          <a href="<?php echo esc_url( get_permalink( $next_post_id ) ); ?>" class="article-nav-link">次の記事 &rarr;</a>
          <?php else : ?>
          <span class="article-nav-link article-nav-link--disabled">次の記事 &rarr;</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

<?php
endwhile;

get_footer();
?>
