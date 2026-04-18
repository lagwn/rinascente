<?php
/**
 * Archive: Case Study (導入事例一覧)
 *
 * @package YUMEHO
 */
get_header();

$current_case_term = is_tax( array( 'facility_type', 'case_format' ) ) ? get_queried_object() : null;
$hero_title        = '導入事例';
$hero_subtitle     = '病院・介護施設・デイサービスでの<br>課題解決と運用成果をご紹介します';

if ( $current_case_term && ! is_wp_error( $current_case_term ) ) {
    $hero_title = $current_case_term->name . 'の導入事例';
    if ( function_exists( 'yumeho_term_archive_fallback_description' ) ) {
        $hero_subtitle = nl2br( esc_html( yumeho_term_archive_fallback_description( $current_case_term ) ) );
    }
}
?>

<style>
    .tabs {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 56px;
        flex-wrap: wrap;
    }
    .tab-btn {
        padding: 10px 28px;
        border: 1px solid rgba(0,0,0,0.12);
        background: #fff;
        cursor: pointer;
        border-radius: 0;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        color: var(--text-color);
        transition: background 0.2s, color 0.2s;
    }
    .tab-btn.active {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }
    .tab-btn:hover:not(.active) {
        background: rgba(0,104,183,0.05);
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .case-archive-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 24px;
        align-items: stretch;
    }
    .case-archive-card {
        min-width: 0;
        height: 100%;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .case-archive-card__link {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        color: inherit;
        text-decoration: none;
    }
    .case-archive-card__media {
        overflow: hidden;
        background: #f5f5f5;
    }
    .case-archive-card__image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: transform 0.35s ease;
    }
    .case-archive-card__body {
        padding: 18px 18px 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .case-archive-card__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 14px;
    }
    .case-archive-card__chip {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: rgba(0,0,0,0.72);
        background: rgba(0,0,0,0.04);
    }
    .case-archive-card__chip--accent {
        color: var(--primary-color);
        background: rgba(0,104,183,0.08);
    }
    .case-archive-card__highlight {
        background: rgba(0,104,183,0.05);
        border-radius: 8px;
        padding: 13px 14px;
        margin-bottom: 14px;
    }
    .case-archive-card__highlight-value {
        display: block;
        font-size: 1.38rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--primary-color);
    }
    .case-archive-card__highlight-label {
        display: block;
        margin-top: 5px;
        font-size: 0.74rem;
        font-weight: 700;
        color: rgba(0,0,0,0.62);
    }
    .case-archive-card__title {
        margin: 0 0 12px;
        font-size: 1.08rem;
        font-weight: 700;
        line-height: 1.65;
        color: var(--text-color);
    }
    .case-archive-card__summary {
        margin: 0;
        flex: 1;
        font-size: 0.9rem;
        line-height: 1.85;
        color: rgba(0,0,0,0.82);
    }
    .case-archive-card__facility {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px solid rgba(0,0,0,0.08);
    }
    .case-archive-card__facility-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .case-archive-card__icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        border-radius: 50%;
        background: var(--surface-alt);
    }
    .case-archive-card__facility-name {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--text-color);
    }
    .case-archive-card__facility-meta {
        margin-top: 3px;
        font-size: 0.75rem;
        color: rgba(0,0,0,0.62);
    }
    .case-archive-card__cta {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--primary-color);
    }
    .case-archive-card__cta-arrow {
        transition: transform 0.25s ease;
    }
    .case-archive-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 32px rgba(0,104,183,0.12);
    }
    .case-archive-card:hover .case-archive-card__image {
        transform: scale(1.03);
    }
    .case-archive-card:hover .case-archive-card__cta-arrow {
        transform: translateX(4px);
    }

    @media (max-width: 1080px) {
        .case-archive-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 640px) {
        .case-archive-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .case-archive-card__image {
            height: 200px;
        }
    }
</style>

<section class="hero bg-light">
    <div class="container text-center">
        <p class="hero-en">CASES</p>
        <h1 class="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
        <p class="hero-subtitle cases-hero-subtitle"><?php echo wp_kses( $hero_subtitle, array( 'br' => array() ) ); ?></p>
    </div>
