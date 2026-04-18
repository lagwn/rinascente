# CMS 管理コンテンツ要件定義

WordPress 実装用 — 原本: cmsコンテンツ.pdf（2026.04.07）

対象: コーポレートサイト（Rinascentes）/ 製品サイト（YUMEHO・MICA30）/ 会員専用サイト

---

## 全体方針

```
CMS化 優先度:
  高（日常更新） → ニュース、FAQ、導入事例
  高（一元管理） → 会社情報（3サイト×12箇所以上に掲載）
  中（価格変更） → 価格テーブル、スペック数値、シミュレーション係数
  低（限定公開） → 会員専用コンテンツ（動画・資料・サポート）
```

---

## 01 | ニュース / プレスリリース

### 掲載箇所

| ファイル | 用途 |
|---------|------|
| rinascentes/press.html | プレスリリース一覧（6件 + ページネーション） |
| rinascentes/news-*.html（4ページ） | 個別記事ページ |
| rinascentes/index.html | トップページに最新ニュースセクション |

### カスタム投稿タイプ: `news`

```php
'slug'     => 'press'
'has_archive' => true
'supports' => ['title', 'editor', 'thumbnail', 'excerpt']
```

### フィールド定義

| フィールド名 | 型 | 説明 | 備考 |
|------------|-----|------|------|
| タイトル | テキスト（WP標準） | 記事の見出し | 一覧ページに表示 |
| カテゴリ | カスタムタクソノミー `news_category` | 会社情報 / 製品情報 / 導入事例 / 受賞・認証 / 事業展開 | フィルター & バッジ表示 |
| 公開日 | 日付（WP標準 `post_date`） | YYYY.MM.DD 形式 | 一覧の並び順 |
| 概要テキスト | テキスト（WP標準 `excerpt`） | 一覧ページに表示される概要（80〜120字） | 一覧専用 |
| 本文 | リッチテキスト（WP標準 `content`） | 記事本文（見出し・段落・引用対応） | Gutenberg エディタ |
| アイキャッチ画像 | 画像（WP標準 `thumbnail`） | 一覧カード & OGP用 | |

### 一覧表示ルール

- 1ページ6件表示 + ページネーション
- カテゴリでフィルター切替（タブ or ボタン）
- トップページには最新3件を自動表示

---

## 02 | 会社情報

### 重要事項

> **同一情報を3サイト×12箇所以上に掲載**しているため、CMS で1箇所管理→全ページ自動反映の仕組みが必須。

### 掲載箇所

| ファイル | 用途 |
|---------|------|
| rinascentes/identity.html | 企業理念ページ全体 |
| yumeho/company.html | YUMEHO会社概要 |
| mica30/company.html | MICA30会社概要 |
| 全ページフッター（×3サイト） | フッター内の会社情報 |

### 実装方法: WP カスタマイザー or オプションページ

ACF不使用のため `customize_register` またはテーマ設定ページで管理。

### フィールド定義

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| 会社名 | テキスト | 株式会社Rinascente（リナシェンテ） |
| 代表者名 | テキスト | 代表取締役 〇〇〇〇 |
| 設立日 | テキスト | 2026年8月設立 |
| 所在地 | テキスト | 〇〇〇〇 |
| 電話番号 | テキスト | 0859-00-1234 |
| 事業内容 | テキスト | 医療機器・福祉機器の企画、販売 |
| 主要製品 | テキスト | YUMEHO, MICA30 |
| 受付時間 | テキスト | 平日 9:00〜17:00 |

### 実装コード例

```php
// functions.php — カスタマイザーに会社情報セクション追加
function company_info_customizer($wp_customize) {
    $wp_customize->add_section('company_info', [
        'title'    => '会社情報（全サイト共通）',
        'priority' => 30,
    ]);

    $fields = [
        'company_name'    => '会社名',
        'company_ceo'     => '代表者名',
        'company_founded' => '設立日',
        'company_address' => '所在地',
        'company_tel'     => '電話番号',
        'company_business'=> '事業内容',
        'company_products'=> '主要製品',
        'company_hours'   => '受付時間',
    ];

    foreach ($fields as $id => $label) {
        $wp_customize->add_setting($id, ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'company_info',
            'type'    => 'text',
        ]);
    }
}
add_action('customize_register', 'company_info_customizer');

// テンプレート内での使用
// <?php echo esc_html(get_theme_mod('company_name', '株式会社Rinascente')); ?>
```

