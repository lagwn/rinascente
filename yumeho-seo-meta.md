# YUMEHO サイト SEO メタ要素定義書

WordPress テンプレート用 — 2026-04-09 策定

> **注意: 会社情報の動的管理**
> 会社名・電話番号・住所などはダミー値のため変更予定あり。
> WP テーマでは全てカスタマイザー（`get_theme_mod()`）経由で取得するため、
> 管理画面で値を変更すれば以下の箇所に自動反映される:
> - Rank Math の構造化データ（Organization schema の name / telephone / address）
> - フッターの会社情報
> - お問い合わせフォームの自動返信メール署名
> - front-page.php の会社名表示
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

### ペルソナ A: 施設長・事務長（意思決定者）
- **属性**: 50代男性、病院またはリハビリ施設の管理職
- **課題**: 転倒事故リスク、慢性的なスタッフ不足、稟議の壁
- **検索行動**: 「歩行リハビリ 設備」「介護施設 転倒防止」「リハビリ機器 補助金」
- **決定要因**: ROI（投資対効果）、安全性エビデンス、補助金活用可否

### ペルソナ B: リハビリ専門職（PT/OT）
- **属性**: 30代、理学療法士/作業療法士
- **課題**: マンパワー不足で訓練回数が確保できない、腰痛
- **検索行動**: 「歩行訓練 免荷装置」「ハーネス式 歩行支援」「歩行リハ 機器比較」
- **決定要因**: 臨床的有用性、患者の安全性、操作性

### ペルソナ C: 購買・設備担当
- **属性**: 40代、施設の設備投資・調達担当
- **検索行動**: 「歩行支援装置 価格」「リハビリ機器 リース」「介護ロボット 見積」
- **決定要因**: 初期費用、ランニングコスト、設置要件、納期

---

## サイト共通設定

```
サイト名: YUMEHO（ウォークメイト）
区切り文字: |
OGP type: website（トップ）/ article（下層）
OGP image: /assets/img/ogp-yumeho.jpg（1200×630px推奨）
locale: ja_JP
twitter:card: summary_large_image
```

---

## 全ページ メタ要素一覧

### index.html（トップページ）

```html
<title>YUMEHO（ウォークメイト）| 転倒の不安から解放する歩行リハビリ支援システム</title>
<meta name="description" content="YUMEHOは転倒リスクを物理的に防ぎ、両手フリーで多様なリハビリ課題を実現する歩行支援システムです。天井型・スタンド型から選択可能。見守り1名で運用でき、スタッフ負担を大幅軽減します。病院・介護施設向け。">
<meta name="keywords" content="YUMEHO,ウォークメイト,歩行リハビリ,歩行支援システム,転倒防止,免荷装置,リハビリ機器,病院,介護施設,デイサービス">
<link rel="canonical" href="https://example.com/yumeho/">
<meta property="og:title" content="YUMEHO | 転倒の不安から解放する歩行リハビリ支援システム">
<meta property="og:description" content="転倒リスクを物理的に防ぎ、両手フリーで多様なリハビリ課題を実現。天井型・スタンド型対応。見守り1名運用。">
<meta property="og:type" content="website">
<meta property="og:url" content="https://example.com/yumeho/">
<meta property="og:image" content="https://example.com/yumeho/assets/img/ogp-yumeho.jpg">
<meta property="og:site_name" content="YUMEHO（ウォークメイト）">
<meta property="og:locale" content="ja_JP">
<meta name="twitter:card" content="summary_large_image">
```

### product.html（製品紹介）

```html
<title>製品紹介 | YUMEHO（ウォークメイト）歩行リハビリ支援システム</title>
<meta name="description" content="YUMEHOの製品仕様。天井直付型FCW-3000と、天井工事不要のスタンド型PGT-9000/9001。特許取得G-Suitハーネスとデュアルレールで安全な免荷歩行訓練を実現。オプション製品・システム構成も掲載。">
<meta name="keywords" content="YUMEHO,製品紹介,FCW-3000,PGT-9000,PGT-9001,天井型,スタンド型,G-Suit,ハーネス,デュアルレール,免荷装置">
<link rel="canonical" href="https://example.com/yumeho/product.html">
<meta property="og:title" content="製品紹介 | YUMEHO 歩行リハビリ支援システム">
<meta property="og:description" content="天井直付型・スタンド型の2モデル。特許G-Suitハーネスとデュアルレールで安全な免荷歩行訓練。">
<meta property="og:type" content="article">
```

