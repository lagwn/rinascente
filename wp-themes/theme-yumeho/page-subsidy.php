<?php
/**
 * Template Name: 補助金ガイド
 *
 * @package YUMEHO
 */
get_header();
?>

<style>
    /* ── Subsidy Cards ── */
    .subsidy-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 56px;
    }
    .subsidy-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        border-top: 3px solid var(--primary-color);
    }
    .subsidy-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .subsidy-card__body {
        padding: 28px 28px 24px;
    }
    .subsidy-card__number {
        font-family: var(--font-logo);
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        color: var(--primary-color);
        opacity: 0.82;
        margin-bottom: 8px;
    }
    .subsidy-card h3 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-color);
        line-height: 1.5;
    }
    .subsidy-card p {
        font-size: 0.84rem;
        line-height: 1.78;
        color: rgba(0,0,0,0.82);
        margin-bottom: 0;
    }
    .subsidy-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    .subsidy-card__tag {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: var(--primary-color);
        background: rgba(0,104,183,0.06);
        border: 1px solid rgba(0,104,183,0.12);
        border-radius: 3px;
        padding: 3px 10px;
    }

    /* ── Schedule Timeline ── */
    .schedule-timeline {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0;
        margin-bottom: 56px;
        padding: 0 20px;
    }
    .schedule-timeline::before {
        content: "";
        position: absolute;
        top: 23px;
        left: 60px;
        right: 60px;
        height: 2px;
        background: repeating-linear-gradient(
            to right,
            rgba(0,104,183,0.2) 0, rgba(0,104,183,0.2) 6px,
            transparent 6px, transparent 12px
        );
    }
    .schedule-step {
        position: relative;
        text-align: center;
        flex: 1;
        min-width: 0;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .schedule-step.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .schedule-marker {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-logo);
        font-size: 0.85rem;
        font-weight: 700;
        color: #fff;
        margin: 0 auto 14px;
        box-shadow: 0 3px 14px rgba(0,104,183,0.22);
        position: relative;
        z-index: 2;
    }
    .schedule-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 4px;
        line-height: 1.5;
    }
    .schedule-note {
        font-size: 0.72rem;
        color: rgba(0,0,0,0.72);
        line-height: 1.6;
    }
    .schedule-arrow {
        position: absolute;
        top: 18px;
        right: -8px;
        color: var(--primary-color);
        opacity: 0.7;
        z-index: 3;
    }

    /* ── Support Section ── */
    .support-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }
    .support-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 22px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.04);
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .support-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 28px rgba(0,104,183,0.1);
    }
    .support-card__icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--primary-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .support-card__icon svg {
        width: 22px;
        height: 22px;
        stroke: #fff;
        fill: none;
        stroke-width: 1.8;
    }
    .support-card h4 {
        font-size: 0.92rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-color);
    }
    .support-card p {
        font-size: 0.78rem;
        line-height: 1.72;
        color: rgba(0,0,0,0.82);
        margin-bottom: 0;
    }

    /* ── Highlight Box ── */
    .subsidy-highlight {
        background: linear-gradient(135deg, rgba(0,104,183,0.06), rgba(0,104,183,0.02));
        border: 1px solid rgba(0,104,183,0.14);
        border-left: 4px solid var(--primary-color);
        border-radius: 10px;
        padding: 32px 36px;
        margin-bottom: 56px;
    }
    .subsidy-highlight h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .subsidy-highlight p {
        font-size: 0.88rem;
        line-height: 1.85;
        color: rgba(0,0,0,0.85);
        margin-bottom: 0;
    }
    .subsidy-highlight .stat {
        font-weight: 700;
        color: var(--primary-color);
    }

    /* ── Intro ── */
    .subsidy-intro {
        text-align: center;
        max-width: 780px;
        margin: 0 auto 56px;
        font-size: 0.92rem;
        color: rgba(0,0,0,0.85);
        line-height: 1.85;
    }
    .hero.bg-light .hero-subtitle.hero-subtitle--subsidy {
        max-width: none;
        white-space: nowrap;
    }

    @media (max-width: 860px) {
        .hero.bg-light .hero-subtitle.hero-subtitle--subsidy {
            white-space: normal;
        }
        .subsidy-cards { grid-template-columns: 1fr; }
        .support-grid { grid-template-columns: 1fr 1fr; }
        .schedule-timeline {
            flex-direction: column;
            align-items: stretch;
            gap: 24px;
            padding: 0;
        }
        .schedule-timeline::before {
            top: 0;
            bottom: 0;
            left: 23px;
            right: auto;
            width: 2px;
            height: auto;
            background: repeating-linear-gradient(
                to bottom,
                rgba(0,104,183,0.2) 0, rgba(0,104,183,0.2) 6px,
                transparent 6px, transparent 12px
            );
        }
        .schedule-step {
            text-align: left;
            padding-left: 64px;
        }
        .schedule-marker {
            position: absolute;
            left: 0;
            top: 0;
            margin: 0;
        }
        .schedule-arrow { display: none; }
    }
    @media (max-width: 540px) {
        .support-grid { grid-template-columns: 1fr; }
    }
