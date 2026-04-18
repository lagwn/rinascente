# 本番移行計画 — 真誠会 Rinascente / YUMEHO

**更新日**: 2026-04-12  
**公開予定日**: 2026-05-10

## 1. 結論

今回の案件は、**Xserver 1契約 + 4環境構成**で進めるのが最も安全です。

- **本番**
  - `https://onerinascente.co.jp`（Rinascente）
  - `https://rinascenteyumeho.jp`（YUMEHO）
- **dev / client check**
  - `https://corp-dev.rinascenteyumeho.jp`（Rinascente）
  - `https://dev.rinascenteyumeho.jp`（YUMEHO）

一番重要なのは、**本番ドメインを dev 環境に使わないこと**です。  
既存メモでは `rinascenteyumeho.jp` を dev 兼用にしていましたが、これは以下の理由で避けた方が安全です。

- 途中段階の内容がインデックスされるリスクがある
- クライアント確認中の URL がそのまま本番になる
- `search-replace` と canonical の切替が複雑になる
- 公開直前に「noindex 解除」「Basic 認証解除」「GA/GSC 本接続」が重なりやすい

したがって、**dev はサブドメイン**、**prod は正式ドメイン**に分離します。

## 2. 2026年5月10日に間に合うか

### 結論

**間に合う可能性は高いです。**  
ただし、懸念点はサーバー構築ではなく **`onerinascente.co.jp` の取得タイミング**です。

JPRS の公開情報では、

- `co.jp` は **日本国内に登記されている企業のみ**登録可能
- さらに **仮登録制度**があり、**6カ月以内に登記予定なら登記前でも申請可能**

と案内されています。

このため、ベストは次のどちらかです。

1. **2026年4月中に仮登録できるかを registrar に確認し、可能なら先に押さえる**
2. それが難しければ、**2026年5月1日に登記簿取得後すぐ申請**し、サーバー側はそれ以前に全部準備しておく

つまり、`onerinascente.co.jp` のリスクは「サイト構築が間に合うか」ではなく、**ドメインの確保・反映がどれだけ早く終わるか**です。

## 3. Xserver で確認できた仕様

2026年4月12日時点で、Xserver の公式ページ・マニュアルで確認できた内容は次の通りです。

- スタンダードプランは通常 **月額 990円〜**、キャンペーン表示では **月額 693円〜**
- **10日間無料お試し**
- **500GB / NVMe** ストレージ
- **マルチドメイン無制限**
- **MySQL/MariaDB 無制限**
- **無料独自SSL**
- **SSH** 利用可
- **Cron** 利用可、`Cron結果の通知アドレス` も設定可能
- **PHP 8.5.x / 8.4.x / 8.3.x / 8.2.x / 8.1.x / 8.0.x** が選択可能
- **FastCGI + OPcache/APC** が標準有効
- **WordPress簡単移行** あり
- **hosts ファイル編集による切替前確認**が可能
- **自動バックアップ 14日分**（Web / メール / MySQL）
- **WAF設定**
- **WordPressセキュリティ設定**
- **Web改ざん検知設定はビジネスプラン表記**

### この案件での判断

- **契約プランは Xserver スタンダードで十分**
- ただし **Xserver の Web 改ざん検知を公式機能として使いたいならビジネスプランが必要**
- 今回は既存テーマ内に Slack 通知実装がすでにあるため、**スタンダード + Wordfence + 既存 Slack 通知**で十分現実的

## 4. 推奨構成

### 4.1 環境構成

| 用途 | Rinascente | YUMEHO |
|---|---|---|
| dev / client check | `corp-dev.rinascenteyumeho.jp` | `dev.rinascenteyumeho.jp` |
| production | `onerinascente.co.jp` | `rinascenteyumeho.jp` |

### 4.2 WordPress / DB 構成

**4つの WordPress を分離**します。

- `rinascente_dev`
- `rinascente_prod`
- `yumeho_dev`
- `yumeho_prod`

DB もユーザーも分けます。