### simulation.html（導入シミュレーション）

```html
<title>導入シミュレーション | YUMEHO 最適構成を自動診断</title>
<meta name="description" content="施設種別・設置方式・オプションを選ぶだけで、YUMEHOの最適システム構成と概算費用を自動診断。天井型・スタンド型、レール長、オプション製品を含む見積シミュレーションを即時確認できます。">
<meta name="keywords" content="YUMEHO,導入シミュレーション,見積,概算費用,システム構成,設置方式,天井型,スタンド型,リハビリ機器">
<link rel="canonical" href="https://example.com/yumeho/simulation.html">
<meta property="og:title" content="導入シミュレーション | YUMEHO 最適構成を自動診断">
<meta property="og:description" content="3ステップで最適なYUMEHO構成と概算費用を自動診断。施設に合わせたカスタム提案。">
<meta property="og:type" content="article">
```

### cases.html（導入事例）

```html
<title>導入事例 | YUMEHO 病院・介護施設での成果実績</title>
<meta name="description" content="YUMEHOの導入事例。回復期リハビリ病院で訓練機会1.5倍、スタッフ3名→1名体制を実現。整形外科クリニック、デイサービスでの転倒事故ゼロ実績。導入前後の数値比較を掲載。">
<meta name="keywords" content="YUMEHO,導入事例,病院,介護施設,デイサービス,リハビリ,転倒防止,スタッフ削減,訓練機会,成果">
<link rel="canonical" href="https://example.com/yumeho/cases.html">
<meta property="og:title" content="導入事例 | YUMEHO 病院・介護施設での成果実績">
<meta property="og:description" content="訓練機会1.5倍、介助3名→見守り1名。転倒事故ゼロ。数値で見るYUMEHO導入効果。">
<meta property="og:type" content="article">
```

### flow.html（導入の流れ）

```html
<title>導入の流れ | YUMEHO お問い合わせから稼働まで最短2週間</title>
<meta name="description" content="YUMEHOの導入フロー。お問い合わせ→ヒアリング→現地調査→レイアウト提案→設置工事→操作研修→稼働開始。最短2週間で対応。専任スタッフが一貫サポートいたします。">
<meta name="keywords" content="YUMEHO,導入フロー,導入の流れ,設置工事,現地調査,操作研修,納期,サポート">
<link rel="canonical" href="https://example.com/yumeho/flow.html">
<meta property="og:title" content="導入の流れ | YUMEHO 最短2週間で稼働開始">
<meta property="og:description" content="お問い合わせから稼働まで最短2週間。専任スタッフによる一貫サポート体制。">
<meta property="og:type" content="article">
```

### price.html（価格・見積）

```html
<title>価格・見積 | YUMEHO 購入・リース対応・補助金活用</title>
<meta name="description" content="YUMEHOの価格・見積情報。施設に応じたカスタム構成のため個別見積制。購入・リース両対応。補助金・助成金活用で自己負担を軽減。導入効果シミュレーションで投資対効果もご確認いただけます。">
<meta name="keywords" content="YUMEHO,価格,見積,リース,購入,補助金,助成金,費用,導入効果,ROI,リハビリ機器">
<link rel="canonical" href="https://example.com/yumeho/price.html">
<meta property="og:title" content="価格・見積 | YUMEHO 購入・リース・補助金対応">
<meta property="og:description" content="個別見積制。購入・リース両対応。補助金活用で自己負担軽減。導入効果シミュレーション付き。">
<meta property="og:type" content="article">
```

### subsidy.html（補助金ガイド）

