<?php
/**
 * News Archive Template
 *
 * @package Rinascente
 */

get_header();
?>
<?php
$featured_news = function_exists( 'rinascente_get_press_featured_news_query' ) ? rinascente_get_press_featured_news_query() : new WP_Query();

$news_categories = get_terms(
    array(
        'taxonomy'   => 'news_category',
        'hide_empty' => true,
    )
);

$category_order = array( '会社情報', '製品情報', '導入事例', '受賞・認証', '事業展開' );
$ordered_categories = array();
$current_news_term   = is_tax( 'news_category' ) ? get_queried_object() : null;

if ( $news_categories && ! is_wp_error( $news_categories ) ) {
    $indexed_categories = array();

    foreach ( $news_categories as $category ) {
        $indexed_categories[ $category->name ] = $category;
    }

    foreach ( $category_order as $category_name ) {
        if ( isset( $indexed_categories[ $category_name ] ) ) {
            $ordered_categories[] = $indexed_categories[ $category_name ];
            unset( $indexed_categories[ $category_name ] );
        }
    }

    if ( $indexed_categories ) {
        foreach ( $indexed_categories as $category ) {
            $ordered_categories[] = $category;
        }
    }
}
?>

  <div class="page-hero">
    <div class="container">
      <div class="page-hero-label">News &amp; Press Release</div>
      <h1><?php echo esc_html( $current_news_term && ! is_wp_error( $current_news_term ) ? $current_news_term->name : 'Press' ); ?></h1>
      <p><?php echo esc_html( $current_news_term && ! is_wp_error( $current_news_term ) ? rinascente_term_archive_fallback_description( $current_news_term ) : 'Rinascente グループの最新ニュース、製品情報、企業発表をお届けします。' ); ?></p>
    </div>
  </div>

  <section class="section bg-cream">
    <div class="container">

      <?php
      if ( $featured_news->have_posts() ) :
          $featured_news->the_post();

          $featured_categories    = get_the_terms( get_the_ID(), 'news_category' );
          $featured_category_name = $featured_categories && ! is_wp_error( $featured_categories ) ? $featured_categories[0]->name : 'ニュース';
          $featured_title         = get_the_title();
          $featured_date          = get_the_date( 'Y.m.d' );
          $featured_excerpt       = get_the_excerpt();
          $featured_badge_class   = 'badge badge-light';

          if ( in_array( $featured_category_name, array( '製品情報', '導入事例' ), true ) ) {
              $featured_badge_class = 'badge badge-blue';
          }
          ?>
      <div class="press-featured-wrap fade-up">
        <span class="label press-featured-label">Featured</span>
        <a href="<?php the_permalink(); ?>" class="press-featured">
          <div class="press-featured__content">
            <span class="press-featured__eyebrow">Press Highlight</span>
            <div class="press-featured__meta">
              <span class="<?php echo esc_attr( $featured_badge_class ); ?>"><?php echo esc_html( $featured_category_name ); ?></span>
              <span class="press-featured__date"><?php echo esc_html( $featured_date ); ?></span>
            </div>
            <h2 class="press-featured__title"><?php echo esc_html( $featured_title ); ?></h2>
            <?php if ( $featured_excerpt ) : ?>
            <p class="press-featured__excerpt"><?php echo esc_html( $featured_excerpt ); ?></p>
            <?php endif; ?>
            <div class="press-featured__footer">
              <div class="press-featured__info">
                <div class="press-featured__info-item">
                  <span class="press-featured__info-label">Category</span>
                  <span class="press-featured__info-value"><?php echo esc_html( $featured_category_name ); ?></span>
                </div>
                <div class="press-featured__info-item">
                  <span class="press-featured__info-label">Published</span>
                  <span class="press-featured__info-value"><?php echo esc_html( $featured_date ); ?></span>
                </div>
              </div>
              <span class="press-featured__cta">
                続きを読む <span aria-hidden="true">&rarr;</span>
              </span>
            </div>
          </div>
        </a>
      </div>
          <?php
          wp_reset_postdata();
      endif;
      ?>

      <div class="press-filter fade-up">
        <?php if ( $current_news_term && ! is_wp_error( $current_news_term ) ) : ?>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'news' ) ); ?>" class="filter-btn">すべて</a>
        <?php foreach ( $ordered_categories as $category ) : ?>
        <a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="filter-btn<?php echo ( (int) $current_news_term->term_id === (int) $category->term_id ) ? ' active' : ''; ?>"><?php echo esc_html( $category->name ); ?></a>
        <?php endforeach; ?>
        <?php else : ?>
        <button class="filter-btn active" data-filter="すべて">すべて</button>
        <?php foreach ( $ordered_categories as $category ) : ?>
        <button class="filter-btn" data-filter="<?php echo esc_attr( $category->name ); ?>"><?php echo esc_html( $category->name ); ?></button>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="fade-up d-100">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                if ( function_exists( 'rinascente_is_mica30_related_post' ) && rinascente_is_mica30_related_post( get_post() ) ) {
                    continue;
                }
                get_template_part( 'template-parts/card-news' );
            endwhile;
        else :
            ?>
        <p style="color:var(--mid-gray);font-size:0.9rem;">ニュースはまだ投稿されていません。</p>
        <?php endif; ?>
      </div>

      <?php
      $pagination_links = paginate_links(
          array(
              'type'      => 'array',
              'mid_size'  => 1,
              'prev_text' => '&lsaquo;',
              'next_text' => '&rsaquo;',
          )
      );

      if ( $pagination_links ) :
          ?>
      <nav class="press-pagination fade-up" aria-label="ページネーション">
        <div class="nav-links">
          <?php foreach ( $pagination_links as $pagination_link ) : ?>
            <?php echo wp_kses_post( $pagination_link ); ?>
          <?php endforeach; ?>
        </div>
      </nav>
      <?php endif; ?>

      <?php
      rinascente_render_internal_pathways(
        'news_archive',
        array(
          'title' => 'Pressとあわせて確認したいページ',
          'intro' => '製品情報や企業発表を見ながら、会社情報、導入事例、関連コラムへ移りやすい導線をまとめています。',
        )
      );
      ?>

    </div>
  </section>

  <?php if ( ! $current_news_term || is_wp_error( $current_news_term ) ) : ?>
  <script>
  (function(){
    const items = document.querySelectorAll('.press-list-item');
    document.querySelectorAll('.filter-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        var filter = btn.dataset.filter;
        items.forEach(function(item){
          var show = filter === 'すべて' || item.dataset.category === filter;
          item.style.display = show ? '' : 'none';
          item.style.borderBottom = '';
        });
        var visible = Array.from(items).filter(function(i){ return i.style.display !== 'none'; });
        if (visible.length) visible[visible.length - 1].style.borderBottom = 'none';
      });
    });
  })();
  </script>
  <?php endif; ?>

<?php get_footer(); ?>
