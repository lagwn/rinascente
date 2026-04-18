# GitHub Actions 自動デプロイ設定ガイド

このプロジェクトは GitHub Actions で自動デプロイが設定されています。

## 1. SSH 鍵の準備

```bash
# ローカルマシンで SSH 秘密鍵を生成（既存キーがある場合はスキップ）
ssh-keygen -t ed25519 -f ~/.ssh/xserver_deploy -N ""

# 公開鍵をサーバーに登録（Xserver の管理画面など）
cat ~/.ssh/xserver_deploy.pub
```

## 2. GitHub Secrets の設定

GitHub リポジトリ → Settings → Secrets → New repository secret で以下を追加：

### SSH 認証情報
| Secret 名 | 値 | 説明 |
|----------|---|---|
| `XSERVER_SSH_KEY` | (秘密鍵の内容) | Xserver SSH 秘密鍵（`~/.ssh/xserver_deploy` の内容） |
| `XSERVER_HOST` | `rinascente01.xsrv.jp` | Xserver ホスト名 |
| `XSERVER_USER` | `rinascente01` | Xserver SSH ユーザー |
| `XSERVER_PORT` | `10022` | SSH ポート |

### Staging 環境（develop ブランチ自動デプロイ）
| Secret 名 | 値 |
|----------|---|
| `STAGING_YUMEHO_URL` | `https://dev.rinascenteyumeho.jp` |
| `STAGING_RINASCENTE_URL` | `https://corp-dev.rinascenteyumeho.jp` |
| `STAGING_YUMEHO_WP_PATH` | `/home/rinascente01/rinascenteyumeho.jp/public_html/dev.rinascenteyumeho.jp` |
| `STAGING_RINASCENTE_WP_PATH` | `/home/rinascente01/rinascenteyumeho.jp/public_html/corp-dev.rinascenteyumeho.jp` |
| `STAGING_YUMEHO_THEME_DIR` | `/home/rinascente01/rinascenteyumeho.jp/public_html/dev.rinascenteyumeho.jp/wp-content/themes/theme-yumeho` |
| `STAGING_RINASCENTE_THEME_DIR` | `/home/rinascente01/rinascenteyumeho.jp/public_html/corp-dev.rinascenteyumeho.jp/wp-content/themes/theme-rinascente` |

### Production 環境（main ブランチ自動デプロイ）
| Secret 名 | 値 | 説明 |
|----------|---|---|
| `PRODUCTION_YUMEHO_URL` | `https://rinascenteyumeho.jp` | 本番 YUMEHO サイト |
| `PRODUCTION_RINASCENTE_URL` | `https://onerinascente.co.jp` | 本番 Rinascente サイト |
| `PRODUCTION_YUMEHO_WP_PATH` | `/home/your_user/rinascenteyumeho.jp/public_html` | 本番パス |
| `PRODUCTION_RINASCENTE_WP_PATH` | `/home/your_user/onerinascente.co.jp/public_html` | 本番パス |
| `PRODUCTION_YUMEHO_THEME_DIR` | `/home/your_user/rinascenteyumeho.jp/public_html/wp-content/themes/theme-yumeho` | 本番テーマパス |
| `PRODUCTION_RINASCENTE_THEME_DIR` | `/home/your_user/onerinascente.co.jp/public_html/wp-content/themes/theme-rinascente` | 本番テーマパス |

### Slack 通知（オプション）
| Secret 名 | 値 | 説明 |
|----------|---|---|
| `SLACK_WEBHOOK` | `https://hooks.slack.com/services/...` | Slack の Incoming Webhook URL |

## 3. 自動デプロイのトリガー

### Staging（開発環境）に自動デプロイ
```bash
# develop ブランチに push
git checkout develop
git commit -m "Update theme"
git push origin develop
```
→ `https://dev.rinascenteyumeho.jp/` に自動反映

### Production（本番環境）に自動デプロイ
```bash
# main ブランチに push
git checkout main
git commit -m "Release v1.0"
git push origin main
```
→ `https://rinascenteyumeho.jp/` に自動反映

## 4. デプロイ対象ファイル

以下のファイルを変更すると自動デプロイが実行されます：
- `wp-themes/**` — WP テーマファイル
- `yumeho/assets/**` — YUMEHO アセット
- `rinascentes/assets/**` — Rinascente アセット
- `config/*.env` — デプロイ設定
- `.github/workflows/deploy.yml` — ワークフロー定義

## 5. デプロイの確認

### GitHub Actions ダッシュボード
Repository → Actions → Deploy Workflow で実行状況を確認

### Slack 通知
デプロイ成功/失敗時に Slack に通知（設定時）

### 手動デプロイ（必要な場合）
```bash
./scripts/deploy-theme.sh yumeho staging
./scripts/deploy-theme.sh rinascente staging
```

## トラブルシューティング

### デプロイが失敗する
1. SSH 鍵の権限確認：`~/.ssh/xserver_deploy` が `600` であること
2. Xserver で公開鍵が登録されているか確認
3. `XSERVER_HOST`, `XSERVER_USER`, `XSERVER_PORT` が正しいか確認

### スモークテストで失敗
1. デプロイ先のテーマが正しく反映されたか確認
2. WP キャッシュが清除されたか確認
3. サイト URL が正しいか確認

### Slack 通知が来ない
- Webhook URL が正しいか確認
- Slack チャンネルの権限確認
