<?php
/**
 * Archive: Column
 *
 * @package Rinascente
 */
get_header();
?>

  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container">
      <?php
      $hero_label = 'Column';
      $hero_title = 'Column';
      $hero_desc  = '医療・福祉の現場に役立つ最新動向やノウハウをお届けします。';

      if ( is_tax( 'column_category' ) ) {
          $current_term = get_queried_object();
          if ( $current_term && ! is_wp_error( $current_term ) ) {
              $hero_label = 'Column / ' . $current_term->name;
              $hero_title = $current_term->name;
              $hero_desc  = function_exists( 'rinascente_term_archive_fallback_description' )
                  ? rinascente_term_archive_fallback_description( $current_term )
                  : ( $current_term->description ? $current_term->description : '「' . $current_term->name . '」カテゴリーの記事一覧' );
          }
      }

      // 日本語が含まれているか判定
      $is_jp_title = preg_match( '/[\x{3000}-\x{303F}\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{4E00}-\x{9FFF}\x{FF00}-\x{FFEF}]/u', $hero_title );
      ?>
      <div class="page-hero-label"><?php echo esc_html( $hero_label ); ?></div>
      <h1<?php if ( $is_jp_title ) echo ' class="is-jp"'; ?>><?php echo esc_html( $hero_title ); ?></h1>
      <p><?php echo esc_html( $hero_desc ); ?></p>
    </div>
  </div>

  <style>
    .column-archive {
      padding: clamp(48px, 7vw, 96px) 0;
    }
    .column-filter {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: clamp(32px, 4vw, 48px);
      justify-content: center;
    }
    .column-filter-btn {
      padding: 8px 18px;
      border: 1px solid rgba(0,0,0,0.12);
      background: #fff;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.04em;
      color: var(--charcoal);
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s, color 0.2s, border-color 0.2s;
    }
    .column-filter-btn:hover {
      border-color: var(--gold-deep);
      color: var(--gold-deep);
    }
    .column-filter-btn.is-active {
      background: var(--charcoal);
      color: #fff;
      border-color: var(--charcoal);
    }
    .column-archive-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: clamp(20px, 2.5vw, 32px);
    }
    .column-archive-card {
      display: flex;
      flex-direction: column;
      background: #fff;
      border: 1px solid var(--line-light);
      border-radius: var(--r-lg);
      padding: 32px 28px;
      text-decoration: none;
      color: inherit;
      transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.3s;
    }
    .column-archive-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 18px 40px rgba(0,0,0,0.08);
    }
    .column-archive-card__category {
      font-size: 0.7rem;
      font-weight: 700;
      letter-spacing: 0.14em;
      color: var(--gold-deep);
      margin-bottom: 14px;
      text-transform: uppercase;
    }
    .column-archive-card__title {
      font-size: 1.1rem;
      font-weight: 700;
      line-height: 1.6;
      color: var(--charcoal);
      margin: 0 0 16px;
      flex: 1;
    }
    .column-archive-card__excerpt {
      font-size: 0.85rem;
      line-height: 1.8;
      color: var(--mid-gray);
      margin: 0 0 20px;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .column-archive-card__meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.75rem;
      color: var(--mid-gray);
      padding-top: 16px;
      border-top: 1px solid var(--line-light);
    }
    .column-archive-card__read {
      font-weight: 700;
      color: var(--charcoal);
    }
    .column-archive-pagination {
      margin-top: clamp(48px, 6vw, 80px);
    }
    .column-archive-pagination .nav-links {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;
      gap: 10px;
    }
    .column-archive-pagination .page-numbers {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 44px;
      height: 44px;
      padding: 0 14px;
      border: 1px solid rgba(0,0,0,0.12);
      border-radius: 999px;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--charcoal);
      text-decoration: none;
      background: #fff;
      transition: all 0.25s cubic-bezier(0.2, 0.8, 0.2, 1);
      font-family: var(--font-display, 'Cormorant Garamond', serif);
      font-style: italic;
      font-weight: 400;
      letter-spacing: 0.02em;
    }
    .column-archive-pagination a.page-numbers:hover {
      border-color: var(--gold-deep);
      color: var(--gold-deep);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(200,169,110,0.15);
    }
    .column-archive-pagination .page-numbers.current {
      background: var(--charcoal);
      color: #fff;
      border-color: var(--charcoal);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .column-archive-pagination .page-numbers.dots {
      border-color: transparent;
      background: transparent;
      color: var(--mid-gray);
    }
    .column-archive-pagination .page-numbers.prev,
    .column-archive-pagination .page-numbers.next {
      padding: 0 20px;
      font-family: inherit;
      font-size: 0.78rem;
      font-weight: 600;
      font-style: normal;
      letter-spacing: 0.08em;
    }
    @media (max-width: 640px) {
      .column-archive-pagination .page-numbers {
        min-width: 40px;
        height: 40px;
        padding: 0 10px;
        font-size: 0.8rem;
      }
      .column-archive-pagination .page-numbers.prev,
      .column-archive-pagination .page-numbers.next {
        padding: 0 14px;
        font-size: 0.72rem;
      }
    }
    .column-archive-empty {
      text-align: center;
      padding: 80px 0;
      color: var(--mid-gray);
    }

    @media (max-width: 860px) {
      .column-archive-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
    @media (max-width: 640px) {
      .column-archive-grid {
        grid-template-columns: 1fr;
      }
      .column-archive-card {
        padding: 24px 20px;
      }
    }
  </style>

  <section class="column-archive bg-cream">
    <div class="container">

      <?php
      $all_cats = get_terms( array(
          'taxonomy'   => 'column_category',
          'hide_empty' => true,
      ) );
      $current_cat = is_tax( 'column_category' ) ? get_queried_object() : null;
      ?>

      <?php if ( ! empty( $all_cats ) && ! is_wp_error( $all_cats ) ) : ?>
      <div class="column-filter">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'column' ) ); ?>"
           class="column-filter-btn<?php if ( ! $current_cat ) echo ' is-active'; ?>">すべて</a>
        <?php foreach ( $all_cats as $cat ) : ?>
        <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
           class="column-filter-btn<?php if ( $current_cat && $current_cat->term_id === $cat->term_id ) echo ' is-active'; ?>"><?php echo esc_html( $cat->name ); ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ( have_posts() ) : ?>
      <div class="column-archive-grid">
        <?php while ( have_posts() ) : the_post();
          $col_cats = get_the_terms( get_the_ID(), 'column_category' );
          $col_cat_name = $col_cats && ! is_wp_error( $col_cats ) ? $col_cats[0]->name : '';
        ?>
        <a href="<?php the_permalink(); ?>" class="column-archive-card">
          <?php if ( $col_cat_name ) : ?>
          <div class="column-archive-card__category"><?php echo esc_html( $col_cat_name ); ?></div>
          <?php endif; ?>
          <h2 class="column-archive-card__title"><?php the_title(); ?></h2>
          <?php if ( has_excerpt() ) : ?>
          <p class="column-archive-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
          <?php endif; ?>
          <div class="column-archive-card__meta">
            <span><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
            <span class="column-archive-card__read">続きを読む →</span>
          </div>
        </a>
        <?php endwhile; ?>
      </div>

      <div class="column-archive-pagination">
        <?php
        the_posts_pagination( array(
            'prev_text' => '← 前へ',
            'next_text' => '次へ →',
        ) );
        ?>
      </div>
      <?php else : ?>
      <div class="column-archive-empty">
        <p>コラムはまだ投稿されていません。</p>
      </div>
      <?php endif; ?>

      <?php
      rinascente_render_internal_pathways(
        'column_archive',
        array(
          'title' => 'コラムとあわせて確認したいページ',
          'intro' => '医療・福祉の現場情報を読み進めながら、会社情報、導入事例、Press の更新にもつながる導線をまとめています。',
        )
      );
      ?>

    </div>
  </section>

<?php get_footer(); ?>
