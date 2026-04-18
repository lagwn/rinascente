<?php
/**
 * Generic Page Template
 *
 * @package Rinascente
 */

get_header();
?>

    <div class="page-hero">
      <div class="container">
        <div class="page-hero-label">Page</div>
        <h1 class="is-jp"><?php the_title(); ?></h1>
      </div>
    </div>

    <section class="section bg-cream">
      <div class="container" style="max-width:820px;">
        <?php
        while ( have_posts() ) : the_post();
            the_content();
        endwhile;
        ?>
      </div>
    </section>

<?php get_footer(); ?>
