# WP テーマ フォームシステム

プラグイン不要の PHP フォーム（入力→確認→送信→完了）

---

## ファイル構成

```
wp-theme-parts/
├── inc/
│   ├── class-form-handler.php      ← コアクラス（全サイト共通）
│   ├── form-config-yumeho.php      ← YUMEHO フォーム設定
│   └── form-config-rinascente.php  ← Rinascente フォーム設定
├── template-parts/
│   └── form-renderer.php           ← 表示テンプレート（3ステップ対応）
└── README.md
```

---

## WP テーマへの組み込み方

### 1. functions.php に読み込み追加

```php
// functions.php
require_once get_template_directory() . '/inc/class-form-handler.php';
require_once get_template_directory() . '/inc/form-config-yumeho.php';
// Rinascente テーマの場合:
// require_once get_template_directory() . '/inc/form-config-rinascente.php';
```

### 2. ページテンプレートで使用

```php
<?php
/**
 * Template Name: お問い合わせ
 */

get_header();

// フォーム初期化 & POST処理
$form = yumeho_contact_form();  // or rinascente_contact_form()
$form->process();
?>

<section class="section">
    <div class="container" style="max-width: 800px;">
        <div class="form-container">
            <?php include locate_template('template-parts/form-renderer.php'); ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
```

---

## 処理フロー

```
[入力画面]
    ↓ POST (form_action=confirm)
    ↓ サニタイズ → バリデーション
    ↓ エラーあり → [入力画面] に戻る（エラー表示 + 値復元）
    ↓ エラーなし
[確認画面]
    ├─ 「戻る」→ POST (form_action=back) → [入力画面]（値復元）
    └─ 「送信」→ POST (form_action=send)
        ↓ 再バリデーション（改ざん防止）
        ↓ wp_mail() で管理者宛メール送信
        ↓ wp_mail() で自動返信メール送信
[完了画面]
```

---

## セキュリティ

| 対策 | 実装 |
|------|------|
| CSRF | WordPress nonce（`wp_nonce_field` / `wp_verify_nonce`） |
| XSS | `esc_html()` / `esc_attr()` / `esc_textarea()` で全出力エスケープ |
| インジェクション | `sanitize_text_field()` / `sanitize_email()` で入力サニタイズ |
| 二重送信防止 | 確認画面で再 POST する方式（トークン付き） |
| メールヘッダインジェクション | `sanitize_email()` + ヘッダーの直接組立て回避 |

---

## シミュレーション結果の引き継ぎ（YUMEHO）

simulation.html から contact ページへ遷移する際、URLパラメータで結果を渡す。

```javascript
// simulation.html 側
function handleSubmitToContact(e) {
    e.preventDefault();
    const config = document.getElementById('resultList').innerText;
    const url = 'contact.html?config=' + encodeURIComponent(config);
    window.location.href = url;
}
```

```php
// page-contact.php 側（フォーム初期化前に）
if (isset($_GET['config'])) {
    // URLパラメータをフォームの message フィールドにプリセット
    add_filter('rinascente_form_default_value', function($value, $name) {
        if ($name === 'message' && isset($_GET['config'])) {
            return "【シミュレーション結果】\n" . sanitize_textarea_field(urldecode($_GET['config']));
        }
        return $value;
    }, 10, 2);
}
```

---

## メール設定の本番環境対応

### wp-config.php で SMTP 設定（推奨）

WordPress の `wp_mail()` はデフォルトで PHP `mail()` を使用するが、
本番環境では SMTP 経由にすることを推奨。

```php
// functions.php に追加、または WP Mail SMTP プラグインを使用
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.example.com';
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = 'noreply@example.com';
    $phpmailer->Password   = 'your-password';
    $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $phpmailer->CharSet    = 'UTF-8';
});
```

### 管理者メールアドレスの変更

```php
// form-config-yumeho.php 内
$form->set_mail_config([
    'admin_email' => 'info@rinascente.co.jp',  // 本番メールアドレス
    // ...
]);
```

---

## フォームの CSS

既存のプロトタイプ CSS（`.form-group`, `.form-control`, `.form-label` 等）を
そのままテーマの style.css に移植すれば、デザインは変更不要。

追加で必要なクラス:

```css
/* ステップインジケーター */
.form-step-indicator { display:flex; align-items:center; justify-content:center; gap:0; margin-bottom:32px; }
.form-step-item { display:flex; flex-direction:column; align-items:center; gap:6px; font-size:0.78rem; color:rgba(0,0,0,0.35); }
.form-step-item span { width:36px; height:36px; border-radius:50%; border:2px solid rgba(0,0,0,0.12); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.85rem; }
.form-step-item.active span { border-color:var(--primary-color); color:var(--primary-color); background:rgba(0,104,183,0.06); }
.form-step-item.active { color:var(--primary-color); font-weight:600; }
.form-step-item.done span { border-color:var(--primary-color); background:var(--primary-color); color:#fff; }
.form-step-line { flex:1; height:2px; background:rgba(0,0,0,0.08); margin:0 8px; }
.form-step-line.active { background:var(--primary-color); }

/* エラー */
.form-error-summary { background:#fef2f2; border:1px solid #fca5a5; border-radius:8px; padding:16px 20px; margin-bottom:24px; }
.form-error-summary p { font-weight:700; color:#dc2626; margin-bottom:8px; }
.form-error-summary ul { margin:0; padding-left:20px; color:#dc2626; font-size:0.88rem; }
.form-group.has-error .form-control { border-color:#dc2626; }
.form-field-error { color:#dc2626; font-size:0.82rem; margin:4px 0 0; }

/* 確認テーブル */
.confirm-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
.confirm-table th { text-align:left; padding:12px 16px; font-size:0.85rem; color:rgba(0,0,0,0.55); border-bottom:1px solid rgba(0,0,0,0.06); width:30%; vertical-align:top; }
.confirm-table td { padding:12px 16px; font-size:0.95rem; border-bottom:1px solid rgba(0,0,0,0.06); }

/* アクション */
.form-actions { text-align:center; margin-top:32px; }
.form-actions--two { display:flex; gap:12px; margin-top:28px; }

/* 完了 */
.form-complete { text-align:center; padding:48px 0; }
.form-complete__icon { width:64px; height:64px; border-radius:50%; background:var(--primary-color); display:flex; align-items:center; justify-content:center; margin:0 auto 24px; }
.form-complete h3 { font-size:1.4rem; font-weight:700; margin-bottom:12px; }
.form-complete p { font-size:0.92rem; color:rgba(0,0,0,0.6); line-height:1.75; margin-bottom:8px; }
.form-complete .btn { margin-top:24px; }

/* 確認画面リード */
.form-confirm-lead { font-size:0.88rem; color:rgba(0,0,0,0.55); margin-bottom:24px; line-height:1.6; }
```

---

## スパム対策（オプション）

プラグインなしでの基本的なスパム対策:

```php
// class-form-handler.php の validate() に追加

// ハニーポット（不可視フィールドに入力があったらスパム判定）
if (!empty($_POST['website_url'])) {  // ダミーフィールド
    $this->step = 'complete'; // スパマーには完了画面を見せて静かに破棄
    return;
}

// 時間制限（フォーム表示から3秒以内の送信はボット判定）
$form_loaded = intval($_POST['_form_ts'] ?? 0);
if ($form_loaded > 0 && (time() - $form_loaded) < 3) {
    $this->step = 'complete';
    return;
}
```

```html
<!-- フォームに追加 -->
<div style="position:absolute;left:-9999px;" aria-hidden="true">
    <input type="text" name="website_url" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="_form_ts" value="<?php echo time(); ?>">
```
