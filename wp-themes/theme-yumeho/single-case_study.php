<?php
/**
 * 導入事例 個別ページ
 *
 * @package YUMEHO
 */
get_header();
?>

<style>
    /* ── Case card: tabloid editorial style ── */
    .case-card {
        display: grid;
        grid-template-columns: 5fr 6fr;
        gap: 0;
        align-items: stretch;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        margin-bottom: 48px;
    }
    .case-img-wrap {
        position: relative;
        overflow: hidden;
        background: #f5f5f5;
    }
    .case-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .case-body {
        padding: 48px 44px;
    }
    .case-tag {
        display: inline-block;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: var(--primary-color);
        background: rgba(0,104,183,0.08);
        padding: 7px 16px;
        border-radius: 999px;
        margin-bottom: 20px;
    }
    .case-headline {
        font-size: clamp(1.6rem, 2.8vw, 2.1rem);
        font-weight: 700;
        line-height: 1.4;
        color: var(--text-color);
        margin: 0 0 20px;
        letter-spacing: -0.01em;
    }
    .case-headline em {
        color: var(--primary-color);
        font-style: normal;
        font-size: 1.15em;
    }
    .case-facility {
        font-size: 1rem;
        font-weight: 700;
        color: rgba(0,0,0,0.85);
        margin-bottom: 6px;
    }
    .case-meta {
        font-size: 0.85rem;
        color: rgba(0,0,0,0.55);
        margin-bottom: 28px;
        letter-spacing: 0.04em;
    }
    .case-section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--primary-color);
        letter-spacing: 0.06em;
        margin-top: 26px;
        margin-bottom: 10px;
    }
    .case-section-title::before {
        content: "";
        display: inline-block;
        width: 28px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    .case-body p {
        font-size: 0.95rem;
        line-height: 1.95;
        color: rgba(0,0,0,0.8);
        margin-bottom: 0;
    }
    .case-rich-text {
        font-size: 0.95rem;
        line-height: 1.95;
        color: rgba(0,0,0,0.8);
    }
    .case-rich-text > :first-child { margin-top: 0; }
    .case-rich-text > :last-child { margin-bottom: 0; }
    .case-rich-text p,
    .case-rich-text ul,
    .case-rich-text ol {
        margin: 0;
    }
    .case-rich-text p + p,
    .case-rich-text p + ul,
    .case-rich-text ul + p,
    .case-rich-text ol + p,
    .case-rich-text ul + ul,
    .case-rich-text ol + ol {
        margin-top: 0.8em;
    }
    .case-rich-text ul,
    .case-rich-text ol {
        padding-left: 1.2em;
    }
    .case-rich-text li + li {
        margin-top: 0.35em;
    }
    .case-metrics {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
        background: rgba(0,104,183,0.04);
        border-left: 3px solid var(--primary-color);
        margin: 8px 0 24px;
        border-radius: 0 4px 4px 0;
    }
    .case-metrics__item {
        padding: 18px 16px;
        text-align: center;
    }
    .case-metrics__item:not(:last-child) {
        border-right: 1px solid rgba(0,104,183,0.10);
    }
    .case-metrics__label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        color: rgba(0,0,0,0.55);
        margin-bottom: 6px;
    }
    .case-metrics__value {
        display: block;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--primary-color);
        line-height: 1.4;
    }
    .case-metrics__value > :first-child { margin-top: 0; }
    .case-metrics__value > :last-child { margin-bottom: 0; }
    .case-metrics__value p,
    .case-metrics__value ul,
    .case-metrics__value ol {
        margin: 0;
        font-size: inherit;
        line-height: inherit;
        color: inherit;
    }
    .case-metrics__value p + p,
    .case-metrics__value p + ul,
    .case-metrics__value ul + p {
        margin-top: 0.4em;
    }
    .case-metrics__value ul,
    .case-metrics__value ol {
        padding-left: 1.1em;
        text-align: left;
    }
    .case-pullquote {
        margin-top: 28px;
        padding: 20px 24px;
        background: rgba(255,193,7,0.08);
        border-left: 4px solid #ffc107;
        font-size: 1rem;
        font-style: italic;
        color: rgba(0,0,0,0.8);
        line-height: 1.7;
        border-radius: 0 6px 6px 0;
    }
    .case-pullquote__text > :first-child { margin-top: 0; }
    .case-pullquote__text > :last-child { margin-bottom: 0; }
    .case-pullquote__speaker {
        margin-top: 10px;
        font-size: 0.84rem;
        font-style: normal;
        color: rgba(0,0,0,0.55);
    }
    .case-ringi-process {
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid rgba(0,0,0,0.08);
    }
    .case-ringi-process .case-section-title {
        margin-top: 0;
    }
    .case-ringi-process p {
        font-size: 0.92rem;
        line-height: 1.95;
        color: rgba(0,0,0,0.82);
    }
    .case-ringi-process .case-rich-text {
        font-size: 0.92rem;
        line-height: 1.95;
        color: rgba(0,0,0,0.82);
    }

    .single-case-back {
        display: inline-flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        background: #fff;
        border: 1px solid rgba(0,104,183,0.16);
        border-radius: 14px;
        box-shadow: 0 10px 28px rgba(0,0,0,0.05);
        color: var(--text-color);
        text-decoration: none;
        margin-bottom: 28px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .single-case-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(0,0,0,0.08);
        border-color: rgba(0,104,183,0.26);
        text-decoration: none;
    }
    .single-case-back__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 999px;
        background: rgba(0,104,183,0.08);
        color: var(--primary-color);
        font-size: 1rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .single-case-back__body {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .single-case-back__eyebrow {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--primary-color);
    }
    .single-case-back__label {
        font-size: 0.96rem;
        font-weight: 700;
        line-height: 1.5;
        color: var(--text-color);
    }
    .single-case-back__desc {
        font-size: 0.8rem;
        line-height: 1.6;
        color: rgba(0,0,0,0.58);
    }

    @media (max-width: 860px) {
        .case-card { grid-template-columns: 1fr; }
        .case-img-wrap { max-height: 260px; }
        .case-body { padding: 32px 28px; }
        .case-metrics { grid-template-columns: 1fr; }
        .case-metrics__item:not(:last-child) {
            border-right: none;
            border-bottom: 1px solid rgba(0,104,183,0.10);
        }
        .single-case-back {
            width: 100%;
            padding: 14px 16px;
        }
    }

    @media (max-width: 767px) {
        .single-case-hero .container {
            padding-inline: 16px;
        }
        .single-case-hero .hero-en {
            text-align: center;
        }
        .single-case-hero .hero-title {
            width: min(100%, 13.5em);
            margin: 0;
            text-align: left !important;
            font-size: clamp(1rem, 4.6vw, 1.12rem);
            letter-spacing: 0.04em;
            line-height: 1.75;
        }
    }
