<?php
/**
 * YUMEHO コラム詳細（コーポレートサイトから取得 + canonical 設定）
 */
$slug   = sanitize_title( get_query_var( 'yumeho_column_slug' ) );
$column = yumeho_fetch_shared_column_by_slug( $slug );

// 取得失敗 or 公開フラグなし → 404
if ( ! $column ) {
    status_header( 404 );
    nocache_headers();
    include get_query_template( '404' );
    exit;
}

// canonical タグをコーポレートサイト側のURLに設定
add_action( 'wp_head', function() use ( $column ) {
    echo '<link rel="canonical" href="' . esc_url( $column['link'] ) . '">' . "\n";
}, 1 );

// title タグをコラムタイトルに変更
add_filter( 'pre_get_document_title', function() use ( $column ) {
    return $column['title'] . ' | コラム | ' . get_bloginfo( 'name' );
} );

get_header();

// 関連記事を取得
$related_columns = array_filter(
    yumeho_fetch_shared_columns( 6 ),
    function( $c ) use ( $column ) {
        return $c['slug'] !== $column['slug'];
    }
);
$related_columns = array_slice( $related_columns, 0, 3 );
?>

<style>
    .yumeho-column-single-hero {
        background: linear-gradient(135deg, #001f3f 0%, #003d7a 100%);
        color: #fff;
        padding: clamp(100px, 12vw, 160px) 0 clamp(56px, 7vw, 88px);
        position: relative;
    }
    .yumeho-column-single-hero .container { position: relative; z-index: 1; }
    .yumeho-column-breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: rgba(255,255,255,0.55);
        margin-bottom: 28px;
        flex-wrap: wrap;
    }
    .yumeho-column-breadcrumb a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }
    .yumeho-column-breadcrumb a:hover { color: #48cae4; }
    .yumeho-column-breadcrumb__sep { opacity: 0.4; }
    .yumeho-column-category-badge {
        display: inline-block;
        padding: 6px 16px;
        background: rgba(72,202,228,0.15);
        border: 1px solid rgba(72,202,228,0.4);
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        color: #48cae4;
        text-transform: uppercase;
        margin-bottom: 24px;
    }
    .yumeho-column-title {
        font-size: clamp(1.6rem, 3.2vw, 2.6rem);
        font-weight: 700;
        line-height: 1.5;
        color: #fff;
        margin: 0 0 24px;
        max-width: 820px;
        word-break: keep-all;
        overflow-wrap: anywhere;
    }
    .yumeho-column-meta {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        font-size: 0.82rem;
        color: rgba(255,255,255,0.6);
    }
    .yumeho-column-meta strong {
        color: rgba(255,255,255,0.9);
        font-weight: 600;
        margin-right: 6px;
    }

    /* YUMEHO 独自リード文 */
    .yumeho-column-lead {
        max-width: 760px;
        margin: 0 auto clamp(36px, 4vw, 56px);
        padding: 28px 32px;
        background: linear-gradient(135deg, rgba(0,104,183,0.06), rgba(72,202,228,0.04));
        border-left: 4px solid var(--primary-color);
        border-radius: 0 8px 8px 0;
        font-size: 1rem;
        line-height: 2;
        color: var(--text-color);
    }
    .yumeho-column-lead-label {
        display: inline-block;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: var(--primary-color);
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .yumeho-column-body {
        padding: clamp(48px, 6vw, 88px) 0;
    }
    .yumeho-column-content {
        max-width: 760px;
        margin: 0 auto;
        font-size: 1rem;
        line-height: 2;
        color: rgba(0,0,0,0.85);
    }
    .yumeho-column-content h2 {
        font-size: clamp(1.25rem, 1.8vw, 1.5rem);
        font-weight: 700;
        color: var(--text-color);
        margin: clamp(40px, 5vw, 64px) 0 clamp(16px, 2vw, 24px);
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary-color);
    }
    .yumeho-column-content h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-color);
        margin: clamp(32px, 4vw, 44px) 0 14px;
        padding-left: 16px;
        border-left: 4px solid var(--primary-color);
    }
    .yumeho-column-content p { margin-bottom: 1.6em; }
    .yumeho-column-content ul,
    .yumeho-column-content ol {
        padding-left: 1.6em;
        margin-bottom: 1.6em;
    }
    .yumeho-column-content li { margin-bottom: 0.6em; }
    .yumeho-column-content strong { font-weight: 700; color: var(--text-color); }
    .yumeho-column-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.8em 0;
        font-size: clamp(0.96rem, 1.05vw, 1.02rem);
        line-height: 1.75;
    }
    .yumeho-column-content th,
    .yumeho-column-content td {
        padding: 20px 28px;
        border-bottom: 1px solid #d8d0c4;
        text-align: left;
        vertical-align: middle;
    }
    .yumeho-column-content th {
        width: 38%;
        background: #f4efe6;
        font-weight: 700;
        color: #5e5a52;
    }
    .yumeho-column-content td {
        color: rgba(0,0,0,0.88);
    }

    /* 関連記事 */
    .yumeho-column-related {
        background: var(--surface-alt, #f8fafe);
        padding: clamp(56px, 7vw, 96px) 0;
    }
    .yumeho-column-related__title {
        text-align: center;
        font-size: clamp(1.25rem, 2vw, 1.6rem);
        font-weight: 700;
        margin: 0 0 clamp(32px, 4vw, 48px);
    }
    .yumeho-column-related__title::before {
        content: "";
        display: block;
        width: 40px;
        height: 2px;
        background: var(--primary-color);
        margin: 0 auto 14px;
    }
    .yumeho-column-related-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    .yumeho-column-related-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line-color);
        border-radius: 12px;
        padding: 24px 22px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .yumeho-column-related-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(0,0,0,0.06);
    }
    .yumeho-column-related-card__category {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        color: var(--primary-color);
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .yumeho-column-related-card__title {
        font-size: 0.9rem;
        font-weight: 700;
        line-height: 1.55;
        color: var(--text-color);
        margin: 0 0 12px;
        flex: 1;
    }
    .yumeho-column-related-card__date {
        font-size: 0.72rem;
        color: rgba(0,0,0,0.5);
        padding-top: 12px;
        border-top: 1px solid var(--line-color);
    }
    .yumeho-column-pathways {
        background: var(--surface-alt, #f8fafe);
        padding: 0 0 clamp(56px, 7vw, 96px);
    }
    .yumeho-column-pathways .yumeho-pathways {
        position: static;
        left: auto;
        width: 100%;
        transform: none;
        margin-top: 0;
    }
    .yumeho-column-back {
        text-align: center;
        margin-top: clamp(36px, 4vw, 56px);
    }

    @media (max-width: 860px) {
        .yumeho-column-related-grid { grid-template-columns: 1fr; }
        .yumeho-column-content th,
        .yumeho-column-content td { padding: 14px 16px; }
    }
</style>

<section class="yumeho-column-single-hero">
    <div class="container">
        <div class="yumeho-column-breadcrumb">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
            <span class="yumeho-column-breadcrumb__sep">/</span>
            <a href="<?php echo esc_url( home_url( '/column/' ) ); ?>">Column</a>
        </div>

        <?php if ( $column['category'] ) : ?>
        <div class="yumeho-column-category-badge"><?php echo esc_html( $column['category'] ); ?></div>
        <?php endif; ?>

        <h1 class="yumeho-column-title"><?php echo esc_html( $column['title'] ); ?></h1>

        <div class="yumeho-column-meta">
            <span><strong>投稿日</strong><?php echo esc_html( wp_date( 'Y年n月j日', strtotime( $column['date'] ) ) ); ?></span>
        </div>
    </div>
</section>

<section class="yumeho-column-body">
    <div class="container">
        <?php if ( $column['yumeho_lead'] ) : ?>
        <div class="yumeho-column-lead">
            <span class="yumeho-column-lead-label">For YUMEHO Users</span>
            <p style="margin:0;"><?php echo nl2br( esc_html( $column['yumeho_lead'] ) ); ?></p>
        </div>
        <?php endif; ?>

        <article class="yumeho-column-content">
            <?php echo wp_kses_post( $column['content'] ); ?>
        </article>
    </div>
</section>

<?php if ( ! empty( $related_columns ) ) : ?>
<section class="yumeho-column-related">
    <div class="container">
        <h2 class="yumeho-column-related__title">関連するコラム</h2>
        <div class="yumeho-column-related-grid">
            <?php foreach ( $related_columns as $r ) : ?>
            <a href="<?php echo esc_url( home_url( '/column/' . $r['slug'] . '/' ) ); ?>" class="yumeho-column-related-card">
                <?php if ( $r['category'] ) : ?>
                <div class="yumeho-column-related-card__category"><?php echo esc_html( $r['category'] ); ?></div>
                <?php endif; ?>
                <h3 class="yumeho-column-related-card__title"><?php echo esc_html( $r['title'] ); ?></h3>
                <div class="yumeho-column-related-card__date"><?php echo esc_html( wp_date( 'Y.m.d', strtotime( $r['date'] ) ) ); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="yumeho-column-back">
            <a href="<?php echo esc_url( home_url( '/column/' ) ); ?>" class="btn btn-secondary">コラム一覧に戻る</a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="yumeho-column-pathways">
    <div class="container">
        <?php
        yumeho_render_internal_pathways(
            'column',
            array(
                'title' => 'コラムとあわせて確認したいページ',
                'intro' => '内容を実際の導入検討へつなげやすいように、FAQ・事例・概算確認の導線をまとめています。',
            )
        );
        ?>
    </div>
</section>

<?php get_footer(); ?>
