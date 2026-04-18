<?php
/**
 * Template Name: Press
 *
 * Press page template for Rinascente corporate site.
 * Displays featured and list content from the News post type.
 *
 * @package Rinascente
 */

get_header();

$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$featured_news = function_exists( 'rinascente_get_press_featured_news_query' ) ? rinascente_get_press_featured_news_query() : new WP_Query();

$news_categories = get_terms(
    array(
        'taxonomy'   => 'news_category',
        'hide_empty' => true,
    )
);

$category_order     = array( '会社情報', '製品情報', '導入事例', '受賞・認証', '事業展開' );
$ordered_categories = array();

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

$news_query_args = array(
    'post_type'      => 'news',
    'posts_per_page' => 6,
    'paged'          => $paged,
);

if ( function_exists( 'rinascente_exclude_hidden_post_ids_from_query_args' ) ) {
    $news_query_args = rinascente_exclude_hidden_post_ids_from_query_args( $news_query_args, 'news' );
}

$news_query = new WP_Query( $news_query_args );
?>

    <div class="page-hero">
      <div class="container">
        <div class="page-hero-label">News &amp; Press Release</div>
        <h1>Press</h1>
        <p>Rinascente グループの最新ニュース、製品情報、企業発表をお届けします。</p>
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
          <button class="filter-btn active" data-filter="すべて">すべて</button>
          <?php foreach ( $ordered_categories as $category ) : ?>
          <button class="filter-btn" data-filter="<?php echo esc_attr( $category->name ); ?>"><?php echo esc_html( $category->name ); ?></button>
          <?php endforeach; ?>
        </div>

        <div class="fade-up d-100">
          <?php
          if ( $news_query->have_posts() ) :
              while ( $news_query->have_posts() ) :
                  $news_query->the_post();

                  if ( function_exists( 'rinascente_is_mica30_related_post' ) && rinascente_is_mica30_related_post( get_post() ) ) {
                      continue;
                  }

                  get_template_part( 'template-parts/card-news' );
              endwhile;
              wp_reset_postdata();
          else :
              ?>
          <p style="color:var(--mid-gray);font-size:0.9rem;">ニュースはまだ投稿されていません。</p>
          <?php endif; ?>
        </div>

        <?php
        $pagination_links = paginate_links(
            array(
                'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                'format'    => '',
                'current'   => $paged,
                'total'     => max( 1, (int) $news_query->max_num_pages ),
                'type'      => 'array',
                'mid_size'  => 1,
                'prev_text' => '&lsaquo;',
                'next_text' => '&rsaquo;',
            )
        );

        if ( $pagination_links && $news_query->max_num_pages > 1 ) :
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

<script>
  (function() {
    const items = document.querySelectorAll('.press-list-item');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(function(btn) {
      btn.addEventListener('click', function() {
        buttons.forEach(function(button) {
          button.classList.remove('active');
        });

        btn.classList.add('active');

        const filter = btn.dataset.filter;

        items.forEach(function(item) {
          const show = filter === 'すべて' || item.dataset.category === filter;
          item.style.display = show ? '' : 'none';
          item.style.borderBottom = '';
        });

        const visibleItems = Array.from(items).filter(function(item) {
          return item.style.display !== 'none';
        });

        if ( visibleItems.length ) {
          visibleItems[visibleItems.length - 1].style.borderBottom = 'none';
        }
      });
    });
  })();
</script>

<?php get_footer(); ?>
