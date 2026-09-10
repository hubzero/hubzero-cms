<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/22/security_considerations/hardencms
source-id: 2828
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Hardening the CMS

The settings and extensions inside the administrator interface that decide
how strict the hub is. Everything on this page is part of Hubzero and can be
checked in the source tree. The host underneath is covered separately in
[Operating system hardening](../hardeningguide.md).

## Force HTTPS

**Site** > **Global Configuration** > **Server** > **Force SSL** takes three
values: **None**, **Administrator Only**, and **Entire Site**. Set it to
**Entire Site**.

This is a redirect inside the application, so the request has already
reached PHP by the time it fires. Redirect at the web server as well.

## Sessions and cookies

**Session Settings** are on the **System** tab of **Global Configuration**:

| Field | Default | Notes |
|---|---|---|
| **Session Lifetime** | 15 | Minutes of inactivity before a session expires. Shorter is safer; too short annoys people mid-form. |
| **Session Handler** | `none` | Where sessions are stored. The list offers whatever backends the server supports — `database`, `file`, `memcached`, `redis`, `apc` and so on. `database` is the usual choice. |

**Cookie Settings** are on the **Site** tab, and hold **Cookie Domain** and
**Cookie Path**. Leave both empty unless the hub genuinely shares a session
with a sibling host — widening the cookie domain widens who receives the
session cookie.

## Text filters

**Global Configuration** > **Text Filters** sets, per access group, what
HTML a member may submit through an editor field. Each group gets one of:

| Option | Effect |
|---|---|
| **Default Black List** | Strips the tags and attributes commonly used in attacks. This is the shipped behaviour. |
| **Custom Black List** | Strips the tags and attributes you list, instead of the default set. |
| **White List** | Strips everything except the tags and attributes you list. |
| **No HTML** | Strips all HTML. |
| **No Filtering** | Submits the markup untouched. |

**No Filtering** should be reserved for groups whose members you would trust
with shell access, because it is equivalent. The filter applies to editor
fields; it is not a substitute for a component escaping its own output.

## Login thresholds

**Users** > **Members** > **Options** > **Login Settings** limits how often
one account may fail to log in, how often one account may request a
password reset, and how many accounts may be blocked from one address before
the hub asks Fail2Ban to ban it.

The fields, their defaults, and what the code actually counts are described
in [CMS-controlled Fail2Ban jail](cmscontrolled_fail2ban_jail.md). The
parameters themselves are listed in the
[Members configuration reference](../../../reference/configuration/components/members.md#login).

## Password rules

**Users** > **Members** > **Passwords** holds two screens.

**Password Rules** is an ordered list. The list shows **Id**, **Rule**,
**Description**, **Ordering** and **Enabled**; opening a rule adds
**Value**, **Failure message**, **Group** and **Class**. The **Group** field
scopes a rule to one access group, so you can demand more of administrators
than of ordinary members. The toolbar carries **Restore Defaults**, which
replaces the whole list with the shipped set.

**Password Blacklist** is a list of words a password may not contain.

Enable **System - Password** as well. It watches every request from a signed
in member and, if their stored password no longer satisfies the rules or has
expired, diverts them to the change-password screen until they fix it. A
short list of tasks — logging out, submitting a support ticket, saving the
new password — is exempt so the member is not trapped.

The hashing mechanism itself is set under **Options** > **Password** and
defaults to `sha512`.

## Second authentication factors

Two plugins in the `authfactors` group ship with 2.4:

| Plugin | Second factor |
|---|---|
| **Authfactors - Certificate** | A client-side SSL certificate. |
| **Authfactors - Google** | A time-based one-time code from an authenticator app. |

Neither has any parameters of its own. Which interfaces demand a second
factor is set on **System - Authfactors**, whose **Clients** parameter is a
pair of checkboxes for the site and the administrator interface; only the
administrator interface is ticked by default. If **System - Authfactors** is
disabled, no second factor is ever asked for however the `authfactors`
plugins are set.

## Security headers

Two system plugins add response headers. Both ship disabled.

**System - Content Security Policy** sets `Content-Security-Policy`,
`Content-Security-Policy-Report-Only`, or both, depending on its **Mode**
parameter. It writes eleven directives from its own parameters:
`base-uri`, `object-src`, `child-src`, `connect-src`, `default-src`,
`font-src`, `form-action`, `frame-src`, `img-src`, `script-src` and
`style-src`. The token `{host}` in any value is replaced with the request's
host name, and a `report-uri` is appended when one is set. Start in
**Report Only** and read the reports before you enforce: the shipped
`script-src` default still allows `'unsafe-inline'` and `'unsafe-eval'`,
and tightening it will break screens until they are cleaned up.

**System - Referrer Policy** sets `Referrer-Policy`. Its default is
`same-origin`.

Other headers — HSTS, `X-Content-Type-Options`, `X-Frame-Options` — are not
set by the CMS. Add them at the web server.

## Spam

The `antispam` plugin group and the **Content - Antispam** plugin that
drives it have their own chapter: [Spam](../../11-spam.md).

## Rate limiting

Hubzero rate-limits the **API only**, per application token, over a short
and a long window. The limits live in the application's own
`rate_limit` configuration and have no field in **Global Configuration** —
the manifest declares one, but no screen renders it. Site page requests are
not rate limited by the CMS; do that at the web server.

## Scan what you add

A web application scanner looks for the mistakes PHP applications make:
cross-site scripting, cross-site request forgery, SQL injection, and the
rest. Scan any component you write yourself, any third-party component, and
any component from the Hubzero release that you have modified — a local
change can introduce a hole the release does not have. Free scanners are
adequate for this; Purdue uses AppScan.

If you find a vulnerability in the Hubzero release itself, report it to the
project rather than filing it publicly.
