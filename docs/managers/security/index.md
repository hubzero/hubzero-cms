<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
screenshots: stale
-->
# Security considerations

A hub is a public web application on a public server, so its security has
two halves. The operating system, the web server, and the services around
them are hardened by the system administrator with tools that are not part
of this repository. The CMS has its own settings that decide how sessions
and cookies behave, how quickly a brute-force attempt is throttled, which
response headers the hub sets, and how spam is caught. This section covers
both, says plainly which is which, and is equally plain about the screens
that look like security controls and are not.

## What the CMS gives you

| Area | Where |
|---|---|
| Force HTTPS | **Site** > **Global Configuration** > **Server** > **Force SSL** |
| Session lifetime and handler | **Site** > **Global Configuration** > **System** > **Session Settings** |
| Cookie domain and path | **Site** > **Global Configuration** > **Site** > **Cookie Settings** |
| What HTML each access group may submit | **Site** > **Global Configuration** > **Text Filters** — but see the warning under [Text filters](03-hardening-the-cms.md#text-filters): nothing reads it |
| Failed login and password-reset thresholds | **Users** > **Members** > **Options** > **Login Settings** |
| Password rules and password blacklist | **Users** > **Members** > **Passwords** |
| Second authentication factors | **Extensions** > **Plug-in Manager**, the `authfactors` group |
| Content-Security-Policy header | **Extensions** > **Plug-in Manager** > **System - Content Security Policy** (ships disabled) |
| Referrer-Policy header | **Extensions** > **Plug-in Manager** > **System - Referrer Policy** (ships enabled) |
| Spam detection | **Extensions** > **Plug-in Manager**, the `antispam` group |
| Upload virus scanning | The `virus_scanner` key in `configuration.php` |

Every parameter behind these screens is listed in the generated
[configuration reference](../../reference/configuration/index.md).

## Where to start

Nine tenths of what makes a hub hard to attack is the system administrator's
work, and none of it is in the administrator interface. If you are the person
who inherited the hub and not the person who runs the server, this is the
list that is actually yours, cheapest first. Each links to the section that
explains it.

1. **Set [Force SSL](03-hardening-the-cms.md#force-https) to Entire Site.** On a site it is the only
   value that marks the session cookie secure, and the shipped value is
   **None**. Two minutes, reversible, and nothing else on this page matters
   as much.
2. **Choose a [session handler](03-hardening-the-cms.md#sessions-and-cookies).** The shipped value
   leaves sessions to PHP's own storage. `database` is the usual choice on a
   hub and is what makes sessions visible and purgeable.
3. **Do not spend time on [Text Filters](03-hardening-the-cms.md#text-filters).** The screen looks
   like the most important one on this list and nothing reads it. Knowing
   that saves you an afternoon and a false sense of a control you do not
   have.
4. **Leave the [login thresholds](03-hardening-the-cms.md#login-thresholds) alone** unless you have
   a reason. The shipped values are sensible, and 0 does not mean "no limit"
   — see the warning there.
5. **Check that [System - Referrer Policy](03-hardening-the-cms.md#security-headers) is still on.**
   It ships enabled with a sensible policy, which means the only thing to do
   is not turn it off. Confirm it, then leave it.
6. **Then, if you have somewhere to test, work through
   [System - Content Security Policy](03-hardening-the-cms.md#security-headers)** in report-only
   mode. That one ships disabled, and it is a project rather than a toggle.

Everything else on the CMS side is a response to something: spam arriving,
an account under attack, a member locked out. The
[security questions](01-questions.md) below cover those.

## Reporting a vulnerability

If you find a vulnerability in the Hubzero release itself, report it to the
project rather than filing it in a public tracker.
