<!--
status: generated
source: core/components/com_support/config/config.xml
-->

# Support (com_support)

Parameters from [`core/components/com_support/config/config.xml`](../../../../core/components/com_support/config/config.xml), as shown on the component's **Options** screen in the administrator interface.

## Basic

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `group` | Group | text | — | Group ID to pull users from |
| `email_processing` | Enable email interface | list | `0 (No)` | Allow for processing of responses via e-mail to ticketting system. Make sure /etc/hubmail_gw.conf exists. Options: `0` No, `1` Yes. |
| `emails` | Notify when ticket created | textarea | `{config.mailfrom}` | A comma-separated list of email addresses a new ticket should be sent to. |
| `email_terse` | Email Content | list | `0 (Detailed)` | Determines if any ticket details should be allowed in emails or not. If set to TERSE, emails will only contain a notification that the ticket has been updated and not contain any further details. Options: `0` Detailed, `1` Terse (no ticket details). |

## Abuse

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `abuse_notify` | Send notification when report created | list | `1 (Yes)` | Send an email notification when an abuse report is created?. Options: `0` No, `1` Yes. |
| `abuse_emails` | Notify when abuse report created | textarea | `{config.mailfrom}` | Add a line for each email address a new ticket should be sent to. |

## Files

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `webpath` | Upload path | text | `/site/tickets` | File path for attachments |
| `maxAllowed` | Max upload | text | — | Maximum upload file size in bytes. Leave blank to inherit the Media Manager's settings. |
| `file_ext` | Extensions | textarea | — | Allowed file types. Leave blank to inherit the Media Manager's settings. |

## Spam

| Parameter | Label | Type | Default | Description |
|---|---|---|---|---|
| `blacklist` | IP Blacklist | textarea | — | A comma-separated list of IPs to be blacklisted |
| `badwords` | Bad words | textarea | `viagra, pharmacy, xanax, phentermine, dating, ringtones, tramadol, hydrocodone, levitra, ambien, vicodin, fioricet, diazepam, cash advance, free online, online gambling, online prescriptions, debt consolidation, baccarat, loan, slots, credit, mortgage, casino, slot, texas holdem, teen nude, orgasm, gay, fuck, crap, shit, asshole, cunt, fucker, fuckers, motherfucker, fucking, milf, cocksucker, porno, videosex, sperm, hentai, internet gambling, kasino, kasinos, poker, lottery, texas hold em, texas holdem, fisting` | A comma-separated list of spam words |
