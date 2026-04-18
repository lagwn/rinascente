<?php
/**
 * Single: Column
 *
 * @package Rinascente
 */
get_header();

while ( have_posts() ) : the_post();
    $post_id   = get_the_ID();
    $col_cats  = get_the_terms( $post_id, 'column_category' );
    $cat_name  = $col_cats && ! is_wp_error( $col_cats ) ? $col_cats[0]->name : '';
    $cat_link  = $col_cats && ! is_wp_error( $col_cats ) ? get_term_link( $col_cats[0] ) : '';

    // 前後記事
    $prev_post = get_previous_post();
    $next_post = get_next_post();

    // 関連記事（同カテゴリー、自分以外、最大3件）
    $related_args = array(
        'post_type'      => 'column',
        'posts_per_page' => 3,
        'post__not_in'   => array( $post_id ),
        'orderby'        => 'rand',
    );
    if ( $col_cats && ! is_wp_error( $col_cats ) ) {
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'column_category',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $col_cats, 'term_id' ),
            ),
        );
    }
    $related_query = new WP_Query( $related_args );
?>

  <style>
    .column-single-hero {
      background: var(--bg-dark);
      color: #fff;
      padding: clamp(100px, 12vw, 160px) 0 clamp(64px, 8vw, 100px);
      position: relative;
      overflow: hidden;
    }
    .column-single-hero::before {
      content: "";
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse at top right, rgba(200,169,110,0.08), transparent 50%);
      pointer-events: none;
    }
    .column-single-hero .container {
      position: relative;
      z-index: 1;
      max-width: 760px;
      padding-left: clamp(24px, 5vw, 40px);
      padding-right: clamp(16px, 3vw, 24px);
    }
    .column-single-breadcrumb {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.75rem;
      color: rgba(255,255,255,0.55);
      margin-bottom: 28px;
      flex-wrap: wrap;
    }
    .column-single-breadcrumb a {
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      transition: color 0.2s;
    }
    .column-single-breadcrumb a:hover {
      color: var(--gold);
    }
    .column-single-breadcrumb__sep {
      opacity: 0.4;
    }
    .column-single-category {
      display: inline-block;
      padding: 6px 16px;
      background: rgba(200,169,110,0.15);
      border: 1px solid rgba(200,169,110,0.3);
      border-radius: 999px;
      font-size: 0.72rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      color: var(--gold);
      text-transform: uppercase;
      margin-bottom: 24px;
    }
    .column-single-title {
      font-size: clamp(1.6rem, 3.2vw, 2.6rem);
      font-weight: 700;
      line-height: 1.5;
      color: #fff;
      margin: 0 0 24px;
      max-width: 820px;
      word-break: keep-all;
      overflow-wrap: anywhere;
    }
    .column-single-meta {
      display: flex;
      gap: 24px;
      flex-wrap: wrap;
      font-size: 0.82rem;
      color: rgba(255,255,255,0.6);
    }
    .column-single-meta strong {
      color: rgba(255,255,255,0.9);
      font-weight: 600;
      margin-right: 6px;
    }

    .column-single-body {
      padding: clamp(48px, 6vw, 88px) 0;
    }
    .column-single-body .container,
    .column-related .container {
      max-width: 760px;
      padding-inline: clamp(16px, 3vw, 24px);
    }
    .column-single-content {
      max-width: 720px;
      margin: 0 auto;
      font-size: 1rem;
      line-height: 2;
      color: rgba(0,0,0,0.85);
    }
    .column-single-content h2 {
      font-size: clamp(1.25rem, 1.8vw, 1.5rem);
      font-weight: 700;
      color: var(--charcoal);
      margin: clamp(40px, 5vw, 64px) 0 clamp(16px, 2vw, 24px);
      padding-bottom: 12px;
      border-bottom: 2px solid var(--gold);
      position: relative;
    }
    .column-single-content h3 {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--charcoal);
      margin: clamp(32px, 4vw, 44px) 0 14px;
      padding-left: 16px;
      border-left: 4px solid var(--gold);
    }
    .column-single-content p {
      margin-bottom: 1.6em;
    }
    .column-single-content table {
      width: 100%;
      border-collapse: collapse;
      margin: 1.8em 0;
    }
    .column-single-content th,
    .column-single-content td {
      padding: 20px 28px;
      text-align: left;
      vertical-align: middle;
      border-bottom: 1px solid #d8d0c4;
    }
    .column-single-content th {
      width: 38%;
      font-weight: 700;
      color: #5e5a52;
      background: #f4efe6;
    }
    .column-single-content td {
      color: var(--charcoal);
    }
    .column-single-content ul,
    .column-single-content ol {
      padding-left: 1.6em;
      margin-bottom: 1.6em;
    }
    .column-single-content li {
      margin-bottom: 0.6em;
    }
    .column-single-content strong {
      font-weight: 700;
      color: var(--charcoal);
    }
    .column-single-content a {
      color: var(--gold-deep);
      text-decoration: underline;
      text-underline-offset: 3px;
    }
    .column-single-content blockquote {
      margin: clamp(28px, 3vw, 40px) 0;
      padding: 24px 28px;
      background: rgba(200,169,110,0.06);
      border-left: 4px solid var(--gold);
      border-radius: 0 6px 6px 0;
      font-style: italic;
      color: rgba(0,0,0,0.75);
    }

    .column-single-nav {
      max-width: 760px;
      margin: clamp(56px, 6vw, 80px) auto 0;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .column-single-nav a {
      display: block;
      padding: 20px 24px;
      background: #fff;
      border: 1px solid var(--line-light);
      border-radius: var(--r-lg);
      text-decoration: none;
      color: inherit;
      transition: border-color 0.25s, transform 0.25s;
    }
    .column-single-nav a:hover {
      border-color: var(--gold-deep);
      transform: translateY(-2px);
    }
    .column-single-nav__label {
      display: block;
      font-size: 0.72rem;
      color: var(--mid-gray);
      letter-spacing: 0.08em;
      margin-bottom: 6px;
    }
    .column-single-nav__title {
      display: block;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--charcoal);
      line-height: 1.5;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .column-single-nav__item--next {
      text-align: right;
    }
    .column-single-nav__empty {
      padding: 20px 24px;
      background: rgba(0,0,0,0.03);
      border: 1px dashed var(--line-light);
      border-radius: var(--r-lg);
      color: var(--mid-gray);
      font-size: 0.85rem;
    }

    .column-related {
      background: var(--bg-cream);
      padding: clamp(56px, 7vw, 96px) 0;
    }
    .column-related__title {
      text-align: center;
      font-size: clamp(1.25rem, 2vw, 1.6rem);
      font-weight: 700;
      color: var(--charcoal);
      margin: 0 0 clamp(32px, 4vw, 48px);
      letter-spacing: 0.04em;
    }
    .column-related__title::before {
      content: "";
      display: block;
      width: 40px;
      height: 2px;
      background: var(--gold);
      margin: 0 auto 14px;
    }
    .column-related-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }
    .column-related-card {
      display: flex;
      flex-direction: column;
      background: #fff;
      border: 1px solid var(--line-light);
      border-radius: var(--r-lg);
      padding: 24px 22px;
      text-decoration: none;
      color: inherit;
      transition: transform 0.25s, box-shadow 0.25s;
    }
    .column-related-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 14px 32px rgba(0,0,0,0.06);
    }
    .column-related-card__category {
      font-size: 0.65rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      color: var(--gold-deep);
      margin-bottom: 10px;
      text-transform: uppercase;
    }
    .column-related-card__title {
      font-size: 0.9rem;
      font-weight: 700;
      line-height: 1.55;
      color: var(--charcoal);
      margin: 0 0 12px;
      flex: 1;
    }
    .column-related-card__date {
      font-size: 0.72rem;
      color: var(--mid-gray);
      padding-top: 12px;
      border-top: 1px solid var(--line-light);
    }
    .column-related-back {
      text-align: center;
      margin-top: clamp(36px, 4vw, 56px);
    }

    @media (max-width: 860px) {
      .column-related-grid {
        grid-template-columns: 1fr;
      }
      .column-single-nav {
        grid-template-columns: 1fr;
      }
      .column-single-hero .container,
      .column-single-body .container,
      .column-related .container {
        width: min(760px, 100% - 32px);
      }
      .column-single-content th,
      .column-single-content td {
        padding: 14px 16px;
      }
    }

    @media (max-width: 640px) {
      .column-single-hero {
        padding: 76px 0 32px;
      }
      .column-single-body {
        padding: 40px 0 56px;
      }
      .column-single-title {
        font-size: clamp(1.4rem, 6vw, 1.9rem);
      }
    }

    @media (max-width: 375px) {
      .column-single-hero .container,
      .column-single-body .container,
      .column-related .container {
        width: min(760px, 100% - 16px);
      }
    }
  </style>

  <!-- Hero -->
  <section class="column-single-hero">
    <div class="container">
      <div class="column-single-breadcrumb">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
        <span class="column-single-breadcrumb__sep">/</span>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'column' ) ); ?>">Column</a>
        <?php if ( $cat_name && $cat_link ) : ?>
        <span class="column-single-breadcrumb__sep">/</span>
        <a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
        <?php endif; ?>
      </div>

      <?php if ( $cat_name ) : ?>
      <div class="column-single-category"><?php echo esc_html( $cat_name ); ?></div>
      <?php endif; ?>

      <h1 class="column-single-title"><?php the_title(); ?></h1>

      <div class="column-single-meta">
        <span><strong>投稿日</strong><?php echo esc_html( get_the_date( 'Y年n月j日' ) ); ?></span>
        <?php if ( get_the_modified_date() !== get_the_date() ) : ?>
        <span><strong>更新日</strong><?php echo esc_html( get_the_modified_date( 'Y年n月j日' ) ); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Body -->
  <section class="column-single-body bg-white">
    <div class="container">
      <article class="column-single-content">
        <?php the_content(); ?>
      </article>

      <?php
      rinascente_render_internal_pathways(
        'column',
        array(
          'title' => 'コラムとあわせて確認したいページ',
          'intro' => '関連記事だけでなく、会社情報や導入事例にも移りやすいように導線を整理しています。',
        )
      );
      ?>

      <!-- 前後記事 -->
      <nav class="column-single-nav">
        <?php if ( $prev_post ) : ?>
        <a href="<?php echo esc_url( get_permalink( $prev_post ) ); ?>">
          <span class="column-single-nav__label">← 前の記事</span>
          <span class="column-single-nav__title"><?php echo esc_html( get_the_title( $prev_post ) ); ?></span>
        </a>
        <?php else : ?>
        <div class="column-single-nav__empty">前の記事はありません</div>
        <?php endif; ?>

        <?php if ( $next_post ) : ?>
        <a href="<?php echo esc_url( get_permalink( $next_post ) ); ?>" class="column-single-nav__item--next">
          <span class="column-single-nav__label">次の記事 →</span>
          <span class="column-single-nav__title"><?php echo esc_html( get_the_title( $next_post ) ); ?></span>
        </a>
        <?php else : ?>
        <div class="column-single-nav__empty">次の記事はありません</div>
        <?php endif; ?>
      </nav>
    </div>
  </section>

  <!-- 関連記事 -->
  <?php if ( $related_query->have_posts() ) : ?>
  <section class="column-related">
    <div class="container">
      <h2 class="column-related__title">関連するコラム</h2>
      <div class="column-related-grid">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post();
          $r_cats = get_the_terms( get_the_ID(), 'column_category' );
          $r_cat_name = $r_cats && ! is_wp_error( $r_cats ) ? $r_cats[0]->name : '';
        ?>
        <a href="<?php the_permalink(); ?>" class="column-related-card">
          <?php if ( $r_cat_name ) : ?>
          <div class="column-related-card__category"><?php echo esc_html( $r_cat_name ); ?></div>
          <?php endif; ?>
          <h3 class="column-related-card__title"><?php the_title(); ?></h3>
          <div class="column-related-card__date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></div>
        </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>

      <div class="column-related-back">
        <a href="<?php echo esc_url( get_post_type_archive_link( 'column' ) ); ?>" class="btn btn-outline-dark">コラム一覧に戻る</a>
      </div>
    </div>
  </section>
  <?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
