# Rinascente コーポレートサイト SEO メタ要素定義書

WordPress テンプレート用 — 2026-04-09 策定

> **注意: 会社情報の動的管理**
> 会社名・電話番号・住所などはダミー値のため変更予定あり。
> WP テーマでは全てカスタマイザー（`get_theme_mod()`）経由で取得するため、
> 管理画面で値を変更すれば以下の箇所に自動反映される:
> - 構造化データ（Organization schema の name / telephone / address）
> - ヘッダー・フッターの会社情報
> - お問い合わせフォームの自動返信メール署名
> - トップページの会社名表示
> 
> **Rank Math の title / description でも `%company_name%` `%company_tel%` 等のカスタム変数が使える。**
> テーマの functions.php で `rank_math_register_var_replacement` を登録済み。
> Rank Math の設定でこれらの変数を使えば、カスタマイザー変更で title / description にも自動反映される。
>
> 使用可能なカスタム変数:
> - `%company_name%` — 会社名
> - `%company_tel%` — 電話番号
> - `%company_address%` — 所在地
> - `%company_hours%` — 受付時間
> - `%sitename%` — サイト名（WordPress標準変数）

---

## ペルソナ定義

### ペルソナ A: 医療・介護施設の経営層
- **属性**: 50代、病院理事長・施設長・法人本部
- **課題**: 施設のリハビリ体制強化、設備投資判断、人材不足対策
- **検索行動**: 「リナシェンテ」「医療機器 メーカー」「歩行リハビリ 企業」
- **求める情報**: 企業の信頼性、導入実績、製品ラインナップの全体像

### ペルソナ B: 事業提携・パートナー候補
- **属性**: 40代、医療機器ディーラー・代理店・商社
- **課題**: 取り扱い製品の拡充、新規メーカーとの提携
- **検索行動**: 「Rinascente 会社概要」「YUMEHO メーカー」「医療機器 新製品」
- **求める情報**: 企業ビジョン、製品競争力、成長性

### ペルソナ C: メディア・業界関係者
- **属性**: 30-40代、医療専門メディア記者、業界アナリスト
- **検索行動**: 「リナシェンテ プレスリリース」「YUMEHO 新製品」「介護ロボット 認証」
- **求める情報**: 最新ニュース、認証情報、導入実績の数値

---

## サイト共通設定

```
サイト名: Rinascente（リナシェンテ）
区切り文字: |
OGP type: website（トップ）/ article（下層）
OGP image: /assets/img/ogp-rinascente.jpg（1200×630px推奨）
locale: ja_JP
twitter:card: summary_large_image
```

---

## 全ページ メタ要素一覧

### index.html（トップページ）

```html
<title>株式会社Rinascente（リナシェンテ）| 医療・福祉から、その先へ。</title>
<meta name="description" content="Rinascente（リナシェンテ）は、歩行リハビリ支援システムYUMEHO・造影剤注入装置MICA30を展開する医療福祉機器メーカーです。「再生」の理念のもと、病院・介護施設の課題解決と、ヘルスケア領域の拡張に取り組んでいます。">
<meta name="keywords" content="Rinascente,リナシェンテ,医療機器,福祉機器,YUMEHO,MICA30,歩行リハビリ,造影剤注入装置,Healthcare">
<link rel="canonical" href="https://example.com/">
<meta property="og:title" content="Rinascente | 医療・福祉から、その先へ。">
<meta property="og:description" content="YUMEHO・MICA30を展開する医療福祉機器メーカー。再生の理念で現場課題を解決し続けます。">
<meta property="og:type" content="website">
<meta property="og:url" content="https://example.com/">
<meta property="og:image" content="https://example.com/assets/img/ogp-rinascente.jpg">
<meta property="og:site_name" content="Rinascente（リナシェンテ）">
<meta property="og:locale" content="ja_JP">
<meta name="twitter:card" content="summary_large_image">
```

### identity.html（企業理念・ブランド）

