# WordPress テンプレート構築 事前準備チェックリスト

2026-04-09 策定

---

## 作成済みドキュメント一覧

| ファイル | 内容 |
|---------|------|
| yumeho-seo-meta.md | YUMEHO 全ページの title / description / OGP / 構造化データ |
| rinascente-seo-meta.md | Rinascente 全ページの title / description / OGP / 構造化データ |
| wp-structured-data-guide.md | 構造化データ実装ガイド（Rank Math Free + テーマ補完） |
| wp-cms-requirements.md | CMS管理コンテンツ要件定義（投稿タイプ / タクソノミー / フィールド） |
| wp-theme-parts/ | フォームシステム PHP 実装（入力→確認→完了 + 自動返信） |

---

## 1. アクセス解析・タグ管理

### Google Analytics 4（GA4）

```
状態: アカウント取得待ち
```

**取得後の作業:**
- GA4 プロパティ作成 → 計測 ID（G-XXXXXXXXXX）を取得
- データストリーム（ウェブ）を追加
- 3サイト分のストリームを作成（Rinascente / YUMEHO / MICA30）

**WP テーマへの実装:**

```php
// functions.php — GA4 計測タグ挿入
function insert_ga4_tag() {
    $ga_id = get_theme_mod('ga4_measurement_id', '');
    if (empty($ga_id)) return;
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?php echo esc_js($ga_id); ?>');
    </script>
    <?php
}
add_action('wp_head', 'insert_ga4_tag', 1);
```

カスタマイザーから計測 ID を入力できるようにする:

```php
function ga4_customizer($wp_customize) {
    $wp_customize->add_section('analytics', [
        'title' => 'アクセス解析',
        'priority' => 160,
    ]);
    $wp_customize->add_setting('ga4_measurement_id', [
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('ga4_measurement_id', [
        'label'   => 'GA4 計測ID（G-XXXXXXXXXX）',
        'section' => 'analytics',
        'type'    => 'text',
    ]);
}
add_action('customize_register', 'ga4_customizer');
```

### Google Tag Manager（推奨）

GA4 を直接埋め込むより GTM 経由のほうが将来の拡張（広告タグ・ヒートマップ等）に対応しやすい。

```
GTM コンテナ ID: GTM-XXXXXXX（取得後に設定）
```

```php
// functions.php — GTM（head + body）
function insert_gtm_head() {
    $gtm_id = get_theme_mod('gtm_container_id', '');
    if (empty($gtm_id)) return;
    ?>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');</script>
    <?php
}
add_action('wp_head', 'insert_gtm_head', 1);

function insert_gtm_body() {
    $gtm_id = get_theme_mod('gtm_container_id', '');
    if (empty($gtm_id)) return;
    ?>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php
}
add_action('wp_body_open', 'insert_gtm_body');
```

### Google Search Console

```
状態: GA4 取得後に設定
```

**作業:**
- サイト所有権の確認（HTML タグ or DNS レコード）
- 3サイト分を登録
- XML サイトマップ送信（Rank Math が自動生成）
- インデックス登録リクエスト

---

## 2. ドメイン・サーバー

### 確認事項

| 項目 | 確認状況 | 備考 |
|------|---------|------|
| ドメイン取得 | □ 未 / □ 済 | 例: rinascente.co.jp |
| DNS 設定 | □ 未 / □ 済 | ネームサーバー・Aレコード |
| SSL 証明書 | □ 未 / □ 済 | Let's Encrypt（無料）or サーバー付属 |
| サーバー契約 | □ 未 / □ 済 | |
| PHP バージョン | — | 8.1 以上推奨 |
| MySQL バージョン | — | 8.0 以上推奨 |
| メールサーバー | □ 未 / □ 済 | wp_mail() の送信元。SMTP 設定推奨 |

### サーバー要件

| 項目 | 推奨値 |
|------|--------|
| PHP | 8.1+ |
| MySQL | 8.0+ / MariaDB 10.6+ |
| メモリ | 256MB 以上 |
| ストレージ | 10GB 以上（画像・動画含む） |
| SSL | 必須（常時 HTTPS） |
| HTTP/2 | 対応推奨 |

### 本番環境: Xserver スタンダードプラン（マルチドメイン運用）

