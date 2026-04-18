# WordPress 構造化データ実装ガイド

Rinascente / YUMEHO サイト共通 — 2026-04-09 策定

---

## 推奨アーキテクチャ: Rank Math Free + テーマ補完

プラグイン費用ゼロで、SEOメタ・構造化データ・OGPを一元管理する。

```
┌─────────────────────────────────────────────┐
│  Layer 1: Rank Math Free                             │
│  → title / description / OGP / パンくず 自動生成      │
│  → ページ単位のスキーマタイプ選択 & プロパティ編集       │
│  → FAQPage ブロック（Gutenberg 連携）                 │
│  → NewsArticle / Product 等 15種以上のスキーマ対応     │
├─────────────────────────────────────────────┤
│  Layer 2: テーマ functions.php                        │
│  → Organization（全ページ共通グローバルスキーマ）        │
│  → Rank Math では設定しにくい複雑なネスト構造を補完      │
│  → カスタム投稿タイプの自動スキーマ出力                  │
└─────────────────────────────────────────────┘
```

---

## 必要プラグイン

| プラグイン | 用途 | コスト |
|-----------|------|--------|
| **Rank Math Free** | SEOメタ + 構造化データ + OGP + パンくず + サイトマップ | **無料** |

ACF Pro は不要。Rank Math Free の内蔵スキーマエディタで全ページの構造化データを管理画面から設定できる。

---

## Rank Math Free で出来ること

### SEO メタ管理
- ページごとの title / description カスタマイズ
- SNS 用 OGP（Facebook / Twitter）個別設定
- SEO スコア表示（リアルタイムプレビュー）
- XML サイトマップ自動生成
- パンくずリスト（BreadcrumbList JSON-LD 自動出力）

### 構造化データ（Schema）管理
- **ページ単位でスキーマタイプを選択**（管理画面 GUI）
- 対応スキーマタイプ（Free版）:
  - Article / NewsArticle / BlogPosting
  - Product
  - FAQPage
  - WebPage / AboutPage / ContactPage / CollectionPage
  - Organization / LocalBusiness
  - Person
  - Event
  - Course
  - Recipe
  - Service
  - SoftwareApplication
  - VideoObject
  - その他
- **プロパティをフォーム入力で編集**（コード不要）
- **カスタム投稿タイプにも自動対応**

### FAQPage スキーマ
- Gutenberg の FAQ ブロックを使用 → 自動で FAQPage JSON-LD を出力
- Q&A を管理画面で追加・編集するだけ

---

## 実装手順

### Phase 1: Rank Math 初期設定

```
1. Rank Math Free をインストール・有効化
2. セットアップウィザードを実行
   - サイトタイプ: 「Small Business」を選択
   - Organization 情報を入力（社名、ロゴ、連絡先）
   - ソーシャルプロフィールを入力（あれば）
3. Rank Math > 一般設定 > モジュール で以下を有効化:
   - ✅ SEO Analysis
   - ✅ Schema (Structured Data)
   - ✅ Sitemap
   - ✅ Breadcrumbs（※テーマ側で出力する場合）
4. Rank Math > Titles & Meta で各投稿タイプのデフォルト設定:
   - 固定ページ: デフォルトスキーマ = WebPage
   - 投稿（news）: デフォルトスキーマ = NewsArticle
   - カスタム投稿タイプ（case_study）: デフォルトスキーマ = Article
```

### Phase 2: カスタム投稿タイプ登録（functions.php）

```php
// functions.php

// ── ニュース（プレスリリース）投稿タイプ ──
function register_news_post_type() {
    register_post_type('news', [
        'labels' => [
            'name'          => 'ニュース',
            'singular_name' => 'ニュース',
            'add_new_item'  => '新しいニュースを追加',
            'edit_item'     => 'ニュースを編集',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'press'],
        'menu_icon'    => 'dashicons-megaphone',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest' => true, // Gutenberg 対応
    ]);
}
add_action('init', 'register_news_post_type');

// ── 導入事例 投稿タイプ ──
function register_case_study_post_type() {
    register_post_type('case_study', [
        'labels' => [
            'name'          => '導入事例',
            'singular_name' => '導入事例',
            'add_new_item'  => '新しい事例を追加',
            'edit_item'     => '事例を編集',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'cases'],
        'menu_icon'    => 'dashicons-building',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'register_case_study_post_type');
```

### Phase 3: グローバル Organization スキーマ（functions.php）

Rank Math のセットアップで基本的な Organization は出力されるが、
詳細なプロパティ（brand、contactPoint 等）を補完する。

