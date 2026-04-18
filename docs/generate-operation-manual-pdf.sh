#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
DOCS_DIR="$ROOT_DIR/docs"
INPUT_MD="$DOCS_DIR/wp-operation-manual.md"
PRINT_CSS="$DOCS_DIR/wp-operation-manual-print.css"
OUTPUT_HTML="$DOCS_DIR/wp-operation-manual.html"
OUTPUT_PDF="$DOCS_DIR/wp-operation-manual.pdf"
CHROME_BIN="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

if ! command -v pandoc >/dev/null 2>&1; then
  echo "pandoc が見つかりません。" >&2
  exit 1
fi

if [[ ! -x "$CHROME_BIN" ]]; then
  echo "Google Chrome が見つかりません: $CHROME_BIN" >&2
  exit 1
fi

pandoc \
  "$INPUT_MD" \
  --from=gfm \
  --to=html5 \
  --standalone \
  --toc \
  --metadata title="WordPress 更新マニュアル" \
  --css "$(basename "$PRINT_CSS")" \
  --resource-path="$DOCS_DIR" \
  --output="$OUTPUT_HTML"

"$CHROME_BIN" \
  --headless=new \
  --disable-gpu \
  --allow-file-access-from-files \
  --print-to-pdf-no-header \
  --print-to-pdf="$OUTPUT_PDF" \
  "file://$OUTPUT_HTML" \
  >/dev/null 2>&1

echo "HTML: $OUTPUT_HTML"
echo "PDF:  $OUTPUT_PDF"
