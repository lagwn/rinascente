#!/bin/bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "=== Staging Deploy ==="
echo "1) YUMEHO"
echo "2) Rinascente"
echo "3) Both"
read -r -p "Choose [1-3]: " choice

case "$choice" in
  1)
    ./scripts/deploy-theme.sh yumeho staging
    ./scripts/smoke-test.sh yumeho staging
    ;;
  2)
    ./scripts/deploy-theme.sh rinascente staging
    ./scripts/smoke-test.sh rinascente staging
    ;;
  3)
    ./scripts/deploy-theme.sh yumeho staging
    ./scripts/deploy-theme.sh rinascente staging
    ./scripts/smoke-test.sh all staging
    ;;
  *)
    echo "Cancelled."
    exit 1
    ;;
esac

echo
echo "Staging deploy finished."
read -r -p "Press Enter to close..." _