```
サーバー: Xserver スタンダード
構成:     マルチドメイン（ドメイン別に独立 WP インストール）
PHP:     8.1+（Xserver 管理画面で設定）
MySQL:   MariaDB 10.5+（Xserver 標準）
SSL:     無料独自 SSL（Let's Encrypt）
```

**ドメイン構成（確定）:**
```
ドメインA/  → Rinascente コーポレートサイト（独立 WP）
ドメインB/  → YUMEHO 製品サイト（独立 WP）
ドメインC/  → MICA30 製品サイト（独立 WP）※ペンディング
```

各ドメインに独立した WordPress をインストール。マルチサイトは使用しない。

### Xserver セットアップ手順

```
1. Xserver サーバーパネル > ドメイン設定 > ドメインを追加
2. 各ドメインで SSL 設定 > 無料独自SSL を ON
3. 各ドメインで WordPress 簡単インストール
   - サイト名: Rinascente / YUMEHO
   - ユーザー名: admin 以外を設定（セキュリティ）
   - パスワード: 強力なもの
4. PHP バージョン設定 > PHP 8.1 以上を選択
5. Xserver > サーバーパネル > php.ini 設定:
   - memory_limit: 256M
   - upload_max_filesize: 64M
   - post_max_size: 64M
   - max_execution_time: 300
```

### Xserver 固有の wp-config.php 設定

```php
// wp-config.php に追加

// Xserver 環境最適化
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

// セキュリティ
define('DISALLOW_FILE_EDIT', true);
define('WP_AUTO_UPDATE_CORE', true);

// SSL 強制（Xserver の無料SSL使用時）
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// デバッグ（開発時のみ true、本番は false）
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
```

### Xserver メール送信（SMTP）

Xserver は PHP `mail()` が使えるため `wp_mail()` はそのまま動作する。
ただし到達率を上げるには Xserver のメールアカウントを SMTP 経由で使用:

```php
// functions.php — Xserver SMTP 設定
add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'sv***.xserver.jp';  // Xserver のSMTPホスト
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 587;
    $phpmailer->Username   = 'noreply@yourdomain.com';  // Xserver で作成したメールアカウント
    $phpmailer->Password   = 'メールパスワード';
    $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $phpmailer->CharSet    = 'UTF-8';
    $phpmailer->From       = 'noreply@yourdomain.com';
    $phpmailer->FromName   = get_bloginfo('name');
});
```

### Xserver キャッシュ設定

Xserver スタンダードには Xアクセラレータ（サーバーキャッシュ）が標準搭載:

```
サーバーパネル > Xアクセラレータ > ON（Ver.2 推奨）
→ WordPress のページキャッシュ + PHP OPcache が自動有効化
→ キャッシュプラグイン不要
```

---

## 3. 画像最適化

### プロトタイプからの画像移行

現在のプロトタイプには大量の画像がある。WP 移行時に最適化が必要。

**作業:**
- [ ] 全画像を WebP に変換（EWWW Image Optimizer Free or コマンドライン `cwebp`）
- [ ] レスポンシブ画像の `srcset` 対応（WP 標準で自動生成）
- [ ] OGP 用画像（1200×630px）を各サイト分作成
- [ ] ファビコンを ICO 形式にも変換（IE 対応不要なら PNG のみで OK）

```bash
# WebP 一括変換（コマンドライン）
for f in assets/img/*.{jpg,png}; do
  cwebp -q 80 "$f" -o "${f%.*}.webp"
done
```

**WP テーマでの WebP 対応:**

```php
// functions.php — WebP アップロード許可
function allow_webp_upload($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
}
add_filter('upload_mimes', 'allow_webp_upload');
```

---

## 4. セキュリティ

### WP 基本セキュリティ設定

```php
// functions.php に追加

// WordPress バージョン情報を非表示
remove_action('wp_head', 'wp_generator');

// XML-RPC を無効化（ブルートフォース対策）
add_filter('xmlrpc_enabled', '__return_false');

// REST API のユーザー列挙を制限
add_filter('rest_endpoints', function($endpoints) {
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});

// ログイン試行回数制限（プラグイン不要の簡易版）
function limit_login_attempts() {
    $max_attempts = 5;
    $lockout_time = 900; // 15分
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key) ?: 0;

    if ($attempts >= $max_attempts) {
        wp_die('ログイン試行回数の上限に達しました。15分後にお試しください。', 'ロックアウト', ['response' => 429]);
    }
}
add_action('wp_login_failed', function() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key) ?: 0;
    set_transient($transient_key, $attempts + 1, 900);
});
add_action('login_init', 'limit_login_attempts');
```

