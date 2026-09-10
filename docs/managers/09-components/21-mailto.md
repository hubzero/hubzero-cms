<!--
status: rewritten
reviewed-against: 2.4-main @ 009ec973b7
reviewed: 2026-09-10
screenshots: none
-->
# Mailto

Mailto is the "email this link to a friend" popup. It has no administrator
interface — no screen, no menu entry, no options, no permission rules — so
there is nothing here for a manager to configure. Its `admin/` directory
contains a single language file naming the component and nothing else. This
chapter exists to say what the component is and how to switch it off.

## Where it appears

One thing on a hub links to it: the email icon in the article layouts of the
articles component, drawn when **Show Email Icon** is on. That option
defaults to **Show** in the Articles component's own options, and a menu item,
a category or an individual article can override it either way. No other
component offers it — resources, publications, wiki pages and groups each have
their own sharing links, and none of them route through Mailto.

The component is enabled on a plain installation. An installation seeded with
the sample data disables it outright, along with banners, contacts, weblinks
and wrapper.

Enabled with the icon showing is the shipped default, and on most hubs it is
the wrong one — see [What it does](#what-it-does) for why. It is also close
to harmless: on a hub that uses resources, publications and groups rather than
articles, the icon appears on nothing and nobody ever reaches the form.
Switching it off costs one setting and is entirely reversible.

## What it does

Clicking the icon opens a small popup window with four fields — **Email to**,
**Sender**, **Your Email** and **Subject** — and a **Send** button. For a
signed-in member the sender name and address are filled in from the profile.
**Send** mails the recipient one fixed sentence naming the site, the sender and
the link.

The link itself is never taken from the form. When the icon is drawn, the URL
is hashed and the hash stored in the visitor's session; the form carries only
the hash, and the send step looks the URL back up and refuses it if it is not
a local one. The four fields are scanned for injected mail headers, and a form
submitted less than twenty seconds after it was drawn is refused with *Email
could not be sent*. Between them these make the form hard to use as an open
relay, which is the usual fate of a send-to-a-friend form.

The mail goes out **from the address the visitor typed**, not from the hub's
own **From email**. On a hub whose outgoing mail is SPF- or DMARC-protected,
that is a good reason to leave the feature off: the message either fails
authentication at the recipient or is delivered as the hub apparently
impersonating a stranger.

## Turning it off

Set **Show Email Icon** to **Hide** in the Articles component's options — see
[Article Manager](../08-content/articlemanager.md) — and check that no menu
item, category or article overrides it back. That removes the icon, which is
the tidy way.

The component can also be disabled outright: **Extensions → Extension Manager
→ Manage**, find *Mailto*, and unpublish it. It is a protected extension but
the status toggle still works on it. With the component disabled the icon is
still drawn, and clicking it produces a 404 — so if you disable it, hide the
icon too.
