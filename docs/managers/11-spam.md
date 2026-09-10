<!--
status: rewritten
reviewed-against: 2.4-main @ 35f103b1b3
reviewed: 2026-09-10
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

A hub behind a single-sign-on where every account belongs to a named person
at a known institution rarely needs any of this. A hub that lets anyone on
the internet register and post — which is most public hubs — will need it
sooner or later, usually the week after a paper cites the hub and the address
starts appearing in scraped lists. Turning the filters on before that happens
costs nothing; turning them on during it means learning the screens while the
queue grows.

Nothing here is hard to undo. Every plugin on this page is a **Status**
toggle in the Plug-in Manager, and turning one off stops it immediately. The
irreversible actions are the ones at the end — deleting content and deleting
accounts.

## When a wave arrives

Read this part first if something is happening now. It assumes a public hub
that has just started collecting a few dozen junk forum posts and profile
pages an hour.

Work in this order. The first three steps stop the bleeding; the rest is
clean-up.

1. **Turn the filters on.** **Extensions → Plug-in Manager**. Enable
   **Content - Antispam** first — without it nothing is checked, however many
   detectors you enable. Then enable **Antispam - Black List** and
   **Antispam - Link Rife**: they are the two that work immediately, with no
   account anywhere and no training. See
   [The detectors that ship](#the-detectors-that-ship).
2. **Turn on the jail.** Enable **System - Spamjail**. Refusing a post does
   not stop a script retrying; the jail does. A member is jailed on the sixth
   refusal in one session or the eleventh over the account's life, and lands
   on a "spam detected" page whatever they ask for.
3. **Check that it fires on the actual text.** **Components → Support →
   Abuse → Spam Check**. Paste one of the junk posts into **Sample Content**
   and select **Check**. The right-hand panel names every detector that ran
   and what it decided. If they all say ham, the shipped word list does not
   match this spammer — add words from the sample to **Bad words** on
   **Antispam - Black List** and check again.
4. **Deal with the accounts.** **Users → Members**, tick the offending rows,
   and select **Block**. **Block** works on a whole selection at once, which
   is why it comes before deleting anything. The full per-account procedure,
   which matters when the profile *is* the spam, is under
   [Dealing with a spam account](#dealing-with-a-spam-account).
5. **Clear the reported content.** **Components → Support → Abuse** lists
   what members have reported. Open each one and choose **Remove as Spam**
   rather than **Delete item**, so the trainable detectors learn from it.
6. **Then think about the account-level filters.** If accounts are being
   created faster than you can block them, the problem is registration, not
   content: a CAPTCHA on the registration form is the lever, and it is in
   [Integrations](02-advancedsetup.md#recaptcha) and
   [Registration](05-configuring/02-registration.md).

> **Warning:** Step 5 emails the content's author every time you release or
> remove an item. Block the account first, or the spammer receives a message
> per deletion.

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

Three of them work the moment you enable them: **Black List**, **Link Rife**
and **Baba Ji Spam Detector**. Two need something outside the hub before they
do anything — **Akismet** needs an API key from Akismet, and **SpamAssassin**
needs a SpamAssassin your web server can reach, either a local `spamd` or a
remote filter service. **Bayesian Filter** needs neither, but it decides
nothing useful until it has been trained on content the hub has taken down.

The two that need an account fail quietly rather than loudly. Enable
**Akismet** without a key and it registers no detector at all: the Plug-in
Manager shows it enabled, the **Spam Check** report does not list it, and
nothing tells you why. If a detector you enabled does not appear on that
report, it is inert.

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

## Two filters this page is not about

Managers hunting for a spam setting find these and change them expecting the
antispam plugins to react. Neither has anything to do with the `antispam`
group.

**Support's own ticket filter.** The Support component's **Options** carry an
**Antispam** tab — the name is the trap — holding an **IP Blacklist** and a
**Bad words** list of its own, with a much longer shipped word list than the
Black List plugin's. They are read by the support ticket form and nowhere
else, and only for a submitter who is not signed in: a ticket from a member
goes straight through. The same check
refuses a ticket containing five or more occurrences of `http://` — which
misses `https://` entirely, so it catches almost nothing on the modern web.
Recorded with the project.

**The registration CAPTCHA.** A CAPTCHA stops a script creating accounts. It
does nothing about a human who registers and then posts advertising, and
nothing about an account created before you turned it on. It is in
[Integrations](02-advancedsetup.md#recaptcha).

## Honeypots

A few forms — new questions in Answers, project setup, event submission —
carry a hidden honeypot field and an encrypted timestamp. A submission is
rejected if the hidden field comes back filled in, or if the form was
submitted faster than a human could have typed it. This runs whether or not
any antispam plugin is enabled, and has nothing to configure.
