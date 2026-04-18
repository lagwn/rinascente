<?php
/**
 * Single Case Study Template
 *
 * @package Rinascente
 */

get_header();

while ( have_posts() ) :
    the_post();

    $product_terms = get_the_terms( get_the_ID(), 'product_type' );
    $product_name  = ( $product_terms && ! is_wp_error( $product_terms ) ) ? $product_terms[0]->name : '';
    $facility_name = get_post_meta( get_the_ID(), '_rinascente_facility_name', true );
    $model_name    = get_post_meta( get_the_ID(), '_rinascente_model_name', true );
    $staff_name    = get_post_meta( get_the_ID(), '_rinascente_staff_name', true );
    $staff_comment = get_post_meta( get_the_ID(), '_rinascente_staff_comment', true );
    $results       = array();

    for ( $i = 1; $i <= 3; $i++ ) {
        $key = get_post_meta( get_the_ID(), '_rinascente_result_' . $i . '_key', true );
        $val = get_post_meta( get_the_ID(), '_rinascente_result_' . $i . '_val', true );
        if ( $key || $val ) {
            $results[] = array(
                'key' => $key,
                'val' => $val,
            );
        }
    }
    ?>

  <main>
    <div class="article-hero">
      <div class="container" style="max-width:760px;">
        <div style="margin-bottom:20px;">
          <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" style="font-size:0.82rem;color:rgba(255,255,255,0.45);text-decoration:none;">&larr; Cases一覧に戻る</a>
        </div>
        <div class="article-meta">
          <?php if ( $product_name ) : ?>
          <span class="badge badge-light"><?php echo esc_html( $product_name ); ?></span>
          <?php endif; ?>
          <?php if ( $facility_name ) : ?>
          <span class="article-date"><?php echo esc_html( $facility_name ); ?></span>
          <?php endif; ?>
        </div>
        <h1 class="article-title"><?php the_title(); ?></h1>
        <?php if ( has_excerpt() ) : ?>
        <p style="margin-top:18px;font-size:0.95rem;line-height:1.8;color:rgba(255,255,255,0.72);max-width:52ch;"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <?php endif; ?>
      </div>
    </div>

    <section class="section bg-white" style="padding-top:0;">
      <div class="container">
        <div class="article-body">
          <?php if ( has_post_thumbnail() ) : ?>
          <div style="margin-bottom:32px;border-radius:16px;overflow:hidden;">
            <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%;height:auto;display:block;' ) ); ?>
          </div>
          <?php endif; ?>

          <?php if ( $model_name || $staff_name ) : ?>
          <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:28px;">
            <?php if ( $model_name ) : ?>
            <div style="background:var(--cream);padding:18px 20px;border-radius:10px;">
              <div style="font-size:0.72rem;letter-spacing:0.08em;color:var(--mid-gray);margin-bottom:8px;">導入モデル</div>
              <div style="font-size:0.96rem;font-weight:700;"><?php echo esc_html( $model_name ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $staff_name ) : ?>
            <div style="background:var(--cream);padding:18px 20px;border-radius:10px;">
              <div style="font-size:0.72rem;letter-spacing:0.08em;color:var(--mid-gray);margin-bottom:8px;">担当者</div>
              <div style="font-size:0.96rem;font-weight:700;"><?php echo esc_html( $staff_name ); ?></div>
            </div>
            <?php endif; ?>
          </div>
          <?php endif; ?>

          <?php the_content(); ?>

          <?php if ( ! empty( $results ) ) : ?>
          <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0;margin:32px 0;border:1px solid rgba(0,104,183,0.12);border-radius:10px;overflow:hidden;">
            <?php foreach ( $results as $result ) : ?>
            <div style="padding:18px 16px;text-align:center;background:rgba(0,104,183,0.03);">
              <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;color:#0068b7;margin-bottom:8px;"><?php echo esc_html( $result['key'] ); ?></div>
              <div style="font-size:0.9rem;line-height:1.7;"><?php echo wp_kses_post( nl2br( esc_html( $result['val'] ) ) ); ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <?php if ( $staff_comment ) : ?>
          <blockquote style="margin:28px 0 0;padding:20px 24px;background:rgba(0,104,183,0.04);border-left:3px solid #0068b7;border-radius:0 10px 10px 0;font-size:1rem;line-height:1.8;color:#0068b7;">
            <?php echo esc_html( $staff_comment ); ?>
          </blockquote>
          <?php endif; ?>

          <?php
          rinascente_render_internal_pathways(
              'case_study',
              array(
                  'title' => '導入事例とあわせて確認したいページ',
                  'intro' => '他の事例や企業情報、最新のお知らせまで続けて確認できるようにしています。',
              )
          );
          ?>

          <div class="article-nav">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'case_study' ) ); ?>" style="font-size:0.88rem;font-weight:700;color:var(--charcoal);display:inline-flex;align-items:center;gap:6px;">&larr; Cases一覧に戻る</a>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php
endwhile;

get_footer();