### マルチサイト対応

3サイト（Rinascente / YUMEHO / MICA30）が WP マルチサイトの場合:
- メインサイト（Rinascente）のカスタマイザーに会社情報を一元管理
- 子サイトからは `switch_to_blog(1)` で取得、または REST API で配信

シングルサイトの場合:
- 共通テーマ内のカスタマイザーで一元管理
- テンプレート内で `get_theme_mod()` で取得

---

## 03 | 導入事例 / 施設の声

### 掲載箇所

| ファイル | 用途 |
|---------|------|
| rinascentes/cases.html | コーポレート導入事例一覧（6件）→ YUMEHO/MICA30 統合 |
| yumeho/cases.html | YUMEHO導入事例（病院・デイサービス・クリニック等） |
| mica30/cases.html | MICA30導入事例（病院・健診施設・クリニック） |
| mica30/voices.html | ご利用施設の声（施設 × 担当者）6〜8件 |

### カスタム投稿タイプ A: `case_study`（導入事例）

```php
'slug'        => 'cases'
'has_archive' => true
'supports'    => ['title', 'editor', 'thumbnail', 'excerpt']
```

### フィールド定義（導入事例）

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| 施設名・医療機関名 | テキスト / セレクト | 「〇〇総合病院」「△△リハビリ病棟」等 |
| 対象製品 | カスタムタクソノミー `product_type` | YUMEHO / MICA30（コーポレートページではフィルタ切替） |
| 施設種別 | カスタムタクソノミー `facility_type` | 病院 / 介護老健 / デイサービス / クリニック |
| 導入課題 / 決め手 / 導入効果 | リッチテキスト（本文 or カスタムフィールド） | テキスト・リストに対応 |
| 担当コメント / 担当者名・役職 | テキスト（カスタムフィールド） | 「導入して本当に...」のコメント + 〇〇 × 部署 |
| 数値成果 | キーバリュー（カスタムフィールド） | 「転倒事故: 0件」「訓練1.5倍」等の定量成果 |
| 施設画像 | 画像（アイキャッチ） | カード表示用サムネイル |

### カスタムフィールド実装（ネイティブメタボックス）

```php
// 導入事例用メタボックス
function case_study_meta_boxes() {
    add_meta_box('case_details', '事例詳細', 'render_case_meta', 'case_study', 'normal');
}
add_action('add_meta_boxes', 'case_study_meta_boxes');

function render_case_meta($post) {
    wp_nonce_field('case_meta', '_case_nonce');
    $fields = [
        'facility_name' => ['label' => '施設名', 'type' => 'text'],
        'staff_name'    => ['label' => '担当者名・役職', 'type' => 'text'],
        'staff_comment' => ['label' => '担当者コメント', 'type' => 'textarea'],
        'result_1_key'  => ['label' => '成果①（項目名）', 'type' => 'text'],
        'result_1_val'  => ['label' => '成果①（数値）', 'type' => 'text'],
        'result_2_key'  => ['label' => '成果②（項目名）', 'type' => 'text'],
        'result_2_val'  => ['label' => '成果②（数値）', 'type' => 'text'],
        'result_3_key'  => ['label' => '成果③（項目名）', 'type' => 'text'],
        'result_3_val'  => ['label' => '成果③（数値）', 'type' => 'text'],
    ];
    foreach ($fields as $key => $f) {
        $val = get_post_meta($post->ID, $key, true);
        echo "<p><label><strong>{$f['label']}</strong></label><br>";
        if ($f['type'] === 'textarea') {
            echo "<textarea name='{$key}' style='width:100%;height:80px;'>" . esc_textarea($val) . "</textarea>";
        } else {
            echo "<input type='text' name='{$key}' value='" . esc_attr($val) . "' style='width:100%;'>";
        }
        echo "</p>";
    }
}
```

