# コラム機能 サイト間連携 運用・拡張ガイド

Rinascente コーポレート ↔ YUMEHO 製品サイトのコラム連動について

策定: 2026-04-11

---

## 現在の構成（Phase 1: 連動方式）

### アーキテクチャ

```
[Rinascente コーポレート]
  └─ column CPT(管理画面で作成・編集)
       └─ メタフィールド:
          ├─ _rinascente_yumeho_publish (チェックボックス)
          └─ _rinascente_yumeho_lead (YUMEHO用リード文)
       └─ REST API公開: /wp-json/wp/v2/column

[YUMEHO 製品サイト]
  └─ column CPT なし(API取得のみ)
       └─ /column/, /column/{slug}/ をリライトルールで処理
       └─ コーポレートからAPIで取得・5分キャッシュ
       └─ 詳細ページに canonical タグでコーポレートURLを指定
```

### 編集者の運用

```
1. コーポレート管理画面でコラム記事を作成
2. 「YUMEHO サイトでも公開する」 ☑
3. 「YUMEHO 用リード文」を 100〜200字で記入(任意)
4. 公開
   ↓ 最大5分後
5. YUMEHO サイトの /column/ にも自動表示
```

### SEO 設計

| 項目 | 設計 |
|------|------|
| 正規 URL | コーポレート(https://onerinascente.co.jp/column/{slug}/) |
| canonical タグ | YUMEHO 側 →コーポレートURL を指定 |
| 重複コンテンツ判定 | 回避(canonical で評価集約) |
| YUMEHO 訪問者の体験 | 独自リード文 + 共通本文で情報提供 |

---

## 完全独自記事への切り替え方法

連動運用を進めて、必要に応じて段階的に独自化できます。**今すぐ準備する必要はなく、必要になったタイミングで実装可能** です。

---

### レベル1: 個別記事の差別化(コード変更不要・即対応可能)

**対応内容**
- コーポレート編集画面で「YUMEHO 用リード文」を 300〜500字に拡充
- YUMEHO 視点の独自内容を盛り込む(製品活用・実践Tips)

**メリット**
- コード変更ゼロ
- 編集作業のみ

**デメリット**
- 本文は共通(完全独自ではない)

---

### レベル2: YUMEHO に独自記事を併用(部分的独自化)

**対応内容**
- YUMEHO 側に独自コラム CPT を追加
- 連動記事と独自記事を併用表示
- 一覧では日付順に統合

**実装ステップ**

#### Step 1: YUMEHO 独自 CPT 登録

`functions.php` に追加:

```php
register_post_type( 'yumeho_column', array(
    'labels' => array(
        'name'          => 'コラム(独自)',
        'singular_name' => 'コラム',
    ),
    'public'       => true,
    'has_archive'  => false, // 既存ルーティングを使用
    'rewrite'      => false,
    'menu_icon'    => 'dashicons-edit-large',
    'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    'show_in_rest' => true,
) );

register_taxonomy( 'yumeho_column_category', 'yumeho_column', array(
    'labels' => array( 'name' => 'コラムカテゴリー' ),
    'public'       => true,
    'hierarchical' => true,
    'show_in_rest' => true,
) );
```

#### Step 2: スラッグ取得関数の拡張

```php
function yumeho_get_column_by_slug( $slug ) {
    // ローカル独自記事を優先
    $local = get_page_by_path( $slug, OBJECT, 'yumeho_column' );
    if ( $local && 'publish' === $local->post_status ) {
        return array(
            'source' => 'local',
            'post'   => $local,
        );
    }

    // なければコーポレートから取得
    $shared = yumeho_fetch_shared_column_by_slug( $slug );
    if ( $shared ) {
        return array(
            'source' => 'shared',
            'data'   => $shared,
        );
    }
    return null;
}
```

#### Step 3: 一覧で両方を統合

```php
function yumeho_get_all_columns( $limit = 12 ) {
    // ローカル記事
    $local_posts = get_posts( array(
        'post_type'      => 'yumeho_column',
        'posts_per_page' => -1,
    ) );

    $local = array_map( function( $p ) {
        return array(
            'source'  => 'local',
            'slug'    => $p->post_name,
            'title'   => $p->post_title,
            'excerpt' => get_the_excerpt( $p ),
            'date'    => $p->post_date,
        );
    }, $local_posts );

    // 連動記事
    $shared = yumeho_fetch_shared_columns( $limit );

    // マージして日付降順
    $merged = array_merge( $local, $shared );
    usort( $merged, function( $a, $b ) {
        return strtotime( $b['date'] ) - strtotime( $a['date'] );
    } );

    return array_slice( $merged, 0, $limit );
}
```

#### Step 4: テンプレート分岐

`yumeho-column-single.php` の冒頭:

```php
$result = yumeho_get_column_by_slug( $slug );
if ( ! $result ) {
    status_header( 404 );
    include get_query_template( '404' );
    exit;
}

if ( 'local' === $result['source'] ) {
    // 独自記事 — canonical なし、自サイトを正規URL扱い
    $post = $result['post'];
    setup_postdata( $post );
    // ... 独自記事用の表示
} else {
    // 連動記事 — canonical でコーポレートを指定
    $column = $result['data'];
    add_action( 'wp_head', function() use ( $column ) {
        echo '<link rel="canonical" href="' . esc_url( $column['link'] ) . '">';
    }, 1 );
    // ... 既存の連動記事表示
}
```

#### Step 5: 一覧表示でラベル分け(任意)

連動記事には「コーポレートサイトより」のラベル、独自記事には「YUMEHO オリジナル」のラベルなどを表示すると、ユーザーにも編集者にも分かりやすい。

**メリット**
- 段階的な移行が可能
- 連動記事を残しつつ、YUMEHO 独自記事も追加できる
- SEO 評価が独自記事には残る(canonical なし)

**デメリット**
- 編集者は2つの管理画面を使い分ける必要
- 実装に1〜2日

---

### レベル3: 完全独立化(連動を停止)

**対応内容**
- 連動関数を停止
- ローカル CPT のみで運用
- 既存の連動記事は引き取りまたは廃止

**実装ステップ**

#### Step 1: 連動関数の停止

```php
function yumeho_fetch_shared_columns( $limit = 6 ) {
    return array(); // 連動停止
}

function yumeho_fetch_shared_column_by_slug( $slug ) {
    return null; // 連動停止
}
```

#### Step 2: ローカル記事のみで運用

レベル2 で追加した `yumeho_column` を main CPT として運用継続。

#### Step 3: 既存の連動記事を引き取り(必要に応じて)

YUMEHO で残したい記事は、コーポレート側のコンテンツをコピー:

```sql
-- コーポレート DB から該当記事を取得して YUMEHO 側にINSERT
-- 手動コピーまたは WP-CLI でエクスポート/インポート
```

WP-CLI を使う場合:

```bash
# コーポレート側でエクスポート
wp --path=/path/to/rinascente export --post_type=column --post__in=114,117,124

# YUMEHO 側でインポート
wp --path=/path/to/yumeho import --authors=create export.xml
# その後、post_type を 'yumeho_column' に変更
wp --path=/path/to/yumeho post update <ID> --post_type=yumeho_column
```

#### Step 4: canonical タグ削除

`yumeho-column-single.php` の `add_action('wp_head', ...)` を削除。

#### Step 5: 旧URL 維持のためのリダイレクト

連動停止後に旧URL でアクセスがあった場合の対応:

```php
add_action( 'template_redirect', function() {
    $slug = get_query_var( 'yumeho_column_slug' );
    if ( ! $slug ) return;

    // ローカル記事に存在すれば通常表示
    if ( get_page_by_path( $slug, OBJECT, 'yumeho_column' ) ) return;

    // なければコーポレートに 301 リダイレクト
    $corporate_url = yumeho_related_site_url( 'corporate', '/column/' . $slug . '/' );
    if ( $corporate_url && '#' !== $corporate_url ) {
        wp_redirect( $corporate_url, 301 );
        exit;
    }
} );
```

**メリット**
- YUMEHO 独自の SEO 価値を 100% 確保
- 完全に独立したコンテンツ戦略

**デメリット**
- 全記事を YUMEHO 側で管理する必要
- 過去の連動記事の引き取り作業

---

## レベル別 比較表

| 観点 | レベル1(現状) | レベル2(併用) | レベル3(独立) |
|------|------------|-------------|------------|
| **コード変更** | 不要 | 1〜2日の作業 | 半日〜1日の作業 |
| **編集者の負担** | コーポのみ | 両サイト | YUMEHO のみ |
| **連動記事** | あり | あり | なし(廃止 or 引き取り) |
| **YUMEHO 独自記事** | なし | あり | あり |
| **YUMEHO 側 SEO 評価** | △(canonical 集約) | ○(独自記事のみ) | ◎ |
| **コーポ側 SEO 評価** | ◎ | ◎ | ○(連動停止で被リンク減) |
| **重複コンテンツリスク** | なし(canonical) | なし | なし |
| **運用負荷** | 低 | 中 | 高 |

---

## 切り替えタイミングの判断基準

| 状況 | 推奨レベル |
|------|----------|
| 試験運用中、効果不明 | レベル1(現状維持) |
| YUMEHO 訪問者がコラムをよく見ている | レベル2 を検討 |
| YUMEHO 製品特化のコンテンツを増やしたい | レベル2 |
| YUMEHO 単体で月10件以上更新できる体制 | レベル3 |
| YUMEHO のオーガニック流入を伸ばしたい | レベル3 |
| コーポレートサイトのコラム運用が止まる | レベル3 |

---

## 推奨ロードマップ

```
[Phase 1: 現在] レベル1
  └─ 連動運用 + YUMEHO 独自リード文
  └─ 半年〜1年運用
       ↓ アクセス解析で効果測定
       ↓ YUMEHO のコラムページ流入を確認

[Phase 2: 必要時] レベル2
  └─ YUMEHO 独自記事の追加
  └─ 連動記事と併用
  └─ さらに半年〜1年運用
       ↓ 独自記事の SEO 効果を確認

[Phase 3: 体制が整ったら] レベル3
  └─ 完全独立化
  └─ コーポレートと棲み分け
```

---

## アクセス解析でチェックする指標

### レベル2 への移行を検討すべきサイン

- YUMEHO `/column/` の月間 PV が 100 以上
- YUMEHO コラムから製品ページへの遷移率が 5% 以上
- コラム経由の YUMEHO お問い合わせが月1件以上

### レベル3 への移行を検討すべきサイン

- YUMEHO 独自記事の検索流入が連動記事を上回る
- 「YUMEHO + 製品関連キーワード」の検索順位が上昇
- コーポレートサイトのコラム運用が停滞

---

## トラブルシューティング

### Q. 切り替え後、過去の SEO 評価は引き継がれる?

A. レベル2 では canonical の有無で評価先が変わるため、独自記事は新規評価のスタートになります。レベル3 で 301 リダイレクトを実装すれば、過去の被リンクの一部は YUMEHO に流れます。

### Q. 連動を一時停止したい場合は?

A. `yumeho_fetch_shared_columns()` を return array() にするだけで即停止できます。再開は元に戻すだけ。

### Q. レベル2 で運用している時、編集者はどう判断する?

A. 「業界トレンド・経営論」はコーポレート、「YUMEHO 製品の活用方法」は YUMEHO 独自、という棲み分けが分かりやすい。

---

## 実装ファイル一覧(参考)

### Rinascente 側

- `theme-rinascente/functions.php` — column CPT + メタフィールド + REST API公開
- `theme-rinascente/archive-column.php` — 一覧テンプレート
- `theme-rinascente/single-column.php` — 詳細テンプレート
- `theme-rinascente/taxonomy-column_category.php` — カテゴリーアーカイブ

### YUMEHO 側

- `theme-yumeho/functions.php` — REST 取得関数 + リライトルール
- `theme-yumeho/yumeho-column-archive.php` — 一覧テンプレート
- `theme-yumeho/yumeho-column-single.php` — 詳細テンプレート(canonical 付き)
- `theme-yumeho/front-page.php` — トップページのコラムセクション
