<!--
status: rewritten
reviewed-against: 2.4-main @ be0bd4c772
reviewed: 2026-09-10
screenshots: none
-->
# Messages

Two unrelated things on a hub are called messaging, and this chapter is about
the smaller and less useful of them. Read the next section before you go
looking for a screen.

## Which messaging is which

**Hub notifications and member-to-member messages** are handled by the
framework's message system, which writes to the `#__xmessage*` tables. This is
what members mean by "messages": the **Messages** tab on a member's profile,
with its Inbox, Archive, Trash, Sent and Settings, and everything the hub
sends them — a reply on a support ticket, a group invitation, a wish list
change, a project notice. Components send those by firing
`xmessage.onSendMessage`, and the **XMessage** plugins (**Email**,
**Internal**, **Handler**) decide how each one is delivered. What the member
does not want by email, they can switch to on-site delivery only, per
notification type, from that tab's **Settings**.

Nothing in this chapter touches that system. It is configured through:

- **Extensions > Plugins**, `members` folder, **Members - Messages**, which
  puts the tab on the profile and sets the **Default Method** for a member who
  has never chosen — Email or Internal.
- **Extensions > Plugins**, `xmessage` folder, where **XMessage - Handler**
  carries the rate limits (**time_limit**, **daily_limit**) that stop a
  runaway component from flooding a mailbox.
- The **Messaging** tab on a member's record; see
  [Members](../users/members/README.md).

**The Messages component**, documented below, is a separate, administrator-only
private-message inbox using the `#__messages` table. It exists so that one
administrator can send another a note inside the administrator interface. It
has nothing to do with member messaging, sends nothing to the site, and is
inherited from the upstream CMS rather than written for Hubzero.

## Whether you can reach it

Probably not. The component's install migration deliberately creates no entry
in the administrator's **Components** menu, and nothing else in the tree links
to it: it is reachable only by typing

```
/administrator/index.php?option=com_messages
```

Given that, treat this component as dormant. It is not a supported way to
communicate with hub members, and nothing in the site depends on it.

## The screens

If you do open it, there are two.

### Messages

A list of the messages sent to *you* — the list is filtered to the logged-in
administrator, so two administrators never see the same rows. Columns are
**Subject**, which opens the message, **Read**, **From**, and the date. The
state icon in the **Read** column toggles a message between read and unread.

Above the list, a search box matching the subject or body, and a select
filtering by **Read**, **Unread** or **Trashed**.

The toolbar offers **New Private Message**, **Mark As Read**, **Mark as
Unread**, **Trash**, **My Settings**, **Options** and **Help**. Two of these
do nothing:

- **Trash** posts a task the controller does not implement, so it silently
  redraws the list. There is no trash state and no way to reach one; the
  **Trashed** filter therefore never matches anything.
- **Empty Trash**, which replaces **Trash** when the **Trashed** filter is
  selected, does delete, but as the filter cannot select any rows it never has
  anything to delete. **Delete** is permanent when it does run.

Selecting **Unread** in the filter shows both read and unread messages: the
controller treats the value 0 as "no filter".

### Writing a message

**New Private Message** opens a form with a **Recipient** picker, a
**Subject** and a **Message** body, and a **Send** button. The recipient
picker is the standard member autocompleter.

Sending stores the message and, if the sender's mail setting says so, emails
the recipient a note telling them to log in and read it. Only administrators
who can reach the administrator interface can be sent one, because only they
can read it.

Reading a message opens it in a read-only view with a **Reply** button, which
opens a blank compose form — it does not quote or address the original.

### My Settings

The **My Settings** toolbar button opens a small pop-up with three
per-administrator settings, stored in `#__messages_cfg`:

| Setting | What it is meant to do |
|---|---|
| Lock Inbox | Refuse new messages. |
| Email New Messages | Email you when a message arrives. |
| Auto-purge Messages (days) | Delete messages after this many days. |

> **Warning:** None of the three has any effect. **Lock Inbox** and
> **Auto-purge Messages** are stored and never read by anything. **Email New
> Messages** is read, but from the component's own options rather than from
> your saved setting, and the component has no such option — so the check
> always passes and the notification email is always sent.

## Options

The component's configuration has only a **Permissions** tab; there are no
settings. `config/access.xml` declares Configure, Access Administration
Interface, Create, Delete and Edit State. Access Administration Interface is
what admits you to the screens.

## What a manager should do

Nothing. Leave the component installed — removing it is not supported and
nothing gains from it — and do member communication through the systems that
members can actually see: the notification settings described at the top of
this chapter, and [Newsletters](newsletters.md) for anything sent to many
people at once.
