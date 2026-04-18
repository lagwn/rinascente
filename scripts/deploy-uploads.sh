#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# shellcheck source=./common.sh
source "$SCRIPT_DIR/common.sh"

usage() {
  cat <<'EOF'
Usage:
  ./scripts/deploy-uploads.sh <site> <staging|production> [--delete]

Examples:
  ./scripts/deploy-uploads.sh yumeho staging
  ./scripts/deploy-uploads.sh rinascente production --delete
EOF
}

SITE="${1:-}"
ENV_INPUT="${2:-}"
DELETE_FLAG="${3:-}"

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

LOCAL_UPLOADS_DIR="$(uploads_local_dir "$SITE")"
REMOTE_UPLOADS_DIR="$(site_config_value "$ENV_NAME" "$SITE" uploads_dir)"

[[ -d "$LOCAL_UPLOADS_DIR" ]] || die "ローカル uploads が見つかりません: $LOCAL_UPLOADS_DIR"
[[ -n "$REMOTE_UPLOADS_DIR" ]] || die "remote uploads dir が未設定です。config/${ENV_NAME}.env を確認してください。"

log "INFO" "Deploying ${SITE} uploads to ${ENV_NAME}"
if [[ "$DELETE_FLAG" == "--delete" ]]; then
  rsync -avz --delete \
    --exclude='.DS_Store' \
    -e "$(rsync_rsh)" \
    "$LOCAL_UPLOADS_DIR/" \
    "$(ssh_target):$REMOTE_UPLOADS_DIR/"
else
  rsync -avz \
    --exclude='.DS_Store' \
    -e "$(rsync_rsh)" \
    "$LOCAL_UPLOADS_DIR/" \
    "$(ssh_target):$REMOTE_UPLOADS_DIR/"
fi

log "DONE" "${SITE} uploads deployed to ${ENV_NAME}"
