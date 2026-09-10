<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/22/security_considerations
source-id: 2825
modified: 2025-01-31
imported: 2026-09-09
merged-from: 2.2
source-state: unpublished
-->
# Security considerations

A hub is a public web application on a public server, so its security has
two halves. The operating system, the web server, and the services around
them are hardened by the system administrator with tools that are not part
of this repository. The CMS has its own settings that decide how sessions
and cookies behave, what HTML members may submit, how quickly a brute-force
attempt is throttled, and how spam is caught. This section covers both, and
says plainly which is which.

## In this section

- [Operating system hardening](hardeningguide.md) — what the people who run
  the Purdue hubs do to the host underneath a hub. All of it is external to
  the CMS.
- [Hardening the CMS](hardencms/README.md) — the settings and extensions in
  the administrator interface that affect the hub's own security posture.
- [CMS-controlled Fail2Ban jail](hardencms/cmscontrolled_fail2ban_jail.md) —
  how the login thresholds under **Users** > **Members** > **Options** work,
  and how the CMS hands an address to Fail2Ban.

Spam has its own chapter: [Spam](../spam.md).

## What the CMS gives you

| Area | Where |
|---|---|
| Force HTTPS | **Site** > **Global Configuration** > **Server** > **Force SSL** |
| Session lifetime and handler | **Site** > **Global Configuration** > **System** > **Session Settings** |
| Cookie domain and path | **Site** > **Global Configuration** > **Site** > **Cookie Settings** |
| What HTML each access group may submit | **Site** > **Global Configuration** > **Text Filters** |
| Failed login and password-reset thresholds | **Users** > **Members** > **Options** > **Login Settings** |
| Password rules and password blacklist | **Users** > **Members** > **Passwords** |
| Second authentication factors | **Extensions** > **Plug-in Manager**, the `authfactors` group |
| Content-Security-Policy header | **Extensions** > **Plug-in Manager** > **System - Content Security Policy** |
| Spam detection | **Extensions** > **Plug-in Manager**, the `antispam` group |
| Upload virus scanning | The `virus_scanner` key in `configuration.php` |

Every parameter behind these screens is listed in the generated
[configuration reference](../../reference/configuration/README.md).

## Reporting a vulnerability

If you find a vulnerability in the Hubzero release itself, report it to the
project rather than filing it in a public tracker.
