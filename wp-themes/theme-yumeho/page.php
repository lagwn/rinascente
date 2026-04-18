<?php
/**
 * Generic Page Template
 *
 * @package YUMEHO
 */
get_header();
?>

    <section class="hero bg-light">
        <div class="container text-center">
            <h1 class="hero-title"><?php the_title(); ?></h1>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            endif;
            ?>
        </div>
    </section>

<?php get_footer(); ?>
