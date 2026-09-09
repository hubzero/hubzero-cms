<!--
status: rewritten
reviewed-against: 2.4-main @ d48e29db14
reviewed: 2026-09-09
source: https://help.hubzero.org/documentation/240/users/publications/curation
-->
# Curation

Curation is the review a publication goes through between the moment its
authors submit it and the moment it goes live. A curator reads every part of
the draft, marks each one as acceptable or as needing work, and either
approves the publication or sends it back. This chapter covers both sides:
reviewing as a curator, and answering a review as an author. Submitting a
draft in the first place is covered in [Publications](README.md).

## Who can curate

Curation is open to members of the hub's curators group and to site
administrators. Where a publication type names a curators group of its own,
its members see only publications of that type. Anyone else can still be
**assigned** to a single publication, and then sees only that one.

You have to be logged in. Visiting the curation pages as a guest gives you
*Please login to access publication curation*; a logged-in member with no
curation rights gets *We are sorry. You are not authorized for publication
curation.*

## The curation list

Go to `/publications/curation`. The page opens with *Below is a list of
publications you are authorized to curate.* and two filters, **All** and
**Assigned to me**.

The table lists every submission waiting on someone, with columns **ID**, a
thumbnail, **Title**, the version label, **Content** (the publication type),
**Submitted**, and **Status**. Click any heading to sort by it; the default
is newest submission first. **Status** is either *pending curator review* or
*pending author changes*, and the submitted column says *Submitted* or
*Re-submitted*, with the date and the name of the person who sent it.

Each row ends with up to three controls:

| Control | What it does |
|---|---|
| **Assign** | Opens **Assign a Curator**. Type a name and pick the member from the drop-down, then **Save**. Once someone is assigned the cell reads *Assigned to <name>*; clicking the name reopens the box to **Change assignment**. |
| **Review** | Opens the review screen. It appears only while the publication is pending curator review. |
| **History** | Opens **Curation History**, a dated log of every review action and author reply, each entry marked *curator* or *author*. |

A small icon beside those opens the publication's public page in a new view.

> **Note:** Only one curator is assigned at a time, and assigning someone
> emails them a request to review. Assignment is not required — anyone with
> curation rights can review anything they can see — but a member who is
> only assigned, and is in no curators group, sees nothing else.

## Reviewing a publication

The review screen shows the publication's type, title and version, when it
was submitted and by whom, who it is assigned to, and any **Submitter
comment(s)** the authors left. Under that: *Review all items below and check
them off as complete or requiring changes.*

Each part of the draft — content, description, authors, license, tags,
citations, notes — appears as its own block, with the curator instructions
the administrator wrote for that block beside it, and a checker with four
states:

| State | Meaning |
|---|---|
| not reviewed yet | You have not judged this item. |
| looks good | You have accepted it. |
| changes required | You have asked for a change. |
| item updated, needs review | The authors changed it after your review; look again. |

Choose **looks good** to accept an item. Choose **changes required** and a
**Request changes** box opens: *Explain to authors what needs to change*.
Write the explanation and press **Request changes**. Where the authors have
already answered an earlier request, their reply appears on the item as
*Author comment:*, and an item they were let off shows *Requirement
skipped*.

Two buttons at the top act on the whole submission:

- **Approve publication** publishes the version. The hub stamps the accepted
  date, freezes the type's manifest onto the version so the record cannot
  drift, updates or registers the DOI with the identifier service, adds the
  publication to the authors' ORCID records where they have connected one,
  and — where the hub archives to a trusted repository and no grace period is
  set — copies the version into archival storage. You get *Publication has
  been approved to be published*, and the authors are emailed *Your
  publication has been approved. You can view it live at …*
- **Kick back to authors** sets the version to *pending author changes* and
  returns it to them. You get *Publication sent back to authors for changes*,
  and they are emailed *Administrator has reviewed your submitted publication
  and requested changes.*

> **Note:** Both buttons stay greyed out until every item carries a mark,
> and only one of them lights up: **Approve publication** when everything
> passed, **Kick back to authors** when at least one item still needs work.
> An item the authors have changed since you looked reverts to *item
> updated, needs review* and locks the buttons again until you re-check it.

## Answering a review as an author

When a publication comes back, its version status is *Changes required*.
Open it from the project's **Publications** area, or follow the link in the
email, and press **Make changes**.

The panel bar shows which parts need work. Open one and you see the curator's
explanation. You have two ways to clear it:

- Make the change the curator asked for.
- Press **Dispute this** under *Don't agree with curator?* This opens
  **Dispute curator's request for changes**, where you explain why you do not
  wish to make the change. Where the curator asked you to meet a requirement
  you cannot, **Request to skip requirement** does the same thing for that.

Every flagged item has to be either changed or disputed before you can
re-submit. Once they all are, **OK to submit** appears in the version bar.
Follow it to the review panel, check the publishing settings and the Terms of
Deposit box again, and press **Re-submit draft to be published**.

The publication goes back to *pending curator review*, marked as
re-submitted, and the curator sees which items you changed and which you
disputed. Your changes and their comments both land in the **History**, so
either side can read the whole exchange later.

## Skipping review

Some hubs approve some submissions automatically: a publication type can be
set to auto-approve, and the hub can name particular members whose
submissions never wait. In those cases the review panel offers the tick box
*I would like for this publication to be reviewed instead of automatically
being published*, so you can ask for a curator anyway.
