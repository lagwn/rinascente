<?php
/**
 * Template Name: お問い合わせ
 *
 * Contact page template for Rinascente corporate site.
 *
 * @package Rinascente
 */

get_header();
?>

  <main>

    <div class="page-hero">
      <div class="container">
        <div class="page-hero-label">Contact</div>
        <h1>Contact</h1>
        <p>製品のご相談から事業提携まで、あらゆるお問い合わせをお受けしています。</p>
      </div>
    </div>

    <section class="section bg-cream">
      <div class="container">
        <div class="contact-grid" style="display:grid;grid-template-columns:1fr 1.5fr;gap:clamp(32px,6vw,72px);align-items:start;">

          <!-- Left: Info -->
          <div class="fade-up" style="position:sticky;top:100px;">
            <span class="label" style="color:var(--gold-deep);margin-bottom:16px;display:block;">Get in Touch</span>
            <h2 style="
              font-family:var(--font-body);
              font-size:clamp(1.8rem,3vw,3rem);
              font-style:normal;
              font-weight:700;
              color:var(--charcoal);
              line-height:1.15;
              margin-bottom:24px;
            ">お気軽に<br>ご相談ください。</h2>

            <div style="display:grid;gap:16px;margin-bottom:32px;">
              <div style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:20px;background:white;">
                <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:var(--gold-deep);margin-bottom:8px;">Phone</div>
                <div style="font-size:1.1rem;font-weight:700;"><?php echo esc_html( get_theme_mod( 'company_tel', '0859-00-1234' ) ); ?></div>
                <div style="font-size:0.78rem;color:var(--mid-gray);margin-top:4px;"><?php echo esc_html( get_theme_mod( 'company_hours', '平日 9:00〜17:00' ) ); ?></div>
              </div>
              <div style="border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-md);padding:20px;background:white;">
                <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:var(--gold-deep);margin-bottom:8px;">Address</div>
                <div style="font-size:0.9rem;font-weight:500;"><?php echo esc_html( get_theme_mod( 'company_address', '○○○○' ) ); ?></div>
              </div>
            </div>

            <div style="border-top:1px solid rgba(0,0,0,0.08);padding-top:24px;">
              <p style="font-size:0.82rem;font-weight:700;color:var(--charcoal);margin-bottom:12px;">製品別のお問い合わせ</p>
              <div style="display:grid;gap:8px;">
                <a href="<?php echo esc_url( add_query_arg( 'tmptype', '導入・見積相談', rinascente_related_site_url( 'yumeho', '/contact/' ) ) ); ?>" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:var(--r-md);border:1px solid rgba(0,104,183,0.2);background:rgba(0,104,183,0.04);color:#0068b7;font-size:0.85rem;font-weight:700;transition:background 0.2s;">
                  YUMEHO のお問い合わせ <span>&rarr;</span>
                </a>
              </div>
            </div>
          </div>

          <!-- Right: Form -->
          <div class="fade-up d-200">
            <div style="background:white;border:1px solid rgba(0,0,0,0.08);border-radius:var(--r-xl);padding:clamp(24px,4vw,44px);">
              <?php
              if ( function_exists( 'rinascente_contact_form' ) ) :
                  $form = rinascente_contact_form();
                  $form->process();
                  include get_template_directory() . '/template-parts/form-renderer.php';
              else :
              ?>
              <p>お問い合わせフォームの設定中です。</p>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

<?php get_footer(); ?>
