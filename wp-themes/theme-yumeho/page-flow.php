<?php
/**
 * Template Name: 導入の流れ
 *
 * @package YUMEHO
 */
get_header();
?>

<style>
    /* ── Flow Timeline ── */
    .flow-timeline {
        position: relative;
        max-width: 860px;
        margin: 0 auto;
        padding-left: 60px;
    }
    /* vertical line */
    .flow-timeline::before {
        content: "";
        position: absolute;
        left: 23px;
        top: 0;
        bottom: 0;
        width: 1px;
        background: repeating-linear-gradient(
            to bottom,
            rgba(0,104,183,0.18) 0, rgba(0,104,183,0.18) 5px,
            transparent 5px, transparent 11px
        );
    }

    .flow-item {
        position: relative;
        margin-bottom: 56px;
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .flow-item.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .flow-item:last-child { margin-bottom: 0; }

    /* circle marker on timeline */
    .flow-marker {
        position: absolute;
        left: -60px;
        top: 0;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-logo);
        font-size: 1.15rem;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.04em;
        box-shadow: 0 3px 14px rgba(0,104,183,0.22);
        z-index: 2;
    }

    .flow-card {
        background: #fff;
        border-radius: 10px;
        padding: 32px 36px;
        box-shadow: 0 2px 18px rgba(0,0,0,0.05);
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }
    .flow-card:hover {
        box-shadow: 0 8px 32px rgba(0,104,183,0.1);
        transform: translateY(-3px);
    }

    .flow-step-label {
        font-family: var(--font-logo);
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--primary-color);
        opacity: 0.85;
        margin-bottom: 6px;
    }

    .flow-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 10px;
        line-height: 1.5;
    }

    .flow-card p {
        font-size: 0.88rem;
        line-height: 1.85;
        color: rgba(0,0,0,0.85);
        margin-bottom: 0;
    }

    .flow-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 14px;
    }
    .flow-tag {
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        color: var(--primary-color);
        background: rgba(0,104,183,0.06);
        border: 1px solid rgba(0,104,183,0.12);
        border-radius: 3px;
        padding: 3px 10px;
    }

    .flow-duration {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 12px;
        font-size: 0.72rem;
        color: rgba(0,0,0,0.7);
    }
    .flow-duration svg {
        width: 13px;
        height: 13px;
        opacity: 0.8;
    }

    .flow-intro {
        text-align: center;
        max-width: 560px;
        margin: 0 auto 56px;
        font-size: 0.92rem;
        color: rgba(0,0,0,0.85);
        line-height: 1.85;
    }
    .flow-intro strong {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* ── Step 05 expanded ── */
    .flow-subsection-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--primary-color);
        letter-spacing: 0.08em;
        margin: 28px 0 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(0,104,183,0.10);
    }

    .training-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 8px;
    }

    .training-module {
        background: rgba(0,104,183,0.03);
        border: 1px solid rgba(0,104,183,0.08);
        border-radius: 8px;
        padding: 20px 22px;
    }
    .training-module-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .training-module-title .tm-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .training-module-title .tm-icon svg {
        width: 14px;
        height: 14px;
        stroke: #fff;
        fill: none;
        stroke-width: 2;
    }
    .training-module ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .training-module ul li {
        font-size: 0.78rem;
        line-height: 1.75;
        color: rgba(0,0,0,0.82);
        position: relative;
        padding-left: 14px;
    }
    .training-module ul li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 8px;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: var(--primary-color);
        opacity: 0.7;
    }

    .support-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 8px;
    }
    .support-item {
        text-align: left;
        padding: 22px 16px;
        background: rgba(0,104,183,0.03);
        border: 1px solid rgba(0,104,183,0.08);
        border-radius: 8px;
    }
    .support-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }
    .support-item-icon svg {
        width: 18px;
        height: 18px;
        stroke: #fff;
        fill: none;
        stroke-width: 2;
    }
    .support-item-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 8px;
    }
    .support-item-desc {
        font-size: 0.75rem;
        line-height: 1.7;
        color: rgba(0,0,0,0.8);
    }

    @media (max-width: 640px) {
        .flow-timeline { padding-left: 44px; }
        .flow-timeline::before { left: 16px; }
        .flow-marker {
            left: -44px;
            width: 34px;
            height: 34px;
            font-size: 0.95rem;
        }
        .flow-card { padding: 24px 20px; }
        .training-grid { grid-template-columns: 1fr; }
        .support-row { grid-template-columns: 1fr; }
    }
