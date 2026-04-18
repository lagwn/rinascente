    <!-- Footer -->
    <footer class="footer">
        <?php
        $yumeho_member_page_url  = yumeho_member_page_url();
        $yumeho_member_login_url = yumeho_member_login_url( $yumeho_member_page_url );
        $yumeho_mica30_enabled   = function_exists( 'yumeho_mica30_enabled' ) && yumeho_mica30_enabled();
        ?>
        <div class="container">
            <div class="footer-links">
                <a href="<?php echo esc_url( home_url('/') ); ?>">ホーム</a>
                <a href="<?php echo esc_url( home_url('/product/') ); ?>">製品紹介</a>
                <a href="<?php echo esc_url( home_url('/simulation/') ); ?>">導入シミュレーション</a>
                <a href="<?php echo esc_url( home_url('/cases/') ); ?>">導入事例</a>
                <a href="<?php echo esc_url( home_url('/flow/') ); ?>">導入フロー</a>
                <a href="<?php echo esc_url( home_url('/price/') ); ?>">価格・見積</a>
                <a href="<?php echo esc_url( home_url('/subsidy/') ); ?>">補助金ガイド</a>
                <a href="<?php echo esc_url( home_url('/faq/') ); ?>">よくある質問</a>
                <a href="<?php echo esc_url( home_url('/contact/') ); ?>">お問い合わせ</a>
                <a href="<?php echo esc_url( home_url('/company/') ); ?>">会社概要</a>
                <a href="<?php echo esc_url( $yumeho_member_login_url ); ?>">会員サイト</a>
                <?php if ( $yumeho_mica30_enabled ) : ?>
                <a href="<?php echo esc_url( yumeho_related_site_url( 'mica30' ) ); ?>">MICA30 サイト</a>
                <?php endif; ?>
                <a href="<?php echo esc_url( yumeho_related_site_url( 'corporate' ) ); ?>">コーポレートサイト</a>
                <a href="<?php echo esc_url( home_url('/privacy-policy/') ); ?>">プライバシーポリシー</a>
                <a href="<?php echo esc_url( home_url('/terms/') ); ?>">利用規約</a>
                <a href="<?php echo esc_url( home_url('/commercial-law/') ); ?>">特定商取引法表記</a>
            </div>
            <a href="<?php echo esc_url( home_url('/contact/') ); ?>" class="footer-cta-sp">
                <span class="floating-cta__pulse"></span>
                資料請求・お問い合わせ
            </a>
            <p class="copyright">&copy; <?php echo date('Y'); ?> YUMEHO All Rights Reserved.</p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>

</html>
