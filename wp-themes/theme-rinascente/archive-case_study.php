<?php
/**
 * Case Study Archive Template
 *
 * @package Rinascente
 */

get_header();

$mica30_enabled      = function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled();
$shared_yumeho_cases = rinascente_fetch_shared_cases( 'yumeho', 6 );
$featured_case       = ! empty( $shared_yumeho_cases ) ? $shared_yumeho_cases[0] : null;
$featured_facility_name = $featured_case ? rinascente_shared_case_facility_name( $featured_case ) : '';
$featured_facility_type = $featured_case ? rinascente_shared_case_facility_type( $featured_case ) : '';
$featured_meta_parts    = $featured_case ? rinascente_shared_case_meta_parts( $featured_case ) : array();
$featured_summary       = $featured_case ? rinascente_shared_case_feature_summary( $featured_case ) : '';
$featured_metrics       = $featured_case ? rinascente_shared_case_metrics( $featured_case ) : array();
$featured_challenge     = $featured_case ? rinascente_case_plain_summary( $featured_case['challenge'] ?? '' ) : '';
$featured_reason        = $featured_case ? rinascente_case_plain_summary( $featured_case['reason'] ?? '' ) : '';
$featured_ringi         = $featured_case ? rinascente_case_plain_summary( $featured_case['ringi_process'] ?? '' ) : '';
$featured_quote         = $featured_case ? rinascente_case_plain_summary( $featured_case['pullquote'] ?? '' ) : '';
$featured_quote_label   = $featured_case ? trim( (string) ( $featured_case['pullquote_speaker'] ?? '' ) ) : '';
?>

  <main>

    <div class="page-hero">
      <div class="container">
        <div class="page-hero-label">Case Studies</div>
        <h1>Cases</h1>
        <p><?php echo esc_html( $mica30_enabled ? 'YUMEHO・MICA30を導入いただいた施設での実際の成果をご紹介します。' : 'YUMEHO を導入いただいた施設での実際の成果をご紹介します。' ); ?></p>
      </div>
    </div>

    <div style="background:var(--charcoal);border-bottom:1px solid var(--line-dark);">
      <div class="container">
        <div class="cases-stats-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(255,255,255,0.05);">
          <div style="padding:clamp(16px,2.5vw,28px);text-align:center;">
            <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:var(--gold);line-height:1;">20+</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:4px;">導入施設数</div>
          </div>
          <div style="padding:clamp(16px,2.5vw,28px);text-align:center;">
            <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:var(--gold);line-height:1;">1.5&times;</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:4px;">平均訓練機会向上</div>
          </div>
          <div style="padding:clamp(16px,2.5vw,28px);text-align:center;">
            <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:var(--gold);line-height:1;">40%</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:4px;">スタッフ負担軽減</div>
          </div>
          <div style="padding:clamp(16px,2.5vw,28px);text-align:center;">
            <div style="font-family:'Cormorant Garamond',serif;font-style:italic;font-size:clamp(1.8rem,3vw,2.8rem);font-weight:300;color:var(--gold);line-height:1;">31%</div>
            <div style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin-top:4px;">利用者参加率向上</div>
          </div>
        </div>
      </div>
    </div>

    <section class="section bg-cream">
      <div class="container">
        <span class="label" style="color:var(--gold-deep);margin-bottom:16px;display:block;">Featured Case</span>
        <div class="cases-featured-grid fade-up" style="display:grid;grid-template-columns:<?php echo ! empty( $featured_case['image_url'] ) ? '5fr 6fr' : '1fr'; ?>;gap:clamp(24px,4vw,48px);align-items:start;border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-xl);padding:clamp(28px,4vw,52px);background:white;">
          <?php if ( $featured_case ) : ?>
          <?php if ( ! empty( $featured_case['image_url'] ) ) : ?>
          <div style="overflow:hidden;border-radius:var(--r-lg);">
            <img src="<?php echo esc_url( $featured_case['image_url'] ); ?>" alt="<?php echo esc_attr( $featured_facility_name ?: $featured_case['title'] ); ?>" style="width:100%;height:auto;display:block;">
          </div>
          <?php endif; ?>
          <div class="cases-featured-copy">
            <div class="cases-featured-meta" style="display:flex;gap:10px;align-items:center;margin-bottom:20px;flex-wrap:wrap;">
              <span class="badge badge-blue"><?php echo esc_html( $featured_case['product_name'] ); ?></span>
              <?php if ( $featured_facility_type ) : ?>
              <span class="badge badge-light"><?php echo esc_html( $featured_facility_type ); ?></span>
              <?php endif; ?>
              <?php if ( ! empty( $featured_case['product_model'] ) ) : ?>
              <span style="font-size:0.78rem;color:var(--mid-gray);"><?php echo esc_html( $featured_case['product_model'] ); ?></span>
              <?php endif; ?>
            </div>
            <h2 style="font-size:clamp(1.3rem,2.5vw,2rem);font-weight:700;line-height:1.35;margin-bottom:16px;color:var(--charcoal);">
              <?php echo esc_html( $featured_case['title'] ); ?>
            </h2>
            <?php if ( $featured_facility_name ) : ?>
            <div style="font-size:1rem;font-weight:700;color:var(--charcoal);margin-bottom:6px;"><?php echo esc_html( $featured_facility_name ); ?></div>
            <?php endif; ?>
            <?php if ( ! empty( $featured_meta_parts ) ) : ?>
            <div style="font-size:0.8rem;color:var(--mid-gray);margin-bottom:20px;"><?php echo esc_html( implode( ' ・ ', $featured_meta_parts ) ); ?></div>
            <?php endif; ?>
            <?php if ( $featured_summary ) : ?>
            <p class="cases-featured-summary" style="font-size:0.92rem;color:rgba(0,0,0,0.75);line-height:1.85;margin-bottom:24px;">
              <?php echo esc_html( $featured_summary ); ?>
            </p>
            <?php endif; ?>
            <?php if ( ! empty( $featured_metrics ) ) : ?>
            <div class="cases-metrics" style="display:grid;grid-template-columns:repeat(<?php echo count( $featured_metrics ); ?>,minmax(0,1fr));gap:0;background:rgba(0,104,183,0.04);border-left:3px solid #0068b7;border-radius:0 6px 6px 0;margin-bottom:24px;">
              <?php foreach ( $featured_metrics as $metric ) : ?>
              <div class="cases-metric" style="padding:14px 12px;text-align:center;border-right:1px solid rgba(0,104,183,0.1);">
                <div class="cases-metric__label" style="font-size:0.7rem;font-weight:700;color:rgba(0,0,0,0.55);margin-bottom:4px;"><?php echo esc_html( $metric['label'] ); ?></div>
                <div class="cases-metric__value" style="font-size:0.95rem;font-weight:700;color:#0068b7;line-height:1.6;"><?php echo wp_kses_post( (string) $metric['value'] ); ?></div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="cases-spec-grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:24px;">
              <div class="cases-spec-card" style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:16px;">
                <div class="cases-spec-card__label" style="font-size:0.68rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--mid-gray);margin-bottom:6px;">施設情報</div>
                <div class="cases-spec-card__value" style="font-size:0.95rem;font-weight:700;color:var(--charcoal);line-height:1.6;"><?php echo esc_html( $featured_facility_name ?: 'YUMEHO導入施設' ); ?></div>
                <?php if ( $featured_facility_type ) : ?>
                <div class="cases-spec-card__meta" style="font-size:0.82rem;color:var(--mid-gray);margin-top:6px;"><?php echo esc_html( $featured_facility_type ); ?></div>
                <?php endif; ?>
              </div>
              <div class="cases-spec-card" style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:16px;">
                <div class="cases-spec-card__label" style="font-size:0.68rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--mid-gray);margin-bottom:6px;">対象製品</div>
                <div class="cases-spec-card__value" style="font-size:0.95rem;font-weight:700;color:var(--charcoal);line-height:1.6;"><?php echo esc_html( $featured_case['product_model'] ?: $featured_case['product_name'] ); ?></div>
                <?php if ( ! empty( $featured_meta_parts ) ) : ?>
                <div class="cases-spec-card__meta" style="font-size:0.82rem;color:var(--mid-gray);margin-top:6px;"><?php echo esc_html( implode( ' ・ ', $featured_meta_parts ) ); ?></div>
                <?php endif; ?>
              </div>
              <?php if ( $featured_challenge ) : ?>
              <div class="cases-spec-card cases-spec-card--body" style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:16px;">
                <div class="cases-spec-card__label" style="font-size:0.68rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--mid-gray);margin-bottom:6px;">導入前の課題</div>
                <div class="cases-spec-card__body" style="font-size:0.85rem;line-height:1.75;color:rgba(0,0,0,0.78);"><?php echo esc_html( $featured_challenge ); ?></div>
              </div>
              <?php endif; ?>
              <?php $decision_text = $featured_reason ?: $featured_ringi; ?>
              <?php if ( $decision_text ) : ?>
              <div class="cases-spec-card cases-spec-card--body" style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:16px;">
                <div class="cases-spec-card__label" style="font-size:0.68rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--mid-gray);margin-bottom:6px;"><?php echo esc_html( $featured_reason ? '導入の決め手' : '導入決定までの経緯' ); ?></div>
                <div class="cases-spec-card__body" style="font-size:0.85rem;line-height:1.75;color:rgba(0,0,0,0.78);"><?php echo esc_html( $decision_text ); ?></div>
              </div>
              <?php endif; ?>
            </div>
            <?php if ( $featured_quote ) : ?>
            <div style="padding:14px 18px;background:rgba(200,169,110,0.08);border-left:3px solid var(--gold);font-size:0.88rem;font-style:italic;color:rgba(0,0,0,0.75);border-radius:0 4px 4px 0;margin-bottom:24px;line-height:1.7;">
              「<?php echo esc_html( $featured_quote ); ?>」
              <?php if ( $featured_quote_label ) : ?>
              <span style="font-style:normal;color:var(--mid-gray);">─ <?php echo esc_html( $featured_quote_label ); ?></span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            <a href="<?php echo esc_url( $featured_case['link'] ); ?>" class="btn btn-dark">YUMEHOの事例詳細を見る</a>
          </div>
          <?php else : ?>
          <div>
            <h2 style="font-size:clamp(1.3rem,2.5vw,2rem);font-weight:700;line-height:1.35;margin-bottom:16px;color:var(--charcoal);">YUMEHO側の導入事例を準備中です。</h2>
            <p style="font-size:0.95rem;color:var(--mid-gray);line-height:1.75;margin-bottom:24px;">導入事例は YUMEHO サイトで入力し、Rinascente の公開ページに共通表示します。</p>
            <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/cases/' ) ); ?>" class="btn btn-dark">YUMEHOの全事例を見る</a>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="section bg-light">
      <div class="container">
        <div class="section-header fade-up" style="display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;">
          <div>
            <span class="label" style="color:var(--gold-deep);">Case Studies</span>
            <h2 style="margin-top:8px;">導入事例一覧</h2>
          </div>
          <div style="display:flex;gap:8px;">
            <button class="filter-btn active" data-filter="all" style="padding:6px 14px;border-radius:999px;border:1px solid rgba(0,0,0,0.15);font-size:0.78rem;font-weight:700;background:var(--black);color:white;cursor:pointer;">すべて</button>
            <button class="filter-btn" data-filter="yumeho" style="padding:6px 14px;border-radius:999px;border:1px solid rgba(0,0,0,0.15);font-size:0.78rem;font-weight:700;background:white;color:var(--mid-gray);cursor:pointer;">YUMEHO</button>
            <?php if ( $mica30_enabled ) : ?>
            <button class="filter-btn" data-filter="mica30" style="padding:6px 14px;border-radius:999px;border:1px solid rgba(0,0,0,0.15);font-size:0.78rem;font-weight:700;background:white;color:var(--mid-gray);cursor:pointer;">MICA30</button>
            <?php endif; ?>
          </div>
        </div>

        <div class="cases-all-grid fade-up d-100" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;" data-stagger>
          <?php foreach ( $shared_yumeho_cases as $shared_case ) : ?>
          <?php
          $card_facility_name = rinascente_shared_case_facility_name( $shared_case );
          $card_facility_type = rinascente_shared_case_facility_type( $shared_case );
          $card_meta_parts    = rinascente_shared_case_meta_parts( $shared_case );
          $card_summary       = rinascente_shared_case_feature_summary( $shared_case );
          ?>
          <a href="<?php echo esc_url( $shared_case['link'] ); ?>" class="case-card" data-product="yumeho">
            <div class="case-card-img" style="background:#0d1b22;position:relative;overflow:hidden;">
              <img src="<?php echo esc_url( $shared_case['image_url'] ?: get_template_directory_uri() . '/assets/img/case_hospital.webp' ); ?>" alt="" aria-hidden="true" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center;opacity:0.85;" decoding="async">
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:#0068b7;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;"><?php echo esc_html( $shared_case['product_name'] ); ?></div>
              <div style="font-size:0.82rem;font-weight:700;color:var(--charcoal);margin-bottom:4px;"><?php echo esc_html( $card_facility_name ?: 'YUMEHO導入施設' ); ?></div>
              <?php if ( ! empty( $card_meta_parts ) ) : ?>
              <div style="font-size:0.72rem;color:var(--mid-gray);margin-bottom:8px;letter-spacing:0.04em;"><?php echo esc_html( implode( ' ・ ', $card_meta_parts ) ); ?></div>
              <?php endif; ?>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;"><?php echo esc_html( $shared_case['title'] ); ?></h3>
              <p style="font-size:0.82rem;color:var(--mid-gray);margin-bottom:14px;line-height:1.6;"><?php echo esc_html( $card_summary ?: $shared_case['excerpt'] ); ?></p>
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="badge badge-light">YUMEHO</span>
                <?php if ( $card_facility_type ) : ?>
                <span class="badge badge-light"><?php echo esc_html( $card_facility_type ); ?></span>
                <?php endif; ?>
                <?php if ( ! empty( $shared_case['product_model'] ) ) : ?>
                <span class="badge badge-light"><?php echo esc_html( $shared_case['product_model'] ); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
          <?php endforeach; ?>

          <?php if ( $mica30_enabled ) : ?>
          <a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>" class="case-card" data-product="mica30">
            <div class="case-card-img" style="background:#0a181e;position:relative;overflow:hidden;">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/mica30.webp' ); ?>" alt="" aria-hidden="true" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center 30%;opacity:0.85;">
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:#005f73;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;">MICA30</div>
              <div style="font-size:0.78rem;color:var(--mid-gray);margin-bottom:8px;">循環器専門病院</div>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;">バリアブルモードで複雑なインターベンションに対応</h3>
              <p style="font-size:0.82rem;color:var(--mid-gray);margin-bottom:14px;line-height:1.6;">速度リアルタイム変更で複雑な血管造影のニーズを満たす。</p>
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="badge badge-light">MICA30</span>
                <span class="badge badge-light">循環器科</span>
              </div>
            </div>
          </a>

          <a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>" class="case-card" data-product="mica30">
            <div class="case-card-img" style="background:#081520;position:relative;overflow:hidden;">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/mica30.webp' ); ?>" alt="" aria-hidden="true" decoding="async" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center 60%;opacity:0.85;">
            </div>
            <div class="case-card-body">
              <div class="case-product-tag" style="color:#005f73;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;">MICA30</div>
              <div style="font-size:0.78rem;color:var(--mid-gray);margin-bottom:8px;">総合病院 放射線科</div>
              <h3 style="font-size:0.95rem;font-weight:700;margin-bottom:8px;line-height:1.45;">2系統気泡センサーによる安全性向上と業務効率化</h3>
              <p style="font-size:0.82rem;color:var(--mid-gray);margin-bottom:14px;line-height:1.6;">準備時間を短縮しつつ、安全確認の信頼性が大幅に向上。</p>
              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <span class="badge badge-light">MICA30</span>
                <span class="badge badge-light">総合病院</span>
              </div>
            </div>
          </a>
          <?php endif; ?>

          <?php if ( empty( $shared_yumeho_cases ) ) : ?>
          <p style="grid-column:1/-1;color:var(--mid-gray);font-size:0.9rem;">YUMEHO 側の導入事例を取得できなかったため、表示を準備中です。</p>
          <?php endif; ?>
        </div>

        <?php
        rinascente_render_internal_pathways(
            'case_study_archive',
            array(
                'title' => '導入事例とあわせて確認したいページ',
                'intro' => 'YUMEHO や関連製品の導入事例を比較しながら、会社情報、Press、関連コラムまで続けて確認できます。',
            )
        );
        ?>
      </div>
    </section>

    <section class="section bg-dark">
      <div class="container cases-cta" style="text-align:center;max-width:640px;">
        <div class="fade-up">
          <span class="label" style="color:var(--gold);">Your Case</span>
          <span class="gold-line gold-line-center" style="margin:14px auto 24px;"></span>
          <h2 style="font-family:var(--font-body);font-size:clamp(1.4rem,2.6vw,2.2rem);font-style:normal;font-weight:700;color:var(--white);margin-bottom:16px;">
            <span style="white-space:nowrap;">導入事例の掲載に</span><span style="white-space:nowrap;">ご協力いただける</span><br><span style="white-space:nowrap;">施設を募集しています。</span>
          </h2>
          <p style="color:rgba(255,255,255,0.6);max-width:52ch;margin-inline:auto;margin-bottom:28px;">
            導入事例のご提供や掲載にご協力いただける施設様を募集しています。<br>費用は一切かかりません。
          </p>
          <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-gold btn-lg">お問い合わせ</a>
            <a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/contact/' ) ); ?>" class="btn btn-outline-light btn-lg">YUMEHOの資料請求</a>
            <?php if ( $mica30_enabled ) : ?>
            <a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/contact/' ) ); ?>" class="btn btn-outline-light btn-lg">MICA30の資料請求</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

  </main>

  <script>
  (function(){
    document.querySelectorAll('.filter-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        document.querySelectorAll('.filter-btn').forEach(function(button){
          button.classList.remove('active');
          button.style.background = 'white';
          button.style.color = 'var(--mid-gray)';
          button.style.borderColor = 'rgba(0,0,0,0.15)';
        });
        btn.classList.add('active');
        btn.style.background = 'var(--black)';
        btn.style.color = 'white';
        btn.style.borderColor = 'var(--black)';

        var filter = btn.dataset.filter;
        document.querySelectorAll('.cases-all-grid .case-card').forEach(function(card){
          card.style.display = (filter === 'all' || card.dataset.product === filter) ? '' : 'none';
        });
      });
    });
  })();
  </script>

<?php get_footer(); ?>