### コレクション B: 施設の声（voices → MICA30 配下）

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| 関係者 / 診療科 / 施設画像 | テキスト / セレクト / 画像 | |
| 導入前課題 / 決め手 / 導入後の変化 | リッチテキスト + キーバリュー | 施設の具体名と合わせ評価情報 |

→ `case_study` 投稿タイプにタクソノミー `case_format`（導入事例 / 施設の声）を追加して一元管理も可。

---

## 04 | FAQ

### 掲載箇所

| ファイル | 用途 |
|---------|------|
| yumeho/faq.html | 6件（カテゴリ: 導入 / コスト / 運用 / デモ） |
| mica30/faq.html | 10件以上（カテゴリ: 6項目）アコーディオン表示 |

### カスタム投稿タイプ: `faq`

```php
'slug'        => 'faq'
'has_archive' => false
'supports'    => ['title', 'editor', 'custom-fields']
'publicly_queryable' => false  // 個別ページは不要（一覧のみ）
```

### フィールド定義

| フィールド名 | 型 | 説明 | 備考 |
|------------|-----|------|------|
| 対象製品 | カスタムタクソノミー `product_type` | YUMEHO / MICA30 | 各ページで絞り込み |
| カテゴリ | カスタムタクソノミー `faq_category` | 製品について/安全性 等 | タブフィルタ / カテゴリ表示 |
| 質問 (Q) | テキスト（WP標準 `title`） | アコーディオンの見出し部分 | |
| 回答 (A) | リッチテキスト（WP標準 `content`） | 展開時に表示（段落・リスト・リンク対応） | |
| 表示順 | 数値（`menu_order`） | 並び順制御 | |

### テンプレートでの取得例

```php
$faqs = new WP_Query([
    'post_type'      => 'faq',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'tax_query'      => [[
        'taxonomy' => 'product_type',
        'field'    => 'slug',
        'terms'    => 'yumeho',  // or 'mica30'
    ]],
]);
```

---

## 05 | 価格 / スペック数値

### 掲載箇所

| ファイル | 用途 |
|---------|------|
| yumeho/price.html | 価格テーブル・概算月額料金 |
| yumeho/simulation.html | コスト計算パラメーターデータソース |
| mica30/price.html | 価格・標準スペック表 |
| mica30/product.html | 製品スペック表（注入量・耐圧等） |

### YUMEHO 価格データ

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| 製品構成 / 型番 | テキスト | スタンド型・天井型 等（変更頻度: 高） |
| 基本価格 / オプション価格 | 数値（円） | 税込・税別両方 |
| シミュレーション係数 | 数値 | 施設規模別の計算パラメーター |
| 補助金関連情報 | テキスト | 金額・割合・補足・上限 |

### MICA30 スペックデータ

| フィールド名 | 値 |
|------------|-----|
| 注入量 | 0.05〜30mL |
| 注入速度 | 0.05〜5mL/s |
| 最大圧力 | 50〜500psi |
| 電源 / 寸法 / 重量 | AC100-240V, 250VA / 寸法 / 重量 |

### 実装方法: テーマ設定ページ（オプションページ）

価格・スペックは複数ページで参照されるため、**データソースとして一元管理が必須**。

