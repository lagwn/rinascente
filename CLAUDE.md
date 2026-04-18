# プロジェクトルール — 真誠会プロトタイプ

## デュアル書き込みルール（重要）

プロトタイプと WordPress テーマの2箇所に同一ファイルが存在する。
**片方を変更したら、必ずもう片方にも同じ変更を反映すること。**

### パス対応表

#### YUMEHO

| プロトタイプ | WP テーマ |
|------------|----------|
| `yumeho/assets/css/style.css` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/css/style.css` |
| `yumeho/assets/css/site-switcher.css` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/css/site-switcher.css` |
| `yumeho/assets/js/main.js` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/js/main.js` |
| `yumeho/assets/js/background-effect.js` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/js/background-effect.js` |
| `yumeho/assets/js/interactive.js` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/js/interactive.js` |
| `yumeho/assets/js/pricing.js` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/js/pricing.js` |
| `yumeho/assets/img/*` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/img/*` |

#### Rinascente

| プロトタイプ | WP テーマ |
|------------|----------|
| `rinascentes/assets/css/style.css` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/css/style.css` |
| `rinascentes/assets/css/site-switcher.css` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/css/site-switcher.css` |
| `rinascentes/assets/js/main.js` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/js/main.js` |
| `rinascentes/assets/img/*` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/img/*` |

#### WP テーマ定義ファイル（wp-themes/ → wp/ の同期）

| ソース（開発用） | インストール先 |
|----------------|-------------|
| `wp-themes/theme-yumeho/*.php` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/*.php` |
| `wp-themes/theme-yumeho/inc/*` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/inc/*` |
| `wp-themes/theme-yumeho/template-parts/*` | `wp/yumeho/app/public/wp-content/themes/theme-yumeho/template-parts/*` |
| `wp-themes/theme-rinascente/*.php` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/*.php` |
| `wp-themes/theme-rinascente/inc/*` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/inc/*` |
| `wp-themes/theme-rinascente/template-parts/*` | `wp/rinascente/app/public/wp-content/themes/theme-rinascente/template-parts/*` |

### 変更時の手順

1. **CSS/JS/画像を変更する場合:**
   - プロトタイプ側（`yumeho/assets/` or `rinascentes/assets/`）を編集
   - 同じファイルを WP テーマ側にもコピー

2. **WP テンプレート（PHP）を変更する場合:**
   - `wp-themes/` 内のファイルを編集
   - WP Local 環境（`wp/` 配下）にもコピー

3. **プロトタイプ HTML を変更する場合:**
   - プロトタイプ HTML を編集
   - 対応する WP テンプレート PHP にも同じ構造変更を反映

### 一括同期コマンド

```bash
# YUMEHO: プロトタイプ → WP テーマ（アセットのみ）
rsync -av --delete yumeho/assets/ wp/yumeho/app/public/wp-content/themes/theme-yumeho/assets/

# YUMEHO: wp-themes → WP Local（PHP テンプレート）
rsync -av --delete wp-themes/theme-yumeho/ wp/yumeho/app/public/wp-content/themes/theme-yumeho/ --exclude='assets/'

# Rinascente: プロトタイプ → WP テーマ（アセットのみ）
rsync -av --delete rinascentes/assets/ wp/rinascente/app/public/wp-content/themes/theme-rinascente/assets/

# Rinascente: wp-themes → WP Local（PHP テンプレート）
rsync -av --delete wp-themes/theme-rinascente/ wp/rinascente/app/public/wp-content/themes/theme-rinascente/ --exclude='assets/'
```

## ディレクトリ構成

```
プロトタイプ/
├── rinascentes/          ← Rinascente プロトタイプ（HTML）
├── yumeho/               ← YUMEHO プロトタイプ（HTML）
├── mica30/               ← MICA30 プロトタイプ（HTML）※ペンディング
├── wp-themes/
│   ├── theme-rinascente/ ← Rinascente WP テーマ（開発マスター）
│   └── theme-yumeho/     ← YUMEHO WP テーマ（開発マスター）
├── wp/
│   ├── rinascente/       ← Local WP 環境（Rinascente）
│   └── yumeho/           ← Local WP 環境（YUMEHO）
├── wp-theme-parts/       ← 共通 PHP パーツ（フォーム等）
├── 提案資料/              ← 提案書・要件定義 PDF
├── CLAUDE.md             ← このファイル
├── yumeho-seo-meta.md
├── rinascente-seo-meta.md
├── wp-cms-requirements.md
├── wp-structured-data-guide.md
├── wp-pre-build-checklist.md
├── wp-security-monitoring.md
└── wp-theme-parts/README.md
```
