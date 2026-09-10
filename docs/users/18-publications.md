<!--
status: rewritten
reviewed-against: 2.4-main @ 42a7a5b5c7
reviewed: 2026-09-10
source: https://help.hubzero.org/documentation/240/users/publications
-->
# Publications

A publication is a citable release of your work — a dataset, a paper, a set
of images, a piece of software. You assemble one from files you have already
put in a project, a curator reviews it, and once it is approved the hub gives
it a permanent page and a DOI so other people can cite it. Published work
lives at `/publications` on the hub.

You publish when you need someone outside your team to be able to point at
the exact thing you used. The soil lab from [Projects](16-projects.md) has
spent a year measuring nitrate in field samples; the paper is nearly
written, and the journal wants the data available. The lab publishes the
cleaned dataset from its project, gets back a DOI, and puts that DOI in the
paper's data-availability statement. Anyone who follows it a decade later
gets the same files the paper was written from.

That last part is the whole point, and it is what makes a publication
different from everything else on the hub:

- A **project** is private and changes every day. A publication is public
  and frozen. Once a version is approved you cannot quietly edit it — you
  add a new version instead, and the old one stays reachable at its own DOI.
- A **collection** is a personal board of things you found interesting. It
  is not citable and nothing reviews it. See
  [Collections](01-collections.md).
- A **resource** page describes work. A publication *is* the work, with the
  files attached and an identifier on them.

Publishing is deliberate. Putting a file in a project does not publish it,
and nothing publishes itself.

> **Note:** A DOI is issued only where the hub holds an account with an
> identifier service and an administrator has configured it. Those settings
> ship empty. On a hub without them, publications still get a permanent page
> and a version history, but no DOI, and the `doi:` line does not appear.
> Whether a given publication type demands a DOI at all is set per type by
> an administrator. If you need a citable identifier, check with the hub's
> support staff before you submit.

## Finding a publication

The Publications home page lists **Recent Publications** and **Popular
Publications** side by side, with a **Browse Publications** button at the top
right. Each entry shows a thumbnail, the title, the publication date, the
category, and the contributors.

**Browse Publications** is where you search. Type a word or phrase into
**Enter keyword or phrase** and press **Search**. Narrow the results with the
**Category** list — pick one and press **Search** again — or click a tag in
the **Popular Tags** list beside the results. Tags stack: each one you add
appears above the results with an `x` that removes it. Sort with **Title**,
**Published**, and, where the hub shows rankings, **Ranking**.

Every publication has a permanent address you can share:
`https://<your hub>/publications/<id>`. Adding a version number reaches one
particular release, as in `/publications/42/3`.

## Reading a publication

The top of a publication page carries the title, the authors, a short summary
and the category it is listed in. To the right sits the download or launch
button and, under it, the version and license lines. The version line reads
something like *Version 1.2 - published on 12 Mar 2026* and, where the work
has a DOI, shows `doi:` followed by the identifier and a **cite this** link.

The **About** tab below holds the full description, the image gallery, the
list of what is in the publication, any extra metadata the hub collects, the
tags, and the release **Notes** for that version. Other tabs appear depending
on what the hub has enabled for that category:

| Tab | What it holds |
|---|---|
| Versions | Every release of this publication, with its DOI, so you can move between them. |
| Supporting Docs | Files that come with the work but are not the main content. |
| Citations | Works this publication references, and works that reference it. |
| Questions | Questions and answers about the publication. |
| Reviews | Star ratings and written reviews from members. |
| Usage | Download and view figures. |
| Forks | Copies other members have made of this work. |
| Wishlist | Requests for changes or additions. |

> **Note:** **Reviews** and **Questions** only appear on the most recent
> public release. Open an older version and those tabs are gone.

**Watch publication**, in the sidebar, tells the hub to email you when a new
version of that publication is released; the same button then reads **Stop
watching publication**.

## Downloading

The button at the top right of the page downloads the content. What it says
depends on what is attached: **Download** for a single file, **Download
Bundle** with the total size for several, **View publication** for a link,
**Go to data** for a database. Where the files have been packaged, **Show
bundle contents** lists what is inside, with the size and an MD5 checksum you
can use to verify what you got.

Very large bundles are built in the background. The button then reads
**Download Bundle (preparing…)** and starts the download by itself once the
package is ready. If the dataset is too big to bundle at all, the button says
**Download Bundle (unavailable)** and you download the files one at a time
from the publication page instead.

Some publications are restricted. If the content needs an account you are
told *To access the content, you need to be logged in to the site*; if it is
limited to particular groups, *Publication content is restricted to
logged-in members of limited groups*. A retired publication says *We're
sorry! The content of this version is unpublished and cannot be viewed*, and
following the DOI of a withdrawn dataset lands you on a **Tombstone Dataset**
page that says why it was retracted.

## Citing

Under **Cite this work** on the About tab the hub prints the citation in the
form it wants you to use, with the DOI as a link. Two export links follow:
**BibTex** and **EndNote**, each of which downloads a file you can import
into a reference manager.

## Publishing your own work

Everything up to here is about reading somebody else's publication. This is
the part where you make one.

You need an account, and the hub must have publishing switched on. Most work
starts in a project: go to your project, open **Publications** in its menu,
and select **Start a new publication**. Working inside a project means your
team-mates appear in the author picker, your files are already there, and
related publications share a project page.

If you only want to release a few files and do not need a project, go to
`/publications`, press **Start publishing »**, and use the buttons under
*Want to publish file(s) quickly without starting a project? There is a way.*
The hub creates a hidden project for you behind the scenes. Some hubs turn
this shortcut off, in which case only the project route is offered.

