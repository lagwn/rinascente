#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CONFIG_DIR="$PROJECT_ROOT/config"

log() {
  printf '[%s] %s\n' "$1" "$2"
}

die() {
  log "ERROR" "$1" >&2
  exit 1
}

normalize_env_name() {
  case "${1:-}" in
    staging|stage|dev)
      printf 'staging'
      ;;
    production|prod)
      printf 'production'
      ;;
    *)
      return 1
      ;;
  esac
}

ensure_env_file() {
  local env_name="$1"
  local env_file="$CONFIG_DIR/${env_name}.env"
  local example_file="$CONFIG_DIR/${env_name}.env.example"

  if [[ -f "$env_file" ]]; then
    return 0
  fi

  if [[ -f "$example_file" ]]; then
    cp "$example_file" "$env_file"
    die "config/${env_name}.env を example から作成しました。値を埋めて再実行してください。"
  fi

  die "config/${env_name}.env が見つかりません。"
}

load_env() {
  local env_name="$1"
  local env_file="$CONFIG_DIR/${env_name}.env"

  ensure_env_file "$env_name"

  set -a
  # shellcheck source=/dev/null
  source "$env_file"
  set +a
}

ssh_target() {
  printf '%s@%s' "$XSERVER_USER" "$XSERVER_HOST"
}

rsync_rsh() {
  printf 'ssh -p %s -o StrictHostKeyChecking=accept-new' "${XSERVER_PORT:-10022}"
}

run_ssh() {
  ssh -p "${XSERVER_PORT:-10022}" -o StrictHostKeyChecking=accept-new "$(ssh_target)" "$@"
}

theme_local_dir() {
  case "$1" in
    yumeho)
      printf '%s/wp-themes/theme-yumeho' "$PROJECT_ROOT"
      ;;
    rinascente)
      printf '%s/wp-themes/theme-rinascente' "$PROJECT_ROOT"
      ;;
    *)
      return 1
      ;;
  esac
}

uploads_local_dir() {
  case "$1" in
    yumeho)
      printf '%s' "${LOCAL_YUMEHO_UPLOADS_DIR:-$PROJECT_ROOT/wp/yumeho/app/public/wp-content/uploads}"
      ;;
    rinascente)
      printf '%s' "${LOCAL_RINASCENTE_UPLOADS_DIR:-$PROJECT_ROOT/wp/rinascente/app/public/wp-content/uploads}"
      ;;
    *)
      return 1
      ;;
  esac
}

site_config_value() {
  local env_name="$1"
  local site="$2"
  local kind="$3"

  case "${env_name}:${site}:${kind}" in
    staging:yumeho:url) printf '%s' "${STAGING_YUMEHO_URL:-}" ;;
    staging:rinascente:url) printf '%s' "${STAGING_RINASCENTE_URL:-}" ;;
    production:yumeho:url) printf '%s' "${PRODUCTION_YUMEHO_URL:-}" ;;
    production:rinascente:url) printf '%s' "${PRODUCTION_RINASCENTE_URL:-}" ;;

    staging:yumeho:wp_path) printf '%s' "${STAGING_YUMEHO_WP_PATH:-}" ;;
    staging:rinascente:wp_path) printf '%s' "${STAGING_RINASCENTE_WP_PATH:-}" ;;
    production:yumeho:wp_path) printf '%s' "${PRODUCTION_YUMEHO_WP_PATH:-}" ;;
    production:rinascente:wp_path) printf '%s' "${PRODUCTION_RINASCENTE_WP_PATH:-}" ;;

    staging:yumeho:theme_dir) printf '%s' "${STAGING_YUMEHO_THEME_DIR:-}" ;;
    staging:rinascente:theme_dir) printf '%s' "${STAGING_RINASCENTE_THEME_DIR:-}" ;;
    production:yumeho:theme_dir) printf '%s' "${PRODUCTION_YUMEHO_THEME_DIR:-}" ;;
    production:rinascente:theme_dir) printf '%s' "${PRODUCTION_RINASCENTE_THEME_DIR:-}" ;;

    staging:yumeho:uploads_dir) printf '%s' "${STAGING_YUMEHO_UPLOADS_DIR:-}" ;;
    staging:rinascente:uploads_dir) printf '%s' "${STAGING_RINASCENTE_UPLOADS_DIR:-}" ;;
    production:yumeho:uploads_dir) printf '%s' "${PRODUCTION_YUMEHO_UPLOADS_DIR:-}" ;;
    production:rinascente:uploads_dir) printf '%s' "${PRODUCTION_RINASCENTE_UPLOADS_DIR:-}" ;;

    staging:yumeho:expect) printf '%s' "${STAGING_YUMEHO_EXPECT:-}" ;;
    staging:rinascente:expect) printf '%s' "${STAGING_RINASCENTE_EXPECT:-}" ;;
    production:yumeho:expect) printf '%s' "${PRODUCTION_YUMEHO_EXPECT:-}" ;;
    production:rinascente:expect) printf '%s' "${PRODUCTION_RINASCENTE_EXPECT:-}" ;;
    *)
      return 1
      ;;
  esac
}