- `wp_rinascente_dev`
- `wp_rinascente_prod`
- `wp_yumeho_dev`
- `wp_yumeho_prod`

### 4.3 dev 環境のルール

dev は必ず以下を有効化します。

- `noindex`
- Basic 認証またはアクセス制限
- GA4 / GSC の本番接続はしない
- クライアント確認用としてのみ利用

## 5. 今回の案件で既に入っている設定

現状コードを見ると、移行時に活かせるものがすでにあります。

### 5.1 GA4 / GTM / Slack Webhook の受け皿

- Rinascente: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-rinascente/functions.php:3245)
- YUMEHO: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-yumeho/functions.php:4288)

どちらも Customizer で次を設定できる状態です。

- GA4 測定ID
- GTM コンテナID
- Slack Webhook URL

### 5.2 セキュリティ通知の Slack 送信

- Rinascente: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-rinascente/functions.php:3394)
- YUMEHO: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-yumeho/functions.php:4432)

すでに次のイベントを Slack webhook へ送る実装があります。

- ログイン失敗
- 管理者ログイン
- Wordfence 通知メール
- プラグイン / テーマ更新完了
- ユーザー登録
- 権限変更

つまり、**無料で脅威検知 → Slack 通知**の土台はもうあります。

### 5.3 クロスサイト URL 設定

- YUMEHO → corporate URL: [platform-support.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-yumeho/inc/platform-support.php:21)
- Rinascente → YUMEHO URL: [platform-support.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইプ/wp-themes/theme-rinascente/inc/platform-support.php:21)

本番切替時は、この URL を **dev 用から prod 用へ切り替える**必要があります。

### 5.4 SMTP はまだ未設定

- Rinascente: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-rinascente/functions.php:3578)
- YUMEHO: [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-yumeho/functions.php:4649)

SMTP は **プレースホルダだけで未有効**です。  
本番前に必ず設定が必要です。

## 6. ベストプラクティスな進め方

## 6.1 すぐやること

### 2026-04-12 〜 2026-04-15

1. **Xserver を契約**
2. **`rinascenteyumeho.jp` を取得**
3. Xserver 上に以下を作る
   - `dev.rinascenteyumeho.jp`
   - `corp-dev.rinascenteyumeho.jp`
4. **Git 管理を開始**
5. ローカルからの移行方式を `DB + uploads + theme code` に統一

### Git 管理は必須

現在この作業ディレクトリは Git 管理されていません。  
`local appで修正 → dev同期 → client check → 本番アップ` を自動化したいなら、**まず Git を source of truth にする**のが前提です。

## 6.2 初回の Local → dev 移行

### 結論

**Xserver の WordPress簡単移行を主経路にはしない**方が安全です。

理由:

- 公式マニュアルでは「他社サーバーで運用中の WordPress をログイン情報で移行」と案内されている
- Local by Flywheel は通常、外部から直接アクセスできない
- テーマ・uploads・DB を分けて扱う方が再現性が高い

### 推奨手順

1. **コード**
   - この repo を Git 化
   - `wp-themes/` をデプロイ対象にする

2. **DB**
   - Local から SQL export
   - dev 側へ import
   - `search-replace` で URL 置換

3. **uploads**
   - `wp-content/uploads/` を rsync / SFTP で同期

4. **固定設定**
   - Slack webhook
   - GA4 / GTM
   - 関連サイト URL
   - noindex
   - Basic 認証

## 6.3 client check の運用

### client check 中の master は dev

2026-05-10 の公開までは、

- **コードの master** = Git
- **確認用コンテンツの master** = dev

と決めます。

やってはいけないこと:

- dev と prod の両方で同時に手作業更新する
- client check 中に本番でも別の内容を作り始める

## 6.4 本番切替

### YUMEHO

`rinascenteyumeho.jp` は本番ドメインなので、  
公開直前に **`dev.rinascenteyumeho.jp` → `rinascenteyumeho.jp`** へクローンする形が安全です。

### Rinascente

