<!--
status: rewritten
reviewed-against: 2.4-main @ f22290e4e4
reviewed: 2026-09-09
screenshots: none
source: https://help.hubzero.org/documentation/240/managers/spam
source-id: 3418
modified: 2014-11-20
imported: 2026-09-09
-->
# Spam

Hubzero screens content as it is submitted. One plugin, **Content -
Antispam**, hooks the save and hands the text to every enabled plugin in the
`antispam` group; each of those decides for itself whether the text looks
like spam. A separate plugin, **System - Spamjail**, stops a member who
trips the check too often. Neither is enabled on a fresh install.

## What spam looks like

Signs a member account exists only to post spam:

- A profile picture that has nothing to do with the hub.
- A biography unrelated to the hub's subject.
- Links to unrelated commercial sites, in the profile URL or in posts.
- Advertising, promotional copy, or contact details for a business.
- Text stuffed with links in a field that rarely needs one.

## Turning the filters on

1. Go to **Extensions** > **Plug-in Manager**.
2. Enable **Content - Antispam**. Nothing is checked without it, however
   many detectors you enable.
3. Enable one or more plugins from the `antispam` group and fill in
   whatever credentials or thresholds they need.
4. Enable **System - Spamjail** if you want repeat offenders stopped rather
   than merely refused.

## How the check runs

When a member saves content — a blog post, a forum reply, a wish, an answer
— the component fires an `onContentBeforeSave` event before writing to the
database. **Content - Antispam** listens for it and:

1. Does nothing at all in the administrator interface. The check runs on
   the site only.
2. Skips Super Users, then anyone the whitelist covers.
3. Collects every enabled `antispam` plugin's detector and runs the text
   through **all of them**. It does not stop at the first hit — every
   detector reports, and the content is spam if any one of them says so.
4. On a hit, shows the **Message** from the plugin's own parameters, adds
   one to the member's session and lifetime spam counters, and refuses the
   save.

Order in the **Plug-in Manager** therefore does not decide which detector
wins; there is no winner. It only decides the order of the rows on the
**Spam Check** report.

### Content - Antispam parameters

| Parameter | Default | Effect |
|---|---|---|
| **Message** | "The submitted text was detected as possible spam or containing inappropriate content." | Shown to the member when their content is refused. |
| **Learn Spam** | Yes | Feed refused content back to the detectors that can be trained. |
| **Learn Ham** | Yes in the manifest | Feed accepted content back as known-good. |
| **Log spam** | No | Append a line to `cmsspam.log` for each hit. |
| **Access Groups** (Whitelist tab) | Disabled/Off | The only other choice is **Administrators**, which exempts anyone with `core.manage`. |
| **Usernames** (Whitelist tab) | empty | Comma-separated usernames that are never checked. |

> **Note:** **Learn Ham** defaults to Yes in the manifest but the code reads
> it with a fallback of No, so a hub that has never saved this plugin's
> parameters does not learn from accepted content. Save the plugin once to
> make the setting take effect either way.

A logged line records whether the verdict was `spam` or `ham`, the address,
the member id and username, an MD5 of the text, and the request URI. The
text itself is not written to the log.

## The detectors that ship

Six plugins in the `antispam` group. Their parameters are listed in the
[antispam plugin reference](../reference/configuration/plugins/antispam.md).

| Plugin | How it decides | Needs |
|---|---|---|
| **Antispam - Akismet** | Sends the text, the author's name, email and address to the Akismet web service. | An Akismet API key. Without one the plugin registers no detector and is silently inert. |
| **Antispam - Baba Ji Spam Detector** | Scores the text against patterns from one specific mass spammer: long international phone numbers, variants of the name "Babaji" in the text, the email address, or the username. | Nothing. |
| **Antispam - Bayesian Filter** | A Bayesian classifier trained from content the hub has taken down. **Threshold** is the probability above which text is spam; the default is `0.95`. **Train** controls whether it learns. | Nothing, but it is useless until trained. |
| **Antispam - Black List** | Matches a list of words. **Bad words** ships with a list of pharmacy spam and profanity; edit it to suit. | Nothing. |
| **Antispam - Link Rife** | Counts links. **Link Frequency** (default 10) is the number of links that is spam outright. **Link to Text Ratio** (default 40) is the percentage of links to words at or above which the text is a "link overflow". **Link validation** (default No) additionally resolves each link's host against `zen.spamhaus.org`, `multi.surbl.org` and `black.uribl.com`. | DNS lookups, if you enable link validation. |
| **Antispam - SpamAssassin** | Hands the text to SpamAssassin. **Client** chooses **local** — a `spamd` on **Hostname** and **Port**, default `localhost:783`, or a Unix socket — or **remote**, an HTTP filter service, default Postmark's. | A reachable SpamAssassin. |

