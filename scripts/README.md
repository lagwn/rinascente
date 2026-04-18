# Deploy Scripts

このディレクトリには、真誠会プロジェクトの `staging / production` 反映用スクリプトを置いています。

## 1. 先にやること

1. `config/staging.env.example` を `config/staging.env` にコピー
2. `config/production.env.example` を `config/production.env` にコピー
3. Xserver の SSH 情報、URL、remote path を埋める

`deploy-theme.sh` などを初回実行したとき、`config/*.env` が無ければ example から自動生成します。

## 2. 主なコマンド

```bash
./scripts/deploy-theme.sh yumeho staging
./scripts/deploy-theme.sh rinascente production

./scripts/deploy-uploads.sh yumeho staging
./scripts/deploy-uploads.sh rinascente production --delete

./scripts/deploy-manuals.sh staging
./scripts/deploy-manuals.sh production

./scripts/smoke-test.sh all staging
./scripts/smoke-test.sh all production
```

## 3. ターミナルを使いたくない場合

- `scripts/deploy-staging.command`
- `scripts/deploy-production.command`

をダブルクリックすると、選択式で deploy を実行できます。

## 4. 運用ルール

- ローカルの修正元は `wp-themes/`
- 本番サーバー上でテーマを直接編集しない
- まず staging に反映し、確認後に production へ上げる
- `deploy-theme.sh` は `assets/movie` を `--delete` の対象外にしているため、別端末から deploy しても既存の本番動画を消しにくい
- uploads は必要なときだけ同期する
- マニュアルは `deploy-manuals.sh` を使い、画像・PDF の公開権限を崩さない
