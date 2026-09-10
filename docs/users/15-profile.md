<!--
status: rewritten
reviewed-against: 2.4-main @ 1924c22171
reviewed: 2026-09-09
screenshots: ok
source: https://help.hubzero.org/documentation/240/users/profile
source-id: 3312
modified: 2013-02-15
imported: 2026-09-09
-->
# Member profile

Your profile is what other members see when they look you up. It is one tab of
your member area at `/members/myaccount`; the others — Dashboard, Account,
Groups, Projects, Usage and so on — are listed down the left-hand side, and
which of them exist depends on which Members plugins your hub has enabled.

## Editing your profile

![The profile fields, each with an Edit link](media/profile-member-profile-1.png)

The profile is a list of rows, one per field, each showing a label and its
current value. Editing happens in place:

1. Open the **Profile** tab of your member area.
2. Select **Edit** at the right of the row you want to change. The row opens
   to reveal the field's inputs and, beside them, a **Privacy** menu.
3. Change the value.
4. Select **Save**. **Cancel** closes the row and discards the change.

Errors come back into the same row: a missing required value or a rejected one
is reported above the inputs.

Which rows exist is up to your hub. Name, username and email are always there;
everything else — organisation, telephone, address, biography, interests,
ORCID, web site — comes from the profile form the hub's administrators built.

Two rows behave differently:

- A field an administrator has marked read-only shows a notice instead of
  inputs: *The "…" profile field has been marked as read-only by a HUB
  administrator.* Ask support if it is wrong.
- Passwords are not edited here. Use the **Account** tab, which also lists the
  external services linked to your account and lets you set a local password
  and manage an SSH public key.

Above the rows is a **Profile Completeness** meter. On hubs that run
incremental registration, filling in fields can earn points.

> **Note:** If your hub has since made a field compulsory that was optional
> when you registered, the profile opens with those fields listed under
> *You must update your profile before continuing* and holds you there until
> they are answered.

## Privacy

Privacy works at two levels, and the outer one wins.

**The whole profile.** At the top of the Profile tab is a toggle reading
*Public Profile :: Click here to set your profile private.* or *Private
Profile :: Click here to set your profile public.* Selecting it flips the
state immediately. A private profile cannot be opened by other members at all.

**Individual fields.** While the profile is public, each field's **Privacy**
menu offers three settings:

| Setting | Who can see the value |
|---|---|
| **Public (anyone can see)** | Everyone, including visitors who are not logged in. |
| **Registered users (only logged in members can see)** | Anyone logged in to the hub. |
| **Private (only you can see)** | Only you. |

While the profile as a whole is private, those menus are replaced by the
message *Account must be public to set access on individual profile fields.*
Set the profile public first if you want per-field control.

The state a new account starts in comes from the hub's **Default Privacy**
setting, not from a fixed default in the software, so it differs between hubs.
Check the toggle after you register rather than assuming.

> **Note:** Declining a new version of the Terms of Use sets your profile
> private and logs you out. Agreeing again does not put it back; you have to
> set it public yourself.

## Your profile picture

New accounts get a default silhouette. To replace it:

1. Open the **Profile** tab. The picture control only appears there, even
   though the picture itself is shown on every tab.
2. Move the pointer over your picture and select the **Change Picture** band
   that appears over it. A pop-up opens with the current picture on the left
   and an upload area on the right.
3. Select **Upload an Image**, or drag a file onto the area. The file uploads
   as soon as it is chosen — there is no separate save step. When it finishes,
   the preview and every copy of your picture on the page are replaced and the
   pop-up closes.
4. To go back to the silhouette, select **[Remove Picture]**. The link only
   appears when you have a picture of your own.
