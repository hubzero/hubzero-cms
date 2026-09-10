<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: stale
source: https://help.hubzero.org/documentation/22/security_considerations/hardencms/cmscontrolled_fail2ban_jail
source-id: 2829
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# CMS-controlled Fail2Ban jail

Hubzero throttles brute-force attempts in three stages, all configured on
one screen. The first two stages are entirely inside the CMS. The third
hands the offending address to Fail2Ban, which is server software outside
this repository and has to be set up by the system administrator first.

## The settings

Go to **Users** > **Members**, then **Options** in the toolbar. The
**Members Configuration** window opens; the fields below are on the **Login
Settings** tab.

| Field | Parameter | Default | What it does |
|---|---|---|---|
| **Maximum Reset Count** | `reset_count` | 10 | Password reset requests allowed per account per window. |
| **Time in Hours** | `reset_time` | 1 | The reset window. |
| **Maximum Failed Login Attempts** | `login_attempts_limit` | 10 | Failed logins allowed per account per window. |
| **Time in Hours** | `login_attempts_timeframe` | 1 | The failed-login window. |
| **Purge Log After** | `login_log_timeframe` | Never | How long attempts stay in the log table. |
| **Fail2Ban** | `fail2ban` | Off | Whether stage three runs at all. |
| **Maximum Number Blocked Accounts** | `blocked_accounts_limit` | 10 | Blocked accounts allowed per address per window. |
| **Time in Hours** | `blocked_accounts_timeframe` | 1 | The blocked-accounts window. |
| **Fail2Ban Jail** | `fail2ban-jail` | `hub-login` | Name of the jail to ban into. |