</style>

    <section class="hero bg-light">
        <div class="container text-center">
            <p class="hero-en">SUBSIDY</p>
            <h1 class="hero-title">補助金・助成金 活用ガイド</h1>
            <p class="hero-subtitle hero-subtitle--subsidy">YUMEHOの導入に活用可能な補助金・助成金制度と、申請サポートについてご案内します。</p>
        </div>
    </section>

    <!-- Section 1: 活用可能な補助金制度 -->
    <section class="section">
        <div class="container" style="max-width: 960px; margin: 0 auto;">

            <p class="subsidy-intro">YUMEHOの導入には、国や自治体のさまざまな補助金・助成金制度を活用できる可能性があります。<br class="br-pc">主な制度と申請のポイントをご紹介します。</p>

            <div class="section-heading">
                <p class="section-kicker">Subsidy Programs</p>
                <h2 class="section-title">活用可能な補助金制度</h2>
            </div>

            <div class="subsidy-cards">

                <div class="subsidy-card animate-on-scroll">
                    <div class="subsidy-card__body">
                        <div class="subsidy-card__number">01</div>
                        <h3>介護ロボット導入支援事業</h3>
                        <p>厚生労働省が推進する介護ロボットの導入を支援する事業。YUMEHOは対象機器として認定されています。補助率：導入費用の1/2（上限あり）</p>
                        <div class="subsidy-card__meta">
                            <span class="subsidy-card__tag">厚生労働省</span>
                            <span class="subsidy-card__tag">介護ロボット</span>
                            <span class="subsidy-card__tag">補助率 1/2</span>
                        </div>
                    </div>
                </div>

                <div class="subsidy-card animate-on-scroll delay-100">
                    <div class="subsidy-card__body">
                        <div class="subsidy-card__number">02</div>
                        <h3>ものづくり補助金</h3>
                        <p>中小企業庁による設備投資支援。生産性向上に寄与する設備導入として申請可能なケースがあります。</p>
                        <div class="subsidy-card__meta">
                            <span class="subsidy-card__tag">中小企業庁</span>
                            <span class="subsidy-card__tag">設備投資</span>
                            <span class="subsidy-card__tag">生産性向上</span>
                        </div>
                    </div>
                </div>

                <div class="subsidy-card animate-on-scroll delay-200">
                    <div class="subsidy-card__body">
                        <div class="subsidy-card__number">03</div>
                        <h3>IT導入補助金</h3>
                        <p>歩行データ計測キット（PC連携）を含む構成の場合、IT導入補助金の対象となる可能性があります。</p>
                        <div class="subsidy-card__meta">
                            <span class="subsidy-card__tag">経済産業省</span>
                            <span class="subsidy-card__tag">IT導入</span>
                            <span class="subsidy-card__tag">デジタル化</span>
                        </div>
                    </div>
                </div>

                <div class="subsidy-card animate-on-scroll delay-300">
                    <div class="subsidy-card__body">
                        <div class="subsidy-card__number">04</div>
                        <h3>各自治体独自の助成制度</h3>
                        <p>都道府県・市区町村ごとに独自の福祉機器導入支援制度があります。お住まいの自治体の制度をご確認ください。</p>
                        <div class="subsidy-card__meta">
                            <span class="subsidy-card__tag">自治体</span>
                            <span class="subsidy-card__tag">福祉機器</span>
                            <span class="subsidy-card__tag">地域別</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Section 2: 申請スケジュール -->
            <div class="section-heading">
                <p class="section-kicker">Application Schedule</p>
                <h2 class="section-title">申請スケジュール</h2>
            </div>

            <div class="schedule-timeline">
                <div class="schedule-step animate-on-scroll">
                    <div class="schedule-marker">1</div>
                    <div class="schedule-label">事前相談</div>
                    <div class="schedule-note">随時</div>
                    <svg class="schedule-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <div class="schedule-step animate-on-scroll delay-100">
                    <div class="schedule-marker">2</div>
                    <div class="schedule-label">公募期間の確認</div>
                    <div class="schedule-note">年度初め</div>
                    <svg class="schedule-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <div class="schedule-step animate-on-scroll delay-200">
                    <div class="schedule-marker">3</div>
                    <div class="schedule-label">申請書類の準備</div>
                    <div class="schedule-note">1〜2ヶ月</div>
                    <svg class="schedule-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <div class="schedule-step animate-on-scroll delay-300">
                    <div class="schedule-marker">4</div>
                    <div class="schedule-label">審査・採択</div>
                    <div class="schedule-note">1〜2ヶ月</div>
                    <svg class="schedule-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </div>
                <div class="schedule-step animate-on-scroll delay-400">
                    <div class="schedule-marker">5</div>
                    <div class="schedule-label">導入・実績報告</div>
                    <div class="schedule-note">採択後</div>
                </div>
            </div>

            <!-- Section 3: Rinascenteの申請サポート -->
            <div class="section-heading">
                <p class="section-kicker">Application Support</p>
                <h2 class="section-title">Rinascenteの申請サポート</h2>
            </div>

            <div class="support-grid">
                <div class="support-card animate-on-scroll">
                    <div class="support-card__icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <h4>申請書類の作成支援</h4>
                    <p>テンプレートを提供し、記入例とともにスムーズな書類作成をサポートします。</p>
                </div>
                <div class="support-card animate-on-scroll delay-100">
                    <div class="support-card__icon">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <h4>導入効果の数値化</h4>
                    <p>稟議資料と連携し、費用対効果を定量的に可視化するサポートを行います。</p>
                </div>
                <div class="support-card animate-on-scroll delay-200">
                    <div class="support-card__icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <h4>過去の採択事例</h4>
                    <p>これまでに採択された施設の事例をご紹介し、申請のポイントをお伝えします。</p>
                </div>
                <div class="support-card animate-on-scroll delay-300">
                    <div class="support-card__icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <h4>制度選定のアドバイス</h4>
                    <p>施設の状況に最適な補助金制度を選定し、申請戦略をご提案します。</p>
                </div>
            </div>

            <!-- Highlight Box -->
            <div class="subsidy-highlight animate-on-scroll">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    補助金を活用した導入実績
                </h3>
                <p>過去2年間で<span class="stat">6施設</span>が補助金を活用してYUMEHOを導入。申請サポートの採択率は<span class="stat">80%以上</span>です。制度の選定から申請書類の作成まで、専任スタッフが一貫してサポートいたします。</p>
            </div>

            <!-- CTA -->
            <div class="text-center" style="margin-top: 32px; margin-bottom: 24px;">
                <p style="font-size: 0.92rem; color: rgba(0,0,0,0.85); line-height: 1.85; margin-bottom: 24px;">補助金を活用した導入のご相談は、<br class="br-sp">お気軽にお問い合わせください。</p>
                <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg">補助金活用のご相談 &rarr;</a>
            </div>

        </div>
    </section>

<script>
    // Animate schedule steps on scroll
    const scheduleSteps = document.querySelectorAll('.schedule-step');
    const stepObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.2 });
    scheduleSteps.forEach(step => stepObserver.observe(step));
</script>

<?php get_footer(); ?>