```php
// functions.php

function rinascente_enhanced_organization_schema() {
    // トップページのみ出力（Rank Math の Organization と共存）
    if (!is_front_page()) return;

    $schema = [
        '@context'       => 'https://schema.org',
        '@type'          => 'Organization',
        'name'           => '株式会社Rinascente',
        'alternateName'  => ['リナシェンテ', 'Rinascente Inc.'],
        'url'            => home_url('/'),
        'logo'           => get_template_directory_uri() . '/assets/img/logo.png',
        'telephone'      => '0859-00-1234',
        'description'    => '医療・福祉機器の企画・販売。YUMEHO・MICA30を展開。',
        'foundingDate'   => '2026',
        'contactPoint'   => [
            '@type'             => 'ContactPoint',
            'telephone'         => '0859-00-1234',
            'contactType'       => 'customer service',
            'availableLanguage' => 'Japanese',
            'hoursAvailable'    => [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday','Tuesday','Wednesday','Thursday','Friday'],
                'opens'     => '09:00',
                'closes'    => '17:00',
            ],
        ],
        'brand' => [
            ['@type' => 'Brand', 'name' => 'YUMEHO', 'description' => '歩行リハビリ支援システム'],
            ['@type' => 'Brand', 'name' => 'MICA30', 'description' => '造影剤注入装置'],
        ],
        'knowsAbout' => [
            '歩行リハビリテーション',
            '医療機器',
            '福祉機器',
            '造影剤注入装置',
        ],
    ];

    echo '<script type="application/ld+json">'
        . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        . '</script>' . "\n";
}
add_action('wp_head', 'rinascente_enhanced_organization_schema', 5);
```

### Phase 4: Rank Math フィルターで投稿タイプ別スキーマを拡張

Rank Math の出力する JSON-LD を PHP フィルターでカスタマイズする。
これにより、管理画面の入力 + テーマのコード補完を組み合わせられる。

```php
// functions.php

// ── ニュース記事: publisher を自動補完 ──
add_filter('rank_math/snippet/rich_snippet', function($data, $post) {
    if (get_post_type($post) !== 'news') return $data;

    // publisher が未設定なら自動追加
    if (!isset($data['publisher'])) {
        $data['publisher'] = [
            '@type' => 'Organization',
            'name'  => '株式会社Rinascente',
            'logo'  => [
                '@type' => 'ImageObject',
                'url'   => get_template_directory_uri() . '/assets/img/logo.png',
            ],
        ];
    }

    return $data;
}, 20, 2);

// ── 導入事例: 施設情報を自動追加 ──
add_filter('rank_math/snippet/rich_snippet', function($data, $post) {
    if (get_post_type($post) !== 'case_study') return $data;

    // カスタムフィールドから施設名を取得して about に追加
    $facility_name = get_post_meta($post->ID, 'facility_name', true);
    if ($facility_name) {
        $data['about'] = [
            '@type' => 'MedicalOrganization',
            'name'  => $facility_name,
        ];
    }

    return $data;
}, 20, 2);

// ── 製品ページ: Product スキーマを拡張 ──
add_filter('rank_math/snippet/rich_snippet', function($data, $post) {
    if (!is_page('product')) return $data;

    $data['manufacturer'] = [
        '@type' => 'Organization',
        'name'  => '株式会社Rinascente',
    ];
    $data['category'] = '医療機器・福祉機器';
    $data['audience'] = [
        '@type'        => 'Audience',
        'audienceType' => '病院・介護施設・デイサービス',
    ];

    return $data;
}, 20, 2);
```

---

## ページタイプ × スキーマ対応表

### Rinascente コーポレートサイト

| ページ | URL | Schema @type | 設定方法 |
|--------|-----|-------------|---------|
| トップ | / | Organization | functions.php + Rank Math |
| 企業理念 | /identity/ | AboutPage | Rank Math 管理画面で選択 |
| 導入事例一覧 | /cases/ | CollectionPage | Rank Math 管理画面で選択 |
| 導入事例詳細 | /cases/{slug}/ | Article | Rank Math デフォルト + filter 拡張 |
| ニュース一覧 | /press/ | CollectionPage | Rank Math 管理画面で選択 |
| ニュース記事 | /press/{slug}/ | NewsArticle | Rank Math デフォルト + filter 拡張 |
| お問い合わせ | /contact/ | ContactPage | Rank Math 管理画面で選択 |

### YUMEHO プロダクトサイト