```php
// functions.php — 価格管理ページ
function add_pricing_admin_page() {
    add_menu_page(
        '価格・スペック管理',
        '価格・スペック',
        'manage_options',
        'pricing-specs',
        'render_pricing_page',
        'dashicons-calculator',
        30
    );
}
add_action('admin_menu', 'add_pricing_admin_page');

function render_pricing_page() {
    if (isset($_POST['_pricing_nonce']) && wp_verify_nonce($_POST['_pricing_nonce'], 'save_pricing')) {
        // 保存処理
        $fields = ['yumeho_ceiling_price', 'yumeho_stand_price', 'yumeho_harness_price',
                   'mica30_injection_range', 'mica30_speed_range', 'mica30_pressure_max'];
        foreach ($fields as $f) {
            update_option($f, sanitize_text_field($_POST[$f] ?? ''));
        }
        echo '<div class="updated"><p>保存しました。</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>価格・スペック管理</h1>
        <form method="post">
            <?php wp_nonce_field('save_pricing', '_pricing_nonce'); ?>

            <h2>YUMEHO 価格</h2>
            <table class="form-table">
                <tr><th>天井直付型（FCW-3000）基本価格</th>
                    <td><input type="text" name="yumeho_ceiling_price" value="<?php echo esc_attr(get_option('yumeho_ceiling_price')); ?>" class="regular-text"> 円（税別）</td></tr>
                <tr><th>スタンド型（PGT-9000）基本価格</th>
                    <td><input type="text" name="yumeho_stand_price" value="<?php echo esc_attr(get_option('yumeho_stand_price')); ?>" class="regular-text"> 円（税別）</td></tr>
                <tr><th>追加ハーネス単価</th>
                    <td><input type="text" name="yumeho_harness_price" value="<?php echo esc_attr(get_option('yumeho_harness_price', '200000')); ?>" class="regular-text"> 円（税別）</td></tr>
            </table>

            <h2>MICA30 スペック</h2>
            <table class="form-table">
                <tr><th>注入量範囲</th>
                    <td><input type="text" name="mica30_injection_range" value="<?php echo esc_attr(get_option('mica30_injection_range', '0.05〜30mL')); ?>" class="regular-text"></td></tr>
                <tr><th>注入速度範囲</th>
                    <td><input type="text" name="mica30_speed_range" value="<?php echo esc_attr(get_option('mica30_speed_range', '0.05〜5mL/s')); ?>" class="regular-text"></td></tr>
                <tr><th>最大圧力</th>
                    <td><input type="text" name="mica30_pressure_max" value="<?php echo esc_attr(get_option('mica30_pressure_max', '500psi')); ?>" class="regular-text"></td></tr>
            </table>

            <?php submit_button('保存'); ?>
        </form>
    </div>
    <?php
}

// テンプレートでの使用
// <?php echo esc_html(get_option('yumeho_ceiling_price')); ?>
```

### 注意事項

- price / simulation / 各ページに同一データが反映されるよう、`get_option()` で統一取得
- シミュレーション JS のパラメーターは `wp_localize_script()` で PHP → JS に渡す

```php
wp_localize_script('simulation-js', 'YumehoPriceData', [
    'ceilingPrice' => intval(get_option('yumeho_ceiling_price', 0)),
    'standPrice'   => intval(get_option('yumeho_stand_price', 0)),
    'harnessPrice' => intval(get_option('yumeho_harness_price', 200000)),
]);
```

---

## 06 | 会員専用サイト

### 構成概要

| ファイル | 用途 |
|---------|------|
| rinascentes/login.html | コーポレートのマイページ認証 + ログインフォーム |
| rinascentes/member.html | ダッシュボード / コンテンツ一覧 |
| yumeho/login.php → member.php | YUMEHO会員ページ |

### 会員ダッシュボード コンテンツ

#### A. 導入実績（注文・契約管理）

| 情報 | 説明 |
|------|------|
| 注文書 / 見積書 / 契約 | テーブル型データ |
| 支払いステータス | 決済連携時に使用（現在ペンディング） |
| 施設名 / 契約日 / 契約情報 | 会員が確認する情報 |

→ WP 実装: カスタム投稿タイプ `contract`（管理者のみ編集可）+ 会員IDで絞り込み

#### B. 動画コンテンツ（期間限定公開）

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| YouTube動画ID | テキスト | 例: M7lc1UVf-VE |
| タイトル / 説明文 | テキスト | 「見えるリハビリ」研修 施術・編集の力 |
| カテゴリ / 対象製品 | セレクト | 設置方法 or 利用方法 / YUMEHO or MICA30 |
| 公開期間 | 日付（開始 / 終了） | 例: 契約から6ヶ月限定 |

→ WP 実装: カスタム投稿タイプ `member_video`
→ 閲覧制限: `is_user_logged_in()` + ユーザーメタで契約製品を判定

#### C. 資料ダウンロード（6件程度）

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| タイトル | テキスト | 仕様書・運用ガイド等 |
| ファイル | ファイル（PDF/DXF） | メディアライブラリで管理 |
| ファイルサイズ | テキスト（自動取得可） | |
| 更新日 | 日付 | |
| カテゴリ | セレクト | 仕様書 / 運用ガイド / コスト計算 等 |