```html
<title>Corporate Identity | Rinascente 企業理念・ブランドビジョン</title>
<meta name="description" content="Rinascente（リナシェンテ）の企業理念とブランドアイデンティティ。イタリア語で「再生する者」を意味する社名に込めた思い。Rebirth・Integrity・Empathy・Expansion・Eleganceの5つの価値観。Vision 2030「人が、何度でも立ち上がれる世界へ。」">
<meta name="keywords" content="Rinascente,企業理念,ブランド,ビジョン,Vision 2030,再生,リナシェンテ,医療福祉,Corporate Identity">
<link rel="canonical" href="https://example.com/identity.html">
<meta property="og:title" content="Corporate Identity | Rinascente 企業理念">
<meta property="og:description" content="再生する者 — Rinascenteの5つの価値観とVision 2030「人が、何度でも立ち上がれる世界へ。」">
<meta property="og:type" content="article">
```

### cases.html（導入事例）

```html
<title>導入事例 | Rinascente YUMEHO・MICA30の成果実績</title>
<meta name="description" content="YUMEHO・MICA30の導入事例。20施設以上の採用実績。歩行訓練機会1.5倍増、スタッフ負担40％削減、利用者参加率31％向上。病院回復期病棟、介護老健施設、デイサービスでの実際の成果をご紹介。">
<meta name="keywords" content="Rinascente,導入事例,YUMEHO,MICA30,病院,介護施設,訓練機会,スタッフ削減,成果実績">
<link rel="canonical" href="https://example.com/cases.html">
<meta property="og:title" content="導入事例 | Rinascente YUMEHO・MICA30">
<meta property="og:description" content="20施設以上の採用実績。訓練機会1.5倍、スタッフ負担40％削減の成果を数値で紹介。">
<meta property="og:type" content="article">
```

### press.html（プレスリリース・ニュース）

```html
<title>Press（プレスリリース・ニュース）| Rinascente</title>
<meta name="description" content="Rinascente（リナシェンテ）グループの最新ニュース。YUMEHO・MICA30の製品情報、認証取得、導入事例、事業展開に関するプレスリリースをお届けします。">
<meta name="keywords" content="Rinascente,プレスリリース,ニュース,YUMEHO,MICA30,新製品,認証,医療機器">
<link rel="canonical" href="https://example.com/press.html">
<meta property="og:title" content="Press | Rinascente ニュース・プレスリリース">
<meta property="og:description" content="YUMEHO・MICA30の製品情報、認証取得、事業展開の最新ニュースをお届けします。">
<meta property="og:type" content="article">
```

### contact.html（お問い合わせ）

```html
<title>お問い合わせ | Rinascente 製品相談・事業提携・採用</title>
<meta name="description" content="Rinascente（リナシェンテ）へのお問い合わせ。YUMEHO・MICA30の製品相談、デモ依頼、事業提携のご提案、採用に関するお問い合わせを受け付けています。TEL: 0859-00-1234（平日 9:00-17:00）">
<meta name="keywords" content="Rinascente,お問い合わせ,問い合わせ,製品相談,デモ,事業提携,採用,連絡先">
<link rel="canonical" href="https://example.com/contact.html">
<meta property="og:title" content="お問い合わせ | Rinascente">
<meta property="og:description" content="製品相談・デモ依頼・事業提携のご提案を受付中。TEL: 0859-00-1234（平日 9:00-17:00）">
<meta property="og:type" content="article">
```

### news-20260315.html（新型スタンド型 販売開始）

```html
<title>YUMEHO 新型スタンド型（PGT-9001）販売開始 | Press | Rinascente</title>
<meta name="description" content="YUMEHOの新型スタンド型PGT-9001の販売を開始。従来比30％の省スペース設計で最小設置幅1.4mを実現。天井工事不要で小規模施設・クリニックにも対応。2026年3月15日発表。">
<meta name="keywords" content="YUMEHO,PGT-9001,スタンド型,新製品,省スペース,小規模施設,歩行リハビリ">
<link rel="canonical" href="https://example.com/news-20260315.html">
<meta property="og:title" content="YUMEHO 新型スタンド型（PGT-9001）販売開始">
<meta property="og:description" content="従来比30％の省スペース設計。最小設置幅1.4m。天井工事不要で小規模施設に対応。">
<meta property="og:type" content="article">
<meta property="article:published_time" content="2026-03-15">
```

### news-20260228.html（補助金対象機器認定）

