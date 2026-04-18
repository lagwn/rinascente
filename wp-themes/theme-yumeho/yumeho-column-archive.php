<?php
/**
 * YUMEHO コラム一覧（コーポレートサイトから取得）
 */
get_header();

$paged   = max( 1, (int) get_query_var( 'yumeho_column_paged' ) );
$columns = yumeho_fetch_shared_columns( 12 );
?>

<style>
    .yumeho-column-archive {
        padding: clamp(48px, 7vw, 96px) 0;
    }
    .yumeho-column-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: clamp(20px, 2.5vw, 32px);
    }
    .yumeho-column-card {
        display: flex;
        flex-direction: column;
        background: #fff;
        border: 1px solid var(--line-color);
        border-radius: 12px;
        padding: 32px 28px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.3s, border-color 0.3s;
    }
    .yumeho-column-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.08);
        border-color: rgba(0,104,183,0.3);
    }
    .yumeho-column-card__category {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        color: var(--primary-color);
        margin-bottom: 14px;
        text-transform: uppercase;
    }
    .yumeho-column-card__title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.6;
        color: var(--text-color);
        margin: 0 0 16px;
        flex: 1;
    }
    .yumeho-column-card__excerpt {
        font-size: 0.85rem;
        line-height: 1.8;
        color: rgba(0,0,0,0.65);
        margin: 0 0 20px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .yumeho-column-card__meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: rgba(0,0,0,0.5);
        padding-top: 16px;
        border-top: 1px solid var(--line-color);
    }
    .yumeho-column-card__read {
        font-weight: 700;
        color: var(--primary-color);
    }
    .yumeho-column-empty {
        text-align: center;
        padding: 80px 0;
        color: rgba(0,0,0,0.5);
    }
    .yumeho-column-archive-pathways {
        margin-top: clamp(40px, 5vw, 64px);
    }
    .yumeho-column-archive-pathways .yumeho-pathways {
        position: static;
        left: auto;
        width: 100%;
        transform: none;
        margin-top: 0;
    }

    /* ページネーション */
    .yumeho-column-pagination {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: clamp(48px, 6vw, 80px);
        flex-wrap: wrap;
    }
    .yumeho-column-pagination a,
    .yumeho-column-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 44px;
        padding: 0 14px;
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-color);
        text-decoration: none;
        background: #fff;
        transition: all 0.25s;
    }
    .yumeho-column-pagination a:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    .yumeho-column-pagination .current {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
    }

    @media (max-width: 860px) {
        .yumeho-column-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .yumeho-column-grid {
            grid-template-columns: 1fr;
        }
        .yumeho-column-card {
            padding: 24px 20px;
        }
    }
</style>

<section class="hero bg-light">
    <div class="container text-center">
        <p class="hero-en">COLUMN</p>
        <h1 class="hero-title">コラム</h1>
        <p class="hero-subtitle cases-hero-subtitle">医療・福祉の現場に役立つ最新動向や<br class="br-sp">ノウハウをお届けします</p>
    </div>
</section>

<section class="yumeho-column-archive">
    <div class="container">
        <?php
        $per_page = 6;
        $total    = count( $columns );
        $offset   = ( $paged - 1 ) * $per_page;
        $page_columns = array_slice( $columns, $offset, $per_page );
        $total_pages  = max( 1, (int) ceil( $total / $per_page ) );
        ?>

        <?php if ( ! empty( $page_columns ) ) : ?>
        <div class="yumeho-column-grid">
            <?php foreach ( $page_columns as $col ) :
                $local_url = home_url( '/column/' . $col['slug'] . '/' );
            ?>
            <a href="<?php echo esc_url( $local_url ); ?>" class="yumeho-column-card">
                <?php if ( $col['category'] ) : ?>
                <div class="yumeho-column-card__category"><?php echo esc_html( $col['category'] ); ?></div>
                <?php endif; ?>
                <h2 class="yumeho-column-card__title"><?php echo esc_html( $col['title'] ); ?></h2>
                <?php if ( $col['excerpt'] ) : ?>
                <p class="yumeho-column-card__excerpt"><?php echo esc_html( $col['excerpt'] ); ?></p>
                <?php endif; ?>
                <div class="yumeho-column-card__meta">
                    <span><?php echo esc_html( wp_date( 'Y.m.d', strtotime( $col['date'] ) ) ); ?></span>
                    <span class="yumeho-column-card__read">続きを読む →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ( $total_pages > 1 ) : ?>
        <div class="yumeho-column-pagination">
            <?php if ( $paged > 1 ) : ?>
            <a href="<?php echo esc_url( home_url( '/column/' . ( $paged > 2 ? 'page/' . ( $paged - 1 ) . '/' : '' ) ) ); ?>">← 前へ</a>
            <?php endif; ?>
            <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
                <?php if ( $i === $paged ) : ?>
                <span class="current"><?php echo $i; ?></span>
                <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/column/' . ( $i > 1 ? 'page/' . $i . '/' : '' ) ) ); ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ( $paged < $total_pages ) : ?>
            <a href="<?php echo esc_url( home_url( '/column/page/' . ( $paged + 1 ) . '/' ) ); ?>">次へ →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else : ?>
        <div class="yumeho-column-empty">
            <p>コラムはまだ投稿されていません。</p>
        </div>
        <?php endif; ?>

        <div class="yumeho-column-archive-pathways">
            <?php
            yumeho_render_internal_pathways(
                'column_archive',
                array(
                    'title' => 'コラムとあわせて確認したいページ',
                    'intro' => '歩行訓練、転倒予防、導入検討の情報収集を進めるときに、FAQ・事例・補助制度も一緒に確認できます。',
                )
            );
            ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