```html
<title>補助金・助成金 活用ガイド | YUMEHO 介護ロボット導入支援事業対応</title>
<meta name="description" content="YUMEHOの導入に活用できる補助金・助成金制度ガイド。介護ロボット導入支援事業、ものづくり補助金等に対応。申請サポート実績あり。主な制度の概要・補助率・申請のポイントを解説します。">
<meta name="keywords" content="YUMEHO,補助金,助成金,介護ロボット導入支援,ものづくり補助金,IT導入補助金,申請サポート,福祉機器">
<link rel="canonical" href="https://example.com/yumeho/subsidy.html">
<meta property="og:title" content="補助金・助成金 活用ガイド | YUMEHO">
<meta property="og:description" content="介護ロボット導入支援事業等に対応。補助率・申請のポイントを解説。申請サポート実績あり。">
<meta property="og:type" content="article">
```

### faq.html（よくある質問）

```html
<title>よくある質問（FAQ）| YUMEHO 設置・費用・運用・デモ</title>
<meta name="description" content="YUMEHOに関するよくある質問。設置工事の期間は？天井工事不要のモデルは？費用の目安は？補助金は使える？デモ体験できる？など、導入検討に必要な情報をQ&A形式で網羅しています。">
<meta name="keywords" content="YUMEHO,FAQ,よくある質問,設置,費用,補助金,デモ,保守,メンテナンス,歩行リハビリ">
<link rel="canonical" href="https://example.com/yumeho/faq.html">
<meta property="og:title" content="よくある質問 | YUMEHO 歩行リハビリ支援システム">
<meta property="og:description" content="設置・費用・補助金・デモ体験など導入検討に必要なQ&Aを網羅。">
<meta property="og:type" content="article">
```

### company.html（会社概要）

```html
<title>会社概要 | 株式会社Rinascente（リナシェンテ）</title>
<meta name="description" content="株式会社Rinascente（リナシェンテ）の会社概要。医療・福祉の現場で「再生」をテーマに、YUMEHO歩行リハビリ支援システム・MICA30造影剤注入装置の企画・販売を行っています。">
<meta name="keywords" content="Rinascente,リナシェンテ,会社概要,医療機器,福祉機器,YUMEHO,MICA30">
<link rel="canonical" href="https://example.com/yumeho/company.html">
<meta property="og:title" content="会社概要 | 株式会社Rinascente">
<meta property="og:description" content="医療・福祉機器の企画・販売。YUMEHO・MICA30の開発元。再生をテーマに現場課題を解決。">
<meta property="og:type" content="article">
```

### contact.html（お問い合わせ）

```html
<title>資料請求・お問い合わせ | YUMEHO 歩行リハビリ支援システム</title>
<meta name="description" content="YUMEHOの資料請求・お問い合わせ。製品カタログ、導入事例集、稟議用サマリー、概算見積をご用意しています。2営業日以内に専任スタッフからご連絡いたします。デモ体験も受付中。">
<meta name="keywords" content="YUMEHO,資料請求,お問い合わせ,カタログ,見積,デモ体験,導入相談">
<link rel="canonical" href="https://example.com/yumeho/contact.html">
<meta property="og:title" content="資料請求・お問い合わせ | YUMEHO">
<meta property="og:description" content="カタログ・導入事例集・概算見積をご用意。2営業日以内にご連絡。デモ体験も受付中。">
<meta property="og:type" content="article">
```

---

## 構造化データ（トップページ用）

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "YUMEHO（ウォークメイト）",
  "description": "転倒リスクを物理的に防ぎ、両手フリーで多様なリハビリ課題を実現する歩行リハビリ支援システム",
  "brand": {
    "@type": "Brand",
    "name": "YUMEHO"
  },
  "manufacturer": {
    "@type": "Organization",
    "name": "株式会社Rinascente",
    "url": "https://example.com/"
  },
  "category": "医療機器・福祉機器",
  "audience": {
    "@type": "Audience",
    "audienceType": "病院・介護施設・デイサービス"
  }
}
```

---

## WordPress テンプレート実装メモ

- `<title>` は `wp_title()` + `bloginfo('name')` で生成
- `meta description` は カスタムフィールド `seo_description` で管理
- `meta keywords` は カスタムフィールド `seo_keywords` で管理
- OGP は `og:image` をアイキャッチ画像にフォールバック
- canonical URL は `wp_head()` 内で自動出力（Yoast/RankMath 連携推奨）
- 構造化データは JSON-LD で `wp_footer()` に出力
