#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# shellcheck source=./common.sh
source "$SCRIPT_DIR/common.sh"

usage() {
  cat <<'EOF'
Usage:
  ./scripts/deploy-manuals.sh <staging|production>

Examples:
  ./scripts/deploy-manuals.sh staging
  ./scripts/deploy-manuals.sh production
EOF
}

ENV_INPUT="${1:-}"

if [[ -z "$ENV_INPUT" ]]; then
  usage
  exit 1
fi

ENV_NAME="$(normalize_env_name "$ENV_INPUT")" || die "環境名は staging または production を指定してください。"

load_env "$ENV_NAME"

LOCAL_MANUAL_DIR="$PROJECT_ROOT/docs"
MANUAL_SUBDIR="manuals/wp-operation-manual"
MANUAL_ASSET_SUBDIR="$MANUAL_SUBDIR/wp-operation-manual-assets"

[[ -d "$LOCAL_MANUAL_DIR" ]] || die "ローカル docs が見つかりません: $LOCAL_MANUAL_DIR"

for SITE in yumeho rinascente; do
  REMOTE_WP_PATH="$(site_config_value "$ENV_NAME" "$SITE" wp_path)"
  [[ -n "$REMOTE_WP_PATH" ]] || die "remote wp path が未設定です。config/${ENV_NAME}.env を確認してください。"

  REMOTE_MANUAL_DIR="${REMOTE_WP_PATH}/${MANUAL_SUBDIR}"
  REMOTE_MANUAL_ASSET_DIR="${REMOTE_WP_PATH}/${MANUAL_ASSET_SUBDIR}"

  log "INFO" "Deploying manuals to ${SITE} (${ENV_NAME})"

  run_ssh "mkdir -p '${REMOTE_MANUAL_ASSET_DIR}'"

  rsync -avz --delete \
    --exclude='.DS_Store' \
    --chmod=Du=rwx,Dgo=rx,Fu=rw,Fgo=r \
    -e "$(rsync_rsh)" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual-viewer.html" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual.html" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual.pdf" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual.md" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual-print.css" \
    "$(ssh_target):$REMOTE_MANUAL_DIR/"

  rsync -avz --delete \
    --exclude='.DS_Store' \
    --chmod=Du=rwx,Dgo=rx,Fu=rw,Fgo=r \
    -e "$(rsync_rsh)" \
    "$LOCAL_MANUAL_DIR/wp-operation-manual-assets/" \
    "$(ssh_target):$REMOTE_MANUAL_ASSET_DIR/"

  run_ssh "cp '${REMOTE_MANUAL_DIR}/wp-operation-manual-viewer.html' '${REMOTE_MANUAL_DIR}/index.html' && chmod 644 '${REMOTE_MANUAL_DIR}/index.html' && find '${REMOTE_MANUAL_DIR}' -type d -exec chmod 755 {} + && find '${REMOTE_MANUAL_DIR}' -type f -exec chmod 644 {} +"

  log "DONE" "Manuals deployed to ${SITE} (${ENV_NAME})"
done
