<?php
/**
 * 汎用フォームレンダラー
 *
 * 使い方（ページテンプレート内で）:
 *   $form = yumeho_contact_form();  // or rinascente_contact_form()
 *   $form->process();
 *   include locate_template('template-parts/form-renderer.php');
 */

if (!defined('ABSPATH')) exit;
if (!isset($form) || !($form instanceof Rinascente_Form_Handler)) return;

$step   = $form->get_step();
$data   = $form->get_data();
$errors = $form->get_errors();
$fields = $form->get_fields();
?>

<?php if ($step === 'input'): ?>
<!-- ═══════════════════════════════════
     STEP 1: 入力
     ═══════════════════════════════════ -->
<div class="form-steps">
    <div class="form-step active"><span class="form-step__num">1</span><span class="form-step__label">入力</span></div>
    <div class="form-step__line"></div>
    <div class="form-step"><span class="form-step__num">2</span><span class="form-step__label">確認</span></div>
    <div class="form-step__line"></div>
    <div class="form-step"><span class="form-step__num">3</span><span class="form-step__label">完了</span></div>
</div>

<?php if (!empty($errors)): ?>
<div class="form-error-summary">
    <p>入力内容にエラーがあります。</p>
    <ul>
    <?php foreach ($errors as $err): ?>
        <li><?php echo esc_html($err); ?></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="post" action="" novalidate>
    <?php echo $form->render_nonce(); ?>

    <?php foreach ($fields as $name => $config):
        $type        = $config['type'] ?? 'text';
        $label       = $config['label'] ?? $name;
        $required    = !empty($config['required']);
        $placeholder = $config['placeholder'] ?? '';
        $options     = $config['options'] ?? [];
        $value       = $form->field_value($name);
        $error       = $form->field_error($name);
        $error_class = $error ? ' has-error' : '';
    ?>
    <div class="form-group<?php echo $error_class; ?>">

        <?php if ($type !== 'checkbox'): ?>
        <label class="form-label" for="field_<?php echo esc_attr($name); ?>">
            <?php echo esc_html($label); ?>
            <?php if ($required): ?><span class="required">必須</span><?php endif; ?>
        </label>
        <?php endif; ?>

        <?php if ($error): ?>
        <p class="form-field-error"><?php echo esc_html($error); ?></p>
        <?php endif; ?>

        <?php if ($type === 'text' || $type === 'email' || $type === 'tel'): ?>
            <input
                type="<?php echo esc_attr($type); ?>"
                id="field_<?php echo esc_attr($name); ?>"
                name="<?php echo esc_attr($name); ?>"
                value="<?php echo $value; ?>"
                placeholder="<?php echo esc_attr($placeholder); ?>"
                class="form-control"
                <?php echo $required ? 'required' : ''; ?>
            >

        <?php elseif ($type === 'textarea'): ?>
            <textarea
                id="field_<?php echo esc_attr($name); ?>"
                name="<?php echo esc_attr($name); ?>"
                rows="5"
                placeholder="<?php echo esc_attr($placeholder); ?>"
                class="form-control"
            ><?php echo esc_textarea($data[$name] ?? ''); ?></textarea>

        <?php elseif ($type === 'select'): ?>
            <select
                id="field_<?php echo esc_attr($name); ?>"
                name="<?php echo esc_attr($name); ?>"
                class="form-control form-select"
            >
                <option value="">選択してください</option>
                <?php foreach ($options as $opt): ?>
                <option value="<?php echo esc_attr($opt); ?>"
                    <?php selected($data[$name] ?? '', $opt); ?>>
                    <?php echo esc_html($opt); ?>
                </option>
                <?php endforeach; ?>
            </select>

        <?php elseif ($type === 'radio'): ?>
            <div class="radio-group">
            <?php foreach ($options as $opt): ?>
                <label class="radio-label">
                    <input type="radio"
                        name="<?php echo esc_attr($name); ?>"
                        value="<?php echo esc_attr($opt); ?>"
                        <?php checked($data[$name] ?? '', $opt); ?>
                        <?php echo $required ? 'required' : ''; ?>
                    >
                    <?php echo esc_html($opt); ?>
                </label>
            <?php endforeach; ?>
            </div>

        <?php elseif ($type === 'checkbox'): ?>
            <label class="checkbox-label">
                <input type="checkbox"
                    name="<?php echo esc_attr($name); ?>"
                    value="1"
                    <?php checked(!empty($data[$name])); ?>
                    <?php echo $required ? 'required' : ''; ?>
                >
                <span><?php echo esc_html($label); ?></span>
                <?php if ($required): ?><span class="required">必須</span><?php endif; ?>
            </label>
        <?php endif; ?>

    </div>
    <?php endforeach; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">確認画面へ</button>
    </div>
</form>


<?php elseif ($step === 'confirm'): ?>
<!-- ═══════════════════════════════════
     STEP 2: 確認
     ═══════════════════════════════════ -->
<div class="form-steps">
    <div class="form-step done"><span class="form-step__num">1</span><span class="form-step__label">入力</span></div>
    <div class="form-step__line"></div>
    <div class="form-step active"><span class="form-step__num">2</span><span class="form-step__label">確認</span></div>
    <div class="form-step__line"></div>
    <div class="form-step"><span class="form-step__num">3</span><span class="form-step__label">完了</span></div>
</div>

<p class="form-confirm-lead">入力内容をご確認ください。修正する場合は「戻る」を押してください。</p>

<table class="confirm-table">
<?php foreach ($fields as $name => $config):
    if (($config['type'] ?? '') === 'checkbox') continue;
    $value = $data[$name] ?? '';
    if ($value === '') continue;
?>
    <tr>
        <th><?php echo esc_html($config['label'] ?? $name); ?></th>
        <td><?php echo nl2br(esc_html($value)); ?></td>
    </tr>
<?php endforeach; ?>
</table>

<div class="form-actions form-actions--two">
    <!-- 戻るボタン -->
    <form method="post" action="" style="flex:1;">
        <?php echo $form->render_back_fields(); ?>
        <button type="submit" class="btn btn-secondary btn-lg" style="width:100%;">← 戻る</button>
    </form>

    <!-- 送信ボタン -->
    <form method="post" action="" style="flex:2;">
        <?php echo $form->render_hidden_fields(); ?>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">送信する →</button>
    </form>
</div>


<?php elseif ($step === 'complete'): ?>
<!-- ═══════════════════════════════════
     STEP 3: 完了
     ═══════════════════════════════════ -->
<div class="form-steps">
    <div class="form-step done"><span class="form-step__num">1</span><span class="form-step__label">入力</span></div>
    <div class="form-step__line"></div>
    <div class="form-step done"><span class="form-step__num">2</span><span class="form-step__label">確認</span></div>
    <div class="form-step__line"></div>
    <div class="form-step active"><span class="form-step__num">3</span><span class="form-step__label">完了</span></div>
</div>

<div class="form-complete">
    <div class="form-complete__icon">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
            <path d="M5 13l4 4L19 7" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <h3>送信が完了しました</h3>
    <p>お問い合わせいただきありがとうございます。</p>
    <p>担当者より2営業日以内にご連絡いたします。</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">トップページへ戻る</a>
</div>


<?php elseif ($step === 'error'): ?>
<!-- ═══════════════════════════════════
     エラー
     ═══════════════════════════════════ -->
<div class="form-error-page">
    <h3>送信エラー</h3>
    <?php foreach ($errors as $err): ?>
    <p><?php echo esc_html($err); ?></p>
    <?php endforeach; ?>
    <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-primary">フォームに戻る</a>
</div>

<?php endif; ?>
