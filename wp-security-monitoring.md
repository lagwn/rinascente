# WordPress セキュリティ監視 + Slack 即時通知 構築ガイド

2026-04-09 策定

---

## 概要

```
不正ログイン試行を検知  →  ファイル改ざんを検知  →  マルウェア侵入を検知  →  Slack に即時通知
ブルートフォース攻撃を      コアファイルの変更を        ウイルス・不正コードを      異常検知と同時に
自動ブロック               即座に検出                スキャン                 担当者へ通知
```

---

## 使用プラグイン: Wordfence Security（Free）

| 機能 | Free 版対応 | 備考 |
|------|-----------|------|
| ブルートフォース防御 | ✅ | ログイン試行回数制限 + IP ブロック |
| ファイル改ざん検知 | ✅ | WP コア / プラグイン / テーマファイルの差分検出 |
| マルウェアスキャン | ✅ | 定期スキャン（Free: 手動 or 72時間間隔） |
| ファイアウォール（WAF） | ✅ | アプリケーション層の攻撃防御 |
| リアルタイムトラフィック監視 | ✅ | アクセスログのライブ表示 |
| 2段階認証（2FA） | ✅ | 管理者ログインの強化 |
| IP ブラックリスト | ✅ | 手動 + 自動ブロック |
| Slack 通知 | ⚠️ | Wordfence 標準はメール通知。Slack 連携は webhook で構築 |

---

## セットアップ手順

### Phase 1: Wordfence インストール & 基本設定

```
1. プラグイン > 新規追加 > 「Wordfence Security」を検索 > インストール > 有効化
2. Wordfence > Dashboard > セットアップウィザードを実行
   - メール通知先: 管理者メールアドレス
   - セキュリティレベル: 推奨設定を適用
```

### Phase 2: ブルートフォース防御設定

```
Wordfence > All Options > Brute Force Protection

推奨設定:
├─ Lock out after how many login failures: 5（5回失敗でロック）
├─ Lock out after how many forgot password attempts: 3
├─ Count failures over what time period: 4 hours
├─ Amount of time a user is locked out: 30 minutes
├─ Immediately lock out invalid usernames: ✅ ON
└─ Prevent discovery of usernames: ✅ ON
```

### Phase 3: ファイル改ざん検知設定

```
Wordfence > All Options > Scan Options

推奨設定:
├─ Scan core files against repository versions: ✅ ON
├─ Scan theme files against repository versions: ✅ ON
├─ Scan plugin files against repository versions: ✅ ON
├─ Scan for changed files: ✅ ON
├─ Check if files are accessible from outside: ✅ ON
└─ Scan Scheduling: ✅ Enable（Free版: Low priority 自動スキャン）
```

### Phase 4: マルウェアスキャン設定

```
Wordfence > All Options > Scan Options

追加設定:
├─ Scan files outside your WordPress installation: ✅ ON
├─ Scan images, binary, and other files as if executable: ✅ ON
├─ Enable HIGH SENSITIVITY scanning: ✅ ON（誤検知が増える場合は OFF）
├─ Scan for malware signatures: ✅ ON
└─ Scan for known malicious URLs: ✅ ON
```

---

## Phase 5: Slack 即時通知の構築

Wordfence のメール通知を Slack に転送する仕組みを構築する。

### 方法 A: Slack Incoming Webhook + functions.php（推奨）

Wordfence はメール通知をトリガーするが、PHP フックで Slack にも同時送信する。

#### Step 1: Slack Webhook URL を取得

```
1. Slack ワークスペース > 設定 > App管理 > Incoming Webhooks
2. 「Add New Webhook to Workspace」
3. 通知先チャンネルを選択（例: #security-alerts）
4. Webhook URL を取得（https://hooks.slack.com/services/T.../B.../xxx...）
```

#### Step 2: カスタマイザーに Webhook URL を登録

```php
// functions.php — Slack Webhook 設定
function slack_security_customizer($wp_customize) {
    $wp_customize->add_setting('slack_webhook_url', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('slack_webhook_url', [
        'label'   => 'Slack Webhook URL（セキュリティ通知）',
        'section' => 'analytics',  // GA4 と同じセクションに配置
        'type'    => 'url',
    ]);
}
add_action('customize_register', 'slack_security_customizer');
```

