#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

echo "=========================================="
echo "GitHub Secrets Setup"
echo "=========================================="
echo ""
echo "このスクリプトは GitHub CLI (gh) を使って Secrets を設定します。"
echo "事前に以下が必要です："
echo "  1. GitHub CLI インストール: https://cli.github.com/"
echo "  2. gh auth login で認証済み"
echo ""

# Check if gh CLI is installed
if ! command -v gh &> /dev/null; then
    echo "❌ GitHub CLI が見つかりません。"
    echo "インストール: https://cli.github.com/"
    exit 1
fi

# Check if authenticated
if ! gh auth status &> /dev/null; then
    echo "❌ GitHub に認証されていません。"
    echo "実行: gh auth login"
    exit 1
fi

# Get repo info
REPO=$(gh repo view --json nameWithOwner -q 2>/dev/null || echo "")
if [ -z "$REPO" ]; then
    echo "❌ GitHub リポジトリが見つかりません。"
    echo "このディレクトリで git remote origin を設定してください。"
    exit 1
fi

echo "✓ リポジトリ: $REPO"
echo ""

# Load environment variables
cd "$PROJECT_ROOT"
if [ ! -f "config/staging.env" ]; then
    echo "❌ config/staging.env が見つかりません。"
    exit 1
fi

source config/staging.env

# Prompt for SSH key
echo "SSH 秘密鍵を入力してください。"
echo "(例: ~/.ssh/xserver_deploy または ~/.ssh/id_rsa)"
read -p "秘密鍵パス: " SSH_KEY_PATH

if [ ! -f "$SSH_KEY_PATH" ]; then
    echo "❌ ファイルが見つかりません: $SSH_KEY_PATH"
    exit 1
fi

SSH_KEY_CONTENT=$(cat "$SSH_KEY_PATH")

# Set secrets
echo ""
echo "Secrets を設定中..."

gh secret set XSERVER_SSH_KEY --body "$SSH_KEY_CONTENT" --repo "$REPO" && echo "✓ XSERVER_SSH_KEY"
gh secret set XSERVER_HOST --body "$XSERVER_HOST" --repo "$REPO" && echo "✓ XSERVER_HOST"
gh secret set XSERVER_USER --body "$XSERVER_USER" --repo "$REPO" && echo "✓ XSERVER_USER"
gh secret set XSERVER_PORT --body "$XSERVER_PORT" --repo "$REPO" && echo "✓ XSERVER_PORT"

gh secret set STAGING_YUMEHO_URL --body "$STAGING_YUMEHO_URL" --repo "$REPO" && echo "✓ STAGING_YUMEHO_URL"
gh secret set STAGING_RINASCENTE_URL --body "$STAGING_RINASCENTE_URL" --repo "$REPO" && echo "✓ STAGING_RINASCENTE_URL"
gh secret set STAGING_YUMEHO_WP_PATH --body "$STAGING_YUMEHO_WP_PATH" --repo "$REPO" && echo "✓ STAGING_YUMEHO_WP_PATH"
gh secret set STAGING_RINASCENTE_WP_PATH --body "$STAGING_RINASCENTE_WP_PATH" --repo "$REPO" && echo "✓ STAGING_RINASCENTE_WP_PATH"
gh secret set STAGING_YUMEHO_THEME_DIR --body "$STAGING_YUMEHO_THEME_DIR" --repo "$REPO" && echo "✓ STAGING_YUMEHO_THEME_DIR"
gh secret set STAGING_RINASCENTE_THEME_DIR --body "$STAGING_RINASCENTE_THEME_DIR" --repo "$REPO" && echo "✓ STAGING_RINASCENTE_THEME_DIR"

# Optional: Slack webhook
read -p "Slack Webhook URL を設定しますか? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Slack Webhook URL: " SLACK_WEBHOOK
    gh secret set SLACK_WEBHOOK --body "$SLACK_WEBHOOK" --repo "$REPO" && echo "✓ SLACK_WEBHOOK"
fi

echo ""
echo "=========================================="
echo "✓ Secrets 設定完了！"
echo "=========================================="
echo ""
echo "今後のデプロイ:"
echo "  1. develop ブランチに push → Staging に自動デプロイ"
echo "  2. main ブランチに push → Production に自動デプロイ"
echo ""