</style>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">FLOW</p>
            <h1 class="hero-title">導入の流れ</h1>
            <p class="hero-subtitle">お問い合わせからデモ体験、現地調査、設置、研修まで。<br>専任スタッフが一貫してサポートいたします。</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <p class="flow-intro">お問い合わせから最短<strong>2週間</strong>で導入可能。<br>既存施設への後付けもスムーズに対応します。</p>

            <div class="flow-timeline">

                <div class="flow-item animate-on-scroll">
                    <div class="flow-marker">01</div>
                    <div class="flow-card">
                        <div class="flow-step-label">STEP 01</div>
                        <h2 class="flow-title">お問い合わせ・ヒアリング</h2>
                        <p>施設の状況や課題をお伺いし、最適な導入プランを一緒に検討します。資料請求・デモ体験のご依頼もこちらから。</p>
                        <div class="flow-tags">
                            <span class="flow-tag">資料請求</span>
                            <span class="flow-tag">稟議用サマリー</span>
                            <span class="flow-tag">仕様資料</span>
                        </div>
                    </div>
                </div>

                <div class="flow-item animate-on-scroll">
                    <div class="flow-marker">02</div>
                    <div class="flow-card">
                        <div class="flow-step-label">STEP 02</div>
                        <h2 class="flow-title">デモ体験・現地調査・採寸</h2>
                        <p>実機でG-Suitの装着感やデュアルレールの走行感をご確認いただけます。専門スタッフが施設にお伺いし、天井高・幅・障害物・電源を確認します。</p>
                        <div class="flow-tags">
                            <span class="flow-tag">実機デモ</span>
                            <span class="flow-tag">現地採寸</span>
                            <span class="flow-tag">構造確認</span>
                        </div>
                        <div class="flow-duration">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            所要時間：約1時間
                        </div>
                    </div>
                </div>

                <div class="flow-item animate-on-scroll">
                    <div class="flow-marker">03</div>
                    <div class="flow-card">
                        <div class="flow-step-label">STEP 03</div>
                        <h2 class="flow-title">レイアウト提案・お見積り</h2>
                        <p>天井型（FCW-3000）またはスタンド型（PGT-9000/9001）の最適なレール配置とシステム構成をご提案。お見積書を提出いたします。</p>
                        <div class="flow-tags">
                            <span class="flow-tag">レール配置図</span>
                            <span class="flow-tag">見積書</span>
                            <span class="flow-tag">購入・リース対応</span>
                        </div>
                    </div>
                </div>

                <div class="flow-item animate-on-scroll">
                    <div class="flow-marker">04</div>
                    <div class="flow-card">
                        <div class="flow-step-label">STEP 04</div>
                        <h2 class="flow-title">ご契約・設置サポート</h2>
                        <p>設置にあたっての工事業者向け動画・マニュアルをご提供します。施設側の工事業者様にてスムーズに設置いただけます。</p>
                        <div class="flow-tags">
                            <span class="flow-tag">施工マニュアル提供</span>
                            <span class="flow-tag">動画で手順解説</span>
                        </div>
                        <div class="flow-duration">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            設置サポート：動画 + マニュアル一式
                        </div>
                    </div>
                </div>

                <div class="flow-item animate-on-scroll">
                    <div class="flow-marker">05</div>
                    <div class="flow-card">
                        <div class="flow-step-label">STEP 05</div>
                        <h2 class="flow-title">導入研修・運用開始・アフターサポート</h2>
                        <p>介護職員・PT/OTリーダーを対象に、G-Suit装着から安全運用、プレイ型リハビリ実技まで実践的な研修を実施。導入後も定期点検・運用相談・再研修で継続サポートいたします。</p>
                        <div class="flow-duration">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            研修：半日〜1日 / アフターサポート：継続
                        </div>

                        <!-- Sub-section 1: 研修プログラムの内容 -->
                        <h3 class="flow-subsection-title">研修プログラムの内容</h3>
                        <div class="training-grid">
                            <div class="training-module">
                                <div class="training-module-title">
                                    <span class="tm-icon"><svg viewBox="0 0 24 24"><path d="M12 2a5 5 0 015 5v3H7V7a5 5 0 015-5z"/><rect x="3" y="10" width="18" height="12" rx="2"/></svg></span>
                                    G-Suit装着研修
                                </div>
                                <ul>
                                    <li>ハーネスの正しい装着手順（ステップバイステップ）</li>
                                    <li>体型別のサイズ選定とフィッティング調整</li>
                                    <li>装着時間の短縮テクニック（目標：3分以内）</li>
                                </ul>
                            </div>
                            <div class="training-module">
                                <div class="training-module-title">
                                    <span class="tm-icon"><svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                                    安全運用研修
                                </div>
                                <ul>
                                    <li>レール走行操作と緊急停止手順</li>
                                    <li>転倒検知時の対応フロー</li>
                                    <li>利用者様の体調確認チェックリスト</li>
                                </ul>
                            </div>
                            <div class="training-module">
                                <div class="training-module-title">
                                    <span class="tm-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg></span>
                                    プレイ型リハビリ実技
                                </div>
                                <ul>
                                    <li>ボール投げ・バドミントン等の実技指導</li>
                                    <li>集団運用時のスタッフ配置プラン</li>
                                    <li>リハビリ記録の記入方法</li>
                                </ul>
                            </div>
                            <div class="training-module">
                                <div class="training-module-title">
                                    <span class="tm-icon"><svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91A6 6 0 0114.7 6.3z"/></svg></span>
                                    日常メンテナンス
                                </div>
                                <ul>
                                    <li>レール・ハーネスの日常点検チェックリスト</li>
                                    <li>G-Suitの清掃・消毒手順</li>
                                    <li>消耗品の交換時期と発注方法</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Sub-section 2: 運用開始後のサポート体制 -->
                        <h3 class="flow-subsection-title">運用開始後のサポート体制</h3>
                        <div class="support-row">
                            <div class="support-item">
                                <div class="support-item-icon">
                                    <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                                </div>
                                <div class="support-item-title">定期点検</div>
                                <p class="support-item-desc">専門スタッフによる年1回の定期点検。レール走行性能・ハーネス状態・安全機構を総合チェック。</p>
                            </div>
                            <div class="support-item">
                                <div class="support-item-icon">
                                    <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                </div>
                                <div class="support-item-title">運用相談窓口</div>
                                <p class="support-item-desc">操作方法や運用改善のご相談に専任スタッフが対応。電話・メールでお気軽にどうぞ。</p>
                            </div>
                            <div class="support-item">
                                <div class="support-item-icon">
                                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                                </div>
                                <div class="support-item-title">研修フォローアップ</div>
                                <p class="support-item-desc">スタッフ入れ替わり時の再研修や、プレイ型リハビリの新メニュー提案にも対応します。</p>
                            </div>
                        </div>

                        <div class="flow-tags">
                            <span class="flow-tag">実技研修</span>
                            <span class="flow-tag">運用マニュアル</span>
                            <span class="flow-tag">定期点検</span>
                            <span class="flow-tag">研修動画（会員エリア）</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="text-center" style="margin-top: 64px;">
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">まずはご相談ください</a>
            </div>
        </div>
    </section>

<?php get_footer(); ?>