```html
<title>YUMEHO 福祉機器普及推進事業 補助金対象機器に認定 | Press | Rinascente</title>
<meta name="description" content="YUMEHOが厚生労働省の福祉機器普及推進事業補助金の対象機器に認定。導入費用の最大2/3が補助される可能性があり、施設の自己負担を大幅軽減。2026年2月28日発表。">
<meta name="keywords" content="YUMEHO,補助金,福祉機器,認定,厚生労働省,介護ロボット,助成金">
<link rel="canonical" href="https://example.com/news-20260228.html">
<meta property="og:title" content="YUMEHO 補助金対象機器に認定">
<meta property="og:description" content="厚労省の福祉機器普及推進事業補助金の対象に。導入費用の最大2/3が補助対象。">
<meta property="og:type" content="article">
<meta property="article:published_time" content="2026-02-28">
```

### news-20260210.html（MICA30 チューブ認証取得）

```html
<title>MICA30 造影用耐圧チューブ 医療機器認証取得 | Press | Rinascente</title>
<meta name="description" content="MICA30用の造影用耐圧チューブ（認証番号：304ADBZX00072000）が医療機器認証を取得。500psi耐圧で安全な造影剤注入をサポート。2026年2月10日発表。">
<meta name="keywords" content="MICA30,造影剤,耐圧チューブ,医療機器認証,血管造影,CT">
<link rel="canonical" href="https://example.com/news-20260210.html">
<meta property="og:title" content="MICA30 造影用耐圧チューブ 認証取得">
<meta property="og:description" content="500psi耐圧。安全な造影剤注入をサポートする専用チューブが医療機器認証を取得。">
<meta property="og:type" content="article">
<meta property="article:published_time" content="2026-02-10">
```

### news-20260120.html（コーポレートブランド発表）

```html
<title>コーポレートブランド「Rinascente」発表 グループビジョン刷新 | Press | Rinascente</title>
<meta name="description" content="株式会社リナシェンテがコーポレートブランド「Rinascente」を正式発表。医療・福祉領域から旅行・ウェルネス・教育へ事業領域を拡張するVision 2030を発表。2026年1月20日。">
<meta name="keywords" content="Rinascente,リナシェンテ,コーポレートブランド,Vision 2030,事業拡張,ヘルスケア">
<link rel="canonical" href="https://example.com/news-20260120.html">
<meta property="og:title" content="コーポレートブランド「Rinascente」発表">
<meta property="og:description" content="医療・福祉から旅行・ウェルネスへ。リナシェンテがVision 2030を発表。">
<meta property="og:type" content="article">
<meta property="article:published_time" content="2026-01-20">
```

---

## 構造化データ（トップページ用）

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "株式会社Rinascente",
  "alternateName": ["リナシェンテ", "Rinascente Inc."],
  "url": "https://example.com/",
  "logo": "https://example.com/assets/img/logo-rinascente.png",
  "description": "医療・福祉機器の企画・販売。YUMEHO歩行リハビリ支援システム、MICA30造影剤注入装置を展開。",
  "telephone": "0859-00-1234",
  "foundingDate": "2026",
  "sameAs": [],
  "brand": [
    {
      "@type": "Brand",
      "name": "YUMEHO",
      "description": "歩行リハビリ支援システム"
    },
    {
      "@type": "Brand",
      "name": "MICA30",
      "description": "造影剤注入装置"
    }
  ],
  "knowsAbout": [
    "歩行リハビリテーション",
    "医療機器",
    "福祉機器",
    "造影剤注入装置",
    "介護ロボット"
  ]
}
```

---

## WordPress テンプレート実装メモ

- `<title>` は `wp_title()` + `bloginfo('name')` で生成
- `meta description` は カスタムフィールド `seo_description` で管理
- `meta keywords` は カスタムフィールド `seo_keywords` で管理
- OGP は `og:image` をアイキャッチ画像にフォールバック
- canonical URL は `wp_head()` 内で自動出力
- ニュース記事は `article:published_time` を投稿日から自動出力
- 構造化データは JSON-LD で `wp_footer()` に出力
- SEO プラグイン（Yoast SEO / RankMath）と連携推奨