1. `corp-dev.rinascenteyumeho.jp` で client check 完了
2. `onerinascente.co.jp` 取得後に Xserver へ追加
3. prod 用 WP を作成
4. dev DB / uploads / theme を prod へクローン
5. `hosts` ファイルで **DNS 切替前に確認**
6. 問題なければ DNS 切替

### 重要

**公開直前の master は dev、公開後の master は prod** に切り替えます。

公開後は以下で運用します。

- **コード変更**: local → dev → client / internal check → prod
- **日常コンテンツ更新**: prod で直接
- **dev 更新が必要なとき**: prod から dev を定期 refresh

## 7. `.co.jp` リスクへの実務対応

### 最優先で確認すべきこと

**Xserverドメイン、または使う registrar が JPRS の仮登録制度に対応しているか** をすぐ確認します。

### 推奨判断

- **仮登録できる**  
  → 2026年4月中に `onerinascente.co.jp` を確保しておく

- **仮登録できない**
  → 2026年5月1日に登記簿取得後すぐ申請

### 実務上の見立て

2026年5月1日に申請しても、2026年5月10日に公開できる余地は十分あります。  
ただし、その場合は **5月1日時点でサーバー・WordPress・テーマ・データ・チェックを全部終えておく** 前提です。

## 8. Wordfence は free でよいか

### 結論

**今回の案件は free で開始してよい**です。  
ただし条件があります。

- Xserver の WAF を有効化
- Xserver の WordPress セキュリティ設定を有効化
- Wordfence Free を有効化
- 既存の Slack webhook 通知を設定
- 強固な管理者パスワード + 2FA
- 自動バックアップと復旧手順を用意

### free の弱み

Wordfence の公式情報では、**free はルール更新やマルウェアシグネチャが 30日遅れ**です。  
また、リアルタイム blocklist や premium support はありません。

### それでも free で始めてよい理由

この案件では、すでにテーマ内に Slack 通知があり、

- ログイン失敗
- 管理者ログイン
- 更新
- 権限変更
- Wordfence メール通知

は拾えます。

また Xserver 側にも

- WAF
- WordPress セキュリティ設定
- 14日バックアップ

があるので、**公開初期のコストと防御のバランスは free で十分**です。

### premium を検討する条件

以下のどれかに当てはまるなら premium を検討します。

- 管理者が多い
- 外部からのログイン試行が増えてきた
- 公開後の攻撃面をより早く塞ぎたい
- リアルタイムルール / リアルタイム IP blocklist が欲しい

## 9. free で足りない場合の無料代替

### 第一候補

**今のテーマ実装をそのまま使う**

すでに Slack webhook 通知の実装があるので、  
「脅威の検知 → Slack 通知」の無料導線としてはこれが一番堅いです。

### 推奨構成

- Xserver WAF
- Xserver WordPress セキュリティ設定
- Wordfence Free
- テーマ内 Slack webhook 通知
- Cron 実行結果通知
- 日次バックアップ確認

### 補足

Xserver の **Web改ざん検知設定はビジネスプラン表記**だったため、  
スタンダードを使うなら、改ざん監視の公式機能には頼らず Wordfence + Slack で見た方が現実的です。

## 10. Wordfence の設定タイミング

### dev 構築時

- Wordfence インストール
- 管理者ユーザー保護
- ログイン試行回数制限
- 2FA
- メール通知先設定
- Slack webhook 設定

### client check 中

- WAF は learning / compatibility を見ながら運用
- 除外ルールが必要ならこの期間に調整

### 公開直前

- scan 実行
- 2FA 最終確認
- 通知テスト

### 公開後

- 定期 scan
- Slack 通知確認
- lockout / blocked IP の観測

## 11. Rank Math の設定手順

### 結論

**Rank Math は使うべき**ですが、  
**schema を全面的に Rank Math に任せない**のが今回の正解です。

理由:

- すでにテーマ側で JSON-LD がかなり入っている
- Rinascente / リナシェンテ、YUMEHO / 夢歩 の別名対応もテーマで調整済み
- Rank Math の自動 schema を重ねると、重複や競合が起きやすい

