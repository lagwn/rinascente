<?php
/**
 * Template Name: プライバシーポリシー
 *
 * @package Rinascente
 */

get_header();

$legal_updated_label = '2026年5月10日';
?>

  <!-- Page Hero -->
  <div class="page-hero">
    <div class="container">
      <div class="page-hero-label">Privacy Policy</div>
      <h1>Privacy Policy</h1>
      <p>個人情報の取扱いに関する基本方針を定めています。</p>
    </div>
  </div>

  <style>
    .legal-doc {
      max-width: 760px;
      margin: 0 auto;
      padding: clamp(40px, 6vw, 72px) 0;
    }
    .legal-doc__updated {
      font-size: 0.78rem;
      color: rgba(0,0,0,0.45);
      letter-spacing: 0.08em;
      margin-bottom: clamp(32px, 4vw, 48px);
      text-align: right;
    }
    .legal-doc h2 {
      font-size: clamp(1.05rem, 1.6vw, 1.25rem);
      font-weight: 700;
      color: var(--charcoal, #1a1a1a);
      letter-spacing: 0.02em;
      padding-bottom: 12px;
      margin: clamp(36px, 5vw, 56px) 0 clamp(16px, 2vw, 22px);
      border-bottom: 1px solid rgba(0,0,0,0.1);
      position: relative;
    }
    .legal-doc h2::before {
      content: "";
      display: inline-block;
      width: 24px;
      height: 1px;
      background: var(--gold, #c8a96e);
      vertical-align: middle;
      margin-right: 14px;
    }
    .legal-doc h2:first-of-type {
      margin-top: 0;
    }
    .legal-doc p {
      font-size: 0.92rem;
      line-height: 2;
      color: rgba(0,0,0,0.78);
      margin-bottom: 1.2em;
    }
    .legal-doc ol,
    .legal-doc ul {
      font-size: 0.92rem;
      line-height: 2;
      color: rgba(0,0,0,0.78);
      padding-left: 1.4em;
      margin-bottom: 1.2em;
    }
    .legal-doc li {
      margin-bottom: 0.4em;
    }
    .legal-doc strong {
      font-weight: 700;
      color: var(--charcoal, #1a1a1a);
    }
    .legal-doc a {
      color: var(--gold-deep, #a08550);
      text-decoration: underline;
      text-underline-offset: 3px;
    }
    .legal-doc a:hover {
      color: var(--charcoal, #1a1a1a);
    }
  </style>

  <section class="section bg-cream">
    <div class="container">
      <div class="legal-doc">
        <p class="legal-doc__updated">最終更新日: <?php echo esc_html( $legal_updated_label ); ?></p>
        <?php
        if ( have_posts() ) {
            while ( have_posts() ) : the_post();
                $content = apply_filters( 'the_content', get_the_content() );
                // {{company_name}} などのプレースホルダーをカスタマイザー値に置換
                $content = strtr( $content, array(
                    '{{company_name}}'    => esc_html( get_theme_mod( 'company_name', '株式会社リナシェンテ' ) ),
                    '{{company_tel}}'     => esc_html( get_theme_mod( 'company_tel', '' ) ),
                    '{{company_address}}' => esc_html( get_theme_mod( 'company_address', '' ) ),
                    '{{company_hours}}'   => esc_html( get_theme_mod( 'company_hours', '' ) ),
                ) );
                echo $content;
            endwhile;
        }
        ?>

        <h2>お問い合わせ窓口</h2>
        <p>
          <strong><?php echo esc_html( get_theme_mod( 'company_name', '株式会社リナシェンテ' ) ); ?></strong><br>
          電話: <?php echo esc_html( get_theme_mod( 'company_tel', '0859-00-1234' ) ); ?><br>
          受付時間: <?php echo esc_html( get_theme_mod( 'company_hours', '平日 9:00〜17:00' ) ); ?>
          <?php $address = get_theme_mod( 'company_address', '' ); ?>
          <?php if ( $address ) : ?>
          <br>所在地: <?php echo esc_html( $address ); ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
  </section>

<?php get_footer(); ?>
