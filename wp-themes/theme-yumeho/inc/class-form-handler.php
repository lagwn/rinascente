<?php
/**
 * フォーム処理クラス（入力→確認→送信→完了）
 *
 * 使い方:
 *   $form = new Rinascente_Form_Handler('yumeho_contact', $fields_config);
 *   $form->process();  // POST処理
 *   $step = $form->get_step();  // 'input' | 'confirm' | 'complete' | 'error'
 *   $data = $form->get_data();  // サニタイズ済みデータ
 *   $errors = $form->get_errors();
 */

if (!defined('ABSPATH')) exit;

class Rinascente_Form_Handler {

    private string $form_id;
    private array  $fields;
    private array  $data    = [];
    private array  $errors  = [];
    private string $step    = 'input';

    // メール設定
    private string $admin_email  = '';
    private string $from_name    = '';
    private string $from_email   = '';
    private string $subject      = '';
    private string $auto_reply_subject = '';
    private string $honeypot_field = '_form_hp';
    private string $timestamp_field = '_form_ts';
    private int    $minimum_submit_seconds = 2;
    private int    $rate_limit_seconds = 60;

    /**
     * @param string $form_id  フォーム識別子（nonceに使用）
     * @param array  $fields   フィールド定義配列
     */
    public function __construct(string $form_id, array $fields) {
        $this->form_id = $form_id;
        $this->fields  = $fields;

        // デフォルトメール設定
        $this->admin_email = get_option('admin_email', 'info@example.com');
        $this->from_name   = get_bloginfo('name');
        $this->from_email  = $this->admin_email;
    }

    /* ──────────────────────────────────────
       メール設定
       ────────────────────────────────────── */

    public function set_mail_config(array $config): self {
        foreach (['admin_email', 'from_name', 'from_email', 'subject', 'auto_reply_subject'] as $key) {
            if (isset($config[$key])) {
                $this->$key = $config[$key];
            }
        }
        return $this;
    }

    /* ──────────────────────────────────────
       メイン処理
       ────────────────────────────────────── */

    public function process(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->step = 'input';
            $this->prefill_from_query();
            return;
        }

        // Nonce 検証
        if (!isset($_POST['_wpnonce']) ||
            !wp_verify_nonce($_POST['_wpnonce'], $this->form_id)) {
            $this->step = 'error';
            $this->errors[] = '不正なリクエストです。ページを再読み込みしてお試しください。';
            return;
        }

        // データ取得 & サニタイズ
        $this->sanitize_input();

        // アクション判定
        $action = sanitize_text_field($_POST['form_action'] ?? 'confirm');

        if (!$this->validate_request_security($action)) {
            return;
        }

