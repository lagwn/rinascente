<?php
/**
 * Template Name: お問い合わせ
 *
 * @package YUMEHO
 */
get_header();
?>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">CONTACT</p>
            <h1 class="hero-title">資料請求・お問い合わせ</h1>
            <p class="hero-subtitle">カタログ、導入事例集、稟議用サマリー、概算見積のご相談まで、<br class="br-pc">お気軽にお問い合わせください。</p>
        </div>
    </section>

    <section class="section">
        <div class="container" style="max-width: 800px;">
            <?php
            if ( function_exists( 'yumeho_contact_form' ) ) {
                $form = yumeho_contact_form();
                $form->process();
                include locate_template( 'template-parts/form-renderer.php' );
            } else {
            ?>
            <div class="form-container">
                <form method="post" action="">
                    <?php wp_nonce_field( 'yumeho_contact', 'yumeho_contact_nonce' ); ?>

                    <div class="form-group">
                        <label class="form-label">お問い合わせ内容 <span class="required">必須</span></label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="inquiry_type" value="資料請求" checked> 資料請求</label>
                            <label class="radio-label"><input type="radio" name="inquiry_type" value="導入・見積相談"> 導入・見積相談</label>
                            <label class="radio-label"><input type="radio" name="inquiry_type" value="デモ体験のご依頼"> デモ体験のご依頼</label>
                            <label class="radio-label"><input type="radio" name="inquiry_type" value="現地調査のご依頼"> 現地調査のご依頼</label>
                            <label class="radio-label"><input type="radio" name="inquiry_type" value="その他"> その他</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="facility">施設名・医療機関名 <span class="required">必須</span></label>
                        <input type="text" id="facility" name="facility" class="form-control" placeholder="例：医療法人社団○○会 ○○病院" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">施設種別 <span class="required">必須</span></label>
                        <div class="radio-group">
                            <label class="radio-label"><input type="radio" name="facility_type" value="病院"> 病院</label>
                            <label class="radio-label"><input type="radio" name="facility_type" value="介護老人保健施設"> 介護老人保健施設</label>
                            <label class="radio-label"><input type="radio" name="facility_type" value="デイサービス"> デイサービス</label>
                            <label class="radio-label"><input type="radio" name="facility_type" value="その他"> その他</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">あなたのお立場 <span class="required">必須</span></label>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:8px;">
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.9rem;cursor:pointer;">
                                <input type="radio" name="role" value="pt_ot" required> 理学療法士 / 作業療法士
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.9rem;cursor:pointer;">
                                <input type="radio" name="role" value="director"> 施設長 / 事務長
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.9rem;cursor:pointer;">
                                <input type="radio" name="role" value="procurement"> 購買担当 / 設備委員
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.9rem;cursor:pointer;">
                                <input type="radio" name="role" value="care_worker"> 介護職員
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;font-size:0.9rem;cursor:pointer;">
                                <input type="radio" name="role" value="other"> その他
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_name">担当者様 氏名 <span class="required">必須</span></label>
                        <input type="text" id="contact_name" name="contact_name" class="form-control" placeholder="例：山田 太郎" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">メールアドレス <span class="required">必須</span></label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="例：yamada@example.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tel">電話番号 <span class="required">必須</span></label>
                        <input type="tel" id="tel" name="tel" class="form-control" placeholder="例：03-1234-5678" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">ご質問・ご要望など</label>
                        <textarea id="message" name="message" class="form-control" rows="5" placeholder="ご自由にご記入ください。"></textarea>
                    </div>

                    <div class="text-center" style="margin-top: 40px;">
                        <button type="submit" class="btn btn-primary btn-lg" style="min-width: 240px;">送信する</button>
                    </div>
                </form>
            </div>
            <?php } ?>

            <div style="margin-top: 32px; padding: 24px; background: var(--surface-alt, #f5f5f5); border-radius: 8px; border: 1px solid var(--border-color, #e0e0e0);">
                <h3 style="font-size: 1.1rem; margin-bottom: 12px; color: var(--primary-color, #0068b7);">お問い合わせ後の流れ</h3>
                <ol style="padding-left: 20px; font-size: 0.9rem;">
                    <li style="margin-bottom: 8px;">担当者より<strong>2営業日以内</strong>にご連絡いたします</li>
                    <li style="margin-bottom: 8px;">ご希望に応じて、デモ体験・現地調査の日程を調整</li>
                    <li style="margin-bottom: 8px;">施設に最適な構成とお見積りをご提案</li>
                    <li>稟議用資料の作成サポートも承ります</li>
                </ol>
            </div>
        </div>
    </section>

<style>
.form-container {
    background: var(--white);
    padding: 40px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.form-group { margin-bottom: 24px; }
.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: var(--primary-color);
}
.form-label span.required {
    color: var(--accent-color);
    font-size: 0.8rem;
    margin-left: 8px;
    background: #ffe6eb;
    padding: 2px 6px;
    border-radius: 4px;
}
.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
}
.form-control:focus {
    border-color: var(--secondary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(62, 146, 204, 0.2);
}
.radio-group {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}
.radio-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
</style>

<?php get_footer(); ?>
