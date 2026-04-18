<?php
/**
 * Template Name: 導入シミュレーション
 *
 * @package YUMEHO
 */
$catalog_context = yumeho_shared_product_catalog_context( 'yumeho' );
$pricing_config  = yumeho_pricing_catalog_config();
$stand_systems   = $catalog_context['stand_systems'];
$option_items    = $catalog_context['options'];
$extra_harness   = $catalog_context['by_pricing_key']['harness_extra'] ?? array(
    'display_name' => '追加ハーネス',
    'unit_label'   => '着',
);

$stand_system_notes = array();
foreach ( $stand_systems as $stand_system ) {
    $label = $stand_system['short_name'] ?: $stand_system['display_name'];
    $spec  = $stand_system['spec'] ?: yumeho_product_rail_label( $stand_system );
    if ( '' !== $label && '' !== $spec ) {
        $stand_system_notes[] = $label . '（' . $spec . '）';
    }
}

$ceiling_rail_options = yumeho_system_rail_length_options( $catalog_context['ceiling_system'] ?? array() );
if ( empty( $ceiling_rail_options ) ) {
    $ceiling_rail_options = yumeho_default_ceiling_rail_length_options();
}

get_header();
?>

    <style>
        @media (max-width: 767px) {
            .hero--simulation .container {
                text-align: center !important;
            }

            .hero--simulation .hero-title {
                display: block !important;
                width: fit-content !important;
                max-width: 100% !important;
                margin-inline: auto !important;
                text-align: center !important;
                letter-spacing: 0.12em !important;
            }

            .hero--simulation .hero-subtitle {
                text-align: center !important;
                margin-inline: auto !important;
            }
        }
    </style>

    <section class="hero hero--simulation bg-light">
        <div class="container text-center">
            <p class="hero-en">SIMULATION</p>
            <h1 class="hero-title">導入シミュレーション</h1>
            <p class="hero-subtitle">簡単な質問に答えるだけで、<br>最適なシステム構成をご提案します。</p>
        </div>
    </section>

    <section class="section">
        <div class="container sim-container">

            <!-- Progress Steps -->
            <div class="sim-progress">
                <div class="sim-step active" id="stepIndicator1">
                    <div class="sim-step-num">1</div>
                    <div>施設種別</div>
                </div>
                <div class="sim-step" id="stepIndicator2">
                    <div class="sim-step-num">2</div>
                    <div>設置方式</div>
                </div>
                <div class="sim-step" id="stepIndicator3">
                    <div class="sim-step-num">3</div>
                    <div>構成詳細</div>
                </div>
            </div>

            <form id="simForm">
                <!-- Step 1: Facility Type -->
                <div class="sim-content active" id="step1">
                    <h3 class="text-center" style="margin-bottom: 32px;">どのような施設への導入をご検討ですか？</h3>
                    <div class="sim-options">
                        <label class="sim-option-card">
                            <input type="radio" name="facilityType" value="病院" style="display:none;" onchange="selectOption(this)">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/facility_hospital.webp' ); ?>" alt="病院" class="sim-option-img" decoding="async">
                            <span class="sim-option-title">病院</span>
                            <span>回復期・リハビリ病棟など</span>
                        </label>
                        <label class="sim-option-card">
                            <input type="radio" name="facilityType" value="介護老人保健施設" style="display:none;" onchange="selectOption(this)">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/facility_nursing.webp' ); ?>" alt="老人保健施設" class="sim-option-img" decoding="async">
                            <span class="sim-option-title">老人保健施設</span>
                            <span>在宅復帰支援など</span>
                        </label>
                        <label class="sim-option-card">
                            <input type="radio" name="facilityType" value="デイサービス" style="display:none;" onchange="selectOption(this)">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/facility_dayservice.webp' ); ?>" alt="デイサービス" class="sim-option-img" decoding="async">
                            <span class="sim-option-title">デイサービス</span>
                            <span>機能訓練特化型など</span>
                        </label>
                    </div>
                    <div class="sim-nav-buttons" style="justify-content: flex-end;">
                        <button type="button" class="btn btn-primary" onclick="nextStep(1)">次へ</button>
                    </div>
                </div>

                <!-- Step 2: Installation Type -->
                <div class="sim-content" id="step2">
                    <h3 class="text-center" style="margin-bottom: 32px;">設置場所の条件は？</h3>
                    <div class="sim-options">
                        <label class="sim-option-card">
                            <input type="radio" name="installType" value="天井直付型" style="display:none;" onchange="selectOption(this)">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/install_ceiling.webp' ); ?>" alt="天井直付型" class="sim-option-img" decoding="async">
                            <span class="sim-option-title">天井直付型</span>
                            <span>天井にレールを固定。<br>足元がスッキリ広く使えます。</span>
                        </label>
                        <label class="sim-option-card">
                            <input type="radio" name="installType" value="スタンド型" style="display:none;" onchange="selectOption(this)">
                            <img src="<?php echo esc_url( YUMEHO_URI . '/assets/img/install_stand.webp' ); ?>" alt="スタンド型" class="sim-option-img" decoding="async">
                            <span class="sim-option-title">スタンド型</span>
                            <span>自立フレームを使用。<br>天井工事不要で導入可能です。</span>
                        </label>
                    </div>
                    <div class="sim-nav-buttons">
                        <button type="button" class="btn btn-secondary" onclick="prevStep(2)">戻る</button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">次へ</button>
                    </div>
                </div>

                <!-- Step 3: Details -->
                <div class="sim-content" id="step3">
                    <h3 class="text-center" style="margin-bottom: 32px;">ご希望の構成は？</h3>

                    <div class="card bg-light" style="margin-bottom: 24px; text-align: left;">
                        <h4 style="font-size: 1.1rem; margin-bottom: 16px;">レールの長さ</h4>
                        <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
                            <select id="railLength" name="railLength" style="min-width: 240px; padding: 12px 16px; border: 1px solid #ccc; border-radius: 10px; font-size: 1rem; background: #fff;">
                                <?php foreach ( $ceiling_rail_options as $index => $rail_length ) : ?>
                                    <option value="<?php echo esc_attr( (int) $rail_length ); ?>" <?php selected( 0 === (int) $index ); ?>><?php echo esc_html( (int) $rail_length ); ?>m</option>
                                <?php endforeach; ?>
                            </select>
                            <span id="railLengthVal" style="font-weight: bold; font-size: 1.1rem; color: var(--primary-color);"><?php echo esc_html( (int) $ceiling_rail_options[0] ); ?>m</span>
                        </div>
                        <p id="railLengthNote" style="font-size: 0.9rem; color: #666;">
                            ※天井直付型はカタログ掲載の標準レール長から選択します。
                        </p>
                    </div>

                    <div class="card bg-light" style="margin-bottom: 24px; text-align: left;">
                        <h4 style="font-size: 1.1rem; margin-bottom: 16px;">オプション製品</h4>
                        <?php foreach ( $option_items as $option_item ) : ?>
                            <?php
                            $pricing_key    = $option_item['pricing_option_key'];
                            $selection_type = $option_item['selection_type'] ?: 'quantity';
                            $max_quantity   = max( 1, (int) $option_item['max_quantity'] );
                            $unit_label     = $option_item['unit_label'] ?: '台';
                            $display_name   = $option_item['display_name'] ?: $option_item['title'];
                            ?>
                            <?php if ( 'checkbox' === $selection_type ) : ?>
                                <label style="display:flex; gap:8px; margin-bottom:10px; align-items:center;">
                                    <input type="checkbox" name="optionToggle[<?php echo esc_attr( $pricing_key ); ?>]" value="1" data-option="<?php echo esc_attr( $pricing_key ); ?>" data-selection-type="checkbox">
                                    <span style="font-size:0.92rem;"><?php echo esc_html( $display_name ); ?></span>
                                </label>
                            <?php else : ?>
                                <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
                                    <span style="font-size:0.92rem; min-width:280px;"><?php echo esc_html( $display_name ); ?></span>
                                    <select name="optionQty" data-option="<?php echo esc_attr( $pricing_key ); ?>" data-selection-type="quantity" style="padding:6px 12px; border:1px solid #ccc; border-radius:6px; font-size:0.9rem;">
                                        <?php for ( $i = 0; $i <= $max_quantity; $i++ ) : ?>
                                            <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span style="font-size:0.75rem; color:rgba(0,0,0,0.72);"><?php echo esc_html( $unit_label ); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <div style="display:flex; align-items:center; gap:12px; margin-top:12px;">
                            <span style="font-size:0.92rem; min-width:280px;"><?php echo esc_html( $extra_harness['display_name'] ); ?></span>
                            <select id="harnessCount" name="harnessCount" style="padding:6px 12px; border:1px solid #ccc; border-radius:6px; font-size:0.9rem;">
                                <option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
                            </select>
                            <span style="font-size:0.75rem; color:rgba(0,0,0,0.72);"><?php echo esc_html( $extra_harness['unit_label'] ?: '着' ); ?> @&yen;<?php echo esc_html( number_format( (int) $pricing_config['harnessPrice'] ) ); ?>/<?php echo esc_html( $extra_harness['unit_label'] ?: '着' ); ?>（税別）</span>
                        </div>
                    </div>

                    <div class="sim-nav-buttons">
                        <button type="button" class="btn btn-secondary" onclick="prevStep(3)">戻る</button>
                        <button type="button" class="btn btn-primary" onclick="showResult()">診断結果を表示</button>
                    </div>
                </div>

                <!-- Result -->
                <div class="sim-content" id="result">
                    <div class="sim-result-box">
                        <h3 class="sim-result-title">推奨システム構成</h3>
                        <p style="margin-bottom: 24px;">ご選択内容に基づき、以下の構成を推奨いたします。</p>
                        <ul class="sim-result-list" id="resultList">
                            <!-- JS will populate this -->
                        </ul>

                        <div class="sim-customer-box">
                            <h4 class="sim-customer-title">お客様情報をご入力ください</h4>
                            <div class="sim-customer-grid">
                                <label class="sim-customer-field">
                                    施設名・医療機関名 <span class="sim-required">必須</span>
                                    <input type="text" id="customerFacility" class="sim-customer-input" placeholder="例：医療法人社団○○会 ○○病院" required>
                                </label>
                                <label class="sim-customer-field">
                                    担当者様 氏名 <span class="sim-required">必須</span>
                                    <input type="text" id="customerName" class="sim-customer-input" placeholder="例：山田 太郎" required>
                                </label>
                                <label class="sim-customer-field">
                                    メールアドレス <span class="sim-required">必須</span>
                                    <input type="email" id="customerEmail" class="sim-customer-input" placeholder="例：yamada@example.com" required>
                                </label>
                                <label class="sim-customer-field">
                                    電話番号 <span class="sim-required">必須</span>
                                    <input type="tel" id="customerPhone" class="sim-customer-input" placeholder="例：03-1234-5678" required>
                                </label>
                            </div>
                            <label class="sim-customer-field">
                                備考（任意）
                                <textarea id="customerNote" class="sim-customer-input" rows="3" placeholder="導入時期やご希望条件があればご記入ください。"></textarea>
                            </label>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 16px; align-items: center;">
                            <p style="font-size: 0.9rem; color: #666;">※この内容は概算構成です。詳細なレイアウトや最終的な価格については、現地調査のうえ正式にお見積りいたします。</p>
                            <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-primary btn-lg" onclick="handleSubmitToContact(event)">この内容で見積依頼・相談をする</a>
                            <button type="button" class="btn btn-secondary" onclick="location.reload()">もう一度やり直す</button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <script>
    function handleSubmitToContact(e) {
        e.preventDefault();
        var config = document.getElementById('resultList').innerText.replace(/\n/g, ' / ');
        var facility = (document.getElementById('customerFacility') || {}).value || '';
        var name = (document.getElementById('customerName') || {}).value || '';
        var email = (document.getElementById('customerEmail') || {}).value || '';
        var tel = (document.getElementById('customerPhone') || {}).value || '';
        var note = (document.getElementById('customerNote') || {}).value || '';
        var messageText = 'シミュレーション結果による相談:\n' + config + (note ? '\n\n備考: ' + note : '');
        var params = new URLSearchParams({
            tmptype: '導入・見積相談',
            msg: messageText,
            facility: facility,
            contact_name: name,
            email: email,
            tel: tel
        });
        window.location.href = '<?php echo esc_url( home_url( '/contact/' ) ); ?>?' + params.toString();
    }
    </script>

<?php get_footer(); ?>