### wp-config.php セキュリティ設定

```php
// wp-config.php に追加
define('DISALLOW_FILE_EDIT', true);   // 管理画面からのファイル編集を禁止
define('WP_AUTO_UPDATE_CORE', true);  // コアの自動更新を有効化
```

---

## 5. パフォーマンス

### テーマ側で対応する最適化

```php
// functions.php

// 不要なヘッダー出力を削除
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

// CSS/JS にバージョンクエリを付与（キャッシュバスター）
function theme_asset_version() {
    return wp_get_theme()->get('Version') ?: '1.0.0';
}
```

### 推奨キャッシュ設定（.htaccess）

```apache
# ブラウザキャッシュ
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Gzip 圧縮
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>
```

---

## 6. 法務・コンプライアンス

### 必要なページ

| ページ | 状態 | 備考 |
|--------|------|------|
| プライバシーポリシー | □ 未作成 | GA4 / フォーム / Cookie 使用に必須 |
| 利用規約 | □ 未作成 | 会員機能がある場合は必須 |
| 特定商取引法に基づく表示 | □ 要否確認 | 物販・決済がある場合 |
| Cookie 同意バナー | □ 未実装 | GA4 使用時に推奨（EU 向けは必須） |

### Cookie 同意バナー（簡易実装）

```php
// functions.php
function cookie_consent_banner() {
    ?>
    <div id="cookieConsent" style="display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(10,10,10,0.95);color:#fff;padding:16px 24px;z-index:99999;font-size:0.85rem;display:flex;align-items:center;justify-content:space-between;gap:16px;">
        <p style="margin:0;">当サイトではCookieを使用してアクセス解析を行っています。<a href="/privacy-policy/" style="color:#90cdf4;">プライバシーポリシー</a></p>
        <button onclick="acceptCookies()" style="background:#fff;color:#000;border:none;padding:8px 20px;border-radius:999px;font-weight:700;cursor:pointer;white-space:nowrap;">同意する</button>
    </div>
    <script>
    if(!localStorage.getItem('cookieConsent')){document.getElementById('cookieConsent').style.display='flex';}
    function acceptCookies(){localStorage.setItem('cookieConsent','1');document.getElementById('cookieConsent').style.display='none';}
    </script>
    <?php
}
add_action('wp_footer', 'cookie_consent_banner');
```

---

## 7. 404 ページ

プロトタイプに 404 ページがないため、WP テーマで作成が必要。

```php
// 404.php テンプレート
<?php get_header(); ?>
<section class="section" style="min-height:60vh;display:flex;align-items:center;">
    <div class="container text-center">
        <h1 style="font-size:clamp(3rem,8vw,5rem);font-weight:700;opacity:0.15;margin-bottom:16px;">404</h1>
        <h2>ページが見つかりません</h2>
        <p>お探しのページは移動または削除された可能性があります。</p>
        <a href="<?php echo home_url('/'); ?>" class="btn btn-primary" style="margin-top:24px;">トップページへ戻る</a>
    </div>
</section>
<?php get_footer(); ?>
```

---

## 8. リダイレクト設計

プロトタイプの URL 構造と WP の URL 構造が異なる場合、301 リダイレクトが必要。

| プロトタイプ URL | WP URL（想定） | 対応 |
|----------------|---------------|------|
| /yumeho/index.html | /yumeho/ | .htaccess リダイレクト |
| /yumeho/product.html | /yumeho/product/ | 同上 |
| /rinascentes/news-20260315.html | /press/yumeho-pgt-9001/ | 個別設定 |

```apache
# .htaccess — HTML 拡張子の除去
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)\.html$ /$1/ [R=301,L]
```

---

## 9. テスト・公開チェックリスト

### 公開前テスト