### この案件での役割分担

#### Rank Math にやらせるもの

- title / meta description
- sitemap
- robots.txt
- noindex 制御
- 404 monitor
- redirections
- Search Console 接続
- SNS / OGP 既定値

#### テーマ側を主に使うもの

- Organization / WebSite / Product / FAQPage / Article / BreadcrumbList などの構造化データ
- ブランド別名
- 内部リンク導線
- LLMO 向けの文脈設計

### 初期設定の順番

1. Setup Wizard
2. Search Console 接続
3. Sitemap 設定
4. Robots 設定
5. Titles & Meta 設定
6. 404 Monitor
7. Redirections
8. Role Manager
9. Social / OGP
10. schema の重複確認

### 最初に必ず設定するもの

#### Rinascente

- Site title に `Rinascente` と `リナシェンテ` の両方を反映
- Home title / description のテンプレート反映
- OGP 既定画像

#### YUMEHO

- Site title に `YUMEHO` と `夢歩` の両方を反映
- Home title / description のテンプレート反映
- OGP 既定画像

### schema 重複の扱い

テーマが強い JSON-LD を出しているページでは、  
**Rank Math 側の schema type を最小限にする**か、**schema 出力を抑える**方が安全です。

特に以下は重複注意です。

- front page
- product
- FAQ
- article / column
- news
- breadcrumbs

## 12. SEO / LLMO 観点で本番前にまだ設定すべきもの

### 必須

1. **GA4 / GTM**
   - Rinascente と YUMEHO の本番 ID
   - dev には本番計測を入れない

2. **Search Console**
   - 4 URL それぞれの扱い整理
   - 本番は正式ドメインで登録

3. **OGP 画像最終確認**
   - `ogp-rinascente.jpg`
   - `ogp-yumeho.jpg`

4. **SMTP**
   - フォーム送信
   - 自動返信
   - 管理者通知

5. **robots / noindex**
   - dev は noindex
   - prod は index

6. **関連サイト URL**
   - dev 同士
   - prod 同士

### できればやる

1. Search Console で `sitemap_index.xml` 送信
2. Rich Results Test
3. 404 / redirection の初期ルール整備
4. Brand query の title / description 最終見直し

## 13. 自動化のベストプラクティス

### 結論

**ツールごとに別運用にしない**のが最重要です。

Codex Desktop でも VS Code + Claude Code でも、  
最終的には **同じ repo の同じ deploy script** を呼ぶ形にします。

### source of truth

- **コード**: Git repo
- **環境差分**: env ファイル
- **deploy**: repo 内 script

### 推奨構成

```text
repo/
├── wp-themes/
├── scripts/
│   ├── deploy-staging.command
│   ├── deploy-production.command
│   ├── deploy-theme.sh
│   ├── deploy-uploads.sh
│   ├── sync-db.sh
│   └── smoke-test.sh
├── config/
│   ├── staging.env
│   └── production.env
└── .github/workflows/
    ├── deploy-staging.yml
    └── deploy-production.yml
```

現時点のローカル雛形として、次は作成済みです。

- [scripts/deploy-theme.sh](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/scripts/deploy-theme.sh)
- [scripts/deploy-uploads.sh](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/scripts/deploy-uploads.sh)
- [scripts/smoke-test.sh](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/scripts/smoke-test.sh)
- [scripts/deploy-staging.command](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/scripts/deploy-staging.command)
- [scripts/deploy-production.command](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/scripts/deploy-production.command)
- [.vscode/tasks.json](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/.vscode/tasks.json)
- [config/staging.env.example](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/config/staging.env.example)
- [config/production.env.example](/Users/naoya/Desktop/クライアント1119/真誠会/プロトটাইフゖ/config/production.env.example)

### ターミナルを極力使わない運用

#### いちばん現実的

- Codex Desktop で script を実行
- VS Code Tasks から同じ script を実行
- 必要なら Mac の `.command` をダブルクリック

#### さらに自動化

