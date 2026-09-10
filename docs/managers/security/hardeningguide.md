<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/22/security_considerations/hardeningguide
source-id: 2827
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Operating system hardening

Everything on this page is **external to the CMS**. None of it is configured
from the administrator interface, none of it ships in this repository, and
none of it could be verified against the source tree. It is the practice the
team that runs the Purdue hubs follows on the hosts underneath them,
generalised where the original advice named a distribution or a package that
no longer exists.

Treat it as a starting checklist for whoever administers the server, not as
a specification. Package names, repository URLs, and version numbers below
were correct for Debian a decade ago and should be checked against your own
distribution before you type them.

> **Note:** The CMS-side settings — HTTPS enforcement, sessions, cookies,
> text filters, login thresholds, spam — are covered in
> [Hardening the CMS](hardencms/README.md).

## Keep packages patched

Apply security updates daily, and check that they actually applied. A hub at
Purdue was once compromised through a package update that failed halfway;
the check below would have caught it, and has run every day since.

```bash
apt update && apt upgrade
```

```bash
dpkg --audit
```

```bash
debsums | grep FAILED
```

Run the first two daily and the third at least weekly.

> **Warning:** The original version of this page told administrators to add
> a Hubzero package repository that also shipped patches for Joomla 1.5.
> That has not been true for many releases. Hubzero maintains its own code
> and does not carry Joomla patches; see
> [the security questions](faqs.md#is-a-joomla-vulnerability-a-hubzero-vulnerability).

## Web server

- **Put an application firewall in front of PHP.** ModSecurity is the option
  still maintained. It can block attempts against a vulnerability nobody has
  disclosed yet, and its log lines make a good basis for a Fail2Ban jail.

  > **Warning:** Earlier versions of this page recommended Suhosin
  > (`php5-suhosin`). Suhosin targets PHP 5, which Hubzero 2.4 does not run
  > on. Do not install it.

- **Redirect every plain HTTP request to HTTPS** at the web server. This
  stops mixed-content pages and stops a login form ever being served over
  plain HTTP. Do it here as well as in the CMS — the CMS's own **Force SSL**
  setting (**Site** > **Global Configuration** > **Server**) redirects
  within the application, which is later than you want.

- **Consider a DNS blocklist module** such as `mod_spamhaus`
  (`libapache2-mod-spamhaus` on Debian) to stop known spam-sending addresses
  submitting content while still letting them read. Spamhaus requires a paid
  data feed for commercial use. If you deploy one, say why the request was
  refused; the wording Purdue uses is:

  ```
  Access Denied! Your address is blacklisted. It could be because your
  computer is infected and participates in a spam botnet. If you're using a
  shared access point (e.g., wireless), it's possible that the IP address of
  that access point has been banned because someone else's computer is
  infected.
  ```

- **Aim for an A rating** from the Qualys SSL Labs server test at
  <https://www.ssllabs.com/ssltest/>.

## Fail2Ban

Install Fail2Ban and give it jails for SSH, WebDAV, the web server's error
log, your application firewall's log, exim, SpamAssassin, and the hub's own
authentication log at `/var/log/hubzero/cmsauth.log`.

The CMS writes that log itself, so a jail over it is the one item here you
can verify from the source tree. A failed login appears as a timestamp, the
attempted username, the remote address, and the word `invalid`:

```
2026-09-09 14:22:31 jdoe 203.0.113.7 invalid
```

Purdue bans permanently on any attempt to log in as `root` or another key
account. SSH is configured to refuse root password logins anyway; the
attempt is still allowed to happen so the address can be caught.

Hubzero can also hand an address to a Fail2Ban jail directly, without a log
line for Fail2Ban to match. That is a separate mechanism with its own
setup: see
[CMS-controlled Fail2Ban jail](hardencms/cmscontrolled_fail2ban_jail.md).

> **Note:** The original page argued for blocking all non-local IPv6 because
> the blocking tools of the day could not scale a ban from one address to a
> network. Fail2Ban has handled IPv6 since 0.10, and modern firewalls block
> prefixes as easily as addresses. Weigh the cost of turning IPv6 off
> against the users you exclude; the old blanket recommendation no longer
> holds.

## Antivirus

Install ClamAV, keep `freshclam` running so definitions stay current, and
scan the whole document root at least weekly.

The CMS calls a scanner on uploaded files by itself. The command it runs
comes from the `virus_scanner` key in `configuration.php` and defaults to
`clamscan -i --no-summary --block-encrypted`. A non-zero exit rejects the
upload, so a broken or unreachable scanner blocks uploads rather than
silently passing them.

Verify the whole path with the EICAR test file from
<https://www.eicar.org/download-anti-malware-testfile/> — try to upload it
to a support ticket or a group, and confirm the CMS refuses it.

## Host monitoring

- **Detect unauthorised configuration changes.** Purdue runs a tool called
  Ogre hourly; its engine is open source but the metadata and templates that
  make it useful are not. Any configuration management system that reports
  drift will do.
- **Run `rkhunter` daily** to catch suspicious changes.
- **Run an auditing tool** such as `lynis` and track the hardening score.
  Purdue targets 72 or better.
- **Install a firewall blocklist.** Purdue feeds DShield's list into
  iptables. It is published at <https://www.dshield.org/block.txt> with a
  detached signature at <https://www.dshield.org/block.txt.asc>; verify the
  signature before you load it.
- **Scan the network periodically.** Note that Debian does not bump version
  numbers in service banners when it backports a security patch, so a
  scanner like Nessus reports a great many false positives on a
  fully-patched host. The scans are still worth running to spot newly open
  ports and configuration mistakes.

## File system permissions

Own the Hubzero code with a user other than the one the web server runs as
(`www-data` on Debian), so a compromise of PHP cannot rewrite the
application. Directories the CMS genuinely writes to — uploads, the log
path, the cache and temporary paths — need to stay writable.

## PHP configuration

Hardening guides for `php.ini` are worth reading, but apply them one setting
at a time. Several of the settings such guides recommend break hub
functionality outright — the CMS shells out to a virus scanner and to
`fail2ban-client`, so disabling `exec()` disables both. Test each change on
a staging hub.
