<!--
status: generated
source: core/components/com_massmail/config/config.xml
-->

# Massmail (com_massmail)

Compliant, rate-limited mass mail to users, groups, and addresses.

Parameters from [`core/components/com_massmail/config/config.xml`](../../../../core/components/com_massmail/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Delivery

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `sending_enabled` | Sending enabled | radio | `0 (No)` | Master switch. When off, nothing is delivered; new mailings are held until it is turned back on. Options: `0` No, `1` Yes. |
| `require_approval` | Require approval to send | radio | `0 (No)` | Every mailing must be approved by an authorized user (Super Administrators always qualify) before it is sent, not just those overriding opt-out preferences. Options: `0` No, `1` Yes. |
| `hourly_limit` | Hourly send limit | text | `300` | Maximum messages delivered per rolling hour. Each cron run sends an even share of this (limit ÷ runs-per-hour), and the current Postfix queue counts against it, so a full queue automatically holds off further sending. |
| `maillog_path` | Mail log path | text | `/var/log/maillog` | Postfix log scanned to measure the system-wide send rate across all senders (newsletters, mailman, system mail). Falls back to internal counting plus queue backpressure if unreadable. |
| `site_url` | Site URL | text | — | Absolute site address (e.g. https://example.org) used to build the unsubscribe link in each message. Delivery runs under cron, where there is no request to derive the host from; leave blank to use the global Live Site setting. |

## Recipients

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `no_preference_sendable` | Treat 'no preference' as mailable | radio | `1 (Yes)` | Users who never set a mail preference receive mailings. Explicit opt-outs are always excluded (unless overridden with approval). Options: `0` No, `1` Yes. |
| `require_email_confirmed` | Require confirmed email | radio | `0 (No)` | Only send to users whose email address has been confirmed. Options: `0` No, `1` Yes. |

## Message defaults

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `from_name` | From name | text | — | The name every mailing is sent from. Defaults to the site name if left blank. |
| `from_email` | From email | text | — | The address every mailing is sent from; the mail server must be configured to send as it. Defaults to the site mail-from address if left blank. |
| `footer_text` | Message footer | textarea | — | Appended to every message (e.g. organization identity and postal address for CAN-SPAM compliance). |
| `max_attachment_size` | Max attachment size (bytes) | text | `10485760` | Uploads larger than this are rejected. |
