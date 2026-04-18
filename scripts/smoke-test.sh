#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# shellcheck source=./common.sh
source "$SCRIPT_DIR/common.sh"

usage() {
  cat <<'EOF'
Usage:
  ./scripts/smoke-test.sh <yumeho|rinascente|all> <staging|production>

Examples:
  ./scripts/smoke-test.sh all staging
  ./scripts/smoke-test.sh yumeho production
EOF
}

fetch() {
  curl "${CURL_AUTH_ARGS[@]}" -fsSL --max-time 20 "$1"
}

check_status() {
  local label="$1"
  local url="$2"
  curl "${CURL_AUTH_ARGS[@]}" -fsSIL --max-time 20 "$url" >/dev/null
  log "OK" "$label -> $url"
}

check_contains() {
  local label="$1"
  local url="$2"
  local needle="$3"
  local body

  body="$(fetch "$url")"
  if [[ -n "$needle" ]] && ! grep -Fq "$needle" <<<"$body"; then
    die "$label が想定文字列を含みません: $needle ($url)"
  fi

  log "OK" "$label content matched"
}

check_site() {
  local env_name="$1"
  local site="$2"
  local url
  local expect

  url="$(site_config_value "$env_name" "$site" url)"
  expect="$(site_config_value "$env_name" "$site" expect)"

  [[ -n "$url" ]] || die "${site} の URL が未設定です。config/${env_name}.env を確認してください。"

  check_status "${site} home" "$url/"
  check_contains "${site} home" "$url/" "$expect"
  check_status "${site} wp-json" "$url/wp-json/"
  check_status "${site} contact" "$url/contact/"
}

TARGET="${1:-}"
ENV_INPUT="${2:-}"

if [[ -z "$TARGET" || -z "$ENV_INPUT" ]]; then
  usage
  exit 1
fi

ENV_NAME="$(normalize_env_name "$ENV_INPUT")" || die "環境名は staging または production を指定してください。"
load_env "$ENV_NAME"

CURL_AUTH_ARGS=()
AUTH_USER_VAR="$(printf '%s_BASIC_AUTH_USER' "$(printf '%s' "$ENV_NAME" | tr '[:lower:]' '[:upper:]')")"
AUTH_PASS_VAR="$(printf '%s_BASIC_AUTH_PASS' "$(printf '%s' "$ENV_NAME" | tr '[:lower:]' '[:upper:]')")"
AUTH_USER="${!AUTH_USER_VAR:-}"
AUTH_PASS="${!AUTH_PASS_VAR:-}"

if [[ -n "$AUTH_USER" && -n "$AUTH_PASS" ]]; then
  CURL_AUTH_ARGS=(-u "${AUTH_USER}:${AUTH_PASS}")
fi

case "$TARGET" in
  yumeho)
    check_site "$ENV_NAME" "yumeho"
    ;;
  rinascente)
    check_site "$ENV_NAME" "rinascente"
    ;;
  all)
    check_site "$ENV_NAME" "yumeho"
    check_site "$ENV_NAME" "rinascente"
    ;;
  *)
    die "target は yumeho / rinascente / all を指定してください。"
    ;;
esac

log "DONE" "Smoke test passed for ${TARGET} (${ENV_NAME})"