- `staging` branch push → staging 自動 deploy
- `main` branch または manual approval → production deploy

### この案件の流れ

1. local で修正
2. Git commit
3. `staging` に deploy
4. クライアント確認
5. 問題なければ同一コミットを `production` に deploy

### 大事なルール

- サーバー上で直接テーマ編集しない
- prod だけ直す、をやらない
- dev / prod の差分は env と DB だけに寄せる

## 14. 今回おすすめする実務フロー

### Phase A: 今すぐ

- Xserver 契約
- `rinascenteyumeho.jp` 取得
- staging サブドメイン作成
- Git 化
- deploy script 雛形作成

### Phase B: 4月中

- Local → staging 移行
- クライアント確認
- 修正反映
- `onerinascente.co.jp` の仮登録可否確認

### Phase C: 5月1日前後

- `onerinascente.co.jp` 登録
- prod Rinascente 構築
- `hosts` で確認

### Phase D: 5月8日〜5月9日

- dev から prod へ最終同期
- Search Console / GA / Rank Math / Wordfence 最終設定
- smoke test

### Phase E: 5月10日

- DNS / 公開
- noindex 解除
- sitemap 送信
- 通知監視

## 15. 最終推奨

今回のベストプラクティスは次の一言にまとまります。

**「Xserver スタンダード + 4環境分離 + Git 管理 + Wordfence Free + 既存 Slack 通知 + Rank Math は meta/sitemap/robots 中心、schema はテーマ主導」**

特に重要なのは次の 4 点です。

1. **prod ドメインを dev に使わない**
2. **`.co.jp` は仮登録可否を先に確認**
3. **Wordfence は free で開始してよいが、Xserver 側設定と Slack 通知を必ず併用**
4. **Codex / Claude / VS Code どれからでも同じ deploy script を叩く設計にする**

## 16. 公式ソース

### Xserver

- [Xserver 公式トップ](https://www.xserver.ne.jp/)
- [機能一覧](https://www.xserver.ne.jp/functions/)
- [料金ページ](https://www.xserver.ne.jp/price/)
- [WordPress簡単移行](https://www.xserver.ne.jp/manual/man_install_transfer_wp.php)
- [自動バックアップ](https://www.xserver.ne.jp/functions/service_backup.php)
- [Cron設定](https://www.xserver.ne.jp/manual/man_program_cron.php)
- [ソフトウェア / PHP 仕様](https://www.xserver.ne.jp/manual/man_program_soft.php)
- [SSH設定](https://www.xserver.ne.jp/manual/man_server_ssh.php)
- [WAF設定](https://www.xserver.ne.jp/manual/man_server_waf.php)
- [WordPressセキュリティ設定](https://www.xserver.ne.jp/manual/man_server_wpsecurity.php)

### JPRS / .co.jp

- [JPRS: ビジネス活用](https://jprs.jp/about/use/business/)
- [JPRS: ドメイン名仮登録](https://jprs.jp/about/dom-rule/advance-add/)

### Wordfence

- [Wordfence Central](https://www.wordfence.com/help/central/settings/)
- [Wordfence API / ライセンス関連](https://www.wordfence.com/help/api-key/)
- [Wordfence Pricing](https://www.wordfence.com/products/pricing/)

### Rank Math

- [Rank Math Setup Wizard](https://rankmath.com/kb/how-to-setup/)
- [Rank Math Sitemaps](https://rankmath.com/kb/configure-sitemaps/)
- [Rank Math robots.txt](https://rankmath.com/kb/how-to-edit-robots-txt-with-rank-math/)
- [Rank Math Local SEO / Knowledge Graph](https://rankmath.com/kb/local-seo/)
- [Rank Math Role Manager](https://rankmath.com/kb/role-manager/)

## 17. 補足

Xserver の共有ホスティング資料を確認した範囲では、**スタンダード向けの 1クリック staging 機能は見当たりませんでした**。  
したがって、今回の提案は **サブドメインで手動 staging を作る前提**です。これは確認した公式資料に基づく推論です。