        switch ($action) {
            case 'confirm':
                $this->handle_confirm();
                break;
            case 'back':
                $this->step = 'input';
                break;
            case 'send':
                $this->handle_send();
                break;
            default:
                $this->step = 'input';
        }
    }

    /* ──────────────────────────────────────
       確認画面へ
       ────────────────────────────────────── */

    private function handle_confirm(): void {
        $this->validate();

        if (!empty($this->errors)) {
            $this->step = 'input';
            return;
        }

        $this->step = 'confirm';
    }

    /* ──────────────────────────────────────
       送信処理
       ────────────────────────────────────── */

    private function handle_send(): void {
        $this->validate();

        if (!empty($this->errors)) {
            $this->step = 'input';
            return;
        }

        // 管理者宛メール送信
        $sent = $this->send_admin_mail();

        // 自動返信メール送信
        if ($sent && $this->get_email_field()) {
            $this->send_auto_reply();
        }

        $this->step = $sent ? 'complete' : 'error';

        if ($sent) {
            set_transient($this->get_rate_limit_key(), 1, $this->rate_limit_seconds);
        }

        if (!$sent) {
            $this->errors[] = 'メール送信に失敗しました。しばらく経ってからお試しください。';
        }
    }

    /* ──────────────────────────────────────
       サニタイズ
       ────────────────────────────────────── */

    private function sanitize_input(): void {
        foreach ($this->fields as $name => $config) {
            $raw = isset($_POST[$name]) && !is_array($_POST[$name])
                ? (string) wp_unslash($_POST[$name])
                : '';
            $type = $config['type'] ?? 'text';
            $this->data[$name] = $this->sanitize_field_value($raw, $type);
        }
    }

    private function prefill_from_query(): void {
        foreach ($this->fields as $name => $config) {
            $raw = $this->get_prefill_query_value($name, $config);
            if (null === $raw || '' === $raw) {
                continue;
            }

            $type = $config['type'] ?? 'text';
            $this->data[$name] = $this->sanitize_field_value($raw, $type);
        }
    }

    private function get_prefill_query_value(string $name, array $config): ?string {
        $query_args = array_merge(
            [$name],
            array_values(array_filter((array) ($config['query_args'] ?? []), 'is_string'))
        );

        foreach (array_unique($query_args) as $query_arg) {
            if (!isset($_GET[$query_arg]) || is_array($_GET[$query_arg])) {
                continue;
            }

            return (string) wp_unslash($_GET[$query_arg]);
        }

        return null;
    }

    private function sanitize_field_value(string $raw, string $type): string {
        switch ($type) {
            case 'email':
                return sanitize_email($raw);
            case 'textarea':
                return sanitize_textarea_field($raw);
            case 'select':
            case 'radio':
                return sanitize_text_field($raw);
            case 'checkbox':
                return !empty($raw) ? '同意' : '';
            default:
                return sanitize_text_field($raw);
        }
    }

    /* ──────────────────────────────────────
       バリデーション
       ────────────────────────────────────── */

    private function validate(): void {
        $this->errors = [];

        foreach ($this->fields as $name => $config) {
            $value = $this->data[$name] ?? '';
            $label = $config['label'] ?? $name;

            // 必須チェック
            if (!empty($config['required']) && $value === '') {
                $this->errors[$name] = "{$label}は必須項目です。";
                continue;
            }

            if ($value === '') continue;

            // メール形式チェック
            if (($config['type'] ?? '') === 'email' && !is_email($value)) {
                $this->errors[$name] = "正しいメールアドレスを入力してください。";
            }

            // 電話番号形式チェック
            if (($config['type'] ?? '') === 'tel' && !preg_match('/^[\d\-\+\(\)\s]{7,20}$/', $value)) {
                $this->errors[$name] = "正しい電話番号を入力してください。";
            }
        }
    }

    private function validate_request_security(string $action): bool {
        if ($action === 'back') {
            return true;
        }

        $honeypot = $this->get_request_honeypot_value();
        if ($honeypot !== '') {
            $this->step = 'error';
            $this->errors[] = '送信を完了できませんでした。時間をおいて再度お試しください。';
            return false;
        }

        $submitted_at = $this->get_request_timestamp_value();
        if ($submitted_at < 1 || (time() - $submitted_at) < $this->minimum_submit_seconds) {
            $this->step = 'input';
            $this->errors[] = '送信を完了できませんでした。入力内容を確認して、少し時間をおいて再度お試しください。';
            return false;
        }

        if ($action === 'send' && get_transient($this->get_rate_limit_key())) {
            $this->step = 'input';
            $this->errors[] = '短時間に送信が集中しています。1分ほどおいて再度お試しください。';
            return false;
        }

        return true;
    }

    /* ──────────────────────────────────────
       管理者宛メール
       ────────────────────────────────────── */

    private function send_admin_mail(): bool {
        $subject = $this->subject ?: "【{$this->from_name}】お問い合わせがありました";

        $body = "以下の内容でお問い合わせがありました。\n";
        $body .= str_repeat('─', 40) . "\n\n";

        foreach ($this->fields as $name => $config) {
            if (($config['type'] ?? '') === 'checkbox') continue;
            $label = $config['label'] ?? $name;
            $value = $this->data[$name] ?? '';
            if ($value !== '') {
                $body .= "■ {$label}\n{$value}\n\n";
            }
        }

        $body .= str_repeat('─', 40) . "\n";
        $body .= "送信日時: " . wp_date('Y年m月d日 H:i') . "\n";
        $body .= "送信元IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

        $headers = [
            "From: {$this->from_name} <{$this->from_email}>",
            'Content-Type: text/plain; charset=UTF-8',
        ];

        // 送信者のメールアドレスを Reply-To に設定
        $email = $this->get_email_field();
        if ($email) {
            $sender_name = $this->data[array_key_first(
                array_filter($this->fields, fn($c) => ($c['type'] ?? '') !== 'email' && str_contains(strtolower($c['label'] ?? ''), '名'))
            )] ?? '';
            $headers[] = "Reply-To: {$sender_name} <{$email}>";
        }

        return wp_mail($this->admin_email, $subject, $body, $headers);
    }

    /* ──────────────────────────────────────
       自動返信メール
       ────────────────────────────────────── */

    private function send_auto_reply(): bool {
        $email = $this->get_email_field();
        if (!$email) return false;

        $subject = $this->auto_reply_subject
            ?: "【{$this->from_name}】お問い合わせありがとうございます";

        $name = '';
        foreach ($this->fields as $n => $c) {
            if (str_contains(strtolower($c['label'] ?? ''), '氏名') ||
                str_contains(strtolower($c['label'] ?? ''), '名前') ||
                str_contains(strtolower($c['label'] ?? ''), 'お名前')) {
                $name = $this->data[$n] ?? '';
                break;
            }
        }

        $body  = "{$name} 様\n\n";
        $body .= "この度はお問い合わせいただき、誠にありがとうございます。\n";
        $body .= "以下の内容でお問い合わせを承りました。\n\n";
        $body .= str_repeat('─', 40) . "\n\n";

        foreach ($this->fields as $n => $config) {
            if (($config['type'] ?? '') === 'checkbox') continue;
            $label = $config['label'] ?? $n;
            $value = $this->data[$n] ?? '';
            if ($value !== '') {
                $body .= "■ {$label}\n{$value}\n\n";
            }
        }

        $body .= str_repeat('─', 40) . "\n\n";
        $body .= "担当者より2営業日以内にご連絡いたします。\n";
        $body .= "しばらくお待ちくださいますようお願い申し上げます。\n\n";
        $body .= "━━━━━━━━━━━━━━━━━━━━\n";
        $body .= "{$this->from_name}\n";
        $tel   = function_exists( 'yumeho_theme_mod' ) ? yumeho_theme_mod( 'company_tel', '' ) : '';
        $hours = function_exists( 'yumeho_theme_mod' ) ? yumeho_theme_mod( 'company_hours', '' ) : '';
        if ($tel)   $body .= "TEL: {$tel}";
        if ($hours) $body .= "（{$hours}）";
        if ($tel)   $body .= "\n";
        $body .= "━━━━━━━━━━━━━━━━━━━━\n";
        $body .= "\n※このメールは自動送信です。このメールに返信されても対応できません。\n";

        $headers = [
            "From: {$this->from_name} <{$this->from_email}>",
            'Content-Type: text/plain; charset=UTF-8',
        ];

        return wp_mail($email, $subject, $body, $headers);
    }

    /* ──────────────────────────────────────
       ヘルパー
       ────────────────────────────────────── */

    private function get_email_field(): string {
        foreach ($this->fields as $name => $config) {
            if (($config['type'] ?? '') === 'email') {
                return $this->data[$name] ?? '';
            }
        }
        return '';
    }

    private function get_request_honeypot_value(): string {
        return sanitize_text_field(wp_unslash($_POST[$this->honeypot_field] ?? ''));
    }

    private function get_request_timestamp_value(): int {
        return absint(wp_unslash($_POST[$this->timestamp_field] ?? 0));
    }

    private function get_rate_limit_key(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return 'rinascente_form_rate_' . md5($this->form_id . '|' . $ip);
    }

    private function render_security_fields(bool $persist = false): string {
        $timestamp = $persist ? $this->get_request_timestamp_value() : time();
        if ($timestamp < 1) {
            $timestamp = time();
        }

        $honeypot = $persist ? $this->get_request_honeypot_value() : '';

        $html  = '<div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;" aria-hidden="true">';
        $html .= '<label>入力しないでください';
        $html .= sprintf(
            '<input type="text" name="%s" value="%s" tabindex="-1" autocomplete="off">',
            esc_attr($this->honeypot_field),
            esc_attr($honeypot)
        );
        $html .= '</label></div>';
        $html .= sprintf(
            '<input type="hidden" name="%s" value="%d">',
            esc_attr($this->timestamp_field),
            $timestamp
        );

        return $html;
    }

    /* ──────────────────────────────────────
       テンプレート用 getter
       ────────────────────────────────────── */

    public function get_step(): string     { return $this->step; }
    public function get_data(): array      { return $this->data; }
    public function get_errors(): array    { return $this->errors; }
    public function get_fields(): array    { return $this->fields; }
    public function get_form_id(): string  { return $this->form_id; }

    /** 確認画面用: hidden input を生成 */
    public function render_hidden_fields(): string {
        $html  = wp_nonce_field($this->form_id, '_wpnonce', true, false);
        $html .= '<input type="hidden" name="form_action" value="send">';
        $html .= $this->render_security_fields(true);
        foreach ($this->data as $name => $value) {
            $html .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                esc_attr($name),
                esc_attr($value)
            );
        }
        return $html;
    }

    /** nonce フィールドを生成 */
    public function render_nonce(): string {
        return wp_nonce_field($this->form_id, '_wpnonce', true, false)
             . '<input type="hidden" name="form_action" value="confirm">'
             . $this->render_security_fields(false);
    }

    public function render_back_fields(): string {
        $html  = wp_nonce_field($this->form_id, '_wpnonce', true, false);
        $html .= '<input type="hidden" name="form_action" value="back">';
        $html .= $this->render_security_fields(true);
        foreach ($this->data as $name => $value) {
            $html .= sprintf(
                '<input type="hidden" name="%s" value="%s">',
                esc_attr($name),
                esc_attr($value)
            );
        }
        return $html;
    }

    /** 特定フィールドのエラーメッセージを取得 */
    public function field_error(string $name): string {
        return $this->errors[$name] ?? '';
    }

    /** 特定フィールドの値を取得（input の value 復元用） */
    public function field_value(string $name): string {
        return esc_attr($this->data[$name] ?? '');
    }
}
