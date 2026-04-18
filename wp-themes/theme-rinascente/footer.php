  </main>

  <footer class="site-footer">
    <?php
    $rinascente_member_page_url = rinascente_member_page_url();
    $rinascente_member_login_url = rinascente_member_login_url( $rinascente_member_page_url );
    ?>
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="footer-logo"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo.svg' ); ?>" alt="Rinascente" style="height:32px;vertical-align:middle;margin-right:4px;">Rinascente</div>
          <div class="footer-tagline">復活する。再生する。</div>
          <p>
            <span style="white-space:nowrap;">医療・福祉機器の企画・販売を中心に、</span><span style="white-space:nowrap;">人の生活を支えるソリューションを届けます。</span><br>
            <?php echo esc_html( get_theme_mod( 'company_address', '○○○○' ) ); ?><br>
            <span style="white-space:nowrap;">TEL: <?php echo esc_html( get_theme_mod( 'company_tel', '0859-00-1234' ) ); ?></span>
          </p>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>Corporate</h4>
          <ul>
            <li><a href="<?php echo esc_url( home_url('/') ); ?>">Home</a></li>
            <li><a href="<?php echo esc_url( home_url('/identity/') ); ?>">Identity</a></li>
            <li><a href="<?php echo esc_url( home_url('/press/') ); ?>">Press</a></li>
            <li><a href="<?php echo esc_url( home_url('/cases/') ); ?>">Cases</a></li>
            <li><a href="<?php echo esc_url( home_url('/contact/') ); ?>">Contact</a></li>
            <li><a href="<?php echo esc_url( is_user_logged_in() ? $rinascente_member_page_url : $rinascente_member_login_url ); ?>"><?php echo is_user_logged_in() ? 'Member Page' : 'Member Login'; ?></a></li>
            <?php if ( is_user_logged_in() ) : ?>
            <li><a href="<?php echo esc_url( wp_logout_url( home_url( '/login/' ) ) ); ?>">Logout</a></li>
            <?php endif; ?>
          </ul>
        </div>
        <div class="footer-col footer-col--desktop">
          <h4>YUMEHO</h4>
          <ul>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho' ) ); ?>">トップ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/product/' ) ); ?>">製品紹介</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/simulation/' ) ); ?>">導入シミュレーション</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/cases/' ) ); ?>">導入事例</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/flow/' ) ); ?>">導入フロー</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/price/' ) ); ?>">価格・見積</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/subsidy/' ) ); ?>">補助金ガイド</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/faq/' ) ); ?>">FAQ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/contact/' ) ); ?>">お問い合わせ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'yumeho', '/company/' ) ); ?>">会社概要</a></li>
          </ul>
        </div>
        <?php if ( function_exists( 'rinascente_mica30_enabled' ) && rinascente_mica30_enabled() ) : ?>
        <div class="footer-col footer-col--desktop">
          <h4>MICA30</h4>
          <ul>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30' ) ); ?>">トップ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/product/' ) ); ?>">製品詳細</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/cases/' ) ); ?>">導入事例</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/voices/' ) ); ?>">ご利用施設の声</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/price/' ) ); ?>">価格・導入</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/flow/' ) ); ?>">導入の流れ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/faq/' ) ); ?>">FAQ</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/company/' ) ); ?>">会社概要</a></li>
            <li><a href="<?php echo esc_url( rinascente_related_site_url( 'mica30', '/contact/' ) ); ?>">お問い合わせ</a></li>
          </ul>
        </div>
        <?php endif; ?>
      </div>
      <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> <?php echo esc_html( get_theme_mod( 'company_name', 'Rinascente' ) ); ?>. All Rights Reserved.</span>
        <span>
          <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" style="color:inherit;text-decoration:none;">Privacy Policy</a>
          /
          <a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>" style="color:inherit;text-decoration:none;">Terms</a>
        </span>
      </div>
    </div>
  </footer>

  <?php wp_footer(); ?>
</body>
</html>