> **Warning:** Enabling **Link validation** puts a DNS round trip in the
> path of every save. The blocklists it queries are free only for low
> volumes.

The Mollom plugin described in older documentation is gone. The Mollom
service shut down in 2018 and the plugin is not in 2.4.

## Testing a sample

You can run text through the enabled detectors without posting it.

1. Go to **Components** > **Support**.
2. Choose **Abuse** in the sub-navigation.
3. Choose **Spam Check** in the sub-sub-navigation.
4. Paste the text into **Sample Content** and press **Check** in the toolbar.

The right-hand panel lists every detector that ran, whether it called the
text spam or ham, and the message it gave. This is the quickest way to find
out which detector is refusing a member's post.

## Spam jail

**System - Spamjail** watches every request from a signed-in member on the
site. If the member is jailed, it redirects them to a "spam detected" page
whatever they asked for, letting through only logging out, submitting a
support ticket, and fetching profile pictures.

A member is jailed when either counter is over its limit:

| Counter | Parameter | Default |
|---|---|---|
| Spam hits this session | **Session count** | 5 |
| Spam hits over the lifetime of the account | **User count** | 10 |

Both are strict comparisons, so the defaults mean a sixth hit in one
session, or an eleventh over the account's life, puts the member in jail.
The session counter clears when the session ends. The lifetime counter does
not.

**Jail video** takes a YouTube video id; when set, the page embeds it below the
message. Use it for a short explanation of why the hub filters content.

### Releasing a member

The lifetime counter is the one an administrator has to clear.

1. Go to **Users** > **Members** and open the member.
2. On the **Account** tab find **Lifetime Spam Incidents**.
3. Press **Reset** beside it. That sets the field to zero and applies the
   change immediately.
4. Tell the member they can post again.

> **Note:** **Lifetime Spam Incidents** is only rendered when **System -
> Spamjail** is enabled. If you cannot find the field, that is why.

If a member keeps being caught, whitelist them on **Content - Antispam**
instead of resetting the counter every week: add their username to
**Usernames** on the **Whitelist** tab, or set **Access Groups** to
**Administrators** to exempt everyone who can reach the administrator
interface.

## Dealing with a spam account

Disable the account before you delete what it posted, so the owner is not
emailed about each deletion.

1. Go to **Users** > **Members** and open the account.
2. On the **Account** tab, set **Visibility** to a level the public cannot
   see. This hides a profile whose fields are themselves the spam.
3. Set **Receive email updates?** to **No**.
4. If **Email confirmed** is ticked, untick it.
5. On the **Password** tab, enter a long random string in **New Password**.
   Anything typed there resets the account's password.
6. **Save & Close**.
7. Back on the members list, tick the row and press **Block**.
8. Now find and delete the content the account posted.

Content members have reported is listed under **Components** >
**Support** > **Abuse**. Opening a report offers three actions: **Release
item**, **Remove as Spam**, and **Delete item**. **Remove as Spam** deletes
the item *and* feeds its text back to the trainable detectors as known
spam; **Delete item** only deletes it. Use the first one on real spam so
the Bayesian filter and the remote services learn from it.

## Honeypots

A few forms — new questions in Answers, project setup, event submission —
carry a hidden honeypot field and an encrypted timestamp. A submission is
rejected if the hidden field comes back filled in, or if the form was
submitted faster than a human could have typed it. This runs whether or not
any antispam plugin is enabled, and has nothing to configure.