The same list, generated from the manifest, is in the
[Members configuration reference](../../../reference/configuration/components/members.md#login).

![The Login Settings tab of the Members Configuration window](../../media/cmscontrolled-fail2ban-jail-fail2ban1.png)

> **Warning:** This screenshot predates 2.4. It shows six fields with older
> labels and is missing **Purge Log After**, **Fail2Ban**, and **Fail2Ban
> Jail**. Read the table above, not the picture.

> **Warning:** Setting any of the three limits to `0` does **not** mean
> "no limit", whatever the field's help text says — it makes the check fail
> for everyone immediately. Leave them at one or more.

## Where the counting happens

All three stages read `#__users_log_auth`, the table
[`plg_user_xusers`](../../../../core/plugins/user/xusers/xusers.php) writes to
after every login attempt. Each row holds a username, the remote address, a
status of `success`, `failure` or `blocked`, and a timestamp.

**Purge Log After** trims that table. It is checked on each authentication
attempt, and rows older than the chosen period are deleted. Leaving it at
**Never** lets the table grow without bound, which eventually slows the
login page down.

## Stage one: password reset requests

Counted per account by
[`com_members`'s credentials controller](../../../../core/components/com_members/site/controllers/credentials.php).
It counts the member's outstanding reset tokens created inside
**Time in Hours**, and once that reaches **Maximum Reset Count** it refuses
the request:

> Sorry, you have exceeded your reset request limit. Please wait and try
> again later.

![The reset request limit message on the Reset Password page](../../media/cmscontrolled-fail2ban-jail-fail2ban2.png)

Nothing is blocked and nothing is logged as blocked; the member simply has
to wait for the window to pass.

## Stage two: failed logins

Counted per account by
[`plg_authentication_hubzero`](../../../../core/plugins/authentication/hubzero/hubzero.php).
It counts rows for that username with status `failure` inside **Time in
Hours**. Once the table already holds one fewer than **Maximum Failed Login
Attempts**, the plugin writes a `blocked` row for the account and refuses
the login:

> Your account has been temporarily disabled due to an excessive number of
> failed login attempts.

![The account temporarily disabled message on the sign-in form](../../media/cmscontrolled-fail2ban-jail-fail2ban3.png)

With the default of 10, the tenth attempt inside the hour is the one that is
refused. The account frees itself as the window slides forward; there is no
administrator action to take.

**Authentication - Email Token** applies the same two thresholds to its own
one-time codes, using the same parameters.

## Stage three: too many blocked accounts from one address

This stage only runs when **Fail2Ban** is **On**. With it **Off**,
**Maximum Number Blocked Accounts** has no effect at all.

When it is on, the plugin counts the *distinct* usernames that have a
`blocked` row from the current address inside **Time in Hours**. Once that
count reaches **Maximum Number Blocked Accounts**, the CMS locates
`fail2ban-client` on `PATH` and runs, as the web server user:

```bash
sudo /usr/bin/fail2ban-client set hub-login banip <address>
```

The jail name is whatever **Fail2Ban Jail** holds. The login is refused with
the same "temporarily disabled" message.

If `fail2ban-client` cannot be found, the CMS logs `fail2ban-client not
found.` and lets the login proceed to the account-level checks.

> **Important:** The CMS bans the address; it never unbans it. How long the
> ban lasts is entirely Fail2Ban's `bantime`. Nothing in the CMS calls
> `unbanip`.

> **Note:** This mechanism does **not** work by writing log lines for
> Fail2Ban to match. The CMS calls `fail2ban-client` directly. The jail
> therefore needs a filter that matches nothing, and exists only as a
> container for addresses the CMS pushes into it. The CMS does also write
> `/var/log/hubzero/cmsauth.log`, and you can point an ordinary
> log-scanning jail at that file, but that is a separate jail from this one.

## What the system administrator has to set up

Everything from here down is external to Hubzero. It could not be verified
against this repository; the samples are illustrations, not a supported
configuration, and any competent administrator will write better rules.
Fail2Ban's own documentation is the authority.

### sudo

The web server user needs to run exactly one command as root, with no
password:

```
www-data ALL=(root) NOPASSWD: /usr/bin/fail2ban-client set hub-login banip *
```

Put it in a file under `/etc/sudoers.d/` and check it with `visudo -c`.
Confirm the path with `which fail2ban-client` — the CMS resolves the path
itself, so the sudoers rule has to name the same one. Keep the rule as
narrow as this: it grants root, and a wildcard in the wrong place grants a
great deal more than a ban.

### The jail

```ini
# /etc/fail2ban/jail.local
[hub-login]
enabled   = true
port      = http,https
filter    = hub-login
logpath   = /var/log/hubzero/cmsauth.log
banaction = hublogin-failure
bantime   = 600
findtime  = 1
maxretry  = 1
```

### The filter

The filter has to match nothing, because the CMS supplies the addresses:

```ini
# /etc/fail2ban/filter.d/hub-login.conf
[Definition]
# The CMS bans into this jail directly, so this pattern is never meant to fire.
failregex = ^<HOST> this-filter-never-matches
ignoreregex =
```

### The action

```ini
# /etc/fail2ban/action.d/hublogin-failure.conf
[INCLUDES]
before = iptables-common.conf

[Definition]
actionstart = iptables -N fail2ban-hublogin
              iptables -I INPUT -p tcp -j fail2ban-hublogin

actionstop  = iptables -D INPUT -p tcp -j fail2ban-hublogin
              iptables -F fail2ban-hublogin
              iptables -X fail2ban-hublogin

actioncheck = iptables -n -L fail2ban-hublogin | grep -q fail2ban-hublogin

actionban   = iptables -I fail2ban-hublogin -p tcp --dport 443 -s <ip> -j DROP
              iptables -I fail2ban-hublogin -p tcp --dport 80 -s <ip> -j DROP

actionunban = iptables -D fail2ban-hublogin -p tcp --dport 443 -s <ip> -j DROP
              iptables -D fail2ban-hublogin -p tcp --dport 80 -s <ip> -j DROP

[Init]
name     = DEFAULT
protocol = tcp
chain    = INPUT
```

### Check it

```bash
fail2ban-client status hub-login
```

The output names the jail's filter, its log file, and the addresses
currently banned. `iptables -L fail2ban-hublogin` shows the rules the action
inserted.

> **Note:** The imported version of this page pinned Fail2Ban to 0.9.5 from
> a NeuroDebian repository for Debian wheezy. That advice is a decade out of
> date; use your distribution's current package.

## Turning it off for an event

Consider setting **Fail2Ban** to **Off** during a conference, a class, or a
workshop. A room of people typing the same wrong password produces exactly
the pattern stage three is looking for, and one banned address takes out
everyone behind that NAT. Stages one and two stay in effect and act per
account, which is the behaviour you want in a crowd.