#### Step 3: Slack 通知送信関数

```php
// functions.php

/**
 * Slack にセキュリティアラートを送信
 */
function send_slack_security_alert(string $title, string $message, string $level = 'warning'): void {
    $webhook_url = get_theme_mod('slack_webhook_url', '');
    if (empty($webhook_url)) return;

    $colors = [
        'critical' => '#dc2626',  // 赤
        'warning'  => '#f59e0b',  // オレンジ
        'info'     => '#3b82f6',  // 青
    ];

    $payload = [
        'username'    => 'WP Security Bot',
        'icon_emoji'  => ':shield:',
        'attachments' => [[
            'color'  => $colors[$level] ?? $colors['warning'],
            'title'  => $title,
            'text'   => $message,
            'fields' => [
                ['title' => 'サイト', 'value' => home_url(), 'short' => true],
                ['title' => '検知時刻', 'value' => wp_date('Y/m/d H:i:s'), 'short' => true],
            ],
            'footer' => 'Wordfence + WP Security Monitor',
        ]],
    ];

    wp_remote_post($webhook_url, [
        'body'    => json_encode($payload, JSON_UNESCAPED_UNICODE),
        'headers' => ['Content-Type' => 'application/json'],
        'timeout' => 10,
    ]);
}
```

#### Step 4: 各セキュリティイベントをフック

```php
// functions.php

// ── 1. 不正ログイン試行の検知 ──
add_action('wp_login_failed', function($username) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    send_slack_security_alert(
        '🔐 不正ログイン試行を検知',
        "ユーザー名: `{$username}`\nIP: `{$ip}`\nUA: " . ($_SERVER['HTTP_USER_AGENT'] ?? ''),
        'warning'
    );
});

// ── 2. ログイン成功の通知（管理者のみ） ──
add_action('wp_login', function($user_login, $user) {
    if (!in_array('administrator', $user->roles)) return;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    send_slack_security_alert(
        '✅ 管理者ログイン',
        "ユーザー: `{$user_login}`\nIP: `{$ip}`",
        'info'
    );
}, 10, 2);

// ── 3. Wordfence アラートメールを Slack に転送 ──
add_filter('wp_mail', function($args) {
    // Wordfence からの通知メールを検知
    if (strpos($args['subject'] ?? '', 'Wordfence') !== false ||
        strpos($args['subject'] ?? '', '[Wordfence') !== false) {

        $level = 'warning';
        $subject = $args['subject'];

        // 重大度判定
        if (stripos($subject, 'critical') !== false ||
            stripos($subject, 'malware') !== false ||
            stripos($subject, 'modified') !== false) {
            $level = 'critical';
        }

        // 本文からテキスト抽出（HTML除去）
        $body = wp_strip_all_tags($args['message'] ?? '');
        $body = mb_substr($body, 0, 500);  // 500文字に制限

        send_slack_security_alert(
            "🛡️ Wordfence: {$subject}",
            $body,
            $level
        );
    }
    return $args;  // メールもそのまま送信
});

// ── 4. プラグイン / テーマの変更検知 ──
add_action('upgrader_process_complete', function($upgrader, $options) {
    $type = $options['type'] ?? '';
    $action = $options['action'] ?? '';
    send_slack_security_alert(
        "📦 {$type} が {$action} されました",
        "種別: {$type}\nアクション: {$action}",
        'info'
    );
}, 10, 2);

// ── 5. ユーザーの追加 / 権限変更の検知 ──
add_action('user_register', function($user_id) {
    $user = get_userdata($user_id);
    send_slack_security_alert(
        '👤 新規ユーザー登録',
        "ユーザー名: `{$user->user_login}`\nメール: `{$user->user_email}`\nロール: " . implode(', ', $user->roles),
        'warning'
    );
});

add_action('set_user_role', function($user_id, $role, $old_roles) {
    $user = get_userdata($user_id);
    send_slack_security_alert(
        '⚠️ ユーザー権限変更',
        "ユーザー: `{$user->user_login}`\n旧ロール: " . implode(', ', $old_roles) . "\n新ロール: `{$role}`",
        'critical'
    );
}, 10, 3);
```