</section>

<?php
$facility_terms = get_terms( array(
    'taxonomy'   => 'facility_type',
    'hide_empty' => true,
) );

$cases_query_args = array(
    'post_type'      => 'case_study',
    'posts_per_page' => -1,
    'meta_query'     => array(
        'relation' => 'OR',
        array( 'key' => '_yumeho_case_is_hidden', 'value' => '1', 'compare' => '!=' ),
        array( 'key' => '_yumeho_case_is_hidden', 'compare' => 'NOT EXISTS' ),
    ),
    'orderby' => 'date',
    'order'   => 'DESC',
);

if ( $current_case_term && ! is_wp_error( $current_case_term ) ) {
    $cases_query_args['tax_query'] = array(
        array(
            'taxonomy' => $current_case_term->taxonomy,
            'field'    => 'term_id',
            'terms'    => $current_case_term->term_id,
        ),
    );
}

$cases_query = new WP_Query( $cases_query_args );
?>

<section class="section">
    <div class="container">
        <?php if ( ! $current_case_term && ! empty( $facility_terms ) && ! is_wp_error( $facility_terms ) ) : ?>
        <div class="tabs">
            <button class="tab-btn active" data-tab="all">すべて</button>
            <?php foreach ( $facility_terms as $term ) : ?>
            <button class="tab-btn" data-tab="<?php echo esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $cases_query->have_posts() ) : ?>
        <?php if ( $current_case_term && ! is_wp_error( $current_case_term ) ) : ?>
        <div class="tab-content active">
            <div class="case-archive-grid">
                <?php while ( $cases_query->have_posts() ) : $cases_query->the_post(); ?>
                    <?php get_template_part( 'template-parts/case-card-summary' ); ?>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        </div>
        <?php else : ?>
        <div id="all" class="tab-content active">
            <div class="case-archive-grid">
                <?php while ( $cases_query->have_posts() ) : $cases_query->the_post(); ?>
                    <?php get_template_part( 'template-parts/case-card-summary' ); ?>
                <?php endwhile; ?>
            </div>
        </div>

        <?php
        wp_reset_postdata();
        foreach ( $facility_terms as $term ) :
            $term_query = new WP_Query( array(
                'post_type'      => 'case_study',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'facility_type',
                        'field'    => 'slug',
                        'terms'    => $term->slug,
                    ),
                ),
                'meta_query'     => array(
                    'relation' => 'OR',
                    array( 'key' => '_yumeho_case_is_hidden', 'value' => '1', 'compare' => '!=' ),
                    array( 'key' => '_yumeho_case_is_hidden', 'compare' => 'NOT EXISTS' ),
                ),
            ) );
        ?>
        <div id="<?php echo esc_attr( $term->slug ); ?>" class="tab-content">
            <?php if ( $term_query->have_posts() ) : ?>
                <div class="case-archive-grid">
                    <?php while ( $term_query->have_posts() ) : $term_query->the_post(); ?>
                        <?php get_template_part( 'template-parts/case-card-summary' ); ?>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p style="text-align:center;padding:48px 0;color:#666;">このカテゴリの事例は準備中です。</p>
            <?php endif; ?>
        </div>
        <?php
            wp_reset_postdata();
        endforeach;
        ?>
        <?php endif; ?>

        <?php else : ?>
        <p style="text-align:center;padding:48px 0;color:#666;">導入事例は準備中です。</p>
        <?php endif; ?>

        <?php
        yumeho_render_internal_pathways(
            'cases_archive',
            array(
                'title' => '導入事例とあわせて確認したいページ',
                'intro' => '回復期リハビリ病棟、介護施設、デイサービスでの導入事例を見比べながら、製品構成や費用感も整理できます。',
            )
        );
        ?>
    </div>
</section>

<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var tab = this.dataset.tab;
        document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
        this.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(function(c) { c.classList.remove('active'); });
        var target = document.getElementById(tab);
        if (target) target.classList.add('active');
    });
});
</script>

<?php get_footer(); ?>
