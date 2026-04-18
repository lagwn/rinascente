#!/bin/bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "=== Production Deploy ==="
echo "1) YUMEHO"
echo "2) Rinascente"
echo "3) Both"
read -r -p "Choose [1-3]: " choice
read -r -p "Type PRODUCTION to continue: " confirm

if [[ "$confirm" != "PRODUCTION" ]]; then
  echo "Cancelled."
  exit 1
fi

case "$choice" in
  1)
    ./scripts/deploy-theme.sh yumeho production
    ./scripts/smoke-test.sh yumeho production
    ;;
  2)
    ./scripts/deploy-theme.sh rinascente production
    ./scripts/smoke-test.sh rinascente production
    ;;
  3)
    ./scripts/deploy-theme.sh yumeho production
    ./scripts/deploy-theme.sh rinascente production
    ./scripts/smoke-test.sh all production
    ;;
  *)
    echo "Cancelled."
    exit 1
    ;;
esac

echo
echo "Production deploy finished."
read -r -p "Press Enter to close..." _
