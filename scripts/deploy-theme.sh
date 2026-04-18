#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# shellcheck source=./common.sh
source "$SCRIPT_DIR/common.sh"

usage() {
  cat <<'EOF'
Usage:
  ./scripts/deploy-theme.sh <site> <staging|production>

Examples:
  ./scripts/deploy-theme.sh yumeho staging
  ./scripts/deploy-theme.sh rinascente production
EOF
}

SITE="${1:-}"
ENV_INPUT="${2:-}"

if [[ -z "$SITE" || -z "$ENV_INPUT" ]]; then
  usage
  exit 1
fi

ENV_NAME="$(normalize_env_name "$ENV_INPUT")" || die "環境名は staging または production を指定してください。"

case "$SITE" in
  yumeho|rinascente)
    ;;
  *)
    die "site は yumeho または rinascente を指定してください。"
    ;;
esac

load_env "$ENV_NAME"

LOCAL_THEME_DIR="$(theme_local_dir "$SITE")"
REMOTE_THEME_DIR="$(site_config_value "$ENV_NAME" "$SITE" theme_dir)"
REMOTE_WP_PATH="$(site_config_value "$ENV_NAME" "$SITE" wp_path)"
LOCAL_THEME_MOVIE_DIR="$LOCAL_THEME_DIR/assets/movie"
REMOTE_THEME_MOVIE_DIR="$REMOTE_THEME_DIR/assets/movie"

[[ -d "$LOCAL_THEME_DIR" ]] || die "ローカルテーマが見つかりません: $LOCAL_THEME_DIR"
[[ -n "$REMOTE_THEME_DIR" ]] || die "remote theme dir が未設定です。config/${ENV_NAME}.env を確認してください。"

log "INFO" "Deploying ${SITE} theme to ${ENV_NAME}"

rsync -avz --delete \
  --exclude='.DS_Store' \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='assets/movie' \
  -e "$(rsync_rsh)" \
  "$LOCAL_THEME_DIR/" \
  "$(ssh_target):$REMOTE_THEME_DIR/"

if [[ -d "$LOCAL_THEME_MOVIE_DIR" ]]; then
  log "INFO" "Syncing ${SITE} theme movie assets without delete protection"
  rsync -avz \
    --exclude='.DS_Store' \
    -e "$(rsync_rsh)" \
    "$LOCAL_THEME_MOVIE_DIR/" \
    "$(ssh_target):$REMOTE_THEME_MOVIE_DIR/"
else
  log "INFO" "Local theme movie assets were not found; preserving remote ${SITE} movie files as-is"
fi

run_ssh "if [[ -d '$REMOTE_THEME_DIR' ]]; then find '$REMOTE_THEME_DIR' -type d -exec chmod 755 {} + && find '$REMOTE_THEME_DIR' -type f -exec chmod 644 {} +; fi"

if [[ -n "$REMOTE_WP_PATH" ]]; then
  run_ssh "if command -v wp >/dev/null 2>&1; then cd '$REMOTE_WP_PATH' && { wp cache flush >/dev/null 2>&1 || true; wp rewrite flush >/dev/null 2>&1 || true; }; else echo 'wp-cli not found on remote, skipped flush'; fi"
fi

log "DONE" "${SITE} theme deployed to ${ENV_NAME}"