- [ ] 全ページの表示確認（PC / タブレット / SP）
- [ ] フォーム送信テスト（管理者メール + 自動返信）
- [ ] 全リンクの動作確認（内部 / 外部）
- [ ] 画像の表示・alt 属性確認
- [ ] OGP 確認（Facebook シェアデバッガー / Twitter Card Validator）
- [ ] 構造化データ確認（Google Rich Results Test）
- [ ] PageSpeed Insights で 80 点以上確認
- [ ] robots.txt 確認（インデックスブロック解除）
- [ ] XML サイトマップ確認（Rank Math）
- [ ] SSL（HTTPS）強制リダイレクト確認
- [ ] 404 ページ表示確認
- [ ] 会員ページアクセス制限確認
- [ ] ブラウザテスト（Chrome / Safari / Firefox / Edge）

### 公開後

- [ ] Google Search Console にサイトマップ送信
- [ ] GA4 リアルタイムレポートでデータ取得確認
- [ ] 主要キーワードで検索してインデックス状況確認（1〜2週間後）
- [ ] Core Web Vitals モニタリング開始

---

## 10. WP プラグイン一覧（最小構成）

| プラグイン | 用途 | コスト |
|-----------|------|--------|
| **Rank Math Free** | SEO / 構造化データ / OGP / サイトマップ | 無料 |
| **WP Multibyte Patch** | 日本語対応（文字化け防止） | 無料 |
| **EWWW Image Optimizer** | 画像自動圧縮 / WebP 変換 | 無料 |
| **UpdraftPlus** | バックアップ（DB + ファイル） | 無料 |
| **Wordfence Security** | セキュリティ監視 + WAF + マルウェアスキャン + Slack通知連携 | 無料 |

**詳細:** [wp-security-monitoring.md](wp-security-monitoring.md) にWordfence設定 + Slack即時通知の構築手順を記載

**使用しないもの:**
- ACF Pro（不使用 → ネイティブメタボックス + カスタマイザー）
- フォームプラグイン（不使用 → テーマ内 PHP 実装）
- キャッシュプラグイン（サーバー側で対応推奨）

---

## 11. テーマファイル構成（想定）

```
theme-rinascente/
├── style.css                  ← テーマ情報
├── functions.php              ← 投稿タイプ / タクソノミー / GA4 / セキュリティ
├── index.php
├── header.php
├── footer.php
├── 404.php
├── front-page.php             ← トップページ
├── page.php                   ← 汎用固定ページ
├── page-contact.php           ← お問い合わせ（フォーム）
├── page-identity.php          ← 企業理念
├── single-news.php            ← ニュース記事
├── archive-news.php           ← ニュース一覧
├── single-case_study.php      ← 導入事例詳細
├── archive-case_study.php     ← 導入事例一覧
├── page-faq.php               ← FAQ
├── page-member.php            ← 会員ダッシュボード
├── page-login.php             ← ログイン
├── inc/
│   ├── class-form-handler.php
│   ├── form-config-rinascente.php
│   ├── form-config-yumeho.php
│   └── customizer.php         ← 会社情報 / GA4 / 価格管理
├── template-parts/
│   ├── form-renderer.php
│   ├── card-news.php
│   ├── card-case.php
│   └── card-faq.php
└── assets/
    ├── css/style.css          ← プロトタイプ CSS 移植
    ├── js/main.js             ← プロトタイプ JS 移植
    └── img/                   ← 画像
```

---

## 次のアクション

| 順番 | タスク | 依存 |
|------|--------|------|
| 1 | ドメイン・サーバー契約 | クライアント確認 |
| 2 | WP インストール + 初期設定 | サーバー準備後 |
| 3 | テーマ骨格作成（header/footer/functions.php） | — |
| 4 | プロトタイプ CSS/JS 移植 | テーマ骨格 |
| 5 | カスタム投稿タイプ & タクソノミー登録 | functions.php |
| 6 | 固定ページテンプレート作成 | CSS/JS 移植後 |
| 7 | フォーム実装 | テンプレート |
| 8 | SEO メタ・構造化データ設定 | Rank Math 設定 |
| 9 | GA4 / Search Console 接続 | アカウント取得後 |
| 10 | テスト・修正・公開 | 全工程完了後 |
