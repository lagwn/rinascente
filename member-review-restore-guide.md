# 施設レビュー機能 復活手順

## 現在の状態

- 2026-04-10 時点で、Rinascente の施設レビュー機能は停止中です。
- WordPress テーマでは `RINASCENTE_ENABLE_MEMBER_REVIEWS` が `false` のため、会員ページ・管理画面の両方で非表示です。
- 既存の `member_review` 投稿データは削除していないため、復活時に再利用できます。
- プロトタイプの [member.html](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/rinascentes/member.html) では、レビュー用のナビ・セクション・JS をコメントアウトしています。

## 復活手順

1. [functions.php](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-rinascente/functions.php) の `RINASCENTE_ENABLE_MEMBER_REVIEWS` を `true` に変更する。

```php
if ( ! defined( 'RINASCENTE_ENABLE_MEMBER_REVIEWS' ) ) {
    define( 'RINASCENTE_ENABLE_MEMBER_REVIEWS', true );
}
```

2. [member.html](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/rinascentes/member.html) で、レビュー用に付けているコメントアウトを外す。
   対象は次の3箇所です。
   - サイドバーの `製品レビュー` ナビ
   - `id="reviews"` のセクション全体
   - `Review tabs` から `Helpful buttons` までの JS ブロック

3. 開発用テーマを Local へ同期する。

```bash
rsync -av --delete wp-themes/theme-rinascente/ wp/rinascente/app/public/wp-content/themes/theme-rinascente/ --exclude='assets/'
```

4. Rinascente のアセットを同期する。

```bash
rsync -av --delete rinascentes/assets/ wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/
```

5. キャッシュをクリアする。

```bash
wp cache flush --path=wp/rinascente/app/public
```

## 復活後に確認する項目

- `WP管理画面 > 施設レビュー` が表示されること
- [member/](/Users/naoya/Desktop/クライアント1119/真誠会/プロトタイプ/wp-themes/theme-rinascente/page-member.php) にレビューセクションが再表示されること
- 既存のレビュー投稿が表示されること
- 新規レビュー投稿で `pending` 投稿が作成されること

## 補足

- レビュー機能の停止は「非表示化」です。DB 上の既存レビューを消していないため、必要ならすぐ戻せます。
- もし復活時に UI を以前の状態へ完全に戻したい場合は、同日のコミット差分をあわせて確認すると安全です。
