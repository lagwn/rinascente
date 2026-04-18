<?php
/**
 * Terms Page Template
 *
 * @package YUMEHO
 */

get_header();

$company_name = yumeho_theme_mod( 'company_name', '株式会社Rinascente' );
$company_tel  = yumeho_theme_mod( 'company_tel', '0859-00-1234' );
$company_addr = yumeho_theme_mod( 'company_address', '' );
$company_time = yumeho_theme_mod( 'company_hours', '平日 9:00〜17:00' );
$legal_updated_label = '2026年5月10日';
?>

<style>
    .yumeho-legal-shell {
        max-width: 960px;
        margin: 0 auto;
    }

    .yumeho-legal-lead {
        max-width: 760px;
        margin: 0 auto 36px;
        text-align: center;
    }

    .yumeho-legal-lead__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(0, 162, 255, 0.08);
        color: var(--primary-color);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        margin-bottom: 18px;
    }

    .yumeho-legal-lead p {
        font-size: 0.96rem;
        line-height: 1.95;
        color: rgba(0, 0, 0, 0.72);
        margin: 0;
        text-wrap: balance;
    }

    .yumeho-legal-lead__line {
        display: block;
    }

    .yumeho-legal-lead__line + .yumeho-legal-lead__line {
        margin-top: 0.1em;
    }

    .yumeho-legal-card {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 252, 255, 0.98)),
            linear-gradient(135deg, rgba(0, 162, 255, 0.08), rgba(0, 0, 0, 0));
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 28px;
        box-shadow: 0 20px 60px rgba(10, 26, 46, 0.08);
        padding: clamp(28px, 5vw, 52px);
    }

    .yumeho-legal-card::before {
        content: "";
        position: absolute;
        inset: 0 auto auto 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, #00a2ff 0%, #5ec9ff 55%, rgba(94, 201, 255, 0.1) 100%);
    }

    .yumeho-legal-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 34px;
        padding-bottom: 18px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    .yumeho-legal-meta__label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--primary-color);
    }

    .yumeho-legal-meta__label::before {
        content: "";
        width: 22px;
        height: 1px;
        background: currentColor;
    }

    .yumeho-legal-meta__date {
        font-size: 0.8rem;
        color: rgba(0, 0, 0, 0.45);
        letter-spacing: 0.04em;
        white-space: nowrap;
    }

    .yumeho-legal-body {
        max-width: 760px;
    }

    .yumeho-legal-body h2 {
        margin: 46px 0 18px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        font-size: clamp(1.08rem, 2vw, 1.34rem);
        font-weight: 700;
        line-height: 1.5;
        letter-spacing: 0.02em;
        color: #0f2135;
    }

    .yumeho-legal-body h2:first-of-type {
        margin-top: 0;
    }

    .yumeho-legal-body p,
    .yumeho-legal-body li {
        font-size: 0.95rem;
        line-height: 2;
        color: rgba(0, 0, 0, 0.78);
    }

    .yumeho-legal-body p {
        margin: 0 0 1.15em;
    }

    .yumeho-legal-body ul,
    .yumeho-legal-body ol {
        margin: 0 0 1.3em;
        padding-left: 1.35em;
    }

    .yumeho-legal-body li + li {
        margin-top: 0.35em;
    }

    .yumeho-legal-body a:not(.btn) {
        color: var(--primary-color);
        font-weight: 700;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .yumeho-legal-contact {
        margin-top: 44px;
        padding: 26px 24px;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(0, 162, 255, 0.08), rgba(0, 162, 255, 0.02));
        border: 1px solid rgba(0, 162, 255, 0.14);
    }

    .yumeho-legal-contact__kicker {
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--primary-color);
        margin-bottom: 10px;
    }

    .yumeho-legal-contact h2 {
        margin: 0 0 14px;
        padding: 0;
        border: 0;
        font-size: 1.2rem;
    }

    .yumeho-legal-contact p {
        margin-bottom: 0;
    }

    .yumeho-legal-contact a[href^="tel:"] {
        text-decoration: none;
    }

    .yumeho-legal-actions {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 22px;
        flex-wrap: wrap;
    }

    .yumeho-legal-actions .btn,
    .yumeho-legal-actions .btn:visited {
        color: #fff;
        text-decoration: none;
    }

    @media (max-width: 767px) {
        .yumeho-legal-meta {
            flex-direction: column;
            align-items: flex-start;
        }

        .yumeho-legal-meta__date {
            white-space: normal;
        }

        .yumeho-legal-body h2 {
            margin-top: 38px;
        }
    }
</style>

<section class="hero bg-light">
    <div class="container text-center">
        <p class="hero-en">TERMS</p>
        <h1 class="hero-title"><?php the_title(); ?></h1>
        <p class="hero-subtitle">YUMEHO サイトの閲覧、フォーム送信、見積依頼に関する利用条件をまとめています。</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="yumeho-legal-shell">
            <div class="yumeho-legal-lead animate-on-scroll">
                <div class="yumeho-legal-lead__eyebrow">Terms of Use</div>
                <p>
                    <span class="yumeho-legal-lead__line">製品情報の閲覧から資料請求、導入相談まで、安心してご利用いただくためのルールを整理しています。</span>
                    <span class="yumeho-legal-lead__line">正式な商談条件や契約条件は、個別のお見積書・契約書を優先します。</span>
                </p>
            </div>

            <article class="yumeho-legal-card animate-on-scroll">
                <div class="yumeho-legal-meta">
                    <div class="yumeho-legal-meta__label">YUMEHO Terms</div>
                    <div class="yumeho-legal-meta__date">最終更新日: <?php echo esc_html( $legal_updated_label ); ?></div>
                </div>

                <div class="yumeho-legal-body">
                    <?php
                    if ( have_posts() ) :
                        while ( have_posts() ) :
                            the_post();

                            $content = apply_filters( 'the_content', get_the_content() );
                            $content = yumeho_prepare_legal_page_content( $content );

                            echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        endwhile;
                    endif;
                    ?>

                    <div class="yumeho-legal-contact">
                        <div class="yumeho-legal-contact__kicker">Contact</div>
                        <h2>お問い合わせ窓口</h2>
                        <p>
                            <strong><?php echo esc_html( $company_name ); ?></strong><br>
                            電話: <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $company_tel ) ); ?>"><?php echo esc_html( $company_tel ); ?></a><br>
                            受付時間: <?php echo esc_html( $company_time ); ?>
                            <?php if ( $company_addr ) : ?>
                                <br>所在地: <?php echo esc_html( $company_addr ); ?>
                            <?php endif; ?>
                        </p>
                        <div class="yumeho-legal-actions">
                            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary">資料請求・お問い合わせ</a>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<?php get_footer(); ?>
