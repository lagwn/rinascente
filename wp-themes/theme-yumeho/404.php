<?php
/**
 * 404 Page Template
 *
 * @package YUMEHO
 */
get_header();
?>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">404</p>
            <h1 class="hero-title">ページが見つかりません</h1>
            <p class="hero-subtitle">お探しのページは移動または削除された可能性があります。</p>
        </div>
    </section>

    <section class="section">
        <div class="container text-center">
            <p style="margin-bottom: 32px; font-size: 1rem; line-height: 1.8;">
                URLが正しいかご確認ください。<br>
                以下のリンクからお探しの情報にアクセスできる場合があります。
            </p>
            <div style="display: flex; flex-direction: column; gap: 16px; align-items: center;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary btn-lg">トップページへ戻る</a>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-secondary">お問い合わせ</a>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
