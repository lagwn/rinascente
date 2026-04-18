<?php
/**
 * メインテンプレート（WordPress 必須ファイル）
 * 他のテンプレートにマッチしない場合のフォールバック
 */
get_header();
?>

<section class="hero bg-light">
    <div class="container text-center">
        <h1 class="hero-title"><?php wp_title( '', true ); ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; the_posts_pagination(); else : ?>
            <p>コンテンツが見つかりません。</p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