→ WP 実装: カスタム投稿タイプ `member_document`
→ ダウンロードは `wp_get_attachment_url()` でリンク生成
→ 直リンク防止: `.htaccess` でログイン済みユーザーのみアクセス許可

#### D. 補助金サポート資料

| 情報 | 説明 |
|------|------|
| 認定ステータス | 対象 / 非対象 |
| 制度名 | テキスト |
| 制度PDF / 申請書雛形 | ファイル（2件程度） |

→ `member_document` で管理（カテゴリ: 補助金サポート）

#### E. サポート窓口

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| タイトル | テキスト | サポート窓口名 |
| メール / TEL | テキスト | 連絡先 |
| 受付時間 | テキスト | |

→ テーマ設定ページ（カスタマイザー）で管理

#### F. 施設レビュー / UGC（会員限定）

| フィールド名 | 型 | 説明 |
|------------|-----|------|
| 記入者名 | テキスト | |
| 施設名・施設種別 | テキスト | |
| 導入時期 | セレクト（1〜6ヶ月） | |
| レビュー本文 | テキストエリア | |
| タグ | テキスト | |
| 参考になった数 | 数値 | |

→ WP 実装: カスタム投稿タイプ `member_review`（会員がフロントから投稿）

### 会員認証の実装方法

```php
// WordPress 標準のユーザー管理を使用
// カスタムロール「facility_member」を追加

function add_facility_member_role() {
    add_role('facility_member', '施設会員', [
        'read' => true,
    ]);
}
register_activation_hook(__FILE__, 'add_facility_member_role');

// 会員ページのアクセス制限
function restrict_member_pages() {
    if (is_page_template('page-member.php') && !is_user_logged_in()) {
        wp_redirect(home_url('/login/'));
        exit;
    }
}
add_action('template_redirect', 'restrict_member_pages');
```

---

## カスタム投稿タイプ & タクソノミー 一覧

### 投稿タイプ

| 投稿タイプ | slug | アーカイブ | 用途 |
|-----------|------|----------|------|
| `news` | press | あり | ニュース / プレスリリース |
| `case_study` | cases | あり | 導入事例 / 施設の声 |
| `faq` | faq | なし | よくある質問 |
| `member_video` | — | なし | 会員限定動画 |
| `member_document` | — | なし | 会員限定資料 |
| `member_review` | — | なし | 施設レビュー |

### タクソノミー

| タクソノミー | slug | 対象投稿タイプ | 用途 |
|------------|------|-------------|------|
| `news_category` | news-cat | news | ニュースカテゴリ |
| `product_type` | product | case_study, faq | 対象製品（YUMEHO / MICA30） |
| `facility_type` | facility | case_study | 施設種別 |
| `faq_category` | faq-cat | faq | FAQカテゴリ |
| `case_format` | case-format | case_study | 導入事例 / 施設の声 |

---

## データ管理場所の整理

| データ | 管理場所 | 取得方法 |
|--------|---------|---------|
| 会社情報（社名・住所等） | カスタマイザー | `get_theme_mod()` |
| 価格・スペック数値 | テーマ設定ページ | `get_option()` |
| ニュース記事 | カスタム投稿タイプ `news` | `WP_Query` |
| 導入事例 | カスタム投稿タイプ `case_study` | `WP_Query` |
| FAQ | カスタム投稿タイプ `faq` | `WP_Query` |
| 会員コンテンツ | カスタム投稿タイプ各種 | `WP_Query` + ログイン判定 |
| サポート窓口 | カスタマイザー | `get_theme_mod()` |

---

## 実装優先度

| Phase | 内容 | 工数目安 |
|-------|------|---------|
| **Phase 1** | 投稿タイプ & タクソノミー登録、ニュース CRUD | 基盤構築 |
| **Phase 2** | 導入事例 & FAQ の CMS化、テンプレート接続 | コンテンツ管理 |
| **Phase 3** | 会社情報一元管理、価格・スペック管理ページ | データソース化 |
| **Phase 4** | 会員認証、ダッシュボード、動画・資料管理 | 会員機能 |