Either way you first answer **What are you going to publish?** and pick a
type — **File(s)**, **Databases**, **Series** and so on. The type decides
which panels you are asked to fill in. It is set when the draft is created
and nothing in the front end changes it afterwards, so read the descriptions
before you choose. The soil lab picks **File(s)**: a
cleaned `.csv`, a data dictionary, and the script that produced them.

### Working through the panels

A bar across the top of the draft shows every panel, and its colour tells you
whether that panel is finished. Press **Save & Continue** to move on, or
click any panel in the bar to jump to it. What you get depends on the type,
but a file publication usually asks for:

1. **Content** — the files to publish. **Add a file** opens a selector over
   your project's files; tick what you want and press **save selection**.
   Files you have not uploaded yet can be uploaded from the same selector.
   Each item in the list has a **Remove** control that takes it back out of
   the publication. Types that accept several kinds of content give primary
   files, supporting documents and gallery images their own **Add a file**
   buttons.
2. **Description** — the title, a short abstract, and the full description.
3. **Authors** — **Select author(s)** lists your project team; **Add an
   author** looks up any hub member, or takes a name, organization and email
   for someone without an account. Drag the names to change their order.
4. **Tags** — words and phrases that describe the work, plus the category it
   belongs in.
5. **License** — **Choose License** picks one, and where the license demands
   it you tick a box agreeing to its terms. Some licenses let you edit the
   text.
6. **Citations** — related works. Paste a DOI and the hub fetches the
   citation for you; otherwise enter it by hand.
7. **Notes** — what changed in this version.

### Review and submit

This is the last decision point. After this the draft leaves your hands.

The last panel is **Review**. It tells you whether the draft is complete —
*Your draft is complete and ready for submission* — or what is still missing,
and it collects the last few decisions:

- **Preview publication page**, which opens the page as it will look.
- **Publishing settings**: a **Publication date**. Leave it blank to publish
  as soon as it is approved, or set a future date to embargo the work.
- **Point of Contact** — one or more corresponding authors, which trusted
  repositories require.
- **Publish or Post?**, where the type allows a choice. **Publish draft**
  issues a DOI and freezes most of the record. **Post draft** issues no DOI,
  keeps the publication editable, and leaves the page out of search, reachable
  only by its direct link.
- **Comments** for the curator, and the **Agreements** box: *I and all
  publication authors have read and agree to* the hub's **Terms of Deposit**.

Press **Submit draft**.

What happens next is not up to you. On a hub that reviews submissions, the
version goes to *pending curator review*, the curators are notified, and you
wait. A curator reads every panel of your draft, marks each one as acceptable
or as needing work, and then either approves the publication — which stamps
the accepted date, freezes the record, and registers the DOI — or sends it
back to you with an explanation of what has to change. Either way you get an
email. [Curation](#curation) describes both sides of that exchange, and what
to do when a draft comes back.

On a hub that does not review submissions, the version publishes the moment
you press the button. Auto-approval is a hub-wide option and can also be set
per publication type or for named members, and the hub's shipped
configuration turns it on. Where it is on, the review panel offers the tick
box *I would like for this publication to be reviewed instead of
automatically being published*, and that box is the only way to ask for a
curator. If you are not sure which kind of hub you are on, look at the
version's status straight after submitting: *pending curator review* means
somebody is reading it, *published* means nobody will.

Once a version is published you cannot change most of it — that is what the
DOI guarantees. To correct or extend the work, start a new version from the
**Versions** panel; it keeps the same publication page and adds a new release
with its own DOI.

## Forking

Where the hub allows it and the license permits derivatives, the **Forks**
tab offers **Fork Publication**. Forking copies the files, authors, tags and
citations into a draft of your own — either in a project you already have or
in a new one — so you can build on the work and publish the result. The fork
records where it came from, and the original's page lists it. The **Diff**
button on the Forks tab compares two versions side by side, section by
section.
## Curation

Curation exists because a DOI is a promise. Once a version is approved its
files and its metadata stop changing, and a paper somewhere can cite them,
so somebody looks at the draft before that becomes true. A curator checks
that the files are the ones the description says they are, that the authors
are right, that the licence permits what it claims to permit, and that a
stranger could work out what the data means. That is a different job from
peer review: a curator is not judging whether the science is good.

Curation is the review a publication goes through between the moment its
authors submit it and the moment it goes live. A curator reads every part of
the draft, marks each one as acceptable or as needing work, and either
approves the publication or sends it back. This chapter covers both sides:
reviewing as a curator, and answering a review as an author. Submitting a
draft in the first place is covered in
[Publishing your own work](#publishing-your-own-work) above.

### Who can curate

Curation is open to members of the hub's curators group and to site
administrators. Where a publication type names a curators group of its own,
its members see only publications of that type. Anyone else can still be
**assigned** to a single publication, and then sees only that one.

You have to be logged in. Visiting the curation pages as a guest gives you
*Please login to access publication curation*; a logged-in member with no
curation rights gets *We are sorry. You are not authorized for publication
curation.*

### The curation list

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

### Reviewing a publication

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

### Answering a review as an author

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

### Skipping review

Some hubs approve submissions automatically, and the shipped configuration
does exactly that. Auto-approval can be set three ways: hub-wide in the
component's options, per publication type in that type's curation settings,
and for named members whose submissions never wait. Where any of them
applies, pressing **Submit draft** publishes the version there and then —
accepted date stamped, DOI registered where the hub issues them, no curator
involved.

The review panel offers the tick box *I would like for this publication to be
reviewed instead of automatically being published*, so you can ask for a
curator anyway. On a hub that auto-approves, that box is the difference
between someone checking your dataset and nobody checking it. Tick it for
anything you would be embarrassed to have frozen.
