<?php
/**
 * Template Name: FAQ
 *
 * @package YUMEHO
 */
get_header();
?>

<style>
.faq-categories {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 48px;
    flex-wrap: wrap;
}
.faq-cat-btn {
    padding: 8px 22px;
    border: 1px solid rgba(0,0,0,0.1);
    background: #fff;
    cursor: pointer;
    border-radius: 0;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    color: var(--text-color);
    transition: all 0.2s;
}
.faq-cat-btn.active,
.faq-cat-btn:hover {
    background: var(--primary-color);
    color: #fff;
    border-color: var(--primary-color);
}
.faq-grid {
    max-width: 880px;
    margin: 0 auto;
}
.faq-item {
    background: #fff;
    border: none;
    border-radius: 10px;
    margin-bottom: 16px;
    overflow: hidden;
    box-shadow: 0 2px 14px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s;
}
.faq-item:hover {
    box-shadow: 0 4px 24px rgba(0,104,183,0.08);
}
.faq-question {
    background: none;
    padding: 24px 32px 24px 28px;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 14px;
    cursor: pointer;
    user-select: none;
    transition: color 0.15s;
    line-height: 1.55;
}
.faq-question:hover { color: var(--primary-color); }
.faq-question::before {
    content: "Q";
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    background: var(--primary-color);
    color: #fff;
    font-family: var(--font-logo);
    font-size: 0.82rem;
    font-weight: 700;
    line-height: 1;
}
.faq-question::after {
    content: "";
    flex-shrink: 0;
    margin-left: auto;
    width: 10px;
    height: 10px;
    border-right: 2px solid rgba(0,0,0,0.25);
    border-bottom: 2px solid rgba(0,0,0,0.25);
    transform: rotate(45deg);
    transition: transform 0.25s;
}
.faq-item.open .faq-question::after {
    transform: rotate(-135deg);
}
.faq-answer-wrap {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.35s ease;
}
.faq-item.open .faq-answer-wrap {
    max-height: 500px;
}
.faq-answer {
    padding: 0 32px 28px 72px;
    display: block;
    font-size: 0.88rem;
    line-height: 1.85;
    color: rgba(0,0,0,0.85);
}
.faq-answer::before { display: none; }
@media (max-width: 640px) {
    .faq-question { padding: 18px 18px 18px 16px; font-size: 0.88rem; }
    .faq-answer { padding: 0 18px 20px 52px; }
    .faq-question::before { width: 26px; height: 26px; font-size: 0.75rem; }
}
</style>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">FAQ</p>
            <h1 class="hero-title">よくある質問</h1>
            <p class="hero-subtitle">ご導入や運用に関して、よくいただくご質問にお答えします</p>
        </div>
    </section>

    <section class="section">
        <div class="container">

            <!-- Category filter tabs -->
            <div class="faq-categories">
                <button class="faq-cat-btn active" onclick="filterFaq('all',this)">すべて</button>
                <?php
                $faq_cats = get_terms( array(
                    'taxonomy'   => 'faq_category',
                    'hide_empty' => true,
                ) );
                if ( ! is_wp_error( $faq_cats ) && ! empty( $faq_cats ) ) :
                    $faq_cat_map = array();
                    foreach ( $faq_cats as $cat ) {
                        $faq_cat_map[ $cat->slug ] = $cat;
                    }
                    foreach ( array( 'install', 'cost', 'operation', 'demo' ) as $slug ) :
                        if ( empty( $faq_cat_map[ $slug ] ) ) {
                            continue;
                        }
                        $cat = $faq_cat_map[ $slug ];
                ?>
                <button class="faq-cat-btn" onclick="filterFaq('<?php echo esc_attr( $cat->slug ); ?>',this)"><?php echo esc_html( $cat->name ); ?></button>
                <?php
                    endforeach;
                endif;
                ?>
            </div>

            <div class="faq-grid">
                <?php
                $faq_query = new WP_Query( array(
                    'post_type'      => 'faq',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                ) );

                $first = true;
                if ( $faq_query->have_posts() ) :
                    while ( $faq_query->have_posts() ) : $faq_query->the_post();
                        $faq_terms = get_the_terms( get_the_ID(), 'faq_category' );
                        $cat_slug  = ( $faq_terms && ! is_wp_error( $faq_terms ) ) ? $faq_terms[0]->slug : '';
                ?>
                <div class="faq-item<?php echo $first ? ' open' : ''; ?>" data-cat="<?php echo esc_attr( $cat_slug ); ?>">
                    <div class="faq-question" onclick="toggleFaq(this)"><?php the_title(); ?></div>
                    <div class="faq-answer-wrap"><div class="faq-answer"><?php the_content(); ?></div></div>
                </div>
                <?php
                        $first = false;
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <p class="text-center">FAQはまだ登録されていません。</p>
                <?php endif; ?>
            </div>

            <?php
            yumeho_render_internal_pathways(
                'faq',
                array(
                    'title' => 'FAQとあわせて確認したいページ',
                    'intro' => '設置条件や費用感、補助制度まで続けて見ておくと、導入判断がしやすくなります。',
                )
            );
            ?>

            <div class="text-center" style="margin-top: 56px;">
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: rgba(0,0,0,0.82);">その他ご不明な点がございましたら、<br class="br-sp">お気軽にお問い合わせください。</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">資料請求・お問い合わせ</a>
            </div>

        </div>
    </section>

    <script>
    function toggleFaq(el) {
        el.closest('.faq-item').classList.toggle('open');
    }
    function filterFaq(cat, btn) {
        document.querySelectorAll('.faq-cat-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.faq-item').forEach(item => {
            if (cat === 'all' || item.dataset.cat === cat) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }
    </script>

<?php get_footer(); ?>
