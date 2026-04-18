<?php
/**
 * 404 Error Page Template
 *
 * @package Rinascente
 */

get_header();
?>

  <main>

    <div class="page-hero">
      <div class="container">
        <h1>404</h1>
        <p>お探しのページが見つかりませんでした。</p>
      </div>
    </div>

    <section class="section bg-cream">
      <div class="container" style="text-align:center;padding:clamp(48px,8vw,96px) 0;">
        <p style="font-size:1rem;color:var(--mid-gray);margin-bottom:32px;line-height:1.8;">
          ページが移動または削除された可能性があります。<br>
          URLをご確認のうえ、再度アクセスしてください。
        </p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-dark" style="min-height:48px;">トップページへ戻る</a>
      </div>
    </section>

  </main>

<?php get_footer(); ?>