| ページ | URL | Schema @type | 設定方法 |
|--------|-----|-------------|---------|
| トップ | / | Product + Organization | functions.php + Rank Math |
| 製品紹介 | /product/ | Product | Rank Math 管理画面 + filter 拡張 |
| 導入シミュレーション | /simulation/ | WebPage | Rank Math 管理画面で選択 |
| 導入事例 | /cases/ | CollectionPage | Rank Math 管理画面で選択 |
| 導入の流れ | /flow/ | WebPage | Rank Math 管理画面で選択 |
| 価格・見積 | /price/ | WebPage | Rank Math 管理画面で選択 |
| 補助金ガイド | /subsidy/ | WebPage | Rank Math 管理画面で選択 |
| FAQ | /faq/ | FAQPage | Rank Math FAQ ブロック（自動生成） |
| 会社概要 | /company/ | AboutPage | Rank Math 管理画面で選択 |
| お問い合わせ | /contact/ | ContactPage | Rank Math 管理画面で選択 |

---

## 編集者の操作フロー

```
1. 固定ページ or 投稿を新規作成 / 編集

2. 右サイドバー「Rank Math SEO」パネル:
   ├─ 一般タブ
   │   ├─ SEO タイトル（プレビュー付き）
   │   ├─ メタディスクリプション（文字数カウント付き）
   │   └─ フォーカスキーワード
   ├─ スキーマタブ ← ★ 構造化データはここ
   │   ├─ スキーマタイプ選択（Product / Article / FAQPage 等）
   │   ├─ プロパティ入力フォーム
   │   │   ├─ Headline
   │   │   ├─ Description
   │   │   ├─ Image
   │   │   └─ （タイプに応じた追加項目）
   │   └─ JSON-LD プレビュー
   └─ ソーシャルタブ
       ├─ Facebook 用 title / description / image
       └─ Twitter 用 title / description / image

3. FAQ ページの場合:
   本文にRank Math FAQブロックを挿入
   → Q&A を追加するだけで FAQPage スキーマが自動生成

4. 「公開」ボタンで保存
   → JSON-LD が自動的に <head> に出力
   → functions.php のフィルターが追加プロパティを補完
```

---

## カスタムフィールド（ACF不要 — ネイティブ実装）

導入事例の施設情報など、Rank Math だけでは足りないデータは
WordPress ネイティブのカスタムフィールド + メタボックスで対応する。

```php
// functions.php

// ── 導入事例用メタボックス ──
function case_study_meta_boxes() {
    add_meta_box(
        'case_study_details',
        '事例情報（構造化データ連携）',
        'render_case_study_meta_box',
        'case_study',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'case_study_meta_boxes');

function render_case_study_meta_box($post) {
    wp_nonce_field('case_study_meta', 'case_study_nonce');
    $facility = get_post_meta($post->ID, 'facility_name', true);
    $type     = get_post_meta($post->ID, 'facility_type', true);
    $product  = get_post_meta($post->ID, 'product_name', true);
    ?>
    <p>
        <label>施設名</label><br>
        <input type="text" name="facility_name" value="<?php echo esc_attr($facility); ?>" style="width:100%">
    </p>
    <p>
        <label>施設種別</label><br>
        <select name="facility_type" style="width:100%">
            <option value="">選択</option>
            <option value="病院" <?php selected($type, '病院'); ?>>病院</option>
            <option value="介護老人保健施設" <?php selected($type, '介護老人保健施設'); ?>>介護老人保健施設</option>
            <option value="デイサービス" <?php selected($type, 'デイサービス'); ?>>デイサービス</option>
            <option value="クリニック" <?php selected($type, 'クリニック'); ?>>クリニック</option>
        </select>
    </p>
    <p>
        <label>導入製品</label><br>
        <select name="product_name" style="width:100%">
            <option value="">選択</option>
            <option value="YUMEHO" <?php selected($product, 'YUMEHO'); ?>>YUMEHO</option>
            <option value="MICA30" <?php selected($product, 'MICA30'); ?>>MICA30</option>
        </select>
    </p>
    <?php
}

function save_case_study_meta($post_id) {
    if (!isset($_POST['case_study_nonce']) ||
        !wp_verify_nonce($_POST['case_study_nonce'], 'case_study_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $fields = ['facility_name', 'facility_type', 'product_name'];
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post_case_study', 'save_case_study_meta');
```

---

## コスト比較

| 構成 | 年間コスト | 備考 |
|------|----------|------|
| ~~Yoast Free + ACF Pro~~ | ~~$99/年~~ | ~~旧プラン~~ |
| **Rank Math Free + テーマ補完** | **$0** | **推奨** |
| Rank Math Pro（将来拡張時） | $59/年 | スキーマテンプレート無制限、カスタムスキーマビルダー |

---

## Google 検証

- [Google Rich Results Test](https://search.google.com/test/rich-results) で各ページの出力を検証
- Search Console の「拡張」タブで構造化データのエラーを監視
- 主要ページは公開前に必ずテスト実行
- Rank Math 管理画面の「スキーマ」タブでJSON-LDプレビュー確認
