<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/newsletters
-->
# Newsletters

A hub's newsletter carries news, announcements, and highlights of new
content. Past issues are readable on the hub at `/newsletter`, and you can
have new ones mailed to you by joining a mailing list. You do not need an
account to read them or, on most hubs, to subscribe.

## Reading a newsletter

Go to `/newsletter`. The page shows the most recent public issue, with
**Past Newsletters & Updates** listing the others down the side. Click one to
read it; its address is `https://<your hub>/newsletter/<alias>`, which you
can share.

Two buttons sit at the top of the page. **Save as PDF** turns the issue you
are reading into a PDF and downloads it. **Subscribe To Mailing Lists**
opens the subscription page described below. A **Newsletter Help** link in
the sidebar explains how the hub tracks newsletter opens and clicks.

> **Note:** Only issues the hub has marked public appear here. Newsletters
> sent only by email — plain-text ones especially — are often hidden, so what
> arrives in your inbox may not be on the site.

> **Note:** Making a PDF depends on a converter installed on the hub. Where
> the hub does not have one, the button reports that the newsletter could not
> be output as a PDF.

## Subscribing to a mailing list

Press **Subscribe To Mailing Lists**, or go to `/newsletter/subscribe`.

If you are not logged in, the hub first asks for an email address. Type it
and press **Continue as Guest**, or press **Login** to sign in instead. If
the address you type already belongs to an account, the hub asks you to log
in rather than letting you manage that account's subscriptions as a guest.

The subscription page has two sections. **My Mailing Lists** shows every list
your address is already on, with a checkbox each.
**Public Mailing Lists You May Be Interested In** shows the lists open to
anyone, with their descriptions. Check the ones you want, uncheck the ones
you do not, and press **Save Mailing List Subscriptions**.

Every new subscription starts unconfirmed. The hub emails you a link; until
you click it you receive nothing, and the list shows **Not Confirmed** beside
its name with a **Click here to resend confirmation email** link if the
message did not arrive. Following the link confirms the subscription
immediately.

Private lists never appear here. Someone who runs the hub has to add your
address to one.

Some hubs also put a single-list sign-up box in a page sidebar. Type your
address, submit, and confirm from the email exactly as above.

## Unsubscribing

There are three ways off a list.

- On `/newsletter/subscribe`, uncheck the list under **My Mailing Lists** and
  press **Save Mailing List Subscriptions**. The list then shows
  **Currently Unsubscribed**; re-checking it later sends a fresh confirmation
  email.
- Use the unsubscribe link at the bottom of any newsletter you were sent.
  This opens a page naming the list, asking you to confirm, and offering a
  **Reason for Unsubscribing**: too many emails, content isn't relevant to
  me, I don't remember signing up, privacy concerns, or **Other** with a box
  for your own words. Press **Unsubscribe** to finish. The reason is optional
  and is passed to the hub's staff.
- For the hub's own members list — the one every account is added to at
  registration — the unsubscribe link asks you to log in first, because
  leaving it means turning off the email preference on your profile.

> **Note:** An unsubscribe link is tied to one address and one mailing. If
> you forward a newsletter and someone else uses its link, the hub reports a
> problem with the link rather than unsubscribing them.

## Email tracking

Newsletters sent from the hub normally carry a tiny invisible image and links
that pass through the hub on their way to their destination. That is how the
hub counts how many people opened an issue and which links they followed. No
message content and no reading history is shared outside the hub. The
**Newsletter Help** link on `/newsletter` explains it in the hub's own words.

## Email preference links

Some hubs send messages containing a personal link back to an **Email
Subscriptions** page, of the form
`/newsletter/email-subscriptions?user=…&campaign=…&code=…`. The code in the
link identifies you, so the page opens without a login and lists the kinds of
message the hub sends — news and announcements, the newsletter itself, and
any topics the hub has defined. Set each one and press **Submit**.

These links expire. If yours has, the hub says the code has expired and sends
you to the front page; ask for a new message, or set the same preferences
from your [profile](15-profile.md).