---

## Slack 通知一覧

| イベント | トリガー | 重大度 | 通知内容 |
|---------|---------|--------|---------|
| 不正ログイン試行 | `wp_login_failed` | ⚠️ warning | ユーザー名 / IP / UA |
| 管理者ログイン成功 | `wp_login`（admin のみ） | ℹ️ info | ユーザー名 / IP |
| Wordfence アラート | `wp_mail` フィルター | 🔴 critical / ⚠️ warning | Wordfence メール内容を転送 |
| ファイル改ざん検知 | Wordfence スキャン → メール → Slack | 🔴 critical | 変更されたファイル名 |
| マルウェア検知 | Wordfence スキャン → メール → Slack | 🔴 critical | 検知内容 |
| プラグイン / テーマ更新 | `upgrader_process_complete` | ℹ️ info | 更新種別 |
| 新規ユーザー登録 | `user_register` | ⚠️ warning | ユーザー名 / メール / ロール |
| ユーザー権限変更 | `set_user_role` | 🔴 critical | 旧ロール → 新ロール |

---

## Slack 通知の見え方（例）

```
🛡️ Wordfence: [Critical] File modified on rinascente.co.jp
━━━━━━━━━━━━━━━━━━━━━━
The file wp-config.php has been modified.
The file does not match the original in the repository.

サイト:        https://rinascente.co.jp
検知時刻:      2026/04/09 14:32:15
━━━━━━━━━━━━━━━━━━━━━━
Wordfence + WP Security Monitor
```

```
🔐 不正ログイン試行を検知
━━━━━━━━━━━━━━━━━━━━━━
ユーザー名: `admin`
IP: `203.0.113.42`
UA: Mozilla/5.0 (compatible; bot/1.0)

サイト:        https://rinascente.co.jp
検知時刻:      2026/04/09 14:28:03
━━━━━━━━━━━━━━━━━━━━━━
```

---

## 追加セキュリティ対策（Wordfence 設定）

### 2段階認証（2FA）の有効化

```
Wordfence > Login Security > Two-Factor Authentication

1. 管理者アカウントで 2FA を有効化
2. Google Authenticator / Authy でQRコードをスキャン
3. リカバリーコードを安全な場所に保管
```

### ファイアウォール最適化

```
Wordfence > Firewall > Manage Firewall

1. Web Application Firewall Status: Enabled and Protecting
2. Protection Level: 初回は Learning Mode（1週間）→ その後 Enabled
3. Rate Limiting:
   ├─ How should we treat Google crawlers: Verified Google crawlers have unlimited access
   ├─ If anyone's requests exceed: 240 per minute → throttle
   └─ If a crawler's page views exceed: 240 per minute → block
```

### IP ブロック設定

```
Wordfence > Firewall > Blocking

手動ブロック:
├─ 既知の攻撃元 IP を追加
└─ 国別ブロック（Free版では不可 → Premium のみ）
```

---

## wp-pre-build-checklist.md への追記事項

プラグイン一覧に Wordfence を追加:

| プラグイン | 用途 | コスト |
|-----------|------|--------|
| Rank Math Free | SEO / 構造化データ | 無料 |
| WP Multibyte Patch | 日本語対応 | 無料 |
| EWWW Image Optimizer | 画像最適化 | 無料 |
| UpdraftPlus | バックアップ | 無料 |
| **Wordfence Security** | **セキュリティ監視 + ファイアウォール** | **無料** |

---

## 運用ルール

| 頻度 | 作業 |
|------|------|
| 毎日 | Slack 通知チャンネルを確認 |
| 週1回 | Wordfence ダッシュボードで脅威サマリー確認 |
| 月1回 | フルマルウェアスキャン実行（手動） |
| 四半期 | Wordfence の設定見直し・ルール更新 |
| 随時 | WP コア / プラグイン / テーマのアップデート適用 |
