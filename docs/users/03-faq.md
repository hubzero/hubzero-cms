<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
screenshots: ok
source: https://help.hubzero.org/documentation/240/users/fqas
source-id: 3293
modified: 2014-11-07
imported: 2026-09-09
-->
# Frequently asked questions

Short answers to the things members ask most often. Where an answer belongs to
a bigger subject, it links to the chapter that covers it properly.

The first three are about getting in, because that is where most people get
stuck; the rest are about writing and posting.

## I registered, but I cannot log in. Why?

Almost always one of two gates, in this order:

1. **The email address is not confirmed.** Unless the hub has switched
   confirmation off, the account exists but cannot be used until you follow the
   link in the message the hub sent when you registered. Check the spam folder;
   the message is generated automatically and filters often catch it. If it is
   not there, log in anyway: you reach a page headed *Email Address
   Unconfirmed* that offers to send a new confirmation message to the address
   on the account.
2. **An administrator has not approved the account yet.** Some hubs require
   this as well, and it happens after you confirm. Logging in reaches a page
   headed *Pending Approval*, which carries a link to raise a support ticket if
   you think it is wrong. Where the hub has the approval notice switched on, it
   emails you when the account is approved. Nothing you can do speeds it up.

Not every hub uses either gate. On a hub with both switched off, the account
works the moment you create it. [Registration](20-registration.md#what-happens-next)
sets out the possible sequences.

If the login form rejects the password rather than the account, see the next
answer.

## I have forgotten my password, or lost my username. What now?

Both links sit under the login form.

**Forgot password?** goes to `/members/reset`. Type your username or your email
address; the hub mails you a verification code, you type the code into the next
form, and then you choose a new password. The new password has to satisfy the
same rules the registration form applied.

**Lost username?** goes to `/members/remind`. Type the email address on the
account and the hub mails the username to it.

Two things to know:

- There is a limit on how often you may ask for a reset. Past it the page says
  *Sorry, you have exceeded your reset request limit. Please wait and try again
  later.* Wait rather than retrying.
- If you only ever sign in through an outside service — Google, ORCID, Globus
  and others are possible — there may be no hub password to reset. Set one from
  the **Account** tab of your member area, which is also where you link and
  unlink those services.

## Who can see my profile?

That depends on two settings you control, and the outer one wins: a toggle at
the top of the **Profile** tab makes the whole profile public or private, and,
while it is public, each field has its own **Privacy** menu — public,
registered members only, or you alone. While the profile is private, the
per-field menus are disabled entirely.

Which state your account started in depends on the hub, not on the software, so
check the toggle rather than assuming. [Member profile](15-profile.md#privacy)
covers the decision in full.

A private profile also means other members cannot find you when they go to
invite you to a group or add you to a project.

## How do I change my email address?

1. Log in and go to your member area at `/members/myaccount`.
2. Select the **Profile** tab.
3. Find the **E-mail** row and select **Edit** beside it.
4. Type the new address.
5. Select **Save**.

> **Important:** Changing your email address un-confirms your account. The hub
> sends a confirmation message to the new address, and you have to follow the
> link in it before the account works again. Do not change the address to one
> you cannot read.

Usernames, by contrast, cannot be changed at all. If yours is genuinely a
problem, raise a support ticket.

## What is CKEditor?

CKEditor is the rich-text editor a hub uses wherever you write content — blog
entries, wiki pages, publication abstracts, forum posts, and the
administrator's own screens. It gives you the usual word-processor controls:
headings, bold and italic, lists, links, tables, and images.

![The CKEditor toolbar](media/faq-ckeditor.png)

Two of its buttons are specific to a hub:

- **Source** switches between the formatted view and the underlying HTML.
- **Add Macro** inserts a hub macro — a short instruction in double square
  brackets that the page expands when it is displayed. `[[Video(…)]]`,
  `[[Image(…)]]`, and `[[Link(…)]]` are macros. Selecting the button lists the
  ones your hub has, with the arguments each one takes.

It deliberately offers less than a desktop word processor. Font choices and
colours are left out so that content stays consistent with the site's design.

> **Note:** Which editor a hub uses is a setting. CKEditor is the usual choice
> and the default, but a hub can run a different one, or none at all, in which
> case the toolbar looks different or is absent and you write plain text or
> wiki markup. Macros work either way — they are expanded when the page is
> displayed, not by the editor.

## How do I embed a video?

For a video hosted elsewhere — YouTube, Vimeo, or Kaltura — you do not need to
upload anything. Put the video's URL inside the `Video` macro:

```text
[[Video(https://www.youtube.com/watch?v=FgfGOEpZEOw)]]
```

The macro takes an optional size, either named or positional:

```text
[[Video(https://www.youtube.com/watch?v=FgfGOEpZEOw, width=600, height=338)]]
[[Video(MyVideo.mp4, 640, 380)]]
```

For a video file of your own, upload it first. Wherever the hub gives you an
editor next to an **Uploaded files** area — a blog entry, for instance — you
can attach a file and then refer to it by name:

![Embedding an uploaded MP4](media/faq-embedingmp4.png)

1. Open the page you are writing.
2. Under **Uploaded files**, select **Choose File**, pick the `.mp4` from your
   computer, and select **Upload**. It appears in the list above.
3. Put the cursor where the video should go and select **Add Macro**, or type
   the macro yourself: `[[Video(MyVideo.mp4)]]`, using the name exactly as it
   appears in the file list.
4. Save the page. The macro is replaced by a player when the page is shown; it
   stays as text while you are editing.

## Why was my post rejected as spam?

Hubs run a set of antispam checks over anything you submit. A post can be
rejected for having too many links in it, for linking to a domain on a public
blocklist, or for matching a filter the hub maintains. Rewording the post,
or dropping the links, usually gets it through.

This catches ordinary members more often than you would expect. A first forum
post that lists six references, each a bare URL, looks exactly like a spam
post to a link-counting filter. Writing the references out and linking only the
DOI usually gets the same post through.

If it keeps happening, the hub can put an account in "spam jail", which
replaces every page with a notice reading *We've detected spam-like behavior
from your account.* That is not permanent and it is not an accusation — raise
a support ticket and ask for it to be lifted.

> **Note:** The blocklists a hub consults, Spamhaus among them, are checked
> against the **domains you link to**, not against your own address. If you
> believe your own network is wrongly listed, that is a matter between you and
> the list operator; Spamhaus's own lookup is at
> <https://check.spamhaus.org/>. Nothing on the hub can remove an entry from
> it.

## Something else is wrong

Everything a hub's own staff can fix goes through [Support](12-support.md):
quota increases, an account stuck in one of the states above, a group you
cannot get into. If what you want is a feature the hub does not have, that is
the [Wishlist](29-wishlist.md) instead.