</style>

<section class="hero bg-light single-case-hero">
    <div class="container text-center">
        <p class="hero-en">CASE STUDY</p>
        <h1 class="hero-title"><?php the_title(); ?></h1>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:1100px;">
        <a href="<?php echo esc_url( home_url('/cases/') ); ?>" class="single-case-back">
            <span class="single-case-back__icon" aria-hidden="true">←</span>
            <span class="single-case-back__body">
                <span class="single-case-back__eyebrow">Case Study Index</span>
                <span class="single-case-back__label">導入事例一覧に戻る</span>
                <span class="single-case-back__desc">他の導入事例も続けて比較できます。</span>
            </span>
        </a>

        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'template-parts/case-card' ); ?>

            <?php if ( get_the_content() ) : ?>
            <div style="background:#fff;padding:40px 36px;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.06);margin-bottom:48px;">
                <?php the_content(); ?>
            </div>
            <?php endif; ?>
        <?php endwhile; ?>

        <?php
        yumeho_render_internal_pathways(
            'case_study',
            array(
                'title' => 'この事例を見たあとに確認したいページ',
                'intro' => '他施設との比較、費用感、補助制度まで続けて見ておくと、自施設での検討材料を整理しやすくなります。',
            )
        );
        ?>

        <div style="text-align:center;margin-top:48px;">
            <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="btn btn-primary btn-lg">資料請求・お問い合わせ</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
