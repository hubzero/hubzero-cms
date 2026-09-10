<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/22/security_considerations/faqs
source-id: 2826
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Security questions

Answers to the questions hub managers ask most often about the CMS side of
security. Questions about the host underneath the hub belong in
[Operating system hardening](hardeningguide.md).

## Does an advisory for another content management system apply here?

Usually not, but check each one rather than assuming.

Some naming in this tree is shared with other PHP content management
systems: language keys beginning `J`, the `#__` table-prefix placeholder,
and `com_` component directories. The running code is Hubzero's own. The
application object, the request and response objects, the session handler,
the user object, the registration flow, the router, the database layer, and
the plugin and module systems are all in
[`core/libraries/Hubzero`](../../../core/libraries/Hubzero), and the
components under [`core/components`](../../../core/components) are written
against them.

So an advisory for another project rarely names code a hub actually runs.
It also means you cannot dismiss one on the strength of a version number:
read what the advisory describes and look for the same pattern here.
Hubzero tracks no other project's releases and receives no other project's
security patches, so nothing arrives automatically.

## Where does the CMS record failed logins?

Two places, and they hold different things.

`/var/log/hubzero/cmsauth.log` is a text log. Every line is a timestamp in
`Y-m-d H:i:s` followed by a message, with nothing between them but a space.

**System - HUBzero** writes the line most people mean by "a failed login".
It is the submitted username (stripped to `A-Z 0-9 _ . -`, or `[unknown]`),
the remote address, and the word `invalid`:

```
2026-09-09 14:22:31 jdoe 203.0.113.7 invalid
```

The same plugin writes `<username> <address> detect` when it recognises its
tracking cookie. **System - Log** writes a second, differently shaped line
for the same failure — the authentication type, the word `FAILURE`, and the
plugin's error message, with the username appended in quotes only when its
**Log user names** parameter is on. It carries no address.

The log directory is `/var/log/hubzero` when that directory exists, and the
`log_path` from **Site** > **Global Configuration** > **System** otherwise.
See
[`LogServiceProvider`](../../../core/bootstrap/Site/Providers/LogServiceProvider.php)
for the three loggers the CMS registers: `debug` (`cmsdebug.log`), `auth`
(`cmsauth.log`), and `spam` (`cmsspam.log`). Only the `auth` logger uses the
bare `%datetime% %message%` format; the other two use Monolog's default
line format.

The `#__users_log_auth` database table holds a structured record per attempt, with
`username`, `ip`, `status` and `logged` columns. The CMS reads that table,
not the text log, when it decides whether an account or an address has
crossed a threshold. See
[CMS-controlled Fail2Ban jail](hardencms/cmscontrolled_fail2ban_jail.md).

## Does the CMS scan uploads for viruses?

Yes, wherever a component calls `Filesystem::isSafe()` — support ticket
attachments, group and project files, wiki and blog media, storefront
images, and course media among them. The scan shells out to the command in
the `virus_scanner` configuration key, which defaults to:

```
clamscan -i --no-summary --block-encrypted
```

`virus_scanner` has no field in **Global Configuration**; set it in
`configuration.php` if you want a different command, for example `clamdscan`
against a running daemon. A non-zero exit from the scanner rejects the file,
so a scanner that is missing or whose daemon is down fails closed and logs
the reason.

Installing and updating ClamAV itself is the system administrator's job.

## Does the CMS set a Content-Security-Policy header?

Only if you enable **System - Content Security Policy**. It ships disabled.
The plugin can run in report-only mode, enforcing mode, or both at once, and
it sets `base-uri`, `object-src`, `child-src`, `connect-src`, `default-src`,
`font-src`, `form-action`, `frame-src`, `img-src`, `script-src` and
`style-src` from its own parameters. Start in report-only mode; the shipped
`script-src` default already includes `'unsafe-inline'` and `'unsafe-eval'`,
which much of the interface still needs.

The parameters are listed in the
[system plugin reference](../../reference/configuration/plugins/system.md).

## A member is stuck on a "spam detected" page. How do I release them?

That is the **System - Spamjail** plugin. Clear the counter from **Users** >
**Members**, open the member, and use **Reset** beside **Lifetime Spam
Incidents**. The full procedure, including the per-session counter that
clears itself, is in [Spam](../11-spam.md).

## Can I stop a specific address from reaching the hub?

Not from the CMS. The only address-level action Hubzero takes is handing an
address to a Fail2Ban jail after too many accounts have been blocked from
it, and even that only happens when you turn it on. Blocking, rate limiting
at the network edge, and reputation-based blocklists are all the system
administrator's tools.
